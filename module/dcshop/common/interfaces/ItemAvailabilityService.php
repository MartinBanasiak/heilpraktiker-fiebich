<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 16:16
 */

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 16:00
 */
interface ItemAvailabilityService
{
    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return mixed
     */
    public function isItemAvailable($company, $shopCode, $languageCode, $itemNo, $variantCode);

    /**
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return mixed
     */
    public function isItemOrderable($company, $shopCode, $languageCode, $itemNo, $variantCode);
}