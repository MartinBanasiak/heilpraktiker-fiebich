<?php
declare(strict_types = 1);
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.11.2016
 * Time: 14:50
 */

namespace DynCom\dc\workerqueue\workers\currencyConversion;


use Dotenv\Dotenv;
use DynCom\dc\common\classes\GeneralErrorExceptionHandling;
use DynCom\dc\workerqueue\main\AbstractAutonomousQueueWorker;
use DynCom\dc\workerqueue\main\AutonomousQueueWorker;
use DynCom\dc\workerqueue\main\autonomousQueueWorkerTrait;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(dirname(__DIR__))));
$vendorDir = $baseDir . DIRECTORY_SEPARATOR . 'vendor';
$vendorAutoloaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';
include($vendorAutoloaderPath);


/**
 * Class CurrencyRatesQueueWorker
 * @package DynCom\dc\workerqueue\workers\currencyConversion
 */
class CurrencyRatesQueueWorker implements AutonomousQueueWorker
{

    use autonomousQueueWorkerTrait;

    public const WORKER_SLEEP_DURATION_SECONDS = 30;

    /**
     * CurrencyRatesQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param \DynCom\dc\workerqueue\workers\currencyConversion\FixerIOUpdateCurrencieRatesJobHandler $jobHandler
     */
    public function __construct(JobQueueGateway $gateway, FixerIOUpdateCurrencieRatesJobHandler $jobHandler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($jobHandler);
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
        return;
    }

    /**
     * @param $signal
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


GeneralErrorExceptionHandling::setErrorHandler('', '');
GeneralErrorExceptionHandling::setExceptionHandler('', '');

$logFilePath = $baseDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'CurrencyRatesQueueWorker.log';
$logLevel = getenv('APP_LOG_LEVEL');
$logLevelInt = Logger::toMonologLevel($logLevel);
$logFileHandler = new RotatingFileHandler($logFilePath, 10, $logLevelInt);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('CurrencyRatesQueueWorker', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";


$envDirBase = $baseDir . DIRECTORY_SEPARATOR . 'config';
$envDirWorkerqueue = $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'workerqueue';
$envDirWorker = $envDirWorkerqueue . DIRECTORY_SEPARATOR . 'workers' . DIRECTORY_SEPARATOR . 'currencyConversion';
if (is_dir($envDirBase)) {
    $dotenv = new Dotenv($envDirBase);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenvWorkerQueue = new Dotenv($envDirWorkerqueue);
    $dotenvWorkerQueue->load();
}
if (is_dir($envDirWorker)) {
    $dotenvWorker = new Dotenv($envDirWorker);
    $dotenvWorker->load();
}

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass);

$handlerDBHost = getenv('CURRENCIES_DB_HOST');
$handlerDBPort = getenv('CURRENCIES_DB_PORT');
$handlerDBUser = getenv('CURRENCIES_DB_USER');
$handlerDBPass = getenv('CURRENCIES_DB_PASS');
$handlerDBSchema = getenv('CURRENCIES_DB_SCHEMA');
$handlerDSN = 'mysql:dbname=' . $handlerDBSchema . ';host=' . $handlerDBHost . ';port=' . (int)$handlerDBPort . ';charset=utf8mb4';
$handler = new FixerIOUpdateCurrencieRatesJobHandler($handlerDSN, $handlerDBUser, $handlerDBPass);
$handler->setLogger($logger);


$workerInstance = new CurrencyRatesQueueWorker($gateway, $handler);
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();