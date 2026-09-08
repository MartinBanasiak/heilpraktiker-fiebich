<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.01.2015
 * Time: 23:18
 */

interface GenericDBQueryWrapperInterface {

    /**
     * @param string $hostName
     * @param int    $portNumber
     * @param string $dbName
     * @param string $userName
     * @param string $password
     * @param array  $optionsArray
     */
    public function __construct( $hostName, $portNumber, $dbName, $userName, $password, array $optionsArray = array());

    /**
     * @param $hostName
     */
    public function setHostName( $hostName );

    /**
     * @return string
     */
    public function getHostName();

    /**
     * @param $portNumber
     */
    public function setPortNumber( $portNumber );

    /**
     * @return int
     */
    public function getPortNumber();

    /**
     * @param $dbName
     */
    public function setDBName( $dbName );

    /**
     * @return int
     */
    public function getDBName();

    /**
     * @param $userName
     */
    public function setUserName( $userName );

    /**
     * @return string
     */
    public function getUserName();

    /**
     * @param $password
     */
    public function setPassword( $password );

    /**
     * @param array $optionsArray
     */
    public function setOptionsArray( array $optionsArray );

    /**
     * @return array
     */
    public function getOptionsArray();

    /**
     * @return bool
     */
    public function reconnect();

    /**
     * @param string $query
     * @return GenericDBQueryWrapperInterface
     */
    public function setQuery( $query );

    /**
     * @return string
     */
    public function getQuery();

    /**
     * @return bool
     */
    public function doQuery();

    /**
     * @return bool
     */
    public function prepareQuery();

    /**
     * @param array $array
     *
     * @return bool
     */
    public function bindParameters( array $array );

    /**
     * @return bool
     */
    public function executePreparedStatement();


    /**
     * @param array $params
     *
     * @return bool
     */
    public function bindResult( array $params );


    /**
     * @return bool
     */
    public function startTransaction();

    /**
     * @return bool
     */
    public function commitTransaction();

    /**
     * @return bool
     */
    public function rollbackTransaction();

    /**
     * @return bool
     */
    public function isErrorState();

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @return int
     */
    public function getLastInsertId();

    /**
     * @return int
     */
    public function getNoOfAffectedRows();

    /**
     * @return int
     */
    public function getNoOfReturnedRows();

    /**
     * @return array
     */
    public function getResultArray();

    /**
     * @return array
     */
    public function getNextRow();

    /**
     * @param string $string
     *
     * @return string|bool
     */
    public function escapeString($string);

    /**
     * @return bool
     */
    public function closeStatement();

    public function inTransaction();

    public function getConnectionObject();

}