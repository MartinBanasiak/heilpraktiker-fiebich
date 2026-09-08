<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 23.01.2017
 * Time: 10:26
 */

namespace DynCom\dc\workerqueue\main;


class NullJobHandler implements JobHandler
{
    /**
     * @param Job $job
     */
    public function doJob(Job $job): void
    {
        // Do nothing
    }

    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return '';
    }

}