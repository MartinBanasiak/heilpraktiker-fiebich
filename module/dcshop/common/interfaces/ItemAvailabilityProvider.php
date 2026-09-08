<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2015
 * Time: 11:33
 */

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 10.07.2015
 * Time: 10:11
 */
interface ItemAvailabilityProvider
{
    /**
     * @param WebshopItemInterface $item
     * @return string
     * @throws \ErrorException
     */
    public function getItemAvailability(WebshopItemInterface $item);

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isItemAvailable(WebshopItemInterface $item);

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function hasItemLowAvailability(WebshopItemInterface $item);

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isItemUnavailable(WebshopItemInterface $item);

    /**
     * @param WebshopItemInterface $item
     * @return bool
     */
    public function isItemOrderable(WebshopItemInterface $item);

    /**
     * @param WebshopItemInterface $item
     * @return mixed
     */
    public function getInventory(WebshopItemInterface $item);

    /**
     * @param $itemID
     * @return mixed
     */
    public function getInventoryByItemID($itemID);

    /**
     * @param array $altPrimary
     * @return mixed
     */
    public function getInventoryByAltPrimary(array $altPrimary);

    /**
     * @param $itemID
     * @return mixed
     */
    public function getItemAvailabilityByID($itemID);

    /**
     * @param array $altPrimary
     * @return mixed
     */
    public function getItemAvailabilityByAltPrimary(array $altPrimary);

    /**
     * @param $itemID
     * @return mixed
     */
    public function isItemOrderableByID($itemID);

    /**
     * @param array $altPrimary
     * @return mixed
     */
    public function isItemOrderableByAltPrimary(array $altPrimary);

    /**
     * @param array $altPrimary
     * @param string $variantCode
     * @return mixed
     */
    public function isItemOrderableByAltPrimaryAndVariantCode(array $altPrimary, $variantCode = '');

    /**
     * @param WebshopItemInterface $item
     * @return int
     */
    public function getItemAvailabilityStatus(WebshopItemInterface $item);

}