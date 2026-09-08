<?php
namespace DynCom\dc\tracking;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 21.02.2017
 * Time: 22:07
 */
use DynCom\dc\common\interfaces\Observer;

/**
 * Class TrackingBasketListener
 */
class TrackingEventListener implements Observer
{

    const EVENT_NAME_ITEM_ADDED = 'afterSuccessfullAddToBasket';
    const EVENT_NAME_ITEM_REMOVED = 'afterSuccessfullRemoveFromBasket';
    const EVENT_NAME_ITEM_QTY_CHANGED = 'afterSuccessfullyBasketQtyChange';
    const EVENT_NAME_ITEM_VIEW = 'onItemCardView';
    const EVENT_NAME_CATEGORY_VIEW = 'onCategoryCardView';
    const EVENT_NAME_SEARCH = 'onSearch';
    const EVENT_NAME_ORDER_COMPLETE = 'order_complete';
    const EVENT_NAME_USER_LOGIN = 'afterUserLogin';

    /**
     * @var \PDO
     */
    protected $db;
    /**
     * @var TrackingAPIService
     */
    protected $trackingAPIService;

    public function __construct(TrackingAPIService $trackingAPIService)
    {
        $this->trackingAPIService = $trackingAPIService;
    }

    public function notify($eventName, $data)
    {
        switch($eventName) {
            case TrackingEvent::EVENT_TYPE_BASKET_ADD:
                $this->handleItemAddedEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE:
                $this->handleItemQtyChangedEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_BASKET_REMOVE:
                $this->handleItemRemovedEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_ORDER_COMPLETE:
                $this->handleOrderCompleteEvent($data);
                break;
            case self::EVENT_NAME_USER_LOGIN:
                $this->handleUserLoginEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_CUSTOMER_IDENTIFIED:
                $this->handleCustomerIdentifiedEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM:
                $this->handleItemViewEvent($data);
                break;
            case TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY:
                $this->handleCategoryViewEvent($data);
                break;
        }
    }

    protected function handleItemAddedEvent($eventData)
    {
        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_BASKET_ADD;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $newQty = isset($eventData['new_qty']) ? $eventData['new_qty'] : null;
        $categoryID = isset($GLOBALS['category']['id']) ? $GLOBALS['category']['id'] : null;
        $unitPrice = isset($eventData['unit_price']) ? $eventData['unit_price'] : null;
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;

        if ($itemID && $visitorID && null !== $newQty && null !== $unitPrice) {
            $trackingEventData = [
                'current_item_id' => $itemID,
                'current_visitor_id' => $visitorID,
                'new_qty' => $newQty,
                'unit_price' => $unitPrice,
            ];
            if ($categoryID) {
                $trackingEventData['current_category_id'] = $categoryID;
            }
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID,$itemID,$categoryID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleItemQtyChangedEvent($eventData)
    {
        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_BASKET_QTY_CHANGE;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $oldQty = isset($eventData['old_qty']) ? $eventData['old_qty'] : null;
        $newQty = isset($eventData['new_qty']) ? $eventData['new_qty'] : null;
        $categoryID = isset($GLOBALS['category']['id']) ? $GLOBALS['category']['id'] : null;
        $unitPrice = isset($eventData['unit_price']) ? $eventData['unit_price'] : null;
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;

        if ($itemID && $visitorID && null !== $newQty && null !== $oldQty && null !== $unitPrice) {
            $trackingEventData = [
                'current_item_id' => $itemID,
                'current_visitor_id' => $visitorID,
                'old_qty' => (float)$oldQty,
                'new_qty' => (float)$newQty,
                'unit_price' => (float)$unitPrice,
            ];
            if ($categoryID) {
                $trackingEventData['current_category_id'] = $categoryID;
            }
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID,$itemID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleItemRemovedEvent($eventData)
    {
        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_BASKET_REMOVE;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $unitPrice = isset($eventData['unit_price']) ? $eventData['unit_price'] : null;
        $categoryID = isset($GLOBALS['category']['id']) ? $GLOBALS['category']['id'] : null;
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;

        if ($itemID && $visitorID && null !== $unitPrice) {
            $trackingEventData = [
                'current_item_id' => $itemID,
                'current_visitor_id' => $visitorID,
                'unit_price' => $unitPrice,
            ];
            if ($categoryID) {
                $trackingEventData['current_category_id'] = $categoryID;
            }
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID,$itemID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleOrderCompleteEvent($eventData)
    {

        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_ORDER_COMPLETE;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $salesHeaderID = isset($eventData['sales_header_id']) ? (int)$eventData['sales_header_id'] : null;
        $linkedItemIDs = isset($eventData['linked_item_ids']) && is_array($eventData['linked_item_ids']) ? $eventData['linked_item_ids'] : null;
        $orderItems = isset($eventData['order_items']) && is_array($eventData['order_items']) ? $eventData['order_items'] : null;
        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;

        if ($visitorID && $salesHeaderID && null !== $linkedItemIDs && null !== $orderItems) {
            $trackingEventData = [
                'sales_header_id' => $salesHeaderID,
                'current_visitor_id' => $visitorID,
                'linked_item_ids' => $linkedItemIDs,
                'order_items' => $orderItems,
            ];
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleUserLoginEvent($eventData)
    {
        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_USER_IDENTIFIED;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['identified_user_id']) ? $eventData['identified_user_id'] : null;

        if ($visitorID && $userID ) {
            $trackingEventData = [
                'current_visitor_id' => $visitorID,
                'identified_user_id' => $userID,
            ];
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleCustomerIdentifiedEvent($eventData)
    {
        $uuid = create_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_USER_IDENTIFIED;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $customerID = isset($eventData['identified_customer_id']) ? $eventData['identified_customer_id'] : null;

        if ($visitorID && $customerID ) {
            $trackingEventData = [
                'current_visitor_id' => $visitorID,
                'identified_customer_id' => $customerID,
            ];
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleItemViewEvent($eventData)
    {
        $uuid = get_req_resp_cycle_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_PAGEVIEW_ITEM;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $itemID = isset($eventData['current_item_id']) ? $eventData['current_item_id'] : null;
        $categoryID = isset($eventData['current_category_id']) ? $eventData['current_category_id'] : null;

        if ($visitorID && $itemID) {
            $trackingEventData = [
                'session_id' => $sessionID,
                'current_item_id' => $itemID,
                'current_visitor_id' => $visitorID,
            ];
            if ($categoryID) {
                $trackingEventData['current_category_id'] = $categoryID;
            }
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleCategoryViewEvent($eventData)
    {
        $uuid = get_req_resp_cycle_uuid();
        $eventType = TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY;
        $sessionID = isset($eventData['session_id']) && !empty($eventData['session_id']) ? $eventData['session_id'] : session_id();
        $creationTimestamp = (string)time();

        $visitorID = isset($eventData['current_visitor_id']) ? $eventData['current_visitor_id'] : null;
        $userID = isset($eventData['current_user_id']) ? $eventData['current_user_id'] : null;
        $customerID = isset($eventData['current_customer_id']) ? $eventData['current_customer_id'] : null;
        $categoryID = isset($eventData['current_category_id']) ? $eventData['current_category_id'] : null;

        if ($visitorID && $categoryID) {
            $trackingEventData = [
                'session_id' => $sessionID,
                'current_category_id' => $categoryID,
                'current_visitor_id' => $visitorID,
            ];
            if ($userID) {
                $trackingEventData['current_user_id'] = $userID;
            }
            if ($customerID) {
                $trackingEventData['current_customer_id'] = $customerID;
            }
            $event = new TrackingEvent($uuid,$sessionID,$eventType,$creationTimestamp,$creationTimestamp,$trackingEventData,$visitorID,$userID,$customerID);
            $this->trackingAPIService->addTrackingEvent($event);
        }
    }

    protected function handleSearchEvent($eventData)
    {

    }

}