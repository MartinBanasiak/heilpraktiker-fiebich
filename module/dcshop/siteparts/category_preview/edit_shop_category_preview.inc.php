<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}
switch ($action) {

    case 'edit_shop_category_preview':
        edit_shop_category_preview();
        break;
    case 'delete_shop_category_preview':
        delete_shop_category_preview();
        break;
    case 'new_shop_category_preview':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_cardform.inc.php';
        break;
    case 'save_shop_category_preview':
        save_shop_category_preview();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_listform.inc.php';
        break;
}

function edit_shop_category_preview( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM main_shop_category_preview WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $category_preview_sitepart = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_cardform.inc.php';
    }
}

function delete_shop_category_preview() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_shop_category_preview WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(14, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_listform.inc.php';
}

function save_shop_category_preview() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    if ((int)$_REQUEST["input_id"] > 0) {
        $query    = "UPDATE main_shop_category_preview SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				category_line_no = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_category_line_no']) . "',
				modified_date = FROM_UNIXTIME (" . time() . "),
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_shop_category_preview
			(main_language_id, description, category_line_no, modified_date, modified_user)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_category_line_no']) . "',
				FROM_UNIXTIME (" . time() . "),
				" . (int)$GLOBALS["admin_user"]['id'] . "
			)";
        $inserted = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("category_preview_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("category_preview_msg_success2") . '</div>';
        }

        update_sitepart_changes(19, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 19, $input_id) === FALSE) {
                assoc_sitepart(19, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
                return;
            }

            edit_shop_category_preview($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 19, $input_id) === FALSE) {
                assoc_sitepart(19, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_shop_category_preview($input_id, $messages);
        } else {
            edit_shop_category_preview($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_shop_category_preview["type"] = $_REQUEST["input_type"];
        $input_shop_category_preview["id"]   = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview_cardform.inc.php';
    }
}