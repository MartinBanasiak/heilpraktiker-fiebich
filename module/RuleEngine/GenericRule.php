<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 5:24 AM
 */

class GenericRule implements Rule
{

    const RULE_TYPE_CAMPAIGN = 'CAMPAIGN';

    private static $allowedRuleTypes = [
      self::RULE_TYPE_CAMPAIGN
    ];

    private $id;
    private $type = self::RULE_TYPE_CAMPAIGN;

    private $scope;
    private $name;
    private $conditionSet;
    private $action;

    private $conditionPattern;
    private $actionHash;

    /**
     * GenericRule constructor.
     * @param $scope
     * @param $name
     * @param RuleConditionList $conditionSet
     * @param RuleAction $executeAction
     */
    public function __construct($scope, $name, RuleConditionList $conditionSet, RuleAction $executeAction)
    {
        if(!(strlen($scope) > 0)) {
            throw new \InvalidArgumentException('Parameter \'scope\' must be a string of non-zero length.');
        }
        if(!(strlen($name) > 0)) {
            throw new \InvalidArgumentException('Parameter \'name\' must be a string of non-zero length.');
        }
        $this->scope = $scope;
        $this->name = $name;
        $this->conditionSet = $conditionSet;
        $this->action = $executeAction;
    }

    /**
     * @param RuleContext $context
     */
    public function evaluate(RuleContext $context)
    {
        if($this->conditionSet->allEvaluateTrue($context)) {
            if($context->hasVariable('MatchedRules')) {
                $matchedRules = $context->getVariable('MatchedRules');
                $matchedRulesArr = (array)$matchedRules->getValue();
                $matchedRulesArr[] = $this->getName();
            } else {
                $matchedRules = new GenericRuleContextVariable('MatchedRules',[$this->getName()]);
                $matchedRulesArr = [$this->getName()];
            }
            $matchedRules->setValue($matchedRulesArr);
            $context->setVariable($matchedRules);
            $this->action->execute($context);
        }
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getContextDependencies()
    {

    }

    public function getConditionHashList() {
        return $this->conditionSet->getHashList();
    }

    public function getConditionNodeData() {
        return $this->conditionSet->getNodeData();
    }

    /**
     * @param $type
     * @return bool
     */
    private function isAllowedRuleType($type)
    {
        return in_array($type,self::$allowedRuleTypes,true);
    }

    /**
     * @param $type
     */
    public function setType($type) {
        if(!$this->isAllowedRuleType($type)) {
            $msg = "type '$type' is not allowed. Allowed types are: ";
            foreach(self::$allowedRuleTypes as $allowedType) {
                $msg .= "
                $allowedType
                ";
            }
            throw new \InvalidArgumentException($msg);
        }
        $this->type = $type;
    }

    /**
     * @param $id
     */
    public function setID($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    public function getID() {
        return $this->id;
    }

    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function checkIsApplied(RuleContext $context) {
        return $this->check->execute($context);
    }

    /**
     * @param RuleContext $context
     */
    public function reverseApplication(RuleContext $context) {
        $this->reverse->execute($context);
    }

    /**
     * @return RuleConditionList
     */
    public function getConditionSet()
    {
        return $this->conditionSet;
    }

}