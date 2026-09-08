<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 24.11.2016
 * Time: 19:18
 */

namespace DynCom\dc\workerqueue\main;


use DynCom\dc\workerqueue\socketServer\ServiceSocketServerAdapter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class PDOJobQueueGateWaySocketServerAdapter
 * @package DynCom\dc\workerqueue\main
 */
class PDOJobQueueGateWayStringAdapter implements ServiceSocketServerAdapter
{
    public const CHARSEQ_MSG_START = '-#?#-';
    public const CHARSEQ_MSG_TERM = '-#!#-';

    protected const P_GETNEXTOPEN = '/((?:GET)|(?:PEEK))NEXTOPEN\s+([a-zA-Z_.]+)/';
    protected const P_GETNEXTLOCKED = '/GETNEXTLOCKED\s+([a-zA-Z_.]+)/';
    protected const P_GETNEXTFINISHED = '/GETNEXTFINISHED\s+([a-zA-Z_.]+)/';
    protected const P_GETNEXTFAILED = '/GETNEXTFAILED\s+([a-zA-Z_.]+)/';
    protected const P_UPDATE = '/UPDATE\s+(.*)/';
    protected const P_CREATE = '/CREATE\s+(.*)/';


    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PDOJobQueueGateway
     */
    protected $gateway;

    /**
     * PDOJobQueueGateWayStringAdapter constructor.
     * @param PDOJobQueueGateway $gateway
     * @param LoggerInterface|null $logger
     */
    public function __construct(PDOJobQueueGateway $gateway, LoggerInterface $logger = null)
    {
        $this->gateway = $gateway;
        $this->logger = $logger !== null ? $logger : new NullLogger();
        $this->logger->debug('TEST ' . get_class($this));
    }

    /**
     * @return \Closure
     */
    public function getSingleStringCallable(): \Closure
    {
        return function ($message) {
            return $this->adaptMessageForService($message);
        };
    }

    /**
     * @param string $message
     * @return string
     */
    public function adaptMessageForService(string $message): string
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameter [' . $message . '].');
        $matchesGetNextOpen = [];
        $matchesGetNextLocked = [];
        $matchesGetNextFinished = [];
        $matchesGetNextFailed = [];
        $matchesCreate = [];
        $matchesUpdate = [];

        $returnValue = '';

        //Preg match is expensive but simple to implement so as to match commands and extract parameters
        //Thus, the order of pattern matching attempts reflects the frequency of use of the methods in normal operation
        if (preg_match(self::P_GETNEXTOPEN, $message, $matchesGetNextOpen)) {

            $command = isset($matchesGetNextOpen[1]) ? $matchesGetNextOpen[1] : '';
            $queueName = isset($matchesGetNextOpen[2]) ? $matchesGetNextOpen[2] : '';
            if ($command && $queueName) {
                $peek = ('PEEK' === $command);
                $job = $this->gateway->getNextOpenJob($queueName, $peek);
                $returnValue = json_encode($job);
            } else {

            }
        } elseif (preg_match(self::P_UPDATE, $message, $matchesUpdate)) {

            $jsonJob = isset($matchesUpdate[1]) ? $matchesUpdate[1] : '';
            if ($jsonJob) {
                $job = GenericJob::fromJSON($jsonJob);
                $this->gateway->updateJob($job);
            } else {

            }
        } elseif (preg_match(self::P_CREATE, $message, $matchesCreate)) {

            $jsonJob = isset($matchesCreate[1]) ? $matchesCreate[1] : '';
            if ($jsonJob) {
                $job = GenericJob::fromJSON($jsonJob);
                $this->logger->debug('Attempting to create job from json.');
                $this->logger->debug('JSON was [' . $jsonJob . ']');
                $this->logger->debug('Serialized parsed job: [' . serialize($job) . '].');
                $this->gateway->createJob($job);
            } else {

            }
        } elseif (preg_match(self::P_GETNEXTFAILED, $message, $matchesGetNextFailed)) {

            $queueName = isset($matchesGetNextFailed[1]) ? $matchesGetNextFailed[1] : '';
            if ($queueName) {
                $job = $this->gateway->getNextFailedJob($queueName);
                $returnValue = json_encode($job);
            } else {

            }
        } elseif (preg_match(self::P_GETNEXTFINISHED, $message, $matchesGetNextFinished)) {

            $queueName = isset($matchesGetNextFinished[1]) ? $matchesGetNextFinished[1] : '';
            if ($queueName) {
                $job = $this->gateway->getNextFinishedJob($queueName);
                $returnValue = json_encode($job);
            } else {

            }
        } elseif (preg_match(self::P_GETNEXTLOCKED, $message, $matchesGetNextLocked)) {

            $queueName = isset($matchesGetNextLocked[1]) ? $matchesGetNextLocked[1] : '';
            if ($queueName) {
                $job = $this->gateway->getNextLockedJob($queueName);
                $returnValue = json_encode($job);
            } else {

            }
        } else {
            throw new \InvalidArgumentException('Could not parse message [' . $message . '].');
        }
        if (!$returnValue) {
            $returnValue = 'default return value';
        }
        $returnValue = $this->getRawMessageStartSequence() . $returnValue . $this->getRawMessageTerminationSequence();
        $this->logger->debug('Method [' . __METHOD__ . '] returned [' . $returnValue . '].');
        return $returnValue;
    }

    /**
     * @return string
     */
    public function getRawMessageStartSequence(): string
    {
        return self::CHARSEQ_MSG_START;
    }

    /**
     * @return string
     */
    public function getRawMessageTerminationSequence(): string
    {
        return self::CHARSEQ_MSG_TERM;
    }


}