<?php
declare(strict_types = 1);
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.12.2016
 * Time: 16:00
 */

namespace DynCom\dc\workerqueue\workers\commandLine;

use Dotenv\Dotenv;
use DynCom\dc\workerqueue\main\AbstractAutonomousQueueWorker;
use DynCom\dc\workerqueue\main\AutonomousQueueWorker;
use DynCom\dc\workerqueue\main\autonomousQueueWorkerTrait;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(dirname(__DIR__))));
$vendorDir = $baseDir . '/vendor';
$vendorAutoloaderPath = $vendorDir . '/autoload.php';
include($vendorAutoloaderPath);

/**
 * Class CommandLineJobQueueWorker
 * @package DynCom\dc\workerqueue\workers\commandLine
 */
class CommandLineJobQueueWorker implements AutonomousQueueWorker
{

    use autonomousQueueWorkerTrait;

    public const WORKER_SLEEP_DURATION_SECONDS = 5;

    /**
     * CommandLineJobQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param CommandLineJobHandler $handler
     */
    public function __construct(JobQueueGateway $gateway, CommandLineJobHandler $handler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($handler);
    }

    /**
     * @return int
     */
    protected function getWorkerSleepDurationSeconds(): int
    {
        return self::WORKER_SLEEP_DURATION_SECONDS;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerHup(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Hangup-Signal.');
        $this->defaultSignalHandler($signal);
    }

    /**
     * @param int $signal
     */
    protected function defaultSignalHandler(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('In default signal-handler, signal is [' . $signal . ']... proceeding to release current job if exists exit');
        $this->releaseCurrentJob();
        exit(0);
    }

    /**
     * @param $signal
     */
    protected function signalHandlerInt(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Interrupt-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerKill(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Kill-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerTerm(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Termination-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }


}

$logFilePath = $baseDir . '/logs/CommandLineJobHandler.log';
$logLevel = getenv('APP_LOG_LEVEL');
$logLevelInt = Logger::toMonologLevel($logLevel);
$logFileHandler = new RotatingFileHandler($logFilePath, 10, $logLevelInt);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('CommandLineJobHandler', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";


$envDirBase = $baseDir . '/config';
$envDirWorkerqueue = $projectBaseDir . '/config/workerqueue';
if (is_dir($envDirBase)) {
    $dotenv = new Dotenv($envDirBase);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenv = new Dotenv($envDirWorkerqueue);
    $dotenv->load();
}

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass);

$handler = new CommandLineJobHandler();
$handler->setLogger($logger);

ini_set('display_errors', '1');
error_reporting(E_ERROR);

$workerInstance = new CommandLineJobQueueWorker($gateway, $handler);
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();