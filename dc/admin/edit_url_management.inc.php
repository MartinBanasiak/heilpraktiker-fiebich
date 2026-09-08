<?

switch ($_REQUEST["action"]) {
    case 'edit':
        edit_url_management();
        break;
    case 'delete':
        delete_url_management();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_cardform.inc.php';
        break;
    case 'save':
        save_url_management();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_listform.inc.php';
        break;
}

function edit_url_management() {
    if ($_REQUEST["input_id"] <> '') {
        $query  = "SELECT * FROM main_rewrite_rules WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_url_management = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_listform.inc.php';
    }
}

function delete_url_management() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_rewrite_rules WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_listform.inc.php';
}

function save_url_management() {
    $messages = array();
    $error    = FALSE;

    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $active   = ($_REQUEST["input_active"] == "on") ? '1' : '0';

    if ($_REQUEST["input_id"] <> '') {
        $query     = "UPDATE main_rewrite_rules 
                      SET 
                      old_url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_old_url"]) . "', 
                      new_url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_new_url"]) . "', 
                      rewrite_code = '" . $_REQUEST["input_rewrite_code"] . "', 
                      active = " . $active . "
                    WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        $input_url_management["id"] = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_rewrite_rules 
                    (id, old_url, new_url, rewrite_code, active) 
                      VALUES (
                        NULL,
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_old_url"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_new_url"]) . "',
                        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_rewrite_code"]) . "',
                        " . $active . "
                      )";
        $inserted = TRUE;
    }
    if (($_REQUEST["input_old_url"] == '') | ($_REQUEST["input_new_url"] == '') | ($_REQUEST["input_rewrite_code"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_url_management_fields") . "</div>\n";
        $error      = TRUE;
    }
    /*if ($_REQUEST["input_old_url"] <> urlencode($_REQUEST["input_old_url"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_url_management_encoding") . "</div>\n";
        $error      = TRUE;
    }
    if ($_REQUEST["input_new_url"] <> urlencode($_REQUEST["input_new_url"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_url_management_encoding") . "</div>\n";
        $error      = TRUE;
    }*/
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_rewrite_rules WHERE old_url = '" . $_REQUEST["input_old_url"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_url_management_exists") . "</div>\n";
        $error      = TRUE;
    }

    $input_site["id"] = $_REQUEST["input_id"];

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $messages[]     = '<div class="successbox">' . $translation->get("url_management_msg_success1") . '</div>';
            $last_insert_id = mysqli_insert_id($GLOBALS['mysql_con']);
            $input_url_management["id"] = $last_insert_id;
        } else {

            $messages[] = '<div class="successbox">' . $translation->get("url_management_msg_success2") . '</div>';
        }
    }

    $input_url_management["old_url"]                = $_REQUEST["input_old_url"];
    $input_url_management["new_url"]                = $_REQUEST["input_new_url"];
    $input_url_management["rewrite_code"]           = $_REQUEST["input_rewrite_code"];
    $input_url_management["active"]                 = $active;


    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_url_management_cardform.inc.php';
}

?>