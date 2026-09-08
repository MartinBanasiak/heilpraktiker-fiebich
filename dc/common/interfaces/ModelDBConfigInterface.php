<?php

namespace DynCom\dc\common\interfaces;
/**
 * Interface DynCom\dc\common\interfaces\ModelDBConfigInterface
 */
interface ModelDBConfigInterface
{

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @return array
     */
    public function getAltPrimary();

    /**
     * @return array
     */
    public function getMappedFields();

    /**
     * @return array
     */
    public function getAllowedComparisonOperators();

    /**
     * @return string
     */
    public function getModelClassName();

    /**
     * @return string
     */
    public function getBaseTableName();

    /**
     * @param $name
     * @return bool
     */
    public function fieldExists($name);
}