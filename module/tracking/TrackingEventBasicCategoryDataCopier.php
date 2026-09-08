<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:30
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventBasicCategoryDataCopier implements PipelineStage
{
    const EXISTS_QUERY = '
        SELECT EXISTS(SELECT id FROM category WHERE id = :category_id) AS \'exists\';
    ';

    const GET_QUERY = '
       SELECT
            name,
            code,
            category_description,
            category_description_2,
            meta_keywords,
            meta_description
        FROM
          shop_category
        WHERE
          id = :category_id       
    ';

    const INSERT_QUERY = '
        INSERT INTO
            category
        SET
            `id` =  :category_id,
            `name` = :category_name,
            `code` = :category_code,
            `category_description` = :category_description,
            `category_description_2` = :category_description_2,
            `meta_keywords` = :category_meta_keywords,
            `meta_description` = :category_meta_description
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
        ) {
            return $payload;
        }
        if (
            !(in_array($payload->getEventType(), self::$handledEventTypes, true))
            || !is_array($payload->getEventData())
            || !array_key_exists('current_category_id', $payload->getEventData())
            || !((int)$payload->getEventData()['current_category_id'] > 0)
        ) {
            $this->logger->debug('Payload not handled by class [' . $thisClass . ']');
            return $payload;
        }
        try {
            $categoryID = (int)$payload->getEventData()['current_category_id'];
            if (!$this->categoryExistsInTrackingDB($categoryID)) {
                $this->copyCategoryDataToTrackingDB($categoryID);
            }
        } catch (\Exception $e) {
            $errorShopDb = $this->shopDBHandler->errorInfo();
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['shop_db_error' => $errorShopDb, 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not create category data in tracking-db from shop-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
        $this->logger->debug('Class [' . $thisClass . '] payload handled.');
        return $payload;
    }

    protected function categoryExistsInTrackingDB($id)
    {
        $stmt = $this->trackingDBHandler->prepare(self::EXISTS_QUERY);
        $stmt->bindValue(':category_id', (int)$id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $result = (is_array($result) && array_key_exists('exists', $result) && (bool)$result['exists']);
        return $result;
    }

    protected function copyCategoryDataToTrackingDB($id)
    {
        $getStmt = $this->shopDBHandler->prepare(self::GET_QUERY);
        $getStmt->bindValue(':category_id', (int)$id, \PDO::PARAM_INT);
        $getStmt->execute();
        $result = $getStmt->fetch(\PDO::FETCH_ASSOC);

        if (array_key_exists('code', $result) && $result['code']) {

            $categoryCode = $result['code'];
            $categoryName = $result['name'];
            $categoryDescription = $result['category_description'];
            $categoryDescription2 = $result['category_description_2'];
            $metaKeywords = $result['meta_keywords'];
            $metaDescription = $result['meta_description'];

            $insertStmt = $this->trackingDBHandler->prepare(self::INSERT_QUERY);
            $insertStmt->bindValue(':category_id', (int)$id, \PDO::PARAM_INT);
            $insertStmt->bindValue(':category_code', $categoryCode, \PDO::PARAM_STR);
            $insertStmt->bindValue(':category_name', $categoryName, \PDO::PARAM_STR);
            $insertStmt->bindValue(':category_description', $categoryDescription, \PDO::PARAM_STR);
            $insertStmt->bindValue(':category_description_2', $categoryDescription2, \PDO::PARAM_STR);
            $insertStmt->bindValue(':category_meta_keywords', $metaKeywords, \PDO::PARAM_STR);
            $insertStmt->bindValue(':category_meta_description', $metaDescription, \PDO::PARAM_STR);
            $insertStmt->execute();
        }

    }


}