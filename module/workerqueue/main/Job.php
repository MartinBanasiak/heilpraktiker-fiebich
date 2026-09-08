<?php
namespace DynCom\dc\workerqueue\main;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 11.07.2016
 * Time: 10:59
 */
interface Job
{

    /**
     * Attempts to construct Job from JSON-Representation
     * @param $json string
     * @return Job
     */
    public static function fromJSON(string $json): Job;

    /**
     * @param CronSpecScheduledJob $cronSpecScheduledJob
     * @return Job
     */
    public static function fromCronSpecScheduledJob(CronSpecScheduledJob $cronSpecScheduledJob);

    /**
     * The Fully Qualified Name of the job-queue
     * @return string
     */
    public function getQueueName(): string;

    /**
     * The unique identifier of the job
     * @return int|null
     */
    public function getID(): ?int;

    /**
     * A potential payload as an array
     * @return array
     */
    public function getPayload(): array;

    /**
     * The last set status of the job
     * Must be ENUM('OPEN','LOCKED','FINISHED','FAILED')
     * @return string
     */
    public function getStatus(): string;

    /**
     * Sets the job-status to "OPEN" - the default initial value
     * When re-opening a job after error/exception, increment
     * @return void
     */
    public function setStatusOpen(): void;

    /**
     * Sets the job-status to "LOCKED" - when it is being processed
     * @return void
     */
    public function setStatusLocked(): void;

    /**
     * Sets the job-status to "FINISHED" - when it has been successfully processed
     * @return void
     */
    public function setStatusFinished(): void;

    /**
     * Sets the job-status to "FAILED" - when processing it failed
     * @param int $failureCode
     * @param array $additionalData
     * @return void
     */
    public function setStatusFailed(int $failureCode, array $additionalData = []): void;

    /**
     * Increments the number of unsuccessful attempts at processing the job
     * To be called before re-opening a job after a failed attempt
     * @return void
     */
    public function incrementNoOfUnsuccessfulAttempts(): void;

    /**
     * The code specifying the reason for failure
     * @return int
     */
    public function getFailureCode(): int;

    /**
     * Additional data that can be used to process a failed job
     * @return array
     */
    public function getFailureData(): array;

    /**
     * The timestamp when the job was created
     * @return string
     */
    public function getCreationTimestamp(): string;

    /**
     * The timestamp when the last action (status-change) has been performed
     * @return string
     */
    public function getLastActionTimestamp(): string;

    /**
     * The duration in milliseconds before the job will be released for re-tries
     * A value of zero indicates no limit
     * @return int
     */
    public function getTimeToLiveSeconds(): int;

    /**
     * The number of times an attempt has been made unsuccessfully to complete the job
     * @return int
     */
    public function getNoOfUnsuccessfulAttempts(): int;

    /**
     * The maximum number of attempts to be made to complete the job
     * @return int
     */
    public function getMaxNoOfRetries(): int;

    /**
     * Checks whether timeToLive is not zero and more than timeToLive seconds have passed since the job's creation timestamp
     * @return boolean
     */
    public function hasExceededTimeToLive(): bool;

    /**
     * Checks whether maxNoOfRetries is not zero and noOfUnsuccessfulAttempts is greater or equal maxNoOfRetries
     * @return boolean
     */
    public function hasExceededMaxNoOfRetries(): bool;

}