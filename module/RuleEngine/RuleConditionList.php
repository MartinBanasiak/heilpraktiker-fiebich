<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:41 AM
 */

interface RuleConditionList
{

    /**
     * @param $index
     * @param RuleCondition $ruleCondition
     * @return mixed
     */
    public function addRuleCondition($index, RuleCondition $ruleCondition);

    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function allEvaluateTrue(RuleContext $context);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetExists($index);

    /**
     * @param $index
     * @param RuleCondition $newRuleCondition
     * @return mixed
     */
    public function offsetSet($index, RuleCondition $newRuleCondition);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetGet($index);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetUnset($index);

    /**
     * @param RuleCondition $condition
     * @return mixed
     */
    public function pushRuleCondition(RuleCondition $condition);

    public function getHashList();

    public function getNodeData();

}