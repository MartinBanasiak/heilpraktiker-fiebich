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

    if (isset($_POST["save_data"])) {

        $errormessage = "";
        $newPassword = false;
        $inputOldPassword = $_POST['input_old_password'];
        $inputPassword = $_POST['input_password'];
        $inputPasswordRepetition = $_POST['input_password_2'];
        $inputShipmentAddressID = (int)$_POST['input_shipment_address_id'];
        $shipmentAddressID = $inputShipmentAddressID > 0 ? $inputShipmentAddressID : 'NULL';

        if ($inputPassword === 'nochange' && $inputPasswordRepetition === 'nochange') {

            $query = "UPDATE shop_user SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "',
                                           login = '" . $_POST["input_login"] . "', shop_shipment_address_id = $shipmentAddressID
                      WHERE id = " . $GLOBALS["shop_user"]["id"] . "
                        AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
                        AND company ='" . $GLOBALS['shop']['company'] . "'
                      LIMIT 1";
        } else {
            $newPassword = true;
            $password_options = get_password_options();
            $hashedPassword = password_hash(filter_input(INPUT_POST, 'input_password'), PASSWORD_DEFAULT, $password_options);
            $query = "UPDATE shop_user
                      SET name = '" . $_POST["input_name"] . "', email = '" . $_POST["input_email"] . "', login = '" . $_POST["input_login"] . "',
                          password = '" . $hashedPassword . "', shop_shipment_address_id = $shipmentAddressID 
                      WHERE id = " . $GLOBALS["shop_user"]["id"] . "
                        AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
                        AND company ='" . $GLOBALS['shop']['company'] . "'
                      LIMIT 1";
        }
        if (($_POST["input_name"] == '') || ($_POST["input_email"] == '')) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_shop_user"] . "</div>\n";
            $error = TRUE;
            $errormessage .= $GLOBALS["tc"]["error_1_shop_user"];
        }

        if($newPassword)
        {
            $dbPasswordHash = $GLOBALS["shop_user"]['password'];
            $verifiedPasword = password_verify($inputOldPassword, $dbPasswordHash) ;
            $validOldHash = md5($inputOldPassword) === $dbPasswordHash;
            if ($verifiedPasword || $validOldHash) {
                $verifiedPasword = true;
            } else {
                $verifiedPasword = false;
            }
            if ( ($_POST["input_old_password"] == '') || !$verifiedPasword  ) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
                if(!$error)
                {
                    $errormessage =  $GLOBALS["tc"]["error_7_shop_user"];
                }

                $error = TRUE;

            }

            if ($_POST["input_password"] <> $_POST["input_password_2"]) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
                if(!$error)
                {
                    $errormessage = $GLOBALS["tc"]["error_2_shop_user"];
                }
                $error = TRUE;

            }

            if (strlen($_POST["input_password"]) < $GLOBALS['shop_setup']['password_strength_length']) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
                if(!$error)
                {
                    $errormessage =   str_replace("%", $GLOBALS['shop_setup']['password_strength_length'], $GLOBALS["tc"]["error_3_shop_user"]);
                }
                $error = TRUE;
            }

            if($_POST["input_score"] < 68 ) // strong Password
            {
                if(!$error)
                {
                    $errormessage = $GLOBALS["tc"]["weak_password"];
                }
                $error = TRUE;
            }

            if($verifiedPasword && ($_POST["input_password"] ==  $inputOldPassword) )
            {
                if(!$error)
                {
                    $errormessage = $GLOBALS["tc"]["error_8_shop_user"];
                }
                $error = TRUE;
            }
        }


        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT *
                                FROM shop_user
                                WHERE login = '" . $_POST["input_email"] . "'								
                                    AND company ='" . $GLOBALS['shop']['company'] . "'
                                    AND id != '" . $GLOBALS['shop_user']['id'] . "'");
        if (@mysqli_num_rows($result) > 0) {
            $temp_user = @mysqli_fetch_array($result);
            if ($temp_user["id"] <> $GLOBALS["shop_user"]["id"]) {
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
            get_requestbox($GLOBALS["tc"]["edit_curr_shop_user_success"], "", "success");
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
    } else {
        edit_shop_user();
    }
}

?>