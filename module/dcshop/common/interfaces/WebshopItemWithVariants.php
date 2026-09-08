<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.10.2015
 * Time: 13:59
 */
interface WebshopItemWithVariants extends WebshopItemInterface
{

    /**
     * @return mixed
     */
    public function getAllVariants();

    /**
     * @param $identifier
     * @return mixed
     */
    public function getVariantByIdentifier($identifier);

}