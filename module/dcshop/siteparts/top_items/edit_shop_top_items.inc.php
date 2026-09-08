<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}
switch ($action) {

    case 'edit_shop_top_items':
        edit_shop_top_items();
        break;
    case 'delete_shop_top_items':
        delete_shop_top_items();
        break;
    case 'new_shop_top_items':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_cardform.inc.php';
        break;
    case 'save_shop_top_items':
        save_shop_top_items();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_listform.inc.php';
        break;
}

function edit_shop_top_items($_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM main_shop_top_items WHERE id = '" . (int)$input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $top_items_sitepart = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_cardform.inc.php';
    }
}

function delete_shop_top_items() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_shop_top_items WHERE id = '" . (int)$_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . (int)$_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(16, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_listform.inc.php';
}

function save_shop_top_items() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    if ((int)$_REQUEST["input_id"] > 0) {
        $query    = "UPDATE main_shop_top_items SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				no_of_items = '" . (int)$_REQUEST["input_no_of_items"] . "',
				modified_date = FROM_UNIXTIME (" . time() . "),
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
				WHERE id = '" . (int)$_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = (int)$_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_shop_top_items
			(main_language_id, description, no_of_items, modified_date, modified_user)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . (int)$_REQUEST["input_no_of_items"] . "',
				FROM_UNIXTIME (" . time() . "),
				" . (int)$GLOBALS["admin_user"]['id'] . "
			)";
        $inserted = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("top_items_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("top_items_msg_success1") . '</div>';
        }

        update_sitepart_changes(16, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 16, $input_id) === FALSE) {
                assoc_sitepart(16, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
                return;
            }

            edit_shop_top_items($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 16, $input_id) === FALSE) {
                assoc_sitepart(16, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_shop_top_items($input_id, $messages);
        } else {
            edit_shop_top_items($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_shop_top_items["type"] = (int)$_REQUEST["input_type"];
        $input_shop_top_items["id"]   = (int)$_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items_cardform.inc.php';
    }
}