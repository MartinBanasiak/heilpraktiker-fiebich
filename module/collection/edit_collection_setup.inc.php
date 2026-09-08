<?
use DynCom\dc\common\classes\CollectionTypes;

date_default_timezone_set('Europe/Berlin');

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_collection_setup':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_cardform.inc.php';
        break;
    case 'save_collection_setup':
        save_collection_setup();
        break;
    case 'edit_collection_setup':
        edit_collection_setup();
        break;
    case 'delete_collection_setup' :
        delete_collection_setup();
        break;

    case 'new_line_collection_setup' :
        new_line_collection_setup();
        break;
    case 'edit_line_collection_setup':
        edit_line_collection_setup();
        break;
    case 'delete_line_collection_setup':
        delete_line_collection_setup();
        break;
    case 'save_line_collection_setup':
        save_line_collection_setup();
        break;
    case 'moveup_line_collection_setup':
        moveup_line_collection_setup();
        edit_collection_setup();
        break;
    case 'movedown_line_collection_setup':
        movedown_line_collection_setup();
        edit_collection_setup();
        break;

    case 'new_group_collection_setup' :
        new_group_collection_setup();
        break;
    case 'edit_group_collection_setup':
        edit_group_collection_setup();
        break;
    case 'delete_group_collection_setup':
        delete_group_collection_setup();
        break;
    case 'save_group_collection_setup':
        save_group_collection_setup();
        break;

    case 'load_sitepart_form':
        load_sitepart_form();
        break;

    case 'update_sortorder_collection_setup':
        update_sortorder_collection_setup();
        break;

    // Kopieren eines Kollektionssetups
    case 'copy_collection_setup': copy_collection_setup(); break;
    case 'load_languages': load_site_languages(); break;
    case 'copy_and_save_collection_setup': copy_and_save_collection_setup(); break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_listform.inc.php';
        break;
}

function copy_collection_setup($_id = "", $messages = array()) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {
        $query = "SELECT * FROM main_collection_setup WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_collection = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_copy.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_cardform.inc.php';
    }
}

function load_site_languages() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    ob_start();
    language_select($translation->get("language"), "main_language_id", null, FALSE, $_POST['site_id'], true, "", true);
    $output1 = ob_get_clean();

    echo json_encode(array('site_languages' => $output1));
}

function copy_and_save_collection_setup() {
    require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'dc' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'edit_language_functions.php';
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();
    $error = false;

    $input_collection_setup_id = $_REQUEST["input_id"];
    $input_site_id = $_REQUEST["site_id"];
    $input_language_id = (isset($_REQUEST["main_language_id"]) ? $_REQUEST["main_language_id"] : "");

    if(($input_site_id == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("copy_choose_site_error") . "</div>\n";
        $error = true;
    }

    if(($input_language_id == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("copy_choose_language_error") . "</div>\n";
        $error = true;
    }

    if(!$error) {

        $languageIds = array();

        $query = "DELETE FROM copy_link WHERE session_id = '" . session_id() . "'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // pruefen ob die language Id gleich ist
        if($input_language_id == "all") {
            $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_language WHERE main_site_id = '" . $input_site_id."'");
            while($language = mysqli_fetch_array($result)) {
                $languageIds[] = $language["id"];
            }
        } else {
            $languageIds[] = $input_language_id;
        }

        foreach($languageIds as $languageId) {

            $LanguageFunctions = new LanguageFunctions($GLOBALS["language"]['id'], null, $languageId);
            $LanguageFunctions->set_noCopyPageComponentIncludeLink();
            $LanguageFunctions->set_noCopyPageCollectionGroupLink();
            $LanguageFunctions->set_copyOnlySpecificSiteparts();

            // Siteparts muessen immer neu kopiert werden
            $query = "SELECT main_sitepart_header_id, main_sitepart_id FROM main_collection_link WHERE main_sitepart_id > 0 and main_collection_setup_content_id in (select id from main_collection_setup_content where main_collection_setup_id = '" . $input_collection_setup_id . "')";
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $LanguageFunctions->addSitePartId($lineRow['main_sitepart_id']);
                $LanguageFunctions->addSitePartHeaderId($lineRow['main_sitepart_header_id']);
            }

            // kontaktformular muss auch immer neu erstellt werden
            $query = "SELECT registration_contactform_id FROM main_collection WHERE registration_contactform_id > 0 and main_collection_setup_id = '" . $input_collection_setup_id."'";
            $lineResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            while($lineRow = mysqli_fetch_array($lineResult)) {
                $LanguageFunctions->addSitePartHeaderId($lineRow['registration_contactform_id']);
                $LanguageFunctions->addSitePartId(3);
            }

            $LanguageFunctions->copy_siteparts();
            $LanguageFunctions->copy_collection_setup($input_collection_setup_id, true);
            $LanguageFunctions->copy_collection($input_collection_setup_id, true);
        }

        $messages[] = '<div class="successbox">' . $translation->get("copy_collection_success") . '</div>';
        copy_collection_setup($input_collection_setup_id, $messages);

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        copy_collection_setup($input_collection_setup_id, $messages);
    }
}

function edit_group_collection_setup( $_id = "", $messages = array() ) {

    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_group_id"];
    }

    if ($line_id <> '') {

        $query  = "SELECT * FROM main_collection_setup_group WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_line = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_group_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_group_cardform.inc.php';

    }
}

function delete_group_collection_setup() {
    if ($_REQUEST["input_group_id"] <> '') {
        $query = "DELETE FROM main_collection_setup_group WHERE id = '" . $_REQUEST["input_group_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_collection_group_link WHERE main_collection_setup_group_id = '" . $_REQUEST["input_group_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_component_collection_group_link WHERE main_collection_setup_group_id = '" . $_REQUEST["input_group_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_page_collection_group_link WHERE main_collection_setup_group_id = '" . $_REQUEST["input_group_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    edit_collection_setup();
}

/**
 * save_group_collection_setup() Speichern der inputfelder
 *
 */

function save_group_collection_setup() {
    $translation    = \DynCom\dc\common\classes\Registry::get("translation");
    $messages       = array();
    $id             = $_REQUEST["input_group_id"];
    $default_active = $_REQUEST["input_default_active"];

    if ($_REQUEST["input_group_id"] <> '') {
        $query    = "UPDATE main_collection_setup_group SET
					description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "' ,
					default_active = '" . $default_active . "'
				WHERE id = " . $_REQUEST["input_group_id"] . " LIMIT 1";
        $inserted = FALSE;
    } else {
        $query    = "INSERT INTO main_collection_setup_group (main_collection_setup_id, description, default_active) VALUES (
			'" . (int)$_REQUEST["input_id"] . "',
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
			'" . $default_active . "'
		)";
        $inserted = TRUE;
    }


    if ($_REQUEST["input_description"] == '') {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if ($_REQUEST['save_and_close'] == 1) {
            edit_collection_setup();
        } else {
            if ($inserted === TRUE) {
                $id         = mysqli_insert_id($GLOBALS['mysql_con']);
                $messages[] = '<div class="successbox">' . $translation->get("collection_group_msg_success1") . '</div>';
            } else {
                $messages[] = '<div class="successbox">' . $translation->get("collection_group_msg_success2") . '</div>';
            }
            edit_group_collection_setup($id, $messages);
        }

    } else {
        $input_line["id"]          = $_REQUEST["input_group_id"];
        $input_line["description"] = $_REQUEST["input_description"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_group_cardform.inc.php';
    }
}

function new_group_collection_setup() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_group_cardform.inc.php';
}

function update_sortorder_collection_setup() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE main_collection_setup_content SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_collection_setup() {
    $input_line = array(
        'fieldtype' => $_POST['fieldtype'],
        'type_id'   => $_POST['type_id']
    );
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_line_cardform.inc.php';
}

function load_sitepart_form() {
    $collectionTypes = \DynCom\dc\common\classes\CollectionTypes::get();

    $code = $collectionTypes[$_POST['sitepartid']]['code'];

    $include = MODULE_PATH . "collection/edit_collection_" . $code . "_config.inc.php";
    if (file_exists($include)) {
        require_once($include);
    }
}


function edit_line_collection_setup( $_id = "", $messages = array() ) {

    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {

        $query  = "SELECT * FROM main_collection_setup_content WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_line = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_line_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_line_cardform.inc.php';

    }
}


/**
 * delete_picture() Löschen einzelner Bilder
 *
 * @param mixed $sitepart
 *
 * @return
 */

function edit_collection_setup( $_id = "", $messages = array() ) {

    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_id"];
    }

    if ($input_id <> '') {

        $query = "SELECT * FROM main_collection_setup WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_collection = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_cardform.inc.php';

    }
}


function save_collection_setup() {
    $translation  = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id     = "";
    $input_linked = ($_POST["input_linked"] == "on") ? 1 : 0;
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = " UPDATE main_collection_setup SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				linked = " . (int)$input_linked . ",
				icon = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['setup_icon']) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_code']) . "',
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO main_collection_setup
			(main_language_id, description, code, linked, icon, modified_date, modified_user, all_languages)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_code']) . "',
				" . (int)$input_linked . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['setup_icon']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $input_all_languages . "
			)";
        $inserted    = TRUE;
    }

    if (($_REQUEST["input_code"] == '') | ($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
        $error      = TRUE;
    }
    if ($_REQUEST["input_code"] <> urlencode($_REQUEST["input_code"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
        $error      = TRUE;
    }

    if (!$error) {

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("collection_setup_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("collection_setup_msg_success2") . '</div>';
        }


        edit_collection_setup($input_id, $messages);

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_collection                   = array();
        $input_collection["id"]             = $_REQUEST["input_id"];
        $input_collection["description"]    = $_REQUEST["input_description"];
        $input_collection["code"]           = $_REQUEST['input_code'];
        $input_collection["linked"]         = $input_linked;
        $input_collection["all_languages"]  = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_cardform.inc.php';
    }
}

function delete_collection_setup() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_collection_setup WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_collection_setup_content WHERE main_collection_setup_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_listform.inc.php';
}


function delete_line_collection_setup() {
    if ($_REQUEST["input_line_id"] <> '') {
        $query = "DELETE FROM main_collection_setup_content WHERE id = '" . $_REQUEST["input_line_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    edit_collection_setup();
}

/**
 * save_line_collection_setup() Speichern der inputfelder
 *
 */

function save_line_collection_setup() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages    = array();
    $id          = $_REQUEST["input_line_id"];
    $is_teaser   = ($_POST["main_input_is_teaser"] == "on") ? 1 : 0;

    $options = array();
    foreach ($_POST as $key => $value) {
        if (substr($key, 0, 16) == 'collection_setup') {
            $options[$key] = $value;
        }
    }
    $serialize_options = serialize($options);

    if ($_REQUEST["input_line_id"] <> '') {
        $query    = "UPDATE main_collection_setup_content SET
					fieldtype = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["collection_type"]) . "', 
					fieldname = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["main_input_description"]) . "', 
					code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["main_input_code"]) . "',
					is_teaser = " . $is_teaser . ", 
					type_id = '" . (int)$_REQUEST['main_input_choose_type'] . "',
					options = '" . $serialize_options . "'
				WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
        $inserted = FALSE;
    } else {
        $sorting  = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM main_collection_setup_content WHERE main_collection_setup_id = '" . $_REQUEST["input_id"]."'"));
        $sorting  = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query    = "INSERT INTO main_collection_setup_content (main_collection_setup_id, fieldtype, fieldname, code, is_teaser, type_id, options, sorting) VALUES (
			'" . (int)$_REQUEST["input_id"] . "',
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["collection_type"]) . "', 
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["main_input_description"]) . "', 
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["main_input_code"]) . "',
			" . $is_teaser . ", 
			'" . (int)$_REQUEST['main_input_choose_type'] . "',
			'" . $serialize_options . "',
			" . $sorting . "
		)";
        $inserted = TRUE;
    }
    if (($_REQUEST["main_input_choose_type"] == '') | ($_REQUEST["main_input_choose_type"] == 0)) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_choose_inputtype") . "</div>\n";
        $error      = TRUE;
    }

    if (($_REQUEST["main_input_code"] == '') | ($_REQUEST["main_input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
        $error      = TRUE;
    }

    if ($_REQUEST["main_input_code"] <> urlencode($_REQUEST["main_input_code"])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
        $error      = TRUE;
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if ($inserted === FALSE) {
            update_sitepart_setup($options, $id);
        }

        if ($_REQUEST['save_and_close'] == 1) {
            edit_collection_setup();
        } else {
            if ($inserted === TRUE) {
                $id         = mysqli_insert_id($GLOBALS['mysql_con']);
                $messages[] = '<div class="successbox">' . $translation->get("collection_line_msg_success1") . '</div>';
            } else {
                $messages[] = '<div class="successbox">' . $translation->get("collection_line_msg_success2") . '</div>';
            }
            edit_line_collection_setup($id, $messages);
        }

    } else {
        $input_line["id"]        = $_REQUEST["input_line_id"];
        $input_line["is_teaser"] = $_REQUEST["main_input_is_teaser"];
        $input_line["fieldname"] = $_REQUEST["main_input_description"];
        $input_line["fieldtype"] = $_REQUEST["collection_type"];
        $input_line['type_id']   = $_REQUEST["main_input_choose_type"];
        $input_line["code"]      = $_REQUEST["main_input_code"];
        $input_line['options']   = $serialize_options;
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_setup_line_cardform.inc.php';
    }
}

function update_sitepart_setup( $options, $setup_content_id ) {
    if ($_REQUEST["collection_type"] != "siteparts") {
        return;
    }

    $sitepartId = (int)$_REQUEST['main_input_choose_type'];

    $query               = "SELECT main_sitepart_header_id FROM main_collection_link WHERE main_collection_setup_content_id = '" . $setup_content_id . "' AND main_sitepart_id = '" . $sitepartId."'";
    $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
    $sitepart_header_ids = array();
    while ($row = @mysqli_fetch_array($result, 1)) {
        $sitepart_header_ids[] = $row['main_sitepart_header_id'];
    }

    if (count($sitepart_header_ids) == 0) {
        return;
    }


    switch ($sitepartId) {
        case 2:
            update_sitepart_header_slideshow($options, $sitepart_header_ids);
            break;
        case 3:
            update_sitepart_header_contactform($options, $sitepart_header_ids);
            break;
        case 6:
            update_sitepart_header_gallery($options, $sitepart_header_ids);
            break;
        case 7:
            update_sitepart_header_magicscroll($options, $sitepart_header_ids);
            break;
        case 8:
            update_sitepart_header_googlemaps($options, $sitepart_header_ids);
            break;
    }
}

function update_sitepart_header_slideshow( $options, $sitepart_header_ids ) {
    $query = "UPDATE slideshow_header SET

		width = " . (int)$options['collection_setup_width'] . ",
		height = " . (int)$options['collection_setup_height'] . ",
		effect = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_effect']) . "',
		arrows = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_arrows']) . "',
		eff_interval = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_eff_interval']) . "',
		effect_duration = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_effect_duration']) . "',
		text_effect = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_text_effect']) . "',
		text_pos = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_text_pos']) . "'

	WHERE id IN (" . join(',', $sitepart_header_ids) . ")";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function update_sitepart_header_contactform( $options, $sitepart_header_ids ) {
    $query = "UPDATE contactform_header SET

		sender_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_sender_name"]) . "', 
		sender_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_sender_email"]) . "', 
		recipient_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_recipient_name"]) . "',
		recipient_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_recipient_email"]) . "',
		subject = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_input_subject"]) . "' 

	WHERE id IN (" . join(',', $sitepart_header_ids) . ")";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function update_sitepart_header_gallery( $options, $sitepart_header_ids ) {
    $query = "UPDATE gallery_header SET

		width = " . (int)$options["collection_setup_gallery_width"] . ", 
		height = " . (int)$options["collection_setup_gallery_height"] . ",
		thumb_width = " . (int)$options["collection_setup_gallery_thumb_width"] . ", 
		thumb_height = " . (int)$options["collection_setup_gallery_thumb_height"] . "

	WHERE id IN (" . join(',', $sitepart_header_ids) . ")";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function update_sitepart_header_magicscroll( $options, $sitepart_header_ids ) {
    $query = "UPDATE scrollbar_header SET

		width = " . (int)$options["collection_setup_width"] . ", 
		height = " . (int)$options["collection_setup_height"] . ", 
		item_width = " . (int)$options["collection_setup_item_width"] . ", 
		item_height = " . (int)$options["collection_setup_item_height"] . ", 
		arrows = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_arrows"]) . "',
		eff_interval = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_eff_interval"]) . "',
		effect_duration = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_effect_duration"]) . "',
		items = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_items"]) . "',
		step = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_step"]) . "' 

	WHERE id IN (" . join(',', $sitepart_header_ids) . ")";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function update_sitepart_header_googlemaps( $options, $sitepart_header_ids ) {
    $query = "UPDATE google_maps_header SET

		width = " . (int)$options["collection_setup_google_maps_width"] . ", 
		height = " . (int)$options["collection_setup_google_maps_height"] . ",
		icon_location = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options["collection_setup_icon_location"]) . "'

	WHERE id IN (" . join(',', $sitepart_header_ids) . ")";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

/**
 * Erzeugt eine selectbox zum auswaehlen von input-typen bei einer kollektion
 *
 * @access public
 *
 * @param string $name       Label
 * @param string $selectname name der selectbox
 *
 * @return void
 */
function create_type_select( $name, $selectname = "input_choose_type", $input_line ) {

    $translation     = \DynCom\dc\common\classes\Registry::get("translation");
    $collectionTypes = \DynCom\dc\common\classes\CollectionTypes::get();
    $siteparts       = \DynCom\dc\common\classes\Siteparts::get();
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select onchange=\"load_sitepart_settings(this.value);\" class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";


    // neue typen die nur fuer kollektionen vorgesehen sind
    foreach ($collectionTypes as $typeId => $typeData) {
        $selected = '';
        if (isset($input_line['fieldtype']) && $input_line['fieldtype'] == 'standard' && $typeId == $input_line['type_id']) {
            $selected = 'selected="selected"';
        }
        echo "<option " . $selected . " value=\"" . $typeId . "\">" . $typeData['description'] . "</option>";
    }

//	// siteparts anzeigen
//	echo "<optgroup label=\"Siteparts\" value=\"siteparts\">";
//	foreach($siteparts as $sitepartId => $sitepartData) {
//		$selected = '';
//		
//		if(isset($input_line['fieldtype']) && $input_line['fieldtype'] == 'siteparts' && $sitepartId == $input_line['type_id']) {
//			$selected = 'selected="selected"';
//		}
//		echo "<option " . $selected . " value=\"" . $sitepartId . "\">" . $sitepartData['description'] . "</option>";
//	}
//	echo "</optgroup>";

    echo "</select>";
}


function moveup_line_collection_setup() {
    move_line_collection_setup("<", "DESC", $_POST["input_line_id"]);
}

function movedown_line_collection_setup() {
    move_line_collection_setup(">", "ASC", $_POST["input_line_id"]);
}

function move_line_collection_setup( $way, $order, $pic_id ) {
    $query  = "SELECT * FROM main_collection_setup_content WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM main_collection_setup_content WHERE main_collection_setup_id = " . $curr_pic['main_collection_setup_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE main_collection_setup_content SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_collection_setup_content SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}
