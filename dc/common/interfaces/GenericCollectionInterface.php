<?php

namespace DynCom\dc\common\interfaces;
use Countable;
use IteratorAggregate;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 03:46
 */
interface GenericCollectionInterface extends IteratorAggregate, Countable
{

    /**
     * @return GenericCollectionInterface
     */
    public function getEmptyCollection();

    /**
     * @return  string  class-name for collection entities
     */
    public function getEntryClassName();

    /**
     * @return  array    Array with entity-fields (db-mapped fields)
     */
    public function getFieldsArray();

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool                    Success or failure
     */
    public function add($instance, $idCheck);

    /**
     * @param   mixed $instance An instance of the collection-entry class
     * @param   bool $idCheck Flag whether comparisons should be id-only, default FALSE
     *
     * @return  bool                        Success or failure
     */
    public function remove($instance, $idCheck);

    /**
     * @param   int $id
     *
     * @return  mixed   An instance of the collection-entry class
     */
    public function getByID($id);

    /**
     * @param   array $uniqueKeyArray An array specifying an alternative primary key, of the form:
     *                                array('keyField1Name'=>'keyField1Value','keyField2Name'=>'keyField2Value'...);
     *
     * @return  mixed                   An instance of the collection-entry class
     */
    public function getByAltPrimary(array $uniqueKeyArray);


    /**
     * @return  array   The entire collection as array
     */
    public function getAllAsArray();

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *                                      array(
     *                                          array(
     *                                              array('fieldName','comparator','fieldValue'),
     *                                              **AND**
     *                                              array('andFieldName','andComparator','andFieldValue')
     *                                              ...
     *                                          ),
     *                                          **OR**
     *                                          array(
     *                                              array('orFieldName','orComparator','orFieldValue'),
     *                                              **AND**
     *                                              array('andOrFieldName','andOrComparator','andOrFieldValue')
     *                                              ...
     *                                          )
     *                                          ...
     *                                      )
     *
     * @return  GenericCollectionInterface  An instance of a collection-class for the object in the repository
     */
    public function getByCriteria(array $criteria);

    /**
     * @param   array $criteria Filtering-criteria as array in disjunctive normal form:
     *                              array(
     *                                  array(
     *                                      array('fieldName','comparator','fieldValue'),
     *                                      **AND**
     *                                      array('andFieldName','andComparator','andFieldValue')
     *                                      ...
     *                                  ),
     *                                  **OR**
     *                                  array(
     *                                      array('orFieldName','orComparator','orFieldValue'),
     *                                      **AND**
     *                                      array('andOrFieldName','andOrComparator','andOrFieldValue')
     *                                      ...
     *                                  )
     *                                  ...
     *                              )
     *
     * @return  array   $collection Subset of the current collection as instance of own concrete
     *                              collection class
     */
    public function getByCriteriaAsArray(array $criteria);

    /**
     * @return string
     */
    public function getBaseTableName();

    /**
     * @return mixed
     */
    public function getNullObject();

    /**
     * @param array $arr
     *
     * @return GenericCollectionInterface
     */
    public function mapArrayToNewCollection(array $arr);

    /**
     * @param array $fields
     * @return array
     */
    public function getFieldData(array $fields);

    /**
     * @return mixed
     */
    public function getFirst();

    /**
     * @return mixed
     */
    public function rewind();

    /**
     * @return mixed
     */
    public function valid();

    /**
     * @return mixed
     */
    public function current();

    /**
     * @return mixed
     */
    public function next();

    /**
     * @param $id
     * @return mixed bool
     */
    public function hasID($id);


}