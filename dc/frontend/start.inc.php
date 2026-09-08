<?php
$dir = __DIR__;
$rootDir = dirname(dirname($dir));
$dcDir = $rootDir . '/dc';
$moduleDir = $rootDir . '/module';
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// Verbindung mit Datenbank herstellen
db_connect();
delete_old_visitors();

require_once($dcDir . '/admin/admin_functions.inc.php');
$protocol = ((!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$GLOBALS['url_protocol'] = $protocol;

//Konstanten
define('COOKIE_DAYS_VALID', 30);
define('CMS_PATH', $dcDir . '/');
define('MODULE_PATH', $moduleDir . '/');
define('ROOT_PATH', $rootDir . '/');

// set autoloader
function defaultAutoloader($class_name)
{
    $filePath = sprintf(ROOT_PATH . "/dc/common/classes/%s.php", $class_name);
    if (file_exists($filePath)) {
        require_once($filePath);
    }
}

//spl_autoload_register('defaultAutoloader');

\DynCom\dc\common\classes\Registry::set('translation', new \DynCom\dc\common\classes\Translate("de"));
//Umrechnungs-Globals
$GLOBALS["cm_per_in"] = 0.393700787;
$GLOBALS["mm_per_in"] = 3.93700787;
$GLOBALS["in_per_cm"] = 2.545;
$GLOBALS["in_per_mm"] = 25.45;

// Variablen schützen
$_GET = secure_array($_GET, TRUE);
$_POST = secure_array($_POST, TRUE);

// Setup auslesen
$setup = setup_get();
$GLOBALS["setup"] = $setup;
// Aktuelle Webseite auslesen
$site = (isset($_GET["site"]) && $_GET["site"] <> '') ? site_getbycode($_GET["site"]) : site_getbyid($setup["std_main_site_id"]);
$GLOBALS["site"] = $site;
//echo "SITE IS: ";
set_sitecode_get();
$GLOBALS["insert_live_edit_js"] = FALSE;
$GLOBALS["live_edit_mode"] = FALSE;


// Session-ID generieren
if ($GLOBALS["site"]["use_session_id"]) {

    ini_set("session.use_cookies", 1);
    ini_set("session.use_only_cookies", 1);
    ini_set("session.use_trans_sid", 1);
    session_name("sid" . $site['code']);

    $visitor_set = 0;

    if (!empty($_COOKIE['sid' . $GLOBALS['site']['code']])) {
        $cookie_preset = TRUE;
    } else {
        $cookie_preset = FALSE;
    }
    if (!(strlen(session_id()) > 0)) {
        if (!empty($_GET['sid']) && check_sid_for_login($_GET['sid'], TRUE)) {
            session_id($_GET['sid']);
            session_start();
            $sessID = session_id();
            $sessStatus = session_status();
            $sessActive = $sessStatus === PHP_SESSION_ACTIVE;
        } elseif ($cookie_preset) {
            session_id($_COOKIE['sid' . $GLOBALS['site']['code']]);
            session_start();
            $sessID = session_id();
            $sessStatus = session_status();
            $sessActive = $sessStatus === PHP_SESSION_ACTIVE;
        } else {
            session_start();
            $sessID = session_id();
            $sessStatus = session_status();
            $sessActive = $sessStatus === PHP_SESSION_ACTIVE;
        }
    }

    if (isset($_SESSION['sidcms']) && $_SESSION['sidcms'] == $_SESSION['sid' . $GLOBALS['site']['code']]) {
        if (session_regenerate_id()) {
            $_SESSION['sid' . $GLOBALS['site']['code']] = session_id();
        }
    }

    if (!empty($_GET['live_edit']) && $_GET['live_edit'] == 1) {
        $GLOBALS["insert_live_edit_js"] = TRUE;
    } elseif (!empty($_GET['sid']) && check_sid_for_login($_GET['sid'], TRUE)) {
        $session_start_sid = $_GET['sid'];
        $startby = 'navlogin';
        $visitor = get_visitor_small($session_start_sid);
    } elseif (!empty($_SESSION['sid' . $GLOBALS['site']['code']])) {
        $visitor = get_visitor_small($_SESSION['sid' . $GLOBALS['site']['code']], 1, 0);
        $visitor_set = 1;
    }
    if (!empty($_COOKIE['sid' . $GLOBALS['site']['code']]) && !(!empty($_GET['sid']) && check_sid_for_login($_GET['sid'], TRUE))) {
        //cookie login
        $cookie_sid = $_COOKIE['sid' . $GLOBALS['site']['code']];
        $visitor = get_visitor_small($cookie_sid);
        $_SESSION['sid' . $GLOBALS['site']['code']] = $cookie_sid;

        $remember_token_orig = trim((string)$visitor['remember_token']);
        $cookie_index = $GLOBALS['site']['code'] . '_remember_login';
        $remember_token_compare = trim((string)$_COOKIE[$cookie_index]);
        if (
            ($GLOBALS['visitor']['frontend_login'] && !(bool)$_SESSION['pass_auth'])
            &&
            (
                (
                    $GLOBALS['visitor']['cookie_only']
                    &&
                    (
                        empty($remember_token_orig)
                        ||
                        empty($remember_token_compare)
                    )
                )
                ||
                (
                    !empty($remember_token_orig)
                    &&
                    (
                        empty($remember_token_compare)
                        ||
                        !($remember_token_compare == $remember_token_orig)
                    )
                )
            )
        ) {
            $visitor['frontend_login'] = 0;
            $GLOBALS['visitor']['frontend_login'] = 0;
            $query = 'UPDATE main_visitor SET frontend_login = 0 WHERE id=' . $GLOBALS['visitor']['id'];
            mysqli_query($GLOBALS['mysql_con'], $query);
            logQuery(date('c'), $query, __FILE__, __LINE__, $GLOBALS['querylog'], FALSE, FALSE, __FUNCTION__, TRUE, TRUE);
        }
    }

    //Get session date
    $session_time = strtotime($GLOBALS['visitor']['session_date']);
    $time_diff = time() - $session_time;
    //90 Minutes
    $diff_limit = 90 * 60;

    if (
        $visitor['frontend_login']
        &&
        (!$_SESSION['pass_auth'])
        &&
        (!empty($remember_token_compare))
        &&
        $remember_token_compare == $remember_token_orig
        &&
        $cookie_preset
        //&&
        //($time_diff > $diff_limit)
    ) {
        cookie_login_postprocessing($visitor, $_COOKIE);
    }
    $_SESSION['pass_auth'] = FALSE;
    $GLOBALS['visitor']['pass_auth'] = FALSE;
    $GLOBALS['visitor_set'] = 1;


} elseif (isset($_GET['live_edit']) && $_GET['live_edit'] == 1) {
    ini_set("session.use_cookies", 1);
    ini_set("session.use_only_cookies", 1);
    ini_set("session.use_trans_sid", 1);
    $GLOBALS["insert_live_edit_js"] = TRUE;
    session_name("sid" . $site['code']);
    session_start();
    if (isset($_SESSION['sidcms']) && $_SESSION['sidcms'] == $_SESSION['sid' . $GLOBALS['site']['code']]) {
        if (session_regenerate_id()) {
            $_SESSION['sid' . $GLOBALS['site']['code']] = session_id();
        }
    }
} else {
    session_start();
}


$trackingFuncPath = $moduleDir . '/tracking/tracking_functions.php';
include_once $moduleDir . '/tracking/tracking_functions.php';
//set tracking auth_token, extract referrer info from session and store to globals

$referrer_data = \DynCom\dc\tracking\tracking_get_referrer_info();
$GLOBALS['referrer_data'] = $referrer_data;


if (!$GLOBALS["site"]["use_session_id"]) {

}

// Aktuellen Besucher auslesen und ggf. Währung speichern
if ($visitor_set !== 1 || !((int)$GLOBALS['visitor']['id'] > 0)) {
    $visitor = get_visitor();
}

//[...]
$shop_pdo = get_main_db_pdo_from_env_single_instance();
$company = $language['company'] ?? '';
if (!empty($_GET['currency']) && isset($visitor) && is_array($visitor) && $company !== '') {
    $request_currency = filter_var($_GET['currency'],FILTER_SANITIZE_STRING);
    if (is_valid_currency_for_company($shop_pdo,$request_currency,$company)) {
        update_visitor_currency_in_db($shop_pdo,(int)$visitor['id'],$_GET['currency']);
        $visitor['currency_code'] = $request_currency;
    } else {
        //Handle invalid parameter
    }
}




renew_session_dates($visitor);
$visitor = get_visitor_small(session_id());
$GLOBALS["visitor"] = $visitor;
   if(  (bool)$GLOBALS['site']['is_unique_site']
       &&  $GLOBALS['site']['id'] != $GLOBALS['language']['logout_site_id']
       &&  $GLOBALS['language']['logout_site_id'] > 0
       &&  !$GLOBALS['visitor']['frontend_login']
       && $_GET['action'] <> "shop_login"

   )
   {
       $GLOBALS['site'] = get_site_from_site_id($GLOBALS['language']['logout_site_id']);
       $_GET['site'] = $GLOBALS['site']['code'];

       $redirectSiteLanguages = get_language_by_site_id($GLOBALS['site']['id']);

       $noOfLangs = count($redirectSiteLanguages);
       for ($i = 0; $i < $noOfLangs; $i++) {
           if ($GLOBALS['language']['logout_language_id'] == $getSiteLanguages[$i]['id'] ) {
               $GLOBALS['language'] = $redirectSiteLanguages[$i];
               $_GET['language'] = $redirectSiteLanguages[$i]['code'];
               break;
           }
           $GLOBALS['language'] = $redirectSiteLanguages[0];
           $_GET['language'] = $redirectSiteLanguages[0]['code'];
       }

   }


if (isset($_COOKIE['sidcms'])) {
    $admin_visitor = get_visitor_small($_COOKIE['sidcms']);
    $admin_user_frontend = get_admin_user($admin_visitor);
} else {
    $admin_user_frontend = get_admin_user($visitor);
}

$GLOBALS["admin_user_frontend"] = $admin_user_frontend;

// Aktuelle Sprache und Sprachvariablen auslesen
language_change();
$lang_getfrombrowser = FALSE;
if ($lang_getfrombrowser) {
    $language = (isset($_GET["language"]) && $_GET["language"] <> '') ? language_getbycode($_GET["language"]) : language_getbycode(lang_getfrombrowser());
} else {
    $language = (isset($_GET["language"]) && $_GET["language"] <> '') ? language_getbycode($_GET["language"], $GLOBALS["site"]["id"]) : language_getbyid($site["std_main_language_id"]);
}


$GLOBALS["language"] = $language;

// Textbausteine auslesen
$text_constant = array();
$text_constant_path = rtrim(dirname(__DIR__), '/') . '/common/text_constants.inc.php';
if (is_file($text_constant_path) && is_readable($text_constant_path)) {
    require_once($text_constant_path);
}
$GLOBALS["tc"] = $text_constant[$language["code"]];

// Aktuelles Layout auslesen
$layout = layout_getbyid($language["main_layout_id"]);
$GLOBALS["layout"] = $layout;

// layout klassen auslesen
$GLOBALS["layout_classes"] = get_layout_classes($language["main_layout_id"]);

// Aktuelle Naviationsebene auslesen
$navigation = navigation_get($site, $language);
$GLOBALS["navigation"] = $navigation;

// Tabcounter steuert globale Auswahlfolge von Input-Feldern
$GLOBALS["tabcounter"] = 1;

// deaktivierten bausteine der aktuellen seite lesen
$GLOBALS["inactive_components"] = get_inactive_components($navigation['forward_page_id']);

// pruefen um was es sich fuer ein menupunkt handelt
switch ($navigation['forward_type']) {
    case 1: // seite includen
        $GLOBALS["page"] = page_getbyid($GLOBALS["navigation"]['forward_page_id']);
        // weiteres passiert erst in der get_content() funktion
        break;

    case 2: // interne weiterleitung
        forward_intern($navigation['forward_navigation_id']);
        break;

    case 3: // externe weiterleitung
        forward_extern($navigation['forward_url']);
        break;

    case 5: // interne pfad weiterleitung
        forward_url_intern($navigation['forward_url']);
        break;

    case 6: // shop category
        forward_url_shop_category($navigation['forward_shop_category']);
        break;
}

//Seiten-Meta-Tag-Inhalte in Globals schreiben
set_global_meta_tag_content_site();

//ini_set('display_errors',1);
//include(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . '/dc/DcAutoloader.php');
//$dcAutoloader = new DcAutoloader();
//$dcAutoloader->register();
$initPath = rtrim($rootDir, '/') . '/dc/init.php';
include_once($initPath);

$sessionStorageKeyPrefix = 'dc-one';
$flashMessageBag = new \DynCom\dc\common\classes\NativeSessionFlashMessageBag($sessionStorageKeyPrefix);
$GLOBALS['flashMessageBag'] = $flashMessageBag;
