<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 12:55
 */
trait arrayGettableTrait
{
    /**
     * @return array
     */
    protected function getAllFieldsAsArray() {
        $ref = new \ReflectionClass($this);
        $arr = [];
        foreach ($ref->getProperties() as $property) {
            $name = $property->name;
            $arr[$name] = clone $this->$name;
        }
        return $arr;
    }
}