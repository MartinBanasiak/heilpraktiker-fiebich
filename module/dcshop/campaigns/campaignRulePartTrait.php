<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\RuleEngine\RuleContext;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 29.10.2015
 * Time: 10:13
 */
trait campaignRulePartTrait
{
    /**
     * @param RuleContext $context
     * @param $variableName
     * @return mixed
     */
    protected $ruleKey;

    protected function getContextVariable(RuleContext $context,$variableName)
    {
        $contextVar = null;
        try {
            $contextVar = $context->getVariable($variableName)->getValue();
        } catch(\Exception $e) {

        }
        return $contextVar;
    }

    protected function getThresholdMetByFactorForItemKey(RuleContext $context, $key) {
        $conditionMatchingData = $this->getContextVariable($context,$this->ruleKey . '.ConditionItemMatchingData');
        $factor = 1;
        if (null !== $conditionMatchingData && is_array($conditionMatchingData)) {
            if(
                array_key_exists($key,$conditionMatchingData)
                &&  is_array($conditionMatchingData[$key])
                &&  array_key_exists('threshold_met_by_factor',$conditionMatchingData[$key])
            ) {
                $factor = (int)$conditionMatchingData[$key]['threshold_met_by_factor'];
            } elseif (array_key_exists('all',$conditionMatchingData) && array_key_exists('threshold_met_by_factor',$conditionMatchingData['all'])) {
                $factor = (int)$conditionMatchingData['all']['threshold_met_by_factor'];
            }
        }
        return $factor;
    }

    protected function getThresholdRemainderForItemKey(RuleContext $context, $key) {
        $conditionMatchingData = $this->getContextVariable($context,$this->ruleKey . '.ConditionItemMatchingData');
        $remainder = 1;
        if (null !== $conditionMatchingData && is_array($conditionMatchingData)) {
            if(
                    array_key_exists($key,$conditionMatchingData)
                &&  is_array($conditionMatchingData[$key])
                &&  array_key_exists('threshold_remainder',$conditionMatchingData[$key])
            ) {
                $remainder = (int)$conditionMatchingData[$key]['threshold_remainder'];
            }
        }
        return $remainder;
    }

}