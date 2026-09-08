<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/13/2015
 * Time: 12:52 AM
 */

class FormElement {

    /**
     * @var \DOMElement
     */
    protected $el;
    protected $validationRuleNames;
    protected $label;
    /**
     * @param \DOMElement $el
     * @param null $label
     */
    public function __construct( \DOMElement $el, $label = null ) {
        $this->el = $el;
        if(null !== $label) {
            $this->label = strip_tags($label);
        }
    }

    /**
     * @param $ruleName
     */
    public function addValidationRuleByName($ruleName ) {
        if(!Validator::isValidDefaultInlineRuleName($ruleName)) {
            throw new \InvalidArgumentException("'$ruleName' is not valid name of a validation-rule");
        }
        $this->validationRuleNames[$ruleName] = $ruleName;
    }

    /**
     * @return \DOMElement
     */
    public function getEl() {
        return $this->el;
    }

    /**
     * @param $val
     */
    public function setValue($val ) {
        $this->el->setAttribute('value',strip_tags($val));
    }

}