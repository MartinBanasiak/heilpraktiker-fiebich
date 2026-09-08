<?
switch ($_GET["action_id"]) {
    case 'save':
        save_shop_user();
        break;
    default:
        edit_shop_user();
        break;
}

function edit_shop_user()
{
    $query = "SELECT * FROM shop_user
			WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				AND company ='" . $GLOBALS['shop']['company'] . "'
			LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_shop_user = @mysqli_fetch_array($result);
        $input_shop_user["password"] = "nochange";
        $input_shop_user["password_2"] = "nochange";
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user_cardform.inc.php';
    }
}

function save_shop_user()
{
    $newPassword = false;
    if ($_POST["input_old_password"] == '' && $_POST["input_password"] == '' && $_POST["input_password_2"] == '') {
        $query = "UPDATE shop_user SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "',
									   login = '" . $_POST["input_login"] . "', shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
				  WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
    } else {
        $newPassword = true;
        $query = "UPDATE shop_user
				  SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
				  	  password = '" . md5($_POST["input_password"]) . "', shop_shipment_address_id = '" . $_POST["input_shipment_address_id"] . "'
				  WHERE id = " . $GLOBALS["shop_user"]["id"] . "
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
    }

    $errortext = "";

    if (($_POST["input_name"] == '') | ($_POST["input_login"] == '') | ($_POST["input_email"] == '')) {
        $errortext = $GLOBALS["tc"]["error_1_shop_user"];
        $error = TRUE;
    }

    if ($newPassword) {
        $inputOldPassword = filter_var($_POST['input_old_password'], FILTER_SANITIZE_STRING);
        $dbPasswordHash = $GLOBALS["shop_user"]['password'];
        $verifiedPasword = password_verify($inputOldPassword, $dbPasswordHash);
        $validOldHash = md5($inputOldPassword) === $dbPasswordHash;
        if ($verifiedPasword || $validOldHash) {
            $verifiedPasword = true;
        } else {
            $verifiedPasword = false;
        }
        if (($_POST["input_old_password"] == '') || !$verifiedPasword) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
            if (!$error) {
                $errortext = $GLOBALS["tc"]["error_7_shop_user"];
            }

            $error = TRUE;

        }

        if ($_POST["input_password"] <> $_POST["input_password_2"]) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
            if (!$error) {
                $errortext = $GLOBALS["tc"]["error_2_shop_user"];
            }
            $error = TRUE;

        }

        if (strlen($_POST["input_password"]) < $GLOBALS['shop_setup']['password_strength_length']) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
            if (!$error) {
                $errortext =   str_replace("%", $GLOBALS['shop_setup']['password_strength_length'], $GLOBALS["tc"]["error_3_shop_user"]);
            }
            $error = TRUE;

        }

        if($_POST["input_score"] < 68 ) // strong Password
        {
            if(!$error)
            {
                $errortext = $GLOBALS["tc"]["weak_password"];
            }
            $error = TRUE;
        }

        if ($verifiedPasword && ($_POST["input_password"] == $inputOldPassword)) {
            if (!$error) {
                $errortext = $GLOBALS["tc"]["error_8_shop_user"];
            }
            $error = TRUE;
        }
    }


    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
							FROM shop_user
							WHERE login = '" . $_POST["input_login"] . "'
								AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
								AND company ='" . $GLOBALS['shop']['company'] . "'");
    if (@mysqli_num_rows($result) == 1) {
        $temp_user = @mysqli_fetch_array($result);
        if ($temp_user["id"] <> $GLOBALS["shop_user"]["id"]) {
            $errortext = $GLOBALS["tc"]["error_4_shop_user"];
            $error = TRUE;
        }
    }
    if ($error) {
        get_requestbox($errortext);
    }

    if (!$error) {
        get_requestbox($GLOBALS["tc"]["edit_curr_shop_user_success"], "", "success");
        @mysqli_query($GLOBALS['mysql_con'], $query);
        edit_shop_user();
    } else {
        $input_shop_user["id"] = $_POST["input_id"];
        $input_shop_user["name"] = $_POST["input_name"];
        $input_shop_user["email"] = $_POST["input_email"];
        $input_shop_user["login"] = $_POST["input_login"];
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_user["password"] = "nochange";
            $input_shop_user["password_2"] = "nochange";
        } else {
            $input_shop_user["password"] = "";
            $input_shop_user["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_curr_shop_user_cardform.inc.php';
    }
}

?>