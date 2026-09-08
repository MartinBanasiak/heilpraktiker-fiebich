<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\ItemPriceData;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 23.03.2015
 * Time: 11:37
 */
interface IVATManager
{

    /**
     * @param $VATProdPostingGroup
     *
     * @return float
     */
    public function getVATPercentForProdPostingGroup($VATProdPostingGroup);

    /**
     * @param ItemPriceData $itemPriceData
     *
     * @return float
     */
    public function getVATPercentForProdPostingGroupFromPriceData($itemPriceData);

    /**
     * @return string
     */
    public function getVATBusPostingGroup();

    /**
     * @param ItemPriceData $priceData
     * @return
     */
    public function doVATCalculationOnItemPrice(ItemPriceData $priceData);

    /**
     * @param  float $price
     * @param  float $taxPercentToAdd
     * @return float
     */
    public function getTaxAmountToAdd($price, $taxPercentToAdd);

    /**
     * @param  float $price
     * @param  float $taxPercentIncluded
     * @return float
     */
    public function getIncludedTaxAmount($price, $taxPercentIncluded);

    /**
     * @param float $price
     * @param float $taxPercent
     * @return float
     */
    public function getTaxAmountForShopVATSetting($price, $taxPercent);

    public function isOutputIncludingVAT();

}