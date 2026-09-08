<?php
namespace DynCom\dc\tracking;

use Dotenv\Dotenv;
use DynCom\dc\pipeline\GenericPipeline;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * Created by PhpStorm.
 * User: alsotohy
 * Date: 2/21/2017
 * Time: 11:20 AM
 */
ini_set('display_errors',1);

include_once 'tracking_functions.php';
include_once 'TrackingEventHandler.php';
include_once 'TrackingEvent.php';
include_once 'TrackingAPIService.php';
include_once 'TrackingAPIController.php';

//Directories
$rootDir = dirname(dirname(__DIR__));
$vendorDir = $rootDir . DIRECTORY_SEPARATOR . 'vendor';
$globalConfigDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
$globalLogsDir = $rootDir . DIRECTORY_SEPARATOR . 'logs';
$globalEnvFilePath = $globalConfigDir . DIRECTORY_SEPARATOR . '.env';
$trackingConfigDir = $globalConfigDir . DIRECTORY_SEPARATOR . 'tracking';
$trackingEnvFilePath = $trackingConfigDir . DIRECTORY_SEPARATOR . '.env';
$moduleDir = $rootDir . DIRECTORY_SEPARATOR . 'module';
$trackingDir = $moduleDir . DIRECTORY_SEPARATOR . 'tracking';
$logFilePath = $rootDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'tracking_index.log';


//Vendor autoload
require_once $vendorDir . '/autoload.php';

//Logging
$logLevel = Logger::ERROR;
$envLogLevel = getenv('LOGLEVEL_GLOBAL');
if ($envLogLevel) {
    $logLevel = $envLogLevel;
}
$logFileHandler = new RotatingFileHandler($logFilePath, 10, 'DEBUG');
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('TrackingIndex', [$logFileHandler]);


$service = new TrackingAPIService();
$controller = new TrackingAPIController($service,$logger);

$controller->handleTrackingRequest();