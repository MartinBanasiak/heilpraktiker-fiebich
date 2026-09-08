<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 18.06.2015
 * Time: 10:34
 */

interface GenericTemplateInserterInterface {

    /**
     * @param array $fieldData
     * @return mixed
     */
    public function setFieldData(array $fieldData );

    /**
     * @param Template $template
     * @return mixed
     */
    public function setTemplate(Template $template );

    public function replaceFieldsInTemplate();

}