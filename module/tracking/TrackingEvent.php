<?php
namespace DynCom\dc\tracking;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 20.02.2017
 * Time: 21:19
 */
use InvalidArgumentException;
use JsonSerializable;

/**
 * Class TrackingEvent
 * @package DynCom\dc\tracking
 */
class TrackingEvent implements JsonSerializable
{

    protected $uuid;
    protected $sessionID;
    protected $eventType;
    protected $createdTimestamp;
    protected $lastModifiedTimestamp;
    protected $eventData = [];
    protected $visitorID;
    protected $userID;
    protected $customerID;
    protected $itemID;
    protected $categoryID;

    protected $contentHash;

    public const EVENT_TYPE_PAGEVIEW_ITEM = 'ITEM_VIEW';
    public const EVENT_TYPE_PAGEVIEW_CATEGORY = 'CATEGORY_VIEW';
    public const EVENT_TYPE_PAGEVIEW_BASKET = 'BASKET_VIEW';
    public const EVENT_TYPE_PAGEVIEW_ORDER = 'ORDER_VIEW';
    public const EVENT_TYPE_PAGEVIEW_COMPLETE = 'PAGEVIEW_COMPLETE';
    public const EVENT_TYPE_BASKET_ADD = 'BASKET_ADD';
    public const EVENT_TYPE_BASKET_QTY_CHANGE = 'BASKET_QTY_CHANGE';
    public const EVENT_TYPE_BASKET_REMOVE = 'BASKET_REMOVE';
    public const EVENT_TYPE_ORDER_COMPLETE = 'ORDER_COMPLETE';
    public const EVENT_TYPE_CUSTOMER_IDENTIFIED = 'CUSTOMER_IDENTIFIED';
    public const EVENT_TYPE_USER_IDENTIFIED = 'USER_IDENTIFIED';


    protected const KEY_UUID = 'uuid';
    protected const KEY_SESSION_ID = 'session_id';
    protected const KEY_EVENT_TYPE = 'event_type';
    protected const KEY_CREATION_TIMESTAMP = 'creation_timestamp';
    protected const KEY_LAST_MODIFIED_TIMESTAMP = 'last_modified_timestamp';
    protected const KEY_EVENT_DATA = 'event_data';
    protected const KEY_VISITOR_ID = 'visitor_id';
    protected const KEY_USER_ID = 'user_id';
    protected const KEY_CUSTOMER_ID = 'customer_id';
    protected const KEY_ITEM_ID = 'item_id';
    protected const KEY_CATEGORY_ID = 'category_id';

    protected const VALID_EVENT_TYPES = [
        self::EVENT_TYPE_PAGEVIEW_ITEM,
        self::EVENT_TYPE_PAGEVIEW_CATEGORY,
        self::EVENT_TYPE_PAGEVIEW_BASKET,
        self::EVENT_TYPE_PAGEVIEW_ORDER,
        self::EVENT_TYPE_PAGEVIEW_COMPLETE,
        self::EVENT_TYPE_BASKET_ADD,
        self::EVENT_TYPE_BASKET_QTY_CHANGE,
        self::EVENT_TYPE_BASKET_REMOVE,
        self::EVENT_TYPE_ORDER_COMPLETE,
        self::EVENT_TYPE_CUSTOMER_IDENTIFIED,
        self::EVENT_TYPE_USER_IDENTIFIED,
    ];

    /**
     * TrackingEvent constructor.
     * @param string $uniqueID
     * @param string $sessionID
     * @param string $eventType
     * @param string $createdTimestamp
     * @param string $lastModifiedTimestamp
     * @param array $eventData
     */
    public function __construct(
        string $uniqueID,
        string $sessionID,
        string $eventType,
        string $createdTimestamp,
        string $lastModifiedTimestamp,
        array $eventData,
        int $visitorID = null,
        int $userID = null,
        int $customerID = null,
        int $itemID = null,
        int $categoryID = null
    ) {
        if (empty($uniqueID) || !in_array($eventType, self::VALID_EVENT_TYPES, true)) {
            throw new InvalidArgumentException('TrackingEvent must have a valid event type and nonempty uniqueID.');
        }
        $this->uuid = (string)$uniqueID;
        $this->sessionID = (string)$sessionID;
        $this->eventType = (string)$eventType;
        $this->createdTimestamp = (string)$createdTimestamp;
        $this->lastModifiedTimestamp = (string)$lastModifiedTimestamp;
        $this->eventData = $eventData;
        $this->visitorID = $visitorID ?: null;
        $this->userID = $userID ?: null;
        $this->customerID = $customerID ?: null;
        $this->itemID = $itemID ?: null;
        $this->categoryID = $categoryID ?: null;

        $this->updateContentHash();
    }

    /**
     * @param array $data
     * @return TrackingEvent
     */
    public function withReplacedData(array $data) : TrackingEvent
    {
        if ($data === $this->eventData) {
            return $this;
        }

        $newModifiedTimestamp = (string)time();
        $instance = new self(
            $this->uuid,
            $this->sessionID,
            $this->eventType,
            $this->createdTimestamp,
            $newModifiedTimestamp,
            $data,
            $this->visitorID,
            $this->userID,
            $this->customerID,
            $this->itemID,
            $this->categoryID
        );
        return $instance;
    }

    /**
     * @param array $data
     * @return TrackingEvent
     */
    public function withArrayMergedData(array $data) :TrackingEvent
    {
        $data = array_merge($this->eventData, $data);
        return $this->withReplacedData($data);
    }

    /**
     * @param $jsonMergePatchDataString
     * @return TrackingEvent
     */
    public function withJSONMergePatchData(string $jsonMergePatchDataString): TrackingEvent
    {
        $encodedDataToPatch = json_encode(
            $this,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );
        $encodedMergedData = json_merge_patch_strings($encodedDataToPatch, $jsonMergePatchDataString);
        //$mergedEventData = json_decode($encodedMergedData,true);
        return TrackingEvent::fromJSON($encodedMergedData);

    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @return string
     */
    public function getCreatedTimestamp(): string
    {
        return $this->createdTimestamp;
    }

    /**
     * @return string
     */
    public function getLastModifiedTimestamp(): string
    {
        return $this->lastModifiedTimestamp;
    }

    /**
     * @return array
     */
    public function getEventData(): array
    {
        return $this->eventData;
    }

    /**
     * @return string
     */
    public function getContentHash(): string
    {
        return $this->contentHash;
    }

    /**
     * @return ?string
     */
    public function getSessionID(): ?string
    {
        return $this->sessionID;
    }

    /**
     * @return int|null
     */
    public function getVisitorID(): ?int
    {
        return $this->visitorID;
    }

    /**
     * @param int|null $visitorID
     */
    public function setVisitorID(int $visitorID): void
    {
        $this->visitorID = $visitorID ? $visitorID : null;
        $this->updateContentHash();
    }

    /**
     * @return int|null
     */
    public function getUserID(): ?int
    {
        return $this->userID;
    }

    /**
     * @param int|null $userID
     */
    public function setUserID(int $userID): void
    {
        $this->userID = $userID ? $userID : null;
        $this->updateContentHash();
    }

    /**
     * @return int|null
     */
    public function getCustomerID(): ?int
    {
        return $this->customerID;
        $this->updateContentHash();
    }

    /**
     * @param int|null $customerID
     */
    public function setCustomerID(int $customerID): void
    {
        $this->customerID = $customerID ? (int)$customerID : null;
        $this->updateContentHash();
    }

    /**
     * @return int|null
     */
    public function getItemID(): ?int
    {
        return $this->itemID;
    }

    /**
     * @param int|null $itemID
     */
    public function setItemID(int $itemID): void
    {
        $this->itemID = $itemID ? (int)$itemID : null;
        $this->updateContentHash();
    }

    /**
     * @return int|null
     */
    public function getCategoryID(): ?int
    {
        return $this->categoryID;
    }

    /**
     * @param int|null $categoryID
     */
    public function setCategoryID(int $categoryID): void
    {
        $this->categoryID = $categoryID ? (int)$categoryID : null;
        $this->updateContentHash();
    }


    /**
     * @param TrackingEvent $event
     * @return bool
     */
    public function hashEquals(TrackingEvent $event): bool
    {
        return $event->getContentHash() === $this->getContentHash();
    }


    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $arr = [
            self::KEY_UUID => $this->getUuid(),
            self::KEY_SESSION_ID => $this->getSessionID(),
            self::KEY_EVENT_TYPE => $this->getEventType(),
            self::KEY_CREATION_TIMESTAMP => $this->getCreatedTimestamp(),
            self::KEY_LAST_MODIFIED_TIMESTAMP => $this->getLastModifiedTimestamp(),
            self::KEY_EVENT_DATA => $this->getEventData(),
        ];

        return $arr;
    }

    /**
     * @param string $json
     * @return TrackingEvent
     */
    public static function fromJSON($json): TrackingEvent
    {
        $deserialized = (array)json_decode($json, true);
        $uniqueID = isset($deserialized[self::KEY_UUID]) ? $deserialized[self::KEY_UUID] : '';
        $sessionIDName = isset($deserialized['session_id_name']) ? $deserialized['session_id_name'] : 'sid';
        $sessionID = isset($deserialized[self::KEY_SESSION_ID]) ? $deserialized[self::KEY_SESSION_ID] : isset($_COOKIE[$sessionIDName]) ? $_COOKIE[$sessionIDName] : session_id(
        );
        $eventType = isset($deserialized[self::KEY_EVENT_TYPE]) ? $deserialized[self::KEY_EVENT_TYPE] : '';
        $creationTimestamp = isset($deserialized[self::KEY_CREATION_TIMESTAMP]) ? $deserialized[self::KEY_CREATION_TIMESTAMP] : time(
        );
        $lastModifiedTimestamp = isset($deserialized[self::KEY_LAST_MODIFIED_TIMESTAMP]) ? $deserialized[self::KEY_LAST_MODIFIED_TIMESTAMP] : $creationTimestamp;
        $data = isset($deserialized[self::KEY_EVENT_DATA]) && is_array(
            $deserialized[self::KEY_EVENT_DATA]
        ) ? $deserialized[self::KEY_EVENT_DATA] : [];
        $visitorID = isset($deserialized[self::KEY_VISITOR_ID]) ? (int)$deserialized[self::KEY_VISITOR_ID] : null;
        $userID = isset($deserialized[self::KEY_USER_ID]) ? (int)$deserialized[self::KEY_USER_ID] : null;
        $customerID = isset($deserialized[self::KEY_CUSTOMER_ID]) ? (int)$deserialized[self::KEY_CUSTOMER_ID] : null;
        $itemID = isset($deserialized[self::KEY_ITEM_ID]) ? (int)$deserialized[self::KEY_ITEM_ID] : null;
        $categoryID = isset($deserialized[self::KEY_CATEGORY_ID]) ? (int)$deserialized[self::KEY_CATEGORY_ID] : null;

        if ($uniqueID && $sessionID && $eventType && $data) {
            $instance = new TrackingEvent(
                $uniqueID,
                $sessionID,
                $eventType,
                $creationTimestamp,
                $lastModifiedTimestamp,
                $data,
                $visitorID,
                $userID,
                $customerID,
                $itemID,
                $categoryID
            );
            return $instance;
        }
        throw new InvalidArgumentException(
            'JSON must contain at least unique_id (string), event_type (string), creation_timestamp (string) and data (key-value list).'
        );
    }

    protected function updateContentHash(): void
    {
        $this->contentHash = md5(
            $this->sessionID . $this->eventType . $this->createdTimestamp . $this->lastModifiedTimestamp . json_encode(
                $this->eventData
            ) . $this->visitorID . $this->userID . $this->customerID
        );
    }

}