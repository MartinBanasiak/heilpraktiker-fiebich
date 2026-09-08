<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 29.09.2015
 * Time: 13:33
 */
/*
$testAutoloader = function($className) {
    $path = '../';
    $fullPath = $path . $className . '.php';
    if(file_exists($fullPath)) {
        include($fullPath);
    } else {
        echo "No File at $fullPath";
    }
};

spl_autoload_register($testAutoloader);

$stdClass1 = (object)(['prop1' => 'propVal1','prop2' => 0, 'prop3' => 1, 'prop4' => 2.14]);
$arr1 = ['arr1' => 'arrVal1','arr2' => 'arrVal2', 'arr3' => 0, 'arr4' => 11, 'arr5' => 2.14];
$stdClass1->prop5 = $arr1;
$var1 = 3.1415;

$rcv1 = new GenericRuleContextVariable('stdClass1',$stdClass1);
$rcv2 = new GenericRuleContextVariable('arr1',$arr1);
$rcv3 = new GenericRuleContextVariable('var1',$var1);

$rc = new GenericRuleContext();
$rc->addVariable($rcv1);
$rc->addVariable($rcv2);
$rc->addVariable($rcv3);

$stdClassProp1IsPropVal1Function = function(RuleContext $context) {
    $val = $context->getValue('stdClass1.prop1');
    return ($val === 'propVal1');
};

$closureConditionStdClassProp1IsPropVal1 = new GenericClosureRuleCondition($stdClassProp1IsPropVal1Function);

$stdClassProp1IsNotPropVal1Function = function(RuleContext $context) {
    $val = $context->getValue('stdClass1.prop1');
    return ($val !== 'propVal1');
};

$closureConditionStdClassProp1IsNotPropVal1 = new GenericClosureRuleCondition($stdClassProp1IsNotPropVal1Function);

$var1gtearr5condition = new GenericBinaryGTERuleCondition('var1','arr1.arr5');


$conditionList = new GenericRuleConditionList();

$conditionList->pushRuleCondition($closureConditionStdClassProp1IsPropVal1);
$conditionList->pushRuleCondition($var1gtearr5condition);


$ruleActionFunction = function(RuleContext $context) {
    $var1 = $context->getVariable('var1');
    $var1->setValue(5);
    echo "Rule function executed.";
};

$ruleAction = new GenericRuleAction($ruleActionFunction);



$rule = new GenericRule('global','testRule',$conditionList,$ruleAction);

$rules = new GenericRuleList();
$rules->push($rule);


$ruleManager = new GenericRuleEngine();


$ruleManager->evaluateAllRules($rules,$rc);

echo "VAR1 new value: " . $rc->getValue('var1');

*/
