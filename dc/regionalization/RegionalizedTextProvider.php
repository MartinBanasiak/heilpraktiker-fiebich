<?php
namespace DynCom\dc\regionalization;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 13.10.2016
 * Time: 22:09
 */
interface RegionalizedTextProvider
{
    /**
     * @param $name
     * @param null|string $default
     * @return mixed
     */
    public function getRegionalizedText($name, $default = null);
}