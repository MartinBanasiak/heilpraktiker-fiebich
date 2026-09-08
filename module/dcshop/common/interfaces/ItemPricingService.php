<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.10.2016
 * Time: 14:25
 */

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 18.10.2016
 * Time: 10:25
 */
interface ItemPricingService
{
    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return mixed
     */
    public function getItemCustomerPriceByPrimary(
        $company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode, $quantity
    );

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @return mixed
     */
    public function getItemGraduatedCustomerPricesByPrimary(
        $company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode
    );

    /**
     * @param $company
     * @param $targetShopCode
     * @param $itemShopCode
     * @param $customerShopCode
     * @param $shopLanguageCode
     * @param $customerNo
     * @param $itemNo
     * @param $variantCode
     * @param $quantity
     * @return mixed
     */
    public function getItemDiscountedCustomerPriceByPrimary(
        $company, $targetShopCode, $itemShopCode, $customerShopCode, $shopLanguageCode, $customerNo, $itemNo, $variantCode, $quantity
    );

    /**
     * @param $company
     * @param $targetShopCode
     * @param $languageCode
     * @param $customerShopCode
     * @param $customerNo
     * @return mixed
     */
    public function getCurrencyCode($company, $targetShopCode, $languageCode, $customerShopCode, $customerNo);

    /**
     * @param $company
     * @param $targetShopCode
     * @param $languageCode
     * @param $customerNo
     * @param $customerShopCode
     * @return mixed
     */
    public function getPriceProvider($company, $targetShopCode, $languageCode, $customerNo, $customerShopCode);
}