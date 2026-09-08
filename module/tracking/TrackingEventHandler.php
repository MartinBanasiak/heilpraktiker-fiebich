<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 21.02.2017
 * Time: 14:40
 */
namespace DynCom\dc\tracking;

use PDO, InvalidArgumentException, ErrorException;
use Dotenv\Dotenv;

/**
 * Class TrackingEventHandler
 * @package DynCom\dc\tracking
 */
class TrackingEventHandler
{
    const TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE = 'config/tracking';

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

    const STATEMENT_INSERT_OR_UPDATE_ITEM = '
      
        INSERT INTO 
          `item`
            (
              `id`,
              `item_no`,
              `description`,
              `summary`,
              `variant_type`,
              `retail_price`,
              `base_price`,
              `meta_keywords`,
              `meta_description`,
              `item_attributes`,
              `item_descriptions`,
              `item_reviews`,
              `item_view_seconds_duration`,
              `item_view_counter`,
              `item_details_view_seconds_duration`,
              `item_details_view_counter`
            )
        VALUES
            (
                :id,
                :item_no,
                :description,
                :summary,
                :variant_type,
                :retail_price,
                :base_price,
                :meta_keywords,
                :meta_description,
                :item_attributes,
                :item_descriptions,
                :item_reviews,
                :item_view_seconds_duration,
                :item_view_counter,
                :item_details_view_seconds_duration,
                :item_details_view_counter
                
            )
            ON DUPLICATE KEY UPDATE
              item_view_seconds_duration = item_view_seconds_duration + :item_view_seconds_duration ,
              item_view_counter     = item_view_counter + :item_view_counter,
              item_details_view_seconds_duration = item_details_view_seconds_duration + :item_details_view_seconds_duration ,
              item_details_view_counter     = item_details_view_counter + :item_details_view_counter
    ';

    const QUERY_GET_BASIC_CATEGORY_FIELDS_BY_ID = '
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


    const STATEMENT_INSERT_OR_UPDATE_CATEGORY = '
      
        INSERT INTO 
          `category`
            (
              `id`,
              `name`,
              `code`,
              `category_description`,
              `category_description_2`,
              `meta_keywords`,
              `meta_description`,
              `category_view_seconds_duration`,
              `category_view_counter`
            )
        VALUES
            (
                :id,
                :name,
                :code,
                :category_description,
                :category_description_2,
                :meta_keywords,
                :meta_description,
                :category_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              category_view_seconds_duration = category_view_seconds_duration + :category_view_seconds_duration ,
              category_view_counter     = category_view_counter + 1
            
    ';
    const STATEMENT_INSERT_OR_UPDATE_VISITOR = '
      
        INSERT INTO 
          `visitor`
            (
              `id`,
              `last_ipv4_anon`,
              `last_ipv6_anon`,
              `view_seconds_duration`,
              `view_counter`
            )
        VALUES
            (
                :id,
                :last_ipv4_anon,
                :last_ipv6_anon,
                :view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              view_seconds_duration = view_seconds_duration + :view_seconds_duration,
              view_counter    = view_counter + 1 
    ';

    const STATEMENT_INSERT_OR_UPDATE_VISITOR_ITEM = '
      
        INSERT INTO 
          `visitor_item`
            (
              `visitor_id`,
              `item_id`,
              `view_seconds_duration`,
              `item_view_counter`,
              `item_details_view_seconds_duration`,
              `item_details_view_counter`
            )
        VALUES
            (
                :visitor_id,
                :item_id,
                :view_seconds_duration,
                :item_view_counter,
                :item_details_view_seconds_duration,
                :item_details_view_counter
            )
            ON DUPLICATE KEY UPDATE
              view_seconds_duration = view_seconds_duration + :view_seconds_duration,
              item_view_counter    = item_view_counter + :item_view_counter,
              item_details_view_seconds_duration = item_details_view_seconds_duration + :item_details_view_seconds_duration,
              item_details_view_counter = item_details_view_counter + :item_details_view_counter
                
    ';

    const STATEMENT_INSERT_OR_UPDATE_VISITOR_CATEGORY = '
      
        INSERT INTO 
          `visitor_category`
            (
              `visitor_id`,
              `category_id`,
              `visitor_category_view_seconds_duration`,
              `visitor_category_view_counter`
            )
        VALUES
            (
                :visitor_id,
                :category_id,
                :visitor_category_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              visitor_category_view_seconds_duration = visitor_category_view_seconds_duration + :visitor_category_view_seconds_duration,
              visitor_category_view_counter  = visitor_category_view_counter + 1 
    ';

    const QUERY_GET_BASIC_USER_FIELDS_BY_ID = '
        SELECT
            customer_no,
            name,
            email,
            last_visitor_id
        FROM
          shop_user
        WHERE
          id = :user_id       
    ';

    const QUERY_GET_BASIC_CUSTOMER_FIELDS_BY_ID = '
        SELECT
            customer_no,
            name,
            name_2,
            address,
            post_code,
            city,
            country,
            phone_no,
            email,
            lastname,
            company_name
        FROM
          shop_customer
        WHERE
          id = :customer_id       
    ';

    const STATEMENT_INSERT_OR_UPDATE_USER = '
      
        INSERT INTO 
          `user`
            (
              `id`,
              `customer_no`,
              `name`,
              `email`,
              `last_visitor_id`,
              `user_view_seconds_duration`,
              `user_view_counter`
            )
        VALUES
            (
                :id,
                :customer_no,
                :name,
                :email,
                :last_visitor_id,
                :user_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
            
             user_view_seconds_duration = user_view_seconds_duration + :user_view_seconds_duration,
              user_view_counter     = user_view_counter + 1 
    ';

    const STATEMENT_INSERT_OR_UPDATE_CUSTOMER = '
      
        INSERT INTO 
          `customer`
            (
              `id`,
              `customer_no`,
              `name`,
              `name_2`,
              `address`,
              `post_code`,
              `city`,
              `country`,
              `phone_no`,
              `email`,
              `lastname`,
              `company_name`,
              `customer_view_seconds_duration`,
              `customer_view_counter`
            )
        VALUES
            (
                :id,
                :customer_no,
                :name,
                :name_2,
                :address,
                :post_code,
                :city,
                :country,
                :phone_no,
                :email,
                :lastname,
                :company_name,
                :customer_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              customer_view_seconds_duration = customer_view_seconds_duration + :customer_view_seconds_duration ,
              customer_view_counter  = customer_view_counter + 1 
    ';

    const STATEMENT_INSERT_OR_UPDATE_USER_ITEM = '
      
        INSERT INTO 
          `user_item`
            (
              `user_id`,
              `item_id`,
              `user_item_view_seconds_duration`,
              `user_item_view_counter`,
              `user_item_details_view_seconds_duration`,
              `user_item_details_view_counter`
            )
        VALUES
            (
                :user_id,
                :item_id,
                :user_item_view_seconds_duration,
                :user_item_view_counter,
                :user_item_details_view_seconds_duration,
                :user_item_details_view_counter
            )
            ON DUPLICATE KEY UPDATE
              user_item_view_seconds_duration = user_item_view_seconds_duration + :user_item_view_seconds_duration,
              user_item_view_counter    = user_item_view_counter + :user_item_view_counter,
              user_item_details_view_seconds_duration = user_item_details_view_seconds_duration + :user_item_details_view_seconds_duration,
              user_item_details_view_counter = user_item_details_view_counter + :user_item_details_view_counter
    ';

    const STATEMENT_INSERT_OR_UPDATE_USER_CATEGORY = '
      
        INSERT INTO 
          `user_category`
            (
              `user_id`,
              `category_id`,
              `user_category_view_seconds_duration`,
              `user_category_view_counter`
            )
        VALUES
            (
                :user_id,
                :category_id,
                :user_category_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              user_category_view_seconds_duration = user_category_view_seconds_duration + :user_category_view_seconds_duration ,
              user_category_view_counter  = user_category_view_counter + 1 
    ';


    const STATEMENT_INSERT_OR_UPDATE_CUSTOMER_ITEM = '
      
        INSERT INTO 
          `customer_item`
            (
              `customer_id`,
              `item_id`,
              `customer_item_view_seconds_duration`,
              `customer_item_view_counter`,
              `customer_item_details_view_seconds_duration`,
              `customer_item_details_view_counter`
            )
        VALUES
            (
                :customer_id,
                :item_id,
                :customer_item_view_seconds_duration,
                :customer_item_view_counter,
                :customer_item_details_view_seconds_duration,
                :customer_item_details_view_counter
            )
            ON DUPLICATE KEY UPDATE
              customer_item_view_seconds_duration = customer_item_view_seconds_duration + :customer_item_view_seconds_duration,
              customer_item_view_counter    = customer_item_view_counter + :customer_item_view_counter,
              customer_item_details_view_seconds_duration = customer_item_details_view_seconds_duration + :customer_item_details_view_seconds_duration,
              customer_item_details_view_counter = customer_item_details_view_counter + :customer_item_details_view_counter
    ';

    const STATEMENT_INSERT_OR_UPDATE_CUSTOMER_CATEGORY = '
      
        INSERT INTO 
          `customer_category`
            (
              `customer_id`,
              `category_id`,
              `customer_category_view_seconds_duration`,
              `customer_category_view_counter`
            )
        VALUES
            (
                :customer_id,
                :category_id,
                :customer_category_view_seconds_duration,
                1
            )
            ON DUPLICATE KEY UPDATE
              customer_category_view_seconds_duration = + customer_category_view_seconds_duration + :customer_category_view_seconds_duration ,
              customer_category_view_counter  = customer_category_view_counter + 1 
    ';

    const QUERY_GET_BASIC_VISITOR_FIELDS_BY_ID = '
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


    /**
     * @var PDO
     */
    protected $shopDbConnection;
    /**
     * @var PDO
     */
    protected $trackingDbConnection;


    /**
     * TrackingAPIService constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize()
    {
        //Load env
        $this->loadEnvVariables();

        //Get connection data from env and set db
        $host = getenv('TRACKING_DB_HOST');
        $port = getenv('TRACKING_DB_PORT');
        $schema = getenv('TRACKING_DB_SCHEMA');
        $user = getenv('TRACKING_DB_USER');
        $pass = getenv('TRACKING_DB_PASS');


        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        $dsn = 'mysql:dbname=' . $schema . ';host=' . $host . ';port=' . (string)$port . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $user, $pass, $options);
        $this->trackingDbConnection = $pdo;


        $host = getenv('MAIN_MYSQL_DB_HOST');
        $port = getenv('MAIN_MYSQL_DB_PORT');
        $schema = getenv('MAIN_MYSQL_DB_SCHEMA');
        $user = getenv('MAIN_MYSQL_DB_USER');
        $pass = getenv('MAIN_MYSQL_DB_PASS');


        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        $dsn = 'mysql:dbname=' . $schema . ';host=' . $host . ';port=' . (string)$port . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $user, $pass, $options);
        $this->shopDbConnection = $pdo;
    }

    protected function loadEnvVariables()
    {
        $projectBaseDir = dirname(dirname(__DIR__));
        $envDir = $projectBaseDir . DIRECTORY_SEPARATOR . self::TRACKING_ENV_FILE_DIR_RELATIVE_TO_PROJECT_BASE;
        $envLoader = new Dotenv($envDir);
        $envLoader->load();
    }

    /**
     * @param TrackingEvent $event
     */
    public function handleTrackingEvent(TrackingEvent $event)
    {
        /*  $this->expandItemIDs($event);
          $this->expandCategoryIDs($event);
          $this->handleGlobalCategoryCounters($event);
          $this->handleGlobalItemCounters($event);
          $this->handleGlobalUserCounters($event);
          $this->handleGlobalUserCategoryCounters($event);
          $this->handleGlobalUserItemCounters($event);*/

        $this->handleGlobalUserCounters($event);
        $this->handleGlobalCustomerCounters($event);
        $this->handleGlobalVisitorCounters($event);
        $this->handleGlobalCategoryCounters($event);
        $this->handleGlobalItemCounters($event);

    }

    /**
     * @param TrackingEvent $event
     */
    public function expandItemIDs(TrackingEvent $event)
    {
        $data = $event->getEventData();
        if (array_key_exists('linked_item_ids', $data) && is_array($data['linked_item_ids'])) {
            $items = [];
            foreach ($data['linked_item_ids'] as $itemID) {
                $itemData = $this->getItemTrackingDataByID($itemID);
                $items[] = $itemData;
            }
            unset($data['linked_item_ids']);
            $data['linked_items'] = $items;
        }
        if (array_key_exists('current_item_id',$data) && !empty($data['current_item_id'])) {
            $data['current_item'] = $this->getItemTrackingDataByID($data['current_item_id']);
            unset($data['current_item_id']);
        }
    }

    /**
     * @param array $list
     */
    public function expandCategoryIDs(array $list)
    {
    /*    $data = $event->getEventData();
        if (array_key_exists('current_category_id') && !empty($data['current_category_id'])) {
            $data['current_category'] = $this->getCategoryTrackingDataByID($data['current_category_id']);
            unset($data['current_category_id']);
        }*/
    }

    /**
     * @param TrackingEvent $event
     */
    public function handleGlobalCategoryCounters(TrackingEvent $event)
    {
        $data = $event->getEventData();
        $categoryViewSeconds = $data['active_page_view_time'];
        if (array_key_exists('current_category_id', $data)) {
            $categoryId = $data['current_category_id'];
            $categoryData = $this->getCategoryTrackingDataByID($categoryId);
            $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_CATEGORY);
            $addStmt->bindValue(':id', $categoryId, PDO::PARAM_STR);
            $addStmt->bindValue(':name', $categoryData['name'], PDO::PARAM_STR);
            $addStmt->bindValue(':code', $categoryData['code'], PDO::PARAM_STR);
            $addStmt->bindValue(':category_description', $categoryData['category_description'], PDO::PARAM_STR);
            $addStmt->bindValue(':category_description_2', $categoryData['category_description_2'], PDO::PARAM_STR);
            $addStmt->bindValue(':meta_keywords', $categoryData['meta_keywords'], PDO::PARAM_STR);
            $addStmt->bindValue(':meta_description', $categoryData['meta_description'], PDO::PARAM_STR);
            $addStmt->bindValue(':category_view_seconds_duration', $categoryViewSeconds, PDO::PARAM_STR);

            $result = $addStmt->execute();

            if (array_key_exists('current_visitor_id', $data) && $data['current_visitor_id'] !== null && $result){
                $this->handleGlobalVisitorCategoryCounters($data['current_visitor_id'], $categoryId, $categoryViewSeconds);
            }
            if (array_key_exists('current_user_id', $data) && $data['current_user_id'] !== null && $result) {
                $this->handleGlobalUserCategoryCounters($data['current_user_id'], $categoryId, $categoryViewSeconds);
            }
            if (array_key_exists('current_customer_id', $data) && $data['current_customer_id'] !== null && $result) {
                $this->handleGlobalCustomerCategoryCounters($data['current_customer_id'], $categoryId, $categoryViewSeconds);
            }
        }
    }

    /**
     * @param TrackingEvent $event
     */
    public function handleGlobalItemCounters(TrackingEvent $event)
    {
        $data = $event->getEventData();
        $currentItemId = 0;
        if(array_key_exists('current_item_id', $data))
        {
            $currentItemId = $data['current_item_id'] ;
        }

        if (array_key_exists('linked_item_ids', $data) && is_array($data['linked_item_ids'])) {
            foreach ($data['linked_item_ids'] as $itemID) {

                $itemData = $this->getItemTrackingDataByID($itemID);
                $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_ITEM);
                $addStmt->bindValue(':id', $itemID, PDO::PARAM_STR);
                $addStmt->bindValue(':item_no', $itemData['item_no'], PDO::PARAM_STR);
                $addStmt->bindValue(':description', $itemData['description'], PDO::PARAM_STR);
                $addStmt->bindValue(':summary', $itemData['summary'], PDO::PARAM_STR);
                $addStmt->bindValue(':variant_type', $itemData['variant_type'], PDO::PARAM_STR);
                $addStmt->bindValue(':retail_price', $itemData['retail_price'], PDO::PARAM_STR);
                $addStmt->bindValue(':base_price', $itemData['base_price'], PDO::PARAM_STR);
                $addStmt->bindValue(':meta_keywords', $itemData['meta_keywords'], PDO::PARAM_STR);
                $addStmt->bindValue(':meta_description', $itemData['meta_description'], PDO::PARAM_STR);
                $addStmt->bindValue(':item_attributes', $itemData['item_attributes'], PDO::PARAM_STR);
                $addStmt->bindValue(':item_descriptions', $itemData['item_descriptions'], PDO::PARAM_STR);
                $addStmt->bindValue(':item_reviews', $itemData['item_reviews'], PDO::PARAM_STR);

                $itemViewSeconds = $data['active_page_view_time'];
                $itemViewCounter = 1;
                $itemCardViewSeconds = 0;
                $itemCardCounter = 0;
                if( $itemID == $currentItemId && array_key_exists('active_page_view_time', $data))
                {
                    $itemCardCounter = 1;
                    $itemCardViewSeconds = $data['active_page_view_time'] ;

                    $itemViewSeconds = 0;
                    $itemViewCounter = 0;
                }

                $addStmt->bindValue(':item_view_seconds_duration',$itemViewSeconds, PDO::PARAM_STR);
                $addStmt->bindValue(':item_view_counter',$itemViewCounter, PDO::PARAM_STR);

                $addStmt->bindValue(':item_details_view_seconds_duration',$itemCardViewSeconds, PDO::PARAM_STR);
                $addStmt->bindValue(':item_details_view_counter',$itemCardCounter, PDO::PARAM_STR);

                $result = $addStmt->execute();

                if (array_key_exists('current_visitor_id', $data) && $data['current_visitor_id'] !== null && $result) {
                    $this->handleGlobalVisitorItemCounters($data['current_visitor_id'], $itemID, $itemViewSeconds,$itemCardViewSeconds);
                }
                if (array_key_exists('current_user_id', $data) && $data['current_user_id'] !== null && $result) {
                    $this->handleGlobalUserItemCounters($data['current_user_id'], $itemID, $itemViewSeconds,$itemCardViewSeconds);
                }
                if (array_key_exists('current_customer_id', $data) && $data['current_customer_id'] !== null && $result) {
                    $this->handleGlobalCustomerItemCounters($data['current_customer_id'], $itemID, $itemViewSeconds,$itemCardViewSeconds);
                }
            }

        }

    }

    /**
     * @param TrackingEvent $event
     */
    public function handleGlobalUserCounters(TrackingEvent $event)
    {
        $data = $event->getEventData();
        if (array_key_exists('current_user_id', $data) && $data['current_user_id'] !== null) {
            $userId = $data['current_user_id'];
            $userData = $this->getUserTrackingDataByID($userId);

            $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_USER);
            $addStmt->bindValue(':id', $userId, PDO::PARAM_STR);
            $addStmt->bindValue(':customer_no', $userData['customer_no'], PDO::PARAM_STR);
            $addStmt->bindValue(':name', $userData['name'], PDO::PARAM_STR);
            $addStmt->bindValue(':email', $userData['email'], PDO::PARAM_STR);
            $addStmt->bindValue(':last_visitor_id', $userData['last_visitor_id'], PDO::PARAM_STR);
            $addStmt->bindValue(':user_view_seconds_duration', $data['active_page_view_time'], PDO::PARAM_STR);
            $result = $addStmt->execute();
        }
    }

    /**
     * @param TrackingEvent $event
     */
    public function handleGlobalCustomerCounters(TrackingEvent $event)
    {
        $data = $event->getEventData();
        if (array_key_exists('current_customer_id', $data) && $data['current_customer_id'] !== null) {
            $customerId = $data['current_customer_id'];
            $customerData = $this->getCustomerTrackingDataByID($customerId);

            $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_CUSTOMER);
            $addStmt->bindValue(':id', $customerId, PDO::PARAM_STR);
            $addStmt->bindValue(':customer_no', $customerData['customer_no'], PDO::PARAM_STR);
            $addStmt->bindValue(':name', $customerData['name'], PDO::PARAM_STR);
            $addStmt->bindValue(':name_2', $customerData['name_2'], PDO::PARAM_STR);
            $addStmt->bindValue(':address', $customerData['address'], PDO::PARAM_STR);
            $addStmt->bindValue(':post_code', $customerData['post_code'], PDO::PARAM_STR);
            $addStmt->bindValue(':city', $customerData['city'], PDO::PARAM_STR);
            $addStmt->bindValue(':country', $customerData['country'], PDO::PARAM_STR);
            $addStmt->bindValue(':phone_no', $customerData['phone_no'], PDO::PARAM_STR);
            $addStmt->bindValue(':email', $customerData['email'], PDO::PARAM_STR);
            $addStmt->bindValue(':lastname', $customerData['name'], PDO::PARAM_STR);
            $addStmt->bindValue(':company_name', $customerData['company_name'], PDO::PARAM_STR);
            $addStmt->bindValue(':customer_view_seconds_duration', $data['active_page_view_time'], PDO::PARAM_STR);

            $addStmt->execute();
        }
    }

    /**
     * @param TrackingEvent $event
     */
    public function handleGlobalVisitorCounters(TrackingEvent $event)
    {
        $data = $event->getEventData();
        if (array_key_exists('current_visitor_id', $data) && $data['current_visitor_id'] !== null) {
            $visitorId = $data['current_visitor_id'];
            $visitorData = $this->getVisitorTrackingDataByID($visitorId);

            $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_VISITOR);
            $addStmt->bindValue(':id', $visitorId, PDO::PARAM_STR);
            $addStmt->bindValue(':last_ipv4_anon', $visitorData['last_ipv4_anon'], PDO::PARAM_STR);
            $addStmt->bindValue(':last_ipv6_anon', $visitorData['last_ipv6_anon'], PDO::PARAM_STR);
            $addStmt->bindValue(':view_seconds_duration', $data['active_page_view_time'], PDO::PARAM_STR);
            $addStmt->execute();
        }
    }



    public function handleGlobalVisitorCategoryCounters($visitorId, $categoryId, $categoryViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_VISITOR_CATEGORY);
        $addStmt->bindValue(':visitor_id', $visitorId, PDO::PARAM_STR);
        $addStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $addStmt->bindValue(':visitor_category_view_seconds_duration', $categoryViewSeconds, PDO::PARAM_STR);
        $addStmt->execute();
    }


    public function handleGlobalUserCategoryCounters($userId, $categoryId, $categoryViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_USER_CATEGORY);
        $addStmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $addStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $addStmt->bindValue(':user_category_view_seconds_duration', $categoryViewSeconds, PDO::PARAM_STR);
        $result = $addStmt->execute();
    }

    /**
     * @param $customerId
     * @param $categoryId
     */
    public function handleGlobalCustomerCategoryCounters($customerId, $categoryId, $categoryViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_CUSTOMER_CATEGORY);
        $addStmt->bindValue(':customer_id', $customerId, PDO::PARAM_STR);
        $addStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $addStmt->bindValue(':customer_category_view_seconds_duration', $categoryViewSeconds, PDO::PARAM_STR);
        $result = $addStmt->execute();
    }
    /**
    /**
     * @param $customerId
     * @param $itemId
     */
    public function handleGlobalVisitorItemCounters($visitorId, $itemId, $itemViewSeconds, $itemCardViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_VISITOR_ITEM);
        $addStmt->bindValue(':visitor_id', $visitorId, PDO::PARAM_STR);
        $addStmt->bindValue(':item_id', $itemId, PDO::PARAM_STR);
        $addStmt->bindValue(':view_seconds_duration', $itemViewSeconds, PDO::PARAM_STR);
        $addStmt->bindValue(':item_details_view_seconds_duration', $itemCardViewSeconds, PDO::PARAM_STR);
        $itemViewCounter = 1;
        $itemCardViewCounter = 0;
        if($itemCardViewSeconds > 0)
        {
            $itemCardViewCounter = 1;
            $itemViewCounter = 0;
        }
        $addStmt->bindValue(':item_view_counter', $itemViewCounter, PDO::PARAM_STR);
        $addStmt->bindValue(':item_details_view_counter', $itemCardViewCounter, PDO::PARAM_STR);
        $addStmt->execute();

    }

    public function handleGlobalUserItemCounters($userId, $itemId, $itemViewSeconds, $itemCardViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_USER_ITEM);
        $addStmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $addStmt->bindValue(':item_id', $itemId, PDO::PARAM_STR);
        $addStmt->bindValue(':user_item_view_seconds_duration', $itemViewSeconds, PDO::PARAM_STR);
        $addStmt->bindValue(':user_item_details_view_seconds_duration', $itemCardViewSeconds, PDO::PARAM_STR);
        $itemViewCounter = 1;
        $itemCardViewCounter = 0;
        if($itemCardViewSeconds > 0)
        {
            $itemCardViewCounter = 1;
            $itemViewCounter = 0;
        }
        $addStmt->bindValue(':user_item_view_counter', $itemViewCounter, PDO::PARAM_STR);
        $addStmt->bindValue(':user_item_details_view_counter', $itemCardViewCounter, PDO::PARAM_STR);
        $result = $addStmt->execute();
    }
    public function handleGlobalCustomerItemCounters($customerId, $itemId, $itemViewSeconds, $itemCardViewSeconds)
    {
        $addStmt = $this->trackingDbConnection->prepare(self::STATEMENT_INSERT_OR_UPDATE_CUSTOMER_ITEM);
        $addStmt->bindValue(':customer_id', $customerId, PDO::PARAM_STR);
        $addStmt->bindValue(':item_id', $itemId, PDO::PARAM_STR);
        $addStmt->bindValue(':customer_item_view_seconds_duration', $itemViewSeconds, PDO::PARAM_STR);
        $addStmt->bindValue(':customer_item_details_view_seconds_duration', $itemCardViewSeconds, PDO::PARAM_STR);
        $itemViewCounter = 1;
        $itemCardViewCounter = 0;
        if($itemCardViewSeconds > 0)
        {
            $itemCardViewCounter = 1;
            $itemViewCounter = 0;
        }
        $addStmt->bindValue(':customer_item_view_counter', $itemViewCounter, PDO::PARAM_STR);
        $addStmt->bindValue(':customer_item_details_view_counter', $itemCardViewCounter, PDO::PARAM_STR);
        $result = $addStmt->execute();
    }

    /**
     * @param $id
     */
    protected function getItemTrackingDataByID($id)
    {
        static $memo;
        if (null === $memo) {
            $memo = [];
        }

        if (array_key_exists($id,$memo)) {
            return $memo[$id];
        }

        //Look in tracking_item table first, fill if not present
        //Get description, summary, variant_type, descriptions, reviews, attributes

        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_BASIC_ITEM_FIELDS_BY_ID);
        $stmt->bindValue(':item_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $itemData = $stmt->fetch(PDO::FETCH_ASSOC);


        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_ITEM_ATTRIBUTES_BY_ID);
        $stmt->bindValue(':item_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $itemAttributesData = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_ITEM_TEXT_DESCRIPTIONS_BY_ID);
        $stmt->bindValue(':item_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $itemDescriptionData = $stmt->fetch(PDO::FETCH_ASSOC);


        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_ITEM_REVIEWS_BY_ID);
        $stmt->bindValue(':item_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $itemReviewsData = $stmt->fetch(PDO::FETCH_ASSOC);

        $itemAttributesDataJSon = json_encode($itemAttributesData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $itemDescriptionDataJSon = json_encode($itemDescriptionData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $itemReviewDataJSon = json_encode($itemReviewsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

        $itemData['item_attributes'] = $itemAttributesDataJSon;
        $itemData['item_descriptions'] = $itemDescriptionDataJSon;
        $itemData['item_reviews'] = $itemReviewDataJSon;

        $memo[$id] = $itemData;

        return $itemData;

    }


    /**
     * @param $id
     */
    protected function getCategoryTrackingDataByID($id)
    {
        static $memo;
        if (null === $memo) {
            $memo = [];
        }

        if (array_key_exists($id,$memo)) {
            return $memo[$id];
        }

        //Look in tracking_category table first, fill if not present
        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_BASIC_CATEGORY_FIELDS_BY_ID);
        $stmt->bindValue(':category_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $categoryData = $stmt->fetch(PDO::FETCH_ASSOC);

        $memo[$id] = $categoryData;

        return $categoryData;

    }

    /**
     * @param $id
     */
    protected function getUserTrackingDataByID($id)
    {
        static $memo;
        if (null === $memo) {
            $memo = [];
        }

        if (array_key_exists($id,$memo)) {
            return $memo[$id];
        }

        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_BASIC_USER_FIELDS_BY_ID);
        $stmt->bindValue(':user_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData;

    }

    /**
     * @param $id
     */
    protected function getCustomerTrackingDataByID($id)
    {
        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_BASIC_CUSTOMER_FIELDS_BY_ID);
        $stmt->bindValue(':customer_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $customerData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $customerData;
    }

    /**
     * @param $id
     */
    protected function getVisitorTrackingDataByID($id)
    {
        $stmt = $this->shopDbConnection->prepare(self::QUERY_GET_BASIC_VISITOR_FIELDS_BY_ID);
        $stmt->bindValue(':visitor_id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $customerData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $customerData;
    }

}

