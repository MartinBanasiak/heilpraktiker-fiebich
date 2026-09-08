<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 17.11.2016
 * Time: 16:19
 */

namespace DynCom\dc\workerqueue\workers\customerTopItems;


use DynCom\dc\workerqueue\main\exceptions\InvalidJobPayloadErrorException;
use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use Exception;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Log\NullLogger;

/**
 * Class CustomerTopItemsJobHandler
 * @package DynCom\dc\workerqueue\customerTopItems
 */
class CustomerTopItemsJobHandler implements JobHandler
{


    use jobHandlerTrait;

    protected const KEY_QUANTITY = 'quantity';
    protected const KEY_CUSTOMER_NO = 'customer_no';
    protected const KEY_SHOP_CUSTOMER_ID = 'shop_customer_id';
    protected const KEY_SHOP_USER_ID = 'shop_user_id';
    protected const KEY_LIMIT = 'limit';
    protected const KEY_CHECK_USER_OR_CUSTOMER = 'check_user_or_customer';
    protected const KEY_COMPANY = 'company';
    protected const KEY_SHOP_CODE = 'shop_code';
    protected const KEY_LANGUAGE_CODE = 'language_code';
    protected const KEY_ITEM_NO = 'item_no';
    protected const KEY_RANK = 'rank';
    protected const KEY_TOP_ITEMS_JSON_DATA = 'top_items_json_data';
    protected const PAYLOAD_KEY_NO_OF_TOP_ITEMS = 'no_of_top_items';
    protected const DEFAULT_NO_OF_TOP_ITEMS = 20;
    protected const PAYLOAD_KEY_USERS = 'users';
    protected const PAYLOAD_KEY_CUSTOMERS = 'customers';


    protected const QUERY_GET_CUSTOMER_BY_ID = '
        SELECT
            id AS shop_customer_id,
            company,
            customer_no
        FROM
          shop_customer
        WHERE
          id = :shop_customer_id;
    ';

    protected const QUERY_GET_CUSTOMER_BY_USER_ID = '
        SELECT
            sc.id AS shop_customer_id,
            sc.company,
            sc.customer_no
        FROM
          shop_user su
        LEFT JOIN
          shop_customer sc
        ON (
              sc.company = su.company
          AND sc.customer_no = su.customer_no
        )
        WHERE
              sc.customer_no IS NOT NULL
          AND sc.customer_no != \'\'
          AND su.id = :shop_user_id
        LIMIT 1;
    ';

    protected const QUERY_GET_USERS_CUSTOMERS = '
        SELECT 
            sc.id AS shop_customer_id, 
            sc.customer_no AS customer_no,
            sc.company AS company,
            CASE 
                WHEN su.id IS NULL THEN 0 
                ELSE su.id 
            END AS shop_user_id
        FROM
          shop_customer AS sc
        LEFT JOIN shop_user AS su ON (
              su.customer_no != \'\'
          AND su.customer_no = sc.customer_no
        )
        ORDER BY
          sc.id ASC,
          su.id ASC;
    ';

    protected const QUERY_GET_ALL_USERS_FOR_CUSTOMER_ID = '
        SELECT
            sc.company AS company,
            su.id AS shop_user_id,
            su.customer_no AS customer_no
        FROM
          shop_customer sc 
        LEFT JOIN
          shop_user su
        ON (
              su.company = sc.company
          AND su.customer_no = sc.customer_no
        )
        WHERE 
              sc.id = :shop_customer_id
          AND su.id IS NOT NULL; 
    ';

    protected const QUERY_GET_TOP_ITEMS_WEB_SALES = '
        SELECT
          shsl.company,
          shsl.item_no,
          shsl.quantity AS rank
        FROM 
          shop_sales_header shsh
        LEFT JOIN
          shop_sales_line shsl
          ON (
            shsl.shop_sales_header_id = shsh.id
          )
        WHERE
              (:shop_user_id > 0 AND shsh.shop_user_id = :shop_user_id) 
          OR  (:shop_customer_id > 0 AND shsh.shop_customer_id = :shop_customer_id)          
        GROUP BY
          shsl.company, 
          shsl.item_no
        ORDER BY
          SUM(shsl.quantity) DESC;
    ';

    protected const QUERY_GET_TOP_ITEMS_NAV_SALES = '
        SELECT
          snsl.company,
          snsl.no AS item_no,
          snsl.quantity AS rank 
        FROM
          shop_nav_sales_header snsh
        LEFT JOIN
          shop_nav_sales_line snsl
        ON (
              snsl.document_type = snsh.type
          AND snsl.document_no = snsh.no      
          AND snsl.type=2 #Item
        )
        LEFT JOIN
          shop_sales_header shsh
        ON (
              shsh.company = snsh.company
          AND shsh.order_no = snsh.webshop_order_no
        )
        WHERE
              shsh.id IS NULL #We have already checked webshop orders, so exclude them now
          AND snsh.company = :company
          AND (
                (snsh.sell_to_customer_no = :customer_no)
            OR  (snsh.bill_to_customer_no = :customer_no)
          )
          AND snsh.type NOT IN (3,5) #No Credit Memos or Return Orders
          AND snsl.type = 2 #Item
        GROUP BY
          snsl.company,
          snsl.no
        ORDER BY
          SUM(snsl.quantity) DESC;
    ';

    protected const QUERY_GET_TOP_ITEMS_NAV_INVOICE = '
        SELECT
          ssil.company,
          ssil.no AS item_no,
          ssil.quantity AS rank
        FROM
          shop_sales_invoice_header ssih
        LEFT JOIN
          shop_sales_invoice_line ssil
        ON (
            ssil.document_no = ssih.no      
        )
        LEFT JOIN
          shop_sales_header shsh
        ON (
              shsh.order_no = ssih.webshop_order_no
          AND shsh.company = ssih.company
        )
        WHERE
              shsh.id IS NULL #We have already checked webshop orders, so exclude them now
          AND ssih.company = :company
          AND (
                (ssih.sell_to_customer_no = :shop_customer_no)
            OR  (ssih.bill_to_customer_no = :shop_customer_no)
          )      
          AND ssil.type = 2 #Item
        GROUP BY
          ssil.company,
          ssil.no
        ORDER BY
          SUM(ssil.quantity) DESC;
    ';

    protected const QUERY_GET_TOP_ITEMS_NAV_SHIPMENT = '
        SELECT
          sssl.company,
          sssl.no AS item_no,
          sssl.quantity AS rank
        FROM
          shop_sales_shipment_header sssh
        LEFT JOIN
          shop_sales_shipment_line sssl
        ON (
            sssl.document_no = sssh.no      
        )
        LEFT JOIN
          shop_sales_header shsh
        ON (
              shsh.order_no = sssh.webshop_order_no
          AND shsh.company = sssh.company
        )
        LEFT JOIN
          shop_sales_invoice_header ssih
        ON (
              ssih.order_no = sssh.order_no
          AND ssih.company = sssh.company
        )
        WHERE
              shsh.id IS NULL #We have already checked webshop orders, so exclude them now
          AND ssih.id IS NULL #We have already checked invoices, so exclude them now
          AND sssh.company = :company
          AND (
                (sssh.sell_to_customer_no = :shop_customer_no)
            OR  (sssh.bill_to_customer_no = :shop_customer_no)
          )   
          AND sssl.type = 2 #Item
        GROUP BY
          sssl.company,
          sssl.no
        ORDER BY
          SUM(sssl.quantity) DESC;
    ';

    protected const QUERY_INSERT_UPDATE_TOP_ITEMS = '
        INSERT INTO
          shop_user_customer_top_items
        SET
          shop_user_id = :shop_user_id,
          shop_customer_id = :shop_customer_id,
          top_items_json_data = :top_items_json_data,
          last_timestamp_updated = NOW()
        ON DUPLICATE KEY UPDATE
          shop_user_id = :shop_user_id,
          shop_customer_id = :shop_customer_id,
          top_items_json_data = :top_items_json_data,
          last_timestamp_updated = NOW();
    ';


    /**
     * @var PDO
     */
    protected $db;

    protected $connectionData = [];

    /**
     * CustomerTopItemsJobHandler constructor.
     * @param $pdoDSN
     * @param $pdoUser
     * @param $pdoPass
     * @param array $pdoOptions
     */
    public function __construct(string $pdoDSN, string $pdoUser, string $pdoPass, array $pdoOptions = [])
    {
        $this->connectionData['dsn'] = $pdoDSN;
        $this->connectionData['user'] = $pdoUser;
        $this->connectionData['pass'] = $pdoPass;
        $this->setConnection($this->connectionData);
        $this->unsetConnection();
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }
    }

    /**
     * @param array $connectionData
     */
    public function setConnection(array $connectionData): void
    {
        try {
            $this->db = new Pdo($connectionData['dsn'], $connectionData['user'], $connectionData['pass']);
        } catch (PDOException $e) {
            $logger = $this->getLogger();
            $logger->alert('There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].');
            throw $e;
        }
        return;
    }

    protected function unsetConnection(): void
    {
        $this->db = null;
        return;
    }

    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return "customertopitems";
    }

    /**
     * @param Job $job
     */
    public function doJob(Job $job): void
    {
        $logger = $this->getLogger();
        try {
            $this->handlePayload($job);
            $logger->info('Payload handled successfully.');
        } catch (InvalidJobPayloadErrorException $e) {
            $job->setStatusFailed($e->getCode(), json_encode($e));
            $errMsg = $e->getMessage();
            $logger->alert('InvalidJobPayloadErrorException - [' . $errMsg . ']. Job-Status has been set to failed.');
            return;
        }

        $this->setConnection($this->connectionData);
        try {
            if (array_key_exists(self::PAYLOAD_KEY_USERS, $this->extractedValidatedPayloadData) && is_array($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_USERS]) && count($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_USERS]) > 0) {
                $logger->debug('Calculating Top Items per UserID.');
                $this->getAndUpdateTopItemsPerUser($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_USERS]);
            } elseif (array_key_exists(self::PAYLOAD_KEY_CUSTOMERS, $this->extractedValidatedPayloadData) && is_array($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_CUSTOMERS]) && count($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_CUSTOMERS]) > 0) {
                $logger->debug('Calculating Top Items per CustomerID.');
                $this->getAndUpdateTopItemsPerCustomer($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_CUSTOMERS]);
            } else {
                $logger->debug('Calculating Top Items for all users/customers.');
                $this->getAndUpdateTopItemsForAllUsers($this->extractedValidatedPayloadData[self::PAYLOAD_KEY_NO_OF_TOP_ITEMS]);
            }
        } catch (Exception $e) {
            $job->setStatusFailed($e->getCode(), (array)$e);
            return;
        }

        $this->unsetConnection();
        $job->setStatusFinished();
        return;
    }

    /**
     * @param Job $job
     */
    protected function handlePayload(Job $job): void
    {
        $logger = $this->getLogger();
        $payload = $job->getPayload();
        $userIDs = $this->getPayloadUserIDSet($payload);
        $logger->debug('Top Item calculation limited to following UserIDS: [' . implode(',', $userIDs) . '].');

        $customerIDs = $this->getPayloadCustomerIDSet($payload);
        $logger->debug('Top Item calculation limited to following CustomerIDS: [' . implode(',', $customerIDs) . '].');
        $noOfItems = $this->getPayloadNoOfTopItemsPerUser($payload) > 0 ? $this->getPayloadNoOfTopItemsPerUser($payload) : self::DEFAULT_NO_OF_TOP_ITEMS;
        $this->extractedValidatedPayloadData = [self::PAYLOAD_KEY_USERS => $userIDs, self::PAYLOAD_KEY_CUSTOMERS => $customerIDs, self::PAYLOAD_KEY_NO_OF_TOP_ITEMS => $noOfItems];
        return;
    }

    /**
     * @param array $payload
     * @return array|mixed
     */
    protected function getPayloadUserIDSet(array $payload): array
    {
        if (array_key_exists(self::PAYLOAD_KEY_USERS, $payload) && is_array($payload[self::PAYLOAD_KEY_USERS]) && count($payload[self::PAYLOAD_KEY_USERS]) > 0) {
            return $payload[self::PAYLOAD_KEY_USERS];
        }
        return [];
    }

    /**
     * @param array $payload
     * @return array|mixed
     */
    protected function getPayloadCustomerIDSet(array $payload): array
    {
        if (array_key_exists(self::PAYLOAD_KEY_CUSTOMERS, $payload) && is_array($payload[self::PAYLOAD_KEY_CUSTOMERS]) && count($payload[self::PAYLOAD_KEY_CUSTOMERS]) > 0) {
            return $payload[self::PAYLOAD_KEY_CUSTOMERS];
        }
        return [];
    }

    /**
     * @param array $payload
     * @return int
     */
    protected function getPayloadNoOfTopItemsPerUser(array $payload): int
    {
        if (array_key_exists(self::PAYLOAD_KEY_NO_OF_TOP_ITEMS, $payload) && (int)$payload[self::PAYLOAD_KEY_NO_OF_TOP_ITEMS] > 0) {
            return (int)$payload[self::PAYLOAD_KEY_NO_OF_TOP_ITEMS];
        } else {
            return self::DEFAULT_NO_OF_TOP_ITEMS;
        }
    }

    /**
     * @param array $users
     * @param int $noOfItems
     */
    protected function getAndUpdateTopItemsPerUser(array $users, int $noOfItems = self::DEFAULT_NO_OF_TOP_ITEMS): void
    {
        $logger = $this->getLogger();
        $logger->debug('Getting and updating the top [' . $noOfItems . '] for Users with the following IDs: [' . implode(',', $users) . '].');

        $checkedUserIDs = [];
        foreach ($users as $userID) {
            $userID = (int)$userID;
            $customerData = $this->getCustomerForUserID((int)$userID);
            $company = $customerData[self::KEY_COMPANY] ?? '';
            $customerNo = $customerData[self::KEY_CUSTOMER_NO] ?? '';
            $customerID = $customerData[self::KEY_SHOP_CUSTOMER_ID] ?? 0;

            if (!in_array($userID, $checkedUserIDs, true)) {
                $topItems = $this->getTopItemsForUserCustomer($userID, (int)$customerID, (string)$company, (string)$customerNo, $noOfItems);

                if (count($topItems) > 0) {
                    $this->updateTopItemsForUserCustomer($userID, (int)$customerID, $topItems);
                    $logger->debug('Updated top items for user with id [' . $userID . '].');
                } else {
                    $logger->debug('No top items found for user with id [' . $userID . ']!');
                }
                $checkedUserIDs[] = $userID;
            }
        }
        return;
    }

    /**
     * @param int $userID
     * @return array
     */
    protected function getCustomerForUserID(int $userID): array
    {
        static $memo;
        if (null === $memo) {
            $memo = [];
        }

        if (array_key_exists($userID, $memo)) {
            return $memo[$userID];
        }
        $customerData = [];
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $customerResult = [];
        $stmt = $this->db->prepare(self::QUERY_GET_CUSTOMER_BY_USER_ID);
        $stmt->bindValue(':' . self::KEY_SHOP_USER_ID, $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $customerResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($customerResult) && count($customerResult) > 0) {
                if (is_array($customerResult[0])) {
                    $customerResult = $customerResult[0];
                }
            }
        }
        $customerData[self::KEY_COMPANY] = $customerResult[self::KEY_COMPANY] ?? '';
        $customerData[self::KEY_CUSTOMER_NO] = $customerResult[self::KEY_CUSTOMER_NO] ?? '';
        $customerData[self::KEY_SHOP_CUSTOMER_ID] = $customerResult[self::KEY_SHOP_CUSTOMER_ID] ?? '';
        $memo[$userID] = $customerData;
        return $customerData;
    }

    /**
     * @param int $userID
     * @param int $customerID
     * @param string $company
     * @param string $customerNo
     * @param int $noOfTopItems
     * @return array
     */
    protected function getTopItemsForUserCustomer(int $userID, int $customerID, string $company, string $customerNo, int $noOfTopItems = self::DEFAULT_NO_OF_TOP_ITEMS): array
    {
        $shopTopItems = $this->getShopTopItemsForUserCustomer($userID, $customerID);
        $shopTopItemsCount = count($shopTopItems);
        $navSalesTopItems = $this->getNAVSalesHeaderTopItemsForCustomer($company, $customerNo);
        $navSalesTopItemsCount = count($navSalesTopItems);
        $navInvoiceTopItems = $this->getNAVInvoiceTopItemsForCustomer($company, $customerNo);
        $navInvoiceTopItemsCount = count($navInvoiceTopItems);
        $navShipmentTopItems = $this->getNAVShipmentTopItemsForCustomer($company, $customerNo);
        $navShipmentTopItemsCount = count($navShipmentTopItems);
        $this->logger->info("No of top items from shop for userID [$userID] and customer_no [$customerNo] are [$shopTopItemsCount] - from nav sales: [$navSalesTopItemsCount] - from nav invoice: [$navInvoiceTopItemsCount] - from nav shipment: [$navShipmentTopItemsCount].");
        $mergedLimitedTopItems = $this->mergeTopItemResults($shopTopItems, $navSalesTopItems, $navInvoiceTopItems, $navShipmentTopItems, $noOfTopItems);
        return $mergedLimitedTopItems;
    }

    /**
     * @param int $userID
     * @param int $customerID
     * @return array
     */
    protected function getShopTopItemsForUserCustomer(int $userID, int $customerID): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TOP_ITEMS_WEB_SALES);
        $stmt->bindValue(':' . self::KEY_SHOP_USER_ID, $userID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_SHOP_CUSTOMER_ID, $customerID, PDO::PARAM_INT);
        $result = $this->getTopItemsFromPDOStmt($stmt);
        return $result;
    }

    /**
     * @param PDOStatement $stmt
     * @return array
     */
    protected function getTopItemsFromPDOStmt(PDOStatement $stmt)
    {
        $result = [];
        if ($stmt->execute()) {
            $topItemsResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($topItemsResult) && count($topItemsResult) > 0) {
                foreach ($topItemsResult as $itemRow) {
                    $itemKey = $itemRow[self::KEY_COMPANY] . '|' . $itemRow[self::KEY_ITEM_NO];
                    $result[$itemKey] = $itemRow;
                }
                return $result;
            }
            return $result;
        }
        return $result;
    }

    /**
     * @param string $company
     * @param string $customerNo
     * @return array
     */
    protected function getNAVSalesHeaderTopItemsForCustomer(string $company, string $customerNo): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TOP_ITEMS_NAV_SALES);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_NO, $customerNo, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
        $result = $this->getTopItemsFromPDOStmt($stmt);
        return $result;
    }

    /**
     * @param string $company
     * @param string $customerNo
     * @return array
     */
    protected function getNAVInvoiceTopItemsForCustomer(string $company, string $customerNo): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $stmt = $this->db->prepare(self::QUERY_GET_TOP_ITEMS_NAV_INVOICE);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_NO, $customerNo, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
        $result = $this->getTopItemsFromPDOStmt($stmt);
        return $result;
    }

    /**
     * @param string $company
     * @param string $customerNo
     * @return array
     */
    protected function getNAVShipmentTopItemsForCustomer(string $company, string $customerNo): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }

        $stmt = $this->db->prepare(self::QUERY_GET_TOP_ITEMS_NAV_INVOICE);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_NO, $customerNo, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
        $result = $this->getTopItemsFromPDOStmt($stmt);
        return $result;
    }

    /**
     * Merges results - results must have item-keys in the format "[COMPANY] | [ITEM_NO]" and be ordered top-down by rank (i.e. quantity)
     * @param array $webshopSalesRsults
     * @param array $navSalesHeaderResults
     * @param array $navInvoiceResults
     * @param array $navShipmentResults
     * @param int $noOfTopItems
     * @return array
     */
    protected function mergeTopItemResults(array $webshopSalesRsults, array $navSalesHeaderResults, array $navInvoiceResults, array $navShipmentResults, int $noOfTopItems = self::DEFAULT_NO_OF_TOP_ITEMS): array
    {
        $mergedResult = array_merge($webshopSalesRsults, $navSalesHeaderResults, $navInvoiceResults, $navShipmentResults);
        uasort($mergedResult, [$this, 'compareArraysByRankField']);
        if (count($mergedResult) > $noOfTopItems) {
            $mergedResult = array_slice($mergedResult, 0, $noOfTopItems);
        }
        return $mergedResult;
    }

    /**
     * @param int $userID
     * @param int $customerID
     * @param array $topItems
     */
    protected function updateTopItemsForUserCustomer(int $userID, int $customerID, array $topItems): void
    {
        $topItemsJson = json_encode($topItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $stmt = $this->db->prepare(self::QUERY_INSERT_UPDATE_TOP_ITEMS);
        $stmt->bindValue(':' . self::KEY_SHOP_USER_ID, $userID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_SHOP_CUSTOMER_ID, $customerID, PDO::PARAM_INT);
        $stmt->bindValue(':' . self::KEY_TOP_ITEMS_JSON_DATA, $topItemsJson, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param array $customers
     * @param int $noOfItems
     */
    protected function getAndUpdateTopItemsPerCustomer(array $customers, int $noOfItems = self::DEFAULT_NO_OF_TOP_ITEMS): void
    {
        $logger = $this->getLogger();
        $checkedCustomerIDs = [];
        $logger->debug('Getting and updating the top [' . $noOfItems . '] for Customer with the following IDs: [' . implode(',', $customers) . '].');


        foreach ($customers as $customerID) {
            $customerID = (int)$customerID;
            if (!in_array($customerID, $checkedCustomerIDs, true)) {
                $customerUsers = $this->getAllUsersForCustomerID($customerID);

                if (empty($customerUsers)) {
                    $customer = $this->getCustomerForCustomerID($customerID);
                    $customerUsers[] = [
                        self::KEY_COMPANY => $customer[self::KEY_COMPANY] ?? '',
                        self::KEY_SHOP_USER_ID => 0,
                        self::KEY_SHOP_CUSTOMER_ID => $customerID,
                        self::KEY_CUSTOMER_NO => $customer[self::KEY_CUSTOMER_NO] ?? '',
                    ];
                }


                $hasHadTopItems = false;
                foreach ($customerUsers as $customerUser) {
                    $company = $customerUser[self::KEY_COMPANY] ?? '';
                    $userID = $customerUser[self::KEY_SHOP_USER_ID] ?? 0;
                    $customerID = $customerUser[self::KEY_SHOP_CUSTOMER_ID] ?? 0;
                    $customerNo = $customerUser[self::KEY_CUSTOMER_NO] ?? '';
                    $topItems = $this->getTopItemsForUserCustomer((int)$userID, (int)$customerID, (string)$company, (string)$customerNo, $noOfItems);
                    if (count($topItems) > 0) {
                        $hasHadTopItems = true;
                        $this->updateTopItemsForUserCustomer((int)$userID, (int)$customerID, $topItems);
                    }

                }

                if ($hasHadTopItems) {
                    $logger->debug('Updated top items for customer with id [' . $customerID . '].');
                } else {
                    $logger->debug('No top items found for customer with id [' . $customerID . ']!');
                }
                $checkedCustomerIDs[] = $customerID;
            }
        }
        return;
    }

    /**
     * @param int $customerID
     * @return array
     */
    protected function getAllUsersForCustomerID(int $customerID): array
    {
        $result = [];
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }

        $stmt = $this->db->prepare(self::QUERY_GET_ALL_USERS_FOR_CUSTOMER_ID);
        $stmt->bindValue(':' . self::KEY_SHOP_CUSTOMER_ID, $customerID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $usersResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($usersResult) && count($usersResult) > 0) {
                foreach ($usersResult as $userRow) {
                    $result[$customerID] = [
                        self::KEY_COMPANY => $userRow[self::KEY_COMPANY] ?? '',
                        self::KEY_SHOP_USER_ID => $userRow[self::KEY_SHOP_USER_ID] ?? 0,
                        self::KEY_SHOP_CUSTOMER_ID => $customerID,
                        self::KEY_CUSTOMER_NO => $userRow[self::KEY_CUSTOMER_NO] ?? '',
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @param int $customerID
     * @return array
     */
    protected function getCustomerForCustomerID(int $customerID): array
    {
        $customerData = [];
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException('Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.');
        }
        $stmt = $this->db->prepare(self::QUERY_GET_CUSTOMER_BY_ID);
        $stmt->bindValue(':' . self::KEY_SHOP_CUSTOMER_ID, $customerID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $customerResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($customerResult) && count($customerResult) > 0) {
                if (is_array($customerResult[0])) {
                    $customerResult = $customerResult[0];
                }
                $customerData[self::KEY_COMPANY] = $customerResult[self::KEY_COMPANY] ?? '';
                $customerData[self::KEY_CUSTOMER_NO] = $customerResult[self::KEY_CUSTOMER_NO] ?? '';
                $customerData[self::KEY_SHOP_CUSTOMER_ID] = $customerResult[self::KEY_SHOP_CUSTOMER_ID] ?? '';
            }
        }
        return $customerData;
    }

    /**
     * @param int $noOfItems
     * @throws \ErrorException
     */
    protected function getAndUpdateTopItemsForAllUsers(int $noOfItems = self::DEFAULT_NO_OF_TOP_ITEMS): void
    {
        if (!($this->db instanceof PDO)) {
            $errMsg = 'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.';
            $this->logger->error($errMsg);
            throw new \BadMethodCallException($errMsg);
        }

        $usersStmt = $this->db->query(self::QUERY_GET_USERS_CUSTOMERS);
        if ($usersStmt === FALSE) {
            $errMsg = 'Could not successfully execute query [' . self::QUERY_GET_USERS_CUSTOMERS . ']. Error: [' . print_r($this->db->errorInfo(), true) . '].';
            $this->logger->error($errMsg);
            throw new \ErrorException($errMsg);
        }
        $allUsersCustomersResult = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
        $allUsersCustomersCount = count($allUsersCustomersResult);
        $this->logger->info('Calculating TopItems for [' . $allUsersCustomersCount . '] users/customers.');
        $users = [];
        $customersWithoutUsers = [];
        foreach ($allUsersCustomersResult as $userCustomerResult) {
            $userID = $userCustomerResult[self::KEY_SHOP_USER_ID] ?? 0;
            $customerID = $userCustomerResult[self::KEY_SHOP_CUSTOMER_ID] ?? 0;
            $customerNo = $userCustomerResult[self::KEY_CUSTOMER_NO] ?? '';
            $company = $userCustomerResult[self::KEY_COMPANY] ?? '';

            if ((int)$userID > 0) {
                $users[] = [self::KEY_COMPANY => $company, self::KEY_CUSTOMER_NO => $customerNo, self::KEY_SHOP_CUSTOMER_ID => $customerID, self::KEY_SHOP_USER_ID => $userID];
            } else {
                $customersWithoutUsers[] = [self::KEY_COMPANY => $company, self::KEY_CUSTOMER_NO => $customerNo, self::KEY_SHOP_CUSTOMER_ID => $customerID];
            }
        }

        foreach ($users as $user) {
            $this->getAndUpdateTopItemsFromCustomerUserArray($user, $noOfItems);
        }

        foreach ($customersWithoutUsers as $customerWithoutUser) {
            $this->getAndUpdateTopItemsFromCustomerUserArray($customerWithoutUser, $noOfItems);
        }
        return;
    }

    /**
     * @param array $customerUser
     * @param int $noOfTopItems
     */
    protected function getAndUpdateTopItemsFromCustomerUserArray(array $customerUser, int $noOfTopItems = self::DEFAULT_NO_OF_TOP_ITEMS): void
    {
        $company = $customerUser[self::KEY_COMPANY] ?? '';
        $company = (string)$company;
        $userID = $customerUser[self::KEY_SHOP_USER_ID] ?? 0;
        $userID = (int)$userID;
        $customerID = $customerUser[self::KEY_SHOP_CUSTOMER_ID] ?? 0;
        $customerID = (int)$customerID;
        $customerNo = $customerUser[self::KEY_CUSTOMER_NO] ?? '';
        $customerNo = (string)$customerNo;
        $topItems = $this->getTopItemsForUserCustomer($userID, $customerID, $company, $customerNo, $noOfTopItems);
        //$this->logger->info('Top items for userID [' . $userID . '] and customer_no [' . $customerNo . '] are [' . serialize($topItems) . ']');
        $this->updateTopItemsForUserCustomer($userID, $customerID, $topItems);
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function compareArraysByRankField(array $a, array $b): int
    {
        $aRank = $a[self::KEY_RANK] ?? 0.00;
        $bRank = $b[self::KEY_RANK] ?? 0.00;
        if ($aRank === $bRank) {
            return 0;
        }
        return ($aRank > $bRank) ? -1 : 1; //Higher rank = higher quantity - should be sorted at the top/beginning
    }


}