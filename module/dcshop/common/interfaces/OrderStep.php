<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 01:12
 */
interface OrderStep
{

    public function getFields();

    public function getFieldNames();

    /**
     * Sets input-conditional validation conditions
     * prepares validated entities for Order-hydration
     * and executes side-effects (Login, User-Create,Payment etc)
     * via delegation
     *
     * @param Order order
     */
    public function processRequest(Order $order);

    public function isRequestValid();

    /**
     * @param Order $order
     * @return mixed
     */
    public function hydrateOrder(Order $order);

    public function renderViewClean();

    public function renderViewPrefilledFromSuperglobal();

    public function renderViewPrefilledFromSuperglobalWithErrors

}