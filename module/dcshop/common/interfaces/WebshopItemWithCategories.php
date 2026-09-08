<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 16.10.2015
 * Time: 15:40
 */
interface WebshopItemWithCategories extends WebshopItemInterface
{

    /**
     * @param $categoryCompany
     * @param $categoryShopCode
     * @param $categoryShopLanguageCode
     * @param $categoryLineNo
     * @return mixed
     */
    public function isInCategoryPrimary($categoryCompany, $categoryShopCode, $categoryShopLanguageCode, $categoryLineNo);

    /**
     * @param $categoryLineNo
     * @return mixed
     */
    public function isInCategory($categoryLineNo);

    /**
     * @return mixed
     */
    public function getCategoryArr();

}