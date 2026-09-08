<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\classes\WebshopItemOrderableEntityDecorator;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\GenericRuleContextVariable;
use DynCom\dc\RuleEngine\RuleAction;
use DynCom\dc\RuleEngine\RuleContext;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.10.2015
 * Time: 14:01
 */
class CampaignDiscountAction implements RuleAction
{
    use campaignRulePartTrait;

    protected const DISCOUNT_TYPE_AMOUNT = 'AMNT_DISCOUNT';
    protected const DISCOUNT_TYPE_PERCENT = 'PERCENT_DISCOUNT';

    private $campaignID;
    private $itemBuilder;
    private $actionItemCollection;
    private $discountTargetType;
    private $multiplyApplicable;
    private $discountValue;
    private $discountType;
    private $campaignDescription;
    private $discountApplicationMaxQty;
    private $remainingApplicableQty;
    private $conditionThresholdValue;

    private $blockAllItemDiscountRepositoryInsert = false;

    public function __construct(GenericCampaign $campaign, WebshopItemBuilder $itemBuilder)
    {
        $this->actionItemCollection = $campaign->getActionElements();
        if ($campaign->getDiscountAmnt() > 0) {
            $this->discountType = self::DISCOUNT_TYPE_AMOUNT;
            $this->discountValue = $campaign->getDiscountAmnt();
            $this->discountTargetType = (int)$campaign->getDiscountAmntAppliesTo();
        } elseif ($campaign->getDiscountPercent() > 0) {
            $this->discountType = self::DISCOUNT_TYPE_PERCENT;
            $this->discountValue = $campaign->getDiscountPercent();
            $this->discountTargetType = (int)$campaign->getDiscountPercentAppliesTo();
        }
        $this->campaignID = $campaign->getID();
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $this->campaignID;
        $this->multiplyApplicable = $campaign->isMultiplyApplicable();
        $this->itemBuilder = $itemBuilder;
        $this->campaignDescription = $campaign->getDescription();
        $this->conditionThresholdValue = $campaign->getConditionThresholdValue();
        $this->discountApplicationMaxQty = $campaign->getDiscountApplicationMaxQty();
        $this->remainingApplicableQty = $this->discountApplicationMaxQty;
    }

    public function execute(RuleContext $context)
    {
        $targetAllConditionItems = (
            ($this->discountType === self::DISCOUNT_TYPE_AMOUNT && $this->discountTargetType === GenericCampaign::DISC_AMNT_TYPE_ALL_COND_ITEMS)
            || ($this->discountType === self::DISCOUNT_TYPE_PERCENT && $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_ALL_COND_ITEMS));
        $targetCurrConditionItem = (
            ($this->discountType === self::DISCOUNT_TYPE_AMOUNT && $this->discountTargetType === GenericCampaign::DISC_AMNT_TYPE_CURR_COND_ITEM)
            || ($this->discountType === self::DISCOUNT_TYPE_PERCENT && $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_CURR_COND_ITEM)
        );
        $targetLeastExpCondItem = ($this->discountType === self::DISCOUNT_TYPE_PERCENT && $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_LEAST_EXP_COND_ITEM);
        $targetBasket = (
            ($this->discountType === self::DISCOUNT_TYPE_AMOUNT && $this->discountTargetType === GenericCampaign::DISC_AMNT_TYPE_BASKET)
            || ($this->discountType === self::DISCOUNT_TYPE_PERCENT && $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_BASKET)
        );
        $targetAllActionItems = ($this->discountType === self::DISCOUNT_TYPE_PERCENT && $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_ALL_ACTION_ITEMS);

        if ($targetAllConditionItems) {
            $this->applyDiscountToAllConditionItems($context);
        } elseif ($targetCurrConditionItem) {
            $this->applyDiscountToCurrConditionItem($context);
        } elseif ($targetLeastExpCondItem) {
            $this->applyDiscountToLeastExpensiveConditionItem($context);
        } elseif ($targetBasket) {
            $this->applyDiscountToBasket($context);
        } elseif ($targetAllActionItems) {
            $this->applyDiscountToAllActionItems($context);
        } else {
            throw new \DomainException(
                'Discount Target Type is not recognized. Discount Type: ' . $this->discountType . ' Target Type: ' . $this->discountTargetType
            );
        }

        if ($context->hasVariable('AppliedRules')) {
            $appliedRulesArr = $this->getContextVariable($context, 'AppliedRules');
            $appliedRulesArr[$this->ruleKey] = $this->ruleKey;
        } else {
            $appliedRulesArr = [$this->ruleKey => $this->ruleKey];
        }
        $context->setVariable(new GenericRuleContextVariable('AppliedRules', $appliedRulesArr));
    }

    private function applyDiscountToAllConditionItems(RuleContext $context)
    {
        $discount = $this->getLineDiscount();
        $basket = $this->getContextVariable($context, 'UserBasket');
        $conditionItems = $this->getContextVariable($context, $this->ruleKey . '.ConditionItems');

        if ($basket instanceof UserBasket) {
            foreach ($conditionItems as $conditionItem) {
                if ($conditionItem instanceof BasketEntity) {
                    $this->applyDiscountToBasketEntity($basket, $conditionItem, $discount, $context);
                }
            }
        }
    }

    private function applyDiscountToCurrConditionItem(RuleContext $context)
    {
        

        $basket = $this->getContextVariable($context, 'UserBasket');
        $conditionItemSet = $this->getContextVariable($context, $this->ruleKey . '.CurrConditionItem');
        $discRepo = $this->getContextVariable($context, 'RuleDiscountRepository');

        if (is_iterable($conditionItemSet)) {
            $count = count($conditionItemSet);
            $discount = $this->getSplitLineDiscount($count);
        } else {
            $conditionItemSet = [$conditionItemSet];
            $discount = $this->getLineDiscount();
        }
        foreach ($conditionItemSet as $conditionItem) {
            if ($basket instanceof UserBasket && $conditionItem instanceof BasketEntity) {
                $this->applyDiscountToBasketEntity($basket, $conditionItem, $discount, $context);
                if (!$this->blockAllItemDiscountRepositoryInsert && $discRepo instanceof ActiveActionItemRuleDiscountRepository) {
                    $campaignElementArr = [
                        'type' => GenericCampaignElement::TYPE_ITEM,
                        'include_exclude' => GenericCampaignElement::SET_OPERATOR_INCLUDE,
                        'item_no' => $conditionItem->getIdentifier(),
                        'item_var_code' => $conditionItem->getSubIdentifier()
                    ];
                    $campEl = new GenericCampaignElement(new GenericCampaignElementConfig());
                    $campEl->mapFromArray($campaignElementArr);
                    $campElCollection = $this->actionItemCollection->getEmptyCollection();
                    $campElCollection->add($campEl);
                    $discRepo->addRuleDiscount($discount, $campElCollection);
                }
            }
        }
    }

    private function applyDiscountToLeastExpensiveConditionItem(RuleContext $context)
    {
        $discount = $this->getLineDiscount();
        $basket = $this->getContextVariable($context, 'UserBasket');
        $conditionItems = $this->getContextVariable($context, $this->ruleKey . '.ConditionItems');
        $leastExpensiveConditionItem = $this->getLeastExpensiveItem($conditionItems);
        if ($basket instanceof UserBasket && $leastExpensiveConditionItem instanceof BasketEntity) {
            $this->applyDiscountToBasketEntity($basket, $leastExpensiveConditionItem, $discount, $context);
        }
    }

    private function applyDiscountToBasket(RuleContext $context)
    {
        /**
         * @var $basket UserBasket
         */
        $basket = $this->getContextVariable($context, 'UserBasket');
        if ($basket instanceof UserBasket) {
        if ($this->discountType === self::DISCOUNT_TYPE_PERCENT) {
            $discount = $this->getLineDiscount();
        } else {
            $discount = $this->getSplitLineDiscount($basket->getTotalNoOfPos());
        }
            /**
             * @var $basketEntity BasketEntity
             */

            foreach ($basket as $basketEntity) {
                $key = $basket->getKey($basketEntity);
                $basket->applyLineDiscountByKey($discount,$key);
            }
        }

    }

    private function applyDiscountToAllActionItems(RuleContext $context)
    {
        $discount = $this->getLineDiscount();
        $basket = $this->getContextVariable($context, 'UserBasket');
        $discRepo = $this->getContextVariable($context, 'RuleDiscountRepository');
        foreach ($basket as $basketEntity) {
            if ($basketEntity instanceof BasketEntity && $basketEntity->getOrderableType(
                ) === WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM
            ) {
                $categoryDecorated = $this->itemBuilder->decorateWebshopItemCategories(
                    $basketEntity->getOrderableEntity()
                );
                if (!$basketEntity->isUnitPriceProtected() && $this->actionItemCollection->itemMeetsCriteria(
                        $categoryDecorated
                    )
                ) {
                    $this->applyDiscountToBasketEntity($basket, $basketEntity, $discount, $context);
                    $this->afterAllItemDiscountIndividualApplication($context, $basketEntity);
                }
            }
        }
        if (!$this->blockAllItemDiscountRepositoryInsert && $discRepo instanceof ActiveActionItemRuleDiscountRepository) {
            $discRepo->addRuleDiscount($discount, $this->actionItemCollection);
        }
    }

    private function getLineDiscount()
    {
        $percent = null;
        $amnt = null;
        if ($this->discountType === self::DISCOUNT_TYPE_AMOUNT) {
            $amnt = $this->discountValue;
        } else {
            $percent = $this->discountValue;
        }
        $discount = new GenericLineDiscount(
            GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE,
            $this->campaignID,
            $percent,
            $amnt
        );
        $discount->setNotification($this->campaignDescription);
        return $discount;
    }

    private function getSplitLineDiscount($noOfSplits)
    {
        $splits = (int)$noOfSplits;
        $splits = $splits ?: 1;
        $percent = null;
        $amnt = null;
        if ($this->discountType === self::DISCOUNT_TYPE_AMOUNT) {
            $amnt = $this->discountValue / $splits;
        } else {
            $percent = $this->discountValue;
        }
        $discount = new GenericLineDiscount(
            GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE,
            $this->campaignID,
            $percent,
            $amnt
        );
        $discount->setNotification($this->campaignDescription);
        return $discount;
    }

    private function getInvoiceDiscount()
    {
        $percent = null;
        $amnt = null;
        if ($this->discountType === self::DISCOUNT_TYPE_AMOUNT) {
            $amnt = $this->discountValue;
        } else {
            $percent = $this->discountValue;
        }
        $discount = new GenericInvoiceDiscount(
            GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE,
            $this->campaignID,
            $percent,
            $amnt
        );
        $discount->setNotification($this->campaignDescription);
        return $discount;
    }

    private function getLeastExpensiveItem($items)
    {
        $lowestAmnt = null;
        $lowestItem = null;
        foreach ($items as $item) {
            if ($item instanceof OrderableEntityInterface) {
                if ($lowestAmnt === null || $item->getUnitPrice() < $lowestAmnt) {
                    $lowestAmnt = $item->getUnitPrice();
                    $lowestItem = $item;
                }
            }
        }
        return $lowestItem;
    }

    private function applyDiscountToBasketEntity(
        UserBasket $basket,
        BasketEntity $entity,
        GenericLineDiscount $lineDiscount,
        RuleContext $context
    ) {
        if ($entity->getUnitPrice() == 0.00) {
            return;
        }
        $identKey = $entity->getIdentifier() . '|' . $entity->getSubIdentifier();
        $factor = $this->getThresholdMetByFactorForItemKey($context, $identKey);
        $maxQty = abs($this->remainingApplicableQty) * $factor;
        $remainingApplicableQty = $maxQty / $factor;
        $currKey = $basket->getKey($entity);
        $origKey = $currKey;

        $virginEntity = $this->itemBuilder->getWebshopItemOrderableEntityByID($entity->getID());
        $origUnitPrice = $virginEntity->getUnitPrice();
        unset($virginEntity);

        if ($this->discountApplicationMaxQty == 0) {
            if ($this->multiplyApplicable || !$basket->isLineDiscountAppliedToItemByKey($lineDiscount, $currKey)) {
                $basket->applyLineDiscountByKey($lineDiscount, $currKey);
                $currKey = $basket->setItemQtyAccessibilityByKey($currKey, true);
                $currKey = $basket->setItemUnitPriceAccessibilityByKey($currKey, true);
                $priceData = $entity->getPriceData();
                $priceData->setCrossPrice($origUnitPrice);
            }
        } else {
            $totalEntityQty = $basket->getQtyOfAllPositionsForItem($entity->getIdentifier(), $entity->getSubIdentifier());
            $userCreatedEntityQty = $entity->getQuantity();
            $nonUserCreatedEntityQty = $totalEntityQty - $userCreatedEntityQty;
            //Split lines unless maxQty > Qty

            $qtyToDiscount = $factor * $this->discountApplicationMaxQty;
            $priceToDiscount = $entity->getUnitPrice() * $qtyToDiscount;
            $discountPercent = $lineDiscount->getDiscountedPercentage();
            $discountAmount = $lineDiscount->getDiscountedAmount();
            if ($this->discountType === self::DISCOUNT_TYPE_PERCENT) {
                $percent = $this->discountValue;
                $percentAmount = (($priceToDiscount / 100) * $percent) / $factor;
                $lineDiscount = new GenericLineDiscount(
                    GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE,
                    $this->campaignID,
                    null,
                    $percentAmount
                );
                $lineDiscount->setNotification($this->campaignDescription);
            } else {
                $amount = $priceToDiscount * $factor;
                $lineDiscount = new GenericLineDiscount(
                    GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE,
                    $this->campaignID,
                    $amount
                );
                $lineDiscount->setNotification($this->campaignDescription);
            }
            $basket->applyLineDiscountByKey($lineDiscount, $currKey);
            $priceData = $entity->getPriceData();
            $priceData->setCrossPrice($origUnitPrice);

            /*
            if($userCreatedEntityQty < $qtyToDiscount) {
                $qtyRemaining = 0;
                $transformOriginal = true;
                $splitOriginal = false;
            } else {
                $qtyRemaining = $userCreatedEntityQty - $qtyToDiscount;
                $splitOriginal = true;
                $transformOriginal = false;
            }
            $remainingApplicableQty -= ceil(($qtyToDiscount / $factor));

            $userCreatedEntity = $this->itemBuilder->getWebshopItemBasketEntity($entity->getIdentifier(),$entity->getQuantity(),$entity->getSubIdentifier());
            $clone = $this->itemBuilder->getWebshopItemBasketEntity($entity->getIdentifier(),$entity->getQuantity(),$entity->getSubIdentifier());

            $clone ->setQtySourceType(ItemPriceData::QTY_SOURCE_RULE);
            $clone ->setQtySourceID($this->campaignID);
            $clone ->setPriceSourceType(ItemPriceData::PRICE_SOURCE_RULE);
            $clone ->setPriceSourceID($this->campaignID);
            $clone ->setQtyAccessibility(false);
            $clone ->setUnitPriceAccessibility(false);
            $cloneKey = $basket->getKey($clone);
            if (
                    $this->multiplyApplicable
                ||  (
                            $splitOriginal
                        &&  !$basket->hasItemForKey($cloneKey)
                    )
                ||  (
                            $transformOriginal
                        &&  !$basket->isLineDiscountAppliedToItemByKey($lineDiscount,$origKey)
                    )
            ) {
                if($transformOriginal) {
                    // Make sure is accessible,
                    // apply discount,
                    // set not accessible
                    $currKey = $basket->setItemQtyAccessibilityByKey($origKey,true);
                    $currKey = $basket->setItemUnitPriceAccessibilityByKey($currKey,true);
                    $basket->applyLineDiscountByKey($lineDiscount,$currKey);
                    $basket->setEntityPriceSourceByKey($currKey,ItemPriceData::PRICE_SOURCE_RULE,$this->campaignID);
                    $currKey = $basket->setItemQtyAccessibilityByKey($currKey,false);
                    $currKey = $basket->setItemUnitPriceAccessibilityByKey($currKey,false);
                    $basket->recalculateValues();
                } else {
                    // Remove initial entity from basket,
                    // prepare and add clone,
                    // set clone not accessible,
                    // re-add original
                    $basket->removeItemByKey($origKey);

                    $clone ->setQuantity($qtyToDiscount);
                    $cloneKey = $basket->getKey($clone);
                    if($basket->hasItemForKey($cloneKey)) {
                        $cloneKey = $basket->setItemQtyAccessibilityByKey($cloneKey,true);
                        $cloneKey = $basket->setItemUnitPriceAccessibilityByKey($cloneKey,true);
                        $basket->changeItemQtyByKey($cloneKey,$qtyToDiscount);
                    } else {
                        $clone ->setQtyAccessibility(true);
                        $clone ->setUnitPriceAccessibility(true);
                        $cloneKey = $basket->getKey($clone);
                        $basket->addItem($clone);
                    }
                    if(!$basket->isLineDiscountAppliedToItemByKey($lineDiscount,$cloneKey)) {
                        $basket->applyLineDiscountByKey($lineDiscount,$cloneKey);
                    }
                    $currClone1Key = $basket->setItemUnitPriceAccessibilityByKey($cloneKey,false);
                    $currClone1Key = $basket->setItemQtyAccessibilityByKey($currClone1Key,false);

                    $entityQtyAccessibility = $userCreatedEntity->isQtyProtected();
                    $userCreatedEntity->setQtyAccessibility(true);
                    $userCreatedEntity->setQuantity($qtyRemaining);
                    $userCreatedEntity->setQtyAccessibility($entityQtyAccessibility);
                    $basket->addItem($userCreatedEntity);
                }
            }

            //Calculate and set remaining applicable qty
            $this->remainingApplicableQty = $remainingApplicableQty;
            $this->blockAllItemDiscountRepositoryInsert = true;
            */
            //@TODO: TEST!!!!
        }
    }

    private function afterAllItemDiscountIndividualApplication(RuleContext $context, BasketEntity $entity)
    {
        if (
            $this->discountTargetType === GenericCampaign::DISC_PERC_TYPE_ALL_ACTION_ITEMS
            && $this->discountApplicationMaxQty > 0
            && $this->remainingApplicableQty <= 0
            && (($entity->getQuantity() % $this->conditionThresholdValue) !== 0)
        ) {
            $discount = $this->getLineDiscount();
            $discRepo = $this->getContextVariable($context, 'RuleDiscountRepository');
            if ($discRepo instanceof ActiveActionItemRuleDiscountRepository && $discRepo->discountExists(
                    $discount->getSourceID()
                )
            ) {
                $discRepo->removeDiscount($discount);
            }
            $this->blockAllItemDiscountRepositoryInsert = true;
        }
    }

}