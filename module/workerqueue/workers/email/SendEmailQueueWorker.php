<?php
declare(strict_types = 1);
declare(ticks = 1);
namespace DynCom\dc\workerqueue\workers\email;

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
use Swift_Mailer;
use Swift_SmtpTransport;

date_default_timezone_set('Europe/Berlin');
//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(dirname(__DIR__))));
$vendorDir = $baseDir . DIRECTORY_SEPARATOR . 'vendor';
$vendorAutoloaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';
include($vendorAutoloaderPath);


/**
 * Class SendEmailAutonomousQueueWorker
 * @package DynCom\dc\workerqueue\email
 */
class SendEmailAutonomousQueueWorker implements AutonomousQueueWorker
{
    use autonomousQueueWorkerTrait;

    public const SLEEP_DURATION_SECONDS = 5;

    /**
     * SendEmailAutonomousQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param SendEmailJobHandler $jobHandler
     */
    public function __construct(JobQueueGateway $gateway, SendEmailJobHandler $jobHandler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($jobHandler);
    }


    /**
     * @return int
     */
    protected function getWorkerSleepDurationSeconds()
    {
        return self::SLEEP_DURATION_SECONDS;
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

$envDirGlobal = $baseDir . DIRECTORY_SEPARATOR . 'config';
$envDirWorkerqueue = $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'workerqueue';

if (is_dir($envDirGlobal)) {
    $dotenv = new Dotenv($envDirGlobal);
    $dotenv->load();
}

if (is_dir($envDirWorkerqueue)) {
    $dotenvLocal = new Dotenv($envDirWorkerqueue);
    $dotenvLocal->load();
}

GeneralErrorExceptionHandling::setErrorHandler('', '');
GeneralErrorExceptionHandling::setExceptionHandler('', '');

$pdoHost = getenv('WORKERQUEUE_DB_HOST');
$pdoPort = getenv('WORKERQUEUE_DB_PORT');
$pdoUser = getenv('WORKERQUEUE_DB_USER');
$pdoPass = getenv('WORKERQUEUE_DB_PASS');
$pdoSchema = getenv('WORKERQUEUE_DB_SCHEMA');
$pdoDsn = 'mysql:dbname=' . $pdoSchema . ';host=' . $pdoHost . ';port=' . (int)$pdoPort . ';charset=utf8mb4';

$gateway = new PDOJobQueueGateway($pdoDsn, $pdoUser, $pdoPass);

$swiftMailerIP = getenv('MAIN_SMTP_MAILER_IP');
$swiftMailerPort = getenv('MAIN_SMTP_MAILER_PORT');
$swiftTransport = new Swift_SmtpTransport($swiftMailerIP, $swiftMailerPort);
$swiftMailer = new Swift_Mailer($swiftTransport);

$logFilePath = $baseDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'SendEmailQueueWorker.log';
$logLevel = getenv('APP_LOG_LEVEL');
$logLevelInt = Logger::toMonologLevel($logLevel);
$logFileHandler = new RotatingFileHandler($logFilePath, 10, $logLevelInt);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('SendEmailQueueWorkerLog', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";

$handler = new SendEmailJobHandler($swiftMailer);
$handler->setLogger($logger);

$workerInstance = new SendEmailAutonomousQueueWorker($gateway, $handler);
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();
