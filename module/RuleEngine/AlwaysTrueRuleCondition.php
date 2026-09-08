<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 30.10.2015
 * Time: 13:04
 */
class AlwaysTrueRuleCondition implements RuleCondition
{
    /**
     * @param RuleContext $context
     * @return bool
     */
    public function evaluatesTrue(RuleContext $context)
    {
        return true;
    }

    /**
     * @param RuleContext $context
     */
    public function exportExtractedValuesToRuleContext(RuleContext $context)
    {
        //Do Nothing
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