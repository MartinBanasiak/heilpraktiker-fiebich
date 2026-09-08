<?php
declare(strict_types = 1);
declare(ticks = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 29.11.2016
 * Time: 19:47
 */

namespace DynCom\dc\workerqueue\workers\itemAvailabilityNotification;


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
use DynCom\dc\dcShop\classes\CategoryCollection;
use DynCom\dc\dcShop\classes\CategoryConfig;
use DynCom\dc\dcShop\classes\CategoryRepository;
use DynCom\dc\dcShop\classes\CustomerCollection;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\CustomerRepository;
use DynCom\dc\dcShop\classes\DefaultItemAvailabilityService;
use DynCom\dc\dcShop\classes\DefaultItemPricingService;
use DynCom\dc\dcShop\classes\InventoryStrategyFactory;
use DynCom\dc\dcShop\classes\ShopCollection;
use DynCom\dc\dcShop\classes\ShopConfig;
use DynCom\dc\dcShop\classes\ShopLanguageCollection;
use DynCom\dc\dcShop\classes\ShopLanguageConfig;
use DynCom\dc\dcShop\classes\ShopLanguageRepository;
use DynCom\dc\dcShop\classes\ShopRepository;
use DynCom\dc\dcShop\classes\TextModuleCollection;
use DynCom\dc\dcShop\classes\TextModuleConfig;
use DynCom\dc\dcShop\classes\TextModuleRepository;
use DynCom\dc\dcShop\classes\WebshopItemAttributeService;
use DynCom\dc\dcShop\classes\WebshopItemCanonicalURLProvider;
use DynCom\dc\dcShop\classes\WebshopItemCollection;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\classes\WebshopItemDescriptionCollection;
use DynCom\dc\dcShop\classes\WebshopItemDescriptionConfig;
use DynCom\dc\dcShop\classes\WebshopItemDescriptionRepository;
use DynCom\dc\dcShop\classes\WebshopItemFileCollection;
use DynCom\dc\dcShop\classes\WebshopItemFileConfig;
use DynCom\dc\dcShop\classes\WebshopItemFileRepository;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\classes\WebshopItemService;
use DynCom\dc\dcShop\classes\WebshopItemVariantCollection;
use DynCom\dc\dcShop\classes\WebshopItemVariantConfig;
use DynCom\dc\dcShop\classes\WebshopItemVariantRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantService;
use DynCom\dc\RuleEngine\MockActiveActionItemRuleDiscountRepository;
use DynCom\dc\workerqueue\main\AbstractAutonomousQueueWorker;
use DynCom\dc\workerqueue\main\AutonomousQueueWorker;
use DynCom\dc\workerqueue\main\autonomousQueueWorkerTrait;
use DynCom\dc\workerqueue\main\JobHandler;
use DynCom\dc\workerqueue\main\JobQueueGateway;
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;
use DynCom\dc\workerqueue\workers\ItemAvailabilityNotificationWorker;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

date_default_timezone_set('Europe/Berlin');
//Requires that composer manages autoloading for this module
$baseDir = dirname(dirname(dirname(dirname(__DIR__))));
$vendorDir = $baseDir . '/vendor';
$vendorAutoloaderPath = $vendorDir . '/autoload.php';
include($vendorAutoloaderPath);
include($baseDir . DIRECTORY_SEPARATOR . 'dc' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'common_functions.inc.php');

/**
 * Class ItemAvailabilityNotificationQueueWorker
 * @package DynCom\dc\workerqueue\workers\itemAvailabilityNotification
 */
class ItemAvailabilityNotificationQueueWorker implements AutonomousQueueWorker
{

    use autonomousQueueWorkerTrait;

    public const WORKER_SLEEP_DURATION_SECONDS = 30;

    /**
     * ItemAvailabilityNotificationQueueWorker constructor.
     * @param JobQueueGateway $gateway
     * @param ItemAvailabilityNotificationJobHandler $handler
     */
    public function __construct(JobQueueGateway $gateway, ItemAvailabilityNotificationJobHandler $handler)
    {
        $this->setGateway($gateway);
        $this->setJobHandler($handler);
    }

    /**
     * @return int
     */
    protected function getWorkerSleepDurationSeconds()
    {
        return self::WORKER_SLEEP_DURATION_SECONDS;
    }

    /**
     * @param int $signal
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

GeneralErrorExceptionHandling::setErrorHandler('', '');
GeneralErrorExceptionHandling::setExceptionHandler('', '');

$logFilePath = $baseDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'ItemAvailabilityNotification.log';
$logLevel = getenv('APP_LOG_LEVEL');
$logLevelInt = Logger::toMonologLevel($logLevel);
$logFileHandler = new RotatingFileHandler($logFilePath, 10, $logLevelInt);
$processor = new PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new Logger('ItemAvailabilityNotification', [$logFileHandler]);
echo __FILE__ . " logging to [$logFilePath] (Rotating)";


$envDirBase = $baseDir . DIRECTORY_SEPARATOR . 'config';
$envDirWorkerqueue = $baseDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'workerqueue';

if (is_dir($envDirBase)) {
    $dotenv = new Dotenv($envDirBase);
    $dotenv->load();
}
if (is_dir($envDirWorkerqueue)) {
    $dotenvWorkerQueue = new Dotenv($envDirWorkerqueue);
    $dotenvWorkerQueue->load();
}

$gatewayHost = getenv('WORKERQUEUE_DB_HOST');
$gatewayPort = getenv('WORKERQUEUE_DB_PORT');
$gatewayUser = getenv('WORKERQUEUE_DB_USER');
$gatewayPass = getenv('WORKERQUEUE_DB_PASS');
$gatewaySchema = getenv('WORKERQUEUE_DB_SCHEMA');

$gatewayDSN = 'mysql:dbname=' . $gatewaySchema . ';host=' . $gatewayHost . ';port=' . (int)$gatewayPort . ';charset=utf8mb4';
$gateway = new PDOJobQueueGateway($gatewayDSN, $gatewayUser, $gatewayPass);

$mainHost = getenv('MAIN_MYSQL_DB_HOST');
$mainPort = getenv('MAIN_MYSQL_DB_PORT');
$mainUser = getenv('MAIN_MYSQL_DB_USER');
$mainPass = getenv('MAIN_MYSQL_DB_PASS');
$mainSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
$pdo = new PDOQueryWrapper($mainHost, $mainPort, $mainSchema, $mainUser, $mainPass);
$unwrappedPDO = $pdo->getConnectionObject();

//Init Repositories
$selectionCriteriaHelper = new SelectionCriteriaHelper();

//SiteRepo
$siteConfig = new SiteConfig();
$siteRepo = new SiteRepository($pdo, $siteConfig, $selectionCriteriaHelper, new SiteCollection($siteConfig, $selectionCriteriaHelper), false);

//LanguageRepo
$langConfig = new LanguageConfig();
$languageRepo = new LanguageRepository($pdo, $langConfig, $selectionCriteriaHelper, new LanguageCollection($langConfig, $selectionCriteriaHelper), false);

//Shop
$shopConfig = new ShopConfig();
$shopRepository = new ShopRepository($pdo, $shopConfig, $selectionCriteriaHelper, new ShopCollection($shopConfig, $selectionCriteriaHelper), false);

//ShopLanguage
$shopLanguageConfig = new ShopLanguageConfig();
$shopLanguageRepo = new ShopLanguageRepository($pdo, $shopLanguageConfig, $selectionCriteriaHelper, new ShopLanguageCollection($shopLanguageConfig, $selectionCriteriaHelper), false);

//WebshopItem
$webshopItemConfig = new WebshopItemConfig();
$selectionCriteriaHelper = new SelectionCriteriaHelper();
$webshopItemRepository = new WebshopItemRepository($pdo, $webshopItemConfig, $selectionCriteriaHelper, new WebshopItemCollection($webshopItemConfig, $selectionCriteriaHelper), false);

//ItemFile
$itemFileConfig = new WebshopItemFileConfig();
$itemFileRepo = new WebshopItemFileRepository($pdo, $itemFileConfig, $selectionCriteriaHelper, new WebshopItemFileCollection($itemFileConfig, $selectionCriteriaHelper), false);

//Variant
$webshopItemVariantConfig = new WebshopItemVariantConfig();
$webshopItemVariantRepository = new WebshopItemVariantRepository($pdo, $webshopItemVariantConfig, $selectionCriteriaHelper, new WebshopItemVariantCollection($webshopItemVariantConfig, $selectionCriteriaHelper), false);
$webshopItemVariantService = new WebshopItemVariantService($pdo, $shopRepository, $webshopItemRepository, $webshopItemVariantRepository);

//TextModule
$textModuleConfig = new TextModuleConfig();
$textModuleRepository = new TextModuleRepository($pdo, $textModuleConfig, $selectionCriteriaHelper, new TextModuleCollection($textModuleConfig, $selectionCriteriaHelper), false);

//Customer
$customerConfig = new CustomerConfig();
$customerCollection = new CustomerCollection($customerConfig, $selectionCriteriaHelper);
$customerRepository = new CustomerRepository($pdo, $customerConfig, $selectionCriteriaHelper, $customerCollection, false);

//Category
$categoryConfig = new CategoryConfig();
$categoryRepo = new CategoryRepository($pdo, $categoryConfig, $selectionCriteriaHelper, new CategoryCollection($categoryConfig, $selectionCriteriaHelper), false);

//Description
$descConfig = new WebshopItemDescriptionConfig();
$descriptionRepo = new WebshopItemDescriptionRepository($pdo, $descConfig, $selectionCriteriaHelper, new WebshopItemDescriptionCollection($descConfig, $selectionCriteriaHelper), false);

//Init Availabiltiy Service
$inventoryStrategyFactory = new InventoryStrategyFactory($webshopItemRepository, $webshopItemVariantRepository, $pdo);
$availabilityService = new DefaultItemAvailabilityService($shopRepository, $inventoryStrategyFactory, $webshopItemRepository, $webshopItemVariantRepository);

//CanonicalItemURLProvider
$canonicalItemURLProvider = new WebshopItemCanonicalURLProvider($siteRepo, $languageRepo, $shopRepository, $webshopItemRepository, $webshopItemVariantService, $pdo);

//Mock Rule-Discount Repo
$mockRuleDiscountRepo = new MockActiveActionItemRuleDiscountRepository();

//Pricing Service
$pricingService = new DefaultItemPricingService($shopRepository, $webshopItemRepository, $customerRepository, $shopLanguageRepo, $webshopItemVariantRepository, $mockRuleDiscountRepo, $pdo);

//Attribute Service
$attributeService = new WebshopItemAttributeService($pdo, $selectionCriteriaHelper);

$shippingClassConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassConfig();
$shippingClassCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassCollection($shippingClassConfig,$selectionCriteriaHelper);
$shippingClassRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassRepository($pdo,$shippingClassConfig,$selectionCriteriaHelper,$shippingClassCollection);
$shippingClassPriorityProvider = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider($shippingClassRepository,);

//Item Service
$itemService = new WebshopItemService($pdo, $languageRepo, $shopRepository, $webshopItemRepository, $webshopItemVariantService, $pricingService, $canonicalItemURLProvider, $itemFileRepo, $availabilityService, $categoryRepo, $customerRepository, $inventoryStrategyFactory, $attributeService, $descriptionRepo, $shippingClassPriorityProvider);

$notificationWorker = new ItemAvailabilityNotificationWorker($itemService, $shopLanguageRepo, $gateway, $textModuleRepository, $unwrappedPDO,$logger);


$handler = new ItemAvailabilityNotificationJobHandler($notificationWorker);
$handler->setLogger($logger);

$workerInstance = new ItemAvailabilityNotificationQueueWorker($gateway, $handler);
$workerInstance->setLogger($logger);
$workerInstance->initializeSignalHandling();
$workerInstance->manageJobs();