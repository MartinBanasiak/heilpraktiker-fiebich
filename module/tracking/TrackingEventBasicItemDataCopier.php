<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:27
 */

namespace DynCom\dc\tracking;


use DynCom\dc\pipeline\PipelineStage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TrackingEventBasicItemDataCopier implements PipelineStage
{
    const QUERY_EXISTS = '
        SELECT EXISTS (SELECT id FROM item WHERE id = :item_id) as \'exists\'
    ';


    const QUERY_GET_BASIC_ITEM_FIELDS_BY_ID = '
        SELECT
            item_no,
            description,
            summary,
            variant_type,
            retail_price,
            base_price,
            meta_keywords,
            meta_description
        FROM
          shop_item
        WHERE
          id = :item_id       
    ';

    const QUERY_GET_ITEM_ATTRIBUTES_BY_ID = '
         SELECT
          sa.code AS \'attribute_code\',
          CASE WHEN (si.language_code = ss.default_language_code OR sata.id IS NULL) THEN sa.description ELSE sata.description END AS \'attribute_description\',
          CASE 
            WHEN (sa.data_type = 0 AND sato.id IS NULL) THEN sao.description
            WHEN (sa.data_type = 0 AND sato.id > 0) THEN sato.description
            WHEN sa.data_type = 1 THEN sal.value_integer 
            WHEN sa.data_type = 2 THEN sal.value_decimal 
            WHEN sa.data_type = 3 THEN sal.value_bool 
            WHEN (sa.data_type = 4 AND satl.id IS NULL) THEN sal.value_text
            WHEN (sa.data_type = 4 AND satl.id > 0) THEN satl.description 
          END AS \'attribute_value\', 
          sa.data_type AS \'attribute_value_type\',
          sao.link AS \'option_link\',
          sao.filter AS \'filter_expression\'                      
        FROM
          shop_item si
        LEFT JOIN
          shop_shop ss ON (
                ss.company = si.company
            AND ss.code = si.shop_code
          )          
        LEFT JOIN
          shop_attribute_link sal ON (
                sal.company=si.company
            AND sal.no = si.item_no
          )
        INNER JOIN
          shop_attribute sa 
          ON (
              sa.company = sal.company
            AND
              sa.code = sal.attribute_code                                 
          )
        LEFT JOIN
          shop_attribute_option sao
          ON (
                  sa.data_type = 0
              AND sao.company = sa.company
              AND sao.attribute_code = sa.code
              AND sao.code = sal.value_option
          )
        LEFT JOIN 
          shop_attribute_translation sata ##for attribute
          ON (
                sata.company = sa.company
            AND sata.attribute_code = sa.code
            AND sata.language_code =  si.language_code
            AND sata.type = 0
          )
        LEFT JOIN
          shop_attribute_translation satl ##for attribute link
          ON (
                   sata.company = sa.company
            AND satl.attribute_code = sa.code
            AND satl.language_code = si.language_code
            AND satl.attribute_link_no = si.item_no
            AND satl.type = 1       
          )
        LEFT JOIN
          shop_attribute_translation sato ##for attribute option
          ON (
                sato.company = sa.company
            AND sato.attribute_code = sa.code
            AND sato.language_code = si.language_code
            AND sato.attribute_link_no = sao.code
            AND sato.type = 2       
          )
        WHERE
         si.id = :item_id
        ORDER BY 
          sal.sorting ASC        
    ';

    const QUERY_GET_ITEM_TEXT_DESCRIPTIONS_BY_ID = '
        SELECT
          shop_item_description.description,
          shop_item_description.content
        FROM
          shop_item          
        LEFT JOIN
          shop_item_description ON (
                shop_item.company = shop_item_description.company
            AND shop_item.item_no = shop_item_description.item_no
          )
        WHERE
          shop_item.id = :item_id
    ';

    const QUERY_GET_ITEM_REVIEWS_BY_ID = '
        SELECT 
          shop_item_comments.comment
        FROM
         shop_item          
        LEFT JOIN
          shop_item_comments ON (
                shop_item.company = shop_item_comments.company
            AND shop_item.item_no = shop_item_comments.item_no
          )
        WHERE
          shop_item.id = :item_id
    ';

    const QUERY_INSERT_ITEM = '
        INSERT INTO
          item
        SET
          `id`                = :item_id,
          `item_no`           = :item_no,
          `description`       = :description,
          `summary`           = :summary,
          `variant_type`      = :variant_type,
          `retail_price`      = :retail_price,          
          `base_price`        = :base_price,
          `meta_keywords`     = :meta_keywords,
          `meta_description`  = :meta_description,
          `attributes`   = :attributes,
          `descriptions` = :descriptions,
          `reviews`      = :reviews
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
            ||  !is_array($payload->getEventData())
            ||  !(array_key_exists('current_item_id', $payload->getEventData()) || array_key_exists('linked_item_ids',$payload->getEventData()))
        ) {
            $this->logger->debug('Payload not handled by class [' . $thisClass. ']');
            return $payload;
        }
        try {
            $eventData = $payload->getEventData();
            $itemIDs = [];
            if (array_key_exists('current_item_id',$eventData) && (int)$eventData['current_item_id'] > 0) {
                $itemIDs[] = (int)$eventData['current_item_id'];
            }
            if (array_key_exists('linked_item_ids',$eventData) && is_array($eventData['linked_item_ids'])) {
                foreach ($eventData['linked_item_ids'] as $id) {
                    if ((int)$id > 0) {
                        $itemIDs[] = (int)$id;
                    }
                }
            }
            foreach ($itemIDs as $itemID) {
                if (!$this->itemExistsInTrackingDB($itemID)) {
                    $this->copyItemDataToTrackingDB($itemID);
                }
            }

        } catch (\Exception $e) {
            $errorShopDb = $this->shopDBHandler->errorInfo();
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['shop_db_error' => $errorShopDb, 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error('Could not create item data in tracking-db from shop-db. Event: [' . $payload->jsonSerialize() . '], Error: [' . json_encode($errors) . '].');
        }
        $this->logger->debug('Class [' . $thisClass . '] payload handled.');
        return $payload;
    }

    protected function itemExistsInTrackingDB($id)
    {
        $stmt = $this->trackingDBHandler->prepare(self::QUERY_EXISTS);
        $stmt->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $result = (is_array($result) && array_key_exists('exists',$result) && (bool)$result['exists']);
        return $result;
    }

    protected function copyItemDataToTrackingDB($id)
    {
        $getStmtBasic = $this->shopDBHandler->prepare(self::QUERY_GET_BASIC_ITEM_FIELDS_BY_ID);
        $getStmtBasic->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
        $getStmtBasic->execute();
        $basicResult = $getStmtBasic->fetch(\PDO::FETCH_ASSOC);

        $getStmtDescs = $this->shopDBHandler->prepare(self::QUERY_GET_ITEM_TEXT_DESCRIPTIONS_BY_ID);
        $getStmtDescs->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
        $getStmtDescs->execute();
        $descsResult = $getStmtDescs->fetchAll(\PDO::FETCH_ASSOC);

        $getStmtRevs = $this->shopDBHandler->prepare(self::QUERY_GET_ITEM_REVIEWS_BY_ID);
        $getStmtRevs->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
        $getStmtRevs->execute();
        $revsResult = $getStmtRevs->fetchAll(\PDO::FETCH_ASSOC);

        $getStmtAttrs = $this->shopDBHandler->prepare(self::QUERY_GET_ITEM_ATTRIBUTES_BY_ID);
        $getStmtAttrs->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
        $getStmtAttrs->execute();
        $attrsResult = $getStmtAttrs->fetchAll(\PDO::FETCH_ASSOC);

        $itemDescriptionDataJSON = json_encode($descsResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $itemReviewDataJSON = json_encode($revsResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $itemAttributesDataJSON = json_encode($attrsResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

        if (array_key_exists('item_no',$basicResult) && $basicResult['item_no']) {

            $insertStmt = $this->trackingDBHandler->prepare(self::QUERY_INSERT_ITEM);
            $insertStmt->bindValue(':item_id',(int)$id,\PDO::PARAM_INT);
            $insertStmt->bindValue(':item_no',$basicResult['item_no'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':description',$basicResult['description'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':summary',$basicResult['summary'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':variant_type',$basicResult['variant_type'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':retail_price',$basicResult['retail_price'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':base_price',$basicResult['base_price'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':meta_keywords',$basicResult['meta_keywords'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':meta_description',$basicResult['meta_description'],\PDO::PARAM_STR);
            $insertStmt->bindValue(':attributes',$itemAttributesDataJSON,\PDO::PARAM_STR);
            $insertStmt->bindValue(':descriptions',$itemDescriptionDataJSON,\PDO::PARAM_STR);
            $insertStmt->bindValue(':reviews',$itemReviewDataJSON,\PDO::PARAM_STR);
            $insertStmt->execute();
            $err = $insertStmt->errorInfo();
        }
    }
}