<?php

ini_set("display_errors", 1);

$baseDirectory = rtrim(dirname(dirname(dirname(__DIR__))), '/');
include($baseDirectory . '/vendor/autoload.php');

$envDir = rtrim(dirname(dirname(dirname(__DIR__))), '/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}


$logDir = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
$logFile = 'PaymentNotify.log';
$logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
$logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new \Monolog\Processor\PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new \Monolog\Logger('PaymentNotify', [$logFileHandler]);
$logger->info('Page Name: order_notify');

$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';

$pdoHost = getenv('MAIN_MYSQL_DB_HOST');
$pdoPort = getenv('MAIN_MYSQL_DB_PORT');
$pdoUser = getenv('MAIN_MYSQL_DB_USER');
$pdoPass = getenv('MAIN_MYSQL_DB_PASS');
$pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

$pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

if (isset($_REQUEST["shopdata"]) && !empty($_REQUEST["shopdata"])) {

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

    if (count($resultArray) == 1) {
        $shop = $resultArray[0];

        $BlowFish = new ctBlowfish;

        $data_decrypted = $BlowFish->ctDecrypt($_REQUEST["Data"], $_REQUEST["Len"], $shop["computop_password"]);

        $parts = explode("&", $data_decrypted);
        foreach ($parts as $part) {
            $x = explode('=', $part);
            $paygate_response[$x[0]] = $x[1];
        }


        $logger->info('Date decrypted: ' . $data_decrypted);
        $logger->info('Paygate Response Trans ID: ' . $paygate_response['TransID']);
        $logger->info('Paygate Response Pay ID: ' . $paygate_response['PayID']);
        $logger->info('Paygate Response Status: ' . $paygate_response['Status']);


        if ($paygate_response["Status"] == "OK" || $paygate_response["Status"] == "AUTHORIZED" ||  $paygate_response["Status"] == "AUTHORIZE_REQUEST" ) {

            $prepStatement = "SELECT * FROM shop_sales_header WHERE payment_transaction_id =:transaction_id";

            $params = [
                [':transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $resultArraySalesHeader = $pdo->getResultArray();

            if (count($resultArraySalesHeader) == 1) {
                $sales_header = $resultArraySalesHeader[0];

                $prepStatement = "SELECT *
				  FROM shop_payment_option
				  WHERE line_no=:line_no
				  AND shop_code=:shop_code
				  AND language_code =:language_code
				  AND company = :company
				  LIMIT 1
				  ";

                $params = [
                    [':line_no', $sales_header['payment_option_line_no'], PDO::PARAM_STR],
                    [':shop_code', $sales_header['shop_code'], PDO::PARAM_STR],
                    [':language_code', $sales_header['language_code'], PDO::PARAM_STR],
                    [':company', $sales_header['company'], PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();
                $resultArrayPaymentOption = $pdo->getResultArray();
                $terms = $resultArrayPaymentOption[0];

                $proces_payment = 0;
                if ($terms['checkout'] == '3' || $terms['checkout'] == '5') {
                    $processed = NULL;
                } else {
                    $processed = 'NOW()';
                }
                if ($terms['checkout'] === '3' || $terms['checkout'] === '5' || $terms['checkout'] === '6' || $terms['checkout'] === '7') {
                    $process_payment = 1;
                } else {
                    $process_payment = 0;
                }

                if (isset($_REQUEST['TransactionID']) && !empty($_REQUEST['TransactionID'])) {
                    $payPalTransactionID = $_REQUEST['TransactionID'];
                    $prepStatement1 = "
                        UPDATE shop_sales_header 
                        SET paypal_transaction_id = :paypal_transaction_id
                        WHERE payment_transaction_id =:payment_transaction_id
                      ";

                    $params1 = [
                        [':paypal_transaction_id', $payPalTransactionID, PDO::PARAM_STR],
                        [':payment_transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                    ];
                    $pdo->setQuery($prepStatement1);
                    $pdo->prepareQuery();
                    $pdo->bindParameters($params1);
                    $pdo->executePreparedStatement();
                }


                $prepStatement = "
                    UPDATE shop_sales_header 
                    SET update_insert = 1,
                          update_notify = 1,
                          order_error = 0,
                          payment_processed = :payment_processed, 
                          process_payment = :process_payment, 
                          pay_id = :pay_id 
                    WHERE payment_transaction_id =:payment_transaction_id
				  ";

                $params = [
                    [':payment_processed', $processed, PDO::PARAM_STR],
                    [':process_payment', $process_payment, PDO::PARAM_STR],
                    [':pay_id', $paygate_response['PayID'], PDO::PARAM_STR],
                    [':payment_transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();


                $prepStatement = "
                    UPDATE main_mail_log
                    SET delayed_send = 0,
                        payment_transaction_id = ''
                    WHERE payment_transaction_id =:payment_transaction_id AND delayed_send = 1
				  ";

                $params = [
                    [':payment_transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                ];
                $pdo->setQuery($prepStatement);
                $pdo->prepareQuery();
                $pdo->bindParameters($params);
                $pdo->executePreparedStatement();


                // update coupon
                if ($sales_header['coupon_code'] != '' AND $sales_header['dc_order'] == 0) {
                    $pdo = get_main_db_pdo_from_env_single_instance();

                    static $getCouponLineQuery = '
                        SELECT 
                          *
                        FROM
                          shop_coupon_line 
                        WHERE
                          company = :company
                        AND 
                          shop_code = :shop_code
                        AND 
                          language_code = :language_code
                        AND
                          coupon_code = :coupon_code
                        AND 
                          code = :code
                        AND
                          update_notify = 0
                        LIMIT 1
                    ';

                    $stmt = $pdo->prepare($getCouponLineQuery);
                    $stmt->bindValue(':company', $sales_header["company"], PDO::PARAM_STR);
                    $stmt->bindValue(':shop_code', $sales_header["shop_code"], PDO::PARAM_STR);
                    $stmt->bindValue(':language_code', $sales_header["language_code"], PDO::PARAM_STR);
                    $stmt->bindValue(':coupon_code', $sales_header['coupon_code'], PDO::PARAM_STR);
                    $stmt->bindValue(':code', $sales_header['coupon_header_code'], PDO::PARAM_STR);

                    if (!$stmt->execute()) {
                        $errorInfo = $pdo->errorInfo();
                        $errorString = implode($errorInfo, PHP_EOL);
                        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
                    }

                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $coupon = $stmt->fetch();

                    if (is_array($coupon)) {

                        static $updateCouponLineQuery = '
                        UPDATE 
                          shop_coupon_line 
                        SET 
                          amount = if (value_coupon = 1, :new_coupon_amount, amount),
                          times_used = times_used + 1,
                          update_notify = 1
                        WHERE
                          id = :couponId
                    ';

                        $newCouponAmount = (float)$coupon['amount'] - (float)$sales_header['coupon_amount'];

                        $stmt = $pdo->prepare($updateCouponLineQuery);
                        $stmt->bindValue(':new_coupon_amount', $newCouponAmount, PDO::PARAM_STR);
                        $stmt->bindValue(':couponId', $coupon['id'], PDO::PARAM_STR);

                        if (!$stmt->execute()) {
                            $errorInfo = $pdo->errorInfo();
                            $errorString = implode($errorInfo, PHP_EOL);
                            throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
                        }
                    }
                }

                //ToDO check if update is needed
                if ($sales_header['value_coupon'] != 0) {

                    $prepStatement = "
                      UPDATE shop_coupon_line
					  SET amount_left=(amount_left-" . $sales_header['coupon_amount'] . "),update_insert=1
					  WHERE code=:code 
					  AND coupon_code=:coupon_code 
					  AND update_insert = 0
				    ";

                    $params = [
                        [':code', $sales_header['coupon_header_code'], PDO::PARAM_STR],
                        [':coupon_code', $sales_header['coupon_code'], PDO::PARAM_STR],
                    ];
                    $pdo->setQuery($prepStatement);
                    $pdo->prepareQuery();
                    $pdo->bindParameters($params);
                    $pdo->executePreparedStatement();

                } else {
                    $prepStatement = "
                      UPDATE shop_coupon_line
					  SET update_insert=1
					  WHERE code=:code AND coupon_code=:coupon_code AND update_insert = 0
				    ";

                    $params = [
                        [':code', $sales_header['coupon_header_code'], PDO::PARAM_STR],
                        [':coupon_code', $sales_header['coupon_code'], PDO::PARAM_STR],
                    ];
                    $pdo->setQuery($prepStatement);
                    $pdo->prepareQuery();
                    $pdo->bindParameters($params);
                    $pdo->executePreparedStatement();

                }
                mail_send();
            }
        }
    }

}

?>