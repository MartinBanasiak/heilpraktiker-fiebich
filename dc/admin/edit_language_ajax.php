<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/common_functions.inc.php';
require_once (local_environment()) ? dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dc.config.php' : dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dc-server.config.php';
/*
$GLOBALS['mysql_con'] = mysqli_connect($GLOBALS["myservername"],$GLOBALS["mylogin"],$GLOBALS["mypass"]) or die ("Keine Verbindung hergestellt!");
mysqli_select_db($GLOBALS['mysql_con'],dc) or die ("Keine Verbindung hergestellt!");
@mysqli_query($GLOBALS['mysql_con'],'set character set utf8;');
*/
db_connect();
$company = $_POST["company"];
$shop    = $_POST["shop"];

function get_shops( $company ) {
    $query  = "SELECT code, description FROM shop_shop WHERE company = '" . $company . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $shops  = "<option value=\"\"></option>";
    WHILE ($row = @mysqli_fetch_assoc($result)) {
        $shops .= "<option value=\"" . $row["code"] . "\">" . $row["description"] . "</option>";
    }
    return $shops;
}

function get_languages( $shop ) {
    $query     = "SELECT code, description FROM shop_language WHERE shop_code = '" . $shop . "'";
    $result    = @mysqli_query($GLOBALS['mysql_con'], $query);
    $languages = "<option value=\"\"></option>";
    WHILE ($row = @mysqli_fetch_assoc($result)) {
        $languages .= "<option value=\"" . $row["code"] . "\">" . $row["description"] . "</option>";
    }
    return $languages;
}

IF (isset($company)) {
    $shops = get_shops($company);
    echo $shops;
} ELSEIF (isset($shop)) {
    $langs = get_languages($shop);
    echo $langs;
}
?>