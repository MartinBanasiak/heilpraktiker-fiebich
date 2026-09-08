<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 16:57
 */
class JobFailedAfterTooManyRetriesErrorException extends Exception
{
    const ERROR_CODE = 500;

    private static $messageTemplate = 'A job has undergone too many unsuccessful attempts at processing. Job-ID is [%d]. Queue-Name is [%s]. Creation-timestamp [%s]. Timeout [%d] milliseconds. No of retries is [%d]. Additional info on the error is [%s].';

    /**
     * JobFailedAfterTooManyRetriesErrorException constructor.
     * @param int $jobID The ID of the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string $creationTimestamp The timestamp (as string) at which the job was created
     * @param int $timeout The time-to-live (in milliseconds) of the job
     * @param int $noOfRetries The number of times processing for the job has been attempted unsuccessfully
     * @param string $errorDetails Optional further details on the exception
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(
        $jobID,
        $queueName,
        $creationTimestamp,
        $timeout,
        $noOfRetries,
        $errorDetails = '',
        Exception $previous = null
    )
    {
        $message = sprintf(
            self::$messageTemplate,
            $jobID,
            $queueName,
            $creationTimestamp,
            $timeout,
            $noOfRetries,
            $errorDetails
        );
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}