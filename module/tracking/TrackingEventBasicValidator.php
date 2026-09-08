<?php
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 28.02.2017
 * Time: 20:39
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventBasicValidator implements PipelineStage
{

    const TRACKING_EVENT_VAL_ERR_EMPTY = 'Event is empty.';
    const TRACKING_EVENT_VAL_ERR_SUSPICIOUS = 'Event-Data is outlier or malicious. This incident will be reported.';

    const MODE_THROW_INVALID_ARGUMENT_EXCEPTION = 0;
    const MODE_CONTINUE_WITH_NULL_PAYLOAD = 1;


    const HANDLED_QUERY = '
        INSERT INTO
          tracking_event_handling
        SET
          event_uuid = :event_uuid,
          `handler` = :handler_name,
          handled_timestamp = NOW()
    ';


    protected static $allowedModes = [
        self::MODE_THROW_INVALID_ARGUMENT_EXCEPTION,
        self::MODE_CONTINUE_WITH_NULL_PAYLOAD,
    ];

    protected $mode;
    protected $logger;

    public function __construct($mode = 0, LoggerInterface $logger = null)
    {
        $mode = (int)$mode;
        if (!in_array($mode,self::$allowedModes,true)) {
            throw new \InvalidArgumentException('Mode parameter must be among allowed modes.');
        }
        $this->mode = $mode;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    public function handlePayload($payload)
    {
        $this->logger->debug(__CLASS__ . ' checking payload.');
        if (!($payload instanceof  TrackingEvent)) {
            return $payload;
        }
        $eventData = $payload->getEventData();
        $success = $this->checkAndValidateEventNotEmpty($payload);
        if (!$success) { //Most must be 1 else we would have thrown exception already
            $this->logger->debug(__CLASS__ . ' - payload invalid - continuing with null.');
            return null;
        }
        $success = $this->checkAndValidateActiveViewTime($eventData);
        if (!$success) {
            $this->logger->debug(__CLASS__ . ' - payload invalid - continuing with null.');
            return null;
        }
        $success = $this->checkAndValidateLinkedItemIDLength($eventData);
        if (!$success) {
            $this->logger->debug(__CLASS__ . ' - payload invalid - continuing with null.');
            return null;
        }
        $this->logger->debug(__CLASS__ . ' - payload valid - returning payload.');
        return $payload;
    }

    protected function checkAndValidateEventNotEmpty(TrackingEvent $trackingEvent)
    {
        $dataCount = count($trackingEvent->getEventData());
        $sessionID = $trackingEvent->getSessionID();
        $eventType = $trackingEvent->getEventType();
        if (empty($sessionID) || empty($eventType) || !($dataCount > 0)) {
            if (self::MODE_THROW_INVALID_ARGUMENT_EXCEPTION === $this->mode) {
                $this->logger->debug(__CLASS__ . ' - payload invalid - aborting with InvalidArgumentException.');
                throw new \InvalidArgumentException(self::TRACKING_EVENT_VAL_ERR_EMPTY);
            } else {
                return false;
            }
        }
        return true;
    }

    protected function checkAndValidateActiveViewTime(array $eventData)
    {
        if (array_key_exists('active_page_view_time',$eventData) && (int)$eventData['active_page_view_time'] > 129600) { //longer than 1.5 days
            if (self::MODE_THROW_INVALID_ARGUMENT_EXCEPTION === $this->mode) {
                $this->logger->debug(__CLASS__ . ' - payload invalid - aborting with InvalidArgumentException.');
                throw new \InvalidArgumentException(self::TRACKING_EVENT_VAL_ERR_SUSPICIOUS . ' - pageview-time was given as [' . (int)$eventData['active_page_view_time'] . '].');
            } else {
                return false;
            }
        }
        return true;
    }

    protected function checkAndValidateLinkedItemIDLength(array $eventData)
    {
        if(array_key_exists('linked_item_ids',$eventData) && is_array($eventData['linked_item_ids']) && count($eventData['linked_item_ids']) > 80 ) { //plausible value of max 80 viewed items per pag in a long list
            if (self::MODE_THROW_INVALID_ARGUMENT_EXCEPTION === $this->mode) {
                $this->logger->debug(__CLASS__ . ' - payload invalid - aborting with InvalidArgumentException.');
                throw new \InvalidArgumentException(self::TRACKING_EVENT_VAL_ERR_SUSPICIOUS . ' - no of linked item ids was > 80.');
            } else {
                return false;
            }
        }
        return true;
    }
}