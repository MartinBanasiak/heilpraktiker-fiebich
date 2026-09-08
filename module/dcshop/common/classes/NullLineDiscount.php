<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\Discount;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.10.2016
 * Time: 00:18
 */
class NullLineDiscount implements Discount
{
    /**
     * @return string
     */
    public function getType()
    {
        return '';
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return 0.00;
    }

    /**
     * @param $price
     * @return mixed
     */
    public function getResultForPrice($price)
    {
        return $price;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSourceID()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getNotification()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return '';
    }

}