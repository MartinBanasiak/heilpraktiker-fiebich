<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\WebshopItem;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 11:21
 */

interface IPriceProviderStrategy {

    /**
     * @param WebshopItem $item
     *
     * @param $variantCode
     * @param $quantity
     * @param |Customer $customer
     * @param $currencyCode
     * @return float
     */
    public function executePriceStrategy( WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode );

}