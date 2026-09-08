<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.11.2016
 * Time: 14:09
 */

namespace DynCom\dc\workerqueue\workers\videoConversion;


use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use DynCom\dc\workerqueue\workers\ConvertVideosWorker;

/**
 * Class ConvertVideosJobHandler
 * @package DynCom\dc\workerqueue\workers\videoConversion
 */
class ConvertVideosJobHandler implements JobHandler
{
    use jobHandlerTrait;

    public const QUEUE_NAME = 'videoconversion';

    /** @var  ConvertVideosWorker */
    protected $worker;

    /**
     * ConvertVideosJobHandler constructor.
     * @param ConvertVideosWorker $worker
     */
    public function __construct(ConvertVideosWorker $worker)
    {
        $this->worker = $worker;
    }


    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return self::QUEUE_NAME;
    }

    /**
     * @param Job $job
     */
    public function doJob(Job $job): void
    {
       $this->worker->doWork();
    }

    /**
     * @param Job $job
     */
    protected function handlePayload(Job $job): void
    {
       //Nothing to do
    }


}