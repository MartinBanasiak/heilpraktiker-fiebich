<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\DOMTemplate;
use DynCom\dc\common\interfaces\PHTMLTemplate;
use DynCom\dc\common\interfaces\PlainTemplate;
use DynCom\dc\common\interfaces\Template;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 1:21 AM
 */

class TemplateInserterFactory {

    /**
     * @param Template $template
     * @param array $fieldData
     * @param bool $allowTags
     * @param string $delimiter
     * @param string $prefix
     * @return TemplateDefaultInserter|TemplateDOMInserter|TemplateStringInserter
     */
    public function getInserter(Template $template, array $fieldData, $allowTags = true, $delimiter = '%', $prefix = 'tmplinsert-') {
        //Default is PHTML-Template
        if($template instanceof PHTMLTemplate) {
            $inserter = new TemplateDefaultInserter($template,(object)$fieldData,true);
        } elseif($template instanceof PlainTemplate) {
            $inserter = new TemplateStringInserter($template,$fieldData,true,$delimiter);
        } elseif($template instanceof DOMTemplate) {
            $inserter = new TemplateDOMInserter($template,$fieldData,true,$prefix);
        }
        if(!isset($inserter)) {
            throw new \InvalidArgumentException('Template is not an allowed type. Allowed types: Default, String, DOM.');
        }
        return $inserter;
    }

}