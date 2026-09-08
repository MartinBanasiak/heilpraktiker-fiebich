<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\interfaces\FlattenedGettable;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 12:59
 */
trait flattenedGettableTrait
{

    /**
     * @return array
     */
    public function getFlattened() {
        $ref = new \ReflectionClass($this);
        $arr = [];
        foreach ($ref->getProperties() as $property) {
            $name = $property->name;
            $val = $this->$name;
            $this->addFieldToArray($name,$val,$arr);
        }
        return $arr;
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param array $array
     */
    protected function addFieldToArray($fieldName, &$fieldValue, array &$array) {
        $val = $this->$fieldName;
        $fieldAdded = false;
        if(is_object($val)) {;
            if($val instanceof FlattenedGettable) {
                $subFieldArr = $val->getFlattened();
                foreach ($subFieldArr as $subFieldName => &$subFieldValue) {
                    $this->addFieldToArray($subFieldName, $subFieldValue, $array);
                }
                unset($subFieldValue);
                $fieldAdded = true;
            }
        }
        if(!$fieldAdded) {
            $array[$fieldName] = clone $fieldValue;
        }
    }

}