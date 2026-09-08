<?
$messages = array();
$error    = FALSE;

switch ($_REQUEST["action"]) {
    case 'edit':
        edit_layout();
        break;
    case 'delete':
        delete_layout();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
        break;
    case 'save':
        save_layout();
        break;

    case 'edit_area':
        edit_layout_area();
        break;
    case 'delete_area':
        delete_layout_area();
        break;
    case 'new_area':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_area_cardform.inc.php';
        break;
    case 'save_area':
        save_layout_area();
        break;
    case 'moveup_area':
        moveup_area();
        edit_layout();
        break;
    case 'movedown_area':
        movedown_area();
        edit_layout();
        break;

    case 'edit_class':
        edit_layout_class();
        break;
    case 'delete_class':
        delete_layout_class();
        break;
    case 'new_class':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_class_cardform.inc.php';
        break;
    case 'save_class':
        save_layout_class();
        break;
    case 'moveup_class':
        moveup_class();
        edit_layout();
        break;
    case 'movedown_class':
        movedown_class();
        edit_layout();
        break;

    case 'moveup_inclusion':
        moveup_inclusion();
        edit_layout();
        break;
    case 'movedown_inclusion':
        movedown_inclusion();
        edit_layout();
        break;
    case 'delete_inclusion':
        delete_inclusion();
        edit_layout();
        break;
    case 'edit_inclusion':
        edit_inclusion();
        break;
    case 'new_inclusion':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_inclusions_cardform.inc.php';
        break;
    case 'save_inclusion':
        save_inclusion($sitepart);
        break;

    case 'update_sortorder_area':
        update_sortorder_area();
        break;
    case 'update_sortorder_class':
        update_sortorder_class();
        break;
    case 'update_sortorder_inclusion':
        update_sortorder_inclusion();
        break;

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_listform.inc.php';
        break;
}

function update_sortorder_area() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE main_layout_area SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}


function update_sortorder_inclusion() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE main_layout_inclusions SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}


function update_sortorder_class() {
    $sorting = 1;
    foreach ($_POST['linklistid'] as $itemId) {
        $query = "UPDATE main_layout_class SET sorting = " . $sorting . " WHERE id = '" . $itemId."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $sorting++;
    }
}


function moveup_inclusion() {
    move_inclusion("<", "DESC", $_REQUEST["input_inclusion_id"]);
}

function movedown_inclusion() {
    move_inclusion(">", "ASC", $_REQUEST["input_inclusion_id"]);
}

function move_inclusion( $way, $order, $inclusion_id ) {

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = "  SELECT * FROM main_layout_inclusions WHERE id = :id LIMIT 1
        ";
    $params = [
        [':id', $inclusion_id, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $result = $pdo->getResultArray();

    if (count($result) == 1) {
        $curr_inclusion = $result[0];
    }
    $query  = "SELECT * FROM main_layout_inclusions WHERE main_layout_id = '" . $curr_inclusion["main_layout_id"] . "' AND sorting " . $way . " " . $curr_inclusion["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_inclusion = @mysqli_fetch_array($result);
    }
    if (($curr_inclusion["id"] <> '') && ($change_inclusion["id"] <> '')) {
        $query = "UPDATE main_layout_inclusions SET sorting = " . $change_inclusion["sorting"] . " WHERE id = " . $curr_inclusion["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_layout_inclusions SET sorting = " . $curr_inclusion["sorting"] . " WHERE id = " . $change_inclusion["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function edit_inclusion( $_id = "" ) {
    if ((int)$_id > 0) {
        $incusion_id = $_id;
    } else {
        $incusion_id = $_REQUEST["input_inclusion_id"];
    }

    if ($incusion_id <> '') {
        $query  = "SELECT * FROM main_layout_inclusions WHERE id = '" . $incusion_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_layout_inclusion = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_inclusions_cardform.inc.php';
        }
    } else {
        $main_layout_id = $_REQUEST["input_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
    }
}

function delete_inclusion() {
    $id = $_REQUEST["input_inclusion_id"];
    //Delete from relation-table
    $query = "DELETE FROM main_navigation_layout_inclusions
			WHERE main_layout_inclusions_id = '" . $id."'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    //Delete from layout_inclusions table
    $query = "DELETE FROM main_layout_inclusions
			WHERE id = '" . $id . "' LIMIT 1";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}


function save_inclusion( $sitepart ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $id             = $_REQUEST["input_inclusion_id"];
    $main_layout_id = $_REQUEST["input_id"];
    IF ($main_layout_id == NULL) {
        $main_layout_id = $GLOBALS['language']['main_layout_id'];
    }
    $type           = $_REQUEST["input_inclusion_type"];
    $path           = $_REQUEST["input_inclusion_path"];
    $default_active = $_REQUEST["input_inclusion_default_active"];
    IF ($id <> '') {
        $query = "UPDATE main_layout_inclusions SET
		main_layout_id = '" . $main_layout_id . "',
		type = '" . $type . "',
		path = '" . $path . "',
		default_active = '" . $default_active . "'
		WHERE id = '" . $id . "'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

    } ELSE {
        $sql_1   = "SELECT MAX(sorting) AS 'sorting' FROM main_layout_inclusions WHERE main_layout_id = '" . $main_layout_id."'";
        $query_1 = mysqli_query($GLOBALS['mysql_con'], $sql_1);
        IF (mysqli_num_rows($query_1) == 0) {
            $sorting = 1;
        } ELSE {
            $sorting = mysqli_result($query_1, 0) + 1;
        }
        $sql_2 = "
		INSERT INTO main_layout_inclusions
		(main_layout_id,
		type,
		path,
		default_active,
		sorting)
		VALUES
		('" . $main_layout_id . "','" .
            $type . "','" .
            $path . "','" .
            $default_active . "','" .
            $sorting . "')";
        @mysqli_query($GLOBALS['mysql_con'], $sql_2);

        $id = mysqli_insert_id($GLOBALS['mysql_con']);
    }


    $query  = "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_inclusion_id = $_REQUEST["input_id"];
        $input_layout       = @mysqli_fetch_array($result);
    }

    if ($_REQUEST['save_and_close'] == 1) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
    } else {
        edit_inclusion($id);
    }

}


function edit_layout() {
    if ($_REQUEST["input_id"] <> '') {
        $query  = "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            /*
            $input_layout = @mysqli_fetch_array($result);
            $input_layout["css_include"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["css_include"]);
            $input_layout["css_include_2"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["css_include_2"]);
            $input_layout["css_include_3"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["css_include_3"]);
            $input_layout["css_include_4"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["css_include_4"]);
            $input_layout["css_include_5"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["css_include_5"]);
            $input_layout["fck_style_include"] = str_replace("layout/frontend/" . $input_layout["code"] . "/", "", $input_layout["fck_style_include"]);
            $input_layout["fck_layout_include"] = str_replace("layout/frontend/" . $input_layout["code"] . "/css/", "", $input_layout["fck_css_include"]);
            $input_layout["favicon_include"] = str_replace("layout/frontend/" . $input_layout["code"] . "/", "", $input_layout["favicon_include"]);
            */
            $input_inclusion_id = $_REQUEST["input_id"];
            $input_layout       = @mysqli_fetch_array($result);
            //$input_layout["frontend_include"] = str_replace("dc/frontend/", "", $input_layout["frontend_include"]);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_listform.inc.php';
    }
}

function delete_layout() {
    if ($_REQUEST["input_id"] <> '') {
        $query = "DELETE FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_listform.inc.php';
}

function save_layout() {
    $translation             = \DynCom\dc\common\classes\Registry::get("translation");
    $fck_style_include_path  = $_REQUEST["input_fck_style_include"];
    $fck_layout_include_path = $_REQUEST["input_fck_layout_include"];
    $favicon_include_path    = $_REQUEST["input_favicon_include"];
    //$frontend_include_path = "dc/frontend/" . $_REQUEST["input_frontend_include"];
    $frontend_include_path = $_REQUEST["input_frontend_include"];
    $inserted              = FALSE;
    if ($_REQUEST["input_id"] <> '') {
        $query = "UPDATE main_layout SET code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "', name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "', fck_style_include = '" . $fck_style_include_path . "', fck_css_include = '" . $fck_layout_include_path . "', favicon_include = '" . $favicon_include_path . "', frontend_include = '" . $frontend_include_path . "' WHERE id = '" . $_REQUEST["input_id"] . "' LIMIT 1";
    } else {
        $query    = "INSERT INTO main_layout (code, name, fck_style_include, fck_css_include, favicon_include, frontend_include) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "','" . $fck_style_include_path . "','" . $fck_layout_include_path . "','" . $favicon_include_path . "', '" . $frontend_include_path . "')";
        $inserted = TRUE;
    }
    if (($_REQUEST["input_name"] == '') | ($_REQUEST["input_code"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_name") . "</div>\n";
        $error      = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_layout WHERE code = '" . $_REQUEST["input_code"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }

    $input_layout       = array();
    $input_layout['id'] = $_REQUEST["input_id"];

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted === TRUE) {
            $last_insert_id     = mysqli_insert_id($GLOBALS['mysql_con']);
            $input_layout['id'] = $last_insert_id;
        }
    }

    $input_layout["code"]              = $_REQUEST["input_code"];
    $input_layout["name"]              = $_REQUEST["input_name"];
    $input_layout["frontend_include"]  = $_REQUEST["input_frontend_include"];
    $input_layout["fck_style_include"] = $_REQUEST["input_fck_style_include"];
    $input_layout["fck_css_include"]   = $_REQUEST["input_fck_layout_include"];
    $input_layout["favicon_include"]   = $_REQUEST["input_favicon_include"];

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';

}

function edit_layout_area( $_id = "" ) {
    if ((int)$_id > 0) {
        $area_id = $_id;
    } else {
        $area_id = $_REQUEST["input_area_id"];
    }
    if ($area_id <> '') {
        $query  = "SELECT * FROM main_layout_area WHERE id = '" . $area_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_layout_area = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_area_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
    }
}

function delete_layout_area() {
    $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
    if ($_REQUEST["input_area_id"] <> '') {
        $query = "DELETE FROM main_layout_area WHERE id = '" . $_REQUEST["input_area_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
}

function save_layout_area() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $messages = array();
    $id       = $_REQUEST["input_area_id"];
    if ($_REQUEST["input_area_id"] <> '') {
        $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
        $query        = "UPDATE main_layout_area SET code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_area_code"]) . "', name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_area_name"]) . "' WHERE id = '" . $_REQUEST["input_area_id"] . "' LIMIT 1";
        $inserted     = FALSE;
    } else {
        $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
        $newsort      = mysqli_result(mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting) FROM main_layout_area WHERE main_layout_id = '" . $_REQUEST["input_id"]."'"), 0) + 1;
        //$messages[] = $newsort;
        $query    = "INSERT INTO main_layout_area (code, name, main_layout_id, sorting) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_area_code"]) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_area_name"]) . "', '" . $input_layout["id"] . "','" . $newsort . "')";
        $inserted = TRUE;
    }
    if (($_REQUEST["input_area_name"] == '') | ($_REQUEST["input_area_code"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_name") . "</div>\n";
        $error      = TRUE;
    }

    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_layout_area WHERE main_layout_id = '" . (int)$_REQUEST["input_id"] . "' AND code = '" . $_REQUEST["input_area_code"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_area_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }


    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($_REQUEST['save_and_close'] == 1) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
        } else {
            if ($inserted === TRUE) {
                $id = mysqli_insert_id($GLOBALS['mysql_con']);
            }
            edit_layout_area($id);
        }

    } else {
        $input_layout_area["id"]   = $_REQUEST["input_area_id"];
        $input_layout_area["name"] = $_REQUEST["input_area_name"];
        $input_layout_area["code"] = $_REQUEST["input_area_code"];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_area_cardform.inc.php';
    }
}

function move_layout_area( $way, $order, $layout_area_id ) {
    $query  = "SELECT * FROM main_layout_area WHERE id = '" . $layout_area_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_area = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM main_layout_area WHERE main_layout_id = '" . $curr_area["main_layout_id"] . "' AND sorting " . $way . " " . $curr_area["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_area = @mysqli_fetch_array($result);
    }
    if (($curr_area["id"] <> '') && ($change_area["id"] <> '')) {
        $query = "UPDATE main_layout_area SET sorting = " . $change_area["sorting"] . " WHERE id = " . $curr_area["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_layout_area SET sorting = " . $curr_area["sorting"] . " WHERE id = " . $change_area["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function moveup_area() {
    move_layout_area("<", "DESC", $_REQUEST["input_area_id"]);
}

function movedown_area() {
    move_layout_area(">", "ASC", $_REQUEST["input_area_id"]);
}

/*************************************************************
 * Funktionen fuer layut klassen
 * ***********************************************************
 */
function edit_layout_class( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $area_id = $_id;
    } else {
        $area_id = $_REQUEST["input_class_id"];
    }
    if ($area_id <> '') {
        $query  = "SELECT * FROM main_layout_class WHERE id = '" . $area_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_layout_class = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_class_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
    }
}

function delete_layout_class() {
    $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
    if ($_REQUEST["input_class_id"] <> '') {
        $query = "DELETE FROM main_layout_class WHERE id = '" . $_REQUEST["input_class_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE FROM main_page_link_layout_class_link WHERE main_layout_class_id = " . $_REQUEST["input_class_id"] . " LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
}

function save_layout_class() {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $messages = array();
    $id       = $_REQUEST["input_class_id"];
    if ($_REQUEST["input_class_id"] <> '') {
        $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
        $query        = "UPDATE main_layout_class SET code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_class_code"]) . "', name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_class_name"]) . "' WHERE id = '" . $_REQUEST["input_class_id"] . "' LIMIT 1";
        $inserted     = FALSE;
    } else {
        $input_layout = mysqli_fetch_array(mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_layout WHERE id = '" . $_REQUEST["input_id"] . "'"));
        $newsort      = mysqli_result(mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting) FROM main_layout_class WHERE main_layout_id = '" . $_REQUEST["input_id"]."'"), 0) + 1;
        //$messages[] = $newsort;
        $query    = "INSERT INTO main_layout_class (code, name, main_layout_id, sorting) VALUES ('" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_class_code"]) . "','" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_class_name"]) . "', '" . $input_layout["id"] . "','" . $newsort . "')";
        $inserted = TRUE;
    }
    if (($_REQUEST["input_class_name"] == '') | ($_REQUEST["input_class_code"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_name") . "</div>\n";
        $error      = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_layout_class WHERE main_layout_id = '" . (int)$_REQUEST["input_id"] . "' AND code = '" . $_REQUEST["input_class_code"] . "'");
    if ((@mysqli_num_rows($result) <> 0) && ($_REQUEST["input_class_id"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
        $error      = TRUE;
    }

    // benoetigt fuer ajax calls
    if ($error === TRUE) {
        headerFunctionBridge('EDIT_ERROR: 1');
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($_REQUEST['save_and_close'] == 1) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_cardform.inc.php';
        } else {
            if ($inserted === TRUE) {
                $id         = mysqli_insert_id($GLOBALS['mysql_con']);
                $messages[] = '<div class="successbox">' . $translation->get("layout_class_success1") . '</div>';
            } else {
                $messages[] = '<div class="successbox">' . $translation->get("layout_class_success2") . '</div>';
            }

            edit_layout_class($id, $messages);
        }

    } else {
        $input_layout_class["id"]   = $_REQUEST["input_class_id"];
        $input_layout_class["name"] = $_REQUEST["input_class_name"];
        $input_layout_class["code"] = $_REQUEST["input_class_code"];

        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_class_cardform.inc.php';
    }
}

function move_layout_class( $way, $order, $layout_class_id ) {
    $query  = "SELECT * FROM main_layout_class WHERE id = '" . $layout_class_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_class = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM main_layout_class WHERE main_layout_id = '" . $curr_class["main_layout_id"] . "' AND sorting " . $way . " " . $curr_class["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_class = @mysqli_fetch_array($result);
    }
    if (($curr_class["id"] <> '') && ($change_class["id"] <> '')) {
        $query = "UPDATE main_layout_class SET sorting = " . $change_class["sorting"] . " WHERE id = " . $curr_class["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_layout_class SET sorting = " . $curr_class["sorting"] . " WHERE id = " . $change_class["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function moveup_class() {
    move_layout_class("<", "DESC", $_REQUEST["input_class_id"]);
}

function movedown_class() {
    move_layout_class(">", "ASC", $_REQUEST["input_class_id"]);
}