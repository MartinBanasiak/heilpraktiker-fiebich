<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}
switch ($action) {

    case 'edit_language_switch':
        edit_language_switch();
        break;
    case 'delete_language_switch':
        delete_language_switch();
        break;
    case 'new_language_switch':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_cardform.inc.php';
        break;
    case 'save_language_switch':
        save_language_switch();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_listform.inc.php';
        break;
}

function edit_language_switch( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM main_shop_language_switch WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $language_switch_sitepart = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_cardform.inc.php';
    }
}

function delete_language_switch() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_shop_language_switch WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 1 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(17, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_listform.inc.php';
}

function save_language_switch() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    if ((int)$_REQUEST["input_id"] > 0) {
        $query    = "UPDATE main_shop_language_switch SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				modified_date = FROM_UNIXTIME (" . time() . "),
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
				WHERE id = '" . $_REQUEST["input_id"] . "'  
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO main_shop_language_switch
			(main_language_id, description, modified_date, modified_user)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				FROM_UNIXTIME (" . time() . "),
				" . (int)$GLOBALS["admin_user"]['id'] . "
			)";
        $inserted = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("language_switch_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("language_switch_msg_success2") . '</div>';
        }

        update_sitepart_changes(17, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 17, $input_id) === FALSE) {
                assoc_sitepart(17, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_listform.inc.php';
                return;
            }

            edit_language_switch($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 17, $input_id) === FALSE) {
                assoc_sitepart(17, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_language_switch($input_id, $messages);
        } else {
            edit_language_switch($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_language_switch["type"] = $_REQUEST["input_type"];
        $input_language_switch["id"]   = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_language_switch_cardform.inc.php';
    }
}