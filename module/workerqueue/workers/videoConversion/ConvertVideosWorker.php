<?php
declare(strict_types = 1);
namespace DynCom\dc\workerqueue\workers;

use DynCom\dc\workerqueue\main\GenericJob;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 16:19
 */
class ConvertVideosWorker
{
    public const WORER_QUEUE_NAME = 'convertvideos';

    /**
     * @var JobQueueGateway
     */
    private $jobQueueGateway;

    /**
     * ConvertVideosWorker constructor.
     * @param JobQueueGateway $jobQueueGateway
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        JobQueueGateway $jobQueueGateway,
        LoggerInterface $logger = null
    )
    {
        if (null === $logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
        $this->jobQueueGateway = $jobQueueGateway;
    }

    public function doWork(): void
    {

    }
}