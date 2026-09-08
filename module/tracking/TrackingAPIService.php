<?php
namespace DynCom\dc\tracking;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 20.02.2017
 * Time: 16:55
 */

use Dotenv\Dotenv;
use InvalidArgumentException;
use PDO;

/**
 * Class TrackingAPIService
 * @package DynCom\dc\tracking
 */
class TrackingAPIService
{

    protected const TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE = 'config/tracking';
    protected const KEY_UNIQUE_ID = 'uuid';
    protected const KEY_EVENT_TYPE = 'event_type';
    protected const KEY_CREATION_TIMESTAMP = 'creation_timestamp';
    protected const KEY_LAST_MODIFIED_TIMESTAMP = 'last_modified_timestamp';
    protected const KEY_EVENT_DATA = 'event_data';
    protected const KEY_SESSION_ID = 'session_id';
    protected const KEY_VISITOR_ID = 'visitor_id';
    protected const KEY_USER_ID = 'user_id';
    protected const KEY_CUSTOMER_ID = 'customer_id';
    protected const KEY_ITEM_ID = 'item_id';
    protected const KEY_CATEGORY_ID = 'category_id';
    protected const KEY_EXISTS = 'exists';

    protected const QUERY_GET_TRACKING_EVENTS_BY_SESSION_ID = '
             SELECT    
                `uuid`,            
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
            FROM
                `tracking_events`
            WHERE
              `session_id` = :session_id
    ';

    protected const QUERY_GET_TRACKING_EVENTS_BY_EVENT_TYPE = '
             SELECT
                `uuid`,
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
            FROM
                `tracking_events`
            WHERE
              `event_type` = :event_type
    ';

    protected const QUERY_GET_TRACKING_EVENTS_BY_SESSION_ID_EVENT_TYPE = '
             SELECT
                `uuid`,
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
            FROM
                `tracking_events`
            WHERE
                  `event_type` = :event_type
              AND `session_id` = :session_id
    ';

    protected const QUERY_GET_TRACKING_EVENT =
        '
            SELECT                
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
            FROM
                `tracking_events`
            WHERE
              `uuid` = :uuid
        ';


    protected const QUERY_INSERT_TRACKING_EVENT =
        '
            INSERT INTO
              `tracking_events`
            SET
              `uuid` = :uuid,
              `session_id` = :session_id,
              `event_type` = :event_type,
              `creation_timestamp` = :creation_timestamp,
              `last_modified_timestamp` = :creation_timestamp,
              `event_data` = :event_data,                          
              `visitor_id` = :visitor_id,
              `user_id` = :user_id,
              `customer_id` = :customer_id,
              `item_id` = :item_id,
              `category_id` = :category_id
        ';

    protected const QUERY_UPDATE_TRACKING_EVENT = '        
            UPDATE
              `tracking_events`
            SET              
              `event_data` = :event_data,
              `last_modified_timestamp` = :last_modified_timestamp
            WHERE
              `uuid` = :uuid              
        ';

    protected const QUERY_CHECK_EVENT_EXISTS =
        '
        SELECT EXISTS (
            SELECT 1 FROM `tracking_events` WHERE `uuid` = :uuid
        ) AS \'exists\'
        ';


    protected const QUERY_DELETE_EVENT =
        '
            DELETE FROM
              `tracking_events`
            WHERE
              `uuid` = :uuid            
        ';

    protected const QUERY_FIND_BY_VISITOR_ID = '
        SELECT
                `uuid`,            
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
        FROM
          `tracking_events`
        WHERE
          `visitor_id` = :visitor_id
    ';

    protected const QUERY_FIND_LAST_FOR_EVENT_TYPE_VISITOR_ITEM = '
        SELECT
                `uuid`,            
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
        FROM
          `tracking_events`
        WHERE
              `event_type` = :event_type
          AND `visitor_id` = :visitor_id
          AND `item_id` = :item_id
        ORDER BY
          `creation_timestamp` DESC
        LIMIT 1
    ';

    protected const QUERY_FIND_ALL_VISITOR_EVENTS_BY_TYPE_WITHOUT_USER_BEFORE_TIMESTAMP = '
            SELECT
                `uuid`,            
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
        FROM
          `tracking_events`
        WHERE
              `visitor_id` = :visitor_id
          AND `user_id` IS NULL   
          AND `creation_timestamp` < :creation_timestamp
        ORDER BY
          `creation_timestamp` ASC
    ';

    protected const QUERY_FIND_ALL_VISITOR_EVENTS_WITHOUT_CUSTOMER_BEFORE_TIMESTAMP = '
        SELECT
                `uuid`,            
                `event_type`,
                `session_id`,
                `creation_timestamp`,                
                `last_modified_timestamp`,
                `event_data`,
                `visitor_id`,
                `user_id`,
                `customer_id`,
                `item_id`,
                `category_id`
        FROM
          `tracking_events`
        WHERE
              `visitor_id` = :visitor_id
          AND `customer_id` IS NULL   
          AND `creation_timestamp` < :creation_timestamp
        ORDER BY
          `creation_timestamp` ASC
    ';


    /**
     * @var PDO
     */
    protected $db;


    /**
     * TrackingAPIService constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize(): void
    {
        //Load env
        $this->loadEnvVariables();

        //Get connection data from env and set db
        $host = getenv('TRACKING_MYSQL_DB_HOST');
        $port = getenv('TRACKING_MYSQL_DB_PORT');
        $schema = getenv('TRACKING_MYSQL_DB_SCHEMA');
        $user = getenv('TRACKING_MYSQL_DB_USER');
        $pass = getenv('TRACKING_MYSQL_DB_PASS');


        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        $dsn = 'mysql:dbname=' . $schema . ';host=' . $host . ';port=' . (string)$port . ';charset=utf8mb4';
        if ($pass && $user && $schema && $host) {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $this->db = $pdo;
        }
    }

    protected function loadEnvVariables(): void
    {
        $projectBaseDir = dirname(dirname(__DIR__));
        $envDir = $projectBaseDir . DIRECTORY_SEPARATOR . self::TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE;
        $isFileReadable = is_dir($envDir) && is_file($envDir . DIRECTORY_SEPARATOR . '.env') && is_readable($envDir . DIRECTORY_SEPARATOR . '.env');
        if ($isFileReadable) {
            $envLoader = new Dotenv($envDir);
            $envLoader->load();
        } else {
            throw new InvalidArgumentException('Could not load environment file at [' . $envDir . DIRECTORY_SEPARATOR . '.env' . '].');
        }
    }

    /**
     * @param $uniqueID
     * @return null|TrackingEvent
     */
    public function getTrackingEventByUniqueID(string $uniqueID): ?TrackingEvent
    {
        if (!$this->db) {
            return null;
        }
        //Memoize
        static $memo;
        if (null === $memo) {
            $memo = [];
        }
        if (array_key_exists($uniqueID, $memo)) {
            return $memo[$uniqueID];
        }

        $stmt = $this->db->prepare(self::QUERY_GET_TRACKING_EVENT);
        $stmt->bindValue(':' . self::KEY_UNIQUE_ID, $uniqueID, PDO::PARAM_STR);
        $result = null;
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (
                is_array($row)
                &&  array_key_exists(self::KEY_EVENT_DATA, $row)
                && !empty($row[self::KEY_EVENT_DATA])
                && array_key_exists(self::KEY_SESSION_ID, $row)
                && !empty($row[self::KEY_SESSION_ID])
                && array_key_exists(self::KEY_EVENT_TYPE, $row)
                && !empty($row[self::KEY_EVENT_TYPE])
                && array_key_exists(self::KEY_CREATION_TIMESTAMP, $row)
                && !empty($row[self::KEY_CREATION_TIMESTAMP])
                && array_key_exists(self::KEY_EVENT_DATA, $row)
                && !empty($row[self::KEY_EVENT_DATA])

            ) {
                $sessionID = $row[self::KEY_SESSION_ID];
                $eventType = $row[self::KEY_EVENT_TYPE];
                $creationTimestamp = (string)$row[self::KEY_CREATION_TIMESTAMP];
                $lastModifiedTimestamp = isset($row[self::KEY_LAST_MODIFIED_TIMESTAMP]) ? (string)($row[self::KEY_LAST_MODIFIED_TIMESTAMP]) : $creationTimestamp;
                $data = json_decode($row[self::KEY_EVENT_DATA], true);
                $visitorID = isset($row[self::KEY_VISITOR_ID]) ? (int)$row[self::KEY_VISITOR_ID] : null;
                $userID = isset($row[self::KEY_USER_ID]) ? (int)$row[self::KEY_USER_ID] : null;
                $customerID = isset($row[self::KEY_CUSTOMER_ID]) ? (int)$row[self::KEY_CUSTOMER_ID] : null;
                $itemID = isset($row[self::KEY_ITEM_ID]) ? (int)$row[self::KEY_ITEM_ID] : null;
                $categoryID = isset($row[self::KEY_CATEGORY_ID]) ? (int)$row[self::KEY_CATEGORY_ID] : null;
                $result = new TrackingEvent(
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
            }
        }
        $memo[$uniqueID] = $result;
        return $result;
    }

    /**
     * @param TrackingEvent $trackingEvent
     * @return bool
     */
    public function addTrackingEvent(TrackingEvent $trackingEvent): bool
    {
        if (!$this->db) {
            return false;
        }
        $uuid = $trackingEvent->getUuid();
        $sessionID = $trackingEvent->getSessionID();
        $eventType = $trackingEvent->getEventType();
        $creationTimestamp = $trackingEvent->getCreatedTimestamp();
        $lastModifiedTimestamp = $trackingEvent->getLastModifiedTimestamp();
        $data = $trackingEvent->getEventData();
        $jsonEventData = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );

        $addStmt = $this->db->prepare(self::QUERY_INSERT_TRACKING_EVENT);
        $addStmt->bindValue(':' . self::KEY_UNIQUE_ID, $uuid, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_SESSION_ID, $sessionID, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_EVENT_TYPE, $eventType, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_CREATION_TIMESTAMP, $creationTimestamp, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_LAST_MODIFIED_TIMESTAMP, $lastModifiedTimestamp, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_EVENT_DATA, $jsonEventData, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_VISITOR_ID, $trackingEvent->getVisitorID(), PDO::PARAM_INT);
        $addStmt->bindValue(':' . self::KEY_USER_ID, $trackingEvent->getUserID(), PDO::PARAM_INT);
        $addStmt->bindValue(':' . self::KEY_CUSTOMER_ID, $trackingEvent->getCustomerID(), PDO::PARAM_INT);
        $addStmt->bindValue(':' . self::KEY_ITEM_ID, $trackingEvent->getItemID(), PDO::PARAM_INT);
        $addStmt->bindValue(':' . self::KEY_CATEGORY_ID, $trackingEvent->getCategoryID(), PDO::PARAM_INT);
        return (bool)$addStmt->execute();
    }


    /**
     * @param TrackingEvent $trackingEvent
     * @return bool
     */
    public function putTrackingEvent(TrackingEvent $trackingEvent): bool
    {
        if (!$this->db) {
            return false;
        }
        $uuid = $trackingEvent->getUuid();
        $sessionID = $trackingEvent->getSessionID();
        $createdTimestamp = $trackingEvent->getCreatedTimestamp();
        $eventData = $trackingEvent->getEventData();

        $exists = $this->existsTrackingEventForUniqueID($uuid);

        if (empty($eventData) || (!$exists && (empty($uuid) || empty($sessionID) || empty($createdTimestamp)))) {
            throw new InvalidArgumentException(
                'Can only patch event_data for existing resources. For new resources, uuid, session_id, creation_timestamp and event_data must be set.'
            );
        }

        $lastModifiedTimestamp = (string)time();
        $jsonEventData = json_encode(
            $eventData,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );

        $addStmt = $this->db->prepare(self::QUERY_UPDATE_TRACKING_EVENT);
        $addStmt->bindValue(':' . self::KEY_UNIQUE_ID, $uuid, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_LAST_MODIFIED_TIMESTAMP, $lastModifiedTimestamp, PDO::PARAM_STR);
        $addStmt->bindValue(':' . self::KEY_EVENT_DATA, $jsonEventData, PDO::PARAM_STR);

        return (bool)$addStmt->execute();
    }

    /**
     * @param string $uniqueID
     * @param array $patchData
     * @return bool
     * @throws ErrorException
     */
    public function patchTrackingEvent(string $uniqueID, array $patchData): bool
    {
        if (!$this->db) {
            return false;
        }
        if (array_key_exists(self::KEY_UNIQUE_ID, $patchData)) {
            throw new InvalidArgumentException('Cannot patch uuid field. Only event_data field is patchable.');
        }
        if (array_key_exists(self::KEY_EVENT_TYPE, $patchData)) {
            throw new InvalidArgumentException('Cannot patch event_type field. Only event_data field is patchable.');
        }
        if (array_key_exists(self::KEY_CREATION_TIMESTAMP, $patchData)) {
            throw new InvalidArgumentException(
                'Cannot patch creation_timestamp field. Only event_data field is patchable.'
            );
        }
        if (array_key_exists(self::KEY_LAST_MODIFIED_TIMESTAMP, $patchData)) {
            throw new InvalidArgumentException(
                'Cannot patch last_modified_timestamp field. Only event_data field is patchable.'
            );
        }
        if (!array_key_exists(self::KEY_EVENT_DATA, $patchData)) {
            return false;
        }
        $patchEventData = $patchData[self::KEY_EVENT_DATA];
        $getStmt = $this->db->prepare(self::QUERY_GET_TRACKING_EVENT);
        $getStmt->bindValue(':' . self::KEY_UNIQUE_ID, $uniqueID, PDO::PARAM_STR);
        $result = $getStmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($result);

        if (1 === $count) {
            $oldEventData = json_decode($result[0][self::KEY_EVENT_DATA], true);
            $newEventData = array_merge($oldEventData, $patchEventData);
            $newJsonEventData = json_encode(
                $newEventData,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
            );
            $updStmt = $this->db->prepare(self::QUERY_UPDATE_TRACKING_EVENT);
            $updStmt->bindValue(':' . self::KEY_UNIQUE_ID, $uniqueID, PDO::PARAM_STR);
            $updStmt->bindValue(':' . self::KEY_LAST_MODIFIED_TIMESTAMP, (string)time(), PDO::PARAM_STR);
            $updStmt->bindValue(':' . self::KEY_EVENT_DATA, $newJsonEventData, PDO::PARAM_STR);

            return (bool)$updStmt->execute();
        } else {
            throw new ErrorException('No event with given UUID found.');
        }
    }

    /**
     * @param string $uniqueID
     * @return bool
     */
    public function deleteTrackingEvent(string $uniqueID): bool
    {
        if (!$this->db) {
            return false;
        }
        $delStmt = $this->db->prepare(self::QUERY_DELETE_EVENT);
        $delStmt->bindValue(':' . self::KEY_UNIQUE_ID, $uniqueID);
        return (bool)$delStmt->execute();
    }


    /**
     * @param string $uniqueID
     * @return bool
     */
    public function existsTrackingEventForUniqueID(string $uniqueID): bool
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_CHECK_EVENT_EXISTS);
        $stmt->bindValue(':' . self::KEY_UNIQUE_ID, $uniqueID, PDO::PARAM_STR);
        $result = false;
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (array_key_exists(self::KEY_EXISTS, $row)) {
            $result = (bool)$row[self::KEY_EXISTS];
        }
        return $result;
    }

    public function findBySessionID(string $sessionID): ?array
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TRACKING_EVENTS_BY_SESSION_ID);
        $stmt->bindValue(':' . self::KEY_SESSION_ID, $sessionID, PDO::PARAM_STR);
        $rows = [];
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($row['uuid']) && $row['uuid']) {
                    $evt = new TrackingEvent(
                        $row['uuid'],
                        $row['session_id'],
                        $row['event_type'],
                        $row['creation_timestamp'],
                        $row['last_modified_timestamp'],
                        json_decode($row['event_data'], true),
                        $row['visitor_id'],
                        $row['user_id'],
                        $row['customer_id'],
                        $row['item_id'],
                        $row['category_id']
                    );
                    $rows[] = $evt;
                }
            }
        }
        return $rows;
    }

    public function findByEventType(string $eventType): ?array
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TRACKING_EVENTS_BY_EVENT_TYPE);
        $stmt->bindValue(':' . self::KEY_EVENT_TYPE, $eventType, PDO::PARAM_STR);
        $rows = [];
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($row['uuid']) && $row['uuid']) {
                    $evt = new TrackingEvent(
                        $row['uuid'],
                        $row['session_id'],
                        $row['event_type'],
                        $row['creation_timestamp'],
                        $row['last_modified_timestamp'],
                        json_decode($row['event_data'], true),
                        $row['visitor_id'],
                        $row['user_id'],
                        $row['customer_id'],
                        $row['item_id'],
                        $row['category_id']
                    );
                    $rows[] = $evt;
                }
            }
        }
        return $rows;
    }

    public function findBySessionIDEventType(string $sessionID, string $eventType): ?array
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TRACKING_EVENTS_BY_EVENT_TYPE);
        $stmt->bindValue(':' . self::KEY_SESSION_ID, $sessionID, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_EVENT_TYPE, $eventType, PDO::PARAM_STR);
        $rows = [];
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($row['uuid']) && $row['uuid']) {
                    $evt = new TrackingEvent(
                        $row['uuid'],
                        $row['session_id'],
                        $row['event_type'],
                        $row['creation_timestamp'],
                        $row['last_modified_timestamp'],
                        json_decode($row['event_data'], true),
                        $row['visitor_id'],
                        $row['user_id'],
                        $row['customer_id'],
                        $row['item_id'],
                        $row['category_id']
                    );
                    $rows[] = $evt;
                }
            }
        }
        return $rows;
    }

    public function findLastByEventTypeVisitorItem(string $eventType, int $visitorID, int $itemID): ?TrackingEvent
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_FIND_LAST_FOR_EVENT_TYPE_VISITOR_ITEM);
        $stmt->bindValue(':' . self::KEY_EVENT_TYPE, $eventType, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_VISITOR_ID, (int)$visitorID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_ITEM_ID, (int)$itemID, PDO::PARAM_INT);
        $evt = null;
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($row['uuid']) && $row['uuid']) {
                $evt = new TrackingEvent(
                    $row['uuid'],
                    $row['session_id'],
                    $row['event_type'],
                    $row['creation_timestamp'],
                    $row['last_modified_timestamp'],
                    json_decode($row['event_data'], true),
                    $row['visitor_id'],
                    $row['user_id'],
                    $row['customer_id'],
                    $row['item_id'],
                    $row['category_id']
                );
            }
        }
        return $evt;
    }

    public function getAllVisitorEventsWithoutUserBeforeTimestamp(int $visitorID, string $beforeTimestamp): ?array
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_FIND_ALL_VISITOR_EVENTS_BY_TYPE_WITHOUT_USER_BEFORE_TIMESTAMP);
        $stmt->bindValue(':' . self::KEY_VISITOR_ID, (int)$visitorID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_CREATION_TIMESTAMP, $beforeTimestamp, PDO::PARAM_STR);
        $evt = null;
        $collection = [];
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($row['uuid']) && $row['uuid']) {
                    $evt = new TrackingEvent(
                        $row['uuid'],
                        $row['session_id'],
                        $row['event_type'],
                        $row['creation_timestamp'],
                        $row['last_modified_timestamp'],
                        json_decode($row['event_data'], true),
                        $row['visitor_id'],
                        $row['user_id'],
                        $row['customer_id'],
                        $row['item_id'],
                        $row['category_id']
                    );
                    $collection[] = $evt;
                }
            }
        }
        return $collection;
    }

    public function getAllVisitorEventsWithoutCustomerBeforeTimestamp(int $visitorID, string $beforeTimestamp): ?array
    {
        if (!$this->db) {
            return null;
        }
        $stmt = $this->db->prepare(self::QUERY_FIND_ALL_VISITOR_EVENTS_WITHOUT_CUSTOMER_BEFORE_TIMESTAMP);
        $stmt->bindValue(':' . self::KEY_VISITOR_ID, (int)$visitorID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_CREATION_TIMESTAMP, $beforeTimestamp, PDO::PARAM_STR);
        $evt = null;
        $collection = [];
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($row['uuid']) && $row['uuid']) {
                    $evt = new TrackingEvent(
                        $row['uuid'],
                        $row['session_id'],
                        $row['event_type'],
                        $row['creation_timestamp'],
                        $row['last_modified_timestamp'],
                        json_decode($row['event_data'], true),
                        $row['visitor_id'],
                        $row['user_id'],
                        $row['customer_id'],
                        $row['item_id'],
                        $row['category_id']
                    );
                    $collection[] = $evt;
                }
            }
        }
        return $collection;
    }


}