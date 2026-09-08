<?

switch ($_REQUEST["action"]) {
    case 'edit':
        edit_site();
        break;
    case 'delete':
        delete_site();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_cardform.inc.php';
        break;
    case 'save':
        save_site();
        break;
    case 'send_sitemap':
        send_sitemap();
        break;
    case 'copy':
        copy_site();
        break;
    case 'geoip':
        update_geo_ip_files();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_listform.inc.php';
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_listform.inc.php';
        break;
}

function update_geo_ip_files()
{
    $baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
    $envDir = rtrim($baseDirectory, '/') . '/config/geolocation';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    //file_get_contents(getenv('Geo_Lite_Country_File_URL'));
    // file_get_contents(getenv('Geo_Lite_Country_IPV6_File_URL'));
    $fileName = "GeoIP.dat.gz";
    $url = getenv('Geo_Lite_Country_File_URL');
    $destination = $envDir."/".$fileName;
    $fp = fopen ($destination, 'w+');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
    curl_exec( $ch );
    curl_close( $ch );
    fclose( $fp );

    exec('gunzip '.$destination);

    $fileName = "GeoIPv6.dat.gz";
    $url = getenv('Geo_Lite_Country_IPV6_File_URL');
    $destination = $envDir."/".$fileName;
    $fp = fopen ($destination, 'w+');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
    curl_exec( $ch );
    curl_close( $ch );
    fclose( $fp );

    exec('gunzip '.$destination);



}

function copy_site() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_functions.php';

    $SiteFunctions = new SiteFunctions();

    $step = (isset($_REQUEST['next_step']) ? $_REQUEST['next_step'] : "");
    $copyError = array();
    switch($step) {
        case 'copy_site':

            $bindingSite =  checkBindingUrl($_REQUEST["input_site_url"]);
            if(is_array($bindingSite) && count($bindingSite) > 0 )
            {
                $copyError[count($copyError)] = $translation->get("site_binding_error");
                break;
            }
            // loeschen von ID Zuordnungen
            $query = "DELETE FROM copy_link WHERE session_id = '" . session_id() . "'";
            @mysqli_query($GLOBALS['mysql_con'], $query);
            
            if(!$SiteFunctions->copy_site($_REQUEST["input_id"])) {
                $copyError = $SiteFunctions->errorMessages;
                break;
            }

            echo json_encode(array("next_step" => "copy_language", "message" => $translation->get("copy_start_all_languages"), "copy_language_id" => 0));
            return;
        case 'copy_language':
            if(!$SiteFunctions->copy_language($_REQUEST["input_id"])) {
                $copyError = $SiteFunctions->errorMessages;
                break;
            }

            if($SiteFunctions->copyLanguageId == 0) {
                // keine Sprachen mehr zum kopieren vorhanden.
                break;
            }

            echo json_encode(array("next_step" => "copy_siteparts", "message" => $SiteFunctions->message, "copy_language_id" => $SiteFunctions->copyLanguageId));
            return;

        case 'copy_siteparts';
        case 'copy_collection_setup';
        case 'copy_collection';
        case 'copy_component';
        case 'copy_main_page';
        case 'copy_main_navigation';
            $newSiteId = $SiteFunctions->get_new_id($_REQUEST["input_id"], "main_site");
            $LanguageFunctions = new LanguageFunctions($_REQUEST["copy_language_id"], $newSiteId);
            if(!$LanguageFunctions->$step()) {
                $copyError = $LanguageFunctions->errorMessages;
                break;
            }

            $nextStep = $LanguageFunctions->nextStep;
            if($nextStep == "") {
                // naechste Sprache kopieren.
                $nextStep = "copy_language";
            }

            echo json_encode(array("next_step" => $nextStep, "message" => $LanguageFunctions->message, "copy_language_id" => $_REQUEST["copy_language_id"]));
            return;
        default:
            break;
    }

    if(count($copyError)) {
        $errorDivs = array();
        foreach($copyError as $error) {
            $errorDivs[] = "<div class=\"errorbox\">" . $error . "</div>\n";
        }
        echo json_encode(array("next_step" => "", "message" => $errorDivs, "error" => 1));
    } else {
        echo json_encode(array("next_step" => "", "message" => array('<div class="successbox">' . $translation->get("copy_site_success") . '</div>'), "error" => 0));
    }
}

function edit_site() {

    if ($_REQUEST["input_id"] <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *,
            (SELECT COUNT(id)
                FROM main_navigation
                 WHERE main_site_id = main_site.id) AS 'no_of_navigation'
                  FROM main_site
                   WHERE id = :id LIMIT 1
        ";
        $params = [
            [':id', $_REQUEST["input_id"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $input_site = $result[0];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_listform.inc.php';
    }

}

function delete_site() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_site WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "DELETE FROM main_language WHERE main_site_id = '" . (int)$_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if($GLOBALS["site"]['id'] == $_REQUEST["input_id"]) {
            $messages[] = '<div class="infobox">' . $translation->get("website_msg_info2") . '<br><a href="/dc/?action=admin_logout">' . $translation->get("website_msg_tologin") . '</a></div>';
            if(IS_AJAX) {
                headerFunctionBridge('EDIT_ERROR: 1');
                $input_site = array();
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_cardform.inc.php';
                return;
            }
        }
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_listform.inc.php';
}

function save_site() {
    $messages = array();
    $error    = FALSE;

    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $login_required     = ($_REQUEST["input_login_required"] == "on") ? '1' : '0';
    $use_session_id     = ($_REQUEST["input_use_session_id"] == "on") ? '1' : '0';
    $create_xml_sitemap = ($_REQUEST["input_create_xml_sitemap"] == "on") ? '1' : '0';
    $is_standard_site   = ($_REQUEST["input_is_standard_site"] == "on") ? '1' : '0';
    $use_browser_language_detection = ($_REQUEST["input_use_browser_language_detection"] == "on") ? '1' : '0';
    $use_ip_detection = ($_REQUEST["input_use_ip_detection"] == "on") ? '1' : '0';

    $is_unique_site   = ($_REQUEST["input_is_unique_site"] == "on") ? 1 : 0;

    $mysql_update_standard = "UPDATE main_site SET is_standard_site = 0";


    $site = checkBindingUrl($_REQUEST["input_site_url"]);
    if(count($site) > 0 && $_REQUEST["input_id"] <> $site["id"] )
    {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("site_binding_error") . "</div>\n";
        $error      = TRUE;
    }
    else
    {
        if ($_REQUEST["input_id"] <> '') {
            $query     = "UPDATE main_site 
                      SET code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "', 
                      name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "', 
                      std_main_language_id = '" . $_REQUEST["input_std_main_language_id"] . "', 
                      is_standard_site = " . $is_standard_site . ", 
                      login_required = '" . $login_required . "', 
                      google_tag_container_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_google_tag_container_id"]) . "', 
                      google_analytics_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_google_analytics_id"]) . "', 
                      facebook_pixel_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_facebook_pixel_id"]) . "', 
                      use_session_id = '" . $use_session_id . "', 
                      create_xml_sitemap = '" . $create_xml_sitemap . "', 
                      site_url = '" . $_REQUEST["input_site_url"] . "',
                      use_browser_language_detection = '" . $use_browser_language_detection . "', 
                      use_ip_detection =  '" . $use_ip_detection . "', 
                      default_country_codes = '" .  $_REQUEST["input_default_country_codes"] . "',
                      is_unique_site = " .  $is_unique_site . " 
                    WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
            $this_site = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site WHERE id = '" . $_REQUEST["input_id"] . "'"));
        } else {
            $query    = "INSERT INTO main_site (id,code,name,std_main_language_id, is_standard_site, login_required,google_analytics_id,use_session_id,create_xml_sitemap,site_url,use_browser_language_detection,use_ip_detection ,default_country_codes, facebook_pixel_id, google_tag_container_id, is_unique_site) 
                      VALUES (
                        NULL,
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
                        NULL, 
                        " . $is_standard_site . ", 
                        '" . $login_required . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_google_analytics_id"]) . "',
                        '" . $use_session_id . "',
                        '" . $create_xml_sitemap . "',
                        '" . $_REQUEST["input_site_url"] . "',
                        '" . $use_browser_language_detection . "',
                          '" . $use_ip_detection . "',
                        '" . $_REQUEST["input_default_country_codes"] . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_facebook_pixel_id"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_google_tag_container_id"]) . "',
                        ".$is_unique_site."
                      )";
            $inserted = TRUE;
        }
    }


    if (($_REQUEST["input_code"] == '') | ($_REQUEST["input_name"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
        $error      = TRUE;
    }
    if ($_REQUEST["input_code"] <> urlencode($_REQUEST["input_code"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
        $error      = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_site WHERE code = '" . $_REQUEST["input_code"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }

    $input_site["id"] = $_REQUEST["input_id"];

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $mysql_update_standard);
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $messages[]     = '<div class="successbox">' . $translation->get("website_msg_success1") . '</div>';
            $last_insert_id = mysqli_insert_id($GLOBALS['mysql_con']);
            $new_site       = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_site WHERE id = '" . $last_insert_id . "'"));
            $query_2        = "INSERT INTO main_language (main_site_id, code, name, site_name, site_title_name) VALUES ('" . $new_site["id"] . "', 'de', 'Deutsch', '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $new_site["name"]) . "', '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $new_site["name"]) . "')";
            @mysqli_query($GLOBALS['mysql_con'], $query_2);
            $last_insert_id = mysqli_insert_id($GLOBALS['mysql_con']);
            $std_language   = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT id FROM main_language WHERE id = '" . $last_insert_id . "'"));
            $query_3        = "UPDATE main_site SET std_main_language_id = '" . $std_language["id"] . "' WHERE id = '" . $new_site["id"] . "'";
            @mysqli_query($GLOBALS['mysql_con'], $query_3);
            $input_site["id"] = $new_site["id"];
        } else {

            $messages[] = '<div class="successbox">' . $translation->get("website_msg_success2") . '</div>';
        }


        // Wenn die aktuell gewählte Seite bearbeitet wurde, wird ein Relog erbeten. - MH 02. Dezember 2009


        if ($this_site["code"] == $_REQUEST["site"]) {
            if ($_REQUEST["input_code"] <> $this_site["code"]) {
                $messages[] = '<div class="infobox">' . $translation->get("website_msg_info1") . '<br><a href="/dc/?action=admin_logout">' . $translation->get("website_msg_tologin") . '</a></div>';
            } else {

            }
        }
    }

    $input_site["code"]                 = $_REQUEST["input_code"];
    $input_site["name"]                 = $_REQUEST["input_name"];
    $input_site["std_main_language_id"] = $_REQUEST["input_std_main_language_id"];
    $input_site["login_required"]       = $login_required;
    $input_site["is_standard_site"]     = $is_standard_site;
    $input_site["no_of_navigation"]     = @mysqli_num_rows(@mysqli_query($GLOBALS['mysql_con'], "SELECT id FROM main_navigation WHERE main_site_id = '" . $_REQUEST["input_id"]."'"));
    $input_site["google_analytics_id"]  = $_REQUEST["input_google_analytics_id"];
    $input_site["facebook_pixel_id"]  = $_REQUEST["input_facebook_pixel_id"];
    $input_site["use_session_id"]       = $use_session_id;
    $input_site["create_xml_sitemap"]   = $create_xml_sitemap;
    $input_site["site_url"]             = $_REQUEST["input_site_url"];
    $input_site["use_browser_language_detection"]  = $use_browser_language_detection;
    $input_site["use_ip_detection"]  = $use_ip_detection;
    $input_site["default_country_codes"]  = $_REQUEST["input_default_country_codes"];
    $input_site["google_tag_container_id"]  = $_REQUEST["input_google_tag_container_id"];
    $input_site["is_unique_site"]     = $is_unique_site;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_site_cardform.inc.php';
}

function checkBindingUrl($url)
{
    $prepStatement = "
      SELECT 
            id
      FROM
           main_site
      WHERE
          site_url = :domain 
            AND is_unique_site = 1
      ORDER BY id LIMIT 1";

    $siteDomain = $_SERVER['SERVER_NAME'];
    $dbHost = getenv('MAIN_MYSQL_DB_HOST');
    $dbPort = (int)getenv('MAIN_MYSQL_DB_PORT');
    $dbUser = getenv('MAIN_MYSQL_DB_USER');
    $dbSchema = getenv('MAIN_MYSQL_DB_SCHEMA');
    $dbPass = getenv('MAIN_MYSQL_DB_PASS');

    try {
        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($dbHost, $dbPort, $dbSchema, $dbUser, $dbPass);
        $params = [
            [':domain', $url, PDO::PARAM_STR],
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


?>