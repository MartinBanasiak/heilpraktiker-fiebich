<?

date_default_timezone_set('Europe/Berlin');
require_once __DIR__ . DIRECTORY_SEPARATOR . 'slideshow.config.inc.php';

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_slideshow':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_cardform.inc.php';
        break;
    case 'save_slideshow':
        save_options_slideshow();
        break;
    case 'edit_slideshow':
        edit_slideshow();
        break;
    case 'delete_slideshow' :
        delete_slideshow();
        break;

    case 'new_line_slideshow':
        new_line_slideshow();
        break;
    case 'edit_line_slideshow':
        edit_line_slideshow();
        break;
    case 'delete_line_slideshow':
        delete_line_slideshow();
        break;
    case 'save_line_slideshow':
        save_slideshow_entry();
        break;

    case 'moveup_line_slideshow':
        moveup_pic_slideshow();
        edit_slideshow();
        break;
    case 'movedown_line_slideshow':
        movedown_pic_slideshow();
        edit_slideshow();
        break;

    case 'update_sortorder_slideshow':
        update_sortorder_slideshow();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_listform.inc.php';
        break;
}

function update_sortorder_slideshow() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE slideshow_line SET sorting = " . $sorting . " WHERE id = " . $itemId;
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_slideshow() {
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_line_cardform.inc.php';
}

function edit_slideshow( $_id = "", $messages = array() ) {

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

        $query = "SELECT * FROM slideshow_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_slideshow = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_cardform.inc.php';

    }
}

function edit_line_slideshow( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM slideshow_line WHERE id = '" . $line_id . "'  LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_cardform.inc.php';
    }
}


function save_options_slideshow() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";

    $width  = $_POST['slideshow_width'];
    $height = $_POST['slideshow_height'];
    $effect = $_POST['slideshow_effect'];
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    IF ($_POST['slideshow_arrows'] == "yes") {
        global $arrows;
        $arrows = "";
    } ELSE {
        global $arrows;
        $arrows = "no";
    }
    $eff_interval    = $_POST['slideshow_interval'];
    $effect_duration = $_POST['slideshow_effect_duration'];
    $text_effect     = $_POST['slideshow_text_effect'];
    $text_pos        = $_POST['slideshow_text_pos'];

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE slideshow_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				width = '" . $width . "', 
				height = '" . $height . "', 
				effect = '" . $effect . "',
				arrows = '" . $arrows . "',
				eff_interval = '" . $eff_interval . "',
				effect_duration = '" . $effect_duration . "',
				text_effect = '" . $text_effect . "',
				text_pos = '" . $text_pos . "',
				all_languages = " . $input_all_languages . " 
				WHERE id = " . $_REQUEST["input_id"] . " 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO slideshow_header
			(main_language_id, description, modified_date, modified_user, width, height, effect, arrows, eff_interval, effect_duration, text_effect, text_pos, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				'" . $width . "',
				'" . $height . "',
				'" . $effect . "',
				'" . $arrows . "',
				'" . $eff_interval . "',
				'" . $effect_duration . "',
				'" . $text_effect . "',
				'" . $text_pos . "',
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

        $query  = "SELECT width, height FROM slideshow_header WHERE id = '" . $input_id."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $slideshowWidthHeight = @mysqli_fetch_array($result);
            if ($slideshowWidthHeight['width'] != $width || $slideshowWidthHeight['height'] != $height) {
                $sizeChanged = TRUE;
            }
        }

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("slideshow_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("slideshow_msg_success2") . '</div>';
        }

        if ($sizeChanged === TRUE) {
            $query  = "SELECT * FROM slideshow_line WHERE header_id = '" . $input_id . "' ORDER BY sorting DESC";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            //Wenn neue Größen ungleich gespeicherte Größen -> Lösche alte Bilder, wandle Original in neue Größe und hinterlege
            WHILE ($row = mysqli_fetch_array($result)) {
                $filename = $row['filename'];

                if (file_exists(PATH_RESIZE . $filename) && ($row["width"] <> $width || $row["height"] <> $height)) {
                    unlink(PATH_RESIZE . $filename);
                    image_resize(PATH_ORIGINAL_PICTURE . $filename, PATH_RESIZE . $filename, $width, $height, TRUE);
                }
            }
        }

        update_sitepart_changes(2, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 2, $input_id) === FALSE) {
                assoc_sitepart(2, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_slideshow($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 2, $input_id) === FALSE) {
                assoc_sitepart(2, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_slideshow("", $messages);
                return;
            }

            edit_slideshow($input_id, $messages);
        } else {
            edit_slideshow($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_slideshow                    = array();
        $input_slideshow["id"]              = $_REQUEST["input_id"];
        $input_slideshow["description"]     = $_REQUEST["input_description"];
        $input_slideshow["width"]           = $width;
        $input_slideshow["height"]          = $height;
        $input_slideshow["effect"]          = $effect;
        $input_slideshow["arrows"]          = $arrows;
        $input_slideshow["eff_interval"]    = $eff_interval;
        $input_slideshow["effect_duration"] = $effect_duration;
        $input_slideshow["text_effect"]     = $text_effect;
        $input_slideshow["text_pos"]        = $text_pos;
        $input_slideshow["all_languages"] = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_cardform.inc.php';
    }
}

function delete_slideshow() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM slideshow_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM slideshow_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            while ($row = @mysqli_fetch_array($result, 1)) {
                delete_pictures_slideshow($row['filename']);
            }
        }

        $query = "DELETE FROM slideshow_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 2 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(2, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_listform.inc.php';
}

function delete_pictures_slideshow( $filename ) {

    if (file_exists(PATH_ORIGINAL_PICTURE . $filename)) {
        unlink(PATH_ORIGINAL_PICTURE . $filename);
    }
    if (file_exists("../.." . PATH_ICON . $filename)) {
        unlink("../.." . PATH_ICON . $filename);
    }
    if (file_exists(PATH_RESIZE . $filename)) {
        unlink(PATH_RESIZE . $filename);
    }
}

function delete_line_slideshow() {
    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_line_id"] <> '') {
        $query  = "SELECT * FROM slideshow_line WHERE id= '" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            delete_pictures_slideshow($row['filename']);
            $query = "DELETE FROM slideshow_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_slideshow();
}

/**
 * save_slideshow_entry() Speichern einzelner Bilder
 *
 */

function save_slideshow_entry() {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages    = array();
    $id          = $_REQUEST["input_line_id"];
    $input_validity_from = datetosql($_POST["input_validity_from"]);
    $input_validity_to = datetosql($_POST["input_validity_to"]);

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // neue dummy slideshow anlegen

        // optionen auslesen
        $query  = "SELECT options FROM main_collection_setup_content WHERE id = '" . $_POST['collection_setup_content_id']."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_array($result);

        $options = unserialize($row['options']);
        // hinzufuegen zur datenbank
        $insertquery = "INSERT INTO slideshow_header
			(main_language_id, description, modified_date, modified_user, width, height, effect, arrows, eff_interval, effect_duration, text_effect, text_pos, collection_header)
			VALUES (
				" . $GLOBALS["language"]['id'] . ",
				'',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $options['collection_setup_width'] . ",
				" . $options['collection_setup_height'] . ",
				'" . $options['collection_setup_effect'] . "',
				'" . $options['collection_setup_arrows'] . "',
				'" . $options['collection_setup_eff_interval'] . "',
				'" . $options['collection_setup_effect_duration'] . "',
				'" . $options['collection_setup_text_effect'] . "',
				'" . $options['collection_setup_text_pos'] . "',
				1
			)";
        @mysqli_query($GLOBALS['mysql_con'], $insertquery);
        $header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			'" . $header_id . "',
			2
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = $_REQUEST['input_id'];
    }

    $error              = FALSE;
    $checkfile          = TRUE;
    $description        = ''; // ist disabled
    $previewImageString = ''; // in der datenbank fix drin

    //Bildhöhe und -breite aus Tabelle holen oder mit Standardwerten vorbelegen
    $sql    = "SELECT width, height FROM slideshow_header WHERE id = '" . $header_id."'";
    $result = mysqli_query($GLOBALS['mysql_con'], $sql);
    $row    = mysqli_fetch_array($result);
    IF (isset($row["height"])) {
        $height = $row["height"];
    } ELSE {
        $height = 250;
    }
    IF (isset($row["width"])) {
        $width = $row["width"];
    } ELSE {
        $width = 500;
    }

    if ($id == '' && (($_REQUEST["input_name"] == '') | ($_REQUEST["input_name"] == ''))) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if ($id <> '') {
        $query            = "UPDATE slideshow_line SET 
                                    description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_name']) . "', 
                                    link = '" . addhttp($_POST['input_link']) . "', 
                                    headline= '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_headline']) . "', 
                                    text= '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text']) . "', 
                                    text2= '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text2']) . "',
                                    bg_position= '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_bg_position']) . "',
                                    validity_from = " . $input_validity_from . ",
				                    validity_to = " . $input_validity_to . "
                             WHERE id = " . $id;
        $inserted         = FALSE;
        $checkfile        = FALSE;
        $currentFilename  = '';
        $descriptionQuery = "SELECT preview, filename FROM slideshow_line WHERE id = '" . $id."'";
        $result           = @mysqli_query($GLOBALS['mysql_con'], $descriptionQuery);
        if (@mysqli_num_rows($result) == 1) {
            $row                = @mysqli_fetch_array($result);
            $previewImageString = $row["preview"];
            $currentFilename    = $row['filename'];
        }

    } else {
        $sorting     = mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM slideshow_line WHERE header_id = '" . $header_id."'"));
        $sorting     = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query       = "INSERT INTO slideshow_line (description ,info,header_id,sorting,text,headline,link,text2, bg_position, validity_from, validity_to) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_name']) . "', 'hochgeladen von " . $GLOBALS['admin_user']['name'] . " am " . date('d.m.Y H:i:s') . "','" . $header_id . "','" . $sorting . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text']) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_headline']) . "','" . addhttp($_POST['input_link']) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_text_2']) . "', '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_bg_position']) . "', " . $input_validity_from . "," . $input_validity_to . ")";
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
                $query  = "SELECT MAX(id) AS id FROM slideshow_line";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                $row    = mysqli_fetch_array($result);
                $id     = $row['id'];
            }

            $saveFilename       = $id . "_" . cleanFilename($_FILES['input_file']['name']);
            $previewImageString = "<img src=\"" . PATH_ICON . $saveFilename . "?time=" . time() . "\">";

            if ($inserted === FALSE && $currentFilename != $saveFilename) {
                delete_pictures_slideshow($currentFilename);
            }

            $query = "UPDATE slideshow_line SET filename = '" . $saveFilename . "', preview = '<img src=\'" . PATH_ICON . $saveFilename . "?TIME=" . time() . "\'>' WHERE id ='" . $id."'";
            mysqli_query($GLOBALS['mysql_con'], $query);
            move_uploaded_file($_FILES['input_file']['tmp_name'], PATH_ORIGINAL_PICTURE . $saveFilename);
            image_resize(PATH_ORIGINAL_PICTURE . $saveFilename, PATH_RESIZE . $saveFilename, $width, $height, TRUE);
            image_resize(PATH_ORIGINAL_PICTURE . $saveFilename, "../.." . PATH_ICON . $saveFilename, 200, 30, TRUE);
        }

        if ($inserted == TRUE) {
            $messages[] = '<div class="successbox">' . $translation->get("slideshowline_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("slideshowline_msg_success2") . '</div>';
        }

        if ($_REQUEST['save_and_close'] == 1) {
            edit_slideshow();
        } else {
            edit_line_slideshow($id, $messages);
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
    $input_line["headline"]    = $_REQUEST["input_headline"];
    $input_line["text"]        = $_REQUEST["input_text"];
    $input_line["text2"]        = $_REQUEST["input_text2"];
    $input_line["bg_position"]        = $_REQUEST["input_bg_position"];
    $input_line["link"]        = $_REQUEST["input_link"];
    $input_line["description"] = $_REQUEST['input_name'];
    $input_line["validity_from"] = $_REQUEST["input_validity_from"];
    $input_line["validity_to"] = $_REQUEST["input_validity_to"];
    $input_line["preview"]     = $previewImageString;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_line_cardform.inc.php';
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_pic_slideshow() {
    move_pic_slideshow("<", "DESC", $_POST["input_line_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_pic_slideshow() {
    move_pic_slideshow(">", "ASC", $_POST["input_line_id"]);
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
function move_pic_slideshow( $way, $order, $pic_id ) {
    $query  = "SELECT * FROM slideshow_line WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM slideshow_line WHERE header_id = " . $curr_pic['header_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE slideshow_line SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE slideshow_line SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}