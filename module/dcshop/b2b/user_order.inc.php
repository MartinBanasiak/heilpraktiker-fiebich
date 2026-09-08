<?

use DynCom\dc\common\interfaces\IOCInterface;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\classes\AppliedDiscount;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\WebshopItemOrderableEntityDecorator;
use DynCom\dc\dcShop\interfaces\UserBasket;

if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
    if (!isset($IOCContainer) || !($IOCContainer instanceof IOCInterface)) {
        $IOCContainer = $GLOBALS['IOC'];
    }
    $currUserBasket = $IOCContainer->resolve('$CurrUserBasket');
}

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
    $query          = "UPDATE shop_sales_header SET pay_id = '" . $_SESSION['pay_id'] . "',payment_processed = " . $processed . ", update_insert = 1, successful = 1, order_error=0 WHERE payment_transaction_id ='" . $_SESSION['trans_id'] . "'";
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

        //Bestellbestätigung versenden
        ?>
        <div class="toolbar">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["user_order"] ?></h1>
        </div><?
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
            $message      = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"], $spacer);
            $attachment_1 = get_text_module_attachment($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"]);
            $attachment_2 = get_text_module_attachment($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"], 2);
            $subject      = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["email_order_1_text_module"], $spacer, TRUE);
            if (mail_create($subject, $message, $GLOBALS["shop"]["email_sender"], $GLOBALS["shop_user"]["email"], "", "", TRUE, $attachment_1, $GLOBALS['shop']['email_order_mail_1_copy'], $attachment_2, 0, '')) {
                mail_send();
            }
        }
        /* ---SUBSCRIPTION--- */
        if (!array_key_exists('in_subscription_order', $GLOBALS) || !$GLOBALS['in_subscription_order']) {
            if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
                if (!isset($IOCContainer) || !($IOCContainer instanceof IOCInterface)) {
                    $IOCContainer = $GLOBALS['IOC'];
                }
                $currUserBasket = $IOCContainer->resolve('$CurrUserBasket');
            }
            $currUserBasket->reset();
            shop_empty_user_basket($GLOBALS["visitor"]["id"]);
        }
        /* +++SUBSCRIPTION+++ */
    } else {?>
        <div class="toolbar">
                <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["user_order"] ?></h1>
        </div><?
        echo "<div class=\"infobox\">" . $GLOBALS["tc"]["order_error"] . "</div>\n";
    }

    get_content('order_finish');
    unset_session_safe();
}

if ($_GET['action'] == 'coupon') {
    $basketAmount = $currUserBasket->getBasketTotal();
    //calculate_coupon($_POST['input_coupon_code'], shop_get_basket_amount($GLOBALS["visitor"]['id']));
    calculate_coupon($_POST['input_coupon_code'], $basketAmount);
}
if ($_GET['action'] == 'coupon_delete') {
    if ($_SESSION['coupon']['item_no'] != '') {
        $query = "DELETE FROM shop_user_basket WHERE main_visitor_id = '" . $GLOBALS['visitor']['id'] . "' AND is_coupon_item = 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        foreach ($currUserBasket as $basketItem) {
            if ($basketItem instanceof BasketEntity) {
                if (
                    $basketItem->getCreationSourceType() == BasketEntity::CREATION_SOURCE_TYPE_COUPON
                    &&  $basketItem->getCreationSourceID() == $_SESSION['coupon']['id']
                ) {
                    $key = $currUserBasket->getKey($basketItem);
                    $newKey = $currUserBasket->setItemQtyAccessibilityByKey($key, true);
                    $currUserBasket->removeItemByKey($newKey);
                }
            }
        }
    } else {
        $basketInvoiceDiscounts = $currUserBasket->getAppliedInvoiceDiscounts();
        foreach ($basketInvoiceDiscounts as $appliedInvoiceDisc) {
            if (
                $appliedInvoiceDisc instanceof AppliedDiscount
                &&  $appliedInvoiceDisc->getSourceType() == DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON
                &&  $appliedInvoiceDisc->getSourceID() == $_SESSION['coupon']['id']
            ) {
                $percent = null;
                $amnt = null;
                if ($appliedInvoiceDisc->getDiscountValueType() == DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT) {
                    $amnt = $appliedInvoiceDisc->getDiscountValue();
                } else {
                    $percent = $appliedInvoiceDisc->getDiscountValue();
                }
                $invoiceDisc = new GenericInvoiceDiscount(
                    $appliedInvoiceDisc->getSourceType(),
                    $appliedInvoiceDisc->getSourceID(),
                    $percent,
                    $amnt
                );
                $currUserBasket->removeInvoiceDiscount($invoiceDisc);
            }
        }
    }
    $query = "UPDATE shop_coupon_line SET times_used = times_used-1, last_date_used=CURDATE() WHERE coupon_code='" . $_SESSION['coupon']['coupon_code'] . "' AND code = '" . $_SESSION['coupon']['code'] . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    $_SESSION['coupon'] = '';
    echo("<div class='infobox'>" . $GLOBALS["tc"]["coupon_removed"] . "</div>");
}

$creationSourceTypeCoupon = BasketEntity::CREATION_SOURCE_TYPE_COUPON;
$orderableTypeItem = WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM;
$basketID = $currUserBasket->getID();
$query = <<<SQL
        SELECT
          shop_item.*,
          shop_user_basket_line_new.quantity AS 'basket_quantity',
          shop_user_basket_line_new.subidentifier AS 'var_code',
          shop_user_basket_line_new.unit_price AS 'customer_price',
          shop_item.allow_invoice_discount AS 'allow_invoice_disc',
          CASE WHEN shop_user_basket_value_source_new.created_by_source_type = '$creationSourceTypeCoupon' THEN 1 ELSE 0 END AS 'is_coupon_item',
          0 AS 'changed_to_vpe',
          0 AS 'changed_to_minimum'
        FROM
          shop_user_basket_line_new
        LEFT JOIN
          shop_item
          ON (
                shop_user_basket_line_new.orderable_item_type = '$orderableTypeItem'
            AND shop_item.company = '{$GLOBALS['shop']['company']}'
            AND shop_item.shop_code = '{$GLOBALS['shop']['item_source']}'
            AND shop_item.language_code = '{$GLOBALS['shop_language']['code']}'
            AND shop_item.item_no = shop_user_basket_line_new.identifier
          )
        LEFT JOIN
          shop_user_basket_value_source_new
          ON (
                shop_user_basket_value_source_new.applied_to_header_id = shop_user_basket_line_new.header_id
            AND shop_user_basket_value_source_new.applied_to_line_id = shop_user_basket_line_new.id
            AND shop_user_basket_value_source_new.created_by_source_type != ''
            AND shop_user_basket_value_source_new.created_by_source_type IS NOT NULL
          )
        WHERE
              shop_user_basket_line_new.header_id = $basketID
SQL;
$basketresult = @mysqli_query($GLOBALS['mysql_con'], $query);

$basket_has_content = is_resource($basketresult) ? (@mysql_num_rows($basketresult) > 0) : (count($basketresult) > 0);
if ($basket_has_content) {
    $shipping_query              = "SELECT *
	  					 FROM shop_shipping_option
	  					 WHERE line_no = '" . $_SESSION['shipping_line_no'] . "'
	  					 	AND shop_code = '" . $GLOBALS['shop']['code'] . "' AND company = '" . $GLOBALS['shop']['company'] . "'";
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


        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
                            FROM shop_shipment_address
                          WHERE
                            id = :id
                           AND
                            customer_no = :customer_no
                 ";
        $params = [
            [':id',  $_POST["input_shipment_address_id"], PDO::PARAM_STR],
            [':customer_no',$GLOBALS["shop_customer"]["customer_no"] , PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $shipment_address = $result[0];
            if (!empty(trim($_POST['input_name']))) {
                $shipment_address['contact'] = trim($_POST['inpt_name']);
            }
        }
    } else {
        $shipment_address["name"]      = $GLOBALS["shop_customer"]["name"];
        $shipment_address["name_2"]    = $GLOBALS["shop_customer"]["name_2"];
        $shipment_address["address"]   = $GLOBALS["shop_customer"]["address"];
        $shipment_address["address_2"] = $GLOBALS["shop_customer"]["address_2"];
        $shipment_address["post_code"] = $GLOBALS["shop_customer"]["post_code"];
        $shipment_address["city"]      = $GLOBALS["shop_customer"]["city"];
        $shipment_address["country"]   = $GLOBALS["shop_customer"]["country"];
        $shipment_address["contact"]   = !empty(trim($_POST['input_name'])) ? $_POST['input_name'] : $GLOBALS["shop_user"]["name"];
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
    $subtotal = $currUserBasket->getBasketTotal();
    //$subtotal = shop_get_basket_amount($GLOBALS["visitor"]["id"]);
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

    $subtotal_discount_allowed    = $currUserBasket->getTotalAmntInvoiceDiscountAllowed();
    //TODO: Umrechnung auf Netto wenn Einstellung Brutto(?)
    //$sumVat = $currUserBasket->getSumVATAmounts();
    //$subtotal_discount_allowed -= $sumVat;
    $online_discount              = $GLOBALS["shop"]["online_discount"];
    $online_discount_amount       = $subtotal / 100 * $online_discount;
    $invoice_discount             = (bool)$GLOBALS['shop']['show_invoice_discount'] ? get_invoice_discount($GLOBALS["shop_customer"]["invoice_disc_code"], $subtotal_discount_allowed) : 0;
    $invoice_discount_amount      = $subtotal_discount_allowed / 100 * $invoice_discount;
    $small_quantity_charge_amount = (($subtotal - $online_discount - $invoice_discount) < $GLOBALS["shop"]["small_quantity_charge_limit"]) ? $GLOBALS["shop"]["small_quantity_charge"] : 0;
    $total                        = $subtotal + $small_quantity_charge_amount - $invoice_discount_amount - $online_discount_amount + $GLOBALS['shipping_cost'] - $coupon_discount_amount;
    if ($_GET['action'] == 'payment') {
        require_once 'user_order_payment.inc.php';
    } elseif ($_GET["action"] == "complete_order") {
        finish_order();
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
    }
}
?>