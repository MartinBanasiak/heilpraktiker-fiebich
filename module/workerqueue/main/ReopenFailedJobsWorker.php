<?php
declare(strict_types = 1);
declare(ticks=1);
namespace DynCom\dc\workerqueue\main;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 23.01.2017
 * Time: 10:24
 */

use Dotenv\Dotenv;
use DynCom\dc\common\classes\GeneralErrorExceptionHandling;
use DynCom\dc\workerqueue\main\exceptions\QueueConnectionErrorException;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
$baseDir = dirname(dirname(dirname(__DIR__)));
$vendorDir = $baseDir . '/vendor';
require $vendorDir . '/autoload.php';

class ReopenFailedJobsWorker implements AutonomousQueueWorker
{
    use autonomousQueueWorkerTrait {
        autonomousQueueWorkerTrait::manageJobs as traitManageJobs;
        }


    public const WORKER_SLEEP_DURATION_SECONDS = 10;

    /**
     * ReopenFailedJobsQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param NullJobHandler $handler
     */
    public function __construct(JobQueueGateway $gateway, NullJobHandler $handler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($handler);
    }

    public function manageJobs(): void
    {
        $sleepDuration = $this->getWorkerSleepDurationSeconds();
        while (true) {
            do {
                $failedJob = $this->gateway->getNextFailedJobAllQueues();
                if (!($failedJob instanceof NullJob) && !$failedJob->hasExceededMaxNoOfRetries()) {
                    $failedJob->setStatusOpen();
                    $this->gateway->updateJob($failedJob);
                    $this->logger->info('Reopened failed job with id [' . $failedJob->getID() . '].');
                }
            } while (!($failedJob instanceof NullJob));
            $this->logMemoryUsage($this->logger,Logger::INFO);
            sleep($sleepDuration);
        }

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
        $logger->info('In default signal-handler, signal is [' . $signal . ']... non-queue worker, exiting');
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

$envDirGlobal = $baseDir . '/config';
$envDirWorkerqueue = $baseDir . '/config/workerqueue';

if (is_dir($envDirGlobal)) {
    $dotenv = new Dotenv($envDirGlobal);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenv = new Dotenv($envDirWorkerqueue);
    $dotenv->load();
}


$logFilePath = $baseDir . '/logs/ReopenFailedJobsWorker.log';
$logFileHandler = new RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('ReopenFailedJobsWorker', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass,[]);

$workerInstance = new ReopenFailedJobsWorker($gateway, new NullJobHandler());
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();