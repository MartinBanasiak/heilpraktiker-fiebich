<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:32
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventBasicUserDataCopier implements PipelineStage
{

    const QUERY_EXISTS = '
        SELECT EXISTS(SELECT id FROM user WHERE id = :user_id) AS \'exists\'
    ';

    const QUERY_GET = '
        SELECT
            customer_no            
        FROM
          shop_user
        WHERE
          id = :user_id    
    ';

    const QUERY_INSERT = '
        INSERT INTO
          `user`
        SET
          id = :user_id,
          customer_no = :customer_no
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
            !($payload instanceof TrackingEvent)) {
            return $payload;
        }
        if (
            !(in_array($payload->getEventType(),self::$handledEventTypes,true))
            || !is_array($payload->getEventData())
            || !array_key_exists('current_user_id', $payload->getEventData())
            || !((int)$payload->getEventData()['current_user_id'] > 0)
        ) {
            $this->logger->debug('Payload not handled by class [' . $thisClass. ']');
            return $payload;
        }
        try {
            $userID = (int)$payload->getEventData()['current_user_id'];
            if (!$this->userExistsInTrackingDB($userID)) {
                $this->copyUserDataToTrackingDB($userID);
            }
        } catch (\Exception $e) {
            $errorShopDb = $this->shopDBHandler->errorInfo();
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['shop_db_error' => $errorShopDb, 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not create user data in tracking-db from shop-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
        $this->logger->debug('Class [' . $thisClass . '] payload handled.');
        return $payload;
    }

    protected function userExistsInTrackingDB($id)
    {
        $stmt = $this->trackingDBHandler->prepare(self::QUERY_EXISTS);
        $stmt->bindValue(':user_id',(int)$id,\PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $result = (is_array($result) && array_key_exists('exists',$result) && (bool)$result['exists']);
        return $result;
    }

    protected function copyUserDataToTrackingDB($id)
    {
        $getStmt = $this->shopDBHandler->prepare(self::QUERY_GET);
        $getStmt->bindValue(':user_id',(int)$id,\PDO::PARAM_INT);
        $getStmt->execute();
        $result = $getStmt->fetch(\PDO::FETCH_ASSOC);

        if (array_key_exists('customer_no',$result) && $result['customer_no']) {

            $customerNo = $result['customer_no'];

            $insertStmt = $this->trackingDBHandler->prepare(self::QUERY_INSERT);
            $insertStmt->bindValue(':user_id',(int)$id,\PDO::PARAM_INT);
            $insertStmt->bindValue(':customer_no',$customerNo,\PDO::PARAM_STR);
            $insertStmt->execute();
        }
    }

}