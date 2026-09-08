<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.11.2016
 * Time: 22:56
 */
namespace DynCom\dc\workerqueue\main;

/**
 * Interface CronSpecScheduledJobGateway
 * @package DynCom\dc\workerqueue\main
 */
interface CronSpecScheduledJobGateway
{
    /**
     * @param int $pid
     * @param array $excludedPids
     * @return CronSpecScheduledJob
     */
    public function getNextScheduledJobForPID(int $pid, array $excludedPids): CronSpecScheduledJob;

    /**
     * @param CronSpecScheduledJob $job
     */
    public function createScheduledJob(CronSpecScheduledJob $job): void;

    /**
     * @param int $id
     */
    public function deleteScheduledJobByID(int $id): void;

    /**
     * @param CronSpecScheduledJob $job
     */
    public function updateScheduledJob(CronSpecScheduledJob $job): void;

    /**
     * @param int $pid
     */
    public function releaseJobsFromPID(int $pid): void;
}