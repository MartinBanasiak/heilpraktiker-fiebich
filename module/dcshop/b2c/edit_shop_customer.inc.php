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

    if (isset($_POST["save_data"])) {

        if ($_POST["input_customer_company"] != "") {
            $name_1 = $_POST["input_customer_company"];
            $name_2 = $_POST["input_customer_name"] . " " . $_POST["input_customer_lastname"];
        } else {
            $name_2 = $_POST["input_customer_company"];
            $name_1 = $_POST["input_customer_name"] . " " . $_POST["input_customer_lastname"];
        }
        $address_complete = $_POST["input_shop_customer_address_street"] . " " . $_POST["input_shop_customer_address_no"];



        $query = "UPDATE shop_customer
                  SET name = '" . $name_1 . "', name_2 = '" . $name_2 . "', address = '" . $address_complete . "', address_street = '" . $_POST["input_shop_customer_address_street"] . "', address_no = '" . $_POST["input_shop_customer_address_no"] . "', post_code = '" . $_POST["input_shop_customer_post_code"] . "',
                      city = '" . $_POST["input_shop_customer_city"] . "', country = '" . $_POST["input_country"] . "', phone_no = '" . $_POST["input_shop_customer_phone"] . "',
                      surname = '" . $_POST["input_customer_name"] . "', lastname = '" . $_POST["input_customer_lastname"] . "',  salutation = '" . $_POST["input_customer_salutation"] . "',company_name = '" . $_POST["input_customer_company"] . "'
                  WHERE id = " . $GLOBALS["shop_customer"]["id"] . "
                  LIMIT 1";
    //	if(($_POST["input_name"] == '') | ($_POST["input_login"] == '') | ($_POST["input_email"] == '')) {
    //		echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_shop_customer"] . "</div>\n";
    //		$error = true;
    //	}
    //	if($_POST["input_password"] <> $_POST["input_password_2"]) {
    //		echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_customer"] . "</div>\n";
    //		$error = true;
    //	}
    //	if(strlen($_POST["input_password"]) < 6) {
    //		echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_customer"] . "</div>\n";
    //		$error = true;
    //	}
        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
                                FROM shop_customer
                                WHERE login = '" . $_POST["input_login"] . "'
                                    AND shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'");
        if (@mysqli_num_rows($result) == 1) {
            $temp_user = @mysqli_fetch_array($result);
            if ($temp_user["id"] <> $GLOBALS["shop_customer"]["id"]) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_4_shop_customer"] . "</div>\n";
                $error = TRUE;
                $errormessage = $GLOBALS["tc"]["error_4_shop_customer"];
            }
        }
        if($error) {
            get_requestbox($errormessage,"");
        }
        if (!$error) {
            @mysqli_query($GLOBALS['mysql_con'], $query);
            get_requestbox($GLOBALS["tc"]["edit_shop_customer_success"],"","success");
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
    } else {
        edit_shop_customer();
    }
}

?>