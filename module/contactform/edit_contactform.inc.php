<?

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'edit_contactform':
        edit_contactform();
        break;
    case 'delete_contactform':
        delete_contactform();
        break;
    case 'new_contactform':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_cardform.inc.php';
        break;
    case 'save_contactform':
        save_contactform();
        break;

    case 'save_line_contactform':
        save_line_contactform();
        break;
    case 'new_line_contactform':
        new_line_contactform();
        break;
    case 'edit_line_contactform':
        edit_line_contactform();
        break;
    case 'delete_line_contactform':
        delete_line_contactform();
        break;
    case 'moveup_line_contactform':
        moveup_line_contactform();
        edit_contactform();
        break;
    case 'movedown_line_contactform':
        movedown_line_contactform();
        edit_contactform();
        break;

    case 'update_sortorder_contactform':
        update_sortorder_contactform();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_listform.inc.php';
        break;
}

function update_sortorder_contactform() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE contactform_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_contactform() {

    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_line_cardform.inc.php';
}

function delete_line_contactform() {
    if ($_REQUEST["input_line_id"] <> '') {
        $query = "DELETE FROM contactform_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    edit_contactform();
}

function save_line_contactform() {
    $translation                 = \DynCom\dc\common\classes\Registry::get("translation");
    $messages                    = array();
    $_REQUEST["input_mandatory"] = ($_REQUEST["input_mandatory"]) ? 1 : 0;
    $_REQUEST["input_infield"]   = ($_REQUEST["input_infield"]) ? 1 : 0;
    $id                          = $_REQUEST["input_line_id"];

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // neue dummy gallery anlegen
        // optionen auslesen
        $query  = "SELECT options FROM main_collection_setup_content WHERE id ='" . $_POST['collection_setup_content_id']."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_array($result);

        $options = unserialize($row['options'],['allowed_classes' => false]);
        // hinzufuegen zur datenbank
        $insertquery = "INSERT INTO contactform_header
			(main_language_id, description, modified_date, modified_user, sender_name, sender_email, recipient_name, recipient_email, subject, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_sender_name']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_sender_email']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_recipient_name']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_recipient_email']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_input_subject']) . "',
				1
			)";

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			" . $header_id . ",
			3
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = $_REQUEST['input_id'];
    }

    if ($_REQUEST["input_line_id"] <> '') {
        $query    = "UPDATE contactform_line 
                      SET code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "', 
                      name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "', 
                      typ = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_typ"]) . "', 
                      mandatory = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_mandatory"]) . "', 
                      option_string = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_option_string"]) . "', 
                      infield = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_infield"]) . "' 
                    WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
        $inserted = FALSE;
    } else {
        $sorting  = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM contactform_line WHERE header_id = '" . $_REQUEST["input_id"]."'"));
        $sorting  = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query    = "INSERT INTO contactform_line (header_id, code,name,typ,mandatory,option_string,infield,sorting) 
                      VALUES (
                      " . (int)$header_id . ", 
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
                      '" . $_REQUEST["input_typ"] . "',
                      '" . $_REQUEST["input_mandatory"] . "', 
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_option_string"]) . "',
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_infield"]) . "', 
                      " . $sorting . "
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
    $id_text = ($_REQUEST["input_id"] <> '') ? " AND header_id = '" . $header_id . "'" : "";

    $result  = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM contactform_line WHERE code = '" . $_REQUEST["input_code"] . "'" . $id_text);
    $numrows = @mysqli_num_rows($result);

    if ($numrows > 1) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }

    if ($_REQUEST["input_id"] <> '' && $numrows == 1) {
        $curr_line = @mysqli_fetch_array($result);
        if ($curr_line['id'] != $_REQUEST["input_line_id"]) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
            $error      = TRUE;
        }
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($_REQUEST['save_and_close'] == 1) {

            edit_contactform();
        } else {
            if ($inserted === TRUE) {
                $id = mysqli_insert_id($GLOBALS['mysql_con']);
            }
            edit_line_contactform($id);
        }

    } else {
        $input_line["id"]            = $_REQUEST["input_line_id"];
        $input_line["header_id"]     = $header_id;
        $input_line["typ"]           = $_REQUEST["input_typ"];
        $input_line["code"]          = $_REQUEST["input_code"];
        $input_line["name"]          = $_REQUEST["input_name"];
        $input_line["option_string"] = $_REQUEST["input_option_string"];
        $input_line["mandatory"]     = $_REQUEST["input_mandatory"];
        $input_line["infield"]       = $_REQUEST["input_infield"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_line_cardform.inc.php';
    }
}


function moveup_line_contactform() {
    move_line_contactform("<", "DESC", $_REQUEST["input_line_id"]);
}

function movedown_line_contactform() {
    move_line_contactform(">", "ASC", $_REQUEST["input_line_id"]);
}

function move_line_contactform( $way, $order, $field_id ) {
    $query  = "SELECT * FROM contactform_line WHERE id = '" . $field_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_line = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM contactform_line WHERE header_id = " . $curr_line["header_id"] . " AND sorting " . $way . " " . $curr_line["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_line = @mysqli_fetch_array($result);
    }
    if (($curr_line["id"] <> '') && ($change_line["id"] <> '')) {
        $query = "UPDATE contactform_line SET sorting = " . $change_line["sorting"] . " WHERE id = " . $curr_line["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE contactform_line SET sorting = " . $curr_line["sorting"] . " WHERE id = " . $change_line["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function edit_line_contactform( $_id = "" ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM contactform_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_cardform.inc.php';
    }
}

function edit_contactform( $_id = "", $messages = array() ) {

    if (is_collection_edit()) {
        edit_collection();
        return;
    }

    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query  = "SELECT * FROM contactform_header WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_contactform = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_cardform.inc.php';
        }
    } else {
        $input_contactform = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_cardform.inc.php';
    }
}

function delete_contactform() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM contactform_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "DELETE FROM contactform_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 3 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(3, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_listform.inc.php';
}

function save_contactform() {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    if ($_REQUEST["input_id"] <> '') {
        $query    = "UPDATE contactform_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				sender_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_sender_name"]) . "', 
				sender_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_sender_email"]) . "', 
				recipient_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_recipient_name"]) . "',
				recipient_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_recipient_email"]) . "',
				subject = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_subject"]) . "',
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "'
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        //$query = "INSERT INTO main_contactform (id, main_site_id, code,name,site_name,std_main_navigation_id,site_title_name,meta_description,meta_keywords,main_layout_id,company,shop_code,shop_contactform_code,logout_site_id,logout_contactform_id,logout_navigation_id) VALUES (NULL, '" . $GLOBALS["site"]["id"] . "', '" . $_REQUEST["input_code"]. "','" . $_REQUEST["input_name"]. "','" . $_REQUEST["input_site_name"] . "','" . $_REQUEST["input_std_main_navigation_id"] . "','" . $_REQUEST["input_site_title_name"] . "','" . $_REQUEST["input_meta_description"] . "','" . $_REQUEST["input_meta_keywords"] . "','" . $_REQUEST["input_main_layout_id"] . "','" . $_REQUEST["input_company"] . "','" . $_REQUEST["input_shopcode"] . "','" . $_REQUEST["input_contactform_code"] . "','".$_REQUEST["input_login_site"]."','".$_REQUEST["input_login_contactform"]."','".$_REQUEST["input_login_navigation"]."')";
        $query    = "INSERT INTO contactform_header
			(main_language_id, description, modified_date, modified_user, sender_name, sender_email, recipient_name, recipient_email, subject, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_sender_name']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_sender_email']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_recipient_name']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_recipient_email']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_subject']) . "',
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
            $messages[] = '<div class="successbox">' . $translation->get("contactform_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("contactform_msg_success2") . '</div>';
        }

        update_sitepart_changes(3, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 3, $input_id) === FALSE) {
                assoc_sitepart(3, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_contactform($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 3, $input_id) === FALSE) {
                assoc_sitepart(3, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_contactform($input_id, $messages);
        } else {
            edit_contactform($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_contactform["description"]     = $_REQUEST["input_description"];
        $input_contactform["sender_name"]     = $_REQUEST["input_sender_name"];
        $input_contactform["sender_email"]    = $_REQUEST["input_sender_email"];
        $input_contactform["recipient_name"]  = $_REQUEST["input_recipient_name"];
        $input_contactform["recipient_email"] = $_REQUEST["input_recipient_email"];
        $input_contactform["subject"]         = $_REQUEST["input_subject"];
        $input_contactform["id"]              = $_REQUEST["input_id"];
        $input_contactform["all_languages"]   = $input_all_languages;
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_cardform.inc.php';
    }
}

?>