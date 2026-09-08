<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 10:20 AM
 */

interface IOCInterface {

    /**
     * @param $name
     * @return mixed
     */
    public function resolve($name);

    /**
     * @param $name
     * @param $rule
     * @return mixed
     */
    public function register($name, $rule);

}