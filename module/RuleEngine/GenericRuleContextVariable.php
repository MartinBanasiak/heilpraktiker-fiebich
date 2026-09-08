<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 6:31 AM
 */

class GenericRuleContextVariable implements RuleContextVariable
{
    private $name;
    private $variable;

    /**
     * GenericRuleContextVariable constructor.
     * @param $name
     * @param $variable
     */
    public function __construct($name, $variable)
    {
        if(!(strlen($name) > 0)) {
            throw new \InvalidArgumentException('Parameter \'name\' must be a string of non-zero length.');
        }
        $this->name = $name;
        $this->variable = $variable;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     * @param array $paramArr
     * @return mixed
     */
    public function getValue($name = null, $paramArr = [])
    {
        if($name === null) {
            return $this->variable;
        } elseif (is_string($this->variable) && (is_int($name) || ctype_digit($name))) {
            return $this->variable[(int)$name];
        } elseif(is_array($this->variable)) {
            return $this->variable[$name];
        } elseif(is_object($this->variable) && property_exists($this->variable,$name)) {
            $reflProp = new \ReflectionProperty($this->variable,$name);
            $reflProp->setAccessible(true);
            return $reflProp->getValue($this->variable);
        } elseif(is_object($this->variable) && method_exists($this->variable,$name)) {
            $reflMethod = new \ReflectionMethod($this->variable,$name);
            $reflMethod->setAccessible(true);
            return $reflMethod->invokeArgs($this->variable, $paramArr);
        }
        throw new \DomainException('Cannot resolve valName.');
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        if(is_object($this->variable) && method_exists($this->variable,$name)) {
            $reflMethod = new \ReflectionMethod($this->variable,$name);
            if ($reflMethod->returnsReference()) {
                throw new \DomainException('Cannot call method which returns a reference.');
            }
            $reflMethod->setAccessible(true);
            return $reflMethod->invokeArgs($this->variable, $arguments);
        } elseif(is_callable($this->variable)) {
            return call_user_func_array($this->variable,$arguments);
        }
        throw new \DomainException('Cannot resolve valName.');

    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if(is_array($this->variable) && array_key_exists($name,$this->variable)) {
            return $this->variable[$name];
        } elseif(is_object($this->variable) && property_exists($this->variable,$name)) {
            $reflProp = new \ReflectionProperty($this->variable,$name);
            $reflProp->setAccessible(true);
            return $reflProp->getValue($this->variable);
        }
        throw new \InvalidArgumentException('\'' . $name . '\' is not a registered property or array-entry');
    }

    /**
     * @param $value
     */
    public function setValue($value) {
        $this->variable = $value;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(is_array($this->variable)) {
            $this->variable[$name] = $value;
        } elseif(is_object($this->variable) && property_exists($this->variable,$name)) {
            $reflProp = new \ReflectionProperty($this->variable,$name);
            $reflProp->setAccessible(true);
            $reflProp->setValue($this->variable,$value);
        }
        throw new \InvalidArgumentException('\'' . $name . '\' is not a registered property or array-entry');
    }


}