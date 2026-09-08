<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.11.2016
 * Time: 22:28
 */

namespace DynCom\dc\workerqueue\main;

use DynCom\dc\workerqueue\main\exceptions\JobScheduleResourceErrorException;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class PDOCronSpecScheduledJobGateway
 * @package DynCom\dc\workerqueue\main
 */
class PDOCronSpecScheduledJobGateway implements CronSpecScheduledJobGateway
{
    protected const QUERY_NEXT_FOR_PID = '
        SELECT 
            `id`,
            `cron_spec`,
            `queue_name`,
            `time_to_live`,
            `max_no_of_retries`,
            `payload`
        FROM
            `scheduled_jobs`
        WHERE
            `registered_with_pid` NOT IN (%pidString%)
        ORDER BY `id`
        LIMIT 1        
        FOR UPDATE    
    ';

    protected const QUERY_INSERT_SCHEDULED_JOB = '
        INSERT INTO
          `scheduled_jobs`
        SET
          `cron_spec` = :cron_spec,
          `queue_name` = :queue_name,
          `time_to_live` = :ttl,
          `max_no_of_retries` = :max_retries,
          `payload` = :payload          
    ';

    protected const QUERY_UPDATE_SCHEDULED_JOB = '
        UPDATE
          `scheduled_jobs`
        SET
          `cron_spec` = :cron_spec,
          `queue_name` = :queue_name,
          `time_to_live` = :ttl,
          `max_no_of_retries` = :max_retries,
          `payload` = :payload
        WHERE
          `id` = :id
    ';

    protected const QUERY_UPDATE_SCHEDULED_JOB_PID = '
        UPDATE
            `scheduled_jobs`
        SET
          `registered_with_pid` = :handler_pid
        WHERE
          `id` = :id
    ';

    protected const QUERY_DELETE_SCHEDULED_JOB = '
        DELETE FROM
          `scheduled_jobs`
        WHERE
          `id` = :id
    ';

    protected const QUERY_RELEASE_JOBS_FROM_PID = '
        UPDATE 
          `scheduled_jobs`
        SET
          `pid` = \'\'
        WHERE
          `registered_with_pid` = :pid
    ';


    protected $connectionParams;
    /**
     * @var PDO
     */
    protected $db;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PDOCronSpecScheduledJobGateway constructor.
     * @param $dsn
     * @param $user
     * @param $pass
     * @param array $options
     * @param LoggerInterface|null $logger
     * @throws JobScheduleResourceErrorException
     */
    public function __construct(
        string $dsn,
        string $user,
        string $pass,
        array $options = [],
        LoggerInterface $logger = null
    ) {
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
            $errorDetails = print_r($e, true);
            throw new JobScheduleResourceErrorException($connectionDetails, $errorDetails);
        }
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
        $this->db = null;
    }

    /**
     * @param int $pid
     * @param array $excludedPids
     * @return CronSpecScheduledJob
     * @throws JobScheduleResourceErrorException
     */
    public function getNextScheduledJobForPID(int $pid, array $excludedPids): CronSpecScheduledJob
    {

        $scheduledJob = new NullCronSpecScheduledJob();
        $this->setConnection($this->connectionParams);

        try {
            $this->db->beginTransaction();
            $arr = $this->fetchNextScheduledJobAsArray($excludedPids);
            $scheduledJob = $this->getScheduledJobFromRowAndUpdateWithPIDOrDeleteIfInvalid($arr,$pid);

            if (!$this->db->commit()) {
                $error = $this->db->errorInfo();
                $this->logger->error(
                    'Could not commit DB Transaction for scheduled from pid [' . $pid . '] - Error [' . print_r(
                        $error,
                        true
                    ) . '].'
                );
                throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($error));
            }

        } catch (PDOException $e) {
            throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return $scheduledJob;
    }

    protected function getScheduledJobFromRowAndUpdateWithPIDOrDeleteIfInvalid(array $row, $pid): CronSpecScheduledJob
    {
        $scheduledJob = new NullCronSpecScheduledJob();
        if (array_key_exists('id', $row) && (int)$row['id'] > 0) {
            $id = (int)$row['id'];
            //ttl in seconds
            $ttl = (int)$row['time_to_live'];
            //unix timestamp, fractional
            $creationTimestamp = (int)microtime(true);

            $queueName = $row['queue_name'];
            $maxRetries = (int)$row['max_no_of_retries'];
            $cronSpec = $row['cron_spec'];
            try {
                $payload = (array)json_decode($row['payload'], true);
            } catch (\Throwable $t) {
                $logMsg = 'The job with id [' . $id . '] has no valid payload and will be deleted. Its queuename was [' . $queueName . '] and its payload in json-format was [' . $row['payload'] . ']. Error was [' . json_last_error_msg() . '].';
                $this->logger->error($logMsg);
                $delStmt = $this->db->prepare(self::QUERY_DELETE_SCHEDULED_JOB);
                $delStmt->bindValue(':id', $id);
                $delStmt->execute();
                return new NullCronSpecScheduledJob();
            }

            try {
                $scheduledJob = new GenericCronSpecScheduledJob(
                    $queueName,
                    $creationTimestamp,
                    $ttl,
                    $maxRetries,
                    0,
                    $payload,
                    $cronSpec
                );
            } catch (\Exception $e) {
                $logMsg = 'The job with id [' . $id . '] has no valid cron specification and will be deleted. Its queuename was [' . $queueName . '], its raw cron spec was [' . $cronSpec . '] and its payload in json-format was [' . $row['payload'] . '].';
                $this->logger->error($logMsg);
                $delStmt = $this->db->prepare(self::QUERY_DELETE_SCHEDULED_JOB);
                $delStmt->bindValue(':id', $id);
                $delStmt->execute();
                return new NullCronSpecScheduledJob();
            }
            $this->setSchedulingID($scheduledJob, $id);
            $this->updateScheduledJobPID($id, $pid);
        }
        return $scheduledJob;
    }

    /**
     * @param array $connectionData
     */
    protected function setConnection(array $connectionData): void
    {
        $this->db = null;
        try {
            $this->db = new PDO(
                $connectionData['dsn'],
                $connectionData['user'],
                $connectionData['pass'],
                $connectionData['options']
            );
        } catch (PDOException $e) {
            $this->logger->alert(
                'There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].'
            );
            throw $e;
        }
        return;
    }

    /**
     * @param $object
     * @param $id
     */
    protected function setSchedulingID($object, $id): void
    {
        $id = (int)$id;
        if (!is_object($object)) {
            throw new \InvalidArgumentException("Parameter 'object' must be an object.");
        }
        if (!($id > 0)) {
            throw new \InvalidArgumentException("Parameter 'id' must be a positive integer.");
        }
        $reflClass = new \ReflectionClass($object);
        if (!$reflClass->hasProperty('schedulingID')) {
            throw new \InvalidArgumentException("Object has no property 'schedulingID'.");
        }
        $idProp = $reflClass->getProperty('schedulingID');
        $idProp->setAccessible(true);
        $idProp->setValue($object, (int)$id);
        return;
    }

    protected function unsetConnection(): void
    {
        $this->db = null;
    }

    /**
     * @param CronSpecScheduledJob $job
     * @throws JobScheduleResourceErrorException
     */
    public function createScheduledJob(CronSpecScheduledJob $job): void
    {
        $this->setConnection($this->connectionParams);
        try {
            $cronSpec = $job->getCronSpec();
            $queueName = $job->getQueueName();
            $ttl = $job->getTimeToLiveSeconds();
            $maxRetries = $job->getMaxNoOfRetries();
            $payload = json_encode($job->getPayload());

            $stmt = $this->db->prepare(static::QUERY_INSERT_SCHEDULED_JOB);

            $stmt->bindValue(':cron_spec', $cronSpec, PDO::PARAM_STR);
            $stmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
            $stmt->bindValue(':ttl', $ttl, PDO::PARAM_INT);
            $stmt->bindValue(':max_retries', $maxRetries, PDO::PARAM_INT);
            $stmt->bindValue(':payload', $payload, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($error));
            }

            $id = $this->db->lastInsertId();
            $this->setSchedulingID($job, $id);
        } catch (PDOException $e) {
            throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($e));
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param $id
     * @throws JobScheduleResourceErrorException
     */
    public function deleteScheduledJobByID(int $id): void
    {
        $this->setConnection($this->connectionParams);
        $id = abs($id);
        $stmt = $this->db->prepare(static::QUERY_DELETE_SCHEDULED_JOB);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($error));
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param CronSpecScheduledJob $job
     * @throws JobScheduleResourceErrorException
     */
    public function updateScheduledJob(CronSpecScheduledJob $job): void
    {
        $this->setConnection($this->connectionParams);
        if ((int)$job->getSchedulingID() > 0) {
            try {
                $cronSpec = $job->getCronSpec();
                $queueName = $job->getQueueName();
                $ttl = $job->getTimeToLiveSeconds();
                $maxRetries = $job->getMaxNoOfRetries();
                $payload = json_encode($job->getPayload());

                $stmt = $this->db->prepare(static::QUERY_UPDATE_SCHEDULED_JOB);

                $stmt->bindValue(':cron_spec', $cronSpec, PDO::PARAM_STR);
                $stmt->bindValue(':queue_name', $queueName, PDO::PARAM_STR);
                $stmt->bindValue(':ttl', $ttl, PDO::PARAM_INT);
                $stmt->bindValue(':max_retries', $maxRetries, PDO::PARAM_INT);
                $stmt->bindValue(':payload', $payload, PDO::PARAM_STR);
                $stmt->bindValue(':id', (int)$job->getSchedulingID(), PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $error = $stmt->errorInfo();
                    throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($error));
                }

                $id = $this->db->lastInsertId();
                $this->setSchedulingID($job, $id);
            } catch (PDOException $e) {
                throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($e));
            }
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param $pid
     * @throws JobScheduleResourceErrorException
     */
    public function releaseJobsFromPID(int $pid): void
    {
        $this->setConnection($this->connectionParams);
        $stmt = $this->db->prepare(self::QUERY_RELEASE_JOBS_FROM_PID);
        $stmt->bindValue(':pid', (int)$pid, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new JobScheduleResourceErrorException($this->connectionParams, json_encode($error));
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param $scheduledJobID
     * @param int $pid
     */
    protected function updateScheduledJobPID($scheduledJobID, int $pid): void
    {
        $updateStmt = $this->db->prepare(static::QUERY_UPDATE_SCHEDULED_JOB_PID);
        $updateStmt->bindValue(':handler_pid', $pid, PDO::PARAM_STR);
        $updateStmt->bindValue(':id', $scheduledJobID, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    /**
     * @param array $excludedPids
     * @return array
     */
    protected function fetchNextScheduledJobAsArray(array $excludedPids): array
    {
        $queryRaw = static::QUERY_NEXT_FOR_PID;
        $query = str_replace('%pidString%', implode(',', $excludedPids), $queryRaw);
        //$this->logger->info('Query is [' . $query . '].');
        $fetchStmt = $this->db->query($query);

        $fetchStmt->execute();
        $arr = $fetchStmt->fetch(PDO::FETCH_ASSOC);
        return (array)$arr;
    }

}