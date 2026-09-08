<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 13:15
 */
class InvalidJobPayloadErrorException extends Exception
{
    const ERROR_CODE = 422;

    private static $messageTemplate = 'A job has invalid payload-data and cannot be processed. Job-ID is [%s]. Queue-Name is [%s]. Payload is [%s]. Additional info on the error is [%s]. Please check the status and performance of the workers for the queue.';

    /**
     * InvalidJobPayloadErrorException constructor.
     * @param mixed $jobID The identifier for the job
     * @param string $queueName The name of the queue from which the job was taken
     * @param string|array $payload The job's payload (as json-string or array)
     * @param string $errorDetails Optional further details on the error
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(mixed $jobID, string $queueName, array $payload, string $errorDetails = '', Exception $previous = null)
    {
        if (is_array($payload)) {
            $payload = print_r($payload, 1);
        }
        $message = sprintf(self::$messageTemplate, (string)$jobID, (string)$queueName, (string)$payload, (string)$errorDetails);
        parent::__construct($message, self::ERROR_CODE, $previous);

    }
}