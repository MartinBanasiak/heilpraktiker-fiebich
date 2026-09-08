<?
switch ($_GET["action"]) {
    case 'edit':
        edit_admin_user();
        break;
    case 'delete':
        delete_admin_user($admin_user["id"]);
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_cardform.inc.php';
        break;
    case 'save':
        save_admin_user();
        break;

    case 'open_card':
        open_card();

    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_listform.inc.php';
        break;
}

function open_card() {
    echo "<script type=\"text/javascript\">";

    echo "openCardOnStart = true;";

    echo "</script>";
}

function edit_admin_user( $_id = "", $messages = array() ) {
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_POST["input_id"];
    }

    if ($input_id <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = "  SELECT * FROM main_admin_user WHERE id = :id LIMIT 1 ";
        $params = [
            [':id', $input_id, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $input_admin_user             = $result[0];
            $input_admin_user["password"] = "nochange";
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_cardform.inc.php';
        }
    } else {
        $input_admin_user = array();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_cardform.inc.php';
    }
}

function delete_admin_user( $curr_user_id ) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    if ($_POST["input_id"] <> '') {
        if ($_POST["input_id"] <> $curr_user_id) {
            $query      = "DELETE FROM main_admin_user WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);

            $query = "DELETE FROM main_site_admin_user_link where main_admin_user_id = '" . $_POST["input_id"]."'";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        } else {
            headerFunctionBridge('EDIT_ERROR: 1');
            $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error5") . "</div>";
            edit_admin_user($_POST["input_id"], $messages);
        }
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_listform.inc.php';
}

function save_admin_user() {
    $messages    = array();
    $error       = FALSE;
    $input_id    = "";
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $passwordOptions = get_password_options();
    $hashedPassword = password_hash(filter_input(INPUT_POST,'input_password'),PASSWORD_DEFAULT,$passwordOptions);

    if ($_POST["input_right_create_user"] == 'on') {
        $right_create_user = 1;
    } else {
        $right_create_user = 0;
    }
    if ($_POST["input_id"] <> '') {
        $inserted = FALSE;
        $input_id = $_POST["input_id"];
        $newPassword = false;
        if ($_POST["input_old_password"] == '' && $_POST["input_password"] == '' && $_POST["input_password2"] == '') {
            $query = "UPDATE main_admin_user SET
				name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
				email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_email"]) . "', 
				login = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_login"]) . "', 
				right_create_user = " . $right_create_user . ",
				edit_mode = '" . (int)$_POST['input_edit_mode'] . "',
				main_site_id = '" . (int)$_POST['main_site_id'] . "',
				main_language = '" . $_POST['main_language'] . "'
				WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
        } else {
            $newPassword = true;
            $query = "UPDATE main_admin_user
				SET name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
				email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_email"]) . "', 
				login = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_login"]) . "', 
				password = '" . $hashedPassword . "', 
				right_create_user = " . $right_create_user . " ,
				edit_mode = '" . (int)$_POST['input_edit_mode'] . "',
				main_site_id = '" . (int)$_POST['main_site_id'] . "',
				main_language = '" . $_POST['main_language'] . "'
				WHERE id = '" . $_POST["input_id"] . "' LIMIT 1";
        }
    } else {
        $newPassword = true;
        $query    = "INSERT INTO main_admin_user (id,name,email,login,password,right_create_user, main_site_id, main_language,edit_mode) VALUES (
			NULL,
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_name"]) . "',
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_email"]) . "',
			'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_login"]) . "',
			'" . $hashedPassword . "',
			" . $right_create_user . ",
			'" . (int)$_POST['main_site_id'] . "',
			'" . $_POST['main_language'] . "',
			'" . (int)$_POST['edit_mode'] . "'
			)";
        $inserted = TRUE;
    }

    $siteQuery = "SELECT * from main_site";
    $result = @mysqli_query($GLOBALS['mysql_con'], $siteQuery);
    $siteAdminError = true;
    while ($siterow = @mysqli_fetch_assoc($result)) {
        $arr_ind = "input_site_admin_link_" . $siterow["id"];
        if(isset($_POST[$arr_ind]) && $_POST[$arr_ind] == "on") {
            $siteAdminError = false;
            break;
        }
    }

    if($siteAdminError === true) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("site_admin_user_error") . "</div>\n";
        $error      = TRUE;
    }

    if (($_POST["input_name"] == '') | ($_POST["input_login"] == '')) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error1") . "</div>\n";
        $error      = TRUE;
    }



    if($newPassword)
    {
        $inputOldPassword = filter_var($_POST['input_old_password'], FILTER_SANITIZE_STRING);
        $dbPasswordHash = $GLOBALS["admin_user"]['password'];
        $verifiedPasword = password_verify($inputOldPassword, $dbPasswordHash) ;
        $validOldHash = md5($inputOldPassword) === $dbPasswordHash;
        if ($verifiedPasword || $validOldHash) {
            $verifiedPasword = true;
        } else {
            $verifiedPasword = false;
        }
        $checkOldPassword = true;
        if(!isset($_POST['input_old_password'])) // there is no old password the admin adding new password for another user
        {
            $checkOldPassword = false;
        }

       if($checkOldPassword)
       {
           if ( ($_POST["input_old_password"] == '') || !$verifiedPasword  ) {
               if(!$error)
               {
                   $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error7") . "</div>\n";
               }

               $error = TRUE;
           }
       }

        if ($_POST["input_password"] <> $_POST["input_password2"]) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
            if(!$error)
            {
                $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error2") . "</div>\n";
            }
            $error = TRUE;

        }

        $passwordLength = (int)getenv('PASSWORD_STRENGTH_LENGTH');
        if($passwordLength < 1)
        {
            $passwordLength = 6;
        }

        if (strlen($_POST["input_password"]) < $passwordLength) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
            if(!$error)
            {
                $messages[] = "<div class=\"errorbox\">" .  str_replace("%", $passwordLength, $translation->get("user_error3"))  . "</div>\n";
            }
            $error = TRUE;

        }
        if($_POST["input_score"] < 68 ) // strong Password
        {
            if(!$error)
            {
                $messages[] = "<div class=\"errorbox\">" . $translation->get("weak_password") . "</div>\n";
            }
            $error = TRUE;
        }

        if($checkOldPassword)
        {
            if($verifiedPasword && ($_POST["input_password"] ==  $inputOldPassword) )
            {
                if(!$error)
                {
                    $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error8") . "</div>\n";
                }
                $error = TRUE;
            }
        }

    }

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = "  SELECT * FROM main_admin_user WHERE login = :login  ";
    $params = [
        [':login', $_POST["input_login"], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $result = $pdo->getResultArray();


    if (( count($result) > 0 || $_POST["input_id"] != '')  && ( $result[0]['id'] != $_POST["input_id"] && $result[0]['id'] <> null ) ) {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("user_error4") . "</div>\n";
        $error      = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($inserted == TRUE) {
            $input_id   = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("user_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("user_msg_success2") . '</div>';
        }

        $query = "DELETE FROM main_site_admin_user_link where main_admin_user_id = " . $input_id;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * from main_site";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $deleteAll = true;
        while ($siterow = @mysqli_fetch_assoc($result)) {
            $arr_ind = "input_site_admin_link_" . $siterow["id"];
            if(isset($_POST[$arr_ind]) && $_POST[$arr_ind] == "on") {
                $query = "INSERT INTO main_site_admin_user_link SET
                    main_site_id = " . $siterow["id"] . ",
                    main_admin_user_id = " . $input_id . ",
                    modified_date = '" . date('Y-m-d H:i:s',time()) . "',
                    modified_user = " . (int)$GLOBALS["admin_user"]['id'];
                @mysqli_query($GLOBALS['mysql_con'],$query);
            } else {
                $deleteAll = false;
            }
        }

        if($deleteAll === true) {
            $query = "DELETE FROM main_site_admin_user_link where main_admin_user_id = " . $input_id;
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        // Zuordnung Benutzer zu Shop
        $query = "DELETE FROM main_admin_user_shop_link where main_admin_user_id = '" . $input_id."'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT * from shop_shop";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $deleteAll = true;
        while ($siterow = @mysqli_fetch_assoc($result)) {
            $arr_ind = "input_shop_admin_link_" . $siterow["id"];
            if(isset($_POST[$arr_ind]) && $_POST[$arr_ind] == "on") {
                $active = 1;
            } else {
                $active = 0;
                $deleteAll = false;
            }
            $query = "INSERT INTO main_admin_user_shop_link SET
                shop_code = '" . $siterow["code"] . "',
                main_admin_user_id = " . $input_id . ",
                active = " . $active . ",
                modified_date = '" . date('Y-m-d H:i:s',time()) . "',
                modified_user = " . (int)$GLOBALS["admin_user"]['id'];
            @mysqli_query($GLOBALS['mysql_con'],$query);
        }

        if($deleteAll === true) {
            $query = "DELETE FROM main_admin_user_shop_link where main_admin_user_id = " . $input_id;
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        edit_admin_user($input_id, $messages);
    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $input_admin_user["id"]                = $_POST["input_id"];
        $input_admin_user["name"]              = $_POST["input_name"];
        $input_admin_user["email"]             = $_POST["input_email"];
        $input_admin_user["login"]             = $_POST["input_login"];
        $input_admin_user["right_create_user"] = $right_create_user;
        if (($_POST["input_password"] == "nochange") && ($_POST["input_password2"] == "nochange")) {
            $input_admin_user["password"]  = "nochange";
            $input_admin_user["password2"] = "nochange";
        } else {
            $input_admin_user["password"]  = "";
            $input_admin_user["password2"] = "";
        }
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_admin_user_cardform.inc.php';
    }
}

?>
