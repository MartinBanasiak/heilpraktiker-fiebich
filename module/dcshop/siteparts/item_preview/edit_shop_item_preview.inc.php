<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}
switch ($action) {

    case 'edit_shop_item_preview':
        edit_shop_item_preview();
        break;
    case 'delete_shop_item_preview':
        delete_shop_item_preview();
        break;
    case 'new_shop_item_preview':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_cardform.inc.php';
        break;
    case 'save_shop_item_preview':
        save_shop_item_preview();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_listform.inc.php';
        break;
}

function edit_shop_item_preview( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM main_shop_item_preview WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $item_preview_sitepart = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_cardform.inc.php';
    }
}

function delete_shop_item_preview() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_shop_item_preview WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(14, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_listform.inc.php';
}

function save_shop_item_preview() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    if ($_POST["input_include_sub_categories"] === 'on') {
        $include_sub_categories = 1;
    } else {
        $include_sub_categories = 0;
    }
    if ((int)$_REQUEST["input_id"] > 0) {
        $query    = "UPDATE main_shop_item_preview SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				no_of_items = '" . (int)$_REQUEST["input_no_of_items"] . "',
				category_code_string = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_category_code_string']) . "',
				item_no_string = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_item_no_string']) . "',
				include_sub_categories = '" . $include_sub_categories . "',
				modified_date = FROM_UNIXTIME (" . time() . "),
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_shop_item_preview
			(main_language_id, description, no_of_items, category_code_string, item_no_string, include_sub_categories, modified_date, modified_user)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . (int)$_REQUEST["input_no_of_items"] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_category_code_string']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_item_no_string']) . "',
				'" . $include_sub_categories . "',
				FROM_UNIXTIME (" . time() . "),
				" . (int)$GLOBALS["admin_user"]['id'] . "
			)";
        $inserted = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("item_preview_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("item_preview_msg_success2") . '</div>';
        }

        update_sitepart_changes(14, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 14, $input_id) === FALSE) {
                assoc_sitepart(14, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
                return;
            }

            edit_shop_item_preview($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 14, $input_id) === FALSE) {
                assoc_sitepart(14, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_shop_item_preview($input_id, $messages);
        } else {
            edit_shop_item_preview($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_shop_item_preview["type"] = $_REQUEST["input_type"];
        $input_shop_item_preview["id"]   = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_item_preview_cardform.inc.php';
    }
}