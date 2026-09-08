<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\common\interfaces\GenericDBModelInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/25/2015
 * Time: 11:26 AM
 */
interface WebshopItemInterface extends GenericDBModelInterface
{

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @return string
     */
    public function getShopCode();

    /**
     * @return string
     */
    public function getLanguageCode();

    /**
     * @return string
     */
    public function getItemNo();

    /**
     * @return string
     */
    public function getVariantCode();

    /**
     * @return float
     */
    public function getMinQty();

    /**
     * @return float
     */
    public function getMaxQty();

    /**
     * @return float
     */
    public function getQtyStep();

    /**
     * @return string
     */
    public function getParentItemNo();

    /**
     * @return float
     */
    public function getInventory();

    /**
     * @return float
     */
    public function getLowInventoryLimit();

    /**
     * @return mixed
     */
    public function getWeight();

    /**
     * @return mixed
     */
    public function getNetWeight();

    /**
     * @return mixed
     */
    public function getShippingClass();

    /**
     * @return mixed
     */
    public function allowsInvoiceDiscount();

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @return mixed
     */
    public function getSummary();

    /**
     * @return mixed
     */
    public function getVariantType();

    /**
     * @return mixed
     */
    public function getBasePrice();

    /**
     * @return mixed
     */
    public function getRetailPrice();

    /**
     * @return mixed
     */
    public function getVATProdPostingGroup();

    /**
     * @return mixed
     */
    public function getDiscountGroup();

    /**
     * @return mixed
     */
    public function getItemAvailability();

    public function getCustomizationPrice();

    public function getCustomizationStatus();

    public function getBaseUnitOfMeasure();

    public function getItemSlug();

}