<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.10.2015
 * Time: 01:45
 */
interface WebshopItemWithImages extends WebshopItemInterface
{

    /**
     * @return mixed
     */
    public function getImageData();

    /**
     * @return mixed
     */
    public function getMainImageData();

}