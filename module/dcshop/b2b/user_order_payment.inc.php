<?php
$order_no_query  = "SELECT order_no FROM shop_sales_header ORDER BY order_no DESC LIMIT 1";
$order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
if (@mysqli_num_rows($order_no_result) == 1) {
    $order_no_array = @mysqli_fetch_array($order_no_result);
    $order_no       = $order_no_array["order_no"] + 1;
} else {
    $order_no = 100000;
}
if (isset($_SESSION['trans_id']) && $_SESSION['trans_id'] != '') {
    $query  = "SELECT id FROM shop_sales_header WHERE payment_transaction_id = '" . $_SESSION['trans_id'] . "'";
    $header = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], $query));
    $query  = "DELETE FROM shop_sales_line WHERE shop_sales_header_id = '" . $header['id'] . "'";
    mysqli_query($GLOBALS['mysql_con'], $query);
    $query = "DELETE FROM shop_sales_header WHERE id='" . $header['id'] . "'";
    mysqli_query($GLOBALS['mysql_con'], $query);
}
//Zahlart auslesen
$query  = "SELECT *
		  FROM shop_payment_option
		  WHERE line_no = '" . $_SESSION['payment_line_no'] . "'
		  	AND shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";
$result = mysqli_query($GLOBALS['mysql_con'], $query);
if (mysqli_num_rows($result) > 0) {
    $terms = mysqli_fetch_assoc($result);
}
//Transaktionsid erzeugen wenn Zahlung über paygate
//if($terms['checkout'] == '2' || $terms['checkout'] == '3' || $terms['checkout'] == '4' || $terms['checkout'] == '5')
//{
//Transaktions-ID für paygate
$t_id = (string)mt_rand();
$t_id .= date("yzGis");
//}
//else
//{
//	$t_id = '';
//}
$_SESSION['trans_id'] = $t_id;
//Bestellkopf speichern
if ($_POST["input_drop_shipment"] == "on") {
    $drop_shipment = 1;
} else {
    $drop_shipment = 0;
}
if ($shipment_address["country"] == '') {
    $shipment_address["country"] = $GLOBALS['shop_language']['default_country_code'];
}
if ($invoice_address["country"] == '') {
    $invoice_address["country"] = $GLOBALS['shop_language']['default_country_code'];
}
$shipment_address["contact"]   = !empty(trim($_POST['input_name'])) ? $_POST['input_name'] : $shipment_address["contact"];


$newsletter = 0;
if ($_POST["input_newsletter_checked"] != "") {
    $newsletter = 1;
}
$query = "INSERT INTO shop_sales_header
		  SET company = '" . $GLOBALS['shop']['company'] . "',
		  	  shop_code = '" . $GLOBALS['shop']['code'] . "',
		  	  language_code = '" . $GLOBALS['shop_language']['code'] . "',
		  	  order_no = '" . $order_no . "',
		  	  shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "',
		  	  customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "',
		  	  shop_user_id = '" . $GLOBALS["shop_user"]["id"] . "',
		  	  user_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["shop_user"]["name"]) . "',
		  	  user_email = '" . $GLOBALS["shop_user"]["email"] . "',
		  	  update_insert = 1,
		  	  ship_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name"]) . "',
		  	  ship_to_name_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name_2"]) . "',
		  	  ship_to_address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address"]) . "',
		  	  ship_to_address_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address_2"]) . "',
		  	  ship_to_post_code = '" . $shipment_address["post_code"] . "',
		  	  ship_to_city = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["city"]) . "',
		  	  ship_to_country = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["country"]) . "',
		  	  ship_to_contact = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["contact"]) . "',
		  	  ship_to_telephone_no = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["telephone"]) . "',
		  	  bill_to_customer_no = '" . $GLOBALS["shop_customer"]["bill_to_customer_no"] . "',
		  	  bill_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name"]) . "',
		  	  bill_to_name_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name_2"]) . "',
		  	  bill_to_address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address"]) . "',
		  	  bill_to_address_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address_2"]) . "',
		  	  bill_to_post_code = '" . $invoice_address["post_code"] . "',
		  	  bill_to_city = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["city"]) . "',
		  	  bill_to_country = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["country"]) . "',
		  	  order_date = NOW(),
		  	  your_reference = '" . $_POST["input_your_reference"] . "',
		  	  your_comment = '" . $_POST["input_your_comment"] . "',
		  	  subtotal='" . $subtotal . "',
		  	  online_discount='" . $online_discount . "',
		  	  online_discount_amount='" . $online_discount_amount . "',
			  invoice_discount='" . $invoice_discount . "',
			  invoice_discount_amount='" . $invoice_discount_amount . "',
			  small_quantity_charge_amount='" . $small_quantity_charge_amount . "',
			  total='" . $total . "',
			  requested_delivery_date=" . datetosql($_POST['date']) . ",
			  drop_shipment='" . $drop_shipment . "',
			  shipping_option_line_no='" . $shipping_line_no . "',
			  shipping_cost='" . $shipping_cost . "',
			  payment_option_line_no = '" . $_SESSION['payment_line_no'] . "',
			  payment_cost = '" . $_SESSION['payment_cost'] . "',
			  payment_transaction_id = '" . $t_id . "',
			  currency_code = '" . $GLOBALS['shop_currency']['code'] . "',
			  coupon_amount = '" . $coupon_discount_amount . "',
			  value_coupon = '" . $_SESSION['coupon']['value_coupon'] . "',
			  coupon_code = '" . $_SESSION['coupon']['coupon_code'] . "',
			  newsletter_registration = '" . $newsletter . "'";
if (mysqli_query($GLOBALS['mysql_con'], $query)) {

    $sales_header_id = mysqli_insert_id($GLOBALS['mysql_con']);
    $basketLineIDSalesLineIDMap = [];
    while ($basket_line = @mysqli_fetch_array($basketresult)) {
        $basketLineID = (int)($basket_line['basket_line_id'] ?? 0);
        //Speichern der Bestellzeilen
        $query = "INSERT INTO shop_sales_line
				  SET company = '" . $GLOBALS['shop']['company'] . "',
				  	  shop_code = '" . $GLOBALS['shop']['code'] . "',
				  	  language_code = '" . $GLOBALS['shop_language']['code'] . "',
				  	  order_no = '" . $order_no . "',
				  	  shop_sales_header_id = '" . $sales_header_id . "',
				  	  shop_item_id = '" . $basket_line["id"] . "',
				  	  item_no = '" . $basket_line["item_no"] . "',
				  	  variant_code = '" . $basket_line["var_code"] . "',
				  	  description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $basket_line["description"]) . "',
				  	  summary = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $basket_line["summary"]) . "',
				  	  list_price = '" . $basket_line["base_price"] . "',
				  	  unit_price = '" . $basket_line["customer_price"] . "',
					  quantity = '" . $basket_line["basket_quantity"] . "',
					  line_amount = '" . $basket_line["basket_quantity"] * $basket_line["customer_price"] . "',
					  allow_invoice_disc = '" . $basket_line["allow_invoice_disc"] . "',
					  update_insert = 1";
        mysqli_query($GLOBALS['mysql_con'], $query);
        $salesLineID = mysqli_insert_id($GLOBALS['mysql_con']);
        $basketLineIDSalesLineIDMap[$basketLineID] = $salesLineID;
    }
    //--- US SALES TAX ---

    /**
     * @var $IOCContainer \Dice\Dice
     * @var $taxJarRESTAPIConsumer \DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer
     * @var $taxJarAdapter \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter
     */

    $baseDir = dirname(dirname(dirname(__DIR__)));
    $logDir = $baseDir . DIRECTORY_SEPARATOR . 'logs';
    $logFile = 'USSalesTax.log';
    $logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
    $logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
    $processor = new \Monolog\Processor\PsrLogMessageProcessor();
    $logFileHandler->pushProcessor($processor);
    $logger = new \Monolog\Logger('USSalesTax', [$logFileHandler]);

    $taxEnvDir = $baseDir . '/config/USSalesTax/';
    $dotenvTax = new \Dotenv\Dotenv($taxEnvDir);
    $dotenvTax->load();
    $taxJarRESTAPIConsumer = $IOCContainer->create(\DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer::class);
    $vatMgr = $IOCContainer->create('$VATManager');
    $shopPDO = $IOCContainer->create(\DynCom\dc\common\classes\PDOQueryWrapper::class);
    $taxJarAdapter = new \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter($taxJarRESTAPIConsumer,$vatMgr,$shopPDO,$logger);
    //+++ US SALES TAX +++
}


$_SESSION['order_no'] = $order_no;
//Parameter erzeugen wenn Zahlung über paygate
if ($terms['checkout'] == '2' || $terms['checkout'] == '3' || $terms['checkout'] == '4' || $terms['checkout'] == '5') {
    require_once rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
    //Parameter für paygate
    date_default_timezone_set('Europe/Berlin');
    $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
    $trans_id    = "&TransID=" . $t_id;
    $amount      = "&Amount=" . round($_SESSION['total_basket'] * 100, 0);
    //$amount = "&Amount=1"; //Angabe in kleinster Waehrungseinheit (z.B. Cent)
    $currency = "&Currency=EUR";
    //$order_desc = "&OrderDesc=TEST:0000"; //testparameter
    $query       = "SELECT id FROM shop_user_basket WHERE shop_visitor_id=" . $GLOBALS['visitor']['id'] . " LIMIT 1";
    $row         = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], $query));
    $order_desc  = "&Warenkorb-ID=" . $row['id'];
    $hmac        = "&MAC=" . hash_hmac('sha256', '*' . $trans_id . '*' . $merchant_id . '*' . $amount . '*' . $currency, $GLOBALS['shop']['computop_password']);
    $url_failure = "&URLFailure=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_error.php";
    $url_notify  = "&URLNotify=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_notify.php";
    $url_success = "&URLSuccess=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_success.php";
    $response    = "&Response=Encrypt";
    $ref_nr      = "&RefNr=" . $order_no;
    $userdata    = "&UserData=" . session_id();
    if ($terms['checkout'] == '3' || $terms['checkout'] == '5') {
        $capture = "&Capture=Manual";
        $txtype  = "&Txtype=Auth";
    } else {
        $capture = "&Capture=Auto";
        $txtype  = "";
    }
    if ($_SESSION['visitor_company_shipping'] != "") {
        $name    = "&FirstName=" . $_SESSION["visitor_name_shipping"];
        $company = "&LastName=" . $_SESSION["visitor_company_shipping"];
    } else {
        $name_arr = explode(" ", $_SESSION["visitor_name_shipping"]);
        $name     = "&FirstName=" . $name_arr[0];
        $company  = "&LastName=" . $name_arr[(count($name_arr) - 1)];
    }
    $address = "&AddrStreet=" . $_SESSION["visitor_user_street_shipping"];
    $city    = "&AddrCity=" . $_SESSION["visitor_city_shipping"];
    $plz     = "&AddrZIP=" . $_SESSION["visitor_post_code_shipping"];
    $country = "&AddrCountryCode=" . $_SESSION["visitor_country_shipping"];

    $plaintext        = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata;
    $plaintext_paypal = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata . $capture . $txtype . $name . $company . $address . $city . $plz . $country;
    $len              = strlen($plaintext);  // Length of the plain text string
    $len_paypal       = strlen($plaintext_paypal);
    $BlowFish         = new ctBlowfish;
    $Data             = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);
    $Data_paypal      = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);
}

if ($terms['checkout'] == '4' || $terms['checkout'] == '5') {
    ?>
    <script type="text/javascript">
        window.location.href = "https://www.computop-paygate.com/paypal.aspx?MerchantID=<?=$merchant_id ?>&Len=<?=$len_paypal ?>&Data=<?=$Data_paypal ?>";
    </script>
    <?
    //header('Location: https://www.computop-paygate.com/paypal.aspx?MerchantID='.$merchant_id.'&Len='.$len_paypal.'&Data='.$Data_paypal);
} elseif ($terms['checkout'] == '2' || $terms['checkout'] == '3') {
    ?>
    <div class="toolbar">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["user_order"] ?></h1>
    </div>
    <div class="text-center">
        <iframe width="410px" height="360px" scrolling="no" frameborder="0"
                src="https://www.computop-paygate.com/payssl.aspx?MerchantID=<?= $merchant_id ?>&Len=<?= $len ?>&Data=<?= $Data ?>&BGImage=https://<?= $_SERVER["SERVER_NAME"] ?>/layout/frontend/lauenstein/img/bg_paygate.jpg"></iframe>
    </div>
<?
} else {
    finish_order();
    //header("Location: http://".$_SERVER["SERVER_NAME"]."/".$GLOBALS['site']['code']."/".$GLOBALS['language']['code']."/shop/?shop_category=order&action=complete_order");
}
?>