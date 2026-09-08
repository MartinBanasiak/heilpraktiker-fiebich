<?
/** payment_functions.inc.php **/

// Transaction ID generieren
function generate_transaction_id() {
    if (isset($_SESSION['trans_id']) && $_SESSION['trans_id'] != '') {
        $query  = "SELECT id FROM shop_sales_header WHERE payment_transaction_id = '" . $_SESSION['trans_id'] . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $header = mysqli_fetch_assoc($result);
            $query2 = "DELETE FROM shop_sales_line WHERE shop_sales_header_id = '" . $header['id'] . "'";
            mysqli_query($GLOBALS['mysql_con'], $query2);
            $query3 = "DELETE FROM shop_sales_header WHERE id='" . $header['id'] . "'";
            mysqli_query($GLOBALS['mysql_con'], $query3);
        }
    } else {
        $t_id = (string)mt_rand();
        $t_id .= date("yzGis");
        $_SESSION['trans_id'] = $t_id;
    }
}

// Bonitätsprüfung
function check_creditworthiness() {
    require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
    date_default_timezone_set('Europe/Berlin');

    generate_transaction_id($_SESSION["trans_id"]);

    //Parameter für paygate

    $merchant_id = "MerchantID=" . $GLOBALS['shop']['computop_merchant_id'];
    $trans_id    = "&TransID=" . $_SESSION['trans_id'];
    //TODO: Order_Desc füllen
    $order_desc = "";
    $salutation = "&Salutation=" . $_SESSION["visitor_salutation"];

    $name_arr = explode(" ", $_SESSION["visitor_name"]);
    $fname    = "&FirstName=" . $name_arr[0];
    $lname    = "&LastName=" . $name_arr[(count($name_arr) - 1)];

    $street_arr = preg_split('~([^\d]*) (.*)~', $_SESSION["visitor_address"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $street     = "&AddrStreet=" . $street_arr[0];
    $street_nr  = "&AddrStreetNr=" . $street_arr[(count($street_arr) - 1)];

    $zip         = "&AddrZIP=" . $_SESSION["visitor_post_code"];
    $city        = "&AddrCity=" . $_SESSION["visitor_city"];
    $customer_id = "&CustomerID=" . ""; //customer_no?
    $event_token = "&EventToken=B"; //EventToken = B für Bonität
    $dateofbirth = "&DateOfBirth=" . $_SESSION["visitor_birthday"];

    if (local_environment()) {
        $amount = "&Amount=10";
    } else {
        $amount = "&Amount=" . round($_SESSION['total_basket'] * 100, 0);
    }
    $currency = "&Currency=EUR";

    $plaintext = $merchant_id . $trans_id . $order_desc .
        $salutation . $fname . $lname . $street .
        $street_nr . $zip . $city . $customer_id .
        $event_token . $dateofbirth . $amount . $currency;

    $len      = strlen($plaintext);  // Length of the plain text string
    $BlowFish = new ctBlowfish;
    $Data     = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);

    //TODO: Request an Paygate senden und Antwort auswerten
    $html       = file_get_contents('https://www.computop-paygate.com/payprotect.aspx?MerchantID=' . $GLOBALS['shop']['computop_merchant_id'] . '&Len=' . $len . '&Data=' . $Data);
    $html_parts = explode('&', $html);
    if (count($html_parts) == 2) {
        $len_parts      = explode('=', $html_parts[0]);
        $len            = $len_parts[1];
        $data_parts     = explode('=', $html_parts[1]);
        $data           = $data_parts[1];
        $BlowFish       = new ctBlowfish;
        $data_decrypted = $BlowFish->ctDecrypt($data, $len, $GLOBALS["shop"]["computop_password"]);
        $parts          = explode('&', $data_decrypted);
        if (count($parts) > 0) {
            $paramarray = array();
            foreach ($parts AS $part) {
                echo $part . "<br />";
            }
        } else {
            echo 'Falsches Antwortformat (parts)';
            print_r($parts);
        }
    } else {
        echo "Falsches Antwortformat (html)";
        print_r($html);
    }
    /*
    if(in_array("Status=OK") && in_array("Result==GRUEN"))
        return 1;
    }else{
        return 2;
    }
    */
    return 1;
}

?>