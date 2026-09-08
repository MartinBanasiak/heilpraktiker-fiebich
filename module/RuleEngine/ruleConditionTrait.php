<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 05.10.2015
 * Time: 11:41
 */
trait ruleConditionTrait
{

    protected $extractedVariableArray = [];

    /**
     * @param RuleContext $context
     */
    public function exportExtractedValuesToRuleContext(RuleContext $context)
    {
        foreach ($this->extractedVariableArray as $variable) {
            if($variable instanceof RuleContextVariable) {
                $context->setVariable($variable);
            }
        }
    }



}