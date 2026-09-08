<?php
declare(strict_types = 1);
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.11.2016
 * Time: 14:50
 */

namespace DynCom\dc\workerqueue\workers\customerPrice;


use Dotenv\Dotenv;
use DynCom\dc\common\classes\GeneralErrorExceptionHandling;
use DynCom\dc\common\classes\LanguageCollection;
use DynCom\dc\common\classes\LanguageConfig;
use DynCom\dc\common\classes\LanguageRepository;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\common\classes\SiteCollection;
use DynCom\dc\common\classes\SiteConfig;
use DynCom\dc\common\classes\SiteRepository;
use DynCom\dc\dcShop\classes\CustomerCollection;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\CustomerRepository;
use DynCom\dc\dcShop\classes\DefaultItemPricingService;
use DynCom\dc\dcShop\classes\ShopCollection;
use DynCom\dc\dcShop\classes\ShopConfig;
use DynCom\dc\dcShop\classes\ShopLanguageCollection;
use DynCom\dc\dcShop\classes\ShopLanguageConfig;
use DynCom\dc\dcShop\classes\ShopLanguageRepository;
use DynCom\dc\dcShop\classes\ShopRepository;
use DynCom\dc\dcShop\classes\WebshopItemCollection;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantCollection;
use DynCom\dc\dcShop\classes\WebshopItemVariantConfig;
use DynCom\dc\dcShop\classes\WebshopItemVariantRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantService;
use DynCom\dc\RuleEngine\MockActiveActionItemRuleDiscountRepository;
use DynCom\dc\workerqueue\main\AbstractAutonomousQueueWorker;
use DynCom\dc\workerqueue\main\AutonomousQueueWorker;
use DynCom\dc\workerqueue\main\autonomousQueueWorkerTrait;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(dirname(__DIR__))));
$vendorDir = $baseDir . DIRECTORY_SEPARATOR . 'vendor';
$vendorAutoloaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';
include($vendorAutoloaderPath);
include($baseDir . DIRECTORY_SEPARATOR . 'dc' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'common_functions.inc.php');


/**
 * Class CustomerTopItemsQueueWorker
 * @package DynCom\dc\workerqueue\customerTopItems
 */
class CustomerPriceQueueWorker implements AutonomousQueueWorker
{

    use autonomousQueueWorkerTrait;

    public const WORKER_SLEEP_DURATION_SECONDS = 30;

    /**
     * CustomerTopItemsQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param CustomerTopItemsJobHandler $jobHandler
     */
    public function __construct(JobQueueGateway $gateway, CustomerPriceJobHandler $jobHandler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($jobHandler);
    }

    /**
     * @return int
     */
    protected function getWorkerSleepDurationSeconds(): int
    {
        return self::WORKER_SLEEP_DURATION_SECONDS;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerHup(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Hangup-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param $signal
     */
    protected function defaultSignalHandler(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('In default signal-handler, signal is [' . $signal . ']... proceeding to release current job if exists exit');
        $this->releaseCurrentJob();
        exit(0);
    }

    /**
     * @param $signal
     */
    protected function signalHandlerInt(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Interrupt-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerKill(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Kill-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

    /**
     * @param $signal
     */
    protected function signalHandlerTerm(int $signal): void
    {
        $logger = $this->getLogger();
        $logger->info('Received Termination-Signal.');
        $this->defaultSignalHandler($signal);
        return;
    }

}


#GeneralErrorExceptionHandling::setErrorHandler('', '');
#GeneralErrorExceptionHandling::setExceptionHandler('', '');

$logFilePath = $baseDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'CustomerPrice.log';
$logLevel = getenv('APP_LOG_LEVEL');
$logLevelInt = Logger::toMonologLevel($logLevel);
$logFileHandler = new RotatingFileHandler($logFilePath, 10, $logLevelInt);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('CustomerPrice', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";


$envDirBase = $baseDir . DIRECTORY_SEPARATOR . 'config';
$envDirWorkerqueue = $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'workerqueue';
$envDirWorker = $envDirWorkerqueue . DIRECTORY_SEPARATOR . 'workers' . DIRECTORY_SEPARATOR . 'customerPrice';
if (is_dir($envDirBase)) {
    $dotenv = new Dotenv($envDirBase);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenvWorkerQueue = new Dotenv($envDirWorkerqueue);
    $dotenvWorkerQueue->load();
}
if (is_dir($envDirWorker)) {
    $dotenvWorker = new Dotenv($envDirWorker);
    $dotenvWorker->load();
}

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass);

$handlerDBHost = getenv('ITEM_PRICE_DB_HOST');
$handlerDBPort = getenv('ITEM_PRICE_DB_PORT');
$handlerDBUser = getenv('ITEM_PRICE_DB_USER');
$handlerDBPass = getenv('ITEM_PRICE_DB_PASS');
$handlerDBSchema = getenv('ITEM_PRICE_DB_SCHEMA');
$handlerDSN = 'mysql:dbname=' . $handlerDBSchema . ';host=' . $handlerDBHost . ';port=' . (int)$handlerDBPort . ';charset=utf8mb4';

$shopPDO = new PDOQueryWrapper($handlerDBHost,$handlerDBPort,$handlerDBSchema,$handlerDBUser,$handlerDBPass);
$unwrappedShopPDO = $shopPDO->getConnectionObject();

//Init Repositories
$selectionCriteriaHelper = new SelectionCriteriaHelper();

//SiteRepo
$siteConfig = new SiteConfig();
$siteRepo = new SiteRepository($shopPDO, $siteConfig, $selectionCriteriaHelper, new SiteCollection($siteConfig, $selectionCriteriaHelper), false);

//LanguageRepo
$langConfig = new LanguageConfig();
$languageRepo = new LanguageRepository($shopPDO, $langConfig, $selectionCriteriaHelper, new LanguageCollection($langConfig, $selectionCriteriaHelper), false);

//Shop
$shopConfig = new ShopConfig();
$shopRepository = new ShopRepository($shopPDO, $shopConfig, $selectionCriteriaHelper, new ShopCollection($shopConfig, $selectionCriteriaHelper), false);

//ShopLanguage
$shopLanguageConfig = new ShopLanguageConfig();
$shopLanguageRepo = new ShopLanguageRepository($shopPDO, $shopLanguageConfig, $selectionCriteriaHelper, new ShopLanguageCollection($shopLanguageConfig, $selectionCriteriaHelper), false);

//WebshopItem
$webshopItemConfig = new WebshopItemConfig();
$webshopItemRepository = new WebshopItemRepository($shopPDO, $webshopItemConfig, $selectionCriteriaHelper, new WebshopItemCollection($webshopItemConfig, $selectionCriteriaHelper), false);

//Variant
$webshopItemVariantConfig = new WebshopItemVariantConfig();
$webshopItemVariantRepository = new WebshopItemVariantRepository($shopPDO, $webshopItemVariantConfig, $selectionCriteriaHelper, new WebshopItemVariantCollection($webshopItemVariantConfig, $selectionCriteriaHelper), false);
$webshopItemVariantService = new WebshopItemVariantService($shopPDO, $shopRepository, $webshopItemRepository, $webshopItemVariantRepository);

//Customer
$customerConfig = new CustomerConfig();
$customerCollection = new CustomerCollection($customerConfig, $selectionCriteriaHelper);
$customerRepository = new CustomerRepository($shopPDO, $customerConfig, $selectionCriteriaHelper, $customerCollection, false);

//Mock Rule-Discount Repo
$mockRuleDiscountRepo = new MockActiveActionItemRuleDiscountRepository();

//Pricing Service
$pricingService = new DefaultItemPricingService($shopRepository, $webshopItemRepository, $customerRepository, $shopLanguageRepo, $webshopItemVariantRepository, $mockRuleDiscountRepo, $shopPDO);


$handler = new CustomerPriceJobHandler($pricingService,$handlerDSN,$handlerDBUser,$handlerDBPass);
$handler->setLogger($logger);

ini_set('display_errors', '1');
error_reporting(E_ERROR|E_WARNING|E_PARSE);

$workerInstance = new CustomerPriceQueueWorker($gateway, $handler);
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();
