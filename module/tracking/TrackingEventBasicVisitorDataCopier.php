<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:31
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventBasicVisitorDataCopier implements PipelineStage
{
    protected const QUERY_EXISTS = '
        SELECT EXISTS(SELECT id FROM visitor WHERE id = :visitor_id) as \'exists\'
    ';

    protected const QUERY_GET = '
        SELECT
          session_id,
          last_ipv4_anon,
          last_ipv6_anon,
          session_date            
        FROM
          main_visitor
        WHERE
          id = :visitor_id   
    ';

    protected const QUERY_INSERT = '
        INSERT INTO 
          visitor
        SET
          id = :visitor_id,
          session_id = :session_id,
          last_ipv4_anon = :ipv4_anon,
          last_ipv6_anon = :ipv6_anon,
          session_date = :session_date          
    ';

    protected static $handledEventTypes = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_BASKET,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ORDER,
    ];

    /**
     * @var \PDO
     */
    protected $shopDBHandler;
    /**
     * @var \PDO
     */
    protected $trackingDBHandler;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    public function __construct(\PDO $shopDBHandler, \PDO $trackingDBHandler, LoggerInterface $logger = null)
    {
        $this->shopDBHandler = $shopDBHandler;
        $this->trackingDBHandler = $trackingDBHandler;
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
        $thisClass = get_class($this);
        $this->logger->debug('Class [' . $thisClass . '] checking payload [' . var_export($payload,1) . '].');
        if (
            !($payload instanceof TrackingEvent)
            || !(in_array($payload->getEventType(),self::$handledEventTypes,true))
            || !is_array($payload->getEventData())
            || !array_key_exists('current_visitor_id', $payload->getEventData())
            || !((int)$payload->getEventData()['current_visitor_id'] > 0)
        ) {
            $this->logger->debug('Payload not handled by class [' . $thisClass. ']');
            return $payload;
        }
        try {
            $visitorID = (int)$payload->getEventData()['current_visitor_id'];
            if (!$this->visitorExistsInTrackingDB($visitorID)) {
                $this->copyVisitorDataToTrackingDB($visitorID);
            }
        } catch (\Exception $e) {
            $errorShopDb = $this->shopDBHandler->errorInfo();
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['shop_db_error' => $errorShopDb, 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not create visitor data in tracking-db from shop-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
        $this->logger->debug('Class [' . $thisClass . '] payload handled.');
        return $payload;
    }

    protected function visitorExistsInTrackingDB($id)
    {
        $stmt = $this->trackingDBHandler->prepare(self::QUERY_EXISTS);
        $stmt->bindValue(':visitor_id',(int)$id,\PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $result = (is_array($result) && array_key_exists('exists',$result) && (bool)$result['exists']);
        return $result;
    }

    protected function copyVisitorDataToTrackingDB($id)
    {
        $getStmt = $this->shopDBHandler->prepare(self::QUERY_GET);
        $getStmt->bindValue(':visitor_id',(int)$id,\PDO::PARAM_INT);
        $getStmt->execute();
        $result = $getStmt->fetch(\PDO::FETCH_ASSOC);
        if (array_key_exists('session_id',$result) && $result['session_id']) {

            $sessionID = $result['session_id'];
            $ipv4 = $result['last_ipv4_anon'];
            $ipv6 = $result['last_ipv6_anon'];
            $sessionDate = $result['session_date'];

            $insertStmt = $this->trackingDBHandler->prepare(self::QUERY_INSERT);
            $insertStmt->bindValue(':visitor_id',(int)$id,\PDO::PARAM_INT);
            $insertStmt->bindValue(':session_id',$sessionID,\PDO::PARAM_STR);
            $insertStmt->bindValue(':ipv4_anon',$ipv4,\PDO::PARAM_STR);
            $insertStmt->bindValue(':ipv6_anon',$ipv6,\PDO::PARAM_STR);
            $insertStmt->bindValue(':session_date',$sessionDate,\PDO::PARAM_STR);
            $insertStmt->execute();
            $err = $insertStmt->errorInfo();
        }
    }

}