<?
switch ($_GET["action_id"]) {
    case 'save':
        save_shop_user();
        break;
    default:
        edit_shop_user();
        break;
}

function edit_shop_user() {
    $query  = "SELECT * FROM shop_user
			WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				AND company ='" . $GLOBALS['shop']['company'] . "'
			LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_shop_user               = @mysqli_fetch_array($result);
        $input_shop_user["password"]   = "nochange";
        $input_shop_user["password_2"] = "nochange";
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user_cardform.inc.php';
    }
}

function save_shop_user() {
    if ($_POST["input_password"] == 'nochange') {
        $query = "UPDATE shop_user SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "',
									   login = '" . $_POST["input_login"] . "', shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
				  WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
    } else {
        $query = "UPDATE shop_user
				  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
				  	  password = '" . md5($_POST["input_password"]) . "', shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
				  WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
    }
    if (($_POST["input_name"] == '') | ($_POST["input_login"] == '') | ($_POST["input_email"] == '')) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_shop_user"] . "</div>\n";
        $error = TRUE;
    }
    if ($_POST["input_password"] <> $_POST["input_password_2"]) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
        $error = TRUE;
    }
    if (strlen($_POST["input_password"]) < 6) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
        $error = TRUE;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
							FROM shop_user
							WHERE login = '" . $_POST["input_login"] . "'
								AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
								AND company ='" . $GLOBALS['shop']['company'] . "'");
    if (@mysqli_num_rows($result) == 1) {
        $temp_user = @mysqli_fetch_array($result);
        if ($temp_user["id"] <> $GLOBALS["shop_user"]["id"]) {
            echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_4_shop_user"] . "</div>\n";
            $error = TRUE;
        }
    }
    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        edit_shop_user();
    } else {
        $input_shop_user["id"]    = $_POST["input_id"];
        $input_shop_user["name"]  = $_POST["input_name"];
        $input_shop_user["email"] = $_POST["input_email"];
        $input_shop_user["login"] = $_POST["input_login"];
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_user["password"]   = "nochange";
            $input_shop_user["password_2"] = "nochange";
        } else {
            $input_shop_user["password"]   = "";
            $input_shop_user["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user_cardform.inc.php';
    }
}

?>