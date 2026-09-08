<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use mysqli_result;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.01.2015
 * Time: 23:39
 */

class MySQLiQueryWrapper implements GenericDBQueryWrapperInterface {

    protected $hostName;
    protected $portNumber;
    protected $dbName;
    protected $userName;
    protected $password;
    protected $optionsArray;

    protected $connectionObject;

    protected $query = '';
    protected $stmt;
    protected $result;

    protected $errorState = FALSE;
    protected $errorMessage = '';


    /**
     * @param string $hostName
     * @param int    $portNumber
     * @param string $dbName
     * @param string $userName
     * @param string $password
     * @param array  $optionsArray
     */
    public function __construct( $hostName, $portNumber, $dbName, $userName, $password, $optionsArray = array()  ) {
        $this->hostName = ($hostName ?: 'localhost');
        $this->portNumber = ($portNumber ? (int)$portNumber : 3306);
        $this->dbName = ($dbName ?:'dcshop');
        $this->userName = ($userName ?: 'root');
        $this->password = $password;
        $this->optionsArray = (is_array($optionsArray) ? $optionsArray : array());

        $this->connectionObject = mysqli_init();

        if(count($this->optionsArray) > 0) {
            foreach($this->optionsArray as $optionPair) {
                if(!is_array($optionPair) || count($optionPair) !== 2) {
                    $this->errorState = TRUE;
                    $this->errorMessage .= 'At least one entry in the options array is not an array with two entries.';
                } else {
                    if(!$this->connectionObject->options($optionPair[0],$optionPair[1])) {
                        $this->errorState = TRUE;
                        $this->errorMessage .= $this->connectionObject->error . ' || ';
                    }
                }
            }
        }

        if(!($this->connectionObject->real_connect($this->hostName,$this->userName,$this->password,$this->dbName,$this->portNumber))) {
            $this->errorState = TRUE;
            $this->errorMessage .= $this->connectionObject->error . ' || ';
        }

        if (!($this->connectionObject->ping())) {
            $this->errorState   = TRUE;
            $this->errorMessage .= $this->connectionObject->connect_error;
        }

        $this->connectionObject->set_charset('utf8');

    }

    /**
     * @param string $hostName
     */
    public function setHostName( $hostName ) {
        $this->hostName = $hostName;
    }

    /**
     * @param int $portNumber
     */
    public function setPortNumber( $portNumber ) {
        $this->portNumber = $portNumber;
    }

    /**
     * @param string $dbName
     */
    public function setDbName( $dbName ) {
        $this->dbName = $dbName;
    }

    /**
     * @param string $userName
     */
    public function setUserName( $userName ) {
        $this->userName = $userName;
    }

    /**
     * @param string $password
     */
    public function setPassword( $password ) {
        $this->password = $password;
    }

    /**
     * @param array $optionsArray
     */
    public function setOptionsArray( array $optionsArray ) {
        $this->optionsArray = $optionsArray;
    }

    /**
     * @param string $query
     */
    public function setQuery( $query ) {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getHostName() {
        return $this->hostName;
    }

    /**
     * @return int
     */
    public function getPortNumber() {
        return $this->portNumber;
    }

    /**
     * @return string
     */
    public function getDbName() {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * @return array
     */
    public function getOptionsArray() {
        return $this->optionsArray;
    }

    /**
     * @return mixed
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @return boolean
     */
    public function isErrorState() {
        return $this->errorState;
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function reconnect() {

        if(count($this->optionsArray) > 0) {
            foreach($this->optionsArray as $optionPair) {
                if(!is_array($optionPair) || count($optionPair) !== 2) {
                    $this->errorState = TRUE;
                    $this->errorMessage .= 'At least one entry in the options array is not an array with two entries. || ';
                } else {
                    if(!$this->connectionObject->options($optionPair[0],$optionPair[1])) {
                        $this->errorState = TRUE;
                        $this->errorMessage .= $this->connectionObject->error . ' || ';
                    }
                }
            }
        }

        if(!($this->connectionObject->real_connect($this->hostName,$this->userName,$this->password,$this->dbName,$this->portNumber))) {
            $this->errorState = TRUE;
            $this->errorMessage .= $this->connectionObject->error . ' || ';
        }

        if (!($this->connectionObject->ping())) {
            $this->errorState   = TRUE;
            $this->errorMessage .= $this->connectionObject->connect_error;
        }

        if($this->errorState) {
            $this->query = '';
            $this->stmt = NULL;
            $this->result = NULL;
            return FALSE;
        }

        $this->connectionObject->set_charset('utf8');

        $this->query        = '';
        $this->stmt         = NULL;
        $this->result       = NULL;
        $this->errorState   = FALSE;
        $this->errorMessage = '';
        return TRUE;
    }

    /**
     * @return bool
     */
    public function doQuery() {

        if(!$this->connectionObject->ping()) {
            throw new \Exception($this->connectionObject->connect_error);
            return FALSE;
        }
        if(empty($this->query)) {
            throw new \Exception('Query is not set');
            return FALSE;
        }
        $result = $this->connectionObject->query($this->query);
        if($result === TRUE) {
            return TRUE;
        }
        if(!($result instanceof mysqli_result)) {
            $this->stmt = NULL;
            $this->result = NULL;
            $this->errorState = TRUE;
            $this->errorMessage .= $this->connectionObject->error . ' || ';
            return FALSE;
        }

        $this->result = $result;
        return TRUE;

    }

    /**
     * @return bool
     */
    public function prepareQuery() {
        if(empty($this->query)) {
            throw new \Exception('Query is not set.');
            return FALSE;
        }

        if(!($stmt = $this->connectionObject->prepare($this->query))) {
            $this->stmt = NULL;
            $this->result = NULL;
            return FALSE;
        }

        $this->stmt = $stmt;
        return TRUE;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    public function bindParameters( array $array ) {
        // TODO: Implement bindParameters() method.
    }

    /**
     * @return bool
     */
    public function executePreparedStatement() {
        // TODO: Implement executePreparedStatement() method.
    }


    /**
     * @param array $params
     *
     * @return bool
     */
    public function bindResult( array $params ) {
        // TODO: Implement bindReturnValues() method.
    }


    /**
     * @return bool
     */
    public function startTransaction() {
        // TODO: Implement startTransaction() method.
    }

    /**
     * @return bool
     */
    public function commitTransaction() {
        // TODO: Implement commitTransaction() method.
    }

    /**
     * @return bool
     */
    public function cancelTransaction() {
        // TODO: Implement cancelTransaction() method.
    }

    /**
     * @return int
     */
    public function getLastInsertId() {
        // TODO: Implement getLastInsertID() method.
    }

    /**
     * @return int
     */
    public function getNoOfAffectedRows() {
        // TODO: Implement getNoOfAffectedRows() method.
    }

    /**
     * @return int
     */
    public function getNoOfReturnedRows() {
        // TODO: Implement getNoOfReturnedRows() method.
    }

    /**
     * @return Resource
     */
    public function getResultResource() {
        // TODO: Implement getResultResource() method.
    }

    /**
     * @return array
     */
    public function getResultArray() {
        // TODO: Implement getResultArray() method.
    }

    /**
     * @return array
     */
    public function getNextRow() {
        // TODO: Implement getNextRow() method.
    }

    public function rollbackTransaction()
    {
        // TODO: Implement rollbackTransaction() method.
    }

    /**
     * @param $string
     */
    public function escapeString($string)
    {
        // TODO: Implement escapeString() method.
    }

    public function closeStatement()
    {
        // TODO: Implement closeStatement() method.
    }

    public function inTransaction()
    {
        // TODO: Implement inTransaction() method.
    }


}