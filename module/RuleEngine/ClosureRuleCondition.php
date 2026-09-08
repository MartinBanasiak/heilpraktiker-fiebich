<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:41 AM
 */

interface ClosureRuleCondition extends RuleCondition
{
    /**
     * ClosureRuleCondition constructor.
     * @param  $booleanCallable
     */
    public function __construct(\Closure $booleanCallable);
}