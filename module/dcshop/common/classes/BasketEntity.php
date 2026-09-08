<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 14:13
 */
class BasketEntity implements OrderableEntityInterface
{

    use hookableTrait;

    const CREATION_SOURCE_TYPE_USER = 'USER_CREATED';
    const CREATION_SOURCE_TYPE_COUPON = 'COUPON_CREATED';
    const CREATION_SOURCE_TYPE_RULE = 'RULE_CREATED';
    const CREATION_SOURCE_TYPE_SUBSCRIPTION = 'SUBSCRIPTION_CREATED';

    private static $allowedCreationSourceTypes = [
        self::CREATION_SOURCE_TYPE_USER,
        self::CREATION_SOURCE_TYPE_COUPON,
        self::CREATION_SOURCE_TYPE_RULE,
        self::CREATION_SOURCE_TYPE_SUBSCRIPTION
    ];


    protected $id = 0;

    protected $prevQuantity = 0.00;

    protected $quantity = 0.00;

    protected $minQuantity = 1.00;

    protected $maxQuantity = 99.00;

    protected $qtyStep = 1.00;

    protected $visitor;

    protected $orderableEntity;

    protected $creationSourceType = self::CREATION_SOURCE_TYPE_USER;

    protected $creationSourceID;

    protected $appliedQtyChanges;

    private $creationNotification;

    private $lineNo;

    private $shipping_class = '';

    private $shipping_class_priority = '';
    /**
     * @var VATManager
     */
    private $vatManager;

    /**
     * BasketEntity constructor.
     * @param OrderableEntityInterface $orderableEntity
     * @param Visitor $visitor
     * @param VATManager $vatManager
     * @param $quantity
     * @param int $id
     */
    public function __construct(
        OrderableEntityInterface $orderableEntity, Visitor $visitor, VATManager $vatManager, $quantity, $id = 0
    ) {
        if ($orderableEntity instanceof BasketEntity) {
            throw new \InvalidArgumentException('OrderableEntity is already a BasketEntity.');
        }
        $this->orderableEntity = $orderableEntity;
        $this->minQuantity = $this->orderableEntity->getMinQty();
        $this->maxQuantity = $this->orderableEntity->getMaxQty();
        $this->qtyStep = $this->orderableEntity->getQtyStep();
        $this->visitor = $visitor;
        $this->setQuantity((float)$quantity);
        $this->id = (int)$id;
        $this->shipping_class = $this->orderableEntity->getShippingClass();
        $this->vatManager = $vatManager;
    }

    /**
     * @return int
     */
    public function getOrderableType()
    {
        return (int)$this->orderableEntity->getOrderableType();
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return (string)$this->orderableEntity->getIdentifier();
    }

    /**
     * @return string
     */
    public function getSubIdentifier()
    {
        return (string)$this->orderableEntity->getSubIdentifier();
    }

    /**
     * @return string
     */
    public function getUnitCode()
    {
        return (string)$this->orderableEntity->getUnitPrice();
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return (float)$this->quantity;
    }

    /**
     * @param float $quantity
     * @param string $qtySourceType
     * @param int $qtySourceID
     */
    public function setQuantity($quantity, $qtySourceType = ItemPriceData::QTY_SOURCE_USER, $qtySourceID = 0)
    {
        if ($qtySourceType === ItemPriceData::QTY_SOURCE_USER && $qtySourceID === 0) {
            $qtySourceID = $this->visitor->getID();
        }
        $newQty = (float)$quantity;
		adjust_qty_by_min_max_step($newQty,$this->quantity,$this->minQuantity,$this->maxQuantity,$this->qtyStep);
        /*$ratio = ($newQty / $this->qtyStep);
        $remainder = $newQty % $this->qtyStep;
        if ($remainder !== 0) {
            $nextLowerAllowedInterval = floor($ratio);
            $nextHigherAllowedInterval = ceil($ratio);
            $remainder = $ratio - $nextLowerAllowedInterval;
            if ($remainder >= 0.5) {
                $quantity = $nextHigherAllowedInterval;
            } elseif ($remainder <= 0.5 && $remainder > 0) {
                $quantity = $nextLowerAllowedInterval;
            }
        }*/
        if ($this->quantity <> $newQty) {
			$this->prevQuantity = $this->quantity;
			$this->quantity = $newQty;

			$appliedDiscounts = $this->getAppliedLineDiscounts();
            $newDiscounts = [];
            foreach ($appliedDiscounts as $ad) {
                if ($ad instanceof AppliedDiscount) {
                    $valType = $ad->getDiscountValueType();
                    $percent = $valType === GenericLineDiscount::DISCOUNT_VALUE_TYPE_PERCENT ? (float)$ad->getDiscountValue() : 0.0;
                    $amount = $valType === GenericLineDiscount::DISCOUNT_VALUE_TYPE_AMOUNT ? (float)$ad->getDiscountValue() : 0.0;
                    $newDisc = new GenericLineDiscount($ad->getSourceType(),$ad->getSourceID(),$percent,$amount);
                    $newDiscounts[] = $newDisc;
                }
            }

			$this->orderableEntity->setQuantity($newQty);

            foreach ($newDiscounts as $nd) {
                if ($nd instanceof GenericLineDiscount) {
                    $this->applyDiscount($nd,$this->vatManager);
                }
            }

			$this->setQtySourceType($qtySourceType);
			$this->setQtySourceID($qtySourceID);
			if (($newQty <> $this->prevQuantity) && ($this->prevQuantity > 0)) {
				$valSource = new BasketValueSource();
				$valSource->setQtySetBySourceType($qtySourceType);
				$valSource->setQtySetBySourceId($qtySourceID);
				$valSource->setQtyBeforeSetting($this->prevQuantity);
				$valSource->setQtyAfterSetting($newQty);
				$this->appliedQtyChanges[] = $valSource;
				$key = $this->getIdentifier() . '|' . $this->getSubIdentifier();
				$eventName = $key . '.quantityChanged';
				$this->updateHooks($eventName, $this);
			}
		}
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return (float)$this->orderableEntity->getUnitPrice();
    }

    /**
     * @return string
     */
    public function getVATCode()
    {
        return (string)$this->orderableEntity->getVATCode();
    }

    /**
     * @return float
     */
    public function getVATPercent()
    {
        return (float)$this->orderableEntity->getVATPercent();
    }

    /**
     * @return float
     */
    public function getVATAmount()
    {
        return (float)$this->orderableEntity->getVATAmount();
    }

    /**
     * @return float
     */
    public function getLineAmount()
    {
        return (float)$this->orderableEntity->getLineAmount();
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return (string)$this->orderableEntity->getCompany();
    }

    /**
     * @return false|float
     */
    public function getWeight()
    {
        return ($this->getQuantity() * $this->orderableEntity->getWeight());
    }

    /**
     * @return bool
     */
    public function allowsInvoiceDiscount()
    {
        return $this->orderableEntity->allowsInvoiceDiscount();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->orderableEntity->getDescription();
    }

    public function getMinQty()
    {
        return $this->orderableEntity->getMinQty();
    }

    public function getMaxQty()
    {
        return $this->orderableEntity->getMaxQty();
    }

    public function getQtyStep()
    {
        return $this->orderableEntity->getQtyStep();
    }

    public function getCustomer()
    {
        return $this->orderableEntity->getCustomer();
    }

    /**
     * @param Customer $customer
     * @return mixed
     */
    public function setCustomer(Customer $customer)
    {
        return $this->orderableEntity->setCustomer($customer);
    }

    /**
     * @return int
     */
    public function getDBID()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->orderableEntity->getID();
    }

    /**
     * @return int
     */
    public function getEntityID()
    {
        return $this->orderableEntity->getID();
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        if ($this->getUnitPrice() == 0.00) {
            return;
        }
        $this->orderableEntity->applyDiscount($discount, $VATManager);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->orderableEntity->removeDiscount($discount, $VATManager);
    }

    /**
     * @param IVATManager|null $VATMAnager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATMAnager = null)
    {
        $this->orderableEntity->removeAllAppliedDiscounts($VATMAnager);
    }

    /**
     * @return float
     */
    public function getPreviousQuantity()
    {
        return $this->prevQuantity;
    }

    public function getPriceSourceType()
    {
        return $this->orderableEntity->getPriceSourceType();
    }

    public function getPriceSourceID()
    {
        return $this->orderableEntity->getPriceSourceID();
    }

    public function getQtySourceType()
    {
        return $this->orderableEntity->getQtySourceType();
    }

    public function getQtySourceID()
    {
        return $this->orderableEntity->getQtySourceID();
    }

    public function getAppliedLineDiscounts()
    {
        return $this->orderableEntity->getAppliedLineDiscounts();
    }

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        return $this->orderableEntity->isDiscountApplied($discount);
    }

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceGross(IVATManager $VATManager)
    {
        if ($VATManager->isOutputIncludingVAT()) {
            return $this->getUnitPrice();
        } else {
            return ($this->getUnitPrice() + $this->getVATAmount());
        }
    }

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceNet(IVATManager $VATManager)
    {
        if ($VATManager->isOutputIncludingVAT()) {
            return ($this->getUnitPrice() - $this->getVATAmount());
        } else {
            return $this->getUnitPrice();
        }
    }

    /**
     * @param $id
     */
    public function setID($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @param $creationSourceType
     */
    public function setCreationSourceType($creationSourceType)
    {
        if (!in_array($creationSourceType, self::$allowedCreationSourceTypes, true)) {
            throw new \InvalidArgumentException('Parameter \'creationSourceType\' must be a valid source type.');
        }
        $this->creationSourceType = $creationSourceType;
    }

    /**
     * @param $id
     */
    public function setCreationSourceID($id)
    {
        $this->creationSourceID = $id;
    }

    /**
     * @return string
     */
    public function getCreationSourceType()
    {
        return $this->creationSourceType;
    }

    /**
     * @return mixed
     */
    public function getCreationSourceID()
    {
        return $this->creationSourceID;
    }

    /**
     * @param BasketValueSource $valueSource
     * @param IVATManager $VATManger
     */
    public function applyBasketValueSource(BasketValueSource $valueSource, IVATManager $VATManger)
    {
        $appliedToLineID = $valueSource->getAppliedToLineID();
        if ($appliedToLineID !== $this->getDBID()) {
            throw new \InvalidArgumentException(
                'BasketEntity must have a DB-id and BasketValueSource\'s \'applied_to_line_id\' must match that id. Attempted value source line id: ' . $appliedToLineID . '
            attempted entity-db-id: ' . $this->getDBID()
            );
        }

        $createdBySourceType = $valueSource->getCreatedBySourceType();
        $createdBySourceID = $valueSource->getCreatedBySourceID();
        $creationNotification = $valueSource->getCreationNotification();
        if (!empty($createdBySourceType)) {
            $this->setCreationSourceType($createdBySourceType);
            $this->setCreationSourceID($createdBySourceID);
            if (!empty($creationNotification)) {
                $this->setCreationNotification($creationNotification);
            }
        }

        $qtySetBySourceType = $valueSource->getQtySetBySourceType();
        $qtySetBySourceID = $valueSource->getQtySetBySourceID();
        $qtyBeforeSetting = $valueSource->getQtyBeforeSetting();
        $qtyAfterSetting = $valueSource->getQtyAfterSetting();
        if (!empty($qtySetBySourceType)) {
            $this->setQtySourceType($qtySetBySourceType);
            $this->setQtySourceID($qtySetBySourceID);
        }
        if (($qtyAfterSetting != $qtyBeforeSetting) && (null !== $qtyBeforeSetting) && ($qtyBeforeSetting > 0)) {
            $this->appliedQtyChanges[] = $valueSource;
        }

        $unitPriceSetBySourceType = $valueSource->getUnitPriceSetBySourceType();
        $unitPriceSetBySourceID = $valueSource->getUnitPriceSetBySourceID();
        $unitPriceBeforeSetting = $valueSource->getUnitPriceBeforeSetting();
        $unitPriceAfterSetting = $valueSource->getUnitPriceAfterSetting();
        if ($unitPriceSetBySourceType != '' && ItemPriceData::isAllowedPriceSourceType($unitPriceSetBySourceType)) {
            $this->setPriceSourceType($unitPriceSetBySourceType);
        }
        if (!empty($unitPriceSetBySourceID) && ItemPriceData::isAllowedPriceSourceType($unitPriceSetBySourceType)) {
            $this->setPriceSourceID($unitPriceSetBySourceID);
        }
        if ($unitPriceAfterSetting < $unitPriceBeforeSetting) {

            $couponLine = $valueSource->getCouponLine();
            if (null !== $couponLine) {
                $amountToDiscount = $couponLine->value_type === CouponLine::VALUE_TYPE_AMOUNT && (int)$couponLine->value_coupon === 0 ? $couponLine->amount : 0.0;
                $percentToDiscount = $couponLine->value_type === CouponLine::VALUE_TYPE_PERCENT && (int)$couponLine->value_coupon === 0 ? $couponLine->percentage : 0.0;
            } else {
                $amountToDiscount = abs($unitPriceBeforeSetting - $unitPriceAfterSetting);
                $percentToDiscount = 0.0;
            }
            $discount = new GenericLineDiscount($unitPriceSetBySourceType, $unitPriceSetBySourceID, $percentToDiscount, $amountToDiscount);
            $this->applyDiscount($discount, $VATManger);
        } elseif ($unitPriceAfterSetting > 0 && ($unitPriceAfterSetting < $unitPriceBeforeSetting)) {
            $this->getPriceData()->setUnitPrice($unitPriceAfterSetting);
        }

    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        $this->orderableEntity->setPriceSourceType($sourceType);
    }

    /**
     * @param $id
     */
    public function setPriceSourceID($id)
    {
        $this->orderableEntity->setPriceSourceID($id);
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        $this->orderableEntity->setQtySourceType($sourceType);
    }

    /**
     * @param $id
     */
    public function setQtySourceID($id)
    {
        $this->orderableEntity->setQtySourceID($id);
    }

    /**
     * @return \DynCom\dc\dcShop\interfaces\ItemPriceDataInterface
     */
    public function getPriceData()
    {
        return $this->orderableEntity->getPriceData();
    }


    /**
     * @return OrderableEntityInterface
     */
    public function getOrderableEntity()
    {
        return $this->orderableEntity;
    }

    /**
     * @return mixed
     */
    public function getAppliedQtyChanges()
    {
        return $this->appliedQtyChanges;
    }

    /**
     * @param $lineNo
     */
    public function setLineNo($lineNo)
    {
        $this->lineNo = (int)$lineNo;
    }

    /**
     * @return int
     */
    public function getLineNo()
    {
        return (int)$this->lineNo;
    }

    /**
     * @param $isAccessible
     */
    public function setQtyAccessibility($isAccessible)
    {
        $this->orderableEntity->setQtyAccessibility($isAccessible);
    }

    /**
     * @param $isAccessible
     */
    public function setUnitPriceAccessibility($isAccessible)
    {
        $this->orderableEntity->setUnitPriceAccessibility($isAccessible);
    }

    public function isQtyProtected()
    {
        return $this->orderableEntity->isQtyProtected();
    }

    public function isUnitPriceProtected()
    {
        return $this->orderableEntity->isUnitPriceProtected();
    }

    public function getCrossPrice()
    {
        return $this->orderableEntity->getCrossPrice();
    }

    /**
     * @param $notification
     */
    public function setCreationNotification($notification)
    {
        $this->creationNotification = $notification;
    }

    /**
     * @return mixed
     */
    public function getCreationNotification()
    {
        return $this->creationNotification;
    }

    public function isOrderable()
    {
        return $this->orderableEntity->isOrderable();
    }

    public function isAvailable()
    {
        return $this->orderableEntity->isAvailable();
    }

    public function isUnavailable()
    {
        return $this->orderableEntity->isUnavailable();
    }

    public function getAvailability()
    {
        return $this->orderableEntity->getAvailability();
    }

    public function getInventory()
    {
        return $this->orderableEntity->getInventory();
    }

    /**
     * @return mixed
     */
    public function getCustomizationHash()
	{
		return $this->orderableEntity->getCustomizationHash();
	}

    /**
     * @return mixed
     */
    public function getShopCode()
	{
		return $this->orderableEntity->getShopCode();
	}

    /**
     * @return mixed
     */
    public function getShopLanguageCode()
	{
		return $this->orderableEntity->getLanguageCode();
	}


    /**
     * @return mixed
     */
    public function getGreetingCardText()
    {
        return $this->orderableEntity->getGreetingCardText();
    }

    /**
     * @param $text
     * @return mixed
     */
    public function setGreetingCardText($text)
    {
        return $this->orderableEntity->setGreetingCardText($text);
    }

    /**
     * @param $entityKey
     * @param $linkType
     */
    public function setBasketEntityLink($entityKey, $linkType)
    {
        $this->orderableEntity->setBasketEntityLink($entityKey, $linkType);
    }

    public function getBasketEntityLink()
    {
        return $this->orderableEntity->getBasketEntityLink();
    }

    /**
     * @param $maxQty
     */
    public function setMaxQty($maxQty)
    {
        $maxQty = (float)$maxQty;
        $this->orderableEntity->setMaxQty($maxQty);
        $this->maxQuantity = $maxQty;
    }

    /**
     * @param $minQty
     */
    public function setMinQty($minQty)
    {
        $minQty = (float)$minQty;
        $this->orderableEntity->setMinQty($minQty);
        $this->minQuantity = $minQty;
    }

    /**
     * @return string
     */
    public function getShippingClass()
    {
        return (string)$this->shipping_class;
    }

    public function getCurrencyCode()
    {
        return $this->orderableEntity->getCurrencyCode();
    }

    public function getShippingClassAndPriority()
    {
        return $this->orderableEntity->getShippingClassAndPriority();
    }
}