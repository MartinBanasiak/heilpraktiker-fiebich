<?php
namespace DynCom\dc\common\abstracts;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 18.06.2015
 * Time: 10:09
 */

abstract class TemplateInserterBase {

    protected $template;
    protected $fieldData;
    protected $allowTags = false;

    /**
     * TemplateInserterBase constructor.
     * @param GenericTemplateInterface $template
     * @param array $fieldData
     * @param bool $allowTags
     */
    public function __construct(GenericTemplateInterface $template, array $fieldData, $allowTags = false) {
        $this->template = $template;
        $this->fieldData = (($this->_validateFieldData($fieldData)) ? $fieldData : array());
        $this->allowTags = (bool)$allowTags;
    }

    /**
     * @param array $fieldData
     * @return mixed
     */
    abstract protected function _validateFieldData(array $fieldData);

    abstract public function insertData();
}