<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {

    case 'edit_textcontent':
        edit_textcontent();
        break;
    case 'delete_textcontent':
        delete_textcontent();
        break;
    case 'new_textcontent':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_cardform.inc.php';
        break;
    case 'save_textcontent':
        save_textcontent();
        break;

    case 'get_pages_textcontent':
        get_pages($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], 0);
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_listform.inc.php';
        break;
}

function edit_textcontent( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM textcontent_header WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_textcontent = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_cardform.inc.php';
        }
    } else {
        $input_textcontent = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_cardform.inc.php';
    }
}

function delete_textcontent() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM textcontent_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(1, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_listform.inc.php';
}

function save_textcontent() {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    if ($_REQUEST["input_id"] <> '') {
        $query    = "UPDATE textcontent_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				content = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["textcontent"]) . "', 
				type = 0,
				all_languages = " . $input_all_languages . "
				WHERE id = " . $_REQUEST["input_id"] . " 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO textcontent_header
			(main_language_id, description, modified_date, modified_user, type, content, all_languages)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				0,
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['textcontent']) . "',
				" . $input_all_languages . "
			)";
        $inserted = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }


    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("textcontent_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("textcontent_msg_success2") . '</div>';
        }

        update_sitepart_changes(1, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 1, $input_id) === FALSE) {
                assoc_sitepart(1, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_textcontent($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 1, $input_id) === FALSE) {
                assoc_sitepart(1, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_textcontent($input_id, $messages);
        } else {
            edit_textcontent($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_textcontent["description"] = $_REQUEST["input_description"];
        $input_textcontent["type"]        = 0;
        $input_textcontent["content"]     = $_REQUEST["textcontent"];
        $input_textcontent["id"]          = $_REQUEST["input_id"];
        $input_textcontent["all_languages"]          = $input_all_languages;
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent_cardform.inc.php';
    }
}