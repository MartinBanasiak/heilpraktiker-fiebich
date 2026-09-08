<?php
namespace DynCom\dc\common\classes;
use DOMDocument;
use DOMImplementation;
use DOMNode;
use DOMText;
use DynCom\dc\common\interfaces\GenericViewInterface;
use Traversable;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/13/2015
 * Time: 8:28 PM
 */
class RenderableElementResolver
{

    const TARGET_TYPE_DOMELEMENT = 'DOMElement';
    const TARGET_TYPE_STRING = 'string';
    const TARGET_TYPE_PHTML = 'PHTML';


    /**
     * @param $typeName
     * @return bool
     */
    public function isAllowedTargetType($typeName) {
        return(in_array($typeName,[
            self::TARGET_TYPE_STRING,
            self::TARGET_TYPE_DOMELEMENT,
            self::TARGET_TYPE_PHTML
        ],true));
    }

    /**
     * @param $element
     * @param string $targetType
     * @return DOMNode|DOMText|string
     */
    public function resolveElement($element,$targetType = self::TARGET_TYPE_STRING) {
       if($targetType === self::TARGET_TYPE_STRING) {
           return $this->resolveElementToString($element);
       } elseif($targetType === self::TARGET_TYPE_DOMELEMENT) {
           return $this->resolveElementToDOMElement($element);
       } elseif($targetType === self::TARGET_TYPE_PHTML) {
           return $this->resolveElementToPHTMLField($element);
       }
        throw new \InvalidArgumentException(
          "targetType must be string or DOMElement."
        );
    }

    /**
     * @param $element
     * @return DOMNode|DOMText
     */
    public function resolveElementToDOMElement($element) {

        $domImpl = new DOMImplementation();
        $doc = $domImpl->createDocument(null, 'html',
            $domImpl->createDocumentType("html",
                "-//W3C//DTD XHTML 1.0 Transitional//EN",
                "/dc/common/xhtml11.dtd"));
        $doc->formatOutput = true;
        $el = $doc->createTextNode('');
        //Resolve callable
        $element = is_callable($element) ? $element() : $element;
        //Resolve stringlike no tags
        if(((is_scalar($element) || is_bool($element)) && (strip_tags($element) === (string)$element))) {
            $el = &$doc->createTextNode((string)$element);
            //Resolve stringlike with tags
        } elseif((is_scalar($element) || is_bool($element))) {
            $tmpDOM = $domImpl->createDocument(null, 'html',
                $domImpl->createDocumentType("html",
                    "-//W3C//DTD XHTML 1.0 Transitional//EN",
                    "/dc/common/xhtml11.dtd"));
            $tmpDOM->formatOutput = true;
            @$tmpDOM->loadXML($element);
            $el = $doc->importNode($tmpDOM->documentElement);
            //Resolve DOMNodes
        } elseif( $element instanceof DOMNode) {
            $el = $doc->importNode($element,true);
            //Resolve DOMDocument
        } elseif($element instanceof DOMDocument) {
            $el = $doc->importNode($element->firstChild,true);
        } elseif($element instanceof GenericViewInterface) {
            if(!$element->isRendered()) {
                $element->render(new TemplateInserterFactory());
            }
            $tmpDOM = $domImpl->createDocument(null, 'html',
                $domImpl->createDocumentType("html",
                    "-//W3C//DTD XHTML 1.0 Transitional//EN",
                    "/dc/common/xhtml11.dtd"));
            $tmpDOM->formatOutput = true;
            @$tmpDOM->loadXML($element->getContent());
            $el = $doc->importNode($tmpDOM->documentElement);
        } elseif($element instanceof Form) {
            if(!$element->isFormRendered()) {
                $element->render();
            }
            $tmpDOM = $domImpl->createDocument(null, 'html',
                $domImpl->createDocumentType("html",
                    "-//W3C//DTD XHTML 1.0 Transitional//EN",
                    "/dc/common/xhtml11.dtd"));
            $tmpDOM->formatOutput = true;
            @$tmpDOM->loadXML($el = $element->getRenderedContent());
            $el = $doc->importNode($tmpDOM->documentElement);
        } elseif ($element instanceof Traversable) {
            $arr = [];
            foreach($element as $elementField) {
                $arr[] = $this->resolveElementToDOMElement($elementField);
            }
            $DOMElWrapper = $doc->createElement('div');
            foreach($arr as $arrEl) {
                $DOMElWrapper->appendChild($arrEl);
            }
            $el = $DOMElWrapper;
        }
        return $el;
    }

    /**
     * @param $element
     * @return string
     */
    public function resolveElementToString($element) {
        $el = '';
        //Resolve callable
        $element = is_callable($element) ? $element() : $element;
        if(((is_scalar($element) || is_bool($element)))) {
            $el = (string)$element;
        } elseif( $element instanceof DOMNode) {
            $el = $element->ownerDocument->saveXML($element);
        } elseif($element instanceof DOMDocument) {
            $el = $element->saveXML($element->firstChild);
        } elseif($element instanceof GenericViewInterface) {
            if(!$element->isRendered()) {
                $element->render(new TemplateInserterFactory());
            }
            $el = $element->getContent();
        } elseif($element instanceof Form) {
            if(!$element->isFormRendered()) {
                $element->render();
            }
            $el = $element->getRenderedContent();
        } elseif ($element instanceof Traversable) {
            $wrapperString = '';
            foreach($element as $elementField) {
                $wrapperString .= $this->resolveElementToString($elementField);
            }
        }
        return $el;
    }

    /**
     * @param $element
     * @return mixed
     */
    public function resolveElementToPHTMLField($element) {
        //Do nothing
        return $element;
    }

}