<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 14:21
 */
class JobProcessingMissingBackingServiceException extends Exception
{
    const ERROR_CODE = 501;
    private static $messageTemplate = 'Processing of a job requires a backing service which is not found. Job-id is [%d]. Queue-name is [%s]. Backing-service name is [%s]. Backing-service connection details are [%s]. Additional info on the connection error is [%s]. Please check if the backing-service is running and accepting connections.';

    /**
     * JobProcessingMissingBackingServiceException constructor.
     * @param int $jobID The ID of the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string $backingServiceName The name of the missing backing service
     * @param array $resourceAccessConfig Details about the connection
     * @param string $additionalInfo Optional further details about the error
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(
        $jobID,
        $queueName,
        $backingServiceName,
        array $resourceAccessConfig = [],
        $additionalInfo = '',
        Exception $previous = null
    )
    {
        if (isset($resourceAccessConfig['pass'])) {
            unset($resourceAccessConfig['pass']);
        }
        $connectionErrorDetailsString = print_r($resourceAccessConfig, 1);

        $message = sprintf(
            self::$messageTemplate,
            $jobID,
            $queueName,
            $backingServiceName,
            $connectionErrorDetailsString,
            $additionalInfo
        );
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}