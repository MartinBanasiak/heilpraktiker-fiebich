<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\GenericLineDiscount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 07.07.2015
 * Time: 19:44
 */
interface OrderableEntityInterface
{

    const BASKET_ENTITY_LINK_TYPE_NO_LINK = 0;
    const BASKET_ENTITY_LINK_TYPE_WRAPPING_TO_ITEM = 1;

    /**
     * @return int
     */
    public function getID();

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @return int
     */
    public function getOrderableType();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getSubIdentifier();


    /**
     * @return string
     */
    public function getUnitCode();

    /**
     * @return float
     */
    public function getQuantity();

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return float
     */
    public function getUnitPrice();

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceGross(IVATManager $VATManager);

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceNet(IVATManager $VATManager);

    /**
     * @return string
     */
    public function getVATCode();

    /**
     * @return float
     */
    public function getVATPercent();

    /**
     * @return float
     */
    public function getVATAmount();

    /**
     * @return float
     */
    public function getLineAmount();

    /**
     * @return float|false
     */
    public function getWeight();

    /**
     * @return bool
     */
    public function allowsInvoiceDiscount();

    /**
     * @return mixed
     */
    public function getDescription();

    public function getMinQty();

    public function getMaxQty();

    public function getQtyStep();

    public function getCustomer();

    /**
     * @param Customer $customer
     * @return mixed
     */
    public function setCustomer(Customer $customer);

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     * @return mixed
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager);

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     * @return mixed
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager);

    /**
     * @param IVATManager $VATManager
     * @return mixed
     */
    public function removeAllAppliedDiscounts(IVATManager $VATManager);

    public function getPriceSourceType();

    /**
     * @param $sourceType
     * @return mixed
     */
    public function setPriceSourceType($sourceType);

    public function getPriceSourceID();

    /**
     * @param $id
     * @return mixed
     */
    public function setPriceSourceID($id);

    public function getQtySourceType();

    /**
     * @param $sourceType
     * @return mixed
     */
    public function setQtySourceType($sourceType);

    public function getQtySourceID();

    /**
     * @param $id
     * @return mixed
     */
    public function setQtySourceID($id);

    public function getAppliedLineDiscounts();

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount);

    /**
     * @return ItemPriceDataInterface
     */
    public function getPriceData();

    /**
     * @param $isAccessible
     * @return mixed
     */
    public function setQtyAccessibility($isAccessible);

    /**
     * @param $isAccessible
     * @return mixed
     */
    public function setUnitPriceAccessibility($isAccessible);

    public function isQtyProtected();

    public function isUnitPriceProtected();

    public function getCrossPrice();

    public function isOrderable();

    public function isAvailable();

    public function isUnavailable();

    public function getAvailability();

    public function getInventory();

    public function getBasketEntityLink();

    public function getShippingClass();

    public function getCurrencyCode();

}