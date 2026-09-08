<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 7/14/2015
 * Time: 11:11 AM
 */
trait genericPlainTemplateTrait
{

    protected $rawContent = '';
    protected $delimiter = '';

    /**
     * @return string
     */
    public function getDelimiter() {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }


    /**
     * @return array
     */
    public function getFieldsAuto()
    {
        $rawContent = $this->getRawContent();
        $delimiter = $this->getDelimiter();
        $tokenRegex = "/(?<=\\{\\$delimiter)(.*?)(?=\\$delimiter\\})/";
        $fieldsArr = [];
        preg_match_all($tokenRegex,$rawContent,$fieldsArr);
        if(is_array($fieldsArr[0])){
            return $fieldsArr[0];
        }
        return [];
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->getFieldsAuto();
    }

}