<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 13:15
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventCustomerCounterUpdater implements PipelineStage
{
    const CUSTOMER_UPDATE_QUERY = '
        UPDATE 
          customer
        SET 
          customer_total_view_duration_sec = customer_total_view_duration_sec + :additional_view_seconds,
          customer_total_view_count = customer_total_view_duration_sec + :additional_views
        WHERE
          id = :customer_id        
    ';

    protected static $handledEventTypes = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_BASKET,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ORDER,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE,
    ];

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

    public function __construct(\PDO $trackingDBHandler, TrackingAPIService $trackingAPIService, LoggerInterface $logger = null)
    {
        $this->trackingDBHandler = $trackingDBHandler;
        $this->trackingAPIService = $trackingAPIService;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    /**
     * @param $payload
     * @return $payload
     */
    public function handlePayload($payload)
    {
        if (
            !($payload instanceof TrackingEvent)
            || !(in_array($payload->getEventType(), self::$handledEventTypes, true))
            || is_array($payload->getEventData())
        ) {
            return $payload;
        }
        $eventData = $payload->getEventData();
        $customerID = null;

        $additionalViewSeconds = 0;
        $additionalViews = 0;

		$start = null;
        try {
            if (TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE === $payload->getEventType()) {
				$start = microtime(true);
                $referencedEventUUID = $eventData['referenced_pageview_event_uuid'];
                $referencedEvent = $this->trackingAPIService->getTrackingEventByUniqueID($referencedEventUUID);
                if ($referencedEvent) {
                    $referencedEventData = $referencedEvent->getEventData();
                    $customerID = isset($referencedEventData['current_customer_id']) ? $referencedEventData['current_customer_id'] : null;
                    $additionalViewSeconds = (int)$eventData['active_page_view_time'];
                }
            } else {
                $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
                $additionalViews = 1;
            }
            if ($customerID && ($additionalViews || $additionalViewSeconds)) {
                $this->updateCustomer($customerID,$additionalViewSeconds,$additionalViews);
            }

        } catch (\Exception $e) {
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['exception' => ['code' => $e->getCode(), 'msg' => $e->getMessage()], 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not update category-related counters in tracking-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
		if ($start) {
			$duration = microtime(true) - $start;
			$this->logger->debug('Handle pageview-complete in ' . __CLASS__ . '  took ' . $duration . ' seconds');
		}
        return $payload;
    }


    protected function updateCustomer($customerID, $additionalViewSeconds, $additionalViews)
    {
        $stmt = $this->trackingDBHandler->prepare(self::CUSTOMER_UPDATE_QUERY);
        $stmt->bindValue(':customer_id',(int)$customerID,\PDO::PARAM_INT);
        $stmt->bindValue(':additional_view_seconds',(int)$additionalViewSeconds,\PDO::PARAM_INT);
        $stmt->bindValue(':additional_views',(int)$additionalViews,\PDO::PARAM_INT);
        $stmt->execute();
    }

}