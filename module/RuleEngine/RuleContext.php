<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:40 AM
 */

interface RuleContext
{
    /**
     * @param RuleContextVariable $variable
     * @return mixed
     */
    public function addVariable(RuleContextVariable $variable);

    /**
     * @param RuleContextVariable $variable
     * @return mixed
     */
    public function setVariable(RuleContextVariable $variable);

    /**
     * @param $name
     * @return RuleContextVariable
     * @throws \InvalidArgumentException
     */
    public function getVariable($name);

    /**
     * @param $fullyQualifiedName
     * @return mixed
     */
    public function getValue($fullyQualifiedName);

    /**
     * @param $name
     * @return mixed
     */
    public function hasVariable($name);

}