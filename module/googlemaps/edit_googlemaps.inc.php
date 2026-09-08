<?
date_default_timezone_set('Europe/Berlin');

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_googlemaps':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_cardform.inc.php';
        break;
    case 'save_googlemaps':
        save_google_maps();
        break;
    case 'edit_googlemaps':
        edit_google_maps();
        break;
    case 'delete_googlemaps' :
        delete_google_maps();
        break;

    case 'new_line_googlemaps':
        new_line_googlemaps();
        break;
    case 'edit_line_googlemaps':
        edit_line_google_maps();
        break;
    case 'delete_line_googlemaps':
        delete_line_google_maps();
        break;
    case 'save_line_googlemaps':
        save_googlemaps_entry();
        break;

    case 'update_sortorder_googlemaps':
        update_sortorder_googlemaps();
        break;

    case 'get_pages_textcontent':
        get_pages($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], 0);
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_listform.inc.php';
        break;
}

function update_sortorder_googlemaps() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE google_maps_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_googlemaps() {
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_line_cardform.inc.php';
}

function edit_google_maps( $_id = "", $messages = array() ) {

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

        $query = "SELECT * FROM google_maps_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_google_maps = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_cardform.inc.php';

    }
}

function edit_line_google_maps( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM google_maps_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_cardform.inc.php';
    }
}


function save_google_maps() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    $width         = $_POST['google_maps_width'];
    $height        = $_POST['google_maps_height'];
    $icon_location = $_POST['google_maps_icon_location'];

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE google_maps_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . $width . "', 
				height = '" . $height . "',
				icon_location = '" . $icon_location . "',
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO google_maps_header
			(main_language_id, description, modified_date, modified_user, width, height, icon_location, all_languages)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . $width . "',
				'" . $height . "',
				'" . $icon_location . "',
				" . $input_all_languages . "
			)";
        $inserted    = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if (!$error) {

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("google_maps_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("google_maps_msg_success2") . '</div>';
        }

        update_sitepart_changes(8, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 8, $input_id) === FALSE) {
                assoc_sitepart(8, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_google_maps($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 8, $input_id) === FALSE) {
                assoc_sitepart(8, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_google_maps($input_id, $messages);
        } else {
            edit_google_maps($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_google_maps                  = array();
        $input_google_maps["id"]            = $_REQUEST["input_id"];
        $input_google_maps["description"]   = $_REQUEST["input_description"];
        $input_google_maps["width"]         = $width;
        $input_google_maps["height"]        = $height;
        $input_google_maps["icon_location"] = $icon_location;
        $input_google_maps["all_languages"] = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_cardform.inc.php';
    }
}

function delete_google_maps() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM google_maps_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM google_maps_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 8 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(8, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_listform.inc.php';
}


function delete_line_google_maps() {
    if ($_REQUEST["input_line_id"] <> '') {
        $query  = "SELECT * FROM google_maps_line WHERE id='" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row   = mysqli_fetch_array($result);
            $query = "DELETE FROM google_maps_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_google_maps();
}

/**
 * save_googlemaps_entry() Speichern einzelner Bilder
 *
 */

function save_googlemaps_entry() {
    $latlng      = geoencode($_POST["input_address"]);
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages    = array();
    $id          = $_REQUEST["input_line_id"];

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // neue dummy gallery anlegen
        // optionen auslesen
        $query  = "SELECT options FROM main_collection_setup_content WHERE id = '" . $_POST['collection_setup_content_id']."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_array($result);

        $options = unserialize($row['options']);
        // hinzufuegen zur datenbank
        $insertquery = "INSERT INTO google_maps_header
			(main_language_id, description, modified_date, modified_user, width, height, icon_location, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . (int)$options['collection_setup_google_maps_width'] . ",
				" . (int)$options['collection_setup_google_maps_height'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $options['collection_setup_icon_location']) . "',
				1
			)";

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			" . $header_id . ",
			8
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = (int)$_REQUEST['input_id'];
    }

    if ($_REQUEST["input_line_id"] <> '') {
        $query    = "UPDATE google_maps_line 
                    SET 
                      modified_date = '" . date('Y-m-d H:i:s',time()) . "', 
                      modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ", 
                      title = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_title"]) . "', 
                      address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_address"]) . "', 
                      description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["textcontent"]) . "', 
                      lat = '" . $latlng['lat'] . "', 
                      lng = '" . $latlng['lng'] . "' 
                    WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
        $inserted = FALSE;
    } else {
        $sorting  = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM google_maps_line WHERE header_id = '" . $header_id."'"));
        $sorting  = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query    = "INSERT INTO google_maps_line (header_id, title, address, description,sorting, lat, lng, modified_date, modified_user) 
                      VALUES (" . $header_id . ", 
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_title"]) . "',
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_address"]) . "',
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["textcontent"]) . "',
                      " . $sorting . ", 
                      '" . $latlng['lat'] . "', 
                      '" . $latlng['lng'] . "', 
                      " . time() . ", 
                      " . (int)$GLOBALS["admin_user"]['id'] . "
                      )";
        $inserted = TRUE;
    }

    if (empty($latlng['lat']) || empty($latlng['lng'])) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_address") . "</div>\n";
        $error      = TRUE;
    }

    if (($_REQUEST["input_title"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_title") . "</div>\n";
        $error      = TRUE;
    }


    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($_REQUEST['save_and_close'] == 1) {

            edit_google_maps();
        } else {
            if ($inserted === TRUE) {
                $id         = mysqli_insert_id($GLOBALS['mysql_con']);
                $messages[] = '<div class="successbox">' . $translation->get("google_maps_line_msg_success1") . '</div>';
            } else {
                $messages[] = '<div class="successbox">' . $translation->get("google_maps_line_msg_success2") . '</div>';
            }
            edit_line_google_maps($id, $messages);
        }

    } else {
        $input_line["id"]          = $_REQUEST["input_line_id"];
        $input_line["header_id"]   = $header_id;
        $input_line["title"]       = $_REQUEST["input_title"];
        $input_line["address"]     = $_REQUEST["input_address"];
        $input_line["description"] = $_REQUEST["textcontent"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_line_cardform.inc.php';
    }
}

?>