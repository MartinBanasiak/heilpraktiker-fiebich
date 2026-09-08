<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\User;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 07.09.2016
 * Time: 14:39
 */
interface WebshopItemOrderabilityService
{
    /**
     * @param User $user
     * @param CustomerWithPermissionGroupsInterface $customer
     * @param WebshopItemInterface $webshopItem
     * @param string|null $error
     * @return bool
     */
    public function isWebshopItemOrderable(
        User $user,
        CustomerWithPermissionGroupsInterface $customer,
        WebshopItemInterface $webshopItem,
        &$error = null
    );
}