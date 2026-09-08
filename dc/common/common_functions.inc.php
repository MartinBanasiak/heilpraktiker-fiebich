<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function dcBasicAuth() {
    if (isset($_SERVER['HTTP_HOST'])) {

        $host_names = explode(".", $_SERVER['HTTP_HOST']);
        $bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

        if ($bottom_host_name === getenv('BASIC_AUTH_DOMAIN')) {
            $authUser = array(
                getenv('BASIC_AUTH_USER_INTERNAL') => getenv('BASIC_AUTH_USER_PASSWORD_INTERNAL'),
                getenv('BASIC_AUTH_USER_EXTERNAL') => getenv('BASIC_AUTH_USER_PASSWORD_EXTERNAL')
            );
            $realm = "Restricted area";
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                showBasicAuth($realm);
            }
            if (!isset($authUser[$_SERVER['PHP_AUTH_USER']]) || !isset($_SERVER['PHP_AUTH_PW'])) {
                showBasicAuth($realm);
            }
            if ($authUser[$_SERVER['PHP_AUTH_USER']] !== $_SERVER['PHP_AUTH_PW']) {
                showBasicAuth($realm);
            }
        }
    }
}

/**
 * @param $realm
 */
function showBasicAuth($realm): void
{
    headerFunctionBridge('WWW-Authenticate: Basic realm="' . $realm . '"');
    headerFunctionBridge('HTTP/1.0 401 Unauthorized');
    die();
}

//mysqli_result analog zu mysql_result
function mysqli_result($res, $row = 0, $col = 0)
{
    if ($row >= 0 && @mysqli_num_rows($res) > $row) {
        @mysqli_data_seek($res, $row);
        $resrow = @mysqli_fetch_row($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return FALSE;
}

// Verbindung mir der Datenbank herstellen
function db_connect()
{
    $servername = $GLOBALS['myservername'];
    $login = $GLOBALS['mylogin'];
    $pass = $GLOBALS['mypass'];
    $db = $GLOBALS['mydb'];

    $GLOBALS['mysql_con'] = mysqli_connect($servername, $login, $pass) or die (mysqli_error($GLOBALS['mysql_con']) . " - Keine Verbindung hergestellt! [$servername, $login, $pass]");
    @mysqli_select_db($GLOBALS['mysql_con'], $db) or die (mysqli_error($GLOBALS['mysql_con']) . " - Keine Verbindung hergestellt [$servername, $login, $pass]!");
    @mysqli_query($GLOBALS['mysql_con'], "set character set 'utf8mb4';");
    @mysqli_query($GLOBALS['mysql_con'], "set names 'utf8mb4';");
}

// Globales führen der Reihenfolge in der Felder angewählt werden
function tabcounter()
{
    $counter = $GLOBALS["tabcounter"];
    $GLOBALS["tabcounter"]++;
    return $counter;
}

// Funktion zum erstellen eines internen Links
function ml($sitepart, $key1, $value1, $key2 = NULL, $value2 = NULL, $key3 = NULL, $value3 = NULL, $key4 = NULL, $value4 = NULL)
{
    $newget = $_GET;
    unset($newget["payment_error"], $newget["login_error"], $newget["sid"], $newget["site"], $newget["language"], $newget["level_1"], $newget["level_2"], $newget["level_3"], $newget["level_4"], $newget["level_5"], $newget["action"], $newget["action_id"], $newget["slevel_1"], $newget["slevel_2"], $newget["slevel_3"], $newget["slevel_4"], $newget["slevel_5"]);

    foreach ($newget as $key => $val) {
        if (substr($key, 0, 6) === 'action' && $val === 'send') {
            unset($newget[$key]);
        }
    }

    if ($key1 <> "") {
        $newget[$key1 . $sitepart["navigation_has_sitepart_id"]] = $value1;
    }
    if ($key2 <> "") {
        $newget[$key2 . $sitepart["navigation_has_sitepart_id"]] = $value2;
    }
    if ($key3 <> "") {
        $newget[$key3 . $sitepart["navigation_has_sitepart_id"]] = $value3;
    }
    if ($key4 <> "") {
        $newget[$key4 . $sitepart["navigation_has_sitepart_id"]] = $value4;
    }

    return ("?" . http_build_query($newget));
}

function ml2($data)
{
    $newget = $_GET;
    unset($newget["sid"], $newget["site"], $newget["language"], $newget["level_1"], $newget["level_2"], $newget["level_3"], $newget["level_4"], $newget["level_5"], $newget["action"], $newget["action_id"], $newget["slevel_1"], $newget["slevel_2"], $newget["slevel_3"], $newget["slevel_4"], $newget["slevel_5"]);
    $newget = array_merge($newget, $data);
    return ("?" . http_build_query($newget));
}

// Array vor SQL-Injection schützen
function secure_array($bad_array, $check_strip_tags = FALSE)
{

    //$keywords = ['INSERT','SELECT','DROP','SHOW','TRUNCATE','CONCAT','information_schema','substring(','version(','ascii','database(','load_file(','OUTFILE','BENCHMARK','unhex'];
    $keywords = [''];

    $secure_array = array();
    foreach ($bad_array as $key => $value) {
        if (is_array($bad_array[$key])) {
            $secure_array[$key] = secure_array($bad_array[$key], $check_strip_tags);
        } else {
            $hex2stringValue = hexToStr($value);
            foreach ($keywords as $keyword) {
                if ((stripos($value, $keyword) !== false) || (stripos($hex2stringValue, $keyword) !== false)) {
                    die();
                }
            }
            $secure_array[$key] = secure_var($value, $check_strip_tags);
        }
    }

    return $secure_array;
}

// Array vor SQL-Injection schützen für navconnect
function secure_array_nav( $bad_array, $check_strip_tags = FALSE ) {

    $keywords = ['INSERT','SELECT','SHOW','TRUNCATE','CONCAT','information_schema','substring(','version(','ascii(','database(','load_file(','OUTFILE','BENCHMARK','unhex'];

    $secure_array = array();
    foreach ($bad_array as $key => $value) {
        if (is_array($bad_array[$key])) {
            $secure_array[$key] = secure_array($bad_array[$key], $check_strip_tags);
        } else {
            $hex2stringValue = hexToStr($value);
            foreach($keywords as $keyword) {
                if ((stripos($value,$keyword) !== false) || (stripos($hex2stringValue,$keyword) !== false)) {
                    die();
                }
            }
            $secure_array[$key] = secure_var($value, $check_strip_tags);
        }
    }
    return $secure_array;
}

function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

// Variable vor SQL-Injection schützen
function secure_var( $bad_var, $check_strip_tags = FALSE ) {
    if (get_magic_quotes_gpc()) {
        $bad_var = stripslashes($bad_var);
    }
    if ($check_strip_tags) {
        $bad_var = strip_tags($bad_var);
    }
    $secure_var = mysqli_real_escape_string($GLOBALS['mysql_con'], $bad_var);
    return $secure_var;
}

// Gibt die Dc-Einstellungen zurück
function setup_get() {
    // satdnardwebsite auslesen. main_setup am 1.11.2014 geloescht
    $setup  = array();
    $query  = "SELECT id FROM main_site WHERE is_standard_site = 1 LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $row                       = @mysqli_fetch_array($result);
        $setup['std_main_site_id'] = $row['id'];
        return $setup;
    }

    // keine default website gefunden, also die erstbeste nehmen
    $query                     = "SELECT id from main_site order by id asc limit 1";
    $result                    = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row                       = @mysqli_fetch_array($result);
    $setup['std_main_site_id'] = $row['id'];
    return $setup;
}

/**
 * Seets site-code in GET if not set
 */
function set_sitecode_get()
{
    if(empty($_GET['site'])) {
        $site = (isset($_GET["site"]) && $_GET["site"] <> '') ? site_getbycode($_GET["site"]) : site_getbyid($GLOBALS["setup"]["std_main_site_id"]);
        $_GET['site'] = $site['code'];
    }
}


// Gibt den Datensatz der aktuellen Frontend-Seite zurück

function site_getbycode( $sitecode ) {
    $query  = ($sitecode <> '') ? "SELECT * FROM main_site WHERE code = '" . $sitecode . "'" : "SELECT * FROM main_site LIMIT 0,1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    } else {
        $setup = setup_get();
        return site_getbyid($setup["std_main_site_id"]);
    }
}

function site_getbyid( $siteid ) {
    $query  = ($siteid <> 0) ? "SELECT * FROM main_site WHERE id = '" . $siteid . "'" : "SELECT * FROM main_site LIMIT 0,1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

// Realisiert einen Seitenwechsel
function site_change() {
    if (($_GET["change_site"]) | ($_POST["input_site_id"] <> '')) {
        $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site WHERE id = '" . $_POST["input_site_id"]."'");
        if (@mysqli_num_rows($result) == 1) {
            $new_site     = @mysqli_fetch_array($result);
            $_GET["site"] = $new_site["code"];
        }
    }
}

// Gibt den Datensatz der aktuellen Frontend-Sprache zurück
function language_getbycode( $languagecode, $main_site_id ) {
    $query  = ($languagecode <> '') ? "SELECT * FROM main_language WHERE code = '" . $languagecode . "' AND main_site_id = '" . $main_site_id . "'" : "SELECT * FROM main_language LIMIT 0,1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

function language_getbyid( $languageid ) {
    $query  = ($languageid <> 0) ? "SELECT * FROM main_language WHERE id = '" . $languageid . "'" : "SELECT * FROM main_language LIMIT 0,1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

// Realisiert den Wechsel der Sprache
function language_change() {
    if (isset($_GET["change_language"]) && ($_GET["change_language"] <> '')) {
        $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_language WHERE code = '" . $_POST["change_language"] . "'");
        if (@mysqli_num_rows($result) == 1) {
            $new_language     = @mysqli_fetch_array($result);
            $_GET["language"] = $new_language["code"];
        }
    }
}

// Sprachen Drop-Down
function language_frontend_select( $selectname = "input_language_id", $value = NULL, $disabled = FALSE ) {
    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<select" . $disabled_text . " style=\"height:18px;\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_language");
    if (mysqli_num_rows($result) > 0) {
        while ($language = mysqli_fetch_array($result)) {
            if ($language["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
            } else {
                echo "<option value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}

// Gibt den Datenbankeintrag des aktuellen Layouts zurück
function layout_getbyid( $layoutid ) {
    $query  = "SELECT * FROM main_layout WHERE id = '" . $layoutid . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $row    = mysqli_fetch_array($result);
    return $row;
}

function page_getbyid( $pageid ) {
    $query  = "SELECT * FROM main_page where id = '" . (int)$pageid."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $row    = mysqli_fetch_array($result);
    return $row;
}

// Gibt den Datenbankeintrag des aktuellen Menübereichs zurück
function navigation_get( $site, $language ) {
    $levelcode = "";
    if (!empty($_GET["level_1"])) {
        $levelcode = $_GET["level_1"];
    }
    if (!empty($_GET["level_2"])) {
        $levelcode = $_GET["level_2"];
    }
    if (!empty($_GET["level_3"])) {
        $levelcode = $_GET["level_3"];
    }
    if (!empty($_GET["level_4"])) {
        $levelcode = $_GET["level_4"];
    }
    if (!empty($_GET["level_5"])) {
        $levelcode = $_GET["level_5"];
    }
    if ($levelcode == "") {
        $navigation = navigation_getbyid($language["std_main_navigation_id"]);
    } else {
        $query  = "SELECT * FROM main_navigation WHERE main_site_id = " . $site["id"] . " AND main_language_id = " . $language["id"] . " AND code = '" . $levelcode . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $navigation = @mysqli_fetch_array($result);
        } else {
            $navigation = navigation_getbyid($language["std_main_navigation_id"]);
        }
    }
    //$navigation = navigation_get_rek($navigation);
    $i = 5;
    while ($i > $navigation["level"]) {
        $_GET["level_" . $i] = "";
        $i--;
    }
    set_navigation_level_vars_rek($navigation);
    return $navigation;
}

function navigation_get_rek( $navigation ) {
    if (($navigation["forward_type"] == 2) && ($navigation["forward_navigation_id"] <> '')) {
        $query = "SELECT * FROM main_navigation WHERE id = '" . $navigation["forward_navigation_id"] . "'";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $navigation_temp = @mysqli_fetch_array($result);
            return navigation_get_rek($navigation_temp);
        }
    } else {
        return $navigation;
    }
}

// Fälscht Navigationsvariablen für Weiterleitung
function set_navigation_level_vars_rek( $navigation ) {
    $_GET["level_" . $navigation["level"]] = $navigation["code"];
    if ($navigation["level"] > 1) {
        $query  = "SELECT * FROM main_navigation WHERE id = " . $navigation["parent_id"];
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $temp_navigation = @mysqli_fetch_array($result);
            set_navigation_level_vars_rek($temp_navigation);
        }
    }
}

// Gib den Datensatz einer Navigationsebene zurück
function navigation_getbyid( $navigationid ) {
    $query  = "SELECT * FROM main_navigation WHERE id = '" . $navigationid . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

function navigation_getbycode( $code, $site = '', $language = '' ) {
    if ($site == '') {
        $site = $GLOBALS["site"];
    }
    if ($language == '') {
        $language = $GLOBALS["language"];
    }
    $query  = "SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "' AND code = '" . $code . "'";

    if(!isset($GLOBALS['mysql_con']))
    {
        db_connect();
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

// Titel der Seite zusammenstellen
function site_title( $language, $navigation ) {
    if ($_GET["card"] != '') {
        $item = get_item_by_card_id($GLOBALS["shop"]["company"], $GLOBALS["shop"]["code"], $language["code"], $_GET["card"]);
        if (strlen($item["site_title"]) > 0) {
            $title = $item["site_title"] . " | " . $GLOBALS["language"]["site_title_name"];
        } else {
            $title = $item["description"] . " | " . $GLOBALS["language"]["site_title_name"];
        }
    } else {
        if ($_GET['shop_category'] == 'search') {
            $title = $GLOBALS["tc"]["search"] . " | ";
        } elseif ($_GET['shop_category'] == 'order') {
            $title = $GLOBALS["tc"]["order"] . " | ";
        } elseif ($_GET['shop_category'] == 'basket') {
            $title = $GLOBALS["tc"]["shopping_basket"] . " | ";
        } elseif ($GLOBALS["category"]["site_titel"] == '') {
            if ($GLOBALS["category"]["name"] != '') {
                $title = $GLOBALS["category"]["name"] . " | ";
            } elseif ($navigation["title_name"] <> '') {
                $title = $navigation["title_name"] . " | ";
            }
        } else {
            $title = $GLOBALS["category"]["site_titel"] . " | ";
        }
        $title .= $GLOBALS['language']["site_title_name"];
    }
    return $title;
}

//Canonical infos füllen
function get_canonical() {
    $url    = $_SERVER["SERVER_NAME"] . $_SERVER["REDIRECT_URL"];
    $domain = $_SERVER["SERVER_NAME"];
    //Sonderseiten
    switch ($_GET['shop_category']) {
        case "search":
            //$path = "<link rel=\"canonical\" href=\"" . $url . "?shop_category=search\" />";
            $path = '<meta name="robots" content="noindex">';
            break;
        case "order":
            //$path = "<link rel=\"canonical\" href=\"" . $url . "?shop_category=order\" />";
            $path = '<meta name="robots" content="noindex">';
            break;
        case "basket":
            // $path = "<link rel=\"canonical\" href=\"" . $url . "?shop_category=basket\" />";
            $path = '<meta name="robots" content="noindex">';
            break;
        case "account":
            // $path = "<link rel=\"canonical\" href=\"" . $url . "?shop_category=account\" />";
            $path = '<meta name="robots" content="noindex">';
            break;
        case "favorites":
            //$path = "<link rel=\"canonical\" href=\"" . $url . "?shop_category=favorites\" />";
            $path = '<meta name="robots" content="noindex">';
            break;
    }
    //Weiterleitungen
    $query            = "SELECT * FROM main_navigation WHERE id = '" . $GLOBALS['navigation']['id'] . "'";
    $result           = mysqli_query($GLOBALS['mysql_con'], $query);
    $row              = mysqli_fetch_object($result);
    $main_site_id     = $row->main_site_id;
    $main_language_id = $row->main_language_id;
    // Wird später durch parent_id überschrieben
    $parent_id = $row->parent_id;
    $forward   = $row->forward;
    if ($forward) {
        $parent_id = $row->forward_navigation_id;
    }
    $code = $row->code;
    //prüft ob shop, falls nicht > weiterleitungscanonical
    if ($code != 'shop') {
        $path = NULL;
        while ($parent_id != NULL) {
            $query_2 = "SELECT * FROM main_navigation WHERE id = '" . $parent_id . "'";

            $result_2  = mysqli_query($GLOBALS['mysql_con'], $query_2);
            $parent_id = '0';
            while ($row = mysqli_fetch_object($result_2)) {
                if ($row->code != $code) {
                    $path = $row->code . "/" . $path;
                }
                if ($row->forward != "0") {
                    $path      = "";
                    $parent_id = $row->forward_navigation_id;
                } else {
                    $parent_id = $row->parent_id;
                }
            }
        }
        //canonical pfad erstellen
        $path = $path . $code;

        $noindexValue = "";
        if((bool)$GLOBALS['page']['noindex'])
        {
            $noindexValue = "noindex, ";
        }
        if((bool)$GLOBALS['page']['nofollow'])
        {
            $noindexValue .= "nofollow, ";
        }

        if( isset($_GET['collection_id']) && $_GET['collection_id'] <> '')
        {
            $query2       = "SELECT main_collection_setup_group.description,main_collection.noindex, main_collection.nofollow FROM main_collection JOIN main_collection_group_link left join main_collection_setup_group on (main_collection_setup_group.id = main_collection_group_link.main_collection_setup_group_id) where main_collection.id ='". $_GET['collection_id']."' and main_collection_group_link.active = 1 and main_collection_group_link.main_collection_id = '" . $_GET['collection_id']."'";
            $result2      = @mysqli_query($GLOBALS['mysql_con'], $query2);
            $groupCode = '';
            while ($row2 = @mysqli_fetch_array($result2)) {
                $groupCode = $row2['description'];
                if($row2['noindex'])
                {
                    $noindexValue = "noindex, ";
                }
                if($row2['nofollow'])
                {
                    $noindexValue .= "nofollow, ";
                }
            }

            if($groupCode <> '')
            {
                $path = $path ."/" .get_collection_rewrite($groupCode, filter_var($_GET['collection_id'],FILTER_SANITIZE_NUMBER_INT));
                $path = rtrim($path,"/");
            }


        }



        $path = "<link rel='canonical' href='https://" . $domain . "/" . customizeUrl(). "/" . $path . "/'/>";

        if($noindexValue <> "")
        {
            $noindexValue = rtrim($noindexValue,', ');
            $path = '<meta name="robots" content="'.$noindexValue.'">';
        }
    } else {
        //Artikelliste ohne Sortieroption
        if (isset($GLOBALS["category"]["id"]) || isset($_GET['card'])) {
            if (!isset($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }
            if (!isset($_GET['card'])) {

                $path = '<link rel="canonical" href="https://' . $_SERVER["SERVER_NAME"] . $_SERVER["REDIRECT_URL"] . '"/>';
                if($page > 1 )
                {
                    $path = '<meta name="robots" content="noindex">';
                }
            } else {
                $card = $_GET['card'];

                $urlend    = $_SERVER["REQUEST_URI"];
                $urlend    = explode("?", $urlend);
                $urlmiddle = $urlend[0];
                $canonical = "";
                IF ($_GET['card'] != '') {
                    $itemquery  = "SELECT * FROM shop_item WHERE id='" . mysqli_real_escape_string($GLOBALS['mysql_con'] ,$_GET["card"])."'";
                    $itemresult = @mysqli_query($GLOBALS['mysql_con'], $itemquery);
                    $item       = @mysqli_fetch_assoc($itemresult);
                    if (strlen($item["parent_item_no"]) > 0) {
                        $parentitemquery  = "SELECT * FROM shop_item WHERE company='" . $GLOBALS["shop"]["company"] . "' AND shop_code='" . $GLOBALS["shop"]["item_source"] . "' AND language_code='" . $GLOBALS["shop_language"]["code"] . "' AND item_no='" . $item["parent_item_no"] . "'";
                        $parentitemresult = @mysqli_query($GLOBALS['mysql_con'], $parentitemquery);
                        $parent_item      = @mysqli_fetch_assoc($parentitemresult);
                    } else {
                        $parent_item = $item;
                    }
                    IF ($parent_item['id'] == '' || $parent_item['id'] == $item['id']) {
                        $canonical_card_id = $item['id'];
                        IF ($item['main_category_line_no'] <> 0) {
                            $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category 
                                WHERE line_no = '" . $item['main_category_line_no'] . "'
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                        } ELSE {
                            $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category 
                                WHERE line_no = 
                                  (SELECT category_line_no FROM shop_item_has_category 
                                    WHERE item_no = '" . $item['item_no'] . "'
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["item_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'
                                    GROUP BY category_line_no ORDER BY category_line_no ASC LIMIT 1)
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                        }
                        $cat_result   = @mysqli_query($GLOBALS['mysql_con'], $cat_query);
                        $itemcategory = @mysqli_fetch_array($cat_result);
                        $caturl       = array();
                        $seek         = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                        $replace      = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                        $caturl[0]    = str_replace($seek, $replace, $itemcategory["code"]);
                        $caturl[0]    = htmlspecialchars($caturl[0], ENT_QUOTES, "UTF-8");
                        $caturl[0]    = $caturl[0] . "/";
                        $i            = 1;
                        WHILE ($itemcategory['plo'] > 0) {
                            $cat_query_rek = "SELECT code, name, parent_line_no AS plo 
                                              FROM shop_category 
                                              WHERE line_no = '" . $itemcategory['plo'] . "'
                                                AND company = '". $GLOBALS["shop"]["company"] ."'
                                                AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                                AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                            $cat_res_rek   = @mysqli_query($GLOBALS['mysql_con'], $cat_query_rek);
                            $itemcategory  = @mysqli_fetch_array($cat_res_rek);
                            IF ($itemcategory['name'] != '') {
                                $seek       = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                                $replace    = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                                $caturl[$i] = str_replace($seek, $replace, $itemcategory["code"]);
                                $caturl[$i] = htmlspecialchars($caturl[$i], ENT_QUOTES, "UTF-8");
                                $caturl[$i] = $caturl[$i] . "/";
                            } ELSE {
                                $caturl[$i] = '';
                            }
                            $i++;
                        }
                        $catstring = "";
                        DO {
                            $catstring .= $caturl[$i];
                            $i--;
                        } WHILE ($i >= 0);
                        $seek            = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                        $replace         = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                        $itemdescription = str_replace($seek, $replace, $item["description"]);
                        $itemdescription = htmlspecialchars($itemdescription, ENT_QUOTES, "UTF-8");
                        $itemdescription = str_replace("&Acirc;", "", $itemdescription);
                        $itemdescription = str_replace("&acirc;", "", $itemdescription);
                        $itemdescription = trim($itemdescription, "+");
                        $base_string     = "https://" . $_SERVER["SERVER_NAME"] . "/";
                        //   $request_string = customizeUrl() . "/" . $catstring . $itemdescription . "-p" . $canonical_card_id . "/";
                        //$request_string = customizeUrl() . "/". $itemdescription . "-p" . $canonical_card_id . "/";
                        $request_string = customizeUrl() . "/". $item["item_slug"] . "-p" . $canonical_card_id . "/";
                        $request_string  = str_replace("//", "/", $request_string);
                        $canonical       = $base_string . $request_string;
                        $canonical       = str_replace(" ", "-", $canonical);
                        $canonical       = str_replace("--", "-", $canonical);
                    } else {
                        $canonical_card_id = $parent_item["id"];
                        IF ($parent_item['main_category_line_no'] <> 0) {
                            $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category 
                                WHERE line_no = '" . $parent_item['main_category_line_no'] . "'
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                        } ELSE {
                            $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category 
                                WHERE line_no = 
                                  (SELECT category_line_no FROM shop_item_has_category 
                                    WHERE item_no = '" . $parent_item['item_no'] . "'
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["item_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'
                                    GROUP BY category_line_no ORDER BY category_line_no ASC LIMIT 1)
                                    AND company = '". $GLOBALS["shop"]["company"] ."'
                                    AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                    AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                        }
                        $cat_result   = @mysqli_query($GLOBALS['mysql_con'], $cat_query);
                        $itemcategory = @mysqli_fetch_array($cat_result);
                        $caturl       = array();
                        IF ($itemcategory['code'] != '') {
                            $seek      = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                            $replace   = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                            $caturl[0] = str_replace($seek, $replace, $itemcategory["code"]);
                            $caturl[0] = htmlspecialchars($caturl[0], ENT_QUOTES, "UTF-8");
                            $caturl[0] = $caturl[0] . "/";
                        } ELSE {
                            $caturl[0] = '';
                        }
                        $i = 1;
                        WHILE ($itemcategory['plo'] > 0) {
                            $cat_query_rek = "SELECT code, name, parent_line_no AS plo 
                                              FROM shop_category 
                                              WHERE line_no = '" . $itemcategory['plo'] . "'
                                                AND company = '". $GLOBALS["shop"]["company"] ."'
                                                AND shop_code = '". $GLOBALS["shop"]["category_source"] ."'
                                                AND language_code = '". $GLOBALS["shop_language"]["code"] ."'";
                            $cat_res_rek   = @mysqli_query($GLOBALS['mysql_con'], $cat_query_rek);
                            $itemcategory  = @mysqli_fetch_array($cat_res_rek);
                            IF ($itemcategory['name'] != '') {
                                $seek       = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                                $replace    = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                                $caturl[$i] = str_replace($seek, $replace, $itemcategory["code"]);
                                $caturl[$i] = htmlspecialchars($caturl[$i], ENT_QUOTES, "UTF-8");
                                $caturl[$i] = $caturl[$i] . "/";
                            } ELSE {
                                $caturl[$i] = '';
                            }
                            $i++;
                        }
                        $catstring = "";
                        DO {
                            $catstring .= $caturl[$i];
                            $i--;
                        } WHILE ($i >= 0);
                        $seek            = array('ä', 'ö', 'ü', 'ß','+', '*', ' ', '.', '/', '\\', '%', ',', 'Ø', 'Ã', 'ø', 'ã', 'Õ', 'õ', '"', "''", "'");
                        $replace         = array('ae', 'oe', 'ue', 'ss','plus', '-', '-', '-', '', '', 'proz', '+', '&Oslash;', '&Atilde;', '&oslash;', '&atilde;', '&Otilde;', '&otilde;', '&quot;', '&quot;', '');
                        $itemdescription = str_replace($seek, $replace, $item["description"]);
                        $itemdescription = htmlspecialchars($itemdescription, ENT_QUOTES, "UTF-8");
                        $itemdescription = str_replace("&Acirc;", "", $itemdescription);
                        $itemdescription = str_replace("&acirc;", "", $itemdescription);
                        $itemdescription = trim($itemdescription, "+");
                        $base_string = "https://" . $_SERVER["SERVER_NAME"] . "/";
                        // $request_string = customizeUrl() . "/" . $catstring . $itemdescription . "-p" . $canonical_card_id . "/";
                        //$request_string = customizeUrl() . "/" . $itemdescription . "-p" . $canonical_card_id . "/";
                        $request_string = customizeUrl() . "/". $item["item_slug"] . "-p" . $canonical_card_id . "/";
                        $request_string = str_replace("//", "/", $request_string);
                        $canonical = $base_string . $request_string;
                        $canonical = str_replace(" ", "-", $canonical);
                        $canonical = str_replace("--", "-", $canonical);
                    }
                }
                $canonical = strtolower($canonical);
                $path = '<link rel="canonical" href="' . $canonical . '"/>';
            }
        }
    }
    if ($path != $url) {
        $path = strtolower($path);
        return $path;
    }
}

//Facebook meta daten Infos füllen
function get_fb_meta_info() {
    $card_id = (int)$_GET['card'];
    if (isset($card_id)) {
        $query  = "SELECT * FROM shop_item WHERE id = '" . $card_id . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $item_no      = $row->item_no;
            $product_name = $row->description;
            $company = $row->company;
            $shop_code = $row->shop_code;
            $shop_language_code = $row->language_code;
            //image name füllen
            $query_2  = "SELECT * FROM shop_item_file WHERE item_no = '" . $item_no . "' AND company = '$company' AND shop_code = '$shop_code' AND language_code = '$shop_language_code' LIMIT 1";
            $result_2 = mysqli_query($GLOBALS['mysql_con'], $query_2);
            while ($row_2 = mysqli_fetch_object($result_2)) {
                $product_img = $row_2->filename;
            }
            //produkt beschreibung füllen
            $query_3  = "SELECT * FROM shop_item_description WHERE item_no = '" . $item_no . "'  AND company = '$company' AND shop_code = '$shop_code' AND language_code = '$shop_language_code' AND marketplace_only = 0 LIMIT 1";
            $result_3 = mysqli_query($GLOBALS['mysql_con'], $query_3);
            while ($row_3 = mysqli_fetch_object($result_3)) {
                $product_description = $row_3->content;
            }
        }
        $fb_meta_data = "<meta property='og:image' content='" . $_SERVER["SERVER_NAME"] . "/userdata/dcshop/images/thumb_2/" . $product_img . "'/>";
        $fb_meta_data .= "<meta property='og:title' content='" . $product_name . "'/>";
        $fb_meta_data .= "<meta property='og:url' content='" . $_SERVER["SERVER_NAME"] . $_SERVER["REDIRECT_URL"] . "-p" . $card_id . "/'/>";
        $fb_meta_data .= "<meta property='og:description' content='" . $product_description . "'/>";
        $fb_meta_data .= "<meta property='type' content='article'/>";

        return $fb_meta_data;
    }
}

//Bild erstellen
function get_image( $item ,$type = 1) {
    $item_no        = $item['item_no'];
    $parent_item_no = $item["parent_item_no"];
    //image name füllen
    $query  = "SELECT * FROM shop_item_file WHERE (item_no = '" . $item_no . "' OR item_no = '" . $parent_item_no . "') and customization = 0   LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = mysqli_fetch_object($result)) {
        $product_img = $row->filename;
    }
    if ($product_img == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][$type]["path"] . "/" . $product_img)) {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            $product_img = $GLOBALS['shop_language']['item_placeholder_image'];
        } else {
            $product_img = "noimage.jpg";
        }
    }
    //$fb_review_data .= "<div class='label_article_name'>".$product_name."</div>";
    $image_link = "<div id='product_img'><img src='" . $GLOBALS['projectRoot'] . "/userdata/dcshop/images/thumb_". $type . "/" . $product_img . "' /></div>";
    return $image_link;
}


//Bild erstellen
function get_customization_image( $item ,$type = 1) {
    $item_no        = $item['item_no'];
    $parent_item_no = $item["parent_item_no"];
    //image name füllen
    $query  = "SELECT * FROM shop_item_file WHERE ( item_no = '" . $item_no . "' OR item_no = '" . $parent_item_no . "' ) and customization = 1  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if($result->num_rows == 0)
    {
        return get_image($item, $type);
    }

    while ($row = mysqli_fetch_object($result)) {
        $product_img = $row->filename;
    }
    if ($product_img == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][$type]["path"] . "/" . $product_img)) {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            $product_img = $GLOBALS['shop_language']['item_placeholder_image'];
        } else {
            $product_img = "noimage.jpg";
        }
    }
    //$fb_review_data .= "<div class='label_article_name'>".$product_name."</div>";
    $image_link = "<div id='product_img'><img src='" . $GLOBALS['projectRoot'] . "/userdata/dcshop/images/thumb_". $type . "/" . $product_img . "' /></div>";
    return $image_link;
}

//Facebook meta daten review erstellen
function create_fb_review( $card_id, $url ) {
    if (isset($card_id)) {
        $query  = "SELECT * FROM shop_item WHERE id = '" . $card_id . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        while ($row = mysqli_fetch_object($result)) {
            $item_no             = $row->item_no;
            $product_name        = $row->description;
            $product_description = $row->summary;
            //image name füllen
            $query_2  = "SELECT * FROM shop_item_file WHERE item_no = '" . $item_no . "' LIMIT 1";
            $result_2 = mysqli_query($GLOBALS['mysql_con'], $query_2);
            while ($row_2 = mysqli_fetch_object($result_2)) {
                $product_img = $row_2->filename;
            }
            //produkt beschreibung füllen
            //$query_3 = "SELECT * FROM shop_item_description WHERE item_no = '".$item_no."' LIMIT 1";
            //$result_3 = mysqli_query($GLOBALS['mysql_con'],$query_3);
            //while($row_3 = mysqli_fetch_object($result_3))
            //{
            //	$product_description = $row_3->summary;
            //} 
        }
        //$fb_review_data .= "<div class='label_article_name'>".$product_name."</div>";
        $fb_review_data .= "<div id='fb_product_img' class='modal-info-image'><img src='" . $GLOBALS['projectRoot'] . "/userdata/dcshop/images/thumb_2/" . $product_img . "' /></div>";
        //$fb_review_data .= "<div id='fb_product_url'><a href='?card=".$card_id."'>".$url."?card=".$card_id."</a></div>";
        $fb_review_data .= "<div id='user_queue_order_text' class='modal-info-description'><div id='fb_product_name'><strong>" . $product_name . "</strong></div><div id='fb_product_description'>" . $product_description . "</div></div>";


        return $fb_review_data;
    }
}

function get_modal_infobox($item) {?>
    <div class="modal-item-info">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="modal-item-image">
                    <?= get_image($item,2) ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="modal-item-description">
                    <strong><?= $item['description'] ?></strong>
                    <br />
                    <?= $item['summary'] ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

//Metadaten füllen, Übergabe per call-by-reference
function get_meta_data( $navigation, &$site_title, &$meta_keywords, &$meta_description, $is_shop = TRUE ) {
    // Meta-Daten aus Sprache füllen 
    //$meta_description = $GLOBALS["language"]["meta_description"];
    //$meta_keywords = $GLOBALS["language"]["meta_keywords"];  
    // Meta-Daten aus Navigation füllen
    if ($navigation["menu_name"] <> '') {
        $site_title = $navigation["menu_name"] . " | ";
    }
    if ($navigation["title_name"] <> '') {
        $site_title = $navigation["title_name"] . " | ";
    }
    if ($navigation["meta_description"] <> '') {
        $meta_description = $navigation["meta_description"];
    }
    if ($navigation["meta_keywords"] <> '') {
        $meta_keywords = $navigation["meta_keywords"];
    }
    // Meta-Daten für News füllen
    $query  = "SELECT * FROM main_navigation_has_sitepart WHERE main_navigation_id = " . $navigation["id"] . " AND main_sitepart_id = 2 LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $navigation_has_sitepart = @mysqli_fetch_array($result);
        if ($_GET["news" . $navigation_has_sitepart["id"]] <> '') {
            $query  = "SELECT * FROM news_entry WHERE id = " . $_GET["news" . $navigation_has_sitepart["id"]] . " LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $news = @mysqli_fetch_array($result);
                if ($news["headline"] <> '') {
                    $site_title = $news["headline"] . " | ";
                }
                if ($news["summary"] <> '') {
                    $news["summary"]  = str_replace("\n", "", $news["summary"]);
                    $news["summary"]  = str_replace("\r", "", $news["summary"]);
                    $meta_description = $news["summary"];
                }
                if ($news["meta_keywords"] <> '') {
                    $meta_keywords = $news["meta_keywords"];
                }
            }
        }
    }
    //Metadaten nur für Shop
    if ($is_shop) {
        //Sonderkategorien
        if ($_GET['shop_category'] == 'search') {
            $site_title       = $GLOBALS["tc"]["search"] . " | ";
            $meta_description = '';
            $meta_keywords    = '';
        } elseif ($_GET['shop_category'] == 'order') {
            $site_title       = $GLOBALS["tc"]["order"] . " | ";
            $meta_description = '';
            $meta_keywords    = '';
        } elseif ($_GET['shop_category'] == 'basket') {
            $site_title       = $GLOBALS["tc"]["shopping_basket"] . " | ";
            $meta_description = '';
            $meta_keywords    = '';
        } elseif ($_GET['shop_category'] == 'account') {
            $site_title       = $GLOBALS["tc"]["my_account"] . " | ";
            $meta_description = '';
            $meta_keywords    = '';
        } elseif ($_GET['shop_category'] == 'favorites') {
            if ($GLOBALS["shop"]["shop_typ"] == 1) {
                $site_title       = $GLOBALS["tc"]["favorites"];
            } else {
                $site_title       = $GLOBALS["tc"]["favorites_b2b"];
            }
            $meta_description = '';
            $meta_keywords    = '';
        } else {
            //Artikelkategorie oder Artikelkarte
            if ($GLOBALS['category'] != '') {
                if ($GLOBALS["category"]["site_titel"] != '') {
                    $site_title = $GLOBALS["category"]["site_titel"] . " | ";
                } elseif ($GLOBALS["category"]["name"] != '') {
                    $site_title = $GLOBALS["category"]["name"] . " | ";
                }
                $meta_keywords    = $GLOBALS["category"]["meta_keywords"];
                $meta_description = ($GLOBALS["category"]["meta_description"] == '') ? $GLOBALS["category"]["category_description"] : $GLOBALS["category"]["meta_description"];
            }
            if ($_GET['card'] != '') {
                if ($GLOBALS["item"]["site_title"] != "" || $GLOBALS["item"]["description"] != "") {
                    $site_title = ($GLOBALS["item"]["site_title"] == '') ? $GLOBALS["item"]["description"] . " | " : $GLOBALS["item"]["site_title"] . " | ";
                }
                if ($GLOBALS["item"]["meta_keywords"] != "" || $GLOBALS["item"]["search_query"] != "") {
                    $meta_keywords = ($GLOBALS["item"]["meta_keywords"] == '') ? $GLOBALS["item"]["search_query"] : $GLOBALS["item"]["meta_keywords"];
                }
                if ($GLOBALS["item"]["meta_description"] != "" || $GLOBALS["item"]["summary"] != "") {
                    $meta_description = ($GLOBALS["item"]["meta_description"] == '') ? $GLOBALS["item"]["summary"] : $GLOBALS["item"]["meta_description"];
                }
            }
        }
    }
    // Meta-Daten aus Sprache anfügen
    $site_title .= $GLOBALS['language']["site_title_name"];
    $meta_description .= substr(strip_tags($GLOBALS["language"]["meta_description"]), 0, 200);
    $meta_keywords .= $GLOBALS["language"]["meta_keywords"];
}

// Gibt den Navigationspfad zurück
function curr_navigation_path( $site, $language, $navigation ) {
    return curr_navigation_path_rek($site["code"], $language["code"], $navigation["parent_id"], '');
}

function curr_navigation_path_rek( $sitecode, $languagecode, $parent_id, $path ) {
    if ($parent_id > 0) {
        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_view_active_navigation WHERE id = '".$parent_id."'");
        $nav    = @mysqli_fetch_array($result);
        switch ($nav["level"]) {
            case 4:
                $link = "/$sitecode/$languagecode/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/" . $_GET["level4"] . "/";
                break;
            case 3:
                $link = "/$sitecode/$languagecode/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/";
                break;
            case 2:
                $link = "/$sitecode/$languagecode/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/";
                break;
            case 1:
                $link = "/$sitecode/$languagecode/" . $_GET["level_1"] . "/";
                break;
        }
        $path = "<a href=\"$link\">" . htmlspecialchars($nav["menu_name"], ENT_QUOTES, "UTF-8") . "</a>" . $path;
        $path = curr_navigation_path_rek($sitecode, $languagecode, $nav["parent_id"], $path);
    }
    return $path;
}


//TF: 18.09.13 für Breadcrumb redaktionell, von NBM kopiert
function cust_curr_navigation_path( $site, $language, $navigation ) {
    if ((int)$navigation['id'] <> (int)$language['std_main_navigation_id'] && !isset($_GET['collection_id'])) {
        return '<div class="breadcrumb"><span><a href="/' . customizeUrl() . '/">' . $GLOBALS["tc"]["homepage"] . '</a>' . cust_curr_navigation_path_rek($site["code"], $language["code"], $navigation["parent_id"], '') . "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<span class='current'>" . $navigation["menu_name"] . '</span></span></div>';
    } elseif(isset($_GET['collection_id'])) {
        switch ($navigation["level"]) {
            case 4:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/" . $_GET["level4"] . "/";
                break;
            case 3:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/";
                break;
            case 2:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/";
                break;
            case 1:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/";
                break;
        }

        $IOCContainer = $GLOBALS['IOC'];
        $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');

        $collectionID = filter_var($_GET['collection_id'],FILTER_SANITIZE_NUMBER_INT);

        $prepStatement =  "
          SELECT
                *
          FROM main_collection
              WHERE  id = :collectionId
            ";
        $params = [
            [':collectionId', $collectionID, PDO::PARAM_INT],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) == 1) {
            return '<div class="breadcrumb">
                <span><a href="/' . customizeUrl(). '/">' . $GLOBALS["tc"]["homepage"] . '</a>'
                . cust_curr_navigation_path_rek($site["code"], $language["code"], $navigation["parent_id"], '')
                . "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<a href=\"".$link."\"><span>" . $navigation["menu_name"] . '</span></a>
                &nbsp;<i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;<span class=\'current\'>'.$resultArray[0]["description"].'</span>
             </span></div>';
        } else {
            return '<div class="breadcrumb"><span><a href="/' .customizeUrl() . '/">' . $GLOBALS["tc"]["homepage"] . '</a>' . cust_curr_navigation_path_rek($site["code"], $language["code"], $navigation["parent_id"], '') . "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<span class='current'>" . $navigation["menu_name"] . '</span></span></div>';
        }


    } else {
        return '<div class="breadcrumb"><span><a href="/' . customizeUrl() . '/">' . $GLOBALS["tc"]["homepage"] . '</a></span></div>';
    }
}

function cust_curr_navigation_path_rek( $sitecode, $languagecode, $parent_id, $path ) {
    if ($parent_id > 0) {
        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_view_active_navigation WHERE id = '".$parent_id."'");
        $nav    = @mysqli_fetch_array($result);
        switch ($nav["level"]) {
            case 4:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/" . $_GET["level4"] . "/";
                break;
            case 3:
                $link = "/".customizeUrl()."/". $_GET["level_1"] . "/" . $_GET["level_2"] . "/" . $_GET["level_3"] . "/";
                break;
            case 2:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/" . $_GET["level_2"] . "/";
                break;
            case 1:
                $link = "/".customizeUrl()."/" . $_GET["level_1"] . "/";
                break;
        }
        if ($nav["level"] > 1) {
            $path = "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<a href=\"$link\">" . htmlspecialchars($nav["menu_name"], ENT_QUOTES, "UTF-8") . "</a>" . $path;
        }
        $path = cust_curr_navigation_path_rek($sitecode, $languagecode, $nav["parent_id"], $path);
    }
    return $path;
}


function navigation_path( $site, $language, $navigation ) {
    return "/" . $site["code"] . "/" . $language["code"] . "/" . navigation_path_rek($navigation["id"], '');
}

function current_site_navigation_path( $site = '', $language = '', $navigation ) {
    return "/" .customizeUrl(). "/" . navigation_path_rek($navigation["id"], '');
}

function navigation_path_rek( $navigationid, $path ) {
    if ($navigationid <> '') {
        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_navigation WHERE id = '" . $navigationid."'");
        $nav    = @mysqli_fetch_array($result);
        $path   = $nav["code"] . "/" . $path;
        if ($nav["level"] > 0) {
            $path = navigation_path_rek($nav["parent_id"], $path);
        }
    }
    return $path;
}

// Gibt den Link auf die Seite auf der das mitgegebene Sitepart vorkommt zurück
function get_link_to_sitepart( $sitepartid ) {
    $query  = "SELECT * FROM main_navigation_has_sitepart WHERE id = '" . $sitepartid."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sitepart = @mysqli_fetch_array($result);
        $link     = get_link_to_navigation($sitepart["main_navigation_id"]);
    }
    return $link;
}

// Gibt den Link auf eine Navigationsebene zurück
function get_link_to_navigation( $navigation_id ) {
    $query  = "SELECT * FROM main_navigation WHERE id = '" . $navigation_id."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $navigation = @mysqli_fetch_array($result);
        //  $link       = navigation_path($GLOBALS["site"], $GLOBALS["language"], $navigation);
        $link =     current_site_navigation_path($GLOBALS["site"], $GLOBALS["language"], $navigation);
    }
    return $link;
}

// Funktionen zur Menüdarstellung
// fullmenu Stellt das gesamte aufgeklappte Menü dar, menu stellt das bis zur aktuell gewählten Ebene aufgeklappte Menü dar, menu_area startet auf einem beliebigen Navigationseintrag
function navigation_full_menu( $site, $language, $navigation, $from_level, $to_level ) {
    navigation_fullmenu_rek($site, $language, $navigation, 0, 1, "", $from_level, $to_level);
    echo "\n";
    return TRUE;
}

function navigation_menu( $site, $language, $navigation, $from_level, $to_level ) {
    navigation_menu_rek($site, $language, $navigation, 0, 1, "", $from_level, $to_level);
    echo "\n";
    return TRUE;
}

function navigation_menu_area( $site, $language, $navigation_code, $to_level = 5 ) {
    $navigation = navigation_getbycode($navigation_code);
    if (null === $navigation) {
        $parent_id  = 0;
        $level      = 1;
        $navcode    = '';
    } else {
        $parent_id  = $navigation["id"];
        $level      = $navigation["level"] + 1;
        $navcode    = $navigation["code"] . "/";
    }
    navigation_menu_rek($site, $language, $navigation, $parent_id, $level, $navcode, 1, $to_level);
    echo "\n";
    return TRUE;
}

function navigation_full_menu_area( $site, $language, $navigation_code, $to_level = 5 ) {
    $navigation = navigation_getbycode($navigation_code);
    $parent_id  = "";
    $level      = 1;
    $navcode    = "";
    if (!is_null($navigation)) {
        $parent_id  = $navigation["id"];
        $level      = $navigation["level"] + 1;
        $navcode    = $navigation["code"] . "/";
    }
    navigation_fullmenu_rek($site, $language, $navigation, $parent_id, $level, $navcode, 1, $to_level);
    echo "\n";
    return TRUE;
}

function navigation_fullmenu_rek( $site, $language, $navigation, $parent_id, $level, $navcode, $from_level, $to_level ) {
    switch ($level) {
        case 1:
            $levelcode = $_GET["level_1"];
            break;
        case 2:
            $levelcode = $_GET["level_2"];
            break;
        case 3:
            $levelcode = $_GET["level_3"];
            break;
        case 4:
            $levelcode = $_GET["level_4"];
            break;
        case 5:
            $levelcode = $_GET["level_5"];
            break;
    }
    $spaces = "    ";
    while ($count <= $level) {
        $spaces .= "  ";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . (int)$parent_id."'";
    $result          = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_view_active_navigation WHERE main_site_id = '" . (int)$site["id"] . "' AND main_language_id = '" . (int)$language["id"]."'" . $parent_id_query . " ORDER BY sorting ASC");
    $show_level      = (($level >= $from_level) && ($level <= $to_level)) ? TRUE : FALSE;
    if (@mysqli_num_rows($result) > 0) {
        $show_main_level = $show_level;
        if ($show_main_level) {
            echo "\n" . $spaces . "<ul class=\"level_$level\">";
        }
        while ($nav = @mysqli_fetch_array($result)) {
            if (($nav["code"] == 'Infos') & ($to_level == 1)) {
                $show_level = FALSE;
            }
            $newnavcode = $navcode . $nav["code"] . "/";
            $active     = "";
            if ($nav["code"] == $levelcode) {
                $active = "class=\"active_tree\" ";
            }
            if ($nav["code"] == $navigation["code"]) {
                $active = "class=\"active\" ";
            }
            if ($show_level) {
                $addr = "/" . customizeUrl() . "/" . $newnavcode;
                $addr = str_replace('//','/',$addr);
                echo "\n" . $spaces . "  <li class=\"level_$level ".$nav["code"]."\"><a " . $active . "href=\"" . $addr."\">" . $nav["menu_name"] . "</a>";
            }
            navigation_fullmenu_rek($site, $language, $navigation, $nav["id"], ($level + 1), $newnavcode, $from_level, $to_level);
            if ($show_level) {
                echo "</li>";
            }
        }
        if ($show_main_level) {
            echo "\n" . $spaces . "</ul>";
        }
    }
    return TRUE;
}

function navigation_menu_rek( $site, $language, $navigation, $parent_id, $level, $navcode, $from_level, $to_level ) {

    switch ($level) {
        case 1:
            $levelcode = $_GET["level_1"];
            break;
        case 2:
            $levelcode = $_GET["level_2"];
            break;
        case 3:
            $levelcode = $_GET["level_3"];
            break;
        case 4:
            $levelcode = $_GET["level_4"];
            break;
        case 5:
            $levelcode = $_GET["level_5"];
            break;
    }
    $spaces = "";
    $count  = 0;
    while ($count <= $level) {
        $spaces .= "";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
    $query           = "SELECT * FROM main_view_active_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"]."'" . $parent_id_query . " ORDER BY sorting ASC";
    $result          = @mysqli_query($GLOBALS['mysql_con'], $query);
    $show_level      = (($level >= $from_level) && ($level <= $to_level)) ? TRUE : FALSE;
    if (@mysqli_num_rows($result) == 0) {
        return;
    }

    $show_main_level = $show_level;
    if ($show_main_level) {
        echo "" . $spaces . "<ul class=\"level_$level\">";
    }
    while ($nav = @mysqli_fetch_array($result)) {
        if ($nav["forward_type"] == 4) {
            $show_level = FALSE;
        } // platzhalter
        $newnavcode = $navcode . $nav["code"] . "/";
        $active     = "";
        if ($nav["code"] == $levelcode) {
            $active = "class=\"active_tree\" ";
        }
        if ($nav["code"] == $navigation["code"]) {
            $active = "class=\"active\" ";
        }

        $target = "";
        $href   = "/" .customizeUrl() . "/$newnavcode";
        if ($nav["forward_type"] == 3) {
            $target = 'target="_blank"';
            $href = $nav['forward_url'];
        }

        if ($show_level) {
            echo "" . $spaces . "<li class=\"level_$level  ".$nav["code"]."\"><a " . $target . " " . $active . "href=\"" . $href . "\">" . $nav["menu_name"] . "</a>";
        }
        if ($nav["code"] == $levelcode) {
            navigation_menu_rek($site, $language, $navigation, $nav["id"], ($level + 1), $newnavcode, $from_level, $to_level);
        }
        if ($show_level) {
            echo "</li>";
        }
        $show_level = (($level >= $from_level) && ($level <= $to_level)) ? TRUE : FALSE;
        $count++;
    }
    if ($show_main_level) {
        echo "" . $spaces . "</ul>";
    }

    return TRUE;
}

// Erzeugt XML-Sitemap für Suchmaschinen
function navigation_xmlsitemap( $site, $language, $navigation_code, $to_level = 5 ) {
    $navigation = navigation_getbycode($navigation_code);
    $parent_id  = $navigation["id"];
    $level      = $navigation["level"] + 1;
    $navcode    = $navigation["code"] . "/";
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
    navigation_fullmenu_rek($site, $language, $navigation, $parent_id, $level, $navcode, 1, $to_level);
    echo "\n";
    return TRUE;
}

function navigation_xmlsitemap_rek( $site, $language, $navigation, $parent_id, $level, $navcode, $from_level, $to_level ) {
    $spaces = "    ";
    while ($count <= $level) {
        $spaces .= "  ";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
    $result          = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_view_active_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = " . $language["id"] . $parent_id_query . " ORDER BY sorting ASC");
    $show_level      = (($level >= $from_level) && ($level <= $to_level)) ? TRUE : FALSE;
    if (@mysqli_num_rows($result) > 0) {
        if ($show_level) {
            echo "\n" . $spaces . "<ul class=\"level_$level\">";
        }
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active     = "";
            if ($nav["code"] == $navigation["code"]) {
                $active = "class=\"active\" ";
            }
            if ($show_level) {
                echo "\n" . $spaces . "  <li class=\"level_$level\"><a " . $active . "href=\"/" . customizeUrl() . "/$newnavcode\">" . $nav["menu_name"] . "</a>";
            }
            navigation_fullmenu_rek($site, $language, $navigation, $nav["id"], ($level + 1), $newnavcode, $from_level, $to_level);
            if ($show_level) {
                echo "</li>";
            }
        }
        if ($show_level) {
            echo "\n" . $spaces . "</ul>";
        }
    }
    return TRUE;
}

// Erzeugt Dropdown mit den Sprachen
function get_language_select( $selectname = "input_language_id", $selected_id = NULL, $send_form = NULL ) {
    $send_form_text = ($send_form <> "") ? " onChange=\"document." . $send_form . ".submit();\"" : "";
    echo "<select" . $send_form_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_language");
    if (mysqli_num_rows($result) > 0) {
        while ($language = mysqli_fetch_array($result)) {
            if ($language["id"] == $selected_id) {
                echo "<option selected=\"selected\" value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
            } else {
                echo "<option value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}

// Ereugt Dropdown mit den Seiten
function get_site_select( $selectname = "input_site_id", $selected_id = NULL, $send_form = NULL ) {
    $send_form_text = ($send_form <> "") ? " onChange=\"document." . $send_form . ".submit();\"" : "";
    echo "<select" . $send_form_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site");
    if (mysqli_num_rows($result) > 0) {
        while ($site = mysqli_fetch_array($result)) {
            $selected_id_text = ($site["id"] == $selected_id) ? " selected=\"selected\"" : "";
            echo "<option" . $selected_id_text . " value=\"" . $site["id"] . "\">" . $site["name"] . "</option>";
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}

// Erzeugt Dropdown mit Siteparts
function get_sitepart_select( $selectname = "input_sitepart_id", $selected_id = NULL ) {
    echo "<select class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site");
    if (mysqli_num_rows($result) > 0) {
        while ($site = mysqli_fetch_array($result)) {
            $selected_id_text = ($site["id"] == $selected_id) ? " selected=\"selected\"" : "";
            echo "<option" . $selected_id_text . " value=\"" . $site["id"] . "\">" . $site["name"] . "</option>";
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}

// Erzeugt Dropdown zum wechseln der Sprache
function get_language_change_select( $selectname = "input_language_id", $selected_id = NULL, $site ) {
    echo "<select class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\" onchange=\"MM_jumpMenu('parent',this,0)\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_language WHERE main_site_id = '" . $site["id"] . "'");
    if (mysqli_num_rows($result) > 0) {
        while ($language = mysqli_fetch_array($result)) {
            $selected_language_text = ($language["id"] == $selected_id) ? " selected=\"selected\"" : "";
            echo "<option" . $selected_language_text . " value=\"/dc/" . customizeUrl() . "/\">" . $language["name"] . "</option>";
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}

// Erzeugt Drop-Down zum wechseln der Seite
function get_site_change_select( $selectname = "site_language_id", $selected_id = NULL, $send_form = NULL ) {
    $send_form_text = ($send_form <> "") ? " onChange=\"document." . $send_form . ".submit();\"" : "";
    echo "<select" . $send_form_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\" onchange=\"MM_jumpMenu('parent',this,0)\">";
    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site");
    if (mysqli_num_rows($result) > 0) {
        while ($site = mysqli_fetch_array($result)) {
            $selected_id_text = ($site["id"] == $selected_id) ? " selected=\"selected\"" : "";
            echo "<option" . $selected_id_text . " value=\"/dc/" . $site["code"] . "/\">" . $site["name"] . "</option>";
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select>";
}


function get_site_language_menu( $_currentSite, $_currentLanguage ) {
    // get all languages

    $return       = array();
    $output       = '';
    $selectedSite = '';
    $languages    = array();
    $sites        = array();

    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT main_language.* FROM main_language
      left join main_site on main_language.main_site_id = main_site.id
      order by main_site.name, main_language.name");
    if (mysqli_num_rows($result) > 0) {
        while ($language = mysqli_fetch_array($result)) {
            $languages[] = $language;
            //$selected_language_text = ($language["id"] == $selected_id) ? " selected=\"selected\"" : "";
            //if($language["id"] == $selected_id)
            //echo "<option" . $selected_language_text . " value=\"/dc/" . $site["code"] . "/" . $language["code"] . "/\">" . $language["name"] . "</option>";
        }
    }


    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site order by name asc");
    if (mysqli_num_rows($result) > 0) {
        while ($site = mysqli_fetch_array($result)) {
            $sites[$site['id']] = $site;
        }
    }

    $query             = "SELECT * FROM main_site_admin_user_link where main_admin_user_id = " . (int)$GLOBALS["admin_user"]['id'];
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    $siteAdminLink = false;
    $siteAdminAvailableSites = array();
    if (@mysqli_num_rows($result) > 0) {
        $siteAdminLink = true;
        while ($row = @mysqli_fetch_array($result)) {
            $siteAdminAvailableSites[] = $row['main_site_id'];
        }
    }

    if (count($languages)) {
        foreach ($languages as $language) {

            if($siteAdminLink === true && !in_array($language['main_site_id'], $siteAdminAvailableSites)) {
                continue;
            }

            if ($_currentSite['id'] == $language['main_site_id'] && $_currentLanguage['id'] == $language['id']) {
                $selectedSite .= '<a href="#">' . $sites[$language['main_site_id']]['name'] . ' - ' . $language['name'] . '</a>';
            }
            $output .= sprintf('<li><a href="%s">%s - %s</a></li>',
                "/dc/" . $sites[$language['main_site_id']]['code'] . "/" . $language['code'] . "/",
                $sites[$language['main_site_id']]['name'],
                $language['name']
            );
        }
    } else {
        $output .= '<li><a href="#">Keine Website angelegt</a></li>';
    }

    $return['selectedSite']     = $selectedSite;
    $return['siteLanguageMenu'] = $output;

    return $return;
}

// Erstellt einen Eintrag im Mail-Log, wird erst mit mail_send versendet
function mail_create($subject, $message, $from, $to, $from_name = '', $to_name = '', $html_mail = FALSE, $attachment = "", $bcc = "", $attachment_2 = '', $delayed = 0, $payment_transaction_id = '') {
    $html_mail = ($html_mail) ? 1 : 0;
    if (($from <> '') && ($to <> '') && ($subject <> '')) {
        if (is_array($attachment)) {
            $attachment = serialize($attachment);
        }
        $query = "INSERT INTO main_mail_log(create_date, subject, message, from_adress, to_adress, from_name, 
                to_name, html_mail, attachment,bcc_address,attachment_2, main_site_code, main_language_code, shop_code, shop_language_code,delayed_send,payment_transaction_id)
				  VALUES (NOW(), '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $subject) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $message) . "','" . $from . "','" . $to . "','" . $from_name . "',
				'" . $to_name . "'," . $html_mail . ", '" . $attachment . "','" . $bcc . "','" . $attachment_2 . "','".$GLOBALS["site"]["code"]."','".$GLOBALS["language"]["code"]."','".$GLOBALS["shop"]["code"]."','".$GLOBALS["shop_language"]["code"]."'," .$delayed.",'".$payment_transaction_id."'
		)";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            return mysqli_insert_id($GLOBALS['mysql_con']);
        }
    } else {
        return FALSE;
    }
}

function mail_send() {
    $counter = 0;
    $query   = "SELECT * FROM main_mail_log WHERE send_date IS NULL AND create_date >= SUBDATE(NOW(), 2) AND delayed_send = 0";
    $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($maildata = @mysqli_fetch_array($result)) {
            $mail_send = FALSE;
            $mail = new PHPMailer();
            $mail->isHTML(true);
            $mail->setFrom($maildata["from_adress"],$maildata["from_name"]);
            $mail->addAddress($maildata["to_adress"],$maildata["to_name"]);
            if ($maildata['bcc_address'] != "") {
                $mail->addBCC($maildata['bcc_address']);
            }

            if (substr($maildata["subject"], 0, 10) == "<TEXTMAIL>") {
                $mail->Subject = utf8_decode(substr($maildata["subject"], 10));
                $mail->msgHTML(utf8_decode(mail_html_body(strip_tags($maildata["message"]),$maildata["html_mail"])));
            } else {
                $mail->Subject = utf8_decode($maildata["subject"]);
                $mail->msgHTML(utf8_decode(mail_html_body(($maildata["message"]),$maildata["html_mail"])));
            }
            $attachments = @unserialize($maildata["attachment"]);
            if ($attachments !== false) {
                foreach ($attachments as $attachment_line) {
                    if ($attachment_line != "" && (file_exists(rtrim(dirname(dirname(__DIR__)),'/\\') . $attachment_line))) {
                        $attachment_name = pathinfo($attachment_line);
                        $attachment_name = $attachment_name["basename"];
                        $mail->addAttachment(realpath("../.." . $attachment_line),utf8_decode($attachment_name));
                    }
                }
            } else {
                if (($maildata["attachment"] != "" && (file_exists("../.." . $maildata["attachment"]))) || ($maildata['attachment_2'] != '') && (file_exists("../.." . $maildata["attachment_2"]))) {
                    $mail->addAttachment(realpath("../.." . $maildata['attachment']),utf8_decode(basename($maildata['attachment'])));
                    if ($maildata['attachment_2'] != '') {
                        $mail->addAttachment(realpath("../.." . $maildata['attachment_2']),utf8_decode(basename($maildata['attachment_2'])));
                    }
                }
            }
            if ($mail->send()) {
                $mail_send = TRUE;
            }
            if ($mail_send) {
                $query = "UPDATE main_mail_log SET send_date = now() WHERE id = " . $maildata["id"];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $counter++;
            }
        }
    }
    return $counter;
}

// Sendet alle noch nicht gesendeten E-Mails aus dem Mail-Log
function mail_send_old() {
    $counter = 0;
    $query   = "SELECT * FROM main_mail_log WHERE send_date IS NULL AND create_date >= SUBDATE(NOW(), 2) AND delayed_send = 0";
    $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($mail = @mysqli_fetch_array($result)) {
            $attachments = @unserialize($mail["attachment"]);
            if ($attachments !== false) {
                $attachment = array();
                foreach ($attachments as $attachment_line) {
                    if ($attachment_line != "" && (file_exists(rtrim(dirname(dirname(__DIR__)),'/\\') . $attachment_line))) {
                        $attachment[] = realpath("../.." . $attachment_line);
                    }
                }
                $boundary        = md5(date('r', time()));
                $attachment_file = mail_attachment($attachment, $boundary);
                $body            = mail_html_body($mail["message"], $mail["html_mail"], $boundary);
                $body .= $attachment_file;
            } else {
                if (($mail["attachment"] != "" && (file_exists("../.." . $mail["attachment"]))) || ($mail['attachment_2'] != '') && (file_exists("../.." . $mail["attachment_2"]))) {
                    if ($mail['attachment_2'] != '') {
                        $attachment = array(realpath("../.." . $mail['attachment']), realpath("../.." . $mail['attachment_2']));
                    } else {
                        $attachment = realpath("../.." . $mail['attachment']);
                    }
                    $boundary        = md5(date('r', time()));
                    $attachment_file = mail_attachment($attachment, $boundary);
                    $body            = mail_html_body($mail["message"], $mail["html_mail"], $boundary);
                    $body .= $attachment_file;
                } else {
                    $body = mail_html_body($mail["message"], $mail["html_mail"]);
                }
            }
            $header = mail_html_header($mail["from_adress"], $mail["to_adress"], $mail["from_name"], $mail["to_name"], $mail["attachment"], $boundary, $mail['bcc_address']);
            if (substr($mail["subject"], 0, 10) == "<TEXTMAIL>") {
                if (@mail(utf8_decode($mail["to_adress"]), utf8_decode(substr($mail["subject"], 10)), utf8_decode(strip_tags($mail["message"])), "From: " . utf8_decode($mail["from_adress"]))) {
                    $mail_send = TRUE;
                }
            } else {
                if (@mail(utf8_decode($mail["to_adress"]), utf8_decode($mail["subject"]), utf8_decode($body), utf8_decode($header),'-f '.$mail["from_adress"])) {
                    $mail_send = TRUE;
                }
            }
            if ($mail_send) {
                $query = "UPDATE main_mail_log SET send_date = now() WHERE id = " . $mail["id"];
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $counter++;
            }
        }
    }
    return $counter;
}

function mail_html_body( $message, $html_mail = FALSE, $boundary = "" ) {
    if ($boundary != "") {
        $body = "--" . $boundary . "\n";
        $body .= "Content-Type: text/html\n";
        $body .= "Content-Transfer-Encoding: 8bit\n\n";
    }
    $body .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n";
    $body .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n";
    $body .= "<head>\r\n";
    $body .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\r\n";
    $body .= "<style type=\"text/css\">\r\n";
    $body .= "body,td,th { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #000000; }\r\n";
    $body .= "h1 { font-size: 18px; color: ".$GLOBALS['shop_setup']['mail_color']."; margin: 0px; padding: 0px;margin-bottom: 10px}\r\n";
    $body .= "h2 { font-size: 16px; color: ".$GLOBALS['shop_setup']['mail_color']."; margin: 0px; padding: 0px;}\r\n";
    $body .= "h3,h4,h5 { font-size: 14px; color: ".$GLOBALS['shop_setup']['mail_color']."; margin: 0px; padding: 0px;}\r\n";
    $body .= ".small { font-size: 10px;}\r\n";
    $body .= ".order_price_total { text-align: right; }\r\n";
    $body .= ".text-right { text-align: right; }\r\n";
    $body .= "</style></head>\r\n";
    $body .= "<body style='text-align:center' align='center'><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\"><tr><td>\r\n";
    if ($html_mail) {
        $body .= $message . "\r\n";
    } else {
        $body .= nl2br($message) . "\r\n";
    }
    $body .= "</td></tr></table></body>\r\n";
    $body .= "</html>\r\n";
    return $body;
}

function mail_html_header( $from, $to, $from_name = '', $to_name = '', $attachment = "", $boundary = "", $bcc = "" ) {
    if ($from <> '') {
        $header = ($from_name <> '') ? "From: " . $from_name . " <" . $from . ">\n" : "From: " . $from . "\n";
    }
    if ($bcc != "") {
        $header .= "Bcc: " . $bcc . "\n";
    }
    $header .= "MIME-Version: 1.0\n";
    if (($attachment != "") && (file_exists("../.." . $attachment))) {
        $header .= "Content-Type: multipart/mixed; boundary=$boundary\n";
        $header .= "Content-Transfer-Encoding: 8bit\n\n";
    } else {
        $header .= "Content-type: text/html; charset=iso-8859-1\n\n";
    }
    return $header;
}

function mail_attachment( $attachment, $boundary ) {
    if (!is_array($attachment)) {
        $attachment_file = "--" . $boundary . "\n";
        $filename        = basename($attachment);
        $h               = fopen($attachment, 'rb');
        if (is_readable("../.." . $attachment)) {
            $filecontents = fread($h, filesize("../.." . $attachment));
        } else {
            $filecontents = fread($h, filesize($attachment));
        }
        $file_base64 = chunk_split(base64_encode($filecontents));

        $attachment_file .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$filename\"\n";
        $attachment_file .= "Content-Transfer-Encoding: base64\n";
        $attachment_file .= "Content-Disposition: attachment; " .
            "filename=$filename\n\n";
        $attachment_file .= $file_base64;
        $attachment_file .= "\n";
        $text .= "--$boundary--";
    } else {
        for ($x = 0; $x < count($attachment); $x++) {
            $attachment_file .= "--" . $boundary . "\n";
            $file     = fopen($attachment[$x], "rb");
            $data     = fread($file, filesize($attachment[$x]));
            $filename = basename($attachment[$x]);
            fclose($file);
            $data = chunk_split(base64_encode($data));
            $attachment_file .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$filename\"\n" .
                "Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" .
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
    }
    $attachment_file .= "--" . $boundary . "--\n";
    return $attachment_file;

}


// Speichert Bilder in flexibler Größe ab
function image_resize( $file, $save, $width, $height, $prop = TRUE ) {
    if (file_exists($file)) {

        $infos = @getimagesize($file);
        // Neue Maße berechnen unter beibehaltung der Proportionen
        if ($prop) {
            $iWidth  = $infos[0];
            $iHeight = $infos[1];
            $iRatioW = $width / $iWidth;
            $iRatioH = $height / $iHeight;
            if ($iRatioW > 1 && $iRatioH > 1) {
                $iNewW = $iWidth;
                $iNewH = $iHeight;
            } else {
                if ($iRatioW < $iRatioH) {
                    $iNewW = round($iWidth * $iRatioW);
                    $iNewH = round($iHeight * $iRatioW);
                } else {
                    $iNewW = round($iWidth * $iRatioH);
                    $iNewH = round($iHeight * $iRatioH);
                }
            }

        } else {
            $iNewW = $width;
            $iNewH = $height;
        }
        // Übergebnes Bild in Variable lesen
        switch ($infos[2]) {
            case 1:
                if (detect_animated_gif($file)) {
                    $is_animated = TRUE;
                } else {
                    $curr_img         = imagecreatefromgif($file);
                    $set_transparency = TRUE;
                }
                break;
            case 2:
                $curr_img = imagecreatefromjpeg($file);
                break;
            case 3:
                $curr_img         = imagecreatefrompng($file);
                $set_transparency = TRUE;
                break;
        }
        if (!$is_animated) {

            // Neues Bild mit den berechneten Maßen erzeugen
            $new_img = imagecreatetruecolor($iNewW, $iNewH);
            // Transparenz für PNG- und GIF-Bilder erhalten
            if ($set_transparency) {
                imagealphablending($new_img, FALSE);
                imagesavealpha($new_img, TRUE);
            }

            // Bildgröße umwandeln
            imagecopyresampled($new_img, $curr_img, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);

            // Umgewandeltes Bild abspeichern
            switch ($infos[2]) {
                case 1:
                    @unlink($save);
                    imagegif($new_img, $save);
                    break;
                case 2:
                    @unlink($save);
                    imagejpeg($new_img, $save, 95);
                    break;
                case 3:
                    @unlink($save);
                    imagepng($new_img, $save, 4);
                    break;
            }
        } else {
            @unlink($save);
            copy($file, $save);
        }
    }
}


function filename_validation( $_filename, $_ending = FALSE ) {
    if (!is_string($_filename)) {
        return FALSE;
    }
    if (!is_bool($_ending) && !is_array($_ending) && !is_string($_ending)) {
        return FALSE;
    }
    $_filename = strtolower($_filename);
    $replace   = array(
        '/ä/'              => 'ae',
        '/ü/'              => 'ue',
        '/ö/'              => 'oe',
        '/ß/'              => 'ss',
        '/\040/'           => '_',
        '/[^a-z0-9_\.\-]/' => ''
    );
    $_filename = preg_replace(array_keys($replace), array_values($replace), $_filename);
    $regex     = '';
    if (is_array($_ending)) {
        if (!deleteArrayElement($_ending, '')) {
            $regex = '(\.(' . implode('|', $_ending) . '))$';
        } else {
            $regex = '([\.(' . implode('|', $_ending) . ')]?)$';
        }
        $regex = '(\.(' . implode('|', $_ending) . '))$';
    } elseif (is_string($_ending)) {
        $regex = '\.' . $_ending;
    } elseif ($_ending) {
        $regex = '\.[a-z0-9_\.\-]+';
    }
    if (preg_match('/^[a-z0-9_\.\-]+' . $regex . '/', $_filename)) {
        return $_filename;
    } else {
        return FALSE;
    }
}


function detect_animated_gif( $filename ) {
    if ($fh = @fopen($filename, 'rb')) {
        $count = 0;
        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); //read 100kb at a time
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
        }
        fclose($fh);
    }
    if ($count > 1) {
        return (TRUE);
    }
}

// Gibt CSS-Klasse für bestimmte Dateitypen zurück
function get_button_file_typ( $filename ) {
    $filename_array = explode('.', $filename);
    $file_typ       = $filename_array[(count($filename_array) - 1)];
    switch ($file_typ) {
        case 'pdf':
            return "button_pdf";
            break;
        case 'doc':
        case 'docx':
        case 'txt':
            return "button_doc";
            break;
        case 'xls':
        case 'xlsx':
        case 'csv':
        case 'rtf':
            return "button_xls";
            break;
        case 'zip':
        case 'rar':
            return "button_zip";
            break;
        case 'jpg':
        case 'gif':
        case 'bmp':
        case 'tif':
        case 'eps':
        case 'png':
            return "button_img";
            break;
        default:
            return "";
            break;
    }
}

// Gibt CSS-Klasse für bestimmte Dateitypen zurück
function get_button_file_typ_new( $filename ) {
    $filename_array = explode('.', $filename);
    $file_typ       = $filename_array[(count($filename_array) - 1)];
    switch ($file_typ) {
        case 'pdf':
            return "pdf-o";
            break;
        case 'doc':
        case 'docx':
            return "word-o";
            break;
        case 'txt':
            return "text-o";
            break;
        case 'xls':
        case 'xlsx':
        case 'csv':
        case 'rtf':
            return "excel-o";
            break;
        case 'zip':
        case 'rar':
            return "archive-o";
            break;
        case 'jpg':
        case 'gif':
        case 'bmp':
        case 'tif':
        case 'eps':
        case 'png':
            return "image-o";
            break;
        case 'mp3':
            return "audio-o";
            break;
        case 'mp4':
        case 'webm':
        case 'ogg':
        case 'm4v':
            return "movie-o";
            break;
        case 'ppt':
        case 'pptx':
            return "powerpoint-o";
            break;
        default:
            return "o";
            break;
    }
}

// Generiert Passwort
function generate_password( $zeichen = 8 ) {
    $chars = array_merge(
        range(0, 9),
        range('a', 'z'),
        range('A', 'Z')
    );
    shuffle($chars);
    return implode('', array_slice($chars, 0, $zeichen));
}

function delete_old_visitors()
{
    $query = "DELETE FROM 
		main_visitor 
	WHERE 
	(
		session_date != '0000-00-00 00:00:00'
	  AND
		session_date IS NOT NULL
	  AND
		valid_until != '0000-00-00 00:00:00'
	  AND
		valid_until IS NOT NULL
	  AND
		valid_until <= NOW()	
	  AND
		nav_login != 1
	)
	OR
	(
		session_date != '0000-00-00 00:00:00'
	  AND
		session_date IS NOT NULL
	  AND
		(main_user_id IS NULL OR main_user_id = 0)
	  AND
		(main_admin_user_id IS NULL OR main_admin_user_id = 0)
	  AND
		session_date <= DATE_SUB(NOW(),INTERVAL 90 MINUTE)	
	  AND
		nav_login != 1	
	)
	OR
	(
		session_date != '0000-00-00 00:00:00'
	  AND
		session_date IS NOT NULL
	  AND
		(
			remember_token IS NULL
		  OR
			remember_token = ''
		)
	  AND
		session_date <= DATE_SUB(NOW(),INTERVAL 90 MINUTE)	
	  AND
		nav_login != 1		
	)
	OR
	(
		nav_login = 1
	  AND
		(session_date <= DATE_SUB(NOW(),INTERVAL 90 MINUTE) OR session_date IS NULL OR session_date = '0000-00-00 00:00:00')
	)";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

// Gibt den Datensatz des über die Session ermittelten Besuchers wieder
function get_visitor() {

    if ($_GET['sid'] != '') {
        $query  = "SELECT * FROM main_visitor WHERE session_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_GET['sid']) . "' AND (remember_token = '' OR remember_token IS NULL)";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (mysqli_num_rows($result) == 1) {
            $row         = mysqli_fetch_assoc($result);
            $countquery  = "SELECT * FROM main_visitor WHERE session_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],session_id()) . "' AND (remember_token = '' OR remember_token IS NULL)";
            $countresult = mysqli_query($GLOBALS['mysql_con'], $countquery);
            if (mysqli_num_rows($countresult) > 0) {
                $query = "DELETE FROM main_visitor WHERE session_id = '" . $_GET['sid'] . "' AND (remember_token = '' OR remember_token IS NULL)";
                mysqli_query($GLOBALS['mysql_con'], $query);
                $oldvisitor = mysqli_fetch_assoc($countresult);
                $query      = "UPDATE main_visitor 
						  SET frontend_login = TRUE, cookie_only = FALSE, session_date  = NOW(), main_user_id = '" . $row['main_user_id'] . "'
						  WHERE id = '" . $oldvisitor['id'] . "'";
                mysqli_query($GLOBALS['mysql_con'], $query);
            } else {
                $query = "UPDATE main_visitor SET session_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],session_id()) . "' WHERE id = " . $row['id'];
                mysqli_query($GLOBALS['mysql_con'], $query);
            }
        }
    }
    $query  = "SELECT * FROM main_visitor WHERE session_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],session_id()) . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if ((!empty($_POST['remember_login']) && ($_POST['remember_login'] == 'on')) || !empty($GLOBALS['visitor']['remember_token'])) {
        $cookie_timeout = COOKIE_DAYS_VALID * 24 * 60 * 60;
        setcookie('sid' . $GLOBALS['site']['code'], session_id(), time() + $cookie_timeout, '/', NULL, NULL, true);
    } else {
        $cookie_timeout = 90 * 60 * 60;
        setcookie('sid' . $GLOBALS['site']['code'], session_id(), NULL, '/', NULL, NULL, true);
    }
    if (@mysqli_num_rows($result) != 0) {
        $visitor         = @mysqli_fetch_array($result);
        $query           = "UPDATE main_visitor SET session_date = NOW() WHERE id = '" . $visitor["id"] . "'";
        $_SESSION['vid'] = $visitor['id'];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        return $visitor;
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipv4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? $ip : '';
        $regexCount = null;
        $ipv4anon = preg_replace('/(\\d{1,3})\\.(\\d{1,3})\\.(\\d{1,3}).*/', '$1.$2.$3.0', $ipv4, -1, $regexCount);
        $ipv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $ip : '';
        $ipv6sections = [];
        $ipv6anon = implode(':',explode(':',$ipv6,2));

        $query = "INSERT INTO main_visitor (id,session_id,last_ipv4_anon,last_ipv6_anon,session_date,valid_until) VALUES (NULL,'" . mysqli_real_escape_string($GLOBALS['mysql_con'],session_id()) . "','".$ipv4anon."','".$ipv6anon."',NOW(),DATE_ADD(NOW(),INTERVAL 30 DAY))";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            $id      = mysqli_insert_id($GLOBALS['mysql_con']);
            $query   = "SELECT * FROM main_visitor WHERE id='" .$id."'";
            $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
            $visitor = @mysqli_fetch_assoc($result);
            return $visitor;
        } else {
            die('Es konnte kein Besucher-Datensatz erzeugt oder ermittelt werden. Bitte wenden Sie sich an den Support. Fehler: ' . mysqli_error($GLOBALS['mysql_con']));
        }
    }
}

// Holt Datensatz des Admin-Benutzers anhand eines Besuchereintrags

function get_admin_user( $visitor ) {
    if ($visitor["admin_login"]) {
        $query  = "SELECT main_admin_user.* FROM main_visitor LEFT join main_admin_user ON main_admin_user.id = main_visitor.main_admin_user_id WHERE main_visitor.id = '" . $visitor["id"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $admin_user = @mysqli_fetch_array($result);
            return $admin_user;
        }
    }

    return FALSE;
}

function update_visitor_data( $_data = array() ) {
    $query  = "select data from main_visitor where id = " . $GLOBALS["visitor"]['id'];
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $visitordata = @mysqli_fetch_array($result);
        $data        = unserialize($visitordata['data']);

        if (is_array($data) && count($data)) {
            $_data = array_merge($data, $_data);
        }
    }

    $data                       = serialize($_data);
    $query                      = "UPDATE main_visitor set data = '" . $data . "' WHERE id = " . $GLOBALS["visitor"]['id'];
    $GLOBALS["visitor"]['data'] = $data;
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function get_visitor_data() {
    $data = unserialize($GLOBALS["visitor"]['data']);
    if ($data == "") {
        return array();
    }

    return $data;
}

// Ermittelt den Sprachcode anhand des Browsers des Besuchers
function lang_getfrombrowser() {
    $language         = language_getbyid($GLOBALS["site"]["std_main_language_id"]);
    $default_language = $language["code"];
    $query            = "SELECT code FROM main_language";
    $result           = @mysqli_query($GLOBALS['mysql_con'], $query);
    $i                = 0;
    if (@mysqli_num_rows($result) > 0) {
        while ($language = @mysqli_fetch_array($result)) {
            $allowed_languages[$i] = $language["code"];
        }
        $strict_mode   = FALSE;
        $lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        if (empty($lang_variable)) {
            return $default_language;
        }
        $accepted_languages = preg_split('/,\s*/', $lang_variable);
        $current_lang       = $default_language;
        $current_q          = 0;
        foreach ($accepted_languages as $accepted_language) {
            $res = preg_match('/^([a-z]{1,8}(?:-[a-z]{1,8})*)' .
                '(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);
            if (!$res) {
                continue;
            }
            $lang_code = explode('-', $matches[1]);
            $lang_quality = 1.0;
            if (isset($matches[2])) {
                $lang_quality = (float)$matches[2];
            }
            while (count($lang_code)) {
                if (in_array(strtolower(join('-', $lang_code)), $allowed_languages)) {
                    if ($lang_quality > $current_q) {
                        $current_lang = strtolower(join('-', $lang_code));
                        $current_q    = $lang_quality;
                        break;
                        echo $current_lang . " " . $current_q . " - ";
                    }
                }
                if ($strict_mode) {
                    break;
                }
                array_pop($lang_code);
            }
        }
        return $current_lang;
    }
}

// Wandelt ein Deutsch formatiertes Datum in SQL-Format um
function datetosql( $date ) {
    if ($date <> '') {
        $newdate = date("Y-m-d", strtotime($date));
        return "'" . $newdate . "'";
    } else {
        return "NULL";
    }
}

// Wandelt ein SQL-Datum in das deutsche Format um
function datefromsql( $date ) {
    if ($date <> '') {
        $newdate = date("d.m.Y", strtotime($date));
        return $newdate;
    } else {
        return "";
    }
}
// Wandelt ein Deutsch formatiertes Datum in SQL-Format um
function datetimetosql( $date ) {
    if ($date <> '') {
        $newdate = date("Y-m-d H:i", strtotime($date));
        return "'" . $newdate . "'";
    } else {
        return "NULL";
    }
}

// Wandelt ein SQL-Datum in das deutsche Format um
function datetimefromsql( $date ) {
    if ($date <> '') {
        $newdate = date("d.m.Y H:i", strtotime($date));
        return $newdate;
    } else {
        return "";
    }
}

// Funktion zum anzeige eine Tabelle aus einem SQL-Query
function linklist( $result, $formname = "linklist", $format = NULL, $inputname = "input_id", $dblclick_action = "edit", $sortable = FALSE, $sortableUpdateAction = 'update_sortorder' ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    if ((int)$GLOBALS['shop']['id'] > 0) {
        linklist2($result, $formname, $format, $inputname);
    } else {

        $num_rows = @mysqli_num_rows($result);
        if ($num_rows >= 1) {
            $firstrow     = TRUE;
            $sortableText = $sortable === TRUE ? ' sortableTable' : '';
            echo "  <table data-sortableupdateaction=\"{$sortableUpdateAction}\" cellpadding=0 cellspacing=0 border=0 class=\"linklist" . $sortableText . "\">\n";
            while ($row = @mysqli_fetch_array($result, 1)) {
                if ($firstrow) {
                    // insert header
                    @reset($format);
                    echo "    <tr>\n";
                    $counter = 1;
                    foreach ($row as $key => $value) {
                        if ($key == "option") {
                            @next($format);
                            continue;
                        }

                        $curr_format = ($key <> "id") ? @current($format) : "option";

                        echo "      " . format_key($key, $inputname, $curr_format) . "\n";

                        if ($sortable === TRUE && $counter == 2) {
                            echo "      " . format_key("", "") . "\n";
                        }

                        @next($format);
                        $counter++;
                    }
                    echo "    </tr>\n";

                    // insert seperator
                    @reset($format);
                    echo "    <tr>\n";
                    foreach ($row as $key => $value) {
                        $curr_format = ($key <> "id") ? @current($format) : "option";
                        echo "      " . format_seperator($key) . "\n";
                        @next($format);
                    }
                    echo "    </tr>\n";

                    $firstrow = FALSE;
                }

                $markCheckedRow = FALSE;
                if (isset($_REQUEST['linklist_form'])) {
                    if ($formname == $_REQUEST['linklist_form']) {
                        $markCheckedRow = TRUE;
                    }
                } else {
                    $markCheckedRow = TRUE;
                }

                if ($markCheckedRow === TRUE) {
                    $checkedRow = (isset($_REQUEST[$inputname]) && $row['id'] == $_REQUEST[$inputname]) ? 'true' : 'false';
                }

                echo "    <tr id=\"linklistid_{$row['id']}\" class=\"linklist_content\" data-checked=\"" . $checkedRow . "\" data-inputname=\"" . $inputname . "\" data-action=\"" . $dblclick_action . "\" data-inputid=\"{$row['id']}\">\n";
                @reset($format);
                $counter = 1;
                foreach ($row as $key => $value) {


                    $curr_format = ($key <> "id") ? @current($format) : "option";
                    if ($key == "option") {
                        @next($format);
                        continue;
                    }

                    echo "      " . format_value($value, $inputname, $curr_format) . "\n";

                    if ($sortable === TRUE && $counter == 2) {
                        echo "      " . format_value("", "sorticon", "sort") . "\n";
                    }

                    @next($format);
                    $counter++;
                }
                echo "    </tr>\n";
            }
            echo "  </table>\n";
        } else {
            echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
        }
    }
}

// Funktion zum anzeige eine Tabelle aus einem SQL-Query für shop
function linklist2( $result, $formname = "linklist", $format = NULL, $inputname = "input_id" ) {

    $num_rows = @mysqli_num_rows($result);
    if ($num_rows >= 1) {
        $firstrow          = TRUE;
        $input_arr_counter = 0;
        echo "  <div class=\"linklist table_area\">\n";
        while ($row = @mysqli_fetch_array($result, 1)) {
            if ($firstrow) {
                @reset($format);
                echo "    <div class='table_row table_header'>\n";
                foreach ($row as $key => $value) {

                    $curr_format = ($key <> "id") ? @current($format) : "option";

                    echo "      " . format_key2($key, $inputname, $curr_format) . "\n";
                    @next($format);
                }
                echo "    </div>\n";
                $firstrow = FALSE;
            }
            $input_arr_text = ($num_rows > 1) ? "[" . $input_arr_counter . "]" : "";
            echo "<div class='table_row table_body' onClick=\"document." . $formname . "." . $inputname . $input_arr_text . ".checked = true; \">\n";
            $input_arr_counter++;
            @reset($format);
            foreach ($row as $key => $value) {
                $curr_format = ($key <> "id") ? @current($format) : "option";
                echo "      " . format_value2($value, $inputname, $curr_format, $key) . "\n";
                @next($format);
            }
            echo "</div>\n";
        }
        echo "</div>\n";
    } else {
        $style = ($formname == "form_sales_return_history_list" ? " style=\"margin-top: 0 !important;\"" : "");
        echo "<div class=\"emptybox\"".$style.">Keine Datens&auml;tze vorhanden</div>";
    }
    switch ($formname) {
        case "form_shipment_address_list":
            $action = 'edit_shipment_address&action_id=edit';
            break;
        case "form_shop_user_list":
            $action = 'edit_shop_user&action_id=edit';
            break;
        case "form_order_history_list":
            $action = 'order_history&action_id=card';
            break;
        case "form_sales_contract_history_list":
            $action = 'contract_history&action_id=card';
            break;
        case "form_sales_invoice_history_list":
            $action = 'sales_invoice_history&action_id=card';
            break;
        case "form_sales_shipment_history_list":
            $action = 'sales_shipment_history&action_id=card';
            break;
        case "form_sales_cr_memo_history_list":
            $action = 'sales_cr_memo_history&action_id=card';
            break;
        case "form_sales_return_history_list":
            $action = 'sales_return_history&action_id=card';
            break;
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#<?=$formname;?> .table_body').click(function(){
                $('#<?=$formname;?>').attr('action','?action=<?=$action?>');
                $('#<?=$formname;?>').submit();
            });
        });
    </script>

    <?php
}

function format_value( $value, $inputname, $format = NULL ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    switch ($format) {
        case 'text':
            return "<td class=\"" . $format . "\"><div>" . $value . "</div></td>";
            break;
        case 'date':
            return "<td class=\"" . $format . "\"><div>" . $value . "</div></td>";
            break;
        case 'euro':
            return "<td class=\"" . $format . "\"><div style='text-align: right;'>" . number_format($value, 2, ',', '.') . " &euro;</div></td>";
            break;
        case 'decimal':
            return "<td class=\"" . $format . "\"><div>" . number_format($value, 2, ',', '.') . "</div></td>";
            break;
        case 'integer':
            return "<td class=\"" . $format . "\"><div>" . number_format($value, 0, '', '') . "</div></td>";
            break;
        case 'boolean':
            return ($value == 1) ? "<td class=\"" . $format . "\"><div>" . $translation->get("yes") . "</div></td>" : "<td><div>" . $translation->get("no") . "</div></td>";
            break;
        case 'boolean_active':
            return ($value == 1) ? "<td class=\"" . $format . "\"><div>" . $translation->get("active") . "</div></td>" : "<td><div>" . $translation->get("inactive") . "</div></td>";
            break;
        case 'option':
            return "<td style=\"width: 20px;display:none;\" class=\"" . $format . "\"></td>";
            break;
        case 'sort':
            return "<td width=\"100\" class=\"sorticon\"><div>" . $value . "</div></td>";
            break;
        default:
            return "<td class=\"" . $format . "\"><div>" . $value . "</div></td>";
            break;
    }
}

function format_value2( $value, $inputname, $format = NULL ,$key = "" ) {
    if($key != "") {
        $label = "<span class=\"table_cell_label\">" . $key . "</span> ";
    }else{
        $label = "";
    }
    switch ($format) {
        case 'text':
            return "<div class=\"table_cell " . $format . "\">".$label . "<span>".htmlspecialchars($value, ENT_QUOTES, "UTF-8") . "</span></div>";
            break;
        case 'date':
            return "<div class=\"table_cell text-right datepicker\">" . $label . "<span>".htmlspecialchars($value, ENT_QUOTES, "UTF-8") . "</span></div>";
            break;
        case 'euro':
            return "<div class=\"table_cell text-right " . $format . "\">" . $label . "<span>".number_format($value, 2, ',', '.') . " &euro;</span></div>";
            break;
        case 'decimal':
            return "<div class=\"table_cell text-right " . $format . "\">" . $label . "<span>".number_format($value, 2, ',', '.') . "</span></div>";
            break;
        case 'integer':
            return "<div class=\"table_cell text-right " . $format . "\">" . $label . "<span>".number_format($value, 0, '', '') . "</span></div>";
            break;
        case 'boolean':
            return ($value == 1) ? "<div class=\"table_cell " . $format . "\">".$label."<span>Ja</span></div>" : "<div class=\"table_cell " . $format . "\">".$label."<span>Nein</span></div>";
            break;
        case 'boolean_active':
            return ($value == 1) ? "<div class=\"table_cell " . $format . "\">".$label."<span>Aktiv</span></div>" : "<div class=\"table_cell " . $format . "\">".$label."<span>Inaktiv</span></div>";
            break;
        case 'option':
            $checked = ($value == $_POST[$inputname]) ? " checked=\"checked\"" : "";
            return "<div style=\"width: 20px; display:none;\" class=\"table_cell " . $format . "\"><input id=\"" . $inputname . "\" name=\"" . $inputname . "\" type=\"radio\" value=\"" . $value . "\"" . $checked . "/></div>";
            break;
        default:
            return "<div class=\"table_cell " . $format . "\">" . $label . "<span>".htmlspecialchars($value, ENT_QUOTES, "UTF-8") . "</span></div>";
            break;
    }
}

function format_key( $key, $inputname, $format = NULL ) {
    switch ($format) {
        case 'option':
            return "<td style=\"display:none;\" class=\"" . $format . "\"><div>&nbsp;</div></td>";
            break;
        default:
            return "<td class=\"" . $format . "\"><div>" . $key . "</div></td>";
            break;
    }
}

function format_key2( $key, $inputname, $format = NULL ) {
    switch ($format) {
        case 'option':
            return "<div class=\"table_cell " . $format . "\" style=\"display:none\">&nbsp;</div>";
            break;
        case 'date' :
            return "<div class=\"table_cell text-right datepicker\">" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</div>";
            break;
        case 'euro':
            return "<div class=\"table_cell text-right ". $format . "\">" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</div>";
            break;
        case 'decimal':
            return "<div class=\"table_cell text-right ". $format . "\">" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</div>";
            break;
        case 'integer':
            return "<div class=\"table_cell text-right ". $format . "\">" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</div>";
            break;
        default:
            return "<div class=\"table_cell " . $format . "\">" . htmlspecialchars($key, ENT_QUOTES, "UTF-8") . "</div>";
            break;
    }
}

function format_seperator( $format ) {
    switch ($format) {
        case 'option':
            return "<td style=\"display:none;\"><div>&nbsp;</div></td>";
            break;
        default:
            return "<td class=\"linklist_seperator\">&nbsp;</td>";
            break;
    }
}

function button( $typ, $name, $formname, $action = "", $confirm = "", $promoted = TRUE ) {
    $confirm_start_text = "";
    $confirm_end_text   = "";
    if ($confirm <> "") {
        $confirm_start_text = "if(confirm('" . $confirm . "')) { ";
        $confirm_end_text   = " }";
    }

    if ($typ == "") {
        $typ = "inhalt_collection graybox";
    }

    $_promoted = $promoted === TRUE ? 'promoted' : 'not_promoted';
    if ($GLOBALS['shop_language']['code'] != '') {
        switch ($typ) {
            case "reset":
                echo "<a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . "document." . $formname . ".reset(); return false;" . $confirm_end_text . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a>\n";
                break;
            default:
                echo "<a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . "document." . $formname . ".action='" . $action . "'; document." . $formname . ".submit(); return false" . $confirm_end_text . ";\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a>\n";
                break;
        }
    } else {
        switch ($typ) {
            case "reset":
                echo "<li class=\"" . $_promoted . "\"> <a data-formname=\"" . $formname . "\" class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"setCurrentToolbarClicked(this);" . $confirm_start_text . "document." . $formname . ".reset(); return false;" . $confirm_end_text . "\">" . $name . "</li></a>\n";
                break;
            default:
                echo "<li class=\"" . $_promoted . "\"> <a data-formname=\"" . $formname . "\" class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"setCurrentToolbarClicked(this);" . $action . ";\">" . $name . "</a></li>\n";
                break;
        }
    }
}

function button2( $typ, $name, $formname, $action = "", $confirm = "" ) {
    if ($confirm <> "") {
        $confirm_start_text = "if(confirm('" . $confirm . "')) { ";
        $confirm_end_text   = " }";
    }
    switch ($typ) {
        case "reset":
            echo "<li><a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . "document." . $formname . ".reset(); return false;" . $confirm_end_text . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a></li>\n";
            break;
        default:
            echo "<li><a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . "document." . $formname . ".action='" . $action . "'; document." . $formname . ".submit(); return false" . $confirm_end_text . ";\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a></li>\n";
            break;
    }
}

function button_finish( $typ, $name, $formname, $action = "", $confirm = "" ) {
    if ($confirm <> "") {
        $confirm_start_text = "if(confirm('" . $confirm . "')) { ";
        $confirm_end_text   = " }";
    }
    switch ($typ) {
        case "reset":
            echo "<a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . " pay_other(); return false;" . $confirm_end_text . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a>\n";
            break;
        default:
            echo "<a class=\"button_" . $typ . "\" href=\"javascript:void(0);\" onclick=\"" . $confirm_start_text . " pay_other(); return false" . $confirm_end_text . ";\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</a>\n";
            break;
    }
}

function set_page_link(
    $sitepart_id,
    $header_id,
    $collection_list = 0,
    $collection_id = 0,
    $collection_setup_id = 0,
    $collection_view_type = 0,
    $collection_items = 0,
    $collection_page_list_id = 0,
    $main_page_group = 0,
    $main_page_group_code = "",
    $main_page_group_name = "",
    $main_page_group_link = ""
) {

    $sorting = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM main_page_link WHERE main_page_id = '" . $_REQUEST["input_page_id"] . "'"));
    $sorting = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;

    $layout_area_id = 0;
    $active         = 0;

    $sessiondata = get_visitor_data();

    if (isset($sessiondata['page_options']['page_option_layoutarea'])) {
        $layout_area_id = $sessiondata['page_options']['page_option_layoutarea'];
    }

    if (isset($sessiondata['page_options']['page_option_active'])) {
        $active = $sessiondata['page_options']['page_option_active'];
    }

    $query = "INSERT INTO main_page_link (
			main_page_id, 
			main_sitepart_id, 
			main_sitepart_header_id,
			main_collection_list, 
			main_collection_id, 
			main_collection_setup_id, 
			main_collection_page_list_id,
			main_collection_items,
			main_collection_view_type,
			main_page_group,
			main_page_group_code,
			main_page_group_name,
			main_page_group_link,
			modified_user, 
			modified_date, 
			layout_area_id, 
			active, 
			sorting
		)
		VALUES (
			'" . $_REQUEST["input_page_id"] . "',
			'" . $sitepart_id . "',
			'" . $header_id . "',
			'" . $collection_list . "',
			'" . $collection_id . "',
			'" . $collection_setup_id . "',
			'" . $collection_page_list_id . "',
			'" . $collection_items . "',
			'" . $collection_view_type . "',
			'" . $main_page_group . "',
			'" . $main_page_group_code . "',
			'" . $main_page_group_name . "',
			'" . $main_page_group_link . "',
			" . (int)$GLOBALS["admin_user"]['id'] . ",
			" . time() . ",
			" . $layout_area_id . ",
			" . $active . ",
			" . $sorting . "
		)
	";

    @mysqli_query($GLOBALS['mysql_con'], $query);
    $input_id = mysqli_insert_id($GLOBALS['mysql_con']);

    $query = "UPDATE main_page set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = '" . (int)$_REQUEST["input_page_id"]."'";
    @mysqli_query($GLOBALS['mysql_con'], $query);

    $_REQUEST['input_id']              = $input_id;
    $_REQUEST['get_real_page_link_id'] = 0;

    update_visitor_data(array(
        'page_options' => array()
    ));
}

function set_component_link( $sitepart_id, $header_id, $collection_list = 0, $collection_id = 0, $collection_setup_id = 0, $collection_view_type = 0, $collection_items = 0, $collection_page_list_id = 0 ) {
    $sorting = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM main_component_link WHERE main_component_id = '" . $_REQUEST["input_component_id"]."'"));
    $sorting = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;

    $query = "INSERT INTO main_component_link (
		main_component_id, 
		main_sitepart_id, 
		main_sitepart_header_id, 
		main_collection_list, 
		main_collection_id, 
		main_collection_setup_id, 
		main_collection_page_list_id,
		main_collection_items,
		main_collection_view_type,
		modified_user, 
		modified_date, 
		sorting
		) VALUES (
			'" . $_REQUEST["input_component_id"] . "',
			'" . $sitepart_id . "',
			'" . $header_id . "',
			'" . $collection_list . "',
			'" . $collection_id . "',
			'" . $collection_setup_id . "',
			'" . $collection_page_list_id . "',
			'" . $collection_items . "',
			'" . $collection_view_type . "',
			" . (int)$GLOBALS["admin_user"]['id'] . ",
			" . time() . ",
			" . $sorting . "
		)
	";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    $input_id = mysqli_insert_id($GLOBALS['mysql_con']);

    $query = "UPDATE main_component set modified_date = '" . date('Y-m-d H:i:s',time()) . "', modified_user = " . (int)$GLOBALS["admin_user"]['id'] . " where id = '" . (int)$_REQUEST["input_component_id"]."'";
    @mysqli_query($GLOBALS['mysql_con'], $query);

    $_REQUEST['input_id']              = $input_id;
    $_REQUEST['get_real_page_link_id'] = 0;
}

function is_page_edit() {
    if ($_REQUEST["input_page_id"] && $_REQUEST["input_page_id"] != "") {
        return TRUE;
    } else {
        return FALSE;
    }
}

function is_component_edit() {
    if ($_REQUEST["input_component_id"] && $_REQUEST["input_component_id"] != "") {
        return TRUE;
    } else {
        return FALSE;
    }
}

function is_collection_edit() {
    $sessiondata = get_visitor_data();

    if ($sessiondata['collection_options']['collection_edit'] === TRUE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Erzeugt eine selectbox zum auswaehlen von input-typen bei einer kollektion
 *
 * @access public
 *
 * @param string $name       Label
 * @param string $selectname name der selectbox
 *
 * @return void
 */
function create_collection_select( $collections, $name, $selectname = "input_choose_collection", $input_line, $disabled = FALSE, $chooseText = FALSE, $onchange = "" ) {

    $translation  = \DynCom\dc\common\classes\Registry::get("translation");
    $siteparts    = \DynCom\dc\common\classes\Siteparts::get();
    $disabledText = "";
    if ($disabled === TRUE) {
        $disabledText = 'disabled="disabled"';
    }

    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select " . $disabledText . " onchange=\"" . $onchange . "\" class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    if ($chooseText === TRUE) {
        echo "<option value=\"\">" . $translation->get("please_choose") . "</option>";
    }

    foreach ($collections as $setupId => $setupData) {
        $selected = '';

        if ($setupId == $input_line['main_collection_setup_id'] || $_REQUEST['filter_collection_setup_id'] == $setupId) {
            $selected = 'selected="selected"';
        }
        echo "<option " . $selected . " value=\"" . $setupId . "\">" . $setupData['description'] . "</option>";
    }

    echo "</select>";
}

function create_contactform_select( $contactforms, $name, $selectname = "input_choose_collection", $input_line, $disabled = FALSE ) {
    $translation  = \DynCom\dc\common\classes\Registry::get("translation");
    $siteparts    = \DynCom\dc\common\classes\Siteparts::get();
    $disabledText = "";
    if ($disabled === TRUE) {
        $disabledText = 'style="display:none;"';
    }

    echo "<div " . $disabledText . " class=\"registration_select\">";

    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    foreach ($contactforms as $setupId => $setupData) {
        $selected = '';

        if ($setupId == $input_line['registration_contactform_id'] || $_REQUEST['registration_contactform_id'] == $setupId) {
            $selected = 'selected="selected"';
        }
        echo "<option " . $selected . " value=\"" . $setupId . "\">" . $setupData['description'] . "</option>";
    }

    echo "</select>";

    echo "</div></div>";
}

/**
 * Erzeugt eine selectbox zum auswählen von Seiten, die eine Kollektionsliste enthalten
 *
 * @access public
 *
 * @param string $name       Label
 * @param string $selectname name der selectbox
 *
 * @return void
 */
function create_page_with_collection_select( $collection_setup_id, $name, $selectname = "input_choose_collection", $input_line, $disabled = FALSE ) {

    if ((int)$collection_setup_id <= 0) {
        return;
    }

    $translation  = \DynCom\dc\common\classes\Registry::get("translation");
    $siteparts    = \DynCom\dc\common\classes\Siteparts::get();
    $disabledText = "";
    if ($disabled === TRUE) {
        $disabledText = 'disabled="disabled"';
    }

    $pageIds = array();
    $query   = "SELECT main_page_id FROM main_page_link WHERE main_collection_setup_id = '" . $collection_setup_id."'";
    $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result, 1)) {
        $pageIds[] = $row['main_page_id'];
    }

    if (count($pageIds) == 0) {
        echo "<p class=\"bold info\">Keine Seite mit diesem Kollektionstyp gefunden.</p>";
        return;
    }

    $pages  = array();
    $query  = "SELECT * FROM main_navigation WHERE forward_page_id IN (" . join(",", $pageIds) . ") AND forward_type = 1 AND main_language_id = " . $GLOBALS["language"]['id'];
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result, 1)) {
        $pages[$row['id']] = $row;
    }

    if (count($pages) == 0) {
        echo "<p class=\"bold info\">Keine Seite mit diesem Kollektionstyp gefunden.</p>";
        return;
    }

    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select " . $disabledText . " class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    foreach ($pages as $pageId => $pageData) {
        $selected = '';

        if ($pageId == $input_line[$selectname]) {
            $selected = 'selected="selected"';
        }
        echo "<option " . $selected . " value=\"" . $pageId . "\">" . $pageData['menu_name'] . "</option>";
    }

    echo "</select>";
}

function create_collection_preview_group_select( $collection_setup_id, $page_link_id, $active_groups = array() ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $query  = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = '" . $collection_setup_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 0) {
        return;
    }

    echo "<div class=\"clearfix\"></div>";

    echo "<h2>" . $translation->get("collection_groups") . "</h2>";
    echo '<table class="cardform" border="0" cellspacing="0" cellpadding="0">';
    while ($row = @mysqli_fetch_array($result)) {
        echo "<tr><td>";
        if (!in_array($row['id'], $active_groups)) {
            input($row['description'], "input_collection_group_" . $row["id"] . "_active", "checkbox", 0);
        } else {
            input($row['description'], "input_collection_group_" . $row["id"] . "_active", "checkbox", 1);
        }
        echo "</td><td></td></tr>";
    }
    echo '</table>';
}

function update_sitepart_changes( $sitepartId, $headerId, $delete = FALSE ) {

    $languageId = (int)$GLOBALS["language"]['id'];

    if ($delete === TRUE) {
        $query = "DELETE FROM main_sitepart_changes where main_language_id = " . $languageId . " and main_sitepart_id = " . (int)$sitepartId . " and main_sitepart_header_id = '" . (int)$headerId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        return;
    }

    // check if row exists
    $query = "SELECT id from main_sitepart_changes where main_language_id = '" . $languageId . "' and main_sitepart_id = '" . (int)$sitepartId . "' and main_sitepart_header_id = '" . (int)$headerId."'";

    $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
    $num_rows = @mysqli_num_rows($result);
    if ($num_rows >= 1) {
        $query = "update main_sitepart_changes SET 
			modified_date = '" . date('Y-m-d H:i:s',time()) . "',
			modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
		WHERE main_language_id = " . $languageId . " and main_sitepart_id = " . (int)$sitepartId . " and main_sitepart_header_id = '" . (int)$headerId."'";
    } else {
        $query = "insert into main_sitepart_changes (main_language_id, main_sitepart_id, main_sitepart_header_id, modified_date, modified_user) VALUES (
			" . $languageId . ",
			" . $sitepartId . ",
			'" . $headerId . "',
			" . time() . ",
			" . (int)$GLOBALS["admin_user"]['id'] . "
		)";
    }

    @mysqli_query($GLOBALS['mysql_con'], $query);
}


//Alte Input-funktion
//function input($name,$inputname,$typ,$value = NULL,$maxlength = NULL,$disabled = FALSE) {
//    echo "<div class=\"label\"><label for=\"" . $inputname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
//	$disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
//	$maxlength_text = ($maxlength <> '') ? " maxlength=\"" . $maxlength . "\"" : "";
//	switch($typ) {
//		case "smalltextarea": echo "<div class=\"input\"><textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"small\"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n"; break;
//		case "textarea": echo "<div class=\"input\"><textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"normal\"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n"; break;
//		case "file": echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"file\" /></div>\n"; break;
//		case "checkbox": $checked_text = ($value) ? "checked=\"checked\"" : ""; echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox\" type=\"checkbox\"" . $disabled_text . $checked_text . "/></div>\n"; break;
//		case "checkbox_with_formreload": $checked_text = ($value) ? "checked=\"checked\"" : ""; echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox\" type=\"checkbox\"" . $disabled_text . $checked_text . " onclick=\"this.form.submit();\"/></div>\n"; break;
//		case "password": echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code\" type=\"password\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div>\n"; break;
//		case "input_pref_delivery_date": echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /><img src=\"/layout/frontend/bluestar/img/calendar_icon.png\" width=\"15\" height=\"16\" onclick='fPopCalendar(\"date\")' /></div> \n"; break;
//		default: echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div>\n"; break;
//	}
//}

function input( $name, $inputname, $typ, $value = "on", $maxlength = NULL, $disabled = FALSE, $evaluate = FALSE, $script = "" ) {
    if ($evaluate && $value == "") {
        //$eval_string = "<font color=\"red\">Bitte fÃ¼llen Sie dieses Feld aus</font>";
        $typ = $typ . "_error";
    } else {
        //$eval_string = "";
    }
    $eval_string = "";
    if ($typ != "smalltext_cc_2_error" && $typ != "hidden") {
        echo "<div class=\"label\"><label for=\"" . $inputname . "\">" . $name . "</label></div>";
    }
    $disabled_text  = ($disabled) ? " disabled=\"disabled\"" : "";
    $maxlength_text = ($maxlength <> '') ? " maxlength=\"" . $maxlength . "\"" : "";
    $value = htmlspecialchars($value);
    if ($typ == "checkbox") {
        if ($value == 1) {
            $value = "on";
        } else if ($value == 0) {
            $value = "";
        }
    }
    //$checked_text = "on";

    switch ($typ) {
        case "smalltextarea":
            echo "<div class=\"input\"><textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"small\"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n";
            break;
        case "textarea":
            echo "<div class=\"input\"><textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"normal\"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n";
            break;
        case "smalltext_cn_1_error":
            echo "<div class=\"input_2\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_1":
            echo "<div class=\"input_2\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_2_error":
            echo "<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_2":
            echo "<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "file":
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"file\" /></div>\n";
            break;
        case "checkbox":
        case "checkbox_error":
            $checked_text = ($value == "on") ? "checked=\"checked\"" : "";
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox\" type=\"checkbox\"" . $disabled_text . $checked_text . " " . $script . "/>" . $eval_string . "</div>\n";
            break;
        case "checkbox_shop":
            $checked_text = ($value == "on") ? "checked=\"checked\"" : "";
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox_shop\" type=\"checkbox\"" . $disabled_text . $checked_text . " " . $script . "/>" . $eval_string . "</div>\n";
            break;
        case "checkbox_with_formreload":
            $checked_text = ($value) ? "checked=\"checked\"" : "";
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox\" type=\"checkbox\"" . $disabled_text . $checked_text . " onclick=\"this.form.submit();\"/>" . $eval_string . "</div>\n";
            break;
        case "checkbox_with_formreload_checked":
            $checked_text = ($value) ? "checked=\"checked\"" : "";
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox\" type=\"checkbox\"" . $disabled_text . $checked_text . " onclick=\"this.form.submit();\"/></div>\n";
            break;
        case "password":
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code\" type=\"password\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div>\n";
            break;
        case "date":
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"datepicker\" type=\"text\"" . $script . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div> \n";
            break;
        case "input_pref_delivery_date":
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\"  " . $script . " readonly /></div> \n";
            break;
        //case "input_pref_delivery_date": echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /><img src=\"/layout/frontend/haendler/img/calendar_icon.png\" width=\"15\" height=\"16\" onclick='fPopCalendar(\"date\")' /></div> \n"; break;
        case "hidden":
            echo "<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"hidden\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />\n";
            break;
        default:
            echo "<div class=\"input\"><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"" . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "</div>\n";
            break;
    }
}

function input_shop($name = '', $inputname = '', $typ = '', $value = "on", $maxlength = NULL, $disabled = FALSE, $evaluate = FALSE, $script = "", $required = false) {
    if ($evaluate && $value == "") {
        $error = "has-danger";
    }else{
        $error = "";
    }
    $eval_string = "";

    if ($typ != "smalltext_cc_2_error" && $typ != "smalltext_cn_2" && $name != "") {
        $label = "<label for=\"" . $inputname . "\">" . $name . "</label>";
    }else{
        $label = "";
    }
    $disabled_text  = ($disabled) ? " disabled=\"disabled\"" : "";
    $maxlength_text = ($maxlength <> '') ? " maxlength=\"" . $maxlength . "\"" : "";
    if ($typ == "checkbox") {
        if ($value == 1) {
            $value = "on";
        } else if ($value == 0) {
            $value = "";
        }
    }
    $reg_val = '';
    if ($required) {
        $req_val = " required";
    }
    $error .= $req_val;
    //$checked_text = "on";
    switch ($typ) {
        case "smalltextarea":
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"small \"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n";
            break;
        case "textarea":
            $breaks = array("<br />","<br>","<br/>");
            $value = str_ireplace($breaks, "\r\n", $value);
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<textarea name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"normal \"" . $disabled_text . $maxlength_text . ">" . $value . "</textarea></div>\n";
            break;
        case "smalltext_cn_1_error":
            echo "<div class=\"input_2 form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_1":
            echo "<div class=\"input_2 form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_2_error":
            echo "<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . " ".$error."\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "smalltext_cn_2":
            echo "<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . " ".$error."\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "\n";
            break;
        case "file":
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . "\" type=\"file\" /></div>\n";
            break;
        case "checkbox":
            $checked_text = ($value == "on") ? "checked=\"checked\"" : "";
            echo "<div class=\"input form-group ".$typ." ".$error." form-check ".$inputname."\"><label class='form-check-label'><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"form-check-input\" type=\"checkbox\"" . $disabled_text . $checked_text . " " . $script . "/>".$name."</label></div>\n";
            break;
        case "checkbox_action":
            $checked_text = ($value == "on") ? "checked=\"checked\"" : "";
            echo "<div class=\"input form-group ".$typ." ".$error." form-check\"><label class='form-check-label color-action'><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"form-check-input\" type=\"checkbox\"" . $disabled_text . $checked_text . " " . $script . "/>".$name."</label></div>\n";
            break;
        case "checkbox_shop":
            $checked_text = ($value == "on") ? "checked=\"checked\"" : "";
            echo "<div class=\"input form-group ".$typ." ".$error." form-check\"><label class='form-check-label'><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"form-check-input\" type=\"checkbox\"" . $disabled_text . $checked_text . " " . $script . "/>".$name."</label></div>\n";
            break;
        case "checkbox_with_formreload":
            $checked_text = ($value) ? "checked=\"checked\"" : "";
            echo "<div class=\"input form-group ".$typ." ".$error." form-check\"><label class='form-check-label'><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"form-check-input\" type=\"checkbox\"" . $disabled_text . $checked_text . " onclick=\"this.form.submit();\"/>".$name."</label></div>\n";
            break;
        case "checkbox_with_formreload_checked":
            $checked_text = ($value) ? "checked=\"checked\"" : "";
            echo "<div class=\"input form-group ".$typ." ".$error." form-check\"><label class='form-check-label'><input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"checkbox \" type=\"checkbox\"" . $disabled_text . $checked_text . " onclick=\"this.form.submit();\"/>".$name."</label></div>\n";
            break;
        case "password":
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"code \" type=\"password\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div>\n";
            break;
        case "date":
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"datepicker \" type=\"text\"" . $script . $disabled_text . $maxlength_text . " value=\"" . $value . "\" /></div> \n";
            break;
        case "input_pref_delivery_date":
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"datepicker \" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\"  " . $script . " readonly /></div> \n";
            break;
        default:
            echo "<div class=\"input form-group ".$typ." ".$error."\">".$label."<input name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"  " . $typ . "\" type=\"text\"" . $disabled_text . $maxlength_text . " value=\"" . $value . "\" " . $script . " />" . $eval_string . "</div>\n";
            break;
    }
}

function text( $name, $value ) {
    echo "<div class=\"label\"><label>" . $name . "</label></div>";
    echo "<div class=\"input\"><input type=\"text\" class=\"text\" disabled=\"disabled\" value=\"" . $value . "\" /></div>\n";
}

function input_select( $name, $inputname, $values, $value_names, $preselect = "", $disabled = FALSE, $auto_update = FALSE, $onchange = "" ) {
    $disabled_text   = ($disabled) ? " disabled=\"disabled\"" : "";
    $autoupdate_text = ($auto_update) ? " onchange=\"this.form.submit();\"" : "";
    echo "<div class=\"label\"><label for=\"" . $inputname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\">\n\t<select " . $onchange . " " . $disabled_text . $autoupdate_text . " name=\"" . $inputname . "\" id=\"" . $inputname . "\" class=\"select\">\n";
    FOR ($i = 0; $i < count($values); $i++) {
        echo "\t\t<option value=\"" . $values[$i] . "\"";
        IF ($values[$i] == $preselect) {
            echo " selected=\"selected\" ";
        }
        echo ">" . $value_names[$i] . "</option>\n";
    }
    echo "\t</select>\n</div>\n";
}

// Gibt die Meta-Tags der aktuellen Navigation als DIV-Boxen zurück
function get_search_tags( $navigation = '' ) {
    if ($navigation == '') {
        $navigation = $GLOBALS["navigation"];
    }
    if ($navigation["search_tags"] <> '') {
        echo "<div class=\"search_tags\">\n";
        $search_tags = explode(",", $navigation["meta_keywords"]);
        foreach ($search_tags as $value) {
            echo "<span>" . $value . "</span>\n";
        }
        echo "</div>\n";
    }
}

// Prüft ob das Projekt lokal oder auf dem Webserver läuft
function local_environment() {
    return ((strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN'));
}


function show_ck_editor( $content, $style, $css_path, $style_path, $name = "textcontent" ) {
    $path = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Registry.php');
    if (file_exists($path)) {
        require_once($path);
    }
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    if ($style == "dc") {
        echo("<textarea name=$name id=$name>" . $content . "</textarea>
			<script type='text/javascript'>
				var editor = CKEDITOR.replace( '$name',{
					customConfig: '/plugins/ckeditor/" . $style . "_config.js',
					contentsCss: ['/" . $css_path . "', '/plugins/ckeditor/plugins/fontawesome/font-awesome/css/font-awesome.min.css'],
	        		filebrowserBrowseUrl : '/plugins/ckfinder/ckfinder.html',
				 	filebrowserImageBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Images',
				 	filebrowserFlashBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Flash',
				 	filebrowserUploadUrl :
				 	   '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				 	filebrowserImageUploadUrl :
				 	   '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				 	filebrowserFlashUploadUrl : '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
					on: {
						save: function(evt) {
							if(jQuery('body').hasClass(\"ckeditor_maximized\")) {
								alert('" . $translation->get('ckeditor_save_error1') . "');
								return false;
							}

							setFormname(jQuery('#textcontent').closest('form').attr('id'));
							loadCard('save', true);
							return false;
						},
		
						maximize: function(e) {
							if(e.editor.getCommand('maximize').state == CKEDITOR.TRISTATE_ON){
				          		jQuery('body').addClass(\"ckeditor_maximized\");
				     		 } else {
				      			// minimized
				      			// remove special css from body
								jQuery('body').removeClass(\"ckeditor_maximized\");
				      		}
						}
					}
	    		} );
			</script>");
    } elseif ($style == "minimal") {
        echo("<textarea name=textcontent id=textcontent>" . $content . "</textarea>
			<script type='text/javascript'>
				var editor = CKEDITOR.replace( 'textcontent',{
					customConfig: '/plugins/ckeditor/" . $style . "_config.js',
				 	filebrowserImageBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Images',
				 	filebrowserImageUploadUrl :
				 	   '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
	    		} );
			</script>");
    } else {
        echo("<textarea name=textcontent id=textcontent>" . $content . "</textarea>
			<script type='text/javascript'>
				var editor = CKEDITOR.replace( 'textcontent',{
					customConfig: '/plugins/ckeditor/" . $style . "_config.js',
					stylesSet: 'my_styles:/" . $style_path . "',
					contentsCss: '/" . $css_path . "',
					filebrowserBrowseUrl : '/plugins/ckfinder/ckfinder.html',
				 	filebrowserImageBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Images',
				 	filebrowserFlashBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Flash',
				 	filebrowserUploadUrl :
				 	   '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
				 	filebrowserImageUploadUrl :
				 	   '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
				 	filebrowserFlashUploadUrl : '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	    		});
				editor.on('instanceReady', function (evt) { evt.editor.execCommand('maximize'); }); 
			</script>");
    }
}

//Gibt Längen- und Breitengrad als Array für als String mitgelieferte Adresse aus
function geoencode( $adress ) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://maps.google.com/maps/api/geocode/json?address=" . urlencode($adress) . "&sensor=false");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $json_data    = curl_exec($ch);
    $json_array   = json_decode($json_data, TRUE);
    $latlng_array = $json_array['results'][0]['geometry']['location'];

    return $latlng_array;
}

//Value von Checkboxen in 1 oder 0 umwandeln
function change_checkboxval_to_bool( $var ) {
    if ($var == "on") {
        return 1;
    } else {
        return 0;
    }
}

function create_meta_tags() {

    $pageData = $GLOBALS["page"];

    // Meta-Daten aus Sprache füllen
    $site_title       = $GLOBALS["navigation"]['menu_name'];
    $meta_description = $GLOBALS["language"]["meta_description"];
    $meta_keywords    = $GLOBALS["language"]["meta_keywords"];

    if(isset($_GET['collection_id']) && (int)$_GET['collection_id'] > 0) {
        $query = "SELECT description,subtitle,meta_description FROM main_collection where id = '" . $_GET['collection_id']."'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        $row = @mysqli_fetch_array($result);

        if($row['subtitle'] <> '')
        {
            $pageData['subtitle'] = $row['subtitle'];
            $GLOBALS["page"]["subtitle"] = $row['subtitle'];
        }
        elseif ($row['description'])
        {
            $pageData['subtitle'] = $row['description'];
            $GLOBALS["page"]["subtitle"] = $row['description'];
        }
        $pageData["meta_description"] = $row['description'];
        if($row['meta_description'] <> '')
        {
            $pageData["meta_description"] = $row['meta_description'];
        }

    }

    // Meta-Daten aus Navigation füllen
    if ($pageData['subtitle'] != "") {
        $site_title = $pageData['subtitle'];
        if ($GLOBALS["language"]["site_title_name"] <> '') {
            $site_title .= ' | ' . $GLOBALS["language"]["site_title_name"];
        }
    }
    if ($pageData["meta_description"] <> '') {
        $meta_description = $pageData["meta_description"];
    }

    if ($pageData["meta_keywords"] <> '') {
        $meta_keywords = $pageData["meta_keywords"];
    }

    // Meta-Daten für News füllen
    /*
	$query = "SELECT * FROM main_navigation_has_sitepart WHERE main_navigation_id = ". $navigation["id"] . " AND main_sitepart_id = 2 LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if (@mysqli_num_rows($result) == 1) {
		$navigation_has_sitepart = @mysqli_fetch_array($result);
		if ($_GET["news" . $navigation_has_sitepart["id"]] <> '') {
			$query = "SELECT * FROM news_entry WHERE id = ". $_GET["news" . $navigation_has_sitepart["id"]] . " LIMIT 1";
			$result = @mysqli_query($GLOBALS['mysql_con'],$query);
			if (@mysqli_num_rows($result) == 1) {
				$news = @mysqli_fetch_array($result);			
				if ($news["headline"] <> '') {
				$site_title = $news["headline"];
					if ($GLOBALS["language"]["site_title_name"] <> '') {
						$site_title .= ' - ' . $GLOBALS["language"]["site_title_name"];	
					}
				}
				if ($news["summary"] <> '') {
					$news["summary"] = str_replace("\n", "", $news["summary"]);
					$news["summary"] = str_replace("\r", "", $news["summary"]);
					$meta_description = $news["summary"];	
				}
				if ($news["meta_keywords"] <> '') {
					$meta_keywords = $news["meta_keywords"];	
				}
			}
		}
	}
	*/
    //Metadaten nur für Shop
    if (is_array($GLOBALS['shop'])) {
        set_global_metadata_shop();
        if(strlen($GLOBALS['site_title']) > 0) {
            $site_title = $GLOBALS['site_title'];
        }
        if(strlen($GLOBALS['meta_description']) > 0) {
            $meta_description = $GLOBALS['meta_description'];
            $meta_description = html_entity_decode($meta_description);
            $meta_description = preg_replace("!\s+!",' ',$meta_description);
            $meta_description = htmlentities($meta_description);
        }
        if(strlen($GLOBALS['meta_keywords']) > 0) {
            $meta_keywords = $GLOBALS['meta_keywords'];
        }
    }
    if ($site_title <> '') {
        echo "<title>" . $site_title . "</title>\n";
    }
    if ($meta_keywords <> '') {
        $GLOBALS["meta_keywords"] = $meta_keywords;
        echo "<meta name=\"Keywords\" content=\"" . $meta_keywords . "\" />\n";
    }
    if ($meta_description <> '') {
        echo "<meta name=\"Description\" content=\"" . $meta_description . "\" />\n";
    }
}

function set_global_meta_tag_content_site() {

    $pageData = $GLOBALS["page"];

    // Meta-Daten aus Sprache füllen
    $site_title       = $GLOBALS["navigation"]['menu_name'];
    $meta_description = $GLOBALS["language"]["meta_description"];
    $meta_keywords    = $GLOBALS["language"]["meta_keywords"];

    if(isset($_GET['collection_id']) && (int)$_GET['collection_id'] > 0) {
        $query = "SELECT description, subtitle, meta_description  FROM main_collection where id = '" . $_GET['collection_id']."'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        $row = @mysqli_fetch_array($result);

        if($row['subtitle'] <> '')
        {
            $pageData['subtitle'] = $row['subtitle'];
            $GLOBALS["page"]["subtitle"] = $row['subtitle'];
        }
        elseif ($row['description'])
        {
            $pageData['subtitle'] = $row['description'];
            $GLOBALS["page"]["subtitle"] = $row['description'];
        }
        $pageData["meta_description"] = $row['description'];
        if($row['meta_description'] <> '')
        {
            $pageData["meta_description"] = $row['meta_description'];
        }

    }

    // Meta-Daten aus Navigation füllen
    if ($pageData['subtitle'] != "") {
        $site_title = $pageData['subtitle'];
        if ($GLOBALS["language"]["site_title_name"] <> '') {
            $site_title .= ' | ' . $GLOBALS["language"]["site_title_name"];
        }
    }
    if ($pageData["meta_description"] <> '') {
        $meta_description = $pageData["meta_description"];
    }

    if ($pageData["meta_keywords"] <> '') {
        $meta_keywords = $pageData["meta_keywords"];
    }

    // Meta-Daten für News füllen
    /*
	$query = "SELECT * FROM main_navigation_has_sitepart WHERE main_navigation_id = ". $navigation["id"] . " AND main_sitepart_id = 2 LIMIT 1";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if (@mysqli_num_rows($result) == 1) {
		$navigation_has_sitepart = @mysqli_fetch_array($result);
		if ($_GET["news" . $navigation_has_sitepart["id"]] <> '') {
			$query = "SELECT * FROM news_entry WHERE id = ". $_GET["news" . $navigation_has_sitepart["id"]] . " LIMIT 1";
			$result = @mysqli_query($GLOBALS['mysql_con'],$query);
			if (@mysqli_num_rows($result) == 1) {
				$news = @mysqli_fetch_array($result);			
				if ($news["headline"] <> '') {
				$site_title = $news["headline"];
					if ($GLOBALS["language"]["site_title_name"] <> '') {
						$site_title .= ' - ' . $GLOBALS["language"]["site_title_name"];	
					}
				}
				if ($news["summary"] <> '') {
					$news["summary"] = str_replace("\n", "", $news["summary"]);
					$news["summary"] = str_replace("\r", "", $news["summary"]);
					$meta_description = $news["summary"];	
				}
				if ($news["meta_keywords"] <> '') {
					$meta_keywords = $news["meta_keywords"];	
				}
			}
		}
	}
	*/

    if ($site_title <> '') {
        $GLOBALS['site_title'] = $site_title;
    } else {
        $GLOBALS['site_title'] = NULL;
    }
    if ($meta_keywords <> '') {
        $GLOBALS["meta_keywords"] = $meta_keywords;
    } else {
        $GLOBALS["meta_keywords"] = NULL;
    }
    if ($meta_description <> '') {
        $GLOBALS['meta_description'] = $meta_description;
    } else {
        $GLOBALS['meta_description'] = NULL;
    }
}

//warenkorb zwischenseite "Lightbox"
function get_shadow_queue() {

    $body = "";
    if ($_GET["action"] == "shop_add_item_to_basket_card" || $_GET["action"] == "shop_add_item_to_basket_list" || $_GET["action"] == "shop_add_item_to_basket") {
        $body = "<div id=\"shadow\" class=\"shadow_bg\" onclick='hide();return false;'></div>";
    }
    return $body;
}

function is_item_no( $string ) {
    $pattern = '/^[0-9]+-?[0-9]*$/';
    $result  = preg_match($pattern, $string);
    if ($result === 1) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function cologne_phon( $word ) {
    $query = 'SELECT cologne_phon(\'' . mysqli_real_escape_string($GLOBALS['mysql_con'],trim($word)) . '\')';
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $cologne_phon = @mysqli_result($result);
    if(strlen($cologne_phon) > 0) {
        $return_value = preg_replace('!\s+!', ' ', $cologne_phon);
        return $return_value;
    }

    /**
     * @param  string $word string to be analyzed
     *
     * @return string  $value represents the Kölner Phonetik value
     * @access public
     */

    // prepare for processing
    $value        = array();
    $value_2      = array();
    $value_3      = array();
    $word         = strtolower($word);
    $substitution = array(
        "ä"  => "a",
        "ö"  => "o",
        "ü"  => "u",
        "ß"  => "ss",
        "ph" => "f",
        " "  => "|"
    );
    foreach ($substitution as $letter => $subst) {
        $word = str_replace($letter, $subst, $word);
    }

    $len = strlen($word);

    // Rule for exeptions
    $exceptionsLeading1 = array(
        4 => array("ca", "ch", "ck", "cl", "co", "cq", "cu", "cx")
    );

    $exl1 = array("ca", "ch", "ck", "cl", "co", "cq", "cu", "cx");

    $exceptionsLeading2 = array(
        8 => array("dc", "ds", "dz", "tc", "ts", "tz")
    );

    $exl2 = array("dc", "ds", "dz", "tc", "ts", "tz");

    $exceptionsLeading3 = array(
        4 => array("ac,ak,ec,ek,ic,ik,oc,ok,uc,uk")
    );

    $exl3 = array("ac,ak,ec,ek,ic,ik,oc,ok,uc,uk");

    $exceptionsFollowing = array("sc", "zc", "cx", "kx", "qx");

    //Table for coding
    $codingTable = array(
        0  => array("a", "e", "i", "j", "o", "u", "y"),
        1  => array("b", "p"),
        2  => array("d", "t"),
        3  => array("f", "v", "w"),
        4  => array("c", "g", "k", "q"),
        48 => array("x"),
        5  => array("l"),
        6  => array("m", "n"),
        7  => array("r"),
        8  => array(),
        9  => Array("h")
    );

    for ($i = 0; $i < $len; $i++) {
        $rank = "";
        $sl   = $word[$i];
        if (isset($word[$i + 1])) {
            $dl = $word[$i] . $word[$i + 1];
        } else {
            $dl = $word[$i];
        }

        //DL-Handling
        if (strlen($dl) == 2) {
            //Leading
            if (($i == 0 && $dl == 'cr') || (in_array($dl, $exl1))) {
                $value[$i] = 4;
            }

            if (in_array($dl, $exl2)) {
                $value[$i] = 8;
            }

            if ($i > 0 && (in_array($dl, $exl3))) {
                $value[$i] = 4;
            }

            //Following
            if ($i > 0 && (in_array($word[$i - 1] . $word[$i], $exceptionsFollowing))) {
                $value[$i] = 8;
            }
        }

        // normal encoding
        if (empty($value[$i])) {
            foreach ($codingTable as $code => $letters) {
                if (in_array($word[$i], $letters)) {
                    $value[$i] = $code;
                }
            }
        }
        if (in_array($sl, array('c', 's', 'z')) && ((isset($word[$i - 1])) && !(in_array($word[$i - 1], array('a', 'e', 'i', 'o', 'u'))))) {
            $value[$i] = 8;
        }

        //Space
        if ($word[$i] == '|') {
            $value[$i] = '|';
        }
    }

    // delete double values
    $len = count($value);

    for ($i = 1; $i < $len; $i++) {
        if ($i > 1 && isset($value[$i - 1]) && $value[$i] == $value[$i - 1]) {
            $value[$i] = "";
        }
    }

    foreach ($value as $key => $value1) {
        if (isset($value1) && ($value1 == 0 || $value1 != '')) {
            $value_2[] = $value1;
        }
    }

    $len = count($value_2);
    // delete vocals
    for ($i = 1; $i < $len; $i++) {
        // omitting first characer code and h
        if (($value_2[$i] === 0 || $value_2[$i] === 9) && ($i !== 0) && ($value_2[$i - 1] !== '|')) {
            $value_2[$i] = "";
        }
    }

    $res_arr = array();
    $z       = 0;
    $value_3 = array();
    foreach ($value_2 as $key => $value) {
        if (isset($value) && $value == 0 || $value != '') {
            $value_3[] = $value;
        }
    }
    for ($i = 0; $i < count($value_3); $i++) {
        if ($value_3[$i] === '|') {
            $res_arr[$z] = ' ';
            $z++;
        } elseif (preg_match('/[0-9]/', $value_3[$i])) {
            $res_arr[$z] = $value_3[$i];
            if ($res_arr[$z] == 0 && $z === 0) {
                $res_arr[$z] = 9;
            }
            $z++;
        }
    }

    foreach ($res_arr as $key => &$value) {
        if ($value == 0) {
            $value = '';
        }
    }
    unset($value);
    $res_arr2 = array();
    foreach ($res_arr as $key => &$value2) {
        if ((int)$value > 0) {
            $res_arr2[] = $value2;
        }
    }
    unset($value2);
    $res_arr2 = array_filter($res_arr2);
    for ($i = 0; $i < count($res_arr2); $i++) {
        if ($res_arr2[$i] == 9) {
            $res_arr2[$i] = 0;
        }
        if ($i > 0 && $res_arr2[$i] === $res_arr2[$i - 1]) {
            $res_arr2[$i] = '';
        }
    }

    $res_arr2 = implode("", $res_arr2);
    return $res_arr2;
}

function get_number_matches( $search_term ) {
    $add_query_num = '';

    $query  = "SELECT *
			  FROM shop_item_cross_reference
			  WHERE item_reference_no like '%" . $search_term . "%'
				AND (customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "' OR customer_no='')
				AND company ='" . $GLOBALS['shop']['company'] . "'
				";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $add_query_num .= " OR shop_view_active_item.item_no LIKE '%" . $row['item_no'] . "%'";
        }
    }

    $number_query = "
	SELECT shop_view_active_item.*
	FROM shop_view_active_item
	WHERE		
		shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
	  AND
		shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["code"] . "'
	  AND
		shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
	  AND	  
		shop_view_active_item.item_no LIKE '%" . $search_term . "%'
	  OR
		shop_view_active_item.ean_no LIKE '%" . $search_term . "%'
		" . $add_query_num . " 
	GROUP BY shop_view_active_item.item_no";


    $result        = @mysqli_query($GLOBALS['mysql_con'], $number_query);
    $search_string = "";
    $sort_string   = "";
    if (@mysqli_num_rows($result) > 0) {
        $search_string = "(";
        $z             = 0;
        while ($item = @mysqli_fetch_assoc($result)) {
            if ($z === 0) {
                $search_string .= " shop_item.id=" . $item["id"];
            } else {
                $search_string .= " OR shop_item.id=" . $item["id"];
            }
            $z++;
        }
        $search_string .= ")";
        $sort_string .= " CASE WHEN shop_item.item_no='" . $search_term . "' THEN 1 ELSE 0 END DESC, CASE WHEN shop_item.ean_no='" . $search_term . "' THEN 1 ELSE 0 END DESC,CASE WHEN shop_item_cross_reference.item_reference_no='" . $search_term . "' THEN 1 ELSE 0 END DESC,
		CASE WHEN shop_item.item_no LIKE '%" . $search_term . "%' THEN 1 ELSE 0 END DESC, CASE WHEN shop_item.ean_no='%" . $search_term . "%' THEN 1 ELSE 0 END DESC,CASE WHEN shop_item_cross_reference.item_reference_no='%" . $search_term . "%' THEN 1 ELSE 0 END DESC";
    }
    $return["search_string"] = $search_string;
    $return["sort_string"]   = $sort_string;
    return $return;
}

function img_size_mm( $img, $dpi ) {


    function px_to_mm( $px, $dpi ) {
        return in_to_mm($px / $dpi);
    }

    function mm_to_px( $mm, $dpi ) {
        return mm_to_in($mm) * $dpi;
    }

    function mm_to_in( $mm ) {
        return $mm * $GLOBALS["mm_per_in"];
    }

    function in_to_mm( $in ) {
        return $in * $GLOBALS["in_per_mm"];
    }

    $dimensions = getimagesize($img);
    $width      = $dimensions[0];
    $height     = $dimensions[1];

    if ($width > 0 && $height > 0) {
        $return[0] = px_to_mm($width, $dpi);
        $return[1] = px_to_mm($height, $dpi);
        return $return;
    } else {
        return FALSE;
    }
}

function log_text( $newtext ) {
    $file    = $GLOBALS["curr_logfile"];
    $current = file_get_contents($file);
    $current .= "\r\n\r\n" . $newtext;
    file_put_contents($file, $current);
}

function get_text_module_attachment( $company, $text_module_code, $attachment_no = 1 ) {
    $query    = 'SELECT attachment_' . $attachment_no . ' FROM shop_text_module WHERE company=\'' . $company . '\' AND code=\'' . $text_module_code . '\'';
    $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
    $filename = @mysqli_result($result, 0, 0);
    if (strlen($filename) > 0) {
        return "/userdata/private/documents/" . $filename;
    } else {
        return '';
    }
}

function get_visitor_small( $sid, $from_session = 0, $from_cookie = 0 ) {

    $query  = 'SELECT * FROM main_visitor WHERE session_id = ?';
    $stmt = mysqli_prepare($GLOBALS["mysql_con"],$query);
    $stmt->bind_param("s",$sid);
    $stmt->execute();
    $stmt->store_result();
    $assoc_array = fetchAssocStatement($stmt);

    if ($stmt->num_rows == 1) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        /* foreach($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			$value = trim($parts[1]);
			$_SESSION[$name] = $value;			
		}	*/
        if ($from_session == 1) {
            $_SESSION['session_login'] = 1;
            $_SESSION['cookie_login']  = 0;
        } elseif ($from_cookie == 1) {
            $_SESSION['session_login'] = 0;
            $_SESSION['cookie_login']  = 1;
        }
        return $assoc_array;
    }
}


function cookie_login_postprocessing( $visitor, $cookies ) {
    $old_sid                    = $cookies['sid' . $GLOBALS['site']['code']];
    $old_token                  = trim($cookies[$GLOBALS['site']['code'] . '_remember_login']);
    $GLOBALS['test']['old_sid'] = $old_sid;
    if (session_regenerate_id()) {
        $newSid = session_id();
        \DynCom\dc\common\classes\Hook::update('session_id_changed',['old_sid' => $old_sid, 'new_sid' => $newSid]);

        $_SESSION['sid' . $GLOBALS['site']['code']] = $newSid;
        $cookie_login                               = FALSE;
        $cookie_removed                             = '';
        foreach ($cookies as $name => $value) {
            if (
                (isset($visitor['remember_token']))
                &&
                (strlen($visitor['remember_token']) > 0)
                &&
                ($name == $GLOBALS['site']['code'] . '_remember_login')
            ) {
                $cookie_login = TRUE;
                setcookie($name, '', time() - 1000,'/' ,NULL, NULL, true);
                setcookie($name, '', time() - 1000, '/', NULL, NULL, true);
            }
        }
        $cookie_timeout = COOKIE_DAYS_VALID * 24 * 60 * 60;
        if ((!empty($visitor['remember_token'])) && (strlen($visitor['remember_token']) > 0) && $cookie_login) {
            $query                 = 'SELECT password FROM shop_user WHERE id=' . $visitor['main_user_id'];
            $result                = @mysqli_query($GLOBALS['mysql_con'], $query);
            $password              = @mysqli_result($result);
            $remember_token_unique = md5(uniqid(session_id(), TRUE) . $password . $GLOBALS['visitor']['main_user_id']);
            $query                 = 'UPDATE main_visitor SET session_id=\'' . session_id() . '\', cookie_only=1, frontend_login=1, remember_token=\'' . $remember_token_unique . '\' WHERE id=' . $visitor['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $visitor_query  = "SELECT * FROM main_visitor WHERE id=" . $visitor['id'];
            $visitor_result = @mysqli_query($GLOBALS['mysql_con'], $visitor_query);
            $visitor        = @mysqli_fetch_assoc($visitor_result);
            if ($visitor['frontend_login']) {
                $GLOBALS['visitor']['frontend_login'] = 1;
            }
            setcookie($GLOBALS['site']['code'] . '_remember_login', $remember_token_unique, time() + $cookie_timeout, '/', NULL, NULL, true);
            setcookie('sid' . $GLOBALS['site']['code'], session_id(), time() + $cookie_timeout, '/', NULL, NULL, true);
        }
    }
}

function nav_login_postprocessing( &$visitor ) {
    $old_sid = $visitor['session_id'];
    if (session_regenerate_id()) {
        $new_sid                                    = session_id();
        $_SESSION['sid' . $GLOBALS['site']['code']] = $new_sid;
        $query                                      = 'UPDATE main_visitor SET session_id = \'' . $new_sid . '\' WHERE session_id = \'' . $old_sid . '\'';
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            $delete_query = 'DELETE FROM main_visitor WHERE main_user_id = ' . $visitor['main_user_id'] . ' AND nav_login=1 AND session_id != \'' . $new_sid . '\'';
            @mysqli_query($GLOBALS['mysql_con'], $delete_query);
            $select_query = 'SELECT * FROM main_visitor WHERE session_id = \'' . $new_sid . '\'';
            $result       = @mysqli_query($GLOBALS['mysql_con'], $select_query);
            if (@mysqli_num_rows($result) > 0) {
                $visitor = @mysqli_fetch_assoc($result);
            }
        } else {
            throw new Exception('NOT UPDATED: ' . $query);
        }

    }
}

function logQuery( $datetime, $query, $file, $line, $toGlobal = NULL, $toFile = FALSE, $toHTML = TRUE, $msg = '', $includeSession = FALSE, $includeCookie = FALSE, $includePost = FALSE, $includeGet = FALSE, $includeArray = NULL ) {
    $logtext = PHP_EOL . $datetime . ' - ' . $msg . PHP_EOL;
    $logtext .= '  QUERY: ' . $query . PHP_EOL;
    $logtext .= '  FILE: ' . $file . PHP_EOL;
    $logtext .= '  LINE: ' . $line . PHP_EOL;
    if ($includeSession) {
        $sessiontext = print_r($_SESSION, 1);
        $sessiontext = str_replace('\n(\n', '\n  (\n', $sessiontext);
        $logtext .= '  SESSION: ' . $sessiontext . PHP_EOL;
    }
    if ($includeCookie) {
        $cookietext = print_r($_COOKIE, 1);
        $cookietext = str_replace('\n(\n', '\n  (\n', $cookietext);
        $logtext .= '  COOKIE: ' . $cookietext . PHP_EOL;
    }
    if ($includePost) {
        $posttext = print_r($_POST, 1);
        $posttext = str_replace('\n(\n', '\n  (\n', $posttext);
        $logtext .= '  POST: ' . $posttext . PHP_EOL;
    }
    if ($includeGet) {
        $gettext = print_r($_GET, 1);
        $gettext = str_replace('\n(\n', '\n  (\n', $gettext);
        $logtext .= '  GET: ' . $gettext . PHP_EOL;
    }
    if (is_array($includeArray)) {
        $arrtext = print_r($includeArray, 1);
        $arrtext = str_replace('\n(\n', '\n  (\n', $arrtext);
        $logtext .= ' CUST-ARRAY: ' . $arrtext . PHP_EOL;
    }
    if (isset($toGlobals)) {
        $toGlobals .= $logtext;
        $GLOBALS['querylog'] .= $logtext;
    } elseif ($toFile) {

    }
    if ($toHTML) {
        echo '<!-- LOG: ' . $logtext . ' -->';
    }
    if ($toFile) {
        $querylog = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/userdata/logfile-visitor.txt", "a+");
        fwrite($querylog, $logtext . PHP_EOL);
        fclose($querylog);
    }
}

function check_sid_for_login( $sid, $nav_login = FALSE ) {
    $nav_login_insert = ($nav_login ? ' AND nav_login=1 ' : '');
    $query            = 'SELECT EXISTS(SELECT id FROM main_visitor WHERE session_id = \'' . mysqli_real_escape_string($GLOBALS['mysql_con'], $sid) . '\' AND main_user_id > 0 AND frontend_login=1 ' . $nav_login_insert . ')';
    $result           = @mysqli_query($GLOBALS['mysql_con'], $query);
    return (@mysqli_result($result, 0, 0) == TRUE ? TRUE : FALSE);
}

function renew_session_dates( $visitor ) {
    if ((int)$visitor['id'] > 0) {
        $query = '
		UPDATE main_visitor SET session_date = NOW(), valid_until = DATE_ADD(NOW(),INTERVAL 30 DAY) WHERE id = ' . (int)$visitor['id'];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function set_shop_globals_from_site() {
    if (!empty($GLOBALS['language']['company']) && !empty($GLOBALS['language']['shop_code']) && !empty($GLOBALS['language']['shop_language_code'])) {
        $company            = $GLOBALS['language']['company'];
        $shop_code          = $GLOBALS['language']['shop_code'];
        $shop_language_code = $GLOBALS['language']['shop_language_code'];

        if (empty($GLOBALS['shop'])) {
            $shop_query  = "SELECT * FROM shop_shop WHERE company = '" . $company . "' AND code = '" . $shop_code . "'";
            $shop_result = @mysqli_query($GLOBALS['mysql_con'], $shop_query);
            $shop        = @mysqli_fetch_assoc($shop_result);
            if (!empty($shop['id']) && (int)$shop['id'] > 0) {
                $GLOBALS['shop'] = $shop;
            }
        }

        if (empty($GLOBALS['shop_language'])) {
            $shop_language_query  = "SELECT * FROM shop_language WHERE company = '" . $company . "' AND shop_code = '" . $shop_code . "' AND code = '" . $shop_language_code . "'";
            $shop_language_result = @mysqli_query($GLOBALS['mysql_con'], $shop_language_query);
            $shop_language        = @mysqli_fetch_assoc($shop_language_result);
            if (!empty($shop_language['id']) && (int)$shop_language['id'] > 0) {
                $GLOBALS['shop_language'] = $shop_language;
            }
        }

    } else {
        set_shop_globals_from_login();
    }
}

function set_shop_globals_from_login() {
    $query  = "SELECT target_site_code, target_language_code FROM main_shop_login WHERE main_language_id = " . (int)$GLOBALS['language']['id'];
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if ((int)@mysqli_num_rows($result) === 1) {
        $row           = mysqli_fetch_assoc($result);
        $site_code     = $row['target_site_code'];
        $language_code = $row['target_language_code'];
        if (!empty($site_code) && !empty($language_code)) {
            //Shop holen
            $query       = "
				SELECT 
					shop_shop.*
				FROM 
					main_language,
					main_site,
					shop_shop
				WHERE
					main_site.code = '" . $site_code . "'
				  AND
					main_language.main_site_id = main_site.id
				  AND
					main_language.code = '" . $language_code . "'
				  AND
					shop_shop.company = main_language.company
				  AND
					shop_shop.code = main_language.shop_code
				";
            $shop_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $shop        = @mysqli_fetch_assoc($shop_result);
            if (!empty($shop['id']) && (int)$shop['id'] > 0) {
                $GLOBALS['shop'] = $shop;
                if ($language_code == 'de' || $language_code == 'DE' || $language_code == 'DEU' || $language_code == 'deu') {
                    $lang_snippet = " AND code IN('de','DE','DEU','deu','dea','DEA') ";
                } elseif ($language_code == 'dea' || $language_code == 'DEA') {
                    $lang_snippet = " AND code IN('dea','DEA') ";
                } elseif ($language_code == 'en' || $language_code == 'EN' || $language_code == 'ENU' || $language_code == 'enu') {
                    $lang_snippet = " AND code IN('en','EN','ENU','enu') ";
                }
                $query  = "SELECT * FROM shop_language WHERE company = '" . $shop['company'] . "' AND shop_code = '" . $shop['code'] . "' " . $lang_snippet;
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                if ((int)@mysqli_num_rows($result) == 1) {
                    $shop_language = mysqli_fetch_assoc($result);
                    if (!empty($shop_language['id']) && (int)$shop_language['id'] > 0) {
                        $GLOBALS['shop_language'] = $shop_language;
                    }
                } else {
                    //echo $query;
                }
            } else {
                //echo $query;
            }
        }
    }
}

function get_login_fields_html( $shop ) {
    $email_field = (
    $show_labels_in_fields ?
        "
			<div>
				<div class='input'>
					<input type='text' name='input_email' id='input_email' value='" . $GLOBALS['shop_user']['email'] . "' />
				</div>
			</div>
			"
        :
        "
			<div>
				<div class='label'><label for='input_email'>" . $GLOBALS['tc']['email'] . "</label></div>
				<div class='input'><input type='text' name='input_email' id='input_email' value='" . $GLOBALS['shop_user']['email'] . "' /></div>
			</div>
			"
    );

    $login_field = (
    $show_labels_in_fields ?
        "
			<div>
				<div class='input'>
					<input type='text' name='input_login' id='input_login' value='" . $GLOBALS['shop_user']['login'] . "' />
				</div>
			</div>
			"
        :
        "
			<div>
				<div class='label'><label for='input_login'>" . $GLOBALS['tc']['login'] . "</label></div>
				<div class='input'><input type='text' name='input_login' id='input_login' value='" . $GLOBALS['shop_user']['login'] . "' /></div>
			</div>
			"
    );

    $customer_no_field = (
    $show_labels_in_fields ?
        "
			<div>
				<div class='input'>
					<input type='text' name='input_customer_no' id='input_customer_no' value='" . $GLOBALS['shop_customer']['customer_no'] . "' />
				</div>
			</div>
			"
        :
        "
			<div>
				<div class='label'><label for='input_customer_no'>" . $GLOBALS['tc']['customer_no'] . "</label></div>
				<div class='input'><input type='text' name='input_customer_no' id='input_customer_no' value='" . $GLOBALS['shop_customer']['customer_no'] . "' /></div>
			</div>
			"
    );

    $password_field = (
    $show_labels_in_fields ?
        "
			<div>
				<div class='input'>
					<input type='text' name='input_password' id='input_password'/>
				</div>
			</div>
			"
        :
        "
			<div>
				<div class='label'><label for='input_password'>" . $GLOBALS['tc']['password'] . "</label></div>
				<div class='input'><input type='password' name='input_password' id='input_password'/></div>
			</div>
			"
    );

    $field_1_html = '';
    $field_2_html = '';

    switch ($shop['login_type']) {
        case 0: //E-Mail & Passwort
            $field_1_html = $email_field;
            break;
        case 1: //Login & Passwort
            $field_1_html = $login_field;
            break;
        case 2: //Kunden-Nr., Login & Passwort
            $field_1_html = $customer_no_field;
            $field_2_html = $login_field;
            break;
        case 3: //Kunden-Nr., E-Mail & Passwort
            $field_1_html = $customer_no_field;
            $field_2_html = $email_field;
            break;
        case 4: //Kunden-Nr. & Passwort
            $field_1_html = $customer_no_field;
            break;
        default:
            break;
    }

    $pre_check_remember_login = (!empty($GLOBALS['visitor']['remember_token']) ? 'checked ' : '');

    $fields = '<input type="hidden" name="action" value="shop_login"/>' . $field_1_html . $field_2_html . $password_field .
        '<!-- <div>
				<div class="label">
					<label for="remember_login">' . $GLOBALS['tc']['remember_login'] . '</label>
				</div>
				<div class="input">
					<input type="checkbox" class="checkbox" name="remember_login" id="remember_login" ' . $pre_check_remember_login . '/>
				</div>
			</div> -->';
    return $fields;
}

function split_name( $name ) {
    $results = array();

    $r    = explode(' ', $name);
    $size = count($r);

    //check first for period, assume salutation if so
    if (mb_strpos($r[0], '.') === FALSE) {
        $results['salutation'] = '';
        $results['first']      = $r[0];
    } else {
        $results['salutation'] = $r[0];
        $results['first']      = $r[1];
    }

    //check last for period, assume suffix if so
    if (mb_strpos($r[$size - 1], '.') === FALSE) {
        $results['suffix'] = '';
    } else {
        $results['suffix'] = $r[$size - 1];
    }

    //combine remains into last
    $start = ($results['salutation']) ? 2 : 1;
    $end   = ($results['suffix']) ? $size - 2 : $size - 1;

    $last = '';
    for ($i = $start; $i <= $end; $i++) {
        $last .= ' ' . $r[$i];
    }
    $results['last'] = trim($last);

    return $results;
}

function validateDate( $date, $format = 'Y-m-d H:i:s' ) {
    $d = DateTime::createFromFormat($format, $date);
    return ($d && $d->format($format) == $date);
}

function stringEndsWith( $string, $endsWith ) {
    $strlen  = strlen($string);
    $testlen = strlen($endsWith);
    if ($testlen > $strlen) {
        return FALSE;
    }
    return substr_compare(strtolower($string), strtolower($endsWith), $strlen - $testlen, $testlen) === 0;
}

function MySQLTypeIsText( $typestring ) {
    $typestring = (string)$typestring;
    return (
        stringEndsWith($typestring, 'CHAR')
        ||
        stringEndsWith($typestring, 'TEXT')
    );
}

function MySQLTypeIsNumeric( $typestring ) {
    $typestring = strtoupper($typestring);
    return (
        stringEndsWith($typestring, 'INT')
        ||
        stringEndsWith($typestring, 'FLOAT')
        ||
        stringEndsWith($typestring, 'DECIMAL')
    );
}

function MySQLTypeIsDate( $typestring ) {
    $typestring = strtoupper($typestring);
    return (
        $typestring == 'DATETIME'
        ||
        $typestring == 'DATE'
        ||
        $typestring == 'TIMESTAMP'
    );
}

function array_depth( array $array ) {
    $max_indentation = 1;

    $array_str = print_r($array, TRUE);
    $lines     = explode("\n", $array_str);

    foreach ($lines as $line) {
        $indentation = (strlen($line) - strlen(ltrim($line))) / 4;

        if ($indentation > $max_indentation) {
            $max_indentation = $indentation;
        }
    }

    return ceil(($max_indentation - 1) / 2) + 1;
}

function mysqli_fetch_all_from_executed_stmt(mysqli_stmt $stmt) {
    try {
        // Get metadata for field names
        $meta = $stmt->result_metadata();

        // initialise some empty arrays
        $fields = $results = $fieldNames = array();

        // This is the tricky bit dynamically creating an array of variables to use
        // to bind the results
        while ($field = $meta->fetch_field()) {
            $fieldNames[] = $field->name;
            $var          = $field->name;
            $$var         = NULL;
            $fields[$var] = &$$var;
        }

        $fieldCount = count($fieldNames);

        // Bind Results
        call_user_func_array(array($stmt,'bind_result'),$fields);

        $i=0;
        while ($stmt->fetch()){
            for($l=0;$l<$fieldCount;$l++) {
                $results[$i][$fieldNames[$l]] = $fields[$fieldNames[$l]];
            }
            $i++;
        }

        $stmt->close();

        return $results;
    } catch(Exception $e) {
        return array();
    }
}

/**
 * @param $id
 *
 * @return bool
 */

function setMailSent( $id ) {
    $query = 'UPDATE main_mail_log SET send_date = now() WHERE id = ' . (int)$id;
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    return (bool)$result;
}


function get_all_whitespace_collapsed_variants($string) {
    $string = str_replace(array(' AND ',' OR '),' ',$string);
    $tokens = explode(' ',$string);
    $urlencodedTokens = array_map(function($str){return urlencode($str);},$tokens);
    $noOfTokens = count($tokens);
    if($noOfTokens === 1) { return array(); }
    //$whitespaceCollapsedStrings = $tokens;
    $whitespaceCollapsedStrings = array();

    for($i = 0;$i <= ($noOfTokens - 1);++$i) {
        $newString = '';
        $newStringWildCard = '';
        for($j = 1;$j <= ($noOfTokens - $i);++$j) {
            $newSuffix = $tokens[(($i + $j)-1)];
            if($newSuffix !== 'AND' && $newSuffix !== 'OR') {
                $arrSearchStr = $newString . $newSuffix;
                $newString .= urlencode($newSuffix);
                $newStringWildCard .= (($newStringWildCard)?'*':'');
                $newStringWildCard .= $newSuffix;
                //$newString .= $newSuffix;
                if(!in_array($arrSearchStr,$tokens) && !in_array($newString,$urlencodedTokens)) {
                    if(!in_array($newString,$whitespaceCollapsedStrings) && mb_strlen($newString,'UTF-8')>0) {
                        $whitespaceCollapsedStrings[] = $newString;
                        //$whitespaceCollapsedStrings[] = $newStringWildCard;
                    }
                }
            }
        }
    }
    return $whitespaceCollapsedStrings;
}

function string_starts_with($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function string_ends_with($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

if (!function_exists("array_column")) {
    function array_column($array,$column_name)
    {
        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
    }
}


function pad_string_to_mb_length($string,$to_length,$padChar = ' ')
{
    $padChar = (string)$padChar;
    $padChar = $padChar[0];
    $to_length = (int)$to_length;
    $currLength = mb_strlen($string);
    if ($currLength < $to_length) {
        while (mb_strlen($string) < $to_length) {
            $string = $string . $padChar;
        }
    }
    return $string;
}

function escapeChars($charsToEscape, $escapeSeq, &$string)
{
    $charsToEscape = preg_quote($charsToEscape, '/');
    $regexSafeEscapeSeq = preg_quote($escapeSeq, '/');
    $escapeSeq = preg_replace('/([$\\\\])/', '\\\$1', $escapeSeq);
    $stringCopy = $string;
    $correctedStringCopy = (preg_replace('/(?<!'.$regexSafeEscapeSeq.')(['.$charsToEscape.'])/', $escapeSeq.'$1', $stringCopy));
    $string = $correctedStringCopy;
}


function adjust_qty_by_min_max_step(&$newQty,$oldQty,$min,$max,$step)
{
    $newQty = (float)$newQty;
    $oldQty = (float)$oldQty;
    $signMultiplier = ($newQty < $oldQty) ? -1 : 1;
    $min = (float)$min;
    $max = (float)$max;
    $step = (float)$step;


    if (($newQty % $step) !== 0) {
        $stepMultiplier = abs($newQty / $step);
        if ($signMultiplier > 0) {
            $stepMultiplier = ceil($stepMultiplier);
        } else {
            $stepMultiplier = floor($stepMultiplier);
        }
        $newQty = $step * $stepMultiplier;
    }

    if ($newQty > $max) {
        $newQty = $max;
    } elseif($newQty < $min) {
        $newQty = $min;
    }

}
function normalize_search_string($string) {
    $string = trim(strip_tags($string));
    $string = preg_replace('/\s+/',' ',$string);
    escapeChars('+-&|!(){}[]^~*?:\\/','\\',$string);
    //$string = preg_replace('/\-/','*',$string);
    //$string = preg_replace('/[^[:alnum:]]/','',$string);
    //$string = preg_replace('/[^a-zA-Z0-9\s+\-_°&*?"]/','',$string);
    $subTerms = explode(' ',$string);
    $subtermCount = count($subTerms);
    //if(strpos($string,' ') === FALSE) return '"' . $string . '"';
    /*
    $termArr = array();

    $initTerm = $string;
    $initPhraseTerm = '"' . $initTerm . '"';
    $termArr[] = '(' . $initTerm . ')';
    $itemNoIntactTerm = "(item_no_intact:($initTerm))";
    $termArr[] = $itemNoIntactTerm;
    $vendorItemNoIntactTerm = "(vender_item_no_intact:($initTerm))";
    $termArr[] = $vendorItemNoIntactTerm;
    $referenceNosTerm = "(reference_nos:($initTerm))";
    $termArr[] = $referenceNosTerm;
    if($subtermCount > 1) {
        $fuzzyTerm = preg_replace('/(?<=^\b|\s\b)([\w]+)/','$1~2',$string);
        $descriptionIntactPhraseTerm = "(description_intact:\"$initTerm\"~2)";
        $termArr[] = $descriptionIntactPhraseTerm;
        $mainCombinedPhraseTerm = "(main_item_desc_combined:\"$initTerm\"~2)";
        $termArr[] = $mainCombinedPhraseTerm;
        $descriptionPhraseTerm = "(description:\"$initTerm\"~2)";
        $termArr[] = $descriptionPhraseTerm;
        $didYouMeanPhraseTerm = "(did_you_mean:\"$initTerm\"~2)";
        $termArr[] = $didYouMeanPhraseTerm;
    } else {
        $fuzzyTerm = $initTerm . '~2';
    }
    $descriptionIntactTerm = "(description_intact:($fuzzyTerm))";
    $termArr[] = $descriptionIntactTerm;
    $mainCombinedExactTerm = "(main_item_desc_combined_exact:($fuzzyTerm))";
    $termArr[] = $mainCombinedExactTerm;
    $didYouMeanTerm = "(did_you_mean:($fuzzyTerm))";
    $termArr[] = $didYouMeanTerm;
    $descriptionTerm = "(description:($initTerm))";
    $termArr[] = $descriptionTerm;
    $mainCombinedTerm = "(main_item_desc_combined:($initTerm))";
    $termArr[] = $mainCombinedTerm;
    $mainCombinedEdgeTerm = "(main_item_desc_combined_edge:($initTerm))";
    $termArr[] = $mainCombinedEdgeTerm;
    $mainCombinedNGramTerm = "(main_item_desc_combined_ngram:($initTerm))";
    $termArr[] = $mainCombinedNGramTerm;

    $termStr = implode(' AND ',$termArr);



    $string = ltrim($string,' AND ');
    $string = preg_replace('/\s+/',' ',$string);
    */
    if($subtermCount > 1) {
        $origString = $string;
    }
    //$string = preg_replace("/(?<=^|\"|\s)([^\s\"]+)(?=\s|\"|$)/",'$1~1',$string);	$string =

    //$string = preg_replace('/(?:\b\sAND\s\b)|(?:(?<!AND|OR)\s(?!AND|OR))/',' AND ',$string);
    //$string = preg_replace('/(?:\b\sOR\s\b)|(?:(?<!AND|OR)\s(?!AND|OR))/',' OR ',$string);

    if($subtermCount > 1) {
        //$string = '("' . $origString . '" AND ' . $string . ')';
        //$string = '("' . $origString . '")';
        $string = "$string";
    }

    return '(' . urlencode($string) . ')';
}

function formatBytes($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = 1024 * 1024;

    if ($bytes >= 0 && $bytes < $kilobyte) {
        return $bytes . " b";
    }

    if ($bytes >= $kilobyte && $bytes < $megabyte) {
        return round($bytes / $kilobyte, $precision) . " kb";
    }

    return round($bytes / $megabyte, $precision) . " mb";
}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey (http://benramsey.com)
 * @license http://opensource.org/licenses/MIT MIT
 */
if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}

function safe_array_dot_access(array $arr, $dotKey, $defaultValue = null) {
    $keyParts = explode('.',$dotKey,100);
    $lastPartIndex = count($keyParts) - 1;
    $i = 0;
    $currPart = $keyParts[$i];
    foreach ($keyParts as $keyPart) {
        $keyPartDigit = $keyPart;
        if (ctype_digit($keyPart)) {
            $keyPartDigit = (int)$keyPart;
        }
        if ($i === $lastPartIndex) {
            if (isset($currPart[$keyPart])) {
                return $currPart[$keyPart];
            } elseif (isset($currPart[$keyPartDigit])) {
                return $currPart[$keyPartDigit];
            } else {
                return $defaultValue;
            }
        }
        if (array_key_exists($keyPart,$currPart)) {
            $currPart = $currPart[$keyPart];
            $i++;
        } elseif (array_key_exists($keyPartDigit,$currPart)) {
            $currPart = $currPart[$keyPartDigit];
            $i++;
        } else {
            return $defaultValue;
        }
    }
    return $defaultValue;
}

function universal_redirect($absoluteRedirectPath,$redirectCode = 302)
{
    if (!headers_sent()) {
        headerFunctionBridge('Location: ' . $absoluteRedirectPath,true,$redirectCode);
        exit(0);
    } else {
        echo "
        <script type=\"text/javascript\">
        window.location.replace(\"$absoluteRedirectPath\");
        </script>
        ";

    }
}

function getSessionErrorMessages()
{
    if (isset($_SESSION['msgBag']['errors'])) {
        return $_SESSION['msgBag']['errors'];
    }
    return [];
}

function getSessionNotifications()
{
    if (isset($_SESSION['msgBag']['notifications'])) {
        return $_SESSION['msgBag']['notifications'];
    }
    return [];
}

function getSessionSuccessMessages()
{
    if (isset($_SESSION['msgBag']['successes'])) {
        return $_SESSION['msgBag']['successes'];
    }
    return [];
}

function printFlashMessages()
{

    $wrapper = '<div class="flash_messages" style="display:none;position:static;">%s</div>';
    $inner = '<div class="flash_message_%s">%s</div>';

    $allInner = '';
    if (array_key_exists('msgBag',$_SESSION)) {
        if (array_key_exists('successes',$_SESSION['msgBag'])) {
            foreach ($_SESSION['msgBag']['successes'] as $successMsg) {
                $allInner .= sprintf($inner,'success',$successMsg);
            }
        }
        if (array_key_exists('errors',$_SESSION['msgBag'])) {
            foreach ($_SESSION['msgBag']['errors'] as $errorMsg) {
                $allInner .= sprintf($inner,'error',$errorMsg);
            }
        }
        if (array_key_exists('notifications',$_SESSION['msgBag'])) {
            foreach ($_SESSION['msgBag']['notifications'] as $notification) {
                $allInner .= sprintf($inner,'notification',$notification);
            }
        }
    }
    $html = sprintf($wrapper,$allInner);
    $javascript = '
    <script type="text/javascript">
    $(\'.flash_messages\').fadeIn().delay(10000).fadeOut();
    </script>
    ';

    echo $html;
    echo $javascript;

    $_SESSION['msgBag'] = null;
    unset($_SESSION['msgBag']);

}

function  generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result);
}

function get_requestbox($text, $title = "", $typ = "error", $instantLoad = true, $id = "requestbox"){
    if($title == ""){
        switch ($typ) {
            case "success":
                $title = $GLOBALS['tc']['success'];
                break;
            case "warning":
                $title = $GLOBALS['tc']['warning'];
                break;
            default:
                $title = $GLOBALS['tc']['error'];
                break;
        }
    }

    if ($instantLoad) {

        ?>
        <script type="text/javascript">
            $(window).load(function(){
                if ($('#<?= $id ?>').parents('.modal').length == 1) {
                    parentModal = $('#<?= $id ?>').parents('.modal');
                    newModal = $('#<?= $id ?>').appendTo('body');
                    newModal.modal('show');
                    parentModal.modal('show');
                } else {
                    $('#<?= $id ?>').modal('show');
                }
            });
        </script>

    <?php } ?>

    <div class="modal fade <?=$typ;?>" id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=$title?></h4>
                </div>
                <div class="modal-body">
                    <?=$text?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function get_password_options() {
    $random_byte_length = 22;
    $salt = '';
    if (function_exists('random_bytes')) {
        return [
            'cost' => 11,
        ];
    } else {
        $salt = mcrypt_create_iv($random_byte_length,MCRYPT_DEV_URANDOM);
        return [
            'cost' => 11,
            'salt' => $salt,
        ];
    }
}

function fetchAssocStatement($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}

function get_nl_coupon($sales_header = "") { //changed

    $dc_code = "";
    if ($sales_header != "") {
        $query = "SELECT * FROM shop_language WHERE company = '".$sales_header["company"]."' AND shop_code = '".$sales_header["shop_code"]."' AND code = '".$sales_header["language_code"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $language = mysqli_fetch_array($result);
        if (is_null($GLOBALS["shop_language"])) {
            $GLOBALS["shop_language"] = $language;
        }
        $query = "SELECT * FROM shop_shop WHERE company = '".$sales_header["company"]."' AND code = '".$sales_header["shop_code"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $shop = mysqli_fetch_array($result);
        if (is_null($GLOBALS["shop"])) {
            $GLOBALS["shop"] = $shop;
        }
    }
    if ($GLOBALS["shop_language"]["nl_coupon_active"] && $GLOBALS["shop_language"]["nl_coupon_header"] != "") {
        $dc_code        = "NC" . random_str(7);

        $counter_line = 1;
        $new_code = true;

        while ($dc_code <> '' && $counter_line < 10 && $new_code == true)
        {
            $query_line = "SELECT id FROM shop_coupon_line
				 WHERE shop_coupon_line.company = '" . $GLOBALS["shop"]["company"] . "'
				 AND shop_coupon_line.coupon_code = '" . $dc_code . "'";
            $result_line = mysqli_query($GLOBALS['mysql_con'], $query_line);

            if (mysqli_num_rows($result_line) > 0) {
                $dc_code = "NC" . random_str(7);
                $new_code = true;
                $counter_line++;
            }
            else
            {
                $new_code = false;
            }
        }

        $header_code    = $GLOBALS["shop_language"]["nl_coupon_header"];

        $pdo = get_main_db_pdo_from_env_single_instance();
        static $setCouponLineQuery = '
            INSERT INTO shop_coupon_line 
                  SET company = :company,
                      code = :couponHeaderCode,
                      coupon_code = :couponCode,
                      times_used = 0,
                      max_no_of_usage = 0,
                      amount = :couponAmount,
                      amount_left = :couponAmount,
                      value_type = 0,
                      value_coupon = 0,
                      active = 1,
                      last_date_used=CURDATE()
        ';

        $stmt = $pdo->prepare($setCouponLineQuery);
        $stmt->bindValue(':company',$GLOBALS["shop"]["company"],PDO::PARAM_STR);
        $stmt->bindValue(':couponHeaderCode',$header_code,PDO::PARAM_STR);
        $stmt->bindValue(':couponCode',$dc_code,PDO::PARAM_STR);
        $stmt->bindValue(':couponAmount',$GLOBALS["shop_language"]["nl_coupon_amount"],PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $errorInfo = $pdo->errorInfo();
            $errorString = implode($errorInfo,PHP_EOL);
            throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
        }

    }
    return $dc_code;

}


function random_str($length, $keyspace = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function get_client_ip_address() {
    $ipAddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipAddress = 'UNKNOWN';
    return $ipAddress;
}

function cartesian_product(array $input) {
    // filter out empty values
    $input = array_filter($input);

    $result = [[]];

    foreach ($input as $key => $values) {
        $append = array();

        foreach($result as $product) {
            foreach($values as $item) {
                $product[$key] = $item;
                $append[] = $product;
            }
        }

        $result = $append;
    }

    return $result;
}

function cartesian_product_generator(array $input)
{
    if ($input) {
        if ($u = array_pop($input)) {
            foreach (cartesian_product_generator($input) as $p) {
                foreach ($u as $v) {
                    yield $p + [count($p) => $v];
                }
            }
        }
    } else {
        yield[];
    }
}

function dropAllActionItemsOnUrlQuery():string{
    $actualURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $actualURLParts = parse_url($actualURL);
    $actualURLPartsQuery = explode('&',$actualURLParts["query"]);
    $i = 0;
    foreach ($actualURLPartsQuery as $actualUrlPartQuery){
        $actualUrlPartQuery = explode('=',$actualUrlPartQuery);
        if ($actualUrlPartQuery[0] === 'action' || $actualUrlPartQuery[0] === 'action_id') {
            unset($actualURLPartsQuery[$i]);
        }
        $i++;
    }
    return implode('&',$actualURLPartsQuery);
}

function get_false_password_login_counter($ipHash,$loginHash)
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " 
            
            SELECT 
    false_login.*
FROM
    false_login
WHERE
    ( false_login.ip_hash = :ip_hash  OR false_login.login_hash = :login_hash )
        ";
    $params = [
        [':ip_hash', $ipHash, PDO::PARAM_STR],
        [':login_hash', $loginHash, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $result = $pdo->getResultArray();
    return $result;
}
function add_false_password_loign_counter($ipHash,$loginHash, $counter = 1, $nextLoginTime, $blockCounter = 0  )
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " 
            
            INSERT INTO `false_login`
                (`ip_hash`, `login_hash`, `total_counter`, `next_login_time`, block_counter)
                VALUES 
                (:ip_hash ,:login_hash, :counter, :next_login_time, 0)
                ON DUPLICATE KEY UPDATE block_counter = :block_counter , next_login_time = :next_login_time

                        ";

    if($counter == 1)
    {
        $prepStatement = " 
            
            INSERT INTO `false_login`
                (`ip_hash`, `login_hash`, `total_counter`, `next_login_time`, block_counter)
                VALUES 
                (:ip_hash ,:login_hash, :counter, :next_login_time, :block_counter)
                ON DUPLICATE KEY UPDATE total_counter = total_counter + 1 , next_login_time = :next_login_time

                        ";
    }
    $params = [
        [':ip_hash', $ipHash, PDO::PARAM_STR],
        [':login_hash', $loginHash, PDO::PARAM_STR],
        [':counter', $counter, PDO::PARAM_STR],
        [':next_login_time', $nextLoginTime, PDO::PARAM_STR],
        [':block_counter', $blockCounter, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
}

/**
 * @param $ipHash
 * @param $loginhash
 * @throws ErrorException
 */
function delete_false_password_login_counter($ipHash, $loginhash)
{
    static $prepStatement = "
            DELETE FROM  `false_login` WHERE ip_hash = :ip_hash OR login_hash = :login_hash
                        ";

    $pdo = get_main_db_pdo_from_env_single_instance();

    $stmt = $pdo->prepare($prepStatement);
    $stmt->bindValue(':ip_hash',$ipHash,PDO::PARAM_STR);
    $stmt->bindValue(':login_hash',$loginhash,PDO::PARAM_STR);
    $stmt->execute();
}

function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}
function is_valid_image($file)
{
    // This is an array that holds all the valid image MIME types
    $valid_types = array("image/jpg", "image/pjpg", "image/pjpeg", "image/jpeg", "image/bmp", "image/x-xbitmap", "image/gif", "image/png", "image/x-png");
    $type = str_replace(array('pjpg','pjpeg','x-png','x-xbitmap'),array('jpg','jpeg','png','bmp'),$file['type']);
    $content = mime_content_type($file['tmp_name']);
    if ($type <> $content) {
        return false;
    }

    if (!in_array($content, $valid_types)) {
        return false;
    }
    return true;
}

function is_valid_file($file)
{

    // This is an array that holds all the valid  MIME types
    $valid_types = array(
        "image/jpg",
        "image/jpeg",
        "image/bmp",
        "image/gif",
        "image/png",
        'audio/basic',
        'audio/x-au',
        'video/avi',
        'video/msvideo',
        'video/x-msvideo',
        'video/avs-video',
        'video/dl',
        'video/x-dl',
        'application/msword',
        'application/msword',
        'video/fli',
        'video/x-fli',
        'audio/mod',
        'audio/x-mod',
        'video/quicktime',
        'video/x-sgi-movie',
        'audio/x-mpeg',
        'video/x-mpeg',
        'video/x-mpeq2a',
        'audio/mpeg3',
        'audio/x-mpeg-3',
        'video/x-mpeg',
        'video/mpeg',
        'audio/mpeg',
        'application/powerpoint',
        'application/vnd.ms-powerpoint',
        'application/x-mspowerpoint',
        'application/mspowerpoint',
        'application/plain',
        'text/plain',
        'video/vdo',
        'video/vivo',
        'video/vnd.vivo',
        'audio/voc',
        'audio/x-voc',
        'audio/wav',
        'audio/x-wav',
        'application/msword',
        'application/msword',
        'application/excel',
        'application/x-excel',
        'application/vnd.ms-excel',
        'application/x-msexcel',
        'application/pdf'
    );

    $allowCompressedFiles = getenv('ALLOW_COMPERESSED_FILES');

    if($allowCompressedFiles)
    {
        array_push($valid_types,   'application/x-compress',
            'application/x-compressed',
            'application/x-compressed',
            'application/x-zip-compressed',
            'application/zip',
            'multipart/x-zip');
    }

    $type = $file['type'];
    $content = mime_content_type($file['tmp_name']);
    if ($type <> $content) {
        return false;
    }
    if (!in_array($content, $valid_types)) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileInfo = $finfo->file($file['tmp_name']);

    if (!in_array($fileInfo, $valid_types)) {
        return false;
    }

    return true;
}


function get_main_db_pdo_from_env()
{
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');
    $dsn = 'mysql:dbname=' . $dbSchema . ';host=' . $dbHost . ';port=' . (int)$dbPort . ';charset=utf8mb4';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    return $pdo;
}


function get_main_db_pdo_from_env_single_instance()
{
    static $pdo;
    if (null === $pdo) {
        $pdo = get_main_db_pdo_from_env();
    }
    return $pdo;
}


function get_tracking_db_pdo_from_env()
{
    $dbHost = getenv('TRACKING_MYSQL_DB_HOST');
    $dbPort = (int)getenv('TRACKING_MYSQL_DB_PORT');
    $dbUser = getenv('TRACKING_MYSQL_DB_USER');
    $dbSchema = getenv('TRACKING_MYSQL_DB_SCHEMA');
    $dbPass = getenv('TRACKING_MYSQL_DB_PASS');
    $dsn = 'mysql:dbname=' . $dbSchema . ';host=' . $dbHost . ';port=' . (int)$dbPort . ';charset=utf8mb4';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    return $pdo;
}


function get_tracking_db_pdo_from_env_single_instance()
{
    static $pdo;
    if (null === $pdo) {
        $pdo = get_tracking_db_pdo_from_env();
    }
    return $pdo;
}


function get_workerqueue_db_pdo_from_env()
{
    $dbHost = getenv('WORKERQUEUE_MYSQL_DB_HOST');
    $dbPort = (int)getenv('WORKERQUEUEMYSQL_DB_PORT');
    $dbUser = getenv('WORKERQUEUE_MYSQL_DB_USER');
    $dbSchema = getenv('WORKERQUEUE_MYSQL_DB_SCHEMA');
    $dbPass = getenv('WORKERQUEUE_MYSQL_DB_PASS');
    $dsn = 'mysql:dbname=' . $dbSchema . ';host=' . $dbHost . ';port=' . (int)$dbPort . ';charset=utf8mb4';
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    return $pdo;
}


function get_workerqueue_db_pdo_from_env_single_instance()
{
    static $pdo;
    if (null === $pdo) {
        $pdo = get_workerqueue_db_pdo_from_env();
    }
    return $pdo;
}


/**
 * @param PDO $pdo
 * @param $company
 * @param $category_shop_code
 * @param $language_code
 * @param $input_page
 * @return mixed
 * @throws ErrorException
 */
function get_linked_category_line_no_for_page_id(PDO $pdo, $page_id): int
{

    static $query = '
        SELECT 
            line_no
        FROM
          shop_category
        WHERE
          company = :company AND
          shop_code = :category_shop_code AND 
          language_code = :language_code AND
          in_use_with_page_id = :page_id 
    ';

    $page = get_page_by_id($pdo, $page_id);
    $language = get_language_by_id($pdo, $page["main_language_id"]);
    $company = $language["company"];
    $shop_code = $language["shop_code"];
    $language_code = $language["shop_language_code"];
    $shop = get_shop($company,$shop_code);
    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':company', $company, PDO::PARAM_STR);
    $stmt->bindValue(':category_shop_code', $category_shop_code, PDO::PARAM_STR);
    $stmt->bindValue(':language_code', $language_code, PDO::PARAM_STR);
    $stmt->bindValue(':page_id', (int)$page_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        $errorInfo = $pdo->errorInfo();
        $errorString = implode($errorInfo, PHP_EOL);
        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
    }
    $categoryRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $categoryLineNo = (is_array($categoryRow) && array_key_exists('line_no',$categoryRow)) ? (int)$categoryRow["line_no"] : 0;
    return $categoryLineNo;
}

/**
 * @param PDO $pdo
 * @param $page_id
 * @return array
 * @throws ErrorException
 */
function get_page_by_id(PDO $pdo, $page_id)
{

    static $query = '
        SELECT 
            *
        FROM
          main_page
        WHERE
          id = :page_id 
    ';

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':page_id', (int)$page_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        $errorInfo = $pdo->errorInfo();
        $errorString = implode($errorInfo, PHP_EOL);
        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
    }
    $pageRow = $stmt->fetch(PDO::FETCH_ASSOC);
    return (array)$pageRow;
}

function get_language_by_id(PDO $pdo, $language_id)
{
    static $query = '
        SELECT 
            *
        FROM
          main_language
        WHERE
          id = :language_id 
    ';

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':language_id', (int)$language_id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        $errorInfo = $pdo->errorInfo();
        $errorString = implode($errorInfo, PHP_EOL);
        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
    }
    $languageRow = $stmt->fetch(PDO::FETCH_ASSOC);
    return (array)$languageRow;
}

function customizeUrl($useLanguage = true, $site = '', $language = '')
{
    if ($site == '') {
        $site = $GLOBALS['site'];
    }
    if ($language == '') {
        $language = $GLOBALS['language'];
    }

    if($useLanguage)
    {
        $url = $site['code'] . '/'. $language['code'];
        if($site['is_unique_site'])
        {
            $url = $language['code'];
        }
    }
    else
    {
        $url = $site['code'].'/';
        if($site['is_unique_site'])
        {
            $url = "";
        }
    }

    return $url;
}

function get_pages($site_id, $language_id, $selected_id, $include_sub = 1, $from_level = 1, $to_level = 3) {
    echo "[[\"".$GLOBALS["tc"]["please_chose"]."\",\"0\"]";
    get_pages_rek($site_id,$language_id, 0, 1, "", $from_level, $to_level, $selected_id);
    echo "]";
}

function get_pages_rek($site_id, $language_id, $parent_id, $level, $navcode, $from_level, $to_level, $selected_id) {
    $spaces = "";
    if ($GLOBALS["is_webform"]) {
        $count = 0;
        if ($GLOBALS["has_languages"]) {
            $count = -1;
        }
    } else {
        $count = 1;
    }



    while($count < $level) {
        $spaces .= "--";
        $count++;
    }


    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = " . $parent_id;
    $query2 = "SELECT * FROM main_navigation WHERE main_site_id = " . $site_id . " AND main_language_id = " . $language_id . $parent_id_query . " ORDER BY sorting ASC";
    //die($query2);
    $result = @mysqli_query($GLOBALS['mysql_con'],$query2);


    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active = "";
            if ($show_level) {
                //echo "\n<option " . $active . "value=\"" . $nav["id"] . "\">" .  $spaces . $nav["menu_name"] . "</option>";

                $query1  = "SELECT * FROM main_navigation WHERE id = " . $nav["id"];
                $result1 = @mysqli_query($GLOBALS['mysql_con'], $query1);
                $targetlink = "0";
                if (@mysqli_num_rows($result1) == 1) {
                    $site_link = @mysqli_fetch_array($result1);
                    $sitec = $GLOBALS["site"];
                    $languagec = $GLOBALS["language"];
                    if ($GLOBALS["is_webform"]) {
                        $query_s = "SELECT * FROM main_site WHERE id = '".$site_id."' LIMIT 1";
                        $query_l = "SELECT * FROM main_language WHERE id = '".$language_id."' LIMIT 1";
                        $result_s = mysqli_query($GLOBALS["mysql_con"],$query_s);
                        $result_l = mysqli_query($GLOBALS["mysql_con"],$query_l);
                        $sitec = mysqli_fetch_array($result_s);
                        $languagec = mysqli_fetch_array($result_l);
                    }
                    $targetlink       = navigation_path($sitec, $languagec, $site_link);
                }


                echo ",[\"" .  $spaces . $nav["menu_name"] . "\",\"".$targetlink."\"]";

                get_pages_rek($site_id, $language_id, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level, $selected_id );
            }
        }
    } else {
        if ($level == 1) {
            echo "[\"-\",\"0\"]";
        }
    }
    return true;
}


function check_bind_site_code_from_domain()
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
    } catch (\Exception $e) {
        //Do noting
    }
    return $siteCode;
}

/**
 * @param PDO $pdo
 * @param string $currency_code
 * @param string $company
 * @return bool
 * @throws ErrorException
 */
function is_valid_currency_for_company(\PDO $pdo, string $currency_code, string $company) : bool
{
    static $query = '
        SELECT EXISTS(
            SELECT 1 
            FROM shop_currency
            WHERE 
                  code = :currency_code
              AND company = :company
            LIMIT 1
        ) 
    ';
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':currency_code',$currency_code,\PDO::PARAM_STR);
    $stmt->bindValue(':company',$company,\PDO::PARAM_STR);
    // Possible errors are intended bubble to global error handling for redirect to error page
    $stmt->execute();
    $resultRow = $stmt->fetch(\PDO::FETCH_NUM);
    if (!is_array($resultRow) || !array_key_exists(0,$resultRow)) {
        $msg = "Could not fetch result for query [$query] with data [company => $company,"
            . "currency_code => $currency_code]";
        throw new ErrorException($msg);
    }
    return (bool)$resultRow[0];
}

function update_visitor_currency_in_db(\PDO $pdo, int $visitor_id, string $currency_code) : void
{
    static $query = '
        UPDATE main_visitor
        SET currency_code = :currency_code
        WHERE id = :visitor_id
    ';
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':visitor_id',$visitor_id,\PDO::PARAM_INT);
    $stmt->bindValue(':currency_code',$currency_code,\PDO::PARAM_STR);
    // Possible errors are intended bubble to global error handling for redirect to error page
    $stmt->execute();
}

/**
 * @param $session_id
 * @return array
 */
function get_visitor_data_by_session_id($session_id)
{
    $prepStatement = 'SELECT * FROM main_visitor WHERE session_id = :session_id ';

    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':session_id', $session_id, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArray = $pdo->getResultArray();

        if (array_key_exists(0, $resArray) && array_key_exists('id', $resArray[0]) && $resArray[0]['id'] > 0) {
            return $resArray[0];
        }
    } catch (\Throwable $e) {
        //Do noting
    }
    return $resArray;
}

function get_logger(string $moduleName = 'global') : \Psr\Log\LoggerInterface
{
    static $logDir;
    static $loggers;
    if (null === $loggers) {
        $loggers['null'] = new \Psr\Log\NullLogger();
    }
    $logger = $loggers['null'];
    if (empty($logDir)) {
        $root = dirname(dirname(__DIR__));
        $logDir = $root . DIRECTORY_SEPARATOR . 'logs';
    }
    if (empty($moduleName)) {
        throw new DomainException('ModuleName cannot be empty');
    }

    $logLevel = getenv('APP_LOG_LEVEL');
    $logLevelInt = \Monolog\Logger::toMonologLevel($logLevel);
    $logFilePath = $logDir . DIRECTORY_SEPARATOR . $moduleName . 'log';
    $logFileHash = md5($logFilePath);
    if (!array_key_exists($logFileHash,$loggers)) {
        $logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, $logLevelInt);
        $processor = new \Monolog\Processor\PsrLogMessageProcessor();
        $logFileHandler->pushProcessor($processor);
        $logger = new \Monolog\Logger($moduleName, [$logFileHandler]);
        $loggers[$logFileHash] = $logger;
    }
    return $logger;
}


function headerFunctionBridge(string $content)
{

    static $statusLineRegex = '/^HTTP\/(\d.\d) (\d{3}) (.*?)$/';
    static $locationLineRegex = '/^[lL]ocation\s*:\s*(.*?)$/';
    static $generalHeaderRegex = '/^([a-zA-Z\-_0-9]+):\s*(.*?)$/';

    $response = $GLOBALS['GLOBAL_RESPONSE'] ?? null;
    $prodeployRouting = $GLOBALS['PRODEPLOY_ROUTING'] ?? false;
    if ($response instanceof \Psr\Http\Message\ResponseInterface && $prodeployRouting) {
        $result = [];
        if (preg_match($statusLineRegex,$content,$result,PREG_PATTERN_ORDER)) {
            [1 => $httpVersion, 2 => $statusCode, 3 => $reasonPhrase] = $result;
            $response = $response->withProtocolVersion($httpVersion);
            $response = $response->withStatus((int)$statusCode,$reasonPhrase);
            $GLOBALS['GLOBAL_RESPONSE'] = &$response;
        } elseif(preg_match($locationLineRegex,$content,$result)) {
            $location = $result[1];
            $response = $response->withHeader('Location',$location);
            $GLOBALS['GLOBAL_RESPONSE'] = &$response;
            exit(0);
        } elseif(preg_match($generalHeaderRegex,$content,$result)) {
            [1 => $headerName, 2 => $headerValue] = $result;
            $response = $response->withHeader($headerName,$headerValue);
            $GLOBALS['GLOBAL_RESPONSE'] = &$response;
        }
    } else {
        header($content);
    }
}