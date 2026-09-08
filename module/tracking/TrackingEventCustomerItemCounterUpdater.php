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

class TrackingEventCustomerItemCounterUpdater implements PipelineStage
{

    const CUSTOMER_UPDATE_QUERY = '
        UPDATE
          `customer`
        SET
          `item_preview_duration_seconds` = `item_preview_duration_seconds` + :additional_preview_seconds,   
          `item_preview_counter` = `item_preview_counter` + :additional_previews,           
          `item_details_view_duration_seconds` = `item_details_view_duration_seconds` + :additional_view_seconds,
          `item_details_view_counter` = `item_details_view_counter` + :additional_views,
          `item_add_to_basket_counter` = `item_add_to_basket_counter` + :additional_basket_additions,     
          `item_total_add_to_basket_qty`  = `item_total_add_to_basket_qty` + :additional_basket_add_qty,
          `item_total_add_to_basket_value` = `item_total_add_to_basket_value` + :additional_basket_add_value, 
          `item_rm_from_basket_counter` = `item_rm_from_basket_counter` + :additional_basket_removals,
          `item_total_rm_from_basket_qty` = `item_total_rm_from_basket_qty` + :additional_basket_rm_qty,
          `item_total_rm_from_basket_value` = `item_total_rm_from_basket_value` + :additional_basket_rm_value,
          `item_order_counter` = `item_order_counter` + :additional_item_order_count,             
          `item_total_order_qty` = `item_total_order_qty` + :additional_item_order_qty,           
          `item_total_order_value` = `item_total_order_value` + :additional_item_order_value
        WHERE
          id = :customer_id
    ';

    const CUSTOMER_ITEM_UPDATE_QUERY = '
        INSERT INTO 
          customer_item
        SET
          customer_id = :customer_id,
          item_id = :item_id,
          preview_duration_seconds = :additional_preview_seconds,
          preview_counter = :additional_previews,
          details_view_duration_seconds = :additional_view_seconds,
          details_view_counter = :additional_views,
          add_to_basket_counter = :additional_basket_additions,
          total_add_to_basket_qty = :additional_basket_add_qty,
          total_add_to_basket_value = :additional_basket_add_value,
          rm_from_basket_counter = :additional_basket_removals, 
          total_rm_from_basket_qty = :additional_basket_rm_qty, 
          total_rm_from_basket_value = :additional_basket_rm_value,
          order_counter = :additional_item_order_count, 
          total_order_qty = :additional_item_order_qty,
          total_order_value = :additional_item_order_value
        ON DUPLICATE KEY UPDATE 
          preview_duration_seconds = preview_duration_seconds + :additional_preview_seconds,
          preview_counter = preview_counter + :additional_previews,
          details_view_duration_seconds = details_view_duration_seconds + :additional_view_seconds,
          details_view_counter = details_view_counter + :additional_views,
          add_to_basket_counter = add_to_basket_counter + :additional_basket_additions,
          total_add_to_basket_qty = total_add_to_basket_qty + :additional_basket_add_qty,
          total_add_to_basket_value = total_add_to_basket_value + :additional_basket_add_value,
          rm_from_basket_counter = rm_from_basket_counter + :additional_basket_removals, 
          total_rm_from_basket_qty = total_rm_from_basket_qty + :additional_basket_rm_qty, 
          total_rm_from_basket_value = total_rm_from_basket_value + :additional_basket_rm_value,
          order_counter = order_counter + :additional_item_order_count, 
          total_order_qty = total_order_qty + :additional_item_order_qty,
          total_order_value = total_order_value + :additional_item_order_value                
    ';

    protected static $handledEventTypes = [
        TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM,
        TrackingEvent::EVENT_TYPE_PAGEVIEW_COMPLETE,
        TrackingEvent::EVENT_TYPE_BASKET_ADD,
        TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE,
        TrackingEvent::EVENT_TYPE_BASKET_REMOVE,
        TrackingEvent::EVENT_TYPE_ORDER_COMPLETE,
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
                case TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM:
                    $this->handlePageviewItem($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_ADD:
                    $this->handleBasketAddEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_REMOVE:
                    $this->handleBasketRemoveEvent($payload);
                    break;
                case TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE:
                    $this->handleBasketChangedEvent($payload);
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

    protected function updateCustomer(
        $customerID,
        $additionalPreviewSeconds = 0,
        $additionalPreviews = 0,
        $additionalViewSeconds = 0,
        $additionalViews = 0,
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
        $stmt = $this->trackingDBHandler->prepare(self::CUSTOMER_UPDATE_QUERY);
        $stmt->bindValue(':customer_id', (int)$customerID, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_preview_seconds', (int)$additionalPreviewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_previews', (int)$additionalPreviews, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_view_seconds', (int)$additionalViewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_views', (int)$additionalViews, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_additions', (int)$additionalBasketAdditions, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_add_qty', (string)(float)$additionalBasketAddQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_add_value', (string)(float)$additionalBasketAddValue, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_removals', (int)$additionalBasketRemovals, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_rm_qty', (string)(float)$additionalBasketRMQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_rm_value', (string)(float)$additionalBasketRMValue, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_item_order_count', (int)$additionalItemOrderCount, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_item_order_qty', (string)(float)$additionalItemOrderQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_item_order_value', (string)(float)$additionalItemOrderValue, \PDO::PARAM_STR);
        $stmt->execute();
        $err = $stmt->errorInfo();
        $this->logger->debug(__CLASS__ . ' - customer update query finished. Error-Info: [' . implode(', ',$err) . '].');
    }
    
    protected function updateCustomerItem(
        $customerID,
        $itemID,
        $additionalPreviewSeconds = 0,
        $additionalPreviews = 0,
        $additionalViewSeconds = 0,
        $additionalViews = 0,
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
        $stmt = $this->trackingDBHandler->prepare(self::CUSTOMER_ITEM_UPDATE_QUERY);
        $stmt->bindValue(':customer_id', (int)$customerID, \PDO::PARAM_INT);
        $stmt->bindValue(':item_id', (int)$itemID, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_preview_seconds', (int)$additionalPreviewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_previews', (int)$additionalPreviews, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_view_seconds', (int)$additionalViewSeconds, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_views', (int)$additionalViews, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_additions', (int)$additionalBasketAdditions, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_add_qty', (string)(float)$additionalBasketAddQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_add_value', (string)(float)$additionalBasketAddValue, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_removals', (int)$additionalBasketRemovals, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_basket_rm_qty', (string)(float)$additionalBasketRMQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_basket_rm_value', (string)(float)$additionalBasketRMValue, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_item_order_count', (int)$additionalItemOrderCount, \PDO::PARAM_INT);
        $stmt->bindValue(':additional_item_order_qty', (string)(float)$additionalItemOrderQty, \PDO::PARAM_STR);
        $stmt->bindValue(':additional_item_order_value', (string)(float)$additionalItemOrderValue, \PDO::PARAM_STR);
        $stmt->execute();
        $err = $stmt->errorInfo();
        $this->logger->debug(__CLASS__ . ' - customer-item update query finished. Error-Info: [' . implode(', ',$err) . '].');
    }

    protected function handlePageviewCompleteEvent(TrackingEvent $trackingEvent)
    {
		$start = microtime(true);
        $eventData = $trackingEvent->getEventData();
        $referencedEventUUID = $eventData['referenced_pageview_event_uuid'];
        $referencedEvent = $this->trackingAPIService->getTrackingEventByUniqueID($referencedEventUUID);
        if ($referencedEvent) {
            $referencedEventData = $referencedEvent->getEventData();

            $eventCustomerID = $trackingEvent->getCustomerID();
            $eventDataCustomerID = isset($eventData['current_customer_id']) ? (int)$eventData['current_customer_id'] : null;
            $referencedEventCustomerID = $referencedEvent->getCustomerID();
            $referencedEventDataCustomerID = isset($referencedEventData['current_customer_id']) ? (int)$referencedEventData['current_customer_id'] : null;
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

            $visitorID = isset($referencedEventData['current_visitor_id']) ? $referencedEventData['current_visitor_id'] : null;
            $currentItemID = isset($referencedEventData['current_item_id']) ? $referencedEventData['current_item_id'] : null;
            $linkedItemIDs = isset($eventData['linked_item_ids']) && is_array($eventData['linked_item_ids']) ? $eventData['linked_item_ids'] : [];
            $additionalViewSeconds = (int)$eventData['active_page_view_time'];

            $totalAdditionalPreviewSeconds = 0;
            $totalAdditionalPreviews = 0;
            $totalAdditionalViewSeconds = 0;
            $totalAdditionalViews = 0;

            if ($currentItemID && $additionalViewSeconds) {
                $totalAdditionalViewSeconds = $additionalViewSeconds;
            }
            if ($customerID && $currentItemID && $additionalViewSeconds) {
                $this->updateCustomerItem($customerID, $currentItemID, 0, 0, $additionalViewSeconds);
            }

            if (!empty($linkedItemIDs)) {
                $totalAdditionalPreviewSeconds = $totalAdditionalViewSeconds;
            }

            foreach ($linkedItemIDs as $linkedItemID) {
                if ($customerID) {
                    $this->updateCustomerItem($customerID, $linkedItemID, $additionalViewSeconds, 1);
                }
            }

            if ($customerID) {
                $this->updateCustomer($customerID, $totalAdditionalPreviewSeconds, $totalAdditionalPreviews, $totalAdditionalViewSeconds, $totalAdditionalViews);
            }
        }
		$duration = microtime(true) - $start;
		$this->logger->debug('Handle pageview-complete in ' . __CLASS__ . '  took ' . $duration . ' seconds');
    }

    protected function handlePageviewItem(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $additionalViews = 1;

        if ($customerID && $itemID) {
            $this->updateCustomer($customerID,0,0,0,$additionalViews);
            $this->updateCustomerItem($customerID,$itemID,0,0,0,$additionalViews);
        }
        
    }


    protected function handleBasketAddEvent(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;

        $additionalBasketAdditions = 1;
        $additionalBasketAddQty = isset($eventData['new_qty']) ? $eventData['new_qty'] : null;
        $additionalBasketAddValue = isset($eventData['unit_price']) && isset($eventData['new_qty']) ? (float)$eventData['unit_price'] * (float)$eventData['new_qty'] : null;

        if ($itemID && $additionalBasketAdditions && $additionalBasketAddQty && $additionalBasketAddValue) {
            if ($customerID) {
                $this->updateCustomer($customerID,0,0,0,0,$additionalBasketAdditions,$additionalBasketAddQty,$additionalBasketAddValue);
                $this->updateCustomerItem($customerID,$itemID,0,0,0,0,$additionalBasketAdditions,$additionalBasketAddQty,$additionalBasketAddValue);
            }
        }
    }

    protected function handleBasketRemoveEvent(TrackingEvent $trackingEvent)
    {

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $additionalBasketRemoves = 1;
        $additionalBasketRMQuantity = isset($eventData['old_qty']) ? $eventData['old_qty'] : null;
        $additionalBasketRemoveValue = isset($eventData['unit_price']) && isset($eventData['new_qty']) ? (float)$eventData['unit_price'] * (float)$eventData['new_qty'] : null;

        if ($itemID && $additionalBasketRemoves && $additionalBasketRMQuantity && $additionalBasketRemoveValue) {
            if ($customerID) {
                $this->updateCustomer($customerID,0,0,0,0,0,0,0,$additionalBasketRemoves,$additionalBasketRMQuantity,$additionalBasketRemoveValue);
                $this->updateCustomerItem($customerID,$itemID,0,0,0,0,0,0,0,$additionalBasketRemoves,$additionalBasketRMQuantity,$additionalBasketRemoveValue);
            }
        }
    }

    protected function handleBasketChangedEvent(TrackingEvent $trackingEvent)
    {
        $additionalBasketAdditions = 0;
        $additionalBasketAddQty = 0.00;
        $additionalBasketAddValue = 0.00;
        $additionalBasketRemoves = 0;
        $additionalBasketRMQuantity = 0.00;
        $additionalBasketRemoveValue = 0.00;

        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $oldQty = isset($eventData['old_qty']) ? (float)$eventData['old_qty'] : 0.00;
        $newQty = isset($eventData['new_qty']) ? (float)$eventData['new_qty'] : 0.00;
        $unitPrice = isset($eventData['unit_price']) ? (float)$eventData['unit_price'] : 0.00;

        if ($newQty > $oldQty) {
            $additionalBasketAdditions = 1;
            $additionalBasketAddQty = abs($newQty - $oldQty);
            $additionalBasketAddValue = $additionalBasketAddQty * $unitPrice;
        } else {
            $additionalBasketRemoves = 1;
            $additionalBasketRMQuantity = abs($oldQty - $newQty);
            $additionalBasketRemoveValue = $additionalBasketRMQuantity * $unitPrice;
        }

        if ($itemID && ($additionalBasketRemoves || $additionalBasketAdditions) && ($additionalBasketRMQuantity || $additionalBasketAddQty) && ($additionalBasketAddValue || $additionalBasketRemoveValue)) {
            if ($customerID) {
                $this->updateCustomer($customerID,0,0,0,0,$additionalBasketAdditions,$additionalBasketAddQty,$additionalBasketAddValue,$additionalBasketRemoves,$additionalBasketRMQuantity,$additionalBasketRemoveValue);
                $this->updateCustomerItem($customerID,$itemID,0,0,0,0,$additionalBasketAdditions,$additionalBasketAddQty,$additionalBasketAddValue,$additionalBasketRemoves,$additionalBasketRMQuantity,$additionalBasketRemoveValue);
            }
        }
    }

    protected function handleOrderCompleteEvent(TrackingEvent $trackingEvent)
    {
        $eventData = $trackingEvent->getEventData();
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $orderItems = isset($eventData['order_items']) && is_array(
            $eventData['order_items']
        ) ? $eventData['order_items'] : [];
            
        $totalAdditionalOrderActions = 1;
        $totalAdditionalOrderQty = 0;
        $totalAdditionalOrderValue =  0.00;

        foreach ($orderItems as $orderItemArr) {
            $itemID = isset($orderItemArr['item_id']) ? $orderItemArr['item_id'] : null;
            $qty = isset($orderItemArr['qty']) ? $orderItemArr['qty'] : null;
            $unitPrice = isset($orderItemArr['unit_price']) ? $orderItemArr['unit_price'] : null;
            $value = (float)$qty * (float)$unitPrice;
            
            $totalAdditionalOrderQty += $qty;
            $totalAdditionalOrderValue += $value;

            if ($itemID && $qty && $value) {
                if ($customerID) {
                    $this->updateCustomerItem($customerID,$itemID,0,0,0,0,0,0,0,0,0,0,1,$qty,$value);
                }
            }
        }
        
        if ($customerID && $totalAdditionalOrderQty && $totalAdditionalOrderValue) {
            $this->updateCustomer($customerID,0,0,0,0,0,0,0,0,0,0,$totalAdditionalOrderActions,$totalAdditionalOrderQty,$totalAdditionalOrderValue);
        }
        
    }

}