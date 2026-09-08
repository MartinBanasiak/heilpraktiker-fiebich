<?php
define('CMS_PATH', dirname(__FILE__) . '/../');
include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/common_functions.inc.php';
require_once (local_environment()) ? dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dc.config.php' : dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dc-server.config.php';
include_once __DIR__ . 'admin_functions.inc.php';

// set translation
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/classes/Translate.php';


db_connect();

$site      = $_POST["site"];
$lang      = $_POST["language"];
$site_code = $_POST["site_code"];


$sitearray = site_getbycode($_POST["site"]);
$siteid    = $sitearray["id"];

$sitearray = site_getbycode($site_code);
$site_id   = $sitearray["id"];


function get_languages( $site ) {
    $translation = new \DynCom\dc\common\classes\Translate();

    $query  = "SELECT id, name FROM main_language WHERE main_site_id = '" . $site . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $langs  = "<option value=\"\"> " . $translation->get("please_choose") . " </option>";
    WHILE ($row = @mysqli_fetch_assoc($result)) {
        $langs .= "<option value=\"" . $row["id"] . "\">" . $row["name"] . "</option>";
    }
    return $langs;
}

IF (isset($siteid) && empty($lang)) {
    $lang = get_languages($siteid);
    echo $lang;
} ELSEIF (isset($site_code) && isset($lang)) {
    $nav = navigation_select_noecho('', $site_id, $lang);
    echo $nav;
}
?>