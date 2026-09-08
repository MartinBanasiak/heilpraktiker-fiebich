<?php
namespace DynCom\dc\dcShop\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 07.07.2015
 * Time: 20:01
 */
trait genericDecoratorTrait {


    protected $decoratedEntity;
    protected $reflectionClass;
    private $decoratorFields = [];
    private $decoratorMethods = [];

    private $serializedFieldCallables = [];
    private $serializedMethodCallables = [];

    abstract protected function getDecoratedEntityClassName();

    /**
     * @param $modelEntity
     * @return bool
     */
    protected function validateModelClass($modelEntity) {
        $name = $this->getDecoratedEntityClassName();
        return ($modelEntity instanceof $name);
    }

    /**
     * genericDecoratorTrait constructor.
     * @param $entityToDecorate
     */
    public function __construct($entityToDecorate) {
        if(!$this->validateModelClass($entityToDecorate)) {
            throw new \InvalidArgumentException(
                "Argument must be instance of class " . $this->getDecoratedEntityClassName() . ' - ' . get_class($entityToDecorate) . ' given.'
            );
        }
        $this->decoratedEntity = $entityToDecorate;
        $this->reflectionClass = new \ReflectionClass($this->decoratedEntity);
    }

    /**
     * @param $name
     * @param $args
     * @return mixed|null
     */
    public function __call($name, $args) {
        if(array_key_exists($name,$this->decoratorMethods)) {
            return call_user_func_array($this->decoratorMethods[$name],$args);
        } elseif($this->reflectionClass->hasMethod($name)) {
            $method = $this->reflectionClass->getMethod($name);
            if($method->isPublic()) {
                return $method->invokeArgs($this->decoratedEntity,$args);
            }
            return null;
        } else {
            return call_user_func_array([$this->decoratedEntity,$name],$args);
        }        
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name) {
        $val = null;
        if(array_key_exists($name,$this->decoratorFields)) {
            $val = $this->decoratorFields[$name];
        } elseif($this->reflectionClass->hasProperty($name)) {
            $prop = $this->reflectionClass->getProperty($name);
            $prop->setAccessible(true);
            $val = $prop->getValue($this->decoratedEntity);
        } elseif ($this->reflectionClass->hasProperty('decoratedEntity')) {
            try {
                $val = $this->decoratedEntity->$name;
            } catch (\Exception $e) {
                //Do nothing
            }
        }
        return $val;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     */
    public function decorateWithProperty($fieldName, $fieldValue) {
        $this->decoratorFields[$fieldName] = $fieldValue;
    }

    /**
     * @param $methodName
     * @param callable $methodCallable
     */
    public function decorateWithMethod($methodName, callable $methodCallable) {
        $this->decoratorMethods[$methodName] = $methodCallable;
    }

}
