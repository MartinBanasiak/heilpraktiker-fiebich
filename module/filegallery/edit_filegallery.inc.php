<?
date_default_timezone_set('Europe/Berlin');
require_once __DIR__ . DIRECTORY_SEPARATOR . 'filegallery.config.inc.php';

$messages = array();
$error = FALSE;

if (isset($custom_action)) {
    $action = $custom_action;
} else {
    $action = $_REQUEST["action"];
}

switch ($action) {
    case 'new_filegallery':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_cardform.inc.php';
        break;
    case 'save_filegallery':
        save_filegallery();
        break;
    case 'edit_filegallery':
        edit_filegallery();
        break;
    case 'delete_filegallery' :
        delete_filegallery();
        break;

    case 'new_line_filegallery':
        new_line_filegallery();
        break;
    case 'edit_line_filegallery':
        edit_line_filegallery();
        break;
    case 'delete_line_filegallery':
        delete_line_filegallery();
        break;
    case 'save_line_filegallery':
        save_filegallery_entry();
        break;

    case 'moveup_line_filegallery':
        moveup_pic_filegallery();
        edit_filegallery();
        break;
    case 'movedown_line_filegallery':
        movedown_pic_filegallery();
        edit_filegallery();
        break;

    case 'update_sortorder_filegallery':
        update_sortorder_filegallery();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_listform.inc.php';
        break;
}

function update_sortorder_filegallery()
{
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE filegallery_line SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}

function new_line_filegallery()
{
    $input_line = array();

    if (is_collection_edit()) {
        $collection_link = get_collection_link($_REQUEST['input_collection_id'], $_REQUEST['collection_setup_content_id']);
        if (isset($collection_link['main_sitepart_header_id'])) {
            $input_line['header_id'] = $collection_link['main_sitepart_header_id'];
        }
    } else {
        $input_line['header_id'] = $_REQUEST['input_id'];
    }

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_line_cardform.inc.php';
}


function edit_filegallery($_id = "", $messages = array())
{

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

        $query = "SELECT * FROM filegallery_header WHERE id = '" . $input_id . "' LIMIT 1";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {

            $input_filegallery = @mysqli_fetch_array($result);

            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_cardform.inc.php';

        }

    } else {

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_cardform.inc.php';

    }
}

function edit_line_filegallery($_id = "", $messages = array())
{
    if ((int)$_id > 0) {
        $line_id = $_id;
    } else {
        $line_id = $_REQUEST["input_line_id"];
    }

    if ($line_id <> '') {
        $query = "SELECT * FROM filegallery_line WHERE id = '" . $line_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_line = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_line_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_cardform.inc.php';
    }
}


function save_filegallery()
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $input_id = "";
    $input_all_languages = ($_POST["input_all_languages"] == "on") ? 1 : 0;

    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_id"] <> '') {
        $insertquery = "UPDATE filegallery_header SET
				description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_description"]) . "',
				modified_date = '" . date('Y-m-d H:i:s',time()) . "',
				modified_user = " . (int)$GLOBALS["admin_user"]['id'] . ",
				all_languages = " . $input_all_languages . "
				WHERE id = '" . $_REQUEST["input_id"] . "' 
			LIMIT 1";
        $inserted = FALSE;
        $input_id = $_REQUEST["input_id"];
    } else {
        $insertquery = "INSERT INTO filegallery_header
			(main_language_id, description, modified_date, modified_user, all_languages)
			VALUES (
				" . (int)$GLOBALS["language"]['id'] . ",
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST['input_description']) . "',
				" . time() . ",
				" . (int)$GLOBALS["admin_user"]['id'] . ",
				" . $input_all_languages . "
			)";
        $inserted = TRUE;
    }

    if (($_REQUEST["input_description"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $insertquery);

        if ($inserted == TRUE) {
            $input_id = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("filegallery_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("filegallery_msg_success2") . '</div>';
        }

        update_sitepart_changes(5, $input_id);

        if (is_page_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_page_id"], 5, $input_id) === FALSE) {
                assoc_sitepart(5, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_content_page();
                return;
            }

            edit_filegallery($input_id, $messages);

        } elseif (is_component_edit()) {
            header('EDIT_ERROR: 1'); // verhindern, dass overlay geschlossen wird
            if (line_already_exists($_REQUEST["input_component_id"], 5, $input_id) === FALSE) {
                assoc_sitepart(5, $input_id, FALSE);
            }

            if ($_REQUEST['force_close'] == 1) {
                edit_component("", $messages);
                return;
            }

            edit_filegallery($input_id, $messages);
        } else {
            edit_filegallery($input_id, $messages);
        }

    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_filegallery = array();
        $input_filegallery["id"] = $_REQUEST["input_id"];
        $input_filegallery["description"] = $_REQUEST["input_description"];
        $input_filegallery["all_languages"] = $input_all_languages;

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_cardform.inc.php';
    }
}

function delete_filegallery()
{
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM filegallery_header WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * FROM filegallery_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            while ($row = @mysqli_fetch_array($result, 1)) {
                delete_files_filegallery($row['filename']);
            }
        }

        $query = "DELETE FROM filegallery_line WHERE header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        // zuordnung von Seiten loeschen
        $query = "DELETE FROM main_page_link WHERE main_sitepart_id = 5 AND main_sitepart_header_id = '" . $_REQUEST["input_id"]."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        update_sitepart_changes(5, $_REQUEST["input_id"], TRUE);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_listform.inc.php';
}

function delete_files_filegallery($filename)
{
    if (file_exists(PATH_ORIGINAL_FILEGALLERY . $filename)) {
        unlink(PATH_ORIGINAL_FILEGALLERY . $filename);
    }
}

function delete_line_filegallery()
{
    //pruefen ob Bild ausgewaehlt
    if ($_REQUEST["input_line_id"] <> '') {
        $query = "SELECT * FROM filegallery_line WHERE id= '" . $_REQUEST["input_line_id"]."'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //Loeschen der Dateien und des DB-Eintrags wenn Eintrag vorhanden
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            delete_files_filegallery($row['filename']);
            $query = "DELETE FROM filegallery_line WHERE id = '" . $_REQUEST["input_line_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
    edit_filegallery();
}

/**
 * save_filegallery_entry() Speichern einzelner Bilder
 *
 */

function save_filegallery_entry()
{

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();
    $id = $_REQUEST["input_line_id"];
    $error = FALSE;
    $checkfile = TRUE;
    $description = '';
    $previewImageString = ''; // in der datenbank fix drin

    // wenn in kollektion, dann die dummy header id auslesen bzw erstmal anlegen
    if (is_collection_edit() && (!isset($_REQUEST['input_id']) || (int)$_REQUEST['input_id'] == 0)) {
        // hinzufuegen zur datenbank

        $headerQuery = "SELECT main_sitepart_header_id FROM main_collection_link
            WHERE main_collection_id = ?
            AND main_collection_setup_content_id = ? LIMIT 1";

        $collectionSetupContentId = filter_var($_REQUEST['collection_setup_content_id'],FILTER_SANITIZE_NUMBER_INT);
        $inputCollectionId = filter_var($_REQUEST['input_collection_id'],FILTER_SANITIZE_NUMBER_INT);

        $stmt = mysqli_prepare($GLOBALS["mysql_con"], $headerQuery);
        mysqli_stmt_bind_param($stmt, "ii", $inputCollectionId, $collectionSetupContentId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $headerRows = mysqli_stmt_num_rows($stmt);

        if ($headerRows == 0) {

            $insertquery = "INSERT INTO filegallery_header
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
        } else {
            mysqli_stmt_bind_result($stmt, $main_sitepart_header_id);
            while (mysqli_stmt_fetch($stmt)) {
                $header_id = $main_sitepart_header_id;
            }
        }
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

        $query = "INSERT INTO main_collection_link (main_collection_id, main_collection_setup_content_id, main_sitepart_header_id, main_sitepart_id)
		VALUES (
			?,
			?,
			?,
			5
		)";

        $stmt = mysqli_prepare($GLOBALS["mysql_con"], $query);
        mysqli_stmt_bind_param($stmt, "iii", $inputCollectionId, $collectionSetupContentId, $header_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);

    } else {
        $header_id = $_REQUEST['input_id'];
    }

    if ($_REQUEST["input_name"] == "" && !empty ($_FILES['input_file']['name'])) {
        $description = cleanFilename($_FILES['input_file']['name']);
    }

    if ($_REQUEST["input_name"] != "") {
        $description = $_REQUEST["input_name"];
    }

    if ($id == '' && $description == "") {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_description") . "</div>\n";
        $error = TRUE;
    }

    if ($id <> '') {
        $query = "UPDATE filegallery_line SET description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $description) . "' WHERE id = '" . $id."'";
        $inserted = FALSE;
        $checkfile = FALSE;
        $currentFilename = '';
        $currentExtension = '';
        $descriptionQuery = "SELECT extension, filename FROM filegallery_line WHERE id = '" . $id."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $descriptionQuery);
        if (@mysqli_num_rows($result) == 1) {
            $row = @mysqli_fetch_array($result);
            $currentFilename = $row['filename'];
            $currentExtension = $row['extension'];
        }

    } else {
        $sorting = mysqli_fetch_array(@mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS 'newsorting' FROM filegallery_line WHERE header_id = " . $header_id));
        $sorting = ($sorting["newsorting"] <> "") ? $sorting = $sorting["newsorting"] : $sorting = 1;
        $query = "INSERT INTO filegallery_line (description,header_id,sorting) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $description) . "','" . $header_id . "','" . $sorting . "')";
        $inserted = TRUE;
    }

    if ($checkfile === TRUE && empty ($_FILES['input_file']['name'])) {
        $error = TRUE;
        $messages[] = "<div class='errorbox'>" . $translation->get("error_filegallery_line1") . "</div>\n";
    }

    if (!empty ($_FILES['input_file']['name'])) {

        $fileCheckerService = new \DynCom\dc\common\classes\UploadedFileTypeCheckingService();
        $filePath = $_FILES['input_file']['tmp_name'];
        $filename_expl = explode(".", $_FILES['input_file']['name']);
        $extension = $filename_expl[count($filename_expl) - 1];
        $allowCompressedFiles = getenv('ALLOW_COMPERESSED_FILES');

        if ($extension === 'zip' && $allowCompressedFiles) {
            if (!$fileCheckerService->isFileAllowed($filePath,$fileCheckerService::TYPE_ZIP,$extension)) {
                $messages[] = "<div class='errorbox'>" . $translation->get("error_filegallery_line2") . "</div>\n";
                $error = TRUE;
            }
        } else {
            $erlaubte_endungen = array("pdf", "doc", "docx", "xls", "xlsx", "csv", "txt", "rtf", "zip", "mp3", "wma", "mpg", "flv", "avi", "jpg", "jpeg", "png", "gif");
            $filename = strtolower($_FILES['input_file']['name']);
            $filename_expl = explode(".", $filename);
            if (!in_array(strtolower($filename_expl[count($filename_expl) - 1]), $erlaubte_endungen)) {
                $messages[] = "<div class='errorbox'>" . $translation->get("error_filegallery_line2") . "</div>\n";
                $error = TRUE;
            }

            if(!is_valid_file($_FILES['input_file']))
            {
                $messages[] = "<div class='errorbox'>" . $translation->get("error_filegallery_line2") . "</div>\n";
                $error = TRUE;
            }
        }
    }

    //pruefen ob Datei zum Upload noch vorhanden
    if ($error === FALSE) {
        mysqli_query($GLOBALS['mysql_con'], $query);

        if (!empty ($_FILES['input_file']['name'])) {
            if ($inserted === TRUE) {
                $query = "SELECT MAX(id) AS id FROM filegallery_line";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                $row = mysqli_fetch_array($result);
                $id = $row['id'];
            }

            $filename = strtolower($_FILES['input_file']['name']);

            $filename_expl = explode(".", $filename);
            $endung = $filename_expl[count($filename_expl) - 1];

            $saveFilename = $id . "_" . cleanFilename($_FILES['input_file']['name']);

            if ($inserted === FALSE && $currentFilename != $saveFilename) {
                delete_files_filegallery($currentFilename);
            }

            $query = "UPDATE filegallery_line SET filename = '" . $saveFilename . "', extension = '" . strtolower($endung) . "' WHERE id =" . $id;
            mysqli_query($GLOBALS['mysql_con'], $query);
            move_uploaded_file($_FILES['input_file']['tmp_name'], PATH_ORIGINAL_FILEGALLERY . $saveFilename);
        }

        if ($inserted == TRUE) {
            $messages[] = '<div class="successbox">' . $translation->get("filegalleryline_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("filegalleryline_msg_success2") . '</div>';
        }

        if ($_REQUEST['save_and_close'] == 1) {
            edit_filegallery();
        } else {
            edit_line_filegallery($id, $messages);
        }
        return;
    }

    // das dropzone plugin zeigt dann mit echo ausgegebene error meldung
    if ($error === true && isset($_POST['dcDropzone'])) {
        headerFunctionBridge("HTTP/1.0 400 Bad Request");
        echo $translation->get("dropzone_upload_failed");
        exit();
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    $input_line = array();
    $input_line["id"] = $_REQUEST["input_line_id"];
    $input_line["header_id"] = $header_id;
    $input_line["description"] = $_REQUEST['input_name'];
    $input_line["filename"] = $_REQUEST['filename'];

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery_line_cardform.inc.php';
}

/**
 * moveup_pic() Bild nach oben
 *
 * @return
 */
function moveup_pic_filegallery()
{
    move_pic_filegallery("<", "DESC", $_POST["input_line_id"]);
}

/**
 * movedown_pic() Bild nach unten
 *
 * @return
 */
function movedown_pic_filegallery()
{
    move_pic_filegallery(">", "ASC", $_POST["input_line_id"]);
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
function move_pic_filegallery($way, $order, $pic_id)
{
    $query = "SELECT * FROM filegallery_line WHERE id = '" . $pic_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_pic = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM filegallery_line WHERE header_id = " . $curr_pic['header_id'] . " AND sorting " . $way . " " . $curr_pic["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_pic = @mysqli_fetch_array($result);
    }
    if (($curr_pic["id"] <> '') && ($change_pic["id"] <> '')) {
        $query = "UPDATE filegallery_line SET sorting = " . $change_pic["sorting"] . " WHERE id = " . $curr_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE filegallery_line SET sorting = " . $curr_pic["sorting"] . " WHERE id = " . $change_pic["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}