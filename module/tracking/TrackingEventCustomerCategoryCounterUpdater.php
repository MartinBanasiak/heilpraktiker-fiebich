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

class TrackingEventCustomerCategoryCounterUpdater implements PipelineStage
{
    const CUSTOMER_UPDATE_QUERY = '
        UPDATE
          `customer`
        SET
          `category_view_duration_seconds` = `category_view_duration_seconds` + :additional_view_seconds,
          `category_view_counter` = `category_view_counter` + :additional_views
        WHERE
          customer_id = :customer_id
    ';

    const CUSTOMER_CATEGORY_UPDATE_QUERY = '
        INSERT INTO 
          customer_category
        SET 
          customer_id = :customer_id,
          category_id = :category_id,
          view_duration_seconds            = view_duration_seconds + :additional_view_seconds,
          view_counter                     = view_counter + :additional_views,
          item_add_to_basket_counter       = item_add_to_basket_counter + :additional_basket_additions,
          item_total_add_to_basket_qty     = item_total_add_to_basket_qty + :additional_basket_add_qty,
          item_total_add_to_basket_value   = item_total_add_to_basket_value + :additional_basket_add_value,
          item_rm_from_basket_counter      = item_rm_from_basket_counter + :additional_basket_removals, 
          item_total_rm_from_basket_qty    = item_total_rm_from_basket_qty + :additional_basket_rm_qty, 
          item_total_rm_from_basket_value  = item_total_rm_from_basket_value + :additional_basket_rm_value,
          item_order_counter               = item_order_counter + :additional_item_order_count, 
          item_total_order_qty             = item_total_order_qty + :additional_item_order_qty,
          item_total_order_value           = item_total_order_value + :additional_item_order_value
        ON DUPLICATE KEY UPDATE 
          view_duration_seconds            = view_duration_seconds + :additional_view_seconds,
          view_counter                     = view_counter + :additional_views,
          item_add_to_basket_counter       = item_add_to_basket_counter + :additional_basket_additions,
          item_total_add_to_basket_qty     = item_total_add_to_basket_qty + :additional_basket_add_qty,
          item_total_add_to_basket_value   = item_total_add_to_basket_value + :additional_basket_add_value,
          item_rm_from_basket_counter      = item_rm_from_basket_counter + :additional_basket_removals, 
          item_total_rm_from_basket_qty    = item_total_rm_from_basket_qty + :additional_basket_rm_qty, 
          item_total_rm_from_basket_value  = item_total_rm_from_basket_value + :additional_basket_rm_value,
          item_order_counter               = item_order_counter + :additional_item_order_count, 
          item_total_order_qty             = item_total_order_qty + :additional_item_order_qty,
          item_total_order_value           = item_total_order_value + :additional_item_order_value
    ';

    protected static $handledEventTypes = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE,
        TrackingEvent::EVENT_TYPE_BASKET_ADD,
        TrackingEvent::EVENT_TYPE_BASKET_REMOVE,
        TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE,
        TrackingEvent::EVENT_TYPE_ORDER_COMPLETE,
    ];

    const KEY_VISITOR_ID = 'visitor_id';
    const KEY_CURRENT_CATEGORY_ID = 'current_category_id';
    const KEY_CURRENT_ITEM_ID = 'current_item_id';
    const KEY_CURRENT_VISITOR_ID = 'current_visitor_id';
    const KEY_CURRENT_USER_ID = 'current_user_id';
    const KEY_CURRENT_CUSTOMER_ID = 'current_customer_id';
    const KEY_ORDER_ITEMS = 'order_items';
    const KEY_ITEM_ID = 'item_id';
    const KEY_QTY = 'qty';
    const KEY_UNIT_PRICE = 'unit_price';
    const KEY_VALUE = 'value';
    const KEY_NO_ITEMS_ADDED = 'no_items_added';
    const KEY_OLD_QTY = 'old_qty';
    const KEY_NEW_QTY = 'new_qty';
    const KEY_ACTIVE_PAGE_VIEW_TIME = 'active_page_view_time';
    const KEY_REFERENCED_PAGEVIEW_EVENT_UUID = 'referenced_pageview_event_uuid';
    const KEY_ADDITIONAL_VIEW_SECONDS = 'additional_view_seconds';
    const KEY_ADDITIONAL_VIEWS = 'additional_views';
    const KEY_ADDITIONAL_BASKET_ADDITIONS = 'additional_basket_additions';
    const KEY_ADDITIONAL_BASKET_ADD_QTY = 'additional_basket_add_qty';
    const KEY_ADDITIONAL_BASKET_ADD_VALUE = 'additional_basket_add_value';
    const KEY_ADDITIONAL_BASKET_REMOVALS = 'additional_basket_removals';
    const KEY_ADDITIONAL_BASKET_RM_QTY = 'additional_basket_rm_qty';
    const KEY_ADDITIONAL_BASKET_RM_VALUE = 'additional_basket_rm_value';
    const KEY_ADDITIONAL_ITEM_ORDER_COUNT = 'additional_item_order_count';
    const KEY_ADDITIONAL_ITEM_ORDER_QTY = 'additional_item_order_qty';
    const KEY_ADDITIONAL_ITEM_ORDER_VALUE = 'additional_item_order_value';
    const KEY_CATEGORY_ID = 'category_id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_USER_ID = 'user_id';

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

    public function __construct(
        \PDO $trackingDBHandler,
        TrackingAPIService $trackingAPIService,
        LoggerInterface $logger = null
    ) {
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
            || !is_array($payload->getEventData())
        ) {
            return $payload;
        }

        $eventType = $payload->getEventType();
        try {

            switch ($eventType) {
                case TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE:
                    $this->handlePageviewCompleteEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY:
                    $this->handlePageviewCategory($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_ADD:
                    $this->handleBasketAddEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE:
                    $this->handleBasketQtyChangeEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_REMOVE:
                    $this->handleBasketRemoveEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_ORDER_COMPLETE:
                    $this->handleOrderCompleteEvent($payload);
                    break;
            }

        } catch (\Exception $e) {
            $errorTrackingDb = $this->trackingDBHandler->errorInfo();
            $errors = ['exception' => ['code' => $e->getCode(), 'msg' => $e->getMessage(
            )], 'tracking_db_error' => $errorTrackingDb];
            $this->logger->error(
                'Could not update category-related counters in tracking-db. Event: [' . $payload->jsonSerialize(
                ) . '], Error: [' . json_encode($errors) . '].'
            );
        }
        return $payload;
    }

    protected function updateCustomer($customerID, $additionalViewSeconds, $additionalViews)
    {
        $stmt = $this->trackingDBHandler->prepare(self::CUSTOMER_UPDATE_QUERY);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_ID, (int)$customerID, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_VIEW_SECONDS, (int)$additionalViewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_VIEWS, (int)$additionalViews, \PDO::PARAM_INT);
        $stmt->execute();
    }

    protected function updateCustomerCategory(
        $customerID,
        $categoryID,
        $additionalViewSeconds,
        $additionalViews,
        $additionalBasketAdditions = 0,
        $additionalBasketAddQty = 0.00,
        $additionalBasketAddValue = 0.00,
        $additionalBasketRemovals = 0,
        $additionalBasketRMQty = 0.00,
        $additionalBasketRMValue = 0.00,
        $additionalItemOrderCount = 0,
        $additionalItemOrderQty = 0.00,
        $additionalItemOrderValue = 0.00
    ) {
        $stmt = $this->trackingDBHandler->prepare(self::CUSTOMER_CATEGORY_UPDATE_QUERY);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_ID, (int)$customerID, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_CATEGORY_ID, (int)$categoryID, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_VIEW_SECONDS, (int)$additionalViewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_VIEWS, (int)$additionalViews, \PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_BASKET_ADDITIONS, (int)$additionalBasketAdditions, \PDO::PARAM_INT);
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_BASKET_ADD_QTY,
            (string)(float)$additionalBasketAddQty,
            \PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_BASKET_ADD_VALUE,
            (string)(float)$additionalBasketAddValue,
            \PDO::PARAM_STR
        );
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_BASKET_REMOVALS, (int)$additionalBasketRemovals, \PDO::PARAM_INT);
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_BASKET_RM_QTY,
            (string)(float)$additionalBasketRMQty,
            \PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_BASKET_RM_VALUE,
            (string)(float)$additionalBasketRMValue,
            \PDO::PARAM_STR
        );
        $stmt->bindValue(':' . self::KEY_ADDITIONAL_ITEM_ORDER_COUNT, (int)$additionalItemOrderCount, \PDO::PARAM_INT);
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_ITEM_ORDER_QTY,
            (string)(float)$additionalItemOrderQty,
            \PDO::PARAM_STR
        );
        $stmt->bindValue(
            ':' . self::KEY_ADDITIONAL_ITEM_ORDER_VALUE,
            (string)(float)$additionalItemOrderValue,
            \PDO::PARAM_STR
        );
        $stmt->execute();
    }

    protected function handlePageviewCompleteEvent(TrackingEvent $trackingEvent)
    {
		$start = microtime(true);
        $eventData = $trackingEvent->getEventData();
        $referencedEventUUID = $eventData[self::KEY_REFERENCED_PAGEVIEW_EVENT_UUID];
        $referencedEvent = $this->trackingAPIService->getTrackingEventByUniqueID($referencedEventUUID);
        if ($referencedEvent) {
            $referencedEventData = $referencedEvent->getEventData();
            $visitorID = isset($referencedEventData[self::KEY_CURRENT_VISITOR_ID]) ? $referencedEventData[self::KEY_CURRENT_VISITOR_ID] : null;
            $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? (int)$eventData[self::KEY_CURRENT_USER_ID] : isset($referencedEventData[self::KEY_CURRENT_USER_ID]) ? $referencedEventData[self::KEY_CURRENT_USER_ID] : null;

            $eventCustomerID = $trackingEvent->getCustomerID();
            $eventDataCustomerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? (int)$eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
            $referencedEventCustomerID = $referencedEvent->getCustomerID();
            $referencedEventDataCustomerID = isset($referencedEventData[self::KEY_CURRENT_CUSTOMER_ID]) ? (int)$referencedEventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
            $customerID = null;
            if ($eventCustomerID) {
                $customerID = $eventCustomerID;
            } elseif ($eventDataCustomerID) {
                $customerID = $eventDataCustomerID;
            } elseif ($referencedEventCustomerID) {
                $customerID = $referencedEventCustomerID;
            } elseif ($referencedEventDataCustomerID) {
                $customerID = $referencedEventDataCustomerID;
            }

            $categoryID = isset($eventData[self::KEY_CURRENT_CATEGORY_ID]) ? $eventData[self::KEY_CURRENT_CATEGORY_ID] : isset($referencedEventData[self::KEY_CURRENT_CATEGORY_ID]) ? (int)$referencedEventData[self::KEY_CURRENT_CATEGORY_ID] : null;
            $additionalViewSeconds = (int)$eventData[self::KEY_ACTIVE_PAGE_VIEW_TIME];

            if ($customerID && $categoryID && $additionalViewSeconds) {
                $this->updateCustomer($customerID, $additionalViewSeconds, 0);
                $this->updateCustomerCategory($customerID, $categoryID, $additionalViewSeconds, 1);
            }
        }
		$duration = microtime(true) - $start;
		$this->logger->debug('Handle pageview-complete in ' . __CLASS__ . '  took ' . $duration . ' seconds');
    }

    protected function handlePageviewCategory(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData[self::KEY_CURRENT_VISITOR_ID]) ? $eventData[self::KEY_CURRENT_VISITOR_ID] : null;
        $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? $eventData[self::KEY_CURRENT_USER_ID] : null;
        $customerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? $eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
        $categoryID = isset($eventData[self::KEY_CURRENT_CATEGORY_ID]) ? $eventData[self::KEY_CURRENT_CATEGORY_ID] : null;
        $additionalViews = 1;

        if ($customerID && $categoryID) {
            $this->updateCustomer($customerID, 0, $additionalViews);
            $this->updateCustomerCategory($customerID, $categoryID, 0, $additionalViews);
        }
    }


    protected function handleBasketAddEvent(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData[self::KEY_CURRENT_VISITOR_ID]) ? $eventData[self::KEY_CURRENT_VISITOR_ID] : null;
        $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? $eventData[self::KEY_CURRENT_USER_ID] : null;
        $customerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? $eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
        $categoryID = isset($eventData[self::KEY_CURRENT_CATEGORY_ID]) ? $eventData[self::KEY_CURRENT_CATEGORY_ID] : null;

        $additionalBasketAdditions = 1;
        $additionalBasketAddQty = isset($eventData[self::KEY_NEW_QTY]) ? $eventData[self::KEY_NEW_QTY] : null;
        $additionalBasketAddValue = isset($eventData[self::KEY_UNIT_PRICE]) && isset($eventData[self::KEY_NEW_QTY]) ? (float)$eventData[self::KEY_UNIT_PRICE] * (float)$eventData[self::KEY_NEW_QTY] : null;

        if ($categoryID && $additionalBasketAdditions && $additionalBasketAddQty && $additionalBasketAddValue) {
            if ($customerID) {
                $this->updateCustomerCategory(
                    $customerID,
                    $categoryID,
                    0,
                    0,
                    $additionalBasketAdditions,
                    $additionalBasketAddQty,
                    $additionalBasketAddValue
                );
            }
        }
    }

    protected function handleBasketRemoveEvent(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData[self::KEY_CURRENT_VISITOR_ID]) ? $eventData[self::KEY_CURRENT_VISITOR_ID] : null;
        $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? $eventData[self::KEY_CURRENT_USER_ID] : null;
        $itemID = isset($eventData[self::KEY_CURRENT_ITEM_ID]) ? $eventData[self::KEY_CURRENT_ITEM_ID] : null;
        $customerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? $eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
        $additionalBasketRemoves = 1;
        $additionalBasketRMQuantity = isset($eventData[self::KEY_OLD_QTY]) ? $eventData[self::KEY_OLD_QTY] : null;
        $additionalBasketRemoveValue = isset($eventData[self::KEY_UNIT_PRICE]) && isset($eventData[self::KEY_NEW_QTY]) ? (float)$eventData[self::KEY_UNIT_PRICE] * (float)$eventData[self::KEY_NEW_QTY] : null;

        $lastVisitorItemBasketAddEvent = $this->trackingAPIService->findLastByEventTypeVisitorItem(
            TrackingEvent::EVENT_TYPE_BASKET_ADD,
            $visitorID,
            $itemID
        );
        $categoryID = null;
        if (null !== $lastVisitorItemBasketAddEvent) {
            $referencedEventData = $lastVisitorItemBasketAddEvent->getEventData();
            if (is_array($referencedEventData) && array_key_exists(
                    self::KEY_CURRENT_CATEGORY_ID,
                    $referencedEventData
                ) && (int)$referencedEventData[self::KEY_CURRENT_CATEGORY_ID] > 0
            ) {
                $categoryID = $referencedEventData[self::KEY_CURRENT_CATEGORY_ID];
            }
        }

        if ($categoryID && $additionalBasketRemoves && $additionalBasketRMQuantity && $additionalBasketRemoveValue) {
            if ($customerID) {
                $this->updateCustomerCategory(
                    $customerID,
                    $categoryID,
                    0,
                    0,
                    0,
                    0,
                    0,
                    $additionalBasketRemoves,
                    $additionalBasketRMQuantity,
                    $additionalBasketRemoveValue
                );
            }
        }
    }

    protected function handleBasketQtyChangeEvent(TrackingEvent $trackingEvent)
    {
        $additionalBasketAdditions = 0;
        $additionalBasketAddQty = 0.00;
        $additionalBasketAddValue = 0.00;
        $additionalBasketRemoves = 0;
        $additionalBasketRMQuantity = 0.00;
        $additionalBasketRemoveValue = 0.00;

        $eventData = $trackingEvent->getEventData();
        $itemID = isset($eventData[self::KEY_CURRENT_ITEM_ID]) ? (int)$eventData[self::KEY_CURRENT_ITEM_ID] : null;
        $visitorID = isset($eventData[self::KEY_CURRENT_VISITOR_ID]) ? $eventData[self::KEY_CURRENT_VISITOR_ID] : null;
        $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? $eventData[self::KEY_CURRENT_USER_ID] : null;
        $customerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? $eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
        $oldQty = isset($eventData[self::KEY_OLD_QTY]) ? (float)$eventData[self::KEY_OLD_QTY] : 0.00;
        $newQty = isset($eventData[self::KEY_NEW_QTY]) ? (float)$eventData[self::KEY_NEW_QTY] : 0.00;
        $unitPrice = isset($eventData[self::KEY_UNIT_PRICE]) ? (float)$eventData[self::KEY_UNIT_PRICE] : 0.00;

        if ($newQty > $oldQty) {
            $additionalBasketAdditions = 1;
            $additionalBasketAddQty = $newQty - $oldQty;
            $additionalBasketAddValue = $additionalBasketAddQty * $unitPrice;
        } elseif ($newQty < $oldQty) {
            $additionalBasketRemoves = 1;
            $additionalBasketRMQuantity = $oldQty - $newQty;
            $additionalBasketRemoveValue = $additionalBasketRMQuantity * $unitPrice;
        }

        $lastVisitorItemBasketAddEvent = $this->trackingAPIService->findLastByEventTypeVisitorItem(
            TrackingEvent::EVENT_TYPE_BASKET_ADD,
            $visitorID,
            $itemID
        );
        $categoryID = null;
        if (null !== $lastVisitorItemBasketAddEvent) {
            $referencedEventData = $lastVisitorItemBasketAddEvent->getEventData();
            if (is_array($referencedEventData) && array_key_exists(
                    self::KEY_CURRENT_CATEGORY_ID,
                    $referencedEventData
                ) && (int)$referencedEventData[self::KEY_CURRENT_CATEGORY_ID] > 0
            ) {
                $categoryID = $referencedEventData[self::KEY_CURRENT_CATEGORY_ID];
            }
        }

        if ($categoryID && ($additionalBasketRemoves > 0 || $additionalBasketAdditions > 0)) {
            if ($customerID) {
                $this->updateCustomerCategory(
                    $customerID,
                    $categoryID,
                    0,
                    0,
                    $additionalBasketAdditions,
                    $additionalBasketAddQty,
                    $additionalBasketAddValue,
                    $additionalBasketRemoves,
                    $additionalBasketRMQuantity,
                    $additionalBasketRemoveValue
                );
            }
        }
    }

    protected function handleOrderCompleteEvent(TrackingEvent $trackingEvent)
    {
        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData[self::KEY_CURRENT_VISITOR_ID]) ? $eventData[self::KEY_CURRENT_VISITOR_ID] : null;
        $userID = isset($eventData[self::KEY_CURRENT_USER_ID]) ? $eventData[self::KEY_CURRENT_USER_ID] : null;
        $customerID = isset($eventData[self::KEY_CURRENT_CUSTOMER_ID]) ? $eventData[self::KEY_CURRENT_CUSTOMER_ID] : null;
        $orderItems = isset($eventData[self::KEY_ORDER_ITEMS]) && is_array(
            $eventData[self::KEY_ORDER_ITEMS]
        ) ? $eventData[self::KEY_ORDER_ITEMS] : [];

        $valuesPerCategory = [];
        $handledItemIDs = [];

        foreach ($orderItems as $orderItemArr) {
            $itemID = isset($orderItemArr[self::KEY_ITEM_ID]) ? $orderItemArr[self::KEY_ITEM_ID] : null;
            $qty = isset($orderItemArr[self::KEY_QTY]) ? $orderItemArr[self::KEY_QTY] : null;
            $unitPrice = isset($orderItemArr[self::KEY_UNIT_PRICE]) ? $orderItemArr[self::KEY_UNIT_PRICE] : null;

            $lastVisitorItemBasketAddEvent = $this->trackingAPIService->findLastByEventTypeVisitorItem(
                TrackingEvent::EVENT_TYPE_BASKET_ADD,
                $visitorID,
                $itemID
            );
            $categoryID = null;
            if (null !== $lastVisitorItemBasketAddEvent) {
                $referencedEventData = $lastVisitorItemBasketAddEvent->getEventData();
                if (is_array($referencedEventData) && array_key_exists(
                        self::KEY_CURRENT_CATEGORY_ID,
                        $referencedEventData
                    ) && (int)$referencedEventData[self::KEY_CURRENT_CATEGORY_ID] > 0
                ) {
                    $categoryID = $referencedEventData[self::KEY_CURRENT_CATEGORY_ID];
                }
            }
            if ($categoryID && array_key_exists($categoryID, $valuesPerCategory)) {
                $valuesPerCategory[$categoryID][self::KEY_QTY] += $qty;
                $valuesPerCategory[$categoryID][self::KEY_VALUE] += $unitPrice * $qty;
                $valuesPerCategory[$categoryID][self::KEY_NO_ITEMS_ADDED] += 1;
                $handledItemIDs[] = $itemID;
            } elseif($categoryID) {
                $valuesPerCategory[$categoryID][self::KEY_QTY] = $qty;
                $valuesPerCategory[$categoryID][self::KEY_VALUE] = $unitPrice * $qty;
                $valuesPerCategory[$categoryID][self::KEY_NO_ITEMS_ADDED] = 1;
            }

        }

        foreach ($valuesPerCategory as $categoryID => $valueArr) {
            $no = (int)$valueArr[self::KEY_NO_ITEMS_ADDED];
            $qty = (float)$valueArr[self::KEY_QTY];
            $value = (float)$valueArr[self::KEY_VALUE];
            if ($customerID) {
                $this->updateCustomerCategory($customerID, $categoryID, 0, 0, 0, 0, 0, 0, 0, 0, $no, $qty, $value);
            }
        }
    }

}