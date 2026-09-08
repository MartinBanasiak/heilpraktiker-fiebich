<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {

    case 'edit_iframe':
        edit_iframe();
        break;
    case 'delete_iframe':
        delete_iframe();
        break;
    case 'new_iframe':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_cardform.inc.php';
        break;
    case 'save_iframe':
        save_iframe();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_listform.inc.php';
        break;
}

function edit_iframe( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM iframe_header WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_iframe = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_cardform.inc.php';
        }
    } else {
        $input_iframe = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_cardform.inc.php';
    }
}

function delete_iframe() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM iframe_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 11 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(11, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_listform.inc.php';
}

function save_iframe() {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    if ($_REQUEST["input_id"] <> '') {
        $query    = "UPDATE iframe_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . (int)$_REQUEST["input_width"] . "', 
				height = '" . (int)$_REQUEST["input_height"] . "', 
				url = '" . addhttp($_REQUEST["input_url"]) . "',
				all_languages = " . $input_all_languages . "
					WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO iframe_header
			(main_language_id, description, modified_date, modified_user, width, height, url, all_languages)
			VALUES (
				" . $GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . (int)$_REQUEST["input_width"] . "',
				'" . (int)$_REQUEST["input_height"] . "',
				'" . addhttp($_REQUEST["input_url"]) . "',
				" . $input_all_languages . "
			)";
        $inserted = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if (($_REQUEST["input_url"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("iframe_error1") . "</div>\n";
        $error      = TRUE;
    }


    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("iframe_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("iframe_msg_success2") . '</div>';
        }

        update_sitepart_changes(11, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 11, $input_id) === FALSE) {
                assoc_sitepart(11, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_iframe($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 11, $input_id) === FALSE) {
                assoc_sitepart(11, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_iframe($input_id, $messages);
        } else {
            edit_iframe($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_iframe["description"] = $_REQUEST["input_description"];
        $input_iframe["width"]       = $_REQUEST["input_width"];
        $input_iframe["height"]      = $_REQUEST["input_height"];
        $input_iframe["url"]         = $_REQUEST["input_url"];
        $input_iframe["all_languages"]  = $input_all_languages;
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_iframe_cardform.inc.php';
    }
}