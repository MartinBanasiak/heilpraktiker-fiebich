<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\classes\WebshopItemOrderableEntityDecorator;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\RuleEngine\ruleConditionTrait;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 27.10.2015
 * Time: 23:20
 */
trait campaignConditionTrait
{
    use ruleConditionTrait;

    protected $elementsToFilterBy;
    protected $ruleKey;

    /**
     * @param UserBasket $basket
     * @param GenericCampaignElementCollection $collection
     * @param WebshopItemBuilder $itemBuilder
     * @return array
     */
    protected function filterBasketByCampaignElementCollection(UserBasket $basket, GenericCampaignElementCollection $collection,WebshopItemBuilder $itemBuilder)
    {
        $filteredItems = [];
        /**
         * @var $basketEntity BasketEntity
         */
        foreach ($basket as $basketEntity) {
            $isOrderableTypeItem = $basketEntity->getOrderableType() === WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM;
            $isUserCreated = $basketEntity->getQtySourceType() === ItemPriceData::QTY_SOURCE_USER && !$basketEntity->isQtyProtected();
            if($isOrderableTypeItem && $isUserCreated) {
                $orderableEntity = $basketEntity->getOrderableEntity();
                $withCategories = $itemBuilder->decorateWebshopItemCategories($orderableEntity);
                if ($collection->itemMeetsCriteria($withCategories)) {
                    $filteredItems[] = $basketEntity;
                }
            }
        }
        return $filteredItems;
    }

    /**
     * @param $basketEntities
     * @return float
     */
    protected function sumLineAmounts($basketEntities)
    {
        $sumLineAmnts = 0.00;
        foreach ($basketEntities as $basketEntity) {
            if($basketEntity instanceof BasketEntity) {
                $sumLineAmnts += $basketEntity->getLineAmount();
            }
        }
        return $sumLineAmnts;
    }

    /**
     * @param $basketEntities
     * @return float
     */
    protected function sumQtys($basketEntities)
    {
        $sumQtys = 0.00;
        foreach ($basketEntities as $basketEntity) {
            if($basketEntity instanceof BasketEntity) {
                $sumQtys += $basketEntity->getQuantity();
            }
        }
        return $sumQtys;
    }


}