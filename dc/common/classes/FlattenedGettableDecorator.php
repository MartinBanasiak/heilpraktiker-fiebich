<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\FlattenedGettable;
use DynCom\dc\dcShop\traits\genericDecoratorTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 16.07.2015
 * Time: 13:12
 */
class FlattenedGettableDecorator implements FlattenedGettable
{

    use genericDecoratorTrait;

    /**
     * FlattenedGettableDecorator constructor.
     * @param $entityToDecorate
     */
    public function __construct($entityToDecorate) {
        if(!is_object($entityToDecorate)) {
            throw new \InvalidArgumentException(
              "entityToDecorate must be an object"
            );
        }
        $this->decoratedEntity = $entityToDecorate;
    }

    /**
     * @return string
     */
    public function getDecoratedEntityClassName() {
        return get_class($this->decoratedEntity);
    }

    /**
     * @return array
     */
    public function getFlattened() {
        $ref = new \ReflectionClass($this->decoratedEntity);
        $arr = [];
        foreach ($ref->getProperties() as $property) {
            $name = $property->name;
            $val = $this->decoratedEntity->$name;
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