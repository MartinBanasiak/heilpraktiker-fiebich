<?php
/* @description     Dice - A minimal Dependency Injection Container for PHP         *
 * @author          Tom Butler tom@r.je                                             *
 * @copyright       2012-2015 Tom Butler <tom@r.je> | http://r.je/dice.html         *
 * @license         http://www.opensource.org/licenses/bsd-license.php  BSD License *
 * @version         1.4                                                           */
/**
 * Modified by Michael Bauer, dc-solution.de, 06/2015
 */
namespace Dice;
use DynCom\dc\common\interfaces\IOCInterface;
use Psr\Container\ContainerInterface;

class Dice implements IOCInterface,ContainerInterface {
    private $rules = [];
    private $cache = [];
    private $instances = [];
    private $cachedParams = [];
    private static $currInstance;

    public function get($name) {
        return $this->resolve($name);
    }

    public function has($name) {
        $hasRule = array_key_exists(strtolower(ltrim($name, '\\')),$this->rules);
        if ($hasRule) {
            return true;
        }
        try {
            $obj = $this->get($name);
        } catch (\Throwable $t) {
            return false;
        }
        return (\is_object($obj));
    }

    public function register($name, $rule) {
        $this->addRule($name,$rule);
        self::$currInstance = $this;
    }

    public function resolve($name) {
        return $this->create($name);
    }

    public function addRule($name, Rule $rule) {
        $rule->objName = $name;
        $this->rules[ltrim(strtolower($name), '\\')] = $rule;
        self::$currInstance = $this;
    }

    public function getRule($name) {
        if (isset($this->rules[strtolower(ltrim($name, '\\'))])) return $this->rules[strtolower(ltrim($name, '\\'))];
        foreach ($this->rules as $key => $rule) {
            if ($rule->instanceOf === null && $key !== '*' && is_subclass_of($name, $key) && $rule->inherit === true) {
                return $rule;
            }
        }
        return isset($this->rules['*']) ? $this->rules['*'] : new Rule;
    }

    public function create($component, array $args = [], $forceNewInstance = false, $share = []) {

        if (!$forceNewInstance && isset($this->instances[$component])) return $this->instances[$component];
        if (empty($this->cache[$component])) {
            $rule = $this->getRule($component);

            $class = new \ReflectionClass($rule->instanceOf ?: $component);
            $constructor = $class->getConstructor();
            if(!array_key_exists($component,$this->cachedParams)) {
                $params = $constructor ? $this->getParams($constructor, $rule) : null;
                $this->cachedParams[$component] = $params;
            } else {
                $params = $this->cachedParams[$component];
            }

            $this->cache[$component] = function($args, $share) use ($component, $rule, $class, $constructor, $params) {
                if(isset($this->instances[$component])) {return $this->instances[$component];}
                if(isset($rule->factoryInstance) && $rule->factoryInstance instanceof Instance) {
                    $factoryObjDInstance = $rule->factoryInstance;
                    $factoryObj = is_callable($factoryObjDInstance->name) ? call_user_func($factoryObjDInstance->name, $this, $share) : $this->create($factoryObjDInstance->name, [], false, $share);
                    $methodName = $factoryObjDInstance->callMethodName;
                    $factoryParams = $factoryObjDInstance->callMethodParams;
                    $factoryReflectionMethod = new \ReflectionMethod($factoryObj,$methodName);
                    //echo "<br>Creating Instance from Factory Method $component<br>";
                    if($rule->shared) $this->instances[$component] = $object = $factoryReflectionMethod->invokeArgs($factoryObj,(is_array($factoryParams) ? $this->expand($factoryParams):array()));
                    else { $object = $factoryReflectionMethod->invokeArgs($factoryObj,(is_array($factoryParams) ? $this->expand($factoryParams):null)); $this->instances[$component] = $object;}
                } else {
                    if ($rule->shared) {
                        try {
                            $this->instances[$component] = $object = $class->newInstanceWithoutConstructor();
                        } catch(\Exception $e) {
                           // echo "<br>Tried Creating Instance WO Constructor $component<br>Debug Backtrace: ";
                            //debug_print_backtrace(0,2);
                            //echo "<br><br>";
                        }

                        $finalArgs = ($constructor) ? $params($args, $share) : null;
                        $args = (array)$args;

                        if ($constructor) {$constructor->invokeArgs($object, $finalArgs);}
                    } else {
                        $object = $params ? $class->newInstanceArgs((array)$params($args, $share)) : new $class->name;
                    }
                }
                if ($rule->call) foreach ($rule->call as $call) {$class->getMethod($call[0])->invokeArgs($object, call_user_func($this->getParams($class->getMethod($call[0]), $rule), $this->expand($call[1])));}
                return $object;
            };
        }
        return $this->cache[$component]($args, $share);
    }

    private function expand($param, array $share = []) {
        if (is_array($param)) { foreach ($param as &$key) {$key = $this->expand($key, $share);} unset($key);}
        else if ($param instanceof Instance) return (is_callable($param->name) && empty($param->getFieldValueForName)) ? call_user_func($param->name, $this, $share) : (empty($param->getFieldValueForName) ? $this->create($param->name, [], false, $share) : $this->create($param->name, [], false, $share)->{$param->getFieldValueForName});
        return $param;
    }

    private function getParams(\ReflectionMethod $method, Rule $rule) {
        $paramInfo = [];
        foreach ($method->getParameters() as $param) {
            $class = $param->getClass() ? $param->getClass()->name : null;
            $paramInfo[$param->getName()] = [$class, $param->allowsNull(), array_key_exists($class, $rule->substitutions), in_array($class, $rule->newInstances, true)];
        }

        return function($args, $share = []) use ($paramInfo, $rule,$method) {
            if ($rule->shareInstances) $share = array_merge($share, array_map([$this, 'create'], $rule->shareInstances));
            if ($share || $rule->constructParams) $args = array_merge($args, $this->expand($rule->constructParams, $share), $share);
            /*$parameters = [];*/
            $namedParams = [];
            foreach ($paramInfo as $paramName => $arr) {

                list($class, $allowsNull, $sub, $new) = $arr;
                if ($args && $count = count($args)) for ($i = 0; $i < $count; $i++) {
                    if (($class && array_key_exists($i,$args) && $args[$i] instanceof $class) || (array_key_exists($i,$args) && $args[$i] === null && $allowsNull)) {
                        $val = $args[$paramName];
                        $namedParams[$paramName] = $val;
                        continue 2;
                    }
                }
                if ($class){$val = $sub ? $this->expand($rule->substitutions[$class], $share) : $this->create($class, [], $new, $share);  /*$parameters[] = $val;*/ $namedParams[$paramName] = $val;}
                else if ($args) { $val = $this->expand(array_shift($args)); /*$parameters[] = $val; */$namedParams[$paramName] = $val;};

            }
            foreach($namedParams as $paramName => &$paramValue) {
                if(is_null($paramValue) && isset($args[$paramName])) {
                    $paramValue = $args[$paramName];
                }
            }
            unset($paramValue);
            return $namedParams;
        };

        /*

            return function($args, $share = []) use ($paramInfo, $rule,$method) {

                if ($rule->shareInstances) $share = array_merge($share, array_map([$this, 'create'], $rule->shareInstances));
                if ($share || $rule->constructParams) { $args = array_merge($args, $this->expand($rule->constructParams, $share), $share);}
                $parameters = [];

                foreach ($paramInfo as $arr) {
                    list($class, $allowsNull, $sub, $new) = $arr;
                    if ($args && $count = count($args)) for ($i = 0; $i < $count; $i++) {


                        if ($class && $args[$i] instanceof $class || ($args[$i] === null && $allowsNull)) {
                            $parameters[] = array_splice($args, $i, 1)[0];
                            continue 2;
                        }
                    }
                    if ($class) $parameters[] = $sub ? $this->expand($rule->substitutions[$class], $share) : $this->create($class, [], $new, $share);
                    else if ($args) $parameters[] = $this->expand(array_shift($args));
                }
                return $parameters;

            };*/

    }

    public static function staticResolve($name)
    {
        return self::$currInstance->resolve($name);
    }

    public static function staticCreate($component, array $args = [], $forceNewInstance = false, $share = []) {
        return self::$currInstance->create($component,$args,$forceNewInstance,$share);
    }
}

class Rule {
    public $shared = false;
    public $constructParams = [];
    public $substitutions = [];
    public $newInstances = [];
    public $instanceOf;
    public $call = [];
    public $inherit = true;
    public $shareInstances = [];
    public $factoryInstance;
}

class Instance {
    public $name;
    public $callMethodName;
    public $callMethodParams;
    public $getFieldValueForName;
    public function __construct($instance) {
        $this->name = $instance;
    }
}
