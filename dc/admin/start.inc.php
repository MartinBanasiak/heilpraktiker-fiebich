<?php
// Startsequenz der Oberfläche
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// Verbindung mit Datenbank herstellen
db_connect();
define('COOKIE_DAYS_VALID', 30);
define('CMS_PATH', dirname(__FILE__) . '/../');
define('MODULE_PATH', dirname(__FILE__) . '/../../module/');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

define('COLLECTION_SITEPART_ID', 99);

/*function dc_autoloader($class) {
    require_once CMS_PATH . sprintf("common/classes/%s.php", $class);
}

spl_autoload_register('dc_autoloader');*/

// Session-ID generieren
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);
session_name("sidcms");

$visitor_set = 0;

if (!empty($_COOKIE['sidcms'])) {
    $cookie_preset = TRUE;
} else {
    $cookie_preset = FALSE;
}
if (!(strlen(session_id()) > 0)) {
    if (!empty($_GET['sidcms']) && check_sid_for_login($_GET['sidcms'])) {
        session_id($_GET['sidcms']);
        session_start();
    } elseif ($cookie_preset) {
        session_id($_COOKIE['sidcms']);
        session_start();
    } else {
        session_start();
    }
} else {
    session_start();
}

if (!empty($_GET['live_edit']) && $_GET['live_edit'] == 1) {
    $GLOBALS["insert_live_edit_js"] = TRUE;
} elseif (!empty($_SESSION['sidcms'])) {
    $visitor     = get_visitor_small($_SESSION['sidcms'], 1, 0);
    $visitor_set = 1;
}
if (!empty($_COOKIE['sidcms'])) {
    //cookie login
    $cookie_sid         = $_COOKIE['sidcms'];
    $visitor            = get_visitor_small($cookie_sid);
    $_SESSION['sidcms'] = $cookie_sid;

    $remember_token_orig    = trim((string)$visitor['remember_token']);
    $cookie_index           = 'cms_remember_login';
    $remember_token_compare = trim((string)$_COOKIE[$cookie_index]);
    if (
        ($GLOBALS['visitor']['admin_login'] && !(bool)$_SESSION['pass_auth'])
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
        $visitor['frontend_login']            = 0;
        $GLOBALS['visitor']['frontend_login'] = 0;
        $query                                = 'UPDATE main_visitor SET admin_login = 0 WHERE id=' . $GLOBALS['visitor']['id'];
        //trigger_eror("LOGOUT! PASS-AUTH: " . $_SESSION['pass_auth'] . " | COOKIE-ONLY: " . $GLOBALS['visitor']['cookie_only'] . " | TOKEN DB: " . $remember_token_orig . " | TOKEN CLIENT: " . $remember_token_compare);
        @mysqli_query($GLOBALS['mysql_con'], $query);
        //logQuery(date('c'), $query, __FILE__, __LINE__, $GLOBALS['querylog'], FALSE, FALSE, __FUNCTION__, TRUE, TRUE);
    }
}


//Get session date
$session_time = strtotime($GLOBALS['visitor']['session_date']);
$time_diff    = time() - $session_time;
//90 Minutes
$diff_limit = 90 * 60;

if (
    $visitor['admin_login']
    &&
    (!$_SESSION['pass_auth'])
    &&
    (!empty($remember_token_compare))
    &&
    $remember_token_compare == $remember_token_orig
    &&
    $cookie_preset
    &&
    ($time_diff > $diff_limit)
) {
    cookie_login_postprocessing($visitor, $_COOKIE);
}
$_SESSION['pass_auth']           = FALSE;
$GLOBALS['visitor']['pass_auth'] = FALSE;
$GLOBALS['visitor_set']          = 1;

if ($visitor_set !== 1) {
    $visitor = get_visitor();
}

// Variablen schützen
$_GET  = secure_array($_GET, TRUE);
$_POST = secure_array($_POST, FALSE);

check_admin_user_login();
renew_session_dates($visitor);
$GLOBALS["visitor"]    = $visitor;
$admin_user            = get_admin_user($visitor);
$GLOBALS["admin_user"] = $admin_user;

// load Translation class into registry
$user_language = (isset($GLOBALS["admin_user"]['main_language']) ? $GLOBALS["admin_user"]['main_language'] : 'de');
\DynCom\dc\common\classes\Registry::set('translation', new \DynCom\dc\common\classes\Translate($user_language));

// Setup, Webseite, Sprache, Layout und aktuelle Menüebene auslesen
$setup            = setup_get();
$GLOBALS["setup"] = $setup;
site_change();
$user_site_id = get_user_site_id();
$site         = ($_GET["site"] <> '') ? site_getbycode($_GET["site"]) : site_getbyid($user_site_id);

// wenn die website nicht erlaubt ist dann die erste erlaubte website auslesen.
// Passiert nur wenn der User direkt die URL der website weiss
if(!is_site_allowed($site['id'])) {
    $setup            = setup_get_allowed_site();
    $GLOBALS["setup"] = $setup;
    $site         = site_getbyid($setup['std_main_site_id']);
}

$GLOBALS["site"] = $site;
language_change();
$language                    = ($_GET["language"] <> '') ? language_getbycode($_GET["language"], $GLOBALS["site"]["id"]) : language_getbyid($site["std_main_language_id"]);
$GLOBALS["language"]         = $language;
$admin_navigation            = admin_navigation_get();
$GLOBALS["admin_navigation"] = $admin_navigation;
$GLOBALS["layout"]           = layout_getbyid($language["main_layout_id"]);

// Startseite setzen
if (!isset($_GET['level_1']) || empty($_GET['level_1'])) {
    $_GET['level_1'] = 'structure';
    if (collection_exists()) {
        $_GET['level_1'] = 'collections';
    }
}

// pruefen ob das menu oben offen ist um class zu setzen
$adminMenu = \DynCom\dc\common\classes\Adminmenu::get();

$linklistmenu = TRUE;
if (isset($_GET["level_1"]) && $_GET["level_1"] != "" && isset($adminMenu[$_GET["level_1"]]['linklistmenu'])) {
    $linklistmenu = $adminMenu[$_GET["level_1"]]['linklistmenu'];
}
if (isset($_GET["level_2"]) && $_GET["level_2"] != "" && isset($adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['linklistmenu'])) {
    $linklistmenu = $adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['linklistmenu'];
}

if ($linklistmenu === TRUE) {
    $GLOBALS["menu_opened"] = TRUE;
} else {
    $GLOBALS["menu_opened"] = FALSE;
}

update_visitor_data(array(
    'collection_options' => array(
        'collection_edit' => FALSE
    )
));

// benoetigt fuer ckfinder
if ($GLOBALS["visitor"]["admin_login"]) {
    $_SESSION['IsAuthorized'] = TRUE;
} else {
    $_SESSION['IsAuthorized'] = FALSE;
}

before_header();

// check for ajax call
if (IS_AJAX) {
    ini_set("display_errors",1);
    if (!$GLOBALS["visitor"]["admin_login"]) {
        headerFunctionBridge('REQUIRES_AUTH: 1');
        exit(); // send  redirect header ans exit
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'content_ajax.inc.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'close.inc.php';

    exit(); // exit script, no need to load the whole content
}

$GLOBALS['shop_setup']['allowed_false_login_times'] = 3;
$GLOBALS['shop_setup']['false_login_waiting_seconds'] = 60;
?>