<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 14.01.2015
 * Time: 10:46
 */
trait arrayMappableTrait {

    use hookableTrait;

    /**
     * @param array $arr
     *
     * @return int
     */
    public function mapFromArray( array $arr ) {
        return $this->_mapArray($arr);
    }


    /**#
     * @param array $arr
     *
     * @return int
     */
    protected function _mapArray( array $arr ) {
        $i = 0;
        foreach ($arr as $key => $val) {
            if (property_exists($this, $key)) {
                ++$i;
                $this->$key = $val;
            }
        }
        $this->updateHooks('changed',$this);
        return $i;
    }

    public function mapFromArrayAndConfig(array $arr, ModelDBConfigInterface $config): Int
    {
        $i = 0;
        foreach ($config->getMappedFields() as $fieldArr) {
            $name = $fieldArr['name'];
            $type = $fieldArr['type'];
            if (property_exists($this,$name) && array_key_exists($name,$arr)) {
                switch($type) {
                    case MySQLTypeIsText($type): $this->$name = (string)$arr[$name];
                        break;
                    case (strcasecmp($type,"decimal") === 0): $this->$name = (float)$arr[$name];
                        break;
                    case MySQLTypeIsNumeric($type): $this->$name = (int)$arr[$name];
                        break;
                    case MySQLTypeIsDate($type): $this->$name = (string)$arr[$name];
                        break;
                    default: throw new \InvalidArgumentException("Cannot map unknown type [$type]");
                }
                $i++;
            }
        }
        return $i;
    }

    /**
     * @param array $arr
     * @return bool
     */
    public function isArrayFullyMappable(array $arr ) {
        foreach ($arr as $key => $val) {
            if(!(property_exists($this,$key)) && !(property_exists($this,'_' . $key))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $arr
     * @return bool
     */
    public function isArrayPartiallyMappable(array $arr ) {
        $i = 0;
        foreach ($arr as $key => $val) {
            if((property_exists($this,$key)) || (property_exists($this,'_' . $key))) {
                $i++;
            }
        }
        return ($i > 0);
    }

    /**
     * @return array
     */
    public function getAllFieldsAsArray() {
        $arr = [];
        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        foreach($props as $prop) {
            $name = $prop->getName();
            $arr[$name] = $this->$name;
        }
        return $arr;
    }


    /**
     * @param array $fields
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getFieldData(array $fields) {
        $validFieldsArr = [];
        foreach($fields as $index => $fieldName) {
            if(!is_string($fieldName)) {
                throw new \InvalidArgumentException("Only strings are allowed as fieldsNames. Array-index $index is not a string.");
            }
            if($this->config->fieldExists($fieldName)) $validFieldsArr[] = $fieldName;
        }
        $dataArr = [];
        foreach($validFieldsArr as $validFieldName) {
            $dataArr[0][$validFieldName] = $this->{$validFieldName};
        }
        return $dataArr;
    }

}