<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:41 AM
 */

interface RuleAction
{

    /**
     * @param RuleContext $context
     * @return mixed
     */
    public function execute(RuleContext $context);

}