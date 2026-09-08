<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 11:11 PM
 */

class GenericRuleList implements RuleList
{


    private $ruleList = [];


    /**
     * @param Rule $rule
     */
    public function add(Rule $rule)
    {
        $ruleIndex = $rule->getScope() . '.' . $rule->getName();
        if(!array_key_exists($ruleIndex,$this->ruleList)) {
            $this->ruleList[$ruleIndex] = $rule;
        }
    }

    /**
     * @param $scope
     * @return GenericRuleList
     */
    public function getByScope($scope)
    {
        $newRuleList = new GenericRuleList();
        for($this->rewind();$this->valid();$this->next()) {
            $currRule = $this->current();
            if($currRule->getScope() === $scope) {
                $newRuleList->add($currRule);
            }
        }
        return $newRuleList;
    }

    /**
     * @param $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return array_key_exists($index,$this->ruleList);
    }

    /**
     * @param $index
     * @param Rule $newRule
     */
    public function offsetSet($index, Rule $newRule)
    {
        $this->ruleList[$index] = $newRule;
    }

    /**
     * @param $index
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->ruleList[$index];
    }

    /**
     * @param $index
     */
    public function offsetUnset($index)
    {
        if(array_key_exists($index,$this->ruleList)) {
            unset($this->ruleList[$index]);
        }
    }

    /**
     * @param Rule $rule
     */
    public function push(Rule $rule)
    {
        $this->add($rule);
    }

    /**
     * @return mixed
     */
    public function bottom()
    {
        return end($this->ruleList);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->ruleList);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->ruleList);
    }

    /**
     * @return string
     */
    public function getIteratorMode()
    {
        return 'FIFO';
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->ruleList);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->ruleList);
    }

    public function next()
    {
        next($this->ruleList);
    }

    public function prev()
    {
        prev($this->ruleList);
    }

    public function rewind()
    {
        reset($this->ruleList);
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->ruleList);
    }

    /**
     * @param $mode
     */
    public function setIteratorMode($mode)
    {
        //
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->ruleList);
    }

    /**
     * @param Rule $rule
     */
    public function unshift(Rule $rule)
    {
        array_unshift($this->ruleList,$rule);
    }

    /**
     * @return array
     */
    public function top()
    {
        return array_values(array_keys($this->ruleList)[0]);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->key(),$this->ruleList);
    }

}