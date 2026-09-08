<?php
declare(strict_types = 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Root dir
$rootDir = dirname(dirname(dirname(dirname(__DIR__))));

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
use DynCom\dc\workerqueue\main\PDOJobQueueGateway;
use DynCom\dc\workerqueue\workers\ItemAvailabilityNotificationWorker;

//Vendor Autoloader
include($rootDir . '/vendor/autoload.php');


//Load environment variables from config if exists
$envDir = $rootDir . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}


include($rootDir . '/dc/common/common_functions.inc.php');


//Init PDOQueryWrapper
$mysqlHost =    getenv('MAIN_MYSQL_DB_HOST');
$mysqlPort =    getenv('MAIN_MYSQL_DB_PORT');
$mysqlUser =    getenv('MAIN_MYSQL_DB_USER');
$mysqlPass =    getenv('MAIN_MYSQL_DB_PASS');
$mysqlSchema =  getenv('MAIN_MYSQL_DB_SCHEMA');
$mysqlDSN = 'mysql:dbname=' . $mysqlSchema . ';host=' . $mysqlHost . ';port=' . (string)$mysqlPort . ';charset=utf8mb4';

$pdo = new PDOQueryWrapper($mysqlHost, $mysqlPort, $mysqlSchema, $mysqlUser, $mysqlPass);
$unwrappedPDO = $pdo->getConnectionObject();

$workerqueueHost = getenv('WORKERQUEUE_DB_HOST');
$workerqueuePort = getenv('WORKERQUEUE_DB_PORT');
$workerqueueUser = getenv('WORKERQUEUE_DB_USER');
$workerqueuePass = getenv('WORKERQUEUE_DB_PASS');
$workerqueueSchema = getenv('WORKERQUEUE_DB_SCHEMA');
$workerqueueDSN = 'mysql:dbname=' . $workerqueueSchema . ';host=' . $workerqueueHost . ';port=' . (string)$workerqueuePort . ';charset=utf8mb4';

//Init Repositories
$selectionCriteriaHelper = new SelectionCriteriaHelper();

//SiteRepo
$siteConfig = new SiteConfig();
$siteRepo = new SiteRepository($pdo, $siteConfig, $selectionCriteriaHelper, new SiteCollection($siteConfig, $selectionCriteriaHelper), false);

//LanguageRepo
$langConfig = new LanguageConfig();
$languageRepo = new LanguageRepository($pdo, $langConfig, $selectionCriteriaHelper, new LanguageCollection($langConfig, $selectionCriteriaHelper), false);

$siteConfig = new SiteConfig();
$siteRepo = new SiteRepository($pdo, $siteConfig, $selectionCriteriaHelper, new SiteCollection($siteConfig, $selectionCriteriaHelper), false);


$shopConfig = new ShopConfig();
$shopRepository = new ShopRepository($pdo, $shopConfig, $selectionCriteriaHelper, new ShopCollection($shopConfig, $selectionCriteriaHelper), false);

$shopLanguageConfig = new ShopLanguageConfig();
$shopLanguageRepo = new ShopLanguageRepository($pdo, $shopLanguageConfig, $selectionCriteriaHelper, new ShopLanguageCollection($shopLanguageConfig, $selectionCriteriaHelper), false);

$webshopItemConfig = new WebshopItemConfig();
$selectionCriteriaHelper = new SelectionCriteriaHelper();
$webshopItemRepository = new WebshopItemRepository($pdo, $webshopItemConfig, $selectionCriteriaHelper, new WebshopItemCollection($webshopItemConfig, $selectionCriteriaHelper), false);


$itemFileCondig = new WebshopItemFileConfig();
$itemFileRepo = new WebshopItemFileRepository($pdo, $itemFileCondig, $selectionCriteriaHelper, new WebshopItemFileCollection($itemFileCondig, $selectionCriteriaHelper), false);

$webshopItemVariantConfig = new WebshopItemVariantConfig();
$webshopItemVariantRepository = new WebshopItemVariantRepository($pdo, $webshopItemVariantConfig, $selectionCriteriaHelper, new WebshopItemVariantCollection($webshopItemVariantConfig, $selectionCriteriaHelper), false);
$webshopItemVariantService = new WebshopItemVariantService($pdo, $shopRepository, $webshopItemRepository, $webshopItemVariantRepository);

$textModuleConfig = new TextModuleConfig();
$textModuleRepository = new TextModuleRepository($pdo, $textModuleConfig, $selectionCriteriaHelper, new TextModuleCollection($textModuleConfig, $selectionCriteriaHelper), false);

$customerConfig = new CustomerConfig();
$customerCollection = new CustomerCollection($customerConfig, $selectionCriteriaHelper);
$customerRepository = new CustomerRepository($pdo, $customerConfig, $selectionCriteriaHelper, $customerCollection, false);

$categoryConfig = new CategoryConfig();
$categoryRepo = new CategoryRepository($pdo, $categoryConfig, $selectionCriteriaHelper, new CategoryCollection($categoryConfig, $selectionCriteriaHelper), false);

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
$shippingClassPriorityProvider = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider($shippingClassRepository,$shopConfig);

//Item Service
$itemService = new WebshopItemService($pdo, $languageRepo, $shopRepository, $webshopItemRepository, $webshopItemVariantService, $pricingService, $canonicalItemURLProvider, $itemFileRepo, $availabilityService, $categoryRepo, $customerRepository, $inventoryStrategyFactory, $attributeService, $descriptionRepo, $shippingClassPriorityProvider);

//Init JobQueueGateWay
$jobQueueGateway = new PDOJobQueueGateway($workerqueueDSN, $workerqueueUser, $workerqueuePass);

//Init Logger
$logfilePath = $rootDir . '/logs/item_availability_notifications.log';
switch (getenv('APP_LOG_LEVEL')) {
    case 'info':
        $logLevel = \Psr\Log\LogLevel::INFO;
        break;
    case 'debug':
        $logLevel = \Psr\Log\LogLevel::DEBUG;
        break;
    case 'notice':
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        break;
    case 'warning':
        $logLevel = \Psr\Log\LogLevel::WARNING;
        break;
    case 'error':
        $logLevel = \Psr\Log\LogLevel::ERROR;
        break;
    case 'alert':
        $logLevel = \Psr\Log\LogLevel::ALERT;
        break;
    case 'critical':
        $logLevel = \Psr\Log\LogLevel::CRITICAL;
        break;
    case 'emergency':
        $logLevel = \Psr\Log\LogLevel::EMERGENCY;
        break;
    default:
        $logLevel = 'error';
        break;
}

//$rotatingFileHandler = new \Monolog\Handler\RotatingFileHandler($logfilePath, 10, $logLevel);
$rotatingFileHandler = new \Monolog\Handler\RotatingFileHandler($logfilePath, 10, LOG_INFO);
$rotatingFileHandler->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());
$logger = new \Monolog\Logger('item_availability_notification_log', [$rotatingFileHandler]);


//Init Worker
include_once __DIR__ . DIRECTORY_SEPARATOR . 'ItemAvailabilityNotificationWorker.php';
$worker = new ItemAvailabilityNotificationWorker($itemService, $shopLanguageRepo, $jobQueueGateway, $textModuleRepository, $unwrappedPDO, $logger);
$worker->doWork();



