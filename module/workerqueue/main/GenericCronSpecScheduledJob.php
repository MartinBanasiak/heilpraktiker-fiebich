<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.11.2016
 * Time: 22:02
 */

namespace DynCom\dc\workerqueue\main;

use Cron\CronExpression;
use DateTime;
use DateTimeInterface;
use Throwable;

/**
 * Class GenericCronSpecScheduledJob
 * @package DynCom\dc\workerqueue\main
 */
class GenericCronSpecScheduledJob extends AbstractJob implements CronSpecScheduledJob
{
    protected $schedulingID;
    protected $cronSpec;
    protected $parsedCronSpec;

    /**
     * GenericCronSpecScheduledJob constructor.
     * @param $queueName
     * @param int $creationTimestamp
     * @param int $timeToLive
     * @param int $maxNoOfRetries
     * @param int $noOfUnsuccessfulAttempts
     * @param array $payload
     * @param $cronSpec
     */
    public function __construct(string $queueName, int $creationTimestamp, int $timeToLive, int $maxNoOfRetries, int $noOfUnsuccessfulAttempts, array $payload, string $cronSpec)
    {
        parent::__construct($queueName, $creationTimestamp, $timeToLive, $maxNoOfRetries, $noOfUnsuccessfulAttempts, $payload);
        $this->cronSpec = $cronSpec;
        $this->parsedCronSpec = CronExpression::factory($this->cronSpec);
    }

    /**
     * @param string $json
     * @return CronSpecScheduledJob
     */
    public static function fromJSON(string $json): CronSpecScheduledJob
    {

        $returnVal = new NullCronSpecScheduledJob();

        $arr = [];
        try {
            $arr = json_decode($json, true);
        } catch (Throwable $t) {
            //Do nothing
        } finally {

            $queueName = isset($arr['queueName']) ? $arr['queueName'] : '';
            $id = isset($arr['id']) ? $arr['id'] : 0;
            $payload = isset($arr['payload']) ? $arr['payload'] : [];
            $status = isset($arr['status']) ? $arr['status'] : '';
            $failureCode = isset($arr['failureCode']) ? $arr['failureCode'] : 0;
            $failureData = isset($arr['failureData']) ? $arr['failureData'] : [];
            $creationTimestamp = isset($arr['creationTimestamp']) ? $arr['creationTimestamp'] : 0;
            $lastActionTimestamp = isset($arr['lastActionTimestamp']) ? $arr['lastActionTimestamp'] : null;
            $timeToLiveSeconds = isset($arr['timeToLiveSeconds']) ? $arr['timeToLiveSeconds'] : 0;
            $noOfUnsuccessfulAttempts = isset($arr['noOfUnsuccessfulAttempts']) ? $arr['noOfUnsuccessfulAttempts'] : 0;
            $maxNoOfRetries = isset($arr['maxNoOfRetries']) ? $arr['maxNoOfRetries'] : 0;
            $schedulingID = isset($arr['schedulingID']) ? $arr['schedulingID'] : 0;
            $cronSpec = isset($arr['cronSpec']) ? $arr['cronSpec'] : '';


            if ($queueName && $status && $cronSpec) {

                $obj = new self($queueName, $creationTimestamp, $timeToLiveSeconds, $maxNoOfRetries, $noOfUnsuccessfulAttempts, $payload, $cronSpec);
                $refl = new \ReflectionClass($obj);
                $statusProp = $refl->getProperty('status');
                $statusProp->setAccessible(true);

                switch ($status) {
                    case self::JOB_STATUS_OPEN:
                        $statusProp->setValue(self::JOB_STATUS_OPEN);
                        break;
                    case self::JOB_STATUS_LOCKED:
                        $statusProp->setValue(self::JOB_STATUS_LOCKED);
                        break;
                    case self::JOB_STATUS_FINISHED:
                        $statusProp->setValue(self::JOB_STATUS_FINISHED);
                        break;
                    case self::JOB_STATUS_FAILED:
                        $statusProp->setValue(self::JOB_STATUS_FAILED);
                        $failureCodeProp = $refl->getProperty('failureCode');
                        $failureCodeProp->setAccessible(true);
                        $failureCodeProp->setValue($failureCode);
                        $failureDataProp = $refl->getProperty('failureData');
                        $failureDataProp->setAccessible(true);
                        $failureDataProp->setValue($failureData);
                        break;
                    default:
                        return $returnVal;
                }

                if ($id) {
                    $idProp = $refl->getProperty('id');
                    $idProp->setAccessible(true);
                    $idProp->setValue($id);
                }

                if ($schedulingID) {
                    $schedulingIDProp = $refl->getProperty('schedulingID');
                    $schedulingIDProp->setAccessible(true);
                    $schedulingIDProp->setValue($schedulingID);
                }

                if ($lastActionTimestamp) {
                    $lastActionTimestampProp = $refl->getProperty('lastActionTimestamp');
                    $lastActionTimestampProp->setAccessible(true);
                    $lastActionTimestampProp->setValue($lastActionTimestamp);
                }

                $returnVal = $obj;

            }
            return $returnVal;
        }

    }

    /**
     * @param DateTimeInterface|null $fromDate
     * @param int $skipN
     * @param bool $allowCurrentDT
     * @return DateTime
     */
    public function getNextRunDate(\DateTimeInterface $fromDate = null, int $skipN = 0, bool $allowCurrentDT = false): ?DateTime
    {
        if ($fromDate instanceof \DateTime) {
            $fromDate = \DateTimeImmutable::createFromMutable($fromDate);
        } elseif ($fromDate instanceof \DateTimeImmutable) {
            $fromDate = clone $fromDate;
        } elseif ($fromDate === null) {
            $fromDate = new \DateTimeImmutable();
        }
        return $this->parsedCronSpec->getNextRunDate($fromDate, $skipN, $allowCurrentDT);
    }

    /**
     * @param DateTimeInterface|null $fromDate
     * @return bool
     */
    public function isDue(\DateTimeInterface $fromDate = null): bool
    {
        if ($fromDate instanceof \DateTime) {
            $fromDate = \DateTimeImmutable::createFromMutable($fromDate);
        } elseif ($fromDate instanceof \DateTimeImmutable) {
            $fromDate = clone $fromDate;
        } elseif ($fromDate === null) {
            $fromDate = new \DateTimeImmutable();
        }
        return $this->parsedCronSpec->isDue($fromDate);
    }

    /**
     * @return string
     */
    public function getCronSpec(): string
    {
        return $this->cronSpec;
    }

    /**
     * @return int|null
     */
    public function getSchedulingID(): ?int
    {
        return $this->schedulingID;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $arr = [];
        $refl = new \ReflectionClass($this);
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