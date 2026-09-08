<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\Address;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\classes\PaymentOption;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 14:27
 */
class SubscriptionRequest
{

    /**
     * @var CurrShopConfiguration
     */
    protected $currShopConfiguration;
    /**
     * @var WebshopItemInterface
     */
    protected $webshopItem;
    /**
     * @var ItemSubscriptionData
     */
    protected $subscriptionItemData;
    /**
     * @var Address
     */
    protected $shipmentAddress;
    /**
     * @var Address
     */
    protected $billingAddress;
    /**
     * @var PaymentOption
     */
    protected $paymentOption;
    /**
     * @var PaymentOption
     */
    protected $shipmentOption;

}