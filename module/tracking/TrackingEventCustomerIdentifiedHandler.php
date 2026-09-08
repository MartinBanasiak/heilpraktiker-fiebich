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

class TrackingEventCustomerIdentifiedHandler implements PipelineStage
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
            new TrackingEventBasicCustomerDataCopier($this->shopDBHandler, $this->trackingDBHandler),
            new TrackingEventCustomerCounterUpdater($this->trackingDBHandler,$this->trackingAPIService),
            new TrackingEventCustomerItemCounterUpdater($this->trackingDBHandler, $this->trackingAPIService),
            new TrackingEventCustomerCategoryCounterUpdater($this->trackingDBHandler, $this->trackingAPIService),
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
        if ($payload->getEventType() !== TrackingEvent::EVENT_TYPE_CUSTOMER_IDENTIFIED) {
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
        $customerID = isset($eventData['identified_customer_id']) ? (int)$eventData['identified_customer_id'] : null;
        $events = [];
        if ($timestamp && $visitorID && $customerID) {
            $collection = $this->trackingAPIService->getAllVisitorEventsWithoutCustomerBeforeTimestamp(
                $visitorID,
                $timestamp
            );
            $count = count($collection);
            foreach ($collection as $event) {
                if ($event instanceof TrackingEvent) {
                    $event->setCustomerID($customerID);
                    $newEvent = $event->withArrayMergedData(['current_customer_id' => $customerID]);
                    $events[] = $newEvent;
                }
            }
        }
        foreach ($events as $event) {
            $this->pipeline->process($event);
        }
    }


}