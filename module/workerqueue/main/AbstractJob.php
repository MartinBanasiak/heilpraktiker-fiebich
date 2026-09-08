<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;


use JsonSerializable;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 11.07.2016
 * Time: 14:22
 */
abstract class AbstractJob implements JsonSerializable
{

    public const JOB_STATUS_OPEN = 'OPEN';
    public const JOB_STATUS_LOCKED = 'LOCKED';
    public const JOB_STATUS_FINISHED = 'FINISHED';
    public const JOB_STATUS_FAILED = 'FAILED';

    protected $queueName;
    protected $id;
    protected $payload = [];
    protected $status;
    protected $failureCode;
    protected $failureData = [];
    protected $creationTimestamp;
    protected $lastActionTimestamp;
    protected $timeToLiveSeconds;
    protected $noOfUnsuccessfulAttempts;
    protected $maxNoOfRetries;

    /**
     * AbstractJob constructor.
     * @param $queueName
     * @param int $creationTimestamp
     * @param int $timeToLive
     * @param int $maxNoOfRetries
     * @param int $noOfUnsuccessfulAttempts
     * @param array $payload
     */
    public function __construct(string $queueName, int $creationTimestamp = 0, int $timeToLive = 0, int $maxNoOfRetries = 0, int $noOfUnsuccessfulAttempts = 0, ?array $payload = [])
    {
        $this->queueName = $queueName;
        $this->timeToLiveSeconds = $timeToLive;
        $this->maxNoOfRetries = $maxNoOfRetries;
        $this->noOfUnsuccessfulAttempts = $noOfUnsuccessfulAttempts;
        $this->payload = $payload;
        $this->creationTimestamp = $creationTimestamp === 0 ? (string)(int)microtime(true) : (string)(int)$creationTimestamp;
        $this->lastActionTimestamp = $this->creationTimestamp;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @return int
     */
    public function getID(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return (array)$this->payload;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatusOpen(): void
    {
        $this->status = static::JOB_STATUS_OPEN;
        $this->setLastActionTimestampToCurrentTime();
        return;
    }

    protected function setLastActionTimestampToCurrentTime(): void
    {
        $this->lastActionTimestamp = microtime(true);
        return;
    }

    public function setStatusLocked(): void
    {
        $this->status = static::JOB_STATUS_LOCKED;
        $this->setLastActionTimestampToCurrentTime();
        return;
    }

    public function setStatusFinished(): void
    {
        $this->status = static::JOB_STATUS_FINISHED;
        $this->setLastActionTimestampToCurrentTime();
        return;
    }

    /**
     * @param int $failureCode
     * @param array $additionalData
     */
    public function setStatusFailed(int $failureCode, array $additionalData = []): void
    {
        $this->status = static::JOB_STATUS_FAILED;
        $this->failureCode = $failureCode;
        $this->failureData = $additionalData;
        $this->incrementNoOfUnsuccessfulAttempts();
        $this->setLastActionTimestampToCurrentTime();
        return;
    }

    public function incrementNoOfUnsuccessfulAttempts(): void
    {
        $this->noOfUnsuccessfulAttempts = ((int)$this->noOfUnsuccessfulAttempts) + 1;
        return;
    }

    /**
     * @return int
     */
    public function getFailureCode(): int
    {
        return (int)$this->failureCode;
    }

    /**
     * @return array
     */
    public function getFailureData(): array
    {
        return $this->failureData;
    }

    /**
     * @return string
     */
    public function getCreationTimestamp(): string
    {
        return (string)$this->creationTimestamp;
    }

    /**
     * @return string
     */
    public function getLastActionTimestamp(): string
    {
        return (string)$this->lastActionTimestamp;
    }

    /**
     * @return int
     */
    public function getTimeToLiveSeconds(): int
    {
        return $this->timeToLiveSeconds;
    }

    /**
     * @return bool
     */
    public function hasExceededTimeToLive(): bool
    {
        $currentTime = microtime(true);
        return ($this->timeToLiveSeconds !== 0 && ($currentTime - $this->creationTimestamp > $this->timeToLiveSeconds));
    }

    /**
     * @return int
     */
    public function getNoOfUnsuccessfulAttempts(): int
    {
        return $this->noOfUnsuccessfulAttempts;
    }

    /**
     * @return int
     */
    public function getMaxNoOfRetries(): int
    {
        return $this->maxNoOfRetries;
    }

    /**
     * @return bool
     */
    public function hasExceededMaxNoOfRetries(): bool
    {
        return ($this->maxNoOfRetries !== 0 && $this->noOfUnsuccessfulAttempts >= $this->maxNoOfRetries);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $arr = [];
        $refl = new ReflectionClass($this);
        $props = $refl->getProperties();
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $val = $prop->getValue($this);
            $arr[$name] = $val;
        }
        return $arr;
    }


}