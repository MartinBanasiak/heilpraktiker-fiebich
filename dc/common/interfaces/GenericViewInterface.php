<?php
namespace DynCom\dc\common\interfaces;
use DynCom\dc\common\classes\TemplateInserterFactory;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 22.01.2015
 * Time: 14:22
 */
interface GenericViewInterface {

    /**
     * @param TemplateInserterFactory $inserterFactory
     */
    public function render(TemplateInserterFactory $inserterFactory);

    /**
     * @return bool
     */
    public function isRendered();

    /**
     * @return string
     */
    public function getContent();

    public function echoContent();



}