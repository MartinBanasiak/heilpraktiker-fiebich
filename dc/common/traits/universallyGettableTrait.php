<?php
namespace DynCom\dc\common\traits;
/**
 * Class universallyGettableTrait
 * @package DynCom\dc\common\traits
 */
trait universallyGettableTrait {

    static $reflectionProperties = [];

    /**
     * @param $propName
     *
     * @return mixed|null
     */
    public function __get( $propName ) {
        return $this->_getProperty($propName);
    }

    /**
     * @param $propName
     *
     * @return mixed|null
     */
    protected function _getProperty( $propName ) {
        if (property_exists($this, $propName)) {
            $fqn = get_class() . '::' . $propName;
            if (array_key_exists($fqn,static::$reflectionProperties)) {
                $reflectionProp = static::$reflectionProperties[$fqn];
            } else {
                $reflectionProp = new \ReflectionProperty($this,$propName);
                static::$reflectionProperties[$fqn] = $reflectionProp;
            }
            $reflectionProp->setAccessible(TRUE);
            $value = NULL;
            if ($reflectionProp->isStatic()) {
                $val = $reflectionProp->getValue();
                return $reflectionProp;
            } else {
                $val = $reflectionProp->getValue($this);
                return $val;
            }
        } elseif (property_exists($this, '_' . $propName)) {
            $altPropName    = '_' . $propName;
            $fqn = get_class() . '::' . $altPropName;
            if (array_key_exists($fqn,static::$reflectionProperties)) {
                $reflectionProp = static::$reflectionProperties[$fqn];
            } else {
                $reflectionProp = new \ReflectionProperty($this,$propName);
                static::$reflectionProperties[$fqn] = $reflectionProp;
            }
            $reflectionProp->setAccessible(TRUE);
            $value = NULL;
            if ($reflectionProp->isStatic()) {
                return $reflectionProp->getValue();
            } else {
                return $reflectionProp->getValue($this);
            }
        }
        return NULL;
    }

    /**
     * @param $propName
     *
     * @return bool
     */
    public function __isset( $propName ) {
        return $this->_propertyIsset($propName);
    }

    /**
     * @param $propName
     *
     * @return bool
     */
    protected function _propertyIsset( $propName ) {
        if (property_exists($this, $propName)) {
            $fqn = get_class() . '::' . $propName;
            if (array_key_exists($fqn,static::$reflectionProperties)) {
                $reflectionProp = static::$reflectionProperties[$fqn];
            } else {
                $reflectionProp = new \ReflectionProperty($this,$propName);
                static::$reflectionProperties[$fqn] = $reflectionProp;
            }
            $reflectionProp->setAccessible(TRUE);
            $value = NULL;
            if ($reflectionProp->isStatic()) {
                $value = $reflectionProp->getValue();
                return (null !== $value);
            } else {
                $value = $reflectionProp->getValue($this);
                return (null !== $value);
            }
        } elseif (property_exists($this, '_' . $propName)) {
            $altPropName    = '_' . $propName;
            $fqn = get_class() . '::' . $altPropName;
            if (array_key_exists($fqn,static::$reflectionProperties)) {
                $reflectionProp = static::$reflectionProperties[$fqn];
            } else {
                $reflectionProp = new \ReflectionProperty($this,$propName);
                static::$reflectionProperties[$fqn] = $reflectionProp;
            }
            $reflectionProp->setAccessible(TRUE);
            $value = NULL;
            if ($reflectionProp->isStatic()) {
                $value = $reflectionProp->getValue();
                return (null !== $value);
            } else {
                $value = $reflectionProp->getValue($this);
                return (null !== $value);
            }
        }
        return FALSE;
    }
}