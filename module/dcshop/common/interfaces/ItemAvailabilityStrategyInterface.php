<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\WebshopItem;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.07.2015
 * Time: 05:15
 */
interface ItemAvailabilityStrategyInterface
{
    /**
     * @param WebshopItem $item
     * @return string|int
     */
    public function getItemAvailabilityCode(WebshopItem $item);

    /**
     * @param string|int $code
     * @return string
     */
    public function getItemAvailabilityTextConstantCode($code);

    /**
     * @param string|int $code
     * @return string
     */
    public function getItemAvailabilityClassName($code);
}