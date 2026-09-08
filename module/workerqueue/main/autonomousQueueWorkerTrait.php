<?php
declare(strict_types = 1);
declare(ticks=1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2016
 * Time: 11:03
 */

namespace DynCom\dc\workerqueue\main;


use DynCom\dc\workerqueue\processManagement\logsMemoryUsage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class autonomousQueueWorkerTrait
 * @package DynCom\dc\workerqueue\main
 */
trait autonomousQueueWorkerTrait
{
    use logsMemoryUsage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var JobHandler
     */
    protected $jobHandler;
    /**
     * @var JobQueueGateway
     */
    protected $gateway;

    /**
     * @var Job
     */
    protected $currentJob;

    public function manageJobs(): void
    {
        $logger = $this->getLogger();
        $jobHandler = $this->getJobHandler();
        $queueName = $jobHandler->getJobQueueName();
        $gateway = $this->getJobQueueGateWay();
        $sleepDurationSeconds = $this->getWorkerSleepDurationSeconds();
        while (true) {
            pcntl_signal_dispatch();
            $logMsg = 'In class [' . get_class() . '] - getting next open job.';
            $logger->info($logMsg);
            $job = $gateway->getNextOpenJob($queueName);
            $this->currentJob = $job;
            if ($job->getID() > 0) {
                $logMsg = 'Job has id [' . $job->getID() . ']-> attempt processing in handler [' . get_class($jobHandler) . '] ...';
                $logger->info($logMsg);

                $jobHandler->doJob($job);

                $logMsg = 'Finished processing job -> update status...';
                $logger->info($logMsg);

                $gateway->updateJob($job);
                $this->currentJob = null;   //Extremely important, otherwise termination signals to the worker will release the last processed job or set it to failed

                $logMsg = 'Job status updated.';
                $logger->info($logMsg);
            }
            $this->logMemoryUsage($this->logger);
            sleep($sleepDurationSeconds);
        }
        return;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $nullLogger = new NullLogger();
            $this->logger = $nullLogger;
        }
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        return;
    }

    /**
     * @return JobHandler
     */
    protected function getJobHandler(): JobHandler
    {
        return $this->jobHandler;
    }

    /**
     * @param JobHandler $jobHandler
     */
    protected function setJobHandler(JobHandler $jobHandler): void
    {
        $this->jobHandler = $jobHandler;
    }

    /**
     * @return JobQueueGateway
     */
    protected function getJobQueueGateWay(): JobQueueGateway
    {
        return $this->gateway;
    }

    /**
     * @return int
     */
    abstract protected function getWorkerSleepDurationSeconds(): int;

    /**
     * @param JobQueueGateway $gateway
     */
    protected function setGateway(JobQueueGateway $gateway): void
    {
        $this->gateway = $gateway;
        return;
    }

    public function initializeSignalHandling()
    {
        pcntl_signal(SIGHUP, [static::class, 'signalHandlerHup']);
        pcntl_signal(SIGINT, [static::class, 'signalHandlerInt']);
        #pcntl_signal(SIGKILL,[get_class(),'signalHandlerKill']);
        pcntl_signal(SIGTERM, [static::class, 'signalHandlerTerm']);
        return;
    }

    /**
     * @param $signal
     */
    abstract protected function defaultSignalHandler(int $signal): void;

    /**
     * @param int $signal
     */
    abstract protected function signalHandlerHup(int $signal): void;

    /**
     * @param int $signal
     */
    abstract protected function signalHandlerInt(int $signal): void;

    /**
     * @param int $signal
     */
    abstract protected function signalHandlerKill(int $signal): void;

    /**
     * @param int $signal
     */
    abstract protected function signalHandlerTerm(int $signal): void;

    /**
     * @param string $msg
     */
    protected function abortCurrentJob(string $msg = ''): void
    {
        $logger = $this->getLogger();
        $logger->info('Checking for and aborting current Job.');
        if (!$msg) {
            $msg = 'Job aborted for unknown reasons';
        }
        if ($this->currentJob instanceof Job) {
            $this->currentJob->setStatusFailed(500, $msg);
            $this->gateway->updateJob($this->currentJob);
            $logger->info('Current job with id [' . $this->currentJob->getID() . '] aborted.');
        } else {
            $logger->info('No current job to release');
        }
        return;
    }

    protected function releaseCurrentJob(): void
    {
        $logger = $this->getLogger();
        $logger->info('Releasing current Job.');
        if ($this->currentJob instanceof Job) {
            $this->currentJob->setStatusOpen();
            $this->gateway->updateJob($this->currentJob);
            $logger->info('Current job with id [' . $this->currentJob->getID() . '] released.');
        } else {
            $logger->info('No current job to release');
        }
        return;
    }

}
