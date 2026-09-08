<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:17
 */

trait hasConfigTrait {

    protected $config;

    public function getConfig() {
        return $this->config;
    }

}