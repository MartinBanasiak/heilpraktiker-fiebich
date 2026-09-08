<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.11.2016
 * Time: 14:09
 */

namespace DynCom\dc\workerqueue\workers\currencyConversion;


use DynCom\dc\workerqueue\main\Job;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\jobHandlerTrait;
use Exception;
use GuzzleHttp\Client;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class FixerIOUpdateCurrencieRatesJobHandler
 * @package DynCom\dc\workerqueue\workers\currencyConversion
 */
class FixerIOUpdateCurrencieRatesJobHandler implements JobHandler
{
    use jobHandlerTrait;

    protected const QUERY_UPDATE_CONVERSION = '
        INSERT INTO `currency_rates`
        SET 
          `base_currency` = :base_currency,
          `conversion_currency` = :conversion_currency,
          `conversion_rate` = :conversion_rate,
          `last_update_timestamp` = NOW()
        ON DUPLICATE KEY UPDATE
          `base_currency` = :base_currency,
          `conversion_currency` = :conversion_currency,
          `conversion_rate` = :conversion_rate,
          `last_update_timestamp` = NOW();
    ';

    const QUEUE_NAME = 'currencyconversion';
    const CONVERSION_API_ADDR = 'http://api.fixer.io/latest';

    protected $connectionParams;
    /**
     * @var Client
     */
    protected $guzzleClient;

    protected $connectionData;

    /**
     * @var PDO
     */
    protected $db;

    /**
     * FixerIOUpdateCurrencieRatesJobHandler constructor.
     * @param $pdoDSN
     * @param $pdoUser
     * @param $pdoPass
     * @param array|null $pdoOptions
     * @param LoggerInterface|null $logger
     */
    public function __construct(string $pdoDSN, string $pdoUser, string $pdoPass, array $pdoOptions = null, LoggerInterface $logger = null)
    {
        $this->guzzleClient = new Client();
        $this->connectionParams = ['dsn' => $pdoDSN,
            'user' => $pdoUser,
            'options' => $pdoOptions,
        ];

        if (null === $logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
        try {
            $this->db = new PDO($pdoDSN, $pdoUser, $pdoPass, $pdoOptions);
        } catch (PDOException $e) {
            $this->logger->alert('There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].');
            throw $e;
        }
        $connectionData = [
            'dsn' => $pdoDSN,
            'user' => $pdoUser,
            'pass' => $pdoPass,
            'options' => $pdoOptions
        ];
        $this->connectionData = $connectionData;
        $this->db = null;
    }


    /**
     * @return string
     */
    public function getJobQueueName(): string
    {
        return self::QUEUE_NAME;
    }

    /**
     * @param Job $job
     */
    public function doJob(Job $job): void
    {
        $this->setConnection($this->connectionData);
        try {
            $data = $this->getExchangeRateData();
            $this->updateExchangeRatesInDBFromData($data);
            $job->setStatusFinished();
        } catch (\Throwable $e) {
            $job->setStatusFailed($e->getCode(), [$e->getMessage()]);
        }
        $this->unsetConnection();
        return;
    }

    /**
     * @param array $connectionData
     */
    protected function setConnection(array $connectionData): void
    {
        $this->db = null;
        try {
            $this->db = new PDO($connectionData['dsn'], $connectionData['user'], $connectionData['pass'], $connectionData['options']);
        } catch (PDOException $e) {
            $this->logger->alert('There was a PDO error establishing the connection. Message was [' . $e->getMessage() . '].');
            throw $e;
        }
        return;
    }

    /**
     * @return array|mixed
     */
    protected function getExchangeRateData(): array
    {
        try {
            $responseStruct = $this->guzzleClient->get(self::CONVERSION_API_ADDR);
            $response = $responseStruct->getBody();
            try {
                $array = \GuzzleHttp\json_decode($response, true);
                if (is_array($array) && array_key_exists('base', $array)) {
                    return $array;
                } else {
                    $errorMsg = 'Could not parse response from fixer.io as json for currency conversion. Response was [' . $response . '].';
                    $this->logger->critical($errorMsg);
                    return [];
                }
            } catch (Exception $e) {
                $errorMsg = 'There was an exception getting and parsing a response from fixer.io for currency conversion. Error message was [' . $e->getMessage() . '].';
                $this->logger->error($errorMsg);
                return [];
            }
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            $this->logger->critical('An error occurred trying to get latest exchange rates from address [' . self::CONVERSION_API_ADDR . ']. The error message was [' . $errorMsg . '].');
            return [];
        }
    }

    /**
     * @param array $data
     */
    protected function updateExchangeRatesInDBFromData(array $data): void
    {
        if (array_key_exists('base', $data) && array_key_exists('date', $data) && array_key_exists('rates', $data) && is_array($data['rates'])) {
            $baseCurrency = $data['base'];
            $date = $data['date'];
            foreach ($data['rates'] as $convCurrency => $convVal) {
                $invBaseCurr = $convCurrency;
                $invConvCurr = $baseCurrency;
                $invVal = 1 / $convVal;

                //Update normal
                $stmt = $this->db->prepare(self::QUERY_UPDATE_CONVERSION);
                $stmt->bindValue(':base_currency', $baseCurrency, PDO::PARAM_STR);
                $stmt->bindValue(':conversion_currency', $convCurrency, PDO::PARAM_STR);
                $stmt->bindValue(':conversion_rate', number_format($convVal, 10, '.', ''), PDO::PARAM_STR);
                $stmt->execute();

                //Update inverted
                $stmt = $this->db->prepare(self::QUERY_UPDATE_CONVERSION);
                $stmt->bindValue(':base_currency', $invBaseCurr, PDO::PARAM_STR);
                $stmt->bindValue(':conversion_currency', $invConvCurr, PDO::PARAM_STR);
                $stmt->bindValue(':conversion_rate', number_format($invVal, 10, '.', ''), PDO::PARAM_STR);
                $stmt->execute();

                $this->logger->info('Updated conversion rate between [' . $baseCurrency . '] and [' . $convCurrency . '] to a rate of [' . $convVal . '] with data from [' . $date . '].');
            }
        } else {
            $this->logger->error('No valid data to update currency exchange rates.');
        }
        return;
    }

    protected function unsetConnection(): void
    {
        $this->db = null;
        return;
    }

    /**
     * @param Job $job
     */
    protected function handlePayload(Job $job): void
    {
        if ($payload = $job->getPayload()) {
            //@TODO: Implement Base currency and conversion currency settings from payload
        }
        return;
    }


}