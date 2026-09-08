<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.11.2016
 * Time: 22:39
 */

namespace DynCom\dc\workerqueue\main;


use DateTimeInterface;

/**
 * Class NullCronSpecScheduledJob
 * @package DynCom\dc\workerqueue\main
 */
class NullCronSpecScheduledJob implements CronSpecScheduledJob
{
    /**
     * @param string $json
     * @return CronSpecScheduledJob
     */
    public static function fromJSON(string $json): CronSpecScheduledJob
    {
        return new self();
    }

    /**
     * @param DateTimeInterface|null $fromDate
     * @param int $skipN
     * @param bool $allowCurrentDT
     * @return \DateTime|null
     */
    public function getNextRunDate(\DateTimeInterface $fromDate = null, int $skipN = 0, bool $allowCurrentDT = false): ?\DateTime
    {
        return null;
    }

    /**
     * @param DateTimeInterface|null $fromDate
     * @return bool
     */
    public function isDue(\DateTimeInterface $fromDate = null): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return '';
    }

    /**
     * @return int|null
     */
    public function getID(): ?int
    {
        return null;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return '';
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

    public function incrementNoOfUnsuccessfulAttempts(): void
    {
        return;
    }

    /**
     * @return int
     */
    public function getFailureCode(): int
    {
        return 0;
    }

    /**
     * @return array
     */
    public function getFailureData(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getCreationTimestamp(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLastActionTimestamp(): string
    {
        return '';
    }

    /**
     * @return int
     */
    public function getTimeToLiveSeconds(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getNoOfUnsuccessfulAttempts(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getMaxNoOfRetries(): int
    {
        return 0;
    }

    /**
     * @return bool
     */
    public function hasExceededTimeToLive(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasExceededMaxNoOfRetries(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getCronSpec(): string
    {
        return '';
    }

    /**
     * @return int|null
     */
    public function getSchedulingID(): ?int
    {
        return null;
    }


}