<?php
namespace DynCom\dc\RuleEngine;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 9/28/2015
 * Time: 4:03 AM
 */

interface RuleContextVariable
{

    /**
     * RuleContextVariable constructor.
     * @param $name
     * @param $variable
     */
    public function __construct($name, $variable);

    public function getName();

    /**
     * @param null $valName
     * @param array $valParams
     * @return mixed
     */
    public function getValue($valName = null, $valParams = []);

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments = []);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value);

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value);

}