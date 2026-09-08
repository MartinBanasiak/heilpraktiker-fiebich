<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 15:11
 */
class JobProcessingInvalidBackingServiceResponseErrorException extends Exception
{

    const ERROR_CODE = 501;
    private static $messageTemplate = 'Processing of a job requires a backing service which provided an invalid response. Job-name is [%s]. Queue-name is [%s]. Backing-service name is [%s]. Backing-service response was [%s]. Backing-service connection details are [%s]. Additional info on the connection error is [%s]. Please check if the backing-service is running and accepting connections.';

    /**
     * JobProcessingMissingBackingServiceException constructor.
     * @param mixed $jobID The ID of the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string $backingServiceName The name of the missing backing service
     * @param string $backingServiceResponse The invalid response returned by the backing service
     * @param array $resourceAccessConfig Details about the connection (as string or array)
     * @param string $additionalInfo Optional further details about the error
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(mixed $jobID, $queueName, $backingServiceName, $backingServiceResponse, array $resourceAccessConfig = [], $additionalInfo = '', Exception $previous = null)
    {
        if (isset($resourceAccessConfig['pass'])) {
            unset($resourceAccessConfig['pass']);
        }
        $serializedResourceAccessConfig = print_r($resourceAccessConfig, true);
        $message = sprintf(self::$messageTemplate, $jobID, $queueName, $backingServiceName, $backingServiceResponse, $serializedResourceAccessConfig, $additionalInfo);
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}