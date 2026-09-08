<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 6:37 AM
 */

class GenericRuleEngine implements RuleEngine
{
    /**
     * @param RuleList $rules
     * @param RuleContext $context
     */
    public function evaluateAllRules(RuleList $rules, RuleContext $context)
    {
        for($rules->rewind();$rules->valid();$rules->next()) {
            /**
             * @var $currRule Rule
             */
            $currRule = $rules->current();
            if($currRule instanceof Rule) {
                $currRule->evaluate($context);
            }
        }
    }

    /**
     * @param RuleList $rules
     * @param RuleContext $context
     * @param $scope
     */
    public function evaluateRulesByScope(RuleList $rules, RuleContext $context, $scope)
    {

        for($rules->rewind();$rules->valid();$rules->next()) {
            $currRule = $rules->current();
            if(($currRule instanceof Rule) && ($currRule->getScope() === $scope)) {
                $currRule->evaluate($context);
            }
        }
    }

}