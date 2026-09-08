<?php
/*$order_no_query  = "SELECT order_no FROM shop_sales_header ORDER BY order_no DESC LIMIT 1";
$order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
if (@mysqli_num_rows($order_no_result) == 1) {
    $order_no_array = @mysqli_fetch_array($order_no_result);
    $order_no       = $order_no_array["order_no"] + 1;
} else {
    $order_no = 100000;
}*/

$baseDir = dirname(dirname(dirname(__DIR__)));
//US Sales Tax ---
$logDir = $baseDir . DIRECTORY_SEPARATOR . 'logs';
$logFile = 'USSalesTax.log';
$logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
$logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
$processor = new \Monolog\Processor\PsrLogMessageProcessor();
$logFileHandler->pushProcessor($processor);
$logger = new \Monolog\Logger('USSalesTax', [$logFileHandler]);

$taxEnvDir = $baseDir . '/config/USSalesTax/';
if (file_exists($taxEnvDir . '.env') && is_file($taxEnvDir . '.env') && is_readable($taxEnvDir . '.env')) {
    $dotenvTax = new \Dotenv\Dotenv($taxEnvDir);
    $dotenvTax->load();
}
$taxJarRESTAPIConsumer = $IOCContainer->create(\DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer::class);
$vatMgr = $IOCContainer->create('$VATManager');
$shopPDO = $IOCContainer->create(\DynCom\dc\common\classes\PDOQueryWrapper::class);
$taxJarAdapter = new \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter($taxJarRESTAPIConsumer, $vatMgr, $shopPDO, $logger);
//US Sales Tax +++
/*if (isset($_SESSION['trans_id']) && $_SESSION['trans_id'] != '') {
    $query = "SELECT id FROM shop_sales_header WHERE payment_transaction_id = '" . $_SESSION['trans_id'] . "'";
    $header = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], $query));
    $query = "DELETE FROM shop_sales_line WHERE shop_sales_header_id = '" . $header['id'] . "'";
    mysqli_query($GLOBALS['mysql_con'], $query);
    $query = "DELETE FROM shop_sales_header WHERE id='" . $header['id'] . "'";
    mysqli_query($GLOBALS['mysql_con'], $query);
}*/
//Zahlart auslesen
$query = "SELECT *
		  FROM shop_payment_option
		  WHERE 
		        company = '" . $GLOBALS['shop']['company'] . "'
		    AND line_no = '" . $_SESSION['payment_line_no'] . "'
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
$t_id = get_payment_transaction_id();

if ($shipment_address["country"] == '') {
    $shipment_address["country"] = $GLOBALS['shop_language']['default_country_code'];
}
if ($invoice_address["country"] == '') {
    $invoice_address["country"] = $GLOBALS['shop_language']['default_country_code'];
}
$newsletter = 0;
if ($_SESSION["newsletter_subscribe"]) {
    $newsletter = 1;
}
if ($_SESSION["visitor_salutation"] == 0 || $_SESSION["visitor_salutation"] == $GLOBALS["tc"]["mr"]) {
    $_SESSION["visitor_salutation_title"] = $GLOBALS["tc"]["mr"];
} else {
    $_SESSION["visitor_salutation_title"] = $GLOBALS["tc"]["mrs"];
}


$dc_order = 0;
$coupon_discount_amount = $_SESSION['coupon']['coupon_discount_amount'];

//Anpassung Sonderversandgutschein ---
if ($_SESSION['shipping_cost'] <> $shipping_cost) {
    $shipping_cost = $_SESSION['shipping_cost'];
}
$shipping_coupon = 0;
$value_type = 0;
if ($_SESSION['coupon']['value_type'] == 3) {
    $value_type = $_SESSION['coupon']['value_type'];
    $shipping_coupon = 1;
}
///+++
$usTaxAmount = 0.00;
if ($_SESSION["dc_id"] <> '') {
    $dc = get_dc($_SESSION["dc_id"]);
    $coupon_discount_amount = $dc["amount"];
    $row = @mysqli_fetch_array($basketresult);
    $total = $row["amount"];
    //log_text("total - user_order_payment line 72: ".$total);
    $subtotal = $row["amount"];
    mysqli_data_seek($basketresult, 0);
    $dc_order = 1;
    $discount = 0;
    $discount_wo_coupon = 0;
    $markup = 0;

} else {
    //$subtotal = shop_get_basket_amount($GLOBALS['visitor']['id']);
    $subtotal = $currUserBasket->getBasketTotal();
    $basketID = $currUserBasket->getID();
    /* ---SUBSCRIPTION--- */
    if (array_key_exists('subscription_item', $GLOBALS) && $GLOBALS['subscription_item']) {
        $itemObj = $GLOBALS['subscription_item'];
        $subtotal = $itemObj->getLineAmount();
    }
    $basketInvoiceDiscountAmount = 0.00;
    /**
     * @var $currUserBasket \DynCom\dc\dcShop\interfaces\UserBasket
     * @var $appliedInvoiceDiscount \DynCom\dc\dcShop\classes\AppliedDiscount
     */
    foreach ($currUserBasket->getAppliedInvoiceDiscounts() as $appliedInvoiceDiscount) {
        if ($appliedInvoiceDiscount->getSourceType() !== \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON && $appliedInvoiceDiscount->getDiscountEvaluationType() === \DynCom\dc\dcShop\classes\GenericInvoiceDiscount::EVALUATION_TYPE_TAXABLE_DISCOUNT) {
            if ($appliedInvoiceDiscount->getDiscountValueType() == \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_VALUE_TYPE_PERCENT) {
                $basketInvoiceDiscountAmount += $subtotal * ($appliedInvoiceDiscount->getDiscountValue() / 100);
            } else {
                $basketInvoiceDiscountAmount += $appliedInvoiceDiscount->getDiscountedAmount();
            }
        }
    }
    $noOfItems = (int)basketNoOfPos();
    $noOfItems = $noOfItems ?: 1;
    $basketInvoiceDiscountPerItem = $basketInvoiceDiscountAmount / $noOfItems;
    /* +++SUBSCRIPTION+++ */
    //Small-quantity-charge
    $small_quantity_charge = ((($GLOBALS['shop']['small_quantity_charge'] > 0) && ($GLOBALS['shop']['small_quantity_charge_limit'] > $subtotal)) ? $GLOBALS['shop']['small_quantity_charge'] : 0);
    //Aufschläge
    $markup = $shipping_cost + $terms['payment_cost'] + $small_quantity_charge;
    //Rabatte zusammenrechnen für Rechnung
    $discount = $online_discount_amount + $invoice_discount_amount + $coupon_discount_amount + $basketInvoiceDiscountAmount;
    //Rabatte ohne Coupon
    $discount_wo_coupon = $online_discount_amount + $invoice_discount_amount + $basketInvoiceDiscountAmount;
    //total
    $total = $subtotal + $markup - $discount;
    //log_text("total - user_order_payment line 79: ".$total."\r\nsubtotal: ".$subtotal."\r\nshipping_cost: ".$shipping_cost."\r\npayment_cost: ".$terms["payment_cost"]."\r\ncoupon_disc: ".$coupon_discount_amount."\r\nonline_disc: ".$online_discount_amount);

    //US SALES TAX ---
    $taxBreakdown = $taxJarAdapter->getTaxBreakdownByBasketHeaderID($currUserBasket->getID());
    if ($taxBreakdown) {
        $usTaxAmount += $taxBreakdown->getTaxCollectable();
    }
    //US SALES TAX +++

}
//PH Billpay +++
//Versandkosten mit Mwst. brechnen
$shipping_cost_header = $shipping_cost;
if ($GLOBALS["shop"]["prices_including_vat"]) {

    set_vat_lines($GLOBALS['visitor']['id'], $GLOBALS['shop']['vat_bus_posting_group'], $_SESSION['coupon'], $discount_wo_coupon, $markup, $currUserBasket, $online_discount_amount);


    $shipping_cost_w_vat = $shipping_cost;
    $shipping_cost = $shipping_cost_w_vat - ($GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['shipping_cost_vat_amount']);
    $discount_total_w_vat = $discount;
    if ($dc["amount"] == '') {

        $bp_total_w_vat = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][3]['order_total_w_vat'];
        $total = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][3]['order_total_wo_vat'];
        /*
         * PH:
        $bp_total_w_vat = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['basket_total_w_vat'];

        $total          = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['basket_total_wo_vat'];
        */
        //log_text("total - user_order_payment line 96: ".$total."\r\nbptotalwvat: ".$bp_total_w_vat);

    } else {

        $bp_total_w_vat = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][3]['order_total_w_vat'];
        /*
         * PH:
        $bp_total_w_vat = $total;
        */
        //log_text("total - user_order_payment line 100: ".$total."\r\nbptotalwvat: ".$bp_total_w_vat);

    }
    //US SALES TAX ---
    $total += $usTaxAmount;
    $subtotal += $usTaxAmount;
    $bp_total_w_vat += $usTaxAmount;
    //US Sales Tax +++
} else {
    //nicht benötigt?
    $shipping_cost_w_vat = $shipping_cost * 1.19;
    $discount_total_w_vat = $discount * 1.19;
    if ($dc["amount"] == '') {
        $bp_total_w_vat = $total * 1.19;

    } else {
        $bp_total_w_vat = $total;
    }
}
//PH Billpay ---

if ($dc["amount"] <> '') {
    $bp_total_w_vat = $dc["amount"];
}

$no_order_complete = FALSE;
if ($bp_total_w_vat > 0 && $_SESSION['payment_line_no'] == 0) {
    $no_order_complete = TRUE;
}

if ($bp_total_w_vat == 0 && ($terms['checkout'] == '2' || $terms['checkout'] == '3' || $terms['checkout'] == '4' || $terms['checkout'] == '5' || $terms['checkout'] == '6' || $terms['checkout'] == '7' || $terms['checkout'] == '11')) {
    $_SESSION['payment_line_no'] = 0;
}

$salutation_sales_header = 0;
if ($_SESSION["visitor_salutation"] == $GLOBALS["tc"]["mr"]) {
    $salutation_sales_header = 1;
}

$local_email = (strlen($GLOBALS["shop_user"]["email"]) > 0) ? $GLOBALS["shop_user"]["email"] : $invoice_address["email"];

// update_insert = 1,
#$state_no_query = "SELECT state_no FROM shop_state WHERE state_code = '".$shipment_address["state_code"]."' LIMIT 1";
#$state_no_result = @mysqli_query($GLOBALS["mysql_con"],$state_no_query);
#$state_no = @mysqli_fetch_assoc($state_no_result);
#user_phone_no
#ship_to_telephone_no


$dt = new DateTime();
$orderDate = $dt->format('Y-m-d');
$state_no["state_no"] = $shipment_address["state_code"];

$salesHeader = [
    'company' => $GLOBALS['shop']['company'],
    'shop_code' => $GLOBALS['shop']['code'],
    'language_code' => $GLOBALS['shop_language']['code'],
    'shop_customer_id' => $GLOBALS["shop_customer"]["id"],
    'customer_no' => $GLOBALS["shop_customer"]["customer_no"],
    'shop_user_id' => $GLOBALS["shop_user"]["id"],
    'user_name' => mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["shop_user"]["name"]),
    'user_email' => $local_email,
    'user_phone_no' => mysqli_real_escape_string($GLOBALS['mysql_con'], $_SESSION['visitor_telephone']),
    'ship_to_name' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name"]),
    'ship_to_name_2' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name_2"]),
    'ship_to_address' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address"]),
    'ship_to_address_2' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address_2"]),
    'ship_to_post_code' => $shipment_address["post_code"],
    'ship_to_city' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["city"]),
    'ship_to_country' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["country"]),
    'ship_to_contact' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["contact"]),
    'ship_to_telephone_no' => mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["telephone"]),
    'bill_to_customer_no' => $GLOBALS["shop_customer"]["bill_to_customer_no"],
    'bill_to_name' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name"]),
    'bill_to_name_2' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name_2"]),
    'bill_to_address' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address"]),
    'bill_to_address_2' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address_2"]),
    'bill_to_post_code' => $invoice_address["post_code"],
    'bill_to_city' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["city"]),
    'bill_to_country' => mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["country"]),
    'order_date' => $orderDate,
    'your_reference' => mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_your_reference"]),
    'your_comment' => mysqli_real_escape_string($GLOBALS['mysql_con'], $_POST["input_your_comment"]),
    'subtotal' => $subtotal,
    'online_discount' => $online_discount,
    'online_discount_amount' => $online_discount_amount,
    'invoice_discount' => $invoice_discount,
    'invoice_discount_amount' => $invoice_discount_amount,
    'small_quantity_charge_amount' => $small_quantity_charge,
    'total' => $bp_total_w_vat,
    'requested_delivery_date' => datetosql($_POST['date']),
    'shipping_option_line_no' => $_SESSION['shipping_line_no'],
    'shipping_cost' => $shipping_cost_header,
    'payment_option_line_no' => $_SESSION['payment_line_no'],
    'payment_cost' => $_SESSION['payment_cost'],
    'payment_transaction_id' => $t_id,
    'currency_code' => $GLOBALS['shop_currency']['code'],
    'coupon_amount' => $coupon_discount_amount,
    'value_coupon' => $_SESSION['coupon']['value_coupon'],
    'coupon_code' => $_SESSION['coupon']['coupon_code'],
    'coupon_header_code' => $_SESSION['coupon']['code'],
    'shipping_coupon' => $shipping_coupon,
    'user_salutation' => $_SESSION["visitor_salutation"],
    'newsletter_registration' => $newsletter,
    'bank_account_no' => $_SESSION["account_no"],
    'bank_branch_no' => $_SESSION["bank_no"],
    'bank_name' => $_SESSION["bank_name"],
    'bon_check' => $_SESSION["creditworthiness"],
    'dc_order' => $dc_order,
    'birthday' => datetosql($_SESSION["visitor_birthday"]),
    'sur_name' => $_SESSION["visitor_surname"],
    'last_name' => $_SESSION["visitor_lastname"],
    'salutation_title' => $_SESSION["visitor_salutation_title"],
    'subscription_code' => '',
    'subscription_cust_line_no' => '',
    'to_nl_transfer' => 1
];

$custID = isset($GLOBALS['shop_customer']['id']) && $GLOBALS['shop_customer']['id'] > 0 ? $GLOBALS['shop_customer']['id'] : existing_customer_id_from_sales_header($salesHeader);
if ((empty($salesHeader['customer_id']) || empty($salesHeader['customer_no'])) && ($custID !== $salesHeader['customer_id']) && $custID > 0) {
    $customerRepository = $IOCContainer->create('DynCom\dc\dcShop\classes\CustomerRepository');
    $customer = $customerRepository->findByID($custID);
    $GLOBALS['shop_customer'] = $customer->getAllFieldsAsArray();
    $salesHeader['customer_id'] = $GLOBALS['shop_customer']['id'];
    $salesHeader['customer_no'] = $GLOBALS['shop_customer']['customer_no'];
}

/* ---SUBSCRIPTION--- */
if (array_key_exists('subscription_item', $GLOBALS) && $GLOBALS['subscription_item']) {

    $custLink = createSubscriptionCustomerLink($GLOBALS['curr_shop_configuration'], $subscriptionItem, $salesHeader);
    $salesHeader['subscription_code'] = $custLink->subscription_code;
    $salesHeader['subscription_cust_line_no'] = $custLink->line_no;

}
/* +++SUBSCRIPTION+++ */

$query_transaction = "start transaction;";

$birthday = $_SESSION["visitor_birthday"] ? "'" . $_SESSION["visitor_birthday"] . "'" : 'NULL';
$query = "    
    insert into shop_sales_header (
        company,
        shop_code,
        language_code,
        order_no,
        shop_customer_id,
        customer_no,
        shop_user_id,
        user_name,
        user_email,
        user_phone_no,
        ship_to_name,
        ship_to_name_2,
        ship_to_address,
        ship_to_address_2,
        ship_to_post_code,
        ship_to_city,
        ship_to_country,
        ship_to_contact,
        ship_to_telephone_no,
        bill_to_customer_no,
        bill_to_name,
        bill_to_name_2,
        bill_to_address,
        bill_to_address_2,
        bill_to_post_code,
        bill_to_city,
        bill_to_country,
        order_date,
        your_reference,
        your_comment,
        subtotal,
        online_discount,
        online_discount_amount,
        invoice_discount,
        invoice_discount_amount,
        small_quantity_charge_amount,
        total,
        requested_delivery_date,
        shipping_option_line_no,
        shipping_cost,
        payment_option_line_no,
        payment_cost,
        payment_transaction_id,
        currency_code,
        coupon_amount,
        value_coupon,
        coupon_code,
        coupon_header_code,
        shipping_coupon,
        user_salutation,
        newsletter_registration,
        bank_account_no,
        bank_branch_no,
        bank_name,
        bon_check,
        dc_order,
        sur_name,
        last_name,
        salutation_title,
        subscription_code,
        subscription_cust_line_no,
        to_nl_transfer,
        birthday
    )	
    SELECT
        '" . $GLOBALS['shop']['company'] . "',
        '" . $GLOBALS['shop']['code'] . "',
        '" . $GLOBALS['shop_language']['code'] . "',
        (CASE WHEN (SELECT count(*) from shop_sales_header) > 0 THEN (max(order_no) + 1) ELSE 10000 END),
        '" . (int)$GLOBALS["shop_customer"]["id"] . "',
        '" . $GLOBALS["shop_customer"]["customer_no"] . "',
        '" . (int)$GLOBALS["shop_user"]["id"] . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["shop_user"]["name"]) . "',
        '" . $local_email . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_SESSION['visitor_telephone']) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["name_2"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["address_2"]) . "',
        '" . $shipment_address["post_code"] . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["city"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["country"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["contact"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $shipment_address["telephone"]) . "',
        '" . $GLOBALS["shop_customer"]["bill_to_customer_no"] . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["name_2"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["address_2"]) . "',
        '" . $invoice_address["post_code"] . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["city"]) . "',
        '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $invoice_address["country"]) . "',
        NOW(),
        '" . $_POST["input_your_reference"] . "',
        '" . $_POST["input_your_comment"] . "',
        '" . (string)(float)$subtotal . "',
        '" . (string)(float)$online_discount . "',
        '" . (string)(float)$online_discount_amount . "',
        '" . (string)(float)$invoice_discount . "',
        '" . (string)(float)$invoice_discount_amount . "',
        '" . (string)(float)$small_quantity_charge . "',
        '" . (string)(float)$bp_total_w_vat . "',
        " . datetosql($_POST['date']) . ",
        '" . $_SESSION['shipping_line_no'] . "',
        '" . $shipping_cost_header . "',
        '" . $_SESSION['payment_line_no'] . "',
        '" . $_SESSION['payment_cost'] . "',
        '" . $t_id . "',
        '" . $GLOBALS['shop_currency']['code'] . "',
        '" . (string)(float)$coupon_discount_amount . "',
        '" . (string)(float)$_SESSION['coupon']['value_coupon'] . "',
        '" . $_SESSION['coupon']['coupon_code'] . "',
        '" . $_SESSION['coupon']['code'] . "',
        '" . $shipping_coupon . "',
        '" . (int)$_SESSION["visitor_salutation"] . "',
        '" . $newsletter . "',
        '" . $_SESSION["account_no"] . "',
        '" . $_SESSION["bank_no"] . "',
        '" . $_SESSION["bank_name"] . "',
        '" . $_SESSION["creditworthiness"] . "',
        '" . $dc_order . "',
        '" . $_SESSION["visitor_surname"] . "',
        '" . $_SESSION["visitor_lastname"] . "',
        '" . (int)$_SESSION["visitor_salutation"] . "',
        '" . $salesHeader['subscription_code'] . "',
        " . (int)$salesHeader['subscription_cust_line_no'] . ",
        1,
        " . $birthday . "
    FROM shop_sales_header;
";

$query_commit = "commit;";

//die($query_new);
if (@mysqli_query($GLOBALS['mysql_con'], $query_transaction)) {
    if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
        $sales_header_id = mysqli_insert_id($GLOBALS['mysql_con']);

        $order_no_query = "SELECT order_no FROM shop_sales_header WHERE id = " . $sales_header_id;
        $order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
        $order_no_array = @mysqli_fetch_array($order_no_result);
        $order_no = $order_no_array["order_no"];

        //---SUBSCRIPTION---
        if (isset($custLink)) {
            $custLink->setOrderNo($order_no);
            $custLinkRepository = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\SubscriptionCustomerLinkRepository');
            $custLinkRepository->updateSingle($custLink);
        }
        //+++SUBSCRIPTION+++

        $basketKeyToSalesLineIDMap = [];
        while ($basket_line = @mysqli_fetch_array($basketresult)) {

            if ($_SESSION["dc_id"] == '') {

                /**
                 * @var $entity \DynCom\dc\dcShop\classes\BasketEntity
                 */
                $entity = $currUserBasket->getItemByKey($basket_line['basket_entity_key']);

                $basket_line["package_for_variant_code"] = "";
                /** @var \DynCom\dc\dcShop\classes\GenericUserBasket $currUserBasket */

                if (!empty($basket_line["package_for_item_no"])) {
                    $packageItem = $currUserBasket->getItemByKey($basket_line["package_for_item_no"]);
                    $basket_line["package_for_item_no"] = $packageItem->getIdentifier();
                    $basket_line["package_for_variant_code"] = $packageItem->getSubIdentifier();
                }


                if (array_key_exists('subscription_item', $GLOBALS) && $GLOBALS['subscription_item']) {
                    $quantity = $basket_line["basket_quantity"];
                    $quantity = $quantity ?: 1.00;
                    $unitPrice = $basket_line["customer_price"];
                    $lineAmount = (int)$quantity * (float)$unitPrice;
                } else {
                    /**
                     * @var $entity \DynCom\dc\dcShop\classes\BasketEntity
                     */
                    $entity = $currUserBasket->getItemByKey($basket_line['basket_entity_key']);

                    $quantity = $entity->getQuantity();
                    $quantity = $quantity ?: 1.00;
                    $unitPrice = $entity->getUnitPrice();
                    $lineAmount = $entity->getLineAmount();
                }

                $discountAmount = $basketInvoiceDiscountPerItem;
                $quantity = $quantity ?: 1.00;
                $discountAmountPerUnit = $discountAmount / $quantity;
                $unitPrice -= $discountAmountPerUnit;
                $lineAmount -= $discountAmount;

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
						  unit_price = '" . $unitPrice . "',
						  quantity = '" . $quantity . "',
						  line_amount = '" . $lineAmount . "',
						  greeting_card_text = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $basket_line["greeting_card_text"]) . "',
						  package_for_item = '" . $basket_line["package_for_item_no"] . "',	
						  package_for_variant_code = '" . $basket_line["package_for_variant_code"] . "',
						  allow_invoice_disc = '" . $basket_line["allow_invoice_disc"] . "',
						  is_coupon_item = '" . $basket_line["is_coupon_item"] . "',
						  update_insert = 1";
                mysqli_query($GLOBALS['mysql_con'], $query);
                $cust_sales_line_id = mysqli_insert_id($GLOBALS['mysql_con']);
                $basketKeyToSalesLineIDMap[($basket_line['basket_entity_key'])] = $cust_sales_line_id;
                $customizationHash = $basket_line['customization_hash'];
                if ($customizationHash) {
                    /** @var \DynCom\dc\dcShop\classes\CustomizationService $customizationService */
                    $customizationService = $IOCContainer->create('DynCom\dc\dcShop\classes\CustomizationService');
                    $customizationData = $customizationService->getCustomizationDataFromHash($customizationHash);
                    foreach ($customizationData as &$row) {
                        unset($row['id']);
                        $row['sales_line_id'] = $cust_sales_line_id;
                        $row['update_insert'] = 1;
                        $row['to_delete'] = 0;
                    }
                    unset($row);
                    $customizationService->saveCustomizationSalesLines($customizationData);
                    mysqli_query($GLOBALS['mysql_con'], "UPDATE shop_sales_line SET customized = 1 WHERE id = '" . $cust_sales_line_id . "'");
                }
            }
        }

        if ($_SESSION["dc_id"] == '') {
            $taxJarAdapter->updateStoredTaxInfoForOrder($order_no, $basketID, $basketKeyToSalesLineIDMap);
        }

        @mysqli_query($GLOBALS['mysql_con'], $query_commit);


        $_SESSION['order_no'] = $order_no;


        if ($_SESSION["dc_id"] <> '') {
            finish_dc_order($_SESSION["dc_id"]);
        }

        $spacer["%order_no%"] = $_SESSION['order_no'];
        $spacer["%email%"] = $_SESSION["visitor_email"];
        $spacer["%anrede%"] = $_SESSION["visitor_salutation"];
        $spacer["%name%"] = $_SESSION["visitor_name"];
        $spacer["%order_lines%"] = "";
        $spacer["%payment_text%"] = "";
        $spacer["%invoice_amount%"] = $bp_total_w_vat;
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'order_mail_lines.inc.php';
        $spacer["%order_lines%"] .= ob_get_contents();
        ob_end_clean();



        if ($GLOBALS["shop_language"]["email_order_1_text_module"] <> '') {
            if ($terms["text_module_order_conf"] <> '') {
                $message = get_text_module(
                    $GLOBALS['shop']['company'],
                    $terms["text_module_order_conf"],
                    $spacer
                );
            } else {
                $message = get_text_module(
                    $GLOBALS['shop']['company'],
                    $GLOBALS["shop_language"]["email_order_1_text_module"],
                    $spacer
                );
            }
            $attachment_1 = get_text_module_attachment(
                $GLOBALS['shop']['company'],
                $GLOBALS["shop_language"]["email_order_1_text_module"]
            );
            $attachment_2 = get_text_module_attachment(
                $GLOBALS['shop']['company'],
                $GLOBALS["shop_language"]["email_order_1_text_module"],
                2
            );
            $subject = get_text_module(
                $GLOBALS['shop']['company'],
                $GLOBALS["shop_language"]["email_order_1_text_module"],
                $spacer,
                true
            );
            mail_create(
                $subject, $message, $GLOBALS["shop"]["email_sender"], $_SESSION["visitor_email"], "", "", true, $attachment_1, $GLOBALS['shop']['email_order_mail_1_copy'], $attachment_2, 1, $t_id
            );
        }

        //Parameter erzeugen wenn Zahlung über paygate

        $computopLanguageCodesKK = ["de" => "de", "al" => "al", "at" => "at", "cz" => "cz", "cs" => "cs", "dk" => "dk", "en" => "en", "fi" => "fi", "fr" => "fr", "gr" => "gr", "hu" => "hu", "it" => "it", "jp" => "jp", "nl" => "nl", "no" => "no", "pl" => "pl", "pt" => "pt", "ro" => "ro", "ru" => "ru", "es" => "es", "se" => "se", "sk" => "sk", "sl" => "sl", "tr" => "tr", "zh" => "zh"];
        $computopLanguageCodesPayPal = ["au" => "au", "de" => "de", "fr" => "fr", "it" => "it", "gb" => "gb", "es" => "es", "en" => "us"];

        if ($terms['checkout'] == '2' || $terms['checkout'] == '3' || $terms['checkout'] == '4' || $terms['checkout'] == '5') {
            require_once $baseDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
            //Parameter für paygate
            date_default_timezone_set('Europe/Berlin');
            $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
            $trans_id = "&TransID=" . $t_id;
            if ($terms['checkout_state'] != 1) {
                //SH: 25.11.13 Bei digitalen Gutschein eine
                $new_amount = $bp_total_w_vat;
                //$amount = "&Amount=".round($_SESSION['total_basket']*100,0);
                $amount = "&Amount=" . round($new_amount * 100, 0);
                $amount_hash = round($new_amount * 100, 0);
                $currency = "&Currency=EUR";
                if ($GLOBALS["shop_language"]["default_currency_code"] != "") {
                    $currency = "&Currency=" . $GLOBALS["shop_language"]["default_currency_code"];
                }
            } else {
                $amount = "&Amount=10"; //Angabe in kleinster Waehrungseinheit (z.B. Cent)
                $order_desc = "&OrderDesc=TEST:0000"; //testparameter
                //SH: 27.08.13 $currency hinzugefügt
                $amount_hash = 10;
                $currency = "&Currency=EUR";
                if ($GLOBALS["shop_language"]["default_currency_code"] != "") {
                    $currency = "&Currency=" . $GLOBALS["shop_language"]["default_currency_code"];
                }
            }

            $row['id'] = $currUserBasket->getID();
            $order_desc = "&Warenkorb-ID=" . $row['id'];
            $hmac = "&MAC=" . hash_hmac('sha256', '*' . $t_id . '*' . $merchant_id . '*' . $amount_hash . '*' . 'EUR', $GLOBALS['shop']['computop_password']);
            $url_failure = "&URLFailure=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_error.php";
            $url_notify = "&URLNotify=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_notify.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $url_success = "&URLSuccess=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_success.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $response = "&Response=Encrypt";
            $ref_nr = "&RefNr=" . $order_no;
            $userdata = "&UserData=" . session_id();
            if ($terms['checkout'] == '3' || $terms['checkout'] == '5') {
                $capture = "&Capture=Manual";
                $txtype = "&Txtype=Auth";
            } else {
                $capture = "&Capture=Auto";
                $txtype = "";
            }

            if ($_SESSION["dc_id"] == '') {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&FirstName=" . $_SESSION["visitor_name_shipping"];
                    $company = "&LastName=" . $_SESSION["visitor_company_shipping"];
                } else {
                    $name_arr = explode(" ", $_SESSION["visitor_name_shipping"]);
                    $name = "&FirstName=" . $_SESSION["visitor_surname"];
                    $company = "&LastName=" . $_SESSION["visitor_lastname"];
                }
                $address = "&AddrStreet=" . $_SESSION["visitor_user_street_shipping"];
                $city = "&AddrCity=" . $_SESSION["visitor_city_shipping"];
                $plz = "&AddrZIP=" . $_SESSION["visitor_post_code_shipping"];
                $country = "&AddrCountryCode=" . $_SESSION["visitor_country_shipping"];
            } else {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&FirstName=" . $_SESSION["visitor_name_shipping"];
                    $company = "&LastName=" . $_SESSION["visitor_company_shipping"];
                } else {
                    //SH: 06.12.13 Vor und Nachname sind ja schon getrennt da, des halb kein explode
                    $name = "&FirstName=" . $_SESSION["visitor_surname_shipping"];
                    $company = "&LastName=" . $_SESSION["visitor_lastname_shipping"];
                }
                $address = "&AddrStreet=" . $_SESSION["visitor_user_street_shipping"];
                $city = "&AddrCity=" . $_SESSION["visitor_city_shipping"];
                $plz = "&AddrZIP=" . $_SESSION["visitor_post_code_shipping"];
                //SH: 25.03.14 hier die $shipment_address["country"], da bei Österreich Bestellungen ein DE übergeben wird
                //$country="&AddrCountryCode=".$_SESSION["visitor_country_shipping"];
                $country = "&AddrCountryCode=" . $shipment_address["country"];
            }

            $state = "";
            if ($_SESSION["shipToState"] != "") {
                $state = "&AddrState=" . $_SESSION["shipToState"];
            }

            $plaintext = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata . $capture;
            $plaintext_paypal = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata . $capture . $txtype . $name . $company . $address . $city . $plz . $country . $state;

            //$len = strlen($plaintext);
            $len = mb_strlen($plaintext, mb_internal_encoding());
            //$len_paypal = strlen($plaintext_paypal);
            $len_paypal = mb_strlen($plaintext_paypal, mb_internal_encoding());

            $BlowFish = new ctBlowfish;
            $Data = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);
            $Data_paypal = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);
        } elseif ($terms['checkout'] == '9') { //sofortüberweisung
            require_once $baseDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
            //Parameter für paygate
            date_default_timezone_set('Europe/Berlin');
            $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
            $trans_id = "&TransID=" . $t_id;
            if ($terms['checkout_state'] != 1) {
                //SH: 25.11.13 Bei digitalen Gutschein eine
                $new_amount = $bp_total_w_vat;
                //$amount = "&Amount=".round($_SESSION['total_basket']*100,0);
                $amount = "&Amount=" . round($new_amount * 100, 0);
                $amount_hash = round($new_amount * 100, 0);
                $currency = "&Currency=EUR";
                $order_desc = "&OrderDesc=" . $order_no;
            } else {
                $amount = "&Amount=10"; //Angabe in kleinster Waehrungseinheit (z.B. Cent)
                $order_desc = "&OrderDesc=TEST:0000"; //testparameter
                //SH: 27.08.13 $currency hinzugefügt
                $amount_hash = 10;
                $currency = "&Currency=EUR";
            }
            $row['id'] = $currUserBasket->getID();
            $order_desc = "&Warenkorb-ID=" . $row['id'];
            $hmac = "&MAC=" . hash_hmac('sha256', '*' . $t_id . '*' . $merchant_id . '*' . $amount_hash . '*' . 'EUR', $GLOBALS['shop']['computop_password']);
            $url_failure = "&URLFailure=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_error.php";
            $url_notify = "&URLNotify=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_notify.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $url_success = "&URLSuccess=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_success.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $response = "&Response=Encrypt";
            $ref_nr = "&RefNr=" . $order_no;
            $userdata = "&UserData=" . session_id();

            if ($_SESSION["dc_id"] == '') {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&FirstName=" . $_SESSION["visitor_name_shipping"];
                    $company = "&LastName=" . $_SESSION["visitor_company_shipping"];
                } else {
                    $name_arr = explode(" ", $_SESSION["visitor_name_shipping"]);
                    $name = "&FirstName=" . $_SESSION["visitor_surname"];
                    $company = "&LastName=" . $_SESSION["visitor_lastname"];
                }
                $address = "&AddrStreet=" . $_SESSION["visitor_user_street_shipping"];
                $city = "&AddrCity=" . $_SESSION["visitor_city_shipping"];
                $plz = "&AddrZIP=" . $_SESSION["visitor_post_code_shipping"];
                $country = "&AddrCountryCode=" . $_SESSION["visitor_country_shipping"];
            } else {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&FirstName=" . urlencode($_SESSION["visitor_name_shipping"]);
                    $company = "&LastName=" . urlencode($_SESSION["visitor_company_shipping"]);

                } else {
                    //SH: 06.12.13 Vor und Nachname sind ja schon getrennt da, des halb kein explode
                    $name = "&FirstName=" . urlencode($_SESSION["visitor_surname_shipping"]);
                    $company = "&LastName=" . urlencode($_SESSION["visitor_lastname_shipping"]);
                }
                $address = "&AddrStreet=" . urlencode($_SESSION["visitor_user_street_shipping"]);
                $city = "&AddrCity=" . urlencode($_SESSION["visitor_city_shipping"]);
                $plz = "&AddrZIP=" . urlencode($_SESSION["visitor_post_code_shipping"]);
                //SH: 25.03.14 hier die $shipment_address["country"], da bei Österreich Bestellungen ein DE übergeben wird
                //$country="&AddrCountryCode=".$_SESSION["visitor_country_shipping"];
                $country = "&AddrCountryCode=" . urlencode($shipment_address["country"]);
            }

            $plaintext = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata . $capture . $txtype . $name . $company . $address . $city . $plz . $country;

            //$len = strlen($plaintext);
            $len = mb_strlen($plaintext, mb_internal_encoding());

            $BlowFish = new ctBlowfish;

            $Data = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);

        } elseif ($terms['checkout'] == '10' || $terms['checkout'] == '11') { //paydirekt
            require_once $baseDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
            //Parameter für paygate
            date_default_timezone_set('Europe/Berlin');
            $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
            $trans_id = "&TransID=" . $t_id;
            if ($terms['checkout_state'] != 1) {
                //SH: 25.11.13 Bei digitalen Gutschein eine
                $new_amount = $bp_total_w_vat;
                //$amount = "&Amount=".round($_SESSION['total_basket']*100,0);
                $amount = "&Amount=" . round($new_amount * 100, 0);
                $amount_hash = round($new_amount * 100, 0);
                $currency = "&Currency=EUR";
            } else {
                $amount = "&Amount=10"; //Angabe in kleinster Waehrungseinheit (z.B. Cent)
                $order_desc = "&OrderDesc=TEST:0000"; //testparameter
                //SH: 27.08.13 $currency hinzugefügt
                $amount_hash = 10;
                $currency = "&Currency=EUR";
            }
            $row['id'] = $currUserBasket->getID();
            $order_desc = "&Warenkorb-ID=" . $row['id'];
            $hmac = "&MAC=" . hash_hmac('sha256', '*' . $t_id . '*' . $merchant_id . '*' . $amount_hash . '*' . 'EUR', $GLOBALS['shop']['computop_password']);
            $url_failure = "&URLFailure=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_error.php";
            $url_notify = "&URLNotify=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_notify.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $url_success = "&URLSuccess=https://" . $_SERVER['SERVER_NAME'] . "/module/dcshop/b2c/order_success.php?shopdata=" . $GLOBALS["shop"]["company"] . "|" . $GLOBALS["shop"]["code"] . "|" . $GLOBALS["shop_language"]["code"];
            $response = "&Response=Encrypt";
            $ref_nr = "&RefNr=" . $order_no;
            $userdata = "&UserData=" . session_id();
            if ($terms['checkout'] == '11') {
                $capture = "&Capture=Manual";
                $txtype = "&Txtype=Auth";
            } else {
                $capture = "&Capture=Auto";
                $txtype = "";
            }


            if ($_SESSION["dc_id"] == '') {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&sdFirstName=" . $_SESSION["visitor_name_shipping"];
                    $company = "&sdLastName=" . $_SESSION["visitor_company_shipping"];
                } else {
                    $name_arr = explode(" ", $_SESSION["visitor_name_shipping"]);
                    $name = "&sdFirstName=" . $_SESSION["visitor_surname"];
                    $company = "&sdLastName=" . $_SESSION["visitor_lastname"];
                }
                $address = "&sdStreet=" . $_SESSION["visitor_user_street_shipping"];
                $city = "&sdCity=" . $_SESSION["visitor_city_shipping"];
                $plz = "&sdZIP=" . $_SESSION["visitor_post_code_shipping"];
                $country = "&sdCountryCode=" . $_SESSION["visitor_country_shipping"];
            } else {
                if ($_SESSION['visitor_company_shipping'] != "") {
                    $name = "&sdFirstName=" . $_SESSION["visitor_name_shipping"];
                    $company = "&sdLastName=" . $_SESSION["visitor_company_shipping"];
                } else {
                    //SH: 06.12.13 Vor und Nachname sind ja schon getrennt da, des halb kein explode
                    $name = "&sdFirstName=" . $_SESSION["visitor_surname_shipping"];
                    $company = "&sdLastName=" . $_SESSION["visitor_lastname_shipping"];
                }
                $address = "&sdStreet=" . $_SESSION["visitor_user_street_shipping"];
                $address .= "&sdStreetNr=" . $_SESSION["visitor_user_street_no_shipping"];

                $city = "&sdCity=" . $_SESSION["visitor_city_shipping"];
                $plz = "&sdZIP=" . $_SESSION["visitor_post_code_shipping"];
                //SH: 25.03.14 hier die $shipment_address["country"], da bei Österreich Bestellungen ein DE übergeben wird
                //$country="&AddrCountryCode=".$_SESSION["visitor_country_shipping"];
                $country = "&sdCountryCode=" . $shipment_address["country"];
            }

            $shopId = '&ShopID=' . $GLOBALS['shop']['paydirekt_api_key'];

            $shAmount = '';
            if ($shipping_cost > 0) {
                $shAmount = '&shAmount=' . $shipping_cost;
            }

            $shoppingBasketAmount = $subtotal;
            if ($terms['payment_cost'] > 0) {
                $shoppingBasketAmount = $shoppingBasketAmount + $terms['payment_cost'];
            }
            if ($small_quantity_charge > 0) {
                $shoppingBasketAmount = $shoppingBasketAmount + $small_quantity_charge;
            }

            $shoppingBasketAmount = '&ShoppingBasketAmount=' . $shoppingBasketAmount; //inkl. Zahlungsgebühr


            $plaintext = "MerchantID=" . $merchant_id . $trans_id . $amount . $currency . $url_success . $url_failure . $url_notify . $userdata . $order_desc . $response . $ref_nr . $userdata . $capture . $txtype . $name . $company . $address . $city . $plz . $country . $shopId . $shoppingBasketAmount . $shAmount;


            //$len = strlen($plaintext);
            $len = mb_strlen($plaintext, mb_internal_encoding());

            $BlowFish = new ctBlowfish;
            $Data = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);
        } elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '6' || $terms['checkout'] == '7')) { //Rechnung oderLastschrift per BillPay
            require_once $baseDir . DIRECTORY_SEPARATOR . 'plugins/paygate/includes/function.inc.php';
            //Parameter für paygate
            if ($_SESSION['total_basket'] > 0 || $dc['id'] <> '') {
                date_default_timezone_set('Europe/Berlin');
                $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
                $trans_id = "&TransID=" . $_SESSION['trans_id'];
                if ($dc['id'] <> '') {
                    $amount = "&Amount=" . round($dc['amount'] * 100, 0);
                } else {
                    $amount = "&Amount=" . round($_SESSION['total_basket'] * 100, 0);
                }
                //$amount = "&Amount=10";
                $currency = "&Currency=EUR";
                $language = "&Language=de";
                $new = "&NewCustomer=guest";
                switch ($terms['checkout']) {
                    case '7':
                        $action = '&BillPayAction=2';
                        break;
                    case '6':
                        $action = '&BillPayAction=1';
                        break;
                }
                $gtc = "&GtcValue=yes";
                /*($_SESSION["visitor_salutation"]) ? ($salutation = $GLOBALS["tc"]["mr"]) : ($salutation = $GLOBALS["tc"]["mrs"]);*/
                $salutation = "&bdSalutation=" . $_SESSION["visitor_salutation"];
                $name_arr = explode(" ", $invoice_address["name"]);
                $fname = "&bdFirstName=" . $_SESSION["visitor_surname"];
                $lname = "&bdLastName=" . $_SESSION["visitor_lastname"];


                $street_arr = preg_split('~([^\d]*) (.*)~', $invoice_address["address"], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                $street = "&bdStreet=" . $invoice_address["address_street"];
                $street_nr = "&bdStreetNr=" . $invoice_address["address_no"];

                $zip = "&bdZip=" . $invoice_address["post_code"];
                $city = "&bdCity=" . $invoice_address["city"];
                switch ($invoice_address["country"]) {

                    case 'DE':
                        $country_code_computop = "DEU";
                        break;
                    case 'AT':
                        $country_code_computop = "AUT";
                        break;

                }
                $country = "&bdCountryCode=" . $country_code_computop;
                $email = "&EMail=" . $invoice_address["email"];
                $use_bill_as_ship = "&UseBillingData=" . "yes";
                $ref_nr = "&RefNr=" . $order_no;


                $dateofbirth = str_replace('\'', '', datetosql($_SESSION["visitor_birthday"]));
                $dateofbirth = "&DateOfBirth=" . $dateofbirth;
                $ip = '&IPAddr=' . $_SERVER["REMOTE_ADDR"];

                mysqli_data_seek($basketresult, 0);
                $itemlist = "&ArticleList=";
                /** @var \DynCom\dc\dcShop\classes\VATManager $vatManager */
                $vatManager = $GLOBALS["IOC"]->resolve('DynCom\dc\dcShop\classes\VATManager');
                if ($dc["id"] <> '') {
                    $itemlist .= str_replace(array(';', '+'), '', $GLOBALS["shop"]["gl_account_coupons"]) . ";" . number_format(1, 0, '', '') . ";Wertgutschein;'';" . number_format(($dc['amount']) * 100, 0, '', '') . ";" . number_format(($dc['amount']) * 100, 0, '', '') . "+";
                } else {
                    while ($basket_line = mysqli_fetch_assoc($basketresult)) {
                        $vatPercent = $vatManager->getVATPercentForProdPostingGroup($basket_line['vat_prod_posting_group']);
                        $basketLineDescription = str_replace(array(';', '+', '&'), "", $basket_line['description']);
                        $itemPrice = round((($basket_line['customer_price'] / (100 + $vatPercent)) * 100), 2);
                        $itemPriceWithVat = $basket_line['customer_price'];
                        $itemlist .= str_replace(array(';', '+'), '', $basket_line["item_no"]) . ";" . number_format($basket_line["basket_quantity"], 0, '', '') . ";" . $basketLineDescription . ";'';" . number_format($itemPrice * 100, 0, '', '') . ";" . number_format(($itemPriceWithVat) * 100, 0, '', '') . "+";
                    }
                }
                $itemlist = substr($itemlist, 0, -1);

                //Order_Desc füllen

                //Gebühren ohne/mit höchstem MwSt-Satz
                //$added_costs_total = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['shipping_cost_amount'] + $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['payment_cost_amount'] + $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['small_quantity_amount'];
                //$added_costs_total_wo_vat = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['shipping_cost_amount_wo_vat'] + $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['payment_cost_amount_wo_vat'] + $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][0]['small_quantity_amount_wo_vat'];
                $added_costs_total = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][3]['order_total_markup_w_vat'];
                $added_costs_total_wo_vat = $GLOBALS['vat_order_visitor_' . $GLOBALS['visitor']['id']][3]['order_total_markup_wo_vat'];

                $order_desc = "&OrderDesc=" . $_SESSION["shipping_description"] . ";" .
                    number_format($added_costs_total_wo_vat * 100, 0, '', '') . ";" .
                    number_format($added_costs_total * 100, 0, '', '') . ";" .
                    number_format($discount_total * 100, 0, '', '') . ";" .
                    number_format($discount_total_w_vat * 100, 0, '', '') . ";" .
                    number_format($total * 100, 0, '', '') . ";" .
                    number_format($bp_total_w_vat * 100, 0, '', '');

                /*
                - Versandkostenname, alphanum., max. 50stellig
                - Versandkosten netto, numerisch, 7stellig
                - Versandkosten brutto, numerisch, max. 7stellig
                - Rabatt netto, numerisch, max. 7stellig
                - Rabatt brutto, numerisch, max. 7stellig
                - Gesamtpreis netto, numerisch, max. 7stellig
                - Gesamtpreis brutto, numerisch, max. 7stellig
                */

                $acc_owner = "";
                $acc_nr = "";
                $acc_iban = "";
                if ($terms['checkout'] == "7") {
                    $acc_owner = "&AccOwner=" . $invoice_address["name"];
                    $acc_nr = "&BIC=" . $_SESSION["account_no"];
                    $acc_iban = "&IBAN=" . $_SESSION["bank_no"];
                }
                $plaintext = "MerchantID=" . $merchant_id . $trans_id . $dateofbirth . $ref_nr . $ip . $amount . $currency . $language . $new . $action . $gtc . $salutation . $fname . $lname . $street . $street_nr . $zip . $city . $country . $email . $use_bill_as_ship . $itemlist . $order_desc;
                $plaintext .= $acc_owner . $acc_nr . $acc_iban;

                //$plaintext = utf8_decode($plaintext);

                $fp = fopen(rtrim(dirname(dirname(dirname(__DIR__))), '/') . "/userdata/logfile_payment.txt", "a+b");
                fwrite($fp, "\r\n" . $plaintext . "\r\n");
                fclose($fp);

                //$len = strlen($plaintext);
                $len = mb_strlen($plaintext, mb_internal_encoding());

                $BlowFish = new ctBlowfish;
                $Data = $BlowFish->ctEncrypt($plaintext, $len, $GLOBALS['shop']['computop_password']);
                //TODO: Alle Pflichtparameter füllen
            }
        } // Payolution ---
        elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '17' || $terms['checkout'] == '18')) {
            // Ratenkauf (17): Akzeptanzprüfung vor Kaufabschluss mit Übergabe der Ratendauer
            // Rechnungskauf/Lastschrift (18): Akzeptanzprüfung vor Kaufabschluss

            if ($terms['checkout'] == "18") {
                $paytype = "INSTALLMENT";
            } else {
                $paytype = "INVOICE";
            }

            // PreAuthorization
            $response = payolution_calculation_or_precheck($paytype, "PA", $invoice_address, $_SESSION['total_basket'], $order_no, $t_id);
            if ($response["Status"] == "OK") {
                $_SESSION['pay_id'] = $response["PayID"];
                $_SESSION['payment_reference'] = $response["paymentpurpose"]; // Verwendungszweck für die Überweisung
                finish_order();
            } else {
                $creditworthiness = FALSE;
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_2.inc.php';
            }
        } elseif ($terms['checkout'] == '20' || $terms['checkout'] == '21') {
            // PayPal Express

            $payID = $_SESSION['paypal_express_PayID'];
            $transID = $_SESSION['paypal_express_TransID'];
            $refrenseNum = $order_no;
            $basketAmount = $bp_total_w_vat;

            $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
            $amount = "&Amount=" . round($basketAmount * 100, 0);
            $currency = "&Currency=EUR";

            $ref_nr = "&RefNr=" . $refrenseNum;
            $trans_id = "&TransID=" . $transID;

            $order_desc = "&OrderDesc=".$GLOBALS['site']['name'].", ".$GLOBALS['tc']['order_no']. ' '.$order_no;
            $plaintext_paypal = "MerchantID=" . $merchant_id . "&PayID=" . $payID . $trans_id . $amount . $currency . $ref_nr ;

            $len_paypal = strlen($plaintext_paypal);
            //$len_paypal = mb_strlen($plaintext_paypal, mb_internal_encoding());

            $BlowFish = new ctBlowfish;
            $Data_paypal = $BlowFish->ctEncrypt($plaintext_paypal, $len_paypal, $GLOBALS['shop']['computop_password']);

        }
        elseif ($terms['checkout'] == '11' )
        {
            // Amazon Pay

            // set order data and confirm it because may be he add to or remove from the basket
            // -----------------4 . Set Order Details and Confirm Order--------------------------
            // -- After this step the order can no be changed so its the last step before paying

            $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
            $orderDesc = "&OrderDesc= ".$GLOBALS['tc']['user_order']." Nr. ".$order_no;
            $eventToken = "&EventToken=SCO";

            $payIDValue = $_SESSION['amazon_PayID'];
            $trasnactionID = $_SESSION['trans_id'];
            $trans_id = "&TransID=" . $trasnactionID;
            $payID = "&PayID=" . $payIDValue;
            $storeName = "&StoreName=".$GLOBALS["site"]["name"];

            $orderRefrenceID = "&OrderReferenceID=". $_SESSION['amazon_order_reference_id'];


            $new_amount = $bp_total_w_vat;
            $amount = "&Amount=" . round($new_amount * 100, 0);
            $amount_hash = round($new_amount * 100, 0);

            $amountValue = $amount_hash;
            $amount = "&Amount=" . $amount_hash;

            $currencyValue = $_SESSION['amazon_payment_currency'];
            $currency = "&Currency=" . $currencyValue;
            $hmac = computop_get_mac($amountValue, $currencyValue, $merchant_id,  $GLOBALS['shop']['computop_hmac_key'], $trasnactionID, $payIDValue);
            $hmac = "&MAC=" . $hmac;

            $plaintext_amazon = "MerchantID=" . $merchant_id . $trans_id . $payID . $hmac . $orderRefrenceID .
                $eventToken . $amount . $currency . $storeName;

            $BlowFish = new ctBlowfish;
            $len_amazon = mb_strlen($plaintext_amazon, mb_internal_encoding());
            $Data_amazon = $BlowFish->ctEncrypt($plaintext_amazon, $len_amazon, $GLOBALS['shop']['computop_password']);


            $payGateResponse = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_amazon . '&Data=' . $Data_amazon);


            $logDir2 = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
            $logFile2 = 'PaymentNotify.log';
            $logFilePath2 = $logDir . DIRECTORY_SEPARATOR . $logFile2;
            $logFileHandler2 = new \Monolog\Handler\RotatingFileHandler($logFilePath2, 10, LOG_INFO);
            $processor2 = new \Monolog\Processor\PsrLogMessageProcessor();
            $logFileHandler2->pushProcessor($processor2);
            $logger2 = new \Monolog\Logger('PaymentNotify', [$logFileHandler2]);

            $logger2->info('Paygate Amazon Set Order Detials and Confirm Order Resquest' . $plaintext_amazon);
            $logger2->info('Paygate Amazon Set Order Detials and Confirm Order Resquest: https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_amazon . '&Data=' . $Data_amazon);

            $payGateResponse = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_amazon . '&Data=' . $Data_amazon);


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



            $logger2->info('Paygate Amazon Set Order Detials and Confirm Order Response Trans ID: ' . $paygate_response['TransID']);
            $logger2->info('Paygate Amazon Set Order Detials and Confirm Order Response Pay ID: ' . $paygate_response['PayID']);
            $logger2->info('Paygate Amazon Set Order Detials and Confirm Order Response Status: ' . $paygate_response['Status']);
           // $logger2->info('Paygate  Amazon Set Order Detials and Confirm Order Response Response: ' .var_dump($paygate_response));


            // ------5-------------- Payment Finalize ---------------------------
            //
            //
            $shopOrderNumber = "&RefNr=".$order_no;
            $eventToken = "&EventToken=ATH";

            $capture = "&Capture=AUTO";

            $textDisplayedOnUserPaymentAccountHistory = "&ChDesc=".$GLOBALS['site']['name'];
            $plaintext_amazon = "MerchantID=" . $merchant_id . $trans_id . $payID . $hmac . $capture.
                $eventToken . $amount . $currency;

            $len_amazon = mb_strlen($plaintext_amazon, mb_internal_encoding());
            $Data_amazon = $BlowFish->ctEncrypt($plaintext_amazon, $len_amazon, $GLOBALS['shop']['computop_password']);

            $logger->info('Paygate Amazon Set Authorize Payment');
            $logger->info('Data = https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_amazon . '&Data=' . $Data_amazon);


        }
        // Payolution +++
        //GLOBALS in Session speichern!
        $_SESSION['site'] = $GLOBALS['site']['code'];
        $_SESSION['is_unique_site'] = $GLOBALS['site']['is_unique_site'];
        $_SESSION['language'] = $GLOBALS['language']['code'];
        $_SESSION['shop'] = $GLOBALS['shop']['code'];
        $_SESSION['shop_language'] = $GLOBALS['shop_language']['code'];
        ?>
        <ul class="processbar">
            <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_address"] ?>
                <div class="arrow"></div>
                <div class="arrow-border"></div>
            </li>
            <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_payment"] ?>
                <div class="arrow"></div>
                <div class="arrow-border"></div>
            </li>
            <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_check"] ?></li>
        </ul>
        <?

        $language = "&Language=de";
        if (array_key_exists($GLOBALS["language"]["code"], $computopLanguageCodesKK)) {
            $language = "&Language=" . $computopLanguageCodesKK[$GLOBALS["language"]["code"]];
        }

        $languagePayPal = "&Language=de";
        if (array_key_exists($GLOBALS["language"]["code"], $computopLanguageCodesPayPal)) {
            $languagePayPal = "&Language=" . $computopLanguageCodesPayPal[$GLOBALS["language"]["code"]];
        }

        //Weiterleiten zu Paypal
        if ($bp_total_w_vat > 0 && ($terms['checkout'] == '4' || $terms['checkout'] == '5')) {
            ?>
            <script type="text/javascript">
                window.location.href = "https://www.computop-paygate.com/paypal.aspx?MerchantID=<?=$merchant_id ?>&Len=<?=$len_paypal ?>&Data=<?=$Data_paypal ?><?= $languagePayPal ?>";
            </script>
            <?
            //header('Location: https://www.computop-paygate.com/paypal.aspx?MerchantID='.$merchant_id.'&Len='.$len_paypal.'&Data='.$Data_paypal);
        } elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '2' || $terms['checkout'] == '3')) {
            ?>
            <div style="text-align:center;">
                <iframe width="100%" height="650px" scrolling="no" frameborder="0"
                        src="https://www.computop-paygate.com/payssl.aspx?MerchantID=<?= $merchant_id ?>&Len=<?= $len ?>&Data=<?= $Data ?>&Template=ct_responsive<?= $language ?>"></iframe>
            </div>
            <?
        } elseif ($terms['checkout'] == '9') { //sofortüberweisung.de
            ?>
            <script type="text/javascript">
                window.location.href = "https://www.computop-paygate.com/sofort.aspx?MerchantID=<?=$merchant_id ?>&Len=<?=$len ?>&Data=<?=$Data ?>";
            </script>
            <?
        } elseif ($terms['checkout'] == '10' || $terms['checkout'] == '11') { //paydirekt
            ?>
            <script type="text/javascript">
                window.location.href = "https://www.computop-paygate.com/paydirekt.aspx?MerchantID=<?=$merchant_id ?>&Len=<?=$len ?>&Data=<?=$Data ?>";
            </script>
            <?
        } // Payolution ---
        elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '17' || $terms['checkout'] == '18')) {


        } // Payolution +++
        elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '6' || $terms['checkout'] == '7')) { //Request an Computop senden und Antwort auswerten (BillPay)
            if ($_SESSION['total_basket'] > 0 || $dc['id'] <> '') {
                //TODO: Request an Paygate senden und Antwort auswerten
                $html = file_get_contents('https://www.computop-paygate.com/Billpay.aspx?MerchantID=' . $merchant_id . '&Len=' . $len . '&Data=' . $Data);
                $html_parts = explode('&', $html);
                if (count($html_parts) == 2) {
                    $len_parts = explode('=', $html_parts[0]);
                    $len = $len_parts[1];
                    $data_parts = explode('=', $html_parts[1]);
                    $data = $data_parts[1];
                    $BlowFish = new ctBlowfish;
                    $data_decrypted = $BlowFish->ctDecrypt($data, $len, $GLOBALS["shop"]["computop_password"]);
                    $parts = explode('&', $data_decrypted);
                    if (count($parts) > 0) {
                        foreach ($parts as $part) {
                            $x = explode('=', $part);
                            $_SESSION["PAYMENT"][$x[0]] = $x[1];
                            // Log-Datei
                            $fp = fopen(rtrim(dirname(dirname(dirname(__DIR__))), '/') . "/userdata/logfile_payment.txt", "a+b");
                            fwrite($fp, "\r\n" . $x[0] . "=" . $x[1] . "\r\n");
                            fclose($fp);
                        }
                        if ($_SESSION["PAYMENT"]["Status"] == "OK") {
                            $_SESSION["pay_id"] = $_SESSION["PAYMENT"]["PayID"];
                            finish_order();
                        } else {
                            // Zahlung fehlgeschlagen
                            if ($_SESSION["PAYMENT"]["BpStatus"] == 'APPROVED') {
                                // Nicht an der Bonität gescheitert
                                $_GET["payment_error"] = 1;
                                require_once __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_3.inc.php';
                            } else {
                                // Keine Bonität, zurück auf Step 2 und Fehlermeldung anzeigen
                                $creditworthiness = FALSE;

                                require_once("user_order_step_2.inc.php");
                            }
                        }
                    } else {
                        echo 'Falsches Antwortformat';
                    }
                } else {
                    echo "Falsches Antwortformat";
                }
            } else {
                finish_order();
            }
        } elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '20' || $terms['checkout'] == '21') ) {
            // PayPal Express
            $result = file_get_contents("https://www.computop-paygate.com/PayPalcomplete.aspx?MerchantID=" . $merchant_id . "&Len=" . $len_paypal . "&Data=" . $Data_paypal);

            $dataArray = explode("&", $result);

            foreach ($dataArray as $val) {
                $tmp = explode('=', $val);
                $finalArray[$tmp[0]] = $tmp[1];
            }


            $BlowFish = new ctBlowfish;

            $data_decrypted = $BlowFish->ctDecrypt($finalArray["Data"], $finalArray["Len"], $GLOBALS['shop']["computop_password"]);

            $parts = explode("&", $data_decrypted);
            foreach ($parts as $part) {
                $x = explode('=', $part);
                $paygate_response[$x[0]] = $x[1];
            }

            $logDir2 = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
            $logFile2 = 'PaymentNotify.log';
            $logFilePath2 = $logDir . DIRECTORY_SEPARATOR . $logFile2;
            $logFileHandler2 = new \Monolog\Handler\RotatingFileHandler($logFilePath2, 10, LOG_INFO);
            $processor2 = new \Monolog\Processor\PsrLogMessageProcessor();
            $logFileHandler2->pushProcessor($processor2);
            $logger2 = new \Monolog\Logger('PaymentNotify', [$logFileHandler2]);

            $logger2->info('Date PayPal Express Order Compelte Response  decrypted: ' . $data_decrypted);
            $logger2->info('Paygate Response Trans ID: ' . $paygate_response['TransID']);
            $logger2->info('Paygate Response Pay ID: ' . $paygate_response['PayID']);
            $logger2->info('Paygate Response Status: ' . $paygate_response['Status']);


            if ($paygate_response["Status"] == "OK" ) {

                $_SESSION['pay_id'] = $paygate_response['PayID'];

                $prepStatement = "SELECT * FROM shop_sales_header WHERE payment_transaction_id =:transaction_id";

                $params = [
                    [':transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                ];

                $shopPdo = $GLOBALS["shopPdo"];
                $shopPdo->setQuery($prepStatement);
                $shopPdo->prepareQuery();
                $shopPdo->bindParameters($params);
                $shopPdo->executePreparedStatement();
                $resultArraySalesHeader = $shopPdo->getResultArray();

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
                    $shopPdo->setQuery($prepStatement);
                    $shopPdo->prepareQuery();
                    $shopPdo->bindParameters($params);
                    $shopPdo->executePreparedStatement();
                    $resultArrayPaymentOption = $shopPdo->getResultArray();
                    $terms = $resultArrayPaymentOption[0];


                    $prepStatement = "
                    UPDATE shop_sales_header 
                    SET update_insert = 1,
                          update_notify = 1,
                          order_error = 0,
                          process_payment = 1, 
                          pay_id = :pay_id 
                    WHERE payment_transaction_id =:payment_transaction_id
				  ";

                    $params = [
                        [':pay_id', $paygate_response['PayID'], PDO::PARAM_STR],
                        [':payment_transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                    ];
                    $shopPdo->setQuery($prepStatement);
                    $shopPdo->prepareQuery();
                    $shopPdo->bindParameters($params);
                    $shopPdo->executePreparedStatement();

                    $prepStatement = "
                    UPDATE main_mail_log
                    SET delayed_send = 0,
                        payment_transaction_id = ''
                    WHERE payment_transaction_id =:payment_transaction_id AND delayed_send = 1
				  ";

                    $params = [
                        [':payment_transaction_id', $paygate_response['TransID'], PDO::PARAM_STR],
                    ];
                    $shopPdo->setQuery($prepStatement);
                    $shopPdo->prepareQuery();
                    $shopPdo->bindParameters($params);
                    $shopPdo->executePreparedStatement();

                    if ($sales_header['value_coupon'] != 0) {

                        //ToDo Check if update is needed
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
                        $shopPdo->setQuery($prepStatement);
                        $shopPdo->prepareQuery();
                        $shopPdo->bindParameters($params);
                        $shopPdo->executePreparedStatement();

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
                        $shopPdo->setQuery($prepStatement);
                        $shopPdo->prepareQuery();
                        $shopPdo->bindParameters($params);
                        $shopPdo->executePreparedStatement();

                    }
                    mail_send();
                }
                $redirectUrl = "https://".$_SERVER["SERVER_NAME"]."/".customizeUrl()."/order/complete_order/";
                headerFunctionBridge('Location: '.$redirectUrl);

                ?>

                <script type="text/javascript">
                    parent.location.href = '<? echo $redirectUrl; ?>';
                </script>
                <?


            }

        }elseif ($bp_total_w_vat > 0 && ($terms['checkout'] == '11' ) ) {
            // Amazon Pay


            $result = file_get_contents('https://www.computop-paygate.com/AmazonAPA.aspx?MerchantID=' . $merchant_id . '&Len=' . $len_amazon . '&Data=' . $Data_amazon);


            $dataArray = explode("&", $result);

            foreach ($dataArray as $val) {
                $tmp = explode('=', $val);
                $finalArray[$tmp[0]] = $tmp[1];
            }


            $BlowFish = new ctBlowfish;

            $data_decrypted = $BlowFish->ctDecrypt($finalArray["Data"], $finalArray["Len"], $GLOBALS['shop']["computop_password"]);

            $parts = explode("&", $data_decrypted);
            foreach ($parts as $part) {
                $x = explode('=', $part);
                $paygate_response[$x[0]] = $x[1];
            }

            $logDir2 = $baseDirectory . DIRECTORY_SEPARATOR . 'logs';
            $logFile2 = 'PaymentNotify.log';
            $logFilePath2 = $logDir . DIRECTORY_SEPARATOR . $logFile2;
            $logFileHandler2 = new \Monolog\Handler\RotatingFileHandler($logFilePath2, 10, LOG_INFO);
            $processor2 = new \Monolog\Processor\PsrLogMessageProcessor();
            $logFileHandler2->pushProcessor($processor2);
            $logger2 = new \Monolog\Logger('PaymentNotify', [$logFileHandler2]);

            $logger2->info('Date Amazon Order Compelte Response  decrypted: ' . $data_decrypted);
            $logger2->info('Paygate Response Trans ID: ' . $paygate_response['TransID']);
            $logger2->info('Paygate Response Pay ID: ' . $paygate_response['PayID']);
            $logger2->info('Paygate Response Status: ' . $paygate_response['Status']);


            if ($paygate_response["Status"] == "OK" ) {

                $_SESSION['pay_id'] = $paygate_response['PayID'];

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


                    $prepStatement = "
                    UPDATE shop_sales_header 
                    SET update_insert = 1,
                          update_notify = 1,
                          order_error = 0,
                          process_payment = 1, 
                          pay_id = :pay_id 
                    WHERE payment_transaction_id =:payment_transaction_id
				  ";

                    $params = [
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
                $redirectUrl = "https://".$_SERVER["SERVER_NAME"]."/".customizeUrl()."/order/complete_order/";
                headerFunctionBridge('Location: '.$redirectUrl);

                ?>

                <script type="text/javascript">
                    parent.location.href = '<? echo $redirectUrl; ?>';
                </script>
                <?

            }

        }
        else {
            finish_order();
            //header("Location: http://".$_SERVER["SERVER_NAME"]."/".$GLOBALS['site']['code']."/".$GLOBALS['language']['code']."/shop/?shop_category=order&action=complete_order");
        }
    } else {
        @mysqli_query($GLOBALS['mysql_con'], $query_commit);
        $_SESSION["order_query_error"] = TRUE;
        //universal_redirect($GLOBALS['url_protocol'] . $_SERVER["SERVER_NAME"] . "/" . customizeUrl() . "/order/buy/?reason=db_could_not_create_header");
        die('COULD NOT EXEC QUERY ' . $query);
    }

} else {
    @mysqli_query($GLOBALS['mysql_con'], $query_commit);
    $_SESSION["order_query_error"] = TRUE;
    universal_redirect($GLOBALS['url_protocol'] . $_SERVER["SERVER_NAME"] . "/" . customizeUrl() . "/order/buy/?reason=db_transaction_invalid");
}
?>