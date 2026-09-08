<?
date_default_timezone_set('Europe/Berlin');
require_once __DIR__ . DIRECTORY_SEPARATOR . 'magicscroll.config.inc.php';

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_magicscroll':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_cardform.inc.php';
        break;
    case 'save_magicscroll':
        save_options_magicscroll();
        break;
    case 'edit_magicscroll':
        edit_magicscroll();
        break;
    case 'delete_magicscroll' :
        delete_magicscroll();
        break;

    case 'new_line_magicscroll':
        new_line_magicscroll();
        break;
    case 'edit_line_magicscroll':
        edit_line_magicscroll();
        break;
    case 'delete_line_magicscroll':
        delete_line_magicscroll();
        break;
    case 'save_line_magicscroll':
        save_magicscroll_entry();
        break;

    case 'moveup_line_magicscroll':
        moveup_pic_magicscroll();
        edit_magicscroll();
        break;
    case 'movedown_line_magicscroll':
        movedown_pic_magicscroll();
        edit_magicscroll();
        break;

    case 'update_sortorder_magicscroll':
        update_sortorder_magicscroll();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_listform.inc.php';
        break;
}

function update_sortorder_magicscroll() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE scrollbar_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_magicscroll() {
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_line_cardform.inc.php';
}


function edit_magicscroll( $_id = "", $messages = array() ) {

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

        $query = "SELECT * FROM scrollbar_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_magicscroll = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_cardform.inc.php';

    }
}

function edit_line_magicscroll( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM scrollbar_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_cardform.inc.php';
    }
}


function save_options_magicscroll() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    $width           = (int)$_POST["magicscroll_width"];
    $height          = (int)$_POST["magicscroll_height"];
    $item_width      = (int)$_POST["magicscroll_item_width"];
    $item_height     = (int)$_POST["magicscroll_item_height"];
    $items           = $_POST["magicscroll_items"];
    $step            = $_POST["magicscroll_step"];
    $eff_interval    = $_POST["magicscroll_interval"];
    $effect_duration = $_POST["magicscroll_effect_duration"];
    $arrows          = $_POST["magicscroll_arrows"];

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE scrollbar_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . $width . "', 
				height = '" . $height . "', 
				item_width = '" . $item_width . "', 
				item_height = '" . $item_height . "', 
				arrows = '" . $arrows . "',
				eff_interval = '" . $eff_interval . "',
				effect_duration = '" . $effect_duration . "',
				items = '" . $items . "',
				step = '" . $step . "',
				all_languages = " . $input_all_languages . " 
			WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO scrollbar_header
			(main_language_id, description, modified_date, modified_user, width, height, item_width, item_height, arrows, eff_interval, effect_duration, items, step, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . $width . "',
				'" . $height . "',
				'" . $item_width . "',
				'" . $item_height . "',
				'" . $arrows . "',
				'" . $eff_interval . "',
				'" . $effect_duration . "',
				'" . $items . "',
				'" . $step . "',
				" . $input_all_languages . "
			)";
        $inserted    = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if (!$error) {

        $sizeChanged = FALSE;

        $query  = "SELECT item_width, item_height FROM scrollbar_header WHERE id = '" . $input_id."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $magicscrollWidthHeight = @mysqli_fetch_array($result);
            if ($magicscrollWidthHeight['item_width'] != $item_width || $magicscrollWidthHeight['item_height'] != $item_height) {
                $sizeChanged = TRUE;
            }
        }

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("scrollbar_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("scrollbar_msg_success2") . '</div>';
        }

        if ($sizeChanged === TRUE) {

            $query  = "SELECT * FROM scrollbar_line WHERE header_id = '" . $input_id . "' ORDER BY sorting DESC";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            //Wenn neue Größen ungleich gespeicherte Größen -> Lösche alte Bilder, wandle Original in neue Größe und hinterlege
            WHILE ($row = mysqli_fetch_array($result)) {
                $filename = $row['filename'];
                if (file_exists(PATH_RESIZE_MAGICSCROLL . $filename)) {
                    unlink(PATH_RESIZE_MAGICSCROLL . $filename);
                    image_resize(PATH_ORIGINAL_PICTURE_MAGICSCROLL . $filename, PATH_RESIZE_MAGICSCROLL . $filename, $item_width, $item_height, TRUE);
                }
            }
        }

        update_sitepart_changes(7, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 7, $input_id) === FALSE) {
                assoc_sitepart(7, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_magicscroll($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 7, $input_id) === FALSE) {
                assoc_sitepart(7, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_magicscroll($input_id, $messages);
        } else {
            edit_magicscroll($input_id, $messages);
        }


    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_magicscroll                    = array();
        $input_magicscroll["id"]              = $_REQUEST["input_id"];
        $input_magicscroll["description"]     = $_REQUEST["input_description"];
        $input_magicscroll["width"]           = $width;
        $input_magicscroll["height"]          = $height;
        $input_magicscroll["item_width"]      = $item_width;
        $input_magicscroll["item_height"]     = $item_height;
        $input_magicscroll["arrows"]          = $arrows;
        $input_magicscroll["eff_interval"]    = $eff_interval;
        $input_magicscroll["effect_duration"] = $effect_duration;
        $input_magicscroll["items"]           = $items;
        $input_magicscroll["step"]            = $step;
        $input_magicscroll["all_languages"]   = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_cardform.inc.php';
    }
}

function delete_magicscroll() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM scrollbar_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM scrollbar_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            while ($row = @mysqli_fetch_array($result, 1)) {
                delete_pictures_magicscroll($row['filename']);
            }
        }

        $query = "DELETE FROM scrollbar_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 7 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(7, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_listform.inc.php';
}

function delete_pictures_magicscroll( $filename ) {

    if (file_exists(PATH_ORIGINAL_PICTURE_MAGICSCROLL . $filename)) {
        unlink(PATH_ORIGINAL_PICTURE_MAGICSCROLL . $filename);
    }
    if (file_exists("../.." . PATH_ICON_MAGICSCROLL . $filename)) {
        unlink("../.." . PATH_ICON_MAGICSCROLL . $filename);
    }
    if (file_exists(PATH_RESIZE_MAGICSCROLL . $filename)) {
        unlink(PATH_RESIZE_MAGICSCROLL . $filename);
    }
}

function delete_line_magicscroll() {
    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_line_id"] <> '') {
        $query  = "SELECT * FROM scrollbar_line WHERE id='" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            delete_pictures_magicscroll($row['filename']);
            $query = "DELETE FROM scrollbar_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_magicscroll();
}

/**
 * save_magicscroll_entry() Speichern einzelner Bilder
 *
 */

function save_magicscroll_entry() {

    $translation        = \DynCom\dc\common\classes\Registry::get("translation");
    $messages           = array();
    $id                 = $_REQUEST["input_line_id"];
    $error              = FALSE;
    $checkfile          = TRUE;
    $description        = ''; // ist disabled
    $previewImageString = ''; // in der datenbank fix drin

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // neue dummy gallery anlegen
        // optionen auslesen
        $query  = "SELECT options FROM main_collection_setup_content WHERE id = '" . $_POST['collection_setup_content_id']."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_array($result);

        $options = unserialize($row['options']);
        // hinzufuegen zur datenbank
        $insertquery = "INSERT INTO scrollbar_header
			(main_language_id, description, modified_date, modified_user, width, height, item_width, item_height, arrows, eff_interval, effect_duration, items, step, collection_header)
			VALUES (
				'" . $GLOBALS["language"]['id'] . "',
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . (int)$options['collection_setup_width'] . ",
				" . (int)$options['collection_setup_height'] . ",
				" . (int)$options['collection_setup_item_width'] . ",
				" . (int)$options['collection_setup_item_height'] . ",
				'" . $options['collection_setup_arrows'] . "',
				" . (int)$options['collection_setup_eff_interval'] . ",
				" . (int)$options['collection_setup_effect_duration'] . ",
				" . (int)$options['collection_setup_items'] . ",
				" . (int)$options['collection_setup_step'] . ",
				1
			)";

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			" . $header_id . ",
			7
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = $_REQUEST['input_id'];
    }

    //Bildhöhe und -breite aus Tabelle holen oder mit Standardwerten vorbelegen
    $sql    = "SELECT width, height FROM scrollbar_header WHERE id = '" . $header_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $sql);
    $row    = mysqli_fetch_array($result);
    IF (isset($row["height"])) {
        $height = $row["height"];
    } ELSE {
        $height = 75;
    }
    IF (isset($row["width"])) {
        $width = $row["width"];
    } ELSE {
        $width = 100;
    }

    if ($id == '' && (($_REQUEST["input_name"] == '') | ($_REQUEST["input_name"] == ''))) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if ($id <> '') {
        $query            = "UPDATE scrollbar_line SET description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_name']) . "', link = '" . addhttp($_POST['input_link']) . "', text= '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text']) . "' WHERE id = '" . $id."'";
        $inserted         = FALSE;
        $checkfile        = FALSE;
        $currentFilename  = '';
        $descriptionQuery = "SELECT preview, filename FROM scrollbar_line WHERE id = '" . $id."'";
        $result           = @mysqli_query($GLOBALS['mysql_con'], $descriptionQuery);
        if (@mysqli_num_rows($result) == 1) {
            $row                = @mysqli_fetch_array($result);
            $previewImageString = $row["preview"];
            $currentFilename    = $row['filename'];
        }

    } else {
        $sorting     = mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM scrollbar_line WHERE header_id = '" . $header_id."'"));
        $sorting     = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query       = "INSERT INTO scrollbar_line (description ,header_id,sorting,text,link) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_name']) . "','" . $header_id . "','" . $sorting . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text']) . "','" . addhttp($_POST['input_link']) . "')";
        $inserted    = TRUE;
        $description = $_POST['input_name'];
    }

    if ($checkfile === TRUE && empty ($_FILES['input_file']['name'])) {
        $error      = TRUE;
        $messages[] = "<div class='errorbox'>" . $translation->get("error_slideshow_line1") . "</div>\n";
    }

    if (!empty ($_FILES['input_file']['name'])) {
        $erlaubte_endungen = array("jpg", "jpeg", "gif", "png");
        $filename          = strtolower($_FILES['input_file']['name']);
        $filename_expl     = explode(".", $filename);
        if (!in_array($filename_expl[count($filename_expl) - 1], $erlaubte_endungen)) {
            $messages[] = "<div class='errorbox'>" . $translation->get("error_slideshow_line2") . "</div>\n";
            $error      = TRUE;
        }
        if(!is_valid_image($_FILES['input_file']))
        {
            $messages[] = "<div class='errorbox'>" . $translation->get("error_slideshow_line2") . "</div>\n";
            $error      = TRUE;
        }
    }

    //pruefen ob Datei zum Upload noch vorhanden
    if ($error === FALSE) {
        mysqli_query($GLOBALS['mysql_con'], $query);

        if (!empty ($_FILES['input_file']['name'])) {
            if ($inserted === TRUE) {
                $query  = "SELECT MAX(id) AS id FROM scrollbar_line";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                $row    = mysqli_fetch_array($result);
                $id     = $row['id'];
            }

            $saveFilename       = $id . "_" . cleanFilename($_FILES['input_file']['name']);
            $previewImageString = "<img src=\"" . PATH_ICON_MAGICSCROLL . $saveFilename . "?time=" . time() . "\">";

            if ($inserted === FALSE && $currentFilename != $saveFilename) {
                delete_pictures_magicscroll($currentFilename);
            }

            $query = "UPDATE scrollbar_line SET filename = '" . $saveFilename . "', preview = '<img src=\'" . PATH_ICON_MAGICSCROLL . $saveFilename . "?TIME=" . time() . "\'>' WHERE id ='" . $id."'";
            mysqli_query($GLOBALS['mysql_con'], $query);
            move_uploaded_file($_FILES['input_file']['tmp_name'], PATH_ORIGINAL_PICTURE_MAGICSCROLL . $saveFilename);
            image_resize(PATH_ORIGINAL_PICTURE_MAGICSCROLL . $saveFilename, PATH_RESIZE_MAGICSCROLL . $saveFilename, $width, $height, TRUE);
            image_resize(PATH_ORIGINAL_PICTURE_MAGICSCROLL . $saveFilename, "../.." . PATH_ICON_MAGICSCROLL . $saveFilename, 200, 30, TRUE);
        }

        if ($inserted == TRUE) {
            $messages[] = '<div class="successbox">' . $translation->get("scrollbarline_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("scrollbarline_msg_success2") . '</div>';
        }

        if ($_REQUEST['save_and_close'] == 1) {
            edit_magicscroll();
        } else {
            edit_line_magicscroll($id, $messages);
        }
        return;
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    $input_line                = array();
    $input_line["id"]          = $_REQUEST["input_line_id"];
    $input_line["header_id"]   = $header_id;
    $input_line["text"]        = $_REQUEST["input_text"];
    $input_line["link"]        = $_REQUEST["input_link"];
    $input_line["description"] = $_REQUEST['input_name'];
    $input_line["preview"]     = $previewImageString;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_line_cardform.inc.php';
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_pic_magicscroll() {
    move_pic_magicscroll("<", "DESC", $_POST["input_line_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_pic_magicscroll() {
    move_pic_magicscroll(">", "ASC", $_POST["input_line_id"]);
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
function move_pic_magicscroll( $way, $order, $pic_id ) {
    $query  = "SELECT * FROM scrollbar_line WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM scrollbar_line WHERE header_id = " . $curr_pic['header_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE scrollbar_line SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE scrollbar_line SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}


?>