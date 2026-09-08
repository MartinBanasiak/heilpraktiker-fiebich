<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2016
 * Time: 11:02
 */

namespace DynCom\dc\workerqueue\main;


use DynCom\dc\workerqueue\main\exceptions\JobFailedAfterTooManyRetriesErrorException;
use DynCom\dc\workerqueue\main\exceptions\JobNotCompletedBeforeTimeoutException;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class jobHandlerTrait
 * @package DynCom\dc\workerqueue\main
 */
trait jobHandlerTrait
{
    /**
     * @var array
     */
    protected $extractedValidatedPayloadData = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @return string
     */
    abstract public function getJobQueueName(): string;

    /**
     * @param Job $job
     * @throws Exception
     */
    abstract public function doJob(Job $job): void;

    /**
     * @param Job $job
     */
    public function processJob(Job $job): void
    {
        $logger = $this->getLogger();
        if ($job instanceof NullJob) {
            $logger->notice('Job is a NullJob - exiting');
            return;
        }
        $logger->notice('Attempting to handle payload for job with id [' . $job->getID() . '].');
        try {
            $this->handlePayload($job);
        } catch (Exception $e) {
            $job->setStatusFailed($e->getCode(), json_encode($e));
            return;
        }

        $maxNoOfRetriesExceeded = $job->hasExceededMaxNoOfRetries();
        $timeToLiveExceeded = $job->hasExceededTimeToLive();
        if (!$timeToLiveExceeded && !$maxNoOfRetriesExceeded) {
            $logger->info('Job is valid -> attempt performing...');
            try {
                $this->doJob($job);
                $logger->info('...Job done!');
            } catch (Exception $e) {
                $errMsg = $e->getMessage();
                $logger->notice('...error [' . $errMsg . '] - job set to failed.');
                $job->setStatusFailed($e->getCode(), json_encode($e));
            }
        } else {
            if ($maxNoOfRetriesExceeded) {
                $logMsg = 'Job with id [' . $job->getID() . '] has exceed MaxNoOfRetries -> set to failed';
                $logger->notice($logMsg);
            } elseif ($timeToLiveExceeded) {
                $logMsg = 'Job with id [' . $job->getID() . '] has exceed TTL -> set to failed';
                $logger->notice($logMsg);
            }
            if ($job->getStatus() !== AbstractJob::JOB_STATUS_FAILED) {
                $additionalData = 'Job Payload: ' . json_encode($job->getPayload());
                $exception = null;
                if ($maxNoOfRetriesExceeded) {
                    $exception = new JobFailedAfterTooManyRetriesErrorException(
                        $job->getID(),
                        $job->getQueueName(),
                        $job->getCreationTimestamp(),
                        $job->getTimeToLiveSeconds(),
                        $job->getMaxNoOfRetries(),
                        $additionalData
                    );
                }
                if ($timeToLiveExceeded) {
                    $exception = new JobNotCompletedBeforeTimeoutException(
                        $job->getID(),
                        $job->getQueueName(),
                        $job->getCreationTimestamp(),
                        $job->getTimeToLiveSeconds(),
                        $additionalData,
                        $exception
                    );
                }
                $job->setStatusFailed($exception->getCode(), json_encode($exception));
            }
        }
        return;
    }

    /**
     * @return LoggerInterface|NullLogger
     */
    protected function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $nullLogger = new NullLogger();
            $this->logger = $nullLogger;
        }
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        return;
    }

    /**
     * Extracts and validates data from Payload
     * Must store extracted validated data in $extractedValidatedPayloadData
     * Must throw exception upon failure
     * @param Job $job
     * @return mixed
     * @throws Exception
     */
    abstract protected function handlePayload(Job $job): void;

}