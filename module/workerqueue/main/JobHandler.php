<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 19.10.2016
 * Time: 11:07
 */

namespace DynCom\dc\workerqueue\main;


/**
 * Interface JobHandler
 * Implementations must check queuename (and thus job-type) of job, ttl, max_attempts and payload,
 * attempt to perform the job and set the job's status accordingly
 * @package DynCom\dc\workerqueue
 */
interface JobHandler
{
    /**
     * @param Job $job
     */
    public function doJob(Job $job): void;

    /**
     * @return string
     */
    public function getJobQueueName(): string;
}