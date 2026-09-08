<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 04.03.2017
 * Time: 14:03
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\GenericPipeline;
use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventUserIdentifiedHandler implements PipelineStage
{

    /**
     * @var \PDO
     */
    protected $shopDBHandler;

    /**
     * @var \PDO
     */
    protected $trackingDBHandler;

    /**
     * @var TrackingAPIService
     */
    protected $trackingAPIService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $pipeline;

    public function __construct(
        \PDO $shopDBHandler,
        \PDO $trackingDBHandler,
        TrackingAPIService $trackingAPIService,
        LoggerInterface $logger = null
    ) {
        $this->shopDBHandler = $shopDBHandler;
        $this->trackingDBHandler = $trackingDBHandler;
        $this->trackingAPIService = $trackingAPIService;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
        $stages = [
            new TrackingEventBasicUserDataCopier($this->shopDBHandler, $this->trackingDBHandler),
            new TrackingEventUserCounterUpdater($this->trackingDBHandler,$this->trackingAPIService),
            new TrackingEventUserItemCounterUpdater($this->trackingDBHandler, $this->trackingAPIService),
            new TrackingEventUserCategoryCounterUpdater($this->trackingDBHandler, $this->trackingAPIService),
        ];

        $this->pipeline = new GenericPipeline($stages);
    }

    /**
     * @param $payload
     * @return $payload
     */
    public function handlePayload($payload)
    {
        if (!($payload instanceof TrackingEvent)) {
            return $payload;
        }
        if ($payload->getEventType() !== TrackingEvent::EVENT_TYPE_USER_IDENTIFIED) {
            return $payload;
        }
        $this->handleEvent($payload);
        return $payload;
    }

    public function handleEvent(TrackingEvent $trackingEvent)
    {
        $eventData = $trackingEvent->getEventData();
        $timestamp = $trackingEvent->getCreatedTimestamp();
        $visitorID = isset($eventData['current_visitor_id']) ? (int)$eventData['current_visitor_id'] : null;
        $userID = isset($eventData['identified_user_id']) ? (int)$eventData['identified_user_id'] : null;
        $events = [];
        if ($timestamp && $visitorID && $userID) {
            $collection = $this->trackingAPIService->getAllVisitorEventsWithoutUserBeforeTimestamp($visitorID,$timestamp);
            foreach ($collection as $event) {
                if ($event instanceof TrackingEvent) {
                    $event->setUserID($userID);
                    $newEvent = $event->withArrayMergedData(['current_user_id' => $userID]);
                    $events[] = $newEvent;
                }
            }
        }
        foreach ($events as $event) {
            $this->pipeline->process($event);
        }
    }


}