<?php
if (
    isset($_GET['access_token']) && $_GET['access_token'] <> ''
    && isset($_GET['token_type']) && $_GET['token_type'] <> ''
    && isset($_GET['expires_in']) && $_GET['expires_in'] <> ''
    && isset($_GET['scope']) && $_GET['scope'] <> ''
    && isset($_GET['refrence_id']) && $_GET['refrence_id'] <> ''
) {
    $baseDirectory = rtrim(dirname(dirname(dirname(__DIR__))), '/');
    include($baseDirectory . '/vendor/autoload.php');

    $envDir = rtrim(dirname(dirname(dirname(__DIR__))), '/') . '/config';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    $rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
    require_once $rootDir . 'plugins/paygate/includes/function.inc.php';
    require_once $rootDir . 'plugins/paygate/computop_functions.inc.php';
    require_once $rootDir . 'dc/dc-server.config.php';
    require_once $rootDir . 'dc/common/common_functions.inc.php';
    require_once $rootDir . 'module/dcshop/common/shop_functions.inc.php';

    $logDir = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
    $logFile = 'PaymentNotify.log';
    $logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
    $logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
    $processor = new \Monolog\Processor\PsrLogMessageProcessor();
    $logFileHandler->pushProcessor($processor);
    $logger = new \Monolog\Logger('PaymentNotify', [$logFileHandler]);
    $logger->info('Page Name: amazon_payment');

    $shopdata = filter_var($_REQUEST["shopdata"], FILTER_SANITIZE_STRING);

    session_id($_GET['UserData']);
    session_start();


   $visitor =  get_visitor_data_by_session_id($_GET['UserData']);

    $shopdata = explode('|', $shopdata);
    $company = $shopdata[0];
    $shopCode = $shopdata[1];

    if (strpos($shopCode, '?') !== false) {
        $shopdata = explode('?', $shopCode);
        $shopCode = $shopdata[0];
    }

    $shop = get_shop_by_company_shop($company, $shopCode);

    $mainLanguage = get_main_language($company, $shopCode, $_SESSION['language']);
    $paymentOption = get_amazon_pay_data($company, $shopCode, $mainLanguage['shop_language_code']);

    $_SESSION["payment_line_no"] = $paymentOption['line_no'];


    // 1. ------------Initialize Payment----------

    $merchant_id = $shop['computop_merchant_id'];
    $trasnactionID = get_payment_transaction_id();
    $trans_id = "&TransID=" . $trasnactionID;
    $accessToken = "&AccessToken=" . $_GET['access_token'];
    $scope = "&Scope=" . $_GET['scope'];
    $eventToken = "&EventToken=LGN";
    $tokenType = "&TokenType=" . $_GET['token_type'];
    $expiry = "&Expiry=" . $_GET['expires_in'];
    $orderRefrenceID = "&OrderReferenceID=" . $_GET['refrence_id'];

    $_SESSION['amazon_order_reference_id'] = $_GET['refrence_id'];
    $logger->info('Transaction Number====' . $trasnactionID);
    $logger->info('Merchant ID====' . $merchant_id);


    $amountValue = $_SESSION['amazon_payment_amount'];
    $amountValue = round($amountValue * 100, 0);
    $amount = "&Amount=" . $amountValue;


    $currencyValue = $_SESSION['amazon_payment_currency'];
    $currency = "&Currency=" . $currencyValue;
    $hmac = computop_get_mac($amountValue, $currencyValue, $merchant_id, $shop['computop_hmac_key'], $trasnactionID);
    $hmac = "&MAC=" . $hmac;
    $logger->info('Hmach==Before Request====' . $hmac);

    $url_notify = "&URLNotify=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_notify.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"];

    $computopLanguageCodesAmazon = ["de" => "de", "gb" => "gb"];
    $languageAmazon = "&CountryCode=DE";
    /*  if (array_key_exists($GLOBALS["language"]["code"], $computopLanguageCodesAmazon)) {
          $languageAmazon = "&Language=" . $computopLanguageCodesAmazon[$GLOBALS["language"]["code"]];
      }*/

    $plaintext_paypal = "MerchantID=" . $merchant_id . $trans_id . $hmac . $accessToken
        . $scope . $eventToken . $tokenType . $expiry . $languageAmazon . $amount . $currency . $url_notify;

    $len_paypal = mb_strlen($plaintext_paypal, mb_internal_encoding());

    $BlowFish = new ctBlowfish;
    $Data_paypal = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);

    $logger->info('Paygate Amazon Authorize Resquest');
    $logger->info('Data = https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);

    $payGateResponse = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);

    $dataArray = explode("&", $payGateResponse);

    foreach ($dataArray as $val) {
        $tmp = explode('=', $val);
        $finalArray[$tmp[0]] = $tmp[1];
    }

    $data_decrypted = $BlowFish->ctDecrypt($finalArray["Data"], $finalArray["Len"], $GLOBALS['shop']['computop_password']);

    $parts = explode("&", $data_decrypted);
    foreach ($parts as $part) {
        $x = explode('=', $part);
        $paygate_response[$x[0]] = $x[1];
    }

    $payID = $paygate_response['PayID'];
    $_SESSION['amazon_PayID'] = $payID;

    $XID = $paygate_response['XID'];
    $transactionID = $paygate_response['TransID'];
    $status = $paygate_response['Status']; // OK
    // The payment is Initialized Sucessfully
    if ($paygate_response['Status'] == "OK") {
        // -----------------2 . Set Order Details --------------------------
        $orderDesc = "&OrderDesc= first reqeust";
        $eventToken = "&EventToken=SOD";
        $payIDValue = $paygate_response['PayID'];
        $payID = "&PayID=" . $payIDValue;
        $storeName = "&StoreName=".$GLOBALS["site"]["name"];

        $hmac = computop_get_mac($amountValue, $currencyValue, $merchant_id, $shop['computop_hmac_key'], $trasnactionID, $payIDValue);
        $hmac = "&MAC=" . $hmac;

        $plaintext_paypal = "MerchantID=" . $merchant_id . $trans_id . $payID . $hmac . $orderRefrenceID .
            $eventToken . $amount . $currency . $storeName;

        $len_paypal = mb_strlen($plaintext_paypal, mb_internal_encoding());
        $Data_paypal = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);

        $logger->info('Paygate Amazon Set Order Detials Resquest');
        $logger->info('Data = https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);

        $payGateResponse = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);


        $dataArray = explode("&", $payGateResponse);

        foreach ($dataArray as $val) {
            $tmp = explode('=', $val);
            $finalArray[$tmp[0]] = $tmp[1];
        }

        $data_decrypted = $BlowFish->ctDecrypt($finalArray["Data"], $finalArray["Len"], $GLOBALS['shop']['computop_password']);

        $parts = explode("&", $data_decrypted);
        foreach ($parts as $part) {
            $x = explode('=', $part);
            $paygate_response[$x[0]] = $x[1];
        }

        //------3-- get order details-----------------

        $eventToken = "&EventToken=GOD";


        $plaintext_paypal = "MerchantID=" . $merchant_id  . $payID .   $eventToken. $orderRefrenceID ;

        $len_paypal = mb_strlen($plaintext_paypal, mb_internal_encoding());
        $Data_paypal = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);

        $logger->info('Paygate Amazon GET Order Detials Resquest');
        $logger->info('Data = https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);

        $payGateResponse = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_paypal . '&Data=' . $Data_paypal);


        $dataArray = explode("&", $payGateResponse);

        foreach ($dataArray as $val) {
            $tmp = explode('=', $val);
            $finalArray[$tmp[0]] = $tmp[1];
        }

        $data_decrypted = $BlowFish->ctDecrypt($finalArray["Data"], $finalArray["Len"], $GLOBALS['shop']['computop_password']);

        $parts = explode("&", $data_decrypted);
        foreach ($parts as $part) {
            $x = explode('=', $part);
            $paygate_response[$x[0]] = $x[1];
            $logger->info('Response value : '.$x[0].'=='.$x[1]);
        }


        //---------------------------



        $email = $paygate_response['buyermail'];
        $fullName = $paygate_response['addrname'];

        $phone = '';
        if (isset($paygate_response['phonenumber'])) {
            $phone = $paygate_response['phonenumber'];
        }

        $companyName = '';
        if (isset($paygate_response['addrstreet'])) {
            $companyName = $paygate_response['addrstreet'];
        }

        $deliveryName = '';
        if (isset($paygate_response['addrname'])) {
            $deliveryName = $paygate_response['addrname'];
        }

        $deliveryStreet = '';
        if (isset($paygate_response['addrstreet2'])) {
            $deliveryStreet = $paygate_response['addrstreet2'];
        }
        $deliveryZip = '';
        if (isset($paygate_response['AddrZip'])) {
            $deliveryZip = $paygate_response['AddrZip'];
        }
        $deliveryCity = '';
        if (isset($paygate_response['AddrCity'])) {
            $deliveryCity = $paygate_response['AddrCity'];
        }
        $deliveryCountry = '';
        if (isset($paygate_response['AddrCountryCode'])) {
            $deliveryCountry = $paygate_response['AddrCountryCode'];
        }


        $billingName = '';
        if (isset($paygate_response['bdaddrname'])) {
            $billingName = $paygate_response['bdaddrname'];
        }
        $billingStreet = '';
        if (isset($paygate_response['bdaddrstreet2'])) {
            $billingStreet = $paygate_response['bdaddrstreet2'];
        }
        $billingCity = '';
        if (isset($paygate_response['bdaddrcity'])) {
            $billingCity = $paygate_response['bdaddrcity'];
        }
        $billingZip = '';
        if (isset($paygate_response['bdaddrzip'])) {
            $billingZip = $paygate_response['bdaddrzip'];
        }
        $billingCountry = '';
        if (isset($paygate_response['bdaddrcountrycode'])) {
            $billingCountry = $paygate_response['bdaddrcountrycode'];
        }

        if ($billingName == '' || $billingStreet == '' || $billingCity == '' || $billingZip == '') {


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


        //-------------------------------------------------------

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


    } else {
        echo 'transaction failed';
    }


} else {
    die('No Permission');
}