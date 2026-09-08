<?php
/**
 * Created by PhpStorm.
 * User: Grosch
 * Date: 25.01.2017
 * Time: 11:45
 */

/**
 * Collection of functions to communicate with the computop paygate
 *
 * Copy this file into /plugins/paygate/
 * and include it in your files with:
 * require_once($_SERVER['DOCUMENT_ROOT']."/plugins/paygate/computop_functions.inc.php");
 */
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';


/**
 * Write a line to the logfile under userdata/log/logfile-payment_PAYMENTTYP_YYYY-KW.txt
 * $payment_typ: eg klarna, payolution, paydirekt, EvWatcher
 * $logtext: The text you want to log
 * $param_arr: a parameter array you need to log (optional)
 */
function computop_payment_log($payment_typ, $logtext, $param_arr = "") {
    $logdir = rtrim(dirname(dirname(__DIR__)),'/\\') . DIRECTORY_SEPARATOR . 'userdata/logs/';
    if ( !is_dir($logdir) ) {
        mkdir($logdir, 0777, true);
    }
    $params = "";
    if ( $param_arr <> '' ) {
        $params_tmp = array();
        foreach ($param_arr as $index => $item) {
            if (is_int($index)) {
                $params_tmp[] = $item;
            } else {
                $params_tmp[] = $index."=".$item;
            }
        }
        $params = " - ".implode(" / ",$params_tmp);
    }
    $fh = fopen($logdir."logfile-payment_".$payment_typ."_".date("Y-W").".txt", "a+");
    fwrite($fh, date('Y-m-d H:i:s') . " (" .$GLOBALS["shop_customer"]["customer_no"]. ") ");
    fwrite($fh, $logtext . $params);
    fwrite($fh, PHP_EOL);
    fclose($fh);
}




/**
 * Encrypts the given array with an mac and returns the Parameterstring
 *
 * $param_arr: everything that needs to be encrypted, incl the mac
 * $amount: the Amount-Value. Needed for the MAC creation
 */
function computop_encrypt_array(
    $param_arr,
    $amount,
    $merchant_id = "",
    $computop_password = ""
    ) {
    date_default_timezone_set('Europe/Berlin');

    $merchant_id = ($merchant_id)?:$GLOBALS['shop']['computop_merchant_id'];
    $computop_password = ($computop_password)?:$GLOBALS['shop']['computop_password'];

    //$plaintext = utf8_decode( implode("&",$param_arr) );
    ////$plaintext = mb_convert_encoding( implode("&",$param_arr), "UTF-8" );
    //$len = strlen($plaintext);

    // creation like paypal, encoding should be utf8
    $plaintext = implode("&",$param_arr);
    $len = strlen(utf8_decode($plaintext));

    $BlowFish = new ctBlowfish;
    $data = $BlowFish->ctEncrypt($plaintext, $len, $computop_password);

    return "?MerchantID=".$merchant_id."&Len=".$len."&Data=".$data;

}


/**
 * Generates the MAC for the communication with the paygate
 * Format: PayID*TransID*MerchantID*Amount*Currency
 */
function computop_get_mac(
    $amount,
    $currency = "EUR",
    $merchant_id = "",
    $hmacpw = "",
    $trans_id = "",
    $pay_id = ""
) {

    $merchant_id = ($merchant_id)?:$GLOBALS['shop']['computop_merchant_id'];
    $hmacpw = ($hmacpw)?:$GLOBALS['shop']['computop_hmac_key'];
    $trans_id = ($trans_id)?:$_SESSION['trans_id'];

    if (empty($merchant_id)) { echo '<!-- ERROR: $merchant_id is empty -->'; }
    if (empty($hmacpw)) { echo '<!-- ERROR: $hmacpw is empty -->'; }
    if (empty($trans_id)) { echo '<!-- ERROR: $trans_id is empty -->'; }

    $tohash = utf8_decode($pay_id.'*'.$trans_id.'*'.$merchant_id.'*'.$amount.'*'.$currency);
    $mac = hash_hmac('sha256', $tohash, $hmacpw);
    return $mac;
}


/**
 * Splits the given $html string and encodes it with another function
 */
function computop_decrypt_response_from_content($pw, $html) {

    $html_parts = explode('&', $html);
    if (count($html_parts) == 2) {
        $part1 = explode('=', $html_parts[0])[1];
        $part2 = explode('=', $html_parts[1])[1];

        if ( is_int($part1) ) {
            return computop_decrypt_response($pw, $part2, $part1);
        } else {
            return computop_decrypt_response($pw, $part1, $part2);
        }
    }
    return 0;

}


/**
 * Decrypts the given response of computop
 * returns an assoc-array containing the answer parameters as key-value-pair
 */
function computop_decrypt_response(
    $pw,
    $len = "",
    $data = ""
) {

    $len = ($len)?:$_REQUEST["Len"];
    $data = ($data)?:$_REQUEST["Data"];

    $BlowFish = new ctBlowfish;
    $data_decrypted = $BlowFish->ctDecrypt($data, $len, $pw);
    $parts = explode('&',$data_decrypted);

    $answer = array();
    foreach($parts as $part){
        $pair = explode("=", $part, 2);
        $answer[$pair[0]] = $pair[1];
    }
    return $answer;

}
