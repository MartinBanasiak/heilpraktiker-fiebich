<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 25.11.2016
 * Time: 04:54
 */
$baseDir = dirname(dirname(__DIR__));
$vendorDir = $baseDir . '/vendor';
$autoloaderPath = $vendorDir . '/autoload.php';
include($autoloaderPath);

$envDirBase = $baseDir . '/config';
$logsDir = $baseDir . '/logs';
$envDirWorker = $baseDir . '/config/workerqueue';
if (is_dir($envDirBase)) {
    $dotenv = new \Dotenv\Dotenv($envDirBase);
    $dotenv->load();
}
if (is_dir($envDirWorker)) {
    $dotenv = new \Dotenv\Dotenv($envDirWorker);
    $dotenv->load();
}

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');


$logFilePath = $logsDir . '/ServerTestLog.log';
$rotatingFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_DEBUG);
$rotatingFileHandler->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());
$logger = new \Monolog\Logger('ServerTestLog', [$rotatingFileHandler]);
$logger->info('TEST');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new \DynCom\dc\workerqueue\main\PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass, [], $logger);
$adapter = new \DynCom\dc\workerqueue\main\PDOJobQueueGateWayStringAdapter($gateway, $logger);

$jobGateWayDaemonAddr = 'tcp://0.0.0.0:4444';

$tcpServer = new \DynCom\dc\workerqueue\socketServer\TCPStreamSocketServer($adapter, $logger);
$tcpServer->listen($jobGateWayDaemonAddr);

