<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 09.10.2015
 * Time: 10:18
 */
class BasketValueSource
{

    use universallyGettableTrait;

    private $id;
    private $applied_to_header_id;
    private $applied_to_line_id;
    private $created_by_source_type;
    private $created_by_source_id;
    private $qty_set_by_source_type;
    private $qty_set_by_source_id;
    private $qty_before_setting;
    private $qty_after_setting;
    private $unit_price_set_by_source_type;
    private $unit_price_set_by_source_id;
    private $unit_price_before_setting;
    private $unit_price_after_setting;
    private $invoice_discount_source_type;
    private $invoice_discount_source_id;
    private $invoice_discount_applied;
    private $basket_total_before_invoice_discount;
    private $basket_total_after_invoice_discount;
    private $create_timestamp;
    private $modify_timestamp;
    private $creation_notification;
    private $price_notification;
    private $qty_notification;
    private $to_delete;
    /**
     * @var CouponLine|null
     */
    protected $couponLine;

    /**
     * @return mixed
     */
    public function getCreationNotification()
    {
        return $this->creation_notification;
    }

    /**
     * @param mixed $creation_notification
     */
    public function setCreationNotification($creation_notification)
    {
        $this->creation_notification = $creation_notification;
    }

    /**
     * @return mixed
     */
    public function getPriceNotification()
    {
        return $this->price_notification;
    }

    /**
     * @param mixed $price_notification
     */
    public function setPriceNotification($price_notification)
    {
        $this->price_notification = $price_notification;
    }

    /**
     * @return mixed
     */
    public function getQtyNotification()
    {
        return $this->qty_notification;
    }

    /**
     * @param mixed $qty_notification
     */
    public function setQtyNotification($qty_notification)
    {
        $this->qty_notification = $qty_notification;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return (int)$this->id;
    }

    /**
     * @return int
     */
    public function getAppliedToHeaderID()
    {
        return (int)$this->applied_to_header_id;
    }

    /**
     * @param mixed $applied_to_header_id
     */
    public function setAppliedToHeaderId($applied_to_header_id)
    {
        $this->applied_to_header_id = $applied_to_header_id;
    }

    /**
     * @return int
     */
    public function getAppliedToLineID()
    {
        return (int)$this->applied_to_line_id;
    }

    /**
     * @param mixed $applied_to_line_id
     */
    public function setAppliedToLineId($applied_to_line_id)
    {
        $this->applied_to_line_id = $applied_to_line_id;
    }

    public function getCreatedBySourceType()
    {
        return $this->created_by_source_type;
    }

    /**
     * @param mixed $created_by_source_type
     */
    public function setCreatedBySourceType($created_by_source_type)
    {
        $this->created_by_source_type = $created_by_source_type;
    }

    /**
     * @return int
     */
    public function getCreatedBySourceID()
    {
        return (int)$this->created_by_source_id;
    }

    /**
     * @param mixed $created_by_source_id
     */
    public function setCreatedBySourceId($created_by_source_id)
    {
        $this->created_by_source_id = $created_by_source_id;
    }

    public function getQtySetBySourceType()
    {
        return $this->qty_set_by_source_type;
    }

    /**
     * @param mixed $qty_set_by_source_type
     */
    public function setQtySetBySourceType($qty_set_by_source_type)
    {
        $this->qty_set_by_source_type = $qty_set_by_source_type;
    }

    /**
     * @return int
     */
    public function getQtySetBySourceID()
    {
        return (int)$this->qty_set_by_source_id;
    }

    /**
     * @param mixed $qty_set_by_source_id
     */
    public function setQtySetBySourceId($qty_set_by_source_id)
    {
        $this->qty_set_by_source_id = $qty_set_by_source_id;
    }

    /**
     * @return float
     */
    public function getQtyBeforeSetting()
    {
        return (float)$this->qty_before_setting;
    }

    /**
     * @param mixed $qty_before_setting
     */
    public function setQtyBeforeSetting($qty_before_setting)
    {
        $this->qty_before_setting = $qty_before_setting;
    }

    /**
     * @return float
     */
    public function getQtyAfterSetting()
    {
        return (float)$this->qty_after_setting;
    }

    /**
     * @param mixed $qty_after_setting
     */
    public function setQtyAfterSetting($qty_after_setting)
    {
        $this->qty_after_setting = $qty_after_setting;
    }

    public function getUnitPriceSetBySourceType()
    {
        return $this->unit_price_set_by_source_type;
    }

    /**
     * @param mixed $unit_price_set_by_source_type
     */
    public function setUnitPriceSetBySourceType($unit_price_set_by_source_type)
    {
        $this->unit_price_set_by_source_type = $unit_price_set_by_source_type;
    }

    /**
     * @return int
     */
    public function getUnitPriceSetBySourceID()
    {
        return (int)$this->unit_price_set_by_source_id;
    }

    /**
     * @param mixed $unit_price_set_by_source_id
     */
    public function setUnitPriceSetBySourceId($unit_price_set_by_source_id)
    {
        $this->unit_price_set_by_source_id = $unit_price_set_by_source_id;
    }

    public function getUnitPriceBeforeSetting()
    {
        return $this->unit_price_before_setting;
    }

    /**
     * @param mixed $unit_price_before_setting
     */
    public function setUnitPriceBeforeSetting($unit_price_before_setting)
    {
        $this->unit_price_before_setting = $unit_price_before_setting;
    }

    public function getUnitPriceAfterSetting()
    {
        return $this->unit_price_after_setting;
    }

    /**
     * @param mixed $unit_price_after_setting
     */
    public function setUnitPriceAfterSetting($unit_price_after_setting)
    {
        $this->unit_price_after_setting = $unit_price_after_setting;
    }

    public function getInvoiceDiscountSourceType()
    {
        return $this->invoice_discount_source_type;
    }

    /**
     * @param mixed $invoice_discount_source_type
     */
    public function setInvoiceDiscountSourceType($invoice_discount_source_type)
    {
        $this->invoice_discount_source_type = $invoice_discount_source_type;
    }

    /**
     * @return int
     */
    public function getInvoiceDiscountSourceID()
    {
        return (int)$this->invoice_discount_source_id;
    }

    /**
     * @param mixed $invoice_discount_source_id
     */
    public function setInvoiceDiscountSourceId($invoice_discount_source_id)
    {
        $this->invoice_discount_source_id = $invoice_discount_source_id;
    }

    /**
     * @return float
     */
    public function getInvoiceDiscountApplied()
    {
        return (float)$this->invoice_discount_applied;
    }

    /**
     * @param mixed $invoice_discount_applied
     */
    public function setInvoiceDiscountApplied($invoice_discount_applied)
    {
        $this->invoice_discount_applied = $invoice_discount_applied;
    }

    public function getBasketTotalBeforeInvoiceDiscount()
    {
        return $this->basket_total_before_invoice_discount;
    }

    /**
     * @param mixed $basket_total_before_invoice_discount
     */
    public function setBasketTotalBeforeInvoiceDiscount($basket_total_before_invoice_discount)
    {
        $this->basket_total_before_invoice_discount = $basket_total_before_invoice_discount;
    }

    public function getBasketTotalAfterInvoiceDiscount()
    {
        return $this->basket_total_after_invoice_discount;
    }

    /**
     * @param mixed $basket_total_after_invoice_discount
     */
    public function setBasketTotalAfterInvoiceDiscount($basket_total_after_invoice_discount)
    {
        $this->basket_total_after_invoice_discount = $basket_total_after_invoice_discount;
    }

    public function getCreateTimestamp()
    {
        return $this->create_timestamp;
    }

    public function getModifyTimestamp()
    {
        return $this->modify_timestamp;
    }

    public function getToDelete()
    {
        return $this->to_delete;
    }

    public function setCouponLine(CouponLine $couponLine)
    {
        if ($this->unit_price_set_by_source_type !== GenericLineDiscount::DISCOUNT_SOURCE_TYPE_COUPON || (int)$this->unit_price_set_by_source_id !== (int)$couponLine->getID()) {
            throw new \DomainException("Cannot set a coupon line on a value source that has a non-matching unit-price-source type or id.");
        }
        $this->couponLine = $couponLine;
    }

    public function getCouponLine(): ?CouponLine
    {
        return $this->couponLine;
    }


}