<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 13:38
 */
class OrderCouponDTO
{

    const COUPON_TYPE_AMOUNT = 0;
    const COUPON_TYPE_PERCENT = 1;
    const COUPON_TYPE_INDIVIDUAL_AMOUNT = 2;
    const COUPON_TYPE_SPECIAL_SHIPPING = 3;

    /**
     * @var CouponHeader
     */
    protected $couponHeader;
    /**
     * @var bool
     */
    protected $couponIsTypeAmount = false;
    /**
     * @var bool
     */
    protected $couponTypeIsPercent = false;
    /**
     * @var bool
     */
    protected $couponTypeIsIndividualAmount = false;
    /**
     * @var bool
     */
    protected $couponTypeIsSpecialShipping = false;

    /**
     * @var bool
     */
    protected $couponTypeIsFreeItem = false;
    /**
     * @var CouponLine
     */
    protected $couponLine;
    /**
     * @var bool
     */
    protected $isValueCoupon;
    /**
     * @var string
     */
    protected $couponCode;
    /**
     * @var float
     */
    protected $amountToApply;

    /**
     * @var float
     */
    protected $percentToApply;

    /**
     * @var float
     */
    protected $amountLeft;

    /**
     * @var WebshopItemInterface
     */
    protected $item;

    /**
     * OrderCouponDTO constructor.
     * @param CouponHeader $couponHeader
     * @param CouponLine|null $couponLine
     * @param WebshopItemInterface|null $item
     */
    public function __construct(CouponHeader $couponHeader, CouponLine $couponLine = null, WebshopItemInterface $item = null)
    {
        $this->isValueCoupon = (bool)$couponHeader->value_coupon;
        $this->couponCode = $couponHeader->code;
        $couponType = $this->couponHeader->coupon_type;
        switch ($couponType) {
            case 0:
                $this->couponIsTypeAmount = true;
                $this->amountToApply = $couponHeader->amount;
                break;
            case 1:
                $this->couponTypeIsPercent = true;
                $this->percentToApply = $couponHeader->percentage;
                break;
            case 2:
                $this->isCouponTypeFreeItem = true;
                $this->item = $item;
                break;
            case 3:
                $this->couponTypeIsIndividualAmount = true;
                $this->amountToApply = $this->isValueCoupon ? $couponLine->amount_left : $couponLine->amount;
                break;
            case 4:
                $this->couponTypeIsSpecialShipping = true;
                break;
            default:
                break;
        }
    }

    /**
     * @return boolean
     */
    public function isCouponTypeAmount()
    {
        return $this->couponIsTypeAmount;
    }

    /**
     * @return boolean
     */
    public function isCouponTypePercent()
    {
        return $this->couponTypeIsPercent;
    }

    /**
     * @return boolean
     */
    public function isCouponTypeIndividualAmount()
    {
        return $this->couponTypeIsIndividualAmount;
    }

    /**
     * @return boolean
     */
    public function isCouponTypeSpecialShipping()
    {
        return $this->couponTypeIsSpecialShipping;
    }

    /**
     * @return bool
     */
    public function isCouponTypeFreeItem()
    {
        return $this->couponTypeIsFreeItem;
    }

    /**
     * @return mixed
     */
    public function getIsValueCoupon()
    {
        return $this->isValueCoupon;
    }

    /**
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @return mixed
     */
    public function getAmountToApply()
    {
        return $this->amountToApply;
    }

    /**
     * @return WebshopItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return float
     */
    public function getAmountLeft()
    {
        return $this->amountLeft;
    }


}