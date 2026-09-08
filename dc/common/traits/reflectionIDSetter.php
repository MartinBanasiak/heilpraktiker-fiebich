<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.10.2015
 * Time: 10:42
 */
trait reflectionIDSetter
{

    /**
     * @param $object
     * @param $id
     */
    public function setID($object, $id)
    {
        $id = (int)$id;
        if(!is_object($object)) {
            throw new \InvalidArgumentException("Parameter 'object' must be an object.");
        }
        if(!($id > 0)) {
            throw new \InvalidArgumentException("Parameter 'id' must be a positive integer.");
        }
        $reflClass = new \ReflectionClass($object);
        if(!$reflClass->hasProperty('id')) {
            throw new \InvalidArgumentException("Object has no property 'id'.");
        }
        $idProp = $reflClass->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($object,$id);
    }

}