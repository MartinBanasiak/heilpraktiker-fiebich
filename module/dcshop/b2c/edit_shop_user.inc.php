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
function edit_shop_user()
{
    if ($_POST["input_id"] <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
				FROM shop_user
				WHERE id = :id
					AND customer_no = :customer_no
					AND company = :company
				LIMIT 1
        ";
        $params = [
            [':id', $_POST["input_id"], PDO::PARAM_STR],
            [':customer_no', $GLOBALS["shop_customer"]["customer_no"], PDO::PARAM_STR],
            [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $input_shop_user = $result[0];
            $input_shop_user["password"] = "nochange";
            $input_shop_user["password_2"] = "nochange";
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
    }


}

function delete_shop_user()
{
    if ($_POST["input_id"] <> '') {
        if ($_POST["input_id"] <> $GLOBALS["shop_user"]["id"]) {
            $query = "DELETE FROM shop_user
					  WHERE id = '" . $_POST["input_id"] . "'
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

function save_shop_user()
{
    $errormessage = "";
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
							FROM shop_user
							WHERE id = '" . $_POST["input_id"] . "'
								AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
								AND company ='" . $GLOBALS['shop']['company'] . "'
							LIMIT 1");
    $temp_user = @mysqli_fetch_array($result);
    if ($temp_user["main_user"]) {
        $_POST["input_right_user_management"] = "on";
        $_POST["input_right_order_history"] = "on";
        $_POST["input_right_order"] = "on";
    }
    $right_user_management = ($_POST["input_right_user_management"] == "on") ? 1 : 0;
    $right_order_history = ($_POST["input_right_order_history"] == "on") ? 1 : 0;
    $right_order = ($_POST["input_right_order"] == "on") ? 1 : 0;
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
            $query2 = '';
        } else {
            $password_options = get_password_options();
            $hashedPassword = password_hash(filter_input(INPUT_POST, 'input_password'), PASSWORD_DEFAULT, $password_options);

            $query = "UPDATE shop_user
					  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
					  	  password = '" . $hashedPassword . "', right_user_management = " . $right_user_management . ",
					  	  right_order_history = " . $right_order_history . ", right_order = " . $right_order . ",
					  	  shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
					  WHERE id = '" . $_POST["input_id"] . "'
					  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					  	AND company ='" . $GLOBALS['shop']['company'] . "'
					  LIMIT 1";
            $remember_token_new = md5(uniqid(session_id(), TRUE) . md5($_POST["input_password"]) . $GLOBALS['visitor']['main_user_id']);
            $query2 = 'UPDATE main_visitor SET remember_token = \'' . $remember_token_new . '\' WHERE main_user_id=' . $GLOBALS['shop_user']['id'] . ' AND remember_token!=\'\' AND (remember_token IS NOT NULL)';
        }
    } else {

        $password_options = get_password_options();
        $hashedPassword = password_hash(filter_input(INPUT_POST, 'input_password'), PASSWORD_DEFAULT, $password_options);
        $query = "INSERT INTO shop_user (id,company,customer_no,name,email,login,password,right_user_management,right_order_history,right_order,shop_shipment_address_id)
				  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS["shop_customer"]["customer_no"] . "','" . $_POST["input_name"] . "',
				  		  '" . $_POST["input_email"] . "','" . $_POST["input_login"] . "','" . $hashedPassword . "'," . $right_user_management . ",
				  		  " . $right_order_history . "," . $right_order . ", '" . $_POST["input_shipment_address_id"] . "')";
    }
    if (($_POST["input_name"] == '') | ($_POST["input_login"] == '') | ($_POST["input_email"] == '')) {
        //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_shop_user"] . "</div>\n";
        $errormessage .= $GLOBALS["tc"]["error_1_shop_user"];
        $error = TRUE;
    }
    if ($_POST["input_password"] <> $_POST["input_password_2"]) {
        //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
        $errormessage .= $GLOBALS["tc"]["error_2_shop_user"];
        $error = TRUE;
    }
    if (strlen($_POST["input_password"]) < 6) {
        //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
        $errormessage .= $GLOBALS["tc"]["error_3_shop_user"];
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
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_4_shop_user"] . "</div>\n";
            $errormessage .= $GLOBALS["tc"]["error_4_shop_user"];
            $error = TRUE;
        }
    }
    if ($error) {
        get_requestbox($errormessage, "");
    }
    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if (strlen($query2) > 0) {
            if (@mysqli_query($GLOBALS['mysql_con'], $query2)) {
                $cookie_timeout = COOKIE_DAYS_VALID * 24 * 60 * 60;
                //MB - Kann nur vor Ausgabe durchgef�hrt werden - Funktion auslagern?
                //setcookie($GLOBALS['site']['code'].'_remember_login', $remember_token_new, time()+$cookie_timeout,'/');
            }
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_listform.inc.php';
    } else {
        $input_shop_user["id"] = $_POST["input_id"];
        $input_shop_user["name"] = $_POST["input_name"];
        $input_shop_user["email"] = $_POST["input_email"];
        $input_shop_user["login"] = $_POST["input_login"];
        $input_shop_user["right_user_management"] = $_POST["input_right_user_management"];
        $input_shop_user["right_order_history"] = $_POST["input_right_order_history"];
        $input_shop_user["right_order"] = $_POST["input_right_order"];
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_user["password"] = "nochange";
            $input_shop_user["password_2"] = "nochange";
        } else {
            $input_shop_user["password"] = "";
            $input_shop_user["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_user_cardform.inc.php';
    }
}

?>