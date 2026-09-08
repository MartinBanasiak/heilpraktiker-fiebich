<?php
//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)),'/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}
switch ($action) {

    case 'edit_newsletter_sitepart':
        edit_newsletter_sitepart();
        break;
    case 'delete_newsletter_sitepart':
        delete_newsletter_sitepart();
        break;
    case 'new_newsletter_sitepart':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_cardform.inc.php';
        break;
    case 'save_newsletter_sitepart':
        save_newsletter_sitepart();
        break;

    case 'get_pages_textcontent':
        get_pages($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], 0);
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_listform.inc.php';
        break;
}

function edit_newsletter_sitepart( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM newsletter_sitepart WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $newsletter_sitepart = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_cardform.inc.php';
    }
}

function delete_newsletter_sitepart() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM newsletter_sitepart WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(15, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_listform.inc.php';
}

function save_newsletter_sitepart() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";

    $newsletter_version = getenv('NEWSLETTER_VERSION');
    if (empty($newsletter_version)) {
        $newsletter_version = 0;
    }

    if ($newsletter_version == 1) {

    } else {
        if (!empty($_REQUEST['input_group_name']) && is_int($_REQUEST['input_group_name'])) {
            $_REQUEST['input_group_id'] = $_REQUEST['input_group_name'];
        }

        if (!(empty($_REQUEST['input_group_name'])
                || empty($_REQUEST['input_account'])
                || empty($_REQUEST['input_login'])
                || empty($_REQUEST['input_password']))
            && (empty($_REQUEST['input_group_id'])
                || $_REQUEST['input_group_id'] == 0)) {
            $cleverReachConnector = new DynCom\dc\common\classes\CleverReachConnector($_REQUEST['input_account'], $_REQUEST['input_login'], $_REQUEST['input_password'], $_REQUEST['input_group_name']);
            if (!$cleverReachConnector->isError()) {
                $_REQUEST['input_group_id'] = $cleverReachConnector->getGroupId();
            }
        }
    }

    if ((int)$_REQUEST["input_id"] > 0) {
        if ($newsletter_version == 1) {
            $query    = "UPDATE newsletter_sitepart SET
				db_id = " . (int)$_REQUEST['input_db_id'] . ",
				access_token = '" . $_REQUEST['input_access_token'] . "',
				type = " . (int)$_REQUEST['input_type'] . ",
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				text = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['textcontent']) . "',
				segment = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_segment']) . "',
				salutation_active = " . (int)$_REQUEST['input_salutation_active'] . ",	
				name_status = " . (int)$_REQUEST['input_name_status'] . ",
				city_active = " . (int)$_REQUEST['input_city_active'] . ",
				post_code_active = " . (int)$_REQUEST['input_post_code_active'] . ",
				birthday_active = " . (int)$_REQUEST['input_birthday_active'] . ",	
				label_position = " . (int)$_REQUEST['input_label_position'] . ",
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				forwarding_url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_forwarding_url']) ."'
				WHERE id = " . $_REQUEST["input_id"] . " 
			LIMIT 1";
        } else {
            $query    = "UPDATE newsletter_sitepart SET
				group_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_group_name']) . "',
				group_id = " . (int)$_REQUEST["input_group_id"] . ",
				account = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_account']) . "',
				login = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_login']) . "',
				password = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_password']) . "',
				type = '" . (int)$_REQUEST['input_type'] . "',
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				text = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['textcontent']) . "',
				segment = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_segment']) . "',
				salutation_active = '" . (int)$_REQUEST['input_salutation_active'] . "',	
				name_status = '" . (int)$_REQUEST['input_name_status'] . "',
				city_active = '" . (int)$_REQUEST['input_city_active'] . "',
				post_code_active = '" . (int)$_REQUEST['input_post_code_active'] . "',
				birthday_active = '" . (int)$_REQUEST['input_birthday_active'] . "',	
				label_position = '" . (int)$_REQUEST['input_label_position'] . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				forwarding_url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_forwarding_url']) ."'
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        }
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        if ($newsletter_version == 1) {
            $query    = "INSERT INTO newsletter_sitepart
			(main_language_id, db_id, access_token, type, description, text, segment, salutation_active, name_status, city_active, post_code_active, birthday_active, label_position,modified_date, modified_user, forwarding_url)
			VALUES (
				" . (int)$GLOBALS['language']['id'] . ",
				" . (int)$_REQUEST['input_db_id'] . ",
				'" . $_REQUEST['input_access_token'] . "',
				" . (int)$_REQUEST['input_type'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['textcontent']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_segment']) . "',
				" . (int)$_REQUEST['input_salutation_active'] . ",	
				" . (int)$_REQUEST['input_name_status'] . ",
				" . (int)$_REQUEST['input_city_active'] . ",
				" . (int)$_REQUEST['input_post_code_active'] . ",
				" . (int)$_REQUEST['input_birthday_active'] . ",
				" . (int)$_REQUEST['input_label_position'] . ",				
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_forwarding_url']) . "'
			)";
        } else {
            $query    = "INSERT INTO newsletter_sitepart
			(main_language_id, group_name, group_id, account, login, password, type, description, text, segment, salutation_active, name_status, city_active, post_code_active, birthday_active, label_position,modified_date, modified_user, forwarding_url)
			VALUES (
				" . (int)$GLOBALS['language']['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_group_name']) . "',
				'" . (int)$_REQUEST["input_group_id"] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_account']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_login']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_password']) . "',
				'" . (int)$_REQUEST['input_type'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['textcontent']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_segment']) . "',
				'" . (int)$_REQUEST['input_salutation_active'] . "',	
				'" . (int)$_REQUEST['input_name_status'] . "',
				'" . (int)$_REQUEST['input_city_active'] . "',
				'" . (int)$_REQUEST['input_post_code_active'] . "',
				'" . (int)$_REQUEST['input_birthday_active'] . "',
				'" . (int)$_REQUEST['input_label_position'] . "',				
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_forwarding_url']) . "'
			)";
        }
        $inserted = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("newsletter_sitepart_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("newsletter_sitepart_msg_success2") . '</div>';
        }

        update_sitepart_changes(15, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 15, $input_id) === FALSE) {
                assoc_sitepart(15, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
                return;
            }

            edit_newsletter_sitepart($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 15, $input_id) === FALSE) {
                assoc_sitepart(15, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_newsletter_sitepart($input_id, $messages);
        } else {
            edit_newsletter_sitepart($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_newsletter_sitepart["type"] = $_REQUEST["input_type"];
        $input_newsletter_sitepart["id"]   = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_newsletter_sitepart_cardform.inc.php';
    }
}