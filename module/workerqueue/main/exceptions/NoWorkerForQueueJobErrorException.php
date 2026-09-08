<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 11:53
 */
class NoWorkerForQueueJobErrorException extends Exception
{
    const ERROR_CODE = 404;
    private static $messageTemplate = 'No worker can be found for queue with name [%s]. Additional info on the error is [%s]. Please check if the backing-service is running and accepting connections. ';

    /**
     * NoHandlerForQueueJobErrorException constructor.
     * @param string $queueName The name of the queue for which no worker is defined
     * @param string $errorDetails Optional further details for the error
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct($queueName, $errorDetails = '', Exception $previous = null)
    {
        $message = sprintf(self::$messageTemplate, $queueName, $errorDetails);
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}