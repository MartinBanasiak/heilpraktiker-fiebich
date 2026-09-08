<?php
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 11:28
 */
class QueueConnectionErrorException extends Exception
{
    const ERROR_CODE = 501;
    private static $messageTemplate = 'No connection to the worker queue could be established. Connection parameters are [%s]. Additional info on the connection error is [%s]. Please check if the backing-service is running and accepting connections.';

    /**
     * QueueConnectionErrorException constructor.
     * @param array $resourceAccessConfig The connection-parameters for the queue
     * @param string $connectionErrorDetails Optional further details on the error
     * @param Exception|null $previous Optional previous Exception
     */
    public function __construct(array $resourceAccessConfig, $connectionErrorDetails = '', Exception $previous = null)
    {
        if (isset($resourceAccessConfig['pass'])) {
            unset($resourceAccessConfig['pass']);
        }
        $serializedConnectionParameters = print_r($resourceAccessConfig, true);
        $message = sprintf(self::$messageTemplate, $serializedConnectionParameters, $connectionErrorDetails);
        parent::__construct($message, self::ERROR_CODE, $previous);
    }

}