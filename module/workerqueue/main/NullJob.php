<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.07.2016
 * Time: 11:22
 */
class NullJob extends AbstractJob implements Job
{
    /**
     * NullJob constructor.
     */
    public function __construct()
    {
        parent::__construct('', 0, 0, 0, 0, []);
        $this->status = '';
    }

    /**
     * @param string $json
     * @return Job
     */
    public static function fromJSON(string $json): Job
    {
        return new self();
    }

    /**
     * @param CronSpecScheduledJob $cronSpecScheduledJob
     * @return NullJob
     */
    public static function fromCronSpecScheduledJob(CronSpecScheduledJob $cronSpecScheduledJob)
    {
        return new NullJob();
    }

    public function setStatusOpen(): void
    {
        return;
    }

    public function setStatusLocked(): void
    {
        return;
    }

    public function setStatusFinished(): void
    {
        return;
    }

    /**
     * @param int $failureCode
     * @param array $additionalData
     */
    public function setStatusFailed(int $failureCode, array $additionalData = []): void
    {
        return;
    }

    protected function setLastActionTimestampToCurrentTime(): void
    {
        return;
    }


}