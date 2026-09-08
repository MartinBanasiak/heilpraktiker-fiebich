<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 11:21 PM
 */

interface PHTMLTemplate extends Template {

    /**
     * @return string
     */
    public function getPHTMLPath();

}