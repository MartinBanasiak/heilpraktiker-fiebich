<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 04.11.2015
 * Time: 10:01
 */
interface TemplateEngine
{
    /**
     * @param $template
     * @param $context
     * @return mixed
     */
    public function render($template, $context);
}