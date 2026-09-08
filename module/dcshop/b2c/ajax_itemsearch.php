<?

use DynCom\dc\common\classes\HTMLSnippetProvider;
use DynCom\dc\common\classes\Language;
use DynCom\dc\common\classes\LanguageCollection;
use DynCom\dc\common\classes\LanguageConfig;
use DynCom\dc\common\classes\LanguageRepository;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\SelectionCriteriaHelper;
use DynCom\dc\common\classes\Site;
use DynCom\dc\common\classes\SiteCollection;
use DynCom\dc\common\classes\SiteConfig;
use DynCom\dc\common\classes\SiteRepository;
use DynCom\dc\common\classes\Templating;
use DynCom\dc\common\classes\URL;
use DynCom\dc\common\classes\Visitor;
use DynCom\dc\common\classes\VisitorCollection;
use DynCom\dc\common\classes\VisitorConfig;
use DynCom\dc\common\classes\VisitorRepository;
use DynCom\dc\dcShop\classes\AdvancedPriceProvider;
use DynCom\dc\dcShop\classes\BasicPriceProvider;
use DynCom\dc\dcShop\classes\Category;
use DynCom\dc\dcShop\classes\CategoryCollection;
use DynCom\dc\dcShop\classes\CategoryConfig;
use DynCom\dc\dcShop\classes\CategoryRepository;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\CustomerCollection;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\CustomerRepository;
use DynCom\dc\dcShop\classes\SalesPriceCollection;
use DynCom\dc\dcShop\classes\SalesPriceConfig;
use DynCom\dc\dcShop\classes\SalesPriceRepository;
use DynCom\dc\dcShop\classes\Shop;
use DynCom\dc\dcShop\classes\ShopCollection;
use DynCom\dc\dcShop\classes\ShopConfig;
use DynCom\dc\dcShop\classes\ShopLanguage;
use DynCom\dc\dcShop\classes\ShopLanguageCollection;
use DynCom\dc\dcShop\classes\ShopLanguageConfig;
use DynCom\dc\dcShop\classes\ShopLanguageRepository;
use DynCom\dc\dcShop\classes\ShopRepository;
use DynCom\dc\dcShop\classes\TextModule;
use DynCom\dc\dcShop\classes\TextModuleCollection;
use DynCom\dc\dcShop\classes\TextModuleConfig;
use DynCom\dc\dcShop\classes\TextModuleRepository;
use DynCom\dc\dcShop\classes\User;
use DynCom\dc\dcShop\classes\UserCollection;
use DynCom\dc\dcShop\classes\UserConfig;
use DynCom\dc\dcShop\classes\UserRepository;
use DynCom\dc\dcShop\classes\VATManager;
use DynCom\dc\dcShop\classes\WebshopItem;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\classes\WebshopItemCollection;
use DynCom\dc\dcShop\classes\WebshopItemConfig;
use DynCom\dc\dcShop\classes\WebshopItemRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantCollection;
use DynCom\dc\dcShop\classes\WebshopItemVariantConfig;
use DynCom\dc\dcShop\classes\WebshopItemVariantRepository;
use DynCom\dc\dcShop\classes\WebshopItemVariantService;
use DynCom\dc\RuleEngine\ActiveActionItemRuleDiscountRepository;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider;


// Konfigurationsdatei laden
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/frontend/frontend_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once  $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/shop_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/category_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/item_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/shop.config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';

// Verbindung mit Datenbank herstellen
db_connect();
// POST dekodieren und schützen
//$_POST = json_decode($_POST,true);
//if (empty($_POST)) {
$_POST = json_decode(stripslashes(file_get_contents("php://input")), TRUE);
//}
//$_GET = json_decode($_GET,true);
$_GET  = secure_array($_GET, TRUE);
$_POST = secure_array($_POST, TRUE);

$sid            = $_POST["sid"];
$GLOBALS["sid"] = $sid;


$company                            = $_POST["company"];
$GLOBALS["shop"]["company"]         = $company;
$shop_code                          = $_POST["shop_code"];
$GLOBALS["shop"]["code"]            = $shop_code;
$language_code                      = $_POST["language_code"];
$GLOBALS["shop_language"]["code"]   = $language_code;
$item_source                        = $_POST["item_source"];
$GLOBALS["shop"]["item_source"]     = $item_source;
$site_language                      = $_POST["site_language"];
$GLOBALS["site"]["language"]        = $site_language;
$GLOBALS["language"]["code"]        = $site_language;
$site_code                          = $_POST["site_code"];

// Setup auslesen
$setup            = setup_get();
$GLOBALS["setup"] = $setup;

// Aktuelle Webseite auslesen
$site                           = ($site_code && $site_code <> '') ? site_getbycode($site_code) : site_getbyid($setup["std_main_site_id"]);
$GLOBALS["site"]                = $site;
$siteDomain = str_replace(['http://','https://'],'',$GLOBALS['site']['site_url']);
//echo "SITE IS: ";
set_sitecode_get();

//$GLOBALS["site"]["code"]            = $site_code;
$GLOBALS["default_img"]             = $_POST["default_img"];
$GLOBALS["layout"]["code"]          = $_POST["layout_code"];
$GLOBALS["shop_currency"]["code"]   = $_POST["currency_code"];
$GLOBALS['shop']['cross_price_typ'] = $_POST["shop_cross_price_type"];
$GLOBALS["show_price"]              = $_POST["show_price"];

// Textbausteine auslesen
$text_constant = array();
$text_constant_path = $rootDir . '/dc/common/text_constants.inc.php';
if (is_file($text_constant_path) && is_readable($text_constant_path)) {
    require_once($text_constant_path);
}
$GLOBALS["tc"] = $text_constant[$site_language];

$beforeOOPInit = microtime(true);
if(!is_callable('DcAutoloader')) {

    function DcAutoloader( $objectName ) {

        $dirArr = array(
            /* Base folders */
            '../../../dc/common/classes/',
            '../../../dc/common/traits/',
            '../../../dc/common/interfaces/',
            /* Shop folders */
            '../../../module/dcshop/common/classes/',
            '../../../module/dcshop/common/traits/',
            '../../../module/dcshop/common/interfaces/',
            /* RMA folders */
            '../../../module/dcshop/rma/classes/',
            '../../../module/dcshop/rma/traits/',
            '../../../module/dcshop/rma/interfaces/'
        );

        foreach ($dirArr as $dir) {
            if (!is_dir($dir)) {
                /*echo "
                    not a dir: " . $dir;*/
            }
            if (file_exists(realpath($dir . $objectName . '.php'))) {
                include(realpath($dir . $objectName . '.php'));
                return;
            }
        }
    }

    spl_autoload_register('DcAutoloader');
}

if(!isset($dbConnObj)) {
    $dbConnObj = new PDOQueryWrapper('localhost', 3506, 'dcshop', 'root', '');
}

if(!isset($snippetProvider)) {
    $snippetProvider = new HTMLSnippetProvider();
}
if(!isset($templateEngine)) {
    $templateEngine = new Templating($snippetProvider, $GLOBALS['tc']);
}

if(!isset($criteriaValidationService)) {
    $criteriaValidationService = new SelectionCriteriaHelper();
}

if(!isset($visitorRepository)) {
    $visitorConfig     = new VisitorConfig();
    $visitorObj        = new Visitor($visitorConfig);
    $visitorCollection = new VisitorCollection($visitorConfig, $criteriaValidationService);
    $visitorRepository = new VisitorRepository($dbConnObj, $visitorConfig, $criteriaValidationService, $visitorCollection, TRUE);
}

if(!isset($siteRepository)) {
    $siteConfig     = new SiteConfig();
    $siteObj        = new Site($siteConfig);
    $siteCollection = new SiteCollection($siteConfig, $criteriaValidationService);
    $siteRepository = new SiteRepository($dbConnObj, $siteConfig, $criteriaValidationService, $siteCollection, TRUE);
}

if(!isset($languageRepository)) {
    $languageConfig     = new LanguageConfig();
    $languageObj        = new Language($languageConfig);
    $languageCollection = new LanguageCollection($languageConfig, $criteriaValidationService);
    $languageRepository = new LanguageRepository($dbConnObj, $languageConfig, $criteriaValidationService, $languageCollection, TRUE);
}

if(!isset($shopRepository)) {
    $shopConfig     = new ShopConfig();
    $shopObj        = new Shop($shopConfig);
    $shopCollection = new ShopCollection($shopConfig, $criteriaValidationService);
    $shopRepository = new ShopRepository($dbConnObj, $shopConfig, $criteriaValidationService, $shopCollection, TRUE);
}

if(!isset($shopLanguageRepository)) {
    $shopLanguageConfig     = new ShopLanguageConfig();
    $shopLanguageObj        = new ShopLanguage($shopLanguageConfig);
    $shopLanguageCollection = new ShopLanguageCollection($shopLanguageConfig, $criteriaValidationService);
    $shopLanguageRepository = new ShopLanguageRepository($dbConnObj, $shopLanguageConfig, $criteriaValidationService, $shopLanguageCollection, TRUE);
}

if(!isset($categoryRepository)) {
    $categoryConfig     = new CategoryConfig();
    $categoryObj        = new Category($categoryConfig);
    $categoryCollection = new CategoryCollection($categoryConfig, $criteriaValidationService);
    $categoryRepository = new CategoryRepository($dbConnObj, $categoryConfig, $criteriaValidationService, $categoryCollection, TRUE);
}

if(!isset($itemRepository)) {
    $itemConfig     = new WebshopItemConfig();
    $itemObj        = new WebshopItem($itemConfig);
    $itemCollection = new WebshopItemCollection($itemConfig, $criteriaValidationService);
    $itemRepository = new WebshopItemRepository($dbConnObj, $itemConfig, $criteriaValidationService, $itemCollection, TRUE);
}

if(!isset($userRepository)) {
    $userConfig     = new UserConfig();
    $userObj        = new User($userConfig);
    $userCollection = new UserCollection($userConfig, $criteriaValidationService);
    $userRepository = new UserRepository($dbConnObj, $userConfig, $criteriaValidationService, $userCollection, FALSE);
}

if(!isset($customerRepository)) {
    $customerConfig     = new CustomerConfig();
    $customerObj        = new Customer($customerConfig);
    $customerCollection = new CustomerCollection($customerConfig, $criteriaValidationService);
    $customerRepository = new CustomerRepository($dbConnObj, $customerConfig, $criteriaValidationService, $customerCollection, FALSE);
}

if(!isset($textModuleRepository)) {
    $textModuleConfig = new TextModuleConfig();
    $textModuleObj = new TextModule($textModuleConfig);
    $textModuleCollection = new TextModuleCollection($textModuleConfig,$criteriaValidationService);
    $textModuleRepository = new TextModuleRepository($dbConnObj,$textModuleConfig,$criteriaValidationService,$textModuleCollection,TRUE);
}

if(!isset($webshopItemRepository)) {
    $webshopItemConfig = new WebshopItemConfig();
    $webshopItemObj = new WebshopItem($webshopItemConfig);
    $webshopItemCollection = new WebshopItemCollection($webshopItemConfig,$criteriaValidationService);
    $webshopItemRepository = new WebshopItemRepository($dbConnObj,$webshopItemConfig,$criteriaValidationService,$webshopItemCollection,FALSE);
}
$afterOOPInit = microtime(true);
$rawOOPInitTime = $afterOOPInit - $beforeOOPInit;

$schema = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) ? URL::PROTOCOL_HTTPS : URL::PROTOCOL_HTTP;
$baseUrl = new URL($schema,null,null,$siteDomain,null,'/' . $GLOBALS['language']['code'] . '/',null,null);
$setupClass = new \DynCom\dc\dcShop\classes\ShopSetup(new \DynCom\dc\dcShop\classes\ShopSetupConfig());
$setupClass->mapFromArray($setup);
$currConfig = new CurrShopConfiguration($setupClass, $shopObj, $shopLanguageObj, $visitorObj, $userObj, $customerObj, $baseUrl);

//Sonderzeichen (ohne Whitespace) aus Suchbegriff entfernen
//$search_term = preg_replace('/[^a-zA-Z0-9\s]/','',$_POST["search_term"]);
$_POST['input_search'] = str_replace(array('+', '%2B'), ' ', $_REQUEST["input_search"]);
$_POST['input_search'] = str_replace('.', '', $_REQUEST["input_search"]);
$_POST['input_search'] = preg_replace('/\G\s|\s(?=\s*$)/', ' ', $_REQUEST["input_search"]);
$search_term           = $_REQUEST["input_search"];


// Login prüfen, Visitor & Customer setzen
/*$visitor            = get_visitor_local(TRUE);
$GLOBALS["visitor"] = $visitor;*/

$user                                             = get_shop_user($GLOBALS["visitor"]);
$GLOBALS["user"]                                  = $user;
$GLOBALS["shop_user"]                             = $user;
$customer                                         = get_shop_customer_local($GLOBALS["user"], $company);
$GLOBALS["customer"]                              = $customer;
$GLOBALS["shop_customer"]                         = $customer;
$GLOBALS["shop_customer"]["customer_price_group"] = $_POST["customer_price_group"];
$GLOBALS["image_config"]                          = unserialize(base64_decode($_POST["image_config"]),['allowed_classes' => false]);


$OOPInit2Start = microtime(true);
$currSite = $siteObj->getNullObject();
$currSite->mapFromArray($GLOBALS['site']);

$currLang = $languageObj->getNullObject();
$currLang->mapFromArray($GLOBALS['language']);

$currShop = $shopRepository->findByAltPrimary(array('company' => $GLOBALS["shop"]["company"],'code' => $GLOBALS["shop"]["code"]));

$currShopLanguage = $shopLanguageObj->getNullObject();
$currShopLanguage->mapFromArray($GLOBALS['shop_language']);
$currVisitor = $visitorRepository->findByAltPrimary(array('session_id' => $_COOKIE['sidb2c']));

if (!isset($customerObj) || !($customerObj->getID() > 0)) {
    $customerObj  = $customerRepository->findByID($GLOBALS['customer']['id']);
}
$GLOBALS['visitor'] = (array) $currVisitor;

$currUser = $userObj->getNullObject();
if(is_array($GLOBALS['shop_user'])) {
    $currUser->mapFromArray($GLOBALS['shop_user']);
}

$currCustomer = $customerObj->getNullObject();
/*if(is_array($GLOBALS['shop_customer'])) {
    $currCustomer->mapFromArray($GLOBALS['shop_customer']);
}*/


if(!isset($salesPriceRepository)) {
    $salesPriceConfig = new SalesPriceConfig();
    $salesPriceCollection = new SalesPriceCollection($salesPriceConfig,$criteriaValidationService);
    $salesPriceRepository = new SalesPriceRepository($dbConnObj,$salesPriceConfig,$criteriaValidationService,$salesPriceCollection,FALSE);
    $VATManager = new VATManager($dbConnObj,$currShop->company,$currShop->vat_bus_posting_group,$currShop->prices_including_vat);
    $basicPriceProvider = new BasicPriceProvider($dbConnObj,$currShop,$VATManager,$currShopLanguage->default_currency_code);

    $variantConfig = new WebshopItemVariantConfig();
    $variantCollection = new WebshopItemVariantCollection($variantConfig,$criteriaValidationService);
    $variantRepo = new WebshopItemVariantRepository($dbConnObj,$variantConfig,$criteriaValidationService,$variantCollection,false);
    $variantService = new WebshopItemVariantService($dbConnObj, $shopRepository, $itemRepository, $variantRepo);

    $shippingClassConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassConfig();
    $shippingClassCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassCollection($shippingClassConfig,$criteriaValidationService);
    $shippingClassRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingClassRepository($dbConnObj,$shippingClassConfig,$criteriaValidationService,$shippingClassCollection);
    $shippingClassPriorityProvider = new ShippingClassPriorityProvider($shippingClassRepositorym,$currConfig);

    $itemBuilder = new WebshopItemBuilder($currVisitor, $currCustomer, $currShop, $currLang, $webshopItemRepository, $variantRepo, $shippingClassPriorityProvider);
    $ruleDiscountRepository = new ActiveActionItemRuleDiscountRepository($dbConnObj,$shopObj);
    $advancedPriceProvider = new AdvancedPriceProvider($basicPriceProvider,$currShop,$VATManager,$currShopLanguage->default_currency_code,$ruleDiscountRepository);
}

$OOPInit2end = microtime(true);
$OOPInit2Time = $OOPInit2end - $OOPInit2Start;
$rawOOPInitTime += $OOPInit2Time;

if (empty($GLOBALS["shop_customer"]["customer_no"])) {
    $GLOBALS["shop_customer"]["customer_no"] = $_POST["customer_no"];
}
$customer_no = $GLOBALS["shop_customer"]["customer_no"];

/*var_dump($sid);
$objectArr = unserialize($visitor['serialized_objects']);
extract($objectArr);
var_dump($advancedPriceProvider);*/

$_REQUEST["input_search"] = trim($_REQUEST["input_search"]);

$initTermCount = count(explode(' ',trim($_POST['input_search'])));
$preparedInputString = normalize_search_string($_REQUEST["input_search"]);
$preparedQueryString = $preparedInputString;
//$preparedQueryString = '(' . $preparedInputString . ')^2';
//$variants = get_all_whitespace_collapsed_variants($_REQUEST["input_search"]);
$variantString = '';
$variantCount = count($variants);
$percent = round(((($initTermCount + $variantCount) / $initTermCount) * 100));
if($variantCount > 0) {
    foreach($variants as $variant) {
        $variantString .= '%20OR%20' . rawurlencode($variant);
    }
    $preparedQueryString = '' . $preparedQueryString . $variantString . '';
}

$queryfilterstring = '&fq=company:"' . urlencode($GLOBALS['shop']['company']) . '"' .
    '&ps=4'.
    '&tie=0.2'.
    '&fq=shop_code:' . urlencode($GLOBALS['shop']['code']) .
    '&fq=language_code:' . urlencode($GLOBALS['shop_language']['code']) .
    '&fq=active:true' .
    '&fq=validity_from:[*%20TO%20NOW]' .
    '&fq=validity_to:[NOW%20TO%20*]' .
    '&group.limit=100' .
    '&mm=' .  rawurlencode('90%') .
    '&q.op=OR' .
    '&qf=item_no_intact^500 description^100 main_item_desc_combined^90 main_item_desc_combined_exact^90 main_item_desc_combined_edge^30 main_item_desc_combined_nowhitespace^100 main_item_desc_combined_nowhitespace_edge^80 main_item_desc_combined_ngram^10 did_you_mean^30 main_item_desc_combined_phon^2 main_item_desc_combined_phon_german^8 main_item_desc_combined_phon_english^8 long_descriptions^30 attribute_names^2 text_phon_german^5 text_phon_english^5' .
    '&pf=description_intact^150 description^100 main_item_desc_combined_exact^140 main_item_desc_combined^90 main_item_desc_combined_nowhitespace^100 did_you_mean^30 main_item_desc_combined_phon^2 main_item_desc_combined_phon_german^8 main_item_desc_combined_phon_english^8 long_descriptions^30 attribute_names^2 text_phon_german^5 text_phon_english^5';

$finalQueryString = 'q=' . $preparedQueryString . '&wt=phps&rows=5&group.ngroups=true' . $queryfilterstring;
$final_url = 'http://localhost:8983/solr/dcshop/itemquery';


$cred = sprintf( 'Authorization: Basic %s',
    base64_encode( 'admin:ICG#2013x' )
);

$opts = array(
    'http' => array(
        'method' => 'GET',
        'header' => $cred
    )
);
$final_url = 'http://127.0.0.1:8983/solr/dcshop/itemquery';
echo "<!-- SOLR: $final_url?$finalQueryString -->";


$beforeSolrRequest = microtime(true);
$process = curl_init();
curl_setopt($process, CURLOPT_URL,$final_url);
curl_setopt($process, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded", $cred));
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_POST,1);
curl_setopt($process, CURLOPT_POSTFIELDS,$finalQueryString);
curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
$curl_return = curl_exec($process);
curl_close($process);
$afterSolrRequest = microtime(true);
$rawSolrCurlTime = $afterSolrRequest - $beforeSolrRequest;

$serializedResult = $curl_return;
$resultUnserialized = unserialize($serializedResult,['allowed_classes' => false]);

$num_found = $resultUnserialized['grouped']['parent_item_no']['ngroups'];
$result_items_pre = $resultUnserialized['grouped']['parent_item_no']['groups'];

$iterator = 0;

$default_img_path = $GLOBALS["image_config"][1]["path"] . "/" . $GLOBALS["default_img"];
$result_items = array();
$limit = 5;

if($num_found > $limit) {
    $iteration_limit = $limit;
} else {
    $iteration_limit = $num_found;
}

$beforeGetItemData = microtime(true);
for($iterator = 0;$iterator < $iteration_limit;$iterator++) {
    $result_items[$iterator] = $result_items_pre[$iterator]['doclist']['docs'][0];
    $currItem = $webshopItemRepository->findByID($result_items[$iterator]['id']);
    $result_items[$iterator]['final_itemlink'] = '/' .customizeUrl(). '/shop' . $result_items[$iterator]['canonical_url'] . '-p' . $result_items[$iterator]['id']."/";
    $tmp_path = '';
    if(!empty($result_items[$iterator]['main_preview_image_filename'])) {
        $tmp_path = $GLOBALS['image_config'][1]['path'] . '/' . $result_items[$iterator]['main_preview_image_filename'];
    }
    if(!file_exists("../../.." . $tmp_path)) {
        $result_items[$iterator]['final_imagelink'] = $default_img_path;
    } else {
        $result_items[$iterator]['final_imagelink'] = $tmp_path;
    }
    $result_items[$iterator]['unit_price'] = $advancedPriceProvider->getItemCustomerPrice($currItem, 1, $currCustomer, $GLOBALS["shop_currency"]["code"]);
    $result_items[$iterator]['cross_price'] = $advancedPriceProvider->getItemCrossPrice($currItem,$currCustomer,$GLOBALS["shop_currency"]["code"]);
}
$afterGetItemData = microtime(true);
$rawGetItemData = $afterGetItemData - $beforeGetItemData;

$suggestions = array();

if ($num_found > 0) {
    echo "<div id=\"item_search_suggestions\">";
    echo "<!-- OOP INIT DURATION: $rawOOPInitTime || SOLR REQUEST DURATION: $rawSolrCurlTime || GET ITEM DATA DURATION: $rawGetItemData -->";
    $i = 1;
    foreach ($result_items as $item) {
        ?>
        <div class="search_suggestion" id="suggestion_<?= $i ?>" tabindex="<?= $i ?>"
             data-suggestionIndex="<?= $i ?>" onclick="window.location.href='<?= $item['final_itemlink'] ?>'">
            <div class="search_suggestion_img">
                <div><img src="<?= $item['final_imagelink'] ?>" alt="" /></div>
            </div>
            <!--<div class="search_suggestion_itemno"><!--<?= "<strong>" . $GLOBALS["tc"]["item_no"] . ":</strong> " . $item["item_no_intact"] ?></div><div>score: <?= $item["score"] ?></div>-->
            <div class="search_suggestion_item_desc"><?= $item['manufacturer'] . ' ' . $item['variant_type_intact'] ?></div>
            <?
            //NO prices so long as no campaign handling
            if($item['cross_price'] > 0 && false) {
             ?>
                <div class="search_suggestion_item_strikethrough_price"><?= $templateEngine->formatPriceEUR($item['cross_price']) ?></div>
            <?
            }
             ?>
            <!-- PRICE-DATA:
            <? //var_dump($item['unit_price']); ?>
            -->
            <!--<div class="search_suggestion_item_price"><?= $item['unit_price'] ?></div>-->
            <div class="clearfloat"></div>
        </div>
        <?
        $i++;
    }
    echo "</div>";
}
/*
function get_visitor_local( $extended = FALSE ) {
    $local_sess_id = session_id();
    if ($extended) {
        $query = "DELETE FROM main_visitor WHERE session_date <= DATE_SUB(NOW(),INTERVAL 90 MINUTE) OR session_id = '' OR session_id IS NULL";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($GLOBALS['sid'] != '') {
            $query  = "SELECT * FROM main_visitor WHERE session_id = '" . $GLOBALS['sid'] . "'";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            if (mysqli_num_rows($result) == 1 && !empty($local_sess_id)) {
                $row         = mysqli_fetch_assoc($result);
                $countquery  = "SELECT * FROM main_visitor WHERE session_id = '" . session_id() . "'";
                $countresult = mysqli_query($GLOBALS['mysql_con'], $countquery);
                if (mysqli_num_rows($countresult) > 0) {
                    $query = "DELETE FROM main_visitor WHERE session_id = '" . $GLOBALS['sid'] . "'";
                    mysqli_query($GLOBALS['mysql_con'], $query);
                    $oldvisitor = mysqli_fetch_assoc($countresult);
                    $query      = "UPDATE main_visitor
							  SET frontend_login = TRUE, session_date  = NOW(), main_user_id = '" . $row['main_user_id'] . "'
							  WHERE id = '" . $oldvisitor['id'] . "'";
                    mysqli_query($GLOBALS['mysql_con'], $query);
                } else {
                    $query = "UPDATE main_visitor SET session_id = '" . session_id() . "' WHERE id = " . $row['id'];
                    mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }
        }
        $query  = "SELECT * FROM main_visitor WHERE session_id = '" . session_id() . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) != 0) {
            $visitor = @mysqli_fetch_array($result);
            $query   = "UPDATE main_visitor SET session_date = NOW() WHERE id = '" . $visitor["id"] . "'";
            @mysqli_query($GLOBALS['mysql_con'], $query);
            return $visitor;
        } else {
            $query = "INSERT INTO main_visitor (id,session_id,session_date) VALUES (NULL,'" . session_id() . "',NOW())";
            if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
                //return get_visitor();
            }
        }
    }
    if (!$extended) {
        $query   = "SELECT * FROM main_visitor WHERE session_id = '" . $GLOBALS["sid"] . "'";
        $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
        $visitor = @mysqli_fetch_assoc($result);
        return $visitor;
    }
}*/

function get_shop_customer_local( $shop_user, $shop_company ) {
    if ($shop_user["customer_no"] <> '') {
        $query  = "SELECT *
				  FROM shop_customer
				  WHERE customer_no = '" . $shop_user["customer_no"] . "'
				  AND company = '" . $shop_company . "'
				  LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (mysqli_num_rows($result) == 1) {
            $shop_customer = @mysqli_fetch_array($result);
            return $shop_customer;
        }
    }
}

?>
