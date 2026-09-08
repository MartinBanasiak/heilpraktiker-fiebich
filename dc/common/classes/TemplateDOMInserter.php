<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\abstracts\TemplateInserterBase;
use DynCom\dc\common\interfaces\DOMTemplate;
use DynCom\dc\common\interfaces\GenericViewInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/22/2015
 * Time: 6:02 PM
 */

class TemplateDOMInserter extends TemplateInserterBase {


    protected $prefix = 'tmplinsert-';

    /**
     * TemplateDOMInserter constructor.
     * @param DOMTemplate $template
     * @param array $fieldData
     * @param bool $allowTags
     * @param string $replacementAttrPrefix
     */
    public function __construct(DOMTemplate $template, array $fieldData, $allowTags = false, $replacementAttrPrefix = 'tmplinsert-') {
        $this->allowTags = false;
        $this->template = $template;
        $this->allowTags = (bool)$allowTags;
        $this->fieldData = (($this->_validateFieldData($fieldData)) ? $fieldData : array());
        if(strip_tags($replacementAttrPrefix) === $replacementAttrPrefix) {
            $this->prefix = $replacementAttrPrefix;
        }
    }

    /**
     * @param array $fieldValues
     * @return bool
     */
    protected function _validateFieldData(array $fieldValues) {
        foreach($fieldValues as $key => $value) {
           if(
               !($value instanceof \DOMNode) &&
               !($value instanceof GenericViewInterface) &&
               !($value instanceof Form) &&
               !($this->allowTags && is_scalar($value)) &&
               !(!$this->allowTags && (strip_tags($value) === (string)$value))
           ) {
               return false;
           }
        }
        return true;
    }

    public function insertData() {
        $doc = $this->template->getDOMDocument();
        $XPath = new \DOMXPath($doc);
        $query = "//@*[starts-with(name(),'{$this->prefix}')]";
        $nodeList = $XPath->query($query);
        $nodeArr = [];
        $len = $nodeList->length;
        $el = $doc->createTextNode('');
        for($i = 0;$i <= $len;++$i) {
            $node = $nodeList->item($i);
            $attrName = $node->nodeName;
            $fieldName = str_replace($this->prefix, '', $attrName);
            $owner = $node->parentNode;
            if($owner instanceof \DOMElement) {
                $owner->removeAttribute($attrName);
                $nodeArr[$fieldName] = $owner;
            }

        }

        foreach($this->fieldData as $key => $val) {
            //Resolve callable
            $val = is_callable($val) ? $val() : $val;
            //Resolve stringlike no tags
            if(((is_scalar($val) || is_bool($val)) && (strip_tags($val) === (string)$val))) {
                $el = $doc->createTextNode((string)$val);
            //Resolve stringlike with tags
            } elseif((is_scalar($val) || is_bool($val))) {
                $domImpl = new \DOMImplementation();
                $tmpDoc = $domImpl->createDocument(null, 'html',
                    $domImpl->createDocumentType("html",
                        "-//W3C//DTD XHTML 1.0 Transitional//EN",
                        "/dc/common/xhtml11.dtd"));
                $tmpDoc->formatOutput = true;
                @$tmpDoc->loadXML((string)$val);
                $el = $doc->importNode($tmpDoc->documentElement);
                unset($tmpDoc);
            //Resolve DOMDocument
            } elseif($val instanceof \DOMDocument) {
                $el = $doc->importNode($val->firstChild,true);
                //Resolve DOMNodes
            } elseif( $val instanceof \DOMNode) {
                $el = $doc->importNode($val,true);
            //Resolve Sub-Views
            } elseif($val instanceof GenericViewInterface) {
                if(!$val->isRendered()) {
                    $val->render(new TemplateInserterFactory());
                }
                $el = $val->getContent();
            //Resolve Forms
            } elseif($val instanceof Form) {
                if(!$val->isFormRendered()) {
                    $val->render();
                }
                $renderedVal = $val->getDOMDocument();
                $el = $doc->importNode($renderedVal->documentElement->firstChild,true);
            }

            $nodeArr[$key]->appendChild($el);

        }
        $this->template->setRenderedContent($doc->saveXML($doc->documentElement));
    }

    /**
     * @param $prefix
     */
    public function setReplacementDataPrefix($prefix) {
        $this->prefix = strip_tags($prefix);
    }

}