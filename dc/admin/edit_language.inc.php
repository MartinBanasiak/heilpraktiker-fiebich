<?

$messages = array();
$error    = FALSE;

switch ($_REQUEST["action"]) {
    case 'edit':
        edit_language();
        break;
    case 'delete':
        delete_language();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_cardform.inc.php';
        break;
    case 'save':
        save_language();
        break;
    case 'copy':
        copy_language();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_listform.inc.php';
        break;
}

function copy_language() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_functions.php';

    $step = (isset($_REQUEST['next_step']) ? $_REQUEST['next_step'] : "");
    $copyError = array();

    if($step == "copy_language") {
        // erster Schritt Tabelle leeren
        $query = "DELETE FROM copy_link WHERE session_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],session_id()) . "'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

    switch($step) {
        case 'copy_language';
        case 'copy_siteparts';
        case 'copy_collection_setup';
        case 'copy_collection';
        case 'copy_component';
        case 'copy_main_page';
        case 'copy_main_navigation';
            $LanguageFunctions = new LanguageFunctions($_REQUEST["input_id"]);
            if(!$LanguageFunctions->$step()) {
                $copyError = $LanguageFunctions->errorMessages;
                break;
            }

            $nextStep = $LanguageFunctions->nextStep;
            if($nextStep == "") {
                break; // switch abbrechen und damit kopiervorgang beenden.
            }

            echo json_encode(array("next_step" => $nextStep, "message" => $LanguageFunctions->message));
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
        echo json_encode(array("next_step" => "", "message" => array('<div class="successbox">' . $translation->get("copy_language_success") . '</div>'), "error" => 0));
    }
}

function edit_language( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *, 
          (SELECT COUNT(id) FROM main_navigation WHERE
           main_language_id = main_language.id) AS 'no_of_navigation' 
           FROM main_language WHERE id = :id LIMIT 1
        ";
        $params = [
            [':id',$input_id, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $input_language = $result[0];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_cardform.inc.php';
        }
    } else {
        $input_language = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_cardform.inc.php';
    }
}

function delete_language() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_language WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if($GLOBALS["language"]['id'] == $_REQUEST["input_id"]) {
            $messages[] = '<div class="infobox">' . $translation->get("language_msg_info1") . '<br><a href="/dc/?action=admin_logout">' . $translation->get("website_msg_tologin") . '</a></div>';
            if(IS_AJAX) {
                headerFunctionBridge('EDIT_ERROR: 1');
                $input_language = array();
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_cardform.inc.php';
                return;
            }
        }
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_listform.inc.php';
}

function save_language() {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $active = 0;
    if($_REQUEST["input_active"] == "on")
    {
        $active = 1;
    }

    $catalogLogin = 0;
    if($_REQUEST["input_catalog_login"] == "on")
    {
        $catalogLogin = 1;
    }


    if ($_REQUEST["input_id"] <> '') {
        $query    = "UPDATE main_language 
                    SET 
                        company = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_company"]) . "', 
                        shop_code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_shopcode"]) . "', 
                        shop_language_code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_language_code"]) . "', 
                        code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "', 
                        locale_code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_REQUEST['input_locale_code']) . "',
                        name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "', 
                        site_name = '', 
                        std_main_navigation_id = '" . $_REQUEST["input_std_main_navigation_id"] . "', 
                        site_title_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_site_title_name"]) . "', 
                        meta_description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_description"]) . "', 
                        meta_keywords = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_keywords"]) . "', 
                        main_layout_id = " . (int)$_REQUEST["input_main_layout_id"] . ", 
                        logout_site_id = " . (int)$_REQUEST["input_login_site"] . ", 
                        logout_language_id = " . (int)$_REQUEST["input_login_language"] . ", 
                        logout_navigation_id = " . (int)$_REQUEST["input_login_navigation"] . ",
                         related_language_codes = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_REQUEST["input_related_language_codes"]) . "',
                         active = " . $active . "
                    WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_language (id, main_site_id, code,locale_code,name,site_name,std_main_navigation_id,site_title_name,meta_description,meta_keywords,main_layout_id,company,shop_code,shop_language_code,logout_site_id,logout_language_id,logout_navigation_id,related_language_codes, active) 
                    VALUES (
                        NULL, 
                        " . (int)$GLOBALS["site"]["id"] . ", 
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_locale_code"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
                        '',
                        " . (int)$_REQUEST["input_std_main_navigation_id"] . ",
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_site_title_name"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_description"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_keywords"]) . "',
                        " . (int)$_REQUEST["input_main_layout_id"] . ",
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_company"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_shopcode"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_language_code"]) . "',
                        " . (int)$_REQUEST["input_login_site"] . ",
                        " . (int)$_REQUEST["input_login_language"] . ",
                        " . (int)$_REQUEST["input_login_navigation"] . ",
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_REQUEST["input_related_language_codes"]) . "',
                         " . $active . "
                    )";
        $inserted = TRUE;
    }

    if (($_REQUEST["input_code"] == '') | ($_REQUEST["input_name"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
        $error      = TRUE;
    }
    if ($_REQUEST["input_code"] <> urlencode($_REQUEST["input_code"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
        $error      = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_language WHERE code = '" . $_REQUEST["input_code"] . "' AND main_site_id = '" . $GLOBALS["site"]["id"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }


    if (!$error) {
        mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted === TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("language_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("language_msg_success2") . '</div>';
            if($GLOBALS["language"]["code"] == $_REQUEST["input_code"]) {
                header('EDIT_ERROR: 1'); // schliessen des overlays verhindern
                $messages[] = '<div class="infobox">' . $translation->get("language_msg_info1") . '<br><a href="/dc/?action=admin_logout">' . $translation->get("website_msg_tologin") . '</a></div>';
            }
        }
        edit_language($input_id, $messages);
    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_language["logout_site"]                = $_REQUEST["input_login_site"];
        $input_language["logout_language"]            = $_REQUEST["input_login_language"];
        $input_language["logout_navigation"]          = $_REQUEST["input_login_navigation"];
        $input_language["company"]                    = $_REQUEST["input_company"];
        $input_language["shop_code"]                  = $_REQUEST["input_shopcode"];
        $input_language["shop_language_code"]         = $_REQUEST["input_language_code"];
        $input_language["id"]                         = $_REQUEST["input_id"];
        $input_language["code"]                       = $_REQUEST["input_code"];
        $input_language["locale_code"]                = $_REQUEST["input_locale_code"];
        $input_language["name"]                       = $_REQUEST["input_name"];
        $input_language["std_std_main_navigation_id"] = $_REQUEST["input_std_main_navigation_id"];
        $input_language["site_title_name"]            = $_REQUEST["input_site_title_name"];
        $input_language["meta_description"]           = $_REQUEST["input_meta_description"];
        $input_language["meta_keywords"]              = $_REQUEST["input_meta_keywords"];
        $input_language["main_navigation_id"]         = $_REQUEST["input_main_layout_id"];
        $input_language["no_of_navigation"]           = (int)@mysqli_num_rows(@mysqli_query($GLOBALS['mysql_con'], "SELECT id FROM main_navigation WHERE main_language_id = '" . $_REQUEST["input_id"]."'"));
        $input_language["related_language_codes"]     = $_REQUEST["input_related_language_codes"];
        $input_language["catalog_login"]                = $_REQUEST["input_catalog_login"];
        $input_language["active"]                = $_REQUEST["input_active"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_cardform.inc.php';
    }
}

?>