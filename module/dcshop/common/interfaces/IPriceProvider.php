<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\WebshopItem;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 11:19
 */

interface IPriceProvider {

    /**
     * @param WebshopItem $item
     *
     * @param |Customer $customer
     * @param $quantity
     * @return float
     */
    public function getItemPriceForCustomerAndQuantity( WebshopItem $item, Customer $customer, $quantity );

    /**
     * @param IPriceProviderStrategy $strategy
     * @return
     */
    public function setPriceProviderStrategy( IPriceProviderStrategy $strategy );

    /**
     * @return IPriceProviderStrategy
     */
    public function getPriceProviderStrategy();

}