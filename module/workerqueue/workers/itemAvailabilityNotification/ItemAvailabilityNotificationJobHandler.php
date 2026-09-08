<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 29.11.2016
 * Time: 19:45
 */

namespace DynCom\dc\workerqueue\workers\itemAvailabilityNotification;


use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use DynCom\dc\workerqueue\workers\ItemAvailabilityNotificationWorker;

/**
 * Class ItemAvailabilityNotificationJobHandler
 * @package DynCom\dc\workerqueue\workers\itemAvailabilityNotification
 */
class ItemAvailabilityNotificationJobHandler implements JobHandler
{

    use jobHandlerTrait;

    public const QUEUE_NAME = 'checkitemavailability';

    /**
     * @var ItemAvailabilityNotificationWorker
     */
    protected $worker;

    /**
     * ItemAvailabilityNotificationJobHandler constructor.
     * @param ItemAvailabilityNotificationWorker $worker
     */
    public function __construct(ItemAvailabilityNotificationWorker $worker)
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
        $this->worker->doWork($job);
    }

    /**
     * @param Job $job
     */
    protected function handlePayload(Job $job): void
    {
        //Nothing to do, no payload needed
    }


}