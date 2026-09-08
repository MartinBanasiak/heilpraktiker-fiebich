<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 22.10.2015
 * Time: 10:31
 */
class ReturnsValueRuleAction implements RuleAction
{
    private $callable;

    /**
     * ReturnsValueRuleAction constructor.
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
     * @return mixed
     */
    public function execute(RuleContext $context)
    {
        $varFunc = $this->callable;
        return $varFunc($context);
    }
}