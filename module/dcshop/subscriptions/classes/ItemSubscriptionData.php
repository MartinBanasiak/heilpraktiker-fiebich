<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\traits\universallyGettableTrait;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 02.07.2015
 * Time: 14:28
 */
class ItemSubscriptionData
{

    use universallyGettableTrait;

    /**
     * @var WebshopItemInterface
     */
    protected $webshopItem;

    /**
     * @var SubscriptionHeaderCollection
     */
    protected $subscriptionHeaderCollection;

    /**
     * @var SubscriptionSequenceStepCollection
     */
    protected $subscriptionSeqStepCollection;

    /**
     * @var array
     */
    protected $subscriptionItemLinks;

    /**
     * @var array
     */
    protected $subscriptionPriceData;

    /**
     * @param WebshopItemInterface $webshopItem
     * @param SubscriptionHeaderCollection $subscriptionHeaderCollection
     * @param SubscriptionSequenceStepCollection $subscriptionSeqStepCollection
     * @param array $subscriptionItemLinks
     * @param array $subscriptionPriceData
     */
    public function __construct(
            WebshopItemInterface $webshopItem,
            SubscriptionHeaderCollection $subscriptionHeaderCollection,
            SubscriptionSequenceStepCollection $subscriptionSeqStepCollection,
            array $subscriptionItemLinks,
            array $subscriptionPriceData) {
        $this->webshopItem = $webshopItem;
        $this->subscriptionHeaderCollection = $subscriptionHeaderCollection;
        $this->subscriptionSeqStepCollection = $subscriptionSeqStepCollection;
        $this->subscriptionItemLinks = $subscriptionItemLinks;
        $this->subscriptionPriceData = $subscriptionPriceData;
    }

    /**
     * @return bool
     */
    public function hasData() {
        $firstHeader = $this->subscriptionHeaderCollection->getFirst();
        return ($this->subscriptionHeaderCollection->getFirst()->id > 0);
    }

    /**
     * @return bool
     */
    public function isTypeOrderOption() {
        if(!$this->hasData()) return false;
        return ((int)$this->subscriptionHeaderCollection->getFirst()->type === 0);
    }

    /**
     * @return bool
     */
    public function isTypeSequenceItem() {
        if(!$this->hasData()) return false;
        return ((int)$this->subscriptionHeaderCollection->getFirst()->type === 1);
    }

    /**
     * @param $headerName
     * @return SubscriptionItemLink|bool
     */
    public function getItemLink($headerName) {
        return isset($this->subscriptionItemLinks[$headerName]) ? $this->subscriptionItemLinks[$headerName] : false;
    }

    /**
     * @param $headerName
     * @return ItemPriceData|SubscriptionItemPriceDecorator|bool
     */
    public function getSubscriptionPriceData($headerName) {
        return isset($this->subscriptionPriceData[$headerName]) ? $this->subscriptionPriceData[$headerName] : false;
    }

    /**
     * @return SubscriptionSequenceStepCollection
     */
    public function getSequenceSteps() {
        return $this->subscriptionSeqStepCollection;
    }

    /**
     * @return SubscriptionHeaderCollection
     */
    public function getSubscriptionHeaders() {
        return $this->subscriptionHeaderCollection;
    }

    /**
     * @return WebshopItemInterface
     */
    public function getItem() {
        return $this->webshopItem;
    }

    public function getSubscriptionShippingOptionLineNo() {
        $firstHeader = $this->subscriptionHeaderCollection->getFirst();
        return $firstHeader->shipping_option_line_no;
    }


}