<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\GenericLineDiscount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 28.09.2015
 * Time: 08:45
 */
interface UserBasket extends \IteratorAggregate,\Countable
{

    /**
     * @return mixed
     */
    public function getID();

    /**
     * @return mixed
     */
    public function isMainBasket();

    /**
     * @param BasketEntity $entity
     * @param bool $noEventTriggers
     * @return mixed
     */
    public function addItem(BasketEntity $entity, $noEventTriggers);

    /**
     * @param $key
     * @param bool $noEventTriggers
     * @return mixed
     */
    public function removeItemByKey($key,$noEventTriggers);

    /**
     * @param $id
     * @return mixed
     */
    public function removeItemByLineID($id);

    /**
     * @param $lineNo
     * @return mixed
     */
    public function removeItemByLineNo($lineNo);

    /**
     * @param $key
     * @param $newQty
     * @return mixed
     */
    public function changeItemQtyByKey($key, $newQty);

    /**
     * @return mixed
     */
    public function getTotalQty();

    /**
     * @return mixed
     */
    public function getTotalNoOfPos();

    /**
     * @return mixed
     */
    public function getBasketTotal();

    /**
     * @param $vatGroupCode
     * @return mixed
     */
    public function getVATAmountsForVATGroup($vatGroupCode);

    /**
     * @return mixed
     */
    public function getVATAmountsPerVATGroup();

    /**
     * @return mixed
     */
    public function getVATAmountsPerVATPercent();

    /**
     * @return mixed
     */
    public function getSumVATAmounts();

    /**
     * @param $key
     * @return mixed
     */
    public function hasItemForKey($key);

    /**
     * @param $key
     * @return mixed
     */
    public function getItemQtyByKey($key);

    /**
     * @param $key
     * @return BasketEntity
     */
    public function getItemByKey($key);

    /**
     * @param GenericLineDiscount $discount
     * @param $key
     * @return mixed
     */
    public function applyLineDiscountByKey(GenericLineDiscount $discount, $key);

    /**
     * @param GenericLineDiscount $discount
     * @param $key
     * @return mixed
     */
    public function removeLineDiscountByKey(GenericLineDiscount $discount, $key);

    /**
     * @param GenericInvoiceDiscount $discount
     * @return mixed
     */
    public function applyInvoiceDiscount(GenericInvoiceDiscount $discount);

    /**
     * @param GenericInvoiceDiscount $discount
     * @return mixed
     */
    public function removeInvoiceDiscount(GenericInvoiceDiscount $discount);

    /**
     * @return mixed
     */
    public function getAppliedInvoiceDiscounts();

    /**
     * @param GenericInvoiceDiscount $discount
     * @return mixed
     */
    public function isInvoiceDiscountApplied(GenericInvoiceDiscount $discount);

    /**
     * @param GenericLineDiscount $discount
     * @param $key
     * @return mixed
     */
    public function isLineDiscountAppliedToItemByKey(GenericLineDiscount $discount, $key);

    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     * @return mixed
     */
    public function hasEntityKeyPriceSource($key, $priceSourceType, $priceSourceID);

    /**
     * @param $key
     * @param $creatorType
     * @param $creatorID
     * @return mixed
     */
    public function isEntityKeyCreatedBy($key, $creatorType, $creatorID);

    /**
     * @param $key
     * @param $priceSourceType
     * @param $priceSourceID
     * @return mixed
     */
    public function setEntityPriceSourceByKey($key, $priceSourceType, $priceSourceID);

    /**
     * @param $key
     * @param $creatorType
     * @param $creatorID
     * @return mixed
     */
    public function setEntityKeyCreatedBy($key, $creatorType, $creatorID);

    /**
     * @return mixed
     */
    public function recalculateValues();

    /**
     * @return mixed
     */
    public function getVisitorID();

    /**
     * @return mixed
     */
    public function getUserID();

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return float
     */
    public function getBasketTotalGross();

    /**
     * @return mixed
     */
    public function getBasketTotalNet();

    /**
     * @return mixed
     */
    public function getHash();

    /**
     * @return mixed
     */
    public function reset();

    /**
     * @return mixed
     */
    public function getNextAvailableLineNo();

    /**
     * @param OrderableEntityInterface $entity
     * @return mixed
     */
    public function getKey(OrderableEntityInterface $entity);

    /**
     * @return mixed
     */
    public function getLastChangedItem();

    /**
     * @param $lineNo
     * @return mixed
     */
    public function getItemByLineNo($lineNo);

    /**
     * QtyAccessibility and UnitPriceAccessibility belong to unique key
     * so a change results in a change of the key. The new key is returned
     * by the function
     * @param $key
     * @param $accessibilty
     * @return mixed
     */
    public function setItemUnitPriceAccessibilityByKey($key,$accessibilty);

    /**
     * QtyAccessibility and UnitPriceAccessibility belong to unique key
     * so a change results in a change of the key. The new key is returned
     * by the function
     * @param $key
     * @param $accessibilty
     * @return mixed
     */
    public function setItemQtyAccessibilityByKey($key,$accessibilty,$noEventTriggers = false);

    /**
     * @return mixed
     */
    public function getInvoiceDiscountAmount();

    /**
     * @return mixed
     */
    public function getItemVATData();

    /**
     * @param $identifier
     * @param $subidentifier
     * @return mixed
     */
    public function getQtyOfAllPositionsForItem($identifier, $subidentifier);

    /**
     * @return mixed
     */
    public function getCompany();

    /**
     * @return mixed
     */
    public function getShopCode();

    /**
     * @return mixed
     */
    public function getShopLanguageCode();

    /**
     * @return mixed
     */
    public function setMinMaxQuantitiesForLinkedItems();

    /**
     * @return mixed
     */
    public function getSumItemWeight();

    /**
     * @return mixed
     */
    public function getBasketItemTotal();

    /**
     * @return mixed
     */
    public function forcePersist();

    /**
     * @return mixed
     */
    public function getShippingClassFilter();

    public function getOrderableEntityCollection();

    public function getLastPersistedHash();

}