<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\IPriceProvider;
use DynCom\dc\dcShop\interfaces\IPriceProviderStrategy;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 11:48
 */

class PriceProvider implements IPriceProvider {

    protected $strategy;

    /**
     * PriceProvider constructor.
     * @param IPriceProviderStrategy $strategy
     */
    public function __construct(IPriceProviderStrategy $strategy) {
        $this->strategy = $strategy;
    }

    /**
     * @param IPriceProviderStrategy $strategy
     */
    public function setPriceProviderStrategy(IPriceProviderStrategy $strategy ) {
        $this->strategy = $strategy;
    }

    /**
     * @return IPriceProviderStrategy
     */
    public function getPriceProviderStrategy() {
        return $this->strategy;
    }

    /**
     * @param WebshopItem $item
     * @param Customer $customer
     * @param $quantity
     */
    public function getItemPriceForCustomerAndQuantity(WebshopItem $item, Customer $customer, $quantity ) {
        $this->strategy->executePriceStrategy($item,$customer,$quantity);
    }

}