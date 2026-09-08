<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\main;

date_default_timezone_set('Europe/Berlin');

use Throwable;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.07.2016
 * Time: 11:15
 */
class GenericJob extends AbstractJob implements Job
{

    /**
     * @param string $json
     * @return Job
     */
    public static function fromJSON(string $json): Job
    {
        $returnVal = new NullJob();

        try {
            $arr = json_decode($json, true);

            $queueName = isset($arr['queueName']) ? $arr['queueName'] : '';
            $id = isset($arr['id']) ? $arr['id'] : 0;
            $payload = isset($arr['payload']) ? $arr['payload'] : [];
            $status = isset($arr['status']) ? $arr['status'] : '';
            $failureCode = isset($arr['failureCode']) ? $arr['failureCode'] : 0;
            $failureData = isset($arr['failureData']) ? $arr['failureData'] : [];
            $creationTimestamp = (int)isset($arr['creationTimestamp']) ? $arr['creationTimestamp'] : 0;
            $lastActionTimestamp = isset($arr['lastActionTimestamp']) ? $arr['lastActionTimestamp'] : null;
            $timeToLiveSeconds = isset($arr['timeToLiveSeconds']) ? $arr['timeToLiveSeconds'] : 0;
            $noOfUnsuccessfulAttempts = isset($arr['noOfUnsuccessfulAttempts']) ? $arr['noOfUnsuccessfulAttempts'] : 0;
            $maxNoOfRetries = isset($arr['maxNoOfRetries']) ? $arr['maxNoOfRetries'] : 0;


            if ($queueName && $status) {

                $obj = new self($queueName, $creationTimestamp, $timeToLiveSeconds, $maxNoOfRetries, $noOfUnsuccessfulAttempts, $payload);
                $refl = new \ReflectionClass($obj);
                $statusProp = $refl->getProperty('status');
                $statusProp->setAccessible(true);

                switch ($status) {
                    case self::JOB_STATUS_OPEN:
                        $statusProp->setValue($obj, self::JOB_STATUS_OPEN);
                        break;
                    case self::JOB_STATUS_LOCKED:
                        $statusProp->setValue($obj, self::JOB_STATUS_LOCKED);
                        break;
                    case self::JOB_STATUS_FINISHED:
                        $statusProp->setValue($obj, self::JOB_STATUS_FINISHED);
                        break;
                    case self::JOB_STATUS_FAILED:
                        $statusProp->setValue($obj, self::JOB_STATUS_FAILED);
                        $failureCodeProp = $refl->getProperty('failureCode');
                        $failureCodeProp->setAccessible(true);
                        $failureCodeProp->setValue($obj, $failureCode);
                        $failureDataProp = $refl->getProperty('failureData');
                        $failureDataProp->setAccessible(true);
                        $failureDataProp->setValue($obj, $failureData);
                        break;
                    default:
                        return $returnVal;
                }

                if ($id) {
                    $idProp = $refl->getProperty('id');
                    $idProp->setAccessible(true);
                    $idProp->setValue($obj, $id);
                }

                if ($lastActionTimestamp) {
                    $lastActionTimestampProp = $refl->getProperty('lastActionTimestamp');
                    $lastActionTimestampProp->setAccessible(true);
                    $lastActionTimestampProp->setValue($obj, $lastActionTimestamp);
                }

                $returnVal = $obj;

            } else {
                $returnVal->queueName_failed_json_decode = $queueName;
                $returnVal->status_failed_json_decode = $status;
                $returnVal->dataArr_failed_json_decode = serialize($arr);
                $returnVal->json_failed_json_decode = $json;
            }
            return $returnVal;

        } catch (Throwable $t) {
            $returnVal->error = $t;
            return new $returnVal;
        }

    }

    /**
     * @param CronSpecScheduledJob $cronSpecScheduledJob
     * @return Job
     */
    public static function fromCronSpecScheduledJob(CronSpecScheduledJob $cronSpecScheduledJob): Job
    {
        $job = new GenericJob($cronSpecScheduledJob->getQueueName(), (int)microtime(true), $cronSpecScheduledJob->getTimeToLiveSeconds(), $cronSpecScheduledJob->getMaxNoOfRetries(), 0, $cronSpecScheduledJob->getPayload());
        return $job;
    }


}