<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;

use DynCom\dc\workerqueue\main\exceptions\InvalidJobPayloadErrorException;
use DynCom\dc\workerqueue\main\exceptions\JobNotCompletedBeforeTimeoutException;
use DynCom\dc\workerqueue\main\exceptions\QueueConnectionErrorException;
use Exception;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 15:53
 */
class PDOJobQueueGateway implements JobQueueGateway
{

    protected const CREATE_JOB_QUERY_TEMPLATE = '
        INSERT INTO 
          general_job_queue
        SET
          queue_name = :queue_name,
          status = \'OPEN\',
          failure_code = 0,
          failure_data = \'\',
          no_of_unsuccessful_attempts = 0,
          last_action_timestamp = FROM_UNIXTIME(:last_action_timestamp),
          creation_timestamp = FROM_UNIXTIME(:creation_timestamp),
          payload = :payload,
          time_to_live = :time_to_live,
          max_no_of_retries = :max_no_of_retries
          
    ';
    protected const GET_NEXT_JOB_QUERY_TEMPLATE = '    
        SELECT
          id,
          queue_name,
          `status`,
          payload,          
          creation_timestamp,
          time_to_live,
          UNIX_TIMESTAMP(creation_timestamp) AS \'creation_timestamp\',
          UNIX_TIMESTAMP(last_action_timestamp) AS \'last_action_timestamp\',
          failure_code,
          failure_data,
          no_of_unsuccessful_attempts,
          max_no_of_retries          
        FROM 
          general_job_queue 
        WHERE 
              (:check_queue_name = 0 OR queue_name = :queue_name) 
          AND `status` = :status_param 
        ORDER BY 
          id
        LIMIT 1
    ';
    protected const SNIPPET_FOR_UPDATE = ' 
        FOR UPDATE';
    protected const UPDATE_QUERY_TEMPLATE = '
        UPDATE 
            general_job_queue
        SET
            `status` = :status_param,
            failure_code = :failure_code,
            failure_data = :failure_data,
            no_of_unsuccessful_attempts = :no_of_unsuccessful_attempts,
            last_action_timestamp = FROM_UNIXTIME(:last_action_timestamp)
        WHERE
            id = :id
    ';
    protected $connectionParams;
    protected $db;
    protected $logger;

    /**
     * PDOJobQueueGateway constructor.
     * @param $dsn
     * @param $user
     * @param $pass
     * @param array $options
     * @param LoggerInterface|null $logger
     * @throws QueueConnectionErrorException
     */
    public function __construct(string $dsn, string $user, string $pass, array $options = [], LoggerInterface $logger = null)
    {
        $this->connectionParams = [
            'dsn' => $dsn,
            'user' => $user,
            'pass' => $pass,
            'options' => $options,
        ];
        try {
            $this->db = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            $connectionDetails = [
                'dsn' => $dsn,
                'user' => $user,
            ];
            $errorDetails = $e->getMessage();
            throw new QueueConnectionErrorException($connectionDetails, $errorDetails);
        }
        $this->logger = $logger !== null ? $logger : new NullLogger();
        $this->unsetConnection();
    }

    protected function unsetConnection(): void
    {
        $this->db = null;
        return;
    }

    /**
     * @param string $queueName
     * @param bool $peek
     * @return Job
     * @throws QueueConnectionErrorException
     */
    public function getNextOpenJob(string $queueName, $peek = false): Job
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . $queueName . '] and [' . $peek . '].');
        $this->setConnection($this->connectionParams);
        try {
            $status = AbstractJob::JOB_STATUS_OPEN;
            $this->db->beginTransaction();
            $preparedStmtStr = static::GET_NEXT_JOB_QUERY_TEMPLATE;
            if (!$peek) {
                $preparedStmtStr .= static::SNIPPET_FOR_UPDATE;
            }
            $fetchStmt = $this->db->prepare($preparedStmtStr);
            $fetchStmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
            $fetchStmt->bindValue(':check_queue_name', 1, PDO::PARAM_INT);
            $fetchStmt->bindValue(':status_param', $status, PDO::PARAM_STR);
            if (!$fetchStmt->execute()) {
                $error = $fetchStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
            $err = $fetchStmt->errorInfo();
            $query = $fetchStmt->queryString;
            $this->logger->debug('Array returned for query [' . $query . '] with params [' . $queueName . '] and [' . $status . '] was [' . json_encode($arr) . ']. Err: [' . json_encode($err) . '].');
            if (is_array($arr) && array_key_exists('id', $arr) && (int)$arr['id'] > 0) {
                $currentStatus = $arr['status'];
                $updateFailureCode = 0;
                $updateFailureData = [];
                if (!$peek) {
                    //ttl in seconds
                    $ttl = (int)$arr['time_to_live'];
                    //unix timestamp, fractional
                    $creation_timestamp = (int)$arr['creation_timestamp'];
                    $cutoff_timestamp = $creation_timestamp + $ttl;
                    $current_timestamp = microtime(true);
                    if ($ttl !== 0 && $current_timestamp > $cutoff_timestamp) {
                        $exception = new JobNotCompletedBeforeTimeoutException((int)$arr['id'], $arr['queue_name'], (int)$creation_timestamp, $ttl);
                        $currentStatus = AbstractJob::JOB_STATUS_FAILED;
                        $updateFailureCode = $exception->getCode();
                        $updateFailureData = json_encode($exception);
                    } else {
                        $currentStatus = AbstractJob::JOB_STATUS_LOCKED;
                        $updateFailureCode = 0;
                        $updateFailureData = '';
                    }
                    $updateNoOfUnsuccessfulAttempts = $arr['no_of_unsuccessful_attempts'];
                    $updateLastActionTimestamp = (string)microtime(true);

                    $updateStmt = $this->db->prepare(static::UPDATE_QUERY_TEMPLATE);
                    $updateStmt->bindValue(':status_param', $currentStatus, PDO::PARAM_STR);
                    $updateStmt->bindValue(':failure_code', $updateFailureCode, PDO::PARAM_INT);
                    $updateStmt->bindValue(':failure_data', $updateFailureData, PDO::PARAM_STR);
                    $updateStmt->bindValue(':no_of_unsuccessful_attempts', $updateNoOfUnsuccessfulAttempts, PDO::PARAM_INT);
                    $updateStmt->bindValue(':last_action_timestamp', $updateLastActionTimestamp, PDO::PARAM_STR);
                    $updateStmt->bindValue(':id', $arr['id'], PDO::PARAM_INT);
                    $updateStmt->execute();
                    if (!$this->db->commit()) {
                        $error = $this->db->errorInfo();
                        throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
                    }
                }
                if ($currentStatus === AbstractJob::JOB_STATUS_FAILED) {
                    $this->logger->info("Line " . __LINE__ . " - returning null job");
                    $job = new NullJob();
                } else {
                    $job = new GenericJob(
                        $arr['queue_name'],
                        (int)$arr['creation_timestamp'],
                        (int)$arr['time_to_live'],
                        (int)$arr['max_no_of_retries'],
                        (int)$arr['no_of_unsuccessful_attempts'],
                        $this->getPayloadAsArray($arr['payload'], $queueName, (int)$arr['id'])
                    );
                    $this->setID($job, $arr['id']);
                    switch ($currentStatus) {
                        case AbstractJob::JOB_STATUS_LOCKED:
                            $job->setStatusLocked();
                            break;
                        case AbstractJob::JOB_STATUS_FAILED:
                            $job->setStatusFailed($updateFailureCode, $updateFailureData);
                            break;
                        case AbstractJob::JOB_STATUS_OPEN:
                            $job->setStatusOpen();
                            break;
                        default:
                            break;
                    }
                }
            } else {
                $this->db->rollBack();
                $job = new NullJob();
                $this->logger->info("Line " . __LINE__ . " - returning null job");
            }
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $job;
    }

    /**
     * @param array $connectionData
     */
    protected function setConnection(array $connectionData): void
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . print_r($connectionData, true) . '].');
        $this->db = null;
        try {
            $this->db = new PDO($connectionData['dsn'], $connectionData['user'], $connectionData['pass'], $connectionData['options']);
        } catch (PDOException $e) {
            $this->logger->alert('There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].');
            throw $e;
        }
        return;
    }

    /**
     * @param $payload
     * @param $queueName
     * @param $jobID
     * @return array|mixed
     * @throws InvalidJobPayloadErrorException
     */
    protected function getPayloadAsArray($payload, string $queueName, int $jobID): array
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . serialize($payload) . '], [' . $queueName . '] and [' . $jobID . '].');
        if (is_array($payload)) {
            return $payload;
        } elseif (is_string($payload)) {
            //attempt json-decode
            $decodedPayload = json_decode($payload, true);
            if ($decodedPayload === null) {
                return [];
            }
            return $decodedPayload;
        } elseif (is_object($payload)) {
            try {
                return (array)$payload;
            } catch (Exception $e) {
                throw new InvalidJobPayloadErrorException($jobID, $queueName, serialize($payload), 'Payload was object which could not be cast to array.');
            }
        } else {
            throw new InvalidJobPayloadErrorException($jobID, $queueName, serialize($payload), 'Payload was neither array, nor string, nor object.');
        }
    }

    /**
     * @param $object
     * @param $id
     */
    protected function setID($object, $id): void
    {
        $id = (int)$id;
        if (!is_object($object)) {
            throw new \InvalidArgumentException("Parameter 'object' must be an object.");
        }
        if (!($id > 0)) {
            throw new \InvalidArgumentException("Parameter 'id' must be a positive integer.");
        }
        $reflClass = new \ReflectionClass($object);
        if (!$reflClass->hasProperty('id')) {
            throw new \InvalidArgumentException("Object has no property 'id'.");
        }
        $idProp = $reflClass->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($object, (int)$id);
        return;
    }

    /**
     * @param string $queueName
     * @return Job
     * @throws QueueConnectionErrorException
     */
    public function getNextFailedJob(string $queueName): Job
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . $queueName . '].');
        $this->setConnection($this->connectionParams);
        try {
            $status = AbstractJob::JOB_STATUS_FAILED;
            $fetchStmt = $this->db->prepare(static::GET_NEXT_JOB_QUERY_TEMPLATE);
            $fetchStmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
            $fetchStmt->bindValue(':check_queue_name', 1, PDO::PARAM_INT);
            $fetchStmt->bindValue(':status_param', $status, PDO::PARAM_STR);
            if (!$fetchStmt->execute()) {
                $error = $fetchStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
            if (array_key_exists('id', $arr) && (int)$arr['id'] > 0) {
                $job = new GenericJob(
                    $arr['queue_name'],
                    (string)(int)$arr['creation_timestamp'],
                    (int)$arr['time_to_live'],
                    (int)$arr['max_no_of_retries'],
                    (int)$arr['no_of_unsuccessful_attempts'],
                    $this->getPayloadAsArray($arr['payload'], $queueName, (int)$arr['id'])
                );
                $this->setID($job, $arr['id']);
                $job->setStatusFailed($arr['failure_code'], [$arr['failure_data']]);
            } else {
                $job = new NullJob();
                $this->logger->info("Line " . __LINE__ . " - returning null job.");
            }
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $job;
    }

    /**
     * @param string $queueName
     * @return Job
     * @throws QueueConnectionErrorException
     */
    public function getNextFinishedJob(string $queueName): Job
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . $queueName . '].');
        $this->setConnection($this->connectionParams);
        try {
            $status = AbstractJob::JOB_STATUS_FINISHED;
            $fetchStmt = $this->db->prepare(static::GET_NEXT_JOB_QUERY_TEMPLATE);
            $fetchStmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
            $fetchStmt->bindValue(':check_queue_name', 1, PDO::PARAM_INT);
            $fetchStmt->bindValue(':status_param', $status, PDO::PARAM_STR);
            if (!$fetchStmt->execute()) {
                $error = $fetchStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
            if (array_key_exists('id', $arr) && (int)$arr['id'] > 0) {
                $job = new GenericJob(
                    $arr['queue_name'],
                    (string)(int)$arr['creation_timestamp'],
                    (int)$arr['time_to_live'],
                    (int)$arr['max_no_of_retries'],
                    (int)$arr['no_of_unsuccessful_attempts'],
                    $this->getPayloadAsArray($arr['payload'], $queueName, (int)$arr['id'])
                );
                $this->setID($job, $arr['id']);
                $job->setStatusFinished();
            } else {
                $job = new NullJob();
                $this->logger->info("Line " . __LINE__ . " - returning null job.");
            }
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $job;
    }

    /**
     * @param string $queueName
     * @return Job
     * @throws QueueConnectionErrorException
     */
    public function getNextLockedJob(string $queueName): Job
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . $queueName . '].');
        $this->setConnection($this->connectionParams);
        try {
            $status = AbstractJob::JOB_STATUS_LOCKED;
            $fetchStmt = $this->db->prepare(static::GET_NEXT_JOB_QUERY_TEMPLATE);
            $fetchStmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
            $fetchStmt->bindValue(':check_queue_name', 1, PDO::PARAM_INT);
            $fetchStmt->bindValue(':status_param', $status, PDO::PARAM_STR);
            if (!$fetchStmt->execute()) {
                $error = $fetchStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
            if (array_key_exists('id', $arr) && (int)$arr['id'] > 0) {
                $job = new GenericJob(
                    $arr['queue_name'],
                    (string)(int)$arr['creation_timestamp'],
                    (int)$arr['time_to_live'],
                    (int)$arr['max_no_of_retries'],
                    (int)$arr['no_of_unsuccessful_attempts'],
                    $this->getPayloadAsArray($arr['payload'], $queueName, (int)$arr['id'])
                );
                $this->setID($job, $arr['id']);
                $job->setStatusLocked();
            } else {
                $job = new NullJob();
            }
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $job;
    }

    /**
     * @param Job $job
     * @throws QueueConnectionErrorException
     */
    public function updateJob(Job $job): void
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . json_encode($job) . '].');
        $this->setConnection($this->connectionParams);
        if ((int)$job->getID() > 0) {
            try {
                $failureCode = $job->getFailureCode();
                $failureCodeType = $failureCode === null ? PDO::PARAM_NULL : PDO::PARAM_INT;

                $updateStmt = $this->db->prepare(static::UPDATE_QUERY_TEMPLATE);
                $updateStmt->bindValue(':status_param', $job->getStatus(), PDO::PARAM_STR);
                $updateStmt->bindValue(':failure_code', $failureCode, $failureCodeType);
                $updateStmt->bindValue(':failure_data', json_encode($job->getFailureData()), PDO::PARAM_STR);
                $updateStmt->bindValue(':no_of_unsuccessful_attempts', $job->getNoOfUnsuccessfulAttempts(), PDO::PARAM_INT);
                $updateStmt->bindValue(':last_action_timestamp', $job->getLastActionTimestamp(), PDO::PARAM_STR);
                $updateStmt->bindValue(':id', $job->getID(), PDO::PARAM_INT);
                if (!$updateStmt->execute()) {
                    $error = $this->db->errorInfo();
                    throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
                }
            } catch (PDOException $e) {
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
            }
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param Job $job
     * @throws QueueConnectionErrorException
     * @return void
     */
    public function createJob(Job $job): void
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameters [' . json_encode($job) . '].');
        $this->setConnection($this->connectionParams);
        try {
            $createStmt = $this->db->prepare(static::CREATE_JOB_QUERY_TEMPLATE);
            $createStmt->bindValue(':queue_name', $job->getQueueName(), PDO::PARAM_STR);
            $createStmt->bindValue(':last_action_timestamp', $job->getLastActionTimestamp(), PDO::PARAM_STR);
            $createStmt->bindValue(':creation_timestamp', $job->getCreationTimestamp(), PDO::PARAM_STR);
            $createStmt->bindValue(':payload', json_encode($job->getPayload()), PDO::PARAM_STR);
            $createStmt->bindValue(':time_to_live', $job->getTimeToLiveSeconds(), PDO::PARAM_INT);
            $createStmt->bindValue(':max_no_of_retries', $job->getMaxNoOfRetries(), PDO::PARAM_INT);
            if (!$createStmt->execute()) {
                $error = $createStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $id = $this->db->lastInsertId();
            $this->setID($job, $id);
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return;
    }

    public function getNextFailedJobAllQueues(): Job
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called.');
        $this->setConnection($this->connectionParams);
        try {
            $status = AbstractJob::JOB_STATUS_OPEN;
            $this->db->beginTransaction();
            $preparedStmtStr = static::GET_NEXT_JOB_QUERY_TEMPLATE;

            $fetchStmt = $this->db->prepare($preparedStmtStr);
            $fetchStmt->bindValue(':queue_name', '', PDO::PARAM_STR);
            $fetchStmt->bindValue(':check_queue_name', 0, PDO::PARAM_INT);
            $fetchStmt->bindValue(':status_param', AbstractJob::JOB_STATUS_FAILED, PDO::PARAM_STR);
            if (!$fetchStmt->execute()) {
                $error = $fetchStmt->errorInfo();
                throw new QueueConnectionErrorException($this->connectionParams, json_encode($error));
            }
            $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
            $err = $fetchStmt->errorInfo();
            $query = $fetchStmt->queryString;
            $this->logger->debug('Array returned for query [' . $query . '] was [' . json_encode($arr) . ']. Err: [' . json_encode($err) . '].');
            if (is_array($arr) && array_key_exists('id', $arr) && (int)$arr['id'] > 0) {
                $currentStatus = $arr['status'];
                $updateFailureCode = 0;
                $updateFailureData = [];

                $job = new GenericJob(
                    $arr['queue_name'],
                    (int)$arr['creation_timestamp'],
                    (int)$arr['time_to_live'],
                    (int)$arr['max_no_of_retries'],
                    (int)$arr['no_of_unsuccessful_attempts'],
                    $this->getPayloadAsArray($arr['payload'], '', (int)$arr['id'])
                );
                $this->setID($job, $arr['id']);
                switch ($currentStatus) {
                    case AbstractJob::JOB_STATUS_LOCKED:
                        $job->setStatusLocked();
                        break;
                    case AbstractJob::JOB_STATUS_FAILED:
                        $job->setStatusFailed($updateFailureCode, $updateFailureData);
                        break;
                    case AbstractJob::JOB_STATUS_OPEN:
                        $job->setStatusOpen();
                        break;
                    default:
                        break;
                }

            } else {
                $this->db->rollBack();
                $job = new NullJob();
                $this->logger->info("Line " . __LINE__ . " - returning null job");
            }
        } catch (PDOException $e) {
            throw new QueueConnectionErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $job;
    }


}