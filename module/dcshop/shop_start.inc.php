<?php
/*
//Alter Code aus Frontend für Mobilgeräterkennung
require_once("../../plugins/mobiledetect/Mobile_Detect.php");
$detect = new Mobile_Detect();
if($detect->isMobile())
{
	?>
		<link type="text/css" rel="stylesheet" href="/layout/frontend/demo/css/mobile.css"></link>
	<?
}
 */
use DynCom\dc\common\classes\Hook;
use DynCom\dc\dcShop\campaigns\CampaignRuleFactory;
use DynCom\dc\dcShop\campaigns\CampaignRuleHelper;
use DynCom\dc\dcShop\campaigns\GenericCampaignRepository;
use DynCom\dc\dcShop\classes\AddToBasketErrorListener;
use DynCom\dc\dcShop\classes\Salesperson;
use DynCom\dc\dcShop\classes\UserBasketListener;
use DynCom\dc\RuleEngine\GenericRuleContext;
use DynCom\dc\RuleEngine\GenericRuleEngine;
use DynCom\dc\RuleEngine\GenericRuleList;

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
/* MB OOP */

$startTime = microtime(true);

//include and register Shop-Autoloader
/*
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/module/dcshop/DcShopAutoloader.php');
$shopAutoloader = new DcShopAutoloader();
$shopAutoloader->register();*/


/* ++MB OOP++ */
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/item_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/shop_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/USSalesTax/bootstrap.php';

// Shop und Shopsprache auslesen
$shop = get_shop($GLOBALS['language']['company'], $GLOBALS['language']['shop_code']);
$GLOBALS['shop'] = $shop;

$shopDir = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop';
$dcDir = $rootDir . DIRECTORY_SEPARATOR . 'dc';
$shopCommonDir = $shopDir . DIRECTORY_SEPARATOR . 'common';
$initFilePath = $dcDir . DIRECTORY_SEPARATOR . 'init.php';
$shopInitFilePath = $shopCommonDir . DIRECTORY_SEPARATOR . 'init.php';
$shopStartFilePath = $shopDir . DIRECTORY_SEPARATOR . 'shop_start.inc.php';
$shopFunctionsPath = $shopCommonDir . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';

if (file_exists($initFilePath) && is_file($initFilePath) && is_readable($initFilePath)) {
    include_once $initFilePath;
}
if (file_exists($shopInitFilePath) && is_file($shopInitFilePath) && is_readable($shopInitFilePath)) {
    include_once $shopInitFilePath;
}

$basUrl = $IOCContainer->create('$ShopBaseURL');

// GLOBALS für Artikel- und Kategorie- und Kundenherkunftsshop auslesen
$GLOBALS['shop']['item_source'] = ($GLOBALS['shop']['use_items_from_shop_code'] != '') ? $GLOBALS['shop']['use_items_from_shop_code'] : $GLOBALS['shop']['item_source'] = $GLOBALS['shop']['code'];
$GLOBALS['shop']['category_source'] = ($GLOBALS['shop']['use_categorys_from_shop_code'] != '') ? $GLOBALS['shop']['use_categorys_from_shop_code'] : $GLOBALS['shop']['category_source'] = $GLOBALS['shop']['code'];
$GLOBALS['shop']['customer_source'] = ($GLOBALS['shop']['use_customer_from_shop_code'] != '') ? $GLOBALS['shop']['use_customer_from_shop_code'] : $GLOBALS['shop']['customer_source'] = $GLOBALS['shop']['code'];

//Shop-Setup des Artikelherkunftsshops holen für MwSt-Berechnung
$item_source_shop = get_shop($GLOBALS['language']['company'], $GLOBALS['shop']['item_source']);
$GLOBALS['item_source_shop'] = $item_source_shop;

// Shopsprache auslesen
$shop_language = get_shop_language($GLOBALS['language']['company'], $GLOBALS['language']['shop_code'], $GLOBALS['language']['shop_language_code']);
$GLOBALS['shop_language'] = $shop_language;

// Shop-Einrichtung auslesen
$shop_setup = get_shop_setup($GLOBALS["language"]["company"]);
$GLOBALS["shop_setup"] = $shop_setup;

// shop.config einbinden (ergänzt shop_setup -> nie vor get_shop_setup)
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/shop.config.php';

if (isset($_GET['card']) && $_GET['card'] <> '')
{
        $item = get_item_by_id($_GET['card']);
        $category =  get_item_default_category($item);
        $GLOBALS['category'] = array_key_exists('id',$category) && $category['id'] > 0 ? $category : null;
        $categoryID = $GLOBALS['category']["id"];
}
else
{
    if($GLOBALS['shop_setup']['show_short_url'])
    {
        $category = null;
        $slevelCode1 = $_GET["slevel_" . 1 . ""] ?? null;
        if (null !== $slevelCode1) {
            $categoryID = (int)get_id_by_code($slevelCode1);
        }
    }
    else
    {
        //Get category (moved to have category in basket event listeners)
        $layer_no = get_layer_no();
        $slevelCode = $_GET["slevel_" . $layer_no . ""] ?? null;
        if (null !== $slevelCode) {
            $categoryID = (int)get_id_by_code($_GET["slevel_" . $layer_no . ""]);
        } else {
            $categoryID = 0;
        }
    }
}

//Eingeloggten Debitor mit falscher Sprache ausloggen
logout_customer_invalid_language($GLOBALS['visitor'], $shop_language['code'], $GLOBALS['mysql_con']);

//shop-spezifische cnfig einbinden (für file_exists immer Pfad von document_root aus - relative Pfade führen immer zu false!!)
if (file_exists(rtrim(dirname(dirname(__DIR__)), '/') . "/module/dcshop/" . $GLOBALS['shop']['code'] . ".config.php")) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . $GLOBALS['shop']['code'] . ".config.php");
}

//textkonstants pro shop
$text_constant = array();
if (file_exists(rtrim(dirname(dirname(__DIR__)), '/') . "/module/dcshop/" . strtolower($GLOBALS['shop']['code']) . ".text_constants.inc.php")) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . strtolower($GLOBALS['shop']['code']) . ".text_constants.inc.php";
    if (is_array($text_constant) && !empty($text_constant)) {
        $text_constant = $text_constant[$language["code"]];
        $GLOBALS["tc"] = array_merge($GLOBALS['tc'], $text_constant);
    }
}

if ($GLOBALS["shop_language"]["fitting_items_text_module"] != '') {
    $GLOBALS["tc"]["references"] = $GLOBALS["shop_language"]["fitting_items_text_module"];
}

if ($GLOBALS["shop_language"]["alternative_items_text_module"] != '') {
    $GLOBALS["tc"]["related_items"] = $GLOBALS["shop_language"]["alternative_items_text_module"];
}

if ($GLOBALS["shop_language"]["accessories_items_text_module"] != '') {
    $GLOBALS["tc"]["accessories"] = $GLOBALS["shop_language"]["accessories_items_text_module"];
}

if ($GLOBALS["shop_language"]["spare_parts_text_module"] != '') {
    $GLOBALS["tc"]["spare_parts"] = $GLOBALS["shop_language"]["spare_parts_text_module"];
}

/** @var \DynCom\dc\common\classes\Templating $templatingRule */
$templatingRule = $IOCContainer->resolve('DynCom\dc\common\classes\Templating');
$templatingRule->updateLocTextConstants($GLOBALS['tc']);


//HTTPS-Prüfung für Endkundenshop
/* Rausgenommen, da neue Shops grundlegend https */
/*
if ($shop['shop_typ'] == 1) {
    if ($_GET['level_1'] != 'shop' && isset($_SERVER['HTTPS'])) {
        headerFunctionBridge("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
    if (($_GET["shop_category"] == 'order') && !isset($_GET["card"])) {
        if (!isset($_SERVER['HTTPS'])) {
            IF (!local_environment() && $GLOBALS['shop_setup']['use_ssl'] == 1) {
                headerFunctionBridge("Location: https://" . $GLOBALS['shop_setup']['ssl_site'] . $_SERVER['REQUEST_URI']);
            }
        }
    } else {
        if (isset($_SERVER['HTTPS']) && $_GET["shop_category"] != 'search') {
            headerFunctionBridge("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }
    }
}*/

if (($GLOBALS["site"]["login_required"] == "1") && $GLOBALS["visitor"]["frontend_login"] && $GLOBALS['cookie_visitor']) {
    $query = 'UPDATE main_visitor SET cookie_only=1 WHERE id=' . $GLOBALS['visitor']['id'];
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

// An- und Abmeldungen ausführen
shop_login_listener();
if ($_GET['login']) {
    side_login();
}

//MB --- OOP---
//Templating
$textProvider = $IOCContainer->create(\DynCom\dc\common\classes\Templating::class);

//Shop Configuration
/**
 * @var $currShop \DynCom\dc\dcShop\classes\Shop
 */
$currShop = $IOCContainer->create('$CurrShop');
/**
 * @var $currShopLang \DynCom\dc\dcShop\classes\ShopLanguage
 */
$currShopLang = $IOCContainer->create('$CurrShopLanguage');
$currVisitorObj = $IOCContainer->create('$CurrVisitor');
$currUserObj = $IOCContainer->create('$CurrUser');
$extCustObj = $IOCContainer->create('$CurrCustomerWithPermissionGroupCodes');
$currShopConfig = $IOCContainer->create('$CurrShopConfig');
$webshopItemBuilder = $IOCContainer->create(\DynCom\dc\dcShop\classes\WebshopItemBuilder::class);

//Register EventListeners
$addToBasketListener = new AddToBasketErrorListener($GLOBALS['flashMessageBag'], $textProvider);
$basketNotifierEventListener = new \DynCom\dc\dcShop\classes\BasketChangeNotificationListener($GLOBALS['flashMessageBag'], $textProvider);
$couponLineRepository = $IOCContainer->create(\DynCom\dc\dcShop\classes\CouponLineRepository::class);
$IVATManager = $IOCContainer->create('$VATManager');
$basketChangeListener = new \DynCom\dc\dcShop\classes\BasketChangeListener($couponLineRepository, $currShopConfig, $IVATManager, $GLOBALS['flashMessageBag'], $textProvider,$webshopItemBuilder);
Hook::registerEventListener($addToBasketListener, AddToBasketErrorListener::EVENT_NAME);
Hook::registerEventListener($basketNotifierEventListener,\DynCom\dc\dcShop\classes\BasketChangeNotificationListener::EVENT_NAME_ITEM_ADDED);
Hook::registerEventListener($basketNotifierEventListener,\DynCom\dc\dcShop\classes\BasketChangeNotificationListener::EVENT_NAME_ITEM_QTY_CHANGED);
Hook::registerEventListener($basketNotifierEventListener,\DynCom\dc\dcShop\classes\BasketChangeNotificationListener::EVENT_NAME_ITEM_REMOVED);
Hook::registerEventListener($basketNotifierEventListener,\DynCom\dc\dcShop\classes\BasketChangeNotificationListener::EVENT_NAME_ITEM_QTY_ADJUSTED);
Hook::registerEventListener($basketChangeListener,\DynCom\dc\dcShop\classes\BasketChangeListener::EVENT_NAME_BASKET_CHANGED);

if (!empty(getenv('TRACKING_API_ROOT'))) {
    $trackingApiService = new \DynCom\dc\tracking\TrackingAPIService();
    $trackingEventListener = new \DynCom\dc\tracking\TrackingEventListener($trackingApiService);
    Hook::registerEventListener($trackingEventListener,\DynCom\dc\tracking\TrackingEventListener::EVENT_NAME_ITEM_ADDED);
    Hook::registerEventListener($trackingEventListener,\DynCom\dc\tracking\TrackingEventListener::EVENT_NAME_ITEM_QTY_CHANGED);
    Hook::registerEventListener($trackingEventListener,\DynCom\dc\tracking\TrackingEventListener::EVENT_NAME_ITEM_REMOVED);
    Hook::registerEventListener($trackingEventListener,\DynCom\dc\tracking\TrackingEventListener::EVENT_NAME_ORDER_COMPLETE);
    Hook::registerEventListener($trackingEventListener,\DynCom\dc\tracking\TrackingEventListener::EVENT_NAME_ITEM_ADDED);
}

//ActiveRuleDiscountRepository
$ruleDiscountRepository = $IOCContainer->create('$activeRuleDiscountRepository');

//Init User Basket
$currUserBasket = $IOCContainer->create('$CurrUserBasket');

//Init User Basket Persistence Handler
$basketPersistenceHandler = $IOCContainer->create('$UserBasketPersistenceHandler');


//Basket Listener
$basketListener = $IOCContainer->create('DynCom\dc\dcShop\classes\UserBasketListener');
if ($basketListener instanceof UserBasketListener) {
    $basketListener->handleRequest();
}

//CategoryTree

$host = getenv('MAIN_MYSQL_DB_HOST');
$port = getenv('MAIN_MYSQL_DB_PORT');
$schema = getenv('MAIN_MYSQL_DB_SCHEMA');
$user = getenv('MAIN_MYSQL_DB_USER');
$pass = getenv('MAIN_MYSQL_DB_PASS');

$dsn = 'mysql:dbname=' . $schema . ';host=' . $host . ';port=' . (string)$port . ';charset=utf8mb4';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn,$user,$pass,$options);
$start = microtime(true);
/** @var \DynCom\dc\dcShop\classes\CategoryTreeBuilder $treeBuilder */
$treeBuilder = new \DynCom\dc\dcShop\classes\CategoryTreeBuilder($pdo);
/** @var \DynCom\dc\dcShop\classes\CategoryTreeNode $root */
$root = $treeBuilder->getCategoryTree($currShop->getCompany(),$currShop->getUseCategoriesFromShopCode(),$currShopLang->code);

$GLOBALS['curr_category_tree'] = $root;

if ($categoryID > 0) {
    $category = get_category_from_tree_as_array_by_id($root, $categoryID);
} else {
    $category = null;
}
$GLOBALS['category'] = $category;

//RuleContext
$ruleContext = $IOCContainer->create('$CurrRuleContext');

//ShippingOptionRepository
$shippingOptionRespository = $IOCContainer->create('$ShippingOptionRepository');

//CampaignRepository
$campaignRepository = $IOCContainer->create('DynCom\dc\dcShop\campaigns\GenericCampaignRepository');

//CampaignRuleApplicationHelper
$campaignRuleHelper = new CampaignRuleHelper($IOCContainer);

//CampaignRuleFactory
$campaignRuleFactory = $IOCContainer->create('DynCom\dc\dcShop\campaigns\CampaignRuleFactory');

//Build RuleList and run RuleEngine
if ($campaignRuleHelper instanceof CampaignRuleHelper && $campaignRuleFactory instanceof CampaignRuleFactory && $campaignRepository instanceof GenericCampaignRepository && $ruleContext instanceof GenericRuleContext) {
    $campaignRuleHelper->defaultPopulateRuleContext($ruleContext);
    $campaignRuleHelper->removeInvalidRules($ruleContext);
    if ($currShopConfig->isValid()) {
        $campaigns = $campaignRepository->getAllForCurrShopConfig($currShopConfig);
        $ruleList = new GenericRuleList();
        foreach ($campaigns as $campaignkey => $campaign) {
            $campaignRule = $campaignRuleFactory->getRuleFromCampaign($campaign);
            if ($campaignRule !== null) {
                $ruleList->add($campaignRule);
                //unset($campaigns[$campaignkey]);
            }
        }
        $GLOBALS['active_campaigns'] = $campaigns;
        $ruleEngine = new GenericRuleEngine();
        $ruleEngine->evaluateAllRules($ruleList, $ruleContext);

        $matchedRules = [];

        if ($ruleContext->hasVariable('AppliedRules')) {
            $matchedRules = $ruleContext->getVariable('AppliedRules');
        }
        ob_start();
        echo "<pre>";
        echo "Applied Rules: ";
        var_dump($matchedRules);
        echo "</pre>";
        $GLOBALS['matched_rules'] = ob_get_clean();
    }
}
$duration = microtime(true) - $startTime;
$GLOBALS['listener_rules_duration'] = " DURATION BASKET LISTENER & RULES: $duration";



    if ($GLOBALS['shop_setup']['show_short_url']) {

        if (isset($GLOBALS['category'])) {
            $path = category_get_full_path($GLOBALS['category']);
            $_SERVER["REQUEST_URI"] = $path;
            extract_site_request_uri_variables();
        }
    }




//MB +++ OP +++

// Login prüfen und Besucher ohne Login auf Loginseite leiten
if (($GLOBALS["site"]["login_required"] == "1") && !$GLOBALS["visitor"]["frontend_login"] && $_GET['action'] != 'login') {
    $logoutSite = site_getbyid($GLOBALS['language']['logout_site_id']);
    $logoutLanguage = language_getbyid($GLOBALS['language']['logout_language_id']);
    $logoutNavigation = navigation_getbyid($GLOBALS['language']['logout_navigation_id']);
    $path = customizeUrl(true,$logoutSite,$logoutLanguage) . navigation_path_rek($logoutNavigation["id"], '');
    die();
    $port = '';
    if ($_SERVER['SERVER_PORT'] !== '80' || $_SERVER['SERVER_PORT'] !== '') {
        $port = ':' . $_SERVER['SERVER_PORT'];
    }
    if ($_POST['login_error'] || $_GET['login_error']) {
        $login_error = '&login_error=true';
    } else {
        $login_error = '';
    }

    die(header('Location: //' . $_SERVER['SERVER_NAME'] . $port . $path . '?action=login' . $login_error));
}

// Benutzer und Debitor auslesen
$shop_user = get_shop_user($GLOBALS["visitor"]);

//FK Vertreterportal ausgewählte customer_id für Vertreter setzen
if ($GLOBALS['shop']['shop_typ'] == 2) {
    if (isset($_POST['customer_select'])) {
        $shop_user['customer_no'] = $_POST['customer_select'];
        $_SESSION['customer_no'] = $_POST['customer_select'];
    } elseif (isset($_POST['input_id']) && $_REQUEST['action_id'] == "set") {
        $customerquery = "SELECT customer_no FROM shop_customer WHERE id = '" . $_POST['input_id'] . "'";
        $customerresult = mysqli_query($GLOBALS['mysql_con'], $customerquery);
        $customer = mysqli_fetch_assoc($customerresult);
        $shop_user['customer_no'] = $customer['customer_no'];
        $_SESSION['customer_no'] = $customer['customer_no'];
    } elseif ($_SESSION['customer_no'] != '') {
        $shop_user['customer_no'] = $_SESSION['customer_no'];
    }
    if ($shop_user['customer_no']) {
        $userObj = $IOCContainer->create('$CurrUser');
        if ($userObj instanceof Salesperson) {
            $userObj->setCustomerNo($shop_user['customer_no']);
        }
    }
}


$GLOBALS["shop_user"] = $shop_user;
$customerObj = $IOCContainer->create('$CurrCustomer');

/**
 * @var $customerObj \DynCom\dc\dcShop\classes\Customer
 */
$shop_customer = [];
$customerNo = $GLOBALS['shop_user']['customer_no'] ?? '';

if ('' !== $customerNo) {
    $shop_customer = $customerObj->getAllFieldsAsArray();
}
//var_dump($GLOBALS['shop_customer']);
//Währung des Kunden setzen, wenn nicht bereits im Visitor gesetzt oder bei neuer Anmeldung
if ($GLOBALS['shop']['shop_typ'] != 2) {
    if (($shop_customer['currency_code'] != '' && $GLOBALS['visitor']['currency_code'] == '') || $_GET['action'] == 'shop_login' || $GLOBALS['shop']['shop_typ'] == 2) {
        $query = "UPDATE main_visitor
				  SET currency_code = '" . $shop_customer['currency_code'] . "'
				  WHERE id = " . $visitor['id'];
        mysqli_query($GLOBALS['mysql_con'], $query);
        $visitor = get_visitor_small(session_id());
    }
}
$GLOBALS["visitor"] = $visitor;
$GLOBALS["shop_customer"] = $shop_customer;

//Shop-Währung auslesen
$currency = get_shop_currency($GLOBALS['language']['company'], $GLOBALS['shop']['customer_source'], $GLOBALS['shop_language']['code'], $GLOBALS['visitor']['currency_code']);
$GLOBALS['shop_currency'] = $currency;

// Felder für Kontaktformulare vorbelegen
$_POST["input_auto_customer_no"] = $shop_customer["customer_no"];
$_POST["input_auto_customer_name"] = $shop_customer['name'];
$_POST["input_auto_user_name"] = $shop_user["name"];
$_POST["input_auto_user_email"] = $shop_user["email"];

// Warenkorbfunktionen ausführen
//shop_basket_listener();


//Globals für Kategorie und Artikel füllen

$GLOBALS['item'] = '';
if ($_GET["card"] <> '') {
    $GLOBALS['item'] = get_item_by_card_id($GLOBALS['shop']['company'], $GLOBALS['shop']['item_source'], $GLOBALS['shop_language']['code'], $_GET["card"]);
}

//TCs für VAT dynamisch setzen
include(rtrim(dirname(dirname(__DIR__)), '/') . '/module/dcshop/common/classes/VatTcProvider.php');
$VatTcProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\VatTcProvider');
$VatTcProvider->setVATTextConstants();


