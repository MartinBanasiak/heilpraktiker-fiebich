<?
if ($_GET["sitepart"] <> '') {
    $_POST["input_navigation_has_sitepart_id"] = $_GET["sitepart"];
    $_GET["action"]                            = "edit_navigation_has_sitepart";
}
$query  = "SELECT * FROM main_navigation WHERE id = '" . $_GET["frontend_nav"] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $frontend_navigation = @mysqli_fetch_array($result);

    switch ($_GET["action"]) {
        case "new_navigation_has_sitepart":
            insert_navigation_has_sitepart($frontend_navigation);
            break;
        case "edit_navigation_has_sitepart":
            edit_navigation_has_sitepart($frontend_navigation);
            break;
        case "save_navigation_has_sitepart":
            save_navigation_has_sitepart($frontend_navigation);
            break;
        case "delete_navigation_has_sitepart":
            delete_navigation_has_sitepart($frontend_navigation);
            break;
        case "moveup_navigation_has_sitepart":
            moveup_navigation_has_sitepart();
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
            break;
        case "movedown_navigation_has_sitepart":
            movedown_navigation_has_sitepart();
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
            break;
        default:
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
            break;
    }
}

function edit_navigation_has_sitepart( $frontend_navigation ) {
    $query  = "SELECT *, main_navigation_has_sitepart.id AS navigation_has_sitepart_id FROM main_navigation_has_sitepart LEFT JOIN main_sitepart ON main_navigation_has_sitepart.main_sitepart_id = main_sitepart.id WHERE main_navigation_has_sitepart.id = '" . $_POST["input_navigation_has_sitepart_id"]."'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_navigation_has_sitepart = @mysqli_fetch_array($result);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_cardform.inc.php';
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
    }
}

function save_navigation_has_sitepart( $frontend_navigation ) {
    if ($_POST["input_navigation_has_sitepart_id"] == '') {
        $error = TRUE;
    }
    if (!$error) {
        $active = ($_POST["input_active"] == "on") ? 1 : 0;
        $query  = "UPDATE main_navigation_has_sitepart SET user_description = '" . $_POST["input_user_description"] . "', active = " . $active . ", modified_date = now(), value_1 = '" . $_POST["input_value_1"] . "', value_2 = '" . $_POST["input_value_2"] . "', value_3 = '" . $_POST["input_value_3"] . "', value_4 = '" . $_POST["input_value_4"] . "', value_5 = '" . $_POST["input_value_5"] . "', layout_area_id = '" . $_POST["input_layout_area_id"] . "' WHERE id = '" . $_POST["input_navigation_has_sitepart_id"] . "' LIMIT 1";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            $query_navigation  = "SELECT main_navigation_id FROM main_navigation_has_sitepart WHERE id = '" . $_POST["input_navigation_has_sitepart_id"] . "'";
            $result_navigation = @mysqli_query($GLOBALS['mysql_con'], $query_navigation);
            if ($result_navigation) {
                $val                     = @mysqli_fetch_array($result_navigation);
                $query_update_navigation = "UPDATE main_navigation SET modified_date = NOW(), modified_admin_user_id = " . $GLOBALS['admin_user']['id'] . " WHERE id = " . $val['main_navigation_id'] . "";
                @mysqli_query($GLOBALS['mysql_con'], $query_update_navigation);
            }
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
    } else {
        $query  = "SELECT *, main_navigation_has_sitepart.id AS navigation_has_sitepart_id FROM main_navigation_has_sitepart LEFT JOIN main_sitepart ON main_navigation_has_sitepart.main_sitepart_id = main_sitepart.id WHERE main_navigation_has_sitepart.id = '" . $_POST["input_navigation_has_sitepart_id"]."'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_navigation_has_sitepart                               = @mysqli_fetch_array($result);
            $input_navigation_has_sitepart["navigation_has_sitepart_id"] = $_POST["input_navigation_has_sitepart_id"];
            $input_navigation_has_sitepart["user_description"]           = $_POST["input_user_description"];
            $input_navigation_has_sitepart["active"]                     = $_POST["input_active"];
            for ($count = 1; $count <= 5; $count++) {
                $_POST["input_value_" . $count] = "";
            }
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_cardform.inc.php';
        }
    }
}

function insert_navigation_has_sitepart( $frontend_navigation ) {
    if (($frontend_navigation["id"] <> '') && ($_POST["input_sitepart_type"] <> '')) {
        $result  = mysqli_query($GLOBALS['mysql_con'], "SELECT MAX(sorting)+1 AS max FROM main_navigation_has_sitepart WHERE main_navigation_id = '" . $frontend_navigation["id"]."'");
        $sorting = @mysqli_fetch_array($result);
        if ($sorting["max"] == '') {
            $sorting["max"] = 1;
        }
        $query = "INSERT INTO main_navigation_has_sitepart (id,main_navigation_id,main_sitepart_id,sorting,modified_date,modified_user_id) VALUES (NULL," . $frontend_navigation["id"] . ",'" . $_POST["input_sitepart_type"] . "'," . $sorting["max"] . ",now()," . $GLOBALS['admin_user']['id'] . ")";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $_POST["input_navigation_has_sitepart_id"] = mysqli_insert_id($GLOBALS['mysql_con']);
        edit_navigation_has_sitepart($frontend_navigation);
    }
}

function delete_navigation_has_sitepart( $frontend_navigation ) {
    if ($_POST["input_navigation_has_sitepart_id"] <> '') {
        $query = "DELETE FROM main_navigation_has_sitepart WHERE id = '" . $_POST["input_navigation_has_sitepart_id"] . "' LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_navigation_has_sitepart_listform.inc.php';
    }
}

function moveup_navigation_has_sitepart() {
    move_navigation_has_sitepart("<", "DESC", $_POST["input_navigation_has_sitepart_id"]);
}

function movedown_navigation_has_sitepart() {
    move_navigation_has_sitepart(">", "ASC", $_POST["input_navigation_has_sitepart_id"]);
}

function move_navigation_has_sitepart( $way, $order, $sitepart_id ) {
    $query  = "SELECT * FROM main_navigation_has_sitepart WHERE id = '" . $sitepart_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_sitepart = @mysqli_fetch_array($result);
    }
    $query  = "SELECT * FROM main_navigation_has_sitepart WHERE main_navigation_id = " . $curr_sitepart["main_navigation_id"] . " AND sorting " . $way . " " . $curr_sitepart["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_sitepart = @mysqli_fetch_array($result);
    }
    if (($curr_sitepart["id"] <> '') && ($change_sitepart["id"] <> '')) {
        $query = "UPDATE main_navigation_has_sitepart SET sorting = " . $change_sitepart["sorting"] . " WHERE id = " . $curr_sitepart["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_navigation_has_sitepart SET sorting = " . $curr_sitepart["sorting"] . " WHERE id = " . $change_sitepart["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}


?>