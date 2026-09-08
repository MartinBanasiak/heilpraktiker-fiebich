<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 20:59
 */
interface Discount
{

    /**
     * @return string
     */
    public function getType();

    /**
     * @return float
     */
    public function getValue();

    /**
     * @param float $price
     * @return float
     */
    public function getResultForPrice($price);

    /**
     * @return string
     */
    public function getSourceType();

    /**
     * @return mixed
     */
    public function getSourceID();

    /**
     * @return string
     */
    public function getNotification();

    /**
     * @return string
     */
    public function getValueType();

}