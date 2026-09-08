<?
if (isset($_GET['id']) || isset($_POST['input_key'])) {
    $_GET = secure_array($_GET);
    $_POST = secure_array($_POST);
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);
    $hashString = $_GET['id'];
    $doHashUpdate = true;
    if( isset($_POST['input_key']) && $_POST['input_key'] != "" )
    {
        $hashString = $_POST['input_key'];
        $doHashUpdate = false;
    }
    else
    {
        $_SESSION['reset_password'] = false;
    }

    $prepStatement = " 
            
            SELECT 
                *
            FROM
                user_new_password_request
            WHERE 
                random_hash = :randomHash
            ORDER by id DESC   
                    ";
    $params = [
        [':randomHash', $hashString, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $hash = $pdo->getResultArray();

    if (count($hash) != 1) {

        echo $GLOBALS['tc']['invalid_link'];
        die();
    } else {


        $email = $hash[0]['user_email'];
        $company = $hash[0]['company'];
        $shopCode = $hash[0]['shop_code'];
        $createDate = $hash[0]['create_date'];

        if($doHashUpdate)
        {

            $hashString = $shopCode. $email. $company . rand() . getenv('SHOP_PASSWORD') . '@!#$%^&*' . time();
            $hashString = hash('sha256', md5(md5($hashString)));

            $prepStatement = " Update user_new_password_request set random_hash = :randomHash 
                    WHERE id = :id
                        ";
            $params = [
                [':randomHash', $hashString, PDO::PARAM_STR],
                [':id', $hash[0]['id'], PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
        }





        $now = new \DateTime();
        $createDateTime = new \DateTime($createDate); // "2012-07-18 21:11:12" for example
        $diff = $now->diff($createDateTime);

        $minutes = ($diff->format('%a') * 1440) + // total days converted to minutes
            ($diff->format('%h') * 60) +   // hours converted to minutes
            $diff->format('%i');

        if ($minutes > 30) {
            echo $GLOBALS['tc']['invalid_link'];
            die();
        }

        if ($_SESSION['reset_password'] == true) {
            $_POST = secure_array($_POST);
            $password_options = get_password_options();
            $hashedPassword = password_hash(filter_input(INPUT_POST, 'input_password'), PASSWORD_DEFAULT, $password_options);

            if ($_POST["input_password"] <> $_POST["input_password2"]) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_2_shop_user"] . "</div>\n";
                if (!$error) {
                    $errormessage = $GLOBALS["tc"]["error_2_shop_user"];
                }
                $error = TRUE;

            }

            if (strlen($_POST["input_password"]) < $GLOBALS['shop_setup']['password_strength_length'] ) {
                //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_3_shop_user"] . "</div>\n";
                if (!$error) {
                    $errormessage = $GLOBALS["tc"]["error_3_shop_user"];
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


            if ($error) {
                get_requestbox($errormessage, "");
            } else {



                $prepStatement = " Delete from `user_new_password_request`
                   WHERE random_hash = :randomHash 

                        ";
                $params = [
                    [':randomHash', $hashString, PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();


                $prepStatement = " Update shop_user set password = :password 
                    WHERE email = :email and company=:company and shop_code =:shop_code
                        ";
                $params = [
                    [':password', $hashedPassword, PDO::PARAM_STR],
                    [':company', $company, PDO::PARAM_STR],
                    [':shop_code', $shopCode, PDO::PARAM_STR],
                    [':email', $email, PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();

                $_SESSION['reset_password'] = false;

                echo '<h1 class="success">'.$GLOBALS['tc']['password_success_title'].'<h1>';
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
                $homePage = $protocol.$_SERVER['SERVER_NAME'].'/'.customizeUrl().'/';
                echo "<script type='text/javascript'>  window.location='".$homePage."'; </script>";
                die();

            }
        }

        $_SESSION['reset_password'] = true;
    }


} else {
    $_SESSION['reset_password'] = false;
    echo $GLOBALS['tc']['invalid_link'];
    die();
}


?>
<? $formname = "form_password_reminder"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="<?= ml($sitepart, "send", "TRUE") ?>">
    <? input_shop($GLOBALS["tc"]["password"], "input_password", "password", "", 50) ?>
    <? input_shop($GLOBALS["tc"]["password_2"], "input_password2", "password", "", 50) ?>
    <? input_shop($GLOBALS["tc"]["email"], "input_email", "hidden", $email, 50) ?>
    <? input("", "input_score", "hidden", 0, 45) ?>
    <? input_shop("", "input_key", "hidden", $hashString, 255) ?>
    <br/>
    <input type="submit" name="button" class="password_button button" id="button"
           value="<?= $GLOBALS["tc"]["save"] ?>">
</form>
<link rel="stylesheet" type="text/css" href="<?= $GLOBALS['projectRoot'] ?>/components/password-strength-meter/dist/password.min.css">
<script type="text/javascript">

    jQuery(document).ready(function($) {
        var  options = {
            shortPass: '<?= $GLOBALS['tc']['short_password'] ?> ',
            badPass: '<?= $GLOBALS['tc']['weak_password'] ?> ',
            goodPass:  '<?= $GLOBALS['tc']['good_password'] ?> ',
            strongPass:  '<?= $GLOBALS['tc']['strong_password'] ?> ',
            containsUsername: '',
            enterPass: '',
            showPercent: false,
            showText: false, // shows the text tips
            animate: true, // whether or not to animate the progress bar on input blur/focus
            animateSpeed: 'fast', // the above animation speed
            username: false, // select the username field (selector or jQuery instance) for better password checks
            usernamePartialMatch: true, // whether to check for username partials
            minimumLength: '<?= $GLOBALS['shop_setup']['password_strength_length'] ?>' // minimum password length (below this threshold, the score is 0)
        };


        $('#input_password').password(options).bind('password.score', function (e, score) {
            $('#input_score').val(score);
        });

    });


</script>