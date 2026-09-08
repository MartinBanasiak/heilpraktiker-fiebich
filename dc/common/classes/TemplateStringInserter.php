<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\abstracts\TemplateInserterBase;
use DynCom\dc\common\interfaces\GenericViewInterface;
use DynCom\dc\common\interfaces\PlainTemplate;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/18/2015
 * Time: 10:52 PM
 */

class TemplateStringInserter extends TemplateInserterBase {

    const DELIMITER_PERCENT = '%';
    const DELIMITER_TILDE = '~';
    const DELIMITER_PIPE = '~';

    protected $allowedDelimiters = array(
        self::DELIMITER_PERCENT,
        self::DELIMITER_TILDE,
        self::DELIMITER_PIPE
    );

    protected $delimiter = '%';

    /**
     * TemplateStringInserter constructor.
     * @param PlainTemplate $template
     * @param array $fieldData
     * @param bool $allowTags
     * @param string $delimiter
     */
    public function __construct(PlainTemplate $template, array $fieldData, $allowTags = false, $delimiter = '%') {
        $this->template = $template;
        $this->fieldData = (($this->_validateFieldData($fieldData)) ? $fieldData : array());
        $this->allowTags = (bool)$allowTags;
        if(isset($delimiter) && (in_array($delimiter,$this->allowedDelimiters,true))) {
            $this->delimiter = $delimiter;
        }
    }

    /**
     * @param array $fieldValues
     * @return bool
     */
    protected function _validateFieldData(array $fieldValues) {
        foreach($fieldValues as $key => $value) {
            if(
                ($this->allowTags && !is_scalar($value)) ||
                (!$this->allowTags && (!is_scalar($value) ||
                (strip_tags($value) !== (string)$value)))) {
                return false;
            }
        }
        return true;
    }

    public function insertData() {
        $content = $this->template->getTemplateString();
        foreach($this->fieldData as $key => $val) {
            $token = $this->delimiter . $key . $this->delimiter;
            $val = is_callable($val) ? $val() : $val;
            $valIsDOMDocument = $val instanceof \DOMDocument;
            $valIsDOMNode = $val instanceof \DOMNode;
            $valIsView = $val instanceof GenericViewInterface;
            if($valIsDOMNode) {
                $val = $val->ownerDocument->saveXML($val);
            } elseif($valIsDOMDocument) {
                $val = $val->saveXML($val->documentElement);
            } elseif($valIsView) {
                if($val->isRendered) $val = $val->getContent();
                else {
                    $val->render(new TemplateInserterFactory());
                    $val = $val->getContent();
                }
            }
            $content = str_replace($token,$val,$content);
        }
        $this->template->setRenderedContent($content);
    }

    /**
     * @param $delimiter
     */
    public function setReplacementDelimiter($delimiter) {
        if(in_array($delimiter,$this->allowedDelimiters,true)) {
            $this->delimiter = $delimiter;
        }
    }

}