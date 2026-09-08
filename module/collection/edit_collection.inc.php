<?php
date_default_timezone_set('Europe/Berlin');

update_visitor_data(array(
    'collection_options' => array(
        'collection_edit' => TRUE
    )
));

$messages = array();
$error = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

$subaction = substr($action, strrpos($action, '_') + 1);

global $collection_setup_id;
$collection_setup_id = NULL;
if (isset($_GET["level_2"]) && (int)$_GET["level_2"]) {
    $collection_setup_id = $_GET["level_2"];
}

if ($action == "" || $subaction == 'collection') {
    switch ($action) {
        case 'new_collection':
            save_collection(TRUE);
            break;
        case 'save_collection':
            save_collection();
            break;
        case 'edit_collection':
            edit_collection();
            break;
        case 'delete_collection' :
            delete_collection();
            break;

        // Kollektionen filtern
        case 'filter_collection':
            filter_collection();
            break;

        case 'moveup_line_collection':
            moveup_collection();
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_listform.inc.php';
            break;
        case 'movedown_line_collection':
            movedown_collection();
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_listform.inc.php';
            break;

        case 'update_sortorder_collection' :
            update_sortorder_collection();
            break;

        case 'open_card_collection':
            open_card_collection();
        default:
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_listform.inc.php';
            break;
    }
} else {
    // hier handelt es sich um ein sitepart
    require_once(MODULE_PATH . $subaction . "/edit_" . $subaction . ".inc.php");
}

function update_sortorder_collection()
{
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE main_collection SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function open_card_collection()
{
    echo "<script type=\"text/javascript\">";

    echo "openCardOnStart = true; openCardAction='edit_collection';";

    echo "</script>";
}

function get_collection_setup_info()
{
    global $collection_setup_id;
    $query = "SELECT * FROM main_collection_setup WHERE id = '" . $collection_setup_id."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);

    return @mysqli_fetch_array($result);
}

function filter_collection()
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $filter_id = $_REQUEST['filter_id'];

    $where = "";
    if ((int)$filter_id > 0) {
        $where = " and main_collection_setup_id = " . $filter_id;
    }

    $query = "SELECT
		main_collection.id, 
		main_collection_setup.description AS '" . $translation->get("type") . "',
		main_collection.description AS '" . $translation->get("description") . "',
		from_unixtime(main_collection.modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
		main_admin_user.name AS '" . $translation->get("changed_by") . "' 
	from 
		main_collection left join main_admin_user
	ON 
		main_collection.modified_user = main_admin_user.id
		inner join main_collection_setup
	ON
	 	main_collection.main_collection_setup_id = main_collection_setup.id
	where 
		(main_collection_setup.main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR main_collection_setup.all_languages = 1) $where ORDER BY id asc";

    $format = array('option', 'text', 'text', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, "form_collection_list", $format, "input_collection_id", "edit_collection");
    }
}

function edit_line_collection($_id = "", $messages = array())
{

    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {

        $query = "SELECT * FROM main_collection_content WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_line = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_line_cardform.php';
        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_line_cardform.php';

    }
}


/**
 * delete_picture() Löschen einzelner Bilder
 *
 * @param mixed $sitepart
 *
 * @return
 */

function edit_collection($_id = "", $messages = array())
{

    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_collection_id"];
    }

    if ($input_id <> '') {

        $query = "SELECT * FROM main_collection WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_collection = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_cardform.inc.php';

    }
}


function save_collection($firstsave = FALSE)
{
    global $collection_setup_id;

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id = "";

    $input_registration = ($_POST["input_registration"] == "on") ? 1 : 0;
    $registration_contactform_id = 0;

    if ($input_registration == 1) {
        $registration_contactform_id = (int)$_POST['registration_contactform_id'];
    }

    $input_validity_from = datetosql($_POST["input_validity_from"]);
    $input_validity_to = datetosql($_POST["input_validity_to"]);

    $input_noindex = ($_POST["input_noindex"] == "on") ? 1 : 0;
    $input_nofollow = ($_POST["input_nofollow"] == "on") ? 1 : 0;

    //edit oder save
    if ($_REQUEST["input_collection_id"] <> '' && $firstsave === FALSE) {
        $insertquery = "UPDATE main_collection SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				registration = " . $input_registration . ",
				registration_contactform_id = '" . $registration_contactform_id . "',
				validity_from = " . $input_validity_from . ",
				validity_to = " . $input_validity_to . ",
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
                subtitle = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_browser_title"]) . "',
				meta_description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_description"]) . "',
				noindex = '" . $input_noindex . "',
				nofollow = '" . $input_nofollow . "',
				session_id = ''
				
				WHERE id = '" . $_REQUEST["input_collection_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_collection_id"];
    } else {
        $sorting = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM main_collection WHERE main_collection_setup_id = '" . $collection_setup_id."'"));
        $sorting = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;

        $insertquery = "INSERT INTO main_collection
			(main_language_id, main_collection_setup_id, description, registration, registration_contactform_id, modified_date, modified_user, validity_from, validity_to, sorting,subtitle,meta_description, noindex, nofollow, session_id)
			VALUES (
				" . $GLOBALS["language"]['id'] . ",
				'" . $collection_setup_id . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . $input_registration . ",
				'" . $registration_contactform_id . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $input_validity_from . ",
				" . $input_validity_to . ",
				" . $sorting . ",
			   '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_browser_title"]) . "',
			    '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_meta_description"]) . "',
			     " . $input_noindex . ",
			      " . $input_nofollow . ",
			      '" . session_id() . "'
			)";
        $inserted = TRUE;
    }

    if ($firstsave === FALSE && ($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_description") . "</div>\n";
        $error = TRUE;
    }
    if ($_REQUEST["input_collection_id"] == '' && ($collection_setup_id === NULL)) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_collection_choose_setup") . "</div>\n";
        $error = TRUE;
    }

    if (!$error) {

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id = mysqli_insert_id($GLOBALS['mysql_con']);
            if ($firstsave === FALSE) {
                $messages[] = '<div class="successbox">' . $translation->get("collection_msg_success1") . '</div>';
            }
        } else {

            // hinzufuegen eines collection links aus NICHT siteparts
            foreach ($_POST as $key => $val) {
                if (strpos($key, "input_collection_hidden_info_") !== FALSE) {
                    $errorArr = handle_standard_content($key, $val, $input_id);

                    if (!empty($errorArr)) {
                        $error = $errorArr["error"];
                        $message = $errorArr["messages"];
                    }
                    continue;
                }

                if (strpos($key, "collection_modul_input_") !== FALSE) {
                    handle_sitepart_content($key, $val, $input_id);
                    continue;
                }
            }
            if (!$error) {
                $messages[] = '<div class="successbox">' . $translation->get("collection_msg_success2") . '</div>';
            } else {
                $messages[] = $message;
            }
        }

        if (!$error) {
            $_REQUEST["input_collection_id"] = $input_id;

            // gruppen speichern
            $query = "SELECT id, default_active FROM main_collection_setup_group WHERE main_collection_setup_id = '" . $collection_setup_id."'";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $box = array();
            while ($box = @mysqli_fetch_assoc($result)) {
                $query_2 = "SELECT id, active FROM main_collection_group_link WHERE main_collection_setup_group_id = '" . $box["id"] . "' AND main_collection_id = '" . $input_id . "' LIMIT 1";
                $result_2 = @mysqli_query($GLOBALS['mysql_con'], $query_2);
                $arr_ind = "input_collection_group_" . $box["id"] . "_active";
                if ($_POST[$arr_ind] == "on") {
                    $active = '1';
                } else {
                    $active = '0';
                }
                if (@mysqli_num_rows($result_2) == 0) {
                    $query_3 = "INSERT INTO main_collection_group_link SET
					main_collection_id = " . $input_id . ",
					main_collection_setup_group_id = " . $box["id"] . ",
					active = " . $active;
                    @mysqli_query($GLOBALS['mysql_con'], $query_3);
                } else {
                    $row = @mysqli_fetch_assoc($result_2);
                    $result_3 = $row[0];
                    if ($active != $result_3["active"]) {
                        $query_3 = "UPDATE main_collection_group_link SET
						active = " . $active . " WHERE
						main_collection_id = " . $input_id . " AND
						main_collection_setup_group_id = " . $box["id"];
                        @mysqli_query($GLOBALS['mysql_con'], $query_3);
                    }
                }
            }

            // Neue Sortierung speichern, wenn gewuenscht
            if ($_REQUEST['input_change_sorting'] == 1) {
                // nach oben schieben
                $query = "UPDATE main_collection SET sorting = 1 WHERE id = " . $input_id;
                @mysqli_query($GLOBALS['mysql_con'], $query);

                $query = "SELECT id from main_collection where id != '" . $input_id . "' and main_collection_setup_id = '" . $collection_setup_id . "' order by sorting asc";
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $sorting = 2;
                while ($row = @mysqli_fetch_assoc($result)) {
                    $query = "UPDATE main_collection SET sorting = " . $sorting . " WHERE id = " . $row['id'];
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                    $sorting++;
                }

            } elseif ($_REQUEST['input_change_sorting'] == 2) {
                // nach unten schieben
                $sorting = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM main_collection WHERE main_collection_setup_id = " . $collection_setup_id));
                $sorting = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;

                $query = "UPDATE main_collection SET sorting = " . $sorting . " WHERE id = " . $input_id;
                @mysqli_query($GLOBALS['mysql_con'], $query);
            }

            edit_collection($input_id, $messages);
        } else {
            $input_collection = array();
            $query = "SELECT * FROM main_collection WHERE id = '" . $input_id . "' LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $input_collection = @mysqli_fetch_array($result);
            }
            headerFunctionBridge('EDIT_ERROR: 1');
            $input_collection["description"] = $_REQUEST["input_description"];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_cardform.inc.php';
        }
    } else {
        $input_collection = array();
        $query = "SELECT * FROM main_collection WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_collection = @mysqli_fetch_array($result);
        }

        headerFunctionBridge('EDIT_ERROR: 1');
        $input_collection["description"] = $_REQUEST["input_description"];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_cardform.inc.php';
    }
}

function handle_sitepart_content($key, $value, $input_id)
{
    switch ($value) {
        case 'textcontent':
            collection_save_textcontent($key);
            break;
        case 'facebook':
            collection_save_facebook($key);
            break;

        case 'youtube':
            collection_save_youtube($key);
            break;
        case 'iframe':
            collection_save_iframe($key);
            break;
        case 'shop_item_preview':
            collection_save_shop_item_preview($key);
            break;
    }
}

function collection_save_textcontent($key)
{
    // pruefen ob header existiert und speichern
    $setup_line_id = substr($key, strlen("collection_modul_input_"));
    $collection_link = get_collection_link($_REQUEST['input_collection_id'], $setup_line_id);
    if (is_array($collection_link)) {
        // update
        $query = "UPDATE textcontent_header SET
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				content = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_textcontent_text_' . $setup_line_id]) . "', 
				type = 0
				WHERE id = " . $collection_link['main_sitepart_header_id'] . " 
			LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        // insert
        $query = "INSERT INTO textcontent_header
			(main_language_id, description, modified_date, modified_user, type, content, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				0,
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_textcontent_text_' . $setup_line_id]) . "',
				1
			)";


        @mysqli_query($GLOBALS['mysql_con'], $query);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			" . $setup_line_id . ",
			" . $header_id . ",
			1
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function collection_save_facebook($key)
{
    // pruefen ob header existiert und speichern
    $setup_line_id = substr($key, strlen("collection_modul_input_"));
    $collection_link = get_collection_link($_REQUEST['input_collection_id'], $setup_line_id);

    $input_show_faces = ($_POST["module_input_facebook_show_faces_" . $setup_line_id] == "on") ? 1 : 0;
    $input_show_posts = ($_POST["module_input_facebook_show_posts_" . $setup_line_id] == "on") ? 1 : 0;

    if (is_array($collection_link)) {
        $query = "UPDATE facebook_header SET
				description = '',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . (int)$_REQUEST["module_input_facebook_width_" . $setup_line_id] . "', 
				height = '" . (int)$_REQUEST["module_input_facebook_height_" . $setup_line_id] . "', 
				show_faces = " . $input_show_faces . ", 
				show_posts = " . $input_show_posts . ", 
				url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["module_input_facebook_url_" . $setup_line_id]) . "'
				WHERE id = " . $collection_link['main_sitepart_header_id'] . " 
			LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $query = "INSERT INTO facebook_header
			(main_language_id, description, modified_date, modified_user, width, height, show_faces, show_posts, url, collection_header)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . (int)$_REQUEST["module_input_facebook_width_" . $setup_line_id] . "',
				'" . (int)$_REQUEST["module_input_facebook_height_" . $setup_line_id] . "',
				" . $input_show_faces . ",
				" . $input_show_posts . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["module_input_facebook_url_" . $setup_line_id]) . "',
				1
			)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			" . $setup_line_id . ",
			" . $header_id . ",
			9
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function collection_save_youtube($key)
{
    // pruefen ob header existiert und speichern
    $setup_line_id = substr($key, strlen("collection_modul_input_"));
    $collection_link = get_collection_link($_REQUEST['input_collection_id'], $setup_line_id);
    $youtube_id = "";

    preg_match(
        '/[\\?\\&]v=([^\\?\\&]+)/',
        $_REQUEST["module_input_youtube_url_" . $setup_line_id],
        $matches
    );
    if (isset($matches[1])) {
        $youtube_id = $matches[1];
    }

    if (is_array($collection_link)) {
        $query = "UPDATE youtube_header SET
				description = '',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . (int)$_REQUEST["module_input_youtube_width_" . $setup_line_id] . "', 
				height = '" . (int)$_REQUEST["module_input_youtube_height_" . $setup_line_id] . "', 
				url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["module_input_youtube_url_" . $setup_line_id]) . "',
				youtube_id = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $youtube_id) . "'
				WHERE id = " . $collection_link['main_sitepart_header_id'] . " 
			LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $query = "INSERT INTO youtube_header
			(main_language_id, description, modified_date, modified_user, width, height, url, youtube_id, collection_header)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . (int)$_REQUEST["module_input_youtube_width_" . $setup_line_id] . "',
				'" . (int)$_REQUEST["module_input_youtube_height_" . $setup_line_id] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["module_input_youtube_url_" . $setup_line_id]) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $youtube_id) . "',
				1
			)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			" . $setup_line_id . ",
			" . $header_id . ",
			10
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function collection_save_iframe($key)
{
    // pruefen ob header existiert und speichern
    $setup_line_id = substr($key, strlen("collection_modul_input_"));
    $collection_link = get_collection_link($_REQUEST['input_collection_id'], $setup_line_id);

    if (is_array($collection_link)) {
        $query = "UPDATE iframe_header SET
				description = '',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . (int)$_REQUEST["module_input_iframe_width_" . $setup_line_id] . "', 
				height = '" . (int)$_REQUEST["module_input_iframe_height_" . $setup_line_id] . "', 
				url = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], addhttp($_REQUEST["module_input_iframe_url_" . $setup_line_id])) . "'
				WHERE id = " . $collection_link['main_sitepart_header_id'] . " 
			LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $query = "INSERT INTO iframe_header
			(main_language_id, description, modified_date, modified_user, width, height, url, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . (int)$_REQUEST["module_input_iframe_width_" . $setup_line_id] . "',
				'" . (int)$_REQUEST["module_input_iframe_height_" . $setup_line_id] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], addhttp($_REQUEST["module_input_iframe_url_" . $setup_line_id])) . "',
				1
			)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			" . $setup_line_id . ",
			" . $header_id . ",
			11
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function collection_save_shop_item_preview($key)
{
    // pruefen ob header existiert und speichern
    $setup_line_id = substr($key, strlen("collection_modul_input_"));
    $collection_link = get_collection_link($_REQUEST['input_collection_id'], $setup_line_id);

    if (is_array($collection_link)) {
        $query    = "UPDATE main_shop_item_preview SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_description_' . $setup_line_id]) . "',
				no_of_items = '" . (int)$_REQUEST["module_input_no_of_items_" . $setup_line_id] . "',
				category_code_string = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_category_code_string_' . $setup_line_id]) . "',
				item_no_string = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_item_no_string_' . $setup_line_id]) . "',
				modified_date = FROM_UNIXTIME (" . time() . "),
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . "
				WHERE id = " . $collection_link['main_sitepart_header_id'] . " 
			LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $query    = "INSERT INTO main_shop_item_preview
			(main_language_id, description, no_of_items, category_code_string, item_no_string, modified_date, modified_user)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_description_' . $setup_line_id]) . "',
				'" . (int)$_REQUEST["module_input_no_of_items_" . $setup_line_id] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_category_code_string_' . $setup_line_id]) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['module_input_item_no_string_' . $setup_line_id]) . "',
				FROM_UNIXTIME (" . time() . "),
				" . (int)$GLOBALS["admin_user"]['id'] . "
			)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			" . $setup_line_id . ",
			" . $header_id . ",
			14
		)";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function handle_standard_content($key, $value, $input_id)
{
    $collection_setup_content = substr($key, strlen("input_collection_hidden_info_"));
    $postValue = $_REQUEST['input_collection_link_' . $collection_setup_content];
    $type_id = $_REQUEST['input_collection_hidden_info_' . $collection_setup_content];
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $newlink = TRUE;
    $query = "SELECT * FROM main_collection_link WHERE main_collection_id = '" . $input_id . "' AND main_collection_setup_content_id = '" . (int)$collection_setup_content."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $linkData = array();
    if (@mysqli_num_rows($result) == 1) {
        $newlink = FALSE;
        $linkData = @mysqli_fetch_array($result);
    }

    // einzelnes bild entfernen, falls checkbox gesetzt
    if (isset($_POST['delete_collection_image_' . $collection_setup_content]) && $_POST['delete_collection_image_' . $collection_setup_content] == "on") {
        delete_collection_image($linkData['data']);
        $linkData['data'] = "";
    }

    $options = array();
    $query = "SELECT options FROM main_collection_setup_content WHERE id = '" . (int)$collection_setup_content."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $row = @mysqli_fetch_array($result);
        $options = unserialize($row['options']);
    }

    switch ($type_id) {

        case 4: //link
            $postValue = addhttp($postValue);
            break;
        case 5: // bild
            $filekey = 'input_collection_link_' . $collection_setup_content;

            $error = FALSE;
            if (!empty ($_FILES[$filekey]['name'])) {
                $erlaubte_endungen = array("jpg", "jpeg", "gif", "png");
                $filename = strtolower($_FILES[$filekey]['name']);
                $filename_expl = explode(".", $filename);
                if (!in_array($filename_expl[count($filename_expl) - 1], $erlaubte_endungen)) {
                    $messages = "<div class='errorbox'>" . $translation->get("error_slideshow_line2") . "</div>\n";
                    $error = TRUE;
                }

                if(!is_valid_image($_FILES[$filekey]))
                {
                    $messages = "<div class='errorbox'>" . $translation->get("error_slideshow_line2") . "</div>\n";
                    $error = TRUE;
                }
            }

            if (!empty ($_FILES[$filekey]['name']) && $error === FALSE) {

                $saveFilename = $input_id . "_" . (int)$collection_setup_content . "_" . cleanFilename($_FILES[$filekey]['name']);

                if ($newlink === FALSE && $linkData['data'] != $saveFilename) {
                    delete_collection_image($linkData['data']);
                }

                delete_collection_image($saveFilename);

                $width = 300;
                $height = 200;
                if ((int)$options['collection_setup_image_width'] > 0) {
                    $width = $options['collection_setup_image_width'];
                }
                if ((int)$options['collection_setup_image_height'] > 0) {
                    $height = $options['collection_setup_image_height'];
                }

                move_uploaded_file($_FILES[$filekey]['tmp_name'], PATH_ORIGINAL_PICTURE_COLLECTION . $saveFilename);
                image_resize(PATH_ORIGINAL_PICTURE_COLLECTION . $saveFilename, PATH_RESIZE_COLLECTION . $saveFilename, $width, $height, TRUE);
                image_resize(PATH_ORIGINAL_PICTURE_COLLECTION . $saveFilename, "../.." . PATH_ICON_COLLECTION . $saveFilename, 200, 100, TRUE);

                $postValue = $saveFilename;

            } else {
                $postValue = $linkData['data'];
            }
            break;
        default:
            // nichts machen
            break;
    }



    if (!$error) {

        if ($newlink === FALSE) {
            $query = "  UPDATE main_collection_link SET data = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $postValue) . "' WHERE main_collection_id = '" . $input_id . "'  AND main_collection_setup_content_id = '" . (int)$collection_setup_content."'";
        } else {
            $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id, data)
			VALUES (
				'" . $input_id . "',
				'" . (int)$collection_setup_content . "',
				0,
				0,
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $postValue) . "'
			)
		";
        }

        @mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        return array('messages' => $messages, 'error' => $error);
    }
}

function delete_collection()
{
    global $collection_setup_id;

    if ($_REQUEST["input_collection_id"] <> '') {
        $query = "DELETE FROM main_collection WHERE id = '" . $_REQUEST["input_collection_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_collection_link WHERE main_collection_id = '" . $_REQUEST["input_collection_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_listform.inc.php';
}

function delete_unused_collections() {
    // nicht mehr benutzte kollektionen aufraumen
    $query = "DELETE FROM main_collection where description = '' AND modified_date < " . (time() - 86400);
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function create_collection_input_field($fieldSetup, $collectionData)
{

    echo "<div class=\"collection_field\">";
    echo "<div class=\"label\"><label for=\"" . $fieldSetup['code'] . "\">" . $fieldSetup['fieldname'] . "</label></div>";
    echo "	<div class=\"input\">";

    if ($fieldSetup['fieldtype'] == 'siteparts') {
        $siteparts = \DynCom\dc\common\classes\Siteparts::get();
        $sitepartConfig = $siteparts[$fieldSetup['type_id']];
        $headerData = get_sitepart_header_data($collectionData['id'], $fieldSetup['id']);
        require(MODULE_PATH . $sitepartConfig['folder'] . "/collection_" . $sitepartConfig['code'] . "_line_listform.inc.php");
    } else {

        // vorhandenen value auslesen
        $data = '';
        $query = "SELECT data FROM main_collection_link WHERE main_collection_setup_content_id = " . $fieldSetup['id'] . " AND main_collection_id = " . $collectionData['id'];
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $row = @mysqli_fetch_array($result);
            $data = $row['data'];
        }

        echo "<form id=\"form_collection_input_" . $fieldSetup['id'] . "\" method=\"post\" name=\"form_collection_input_" . $fieldSetup['id'] . "\">";
        echo "<input type=\"hidden\" name=\"input_collection_hidden_info_" . $fieldSetup['id'] . "\" value=\"" . $fieldSetup['type_id'] . "\" />";
        switch ($fieldSetup['type_id']) {

            case 2: // textarea
                echo "<textarea id=\"input_collection_link_" . $fieldSetup['id'] . "\" class=\"normal\" maxlength=\"255\" name=\"input_collection_link_" . $fieldSetup['id'] . "\">" . htmlspecialchars($data) . "</textarea>";
                break;

            case 3: // datumsfeld
                echo "<input id=\"input_collection_link_" . $fieldSetup['id'] . "\" class=\"code datepicker\" type=\"text\" value=\"" . $data . "\" maxlength=\"255\" name=\"input_collection_link_" . $fieldSetup['id'] . "\">";
                echo "<script type=\"text/javascript\">jQuery('#input_collection_link_" . $fieldSetup['id'] . "').datepicker();</script>";
                break;

            case 4: // link
                echo "<input id=\"input_collection_link_" . $fieldSetup['id'] . "\" class=\"code\" type=\"text\" value=\"" . $data . "\" maxlength=\"255\" name=\"input_collection_link_" . $fieldSetup['id'] . "\">";
                break;

            case 5: // einzelnes bild
                if ($data != "") {
                    echo "<table cellpadding=\"0\" cellspacing=\"0\">";
                    echo "<tr><td>";
                    echo "<img src=\"" . PATH_ICON_COLLECTION . $data . "?time=" . time() . "\" /></td><td><input id=\"delete_collection_image_" . $fieldSetup['id'] . "\" value=\"0\" type=\"hidden\" name=\"delete_collection_image_" . $fieldSetup['id'] . "\" />&nbsp;";
                    echo "<a class='button_delete inline_button left' href='javascript:void(0);' onclick='delete_image(" . $fieldSetup['id'] . ");'>Bild entfernen</a></td>";
                    echo "</tr>";
                    echo "</table>";
                }
                echo "<input id=\"input_collection_link_" . $fieldSetup['id'] . "\" class=\"code\" type=\"file\" name=\"input_collection_link_" . $fieldSetup['id'] . "\">";
                break;

            case 1: // normales inputfeld
            default:
                echo "<input id=\"input_collection_link_" . $fieldSetup['id'] . "\" class=\"code\" type=\"text\" value=\"" . htmlspecialchars($data) . "\" maxlength=\"255\" name=\"input_collection_link_" . $fieldSetup['id'] . "\">";
                break;
        }
        echo "</form>";
    }

    echo "	</div>";
    echo "</div>";
}

function get_sitepart_header_data($collection_id, $setup_content_id)
{
    $siteparts = \DynCom\dc\common\classes\Siteparts::get();

    $collection_link = get_collection_link($collection_id, $setup_content_id);
    if (!is_array($collection_link)) {
        return array();
    }

    if ($collection_link['main_sitepart_id'] == 0) {
        return array();
    }

    $query = "SELECT * FROM " . $siteparts[$collection_link['main_sitepart_id']]['header_table'] . " WHERE id = " . $collection_link['main_sitepart_header_id'];
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $row = @mysqli_fetch_array($result);
        return $row;
    }

    return array();
}

function moveup_line_collection()
{
    move_line_collection("<", "DESC", $_POST["input_line_id"]);
}

function movedown_line_collection()
{
    move_line_collection(">", "ASC", $_POST["input_line_id"]);
}

function move_line_collection($way, $order, $pic_id)
{
    $query = "SELECT * FROM main_collection_content WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM main_collection_content WHERE main_collection_id = " . $curr_pic['main_collection_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE main_collection_content SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_collection_content SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function collection_link_exists($collection_id, $setup_content_id)
{
    $query = "SELECT id FROM main_collection_link WHERE main_collection_id = '" . $collection_id . "' AND main_collection_setup_content_id = '" . $setup_content_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 0) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function get_collection_link($collection_id, $setup_content_id)
{
    $query = "SELECT * FROM main_collection_link WHERE main_collection_id = '" . $collection_id . "' AND main_collection_setup_content_id = '" . $setup_content_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $row = @mysqli_fetch_array($result);
    return $row;
}

function delete_collection_image($filename)
{

    if (file_exists(PATH_ORIGINAL_PICTURE_COLLECTION . $filename) && !empty($filename)) {
        unlink(PATH_ORIGINAL_PICTURE_COLLECTION . $filename);
    }
    if (file_exists("../.." . PATH_ICON_COLLECTION . $filename) && !empty($filename)) {
        unlink("../.." . PATH_ICON_COLLECTION . $filename);
    }
    if (file_exists(PATH_RESIZE_COLLECTION . $filename) && !empty($filename)) {
        unlink(PATH_RESIZE_COLLECTION . $filename);
    }
}

function collection_filter($site_id, $language_id)
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    echo "<div class=\"input\"><select onchange=\"filter_collection();\" class=\"bigselect\" name=\"collection_filter\" id=\"collection_filter\">";
    echo "<option value=''>" . $translation->get("show_all") . "</option>";
    collection_filter_rek($site_id, $language_id);
    echo "</select>";
    echo "</div>";
}

function collection_filter_rek($site_id, $language_id)
{
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_collection_setup WHERE (main_language_id = " . $language_id . " OR all_languages = 1) ORDER BY id ASC");
    if (@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $active = "";
            if (isset($_REQUEST['filter_id']) && $_REQUEST['filter_id'] == $nav['id']) {
                $active = 'selected="selected"';
            }
            echo "\n<option " . $active . " value=\"" . $nav["id"] . "\">" . $nav["description"] . "</option>";
        }
    }
    return TRUE;
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_collection()
{
    move_collection("<", "DESC", $_POST["input_collection_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_collection()
{
    move_collection(">", "ASC", $_POST["input_collection_id"]);
}

/**
 * move_pic() Soprtierung der Galerieeintraege
 *
 * @param mixed $way
 * @param mixed $order
 * @param mixed $pic_id
 *
 * @return
 */
function move_collection($way, $order, $pic_id)
{
    $query = "SELECT main_collection_setup_id FROM main_collection WHERE id = '" . $pic_id."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row = @mysqli_fetch_array($result);

    $main_collection_setup_id = $row['main_collection_setup_id'];

    $query = "SELECT * FROM main_collection WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM main_collection WHERE main_collection_setup_id = '" . $main_collection_setup_id . "' AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE main_collection SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_collection SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function create_collection_groups_select($input_collection)
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $collection_id = $_POST['input_collection_id'];
    $setup_id = $input_collection['main_collection_setup_id'];

    $query = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = '" . $setup_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 0) {
        return;
    }


    while ($row = @mysqli_fetch_array($result)) {
        $query2 = "SELECT active FROM main_collection_group_link WHERE
			main_collection_id = '" . $collection_id . "' AND
			main_collection_setup_group_id = " . $row['id'];

        $result2 = mysqli_query($GLOBALS['mysql_con'], $query2);
        if (@mysqli_num_rows($result2) == 0) {
            input($row['description'], "input_collection_group_" . $row["id"] . "_active", "checkbox", $row["default_active"]);
        } else {
            input($row['description'], "input_collection_group_" . $row["id"] . "_active", "checkbox", @mysqli_result($result2, 0));
        }
    }
}