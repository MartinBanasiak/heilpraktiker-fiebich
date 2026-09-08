<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 13:55
 */
interface Observer
{

    /**
     * @param $eventName
     * @param $data
     * @return mixed
     */
    public function notify($eventName, $data);

}