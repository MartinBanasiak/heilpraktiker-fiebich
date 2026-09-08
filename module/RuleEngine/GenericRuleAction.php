<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 5:28 AM
 */

class GenericRuleAction implements RuleAction
{
    private $callable;

    /**
     * GenericRuleAction constructor.
     * @param callable $function
     */
    public function __construct(callable $function)
    {
        $reflFunc = new \ReflectionFunction($function);
        $params = $reflFunc->getParameters();
        if(
                (count($params) !== 1)
            ||  (!($params[0]->getClass()->getName() === 'RuleContext'))
        ) {
            throw new \InvalidArgumentException('Callable must have exactly one parameter of type \'RuleContext\'.');
        }
        $this->callable = $function;
    }

    /**
     * @param RuleContext $context
     */
    public function execute(RuleContext $context)
    {
        $varFunc = $this->callable;
        $varFunc($context);

    }

}