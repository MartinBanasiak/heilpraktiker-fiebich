<?
function finish_order() {
    $query  = "SELECT *
				  FROM shop_payment_option
				  WHERE line_no='" . $_SESSION['payment_line_no'] . "'
				  AND shop_code='" . $GLOBALS['shop']['code'] . "'
				  AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $terms  = mysqli_fetch_assoc($result);
    if ($terms['checkout'] == '3' || $terms['checkout'] == '5') {
        $processed = 'NOW()';
    } else {
        $processed = 'NULL';
    }
    $order_complete = FALSE;
    $query          = "UPDATE shop_sales_header SET pay_id = '" . $_SESSION['pay_id'] . "',payment_processed = " . $processed . ", update_insert = 1, successful = 1, order_error = 0 WHERE payment_transaction_id ='" . $_SESSION['trans_id'] . "'";
    if (mysqli_query($GLOBALS['mysql_con'], $query)) {
        $order_complete = TRUE;
    }
    if ($order_complete && ($GLOBALS["shop_customer"]["customer_no"] != "")) {
        if ($_SESSION['visitor_new_shipping_address'] == "on") {
            $query = "INSERT INTO shop_shipment_address (id ,shop_customer_id ,main_visitor_id ,name ,name_2 ,contact ,address ,address_2 ,post_code ,city ,country ,telephone) VALUES (NULL , '" .
                $GLOBALS["shop_customer"]["id"] . "', " . $GLOBALS["visitor"]["id"] . ", '" . $shipment_address["name"] .
                "','" . $shipment_address["name_2"] . "','', '" . $shipment_address["address"] .
                "', '" . $shipment_address["address_2"] . "', '" . $shipment_address["post_code"] .
                "', '" . $shipment_address["city"] . "', '" . $shipment_address["country"] .
                "', '')";
            if (!mysqli_query($GLOBALS['mysql_con'], $query)) {
                $order_complete = FALSE;
            }
        }
    }
    if ($order_complete) {
        shop_empty_user_basket($GLOBALS["visitor"]["id"]);
        //Bestellbestätigung versenden
        echo "<div class=\"infobar\">" . $GLOBALS["tc"]["user_order"] . "</div>\n";
        echo "<div class=\"infobox\">";
        $spacer["%order_no%"]    = $_SESSION['order_no'];
        $spacer["%email%"]       = $GLOBALS["shop_user"]["email"];
        $spacer["%order_lines%"] = "";
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'order_mail.inc.php';
        $spacer["%order_lines%"] .= ob_get_contents();
        ob_end_clean();
        echo get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["order_complete_text_module"], $spacer);
        echo "</div>";
        if ($GLOBALS["shop_language"]["email_order_1_text_module"] <> '') {
            $message = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"], $spacer);
            $subject = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"], $spacer, TRUE);
            if (mail_create($subject, $message, $GLOBALS["shop"]["email_sender"], $GLOBALS["shop_user"]["email"], "", "", TRUE, "", $GLOBALS['shop']['email_order_mail_1_copy'], 0, '')) {
                mail_send();
            }
        }
    } else {
        echo "<div class=\"infobar\">" . $GLOBALS["tc"]["user_order"] . "</div>\n";
        echo "<div class=\"infobox\">" . $GLOBALS["tc"]["order_error"] . "</div>\n";
    }
    session_unset();
}

if ($_GET['action'] == 'coupon') {
    calculate_coupon($_POST['input_coupon_code'], shop_get_basket_amount($GLOBALS["visitor"]['id']));
}
if ($_GET['action'] == 'coupon_delete') {
    if ($_SESSION['coupon']['item_no'] != '') {
        $query = "DELETE FROM shop_user_basket WHERE main_visitor_id = '" . $GLOBALS['visitor']['id'] . "' AND is_coupon_item = 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    $query = "UPDATE shop_coupon_line SET times_used = times_used-1, last_date_used=CURDATE() WHERE coupon_code='" . $_SESSION['coupon']['coupon_code'] . "' AND code = '" . $_SESSION['coupon']['code'] . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    $_SESSION['coupon'] = '';
    echo("<div class='infobox'>" . $GLOBALS["tc"]["coupon_removed"] . "</div>");
}

$query        = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.variant_code AS 'var_code',
				 shop_user_basket.customer_price AS 'customer_price', shop_user_basket.allow_invoice_disc AS 'allow_invoice_disc',
				 shop_user_basket.is_coupon_item AS 'is_coupon_item',shop_user_basket.changed_to_minimum AS 'changed_to_minimum',shop_user_basket.changed_to_vpe AS 'changed_to_vpe'
		  FROM shop_user_basket
		  INNER JOIN shop_view_active_item ON shop_view_active_item.id = shop_user_basket.shop_item_id
		  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  ORDER BY shop_user_basket.insert_datetime";
$basketresult = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($basketresult) > 0) {
    $shipping_query              = "SELECT *
	  					 FROM shop_shipping_option
	  					 WHERE line_no = '" . $_SESSION['shipping_line_no'] . "'
	  					 	AND shop_code = '" . $GLOBALS['shop']['code'] . "'";
    $shipping_result             = mysqli_query($GLOBALS['mysql_con'], $shipping_query);
    $ship_info                   = mysqli_fetch_assoc($shipping_result);
    $shipping_cost               = $ship_info['shipping_cost'];
    $shipping_line_no            = $ship_info['line_no'];
    $shipping_description        = $ship_info['description'];
    $shipping_agent_service_code = $ship_info['shipping_agent_service_code'];
    $shipping_agent_code         = $ship_info['shipping_agent_code'];
    if (($_POST["input_shipment_address_id"] == '') && ($GLOBALS["shop_user"]["shop_shipment_address_id"] <> '')) {
        $_POST["input_shipment_address_id"] = $GLOBALS["shop_user"]["shop_shipment_address_id"];
    }
    if (($_POST["input_shipment_address_id"] <> '') && ($_POST["input_shipment_address_id"] <> 0)) {
        $query  = "SELECT * FROM shop_shipment_address WHERE id = '" . $_POST["input_shipment_address_id"] . "' AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $shipment_address = @mysqli_fetch_array($result);
            //MB 2014-08-20 ---
            if (strlen($shipment_address['contact']) > 0) {
                $_POST['input_name'] = $shipment_address['contact'];
            }
            //MB +++
        }
    } else {
        $shipment_address["name"]      = $GLOBALS["shop_customer"]["name"];
        $shipment_address["name_2"]    = $GLOBALS["shop_customer"]["name_2"];
        $shipment_address["address"]   = $GLOBALS["shop_customer"]["address"];
        $shipment_address["address_2"] = $GLOBALS["shop_customer"]["address_2"];
        $shipment_address["post_code"] = $GLOBALS["shop_customer"]["post_code"];
        $shipment_address["city"]      = $GLOBALS["shop_customer"]["city"];
        $shipment_address["country"]   = $GLOBALS["shop_customer"]["country"];
        $shipment_address["contact"]   = $GLOBALS["shop_user"]["name"];
    }
    if ($GLOBALS["shop_customer"]["bill_to_customer_no"] <> '') {
        $invoice_address["name"]      = $GLOBALS["shop_customer"]["bill_to_name"];
        $invoice_address["name_2"]    = $GLOBALS["shop_customer"]["bill_to_name_2"];
        $invoice_address["address"]   = $GLOBALS["shop_customer"]["bill_to_address"];
        $invoice_address["address_2"] = $GLOBALS["shop_customer"]["bill_to_address_2"];
        $invoice_address["post_code"] = $GLOBALS["shop_customer"]["bill_to_post_code"];
        $invoice_address["city"]      = $GLOBALS["shop_customer"]["bill_to_city"];
        $invoice_address["country"]   = $GLOBALS["shop_customer"]["bill_to_country"];
    } else {
        $invoice_address["name"]      = $GLOBALS["shop_customer"]["name"];
        $invoice_address["name_2"]    = $GLOBALS["shop_customer"]["name_2"];
        $invoice_address["address"]   = $GLOBALS["shop_customer"]["address"];
        $invoice_address["address_2"] = $GLOBALS["shop_customer"]["address_2"];
        $invoice_address["post_code"] = $GLOBALS["shop_customer"]["post_code"];
        $invoice_address["city"]      = $GLOBALS["shop_customer"]["city"];
        $invoice_address["country"]   = $GLOBALS["shop_customer"]["country"];
    }
    $order_no_query  = "SELECT order_no
					   FROM shop_sales_header
					   ORDER BY order_no DESC
					   LIMIT 1";
    $order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
    if (@mysqli_num_rows($order_no_result) == 1) {
        $order_no_array = @mysqli_fetch_array($order_no_result);
        $order_no       = $order_no_array["order_no"] + 1;
    } else {
        $order_no = 100000;
    }
    $subtotal = shop_get_basket_amount($GLOBALS["visitor"]["id"]);

    if ($_SESSION['coupon']['percentage'] > 0) {
        $coupon_discount        = $_SESSION['coupon']['percentage'];
        $coupon_discount_amount = round((($coupon_discount / 100) * $subtotal), 2);
    } elseif ($_SESSION['coupon']['amount'] > 0 || $_SESSION['coupon']['amount_left'] > 0) {
        if ($_SESSION['coupon']['value_coupon'] != 1) {
            $coupon_discount_amount = $_SESSION['coupon']['amount'];
        } else {
            if ($subtotal > $_SESSION['coupon']['amount_left']) {
                $coupon_discount_amount = $_SESSION['coupon']['amount_left'];
            } else {
                $coupon_discount_amount = $subtotal;
            }
            $coupon_amount_left = $_SESSION['coupon']['amount_left'] - $coupon_discount_amount;
        }
    } else {
        $coupon_discount_amount = 0;
    }

    $subtotal_discount_allowed    = shop_get_basket_amount($GLOBALS["shop_user"]["id"], TRUE);
    //TODO: Umrechnung auf Netto wenn Einstellung Brutto(?)
    $online_discount              = $GLOBALS["shop"]["online_discount"];
    $online_discount_amount       = $subtotal / 100 * $online_discount;
    $invoice_discount             = get_invoice_discount($GLOBALS["shop_customer"]["invoice_disc_code"], $subtotal_discount_allowed);
    $invoice_discount_amount      = $subtotal_discount_allowed / 100 * $invoice_discount;
    $small_quantity_charge_amount = (($subtotal - $online_discount - invoice_discount) < $GLOBALS["shop"]["small_quantity_charge_limit"]) ? $GLOBALS["shop"]["small_quantity_charge"] : 0;
    $total                        = $subtotal + $small_quantity_charge_amount - $invoice_discount_amount - $online_discount_amount + $GLOBALS['shipping_cost'] - $coupon_discount_amount;
    if ($_GET['action'] == 'payment') {
        $disc_error = FALSE;
        while ($line = mysqli_fetch_assoc($basketresult)) {
            if ($_POST['sp_disc_' . $line['item_no'] . $line['var_code']] > $GLOBALS['shop_user']['max_discount']) {
                $disc_error = TRUE;
            }
        }
        mysqli_data_seek($basketresult, 0);
        if ($disc_error) {
            echo("<div class = 'errorbox'>" . $GLOBALS["tc"]["max_discount_is"] . $GLOBALS['shop_user']['max_discount'] . "%</div>");
            require_once 'user_order_step_1.inc.php';
        } else {
            require_once 'user_order_payment.inc.php';
        }
    } elseif ($_GET["action"] == "complete_order") {
        finish_order();
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
    }
}
?>