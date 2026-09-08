<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\traits\hookableTrait;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\ItemPriceDataInterface;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 07.07.2015
 * Time: 21:28
 */
class WebshopItemOrderableEntityDecorator extends WebshopItemDecorator implements OrderableEntityInterface
{

    use hookableTrait;

    const QTY_SOURCE_TYPE_USER = 'QTY_SOURCE_TYPE_USER';
    const QTY_SOURCE_TYPE_COUPON = 'QTY_SOURCE_TYPE_COUPON';
    const QTY_SOURCE_TYPE_SUBSCRIPTION = 'QTY_SOURCE_TYPE_SUBSCRIPTION';
    const QTY_SOURCE_TYPE_RULE = 'QTY_SOURCE_TYPE_RULE';

    private static $allowedQtySourceTypes = [
        self::QTY_SOURCE_TYPE_USER,
        self::QTY_SOURCE_TYPE_COUPON,
        self::QTY_SOURCE_TYPE_SUBSCRIPTION,
        self::QTY_SOURCE_TYPE_RULE
    ];

    /**
     * Types are NAV Sales Line "Type" Options
     *
     * 0 =>
     * 1 => G/L Account
     * 2 => Item
     * 3 => Resource
     * 4 => Fixed Asset
     * 5 => Charge
     */
    const ORDERABLE_TYPE_ITEM = 2;
    const ORDERABLE_ENTITY_TYPE = self::ORDERABLE_TYPE_ITEM;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $unitPrice;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var string
     */
    protected $currencyCode = '';

    /**
     * @var AdvancedPriceProvider
     */
    protected $priceProvider;

    /**
     * @var ItemPriceDataInterface
     */
    protected $priceData;

    private $qtyProtected = false;

    private $unitPriceProtected = false;
    /**
     * @var ItemAvailabilityProvider
     */
    private $availabilityProvider;

    /**
     * @var float
     */
    protected $maxQty;

    /**
     * @var float
     */
    protected $minQty;

    /**
     * @var string
     */
    protected $countryCode = '';

    /**
     * @var string
     */
    protected $customizationHash = '';

    /**
     * @var string
     */
    protected $greetingCardText = '';

    /**
     * @var string
     */
    protected $linkedBasketEntityKey = '';

    /**
     * @var int
     */
    protected $linkedBasketEntityLinkType = 0;
    /**
     * @var ShippingClassPriorityProvider
     */
    private $shippingClassPriorityProvider;


    /**
     * WebshopItemOrderableEntityDecorator constructor.
     * @param WebshopItemInterface $item
     * @param Customer $customer
     * @param AdvancedPriceProvider $priceProvider
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param ShippingClassPriorityProvider $shippingClassPriorityProvider
     * @param $currencyCode
     */
    public function __construct(
        WebshopItemInterface $item,
        Customer $customer,
        AdvancedPriceProvider $priceProvider,
        ItemAvailabilityProvider $availabilityProvider,
        ShippingClassPriorityProvider $shippingClassPriorityProvider,
        $currencyCode
    )
    {
        if ($item instanceof OrderableEntityInterface) {
            throw new \InvalidArgumentException('Item is already an OrderableEntity.');
        }
        parent::__construct($item);
        $this->quantity = $item->getMinQty();
        $this->priceProvider = $priceProvider;
        $this->customer = $customer;
        $this->currencyCode = $currencyCode;
        $this->availabilityProvider = $availabilityProvider;
        $this->minQty = $item->getMinQty();
        $this->maxQty = $availabilityProvider->getInventory($item);
        if ($availabilityProvider->getItemAvailabilityStatus($item) == 2) {
            $this->maxQty = $item->getMaxQty();
        } elseif ($availabilityProvider->getItemAvailabilityStatus($item) == 1) {
            $this->maxQty = 0;
        }

        $this->shippingClassPriorityProvider = $shippingClassPriorityProvider;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $newQty = (float)$quantity;
        $step = $this->decoratedEntity->getQtyStep();
        $min = $this->minQty;
        $max = $this->maxQty;
        if ($this->getItemAvailability() == 2) {
            $max = $newQty;
        }
        adjust_qty_by_min_max_step($newQty, $this->quantity, $min, $max, $step);
        if ($newQty !== $this->quantity) {
            if (!$this->isUnitPriceProtected()) {
                $this->quantity = (float)$newQty;
                $this->setPriceDataFromProvider();
            } else {
                $this->priceData->updateQuantityAndLineAmountWithoutUnitPrice($newQty);
                $this->quantity = (float)$newQty;
            }
            $this->updateHooks('quantityChanged', $this);
        }
    }

    /**
     * @return int
     */
    public function getOrderableType()
    {
        return self::ORDERABLE_TYPE_ITEM;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getItemNo();
    }

    /**
     * @return string
     */
    public function getSubIdentifier()
    {
        return $this->getVariantCode();
    }

    /**
     * @return mixed
     */
    public function getUnitCode()
    {
        return $this->decoratedEntity->unit_of_measure_code;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getUnitPrice();
    }

    /**
     * @return mixed
     */
    public function getLineAmount()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getPrice();
    }

    /**
     * @return string
     */
    public function getVATCode()
    {
        $this->setPriceDataFromProviderIfNotSet();
        $vatCode = $this->priceData->getVATCode();
        return $vatCode;
    }

    /**
     * @return float
     */
    public function getVATPercent()
    {
        $this->setPriceDataFromProviderIfNotSet();
        $vatPercent = $this->priceData->getVATPercent();
        if ($vatPercent === null) {
            $vatMgr = $this->priceProvider->getVATManager();
            $itemVatProdPostingGroup = $this->priceData->getItemVATProdPostingGroup();
            $vatPercent = $vatMgr->getVATPercentForProdPostingGroup($this->priceData->getItemVATProdPostingGroup());
            $this->priceData->setVATPercent($vatPercent);
        }
        return $vatPercent;
    }

    /**
     * @return float
     */
    public function getVATAmount()
    {
        $this->setPriceDataFromProviderIfNotSet();
        $vatAmnt = $this->priceData->getVATAmount();
        return $vatAmnt;
    }

    /**
     * @return bool|float
     */
    public function getWeight()
    {
        $weight = $this->decoratedEntity->weight;
        if ($weight > 0) {
            return (float)$weight;
        }
        return false;
    }

    /**
     * @return bool|float
     */
    public function getNetWeight()
    {
        $netWeight = $this->decoratedEntity->net_weight;
        if ($netWeight > 0) {
            return (float)$netWeight;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getShippingClass()
    {
        return $this->decoratedEntity->getShippingClass();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->decoratedEntity->getDescription();
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->decoratedEntity->getID();
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager|null $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager = null)
    {
        $this->setPriceDataFromProviderIfNotSet();
        if ($this->getUnitPrice() == 0.00) {
            return;
        }
        $VATManager = (null === $VATManager) ? $this->priceProvider->getVATManager() : $VATManager;
        $this->priceData->applyDiscount($discount, $VATManager);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager|null $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager = null)
    {
        $this->setPriceDataFromProviderIfNotSet();
        $VATManager = (null === $VATManager) ? $this->priceProvider->getVATManager() : $VATManager;
        $this->priceData->removeDiscount($discount, $VATManager);
    }

    /**
     * @param IVATManager|null $VATManager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATManager = null)
    {
        $this->setPriceDataFromProviderIfNotSet();
        $VATMAnager = (null === $VATManager) ? $this->priceProvider->getVATManager() : $VATManager;
        $this->priceData->removeAllAppliedDiscounts($VATMAnager);
    }

    /**
     * @return mixed
     */
    public function getPriceSourceType()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getPriceSourceType();
    }

    /**
     * @return mixed
     */
    public function getPriceSourceID()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getPriceSourceID();
    }

    /**
     * @return mixed
     */
    public function getQtySourceType()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getQtySource();
    }

    /**
     * @return mixed
     */
    public function getQtySourceID()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getQtySourceID();
    }

    /**
     * @return mixed
     */
    public function getAppliedLineDiscounts()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getAppliedLineDiscounts();
    }

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->isDiscountApplied($discount);
    }

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceGross(IVATManager $VATManager)
    {
        $this->setPriceDataFromProviderIfNotSet();
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
        $this->setPriceDataFromProviderIfNotSet();
        if ($VATManager->isOutputIncludingVAT()) {
            return ($this->getUnitPrice() - $this->getVATAmount());
        } else {
            return $this->getUnitPrice();
        }
    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        $this->setPriceDataFromProviderIfNotSet();
        if (!ItemPriceData::isAllowedPriceSourceType($sourceType)) {
            throw new \InvalidArgumentException('sourceType is not a valid PriceSourceType.');
        }
        $this->priceData->setPriceSourceType($sourceType);
    }

    /**
     * @param $id
     */
    public function setPriceSourceID($id)
    {
        $this->setPriceDataFromProviderIfNotSet();
        $this->priceData->setPriceSourceID($id);
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        $this->setPriceDataFromProviderIfNotSet();
        if (!ItemPriceData::isAllowedQtySourceType($sourceType)) {
            throw new \InvalidArgumentException('sourceType ' . $sourceType . ' is not a valid QtySourceType.');
        }
        $this->priceData->setQtySourceType($sourceType);
    }

    /**
     * @param $id
     */
    public function setQtySourceID($id)
    {
        $this->setPriceDataFromProviderIfNotSet();
        $this->priceData->setQtySourceID($id);
    }

    /**
     * @return ItemPriceDataInterface
     */
    public function getPriceData()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData;
    }

    private function setPriceDataFromProvider()
    {
        $hasCustomization = !empty($this->getCustomizationHash());
        $itemPriceDataFromProvider = $this->priceProvider->getItemCustomerPrice(
            $this->decoratedEntity, $this->quantity, $this->customer, $this->currencyCode,$hasCustomization
        );
        $this->priceData = $itemPriceDataFromProvider;
    }

    private function setPriceDataFromProviderIfNotSet()
    {
        if ($this->priceData === null) {
            $this->setPriceDataFromProvider();
        }
    }

    /**
     * @return WebshopItem
     */
    public function getItem()
    {
        return $this->decoratedEntity;
    }

    /**
     * @param $isAccessible
     */
    public function setQtyAccessibility($isAccessible)
    {
        $this->qtyProtected = !((bool)$isAccessible);
    }


    /**
     * @param $isAccessible
     */
    public function setUnitPriceAccessibility($isAccessible)
    {
        $this->unitPriceProtected = !((bool)$isAccessible);
    }

    /**
     * @return bool
     */
    public function isQtyProtected()
    {
        return $this->qtyProtected;
    }

    /**
     * @return bool
     */
    public function isUnitPriceProtected()
    {
        return $this->unitPriceProtected;
    }

    /**
     * @return mixed
     */
    public function getCrossPrice()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getCrossPrice();
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availabilityProvider->getItemAvailability($this);
    }

    /**
     * @return mixed
     */
    public function isAvailable()
    {
        return $this->availabilityProvider->isItemAvailable($this);
    }

    /**
     * @return mixed
     */
    public function isUnavailable()
    {
        return $this->availabilityProvider->isItemUnavailable($this);
    }

    /**
     * @return mixed
     */
    public function isOrderable()
    {
        return $this->availabilityProvider->isItemOrderable($this);
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->decoratedEntity->getInventory();
    }

    /**
     * @return float
     */
    public function getMaxQty()
    {
        return $this->maxQty;
    }

    /**
     * @return string
     */
    public function getCustomizationHash()
    {
        return $this->customizationHash;
    }

    /**
     * @param $customizationHash
     */
    public function setCustomizationHash($customizationHash)
    {
        $this->customizationHash = $customizationHash;
    }

    /**
     * @return string
     */
    public function getGreetingCardText()
    {
        return $this->greetingCardText;
    }

    /**
     * @param $greetingCardText
     */
    public function setGreetingCardText($greetingCardText)
    {
        $this->greetingCardText = $greetingCardText;
    }

    /**
     * @param $entityKey
     * @param $linkType
     */
    public function setBasketEntityLink($entityKey, $linkType)
    {
        $this->linkedBasketEntityKey = (string)$entityKey;
        $this->linkedBasketEntityLinkType = (int)$linkType;
    }

    /**
     * @return array
     */
    public function getBasketEntityLink()
    {
        return ['links_to_entity_key' => $this->linkedBasketEntityKey, 'entity_link_type' => $this->linkedBasketEntityLinkType];
    }

    /**
     * @param $maxQty
     */
    public function setMaxQty($maxQty)
    {
        $this->maxQty = (float)$maxQty;
    }

    /**
     * @param $minQty
     */
    public function setMinQty($minQty)
    {
        $this->minQty = (float)$minQty;
    }

    /**
     * @return float
     */
    public function getMinQty()
    {
        return $this->minQty;
    }

    /**
     * @return mixed
     */
    public function getItemAvailability()
    {
        return $this->decoratedEntity->getItemAvailability();
    }

    public function getCurrencyCode()
    {
        $this->setPriceDataFromProviderIfNotSet();
        return $this->priceData->getCurrencyCode();
    }

    public function getItemSlug()
    {
        return $this->decoratedEntity->getItemSlug();
    }

    public function getShippingClassAndPriority(): array
    {
        return $this->shippingClassPriorityProvider->getItemShippingClassAndPriority($this->decoratedEntity);
    }
}
