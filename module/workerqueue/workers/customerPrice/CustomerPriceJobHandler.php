<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 17.11.2016
 * Time: 16:19
 */

namespace DynCom\dc\workerqueue\workers\customerPrice;


use DynCom\dc\dcShop\classes\DefaultItemPricingService;
use DynCom\dc\dcShop\classes\ItemPriceData;
use DynCom\dc\workerqueue\main\exceptions\InvalidJobPayloadErrorException;
use DynCom\dc\workerqueue\main\exceptions\JobProcessingInvalidBackingServiceResponseErrorException;
use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use Exception;
use PDO;
use PDOException;
use Psr\Log\NullLogger;

/**
 * Class CustomerTopItemsJobHandler
 * @package DynCom\dc\workerqueue\customerTopItems
 */
class CustomerPriceJobHandler implements JobHandler
{


    use jobHandlerTrait;

    protected const KEY_CUSTOMER_NO = 'customer_no';
    protected const KEY_COMPANY = 'company';
    protected const KEY_CODE = 'code';
    protected const KEY_SHOP_CODE = 'shop_code';
    protected const KEY_LANGUAGE_CODE = 'language_code';
    protected const KEY_CUSTOMER_PRICE = 'customer_price';
    protected const KEY_ITEM_NO = 'item_no';
    protected const KEY_VARIANT_CODE = 'variant_code';
    protected const KEY_TIMESTAMP_LAST_CALCULATED = 'last_calc_timestamp';
    protected const PAYLOAD_KEY_CUSTOMERS = 'customers';
    protected const PAYLOAD_KEY_SHOPS = 'shops';

    protected const QUERY_GET_SHOPS =
        '
        SELECT
            company, 
            code
        FROM
          shop_shop		
		WHERE
		  shop_typ != \'2\' #Item-Catalogue without pricing function
        ';

    protected const QUERY_GET_CUSTOMER_ITEM_CROSS =
        '
         SELECT
            shop_item.item_no,
            shop_item.language_code,
            CASE 
              WHEN shop_item_variant.code IS NULL 
              THEN \'\' ELSE shop_item_variant.code
            END AS variant_code,
            CASE
              WHEN shop_customer.customer_no IS NULL
              THEN \'\' ELSE shop_customer.customer_no
            END AS customer_no
        FROM
          shop_item
        LEFT JOIN shop_item_variant ON (
              shop_item_variant.company = shop_item.company
          AND shop_item_variant.item_no = shop_item.item_no
        )
        INNER JOIN (SELECT company,shop_code,customer_no FROM shop_customer WHERE         
              shop_customer.company = :company
          AND shop_customer.shop_code = :shop_code
          UNION SELECT :company AS company,:shop_code AS shop_code,\'\' AS customer_no FROM DUAL) AS shop_customer #Add empty customer for anonymous b2c visitors
        WHERE
              shop_item.company = :company
          AND shop_item.shop_code = :shop_code
        GROUP BY item_no,variant_code,customer_no  
        ORDER BY item_no ASC, variant_code ASC, customer_no ASC
        ';

    protected const QUERY_SET_CUSTOMER_ITEM_PRICE =
        '
        INSERT INTO
          shop_item_customer_price
        SET
          company = :company,
          item_no = :item_no,
          variant_code = :variant_code,
          customer_no = :customer_no,
          shop_code = :shop_code,
          customer_price = :customer_price,
          timestamp_last_calculated = :last_calc_timestamp                                   
        ';

    protected const QUERY_TRUNCATE_CUSTOMER_PRICES =
        '
        TRUNCATE shop_item_customer_price;
        ';

    /**
     * @var PDO
     */
    protected $db;
    protected $pricingService;
    protected $connectionData = [];

    /**
     * CustomerTopItemsJobHandler constructor.
     * @param $pdoDSN
     * @param $pdoUser
     * @param $pdoPass
     * @param array $pdoOptions
     */
    public function __construct(
        DefaultItemPricingService $pricingService,
        string $pdoDSN,
        string $pdoUser,
        string $pdoPass,
        array $pdoOptions = []
    ) {
        $this->connectionData['dsn'] = $pdoDSN;
        $this->connectionData['user'] = $pdoUser;
        $this->connectionData['pass'] = $pdoPass;
        $this->setConnection($this->connectionData);
        $this->unsetConnection();
        $this->pricingService = $pricingService;
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }
    }

    /**
     * @param array $connectionData
     */
    public function setConnection(array $connectionData): void
    {
        $this->db = null;
        try {
            $this->db = new Pdo($connectionData['dsn'], $connectionData['user'], $connectionData['pass']);
        } catch (PDOException $e) {
            $logger = $this->getLogger();
            $logger->alert(
                'There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].'
            );
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
        return "customerprice";
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
            $shops = $this->getShops($job);
            $truncateStmt = $this->db->query(self::QUERY_TRUNCATE_CUSTOMER_PRICES);
            $truncateStmt->execute();
            foreach ($shops as $shop) {
                $company = $shop[self::KEY_COMPANY] ?? '';
                $shopCode = $shop[self::KEY_CODE] ?? '';
                if ($company && $shopCode) {
                    $startTime = microtime(true);
                    $this->logger->info(
                        'Calculating customer prices for shop with company [' . $company . '] and code [' . $shopCode . ']'
                    );
                    $i = 0;
                    try {
                        foreach ($this->generateItemCustomerCrossProduct($company,$shopCode) as $cartProd) {
                            $customerNo = $cartProd[self::KEY_CUSTOMER_NO] ?? '';
                            $itemNo = $cartProd[self::KEY_ITEM_NO] ?? '';
                            $variantCode = $cartProd[self::KEY_VARIANT_CODE] ?? '';
                            $shopLanguageCode = $cartProd[self::KEY_LANGUAGE_CODE] ?? '';
                            if ($itemNo && $shopLanguageCode) {
                                $itemCustPrice = $this->getItemPrice(
                                    $company,
                                    $shopCode,
                                    $shopLanguageCode,
                                    $itemNo,
                                    $variantCode,
                                    $shopCode,
                                    $shopCode,
                                    $customerNo
                                );

                                $unitPrice = (float)$itemCustPrice->getUnitPrice();
                                $this->updateCustomerItemPrice(
                                    $company,
                                    $shopCode,
                                    $customerNo,
                                    $itemNo,
                                    $variantCode,
                                    $unitPrice
                                );
                                $i++;
                            }
                        }
                    } catch (\Throwable $t) {
                        $this->logger->error($t->getMessage());
                        throw new \ErrorException($t->getMessage(), $t->getCode());
                    }
                    $duration = microtime(true) - $startTime;
                    $this->logger->info(
                        'Successfully calculated and stored customer prices for [' . $i . '] cross-products in shop over [' . $duration . '] seconds.'
                    );
                }
            }
        } catch (Exception $e) {
            $job->setStatusFailed($e->getCode(), (array)$e);
            return;
        }

        $this->unsetConnection();
        $job->setStatusFinished();
        return;
    }

    protected function updateCustomerItemPrice(
        string $company,
        string $shopCode,
        string $customerNo,
        string $itemNo,
        string $variantCode,
        float $customerPrice
    ): void {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        $stmt = $this->db->prepare(self::QUERY_SET_CUSTOMER_ITEM_PRICE);
        $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_SHOP_CODE, $shopCode, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_ITEM_NO, $itemNo, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_VARIANT_CODE, $variantCode, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_NO, $customerNo, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_CUSTOMER_PRICE, (string)$customerPrice, PDO::PARAM_STR);
        $stmt->bindValue(':' . self::KEY_TIMESTAMP_LAST_CALCULATED, microtime(true), PDO::PARAM_STR);
        $success = $stmt->execute();
    }

    protected function getShops(Job $job): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        try {
            $stmt = $this->db->query(self::QUERY_GET_SHOPS);
            $stmt->execute();
            $shops = $stmt->fetchAll();
        } catch (\Throwable $t) {
            throw new JobProcessingInvalidBackingServiceResponseErrorException(
                $job->getID(),
                $this->getJobQueueName(),
                'DB',
                $t->getMessage(),
                $this->connectionData
            );
        }
        return $shops;
    }

    protected function getCustomersForShop(string $company, string $shopCode): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        try {
            $stmt = $this->db->prepare(self::QUERY_GET_CUSTOMER_NOS_BY_SHOP);
            $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
            $stmt->bindValue(':' . self::KEY_SHOP_CODE, $shopCode, PDO::PARAM_STR);
            $stmt->execute();
            $customerNos = $stmt->fetchAll();
        } catch (\Throwable $t) {
            throw new JobProcessingInvalidBackingServiceResponseErrorException(
                $job->getID(),
                $this->getJobQueueName(),
                'DB',
                $t->getMessage(),
                $this->connectionData
            );
        }
        return $customerNos;
    }

    protected function getItemIdentifiersForShop(string $company, string $shopCode): array
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        try {
            $stmt = $this->db->prepare(self::QUERY_GET_ITEMS_AND_VARIANTS_BY_SHOP);
            $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
            $stmt->bindValue(':' . self::KEY_SHOP_CODE, $shopCode, PDO::PARAM_STR);
            $stmt->execute();
            $itemIdentifierRows = $stmt->fetchAll();
        } catch (\Throwable $t) {
            throw new JobProcessingInvalidBackingServiceResponseErrorException(
                $job->getID(),
                $this->getJobQueueName(),
                'DB',
                $t->getMessage(),
                $this->connectionData
            );
        }
        return $itemIdentifierRows;
    }

    protected function getItemPrice(
        string $company,
        string $itemShopCode,
        string $languageCode,
        string $itemNo,
        string $variantCode,
        string $targetShopCode,
        string $customerShopCode,
        string $customerNo
    ): ItemPriceData {

        $customerPrice = $this->pricingService->getItemCustomerPriceByPrimary(
            $company,
            $targetShopCode,
            $itemShopCode,
            $customerShopCode,
            $languageCode,
            $customerNo,
            $itemNo,
            $variantCode,
            1
        );
        return $customerPrice;
    }

    /**
     * @param Job $job
     */
    protected function handlePayload(
        Job $job
    ): void {
        $logger = $this->getLogger();
        $this->extractedValidatedPayloadData = [];
        return;
    }

    /**
     * @param array $payload
     * @return array|mixed
     * @TODO: Implement for future use
     */
    protected function getPayloadShopCodeSet(
        array $payload
    ): array {
        if (array_key_exists(self::PAYLOAD_KEY_SHOPS, $payload) && is_array($payload[self::PAYLOAD_KEY_SHOPS]) && count(
                $payload[self::PAYLOAD_KEY_SHOPS]
            ) > 0
        ) {
            return $payload[self::PAYLOAD_KEY_SHOPS];
        }
        return [];
    }

    /**
     * @param array $payload
     * @return array|mixed
     * @TODO: Implement for future use
     */
    protected function getPayloadCustomerNoSet(
        array $payload
    ): array {
        if (array_key_exists(self::PAYLOAD_KEY_CUSTOMERS, $payload) && is_array(
                $payload[self::PAYLOAD_KEY_CUSTOMERS]
            ) && count($payload[self::PAYLOAD_KEY_CUSTOMERS]) > 0
        ) {
            return $payload[self::PAYLOAD_KEY_CUSTOMERS];
        }
        return [];
    }

    protected function cartesianProduct(array $input): array
    {
        // filter out empty values
        $input = array_filter($input);

        $result = [[]];

        foreach ($input as $key => $values) {
            $append = array();

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    protected function cartesianProductGenerator(array $input): \Generator
    {
        if ($input) {
            if ($u = array_pop($input)) {
                foreach ($this->cartesianProductGenerator($input) as $p) {
                    foreach ($u as $v) {
                        yield $p + [count($p) => $v];
                    }
                }
            }
        } else {
            yield[];
        }
    }

    protected function traversibleGenerator(\Traversable $traversable): \Generator
    {
        foreach ($traversable as $el) {
            yield $el;
        }
    }

    protected function getItemCustomerCrossProductGenerator(string $company, string $shopCode): \Generator
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        try {
            $stmt = $this->db->prepare(self::QUERY_GET_CUSTOMER_ITEM_CROSS);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
            $stmt->bindValue(':' . self::KEY_SHOP_CODE, $shopCode, PDO::PARAM_STR);
            return $this->traversibleGenerator($stmt);
        } catch (\Throwable $t) {
            throw new JobProcessingInvalidBackingServiceResponseErrorException(
                $job->getID(),
                $this->getJobQueueName(),
                'DB',
                $t->getMessage(),
                $this->connectionData
            );
        }
    }

    protected function generateItemCustomerCrossProduct(string $company, string $shopCode) : \Generator
    {
        if (!($this->db instanceof PDO)) {
            throw new \BadMethodCallException(
                'Method [' . __METHOD__ . '] cannot be called unless property db has been set to an instance of \PDO.'
            );
        }
        try {
            $stmt = $this->db->prepare(self::QUERY_GET_CUSTOMER_ITEM_CROSS);
            $stmt->bindValue(':' . self::KEY_COMPANY, $company, PDO::PARAM_STR);
            $stmt->bindValue(':' . self::KEY_SHOP_CODE, $shopCode, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            foreach ($stmt as $row) {
                yield $row;
            }
        } catch (\Throwable $t) {
            throw new JobProcessingInvalidBackingServiceResponseErrorException(
                $job->getID(),
                $this->getJobQueueName(),
                'DB',
                $t->getMessage(),
                $this->connectionData
            );
        }
    }
}