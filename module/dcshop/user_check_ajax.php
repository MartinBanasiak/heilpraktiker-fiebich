<?php
ini_set("display_errors",0);
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $token2 = json_decode(base64_decode($_POST["token_2"]));
    $timeout = 3600;

    if($token2->time < (time()-$timeout)){
        die();
    }

    if($token2->token_id !== $_SERVER['SERVER_NAME']){
        die();
    }
    $baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
    include($baseDirectory . '/vendor/autoload.php');
    //Load environment variables from config if exists
    $envDir = rtrim($baseDirectory, '/') . '/config';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }
    $secret = getenv('SHOP_PASSWORD');
    $valid = ($_POST["token_1"] === base64_encode(hash_hmac("sha256",json_encode($token2),$secret)));

    if(!$valid){
        die();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'shop_login' && ($_POST["input_customer_no"] <> '' || $_POST["input_login"] <> '' || $_POST['input_email'] != '') && ($_POST["input_password"] <> '')) {

        $isUser = checkB2bUserLogin();
        echo $isUser;
    }
    else
    {
        die();
    }

    return false;

} else {
    die();
}


function checkB2bUserLogin()
{

    $rootDir = dirname(dirname(__DIR__));
    $dcDir = $rootDir . DIRECTORY_SEPARATOR . 'dc';
    $dcCommonDir = $dcDir . DIRECTORY_SEPARATOR . 'common';

    include($dcCommonDir . DIRECTORY_SEPARATOR . "common_functions.inc.php");

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $input_password = filter_var($_POST['input_password'], FILTER_SANITIZE_STRING);
    $login_type = filter_var($_POST['login_type'], FILTER_SANITIZE_STRING);
    $shop_typ = filter_var($_POST['shop_typ'], FILTER_SANITIZE_STRING);

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $passwordHashingOptions = get_password_options();

    unset($login_snippet);
    switch ($login_type) {
        case 0:
            $login_snippet = ($shop_typ <> 2 ? " AND UPPER(shop_user.email) = UPPER(:email) " : " AND UPPER(email) = UPPER(:email) ");
            break;
        case 1:
            $login_snippet = ($shop_typ <> 2 ? " AND UPPER(shop_user.login) = UPPER(:login) " : " AND UPPER(salesperson_code) = UPPER(:login) ");
            break;
        case 2:
            $login_snippet = ($shop_typ <> 2 ? " AND shop_user.customer_no = :customer_no  AND UPPER(shop_user.login) = UPPER(:login) " : " AND 1=2 "); //Für Salesperson nicht zulässig
            break;
        case 3:
            $login_snippet = ($shop_typ <> 2 ? " AND shop_user.customer_no = :customer_no  AND UPPER(shop_user.email) = UPPER(:login) " : " AND 1=2 "); //Für Salesperson nicht zulässig
            break;
        case 4:
            $login_snippet = ($shop_typ <> 2 ? " AND shop_user.customer_no = :customer_no " : " AND 1=2 "); //Für Salesperson nicht zulässig
            break;
    }
    $prepStatement = "SELECT shop_user.*
          FROM shop_user
          INNER JOIN shop_customer ON shop_customer.company = shop_user.company AND shop_customer.customer_no = shop_user.customer_no
          WHERE 
            shop_user.company = :company
            AND shop_user.shop_code = :shopCode
            AND shop_customer.company = :company
            AND shop_customer.shop_code = :shopCode
            AND shop_customer.language_code = :langaugeCode
             " . $login_snippet . " 
            AND shop_customer.active = 1
          LIMIT 1";


    $params = [
        [':company', $_POST["company"], PDO::PARAM_STR],
        [':shopCode', $_POST['site'], PDO::PARAM_STR],
        [':langaugeCode', $_POST['language'], PDO::PARAM_STR],
        [':email', $_POST["input_email"], PDO::PARAM_STR],
        [':login', $_POST["input_login"], PDO::PARAM_STR],
        [':customer_no', $_POST["input_customer_no"], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resArr = $pdo->getResultArray();

    if (count($resArr) > 0) {
        $dbPasswordHash = $resArr[0]['password'];
        $rowID = $resArr[0]['id'];
        $inputPassword = $input_password;
        $validOldHash = md5($inputPassword) === $dbPasswordHash;
        if (password_verify($inputPassword, $dbPasswordHash) || $validOldHash) {
            if ($validOldHash || password_needs_rehash($dbPasswordHash, PASSWORD_DEFAULT, $passwordHashingOptions)) {
                $rehashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT, $passwordHashingOptions);
                mysqli_query($GLOBALS['mysql_con'], 'UPDATE shop_user SET password = \'' . $rehashedPassword . '\' WHERE id = ' . $rowID);
                $row = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], 'SELECT * FROM shop_user WHERE id = ' . $rowID));
            }
            return true;
        }
    }
    return false;
}