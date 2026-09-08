<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 27.10.2015
 * Time: 23:58
 */
trait closureRuleConditionTrait
{
    use ruleConditionTrait;

    private $callable;

    /**
     * @param RuleContext $context
     * @return bool
     */
    public function evaluatesTrue(RuleContext $context)
    {
        $varFunc = $this->callable;
        $isTrue = (bool)$varFunc($context,$this->extractedVariableArray);
        return $isTrue;
    }
}