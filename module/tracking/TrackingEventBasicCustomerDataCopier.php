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

class TrackingEventBasicCustomerDataCopier implements PipelineStage
{
    const EXISTS_QUERY = '
        SELECT EXISTS(SELECT id FROM customer WHERE id = :customer_id) AS \'exists\';
    ';

    const GET_QUERY = '
        SELECT
            customer_no,
            post_code,
            city,
            country
        FROM
          shop_customer
        WHERE
          id = :customer_id   
    ';

    const INSERT_QUERY = '
        INSERT INTO
          `customer`
        SET
          `id` = :customer_id,
          `customer_no` = :customer_no,
          `post_code_anon` = :customer_post_code_anon,
          `city` = :customer_city,
          `country` = :customer_country
    ';

    protected static $handledEventTypes = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_BASKET,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ORDER,
        TrackingEvent::EVENT_TYPE_BASKET_ADD,
        TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE,
        TrackingEvent::EVENT_TYPE_BASKET_REMOVE,
        TrackingEvent::EVENT_TYPE_ORDER_COMPLETE,
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
        if (!$payload instanceof TrackingEvent) {
            return $payload;
        }

        if (
            !(in_array($payload->getEventType(), self::$handledEventTypes, true))
            || !is_array($payload->getEventData())
            || !array_key_exists('current_customer_id', $payload->getEventData())
            || !((int)$payload->getEventData()['current_customer_id'] > 0)
        ) {
            $this->logger->debug('Payload not handled by class [' . $thisClass . ']');
            return $payload;
        }
        try {
            $customerID = (int)$payload->getEventData()['current_customer_id'];
            if (!$this->customerExistsInTrackingDB($customerID)) {
                $this->copyCustomerDataToTrackingDB($customerID);
            }
        } catch (\Exception $e) {
            $errorShopDb = $this->shopDBHandler->errorInfo();
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['shop_db_error' => $errorShopDb, 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not create customer data in tracking-db from shop-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
        $this->logger->debug('Class [' . $thisClass . '] payload handled.');
        return $payload;
    }

    protected function customerExistsInTrackingDB($id)
    {
        $stmt = $this->trackingDBHandler->prepare(self::EXISTS_QUERY);
        $stmt->bindValue(':customer_id', (int)$id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $result = (is_array($result) && array_key_exists('exists', $result) && (bool)$result['exists']);
        return $result;
    }

    protected function copyCustomerDataToTrackingDB($id)
    {
        $getStmt = $this->shopDBHandler->prepare(self::GET_QUERY);
        $getStmt->bindValue(':customer_id', (int)$id, \PDO::PARAM_INT);
        $getStmt->execute();
        $result = $getStmt->fetch(\PDO::FETCH_ASSOC);

        if (array_key_exists('customer_no', $result) && $result['customer_no']) {

            $customerNo = $result['customer_no'];
            $customerPostCodeAnon = $this->anonymizePostCode($result['post_code']);
            $customerCity = $result['city'];
            $customerCountry = $result['country'];

            $insertStmt = $this->trackingDBHandler->prepare(self::INSERT_QUERY);
            $insertStmt->bindValue(':customer_id', (int)$id, \PDO::PARAM_INT);
            $insertStmt->bindValue(':customer_no', $customerNo, \PDO::PARAM_STR);
            $insertStmt->bindValue(':customer_post_code_anon', $customerPostCodeAnon, \PDO::PARAM_STR);
            $insertStmt->bindValue(':customer_city', $customerCity, \PDO::PARAM_STR);
            $insertStmt->bindValue(':customer_country', $customerCountry, \PDO::PARAM_STR);
            $insertStmt->execute();
        }
    }

    protected function anonymizePostCode($postCode)
    {
        $strlen = strlen($postCode);
        $anonLength = floor(0.8 * $strlen);
        $result = substr($postCode, 0, $anonLength);
        return $result;
    }

}