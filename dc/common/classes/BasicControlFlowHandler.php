<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 1:17 PM
 */

class BasicControlFlowHandler {

    /**
     * @param array $controlFlowArray
     */
    public function evaluateControlFlowArray( array &$controlFlowArray ) {
        foreach($controlFlowArray as &$controlFlowElement) {
            $this->_evaluateControlFlowArrayElementRecursive($controlFlowElement);
        }
    }

    /**
     * @param array $controlFlowElement
     */
    protected function _evaluateControlFlowArrayElementRecursive(array &$controlFlowElement) {
        $count = count($controlFlowElement);
        if(!is_array($controlFlowElement) || !($count > 0 && $count < 4)) {
            throw new \InvalidArgumentException('Every entry to the controlFlowArray must be an array with 3 entries: condition, iftrue, iffalse.');
        }
        $condition = $controlFlowElement[0];
        $ifTrueAction = (isset($controlFlowElement[1]) ? $controlFlowElement[1]:function(){});
        $ifFalseAction = (isset($controlFlowElement[2]) ? $controlFlowElement[2]:function(){});
        if(is_callable(($condition))) {$condition = $condition();}
        if((bool)$condition === true) {
            if(is_callable($ifTrueAction)) {
                $ifTrueAction();
            } elseif(is_array($ifTrueAction)) {
                $this->_evaluateControlFlowArrayElementRecursive($ifTrueAction);
            };
        } elseif((bool)$condition === false) {
            if(is_callable($ifFalseAction)) {
                $ifFalseAction();
            } elseif(is_array($ifFalseAction)) {
                $this->_evaluateControlFlowArrayElementRecursive($ifFalseAction);
            }
        }
    }
}