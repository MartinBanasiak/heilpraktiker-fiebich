<?
date_default_timezone_set('Europe/Berlin');
require_once __DIR__ . DIRECTORY_SEPARATOR . 'gallery.config.inc.php';

$messages = array();
$error    = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_gallery':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_cardform.inc.php';
        break;
    case 'save_gallery':
        save_gallery();
        break;
    case 'edit_gallery':
        edit_gallery();
        break;
    case 'delete_gallery' :
        delete_gallery();
        break;

    case 'new_line_gallery':
        new_line_gallery();
        break;
    case 'edit_line_gallery':
        edit_line_gallery();
        break;
    case 'delete_line_gallery':
        delete_line_gallery();
        break;
    case 'save_line_gallery':
        save_gallery_entry();
        break;

    case 'moveup_line_gallery':
        moveup_pic_gallery();
        edit_gallery();
        break;
    case 'movedown_line_gallery':
        movedown_pic_gallery();
        edit_gallery();
        break;

    case 'update_sortorder_gallery':
        update_sortorder_gallery();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_listform.inc.php';
        break;
}

function update_sortorder_gallery() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE gallery_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_gallery() {
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_line_cardform.inc.php';
}


function edit_gallery( $_id = "", $messages = array() ) {

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

        $query = "SELECT * FROM gallery_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_gallery = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_cardform.inc.php';

    }
}

function edit_line_gallery( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query  = "SELECT * FROM gallery_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_cardform.inc.php';
    }
}


function save_gallery() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id    = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    $width        = (int)$_POST['gallery_width'];
    $height       = (int)$_POST['gallery_height'];
    $thumb_width  = (int)$_POST['gallery_thumb_width'];
    $thumb_height = (int)$_POST['gallery_thumb_height'];

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE gallery_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
			width = '" . $width . "' , 
				height = '" . $height . "' ,
				thumb_width = '" . $thumb_width . "' , 
				thumb_height = '" . $thumb_height . "',
				all_languages = " . $input_all_languages . "
				WHERE id = " . $_REQUEST["input_id"] . " 
			LIMIT 1";
        $inserted    = FALSE;
        $input_id    = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO gallery_header
			(main_language_id, description, modified_date, modified_user, width, height, thumb_width, thumb_height, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $width . ",
				" . $height . ",
				" . $thumb_width . ",
				" . $thumb_height . ",
				" . $input_all_languages . "
			)";
        $inserted    = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if (!$error) {
        $sizeChanged      = FALSE;
        $sizeThumbChanged = FALSE;

        $query  = "SELECT width, height, thumb_width, thumb_height FROM gallery_header WHERE id = '" . $input_id."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $galleryWidthHeight = @mysqli_fetch_array($result);
            if ($galleryWidthHeight['width'] != $width || $galleryWidthHeight['height'] != $height) {
                $sizeChanged = TRUE;
            }

            if ($galleryWidthHeight['thumb_width'] != $thumb_width || $galleryWidthHeight['thumb_height'] != $thumb_height) {
                $sizeThumbChanged = TRUE;
            }
        }

        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("gallery_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("gallery_msg_success2") . '</div>';
        }

        if ($sizeChanged === TRUE) {
            $query  = "SELECT * FROM gallery_line WHERE header_id = '" . $input_id . "' ORDER BY sorting DESC";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            //Wenn neue Größen ungleich gespeicherte Größen -> Lösche alte Bilder, wandle Original in neue Größe und hinterlege
            WHILE ($row = mysqli_fetch_array($result)) {
                $filename = $row['filename'];

                if (file_exists(PATH_RESIZE_GALLERY . $filename) && ($row["width"] <> $width || $row["height"] <> $height)) {
                    unlink(PATH_RESIZE_GALLERY . $filename);
                    image_resize(PATH_ORIGINAL_PICTURE_GALLERY . $filename, PATH_RESIZE_GALLERY . $filename, $width, $height, TRUE);
                }
            }
        }

        if ($sizeThumbChanged === TRUE) {
            $query  = "SELECT * FROM gallery_line WHERE header_id = '" . $input_id . "' ORDER BY sorting DESC";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            //Wenn neue Größen ungleich gespeicherte Größen -> Lösche alte Bilder, wandle Original in neue Größe und hinterlege
            WHILE ($row = mysqli_fetch_array($result)) {
                $filename = $row['filename'];

                if (file_exists(PATH_RESIZE_GALLERY . $filename) && ($row["thumb_width"] <> $thumb_width || $row["thumb_height"] <> $thumb_height)) {
                    unlink(PATH_THUMBNAIL . $filename);
                    image_resize(PATH_ORIGINAL_PICTURE_GALLERY . $filename, PATH_THUMBNAIL_GALLERY . $filename, $thumb_width, $thumb_height, TRUE);
                }
            }
        }

        update_sitepart_changes(6, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 6, $input_id) === FALSE) {
                assoc_sitepart(6, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_gallery($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 6, $input_id) === FALSE) {
                assoc_sitepart(6, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_gallery($input_id, $messages);
        } else {
            edit_gallery($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_gallery                 = array();
        $input_gallery["id"]           = $_REQUEST["input_id"];
        $input_gallery["description"]  = $_REQUEST["input_description"];
        $input_gallery["width"]        = $width;
        $input_gallery["height"]       = $height;
        $input_gallery["thumb_width"]  = $thumb_width;
        $input_gallery["thumb_height"] = $thumb_height;
        $input_gallery["all_languages"] = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_cardform.inc.php';
    }
}

function delete_gallery() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM gallery_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM gallery_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            while ($row = @mysqli_fetch_array($result, 1)) {
                delete_pictures_gallery($row['filename']);
            }
        }

        $query = "DELETE FROM gallery_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 6 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(6, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_listform.inc.php';
}

function delete_pictures_gallery( $filename ) {

    if (file_exists(PATH_ORIGINAL_PICTURE_GALLERY . $filename)) {
        unlink(PATH_ORIGINAL_PICTURE_GALLERY . $filename);
    }
    if (file_exists("../.." . PATH_ICON_GALLERY . $filename)) {
        unlink("../.." . PATH_ICON_GALLERY . $filename);
    }
    if (file_exists(PATH_RESIZE_GALLERY . $filename)) {
        unlink(PATH_RESIZE_GALLERY . $filename);
    }
    if (file_exists(PATH_THUMBNAIL_GALLERY . $filename)) {
        unlink(PATH_THUMBNAIL_GALLERY . $filename);
    }
}

function delete_line_gallery() {
    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_line_id"] <> '') {
        $query  = "SELECT * FROM gallery_line WHERE id='" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            delete_pictures_gallery($row['filename']);
            $query = "DELETE FROM gallery_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_gallery();
}

/**
 * save_gallery_entry() Speichern einzelner Bilder
 *
 */

function save_gallery_entry() {

    $translation        = \DynCom\dc\common\classes\Registry::get("translation");
    $messages           = array();
    $id                 = $_REQUEST["input_line_id"];
    $error              = FALSE;
    $checkfile          = TRUE;
    $description        = '';
    $previewImageString = ''; // in der datenbank fix drin

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // neue dummy gallery anlegen
        // optionen auslesen
        $query  = "SELECT options FROM main_collection_setup_content WHERE id = '" . $_POST['collection_setup_content_id']."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_array($result);

        $options = unserialize($row['options']);

        $headerQuery = "SELECT main_sitepart_header_id FROM main_collection_link
                        WHERE main_collection_id = " . $_REQUEST['input_collection_id'] . "
                        AND main_collection_setup_content_id = " . $_REQUEST['collection_setup_content_id'];
        $headerResult = mysqli_query($GLOBALS['mysql_con'], $headerQuery);
        $headerRows = mysqli_num_rows($headerResult);
        if ($headerRows == 0) {

            // hinzufuegen zur datenbank
            $insertquery = "INSERT INTO gallery_header
                (main_language_id, description, modified_date, modified_user, width, height, thumb_width, thumb_height, collection_header)
                VALUES (
                    '" . $GLOBALS["language"]['id'] . "',
                    '',
                    " . time() . ",
                    " . (int)$GLOBALS["admin_user"]['id'] . ",
                    " . (int)$options['collection_setup_gallery_width'] . ",
                    " . (int)$options['collection_setup_gallery_height'] . ",
                    " . (int)$options['collection_setup_gallery_thumb_width'] . ",
                    " . (int)$options['collection_setup_gallery_thumb_height'] . ",
                    1
                )";

            @mysqli_query($GLOBALS['mysql_con'], $insertquery);
            $header_id = mysqli_insert_id($GLOBALS['mysql_con']);
        } else {
            $header = mysqli_fetch_array($headerResult);
            $header_id = $header['main_sitepart_header_id'];
        }

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			'" . $_REQUEST['input_collection_id'] . "',
			'" . $_REQUEST['collection_setup_content_id'] . "',
			" . $header_id . ",
			6
		)";
        mysqli_query($GLOBALS['mysql_con'], $query);
    } else {
        $header_id = $_REQUEST['input_id'];
    }


    //Bildhöhe und -breite aus Tabelle holen oder mit Standardwerten vorbelegen
    $sql          = "SELECT width, height, thumb_width, thumb_height FROM gallery_header WHERE id ='" . $header_id."'";
    $result       = mysqli_query($GLOBALS['mysql_con'], $sql);
    $row          = mysqli_fetch_array($result);
    $width        = $row['width'];
    $height       = $row['height'];
    $thumb_width  = $row['thumb_width'];
    $thumb_height = $row['thumb_height'];


    if ($width <= 0 || $height <= 0) {
        $width  = 500;
        $height = 250;
    }

    if ($thumb_width <= 0 || $thumb_height <= 0) {
        $width  = 100;
        $height = 100;
    }

    if ($_REQUEST["input_name"] == "" && !empty ($_FILES['input_file']['name'])) {
        $description = cleanFilename($_FILES['input_file']['name']);
    }

    if ($_REQUEST["input_name"] != "") {
        $description = $_REQUEST["input_name"];
    }

    if ($id == '' && $description == "") {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error      = TRUE;
    }

    if ($id <> '') {
        $query            = "UPDATE gallery_line SET description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $description) . "' WHERE id = '" . $id."'";
        $inserted         = FALSE;
        $checkfile        = FALSE;
        $currentFilename  = '';
        $descriptionQuery = "SELECT preview, filename FROM gallery_line WHERE id = '" . $id."'";
        $result           = @mysqli_query($GLOBALS['mysql_con'], $descriptionQuery);
        if (@mysqli_num_rows($result) == 1) {
            $row                = @mysqli_fetch_array($result);
            $previewImageString = $row["preview"];
            $currentFilename    = $row['filename'];
        }

    } else {
        $sorting  = mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM gallery_line WHERE header_id = '" . $header_id."'"));
        $sorting  = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query    = "INSERT INTO gallery_line (description,header_id,sorting) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $description) . "','" . $header_id . "','" . $sorting . "')";
        $inserted = TRUE;
    }

    if ($checkfile === TRUE && empty ($_FILES['input_file']['name'])) {
        $error      = TRUE;
        $messages[] = "<div class='errorbox'>" . $translation->get("error_gallery_line1") . "</div>\n";
    }

    if (!empty ($_FILES['input_file']['name'])) {
        $erlaubte_endungen = array("jpg", "jpeg", "png", "gif");
        $filename          = strtolower($_FILES['input_file']['name']);
        $filename_expl     = explode(".", $filename);
        if (!in_array(strtolower($filename_expl[count($filename_expl) - 1]), $erlaubte_endungen)) {
            $messages[] = "<div class='errorbox'>" . $translation->get("error_gallery_line2") . "</div>\n";
            $error      = TRUE;
        }

        if(!is_valid_image($_FILES['input_file']))
        {
            $messages[] = "<div class='errorbox'>" . $translation->get("error_gallery_line2") . "</div>\n";
            $error      = TRUE;
        }
    }

    //pruefen ob Datei zum Upload noch vorhanden
    if ($error === FALSE) {
        mysqli_query($GLOBALS['mysql_con'], $query);

        if (!empty ($_FILES['input_file']['name'])) {
            if ($inserted === TRUE) {
                $query  = "SELECT MAX(id) AS id FROM gallery_line";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                $row    = mysqli_fetch_array($result);
                $id     = $row['id'];
            }

            $saveFilename       = $id . "_" . cleanFilename($_FILES['input_file']['name']);
            $previewImageString = "<img src=\"" . PATH_ICON_GALLERY . $saveFilename . "?time=" . time() . "\">";

            if ($inserted === FALSE && $currentFilename != $saveFilename) {
                delete_pictures_gallery($currentFilename);
            }

            $query = "UPDATE gallery_line SET filename = '" . $saveFilename . "', preview = '<img src=\'" . PATH_ICON_GALLERY . $saveFilename . "?TIME=" . time() . "\'>' WHERE id = '" . $id."'";
            mysqli_query($GLOBALS['mysql_con'], $query);
            move_uploaded_file($_FILES['input_file']['tmp_name'], PATH_ORIGINAL_PICTURE_GALLERY . $saveFilename);
            image_resize(PATH_ORIGINAL_PICTURE_GALLERY . $saveFilename, PATH_RESIZE_GALLERY . $saveFilename, $width, $height, TRUE);
            image_resize(PATH_ORIGINAL_PICTURE_GALLERY . $saveFilename, PATH_THUMBNAIL_GALLERY . $saveFilename, $thumb_width, $thumb_height, TRUE);
            image_resize(PATH_ORIGINAL_PICTURE_GALLERY . $saveFilename, "../.." . PATH_ICON_GALLERY . $saveFilename, 200, 30, TRUE);
        }

        if ($inserted == TRUE) {
            $messages[] = '<div class="successbox">' . $translation->get("galleryline_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("galleryline_msg_success2") . '</div>';
        }

        if ($_REQUEST['save_and_close'] == 1) {
            edit_gallery();
        } else {
            edit_line_gallery($id, $messages);
        }
        return;
    }

    // das dropzone plugin zeigt dann mit echo ausgegebene error meldung
    if($error === true && isset($_POST['dcDropzone'])) {
        headerFunctionBridge("HTTP/1.0 400 Bad Request");
        echo $translation->get("dropzone_upload_failed");
        exit();
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    $input_line                = array();
    $input_line["id"]          = $_REQUEST["input_line_id"];
    $input_line["header_id"]   = $header_id;
    $input_line["description"] = $_REQUEST['input_name'];
    $input_line["preview"]     = $previewImageString;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_line_cardform.inc.php';
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_pic_gallery() {
    move_pic_gallery("<", "DESC", $_POST["input_line_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_pic_gallery() {
    move_pic_gallery(">", "ASC", $_POST["input_line_id"]);
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
function move_pic_gallery( $way, $order, $pic_id ) {
    $query  = "SELECT * FROM gallery_line WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM gallery_line WHERE header_id = " . $curr_pic['header_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE gallery_line SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE gallery_line SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}