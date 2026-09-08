<?php
namespace DynCom\dc\dcShop\abstracts;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/26/2015
 * Time: 3:43 PM
 */
abstract class ItemPriceDataDecorator implements ItemPriceDataInterface
{

    protected $itemPriceData;

    /**
     * ItemPriceDataDecorator constructor.
     * @param ItemPriceData $itemPriceData
     */
    public function __construct(ItemPriceData $itemPriceData) {
        $this->itemPriceData = $itemPriceData;
    }

    /**
     * @return float
     */
    public function getVATPercent() {
        return $this->itemPriceData->VATPercent;
    }

    /**
     * @param float $VATPercent
     */
    public function setVATPercent( $VATPercent ) {
        $this->itemPriceData->setVATPercent($VATPercent);
    }

    /**
     * @return float
     */
    public function getVATAmount() {
        return $this->itemPriceData->VATAmount;
    }

    /**
     * @param float $VATAmount
     */
    public function setVATAmount( $VATAmount ) {
        $this->itemPriceData->setVATAmount($VATAmount);
    }

    /**
     * @return float
     */
    public function getPriceSource() {
        return $this->itemPriceData->priceSource;
    }


    public function setPriceSourcePriceLine() {
        $this->itemPriceData->setPriceSourcePriceLine();
    }

    public function setPriceSourceItemPrice() {
        $this->itemPriceData->setPriceSourceItemPrice();
    }

    /**
     * @return mixed
     */
    public function getPriceSourceID() {
        return $this->itemPriceData->priceSourceID;
    }

    /**
     * @param mixed $priceSourceID
     */
    public function setPriceSourceID( $priceSourceID ) {
        $this->itemPriceData->setPriceSourceID($priceSourceID);
    }

    /**
     * @return mixed
     */
    public function getVATAdjustmentStatus() {
        return $this->itemPriceData->VATAdjustmentStatus;
    }

    public function setVATAdjustmentStatusUnadjusted() {
        $this->itemPriceData->setVATAdjustmentStatusUnadjusted();
    }

    public function setVATAdjustmentStatusVATAdded() {
        $this->itemPriceData->setVATAdjustmentStatusVATAdded();
    }

    public function setVATAdjustmentStatusVATSubtracted() {
        $this->itemPriceData->setVATAdjustmentStatusVATSubtracted();
    }

    /**
     * @return string
     */
    public function getDiscountStatus() {
        return $this->itemPriceData->discountStatus;
    }

    public function setDiscountStatusNotDiscounted() {
        $this->itemPriceData->setDiscountStatusNotDiscounted();
    }

    public function setDiscountStatusLineDiscountApplied() {
        $this->itemPriceData->setDiscountStatusLineDiscountApplied();
    }

	/**
	* @return float
	*/
	public function getUnitPrice()
	{
		return $this->itemPriceData->getCustomerPrice();
	}

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice( $unitPrice ) {
        $this->itemPriceData->setPrice($unitPrice);
    }

    /**
     * @param float $percent
     */
    public function setDiscountPercent($percent) {
        $this->itemPriceData->setDiscountPercent($percent);
    }

    /**
     * @param boolean $priceIncludesVAT
     */
    public function setPriceIncludesVAT( $priceIncludesVAT ) {
        $this->itemPriceData->setPriceIncludesVAT($priceIncludesVAT);
    }

    /**
     * @param boolean $priceAllowsLineDiscount
     */
    public function setPriceAllowsLineDiscount( $priceAllowsLineDiscount ) {
        $this->itemPriceData->setPriceAllowsLineDiscount($priceAllowsLineDiscount);
    }

    /**
     * @param boolean $priceAllowsInvoiceDiscount
     */
    public function setPriceAllowsInvoiceDiscount( $priceAllowsInvoiceDiscount ) {
        $this->itemPriceData->setPriceAllowsInvoiceDiscount($priceAllowsInvoiceDiscount);
    }

    /**
     * @param mixed $discountAmount
     */
    public function setDiscountAmount( $discountAmount ) {
        $this->itemPriceData->setDiscountAmount($discountAmount);
    }


    /**
     * @return mixed
     */
    public function __toString() {
        return $this->itemPriceData->__toString();
    }

    /**
     * @return mixed
     */
    public function __invoke() {
        return $this->itemPriceData->__invoke();
    }

    /**
     * @param $price
     * @return mixed|string
     */
    protected function _formatPrice($price) {
        if($this->itemPriceData->currencyCode === '' || $this->itemPriceData->currencyCode === 'EUR' || is_null($this->itemPriceData->currencyCode)) {
            $priceStr = number_format((float)$price,2,',','.') . ' €';
            if(substr($priceStr,-7,3) === ',00') $priceStr = str_replace(',00',',-',$priceStr);
            return $priceStr;
        } else {
            return $this->itemPriceData->currencyCode . ' ' . number_format((float)$price,2,'.',',');
        }
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->itemPriceData->getPrice();
    }

    /**
     * @return mixed
     */
    public function getQtySource()
    {
        return $this->itemPriceData->getQtySource();
    }

    public function setPriceSourceCoupon()
    {
        $this->itemPriceData->setPriceSourceCoupon();
    }

    public function setPriceSourceSubscriptionFixPrice()
    {
        $this->itemPriceData->setPriceSourceSubscriptionFixPrice();
    }

    public function setPriceSourceSubscriptionPricePerBillingInterval()
    {
        $this->itemPriceData->setPriceSourceSubscriptionPricePerBillingInterval();
    }

    public function setPriceSourceRule()
    {
        $this->itemPriceData->setPriceSourceRule();
    }

    public function setQtySourceUser()
    {
        $this->itemPriceData->setQtySourceUser();
    }

    public function setQtySourceSubscription()
    {
        $this->itemPriceData->setQtySourceSubscription();
    }

    public function setQtySourceCoupon()
    {
        $this->itemPriceData->setQtySourceCoupon();
    }

    public function setQtySourceRule()
    {
        $this->itemPriceData->setQtySourceRule();
    }

    /**
     * @return mixed
     */
    public function getQtySourceID()
    {
        return $this->itemPriceData->getQtySourceID();
    }

    /**
     * @param $qtySourceID
     */
    public function setQtySourceID($qtySourceID)
    {
        $this->itemPriceData->setQtySourceID($qtySourceID);
    }

    /**
     * @param $price
     */
    public function setPrice($price)
    {
        $this->itemPriceData->setPrice($price);
    }

    /**
     * @return mixed
     */
    public function getFormattedCustomerPrice()
    {
        return $this->itemPriceData->getFormattedCustomerPrice();
    }

    /**
     * @return mixed
     */
    public function getFormattedCrossPrice()
    {
        return $this->itemPriceData->getFormattedCrossPrice();
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->itemPriceData->applyDiscount($discount,$VATManager);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->itemPriceData->removeDiscount($discount,$VATManager);
    }

    /**
     * @param IVATManager $VATManager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATManager)
    {
        $this->itemPriceData->removeAllAppliedDiscounts($VATManager);
    }

    /**
     * @return mixed
     */
    public function getPriceSourceType()
    {
        return $this->itemPriceData->getPriceSourceType();
    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        $this->itemPriceData->setPriceSourceType($sourceType);
    }

    /**
     * @return mixed
     */
    public function getQtySourceType()
    {
        return $this->itemPriceData->getQtySourceType();
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        $this->itemPriceData->setQtySourceType($sourceType);
    }

    /**
     * @return mixed
     */
    public function getAppliedLineDiscounts()
    {
        return $this->itemPriceData->getAppliedLineDiscounts();
    }

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        return $this->itemPriceData->isDiscountApplied($discount);
    }

    /**
     * @return mixed
     */
    public function getCustomerPrice()
    {
        return $this->itemPriceData->getCustomerPrice();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $className = __CLASS__;
        $decoratedClass = 'ItemPriceData';
        if (property_exists($className,$name)) {
            return $this->$name;
        } elseif (property_exists($decoratedClass,$name)) {
            return $this->itemPriceData->$name;
        } else {
            throw new \DomainException('No field with name ' . $name . ' exists in ' . $className . ' or ' . $decoratedClass);
        }
    }

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedPriceSourceType($sourceType)
    {
        return ItemPriceData::isAllowedPriceSourceType($sourceType);
    }

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedDiscountSourceType($sourceType)
    {
        return ItemPriceData::isAllowedDiscountSourceType($sourceType);
    }

    /**
     * @param $sourceType
     * @return mixed
     */
    public static function isAllowedQtySourceType($sourceType)
    {
        return ItemPriceData::isAllowedQtySourceType($sourceType);
    }

    /**
     * @param $newQty
     */
    public function updateQuantityAndLineAmountWithoutUnitPrice($newQty)
    {
        $this->itemPriceData->updateQuantityAndLineAmountWithoutUnitPrice($newQty);
    }

    /**
     * @return mixed
     */
    public function getCrossPrice()
    {
        return $this->itemPriceData->getCrossPrice();
    }

    /**
     * @return mixed
     */
    public function getItemVATProdPostingGroup()
    {
        return $this->itemPriceData->getItemVATProdPostingGroup();
    }

    public function getCurrencyCode()
    {
        return $this->itemPriceData->getCurrencyCode();
    }


}