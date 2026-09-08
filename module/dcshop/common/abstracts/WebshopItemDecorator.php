<?php
namespace DynCom\dc\dcShop\abstracts;
use DynCom\dc\dcShop\classes\WebshopItem;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\traits\genericDecoratorTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 07.07.2015
 * Time: 20:00
 */
abstract class WebshopItemDecorator implements WebshopItemInterface
{

    /**
     * @var WebshopItem
     */
    protected $decoratedEntity;

    use genericDecoratorTrait {
        __construct as traitConstruct;
    }

    /**
     * @return string
     */
    protected function getDecoratedEntityClassName() {
        return 'DynCom\dc\dcShop\interfaces\WebshopItemInterface';
    }

    /**
     * WebshopItemDecorator constructor.
     * @param WebshopItemInterface $item
     */
    public function __construct(WebshopItemInterface $item) {
        $this->traitConstruct($item);
    }


    /**
     * @return bool
     */
    public function isActive() {
        return $this->decoratedEntity->isActive();
    }

    /**
     * @return string
     */
    public function getCompany() {
        return $this->decoratedEntity->getCompany();
    }

    /**
     * @return string
     */
    public function getShopCode() {
        return $this->decoratedEntity->getShopCode();
    }

    /**
     * @return string
     */
    public function getLanguageCode() {
        return $this->decoratedEntity->getLanguageCode();
    }

    /**
     * @return string
     */
    public function getItemNo() {
        return $this->decoratedEntity->getItemNo();
    }

    /**
     * @return string
     */
    public function getVariantCode() {
        return $this->decoratedEntity->getVariantCode();
    }

    /**
     * @return float
     */
    public function getMinQty() {
        return $this->decoratedEntity->getMinQty();
    }

    /**
     * @return float
     */
    public function getMaxQty() {
        return $this->decoratedEntity->getMaxQty();
    }

    /**
     * @return float
     */
    public function getQtyStep() {
        return $this->decoratedEntity->getQtyStep();
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->decoratedEntity->getID();
    }

    /**
     * @return WebshopItemConfig
     */
    public function getConfig()
    {
        return $this->decoratedEntity->getConfig();
    }

    /**
     * @param array $fields
     * @return array
     */
    public function getFieldData(array $fields)
    {
        return $this->decoratedEntity->getFieldData($fields);
    }

    /**
     * @return WebshopItem
     */
    public function getNullObject() {

        return $this->decoratedEntity->getNullObject();

    }

    /**
     * @return string
     */
    public function getParentItemNo() {

        return $this->decoratedEntity->getParentItemNo();
    }

    /**
     * @return float
     */
    public function getInventory() {
        return $this->decoratedEntity->getInventory();
    }

    /**
     * @return mixed
     */
    public function getLowInventoryLimit() {
        return $this->decoratedEntity->getLowInventoryLimit();
    }

    /**
     * @return bool|float
     */
    public function getWeight() {
        $weight = $this->decoratedEntity->weight;
        if($weight > 0) {
            return (float)$weight;
        }
        return false;
    }

    /**
     * @return bool|float
     */
    public function getNetWeight() {
        $netWeight = $this->decoratedEntity->net_weight;
        if($netWeight > 0) {
            return (float)$netWeight;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getShippingClass() {
        return $this->decoratedEntity->getShippingClass();
    }

    /**
     * @return bool
     */
    public function allowsInvoiceDiscount() {
        return $this->decoratedEntity->allowsInvoiceDiscount();
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->decoratedEntity->getDescription();
    }

    /**
     * @return mixed
     */
    public function getSummary() {
        return $this->decoratedEntity->getSummary();
    }

    /**
     * @return mixed
     */
    public function getVariantType() {
        return $this->decoratedEntity->getVariantType();
    }

    /**
     * @return mixed
     */
    public function getRetailPrice()
    {
        $retailPrice = $this->decoratedEntity->getRetailPrice();
        return $retailPrice;
    }

    /**
     * @return mixed
     */
    public function getBasePrice()
    {
        $basePrice = $this->decoratedEntity->getBasePrice();
        return $basePrice;
    }

    /**
     * @return mixed
     */
    public function getVATProdPostingGroup()
    {
        return $this->decoratedEntity->getVATProdPostingGroup();
    }

    /**
     * @return mixed
     */
    public function getDiscountGroup()
    {
        return $this->decoratedEntity->getDiscountGroup();
    }

    /**
     * @return mixed
     */
    public function getItemAvailability() {
        return $this->decoratedEntity->getItemAvailability();
    }

    public function getCustomizationPrice()
    {
        return $this->decoratedEntity->getCustomizationPrice();
    }

    public function getCustomizationStatus()
    {
        return $this->decoratedEntity->getCustomizationStatus();
    }

    public function getBaseUnitOfMeasure()
    {
        return $this->decoratedEntity->getBaseUnitOfMeasure();
    }

    public function getItemSlug()
    {
        return $this->decoratedEntity->getItemSlug();
    }


}