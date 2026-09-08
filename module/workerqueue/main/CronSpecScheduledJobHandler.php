<?php
declare(strict_types = 1);
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 11.11.2016
 * Time: 11:26
 */

namespace DynCom\dc\workerqueue\main;


use DateTimeInterface;
use Dotenv\Dotenv;
use DynCom\dc\workerqueue\processManagement\logsMemoryUsage;
use DynCom\dc\workerqueue\processManagement\managesProcesses;
use Exception;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

date_default_timezone_set('Europe/Berlin');

//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(__DIR__)));
$vendorDir = $baseDir . '/vendor';
$vendorAutoloaderPath = $vendorDir . '/autoload.php';
include($vendorAutoloaderPath);


/**
 * Class CronSpecScheduledJobHandler
 * @package DynCom\dc\workerqueue\main
 */
class CronSpecScheduledJobHandler
{
    use logsMemoryUsage, managesProcesses;

    protected const CYCLE_SLEEP_DURATION_SECONDS = 60;

    protected $pid;
    /**
     * @var CronSpecScheduledJobGateway
     */
    protected $scheduledJobGateway;

    /**
     * @var JobQueueGateway
     */
    protected $jobQueueGateway;

    /**
     * @var $jobsByNextRunTime array
     */
    protected $jobsByNextRunTime = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * CronSpecScheduledJobHandler constructor.
     * @param int $pid
     * @param CronSpecScheduledJobGateway $scheduledJobGateway
     * @param JobQueueGateway $jobQueueGateway
     * @param LoggerInterface|null $logger
     */
    public function __construct(int $pid, CronSpecScheduledJobGateway $scheduledJobGateway, JobQueueGateway $jobQueueGateway, LoggerInterface $logger = null)
    {
        $this->pid = $pid;
        $this->scheduledJobGateway = $scheduledJobGateway;
        $this->jobQueueGateway = $jobQueueGateway;
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    public function handleScheduledJobs(): void
    {
        while (true) {
            pcntl_signal_dispatch();
            $this->loadJobsFromGateway();
            $currDT = new \DateTimeImmutable();
            $currDTString = $currDT->format('Y-m-d H:i');
            $jobsToEnqueue = $this->getJobsToRunAtDateTime($currDT);
            $jobCount = count($jobsToEnqueue);

            $logMsg = 'Job Schedule Handler cycle at [' . $currDT->format('Y-m-d H:i:s') . '] - there are [' . $jobCount . '] number of jobs to enqueue this minute.';
            $this->logger->info($logMsg);

            /**
             * @var $job CronSpecScheduledJob
             */
            $enqueuedJobs = 0;
            foreach ($jobsToEnqueue as $job) {
                $schedulingID = $job->getSchedulingID();
                try {
                    $genericJob = GenericJob::fromCronSpecScheduledJob($job);
                    $genericJob->setStatusOpen();
                    $this->jobQueueGateway->createJob($genericJob);
                    $logMsg = 'Job with scheduling-id [' . $schedulingID . '] has been placed on queue by scheduler.';
                    $this->logger->info($logMsg);
                    $enqueuedJobs++;
                } catch (Exception $e) {
                    $logMsg = 'Error creating queue-entry for job with id [' . $schedulingID . ']. ErrorInfo: [' . json_encode($e) . '].';
                    $this->logger->error($logMsg);
                }
                //unset in array
                unset($this->jobsByNextRunTime[$currDTString][(string)$schedulingID]);
                $nextRunDT = null;
                try {
                    $nextRunDT = $job->getNextRunDate($currDT);
                } catch (Exception $e) {
                    //Do nothing, no valid next run datetime
                }

                if ($nextRunDT === null || $nextRunDT <= $currDT) {
                    $logMsg = 'Job with id [' . $schedulingID . '] has no (valid) next time to run and will be deleted.';
                    $this->logger->info($logMsg);
                    $this->scheduledJobGateway->deleteScheduledJobByID($schedulingID);
                } else {
                    //Store again under new run DT
                    $nextRunDTString = $nextRunDT->format('Y-m-d H:i');
                    $logMsg = 'Next datetime for job to run is [' . $nextRunDTString . ']';
                    $this->logger->info($logMsg);
                    $this->jobsByNextRunTime[$nextRunDTString][(string)$schedulingID] = $job;
                }
            }
            if ($enqueuedJobs !== $jobCount) {
                $logMsg = 'Scheduled job handling result: Only [' . $enqueuedJobs . '] of [' . $jobCount . '] could be successfully enqueued.';
                $this->logger->error($logMsg);
            } else {
                $logMsg = 'Scheduled job handling result: All jobs successfully enqueued';
                $this->logger->info($logMsg);
            }

            $this->logMemoryUsage($this->logger);

            sleep(self::CYCLE_SLEEP_DURATION_SECONDS);
        }
        return;
    }

    protected function loadJobsFromGateway(): void
    {
        $this->logger->info('Fetching own PIDs...');
        $ownPids = $this->getPIDsForPath(__FILE__);
        $ownPidsStr = implode(',', $ownPids);
        $this->logger->info('Own PIDs are [' . $ownPidsStr . ']');
        do {
            $job = $this->scheduledJobGateway->getNextScheduledJobForPID((int)$this->pid, $ownPids);
            if ($job instanceof GenericCronSpecScheduledJob) {
                $this->logger->info('The following job has been fetched from db: [' . var_export($job, true) . '].');
                $jobID = $job->getSchedulingID();
                $cronSpec = $job->getCronSpec();
                $logMsg = 'CronSpec of the job is [' . $cronSpec . '].';
                $this->logger->info($logMsg);
                $nextRunTime = $job->getNextRunDate();
                $nextRunTimeStr = $nextRunTime->format('Y-m-d H:i');
                $this->logger->info('The job will next run at \DateTime [' . $nextRunTimeStr . '].');
                if (!array_key_exists($nextRunTimeStr, $this->jobsByNextRunTime) || !array_key_exists($jobID, $this->jobsByNextRunTime[$nextRunTimeStr])) {
                    $this->jobsByNextRunTime[$nextRunTimeStr][(string)$jobID] = $job;
                }
            }
        } while (!($job instanceof NullCronSpecScheduledJob));
        return;
    }

    /**
     * @param DateTimeInterface $dateTime
     * @return array
     */
    protected function getJobsToRunAtDateTime(\DateTimeInterface $dateTime): array
    {
        $currDTString = $dateTime->format('Y-m-d H:i');
        $arr = [];
        if (array_key_exists($currDTString, $this->jobsByNextRunTime) && is_array($this->jobsByNextRunTime[$currDTString])) {
            $arr = $this->jobsByNextRunTime[$currDTString];
        }
        return $arr;
    }

    /**
     * @param int $signal
     */
    protected function signalHandlerHup(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Hangup-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param int $signal
     */
    protected function defaultSignalHandler(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('In default signal-handler, signal is [' . $signal . ']... proceeding to release current job if exists exit');
        $this->releaseJobs();
        exit(0);
    }

    protected function releaseJobs(): void
    {
        $this->scheduledJobGateway->releaseJobsFromPID($this->pid);
        return;
    }

    /**
     * @param int $signal
     */
    protected function signalHandlerInt(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Interrupt-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param int $signal
     */
    protected function signalHandlerKill(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Kill-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param int $signal
     */
    protected function signalHandlerTerm(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Termination-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

}


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

$pdoHost = getenv('WORKERQUEUE_DB_HOST');
$pdoPort = getenv('WORKERQUEUE_DB_PORT');
$pdoUser = getenv('WORKERQUEUE_DB_USER');
$pdoPass = getenv('WORKERQUEUE_DB_PASS');
$pdoSchema = getenv('WORKERQUEUE_DB_SCHEMA');
$pdoDsn = 'mysql:dbname=' . $pdoSchema . ';host=' . $pdoHost . ';port=' . (int)$pdoPort . ';charset=utf8mb4';


$pid = getmypid();


$logFilePath = $baseDir . '/logs/CronSpecScheduledJobHandler.log';
$logFileHandler = new RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('ScheduledJobLog', [$logFileHandler]);
echo "Logging to [$logFilePath] (Rotating)";


$jobQueueGateway = new PDOJobQueueGateway($pdoDsn, $pdoUser, $pdoPass);
$jobScheduleGateway = new PDOCronSpecScheduledJobGateway($pdoDsn, $pdoUser, $pdoPass, [], $logger);

$scheduledJobHandler = new CronSpecScheduledJobHandler($pid, $jobScheduleGateway, $jobQueueGateway, $logger);
$scheduledJobHandler->handleScheduledJobs();
