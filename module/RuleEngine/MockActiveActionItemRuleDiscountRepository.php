<?php
namespace DynCom\dc\RuleEngine;
use DynCom\dc\dcShop\campaigns\GenericCampaignElementCollection;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\NullLineDiscount;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.10.2016
 * Time: 00:11
 */
class MockActiveActionItemRuleDiscountRepository implements ActiveActionItemRuleDiscountRepositoryInterface
{
    /**
     * @param GenericLineDiscount $discount
     * @param GenericCampaignElementCollection $collection
     * @return null
     */
    public function addRuleDiscount(GenericLineDiscount $discount, GenericCampaignElementCollection $collection)
    {
        return null;
    }

    /**
     * @param $sourceID
     * @return bool
     */
    public function discountExists($sourceID)
    {
        return false;
    }

    /**
     * @param $sourceID
     * @return NullLineDiscount
     */
    public function getDiscount($sourceID)
    {
        return new NullLineDiscount();
    }

    /**
     * @param WebshopItemInterface $item
     * @return array
     */
    public function getMatchingDiscountsForItem(WebshopItemInterface $item)
    {
        return [];
    }

    /**
     * @param $sourceID
     * @return null
     */
    public function removeDiscount($sourceID)
    {
        return null;
    }


}