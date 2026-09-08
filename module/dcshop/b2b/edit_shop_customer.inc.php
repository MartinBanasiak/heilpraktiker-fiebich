<?
switch ($_GET["action_id"]) {
    case 'save':
        save_shop_customer();
        break;
    default:
        edit_shop_customer();
        break;
}

function edit_shop_customer() {
    $query  = "SELECT *
			FROM shop_customer
			WHERE id = " . $GLOBALS["shop_customer"]["id"] . "
			LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_customer = @mysqli_fetch_array($result);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_customer_cardform.inc.php';
    }
}

function save_shop_customer() {
    if ($_POST["input_password"] == 'nochange') {
        $query = "UPDATE shop_customer
				  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "'
				  WHERE id = " . $GLOBALS["shop_customer"]["id"] . " AND shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
				  LIMIT 1";
    } else {
        $query = "UPDATE shop_customer
				  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
				  	  password = '" . md5($_POST["input_password"]) . "'
				  WHERE id = " . $GLOBALS["shop_customer"]["id"] . "
				  	AND shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
				  LIMIT 1";
    }
    if (($_POST["input_name"] == '') | ($_POST["input_login"] == '') | ($_POST["input_email"] == '')) {
        $errortext = $GLOBALS["tc"]["error_1_shop_customer"];
        $error = TRUE;
    }
    if ($_POST["input_password"] <> $_POST["input_password_2"]) {
        $errortext = $GLOBALS["tc"]["error_2_shop_customer"];
        $error = TRUE;
    }
    if (strlen($_POST["input_password"]) < 6) {
        $errortext = $GLOBALS["tc"]["error_3_shop_customer"];
        $error = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
							FROM shop_customer
							WHERE login = '" . $_POST["input_login"] . "'
								AND shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'");
    if (@mysqli_num_rows($result) == 1) {
        $temp_user = @mysqli_fetch_array($result);
        if ($temp_user["id"] <> $GLOBALS["shop_customer"]["id"]) {
            $errortext = $GLOBALS["tc"]["error_4_shop_customer"];
            $error = TRUE;
        }
    }
    if($error){
        get_requestbox($errortext);
    }
    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        get_requestbox($GLOBALS['tc']['data_saved'], "", "success");
        edit_shop_customer();
    } else {
        $input_shop_customer["id"]    = $_POST["input_id"];
        $input_shop_customer["name"]  = $_POST["input_name"];
        $input_shop_customer["email"] = $_POST["input_email"];
        $input_shop_customer["login"] = $_POST["input_login"];
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_customer["password"]   = "nochange";
            $input_shop_customer["password_2"] = "nochange";
        } else {
            $input_shop_customer["password"]   = "";
            $input_shop_customer["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_customer_cardform.inc.php';
    }
}

?>