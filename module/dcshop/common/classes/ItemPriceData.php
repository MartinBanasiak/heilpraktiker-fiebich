<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use NumberFormatter;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.03.2015
 * Time: 10:17
 */
class ItemPriceData implements ItemPriceDataInterface
{

    use universallyGettableTrait;

    const PRICE_SOURCE_PRICE_LINES = 'PRICE_SOURCE_PRICE_LINES';
    const PRICE_SOURCE_ITEM_PRICE = 'PRICE_SOURCE_ITEM_RECORD';
    const PRICE_SOURCE_RULE = 'PRICE_SOURCE_RULE';
    const PRICE_SOURCE_COUPON = 'PRICE_SOURCE_COUPON';
    const PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE = 'PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE';
    const PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL = 'PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL';
	const PRICE_SOURCE_CUSTOMIZATION = 'PRICE_SOURCE_CUSTOMIZATION';

    const QTY_SOURCE_USER = 'QTY_SOURCE_USER';
    const QTY_SOURCE_SUBSCRIPTION = 'QTY_SOURCE_SUBSCRIPTION';
    const QTY_SOURCE_COUPON = 'QTY_SOURCE_COUPON';
    const QTY_SOURCE_RULE = 'QTY_SOURCE_RULE';

    const VAT_ADJUSTMENT_STATUS_UNADJUSTED = 'PRICE_NOT_VAT_ADJUSTED';
    const VAT_ADJUSTMENT_STATUS_VAT_ADDED = 'PRICE_VAT_ADDED';
    const VAT_ADJUSTMENT_STATUS_VAT_SUBTRACTED = 'PRICE_VAT_SUBTRACTED';

    const DISCOUNT_STATUS_NOT_DISCOUNTED = 'NO_DISCOUNT_APPLIED';
    const DISCOUNT_STATUS_LINE_DISC_APPLIED = 'LINE_DISCOUNT_APPLIED';

    private static $allowedPriceSources = [
        self::PRICE_SOURCE_ITEM_PRICE,
        self::PRICE_SOURCE_PRICE_LINES,
        self::PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE,
        self::PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL,
        self::PRICE_SOURCE_RULE,
        self::PRICE_SOURCE_COUPON,
		self::PRICE_SOURCE_CUSTOMIZATION,
    ];

    private static $allowedQtySources = [
        self::QTY_SOURCE_USER,
        self::QTY_SOURCE_COUPON,
        self::QTY_SOURCE_SUBSCRIPTION,
        self::QTY_SOURCE_RULE,
    ];

    private static $allowedDiscountSources = [
        self::DISCOUNT_STATUS_LINE_DISC_APPLIED
    ];

    protected $itemID;
    protected $itemCompany;
    protected $itemNo;
    protected $itemVATProdPostingGroup;
    protected $priceVATBusPostingGroup;
    protected $qty;
    protected $price;
    protected $unitPrice;
    protected $priceIncludesVAT;
    protected $priceAllowsLineDiscount;
    protected $priceAllowsInvoiceDiscount;
    protected $VATPercent;
    protected $VATAmount;
    protected $currencyCode;
    protected $crossPrice = 0.00;

    protected $priceSource;
    protected $priceSourceID;
    protected $VATAdjustmentStatus;

    protected $appliedDiscounts;

    protected $discountStatus;
    protected $discountAmount;
    protected $discountPercent;
    protected $discountSourceID;

    protected $qtySource = self::QTY_SOURCE_USER;
    protected $qtySourceID;

    protected $allowsLineDiscount;

    /**
     * ItemPriceData constructor.
     * @param $itemID
     * @param $itemCompany
     * @param $itemNo
     * @param $itemVATProdPostingGroup
     * @param $qty
     * @param $unitPrice
     * @param $priceIncludesVAT
     * @param $priceAllowsLineDiscount
     * @param $priceAllowsInvoiceDiscount
     * @param $currencyCode
     * @param $priceVATBusPostingGroup
     */
    public function __construct(
        $itemID,
        $itemCompany,
        $itemNo,
        $itemVATProdPostingGroup,
        $qty,
        $unitPrice,
        $priceIncludesVAT,
        $priceAllowsLineDiscount,
        $priceAllowsInvoiceDiscount,
        $currencyCode,
        $priceVATBusPostingGroup = '')
    {

        $this->itemID = (int)$itemID;
        $this->itemCompany = (string)$itemCompany;
        $this->itemNo = (string)$itemNo;
        $this->itemVATProdPostingGroup = (string)$itemVATProdPostingGroup;
        $this->priceVATBusPostingGroup = (string)$priceVATBusPostingGroup !== '' ? (string)$priceVATBusPostingGroup : (string)$itemVATProdPostingGroup;
        $this->unitPrice = (float)$unitPrice;
        $this->price = $this->unitPrice * $qty;
        $this->priceIncludesVAT = (bool)$priceIncludesVAT;
        $this->priceAllowsLineDiscount = (bool)$priceAllowsLineDiscount;
        $this->priceAllowsInvoiceDiscount = (bool)$priceAllowsLineDiscount;
        $this->currencyCode = (string)$currencyCode;

        $this->appliedDiscounts = [];

        $this->setVATAdjustmentStatusUnadjusted();
        $this->setDiscountStatusNotDiscounted();

        $this->itemID = $itemID;
        $this->qty = $qty;

        $this->unitPrice = ($this->price / $this->qty);
    }

    /**
     * @return float
     */
    public function getVATPercent()
    {
        return $this->VATPercent;
    }

    /**
     * @param float $VATPercent
     */
    public function setVATPercent($VATPercent)
    {
        $this->VATPercent = (float)$VATPercent;
    }

    /**
     * @return float
     */
    public function getVATAmount()
    {
        return $this->VATAmount;
    }

    /**
     * @param float $VATAmount
     */
    public function setVATAmount($VATAmount)
    {
        $this->VATAmount = (float)$VATAmount;
    }

    /**
     * @return string
     */
    public function getPriceSource()
    {
        return $this->priceSource;
    }


    public function setPriceSourcePriceLine()
    {
        $this->priceSource = static::PRICE_SOURCE_PRICE_LINES;
    }

    public function setPriceSourceItemPrice()
    {
        $this->priceSource = static::PRICE_SOURCE_ITEM_PRICE;
    }

    public function setPriceSourceSubscriptionFixPrice()
    {
        $this->priceSource = static::PRICE_SOURCE_SUBSCRIPTION_FIX_PRICE;
    }

    public function setPriceSourceSubscriptionPricePerBillingInterval()
    {
        $this->priceSource = static::PRICE_SOURCE_SUBSCRIPTION_PRICE_PER_BILLING_INTERVAL;
    }

    public function setPriceSourceRule()
    {
        $this->priceSource = static::PRICE_SOURCE_RULE;
    }
	
	public function setPriceSourceCustomization()
	{
		$this->priceSource = static::PRICE_SOURCE_CUSTOMIZATION;
	}


    /**
     * @return mixed
     */
    public function getPriceSourceID()
    {
        return $this->priceSourceID;
    }

    /**
     * @param mixed $priceSourceID
     */
    public function setPriceSourceID($priceSourceID)
    {
        $this->priceSourceID = (int)$priceSourceID;
    }

    /**
     * @return mixed
     */
    public function getVATAdjustmentStatus()
    {
        return $this->VATAdjustmentStatus;
    }

    public function setVATAdjustmentStatusUnadjusted()
    {
        $this->VATAdjustmentStatus = static::VAT_ADJUSTMENT_STATUS_UNADJUSTED;
    }

    public function setVATAdjustmentStatusVATAdded()
    {
        $this->VATAdjustmentStatus = static::VAT_ADJUSTMENT_STATUS_VAT_ADDED;
    }

    public function setVATAdjustmentStatusVATSubtracted()
    {
        $this->VATAdjustmentStatus = static::VAT_ADJUSTMENT_STATUS_VAT_SUBTRACTED;
    }

    /**
     * @return string
     */
    public function getDiscountStatus()
    {
        return $this->discountStatus;
    }

    public function setDiscountStatusNotDiscounted()
    {
        $this->discountStatus = static::DISCOUNT_STATUS_NOT_DISCOUNTED;
    }

    public function setDiscountStatusLineDiscountApplied()
    {
        $this->discountStatus = static::DISCOUNT_STATUS_LINE_DISC_APPLIED;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;
        if($this->price == 0.00) {
            $this->unitPrice = 0.00;
            $this->VATAmount = 0.00;
        } else {
            $this->unitPrice = ($this->price / $this->qty);
        }

    }

    /**
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = (float)$unitPrice;
        if ($this->unitPrice == 0.00) {
            $this->price = 0.00;
            $this->VATAmount = 0.00;
        } else {
            $this->price = $this->unitPrice * $this->qty;
        }
    }

    /**
     * @param float $percent
     */
    public function setDiscountPercent($percent)
    {
        $this->discountPercent = (float)$percent;
    }

    /**
     * @param boolean $priceIncludesVAT
     */
    public function setPriceIncludesVAT($priceIncludesVAT)
    {
        $this->priceIncludesVAT = (bool)$priceIncludesVAT;
    }

    /**
     * @param boolean $priceAllowsLineDiscount
     */
    public function setPriceAllowsLineDiscount($priceAllowsLineDiscount)
    {
        $this->priceAllowsLineDiscount = (bool)$priceAllowsLineDiscount;
    }

    /**
     * @param boolean $priceAllowsInvoiceDiscount
     */
    public function setPriceAllowsInvoiceDiscount($priceAllowsInvoiceDiscount)
    {
        $this->priceAllowsInvoiceDiscount = (bool)$priceAllowsInvoiceDiscount;
    }

    /**
     * @param float $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = (float)$discountAmount;
    }

    /**
     * @param int $id
     */
    public function setDiscountSourceID($id)
    {
        $this->discountSourceID = (int)$id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_formatPrice($this->price);
    }

    /**
     * @return float|int
     */
    public function __invoke()
    {
        return $this->price;
    }

    /**
     * @param $price
     * @return mixed|string
     */
    protected function _formatPrice($price,$locale = 'de-DE')
    {
        $currencyCode = $this->currencyCode ?? 'EUR';
        $locale = $locale ?? 'de-DE';
        if ('EUR' === $currencyCode) {
            $formatter = NumberFormatter::create($locale,NumberFormatter::CURRENCY);
            $priceStr = $formatter->formatCurrency($price,$currencyCode);
            if (substr($priceStr, -7, 3) === ',00') {
                $priceStr = str_replace(',00', ',-', $priceStr);
            }
        } else {
            $formatter = NumberFormatter::create($locale,NumberFormatter::CURRENCY);
            $priceStr = $formatter->formatCurrency($price,$currencyCode);
        }
        return $priceStr;
    }

    /**
     * @return mixed
     */
    public function getItemID()
    {
        return $this->itemID;
    }

    /**
     * @param $price
     */
    public function setCrossPrice($price)
    {
        $this->crossPrice = (float)$price;
    }

    /**
     * @return mixed|string
     */
    public function getFormattedCustomerPrice()
    {
        return $this->_formatPrice($this->price);
    }

    /**
     * @return mixed|string
     */
    public function getFormattedCrossPrice()
    {
        return $this->_formatPrice($this->crossPrice);
    }

    /**
     * @return string
     */
    public function getItemVATProdPostingGroup()
    {
        return $this->itemVATProdPostingGroup;
    }

    /**
     * @return string
     */
    public function getPriceVATBusPostingGroup()
    {
        return $this->priceVATBusPostingGroup;
    }

    /**
     * @return string
     */
    public function getVATCode()
    {
        return $this->itemVATProdPostingGroup;
    }

    /**
     * @return float
     */
    public function getCustomerPrice()
    {
        return (float)$this->price;
    }

    /**
     * Applies a discount by undoing potential VAT-adjustment
     * then calculating and applying discount based on those values,
     * and then re-applying (potential) VAT-adjustment
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $discountSourceType = $discount->getSourceType();

        if($discountSourceType === DiscountBase::DISCOUNT_SOURCE_TYPE_LINE_DISCOUNT && !$this->priceAllowsLineDiscount) {
            throw new \InvalidArgumentException('This price does not allow for the application of NAV line disocunts.');
        }

        $key = $discountSourceType . '|' . $discount->getSourceID();
        //ONLY RULE DISCOUNTS MAY BE MULTIPLY APPLIED
        //TO THE SAME ITEM
        if(($discountSourceType !== GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE) &&
            (array_key_exists($key,$this->appliedDiscounts))) {
            return;
        }
        //Undo VAT Adjustment
        $oldPrice = $this->price;
        if (!$this->priceIncludesVAT && $this->getVATAdjustmentStatus() === self::VAT_ADJUSTMENT_STATUS_VAT_ADDED) {
            $oldPrice -= $VATManager->getIncludedTaxAmount($oldPrice, $this->VATPercent);
        } elseif ($this->priceIncludesVAT && $this->getVATAdjustmentStatus() === self::VAT_ADJUSTMENT_STATUS_VAT_SUBTRACTED) {
            $oldPrice += $VATManager->getTaxAmountToAdd($oldPrice, $this->VATPercent);
        }
		
		$oldDiscount = clone $discount;
        $appliedDiscount = new AppliedDiscount($oldPrice, $discount);
		$newPrice = $appliedDiscount->getPriceAfterApplication();
		if ($newPrice !== $oldPrice) {			
			$this->appliedDiscounts[$key] = $appliedDiscount;
			$this->setPrice($newPrice);
			//Redo VAT Adjustment
			$this->setVATAdjustmentStatusUnadjusted();
			$VATManager->doVATCalculationOnItemPrice($this);
		}
    }

    /** Removes discount by unsetting potential VAT-Adjustment,
     *  re-adding discounted gross amount and recalculating VAT
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $key = $discount->getSourceType() . '|' . $discount->getSourceID();
        $oldPrice = $this->price;
        //If price is VAT-adjusted, reset for recalculation before applying discount
        if (!$this->priceIncludesVAT && $this->getVATAdjustmentStatus() === self::VAT_ADJUSTMENT_STATUS_VAT_ADDED) {
            $oldPrice -= $VATManager->getIncludedTaxAmount($oldPrice, $this->VATPercent);
        } elseif ($this->priceIncludesVAT && $this->getVATAdjustmentStatus() === self::VAT_ADJUSTMENT_STATUS_VAT_SUBTRACTED) {
            $oldPrice += $VATManager->getTaxAmountToAdd($oldPrice, $this->VATPercent);
        }
        if (!array_key_exists($key, $this->appliedDiscounts)) {
            throw new \InvalidArgumentException('The discount is not among the discounts applied to this price.');
        }
        $appliedDiscount = $this->appliedDiscounts[$key];
        $newPrice = $oldPrice + $appliedDiscount->getDiscountedAmount();
        $this->setPrice($newPrice);
        unset($this->appliedDiscounts[$key]);
        $VATManager->doVATCalculationOnItemPrice($this);
    }

    /**
     * @param IVATManager $VATManager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATManager)
    {
        $appliedDiscounts = $this->getAppliedLineDiscounts();
        foreach ($appliedDiscounts as $appliedDiscount) {
            if ($appliedDiscount instanceof AppliedDiscount) {
                $percent = null;
                $amnt = null;
                if ($appliedDiscount->getDiscountValueType() == DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT) {
                    $amnt = $appliedDiscount->getDiscountValue();
                } else {
                    $percent = $appliedDiscount->getDiscountValue();
                }
               $lineDisc = new GenericLineDiscount($appliedDiscount->getSourceType(),$appliedDiscount->getSourceID(),$percent,$amnt);
                $this->removeDiscount($lineDisc,$VATManager);
            }
        }
    }

    /**
     * @param GenericLineDiscount $discount
     * @return bool
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        $key = $discount->getSourceType() . '|' . $discount->getSourceID();
        return array_key_exists($key, $this->appliedDiscounts);
    }

    /**
     * @return string
     */
    public function getPriceSourceType()
    {
        return $this->getPriceSource();
    }

    /**
     * @return array
     */
    public function getAppliedLineDiscounts()
    {
        return $this->appliedDiscounts;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return float
     */
    public function getCrossPrice()
    {
        return $this->crossPrice;
    }

    public function setPriceSourceCoupon()
    {
        $this->priceSource = static::PRICE_SOURCE_COUPON;
    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        if (!in_array($sourceType, self::$allowedPriceSources, false)) {
            throw new \InvalidArgumentException('sourceType is not a valid priceSourceType.');
        }
        $this->priceSource = $sourceType;
    }

    public function setQtySourceUser()
    {
        $this->qtySource = static::QTY_SOURCE_USER;
    }

    public function setQtySourceSubscription()
    {
        $this->qtySource = static::QTY_SOURCE_SUBSCRIPTION;
    }

    public function setQtySourceCoupon()
    {
        $this->qtySource = static::QTY_SOURCE_COUPON;
    }

    public function setQtySourceRule()
    {
        $this->qtySource = static::QTY_SOURCE_RULE;
    }

    /**
     * @return string
     */
    public function getQtySource()
    {
        return $this->qtySource;
    }

    /**
     * @return string
     */
    public function getQtySourceType()
    {
        return $this->qtySource;
    }

    public function getQtySourceID()
    {
        return $this->qtySourceID;
    }

    /**
     * @param $id
     */
    public function setQtySourceID($id)
    {
        $this->qtySourceID = (int)$id;
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        if (!in_array($sourceType, self::$allowedQtySources, false)) {
            throw new \InvalidArgumentException('sourceType is not one of the valid types.');
        }
        $this->qtySource = $sourceType;
    }

    /**
     * @param $sourceType
     * @return bool
     */
    public static function isAllowedPriceSourceType($sourceType)
    {
        return in_array($sourceType, self::$allowedPriceSources, true);
    }

    /**
     * @param $sourceType
     * @return bool
     */
    public static function isAllowedDiscountSourceType($sourceType)
    {
        return in_array($sourceType, self::$allowedDiscountSources, true);
    }

    /**
     * @param $sourceType
     * @return bool
     */
    public static function isAllowedQtySourceType($sourceType)
    {
        return in_array($sourceType, self::$allowedQtySources, true);
    }

    /**
     * @param $newQty
     */
    public function updateQuantityAndLineAmountWithoutUnitPrice($newQty)
    {
        $this->qty = (float)$newQty;
        $this->price = $this->unitPrice * $this->qty;
    }

    /**
     * @return bool
     */
    public function isNAVLineDiscountAllowed()
    {
        return $this->priceAllowsLineDiscount;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

}