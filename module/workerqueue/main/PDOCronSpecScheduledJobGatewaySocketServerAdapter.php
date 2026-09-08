<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.12.2016
 * Time: 14:58
 */

namespace DynCom\dc\workerqueue\main;


use DynCom\dc\workerqueue\processManagement\managesProcesses;
use DynCom\dc\workerqueue\socketServer\ServiceSocketServerAdapter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class PDOCronSpecScheduledJobGatewaySocketServerAdapter
 * @package DynCom\dc\workerqueue\main
 */
class PDOCronSpecScheduledJobGatewaySocketServerAdapter implements ServiceSocketServerAdapter
{
    use managesProcesses;

    public const CHARSEQ_MSG_START = '-#?#-';
    public const CHARSEQ_MSG_TERM = '-#!#-';

    protected const P_GETNEXTOPEN = '/GETNEXT\s+([a-zA-Z_.]+)/';
    protected const P_GETBYID = '/GET\s+(0-9+)/';
    protected const P_UPDATE = '/UPDATE\s+(.*)/';
    protected const P_CREATE = '/CREATE\s+(.*)/';

    /**
     * @var CronSpecScheduledJobGateway
     */
    protected $gateway;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PDOCronSpecScheduledJobGatewaySocketServerAdapter constructor.
     * @param CronSpecScheduledJobGateway $gateway
     * @param LoggerInterface|null $logger
     */
    public function __construct(CronSpecScheduledJobGateway $gateway, LoggerInterface $logger = null)
    {
        $this->gateway = $gateway;
        $this->logger = null !== $logger ? $logger : new NullLogger();
    }

    /**
     * Has to parse the message, extract the method of the adapted service to call,
     * extract and check the parameters of the method to call, call it and return a potential return value as string
     * @param string $message
     * @return string
     */
    public function adaptMessageForService(string $message): string
    {
        $this->logger->debug('Method [' . __METHOD__ . '] called with parameter [' . $message . '].');
        $matchesGetNextOpen = [];

        $matchesCreate = [];
        $matchesUpdate = [];

        $returnValue = '';

        //Preg match is expensive but simple to implement so as to match commands and extract parameters
        //Thus, the order of pattern matching attempts reflects the frequency of use of the methods in normal operation
        if (preg_match(self::P_GETNEXTOPEN, $message, $matchesGetNextOpen)) {

            $command = isset($matchesGetNextOpen[1]) ? $matchesGetNextOpen[1] : '';
            $queueName = isset($matchesGetNextOpen[2]) ? $matchesGetNextOpen[2] : '';
            if ($command && $queueName) {

                $pid = ($this->getPIDsForPath(realpath(__DIR__) . __FILE__))[0];
                $job = $this->gateway->getNextScheduledJobForPID((int)$pid, []);
                $returnValue = json_encode($job);
            } else {

            }
        } elseif (preg_match(self::P_UPDATE, $message, $matchesUpdate)) {

            $jsonJob = isset($matchesUpdate[1]) ? $matchesUpdate[1] : '';
            if ($jsonJob) {
                $job = GenericCronSpecScheduledJob::fromJSON($jsonJob);
                $this->gateway->updateScheduledJob($job);
            } else {

            }
        } elseif (preg_match(self::P_CREATE, $message, $matchesCreate)) {

            $jsonJob = isset($matchesCreate[1]) ? $matchesCreate[1] : '';
            if ($jsonJob) {
                $job = GenericCronSpecScheduledJob::fromJSON($jsonJob);
                $this->logger->debug('Attempting to create job from json.');
                $this->logger->debug('JSON was [' . $jsonJob . ']');
                $this->logger->debug('Serialized parsed job: [' . serialize($job) . '].');
                $this->gateway->createScheduledJob($job);
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