<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/25/2015
 * Time: 11:20 AM
 */
class WebshopItemNAVVariantDecorator extends WebshopItemDecorator
{

    /**
     * @var WebshopItemVariant
     */
    protected $navVariant;

    protected $inventory;

    /**
     * @var string
     */
    protected $variant_code;

    /**
     * @var AdvancedPriceProvider
     */
    protected $priceProvider;

    /**
     * WebshopItemNAVVariantDecorator constructor.
     * @param WebshopItemInterface $item
     * @param WebshopItemVariant $variant
     */
    public function __construct(WebshopItemInterface $item, WebshopItemVariant $variant) {
        parent::__construct($item);
        $this->navVariant = $variant;
        $this->variant_code = $variant->code;
        $this->inventory = $variant->inventory;
    }


    /**
     * @return string
     */
    public function getVariantCode() {
        return (null === $this->variant_code) ? '' : $this->variant_code;
    }

    /**
     * @return mixed
     */
    public function getParentItemNo() {
        return $this->decoratedEntity->getItemNo();
    }

    /**
     * @return float
     */
    public function getInventory() {
        return (float)$this->inventory;
    }

    /**
     * @return mixed
     */
    public function getShippingClass() {
        return $this->decoratedEntity->getShippingClass();
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->navVariant->description;
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
     * @param ItemPriceData $priceData
     */
    public function setPrice(ItemPriceData $priceData)
    {
        $this->decoratedEntity->setPrice($priceData);
    }

    /**
     * @return mixed
     */
    public function getPriceData()
    {
        return $this->decoratedEntity->getPriceData();
    }

    /**
     * @return bool
     */
    public function isVariant()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasVariants()
    {
        return false;
    }



}