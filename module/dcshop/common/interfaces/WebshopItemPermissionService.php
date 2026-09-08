<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.10.2016
 * Time: 23:14
 */
interface WebshopItemPermissionService
{

    /**
     * @param $userId
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     * @return mixed
     */
    public function isUserPermittedByUserID($userId, $company, $shopCode, $languageCode, $itemNo);

    /**
     * @param $company
     * @param $customerNo
     * @param $userEmail
     * @return mixed
     */
    public function isUserPermitted($company, $customerNo, $userEmail);

}