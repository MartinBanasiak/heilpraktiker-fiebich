<?php
namespace DynCom\dc\RuleEngine;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\campaigns\GenericCampaignElementCollection;
use DynCom\dc\dcShop\classes\GenericLineDiscount;
use DynCom\dc\dcShop\classes\Shop;
use DynCom\dc\dcShop\classes\WebshopItemCategoryDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithCategories;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 21.10.2015
 * Time: 08:46
 */
class ActiveActionItemRuleDiscountRepository implements ActiveActionItemRuleDiscountRepositoryInterface
{

    private $ruleDiscounts = [];
    /**
     * @var \DynCom\dc\common\classes\PDOQueryWrapper
     */
    private $pdo;
    /**
     * @var Shop
     */
    private $shop;

    /**
     * ActiveActionItemRuleDiscountRepository constructor.
     * @param PDOQueryWrapper $pdo
     * @param Shop $shop
     */
    public function __construct(PDOQueryWrapper $pdo, Shop $shop) {
        $this->pdo = $pdo;
        $this->shop = $shop;
    }

    /**
     * @param GenericLineDiscount $discount
     * @param GenericCampaignElementCollection $collection
     */
    public function addRuleDiscount(GenericLineDiscount $discount, GenericCampaignElementCollection $collection)
    {
        if(!($discount->getSourceType() === GenericLineDiscount::DISCOUNT_SOURCE_TYPE_RULE)) {
            throw new \InvalidArgumentException('Discount must have source type DISCOUNT_SOURCE_TYPE_RULE.');
        }
        if($this->discountExists($discount->getSourceID())) {
            return;
        }
        $this->ruleDiscounts[$discount->getSourceID()]['discount'] = $discount;
        $this->ruleDiscounts[$discount->getSourceID()]['elementCollection'] = $collection;
    }

    /**
     * @param $sourceID
     * @return bool
     */
    public function discountExists($sourceID)
    {
        $sourceID = (string)$sourceID;
        return array_key_exists($sourceID,$this->ruleDiscounts);
    }

    /**
     * @param $sourceID
     * @return mixed
     */
    public function getDiscount($sourceID)
    {
        if(!$this->discountExists($sourceID)) {
            throw new \InvalidArgumentException('There is no active RuleDiscount with the id.' . $sourceID);
        }
        return $this->ruleDiscounts[$sourceID]['discount'];
    }

    /**
     * @param WebshopItemInterface $item
     * @return array
     */
    public function getMatchingDiscountsForItem(WebshopItemInterface $item)
    {

        if(!($item instanceof WebshopItemWithCategories)) {
            $decoratedItem = new WebshopItemCategoryDecorator($item,$this->pdo,$this->shop->getUseCategoriesFromShopCode());
        } else {
            $decoratedItem = $item;
        }
        $arr = [];
        foreach($this->ruleDiscounts as $discountArr) {
            $collection = $discountArr['elementCollection'];
            if($collection instanceof GenericCampaignElementCollection) {
                if($collection->itemMeetsCriteria($decoratedItem)) {
                    $arr[] = $discountArr['discount'];
                }
            }
        }
        return $arr;
    }


    /**
     * @param $sourceID
     */
    public function removeDiscount($sourceID)
    {
        $sourceID = (string)$sourceID;
        if(!$this->discountExists($sourceID)) {
            throw new \InvalidArgumentException('There is no active RuleDiscount with the id.' . $sourceID);
        }
        unset($this->ruleDiscounts[$sourceID]);
    }

}