<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\WebshopItemPermissionService;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 25.10.2016
 * Time: 01:03
 */
class GenericWebshopItemPermissionService implements WebshopItemPermissionService
{
    const QUERY_USER_ITEM_PERMISSION = '';


    /**
     * @param $userId
     * @param $company
     * @param $shopCode
     * @param $languageCode
     * @param $itemNo
     */
    public function isUserPermittedByUserID($userId, $company, $shopCode, $languageCode, $itemNo)
    {
        // TODO: Implement isUserPermittedByUserID() method.
    }

    /**
     * @param $company
     * @param $customerNo
     * @param $userEmail
     */
    public function isUserPermitted($company, $customerNo, $userEmail)
    {
        // TODO: Implement isUserPermitted() method.
    }

}