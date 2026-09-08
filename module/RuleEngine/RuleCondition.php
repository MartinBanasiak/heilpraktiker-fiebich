<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:41 AM
 */

interface RuleCondition
{
    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function evaluatesTrue(RuleContext $context);

    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function exportExtractedValuesToRuleContext(RuleContext $context);

    public function getHash();

    public function getNodeData();

}