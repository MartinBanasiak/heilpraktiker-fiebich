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
    case 'new_slidecontent':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_cardform.inc.php';
        break;
    case 'save_slidecontent':
        save_slidecontent();
        break;
    case 'edit_slidecontent':
        edit_slidecontent();
        break;
    case 'delete_slidecontent' :
        delete_slidecontent();
        break;

    case 'new_line_slidecontent':
        new_line_slidecontent();
        break;
    case 'edit_line_slidecontent':
        edit_line_slidecontent();
        break;
    case 'delete_line_slidecontent':
        delete_line_slidecontent();
        break;
    case 'save_line_slidecontent':
        save_slidecontent_entry();
        break;

    case 'moveup_line_slidecontent':
        moveup_pic_slidecontent();
        edit_slidecontent();
        break;
    case 'movedown_line_slidecontent':
        movedown_pic_slidecontent();
        edit_slidecontent();
        break;

    case 'update_sortorder_slidecontent':
        update_sortorder_slidecontent();
        break;

    case 'get_pages_textcontent':
        get_pages($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], 0);
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_listform.inc.php';
        break;
}

function update_sortorder_slidecontent() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE slidecontent_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_slidecontent() {
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_line_cardform.inc.php';
}

function edit_slidecontent( $_id = "", $messages = array() ) {

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

        $query = "SELECT * FROM slidecontent_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_slidecontent = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_cardform.inc.php';

    }
}

function edit_line_slidecontent( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM slidecontent_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_cardform.inc.php';
    }
}


function save_slidecontent() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE slidecontent_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO slidecontent_header
			(main_language_id, modified_date, modified_user, description, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
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
            $messages[] = '<div class="successbox">' . $translation->get("slidecontent_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("slidecontent_msg_success2") . '</div>';
        }

        update_sitepart_changes(4, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 4, $input_id) === FALSE) {
                assoc_sitepart(4, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_slidecontent($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 4, $input_id) === FALSE) {
                assoc_sitepart(4, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_slidecontent($input_id, $messages);
        } else {
            edit_slidecontent($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_slidecontent                = array();
        $input_slidecontent["id"]          = $_REQUEST["input_id"];
        $input_slidecontent["description"] = $_REQUEST["input_description"];
        $input_slidecontent["all_languages"]= $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_cardform.inc.php';
    }
}

function delete_slidecontent() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM slidecontent_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM slidecontent_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 4 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(4, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_listform.inc.php';
}


function delete_line_slidecontent() {
    if ($_REQUEST["input_line_id"] <> '') {
        $query  = "SELECT * FROM slidecontent_line WHERE id= '" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row   = mysqli_fetch_array($result);
            $query = "DELETE FROM slidecontent_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_slidecontent();
}

/**
 * save_slidecontent_entry() Speichern einzelner Bilder
 *
 */

function save_slidecontent_entry() {
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
        $insertquery = "INSERT INTO slidecontent_header
			(main_language_id, description, modified_date, modified_user, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				1
			)";

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			" . $header_id . ",
			4
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = (int)$_REQUEST['input_id'];
    }

    if ($_REQUEST["input_line_id"] <> '') {
        $query    = "UPDATE slidecontent_line 
                      SET modified_date = '" . date('Y-m-d H:i:s',time()) . "', 
                        modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ", 
                        headline = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_headline"]) . "', 
                        content = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["textcontent"]) . "' 
                      WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
        $inserted = FALSE;
    } else {
        $sorting  = @mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM slidecontent_line WHERE header_id = '" . $header_id."'"));
        $sorting  = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query    = "INSERT INTO slidecontent_line (header_id, headline, content ,sorting, modified_date, modified_user) 
                    VALUES (
                      '" . $header_id . "', 
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_headline"]) . "',
                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["textcontent"]) . "',
                      " . $sorting . ", 
                      " . time() . ", 
                      " . (int)$GLOBALS["admin_user"]['id'] . "
                    )";
        $inserted = TRUE;
    }
    if (($_REQUEST["input_headline"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_headline") . "</div>\n";
        $error      = TRUE;
    }


    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($_REQUEST['save_and_close'] == 1) {

            edit_slidecontent();
        } else {
            if ($inserted === TRUE) {
                $id         = mysqli_insert_id($GLOBALS['mysql_con']);
                $messages[] = '<div class="successbox">' . $translation->get("slidecontent_line_msg_success1") . '</div>';
            } else {
                $messages[] = '<div class="successbox">' . $translation->get("slidecontent_line_msg_success2") . '</div>';
            }
            edit_line_slidecontent($id, $messages);
        }

    } else {
        $input_line["id"]        = $_REQUEST["input_line_id"];
        $input_line["header_id"] = $header_id;
        $input_line["headline"]  = $_REQUEST["input_title"];
        $input_line["content"]   = $_REQUEST["textcontent"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_line_cardform.inc.php';
    }
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_pic_slidecontent() {
    move_pic_slidecontent("<", "DESC", $_POST["input_line_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_pic_slidecontent() {
    move_pic_slidecontent(">", "ASC", $_POST["input_line_id"]);
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
function move_pic_slidecontent( $way, $order, $pic_id ) {
    $query  = "SELECT * FROM slidecontent_line WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM slidecontent_line WHERE header_id = " . $curr_pic['header_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE slidecontent_line SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE slidecontent_line SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}