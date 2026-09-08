<?php
namespace DynCom\dc\RuleEngine;
use DynCom\dc\dcShop\campaigns\GenericCampaignElementCollection;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 21.10.2015
 * Time: 08:46
 */
interface ActiveActionItemRuleDiscountRepositoryInterface
{
    /**
     * @param GenericLineDiscount $discount
     * @param GenericCampaignElementCollection $collection
     * @return mixed
     */
    public function addRuleDiscount(GenericLineDiscount $discount, GenericCampaignElementCollection $collection);

    /**
     * @param $sourceID
     * @return mixed
     */
    public function discountExists($sourceID);

    /**
     * @param $sourceID
     * @return mixed
     */
    public function getDiscount($sourceID);

    /**
     * @param WebshopItemInterface $item
     * @return mixed
     */
    public function getMatchingDiscountsForItem(WebshopItemInterface $item);

    /**
     * @param $sourceID
     * @return mixed
     */
    public function removeDiscount($sourceID);
}