<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\IPriceProviderStrategy;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 24.03.2015
 * Time: 15:23
 */

class SimplePriceStrategy implements IPriceProviderStrategy {

    protected $shop;
    protected $vatManager;

    /**
     * SimplePriceStrategy constructor.
     * @param Shop $shop
     * @param VATManager $vatManager
     */
    public function __construct(Shop $shop, VATManager $vatManager ) {
        $this->shop       = $shop;
        $this->vatManager = $vatManager;
    }

    /**
     * @param WebshopItem $item
     * @param $variantCode
     * @param $quantity
     * @param Customer $customer
     * @param $currencyCode
     */
    public function executePriceStrategy(WebshopItem $item, $variantCode, $quantity, Customer $customer, $currencyCode ) {

    }

}