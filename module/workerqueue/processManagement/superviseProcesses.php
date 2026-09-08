<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\processManagement;

use Dotenv\Dotenv;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
$projectBaseDir = dirname(dirname(dirname(__DIR__)));
$vendorDir = $projectBaseDir . DIRECTORY_SEPARATOR . 'vendor';

require $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';

$envDirGlobal = $projectBaseDir . DIRECTORY_SEPARATOR . 'config';
$envDirWorkerqueue = $projectBaseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'workerqueue';

if (is_dir($envDirGlobal)) {
    $dotenv = new Dotenv($envDirGlobal);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenv = new Dotenv($envDirWorkerqueue);
    $dotenv->load();
}


$logFilePath = $projectBaseDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'superviseProcesses.log';
$logFileHandler = new RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('ProcessSupervisorLog', [$logFileHandler]);
echo "Logging to [$logFilePath] (Rotating)";



$supervisor = new ProcessSupervisor($logger);
$supervisor->superviseProcesses();