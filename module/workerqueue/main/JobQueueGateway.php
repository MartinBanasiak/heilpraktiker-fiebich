<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 11.07.2016
 * Time: 14:11
 */
interface JobQueueGateway
{
    /**
     * @param string $queueName
     * @return Job
     */
    public function getNextOpenJob(string $queueName): Job;


    /**
     * @param string $queueName
     * @return Job
     */
    public function getNextFailedJob(string $queueName): Job;

    /**
     * @param string $queueName
     * @return Job
     */
    public function getNextFinishedJob(string $queueName): Job;

    /**
     * @param string $queueName
     * @return Job
     */
    public function getNextLockedJob(string $queueName): Job;

    /**
     * @param Job $job
     */
    public function updateJob(Job $job): void;

    /**
     * @param Job $job
     */
    public function createJob(Job $job): void;

    public function getNextFailedJobAllQueues() : Job;

}