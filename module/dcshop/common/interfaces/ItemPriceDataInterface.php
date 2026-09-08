<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\GenericLineDiscount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/26/2015
 * Time: 4:53 PM
 */
interface ItemPriceDataInterface
{
    /**
     * @return float
     */
    public function getVATPercent();

    /**
     * @param float $VATPercent
     */
    public function setVATPercent($VATPercent);

    /**
     * @return float
     */
    public function getVATAmount();

    /**
     * @param float $VATAmount
     */
    public function setVATAmount($VATAmount);

    /**
     * @return float
     */
    public function getPrice();

    public function getUnitPrice();

    /**
     * @return float
     */
    public function getPriceSource();

    public function getQtySource();

    public function setPriceSourcePriceLine();

    public function setPriceSourceItemPrice();

    public function setPriceSourceCoupon();

    public function setPriceSourceSubscriptionFixPrice();

    public function setPriceSourceSubscriptionPricePerBillingInterval();

    public function setPriceSourceRule();

    public function setQtySourceUser();

    public function setQtySourceSubscription();

    public function setQtySourceCoupon();

    public function setQtySourceRule();

    /**
     * @return mixed
     */
    public function getPriceSourceID();

    /**
     * @param mixed $priceSourceID
     */
    public function setPriceSourceID($priceSourceID);

    public function getQtySourceID();

    /**
     * @param $qtySourceID
     * @return mixed
     */
    public function setQtySourceID($qtySourceID);

    /**
     * @return mixed
     */
    public function getVATAdjustmentStatus();

    public function setVATAdjustmentStatusUnadjusted();

    public function setVATAdjustmentStatusVATAdded();

    public function setVATAdjustmentStatusVATSubtracted();

    /**
     * @return string
     */
    public function getDiscountStatus();

    public function setDiscountStatusNotDiscounted();

    public function setDiscountStatusLineDiscountApplied();

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * @param float $percent
     */
    public function setDiscountPercent($percent);

    /**
     * @param boolean $priceIncludesVAT
     */
    public function setPriceIncludesVAT($priceIncludesVAT);

    /**
     * @param boolean $priceAllowsLineDiscount
     */
    public function setPriceAllowsLineDiscount($priceAllowsLineDiscount);

    /**
     * @param boolean $priceAllowsInvoiceDiscount
     */
    public function setPriceAllowsInvoiceDiscount($priceAllowsInvoiceDiscount);

    /**
     * @param mixed $discountAmount
     */
    public function setDiscountAmount($discountAmount);

    public function getFormattedCustomerPrice();

    public function getFormattedCrossPrice();

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

    public function getQtySourceType();

    /**
     * @param $sourceType
     * @return mixed
     */
    public function setQtySourceType($sourceType);

    public function getAppliedLineDiscounts();

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount);

    public function getCustomerPrice();

    public function __toString();

    public function __invoke();

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedPriceSourceType($sourceType);

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedDiscountSourceType($sourceType);

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedQtySourceType($sourceType);

    /**
     * @param $newQty
     * @return mixed
     */
    public function updateQuantityAndLineAmountWithoutUnitPrice($newQty);

    public function getCrossPrice();

    public function getItemVATProdPostingGroup();

    public function getCurrencyCode();

}