<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 10:25
 */
interface OrderItemInterface extends OrderableEntityInterface
{

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @return mixed
     */
    public function getOrderID();

    /**
     * @return mixed
     */
    public function getItem();
}