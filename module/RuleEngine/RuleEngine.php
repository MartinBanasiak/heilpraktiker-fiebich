<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 12:18 AM
 */

interface RuleEngine
{

    /**
     * @param RuleList $rules
     * @param RuleContext $context
     * @return mixed
     */
    public function evaluateAllRules(RuleList $rules, RuleContext $context);

    /**
     * @param RuleList $rules
     * @param RuleContext $context
     * @param $scope
     * @return mixed
     */
    public function evaluateRulesByScope(RuleList $rules, RuleContext $context, $scope);
    
}