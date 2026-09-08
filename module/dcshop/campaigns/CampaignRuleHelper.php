<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\interfaces\IOCInterface;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\classes\AppliedDiscount;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\InvoiceDiscount;
use DynCom\dc\dcShop\ShippingOptions\ShippingClass;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\classes\WebshopItemOrderableEntityDecorator;
use DynCom\dc\dcShop\interfaces\Discount;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\RuleEngine\GenericRule;
use DynCom\dc\RuleEngine\GenericRuleContextVariable;
use DynCom\dc\RuleEngine\RuleContext;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 26.10.2015
 * Time: 22:04
 */
class CampaignRuleHelper
{

    const RULE_ACTION_FREE_ITEM = 'RULE_FREE_ITEM';
    const RULE_ACTION_ITEM_DISCOUNT = 'RULE_ITEM_DISCOUNT';
    const RULE_ACTION_INVOICE_DISCOUNT = 'RULE_INVOICE_DISCOUNT';
    const RULE_ACTION_SPECIAL_SHIPPING = 'RULE_SPECIAL_SHIPPING';
    const RULE_ACTION_ITEM_DISCOUNT_SPLIT = 'RULE_ITEM_DISCOUNT_SPLIT';

    /**
     * @var IOCInterface
     */
    private $IOC;
    /**
     * @var WebshopItemBuilder
     */
    private $itemBuilder;

    public function __construct(IOCInterface $IOCInterface)
    {
        $this->IOC = $IOCInterface;
        $this->itemBuilder = $IOCInterface->resolve('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    }


    /**
     * @param RuleContext $context
     */
    public function removeInvalidRules(RuleContext $context)
    {
        $basket = $context->getVariable('UserBasket')->getValue();
        $initiallyAppliedRules = $context->getVariable('InitiallyAppliedBasketRules')->getValue();
        $rulesToDelete = array_keys($initiallyAppliedRules);

        if ($context->hasVariable('AppliedRules')) {
            $appliedRuleKeysVar = $context->getVariable('AppliedRules')->getValue();
            $appliedRuleKeys = array_keys($appliedRuleKeysVar);
            $rulesToDelete = array_diff(array_keys($initiallyAppliedRules),$appliedRuleKeys);
        }
        if($basket instanceof UserBasket) {
            foreach ($rulesToDelete as $toDeleteRuleKey) {
                foreach ($initiallyAppliedRules[$toDeleteRuleKey] as $ruleApplications) {
                    $type = $ruleApplications['type'];
                    switch ($type) {
                        case self::RULE_ACTION_INVOICE_DISCOUNT:
                            $discount = $ruleApplications['object'];
                            if($discount instanceof AppliedDiscount) {
                                $discount = new GenericInvoiceDiscount($discount->getSourceType(),$discount->getSourceID(),null,$discount->getDiscountedAmount());
                            }
                            if($basket->isInvoiceDiscountApplied($discount)) {
                                $basket->removeInvoiceDiscount($discount);
                            }
                            break;
                        case self::RULE_ACTION_FREE_ITEM:
                            $itemKey = $basket->getKey($ruleApplications['object']);
                            if($basket->hasItemForKey($itemKey)) {
                                $newKey = $basket->setItemQtyAccessibilityByKey($itemKey, true,true);
                                $basket->removeItemByKey($newKey,true);
                            }
                            break;
                        case self::RULE_ACTION_ITEM_DISCOUNT:
                            $discount = $ruleApplications['object'];
                            $itemKey = $ruleApplications['item_key'];
                            if($discount instanceof AppliedDiscount) {
                                $discount = new GenericLineDiscount($discount->getSourceType(),$discount->getSourceID(),null,$discount->getDiscountedAmount());
                            }
                            if($basket->hasItemForKey($itemKey) && $basket->isLineDiscountAppliedToItemByKey($discount,$itemKey)) {
                                $newKey = $basket->setItemQtyAccessibilityByKey($itemKey,true);
                                $newKey = $basket->setItemUnitPriceAccessibilityByKey($newKey,true);
                                $basket->removeLineDiscountByKey($discount, $newKey);
                            }
                            break;
                        case self::RULE_ACTION_ITEM_DISCOUNT_SPLIT:
                            $splitEntity = $ruleApplications['object'];
                            $origKey = WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM . '|' . $splitEntity->getIdentifier() . '|' . $splitEntity->getSubidentifier();
                            $qty = $splitEntity->getQuantity();
                            $origQty = 0;

                            $basket->removeItemByKey($basket->getKey($splitEntity));
                            if($basket->hasItemForKey($origKey)) {
                                $origQty = $basket->getItemQtyByKey($origKey);
                                $basket->removeItemByKey($origKey);
                            }
                            $newQty = $qty + $origQty;

                            $newItem = $this->itemBuilder->getWebshopItemBasketEntity($splitEntity->getIdentifier(),$newQty,$splitEntity->getSubIdentifier());
                            $basket->addItem($newItem);

                            break;
                        default:
                            throw new \DomainException('No default handler for applied rule removal specified.');
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param RuleContext $context
     * @return RuleContext
     */
    public function defaultPopulateRuleContext(RuleContext $context)
    {
        $IOCInterface = $this->IOC;
        $currUserBasket = $IOCInterface->resolve('$CurrUserBasket');
        $ruleDiscountRepo = $IOCInterface->resolve('$activeRuleDiscountRepository');
        $shippingOptRepo = $IOCInterface->resolve('$ShippingOptionRepository');

        $currUserBasketVar = new GenericRuleContextVariable('UserBasket',$currUserBasket);
        $ruleDiscRepoVar = new GenericRuleContextVariable('RuleDiscountRepository',$ruleDiscountRepo);
        $shipOptRepoVar = new GenericRuleContextVariable('ShippingOptionRepository',$shippingOptRepo);

        $initiallyAppliedBasketRules = $this->getAppliedRuleKeysFromBasket($currUserBasket);
        $initiallyAppliedBasketRulesVar = new GenericRuleContextVariable('InitiallyAppliedBasketRules',$initiallyAppliedBasketRules);

        $context->addVariable($currUserBasketVar);
        $context->addVariable($ruleDiscRepoVar);
        $context->addVariable($shipOptRepoVar);
        $context->addVariable($initiallyAppliedBasketRulesVar);

        return $context;

    }

    /**
     * @param RuleContext $context
     * @param BasketEntity $basketEntity
     * @param $newQty
     */
    private function changeBasketItemQty(RuleContext $context,BasketEntity $basketEntity,$newQty)
    {

    }

    /**
     * @param RuleContext $context
     * @param GenericLineDiscount $lineDiscount
     * @param $applyToItemKey
     */
    private function applyLineDiscount(RuleContext $context,GenericLineDiscount $lineDiscount,$applyToItemKey)
    {

    }

    /**
     * @param RuleContext $context
     * @param InvoiceDiscount $invoiceDiscount
     */
    private function applyInvoiceDiscount(RuleContext $context,InvoiceDiscount $invoiceDiscount)
    {

    }

    /**
     * @param RuleContext $context
     * @param Discount $discount
     */
    private function removeDiscount(RuleContext $context,Discount $discount)
    {

    }

    /**
     * @param RuleContext $context
     * @param ShippingClass $shippingOption
     */
    private function applySpecialShipping(RuleContext $context, ShippingClass $shippingOption)
    {

    }

    /**
     * @param RuleContext $context
     * @param ShippingClass $shippingOption
     */
    private function removeSpecialShipping(RuleContext $context, ShippingClass $shippingOption)
    {

    }

    private function getAppliedRuleKeysFromBasket(UserBasket $basket)
    {
        $appliedRules = [];
        $invoiceDiscs = $basket->getAppliedInvoiceDiscounts();
        foreach($invoiceDiscs as $invDisc) {
            if($invDisc instanceof AppliedDiscount && $invDisc->getSourceType() === DiscountBase::DISCOUNT_SOURCE_TYPE_RULE) {
                $ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $invDisc->getSourceID();
                if(!array_key_exists($ruleKey,$appliedRules)) {
                    $index = 0;
                } else {
                    $index = count($appliedRules[$ruleKey]);
                }
                $appliedRules[$ruleKey][$index]['type'] = self::RULE_ACTION_INVOICE_DISCOUNT;
                $appliedRules[$ruleKey][$index]['object'] = $invDisc;
            }
        }
        foreach($basket as $basketEntity) {
            if($basketEntity instanceof BasketEntity) {
                $creationSourceType = $basketEntity->getCreationSourceType();
                $priceSourceType = $basketEntity->getPriceSourceType();
                $qtySourceType = $basketEntity->getQtySourceType();
                $priceProtected = $basketEntity->isUnitPriceProtected();
                $qtyProtected = $basketEntity->isQtyProtected();
                if($creationSourceType == BasketEntity::CREATION_SOURCE_TYPE_RULE) {
                    $ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $basketEntity->getCreationSourceID();
                    if(!array_key_exists($ruleKey,$appliedRules)) {
                        $index = 0;
                    } else {
                        $index = count($appliedRules[$ruleKey]);
                    }
                    $appliedRules[$ruleKey][$index]['type'] = self::RULE_ACTION_FREE_ITEM;
                    $appliedRules[$ruleKey][$index]['object'] = $basketEntity;
               /* } elseif($qtyProtected && $priceProtected) {
                    $ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $basketEntity->getPriceSourceID();
                    if(!array_key_exists($ruleKey,$appliedRules)) {
                        $index = 0;
                    } else {
                        $index = count($appliedRules[$ruleKey]);
                    }
                    $appliedRules[$ruleKey][$index]['type'] = self::RULE_ACTION_ITEM_DISCOUNT_SPLIT;
                    $appliedRules[$ruleKey][$index]['object'] = $basketEntity; */
                }
                $appliedDiscounts = $basketEntity->getAppliedLineDiscounts();
                foreach($appliedDiscounts as $lineDisc) {
                    if($lineDisc instanceof AppliedDiscount && $lineDisc->getSourceType() === DiscountBase::DISCOUNT_SOURCE_TYPE_RULE) {
                        $ruleKey = GenericRule::RULE_TYPE_CAMPAIGN . '|' . $lineDisc->getSourceID();
                        if(!array_key_exists($ruleKey,$appliedRules)) {
                            $index = 0;
                        } else {
                            $index = count($appliedRules[$ruleKey]);
                        }
                        $appliedRules[$ruleKey][$index]['type'] = self::RULE_ACTION_ITEM_DISCOUNT;
                        $appliedRules[$ruleKey][$index]['item_key'] = $basket->getKey($basketEntity);
                        $appliedRules[$ruleKey][$index]['object'] = $lineDisc;
                    }
                }
            }
        }
        return $appliedRules;
    }

}