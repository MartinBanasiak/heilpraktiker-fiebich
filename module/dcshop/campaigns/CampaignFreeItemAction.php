<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\GenericRuleContextVariable;
use DynCom\dc\RuleEngine\RuleAction;
use DynCom\dc\RuleEngine\RuleContext;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 29.10.2015
 * Time: 09:24
 */
class CampaignFreeItemAction implements RuleAction
{
    use campaignRulePartTrait;

    protected const KEY_USER_BASKET = 'UserBasket';
    protected const KEY_CURR_CONDITION_ITEM = 'CurrConditionItem';
    protected const FREE_ITEM_TARGET_CURR_COND_ITEM = 'CURR_CONDITION_ITEM';
    protected const FREE_ITEM_TARGET_LEAST_EXP_COND_ITEM = 'LEAST_EXPENSIVE_COND_ITEM';
    protected const FREE_ITEM_TARGET_ALL_ACTION_ITEMs = 'ALL_ACTION_ITEMS';

    private $campaignID;
    private $actionItemCollection;
    private $itemBuilder;
    private $freeItemTargetType;
    private $freeItemQty;
    private $multiplyApplicable;
    private $campaignDescription;

    public function __construct(GenericCampaign $campaign,WebshopItemBuilder $itemBuilder)
    {
        $campaignID = $campaign->getID();
        $this->ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $campaignID;
        $this->campaignID = $campaignID;
        $this->actionItemCollection = $campaign->getActionElements();
        $this->freeItemTargetType = $campaign->getTypeFreeItem();
        $this->freeItemQty = $campaign->getQtyFreeItem();
        $this->multiplyApplicable = $campaign->isMultiplyApplicable();
        $this->itemBuilder = $itemBuilder;
        $this->campaignDescription = $campaign->getDescription();
    }

    public function execute(RuleContext $context)
    {
        switch($this->freeItemTargetType) {
            case (GenericCampaign::FREE_ITEM_TYPE_CURR_COND_ITEM):
                $this->addFreeItemFromCurrCondItem($context);
                break;
            case (GenericCampaign::FREE_ITEM_TYPE_LEAST_EXP_COND_ITEM):
                $this->addFreeItemFromLeastExpCondItem($context);
                break;
            case (GenericCampaign::FREE_ITEM_TYPE_ALL_ACT_ITEM):
                $this->addFreeItemFromAllActionItems($context);
                break;
            default:
                throw new \DomainException('Free Item Target Type is not recognized.');
                break;
        }

        if ($context->hasVariable('AppliedRules')) {
            $appliedRulesArr = $this->getContextVariable($context, 'AppliedRules');
            $appliedRulesArr[$this->ruleKey] = $this->ruleKey;
        } else {
            $appliedRulesArr = [$this->ruleKey => $this->ruleKey];
        }
        $context->setVariable(new GenericRuleContextVariable('AppliedRules',$appliedRulesArr));
    }

    private function addFreeItemFromCurrCondItem(RuleContext $context)
    {
        $basket = $this->getContextVariable($context, self::KEY_USER_BASKET);
        $conditionItemSet = $this->getContextVariable($context, $this->ruleKey . '.' . self::KEY_CURR_CONDITION_ITEM);

        if (is_iterable($conditionItemSet)) {
            $currConditionItem = end($conditionItemSet);
        }

        $campaignID = $this->campaignID;
        if ($basket instanceof UserBasket && $currConditionItem instanceof BasketEntity) {
            $conditionItemKey = $currConditionItem->getIdentifier() . '|' . $currConditionItem->getSubIdentifier();
            //$basket->getKey($currConditionItem);
            $factor = 1;
            if ($this->multiplyApplicable) {
                $factor = $this->getThresholdMetByFactorForItemKey($context, $conditionItemKey);
            }
            $freeItemQty = $this->freeItemQty * $factor;
            $identifier = $currConditionItem->getIdentifier();
            $subidentifier = $currConditionItem->getSubIdentifier();
            $this->addFreeItem($basket, $identifier, $subidentifier, $freeItemQty, $campaignID);
        }

    }

    private function addFreeItemFromLeastExpCondItem(RuleContext $context)
    {
        $campaignID = $this->campaignID;
        $basket = $this->getContextVariable($context,'UserBasket');
        $conditionItems = $this->getContextVariable($context,$this->ruleKey . '.ConditionItems');
        $leastExpensiveCondItem = $this->getLeastExpensiveItem($conditionItems);

        if($basket instanceof UserBasket && $leastExpensiveCondItem instanceof BasketEntity) {
            $conditionItemKey = $leastExpensiveCondItem->getIdentifier() . '|' . $leastExpensiveCondItem->getSubIdentifier();
            //$basket->getKey($leastExpensiveCondItem);
            $factor = 1;
            if($this->multiplyApplicable) {
                $factor = $this->getThresholdMetByFactorForItemKey($context,$conditionItemKey);
            }
            $freeItemQty = $this->freeItemQty * $factor;
            $identifier = $leastExpensiveCondItem->getIdentifier();
            $subidentifier = $leastExpensiveCondItem->getSubIdentifier();
            $this->addFreeItem($basket,$identifier,$subidentifier,$freeItemQty,$campaignID);
        }
    }

    private function addFreeItemFromAllActionItems(RuleContext $context)
    {
        $campaignID = $this->campaignID;
        $basket = $this->getContextVariable($context,'UserBasket');
        $actionItemCollection = $this->actionItemCollection;
        if($basket instanceof UserBasket) {
            foreach ($actionItemCollection as $actionItem) {
                if ($actionItem instanceof GenericCampaignElement && $actionItem->typeIsItem()) {
                    $item = $this->itemBuilder->getWebshopItemOrderableEntityByPrimary($actionItem->item_no,$actionItem->item_var_code);
                    //$conditionItemKey = $basket->getKey($item);
                    $conditionItemKey = $item->getIdentifier() . '|' . $item->getSubIdentifier();
                    $factor = 1;
                    if($this->multiplyApplicable) {
                        $factor = $this->getThresholdMetByFactorForItemKey($context,$conditionItemKey);
                    }
                    $freeItemQty = $this->freeItemQty * $factor;
                    $identifier = $item->getIdentifier();
                    $subidentifier = $item->getSubIdentifier();
                    $this->addFreeItem($basket,$identifier,$subidentifier,$freeItemQty,$campaignID);
                }
            }
        }
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

    private function addFreeItem(UserBasket $basket,$identifier,$subidentifier,$quantity,$campaignID)
    {
        $copyConditionItem = $this->itemBuilder->getWebshopItemBasketEntity($identifier,$quantity,$subidentifier);
        if($copyConditionItem instanceof BasketEntity && $copyConditionItem->getID() > 0) {
            $copyConditionItem->setQuantity($quantity);
            $copyConditionItem->setQtySourceType(ItemPriceData::QTY_SOURCE_RULE);
            $copyConditionItem->setQtySourceID($campaignID);
            $copyConditionItem->setCreationSourceType(BasketEntity::CREATION_SOURCE_TYPE_RULE);
            $copyConditionItem->setCreationSourceID($campaignID);
            $copyConditionItem->setCreationNotification($GLOBALS['tc']['from_campaign'] . ': ' . $this->campaignDescription);
            $priceData = $copyConditionItem->getPriceData();
            $priceData->setPrice(0.00);
            $priceData->setPriceSourceType(ItemPriceData::PRICE_SOURCE_RULE);
            $priceData->setPriceSourceID($campaignID);
            $copyConditionItem->setUnitPriceAccessibility(false);
            $copyConditionItem->setQtyAccessibility(false);
            $basket->addItem($copyConditionItem,true);
        }
    }
}