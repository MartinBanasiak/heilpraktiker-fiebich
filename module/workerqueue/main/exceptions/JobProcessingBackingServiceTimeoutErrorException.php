<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 15:21
 */
class JobProcessingBackingServiceTimeoutErrorException extends Exception
{
    const ERROR_CODE = 504;
    private static $messageTemplate = 'Processing of a job requires a backing service which failed to provide a valid response in time. Job-name is [%s]. Queue-name is [%s]. Backing-service name is [%s]. Timeout was after [%d] seconds. Backing-service connection details are [%s]. Additional info on the connection error is [%s]. Please check the logs of the backing service.';

    /**
     * JobProcessingMissingBackingServiceException constructor.
     * @param mixed $jobID The ID of the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string $backingServiceName The name of the missing backing service
     * @param int $backingServiceTimeout The number of seconds after which the timeout was triggered
     * @param array $resourceAccessConfig Details about the connection
     * @param string $additionalInfo Optional further details about the error
     * @param Exception|null $previous Optional previous \Exception
     */
    public function __construct(mixed $jobID, $queueName, $backingServiceName, $backingServiceTimeout, array $resourceAccessConfig = [], $additionalInfo = '', Exception $previous = null)
    {
        if (isset($resourceAccessConfig['pass'])) {
            unset($resourceAccessConfig['pass']);
        }
        $serializedResourceAccessConfig = print_r($resourceAccessConfig, true);
        $message = sprintf(self::$messageTemplate, (string)$jobID, (string)$queueName, (string)$backingServiceName, (int)$backingServiceTimeout, (string)$serializedResourceAccessConfig, (string)$additionalInfo);
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}