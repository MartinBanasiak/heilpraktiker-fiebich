<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2015
 * Time: 12:11
 */
interface CustomerWithPermissionGroupsInterface extends CustomerInterface
{

    /**
     * @return array
     */
    public function getPermissionGroupCodes();

}