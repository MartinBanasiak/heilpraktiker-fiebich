<?
switch ($_GET["action_id"]) {
    case 'edit':
        edit_shop_user();
        break;
    case 'delete':
        delete_shop_user();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_cardform.inc.php';
        break;
    case 'save':
        save_shop_user();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
        break;
}
function edit_shop_user() {
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT *
				FROM shop_user
				WHERE id = '" . $_POST["input_id"] . "'
					AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					AND company ='" . $GLOBALS['shop']['company'] . "'
				LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_shop_user               = @mysqli_fetch_array($result);
            $input_shop_user["password"]   = "nochange";
            $input_shop_user["password_2"] = "nochange";
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
    }
}

function delete_shop_user() {
    if ($_POST["input_id"] <> '') {
        if ($_POST["input_id"] <> $GLOBALS["shop_user"]["id"]) {
            $query = "DELETE FROM shop_user
					  WHERE id = " . $_POST["input_id"] . "
					  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					  	AND company ='" . $GLOBALS['shop']['company'] . "'
					  LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        } else {
            echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_5_shop_user"] . "</div>\n";
        }
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
}

function save_shop_user() {
    $result    = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
							FROM shop_user
							WHERE id = '" . $_POST["input_id"] . "'
								AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
								AND company ='" . $GLOBALS['shop']['company'] . "'
							LIMIT 1");
    $temp_user = @mysqli_fetch_array($result);
    if ($temp_user["main_user"]) {
        $_POST["input_right_user_management"] = "on";
        $_POST["input_right_order_history"]   = "on";
        $_POST["input_right_order"]           = "on";
    }
    $right_user_management = ($_POST["input_right_user_management"] == "on") ? 1 : 0;
    $right_order_history   = ($_POST["input_right_order_history"] == "on") ? 1 : 0;
    $right_order           = ($_POST["input_right_order"] == "on") ? 1 : 0;
    if ($_POST["input_id"] <> '') {
        if ($_POST["input_password"] == 'nochange') {
            $query = "UPDATE shop_user
					  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
					  	  right_user_management = " . $right_user_management . ", right_order_history = " . $right_order_history . ",
					  	  right_order = " . $right_order . ", shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
					  WHERE id = '" . $_POST["input_id"] . "'
					  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					  	AND company ='" . $GLOBALS['shop']['company'] . "'
					  LIMIT 1";
        } else {

            $password_options = get_password_options();
            $hashedPassword = password_hash(filter_input(INPUT_POST,'input_password'),PASSWORD_DEFAULT,$password_options);
            $query = "UPDATE shop_user
					  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
					  	  password = '" . $hashedPassword . "', right_user_management = " . $right_user_management . ",
					  	  right_order_history = " . $right_order_history . ", right_order = " . $right_order . ",
					  	  shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
					  WHERE id = '" . $_POST["input_id"] . "'
					  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					  	AND company ='" . $GLOBALS['shop']['company'] . "'
					  LIMIT 1";
        }
    } else {

        $password_options = get_password_options();
        $hashedPassword = password_hash(filter_input(INPUT_POST,'input_password'),PASSWORD_DEFAULT,$password_options);
        $query = "INSERT INTO shop_user (id,company,shop_code,customer_no,name,email,login,password,right_user_management,right_order_history,right_order,shop_shipment_address_id)
				  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS['shop']['code'] . "','" . $GLOBALS["shop_customer"]["customer_no"] . "','" . $_POST["input_name"] . "',
				  		  '" . $_POST["input_email"] . "','" . $_POST["input_login"] . "','" . $hashedPassword . "'," . $right_user_management . ",
				  		  " . $right_order_history . "," . $right_order . ", '" . $_POST["input_shipment_address_id"] . "')";
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
        if ($temp_user["id"] <> $_POST["input_id"]) {
            echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_4_shop_user"] . "</div>\n";
            $error = TRUE;
        }
    }
    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
    } else {
        $input_shop_user["id"]                    = $_POST["input_id"];
        $input_shop_user["name"]                  = $_POST["input_name"];
        $input_shop_user["email"]                 = $_POST["input_email"];
        $input_shop_user["login"]                 = $_POST["input_login"];
        $input_shop_user["right_user_management"] = $_POST["input_right_user_management"];
        $input_shop_user["right_order_history"]   = $_POST["input_right_order_history"];
        $input_shop_user["right_order"]           = $_POST["input_right_order"];
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_user["password"]   = "nochange";
            $input_shop_user["password_2"] = "nochange";
        } else {
            $input_shop_user["password"]   = "";
            $input_shop_user["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_cardform.inc.php';
    }
}

?>