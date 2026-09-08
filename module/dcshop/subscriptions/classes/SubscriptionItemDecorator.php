<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\IVATManager;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\subscriptions\interfaces\SubscriptionItemPriceDataInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 13:09
 */
class SubscriptionItemDecorator extends WebshopItemDecorator implements OrderableEntityInterface
{

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

    protected $itemSubscriptionData;

    protected $subscriptionHeader;

    protected $subscriptionItemLink;

    protected $subscriptionPriceData;

    protected $quantity = 0.00;

    protected $minQuantity = 1.00;

    protected $maxQuantity = 99.00;

    protected $qtyStep = 1.00;

    protected $customer;
    protected $linkedBasketEntityLinkType;
    protected $linkedBasketEntityKey;

    private $qtyProtected = false;

    private $unitPriceProtected = false;

    /**
     * @var ItemAvailabilityProvider
     */
    protected $availabilityProvider;

    /**
     * SubscriptionItemDecorator constructor.
     * @param ItemSubscriptionData $itemData
     * @param SubscriptionItemPriceDataInterface $subscriptionPriceData
     * @param ItemAvailabilityProvider $availabilityProvider
     */
    public function __construct(
        ItemSubscriptionData $itemData,
        SubscriptionItemPriceDataInterface $subscriptionPriceData,
        ItemAvailabilityProvider $availabilityProvider
    ) {
        parent::__construct($itemData->getItem());
        $this->itemSubscriptionData = $itemData;
        $this->subscriptionHeader = $subscriptionPriceData->getSubscriptionHeader();
        $this->subscriptionItemLink= $subscriptionPriceData->getSubscriptionItemLink();
        $this->subscriptionPriceData = $subscriptionPriceData;
        $this->quantity = $itemData->isTypeSequenceItem() ? 1.00 : $this->quantity;
        $this->minQuantity = $itemData->isTypeSequenceItem() ? 1.00 : $this->decoratedEntity->getMinQty();
        $this->maxQuantity = $itemData->isTypeSequenceItem() ? 1.00 : $this->decoratedEntity->getMaxQty();
        $this->qtyStep = $itemData->isTypeSequenceItem() ? 1.00 : $this->decoratedEntity->getQtyStep();
        $this->availabilityProvider = $availabilityProvider;
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
        return $this->decoratedEntity->getItemNo();
    }

    /**
     * @return string
     */
    public function getSubIdentifier()
    {
        return $this->decoratedEntity->getVariantCode();
    }

    /**
     * @return string
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
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $ratio = ((float)$quantity / $this->qtyStep);
        $nextLowerAllowedInterval = floor($ratio);
        $nextHigherAllowedInterval = ceil($ratio);
        $remainder = $ratio - $nextLowerAllowedInterval;
        if($remainder >= 0.5) {
            $quantity = $nextHigherAllowedInterval;
        } elseif($remainder <= 0.5 && $remainder > 0) {
            $quantity = $nextLowerAllowedInterval;
        }
        $newQty = (float)$quantity;
        $this->quantity = $newQty;
    }

    /**
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->subscriptionPriceData->getUnitPrice();
    }

    /**
     * @return string
     */
    public function getVATCode()
    {
        return $this->subscriptionPriceData->getItemVATProdPostingGroup();
    }

    /**
     * @return float
     */
    public function getVATPercent()
    {
        return $this->subscriptionPriceData->getVATPercent();
    }

    /**
     * @return float
     */
    public function getVATAmount()
    {
        return $this->subscriptionPriceData->getVATAmount();
    }

    /**
     * @return float
     */
    public function getLineAmount()
    {
        //FEHLER - Customer Price enth�lt qty!
        //return (float)($this->quantity * $this->subscriptionPriceData->getCustomerPrice());
        return (float)($this->subscriptionPriceData->getCustomerPrice());
    }

    /**
     * @return SubscriptionHeader
     */
    public function getSubscriptionHeader()
    {
        return $this->subscriptionHeader;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionItemLink()
    {
        return $this->subscriptionItemLink;
    }

    /**
     * @return SubscriptionItemPriceDataInterface
     */
    public function getSubscriptionPriceData()
    {
        return $this->subscriptionPriceData;
    }


    /**
     * @return bool
     */
    public function isTypeOrderOption() {
        if(!$this->itemSubscriptionData->hasData()) {return false;}
        return ((int)$this->subscriptionHeader->type === 0);
    }

    /**
     * @return bool
     */
    public function isTypeSequenceItem() {
        if(!$this->itemSubscriptionData->hasData()) {return false;}
        return ((int)$this->subscriptionHeader->type === 1);
    }

    /**
     * @return mixed
     */
    public function getSubscriptionShippingOptionLineNo() {
        return $this->itemSubscriptionData->getSubscriptionShippingOptionLineNo();
    }

    /**
     * @return mixed
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function applyDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->subscriptionPriceData->applyDiscount($discount,$VATManager);
    }

    /**
     * @param GenericLineDiscount $discount
     * @param IVATManager $VATManager
     */
    public function removeDiscount(GenericLineDiscount $discount, IVATManager $VATManager)
    {
        $this->subscriptionPriceData->removeDiscount($discount,$VATManager);
    }

    /**
     * @param IVATManager|null $VATMAnager
     */
    public function removeAllAppliedDiscounts(IVATManager $VATMAnager = null)
    {
        $this->subscriptionPriceData->removeAllDiscounts($VATMAnager);
    }


    /**
     * @return mixed
     */
    public function getPriceSourceType()
    {
        return $this->subscriptionPriceData->getPriceSourceType();
    }

    /**
     * @return mixed
     */
    public function getPriceSourceID()
    {
        return $this->subscriptionPriceData->getPriceSourceID();
    }

    /**
     * @return mixed
     */
    public function getQtySourceType()
    {
        return $this->subscriptionPriceData->getQtySource();
    }

    /**
     * @return mixed
     */
    public function getQtySourceID()
    {
        return $this->subscriptionPriceData->getQtySourceID();
    }

    /**
     * @return mixed
     */
    public function getAppliedLineDiscounts()
    {
        return $this->subscriptionPriceData->getAppliedLineDiscounts();
    }

    /**
     * @param IVATManager $VATManager
     * @return float
     */
    public function getUnitPriceGross(IVATManager $VATManager)
    {
        if($VATManager->isOutputIncludingVAT()) {
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
        if($VATManager->isOutputIncludingVAT()) {
            return ($this->getUnitPrice() - $this->getVATAmount());
        } else {
            return $this->getUnitPrice();
        }
    }

    /**
     * @param ItemPriceData $priceData
     */
    public function setPrice(ItemPriceData $priceData)
    {
        throw new \DomainException('SubscriptionItemDecorator sets its own price.');
    }

    /**
     * @return SubscriptionItemPriceDataInterface
     */
    public function getPriceData()
    {
        return $this->subscriptionPriceData;
    }

    /**
     * @param $sourceType
     */
    public function setPriceSourceType($sourceType)
    {
        throw new \DomainException('SubscriptionItemDecorator sets its own price.');
    }

    /**
     * @param $id
     */
    public function setPriceSourceID($id)
    {
        throw new \DomainException('SubscriptionItemDecorator sets its own price.');
    }

    /**
     * @param $sourceType
     */
    public function setQtySourceType($sourceType)
    {
        $this->subscriptionPriceData->setQtySourceType($sourceType);
    }

    /**
     * @param $id
     */
    public function setQtySourceID($id)
    {
        throw new \DomainException('SubscriptionItemDecorator sets its own price.');
    }

    /**
     * @param GenericLineDiscount $discount
     * @return mixed
     */
    public function isDiscountApplied(GenericLineDiscount $discount)
    {
        return $this->subscriptionPriceData->isDiscountApplied($discount);
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
        return $this->subscriptionPriceData->getCrossPrice();
    }

    /**
     * @return mixed
     */
    public function isOrderable()
    {
        if ($this->isTypeOrderOption()) {
            $isOrderable = $this->availabilityProvider->isItemOrderable($this->decoratedEntity);
        } else {
            $altPrimary = [
                'company' => $this->getCompany(),
                'shop_code' => $this->getShopCode(),
                'language_code' => $this->getLanguageCode(),
                'item_no' => $this->subscriptionHeader->item_no_subscription_item,
            ];


            $isOrderable = $this->availabilityProvider->isItemOrderableByAltPrimary($altPrimary);
        }
        return $isOrderable;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getAvailability() !== DefaultItemAvailabilityProvider::INVENTORY_NOT_AVAILABLE;
    }

    /**
     * @return bool
     */
    public function isUnavailable()
    {
        return $this->getAvailability() === DefaultItemAvailabilityProvider::INVENTORY_NOT_AVAILABLE;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        if ($this->isTypeOrderOption()) {
            $availability = $this->availabilityProvider->getItemAvailability($this->decoratedEntity);
        } else {
            $altPrimary = [
                'company' => $this->getCompany(),
                'shop_code' => $this->getShopCode(),
                'language_code' => $this->getLanguageCode(),
                'item_no' => $this->subscriptionHeader->item_no_subscription_item,
            ];

            $availability = $this->availabilityProvider->getItemAvailabilityByAltPrimary($altPrimary);
        }
        return $availability;
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

    public function getCurrencyCode()
    {
        return $this->subscriptionPriceData->getCurrencyCode();
    }


}