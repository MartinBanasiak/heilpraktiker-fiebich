<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use PDO;
use PDOException;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.01.2015
 * Time: 23:38
 */
class PDOQueryWrapper implements GenericDBQueryWrapperInterface
{

    protected $hostName = 'localhost';
    protected $portNumber = '3306';
    protected $dbName = 'dcshop';
    protected $userName = 'root';
    protected $password = '';
    protected $optionsArray = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
    protected $dsn;

    protected $allowedComparisonOperators = array('IS NULL', 'IS NOT NULL', '=', '!=', '<', '>', '>=', '<=', 'IN', 'LIKE');
    protected $comparisonValidation;

    protected $unfinishedQuery = [];
    protected $unfinishedQueryGroups = array();
    protected $currQueryGroup;

    protected $connectionObject;

    protected $query = '';
    /**
     * @var \PDOStatement
     */
    protected $stmt;

    /**
     * @return mixed
     */
    public function getStmt()
    {
        return $this->stmt;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    protected $result;
    protected $resultIndex = 0;

    protected $errorState = TRUE;
    protected $errorMessage = 'No connection';
    protected $errorCode = '-1';


    /**
     * @param string $hostName
     * @param int $portNumber
     * @param string $dbName
     * @param string $userName
     * @param string $password
     * @param array $optionsArray
     */
    public function __construct($hostName, $portNumber, $dbName, $userName, $password, array $optionsArray = NULL)
    {

        $this->hostName = ($hostName ? (string)$hostName : 'localhost');
        $this->portNumber = ($portNumber ? (int)$portNumber : 3306);
        $this->dbName = ($dbName ? (string)$dbName : 'dcshop');
        $this->userName = ($userName ? (string)$userName : 'root');
        $this->password = ($password ? (string)$password : '123');
        $this->optionsArray = ((is_array($optionsArray) && count($optionsArray) > 0) ? $optionsArray : array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

        $this->dsn = 'mysql:dbname=' . $this->dbName . ';host=' . $this->hostName . ';port=' . (string)$this->portNumber . ';charset=utf8mb4';

        $pdo = new \PDO($this->dsn, $this->userName, $this->password, $this->optionsArray);

        if (!$pdo->exec('SET NAMES utf8mb4') || !$pdo->exec('SET CHARACTER SET utf8mb4')) {
            $pdoErrorInfo = $pdo->errorInfo();
            $this->errorState = TRUE;
            $this->errorMessage = print_r($pdoErrorInfo, 1);
        }

        $this->connectionObject = $pdo;
        $this->errorState = FALSE;
        $this->errorMessage = '';

        $this->_setComparisonValidationArray();

    }

    /**
     * @param $hostName
     * @return $this
     */
    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
        return $this;
    }

    /**
     * @param $portNumber
     * @return $this
     */
    public function setPortNumber($portNumber)
    {
        $this->portNumber = $portNumber;
        return $this;
    }

    /**
     * @param $dbName
     * @return $this
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
        return $this;
    }

    /**
     * @param $userName
     * @return $this
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param array $optionsArray
     * @return $this
     */
    public function setOptionsArray(array $optionsArray)
    {
        $this->optionsArray = $optionsArray;
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * @return int
     */
    public function getPortNumber()
    {
        return $this->portNumber;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return array
     */
    public function getOptionsArray()
    {
        return $this->optionsArray;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return boolean
     */
    public function isErrorState()
    {
        return $this->errorState;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function reconnect()
    {

        $pdo = new \PDO($this->dsn, $this->userName, $this->password, $this->optionsArray);

        if (!$pdo->exec('SET NAMES utf8mb4') || !$pdo->exec('SET CHARACTER SET utf8mb4')) {
            $pdoErrorInfo = $pdo->errorInfo();
            $this->query = '';
            $this->stmt = NULL;
            $this->result = NULL;
            $this->errorState = TRUE;
            $this->errorMessage = print_r($pdoErrorInfo, 1) . ' || ';
            return FALSE;
        }

        $this->connectionObject = $pdo;

        $this->errorState = FALSE;
        $this->errorMessage = '';

        $this->query = '';
        $this->stmt = NULL;
        $this->result = NULL;
        $this->errorState = FALSE;
        $this->errorMessage = '';
        return TRUE;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function doQuery()
    {

        if (!($this->connectionObject instanceof \PDO)) {
            throw new \ErrorException('No PDO Connection-object set');
        }

        if (empty($this->query)) {
            throw new \ErrorException('Query is not set');
        }


        //Check env for logging directives
        $logQueries	=	(bool)getenv('LOG_QUERIES');
        $tableFilters	=	(string)getenv('QUERY_LOG_TABLES');
        $methodFilters	=	(string)getenv('QUERY_LOG_METHODS');

        $tablesToLog = explode(',',$tableFilters);
        $methodsToLog = explode(',',$methodFilters);

        if (count($tablesToLog) > 0) {
            $logTable = false;
            foreach($tablesToLog as $tableName) {

                if ($tableName && strpos($this->query,$tableName) !== false) {
                    $logTable = true;
                }
            }
        } else {
            $logTable = true;
        }

        if (count($methodsToLog) > 0) {
            $logMethod = false;
            foreach($methodsToLog as $methodName) {
                if ($methodName && strpos($this->query,$methodName) !== false) {
                    $logMethod = true;
                }
            }
        } else {
            $logMethod = true;
        }

        $logQuery = $logQueries && $logTable && $logMethod;
        if ($logQuery) {
            $filePath = rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . '/logs/query_log.txt';
            $newContent = time() . ' - QUERY: ' . $this->query . PHP_EOL . generateCallTrace() . PHP_EOL . PHP_EOL;
            file_put_contents($filePath,$newContent,FILE_APPEND);
        }

        try {
            $pdoStatement = $this->connectionObject->query($this->query);
        } catch (PDOException $e) {
            $this->stmt = NULL;
            $this->result = NULL;
            $this->errorState = TRUE;
            //echo "... QUERY ERROR!!! ...QUERY : $this->query";
            $errorInfo = print_r($this->connectionObject->errorInfo(), 1);
            $this->errorMessage .= $errorInfo . ' || ';
            return FALSE;
        }

        $this->result = NULL;
        $this->stmt = $pdoStatement;
        $this->unfinishedQuery = [];
        $this->errorMessage = '';
        $this->errorState = false;
        $this->errorCode = '0';
        return TRUE;

    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function prepareQuery()
    {

        if (empty($this->query)) {
            throw new \ErrorException('Query is not set.');
        }
        //FOR VALIDATION of calls, uncomment this:
        // $this->connectionObject = new \PDO($this->dsn,$this->userName,$this->password,$this->optionsArray);
        if (!($stmt = $this->connectionObject->prepare($this->query))) {
            $this->stmt = NULL;
            $this->result = NULL;
            return FALSE;
        }

        $this->result = NULL;
        $this->stmt = $stmt;
        return TRUE;
    }

    /**
     * @param array $array Parameter-Value-Type-Array
     *                      array(
     *                          array(ParameterName1,ParameterValue1,ParameterType1),
     *                          array(ParameterName2,ParameterValue2,ParameterType2)
     *                          ...
     *                      )
     *
     * @return bool
     * @throws \ErrorException
     */
    public function bindParameters(array $array)
    {

        if(!($this->stmt instanceof \PDOStatement)) {
            throw new \ErrorException('No Statement is set.');
        }
        if ($this->stmt->errorCode() > 0) {
            throw new \ErrorException('Statement is in error. ' . print_r($this->stmt->errorInfo(),1));
        }

        $nameTestPattern = '/^(?:\:[a-zA-Z0-9_\-]+)|(?:[0-9]+)$/';
        foreach ($array as $singleParameterArray) {
            if (!is_array($singleParameterArray) || (count($singleParameterArray) !== 3)) {
                throw new \ErrorException('At least one entry in the paramter-array does not contain an array of count 3
                the required format [colon]parameterName');
            }
            $paramName = (string)$singleParameterArray[0];
            $paramValue = $singleParameterArray[1];
            $paramType = $singleParameterArray[2];
            if (!preg_match($nameTestPattern, $paramName)) {
                throw new \ErrorException('At least one parameterName is neither a question-mark nor a string with pattern [colon]parameterName');
            }
            if (!is_int($paramType)) {
                throw new \ErrorException('At least one parameterType is not an integer');
            }
            if (is_numeric($paramName)) {
                $paramIndex = (int)$paramName;
                if (!$this->stmt->bindValue($paramIndex, $paramValue, $paramType)) {
                    throw new \ErrorException('Parameter \'' . $paramName . '\', Value \'' . $paramValue . '\', Type \'' . $paramType . ' could not be bound');
                }
            } else {
                if (!$this->stmt->bindValue($paramName, $paramValue, $paramType)) {
                    throw new \ErrorException('Parameter \'' . $paramName . '\', Value \'' . $paramValue . '\', Type \'' . $paramType . ' could not be bound');
                }
            }
        }
        return TRUE;
    }


    /**
     * @return bool
     * @throws \ErrorException
     */
    public function executePreparedStatement()
    {
        if (!$this->stmt instanceof \PDOStatement) {
            throw new \ErrorException('No prepared statement set');
        }
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->errorState = TRUE;
            $this->errorMessage .= $e->getMessage() . ' || ';
            return FALSE;
        }

        return TRUE;
    }


    /**
     * @param array $params
     * @return bool
     * @throws \ErrorException
     */
    public function bindResult(array $params)
    {

        if (!($this->stmt instanceof \PDOStatement) || ($this->stmt->errorCode() > 0)) {
            throw new \ErrorException('Statement is not prepared or in error');
        }

        $numberMapping = FALSE;
        foreach ($params as $key => $resultBinding) {
            if (!is_array($resultBinding)) {
                $numberMapping = TRUE;
                if (!$this->stmt->bindColumn($key, $resultBinding)) {
                    throw new \ErrorException('At least one entry in the numeric column-binding array could not be bound');
                }
            } elseif ($numberMapping) {
                throw new \ErrorException('Column-binding array is of mixed type. Array has to either contain sub-arrays with column-name - binding-reference structure or numeric array with only binding-reference');
            } else {
                if (count($resultBinding) !== 2) {
                    throw new \ErrorException('at least one sub-array of column-binding array does not have count 2');
                }
                if (!$this->stmt->bindColumn($resultBinding[0], $resultBinding[1])) {
                    throw new \ErrorException('At least one sub-array in column-binding array could not be bound');
                }
            }
        }
        return TRUE;

    }


    /**
     * @return bool
     */
    public function startTransaction()
    {

        if (!$this->connectionObject->beginTransaction()) {
            $this->errorState = TRUE;
            $this->errorMessage .= print_r($this->connectionObject->errorInfo, 1) . ' || ';
            return FALSE;
        }

        $this->stmt = NULL;
        $this->result = NULL;
        $this->errorState = FALSE;
        $this->errorMessage = '';
        return TRUE;
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {

        if (!$this->connectionObject->commit()) {
            $this->errorState = TRUE;
            $this->errorMessage .= print_r($this->connectionObject->errorInfo, 1) . ' || ';
            return FALSE;
        }

        $this->stmt = NULL;
        $this->result = NULL;
        $this->errorState = FALSE;
        $this->errorMessage = '';
        return TRUE;

    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {

        if (!$this->connectionObject->rollBack()) {
            $this->errorState = TRUE;
            $this->errorMessage .= print_r($this->connectionObject->errorInfo, 1) . ' || ';
            return FALSE;
        }

        $this->stmt = NULL;
        $this->result = NULL;
        $this->errorState = FALSE;
        $this->errorMessage = '';
        return TRUE;

    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->connectionObject->lastInsertId();
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function getNoOfAffectedRows()
    {

        if (!($this->stmt instanceof \PDOStatement) || ($this->stmt->errorCode() > 0)) {
            throw new \ErrorException('Statement is not prepared or in error');
        }

        return $this->stmt->rowCount();

    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function getNoOfReturnedRows()
    {

        if (is_array($this->result)) {
            return count($this->result);
        }

        if(!($this->stmt instanceof \PDOStatement)) {
            throw new \ErrorException('No Statement is set.');
        }
        if ($this->stmt->errorCode() > 0) {
            throw new \ErrorException('Statement is in error. ' . print_r($this->stmt->errorInfo(),1));
        }

        if ($result = $this->stmt->fetchAll(PDO::FETCH_BOTH)) {
            $this->result = $result;
            return count($this->result);
        }

        if ($this->stmt->closeCursor() && $this->stmt->execute() && ($result = $this->stmt->fetchAll(PDO::FETCH_BOTH))) {
            $this->result = $result;
            return count($this->result);
        }

        return -1;

    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getResultArray()
    {

        if (is_array($this->result) && (count($this->result) > 0)) {
            return $this->result;
        }

        if ($this->stmt instanceof \PDOStatement) {
            $result = $this->stmt->fetchAll(PDO::FETCH_BOTH);
            if (is_array($result)) {
                $this->result = $result;
                return $this->result;
            } else {
                $this->stmt->closeCursor();
                try {
                    $this->stmt->execute();
                    $result = $this->stmt->fetchAll();
                    if (count($result) > 0) {
                        $this->result = $result;
                        return $this->result;
                    }
                } catch (\Exception $e) {
                    throw new \ErrorException('Result was not already set, fetchAll on $this->stmt didn\'t return a rowset, and resetting cursor and trying fetchAll again did not work');
                }
            }
        }
        return array();
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getNextRow()
    {
        if (is_array($this->result) && is_array($this->result[$this->resultIndex])) {
            $returnValue = $this->result[$this->resultIndex];
            ++$this->resultIndex;
            return $returnValue;
        }
        if ($this->stmt instanceof \PDOStatement) {
            if ($result = $this->stmt->fetchAll()) {
                $this->result = $result;
                ++$this->resultIndex;
                return $this->result[0];
            }
            $this->stmt->closeCursor();
            try {
                $this->stmt->execute();
                $result = $this->stmt->fetchAll();
                if (is_array($result) && (count($result) > 0)) {
                    ++$this->resultIndex;
                    return $this->result[0];
                }
            } catch (\Exception $e) {
                throw new \ErrorException('Result was not already set, fetchAll on $this->stmt didn\'t return a rowset, and resetting cursor and trying fetchAll again did not work');
            }
        }
        return array();
    }

    /**
     * @param string $string
     *
     * @return bool|string
     */
    public function escapeString($string)
    {
        if (!($this->connectionObject instanceof \PDO)) {
            return FALSE;
        }

        return $this->connectionObject->quote($string);
    }

    /**
     * @return bool
     */
    public function closeStatement()
    {

        if (!($this->stmt instanceof \PDOStatement)) {
            return FALSE;
        }
        $this->stmt->closeCursor();
        return TRUE;

    }

    /**
     * @return \PDO
     */
    public function getConnectionObject()
    {
        return $this->connectionObject;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), array('connectionObject'));
    }

    public function __wakeup()
    {
        $pdo = new \PDO($this->dsn, $this->userName, $this->password, $this->optionsArray);

        if (!$pdo->exec('SET NAMES utf8mb4') || !$pdo->exec('SET CHARACTER SET utf8mb4')) {
            $pdoErrorInfo = $pdo->errorInfo();
            $this->errorState = TRUE;
            $this->errorMessage = print_r($pdoErrorInfo, 1);
        }

        $this->connectionObject = $pdo;
        $this->errorState = FALSE;
        $this->errorMessage = '';
    }

    /**
     * @param $select
     * @return $this
     */
    public function select($select)
    {
        if (array_key_exists('lastElement',$this->unfinishedQuery) && null !== $this->unfinishedQuery['lastElement']) {
            throw new \BadMethodCallException("'select' cannot be called if a query was already initialized." . print_r($this->unfinishedQuery,1));
        }
        if (!is_scalar($select) && !is_array($select)) {
            throw new \InvalidArgumentException('The value entered is not a valid select expression. Only arrays and simple strings are allowed.');
        }
        if (is_array($select)) {
            foreach ($select as $key => $selectExpression) {
                if (!is_scalar($selectExpression)) {
                    throw new \InvalidArgumentException("Array-entry $key is not a valid select expression");
                }
            }
        }
        $this->unfinishedQuery['select'] = $select;
        $this->unfinishedQuery['lastElement'] = 'select';
        $this->unfinishedQuery['currJoinName'] = null;
        $this->unfinishedQuery['join'] = [];
        return $this;
    }

    /**
     * @param $fromName
     * @param string $fromAlias
     * @return $this
     */
    public function from($fromName, $fromAlias = '')
    {
        if ($this->unfinishedQuery['lastElement'] !== 'select') {
            throw new \BadMethodCallException("'from' can only be called immediately after calling 'select'.");
        }
        if (!array_key_exists('select', $this->unfinishedQuery)) {
            throw new \BadMethodCallException("'from' can only be called after 'select' has been called successfully.");
        }
        if (!is_scalar($fromName) || strip_tags($fromName) !== (string)$fromName) {
            throw new \InvalidArgumentException('The given name is invalid.');
        }
        if (!is_scalar($fromAlias) || strip_tags($fromAlias) !== (string)$fromAlias) {
            throw new \InvalidArgumentException('The given alias is invalid.');
        }
        $this->unfinishedQuery['from'][] = array('fromName' => $fromName, 'fromAlias' => $fromAlias);
        $this->unfinishedQuery['lastElement'] = 'from';
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $joinType
     * @param $joinName
     * @param $joinAlias
     * @return $this
     */
    public function join($joinType, $joinName, $joinAlias)
    {
        if (!in_array($joinType, array('left', 'right', 'inner', 'straight'))) {
            throw new \InvalidArgumentException("'joinType' must be either 'left', 'right', 'inner' or 'straight");
        }
        if (!in_array($this->unfinishedQuery['lastElement'], array('from', 'join', 'on', 'andOn', 'orOn'))) {
            throw new \BadMethodCallException("'from' can only be called immediately after calling either 'from', 'join','on','andOn' or 'orOn'.");
        }
        if (!array_key_exists('select', $this->unfinishedQuery)) {
            throw new \BadMethodCallException("'from' can only be called after 'select' has been called successfully.");
        }
        if (!is_scalar($joinName) || strip_tags($joinName) !== (string)$joinName) {
            throw new \InvalidArgumentException('The given name is invalid.');
        }
        if (!is_scalar($joinAlias) || strip_tags($joinAlias) !== (string)$joinAlias) {
            throw new \InvalidArgumentException('The given alias is invalid.');
        }
        $arr = array('joinType' => $joinType, 'joinName' => $joinName, 'joinAlias' => $joinAlias);
        $this->unfinishedQuery['join'][$joinName] = $arr;
        $this->unfinishedQuery['currJoinName'] = $joinName;
        $this->unfinishedQuery['lastElement'] = 'join';
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return $this
     */
    public function on($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = false)
    {
        if (!isset($this->unfinishedQuery['currJoinName'])) {
            throw new \BadMethodCallException('There is no join currently open.');
        }
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('join', 'enterParen')))) {
            throw new \BadMethodCallException("'on' can only be called immediately after calling 'join'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . " is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'on';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['ungrouped'][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return $this
     */
    public function andOn($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = false)
    {
        if (!isset($this->unfinishedQuery['currJoinName'])) {
            throw new \BadMethodCallException('There is no join currently open.');
        }
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('on', 'andOn', 'orOn')))) {
            throw new \BadMethodCallException("'on' can only be called immediately after calling either 'on', 'andOn' or 'orOn'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'andOn';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['ungrouped'][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return $this
     */
    public function orOn($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = false)
    {
        if (!isset($this->unfinishedQuery['currJoinName'])) {
            throw new \BadMethodCallException('There is no join currently open.');
        }
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('on', 'andOn', 'orOn')))) {
            throw new \BadMethodCallException("'on' can only be called immediately after calling either 'on', 'andOn' or 'orOn'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'orOn';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['ungrouped'][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $currJoinName = $this->unfinishedQuery['currJoinName'];
            $currJoinRef = &$this->unfinishedQuery['join'][$currJoinName];
            $currJoinRef['on']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return PDOQueryWrapper
     */
    public function where($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = true)
    {
        /*if(isset($this->currQueryGroup)) {
            throw new \BadMethodCallException('A parenthesis needs to close before \'where\' can be called.');
        }*/
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('from', 'join', 'on', 'andOn', 'orOn', 'leaveParen', 'enterParen')))) {
            throw new \BadMethodCallException("'where' can only be called immediately after calling either 'from', 'join', 'on', 'andOn', 'orOn' or  'leaveParen'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'where';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['where']['ungrouped'][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $this->unfinishedQuery['where']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return PDOQueryWrapper
     */
    public function andWhere($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = true)
    {
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('where', 'andWhere', 'orWhere')))) {
            throw new \BadMethodCallException("'andWhere' can only be called immediately after calling either 'where', 'andWhere' or 'orWhere'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'andWhere';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['where']['ungrouped'][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $this->unfinishedQuery['where']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @param bool $fieldValueIsLiteral
     * @return PDOQueryWrapper
     */
    public function orWhere($fieldName, $operator, $fieldValue = null, $fieldValueIsLiteral = true)
    {
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('where', 'andWhere', 'orWhere')))) {
            throw new \BadMethodCallException("'orWhere' can only be called immediately after calling either 'where', 'andWhere', 'on', 'andOn' or 'orOn' .");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'orWhere';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['where']['ungrouped'][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        } else {
            $this->unfinishedQuery['where']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue, 'valueIsLiteral' => (bool)$fieldValueIsLiteral);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $expression
     * @return PDOQueryWrapper
     */
    public function groupBy($expression)
    {
        if (isset($this->currQueryGroup)) {
            throw new \BadMethodCallException('A parenthesis needs to close before \'groupBy\' can be called.');
        }
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('from', 'join', 'on', 'andOn', 'orOn', 'where', 'andWhere', 'orWhere', 'groupBy')))) {
            throw new \BadMethodCallException("'groupBy' can only be called immediately after calling either 'from', 'join', 'on', 'andOn', 'orOn', 'where', 'andWhere', 'orWhere' or 'groupBy' .");
        }
        if (!is_scalar($expression) || (strip_tags($expression) !== (string)$expression)) {
            throw new \InvalidArgumentException(strip_tags($expression) . ' is not a valid gorup-by expression.');
        }
        $this->unfinishedQuery['lastElement'] = 'groupBy';
        $this->unfinishedQuery['groupBy'][] = $expression;
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @return PDOQueryWrapper
     */
    public function having($fieldName, $operator, $fieldValue = null)
    {
        if (isset($this->currQueryGroup)) {
            throw new \BadMethodCallException('A parenthesis needs to close before \'having\' can be called.');
        }
        if (isset($this->currQueryGroup)) {
            throw new \BadMethodCallException('A parenthesis needs to close before \'having\' can be called.');
        }
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('groupBy', 'join', 'on', 'andOn', 'orOn')))) {
            throw new \BadMethodCallException("'having' can only be called immediately after calling either 'groupBy', 'join', 'on', 'andOn' or 'orOn' .");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'having';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['having']['ungrouped'][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        } else {
            $this->unfinishedQuery['having']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => '', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @return PDOQueryWrapper
     */
    public function andHaving($fieldName, $operator, $fieldValue = null)
    {
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('having', 'andHaving', 'orHaving')))) {
            throw new \BadMethodCallException("'andHaving' can only be called immediately after calling either 'having', 'andHaving' or 'orHaving'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'andHaving';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['having']['ungrouped'][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        } else {
            $this->unfinishedQuery['having']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'AND', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $fieldName
     * @param $operator
     * @param null $fieldValue
     * @return PDOQueryWrapper
     */
    public function orHaving($fieldName, $operator, $fieldValue = null)
    {
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('having', 'andHaving', 'orHaving')))) {
            throw new \BadMethodCallException("'orHaving' can only be called immediately after calling either 'having', 'andHaving' or 'orHaving'.");
        }
        if (!in_array($operator, $this->allowedComparisonOperators)) {
            throw new \InvalidArgumentException(strip_tags($operator) . "is not a valid comparison operator.");
        }
        if (!$this->comparisonValidation[$operator]($fieldName, $fieldValue)) {
            throw new \InvalidArgumentException('The given Name and/or Value are not allowed for the given operator.');
        }

        $this->unfinishedQuery['lastElement'] = 'andHaving';
        if (!ctype_digit((string)$this->currQueryGroup)) {
            $this->unfinishedQuery['having']['ungrouped'][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        } else {
            $this->unfinishedQuery['having']['grouped'][$this->currQueryGroup][] = array('precedingOperator' => 'OR', 'fieldName' => $fieldName, 'operator' => $operator, 'fieldValue' => $fieldValue);
        }
        $this->unfinishedQuery['currJoinName'] = null;
        return $this;
    }

    /**
     * @param $expression
     * @param $order
     * @return PDOQueryWrapper
     */
    public function orderBy($expression, $order)
    {
        if (!isset($this->unfinishedQuery['lastElement']) || (!in_array($this->unfinishedQuery['lastElement'], array('from', 'join', 'on', 'andOn', 'orOn', 'where', 'andWhere', 'orWhere', 'groupBy', 'orderBy','leaveParen')))) {
            throw new \BadMethodCallException("'orderBy' can only be called immediately after calling either 'from', 'join', 'on', 'andOn', 'orOn', 'where', 'andWhere', 'orWhere', 'groupBy','orderBy' or 'leaveParen'.");
        }
        if (!is_scalar($expression) || (strip_tags($expression) !== (string)$expression)) {
            throw new \InvalidArgumentException(strip_tags($expression) . ' is not a valid order-by expression.');
        }
        if (strcasecmp($order, 'asc') === 0) {
            $order = 'ASC';
        } elseif (strcasecmp($order, 'desc') === 0) {
            $order = 'DESC';
        } else {
            throw new \InvalidArgumentException('Paramter \'order\' must be \'asc\' or \'desc\' (not case sensitive).');
        }
        $this->unfinishedQuery['currJoinName'] = null;
        $this->unfinishedQuery['lastElement'] = 'orderBy';
        $this->unfinishedQuery['orderBy'][] = array('expression' => $expression, 'order' => $order);
        return $this;
    }

    /**
     * @param null $operator
     * @return $this
     */
    public function enterParentheses($operator = null)
    {
        if (isset($operator)) {
            if (strcasecmp($operator, 'AND') === 0) {
                $operator = 'AND';
            } elseif (strcasecmp($operator, 'OR') === 0) {
                $operator = 'OR';
            } else {
                throw new \InvalidArgumentException("Operator must be 'AND' or 'OR'.");
            }
        }
        if (!isset($this->unfinishedQuery['lastElement']) || !in_array($this->unfinishedQuery['lastElement'], array('join', 'on', 'andOn', 'orOn', 'where', 'andWhere', 'orWhere', 'leaveParen'))) {
            throw new \BadMethodCallException("Can only enter parantheses immediately after calling either 'join', 'on', 'andOn', 'orOn', 'where', 'andWhere','orWhere' or 'leaveParen.");
        }

        $newGroupIndex = 1;
        if (isset($this->unfinishedQueryGroups) && is_array($this->unfinishedQueryGroups) && count($this->unfinishedQueryGroups) > 0) {
            $newGroupIndex = (max(array_keys($this->unfinishedQueryGroups)) + 1);
        }
        $this->unfinishedQueryGroups[$newGroupIndex] = $operator;

        $this->currQueryGroup = $newGroupIndex;

        $this->unfinishedQuery['lastElement'] = 'enterParen';
        return $this;
    }

    /**
     * @return $this
     */
    public function leaveParentheses()
    {
        if (!ctype_digit((string)$this->currQueryGroup)) {
            throw new \BadMethodCallException("Can only leave parantheses if parentheses were set.");
        }
        $newGroupIndex = ($this->currQueryGroup - 1);
        $this->currQueryGroup = $newGroupIndex;
        if ($newGroupIndex <= 1) {
            $this->currQueryGroup = null;
        }
        $this->unfinishedQuery['lastElement'] = 'leaveParen';
        return $this;
    }

    /**
     * @return string
     */
    public function getConstructedQuery()
    {
        if (!isset($this->unfinishedQuery)) {
            throw new \BadMethodCallException('Query has not been constructed yet.');
        }

        //Evaluate Select-part
        $query = 'SELECT ';
        $query .= $this->unfinishedQuery['select'];
        $query .= ' FROM ';
        $i = 0;

        //Evaluate From-part
        foreach ($this->unfinishedQuery['from'] as $fromPart) {
            if ($i > 0) {
                $query .= ', ';
            }
            $query .= $fromPart['fromName'] . ' ';
            if (isset($fromPart['fromAlias'])) {
                $query .= $fromPart['fromAlias'] . ' ';
            }
            ++$i;
        }

        //Evaluate Join-part
        foreach ($this->unfinishedQuery['join'] as $joinPart) {
            $joinType = 'LEFT JOIN ';
            if ($joinPart['joinType'] === 'right') {
                $joinType = 'RIGHT JOIN ';
            } elseif ($joinPart['joinType'] === 'straight') {
                $joinType = 'STRAIGHT_JOIN ';
            }
            $query .= $joinType . $joinPart['joinName'] . ' ';
            if (isset($joinPart['joinAlias'])) {
                $query .= $joinPart['joinAlias'] . ' ';
            }
            if (is_array($joinPart['on'])) {
                $query .= 'ON (';
                if (isset($joinPart['on']['ungrouped']) && is_array($joinPart['on']['ungrouped'])) {
                    foreach ($joinPart['on']['ungrouped'] as $onCondition) {
                        $precedingOperator = $onCondition['precedingOperator'];
                        $fieldName = $onCondition['fieldName'];
                        $operator = $onCondition['operator'];
                        $valueIsLiteral = $onCondition['valueIsLiteral'];
                        $fieldValue = '';
                        if (isset($onCondition['fieldValue'])) {
                            $fieldValue = (($valueIsLiteral) ? $this->escapeString($onCondition['fieldValue']) : $onCondition['fieldValue']);
                        }
                        $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
                        ++$i;
                    }
                }
                if (isset($joinPart['on']['grouped']) && is_array($joinPart['on']['grouped'])) {
                    $i = 0;
                    foreach ($joinPart['on']['grouped'] as $groupKey => $groupArr) {
                        $connector = (($i > 0) || (isset($joinPart['on']['ungrouped']) && is_array($joinPart['on']['ungrouped']))) ? $this->unfinishedQueryGroups[$groupKey] : '';
                        $query .= " $connector (";
                        foreach ($joinPart['on']['grouped'][$groupKey] as $onCondition) {
                            $precedingOperator = $onCondition['precedingOperator'];
                            $fieldName = $onCondition['fieldName'];
                            $operator = $onCondition['operator'];
                            $valueIsLiteral = $onCondition['valueIsLiteral'];
                            $fieldValue = '';
                            if (isset($onCondition['fieldValue'])) {
                                $fieldValue = (($valueIsLiteral) ? $this->escapeString($onCondition['fieldValue']) : $onCondition['fieldValue']);
                            }
                            $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
                        }
                        $query .= ') ';
                        ++$i;
                    }
                }
                $query .= ') ';
            }
        }
        //Evaluate Where-part
        if (array_key_exists('where',$this->unfinishedQuery) && is_array($this->unfinishedQuery['where'])) {
            $query .= ' WHERE ';
            if (array_key_exists('ungrouped',$this->unfinishedQuery['where']) && is_array($this->unfinishedQuery['where']['ungrouped'])) {
                foreach ($this->unfinishedQuery['where']['ungrouped'] as $whereCondition) {
                    $precedingOperator = $whereCondition['precedingOperator'];
                    $fieldName = $whereCondition['fieldName'];
                    $operator = $whereCondition['operator'];
                    $valueIsLiteral = $whereCondition['valueIsLiteral'];
                    $fieldValue = '';
                    if (isset($whereCondition['fieldValue'])) {
                        $fieldValue = (($valueIsLiteral) ? $this->escapeString($whereCondition['fieldValue']) : $whereCondition['fieldValue']);
                    }
                    $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
                }
            }
            if (isset($this->unfinishedQuery['where']['grouped']) && is_array($this->unfinishedQuery['where']['grouped'])) {
                $i = 0;
                $hasWhereUngrouped = array_key_exists('ungrouped',$this->unfinishedQuery['where']) && is_array($this->unfinishedQuery['where']['ungrouped']);
                foreach ($this->unfinishedQuery['where']['grouped'] as $groupKey => $groupArr) {
                    $connector = (($i > 0) || $hasWhereUngrouped) ? $this->unfinishedQueryGroups[$groupKey] : '';
                    $query .= " $connector (";
                    foreach ($this->unfinishedQuery['where']['grouped'][$groupKey] as $whereCondition) {
                        $precedingOperator = $whereCondition['precedingOperator'];
                        $fieldName = $whereCondition['fieldName'];
                        $operator = $whereCondition['operator'];
                        $valueIsLiteral = $whereCondition['valueIsLiteral'];
                        $fieldValue = '';
                        if (isset($whereCondition['fieldValue'])) {
                            $fieldValue = (($valueIsLiteral) ? $this->escapeString($whereCondition['fieldValue']) : $whereCondition['fieldValue']);
                        }
                        $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
                    }
                    $query .= ')';
                    ++$i;
                }
            }
        }
        //Evaluate Group-By-part
        if (isset($this->unfinishedQuery['groupBy']) && is_array($this->unfinishedQuery['groupBy'])) {
            $query .= ' GROUP BY ';
            $i = 0;
            foreach ($this->unfinishedQuery['groupBy'] as $groupExp) {
                if ($i > 0) {
                    $query .= ', ';
                }
                $query .= $groupExp;
                ++$i;
            }
        }
        //Evaluate Having-part
        if (isset($this->unfinishedQuery['having']) && is_array($this->unfinishedQuery['having'])) {
            $query .= ' HAVING ';
            foreach ($this->unfinishedQuery['having']['ungrouped'] as $havingCondition) {
                $precedingOperator = $havingCondition['precedingOperator'];
                $fieldName = $havingCondition['fieldName'];
                $operator = $havingCondition['operator'];
                $valueIsLiteral = $havingCondition['valueIsLiteral'];
                $fieldValue = '';
                if (isset($havingCondition['fieldValue'])) {
                    $fieldValue = (($valueIsLiteral) ? $this->escapeString($havingCondition['fieldValue']) : $havingCondition['fieldValue']);
                }
                $fieldValue = (($havingCondition['fieldValue']) ? "'{$havingCondition['fieldValue']}'" : null);
                $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
            }
            foreach ($this->unfinishedQuery['having']['grouped'] as $groupKey => $groupArr) {
                $connector = $this->unfinishedQueryGroups[$groupKey];
                $query .= " $connector (";
                foreach ($this->unfinishedQuery['having']['grouped'][$groupKey] as $havingCondition) {
                    $precedingOperator = $havingCondition['precedingOperator'];
                    $fieldName = $havingCondition['fieldName'];
                    $operator = $havingCondition['operator'];
                    $valueIsLiteral = $havingCondition['valueIsLiteral'];
                    $fieldValue = '';
                    if (isset($havingCondition['fieldValue'])) {
                        $fieldValue = (($valueIsLiteral) ? $this->escapeString($havingCondition['fieldValue']) : $havingCondition['fieldValue']);
                    }
                    $query .= ' ' . $precedingOperator . ' (' . $fieldName . ' ' . $operator . ' ' . $fieldValue . ') ';
                }
                $query .= ') ';
            }
            $query .= ') ';
        }

        //Evaluate Order-By-part
        if (array_key_exists('orderBy',$this->unfinishedQuery) && is_array($this->unfinishedQuery['orderBy'])) {
            $query .= ' ORDER BY ';
            $i = 0;
            foreach ($this->unfinishedQuery['orderBy'] as $groupExpArr) {
                $expression = $groupExpArr['expression'];
                $order = $groupExpArr['order'];
                if ($i > 0) {
                    $query .= ', ';
                }
                $query .= $expression . ' ' . $order;
                ++$i;
            }
        }
        return $query;
    }

    /**
     * @return $this
     */
    public function setConstructedQuery()
    {
        $this->setQuery($this->getConstructedQuery());
        unset($this->unfinishedQuery);
        return $this;
    }


    protected function _setComparisonValidationArray()
    {
        $arr = array(
            'IS NULL' => function ($name, $value = null) {
                return (is_scalar($name) && (strip_tags($name) === (string)$name));
            },
            'IS NOT NULL' => function ($name, $value = null) {
                return (is_scalar($name) && (strip_tags($name) === (string)$name));
            },
            '=' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            '!=' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            '<' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            '>' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            '>=' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            '<=' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            },
            'IN' => function ($name, $value) {
                return (is_scalar($name) && (strip_tags($name) === (string)$name) && ((is_scalar($value) && (strip_tags($value) === (string)$value)) || (is_array($value) && array_depth($value) === 1)));
            },
            'LIKE' => function ($name, $value) {
                return (is_scalar($name) && is_scalar($value) && (strip_tags($name) === (string)$name) && (strip_tags($value) === (string)$value));
            }
        );
        $this->comparisonValidation = $arr;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->connectionObject->inTransaction();
    }

    /**
     * @param array $arr
     * @param $tableName
     * @param $updateOrInsert
     * @return string
     */
    public function getUpdateInsertQueryFromArray(array $arr, $tableName, $updateOrInsert)
    {
        $strIsUpdate = (strcasecmp($updateOrInsert, 'update') === 0);
        $strIsInsert = (strcasecmp($updateOrInsert, 'insert') === 0);
        if (!(strlen($tableName) > 0)) {
            throw new \InvalidArgumentException("Parameter 'tableName' must not be empty.");
        }
        if (!$strIsInsert && !$strIsUpdate) {
            throw new \InvalidArgumentException('Parameter \'updateOrInsert\' must be a either \'update\' or \'insert\'');
        }
        if ($strIsUpdate && (!array_key_exists('id', $arr) || !($arr['id'] > 0))) {
            throw new \InvalidArgumentException('id-field must be set in array for update operation');
        } elseif ($strIsInsert && array_key_exists('id', $arr) && $arr['id'] > 0) {
            throw new \InvalidArgumentException('id-field must not be set to a positive value for insert operation');
        }

        if ($strIsUpdate) {
            $id = (int)$arr['id'];
            $updateInsertSnippet = "UPDATE `$tableName` ";
            $whereIDSnippet = " WHERE (`id` = $id)";
        } else {
            $updateInsertSnippet = "INSERT INTO `$tableName` ";
            $whereIDSnippet = '';
        }
        $query = $updateInsertSnippet . 'SET ';
        $i = 0;
        foreach ($arr as $fieldName => $fieldValue) {
            if ($fieldName !== 'id') {
                if($i > 0) {
                    $query .= ',';
                }
                if(is_bool($fieldValue)) { $fieldValue = (int)$fieldValue;}
                elseif(is_float($fieldValue)) { $fieldValue = number_format($fieldValue,4,'.','');}
                elseif(is_string($fieldValue)) {$fieldValue = $this->connectionObject->quote($fieldValue);}
                $fieldValue = (string)$fieldValue;
                if('' === $fieldValue) { $fieldValue = '\'\'';}
                $query .= "`$fieldName` = $fieldValue";
                $i++;
            }
        }
        $query .= $whereIDSnippet;
        return $query;
    }

    /**
     * @param $tableName
     * @param $id
     * @return string
     */
    public function getDeleteQueryForID($tableName, $id)
    {
        if (!(strlen($tableName) > 0)) {
            throw new \InvalidArgumentException("Parameter 'tableName' must not be empty.");
        }
        $id = (int)$id;
        if (!($id > 0)) {
            throw new \InvalidArgumentException("Parameter 'id' must be a positive integer.");
        }
        $query = "DELETE FROM `$tableName` WHERE id = $id";
        return $query;
    }

    /**
     * @param $objName
     * @param array $constructorArgFields
     * @return mixed
     */
    public function getResultRowAsObject($objName, array $constructorArgFields = [])
    {
        if(!$this->stmt instanceof \PDOStatement) {
            throw new \RuntimeException('No PDOStatement for query. Cannot execute.');
        }

        $obj = $this->stmt->fetchObject($objName,$constructorArgFields);
        return $obj;
    }

    /**
     * @param $objName
     * @param array $constructorArgFields
     * @return array
     */
    public function getAllResultRowAsArrayOfObjects($objName, array $constructorArgFields = [])
    {
        if(!$this->stmt instanceof \PDOStatement) {
            throw new \RuntimeException('No PDOStatement for query. Cannot execute.');
        }
        $objArr = $this->stmt->fetchAll(PDO::FETCH_CLASS,$objName);
        return $objArr;
    }
}