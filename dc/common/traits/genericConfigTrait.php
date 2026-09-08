<?php
namespace DynCom\dc\common\traits;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.01.2015
 * Time: 23:35
 */

trait genericConfigTrait {


    protected $allowedComparisonOperators = array('IS NULL','IS NOT NULL','=','!=','<','>','>=','<=','IN','LIKE');

    /**
     * @return array
     */
    public function getAllowedComparisonOperators() {
        return $this->allowedComparisonOperators;
    }

    /**
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getAltPrimary() {
        return $this->altPrimary;
    }

    /**
     * @return array
     */
    public function getMappedFields() {
        return $this->mappedFields;
    }

    /**
     * @return mixed
     */
    public function getModelClassName() {
        return $this->modelClassName;
    }

    /**
     * @return mixed
     */
    public function getBaseTableName() {
        if(isset($this->baseTableName)) {
            return $this->baseTableName;
        }
        return $this->tableName;
    }

    /**
     * @param $name
     * @return bool
     */
    public function fieldExists($name) {
        foreach($this->mappedFields as $fieldInfoArr) {
            if($fieldInfoArr['name'] === $name) return true;
        }
        return false;
    }

}