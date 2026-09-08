<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 11.07.2016
 * Time: 10:27
 */
abstract class AbstractAutonomousQueueWorker
{

    use autonomousQueueWorkerTrait;

    /**
     * AbstractAutonomousQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param JobHandler $handler
     */
    public function __construct(JobQueueGateway $gateway, JobHandler $handler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($handler);
    }

}


