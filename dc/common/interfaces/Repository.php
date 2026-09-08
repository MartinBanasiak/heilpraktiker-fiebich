<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 03:24
 */
interface Repository {

    /**
     * @param   mixed $instance An instance of the object in the repository
     *
     * @param $idcheck
     * @return bool Success or failure
     */
    public function add( $instance, $idcheck );

    /**
     * @param   mixed $instance An instance of the object in the repository
     *
     * @return  bool                Success or failure
     */
    public function remove( $instance );

    /**
     * @param   int $id
     *
     * @return  mixed   An instance of the object in the repository or null-object thereof
     */
    public function findByID( $id );

    /**
     * @param   array $uniqueKeyArray
     *
     * @return  mixed   An instance of the object in the repository or null-object thereof
     */
    public function findByAltPrimary( array $uniqueKeyArray );


    /**
     * @param array $criteriaArray
     * @param int $offset
     * @param int $limit
     * @return  GenericCollectionInterface An instance of a collection-class for the object in the repository
     */

    public function findByCriteria( array $criteriaArray, $offset = 0, $limit = 0 );

    /**
     * @return  bool    Success or Failure
     */
    public function updateAll();

    /**
     * @param   GenericDBModelInterface $instance
     *
     * @return  bool                                Success or Failure
     */
    public function updateSingle( GenericDBModelInterface $instance );


    /**
     * @param   GenericDBModelInterface $instance
     *
     * @return  int                                 The DB-ID of the Record for the Shipment
     */
    public function createInDB( GenericDBModelInterface $instance );

    /**
     * @return  mixed   An instance of a null-object of the class in the collection
     */
    public function getNullObject();

    /**
     * @return mixed    The config-object for the collection-entry class of the repository
     */
    public function getConfig();

    /**
     * @return string   The classname of the object for which the repository handles DB-access
     */
    public function getObjectClass();

    /**
     * @param array $altPrimaryArr
     * @return mixed
     */
    public function getFindByAltPrimaryQueryAndValueMappingArray( array $altPrimaryArr );

    /**
     * @param array $criteria
     * @return mixed
     */
    public function getFindByCriteriaQuery( array $criteria );

    /**
     * @param GenericDBModelInterface $instance
     * @return mixed
     */
    public function getUpdateSingleQuery( GenericDBModelInterface $instance );

    /**
     * @param GenericDBModelInterface $instance
     * @return mixed
     */
    public function getCreateInDBQuery( GenericDBModelInterface $instance );

    /**
     * @param $id
     * @return mixed
     */
    public function lockRecordByID($id );

    /**
     * @param $id
     * @return mixed
     */
    public function releaseRecordLock($id );

    /**
     * @param $id
     * @return mixed
     */
    public function isRecordLocked($id );

    /**
     * @param GenericDBModelInterface $instance
     * @return mixed
     */
    public function deleteByID(GenericDBModelInterface $instance );

    /**
     * @param GenericDBModelInterface $instance
     * @return mixed
     */
    public function getDeleteByIDQuery(GenericDBModelInterface $instance );

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function removeEntityFromCache(GenericDBModelInterface $entity );

    /**
     * @param $id
     * @return mixed
     */
    public function recacheEntityFromDBByID($id );

    /**
     * @param $query
     * @return mixed
     */
    public function getAllByQueryAsArray($query);

    /**
     * @param $query
     * @return mixed
     */
    public function getAllByQueryAsCollection($query);

}