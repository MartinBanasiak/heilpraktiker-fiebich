<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 15.03.2016
 * Time: 12:33
 */
interface WebshopItemWithDescriptions extends WebshopItemInterface
{

    /**
     * @return mixed
     */
    public function getDescriptions();

    /**
     * @param $index
     * @return mixed
     */
    public function getDescriptionByIndex($index);

}