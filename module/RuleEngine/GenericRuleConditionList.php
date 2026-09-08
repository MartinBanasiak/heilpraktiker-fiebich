<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 5:42 AM
 */

class GenericRuleConditionList implements RuleConditionList
{

    private $conditionList;

    private $conditionHashList;
    private $conditionNodeData;

    /**
     * GenericRuleConditionList constructor.
     */
    public function __construct() {
        $this->conditionList = new \SplDoublyLinkedList();
    }

    /**
     * @param $index
     * @param RuleCondition $ruleCondition
     */
    public function addRuleCondition($index, RuleCondition $ruleCondition)
    {
        $this->conditionList->add($index,$ruleCondition);
        $hash = $ruleCondition->getHash();
        if(!in_array($hash,$this->conditionHashList,true)) {
            $this->conditionHashList[] = $hash;
            $this->conditionNodeData = $ruleCondition->getNodeData();
        }
    }

    /**
     * @param RuleContext $context
     * @return bool
     */
    public function allEvaluateTrue(RuleContext $context)
    {
        for($this->conditionList->rewind();$this->conditionList->valid();$this->conditionList->next()) {
            $ruleCondition = $this->conditionList->current();
            if(!$ruleCondition->evaluatesTrue($context)) {
                return false;
            }
            if($ruleCondition instanceof RuleCondition) {
                $ruleCondition->exportExtractedValuesToRuleContext($context);
            }
        }
        return true;
    }

    /**
     * @param $index
     * @return bool
     */
    public function offsetExists($index) {
        return $this->conditionList->offsetExists($index);
    }

    /**
     * @param $index
     * @param RuleCondition $newRuleCondition
     */
    public function offsetSet($index, RuleCondition $newRuleCondition) {
        $this->conditionList->offsetSet($index,$newRuleCondition);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function offsetGet($index) {
        return $this->conditionList->offsetGet($index);
    }

    /**
     * @param $index
     */
    public function offsetUnset($index) {
        $this->conditionList->offsetUnset($index);
    }

    /**
     * @param RuleCondition $condition
     */
    public function pushRuleCondition(RuleCondition $condition) {
        $this->conditionList->push($condition);
        $hash = $condition->getHash();
        if(!is_array($this->conditionHashList) || !in_array($hash,$this->conditionHashList,true)) {
            $this->conditionHashList[] = $hash;
            $this->conditionNodeData = $condition->getNodeData();
        }
    }

    public function rewind()
    {
        $this->conditionList->rewind();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->conditionList->valid();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->conditionList->current();
    }

    public function next()
    {
        $this->conditionList->next();
    }

    public function getHashList() {
        return $this->conditionHashList;
    }

    public function getNodeData() {
        return $this->conditionNodeData;
    }
}