<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 03:53
 */

/**
 * Class genericRepositoryTrait
 * Provides properties and methods that constitute a facade for data-access to the entities of the concrete repository class using this trait
 * via findByID, findByAltPrimary, findByCriteria, updateAll, updateSingle, createInDB.
 * Concrete repository class must overwrite constructor to require specific interface for Config and specific class for Collection.
 * For example, repositories for document-classes must require DocumentModelDBConfigInterface and
 * Allows addition and removal by consumers *
 */

trait genericRepositoryTrait
{

    private $allowedComparisonOperators = array('IS NULL', 'IS NOT NULL', '=', '!=', '<', '>', '>=', '<=', 'IN', 'LIKE');
    protected $db;
    /**
     * @var ModelDBConfigInterface
     */
    protected $config;
    protected $criteriaValidationService;
    protected $collection;
    protected $collectionEntryClassName;
    protected $cacheAll;

    private $lockedRecordIDs = array();

    protected $lastDateTimeAllCached = '2000-01-01 00:00:00';

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param ModelDBConfigInterface $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param GenericCollectionInterface $collection
     * @param                                                     $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, ModelDBConfigInterface $config, CriteriaHelperInterface $criteriaValidationService, GenericCollectionInterface $collection, $cacheAll=false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    protected function _cacheAll()
    {
        if ($this->_isTableUpdated()) {
            $allRowsCollection = $this->_getFullTableAsCollection();
            $this->collection = $allRowsCollection;
        }
    }

    /**
     * @return bool
     */
    protected function _isTableUpdated()
    {
        $referenceDateTime = new \DateTime($this->lastDateTimeAllCached);
        $tableName = $this->config->getBaseTableName();
        $query = '
            SELECT (
                CASE
                    WHEN
                    (
                        (`CREATE_TIME` > \'' . $referenceDateTime->format(\DateTime::ATOM) . '\')
                      OR
                        (`UPDATE_TIME` > \'' . $referenceDateTime->format(\DateTime::ISO8601) . '\')
                    )
                    THEN
                        1
                    ELSE
                        0
                 END
            ) AS \'TableIsNewer\'
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_NAME` = \'' . $tableName . '\'
        ';
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resArr = $this->db->getResultArray();
        return (bool)$resArr[0]['TableIsNewer'];
    }

    /**
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    protected function _getFullTableAsCollection()
    {
        $tableName = $this->config->getBaseTableName();
        $query = 'SELECT * FROM `' . $tableName . '`';
        $this->db->setQuery($query);
        $this->db->doQuery();
        $resultArray = $this->db->getResultArray();
        $collection = $this->collection->mapArrayToNewCollection($resultArray);
        return $collection;
    }

    /**
     * @param array $dataArray
     * @return mixed
     */
    protected function _mapArrayToObject(array $dataArray)
    {
        $newInstance = $this->_getNullObject();
        $newInstance->mapFromArrayAndConfig($dataArray,$this->config);
        return $newInstance;
    }

    /**
     * @param      $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function add($instance, $idCheck = FALSE)
    {
        return $this->_add($instance, $idCheck);
    }

    /**
     * @param      $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    protected function _add($instance, $idCheck = FALSE)
    {
        return $this->collection->add($instance, $idCheck);
    }

    /**
     * @param      $instance
     * @param bool $idCheck
     *
     * @return bool
     */
    public function remove($instance, $idCheck = FALSE)
    {
        return $this->collection->remove($instance, $idCheck);
    }

    /**
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getAll()
    {
        return $this->_getAll();
    }

    /**
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    protected function _getAll()
    {
        return $this->collection;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findByID($id)
    {
        return $this->_findByID($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    protected function _findByID($id)
    {
        $id = (int)$id;
        $tableName = $this->config->getBaseTableName();
        $returnObject = $this->_getNullObject();
        if (null === $id || 0 === $id) {
            return $returnObject;
        }

        //try from collection
        foreach ($this->collection as $existingInstance) {
            if ($existingInstance->id === (int)$id) {
                return $existingInstance;
            }
        }

        //have cache refreshed if needed
        if ($this->cacheAll) {
            $this->_cacheAll();
        }
        //try from DB
        $resArr = $this->_getTableRowAsArrayByID(($id));
        if (count($resArr) === 0 || !array_key_exists('id',$resArr[0])) {
            throw new \InvalidArgumentException('No record with id [' . $id . '] exists in table [' . $this->config->getBaseTableName() . '].');
        }
        $dataArr = $this->_filterArrForMappedFields($resArr[0]);
        $returnObject->mapFromArrayAndConfig($dataArr,$this->getConfig());
        $this->_add($returnObject, TRUE);


        return $returnObject;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function _existsIDInTable($id)
    {
        $id = (int)$id;
        $tableName = $this->config->getBaseTableName();

        $query = "SELECT EXISTS (SELECT `id` FROM `{$tableName}` WHERE `id` = '{$id}')";

        $this->db->setQuery($query);
        $this->db->doQuery();
        if (!($this->db->getNoOfReturnedRows()) === 1) {
            return FALSE;
        }

        $resArr = $this->db->getResultArray();
        $retVal = (bool)$resArr[0][0];
        return $retVal;
    }


    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    protected function _getTableRowAsArrayByID($id)
    {
        $id = (int)$id;
        $tableName = $this->config->getBaseTableName();

        $query = "SELECT * FROM `" . $tableName . "` WHERE `id` = '" . $id."'";
        $this->db->setQuery($query);
        $this->db->doQuery();

        if (!($this->db->getNoOfReturnedRows() === 1)) {
            throw new \Exception("Table {$tableName} does not have exactly 1 entry for id {$id}");
        }

        $resArr = $this->db->getResultArray();
        $this->db->closeStatement();
        return $resArr;

    }

    /**
     * @return mixed Null (unset) instance of collection-element class
     */
    protected function _getNullObject() : GenericDBModelInterface
    {
        return $this->collection->getNullObject();
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    protected function _filterArrForMappedFields(array $arr)
    {
        $output = array();
        foreach ($this->config->getMappedFields() as $fieldArr) {
            $name = $fieldArr['name'];
            if (array_key_exists($name, $arr)) {
                $output[$name] = $arr[$name];
            }
        }
        return $output;
    }

    /**
     * @return string
     */
    protected function _getCollectionEntryClassName()
    {
        return $this->collection->getEntryClassName();
    }

    /**
     * @return string
     */
    public function getCollectionEntryClassName()
    {
        return $this->_getCollectionEntryClassName();
    }

    /**
     * @param array $altPrimary
     *
     * @return mixed
     */
    public function findByAltPrimary(array $altPrimary)
    {
        return $this->_findByAltPrimary($altPrimary);
    }

    /**
     * @param array $searchAltPrimary
     * @return mixed
     * @throws \BadMethodCallException
     */
    protected function _findByAltPrimary(array $searchAltPrimary)
    {
        //Try from collection
        foreach ($this->collection as $instance) {
            $instanceAltPrimary = array();
            foreach ($searchAltPrimary as $key => $value) {
                $instanceAltPrimary[$key] = $instance->$key;
            }
            if ($instanceAltPrimary === $searchAltPrimary) {
                return $instance;
            }
        }
        //else try from db
        if ($this->cacheAll) {
            $this->_cacheAll();
        }

        $arr = $this->getFindByAltPrimaryQueryAndValueMappingArray($searchAltPrimary);
        if (empty($arr['valueMappingArray'])) {
            return $this->getNullObject();
            //throw new \DomainException('No mapping of data to defined alt-primary. Data-Arr: ' . print_r($searchAltPrimary,1) . ' | Def-Arr:  ' . print_r($arr,1));
        }

        $valueMappingArray = $arr['valueMappingArray'];
        $query = $arr['query'];
        $this->db->setQuery($query);

        if (!$this->db->prepareQuery()) {
            throw new \BadMethodCallException('Could not prepare query. DB-Error: ' . $this->db->getErrorMessage() . ' || Query: ' . $query);
        }

        if (!$this->db->bindParameters($valueMappingArray)) {
            throw new \BadMethodCallException('Could not bind parameters. DB-Error: ' . $this->db->getErrorMessage() . ' || Query: ' . $query . ' || Param-array: ' . print_r($valueMappingArray, 1));
        }

        if (!$this->db->executePreparedStatement()) {
            throw new \BadMethodCallException('Could not execute prepared statement. DB-Error: ' . $this->db->getErrorMessage() . ' || Query: ' . $query);
        }

        if (!($this->db->getNoOfReturnedRows() === 1)) {
            //throw new \Exception('Query did not return (unique) result. DB-Error: ' . $this->db->getErrorMessage() . ' || Query: ' . $query . ' || Results: ' . $this->db->getNoOfReturnedRows());
            return $this->_getNullObject();
        }

        $resArr = $this->db->getResultArray();

        $this->db->closeStatement();

        $dataArr = $this->_filterArrForMappedFields($resArr[0]);
        $instance = $this->_getNullObject();
        $instance->mapFromArrayAndConfig($dataArr,$this->getConfig());

        $this->_add($instance, TRUE);
        return $instance;

    }

    /**
     * @param array $searchAltPrimary
     * @return array
     */
    public function getFindByAltPrimaryQueryAndValueMappingArray(array $searchAltPrimary)
    {
        $altPrimary = $this->config->getAltPrimary();
        $altPrimaryValues = array();
        $altPrimaryTypes = array();
        foreach ($altPrimary as $name) {
            if (array_key_exists($name, $searchAltPrimary)) {
                $altPrimaryValues[$name] = $searchAltPrimary[$name];
            } else {
                return [];
                //throw new \DomainException('Paramter doesn\'t contain a value for primary-key-field \'' . $name . '\'');
            }
            foreach ($this->config->getMappedFields() as $fieldArr) {
                if ($fieldArr['name'] === $name && array_key_exists('type', $fieldArr)) {
                    $altPrimaryTypes[$name] = $fieldArr['type'];
                } elseif ($fieldArr['name'] === $name) {
                    return [];
                    //throw new \DomainException('Unique Key Field \'' . $name . '\' has no type in config->mappedFields');
                }
            }
        }

        $query = 'SELECT * FROM `' . $this->config->getTableName() . '` WHERE ';
        $i = 0;
        $valueMappingArray = array();
        foreach ($altPrimaryValues as $key => $value) {
            $type = $altPrimaryTypes[$key];
            $paramPlaceholder = ':' . $key;
            if ($i > 0) {
                $query .= 'AND ';
            }
            if (MySQLTypeIsText($type)) {
                $valueMappingArray[] = array($paramPlaceholder, $value, \PDO::PARAM_STR);
                $query .= '`' . $key . '` = ' . $paramPlaceholder . ' ';
            } elseif ($type === 'DECIMAL' || $type === 'FLOAT' || $type === 'DOUBLE') {
                $valueMappingArray[] = array($paramPlaceholder, (string)((float)$value), \PDO::PARAM_STR);
                $query .= '`' . $key . '` = ' . $paramPlaceholder . ' ';
            } elseif (stringEndsWith($type, 'INT')) {
                $valueMappingArray[] = array($paramPlaceholder, $value, \PDO::PARAM_INT);
                $query .= '`' . $key . '` = ' . $paramPlaceholder . ' ';
            } elseif (MySQLTypeIsDate($type)) {
                $valueMappingArray[] = array($paramPlaceholder, (string)$value, \PDO::PARAM_STR);
                $query .= '`' . $key . '` = ' . $paramPlaceholder . ' ';
            }
            ++$i;
        }
        $arr = array('query' => $query, 'valueMappingArray' => $valueMappingArray);
        return $arr;
    }

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return GenericCollectionInterface
     * @throws \Exception
     */
    public function findByCriteria(array $criteria, $offset = 0, $limit = 0, $orderBy = '')
    {
        return $this->_findByCriteria($criteria, $offset, $limit, $orderBy);
    }


    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return GenericCollectionInterface
     * @throws \InvalidArgumentException
     */
    protected function _findByCriteria(array $criteria, $offset = 0, $limit = 0, $orderBy = '')
    {

        $offset = (int)$offset;
        $limit = (int)$limit;

        $resultCollection = $this->collection->getEmptyCollection();

        $errors = [];
        if (!$this->criteriaValidationService->validateCriteria($this->config, $criteria, $errors)) {
            throw new \InvalidArgumentException('Criteria-Array invalid. Errors: ' . print_r($errors, 1));
        }

        if ($this->cacheAll) {
            $this->_cacheAll();
        }

        $query = $this->_criteriaToQuery($criteria);

        if ($orderBy <> '')
        {
            $query .= " ORDER BY ".$orderBy;
        }

        if (($limit !== 0) || ($offset !== 0)) {
            $query .= " LIMIT $offset,$limit";
        }
        $this->db->setQuery($query);
        if (!$this->db->doQuery()) {
            throw new \ErrorException('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
            //return $resultCollection;
        }

        if (!($this->db->getNoOfReturnedRows() > 0)) {
            return $resultCollection;
        }

        $resArr = $this->db->getResultArray();
        $this->db->closeStatement();

        foreach ($resArr as $dataArr) {
            $instance = $this->getNullObject();
            $noOfMappedFields = $instance->mapFromArrayAndConfig($dataArr,$this->getConfig());
            if ($noOfMappedFields > 0) {
                $this->_add($instance, TRUE);
                $resultCollection->add($instance, TRUE);
            }
        }
        return $resultCollection;
    }

    /**
     * @param array $criteria
     * @return string
     */
    public function getFindByCriteriaQuery(array $criteria)
    {
        return $this->_criteriaToQuery($criteria);
    }

    /**
     * @param array $criteria
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function _criteriaToQuery(array $criteria)
    {

        if (!$this->criteriaValidationService->validateCriteria($this->config, $criteria)) {
            throw new \InvalidArgumentException('Criteria-Array invalid');
        }

        $fieldNames = array();
        $fieldTypes = array();
        foreach ($this->config->getMappedFields() as $fieldArr) {
            $fieldNames[] = $fieldArr['name'];
            $fieldTypes[$fieldArr['name']] = $fieldArr['type'];
        }

        $query = 'SELECT * FROM `' . $this->config->getTableName() . '` WHERE ';

        $i = 0;
        foreach ($criteria as $disjunctCriteria) {
            if ($i > 0) {
                $query .= 'OR ';
            }
            $query .= '( ';
            $j = 0;
            foreach ($disjunctCriteria as $conjunctCriterion) {
                if ($j > 0) {
                    $query .= 'AND ';
                }
                $fieldName = $conjunctCriterion[0];
                $fieldType = $fieldTypes[$fieldName];
                $upperFieldType = strtoupper($fieldType);
                $comparator = $conjunctCriterion[1];

                if (!in_array($comparator, $this->allowedComparisonOperators, TRUE)) {
                    throw new \InvalidArgumentException('At least one criterion has invalid comparison-operator: ' . (string)$comparator);
                }

                $compareValue = NULL;
                if (array_key_exists(2, $conjunctCriterion)) {
                    $compareValue = $conjunctCriterion[2];
                }

                if ($comparator === 'IS NULL') {
                    $query .= '`' . $fieldName . '` IS NULL';
                } elseif ($comparator === 'IS NOT NULL') {
                    $query .= '`' . $fieldName . '` IS NOT NULL';
                } elseif ($comparator === '=') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` = ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType === 'FLOAT' || $upperFieldType === 'DOUBLE') {
                        $query .= '`' . $fieldName . '` = \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` = \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` = \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === '!=') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` != ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType=== 'FLOAT' || $upperFieldType === 'DOUBLE') {
                        $query .= '`' . $fieldName . '` != \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` != \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` != \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === '>') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` > ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType === 'FLOAT' || $upperFieldType === 'DOUBLE') {
                        $query .= '`' . $fieldName . '` > \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` > \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` > \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === '<') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` < ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType === 'FLOAT' || $upperFieldType === 'DOUBLE') {
                        $query .= '`' . $fieldName . '` < \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` < \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` < \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === '<=') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` <= ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType === 'FLOAT' || $upperFieldType === 'DOUBLE') {
                        $query .= '`' . $fieldName . '` <= \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` <= \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` <= \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === '>=') {
                    if (MySQLTypeIsText($fieldType)) {
                        $query .= '`' . $fieldName . '` >= ' . $this->db->escapeString($compareValue) . ' ';
                    } elseif ($upperFieldType === 'DECIMAL' || $upperFieldType === 'FLOAT' || $upperFieldType=== 'DOUBLE') {
                        $query .= '`' . $fieldName . '` >= \'' . $compareValue . '\' ';
                    } elseif (stringEndsWith($fieldType, 'INT')) {
                        $query .= '`' . $fieldName . '` >= \'' . $compareValue . '\' ';
                    } elseif (MySQLTypeIsDate($fieldType)) {
                        $query .= '`' . $fieldName . '` >= \'' . (string)$compareValue . '\'';
                    }
                } elseif ($comparator === 'IN') {
                    $query .= '`' . $fieldName . '` IN [';
                    $z = 0;
                    foreach ($compareValue as $val) {
                        if ($z > 0) {
                            $query .= ',';
                        }
                        $query .= '\'' . $val . '\'';
                        ++$z;
                    }
                } elseif ($comparator === 'LIKE') {
                    //fnmatch-Wildcard durch SQL-Wildcard ersetzen
                    $compareValue = str_replace('*', '%', $compareValue);
                    $query .= '`' . $fieldName . '` LIKE ' . $this->db->escapeString($compareValue) . ' ';

                }
                ++$j;
            }
            $query .= ') ';
            ++$i;
        }
        return $query;
    }

    /**
     * @return int
     */
    public function updateAll()
    {
        return $this->_updateAll();
    }

    /**
     * @return int
     */
    protected function _updateAll()
    {
        $count = count($this->collection);
        $successes = 0;
        foreach ($this->collection as $instance) {
            if ($this->updateSingle($instance)) {
                ++$successes;
            }
        }
        return ($count - $successes);
    }

    /**
     * @param $instance
     *
     * @return bool
     */
    public function updateSingle(GenericDBModelInterface $instance)
    {
        return $this->_updateSingle($instance);
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return bool
     * @throws \ErrorException
     */
    protected function _updateSingle(GenericDBModelInterface $instance)
    {
        if (isset($instance->id) && $instance->id > 0 && $this->isRecordLocked($instance->id)) {
            throw new \DomainException('Entity is locked and cannot be updated');
        }
        $this->lockRecordByID($instance->id);
        $query = $this->getUpdateSingleQuery($instance);
        $this->db->setQuery($query);
        if (!$this->db->doQuery()) {
            throw new \ErrorException('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
        }

        $this->db->closeStatement();
        $this->_add($instance, TRUE);
        $this->releaseRecordLock($instance->id);
        return TRUE;
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return string
     * @throws \DomainException
     */
    public function getUpdateSingleQuery(GenericDBModelInterface $instance)
    {

        $className = $this->getCollectionEntryClassName();
        if (!$instance instanceof $className) {
            throw new \DomainException('Parameter \'instance\' is not an instance of type \'' . $className . '\'');
        }
        $primary = array();
        if (property_exists($instance, 'id')) {
            $primary[] = 'id';
        }
        if (is_array($this->config->getAltPrimary()) && (count($this->config->getAltPrimary()) > 0)) {
            $primary = $this->config->getAltPrimary();
        }
        if (count($primary) < 1) {
            throw new \DomainException('No primary key available');
        }
        $mappedFieldsArr = $this->config->getMappedFields();
        $primaryArr = array();
        foreach ($primary as $keyFieldName) {
            if (null === ($instance->$keyFieldName)) {
                throw new \DomainException('Instance lacks value for unique key field \'' . $keyFieldName . '\'');
            } else {
                $primaryArr[] = array('name' => $keyFieldName, 'value' => $instance->$keyFieldName);
            }
        }
        $query = 'UPDATE `' . $this->config->getBaseTableName() . '` SET ';
        $i = 0;
        foreach ($mappedFieldsArr as $fieldArr) {
            $propName = $fieldArr['name'];
            if (null !== $instance->$propName && !in_array($propName, $primary, TRUE)) {
                if ($i > 0) {
                    $query .= ',';
                }
                if (MySQLTypeIsText($fieldArr['type'])) {
                    $query .= '`' . $propName . '` = ' . $this->db->escapeString($instance->$propName) . ' ';
                } elseif ($fieldArr['type'] === 'DECIMAL' || $fieldArr['type'] === 'FLOAT' || $fieldArr['type'] === 'DOUBLE') {
                    $query .= '`' . $propName . '` = ' . (float)$instance->$propName . ' ';
                } elseif (stringEndsWith($fieldArr['type'], 'INT')) {
                    $query .= '`' . $propName . '` = ' . (int)$instance->$propName . ' ';
                } elseif (MySQLTypeIsDate($fieldArr['type'])) {
                    if ($instance->$propName !== 'NULL') {
                        $query .= '`' . $propName . '` = \'' . $instance->$propName . '\' ';
                    } else {
                        $query .= '`' . $propName . '` = NULL';
                    }
                } else {
                    $query .= '`' . $propName . '` = ' . $this->db->escapeString($instance->$propName) . ' ';
                }
                ++$i;
            }
        }
        $query .= 'WHERE ';
        $i = 0;
        foreach ($primaryArr as $primaryFieldArr) {
            $name = $primaryFieldArr['name'];
            $value = $primaryFieldArr['value'];
            foreach ($mappedFieldsArr as $fieldArr) {
                if ($fieldArr['name'] === $name) {
                    $type = $fieldArr['type'];
                    break;
                }
            }
            if (null === $type || $type === '' || !is_string($type)) {
                throw new \DomainException('No type for primary key field \'' . $name . '\'');
            }
            if ($i > 0) {
                $query .= 'AND ';
            }
            if (MySQLTypeIsText($type)) {
                $query .= '`' . $name . '` = ' . $this->db->escapeString($value) . ' ';
            } elseif ($type === 'DECIMAL' || $type === 'FLOAT' || $type === 'DOUBLE') {
                $query .= '`' . $name . '` = \'' . (float)$value . '\' ';
            } elseif (stringEndsWith($type, 'INT')) {
                $query .= '`' . $name . '` = \'' . (int)$value . '\' ';
            } elseif (MySQLTypeIsDate($type)) {
                $query .= '`' . $name . '` = ' . $value . ' ';
            }
            ++$i;
        }
        $query .= 'LIMIT 1';
        return $query;
    }

    /**
     * @param $instance
     *
     * @return bool|int
     */
    public function createInDB(GenericDBModelInterface $instance)
    {
        return $this->_createInDB($instance);
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return bool
     * @throws \ErrorException
     */
    protected function _createInDB(GenericDBModelInterface $instance)
    {
        $query = $this->getCreateInDBQuery($instance);
        $this->db->setQuery($query);
        if (!$this->db->doQuery()) {
            throw new \ErrorException('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
        }

        $affectedRows = $this->db->getNoOfAffectedRows();
        if (!($affectedRows === 1)) {
            $this->db->closeStatement();
            return FALSE;
        }

        $id = $this->db->getLastInsertId();
        $this->db->closeStatement();

        $instance = $this->_findByID($id);
        $this->_add($instance, TRUE);
        return $instance->id;
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return string
     * @throws \DomainException
     */
    public function getCreateInDBQuery(GenericDBModelInterface $instance)
    {
        $className = $this->getCollectionEntryClassName();
        if (!($instance instanceof $className)) {
            throw new \DomainException('Parameter \'instance\' is not an instance of class \'' . $className . '\'');
        }

        $query = 'INSERT INTO `' . $this->config->getTableName() . '` SET ';
        $i = 0;
        foreach ($this->config->getMappedFields() as $fieldArr) {
            $name = $fieldArr['name'];
            $type = strtoupper($fieldArr['type']);
            if ($name !== 'id') {
                if ($i > 0) {
                    $query .= ',';
                }

                if (MySQLTypeIsText($type)) {
                    $query .= '`' . $name . '` = ' . $this->db->escapeString($instance->$name) . ' ';
                } elseif ($type === 'DECIMAL' || $type === 'FLOAT' || $type === 'DOUBLE') {
                    $query .= '`' . $name . '` = \'' . (float)$instance->$name . '\' ';
                } elseif (stringEndsWith($type, 'INT')) {
                    $query .= '`' . $name . '` = \'' . (int)$instance->$name . '\' ';
                } elseif (MySQLTypeIsDate($fieldArr['type'])) {
                    if((null !== $instance->$name) && ($instance->$name !== 'NULL')) {
                        $query .= '`' . $name . '` = \'' . $instance->$name . '\' ';
                    } else {
                        $query .= '`' . $name . '` = NULL';
                    }
                }
                ++$i;
            }
        }
        return $query;
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDeleteByIDQuery(GenericDBModelInterface $instance)
    {
        $className = $this->getCollectionEntryClassName();
        if (!($instance instanceof $className)) {
            throw new \DomainException('Parameter \'instance\' is not an instance of class \'' . $className . '\'');
        }
        if (!((int)$instance->getID() > 0)) {
            throw new \InvalidArgumentException('Instance has no positive value for field \'id\'');
        }
        $query = 'DELETE FROM `' . $this->config->getBaseTableName() . '` WHERE id = ' . (int)$instance->id;
        return $query;
    }

    /**
     * @param GenericDBModelInterface $instance
     * @return bool
     * @throws \DomainException
     */
    public function deleteByID(GenericDBModelInterface $instance)
    {
        if ($this->isRecordLocked($instance->getID())) {
            throw new \DomainException("Entity with id {$instance->getID()} is locked and cannot be deleted");
        }
        $query = $this->getDeleteByIDQuery($instance);
        $this->lockRecordByID($instance->getID());
        $this->remove($instance, true);
        $this->db->setQuery($query);
        if (!$this->db->doQuery()) {
            throw new \BadMethodCallException('Could not execute query. DB-Error: ' . $this->db->getErrorMessage() . ' Query: ' . $query);
        }

        $affectedRows = $this->db->getNoOfAffectedRows();
        $this->db->closeStatement();
        $this->releaseRecordLock($instance->id);
        if (!($affectedRows === 1)) {
            return false;
        }
        return true;
    }

    /**
     * @return Entity Null (unset) instance of the class of elements in the collection
     */
    public function getNullObject()
    {
        return $this->_getNullObject();
    }

    /**
     * @param array $arr
     *
     * @return array
     */
    public function filterArrForMappedFields(array $arr)
    {
        return $this->_filterArrForMappedFields($arr);
    }

    /**
     * @param      $instance
     * @param bool $idCheck
     */
    protected function _remove($instance, $idCheck = FALSE)
    {
        $this->collection->remove($instance, $idCheck);
    }

    /**
     * @return \DynCom\dc\common\interfaces\ModelDBConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->config->getModelClassName();
    }

    /**
     * @param $id
     */
    public function lockRecordByID($id)
    {
        if (!array_key_exists((int)$id, $this->lockedRecordIDs)) {
            $this->lockedRecordIDs[(int)$id] = (int)$id;
        }
    }

    /**
     * @param $id
     */
    public function releaseRecordLock($id)
    {
        if (array_key_exists((int)$id, $this->lockedRecordIDs)) {
            unset($this->lockedRecordIDs[(int)$id]);
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function isRecordLocked($id)
    {
        return array_key_exists((int)$id, $this->lockedRecordIDs);
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function removeEntityFromCache(GenericDBModelInterface $entity)
    {
        if (isset($entity->id) && ((int)$entity->id > 0)) {
            $this->collection->remove($entity, true);
        } else {
            $this->collection->remove($entity, false);
        }
    }

    /**
     * @param $id
     */
    public function recacheEntityFromDBByID($id)
    {
        $this->findByID((int)$id);
    }

    /**
     * @param array $selectionCriteria
     * @param array $fields
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \ErrorException
     */
    public function getFieldData(array $selectionCriteria, array $fields = [], $offset = 0, $limit = 0)
    {
        $resultCollection = $this->_findByCriteria($selectionCriteria,$offset,$limit);
        return $resultCollection->getFieldData($fields);
    }

    /**
     * @return bool
     */
    public function startTransaction()
    {
        return $this->db->startTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->db->commitTransaction();
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->db->rollbackTransaction();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->db->inTransaction();
    }

    /**
     * @param $query
     * @return \Generator
     */
    public function getAllByQueryAsArray($query) : \Generator
    {
        $result = [];
        /**
         * @var $pdo \PDO
         */
        $pdo = $this->db->getConnectionObject();
        $stmt = $pdo->query($query);
        $noOfRows = $stmt->rowCount();
        $objectClassName = $this->getObjectClass();
        $config = $this->getConfig();
        if($noOfRows > 0) {
            while ($obj = $stmt->fetchObject($objectClassName,[$config])) {
                $this->_add($obj,true);
                yield $obj;
            }
        }
    }

    /**
     * @param $query
     * @return GenericCollectionInterface
     */
    public function getAllByQueryAsCollection($query)
    {
        $arr = [];
        foreach ($this->getAllByQueryAsArray($query) as $obj) {
            $arr[] = $obj;
        }

        $coll = $this->collection->getEmptyCollection();
        foreach ($arr as $el) {
            $coll->add($el,false);
        }
        return $coll;
    }

    /**
     * @param array $resultArray
     * @return GenericCollectionInterface
     */
    public function getCollectionForResultArray(array $resultArray)
    {
        $collection = $this->collection->getEmptyCollection();
        foreach ($resultArray as $entityRow) {
            $entity = $this->getNullObject();
            $entity->mapFromArray($entityRow);
            $this->collection->add($entity);
        }
        return $collection;
    }

    /**
     * @param $query
     * @return array
     */
    public function getQueryResultArray($query)
    {
        $result = [];
        $this->db->setQuery($query);
        $this->db->doQuery();
        $noRows = $this->db->getNoOfReturnedRows();
        if($noRows > 0) {
            $resArr = $this->db->getResultArray();
            foreach($resArr as $res) {
                $filtered = $this->_filterArrForMappedFields($res);
                $instance = $this->getNullObject();
                $noOfMappedFields = $instance->mapFromArray($filtered);
                if ($noOfMappedFields > 0) {
                    $this->_add($instance, TRUE);
                    $result[] = $instance;
                }
            }
        }
        return $result;
    }


}