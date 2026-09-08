<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 18.06.2015
 * Time: 10:27
 */

interface Template {

    public function getFields();

    public function getRenderedContent();

    /**
     * @param $content
     * @return mixed
     */
    public function setRenderedContent($content);

}