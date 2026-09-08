<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 12:05 AM
 */

interface PlainTemplate extends Template{
    /**
     * @return string
     */
    public function getRawContent();

    /**
     * @return string
     */
    public function getDelimiter();

}