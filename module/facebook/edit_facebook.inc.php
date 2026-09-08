<?php
$messages = array();
$error    = FALSE;
if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {

    case 'edit_facebook':
        edit_facebook();
        break;
    case 'delete_facebook':
        delete_facebook();
        break;
    case 'new_facebook':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_cardform.inc.php';
        break;
    case 'save_facebook':
        save_facebook();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_listform.inc.php';
        break;
}

function edit_facebook( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM facebook_header WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_facebook = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_cardform.inc.php';
        }
    } else {
        $input_facebook = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_cardform.inc.php';
    }
}

function delete_facebook() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM facebook_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 9 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(9, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_listform.inc.php';
}

function save_facebook() {

    $translation      = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id         = "";
    $input_show_faces = ($_POST["input_show_faces"] == "on") ? 1 : 0;
    $input_show_posts = ($_POST["input_show_posts"] == "on") ? 1 : 0;
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    if ($_REQUEST["input_id"] <> '') {
        $query    = "UPDATE facebook_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . (int)$_REQUEST["input_width"] . "', 
				height = '" . (int)$_REQUEST["input_height"] . "', 
				show_faces = " . $input_show_faces . ", 
				show_posts = " . $input_show_posts . ", 
				url = '" . $_REQUEST["input_url"] . "',
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "'
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $query    = "INSERT INTO facebook_header
			(main_language_id, description, modified_date, modified_user, width, height, show_faces, show_posts, url, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . (int)$_REQUEST["input_width"] . "',
				'" . (int)$_REQUEST["input_height"] . "',
				" . $input_show_faces . ",
				" . $input_show_posts . ",
				'" . $_REQUEST["input_url"] . "',
			    " . $input_all_languages . "
			)";
        $inserted = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if (($_REQUEST["input_url"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("facebook_error1") . "</div>\n";
        $error      = TRUE;
    }


    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("facebook_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("facebook_msg_success2") . '</div>';
        }

        update_sitepart_changes(9, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 9, $input_id) === FALSE) {
                assoc_sitepart(9, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_facebook($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 9, $input_id) === FALSE) {
                assoc_sitepart(9, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_facebook($input_id, $messages);
        } else {
            edit_facebook($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_facebook["description"] = $_REQUEST["input_description"];
        $input_facebook["width"]       = $_REQUEST["input_width"];
        $input_facebook["height"]      = $_REQUEST["input_height"];
        $input_facebook["show_faces"]  = $input_show_faces;
        $input_facebook["show_posts"]  = $input_show_posts;
        $input_facebook["url"]         = $_REQUEST["input_url"];
        $input_facebook["all_languages"]= $input_all_languages;
        $input_facebook["id"]           = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_facebook_cardform.inc.php';
    }
}