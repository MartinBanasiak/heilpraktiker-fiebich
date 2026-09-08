<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 3:31 AM
 */

interface RuleList
{

    /**
     * @param Rule $rule
     * @return mixed
     */
    public function add(Rule $rule);

    /**
     * @param $scope
     * @return mixed
     */
    public function getByScope($scope);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetExists($index);

    /**
     * @param $index
     * @param Rule $newRule
     * @return mixed
     */
    public function offsetSet($index, Rule $newRule);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetGet($index);

    /**
     * @param $index
     * @return mixed
     */
    public function offsetUnset($index);

    /**
     * @param Rule $rule
     * @return mixed
     */
    public function push(Rule $rule);

    public function bottom();

    public function count();

    public function current();

    public function getIteratorMode();

    public function isEmpty();

    public function key();

    public function next();

    public function prev();

    public function rewind();

    public function pop();

    /**
     * @param $mode
     * @return mixed
     */
    public function setIteratorMode($mode);

    public function shift();

    /**
     * @param Rule $value
     * @return mixed
     */
    public function unshift(Rule $value);

    public function top();

    public function valid();

}