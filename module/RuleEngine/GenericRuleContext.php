<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 5:57 AM
 */

class GenericRuleContext implements RuleContext
{

    private $variables = [];

    /**
     * @param RuleContextVariable $variable
     */
    public function addVariable(RuleContextVariable $variable)
    {
        $name = $variable->getName();
        if(array_key_exists($variable->getName(),$this->variables)) {
            throw new \InvalidArgumentException('There is already a RuleContextVariable with the name \'' . $variable->getName() . '\' set for this RuleContext.');
        }
        $this->variables[$name]  = $variable;
    }

    /**
     * @param RuleContextVariable $variable
     */
    public function setVariable(RuleContextVariable $variable)
    {
        $name = $variable->getName();
        $this->variables[$name]  = $variable;
    }

    /**
     * @param $name
     * @return RuleContextVariable
     * @throws \InvalidArgumentException
     */
    public function getVariable($name)
    {
        if(!array_key_exists($name,$this->variables)) {
            throw new \InvalidArgumentException('No variable with name \'' . htmlspecialchars($name) . '\' exists in this RuleContext.');
        }
        return $this->variables[$name];
    }

    /**
     * @param $fullyQualifiedName
     * @return RuleContextVariable|mixed
     */
    public function getValue($fullyQualifiedName)
    {
        //Try FQN
        if(array_key_exists($fullyQualifiedName,$this->variables)) {
            return $this->variables[$fullyQualifiedName]->getValue();
        }
        //Explode FQN into parts
        $parts = explode('.',$fullyQualifiedName);
        $partCount = count($parts);
        //Init lvl1
        $currLvl = 1;
        $currLvlName = $parts[($currLvl - 1)];
        $currLvlVal = $this->getVariable($currLvlName);
        if($currLvlVal instanceof RuleContextVariable) {
            $currLvlVal = $currLvlVal->getValue();
        }
        $maxLevelReached = $currLvl === $partCount;
        //Iteratively get value for given name-part until
        //last level is reached or exception is thrown
        while(!$maxLevelReached) {
            $currLvl++;
            $lastLvlName = $currLvlName;
            $currLvlName = $parts[($currLvl - 1)];
            $lastLvlVal = $currLvlVal;
            if(is_string($lastLvlVal) && ctype_digit($currLvlName)) {
                $currLvlVal = $lastLvlVal[(int)$currLvlName];
            } elseif(is_array($lastLvlVal)) {
                $currLvlVal = $lastLvlVal[$currLvlName];
            } elseif(is_object($lastLvlVal)) {
                if(property_exists($lastLvlVal,$currLvlName)) {
                    $reflProp = new \ReflectionProperty($lastLvlVal,$currLvlName);
                    $reflProp->setAccessible(true);
                    $currLvlVal = $reflProp->getValue($lastLvlVal);
                } elseif(method_exists($lastLvlVal,$currLvlName)) {
                    $reflMethod = new \ReflectionMethod($lastLvlVal,$currLvlName);
                    if($reflMethod->getNumberOfParameters() === 0) {
                        $currLvlVal = $lastLvlVal->$currLvlName();
                    } else {
                        throw new \DomainException('Method \'' . $currLvlName . '\' expects parameters and cannot be handled.');
                    }

                } elseif(method_exists($lastLvlVal,'get' . ucfirst($currLvlName))) {
                    $getMethodName = 'get' . ucfirst($currLvlName);
                    $currLvlVal = $lastLvlVal->$getMethodName();
                } else {
                    throw new \DomainException('Cannot get data with name \'' . htmlspecialchars($currLvlName) , '\' from object with name \'' . $lastLvlName . '\'');
                }
            } else {
                throw new \DomainException('Cannot get data with name \'' . htmlspecialchars($currLvlName) . '\' from object with name \'' . $lastLvlName . '\'');
            }
            //Resolve value if RuleContextVariable
            if($currLvlVal instanceof RuleContextVariable) {
                $currLvlVal = $currLvlVal->getValue();
            }
            $maxLevelReached = ($currLvl === $partCount);
        }
        //Return value of the final level
        return $currLvlVal;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasVariable($name) {
        if(array_key_exists($name,$this->variables)) {
            return true;
        }
        $val = null;
        try{
            $val = $this->getValue($name);
        } catch(\Exception $e) {
            return false;
        }
        return (null !== $val);
    }

}