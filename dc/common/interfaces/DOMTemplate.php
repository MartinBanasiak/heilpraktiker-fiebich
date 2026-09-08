<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 10:27 PM
 */

interface DOMTemplate extends Template {

    /**
     * @return \DOMDocument
     */
    public function getDOMDocument();

    /**
     * @return string
     */
    public function getInserterTokenPrefix();

}