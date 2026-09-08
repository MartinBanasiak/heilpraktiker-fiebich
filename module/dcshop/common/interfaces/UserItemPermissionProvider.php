<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\User;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 10/11/2015
 * Time: 10:03 PM
 */
interface UserItemPermissionProvider
{

    /**
     * @param User $user
     * @param CustomerWithPermissionGroupsInterface $customer
     * @param WebshopItemInterface $webshopItem
     * @return mixed
     */
    public function userHasItemPermission(User $user, CustomerWithPermissionGroupsInterface $customer, WebshopItemInterface $webshopItem);

}