<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 08:51
 */
interface ItemInventoryStrategyInterface
{

    /**
     * @param WebshopItemInterface $item
     * @return float
     * @throws \ErrorException
     */
    public function getItemInventory(WebshopItemInterface $item);

    /**
     * @param $itemID
     * @return float
     * @throws \ErrorException
     */
    public function getItemInventoryByID($itemID);

    /**
     * @param array $altPrimary
     * @return float
     * @throws \ErrorException
     */
    public function getItemInventoryByAltPrimary(array $altPrimary);


}