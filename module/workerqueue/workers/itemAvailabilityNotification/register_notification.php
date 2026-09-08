<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.10.2016
 * Time: 15:24
 */
use DynCom\dc\common\classes\Language;
use DynCom\dc\common\classes\LanguageCollection;
use DynCom\dc\common\classes\LanguageConfig;
use DynCom\dc\common\classes\LanguageRepository;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\common\classes\SiteCollection;
use DynCom\dc\common\classes\SiteConfig;
use DynCom\dc\dcShop\classes\CustomerCollection;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\CustomerRepository;
use DynCom\dc\dcShop\classes\Shop;
use DynCom\dc\dcShop\classes\ShopCollection;
use DynCom\dc\dcShop\classes\ShopConfig;
use DynCom\dc\dcShop\classes\ShopRepository;
use DynCom\dc\dcShop\classes\WebshopItemCollection;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantCollection;
use DynCom\dc\dcShop\classes\WebshopItemVariantConfig;
use DynCom\dc\dcShop\classes\WebshopItemVariantRepository;

$rootDir = dirname(dirname(dirname(dirname(__DIR__))));
//ini_set('display_errors',1);
//error_reporting(E_ALL);
 headerFunctionBridge('Cache-Control: no-cache, must-revalidate'); headerFunctionBridge('Content-type: application/json');

//Vendor Autoloader
include($rootDir . '/vendor/autoload.php');

//Load environment variables from config if exists
$envDir = $rootDir . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}


//Dc Autoloader
include($rootDir . '/dc/DcAutoloader.php');
$dcAutoloader = new DcAutoloader();
$dcAutoloader->register();

//Module Autoloader
include($rootDir . '/module/ModuleAutoloader.php');
$moduleAutoloader = new ModuleAutoloader();
$moduleAutoloader->register();

//Include common functions
include($rootDir . '/dc/common/common_functions.inc.php');


//Evaluate POST-Data
$siteCode = (string)filter_input(INPUT_POST, 'site_code', FILTER_SANITIZE_STRING);
$siteLanguageCode = (string)filter_input(INPUT_POST, 'site_language_code', FILTER_SANITIZE_STRING);
$itemNo = (string)filter_input(INPUT_POST, 'item_no', FILTER_SANITIZE_STRING);
$variantCode = (string)filter_input(INPUT_POST, 'variant_code', FILTER_SANITIZE_STRING);
$customerNo = (string)filter_input(INPUT_POST, 'customer_no', FILTER_SANITIZE_STRING);
$userEmail = (string)filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
$userName = (string)filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);

$dataArr = [
    'siteCode' => $siteCode,
    'siteLanguageCode' => $siteLanguageCode,
    'itemNo' => $itemNo,
    'variantCode' => $variantCode,
    'customerNo' => $customerNo,
    'userEmail' => $userEmail,
    'userName' => $userName,
];


//Init PDOQueryWrapper
$mysqlHost = getenv('MAIN_MYSQL_DB_HOST');
$mysqlPort = getenv('MAIN_MYSQL_DB_PORT');
$mysqlUser = getenv('MAIN_MYSQL_DB_USER');
$mysqlPass = getenv('MAIN_MYSQL_DB_PASS');
$mysqlSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
$mysqlDSN = 'mysql:dbname=' . $mysqlSchema . ';host=' . $mysqlHost . ';port=' . (string)$mysqlPort . ';charset=utf8mb4';
$pdo = new PDOQueryWrapper($mysqlHost, $mysqlPort, $mysqlSchema, $mysqlUser, $mysqlPass);
$unwrappedPDO = $pdo->getConnectionObject();

$selectionCriteriaHelper = new SelectionCriteriaHelper();

//SiteRepo
$siteConfig = new SiteConfig();
$siteRepo = new \DynCom\dc\common\classes\SiteRepository($pdo, $siteConfig, $selectionCriteriaHelper, new SiteCollection($siteConfig, $selectionCriteriaHelper), false);

//LanguageRepo
$langConfig = new LanguageConfig();
$languageRepo = new LanguageRepository($pdo, $langConfig, $selectionCriteriaHelper, new LanguageCollection($langConfig, $selectionCriteriaHelper), false);

//ShopRepo
$shopConfig = new ShopConfig();
$shopRepo = new ShopRepository($pdo, $shopConfig, $selectionCriteriaHelper, new ShopCollection($shopConfig, $selectionCriteriaHelper), false);

//WebshopItemRepo
$webshopItemConfig = new WebshopItemConfig();
$webshopItemRepo = new WebshopItemRepository($pdo, $webshopItemConfig, $selectionCriteriaHelper, new WebshopItemCollection($webshopItemConfig, $selectionCriteriaHelper), false);

//CustomerRepo
$customerConfig = new CustomerConfig();
$customerRepo = new CustomerRepository($pdo, $customerConfig, $selectionCriteriaHelper, new CustomerCollection($customerConfig, $selectionCriteriaHelper), false);

//VariantRepo
$variantConfig = new WebshopItemVariantConfig();
$variantRepo = new WebshopItemVariantRepository($pdo, $variantConfig, $selectionCriteriaHelper, new WebshopItemVariantCollection($variantConfig, $selectionCriteriaHelper), false);

//Check data
$sitePrimary = ['code' => $siteCode];
$site = $siteRepo->findByAltPrimary($sitePrimary);
if (!($site->getID() > 0)) {
    echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
    exit(1);
}

$siteLangPrimary = ['main_site_id' => $site->getID(), 'code' => $siteLanguageCode];
/**
 * @var $language Language
 */
$language = $languageRepo->findByAltPrimary($siteLangPrimary);
if (!($language->getID() > 0)) {
    echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
    exit(1);
}

$targetShopPrimary = $language->getLinkedShopPrimary();
$shopLanguageCode = $language->shop_language_code;

/**
 * @var $targetShop Shop
 */
if (!isset($targetShopPrimary['company']) || empty($targetShopPrimary['company'])) {
    echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
    exit(1);
}

$company = isset($targetShopPrimary['company']) ? $targetShopPrimary['company'] : '';
$targetShop = $shopRepo->findByAltPrimary($targetShopPrimary);
$targetShopCode = $targetShop->getCode();
$itemShopCode = $targetShop->getUseItemsFromShopCode();
$customerShopCode = $targetShop->getUseCustomersFromShopCode();
$categoryShopCode = $targetShop->getUseCategoriesFromShopCode();
$itemShopCode = $itemShopCode ?: $targetShopCode;
$customerShopCode = $customerShopCode ?: $targetShopCode;
$categoryShopCode = $categoryShopCode ?: $targetShopCode;


$itemPrimary = ['company' => $company, 'shop_code' => $itemShopCode, 'language_code' => $shopLanguageCode, 'item_no' => $itemNo];
$item = $webshopItemRepo->findByAltPrimary($itemPrimary);
if (!($item->getID() > 0)) {
    echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
    var_dump($itemPrimary);
    exit(1);
}

if ($variantCode !== '') {
    $variantPrimary = ['company' => $company, 'item_no' => $itemNo, 'code' => $variantCode];
    $variant = $variantRepo->findByAltPrimary($variantPrimary);
    if (!($variant->getID() > 0)) {
        echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
        exit(1);
    }
}

if ($customerNo !== '') {
    $customerPrimary = ['company' => $company, 'shop_code' => $customerShopCode, 'language_code' => $shopLanguageCode, 'customer_no' => $customerNo];
    $customer = $customerRepo->findByAltPrimary($customerPrimary);
    if (!($customer->getID() > 0)) {
        echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
        exit(1);
    }
}

$query = '
        INSERT IGNORE INTO 
        active_item_availability_notification
        SET 
           company = :company,
           site_code = :site_code,
           site_language_code = :site_language_code,
           target_shop_code = :target_shop_code,
           item_shop_code = :item_shop_code,
           customer_shop_code = :customer_shop_code,
           shop_language_code = :shop_language_code,
           item_no = :item_no,
           variant_code = :variant_code,
           customer_no = :customer_no,
           user_name = :user_name,
           user_email = :user_email           
    ';
$stmt = $unwrappedPDO->prepare($query);
$stmt->bindValue(':company', $company, PDO::PARAM_STR);
$stmt->bindValue(':site_code', $siteCode, PDO::PARAM_STR);
$stmt->bindValue(':site_language_code', $siteLanguageCode, PDO::PARAM_STR);
$stmt->bindValue(':target_shop_code', $targetShopCode, PDO::PARAM_STR);
$stmt->bindValue(':item_shop_code', $itemShopCode, PDO::PARAM_STR);
$stmt->bindValue(':customer_shop_code', $customerShopCode, PDO::PARAM_STR);
$stmt->bindValue(':shop_language_code', $shopLanguageCode, PDO::PARAM_STR);
$stmt->bindValue(':item_no', $itemNo, PDO::PARAM_STR);
$stmt->bindValue(':variant_code', $variantCode, PDO::PARAM_STR);
$stmt->bindValue(':customer_no', $customerNo, PDO::PARAM_STR);
$stmt->bindValue(':user_name', $userName, PDO::PARAM_STR);
$stmt->bindValue(':user_email', $userEmail, PDO::PARAM_STR);
$stmt->execute();
$error = $stmt->errorInfo();
if ($error[0] === '00000') {
    echo json_encode(true);
} else {
    echo json_encode(['line' => __LINE__, 'data' => $dataArr]);
}

