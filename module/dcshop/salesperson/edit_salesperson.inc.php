<?php
switch ($_GET["action_id"]) {
    case 'save':
        save_salesperson();
        break;
    default:
        edit_salesperson();
        break;
}

function edit_salesperson() {
    $query  = "SELECT * FROM shop_salesperson WHERE id = '" . $GLOBALS["shop_user"]["id"] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $input_shop_user               = @mysqli_fetch_array($result);
        $input_shop_user["password"]   = "nochange";
        $input_shop_user["password_2"] = "nochange";
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_salesperson_cardform.inc.php';
    }
}

function save_salesperson() {
    if ($_POST["input_password"] != 'nochange') {

        $password_options = get_password_options();
        $hashedPassword = password_hash(filter_input(INPUT_POST,'input_password'),PASSWORD_DEFAULT,$password_options);
        $query              = "UPDATE shop_salesperson SET password = '" . $hashedPassword . "' WHERE id = " . $GLOBALS["shop_user"]["id"] . " LIMIT 1";
        $remember_token_new = md5(uniqid(session_id(), TRUE) . md5($_POST["input_password"]) . $GLOBALS['visitor']['main_user_id']);
        $query2             = 'UPDATE main_visitor SET remember_token = \'' . $remember_token_new . '\' WHERE shop_salesperson_id=' . $GLOBALS['shop_user']['id'] . ' AND remember_token!=\'\' AND (remember_token IS NOT NULL)';
    } else {
        $query2 = '';
    }

    if ($_POST["input_password"] <> $_POST["input_password_2"]) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
        $error = TRUE;
    }
    if (strlen($_POST["input_password"]) < 6) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
        $error = TRUE;
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
        edit_salesperson();
    } else {
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password_2"] == "nochange")) {
            $input_shop_user["password"]   = "nochange";
            $input_shop_user["password_2"] = "nochange";
        } else {
            $input_shop_user["password"]   = "";
            $input_shop_user["password_2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_salesperson_cardform.inc.php';
    }
}

?>