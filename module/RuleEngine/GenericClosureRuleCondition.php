<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 5:37 AM
 */

class GenericClosureRuleCondition implements ClosureRuleCondition
{

    use ruleConditionTrait;

    /**
     * GenericClosureRuleCondition constructor.
     * @param  $booleanCallable
     */
    public function __construct(\Closure $booleanCallable)
    {
        $reflFunc = new \ReflectionFunction($booleanCallable);
        $params = $reflFunc->getParameters();
        if (
            (count($params) !== 1)
            || (!($params[0]->getClass()->getName() === 'RuleContext'))
        ) {
            throw new \InvalidArgumentException('Callable must have exactly one parameter of type \'RuleContext\'.');
        }
        $this->callable = $booleanCallable;
    }
}