<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 29.09.2015
 * Time: 10:30
 */

class GenericBinaryAndRuleCondition implements RuleCondition
{

    use ruleConditionTrait;

    /**
     * GenericBinaryAndRuleCondition constructor.
     * @param $leftHandContextBinding
     * @param $rightHandContextBinding
     */
    public function __construct($leftHandContextBinding, $rightHandContextBinding)
    {
        $callable = function(RuleContext $context) use($leftHandContextBinding,$rightHandContextBinding) {
            return ((bool)$context->getValue($leftHandContextBinding) && (bool)$context->getValue($rightHandContextBinding));
        };
        $this->callable = $callable;
    }

    /**
     * @param RuleContext $context
     */
    public function evaluatesTrue(RuleContext $context)
    {
        // TODO: Implement evaluatesTrue() method.
    }

    public function getHash()
    {
        // TODO: Implement getHash() method.
    }

    public function getNodeData()
    {
        // TODO: Implement getNodeData() method.
    }


}