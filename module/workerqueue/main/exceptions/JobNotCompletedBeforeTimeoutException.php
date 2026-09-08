<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 11:59
 */
class JobNotCompletedBeforeTimeoutException extends Exception
{
    const ERROR_CODE = 408;
    private static $messageTemplate = 'A job was not completed before its set timeout. Job-ID is [%d]. Queue-Name is [%s]. Creation-timestamp [%s]. Timeout [%d] milliseconds. Additional info on the error is [%s]. Please check the status and performance of the workers for the queue.';

    /**
     * JobNotCompletedBeforeTimeoutException constructor.
     * @param int $jobID The ID of the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string $creationTimestamp The timestamp (as string) at which the job was created
     * @param int $timeout The time-to-live (in milliseconds) of the job
     * @param string $errorDetails Optional further details on the exception
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(
        $jobID,
        $queueName,
        $creationTimestamp,
        $timeout,
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
            $errorDetails
        );
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}