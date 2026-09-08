<?php
namespace DynCom\dc\common\classes;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DynCom\dc\common\interfaces\View;
use Traversable;

/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 1:13 AM
 */
class RenderableStringTransformer
{

    /**
     * @param $element
     * @return string
     */
    public function toString($element) {
        $el = '';
        //Resolve callable
        $element = is_callable($element) ? $element() : $element;
        if(((is_scalar($element) || is_bool($element)))) {
            $el = (string)$element;
        } elseif($element instanceof DOMDocument) {
            $el = $element->saveXML($element->firstChild);
        } elseif( $element instanceof DOMNode) {
            $el = $element->ownerDocument->saveXML($element);
        } elseif( $element instanceof DOMNodeList) {
            $wrapperString = '';
            $len = $element->length;
            for($i = 0;$i < $len;++$i) {
                $curr = $element->item($i);
                $wrapperString .= $curr->ownerDocument->saveXML($curr);
            }
        } elseif($element instanceof View) {
            $el = $element->render();
        } elseif($element instanceof Form) {
            if(!$element->isFormRendered()) {
                $element->render();
            }
            $el = $element->getRenderedContent();
        } elseif ($element instanceof Traversable) {
            $wrapperString = '';
            foreach($element as $elementField) {
                $wrapperString .= $this->toString($elementField);
            }
        }
        return $el;
    }

}