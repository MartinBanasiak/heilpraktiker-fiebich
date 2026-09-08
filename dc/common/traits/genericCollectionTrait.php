<?php
namespace DynCom\dc\common\traits;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 17:38
 * @TODO: Rewrite from getByCriteria to end for SplObjectStorage
 */
trait genericCollectionTrait
{

    protected $allowedComparisonOperations = array('IS NULL', 'IS NOT NULL', '=', '!=', '<', '>', '>=', '<=', 'IN', 'LIKE');
    protected $criteriaValidationService;
    /**
     * @var ModelDBConfigInterface
     */
    protected $config;
    protected $elements;
    protected $hashObjectMap = [];
    protected $hashesByID = [];
    protected $hashesByAltPrimary = [];
    protected $hashList = [];
    protected $fields;
    protected $altPrimary;
    protected $entryClassName;
    protected $currIndex = '';
    protected $currIndexPos = null;

    /**
     * @param ModelDBConfigInterface $config
     * @param CriteriaHelperInterface $criteriaValidationService
     */
    public function __construct(ModelDBConfigInterface $config, CriteriaHelperInterface $criteriaValidationService)
    {
        $this->elements = new \SplObjectStorage();
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->entryClassName = $config->getModelClassName();
        $this->altPrimary = $config->getAltPrimary();
    }

    /**
     * @return string
     */
    public function getBaseTableName()
    {
        return $this->config->getBaseTableName();
    }

    /**
     * @return  array   Array of fields (optionally with DB-Type) of the class for entries in the collection
     */
    public function getFieldsArray()
    {
        return $this->config->getMappedFields();
    }

    /**
     * @return  int Implements Countable interface
     */
    public function count()
    {
        //return count($this->elements);
        return count($this->hashObjectMap);
    }

    /**
     * @return \IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator(new \ArrayIterator($this->hashObjectMap));

    }

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool               Success or failure
     */
    public function remove($instance, $idCheck = false)
    {
        return $this->_remove($instance, $idCheck);
    }

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool               Success or failure
     */
    protected function _remove($instance, $idCheck = false)
    {
        $info = $this->getIndexInfo($instance);
        if (
            ($idCheck && (empty($info['id']) || !array_key_exists((string)$info['id'], $this->hashesByID)))
            || (!array_key_exists($info['hash'], $this->hashObjectMap))
        ) {
            return false;
        }
        $this->removeInstance($instance);
        return true;

        if (!$idCheck && $this->elements->contains($instance)) {
            $this->elements->detach($instance);
            return true;
        }
        $c = $this->elements;
        $c->rewind();
        $found = false;
        while ($c->valid() && !$found) {
            $object = $c->current();
            $currID = $object->id;
            if ($instance->id === $currID) {
                $found = true;
                $foundEntity = $object;
            }
            $c->next();
        }
        $c->rewind();
        if ($found) {
            $this->elements->detach($foundEntity);
            return true;
        }
        $c->rewind();
        return false;
    }

    /**
     * @return  array   The entire collection as array
     */
    public function getAllAsArray()
    {
        return array_values($this->hashObjectMap);
        $newArray = array();
        $this->elements->rewind();
        while ($this->elements->valid()) {
            $newArray[] = $this->elements->current();
            $this->elements->next();
        }
        $this->elements->rewind();
        return $newArray;
    }

    /**
     * @param   array $array Array containing instances of the class for entries in the collection
     *
     * @param bool $idCheck
     * @return bool Success or failure
     */
    protected function _resetEntriesFromArray(array $array, $idCheck = false)
    {
        $this->hashesByID = [];
        $this->hashesByAltPrimary = [];
        $this->hashObjectMap = [];
        $className = $this->getEntryClassName();
        //$newCollection = new SplObjectStorage();
        foreach ($array as $instance) {
            if ($instance instanceof $className) {
                //$newCollection->attach($instance);
                $this->add($instance, $idCheck);
            } else {
                return false;
            }
        }
        //$this->elements = $newCollection;
        return true;
    }


    /**
     * @return string
     */
    public function getEntryClassName()
    {
        return $this->entryClassName;
    }

    /**
     * @param   int $id
     *
     * @return  mixed   An instance of the collection-entry class (or Null-Object)
     */
    public function getByID($id)
    {
        return $this->_getByID($id);
    }

    /**
     * @param   int $id
     *
     * @return  mixed   An instance of the collection-entry class
     */
    protected function _getByID($id)
    {
        $obj = $this->getNullObject();
        if (!$this->hasInstanceForID($id)) {
            return $obj;
        }
        return $this->getInstanceByID($id);
        foreach ($this->elements as $instance) {
            if (isset($instance->id) && ($instance->id == (int)$id)) {
                return $instance;
            }
        }
        return $this->getNullObject();
    }

    /**
     * @return  mixed   Empty (unset) instance of the class for entries in the collection
     *                  !MUST! be overriden in classes with different constructor
     */
    public function getNullObject()
    {
        return new $this->entryClassName($this->config);
    }

    /**
     * @param   array $uniqueKeyArray An array specifying an alternative primary key, of the form:
     *                                array('keyField1Name'=>'keyField1Value','keyField2Name'=>'keyField2Value'...);
     *
     * @return  mixed                   An instance of the collection-entry class
     */
    public function getByAltPrimary(array $uniqueKeyArray)
    {
        return $this->getInstanceByAltPrimary($uniqueKeyArray);
        return $this->_getByAltPrimary($uniqueKeyArray);
    }

    /**
     * @param array $uniqueKeyArray
     * @return mixed
     * @throws \DomainException
     */
    protected function _getByAltPrimary(array $uniqueKeyArray)
    {
        $altPrimaryValues = array();
        foreach ($this->altPrimary as $name) {
            if (isset($uniqueKeyArray[$name])) {
                $altPrimaryValues[$name] = $uniqueKeyArray[$name];
            } else {
                throw new \DomainException('Paramter enthält keinen Wert für Schlüsselfeld \'' . $name . '\'');
                return $this->getNullObject();
            }
        }

        $all = $this->getAllAsArray();
        foreach ($all as $instance) {
            $instanceAltPrimary = array();
            foreach ($this->altPrimary as $key => $value) {
                $instanceAltPrimary[$key] = $instance->$key;
            }
            if ($instanceAltPrimary == $uniqueKeyArray) {
                return $instance;
            }
        }
        return $this->getNullObject();
    }

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *                                                  array(
     *                                                      array(
     *                                                          array('fieldName','comparator','fieldValue'),
     *                                                          **AND**
     *                                                          array('fieldName','comparator','fieldValue')
     *                                                          ...
     *                                                      ),
     *                                                      **OR**
     *                                                      array(
     *                                                          array('fieldName','comparator','fieldValue'),
     *                                                          **AND**
     *                                                          array('fieldName','comparator','fieldValue')
     *                                                          ...
     *                                                      )
     *                                                      ...
     *                                                  )
     *
     * @return  GenericCollectionInterface  $collection Subset of the current collection as instance of own concrete
     *                                                  collection class
     */
    public function getByCriteria(array $criteria)
    {
        return $this->_getByCriteria($criteria);
    }

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *                                                  array(
     *                                                      array(
     *                                                          array('fieldName','comparator','fieldValue'),
     *                                                          **AND**
     *                                                          array('fieldName','comparator','fieldValue')
     *                                                          ...
     *                                                      ),
     *                                                      **OR**
     *                                                      array(
     *                                                          array('fieldName','comparator','fieldValue'),
     *                                                          **AND**
     *                                                          array('fieldName','comparator','fieldValue')
     *                                                          ...
     *                                                      )
     *                                                      ...
     *                                                  )
     *
     * @return  GenericCollectionInterface  $collection Subset of the current collection as instance of own concrete
     *                                                  collection class
     */
    protected function _getByCriteria(array $criteria)
    {
        $resultCollection = new static($this->config, $this->criteriaValidationService);

        if (!$this->criteriaValidationService->validateCriteria($this->config, $criteria)) {
            return $resultCollection;
        }

        //foreach ($this->elements as $instance) {
        foreach ($this->hashObjectMap as $instance) {
            foreach ($criteria as $disjunctCriteria) {
                $isValidDisjunct = true;
                foreach ($disjunctCriteria as $conjunctCriterion) {
                    $fieldName = $conjunctCriterion[0];
                    $comparator = $conjunctCriterion[1];
                    $compareValue = isset($conjunctCriterion[2]) ? $conjunctCriterion[2] : '';

                    if ($comparator === 'IS NULL') {
                        if (!(is_null($instance->$fieldName))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === 'IS NOT NULL') {
                        if (is_null($instance->$fieldName)) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '=') {
                        if (!(isset($instance->$fieldName) && $instance->$fieldName == $compareValue)) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '!=') {
                        if (!($instance->$fieldName != $compareValue)) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '>') {
                        if (!(isset($instance->$fieldName) && ($instance->$fieldName > $compareValue))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '<') {
                        if (!(isset($instance->$fieldName) && ($instance->$fieldName < $compareValue))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '<=') {
                        if (!(isset($instance->$fieldName) && !($instance->$fieldName <= $compareValue))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === '>=') {
                        if (!(isset($instance->$fieldName) || ($instance->$fieldName >= $compareValue))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === 'IN') {
                        if (!(in_array($instance->$fieldName, $compareValue))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    } elseif ($comparator === 'LIKE') {
                        //SQL-Wildcard durch fnmatch-Wildcard ersetzen
                        $compareValue = str_replace('%', '*', $compareValue);
                        if (!(fnmatch($compareValue, $instance->$fieldName))) {
                            $isValidDisjunct = false;
                            break;
                        }
                    }
                }
                if ($isValidDisjunct) {
                    $resultCollection->add($instance, true);
                }
            }
        }
        return $resultCollection;
    }

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool                    Success or failure
     */
    public function add($instance, $idCheck = false)
    {
        return $this->_add($instance, $idCheck);
    }

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool                    Success or failure
     * @throws \DomainException
     */
    protected function _add($instance, $idCheck = false)
    {
        $info = $this->getIndexInfo($instance);

        if ((!$idCheck && array_key_exists($info['hash'],$this->hashObjectMap)) || $this->hasInstanceForID($info['id'])) {
            $this->updateByID($instance);
        } else {
            $this->addNewInstance($instance);
        }
        return true;



        $className = $this->getEntryClassName();
        $id = null;
        $hash = $this->elements->getHash($instance);
        if (!($instance instanceof $className)) {
            throw new \DomainException(
                'Parameter \'instance\' is not an instance of class \'' . $className . '\', instead is ' . get_class(
                    $instance
                )
            );
        }
        if (property_exists($instance, 'id')) {
            $idRefl = new \ReflectionProperty($instance, 'id');
            $idRefl->setAccessible(true);
            $id = $idRefl->getValue($instance);
        }
        if (!$idCheck) {
            if (!($this->elements->offsetExists($instance))) {
                $this->elements->attach($instance);
                return true;
            }
            return false;
        }
        if (count($this->elements) > 0) {
            $this->elements->rewind();

            if (!(property_exists($this->config->getModelClassName(), 'id') && isset($instance->id))) {
                throw new \DomainException(
                    'idCheck is Set but either given instance or collection content doesn\'t have id-field'
                );
            }
            $c = $this->elements;
            $c->rewind();
            $found = false;
            while ($c->valid() && !$found) {
                $object = $c->current();
                $currID = $object->id;
                if ($instance->id === $currID) {
                    $found = true;
                    $foundEntity = $object;
                }
                $c->next();
            }
            $c->rewind();
            if ($found) {
                $this->elements->detach($foundEntity);
                $this->elements->attach($instance);
                return true;
            }
        }
        $this->elements->attach($instance);
        $this->elements->rewind();
        return true;
    }

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *          array(
     *              array(
     *                          array('fieldName','comparator','fieldValue'),
     *                  **AND**
     *                          array('fieldName','comparator','fieldValue'),
     *                  ...
     *              ),
     *              **OR**
     *              array(
     *                          array('fieldName','comparator','fieldValue'),
     *                  **AND**
     *                          array('fieldName','comparator','fieldValue'),
     *                  ...
     *              ),
     *              ...
     *          )
     *
     * @return  array   $collection Subset of the current collection as instance of own concrete
     *                              collection class
     */
    public function getByCriteriaAsArray(array $criteria)
    {
        return $this->_getByCriteriaAsArray($criteria);
    }

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *          array(
     *              array(
     *                          array('fieldName','comparator','fieldValue'),
     *                  **AND**
     *                          array('fieldName','comparator','fieldValue'),
     *                  ...
     *              ),
     *              **OR**
     *              array(
     *                          array('fieldName','comparator','fieldValue'),
     *                  **AND**
     *                          array('fieldName','comparator','fieldValue'),
     *                  ...
     *              ),
     *              ...
     *          )
     *
     * @return  array   $collection Subset of the current collection as instance of own concrete
     *                              collection class
     */
    protected function _getByCriteriaAsArray(array $criteria)
    {
        return $this->_getByCriteria($criteria)->getAllAsArray();
    }

    /**
     * @return static
     */
    public function getEmptyCollection()
    {
        return new static($this->config, $this->criteriaValidationService);
    }

    /**
     * @param array $arr
     *
     * @return genericCollectionTrait
     * @throws \DomainException
     */
    public function mapArrayToNewCollection(array $arr)
    {
        $newCollection = $this->getEmptyCollection();
        foreach ($arr as $instanceRow) {
            $newInstance = $this->getNullObject();
            if (!$newInstance->isArrayPartiallyMappable($instanceRow)) {
                throw new \DomainException(
                    'At least one entry in the array cannot be mapped to an object of the given instance.'
                );
            }
            $newInstance->mapArray($instanceRow);
            $newCollection->add($newInstance, true);
        }
        return $newCollection;
    }

    public function hasID($id) :bool
    {
        return array_key_exists($id,$this->hashesByID);
    }

    public function containsInstance($instance) :bool
    {
        return array_key_exists(spl_object_hash($instance),$this->hashObjectMap);
    }

    /**
     * @param array $fields
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getFieldData(array $fields)
    {
        $validFieldsArr = [];
        foreach ($fields as $index => $fieldName) {
            if (!is_string($fieldName)) {
                throw new \InvalidArgumentException(
                    "Only strings are allowed as fieldsNames. Array-index $index is not a string."
                );
            }
            if ($this->config->fieldExists($fieldName)) $validFieldsArr[] = $fieldName;
        }
        $dataArr = [];
        $i = 0;
        //for ($this->elements->rewind(); $this->elements->valid(); $this->elements->next()) {
        //    $el = $this->elements->current();
        foreach ($this->hashObjectMap as $el) {
            foreach ($validFieldsArr as $validFieldName) {
                $dataArr[$i][$validFieldName] = $el->{$validFieldName};
            }
            ++$i;
        }
        return $dataArr;
    }

    public function rewind()
    {
        if($this->count() > 0) {
            $this->currIndex = array_keys($this->hashObjectMap)[0];
            $this->currIndexPos = 0;
        } else {
            $this->currIndex = '';
            $this->currIndexPos = null;
        }
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->currIndex,$this->hashObjectMap) && !empty($this->hashObjectMap[$this->currIndex]);
    }


    /**
     * @return mixed
     */
    public function current()
    {
        return $this->hashObjectMap[$this->currIndex];
    }

    public function next()
    {
        if ($this->count() > 0) {
            $this->currIndexPos++;
            if ($this->currIndexPos > (count($this->hashList) - 1)) {
                $this->currIndexPos = null;
                $this->currIndex = '';
            } else {
                $this->currIndex = $this->hashList[$this->currIndexPos];
            }
        }
    }

    /**
     * @return mixed|object
     */
    public function getFirst()
    {
        $this->rewind();
        if (!empty($this->hashObjectMap)) {
            return $this->hashObjectMap[$this->hashList[0]];
        }
        return $this->getNullObject();
        $this->elements->rewind();
        return $this->elements->valid() ? $this->elements->current() : $this->getNullObject();
    }

    /**
     * @return mixed|object
     */
    public function getNext()
    {
        $this->next();
        return $this->valid() ? $this->current() : $this->getNullObject();
        $this->elements->next();
        return $this->elements->valid() ? $this->elements->current() : $this->getNullObject();
    }

    /**
     * @return bool
     */
    public function isCurrValid()
    {
        return $this->valid();
        return $this->elements->valid();
    }

    /**
     * @param $instance
     */
    private function addNewInstance($instance)
    {
        $initEmpty = (0 === count($this->hashObjectMap));
        $info = $this->getIndexInfo($instance);
        if (!isset($info['id']) || $info['id'] === 0 || $info['id'] === '0') {
            throw new \InvalidArgumentException('Cannot update by id if instance has no non-null, non-zero id-value');
        }
        if (array_key_exists((string)$info['id'], $this->hashesByID)) {
            throw new \InvalidArgumentException(
                'Cannot add. Instance with id ' . $info['id'] . ' already exists in collection.'
            );
        }
        $hash = (string)$info['hash'];
        $hashedAltPrimary = (string)$info['hashed_alt_primary'];
        $this->hashObjectMap[$hash] = $instance;
        $this->hashesByID[(string)$info['id']] = $info['hash'];
        if ($info['hashed_alt_primary'] !== '') {
            $this->hashesByAltPrimary[$hashedAltPrimary] = $info['hash'];
        }
        $this->hashList[] = $info['hash'];
        if ($initEmpty) {
            $this->currIndexPos = 0;
            $this->currIndex = $info['hash'];
        }

    }

    /**
     * @param $instance
     */
    private function updateByID($instance)
    {
        $info = $this->getIndexInfo($instance);
        if (!isset($info['id']) || $info['id'] === 0 || $info['id'] === '0') {
            throw new \InvalidArgumentException('Cannot update by id if instance has no non-null, non-zero id-value');
        }
        if (!array_key_exists((string)$info['id'], $this->hashesByID)) {
            throw new \InvalidArgumentException(
                'Cannot update by id. No instance with id ' . $info['id'] . ' in collection.'
            );
        }
        $oldHash = $this->hashesByID[(string)$info['id']];
        $newHash = (string)$info['hash'];
        $hashedAltPrimary = (string)$info['hashed_alt_primary'];
        if($oldHash !== $newHash) {
            $pos = array_search($oldHash,$this->hashList,true);
            $this->hashList[$pos] = $newHash;
            if ($pos === $this->currIndexPos) {
                $this->currIndex = $newHash;
            }
            unset($this->hashObjectMap[$oldHash]);
        }
        $this->hashObjectMap[$newHash] = $instance;
        $this->hashesByID[(string)$info['id']] = $info['hash'];
        if ($hashedAltPrimary !== '') {
            $this->hashesByAltPrimary[$hashedAltPrimary] = $info['hash'];
        }
    }

    /**
     * @param $id
     */
    private function removeByID($id)
    {
        if (!array_key_exists((string)$id, $this->hashesByID)) {
            throw new \InvalidArgumentException('Cannot remove by id. No instance with id ' . $id . ' in collection.');
        }
        $instance = $this->hashObjectMap[$this->hashesByID[$id]];
        $this->removeInstance($instance);
    }

    /**
     * @param array $altPrimary
     */
    private function removeByAltPrimary(array $altPrimary)
    {
        if (!$this->hasInstanceForAltPrimary($altPrimary)) {
            throw new \InvalidArgumentException(
                'Cannot remove instance. No instance with given alt_primary in collection.'
            );
        }
        $instance = $this->getInstanceByAltPrimary($altPrimary);
        $this->removeInstance($instance);
    }


    /**
     * @param $instance
     */
    private function removeInstance($instance)
    {
        $info = $this->getIndexInfo($instance);
        unset($this->hashObjectMap[(string)$info['hash']], $this->hashesByID[(string)$info['id']], $this->hashesByAltPrimary[(string)$info['hashed_alt_primary']]);
        $hashListPos = array_search((string)$info['hash'],$this->hashList,true);
        unset($this->hashList[$hashListPos]);
        $this->hashList = array_values($this->hashList);
        if ($this->currIndexPos >= $hashListPos) {
            $this->currIndexPos--;
            if ($this->currIndexPos < 0) {
                $this->currIndexPos = 0;
            }
        }
        if (array_key_exists($this->currIndexPos,$this->hashList) && !empty($this->hashList[$this->currIndexPos])) {
            $this->currIndex = $this->hashList[$this->currIndexPos];
        } else {
            $this->currIndex = '';
            $this->currIndexPos = null;
        }

    }

    /**
     * @param $id
     * @return bool
     */
    private function hasInstanceForID($id)
    {
        return array_key_exists((string)$id, $this->hashesByID);
    }

    /**
     * @param array $altPrimary
     * @return bool
     */
    private function hasInstanceForAltPrimary(array $altPrimary)
    {
        $altPrimaryHash = $this->hashAltPrimary($altPrimary);
        return array_key_exists($altPrimaryHash, $this->hashesByAltPrimary);
    }

    /**
     * @param $instance
     * @return bool
     */
    private function hasInstance($instance)
    {
        $info = $this->getIndexInfo($instance);
        return array_key_exists($info['hash'], $this->hashObjectMap);
    }

    /**
     * @param $id
     * @return mixed
     */
    private function getInstanceByID($id)
    {
        if (!$this->hasInstanceForID($id)) {
            throw new \InvalidArgumentException('Cannot get Instance. No instance with id ' . $id . ' in collection.');
        }
        return $this->hashObjectMap[$this->hashesByID[(string)$id]];
    }

    /**
     * @param array $altPrimary
     * @return mixed
     */
    private function getInstanceByAltPrimary(array $altPrimary)
    {
        if (!$this->hasInstanceForAltPrimary($altPrimary)) {
            throw new \InvalidArgumentException(
                'Cannot get Instance. No instance with given alt_primary in collection.'
            );
        }
        $hashedAltPrimary = $this->hashAltPrimary($altPrimary);
        return $this->hashObjectMap[$this->hashesByAltPrimary[$hashedAltPrimary]];
    }

    /**
     * @param $instance
     * @return array
     */
    private function getIndexInfo($instance)
    {
        $hash = spl_object_hash($instance);
        $id = null;
        $altPrimary = [];
        $hashedAltPrimary = '';
        if (property_exists($instance, 'id')) {
            $idRefl = new \ReflectionProperty($instance, 'id');
            $idRefl->setAccessible(true);
            $id = $idRefl->getValue($instance);
        }
        if (null === $id) {
            $id = $hash;
        }
        if (is_array($this->altPrimary)) {
            foreach ($this->altPrimary as $fieldName) {
                if (property_exists($instance, $fieldName)) {
                    $propRefl = new \ReflectionProperty($instance, $fieldName);
                    $propRefl->setAccessible(true);
                    $altPrimary[$fieldName] = $propRefl->getValue($instance);
                }
            }
            $hashedAltPrimary = $this->hashAltPrimary($altPrimary);
        }
        $info = ['hash' => $hash, 'id' => $id, 'alt_primary' => $altPrimary, 'hashed_alt_primary' => $hashedAltPrimary];
        return $info;
    }

    /**
     * @param array $altPrimary
     * @return string
     */
    private function hashAltPrimary(array $altPrimary)
    {
        return md5(json_encode($altPrimary));
    }

}