<?php

namespace DynCom\dc\dcShop\USSalesTax\TaxJar;

use DynCom\dc\dcShop\USSalesTax\Address;
use GuzzleHttp\Client;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 06.06.2017
 * Time: 15:15
 */
class TaxJarRESTAPIConsumer
{

    public const API_ROOT = 'https://api.taxjar.com';
    public const API_VERSION = 'v2';
    public const API_ENDPOINT_CATEGORIES = 'categories';
    public const API_ENDPOINT_RATES = 'rates';
    public const API_ENDPOINT_ESTIMATION = 'taxes';
    public const API_ENDPOINT_ORDER_TRANSACTION = 'transactions/orders';
    public const API_ENDPOINT_REFUND_TRANSACTION = 'transactions/refunds';

    public const TRANSACTION_TYPE_ORDER = 'order';
    public const TRANSACTION_TYPE_REFUND = 'refund';

    protected const VALID_TRANSACTION_TYPES = [
        self::TRANSACTION_TYPE_ORDER,
        self::TRANSACTION_TYPE_REFUND,
    ];

    protected const TRANSACTION_TYPE_ENDPOINT_MAP = [
        self::TRANSACTION_TYPE_ORDER => self::API_ENDPOINT_ORDER_TRANSACTION,
        self::TRANSACTION_TYPE_REFUND => self::API_ENDPOINT_REFUND_TRANSACTION,
    ];

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey;

    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize(): void
    {
        $this->apiKey = getenv('TAXJAR_API_AUTH');
        $this->httpClient = new Client(
            ['headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]]
        );
    }

    protected static function isValidTransactionType(string $transactionType): bool
    {
        return in_array($transactionType, self::VALID_TRANSACTION_TYPES, 1);
    }

    protected static function getTransactionEndpointFromType(string $transationType): string
    {
        return self::TRANSACTION_TYPE_ENDPOINT_MAP[$transationType] ?? '';
    }

    public function estimateTaxes(
        ?Address $fromAddress,
        ?array $nexusAddresses,
        Address $toAddress,
        float $amount,
        float $shipping,
        array $lineItems
    ): TaxInformation {
        if (null === $fromAddress && (!is_array($nexusAddresses) || !($nexusAddresses[0] instanceof Address))) {
            throw new \InvalidArgumentException('Either FromAddress or NexusAddresses have to contain an address');
        }
        $fromAddressArray = $fromAddress->toArray(
            ['countryCode' => 'from_country', 'zip' => 'from_zip', 'state' => 'from_state', 'city' => 'from_city', 'streetAndNo' => 'from_street']
        );
        $toAddressArray = $toAddress->toArray(
            ['countryCode' => 'to_country', 'zip' => 'to_zip', 'state' => 'to_state', 'city' => 'to_city', 'streetAndNo' => 'to_street']
        );
        $lineItemArray = [];
        foreach ($lineItems as $lineItem) {

            if ($lineItem instanceof OrderLineItem) {
                $lineItemArray[] = $lineItem->toArray(['salesTax' => '']);
            }
        }
        $requestData = [];
        foreach ($fromAddressArray as $fromAddrKey => $fromAddrVal) {
            $requestData[$fromAddrKey] = $fromAddrVal;
        }
        foreach ($toAddressArray as $toAddrKey => $toAddrVal) {
            $requestData[$toAddrKey] = $toAddrVal;
        }
        $requestData['amount'] = $amount;
        $requestData['shipping'] = $shipping;
        $requestData['line_items'] = $lineItemArray;

        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::API_ENDPOINT_ESTIMATION;

        $response = $this->httpClient->post($endpoint, ['json' => $requestData]);
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();

        if (200 <= $responseStatus && 300 > $responseStatus) {
            $taxInfo = TaxInformation::fromJSON($responseContent);
            return $taxInfo;
        }

        throw new \ErrorException($responseContent, $responseStatus);
    }

    public function createTransaction(Transaction $transaction, string $transactionType): Transaction
    {
        if (!self::isValidTransactionType($transactionType)) {
            throw new \InvalidArgumentException(
                'Invalid transaction type. Valid types are [' . print_r(self::VALID_TRANSACTION_TYPES, 1) . ']'
            );
        }
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::getTransactionEndpointFromType($transactionType);
        $data = $transaction->toArray();
        //echo ('Sending transaction with data [' . \GuzzleHttp\json_encode($data) . '].');

        $response = $this->httpClient->post(
            $endpoint,
            ['json' => $data]
        );
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $returnedTransaction = Transaction::fromJSON($responseContent);
            return $returnedTransaction;
        }
        throw new \ErrorException($responseContent, $responseStatus);
    }

    public function getTransactions(
        string $transactionType,
        ?\DateTimeImmutable $transactionDate = null,
        ?\DateTimeImmutable $fromTransactionDate = null,
        ?\DateTimeImmutable $toTransactionDate = null
    ): array {
        if (!self::isValidTransactionType($transactionType)) {
            throw new \InvalidArgumentException(
                'Invalid transaction type. Valid types are [' . print_r(self::VALID_TRANSACTION_TYPES, 1) . ']'
            );
        }
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::getTransactionEndpointFromType($transactionType);

        if (null !== $fromTransactionDate && null !== $toTransactionDate) {
            $fromTransactionDateStr = $fromTransactionDate->format('Y-m-d\TH:i:s\Z');
            $toTransactionDateStr = $toTransactionDate->format('Y-m-d\TH:i:s\Z');
        } elseif (null !== $transactionDate) {
            $transactionDateStr = $transactionDate->format('Y-m-d\TH:i:s\Z');
        }
        $queryArr = [];
        if ($fromTransactionDateStr && $toTransactionDateStr) {
            $queryArr = ['from_transaction_date' => $fromTransactionDateStr, 'to_transaction_date' => $toTransactionDateStr];
        } elseif ($transactionDateStr) {
            $queryArr = ['transaction_date' => $transactionDateStr];
        }
        $response = $this->httpClient->get($endpoint, ['query' => $queryArr]);
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $transIDArr = \GuzzleHttp\json_decode($responseContent, true);
            if (array_key_exists('orders', $transIDArr)) {
                $transIDArr = $transIDArr['orders'];
            } elseif (array_key_exists('refunds', $transIDArr)) {
                $transIDArr = $transIDArr['refunds'];
            }
            return $transIDArr;
        }
        throw new \ErrorException($responseContent, $responseStatus);
    }

    public function getTransaction(string $transactionType, string $transactionID): Transaction
    {
        if (!self::isValidTransactionType($transactionType)) {
            throw new \InvalidArgumentException(
                'Invalid transaction type. Valid types are [' . print_r(self::VALID_TRANSACTION_TYPES, 1) . ']'
            );
        }
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::getTransactionEndpointFromType($transactionType);
        $endpoint .= '/' . $transactionID;

        $response = $this->httpClient->get($endpoint);
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $transaction = Transaction::fromJSON($responseContent);
            return $transaction;
        }
        throw new \ErrorException($responseContent, $responseStatus);
    }

    public function updateTransaction(string $transactionType, Transaction $transaction): Transaction
    {
        if (!self::isValidTransactionType($transactionType)) {
            throw new \InvalidArgumentException(
                'Invalid transaction type. Valid types are [' . print_r(self::VALID_TRANSACTION_TYPES, 1) . ']'
            );
        }
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::getTransactionEndpointFromType($transactionType);
        $endpoint .= '/' . $transactionID;

        $response = $this->httpClient->put(
            $endpoint,
            ['json' => $transaction->toArray(
                ['transactionReferenceID' => '', 'userID' => '']
            )]
        );
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $transaction = Transaction::fromJSON($responseContent);
            return $transaction;
        }
        throw new \ErrorException($responseContent, $responseStatus);
    }

    public function deleteTransaction(string $transactionType, string $transactionID): bool
    {
        if (!self::isValidTransactionType($transactionType)) {
            throw new \InvalidArgumentException(
                'Invalid transaction type. Valid types are [' . print_r(self::VALID_TRANSACTION_TYPES, 1) . ']'
            );
        }
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::getTransactionEndpointFromType($transactionType);
        $endpoint .= '/' . $transactionID;

        $response = $this->httpClient->delete($endpoint);
        $responseStatus = $response->getStatusCode();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            return true;
        }
        if (400 === $responseStatus) {
            return false;
        }
        throw new \ErrorException($response->getBody()->getContents(), $responseStatus);
    }

    public function getTaxRates(string $zip, string $country, string $city, string $street = ''): array
    {
        $rates = [];
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::API_ENDPOINT_RATES;

        $queryArr = [
            'zip' => $zip,
            ];
        if ($country) {
            $query['country'] = $country;
        }
        if ($city) {
            $queryArr['city'] = $city;
        }
        if ($street) {
            $queryArr['street'] = $street;
        }

        $response = $this->httpClient->get($endpoint,['query' => $queryArr]);
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $resultArr = \GuzzleHttp\json_decode($responseContent,true);
            foreach ($resultArr as $taxRateArr) {
                $rate = TaxRate::fromArray($taxRateArr);
                $rates[] = $rate;
            }
            return $rates;
        }
        throw new \ErrorException($responseContent,$responseContent);
    }

    public function getTaxCategories(): array
    {
        $categories = [];
        $endpoint = self::API_ROOT . '/' . self::API_VERSION . '/' . self::API_ENDPOINT_CATEGORIES;

        $response = $this->httpClient->get($endpoint);
        $responseStatus = $response->getStatusCode();
        $responseContent = $response->getBody()->getContents();
        if (200 <= $responseStatus && 300 > $responseStatus) {
            $resultArr = \GuzzleHttp\json_decode($responseContent,true);
            foreach ($resultArr as $taxCategoryArr) {
                $category = TaxCategory::fromArray($taxCategoryArr);
                $categories[] = $category;
            }
            return $categories;
        }
        throw new \ErrorException($responseContent,$responseStatus);
    }


}