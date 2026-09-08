<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Class WebshopItem
 */
class WebshopItem implements WebshopItemInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

    public const ORDERABLE_ENTITY_TYPE = 2;

    protected $id;
    protected $company;
    protected $shop_code;
    protected $language_code;
    protected $item_no;
    protected $description;
    protected $summary;
    protected $base_unit_of_measure;
    protected $unit_of_measure_code;
    protected $nav_base_unit_code;
    protected $multiplier;
    protected $variant_type;
    protected $active;
    protected $validity_from;
    protected $validity_to;
    protected $main_picture_line_no;
    protected $main_category_line_no;
    protected $retail_price;
    protected $base_price;
    protected $price_includes_vat;
    protected $inventory;
    protected $insufficient_inventory_limit;
    protected $quantity_on_purchase_order;
    protected $discount_group;
    protected $allow_invoice_discount;
    protected $search_query;
    protected $vendor_no;
    protected $vendor_name;
    protected $parent_item_no;
    protected $order_ranking;
    protected $weight;
    protected $width;
    protected $net_weight;
    protected $height;
    protected $length;
    protected $volume;
    protected $creation_date;
    protected $meta_keywords;
    protected $meta_description;
    protected $site_title;
    protected $allow_gift_package;
    protected $is_gift_package;
    protected $minimum_order_quantity;
    protected $quantity_packing_unit;
    protected $packing_unit_mandatory;
    protected $order_per_packing_unit;
    protected $vat_prod_posting_group;
    protected $to_delete;
    protected $is_greeting_card;
    protected $customizable;
    protected $customization_price;
    protected $shipping_class_code = '';
    protected $always_available;

    protected $isPriceSet = false;
    protected $hasVariants = false;

    protected $item_slug;


    /**
     * @var ItemPriceData
     */
    protected $price;

    /**
     * @param WebshopItemConfig $itemConfig
     */
    public function __construct( WebshopItemConfig $itemConfig ) {
        $this->config = $itemConfig;
    }

    /**
     * @return WebshopItem
     */
    public function getNullObject() {
        return new self($this->config);
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isActiveAtDateTime(\DateTime $dateTime) {
        if(!$this->active) return false;
        $currTimestamp = $dateTime;
        $currTimestamp->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $currTimestamp->modify('today');
        $currTimestamp = $currTimestamp->getTimestamp();
        if(isset($this->validity_from) && $this->validity_from !== '0000-00-00' && $this->validity_from !== '0000-00-00 00:00:00') {
            $validityFromTimestamp = \DateTime::createFromFormat('Y-m-d H:i:s',$this->validity_from);
            $validityFromTimestamp = strtotime($this->validity_from);
            if(!($validityFromTimestamp <= $currTimestamp)) return false;
        }
        if(isset($this->validity_to) && $this->validity_to !== '0000-00-00' && $this->validity_to !== '0000-00-00 00:00:00') {
            $validityToTimestamp = \DateTime::createFromFormat('Y-m-d H:i:s',strtotime($this->validity_to));
            $validityToTimestamp = strtotime($this->validity_to);
            if(!($validityToTimestamp >= $currTimestamp)) return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->isActiveAtDateTime(new \DateTime());
    }

    /**
     * @return string
     */
    public function getVariantCode() {
        return '';
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return (string)$this->company;
    }

    /**
     * @return string
     */
    public function getShopCode()
    {
        return (string)$this->shop_code;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return (string)$this->language_code;
    }

    /**
     * @return string
     */
    public function getItemNo()
    {
        return (string)$this->item_no;
    }

    /**
     * @return float
     */
    public function getMinQty() {
        if(isset($this->minimum_order_quantity) && $this->minimum_order_quantity > 0) {
            return (float)$this->minimum_order_quantity;
        } elseif (((int)$this->order_per_packing_unit === 1) && ((float)$this->quantity_packing_unit > 0)) {
            return (float)$this->quantity_packing_unit;
        }
        return 1.00;
    }

    /**
     * @return float
     */
    public function getMaxQty() {
        return 9999.0;
    }

    /**
     * @return float
     */
    public function getQtyStep() {
        return ((int)$this->order_per_packing_unit === 1 && (float)$this->quantity_packing_unit > 0) ? (float)$this->quantity_packing_unit : (float)$this->getMinQty();
    }

    /**
     * @return string
     */
    public function getParentItemNo() {
        return (string)$this->parent_item_no;
    }

    /**
     * @return float
     */
    public function getInventory() {
        return (float)$this->inventory;
    }

    /**
     * @return float
     */
    public function getLowInventoryLimit() {
        return (float)$this->insufficient_inventory_limit;
    }

    /**
     * @param ItemPriceData $itemPriceData
     */
    public function setPrice(ItemPriceData $itemPriceData) {
        if($itemPriceData->getItemID() !== $this->id) {
            throw new \BadMethodCallException("'ItemPriceData' must have an ItemID of {$this->id}. Currently: {$itemPriceData->getItemID()}");
        }
        $this->price = $itemPriceData;
        $this->isPriceSet = true;
        $this->updateHooks('changed',$this);
    }

    /**
     * @return bool|ItemPriceData
     */
    public function getPriceData() {
        if(!$this->isPriceSet) {
            return false;
        }
        return $this->price;
    }

    /**
     * @return bool|float
     */
    public function getWeight() {
        $weight = $this->weight;
        if($weight > 0) {
            return (float)$weight;
        }
        return false;
    }

    /**
     * @return bool|float
     */
    public function getNetWeight() {
        $netWeight = $this->net_weight;
        if($netWeight > 0) {
            return (float)$netWeight;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getShippingClass() {
        return (string)$this->shipping_class_code;
    }

    /**
     * @return bool
     */
    public function allowsInvoiceDiscount() {
        return (bool)$this->allow_invoice_discount;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return mixed
     */
    public function getVariantType()
    {
        return $this->variant_type;
    }

    /**
     * @return bool
     */
    public function isVariant()
    {
        return (!empty($this->parent_item_no));
    }

    /**
     * @return mixed
     */
    public function hasVariants()
    {
        return $this->hasVariants;
    }

    /**
     * @param $hasVariants
     */
    public function setHasVariants($hasVariants)
    {
        $this->hasVariants = (bool)$hasVariants;
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return (float)$this->base_price;
    }

    /**
     * @return float
     */
    public function getRetailPrice()
    {
        return (float)$this->retail_price;
    }

    /**
     * @return mixed
     */
    public function getVATProdPostingGroup()
    {
        return $this->vat_prod_posting_group;
    }

    /**
     * @return mixed
     */
    public function getDiscountGroup()
    {
        return $this->discount_group;
    }
    /**
     * @return bool
     */
    public function getIsGreetingCard()
    {
        return $this->is_greeting_card;
    }

    /**
     * @return bool
     */
    public function getIsGiftPackage()
    {
        return $this->is_gift_package;
    }

    /**
     * @return bool
     */
    public function getAllowGiftPackage()
    {
        return $this->allow_gift_package;
    }

    /**
     * @return bool
     */
    public function getCustomizationStatus()
    {
        return (int)$this->customizable;
    }

    /**
     * @return mixed
     */
    public function getCustomizationPrice()
    {
        return $this->customization_price;
    }

    /**
     * @return int
     */
    public function getItemAvailability()
    {
        return $this->always_available;
    }

    public function getBaseUnitOfMeasure()
    {
        return $this->base_unit_of_measure;
    }

    /**
     * @return mixed
     */
    public function getItemSlug()
    {
        return $this->item_slug;
    }


}