<?php
$baseDirectory = rtrim(dirname(dirname(dirname(__DIR__))), '/');
include($baseDirectory . '/vendor/autoload.php');

$envDir = rtrim(dirname(dirname(dirname(__DIR__))), '/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/shop_functions.inc.php';

$logDir = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
$logFile = 'PaymentNotify.log';
$logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
$logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new \Monolog\Processor\PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new \Monolog\Logger('PaymentNotify', [$logFileHandler]);
$logger->info('Page Name: order_Success');

$pdoHost = getenv('MAIN_MYSQL_DB_HOST');
$pdoPort = getenv('MAIN_MYSQL_DB_PORT');
$pdoUser = getenv('MAIN_MYSQL_DB_USER');
$pdoPass = getenv('MAIN_MYSQL_DB_PASS');
$pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

$pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

$shopdata = filter_var($_REQUEST["shopdata"], FILTER_SANITIZE_STRING);

$shopdata = explode('|', $shopdata);
$company = $shopdata[0];
$shopCode = $shopdata[1];
$shopLanguageCode = $shopdata[2];

if (strpos($shopLanguageCode, '?') !== false) {
    $shopLanguageCode = explode('?', $shopLanguageCode);
    $shopLanguageCode = $shopLanguageCode[0];
}
$prepStatement = "SELECT * FROM shop_shop where company = :company AND code = :shop_code LIMIT 1";

$params = [
    [':shop_code', $shopCode, PDO::PARAM_STR],
    [':company', $company, PDO::PARAM_STR],
];
$pdo->setQuery($prepStatement);
$pdo->prepareQuery();
$pdo->bindParameters($params);
$pdo->executePreparedStatement();
$resultArray = $pdo->getResultArray();


$shop = $resultArray[0];
/*
$prepStatement2 = "SELECT * FROM shop_sales_header where payment_transaction_id = :payment_transaction_id LIMIT 1";

$params2 = [
    [':payment_transaction_id', $_GET['TransID'], PDO::PARAM_STR],
];
$pdo->setQuery($prepStatement2);
$pdo->prepareQuery();
$pdo->bindParameters($params2);
$pdo->executePreparedStatement();
$resultArray2 = $pdo->getResultArray();

$salesHeader = $resultArray2[0];*/


session_id($_GET['UserData']);
session_start();

$prepStatement3 = "SELECT *
		  FROM shop_payment_option
		  WHERE line_no= :line_no
		  AND shop_code= :shop_code
		  AND language_code = :language_code
          AND company = :company LIMIT 1";

$params3 = [
    [':shop_code', $shop['code'], PDO::PARAM_STR],
    [':company', $shop['company'], PDO::PARAM_STR],
    [':language_code', $shopLanguageCode, PDO::PARAM_STR],
    [':line_no', $_SESSION['payment_line_no'], PDO::PARAM_INT],
];
$pdo->setQuery($prepStatement3);
$pdo->prepareQuery();
$pdo->bindParameters($params3);
$pdo->executePreparedStatement();
$resultArray3 = $pdo->getResultArray();
$paymentOption = $resultArray3[0];

$logger->info('Paygate Response  Trans ID: ' . $_REQUEST['TransID']);
$logger->info('Paygate  Response Pay ID: ' . $_REQUEST['PayID']);
$logger->info('Paygate  Response Status: ' . $_REQUEST['Status']);


if (isset($_GET['Status']) && $_GET['Status'] == 'AUTHORIZE_REQUEST' && ($paymentOption["checkout"] == 20 || $paymentOption["checkout"] == 21)) {
    if (isset($_GET['PayID']) && $_GET['PayID'] <> '' && isset($_GET['TransID']) && $_GET['TransID'] <> '' && isset($_GET['PayID']) && $_GET['PayID'] <> '' && isset($_REQUEST["shopdata"]) && !empty($_REQUEST["shopdata"])) {

        if (count($resultArray) == 1) {

            $_SESSION['paypal_express_PayID'] = $_GET['PayID'];
            $_SESSION['paypal_express_TransID'] = $_GET['TransID'];

            $email = $_GET['e-mail'];
            $fullName = $_GET['name'];
            $firstName = $_GET['firstname'];
            $lastName = $_GET['lastname'];

            $phone = '';
            if (isset($_GET['Phone'])) {
                $phone = $_GET['Phone'];
            }
            $deliveryStreet = '';
            if (isset($_GET['AddrStreet'])) {
                $deliveryStreet = $_GET['AddrStreet'];
            }
            $deliveryZip = '';
            if (isset($_GET['AddrZip'])) {
                $deliveryZip = $_GET['AddrZip'];
            }
            $deliveryCity = '';
            if (isset($_GET['AddrCity'])) {
                $deliveryCity = $_GET['AddrCity'];
            }
            $deliveryCountry = '';
            if (isset($_GET['AddrCountryCode'])) {
                $deliveryCountry = $_GET['AddrCountryCode'];
            }

            $billingName = '';
            if (isset($_GET['BillingName'])) {
                $billingName = $_GET['BillingName'];
            }
            $billingStreet = '';
            if (isset($_GET['BillingAddrStreet'])) {
                $billingStreet = $_GET['BillingAddrStreet'];
            }
            $billingCity = '';
            if (isset($_GET['BillingAddrCity'])) {
                $billingCity = $_GET['AddrCity'];
            }
            $billingZip = '';
            if (isset($_GET['BillingAddrZIP'])) {
                $billingZip = $_GET['BillingAddrZIP'];
            }
            $billingCountry = '';
            if (isset($_GET['BillingAddrCountryCode'])) {
                $billingCountry = $_GET['BillingAddrCountryCode'];
            }

            // Find a match and store it in $result.
            if (preg_match('/([^\d]+)\s?(.+)/i', $deliveryStreet, $result)) {
                $streetName = $result[1];
                if ($streetName <> '') {
                    $deliveryStreet = $streetName;
                }
                $housNumber = $result[2];
            }

            if ($billingName == '' || $billingStreet == '' || $billingCity = '' || $billingZip = '') {
                $billingName = $fullName;
                $billingStreet = $deliveryStreet;
                $billingCity = $deliveryCity;
                $billingZip = $deliveryZip;
                $billingCountry = $deliveryCountry;

            }

            $_SESSION["visitor_name"] = $billingName;
            $_SESSION["visitor_surname"] = $firstName;
            $_SESSION["visitor_lastname"] = $lastName;
            $_SESSION["visitor_address"] = $billingStreet;
            $_SESSION["visitor_address_no"] = $housNumber;
            $_SESSION["visitor_post_code"] = $billingZip;
            $_SESSION["visitor_city"] = $billingCity;
            $_SESSION["visitor_telephone"] = $phone;
            $_SESSION["visitor_email"] = $email;
            $_SESSION["visitor_country"] = $billingCountry;

            $_SESSION["visitor_name_shipping"] = $fullName;
            $_SESSION["visitor_surname_shipping"] = $firstName;
            $_SESSION["visitor_lastname_shipping"] = $lastName;
            $_SESSION["visitor_user_street_shipping"] = $deliveryStreet;
            $_SESSION["visitor_user_street_no_shipping"] = $housNumber;
            $_SESSION["visitor_post_code_shipping"] = $deliveryZip;
            $_SESSION["visitor_city_shipping"] = $deliveryCity;
            $_SESSION["visitor_country_shipping"] = $deliveryCountry;

            $_SESSION['use_paypal_express'] = true;

            // Get Main Language
            $prepStatement = " SELECT 
                                  * FROM
                                   main_language
                                    WHERE
                                  company = :company
                                    AND shop_code = :shop_code
                                     AND code = :language_code
                                   LIMIT 1";

            $params = [
                [':company', $company, PDO::PARAM_STR],
                [':shop_code', $shopCode, PDO::PARAM_STR],
                [':language_code', $_SESSION['language'], PDO::PARAM_STR],

            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $resultArray = $pdo->getResultArray();
            $mainLanguage = $resultArray[0];
            $paymentOption = get_paypal_express_data($company, $shopCode, $mainLanguage['shop_language_code']);
            $_SESSION["payment_line_no"] = $paymentOption['line_no'];
            $bindedSiteCode = check_bind_site_code_from_domain();

            $redirectUrl = "https://" . $_SERVER["SERVER_NAME"] . "/" . $_SESSION['site'] . "/" . $_SESSION['language'] . "/order/buy/";
            if ($bindedSiteCode <> '') {
                $redirectUrl = "https://" . $_SERVER["SERVER_NAME"] . "/" . $_SESSION['language'] . "/order/buy/";
            }

            headerFunctionBridge('Location: ' . $redirectUrl);

            ?>

            <script type="text/javascript">
                window.location.href = "<? echo $redirectUrl; ?>";
            </script>


            <?


        }
    }
} elseif (isset($_GET['Status']) && ($_GET['Status'] == 'OK' || $_GET['Status'] == 'AUTHORIZED')) {

    $_SESSION['pay_id'] = $_GET['PayID'];
    $query = "SELECT *
		  FROM shop_payment_option
		  WHERE line_no='" . $_SESSION['payment_line_no'] . "'
		  AND shop_code='" . $GLOBALS['shop']['code'] . "'
		  AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    $bindedSiteCode = check_bind_site_code_from_domain();

    $redirectUrl = "https://" . $_SERVER["SERVER_NAME"] . "/" . $_SESSION['site'] . "/" . $_SESSION['language'] . "/order/complete_order/";
    if ($bindedSiteCode <> '') {
        $redirectUrl = "https://" . $_SERVER["SERVER_NAME"] . "/" . $_SESSION['language'] . "/order/complete_order/";
    }

    if ($paymentOption['checkout'] == "4" || $paymentOption['checkout'] == "5") {

        $payPalTransactionID = $_REQUEST['TransactionID'];
        $prepStatement = "SELECT * FROM shop_sales_header WHERE payment_transaction_id =:transaction_id";

        $params = [
            [':transaction_id', $_REQUEST['TransID'], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArraySalesHeader = $pdo->getResultArray();

        if (count($resultArraySalesHeader) == 1) {
            $sales_header = $resultArraySalesHeader[0];
            $prepStatement = "
                    UPDATE shop_sales_header 
                    SET paypal_transaction_id = :paypal_transaction_id
                    WHERE payment_transaction_id =:payment_transaction_id
				  ";

            $params = [
                [':paypal_transaction_id', $payPalTransactionID, PDO::PARAM_STR],
                [':payment_transaction_id', $_REQUEST['TransID'], PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
        }

        headerFunctionBridge('Location: ' . $redirectUrl);
    } else {
        ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
        <html>
        <head>


            <script type="text/javascript">
                parent.location.href = '<? echo $redirectUrl; ?>';
                //self.close();
            </script>

        </head>
        <body></body>
        </html>
    <? }
}
?>