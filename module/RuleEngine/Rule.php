<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 12:22 AM
 */

interface Rule
{

    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function evaluate(RuleContext $context);

    public function getScope();

    public function getName();

    public function getContextDependencies();

    public function getConditionHashList();

    public function getConditionNodeData();

    public function getType();

    public function getID();

    public function getConditionSet();

}