<?php
$domainName = 'https://' . $_SERVER['SERVER_NAME'];

ini_set('display_errors', 'false');
//admin redirect
$dcPattern = '/^\/dc(?:\/|$)/';
if (preg_match($dcPattern,$_SERVER['REQUEST_URI'])){
    include(dirname(__DIR__) .DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin.php');
    exit(0);
}
$userdataPattern = '/^\/userdata(?:\/|$)/';
$userdataDotPattern = '/^\/userdata(?:\/\.)/';
$userdataPrivatePattern = '/^\/userdata(?:\/private\/)/';
if (!is_dir($baseDir . $_SERVER['REQUEST_URI']) && !preg_match($userdataPrivatePattern,$_SERVER['REQUEST_URI']) && !preg_match($userdataDotPattern,$_SERVER['REQUEST_URI']) && preg_match($userdataPattern,$_SERVER['REQUEST_URI']) && file_exists($baseDir . $_SERVER['REQUEST_URI'])){
    headerFunctionBridge('Content-type: ' . mime_content_type($baseDir . $_SERVER['REQUEST_URI']));
    include($baseDir . $_SERVER['REQUEST_URI']);
    exit(0);
}

$robotsPattern = '/^\/robots\.txt/';
if (preg_match($robotsPattern,$_SERVER['REQUEST_URI']) && file_exists($baseDir . '/robots.txt')){
    headerFunctionBridge('Content-type: text/plain');
    include($baseDir . '/robots.txt');
    exit(0);
}

//opcache_reset();
//ini_set('output_buffering','On');
date_default_timezone_set('Europe/Berlin');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//Vendor Autoloader
$currDir = __DIR__;
$rootDir = dirname(dirname($currDir));
$dcDir = $rootDir . '/dc';
include($rootDir . '/vendor/autoload.php');

//Load common function
require_once(__DIR__ . DIRECTORY_SEPARATOR . "../common/common_functions.inc.php");

//Load frontend function
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'frontend_functions.inc.php');


$prodeployRouting = false;
if(array_key_exists('PRODEPLOY_ROUTING',$GLOBALS) && $GLOBALS['PRODEPLOY_ROUTING'] === true) {
    $prodeployRouting = true;
} else {
    $GLOBALS['PRODEPLOY_ROUTING'] = false;
}

//Create ServerRequest
if (!isset($request) || !($request instanceof \Psr\Http\Message\ServerRequestInterface)) {
    $request = get_psr7_request_from_globals_single_instance();
}
if (!isset($response) || !($response instanceof \Psr\Http\Message\ResponseInterface)) {
    $response = new \Zend\Diactoros\Response();
}

$GLOBALS['GLOBAL_REQUEST'] = &$request;
$GLOBALS['GLOBAL_RESPONSE'] = &$response;

//Set headers
if ($prodeployRouting) {
    $response = $response->withHeader('Content-Type', 'text/html;charset=utf-8');
    $response = $response->withHeader('X-XSS-Protection', '1;mode=block');
    $response = $response->withHeader('X-Content-Type-Options', 'nosniff');
    $response = $response->withHeader('Strict-Transport-Security', 'max-age=31536000');
    $response = $response->withHeader('Referrer-Policy', 'no-referrer-when-downgrade');
    $response = $response->withHeader('X-Frame-Options', 'sameorigin');
} else {
    headerFunctionBridge("Content-Type: text/html; charset=utf-8");
    headerFunctionBridge("X-XSS-Protection: 1; mode=block");
    headerFunctionBridge("X-Content-Type-Options: nosniff");
    headerFunctionBridge("Strict-Transport-Security: max-age=31536000");
    headerFunctionBridge("Referrer-Policy: no-referrer-when-downgrade");
    headerFunctionBridge("X-Frame-Options: sameorigin");
}


//Load environment variables from config if exists
$baseDir = rtrim(dirname(__DIR__, 2), '/');
$envDir = $baseDir . '/config';
$envFilePath = $envDir . '/.env';
if (is_dir($envDir) && file_exists($envFilePath) && is_file($envFilePath) && is_readable($envFilePath)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$GLOBALS['projectRoot'] = trim(getenv('PROJECT_ROOT'),'/\\');
if (!empty($GLOBALS['projectRoot'])) {
    $GLOBALS['projectRoot'] = '/' . $GLOBALS['projectRoot'];
}


//Load frontend function
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'frontend_functions.inc.php');


if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
    $_SERVER["QUERY_STRING"] = filter_var($_SERVER["QUERY_STRING"], FILTER_SANITIZE_URL);
}
if (isset($_SERVER["REDIRECT_URL"]) && !empty($_SERVER["REDIRECT_URL"])) {
    $_SERVER["REDIRECT_URL"] = filter_var($_SERVER["REDIRECT_URL"], FILTER_SANITIZE_URL);
}
if (isset($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"])) {
    $_SERVER["REQUEST_URI"] = filter_var($_SERVER["REQUEST_URI"], FILTER_SANITIZE_URL);
}

$prepStatement = 'SELECT DISTINCT url  FROM iframe_header';

$dbHost = getenv('MAIN_MYSQL_DB_HOST');
$dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
$dbUser = getenv('MAIN_MYSQL_DB_USER');
$dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
$dbPass = getenv('MAIN_MYSQL_DB_PASS');


$allowedWebSites = "https://www.computop-paygate.com";


$shopPdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
$GLOBALS["shopPdo"] = $shopPdo;
$shopPdo->setQuery($prepStatement);
$shopPdo->prepareQuery();
$shopPdo->executePreparedStatement();
$resArr = $shopPdo->getResultArray();
foreach ($resArr as $site) {
    $allowedWebSites = $allowedWebSites . " " . $site['url'];
}
$allowedWebSites .= "  https://*.amazon.com  https://*.payments-amazon.com ";


//CSP only works in modern browsers Chrome 25+, Firefox 23+, Safari 7+
$cspHeaderName = "Content-Security-Policy";
$cspValue =
    "connect-src 'self' ws://127.0.0.1:35729  *.googleapis.com;" . // XMLHttpRequest (AJAX request), WebSocket or EventSource.
    "default-src  " . $domainName . " *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de stats.g.doubleclick.net ;" . // Default policy for loading html elements
    "frame-ancestors 'self';" . //allow parent framing - this one blocks click jacking and ui redress
    "child-src 'self' " . $allowedWebSites . " *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de stats.g.doubleclick.net ;" . // vaid sources for frames
    "font-src 'self' data: *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de stats.g.doubleclick.net ;" . //For the FONT
    "media-src 'self' " . $domainName . " *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de stats.g.doubleclick.net ;" . // vaid sources for media (audio and video html tags src)
    "img-src 'self' data: https://*.amazon.com  *.ssl-images-amazon.com https://*.payments-amazon.com  https://*.google-analytics.com https://d23yuld0pofhhw.cloudfront.net *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de *.jameda-elements.de *.goyellow.de stats.g.doubleclick.net ;" . // img-src
    "frame-src 'self' " . $allowedWebSites . " *.dr-schmiedel.de *.jameda.de *.google.com *.youtube.com;" . // vaid sources for frames
    "object-src 'none'; " . // valid object embed and applet tags src
    "script-src 'self' 'unsafe-eval' 'unsafe-inline' " . $domainName . " *.docplanner.com *.ssl-images-amazon.com *.amazon.com  *.payments-amazon.com *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de *.jameda-elements.de stats.g.doubleclick.net ;" . // allows js from self, jquery and google analytics.  Inline allows inline js
    "style-src 'self' 'unsafe-inline' *.googleapis.com *.gstatic.com *.youtube.com *.facebook.com *.facebook.net *.google-analytics.com *.googletagmanager.com *.typekit.net *.google.com *.google.de stats.g.doubleclick.net ;";// allows css from self and inline allows inline css
//Sends the Header in the HTTP response to instruct the Browser how it should handle content and what is whitelisted
//Its up to the browser to follow the policy which each browser has varying support
if ($prodeployRouting) {
    $response = $response->withHeader($cspHeaderName,$cspValue);
} else {
    headerFunctionBridge($cspHeaderName . ': ' .$cspValue);
}


//Dc Autoloader
/*
include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/DcAutoloader.php');
$dcAutoloader = new DcAutoloader();
$dcAutoloader->register();*/

$envDirTracking = $envDir . '/tracking';
$envTrackingFilePath = $envDirTracking . '/.env';
if (is_dir($envDirTracking) && file_exists($envTrackingFilePath) && is_file($envTrackingFilePath) && is_readable($envTrackingFilePath)) {
    $dotenvTracking = new \Dotenv\Dotenv($envDirTracking);
    $dotenvTracking->load();
}

$trackingAPIRoot = getenv('TRACKING_API_ROOT');
$trackingAPIVersionString = getenv('TRACKING_API_VERSION');
$trackingAPIIndexRelPath = getenv('TRACKING_API_INDEX_FILE_PATH');
$trackingAPIIndexFilePath = $baseDir . DIRECTORY_SEPARATOR . $trackingAPIIndexRelPath;
$trackingAPIRequestURLPattern = '#\/' . $trackingAPIRoot . '\/' . $trackingAPIVersionString . '\/[^\/]+#i';


require_once((local_environment()) ? $dcDir . "/dc.config.php" : $dcDir . "/dc-server.config.php");
require_once(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "module/dcshop/common/category_functions.inc.php");

checkForActiveRedirects($shopPdo,$request);

$_POST = $request->getParsedBody();
$_GET = $request->getQueryParams();
$_COOKIE = $request->getCookieParams();
$_SERVER = $request->getServerParams();
$_FILES = $request->getUploadedFiles();
$_REQUEST = array_merge($_ENV, $_GET, $_POST, $_COOKIE, $_SESSION ?? []);

extract_request_uri_variables();

include($rootDir . '/dc/common/classes/GeneralErrorExceptionHandling.php');
$redirectMsg = 'We\'re sorry - an internal error occurred. The error has been logged and support notified. We expect the problem to be solved shorty.';

$redirectToPath = '/error.html';
if ($_GET['language'] != "de") {
    $redirectToPath = '/error_' . $_GET['language'] . '.html';
}
$hideErrorPage = (bool)getenv('HIDE_ERROR_PAGE');
if ($hideErrorPage) {
    $redirectToPath = "";
}


//\DynCom\dc\common\classes\GeneralErrorExceptionHandling::setErrorHandler($redirectToPath, $redirectMsg);
//\DynCom\dc\common\classes\GeneralErrorExceptionHandling::setExceptionHandler($redirectToPath, $redirectMsg);

$displayErrors = (bool)(getenv('DISPLAY_ERRORS') ?? true);
$displayErrorsString = $displayErrors ? 'On' : 'Off';
ini_set('display_errors', $displayErrorsString);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
mb_internal_encoding('UTF-8');

check_and_redirect_tracking_api_requests($trackingAPIRequestURLPattern, $trackingAPIIndexFilePath);

dcBasicAuth();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'start.inc.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'check_ip_switch.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'check_language_switch.php';
require_once($GLOBALS["layout"]["frontend_include"]);
require_once __DIR__ . DIRECTORY_SEPARATOR . 'close.inc.php';


function extract_request_uri_variables()
{
    extract_site_request_uri_variables();
}

function extract_site_request_uri_variables()
{
    $GLOBALS['shop_setup']['show_short_url'] = (int)getenv('SHOW_SHORT_URL');
    $bindSite = get_bind_site_code_from_domain();
    $isBindSite = false;

    if ($bindSite <> '') {
        $isBindSite = true;
    }

    $site_base_pattern = '#/([^/]+)/([^/]+)/([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}([^/]+/){0,1}#';
    $site_base_pattern_matches = [];
    preg_match($site_base_pattern, $_SERVER['REQUEST_URI'], $site_base_pattern_matches);

    if (
        ((array_key_exists(1, $site_base_pattern_matches)
                && !empty($site_base_pattern_matches[1]))
            && ((array_key_exists(2, $site_base_pattern_matches)
                && !empty($site_base_pattern_matches[2]))))

        || $isBindSite
        || $_SERVER['REQUEST_URI'] === '/'
        || (preg_match("/\/\?action(.*?)/is",$_SERVER['REQUEST_URI']))

    ) {
        $isShop = false;
        $isRedactionalNavigationEntry = false;
        $firstLevel = "";
        if ($isBindSite) {
            $_GET['site'] = $bindSite;
            $_GET['language'] = filter_var($site_base_pattern_matches[1] ?? '', FILTER_SANITIZE_STRING);
            $firstLevel = filter_var($site_base_pattern_matches[2] ?? '', FILTER_SANITIZE_STRING);
            $firstLevel = rtrim($firstLevel, '/');

        } else {
            $_GET['site'] = filter_var($site_base_pattern_matches[1] ?? '', FILTER_SANITIZE_STRING);
            $_GET['language'] = filter_var($site_base_pattern_matches[2] ?? '', FILTER_SANITIZE_STRING);
            $firstLevel = filter_var($site_base_pattern_matches[3] ?? '', FILTER_SANITIZE_STRING);
            $firstLevel = rtrim($firstLevel, '/');

        }

        if ($firstLevel <> '' && $_GET['site'] <> '') {
            $isRedactionalNavigationEntry = checkCMSNavigation($firstLevel, $_GET['site'], $_GET['language']);

            if (!$isRedactionalNavigationEntry) {
                $isShop = checkShopCategory($firstLevel, $_GET['site'], $_GET['language']);
            }
        }
        // Check the site Code in the URL machtes a site code in DB
        $_GET['site'] = get_site_code_from_domain();
        // Check the language in the URL is matching the site code Active Languages if Not 404
        $getSiteLanguages = get_language_by_site_id($GLOBALS['site']['id']);

        $isLanguageFoundinURL = false;
        $noOfLangs = count($getSiteLanguages);
        if (!array_key_exists('REDIRECT_URL', $_SERVER)) {
            $_SERVER['REDIRECT_URL'] = '/';
        }
        for ($i = 0; $i < $noOfLangs; $i++) {

            if ($_SERVER['REDIRECT_URL'] == '/') {
                $GLOBALS['language'] = $getSiteLanguages[0];
                $_GET['language'] = $getSiteLanguages[0]['code'];
                $isLanguageFoundinURL = true;
                break;

            } else {
                if ($_GET['language'] == $getSiteLanguages[$i]['code'] || $_SERVER['REDIRECT_URL'] == '/' . $getSiteLanguages[$i]['code'] . '/') {
                    $GLOBALS['language'] = $getSiteLanguages[$i];
                    $_GET['language'] = $getSiteLanguages[$i]['code'];
                    $isLanguageFoundinURL = true;
                    break;
                }
            }
        }
        if (!$isLanguageFoundinURL) {
            show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);

        }

        // check if the URL contains Collections
        $collectionSearchUri = $_SERVER['REQUEST_URI'];
        $collectionSearchUri = parse_url($collectionSearchUri);
        $collectionSearchUriPath = $collectionSearchUri["path"];
        $collectionId = (int)substr(strrchr($collectionSearchUriPath, '-'), 1);

        //Try to max category level 5 (index 3 to 8)
        $noOfPathSegments = count($site_base_pattern_matches);
        if ($noOfPathSegments > 8) {
            $noOfPathSegments = 8;
        }

        // last number in the url is the collection id
        if ($collectionId > 0) {
            $noOfPathSegments -= 2;
            $_GET['collection_id'] = $collectionId;
        }
        checkUrlParmeters();

        if (isset($_GET['card']) && $_GET['card'] <> '') {
            $isShop = true;
        }
        if ($isShop) {
            $_GET['level_1'] = 'shop';
        }

        $itemSlagFound = false;

        $counter = 3;
        if ($isBindSite) {
            $counter = 2;
        }
        $levelindex = 0;
        for ($i = $counter; $i <= $noOfPathSegments; $i++) {
            $levelindex = $i - 2;
            if ($isBindSite) {
                $levelindex = $i - 1;
            }
            $matchedPattern = array_key_exists($i, $site_base_pattern_matches) ? $site_base_pattern_matches[$i] : '';
            $code = filter_var(rtrim($matchedPattern, '/'), FILTER_SANITIZE_STRING);

            if (!empty($code)) {
                if ($isShop) {
                    if (!$itemSlagFound) {
                        $_GET['slevel_' . $levelindex] = $code;
                        $isRealShopCategory = checkShopCategory($code, $_GET['site'], $_GET['language']);
                        if (!$isRealShopCategory) {
                            show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
                        }
                    }
                } else {
                    $_GET['level_' . $levelindex] = $code;
                    $isRealNatigation = checkCMSNavigation($code, $_GET['site'], $_GET['language']);
                    $isNavigationForwardToShop = checkCMSNavigationForward($code, $_GET['site'], $_GET['language']);

                    if (!$isRealNatigation && !$isNavigationForwardToShop) {
                        show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
                    }
                }
            }

        }

    }


    if ($_GET['site'] == '' || $_GET['language'] == '') {
        show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
    }

    $urlPath = parse_url($_SERVER['REQUEST_URI']);
    $urlPath = $urlPath["path"];

    // Check All levels have the right Parent
    if ($levelindex > 1 && !isset($_GET['card']) && !isset($_GET['collection_id'])) {
        $checkLevel = $levelindex - 1;

        $currentShop = get_shop($GLOBALS['language']['company'], $GLOBALS['language']['shop_code']);
        $categoryShopCode = $GLOBALS['language']['shop_code'];
        if ($currentShop['use_categorys_from_shop_code'] <> '') {
            $categoryShopCode = $currentShop['use_categorys_from_shop_code'];
        }

        if ($isShop) {

            $lastShopCategoryCode = $_GET['slevel_' . $checkLevel];
            $category = get_category_by_code($GLOBALS['language']['company'], $categoryShopCode, $GLOBALS['language']['shop_language_code'], $lastShopCategoryCode);
            $categoryPath = get_simple_category_path($category, $GLOBALS['language']['company'], $categoryShopCode, $GLOBALS['language']['shop_language_code']);

            $isValidShopCategory = true;
            // !category like basket Category and order and Favorits
            $shopCategories = ["account", "search", "order", "basket", "queue", "dc_order", "favorites", "showAddress", "showAddresses", "editAddress", "createAddress", 'showEditAddress', "newAddress",
                "rma", "item_compare", "address_select", "shipment_payment_option_select", "buy", "payment", "complete_order"];
            if (in_array($_GET['slevel_' . $checkLevel], $shopCategories)) {
                $isValidShopCategory = false;
            }

            if (!$GLOBALS['shop_setup']['show_short_url'] && $isValidShopCategory) {
                if ($urlPath !== $categoryPath) {
                    show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
                }
            }


        } else {
            $lastNavigationCode = $_GET['level_' . $checkLevel];

            $navigation = navigation_getbycode($lastNavigationCode);

            $getNavigationPath = current_site_navigation_path("", "", $navigation);

            if ($urlPath <> $getNavigationPath) {

                // test if its a shop category
                // May be the shop category and the cms navigation has the code
                // this case would happen in the shop category without parent category in the URL b2c/de/basic/ -- b2c/de/info/basic/
                $shopCategoryPath = "";
                $category = get_category_by_code($GLOBALS['language']['company'], $categoryShopCode, $GLOBALS['language']['shop_language_code'], $lastNavigationCode);
                if (isset($category) && $category['id'] <> '') {
                    $shopCategoryPath = $categoryPath = get_simple_category_path($category, $GLOBALS['language']['company'], $categoryShopCode, $GLOBALS['language']['shop_language_code']);
                }

                if ($urlPath <> $shopCategoryPath) {
                    show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
                }

                if ($shopCategoryPath <> '') {
                    $_GET['slevel_' . $checkLevel] = $lastNavigationCode;
                    $_GET['level_1'] = 'shop';

                }

            }
        }

    } elseif (isset($_GET['collection_id']) && $_GET['collection_id'] <> '') {

        // check if this collection exists and with the right URL
        $prepStatement = " SELECT description
                             FROM main_collection 
                              where main_collection.description != '' AND main_collection.id = :collection_id  ";


        $dbHost = getenv('MAIN_MYSQL_DB_HOST');
        $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
        $dbUser = getenv('MAIN_MYSQL_DB_USER');
        $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
        $dbPass = getenv('MAIN_MYSQL_DB_PASS');
        $description = "";
        try {
            $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
            $params = [
                [':collection_id', $_GET['collection_id'], PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $resArr = $pdo->getResultArray();
            if (array_key_exists(0, $resArr) && array_key_exists('description', $resArr[0])) {
                $description = $resArr[0]['description'];

            }
        } catch (Exception $e) {
            //Do noting
        }

        $path = get_collection_rewrite($description, filter_var($_GET['collection_id'], FILTER_SANITIZE_NUMBER_INT));

        $checkLevel = $levelindex;
        $lastNavigationCode = $_GET['level_' . $checkLevel];
        $navigation = navigation_getbycode($lastNavigationCode);
        $getNavigationPath = current_site_navigation_path("", "", $navigation);
        $path = $getNavigationPath . $path;

        if ($urlPath <> $path) {
            show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
        }

    }
    // if there is no navigation level 1 or shop level 1 so the URL should have the site code and the language code.
    if(!isset($_GET['level_1']) && !isset($_GET['slevel_1']))
    {
        if($urlPath <> "/".customizeUrl()."/" && $urlPath <> '/')
        {
            show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
        }
    }


}

function get_site_code_from_domain()
{
    $prepStatement = 'SELECT * FROM main_site WHERE site_url = :domain ORDER BY id';

    $siteDomain = $_SERVER['SERVER_NAME'];
    $siteCode = '';
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':domain', $siteDomain, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArray = $pdo->getResultArray();
        $mainSite = array();
        $uniqueSite = array();

        $siteCodeFound = false;

        if (count($resArray) > 0) {
            foreach ($resArray as $site) {
                if ($site["is_unique_site"] == 1) {
                    $uniqueSite = $site;
                    break;
                } else {
                    if ($site["is_standard_site"] == 1) {
                        $mainSite = $site;
                    }
                    if ($site['code'] == $_GET['site']) {
                        $siteCodeFound = true;
                        $GLOBALS['site'] = $site;
                    }
                }
            }
        }
        if (count($uniqueSite) > 0) {
            if ($_GET['site'] <> '' && ($_GET['site'] <> $uniqueSite['code'])) {
                show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
            }
            $GLOBALS['site'] = $uniqueSite;
            return $uniqueSite['code'];
        }

        if ($siteCodeFound) {
            return $_GET['site'];
        }

        if (count($mainSite) > 0) {
            $GLOBALS['site'] = $mainSite;
            return $mainSite['code'];
        }

    } catch (Exception $e) {
        //Do noting
    }

    // only domian name
    if (count($resArray) > 0 && $_SERVER['REQUEST_URI'] == "/") {
        $GLOBALS['site'] = $resArray[0];
        return $resArray[0]['code'];
    }
    show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
}

function check_and_redirect_tracking_api_requests($regexPattern, $tracking_index_path)
{
    $matches = [];
    $reqURI = $_SERVER['REQUEST_URI'];
    preg_match($regexPattern, $reqURI, $matches);
    if (array_key_exists(0, $matches) && file_exists($tracking_index_path) && is_file($tracking_index_path) && is_readable($tracking_index_path)) {
        include $tracking_index_path;
        exit(0);
    }
}

/* country Codes list
    http://dev.maxmind.com/geoip/legacy/codes/iso3166/
*/
function get_site_from_domain_countryCode($countryCode)
{
    $prepStatement = "
      SELECT 
            *
      FROM
           main_site
      WHERE
          site_url = :domain 
            AND
              use_ip_detection = 1
            AND
              default_country_codes LIKE   CONCAT('%',:countryCode,'%')
      ORDER BY id LIMIT 1";

    $siteDomain = $_SERVER['SERVER_NAME'];
    $siteCode = '';
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':domain', $siteDomain, PDO::PARAM_STR],
            [':countryCode', $countryCode, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            $site = $resArr[0];
        }
    } catch (Exception $e) {
        //Do noting
    }
    return $site;
}

function get_language_by_site_id($siteId)
{

    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);

    $prepStatement = '
              SELECT 
              *
              FROM 
                main_language  
              WHERE 
                    main_site_id = :mainSiteId 
                  AND 
                    active = 1
               ORDER BY id   
            ';

    $params = [
        [':mainSiteId', $siteId, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $siteLanguagesArray = $pdo->getResultArray();

    return $siteLanguagesArray;

}


function get_bind_site_code_from_domain()
{
    $prepStatement = 'SELECT * FROM main_site WHERE site_url = :domain and is_unique_site = 1 ORDER BY id';

    $siteDomain = $_SERVER['SERVER_NAME'];
    $siteCode = '';
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':domain', $siteDomain, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArray = $pdo->getResultArray();

        if (array_key_exists(0, $resArray) && array_key_exists('id', $resArray[0]) && $resArray[0]['id'] > 0) {
            $siteCode = $resArray[0]['code'];
            $GLOBALS['site'] = $resArray[0];
        }
    } catch (Exception $e) {
        //Do noting
    }
    return $siteCode;
}

function checkCMSNavigationForward($navigationCode, $siteCode, $languageCode)
{
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);

    $prepStatement = "
      SELECT 
            main_navigation.id,
            main_navigation.forward_type
      FROM
          main_navigation 
            JOIN 
             main_language
               ON main_navigation.main_language_id = main_language.id  and main_language.code = :langaugeCode
            JOIN
            main_site
               ON main_navigation.main_site_id = main_site.id  and main_site.code = :siteCode and main_site.site_url = :siteUrl
            WHERE    main_navigation.code= :navCode
               ";

    try {

        $params = [
            [':navCode', $navigationCode, PDO::PARAM_STR],
            [':siteCode', $siteCode, PDO::PARAM_STR],
            [':siteUrl', $_SERVER['SERVER_NAME'], PDO::PARAM_STR],
            [':langaugeCode', $languageCode, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0 && $resArr[0]["forward_type"] == 6) {
            return true;
        }
    } catch (Exception $e) {
        //Do noting
    }
    return false;
}

function checkCMSNavigation($navigationCode, $siteCode, $languageCode)
{
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);

    $prepStatement = "
      SELECT 
            main_navigation.id
      FROM
          main_navigation 
            JOIN 
             main_language
               ON main_navigation.main_language_id = main_language.id  and main_language.code = :langaugeCode
            JOIN
            main_site
               ON main_navigation.main_site_id = main_site.id  and main_site.code = :siteCode and main_site.site_url = :siteUrl
            WHERE    main_navigation.code= :navCode
            AND forward_type != 6
               ";

    try {

        $params = [
            [':navCode', $navigationCode, PDO::PARAM_STR],
            [':siteCode', $siteCode, PDO::PARAM_STR],
            [':siteUrl', $_SERVER['SERVER_NAME'], PDO::PARAM_STR],
            [':langaugeCode', $languageCode, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            return true;
        }
    } catch (Exception $e) {
        //Do noting
    }
    return false;
}


function checkShopCategory($categoryCode, $siteCode, $languageCode)
{
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);

    $shopCategories = ["account", "search", "order", "basket", "queue", "dc_order", "favorites", 'showAddresses', "showAddress", 'editAddress', 'showAddress', 'createAddress', 'showEditAddress', "newAddress",
        "rma", "item_compare", "address_select", "shipment_payment_option_select", "buy", "payment", "complete_order"];
    if (in_array($categoryCode, $shopCategories)) {
        return true;
    }

    $product = [];
    $str = "/-p(\d+)\//";
    preg_match($str, $_SERVER['REQUEST_URI'], $product);
    if (isset($product[1]) && $product[1] <> '') {
        $checkItem = checkShopItem($product[1], $categoryCode);
        return $checkItem;
    }

    $prepStatement = "
      SELECT 
            shop_category.id
      FROM
          shop_category 
            JOIN 
             main_language
               ON shop_category.company = main_language.company and shop_category.shop_code = main_language.shop_code and shop_category.code = :categoryCode
            JOIN
            main_site
               ON main_language.id = main_site.std_main_language_id and main_site.code = :siteCode and main_site.site_url = :siteUrl
";

    try {

        $params = [
            [':categoryCode', $categoryCode, PDO::PARAM_STR],
            [':siteCode', $siteCode, PDO::PARAM_STR],
            [':siteUrl', $_SERVER['SERVER_NAME'], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            return true;
        }
    } catch (Exception $e) {
        //Do noting
    }

    $prepStatement = "
      SELECT 
         distinct   shop_category.id
      FROM
          shop_category 
            JOIN 
             shop_shop
               ON shop_category.company = shop_category.company and shop_category.shop_code = shop_shop.code and shop_category.language_code = shop_shop.default_language_code and shop_category.code = :categoryCode
		 JOIN
			 main_language
				 ON main_language.company = shop_shop.company and main_language.shop_code = shop_shop.code and main_language.shop_language_code = shop_shop.default_language_code 
			 JOIN
			 main_site
               ON main_language.id = main_site.std_main_language_id and main_site.site_url = :siteUrl
                 where shop_shop.code = (
										
												select shop_shop.use_categorys_from_shop_code from
                                                shop_shop
                                                
												JOIN
												 main_language
													 ON main_language.company = shop_shop.company and main_language.shop_code = shop_shop.code and main_language.shop_language_code = shop_shop.default_language_code
												JOIN
												 main_site
												   ON main_language.id = main_site.std_main_language_id and main_site.code = :siteCode and main_site.site_url = :siteUrl 
                                                   
                                                   where shop_shop.use_categorys_from_shop_code <> ''
                                          
									)
";

    try {

        $params = [
            [':categoryCode', $categoryCode, PDO::PARAM_STR],
            [':siteCode', $siteCode, PDO::PARAM_STR],
            [':siteUrl', $_SERVER['SERVER_NAME'], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            return true;
        }
    } catch (Exception $e) {
        //Do noting
    }

    return false;

}

function checkUrlParmeters()
{
    $product = [];
    $str = "/-p(\d+)\//";
    preg_match($str, $_SERVER['REQUEST_URI'], $product);
    if (isset($product[1]) && $product[1] <> '') {
        $_GET['card'] = $product[1];
    }

    if (strpos($_SERVER['REQUEST_URI'], '/account/') !== false) {
        $_GET['shop_category'] = "account";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/basket/') !== false) {
        $_GET['shop_category'] = "basket";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/order/') !== false) {
        $_GET['shop_category'] = "order";

        $setStepAction = false;
        if (!array_key_exists('action', $_GET) || ($_GET['action'] !== "coupon" && $_GET['action'] !== "delete_coupon")) {
            $setStepAction = true;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/address_select/') !== false) {
            if ($setStepAction) {
                $_GET['action'] = "step1";
            }

        } elseif (strpos($_SERVER['REQUEST_URI'], '/shipment_payment_option_select/') !== false) {

            if ($setStepAction) {
                $_GET['action'] = "step2";
            }

        } elseif (strpos($_SERVER['REQUEST_URI'], '/buy/') !== false) {
            $_GET['action'] = "step3";
        } elseif (strpos($_SERVER['REQUEST_URI'], '/payment/') !== false) {
            $_GET['action'] = "payment";
        } elseif (strpos($_SERVER['REQUEST_URI'], '/complete_order/') !== false) {
            $_GET['action'] = "complete_order";
        } else {
            $_GET['action'] = "step1";
        }
    }
    if (strpos($_SERVER['REQUEST_URI'], '/queue/') !== false) {
        $_GET['shop_category'] = "queue";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/favorites/') !== false) {
        $_GET['shop_category'] = "favorites";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/search/') !== false) {
        $_GET['shop_category'] = "search";
        if (!empty($_GET['collection_id'])) {
            unset($_GET['collection_id']);
        }
    }
    if (strpos($_SERVER['REQUEST_URI'], '/rma/') !== false) {
        $_GET['shop_category'] = "rma";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/dc_order/') !== false) {
        $_GET['shop_category'] = "dc_order";
    }
    if (strpos($_SERVER['REQUEST_URI'], '/item_compare/') !== false) {
        $_GET['shop_category'] = "item_compare";
    }

}


function get_site_from_site_id($siteId)
{
    $prepStatement = "
      SELECT 
            *
      FROM
           main_site
      WHERE
          id = :id 
      ORDER BY id LIMIT 1";

    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':id', $siteId, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            $site = $resArr[0];
        }
    } catch (Exception $e) {
        //Do noting
    }
    return $site;
}

function checkShopItem($itemId, $itemSlug)
{
    $prepStatement = "
      SELECT 
            *
      FROM
           shop_item
      WHERE
          id = :itemId  and active = 1
           ";

    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':itemId', $itemId, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();
        if (array_key_exists(0, $resArr) && array_key_exists('id', $resArr[0]) && $resArr[0]['id'] > 0) {
            if ($itemSlug <> $resArr[0]['item_slug'] . "-p" . $itemId) {
                show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
            }
        } else {
            show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
        }
    } catch (Exception $e) {
        //Do noting
    }

    // Check if the item is in the correct language and shop
    if(isset($GLOBALS['language']))
    {
       $shopData =  get_shop($GLOBALS['language']['company'], $GLOBALS['language']['shop_code']);
       $itemShopCode = $shopData['code'];
       if($shopData['use_items_from_shop_code'] <> '')
       {
           $itemShopCode = $shopData['use_items_from_shop_code'];
       }
       if($resArr[0]['company'] <> $GLOBALS['language']['company']
           || $resArr[0]['shop_code'] <> $itemShopCode
           || $resArr[0]['language_code'] <> $GLOBALS['language']['shop_language_code'] )
       {
           show_error_404($GLOBALS['GLOBAL_RESPONSE'],$GLOBALS['PRODEPLOY_ROUTING']);
       }
    }

    return true;

}

/**
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param bool $prodeployRouting
 */
function show_error_404(\Psr\Http\Message\ResponseInterface $response, bool $prodeployRouting)
{
    if ($prodeployRouting) {
        $response = $response->withStatus(404,'Not Found');
    } else {
        headerFunctionBridge('HTTP/1.0 404 Not Found');
    }
    include (dirname(__DIR__,2) . '/404.html');
    exit();
}