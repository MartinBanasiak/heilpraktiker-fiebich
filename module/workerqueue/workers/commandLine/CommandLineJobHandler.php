<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\workers\commandLine;

use DynCom\dc\workerqueue\main\exceptions\InvalidJobPayloadErrorException;
use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.12.2016
 * Time: 15:29
 */
class CommandLineJobHandler implements JobHandler
{
    use jobHandlerTrait;

    const QUEUE_NAME = 'commandline';

    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return self::QUEUE_NAME;
    }

    /**
     * @param Job $job
     * @throws Exception
     */
    public function doJob(Job $job): void
    {
        $logger = $this->getLogger();
        try {
            $this->handlePayload($job);
            $logger->info('Payload handled successfully.');
        } catch (InvalidJobPayloadErrorException $e) {
            $job->setStatusFailed($e->getCode(), json_encode($e));
            $errMsg = $e->getMessage();
            $logger->alert('InvalidJobPayloadErrorException - [' . $errMsg . ']. Job-Status has been set to failed.');
            return;
        }
        $cmd = $this->extractedValidatedPayloadData['command'];

        $logger->info('Executing command [' . $cmd . '] in background.');
        $pid = null;
        try {
            $pid = $this->startProcessInBackground($cmd);
        } catch (\Throwable $t) {
            $msg = 'Could not start Process in background for cmd [' . $cmd . ']. Error msg: [' . $t->getMessage() . ']';
            $logger->warning($msg);
            $job->setStatusFailed(400,[$msg]);
            throw new \ErrorException($msg);
        }
        if (!$pid) {
            $msg = 'Could not start process in background for command [' . $cmd . '].';
            $logger->warning($msg);
            $job->setStatusFailed(400,[$msg]);
            throw new \ErrorException('Could not start command [' . $cmd . '].');
        } else {
            $job->setStatusFinished();
            $logger->info('Started process in background. PID was [' . $pid . '].');
        }
    }

    /**
     * @param string $cmd
     * @return int|null
     */
    public function startProcessInBackground(string $cmd): ?int
    {
        $cmd = '/usr/bin/nohup ' . $cmd . ' >/dev/null 2>&1 & echo $!';
        $pid = (int)exec($cmd);
        return $pid > 0 ? $pid : null;
    }

    /**
     * Extracts and validates data from Payload
     * Must store extracted validated data in $extractedValidatedPayloadData
     * Must throw exception upon failure
     * @param Job $job
     * @return mixed
     * @throws Exception
     */
    protected function handlePayload(Job $job): void
    {
        $this->logger->debug('handlePayload called for CommandLineJobHandler with parameter: [' . serialize($job) . '].');
        try {
            $payload = $job->getPayload();
            if (!array_key_exists('command', $payload) || empty($payload['command'])) {
                $msg = 'Payload for CommandLineJob requires a filled [command] field.';
                $this->logger->warning($msg);
                throw new InvalidJobPayloadErrorException($job->getID(), $job->getQueueName(), $payload, $msg);
            }
            $this->extractedValidatedPayloadData['command'] = $payload['command'];
            $this->logger->debug('Extracted payload is [' . var_export($this->extractedValidatedPayloadData, true) . '].');
        } catch (\Throwable $t) {
            $msg = 'Could not successfully extract payload from job. Error was: [' . $t->getMessage() . '].';
            $this->logger->warning($msg);
            throw new InvalidJobPayloadErrorException($job->getID(), $job->getQueueName(), $job->getPayload(), $msg);
        }
    }


}