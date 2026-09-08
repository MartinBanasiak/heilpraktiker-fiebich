<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 10.11.2016
 * Time: 16:28
 */
namespace DynCom\dc\workerqueue\main\exceptions;

use Exception;

/**
 * Class JobScheduleResourceErrorException
 * @package DynCom\dc\workerqueue\main\exceptions
 */
class JobScheduleResourceErrorException extends Exception
{
    const ERROR_CODE = 501;

    private static $messageTemplate = 'Could not access Job Schedule Resource. Resource location/connection info is [%s]. Additional info on the connection error is [%s]. Please check if the backing-service is running and accepting connections.';

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
        $serializedResourceAccessConfig = print_r($resourceAccessConfig, true);
        $message = sprintf(self::$messageTemplate, $serializedResourceAccessConfig, $connectionErrorDetails);
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}