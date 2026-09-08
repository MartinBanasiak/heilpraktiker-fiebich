<?php
use DynCom\dc\common\classes\NewValidator;
use DynCom\dc\common\interfaces\IOCInterface;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\classes\AppliedDiscount;
use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\classes\CustomerConfig;
use DynCom\dc\dcShop\classes\CustomerRepository;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\WebshopItemOrderableEntityDecorator;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemDecorator;

ini_set("display_errors",0);

if ($_POST["subscription_item_id"] == "" && $_POST["subscription_item_qty"] == "" ) {
    unset($GLOBALS['in_subscription_order']);
}
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\') ;
include_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/subscriptions/init.php';
if (!isset($IOCContainer) || !($IOCContainer instanceof IOCInterface)) {
    $IOCContainer = $GLOBALS['IOC'];
}
if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {

    $currUserBasket = $IOCContainer->resolve('$CurrUserBasket');
}
include_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/b2c/subscriptionController.php';
$logfile = 'logfile_total.txt';

require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/payment_functions.inc.php';

if (empty($GLOBALS['shop_customer']) && !empty($_SESSION['temp_customer'])) {
    $GLOBALS['shop_customer'] = $_SESSION['temp_customer'];
}

// Payolution ---
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'plugins/paygate/payolution.inc.php';
// Payolution +++

//US Sales Tax ---
$validated_sanitized_us_address = [];
//US Sales Tax +++

//digitaler Gutscheinversand +++
if ($_GET["dc_id"] <> '') {
    $_SESSION["dc_id"] = $_GET["dc_id"];
    $_SESSION["saved_dc_id"] = '';
}
// digitaler Gutscheinversand ---

//transaktionsdaten für Google Analytics setzen
function get_analytics_trans_header($sales_header)
{
    $tax = 0;
    $query = "
	SELECT 
		DISTINCT 
			svps.vat_percent, 
			ssline.list_price
	FROM 
		shop_sales_line ssline
	JOIN 
		shop_item si 
	  ON 
		si.item_no = ssline.item_no
	JOIN 
		shop_vat_posting_setup svps 
	  ON 
		svps.vat_prod_posting_group = si.vat_prod_posting_group
	WHERE 
		ssline.shop_sales_header_id =  '" . $sales_header["id"] . "'
	AND 
		svps.vat_percent != 0
			";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($itemtax = @mysqli_fetch_assoc($result)) {
        $tax += (($itemtax["list_price"] / (100 + $itemtax["vat_percent"])) * $itemtax["vat_percent"]);
    }
    $tax += (($sales_header["shipping_cost"]) / 119) * 19;

    $trans = array(
        'id' => $sales_header["order_no"],
        'affiliation' => $GLOBALS['shop']['code'],
        'revenue' => $sales_header["total"],
        'shipping' => $sales_header["shipping_cost"],
        'tax' => round($tax, 2)
    );
    return $trans;
}

function get_analytics_trans_items($sales_header)
{
    $items = [];
    if ($sales_header['dc_order']) {
        $item = [
            'id' => $sales_header["order_no"],
            'name' => 'Digitalgutschein Code: ' . $sales_header['coupon_code'],
            'sku' => 'Digitaler Gutschein',
            'price' => $sales_header['subtotal'],
            'quantity' => 1,
        ];
        array_push($items, $item);
    } else {
        $query = "SELECT * FROM shop_sales_line WHERE shop_sales_header_id = '" . $sales_header["id"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        WHILE ($sales_line = @mysqli_fetch_assoc($result)) {
            array_push(
                $items,
                array(
                    'id' => $sales_header["order_no"],
                    'name' => $sales_line["description"],
                    'sku' => $sales_line["item_no"],
                    'price' => $sales_line["unit_price"],
                    'quantity' => round($sales_line["quantity"])
                )
            );
        }
    }

    return $items;
}

// Function to return the JavaScript representation of a TransactionData object.
function getTransactionJs(&$trans)
{
    return "
	ga('ecommerce:addTransaction', {
	  'id': '" . $trans['id'] . "',
	  'affiliation': '" . $trans['affiliation'] . "',
	  'revenue': '" . $trans['revenue'] . "',
	  'shipping': '" . $trans['shipping'] . "',
	  'tax': '" . $trans['tax'] . "'
	});";
}

// Function to return the JavaScript representation of an ItemData object.
function getItemJs(&$transId, &$item)
{
    return "
	ga('ecommerce:addItem', {
	  'id': '" . $transId . "',
	  'name': '" . $item['name'] . "',
	  'sku': '" . $item['sku'] . "',
	  'price': '" . $item['price'] . "',
	  'quantity': '" . $item['quantity'] . "'
	});";
}

function analytics_transaction($sales_header)
{
    ?>
    <script>
        ga('require', 'ecommerce', 'ecommerce.js');

        <?
        $trans = get_analytics_trans_header($sales_header);
        $transitems = get_analytics_trans_items($sales_header);

        echo getTransactionJs($trans);

        foreach ($transitems as &$item) {
            echo getItemJs($trans['id'], $item);
        }
        ?>

        ga('ecommerce:send');
    </script>

    <?
}

function push_google_tag_transaction($sales_header)
{
    $trans = get_analytics_trans_header($sales_header);
    $transitems = get_analytics_trans_items($sales_header);


    $itemsScript = "[ ";
    foreach ($transitems as &$item) {
        $itemsScript .= "  {                           
                                'sku': '".$item['sku']."',
                                'name': '".$item['name']."',
                                'price': ".$item['price'].",
                                'quantity': ".$item['quantity']."
                             },  ";
    }

    $itemsScript  = rtrim($itemsScript,',');
    $itemsScript .=   " ]";
    ?>
    <script>
        // Send transaction data with a pageview if available
        // when the page loads. Otherwise, use an event when the transaction
        // data becomes available.
        dataLayer.push({
            'transactionId': '<?=  $trans['id'] ?>',
            'transactionAffiliation': '<?=  $trans['affiliation'] ?>',
            'transactionTotal': '<?=  $trans['revenue'] ?>',
            'transactionTax': '<?=  $trans['tax'] ?>',
            'transactionShipping': '<?=  $trans['shipping'] ?>',
            'transactionProducts': <?= $itemsScript ?>
        });
    </script>
    <?
}

function facebook_transaction($sales_header) {
    if ($GLOBALS["site"]["facebook_pixel_id"] <> "") { ?>
        <script>
            fbq('track', 'Purchase', {currency: '<?= $GLOBALS['shop_currency']['code'] ?>', value: <?= $sales_header["total"] ?>});
        </script>
    <? }
}


function finish_order()
{
    if (isset($GLOBALS['IOC']) && !isset($IOCContainer)) {
        $IOCContainer = $GLOBALS['IOC'];
    }
    $query = "SELECT *
			  FROM shop_payment_option
			  WHERE line_no='" . $_SESSION['payment_line_no'] . "'
			  AND shop_code='" . $GLOBALS['shop']['code'] . "'
			  AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $terms = mysqli_fetch_assoc($result);
    $proces_payment = 0;
    if ($terms['checkout'] == '3' || $terms['checkout'] == '5' || $terms['checkout'] == '17'|| $terms['checkout'] == '20'|| $terms['checkout'] == '11' ) {
        $processed = 'NULL';
    } else {
        $processed = 'NOW()';
    }
    if ($terms['checkout'] === '3' || $terms['checkout'] === '5' || $terms['checkout'] === '6' || $terms['checkout'] === '7' || $terms['checkout'] == '17' || $terms['checkout'] == '20' || $terms['checkout'] == '11') {
        $process_payment = 1;
    } else {
        $process_payment = 0;
    }
    $order_complete = false;

    //SEPA-Umstellung Abfrage
    $sepa = 0;
    if ($_SESSION["PAYMENT"]['bpaccnr'] == '' && !empty($_SESSION["PAYMENT"]['IBAN'])) {
        $_SESSION["PAYMENT"]['bpaccnr'] = $_SESSION["PAYMENT"]['IBAN'];
        $sepa = 1;
    }
    if ($_SESSION["PAYMENT"]['bpacciban'] == '' && !empty($_SESSION["PAYMENT"]['BIC'])) {
        $_SESSION["PAYMENT"]['bpacciban'] = $_SESSION["PAYMENT"]['BIC'];
        $sepa = 1;
    }

    //PH Billpay-Bankdaten noch hinzugefügt
    $query = "UPDATE shop_sales_header
			  SET 	pay_id = '" . $_SESSION['pay_id'] . "',
					process_payment = " . $process_payment . ",
			  		payment_processed = " . $processed . ",			  		
			  		bp_acc_owner = '" . $_SESSION["PAYMENT"]['bpaccowner'] . "',
					bp_acc_nr = '" . $_SESSION["PAYMENT"]['bpaccnr'] . "',
					bp_acc_iban = '" . $_SESSION["PAYMENT"]['bpacciban'] . "',
					bp_bank = '" . $_SESSION["PAYMENT"]['bpbank'] . "',
					bp_invoice_ref = '" . $_SESSION["PAYMENT"]['bpinvoiceref'] . "',
					payment_reference = '" . $_SESSION['payment_reference'] . "',
					sepa = ".$sepa.",
			  		update_insert = 1,
			  		successful = 1,
			  		update_insert = IF (update_notify = 0,1,IF (update_insert = 0,0,1)),
					order_error = 0 
			  WHERE payment_transaction_id ='" . $_SESSION['trans_id'] . "'";
    if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
        $order_complete = true;
    }

    if ($order_complete) {
        $headerquery = "SELECT * FROM shop_sales_header WHERE payment_transaction_id ='" . $_SESSION['trans_id'] . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $headerquery);
        $sales_header = mysqli_fetch_assoc($result);
        $salesHeaderCopy = $sales_header;


        if (is_array($sales_header) && array_key_exists('id', $sales_header) && (int)$sales_header['id'] > 0) {
            $linesQuery = 'SELECT id,shop_item_id,quantity ,description,unit_price FROM shop_sales_line WHERE shop_sales_header_id = ' . (int)$sales_header['id'] . ' AND shop_item_id > 0';
            $linesResult = mysqli_query($GLOBALS['mysql_con'], $linesQuery);
            $lines = mysqli_fetch_all($linesResult, MYSQLI_ASSOC);
            $salesLinesCopy = $lines;

            //Event Handling ---
            $linkedItems = [];
            $linkedItemIDs = [];
            foreach ($lines as $line) {
                $itemID = (int)$line['shop_item_id'];
                $qty = (float)$line['quantity'];
                $unitPrice = (float)$line['unit_price'];

                $linkedItems[(string)$itemID] = ['item_id' => $itemID, 'qty' => $qty, 'unit_price' => $unitPrice];
                $linkedItemIDs[] = (int)$line['shop_item_id'];
            }


            $eventData = [
                'sales_header_id' => $sales_header['id'],
                'current_visitor_id' => isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null,
                'current_user_id' => $sales_header['shop_user_id'],
                'current_customer_id' => $sales_header['shop_customer_id'],
                'customer_no' => $sales_header['customer_no'],
                'session_id' => session_id(),
                'linked_item_ids' => $linkedItemIDs,
                'order_items' => $linkedItems,
            ];
            \DynCom\dc\common\classes\Hook::update('order_complete', $eventData);
            //Event Handling +++

            //US Sales Tax
            $normedCountry = strtoupper(trim($salesHeaderCopy['ship_to_country']));

            if (('US' === $normedCountry || 'USA' === $normedCountry) && $currShopConfig->getShop()->isUSSalesTaxEnabled()){
                $normedCurrencyCode = strtoupper(trim($salesHeaderCopy['currency_code']));
                $normedCountry = 'US';
                $salesHeaderCopy['ship_to_country'] = $normedCountry;

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
                $taxJarAdapter = new \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter($taxJarRESTAPIConsumer, $vatMgr, $shopPDO, $logger);

                $nexusAddress = new \DynCom\dc\dcShop\USSalesTax\Address('US', getenv('US_NEXUS_ZIP'), getenv('US_NEXUS_STATE'), getenv('US_NEXUS_CITY'), getenv('US_NEXUS_STREET'));
                $taxJarAdapter->createTransactionAndUpdateTaxDataFromDB($salesHeaderCopy, $salesLinesCopy, $nexusAddress);
            }
            //US Sales Tax
        }

        if (!empty($GLOBALS["site"]["google_tag_container_id"])) {
            push_google_tag_transaction($sales_header);
        }

        if (!empty($GLOBALS["site"]["google_analytics_id"])) {
            analytics_transaction($sales_header);
        }

        if (!empty($GLOBALS["site"]["facebook_pixel_id"])) {
            facebook_transaction($sales_header);
        }

        //SH: 20.08.13 Digitaler Gutscheinversand +++
        /*if ($_SESSION["dc_id"] <> '') {
            finish_dc_order($_SESSION["dc_id"]);
        }*/
        //Digitaler Gutscheinversand ---

        //Digitaler Gutscheinversand ---
        //SH: 20.08.13 digitaler Gutscheinversand ---

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
                      coupon_code = :coupon_code
                    AND 
                      code = :code
                    AND
                      update_notify = 0
                    LIMIT 1
                ';

            $stmt = $pdo->prepare($getCouponLineQuery);
            $stmt->bindValue(':company', $sales_header["company"], PDO::PARAM_STR);
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
                $stmt->bindValue(':couponId', $coupon["id"], PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $errorInfo = $pdo->errorInfo();
                    $errorString = implode($errorInfo, PHP_EOL);
                    throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
                }
            }
        }

        //Bestellbestätigung versenden
        echo "<div class=\"order_finished_box\">";
        $spacer["%order_no%"] = $_SESSION['order_no'];
        $spacer["%email%"] = $_SESSION["visitor_email"];
        $spacer["%anrede%"] = $_SESSION["visitor_salutation"];
        $spacer["%name%"] = $_SESSION["visitor_name"];
        $spacer["%order_lines%"] = "";
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'order_mail_lines.inc.php';
        $spacer["%order_lines%"] .= ob_get_contents();
        ob_end_clean();
        //SH: 20.08.13 Digitaler Gutscheinversand +++
        if ($_SESSION["dc_id"] <> '') {
            echo $GLOBALS["tc"]["dc_order_text_1"] . $_SESSION['order_no'] . $GLOBALS["tc"]["dc_order_text_2"];
        } else {
            echo get_text_module(
                $GLOBALS['shop']['company'],
                $GLOBALS["shop_language"]["order_complete_text_module"],
                $spacer
            );
        }

        //SH: 25.09.13 Newsletter nach Bestellabschluss, deshalb hier her +++++++
        if ($GLOBALS['shop_language']['newsletter_registration_text_module'] != '') {
            //$spacer['%nl_button%'] = "<a class=\"button_next_small\" style=\"float: left;\" href=\"/tee/de/newsletter/\">".$GLOBALS["tc"]["login"]."</a>";
            $spacer['%nl_button%'] = "";
            $newsletter_text = get_text_module(
                $GLOBALS['shop']['company'],
                $GLOBALS['shop_language']['newsletter_registration_text_module'],
                $spacer
            );
            echo "<br />" . $newsletter_text;
            if ($_SESSION["visitor_salutation"] == "Herr") {
                $gender = "M";
            } else {
                $gender = "F";
            }
            ?>
            <form id="form_newsletter_subscribe_order" name="form_newsletter_subscribe_order" method="post"
                  action="/tee/de/newsletter/?action=subscribe">
                <input type="hidden" name='input_gender' id='input_gender' value='<?= $gender ?>'/>
                <input type="hidden" name='input_first_name' id='input_first_name'
                       value='<?= $_SESSION["visitor_first_name"] ?>'/>
                <input type="hidden" name='input_name' id='input_name' value='<?= $_SESSION["visitor_last_name"] ?>'/>
                <input type="hidden" name='input_email' id='input_email' value='<?= $_SESSION["visitor_email"] ?>'/>
                <input type="hidden" name='input_birthday' id='input_birthday'
                       value='<?= $_SESSION["visitor_birthday"] ?>'/>
                <input type="hidden" name='from_order' id='from_order' value='1'/>
                <input type="checkbox" <?= $checked ?> name="nl_checkbox" id="nl_checkbox"/>
                <?= $GLOBALS["tc"]["nl_checkbox"] ?>
                <br/>

                <div class="spacer_6"></div>
                <a class="button_next_small" style="float: left;" href="javascript:void(0);"
                   onclick="document.form_newsletter_subscribe_order.submit();"><?= $GLOBALS["tc"]["login"] ?></a>
            </form>
            <?
        }

        //SH: 25.09.13 Newsletter nach Bestellabschluss, deshalb hier her -----
        echo "</div>";
        if ($GLOBALS["shop_language"]["email_order_1_text_module"] <> '') {
            /*$message = get_text_module(
                $GLOBALS['shop']['company'],
                $GLOBALS["shop_language"]["email_order_1_text_module"],
                $spacer
            );
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
            if (mail_create(
                $subject, $message, $GLOBALS["shop"]["email_sender"], $_SESSION["visitor_email"], "", "", true, $attachment_1, $GLOBALS['shop']['email_order_mail_1_copy'], $attachment_2, 0, ''
            )) {
                mail_send();
            } else {
                /*
                echo "<div class=\"infobox\"> Could not create Mail" . $GLOBALS["tc"]["order_error"] . "<br/>Value:</div>\n";
                echo "SUBJECT: " . $subject . " MESSAGE: " . $message . " SENDER:" . $GLOBALS["shop"]["email_sender"] . " SESSION-Mail: " . $_SESSION["visitor_email"] . " Mail Copy: " . $GLOBALS['shop']['email_order_mail_1_copy'];
                */
                $mailquery = "UPDATE main_mail_log
                                            SET delayed_send = 0,
                                                payment_transaction_id = ''
                                            WHERE payment_transaction_id ='". $_SESSION['trans_id']."' AND delayed_send = 1";
                @mysqli_query($GLOBALS["mysql_con"],$mailquery);
                mail_send();
            //}
        } else {

            echo "<div class=\"infobox\"> No Textmodule" . $GLOBALS["tc"]["order_error"] . "</div>\n";

        }
        /* ---SUBSCRIPTION--- */
        if (!array_key_exists('in_subscription_order', $GLOBALS) || !$GLOBALS['in_subscription_order']) {
            if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
                $currUserBasket = $IOCContainer->resolve('$CurrUserBasket');
            }
            $currUserBasket->reset();
            shop_empty_user_basket($GLOBALS["visitor"]["id"]);
        }
        /* +++SUBSCRIPTION+++ */
    } else {
        echo "<div class=\"infobar\">" . $GLOBALS["tc"]["user_order"] . "</div>\n";
        echo "<div class=\"infobox\">" . $GLOBALS["tc"]["order_error"] . "</div>\n";
    }

    get_content('order_finish');
    unset_session_safe();
}

$creditworthiness = true;
$shipment_possible = 1;
if ($_GET['doaction'] == 'coupon') {
    $basketAmount = basketTotal();

    //calculate_coupon($_POST['input_coupon_code'], shop_get_basket_amount($GLOBALS["visitor"]['id']));
    calculate_coupon($_POST['input_coupon_code'], $basketAmount);

}
if ($_GET['doaction'] == 'coupon_delete' && strlen($_SESSION['coupon']['coupon_code']) > 0) {
    if ($_SESSION['coupon']['item_no'] != '') {
        //@TODO: Remove item with creation source coupon and id coupon-id from basket!
        $query = "DELETE FROM shop_user_basket WHERE shop_visitor_id = '" . $GLOBALS['visitor']['id'] . "' AND is_coupon_item = 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
        foreach ($currUserBasket as $basketItem) {
            if ($basketItem instanceof BasketEntity) {
                if (
                    $basketItem->getCreationSourceType() == BasketEntity::CREATION_SOURCE_TYPE_COUPON
                    && $basketItem->getCreationSourceID() == $_SESSION['coupon']['id']
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
                && $appliedInvoiceDisc->getSourceType() == DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON
                && $appliedInvoiceDisc->getSourceID() == $_SESSION['coupon']['id']
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
        foreach ($currUserBasket as $basketItem) {

            if ($basketItem instanceof BasketEntity) {
                $appliedLineDiscs = $basketItem->getAppliedLineDiscounts();
                foreach ($appliedLineDiscs as $appliedLineDisc) {
                    /**
                     * @var $appliedLineDisc AppliedDiscount
                     */
                    if ($appliedLineDisc->getSourceType() === BasketEntity::CREATION_SOURCE_TYPE_COUPON) {
                        $valueType = $appliedLineDisc->getDiscountValueType();
                        $value = $appliedLineDisc->getDiscountValue();
                        $percent = $valueType === DiscountBase::DISCOUNT_VALUE_TYPE_PERCENT ? $value : 0.00;
                        $amnt = $valueType === DiscountBase::DISCOUNT_VALUE_TYPE_AMOUNT ? $value : 0.00;

                        $genericDiscount = new \DynCom\dc\dcShop\classes\GenericLineDiscount($appliedLineDisc->getSourceType(), $appliedLineDisc->getSourceID(), $percent, $amnt);
                        $basketItem->removeDiscount($genericDiscount);
                    }
                }
            }
        }
    }

    /*$query = "UPDATE shop_coupon_line SET times_used = times_used-1, last_date_used=CURDATE() WHERE coupon_code='" . $_SESSION['coupon']['coupon_code'] . "' AND CODE = '" . $_SESSION['coupon']['code'] . "' AND company = '" . $GLOBALS['shop']['company'] . "' AND shop_code='" . $GLOBALS["shop"]["code"] . "' AND language_code='" . $GLOBALS["shop_language"]["code"] . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);*/
    $_SESSION['coupon'] = '';
    unset($_SESSION['coupon']);
    get_requestbox('',$GLOBALS["tc"]["coupon_removed"],'success');
}
//SH: 20.08.13 digitaler Gutscheinversand ---
if ($_SESSION["dc_id"] <> '') {
    $query = "SELECT * FROM shop_digital_coupon WHERE id = '" . $_SESSION["dc_id"] . "'";

//digitaler Gutscheinversand +++

    /* ---SUBSCRIPTION--- */
} elseif (array_key_exists('in_subscription_order', $GLOBALS) && array_key_exists(
        'subscription_item_basket_query',
        $GLOBALS
    ) && $GLOBALS['in_subscription_order'] && $GLOBALS['subscription_item_basket_query']
) {
    $query = $GLOBALS['subscription_item_basket_query'];
    /* +++SUBSCRIPTION+++ */
} else {
    $query = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.variant_code AS 'var_code',
				 shop_user_basket.customer_price AS 'customer_price', shop_user_basket.allow_invoice_disc AS 'allow_invoice_disc',
				 shop_user_basket.is_coupon_item AS 'is_coupon_item',shop_user_basket.changed_to_minimum AS 'changed_to_minimum',shop_user_basket.changed_to_vpe AS 'changed_to_vpe'
		  FROM shop_user_basket
		  INNER JOIN shop_view_active_item ON shop_view_active_item.id = shop_user_basket.shop_item_id
		  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  ORDER BY shop_user_basket.insert_datetime";
    $creationSourceTypeCoupon = BasketEntity::CREATION_SOURCE_TYPE_COUPON;
    $orderableTypeItem = WebshopItemOrderableEntityDecorator::ORDERABLE_TYPE_ITEM;
    $basketID = $currUserBasket->getID();

    $query = <<<SQL
        SELECT
          shop_user_basket_line_new.entity_key AS 'basket_entity_key',
          shop_user_basket_line_new.id AS 'basket_line_id',
          shop_item.*,
          shop_user_basket_line_new.quantity AS 'basket_quantity',
          shop_user_basket_line_new.subidentifier AS 'var_code',
          shop_user_basket_line_new.unit_price AS 'customer_price',
          shop_user_basket_line_new.links_to_entity_key AS 'package_for_item_no',
          shop_user_basket_line_new.greeting_card_text AS 'greeting_card_text',
          shop_user_basket_line_new.customization_hash AS 'customization_hash',
          shop_item.allow_invoice_discount AS 'allow_invoice_disc',
          shop_item.vat_prod_posting_group AS 'vat_prod_posting_group',
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
}
$basketresult = @mysqli_query($GLOBALS['mysql_con'], $query);
$num_rows_basket = @mysqli_num_rows($basketresult);

if ($num_rows_basket > 0) {

    $shipping_query = "SELECT *
	  					 FROM shop_shipping_option
	  					 WHERE line_no = '" . $_SESSION['shipping_line_no'] . "'
	  					 	AND shop_code = '" . $GLOBALS['shop']['code'] . "'
							AND language_code = '" . $GLOBALS['shop_language']['code'] . "'";
    $shipping_result = mysqli_query($GLOBALS['mysql_con'], $shipping_query);
    $ship_info = mysqli_fetch_assoc($shipping_result);
    $shipping_cost = $ship_info['shipping_cost'];
    $shipping_line_no = $ship_info['line_no'];
    $shipping_description = $ship_info['description'];
    $shipping_agent_service_code = $ship_info['shipping_agent_service_code'];
    $shipping_agent_code = $ship_info['shipping_agent_code'];
    $_SESSION["shipping_description"] = $shipping_description;

    $shipment_salutation = $_SESSION['visitor_salutation_shipping'];
    $shipment_title = $_SESSION['visitor_title_shipping'];
    $shipment_address["packstation"] = 0;

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    if ($_SESSION["visitor_packstation_address"] == "on") {
        $shipment_address["name"] = $_SESSION["visitor_surname_shipping_packstation"] . " " . $_SESSION["visitor_lastname_shipping_packstation"];
        $shipment_address["surname"] = $_SESSION["visitor_surname_shipping_packstation"];
        $shipment_address["lastname"] = $_SESSION["visitor_lastname_shipping_packstation"];
        $shipment_address["name_2"] = $_SESSION["visitor_company_shipping_packstation"];
        $shipment_address["address"] = "Packstation " . $_SESSION["visitor_user_street_shipping_packstation"];
        $shipment_address["post_code"] = $_SESSION["visitor_post_code_shipping_packstation"];
        $shipment_address["city"] = $_SESSION["visitor_city_shipping_packstation"];
        $shipment_address["country"] = $_SESSION["visitor_country_shipping"];
        $shipment_address["packstation"] = 1;
    } else {
        if ($_SESSION['visitor_company_shipping'] != "") {
            $shipment_address["name"] = $_SESSION["visitor_company_shipping"];
            $shipment_address["name_2"] = $_SESSION["visitor_surname_shipping"] . " " . $_SESSION["visitor_lastname_shipping"];
        } else {
            if ($_SESSION["visitor_first_name_shipping"] <> '') {
                $shipment_address["name"] = $_SESSION["visitor_first_name_shipping"] . " " . $_SESSION["visitor_last_name_shipping"];
                $shipment_address["surname"] = $_SESSION["visitor_surname_shipping"];
                $shipment_address["lastname"] = $_SESSION["visitor_lastname_shipping"];
            } else {
                $shipment_address["name"] = $_SESSION["visitor_surname_shipping"] . " " . $_SESSION["visitor_lastname_shipping"];
                $shipment_address["surname"] = $_SESSION["visitor_surname_shipping"];
                $shipment_address["lastname"] = $_SESSION["visitor_lastname_shipping"];
            }
            $shipment_address["name_2"] = $_SESSION["visitor_company_shipping"];
        }
        $shipment_address["address"] = $_SESSION["visitor_user_street_shipping"] . " " . $_SESSION["visitor_user_street_no_shipping"];
        $shipment_address["address_2"] = $_SESSION["visitor_user_street_2_shipping"];
        $shipment_address["address_street"] = $_SESSION["visitor_user_street_shipping"];
        $shipment_address["address_no"] = $_SESSION["visitor_user_street_no_shipping"];
        $shipment_address["post_code"] = $_SESSION["visitor_post_code_shipping"];
        $shipment_address["city"] = $_SESSION["visitor_city_shipping"];
    }
    /*if (!empty($GLOBALS['shop_language']['default_country_code'])) {
        $shipment_address["country"] = $_SESSION['input_shop_country_shipping'] ? $_SESSION['input_shop_country_shipping'] : $GLOBALS['shop_language']['default_country_code'];
    } else {
        $shipment_address["country"] = $_SESSION['input_shop_country_shipping'] ?: $_SESSION['visitor_country_shipping'] ?: 'DE';
    }*/
    //@TODO: CHECK CORRECT?!?!
    $shipment_address['country'] = $_SESSION['visitor_country_shipping'] ?: $_SESSION['input_country_shipping'] ?: $GLOBALS['shop_language']['default_country_code'];


    if ($GLOBALS["shop_customer"]["customer_no"] == "") {
        $invoice_salutation = $_SESSION['visitor_salutation'];
        $invoice_title = $_SESSION['visitor_title'];
        if ($_SESSION['visitor_company'] != "") {
            $invoice_address["name"] = $_SESSION["visitor_company"];
            $invoice_address["name_2"] = $_SESSION["visitor_name"];
            $invoice_address["surname"] = $_SESSION["visitor_surname"];
            $invoice_address["lastname"] = $_SESSION["visitor_lastname"];
        } else {
            $invoice_address["name"] = $_SESSION["visitor_name"];
            $invoice_address["name_2"] = $_SESSION["visitor_company"];
            $invoice_address["surname"] = $_SESSION["visitor_surname"];
            $invoice_address["lastname"] = $_SESSION["visitor_surname"];
        }
    } else {
        $invoice_salutation = $_SESSION['visitor_salutation'];
        $invoice_title = $_SESSION['visitor_title'];
        if ($_SESSION['visitor_company'] != "") {
            $invoice_address["name"] = $_SESSION["visitor_company"];
            $invoice_address["name_2"] = $_SESSION["visitor_name"];
            $invoice_address["surname"] = $_SESSION["visitor_surname"];
            $invoice_address["lastname"] = $_SESSION["visitor_lastname"];
        } else {
            $invoice_address["name"] = $_SESSION["visitor_name"];
            $invoice_address["name_2"] = $_SESSION["visitor_company"];
            $invoice_address["surname"] = $_SESSION["visitor_surname"];
            $invoice_address["lastname"] = $_SESSION["visitor_lastname"];
        }
    }
    $invoice_address["address"] = $_SESSION["visitor_address"] . " " . $_SESSION["visitor_address_no"];
    $invoice_address["address_street"] = $_SESSION["visitor_address"];
    $invoice_address["address_no"] = $_SESSION["visitor_address_no"];
    $invoice_address["address_2"] = $_SESSION["visitor_address_2"];
    $invoice_address["post_code"] = $_SESSION["visitor_post_code"];
    $invoice_address["city"] = $_SESSION["visitor_city"];
    $invoice_address["telephone"] = $_SESSION["visitor_telephone"];
    $invoice_address["email"] = $_SESSION["visitor_email"];
    $invoice_address["birthday"] = $_SESSION["visitor_birthday"];
    $invoice_address["country"] = $_SESSION["visitor_country"];
    $invoice_address['vatid'] = $_SESSION['visitor_vatid'];

    $subtotal = $currUserBasket->getBasketItemTotal();//getBasketTotal();
    /* ---SUBSCRIPTION--- */
    if (array_key_exists('in_subscription_order', $GLOBALS) && array_key_exists(
            'subscription_item',
            $GLOBALS
        ) && $GLOBALS['in_subscription_order'] && is_object($GLOBALS['subscription_item'])
    ) {
        $subscriptionItem = $GLOBALS['subscription_item'];
        if ($subscriptionItem instanceof SubscriptionItemDecorator) {
            $subtotal = $subscriptionItem->getLineAmount();
        }
    }
    /* +++SUBSCRIPTION+++ */

    /*	if($_SESSION['coupon']['percentage'] > 0)
        {
            $coupon_discount = $_SESSION['coupon']['percentage'];
            $coupon_discount_amount = round((($coupon_discount/100) * $subtotal),2);
        }
        elseif($_SESSION['coupon']['amount'] > 0 || $_SESSION['coupon']['amount_left'] > 0)
        {
            if($_SESSION['coupon']['value_coupon'] != 1)
            {
                $coupon_discount_amount = $_SESSION['coupon']['amount'];
            }
            else
            {
                if($subtotal > $_SESSION['coupon']['amount_left'])
                {
                    $coupon_discount_amount = $_SESSION['coupon']['amount_left'];
                }
                else
                {
                    $coupon_discount_amount = $subtotal;
                }
                $coupon_amount_left = $_SESSION['coupon']['amount_left'] - $coupon_discount_amount;
            }
        }
        else
        {
            $coupon_discount_amount = 0;
        }*/
    $online_discount = $GLOBALS["shop"]["online_discount"];
    $online_discount_amount = $subtotal / 100 * $online_discount;
    $total = $_SESSION['total_basket'];

    if ($_GET['action'] == 'payment') {
        $goBackToStep3 = false;

        $pdo = get_main_db_pdo_from_env_single_instance();
        check_coupon_before_payment($pdo,$goBackToStep3);

        if ($goBackToStep3) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_3.inc.php';
        } else {
            require_once 'user_order_payment.inc.php';
        }
    }
    if ($_GET["action"] == "complete_order") {
        finish_order();
    } elseif ($_GET["action"] == "step1" && ((check_mandatory_fields_b2c(
                $_POST,
                "start"
            )) || ($_SESSION["order_previous_step"] == "step2") || ($GLOBALS["shop_customer"] != ""))
    ) {
        if (($_SESSION["order_previous_step"] != "step2") && ($_POST["input_order_user_account"] != "use")) {
            session_save_data_b2c("start");
        }
        if ($_POST["input_order_user_account"] == "use") {
            $_GET["action"] = "shop_login";
            $_SESSION["web_login_needed"] = 0;
            shop_login_listener();
            $_GET["action"] = "step1";
            $GLOBALS["shop_customer"] = get_shop_customer(get_shop_user(get_visitor()));
            session_save_data_b2c("use");
        } elseif ($_POST["input_order_user_account"] == "create") {
            $_SESSION["web_login_needed"] = 1;
        } else {
            $_SESSION["web_login_needed"] = 0;
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
    } elseif (
            ($_GET["action"] == "step2")
        && (
                (
                        (
                                check_mandatory_fields_b2c($_POST)
                            &&  addressValidation()
                            && (
                                        ($shipment_address['country'] !== 'US' && $shipment_address['country'] !== 'USA')
                                    ||  (
                                            !($currShopConfig->getShop()->us_sales_tax_enabled) || google_validate_shipping_address($shipment_address)
                                        )
                                )
                        )
                    || (($_SESSION["order_previous_step"] == "step3") || ($_SESSION["order_previous_step"] == "step2"))
                )

            ||  ($_GET['action'] == 'coupon' || $_GET['action'] == 'coupon_delete')
          )
    ) {
        $is_shipping_address = change_checkboxval_to_bool($_POST["input_shipping_is_invoice"]);
        //echo "<br><br>STATUS NEUE ADRESSE: ".$_POST['input_shipping_new_address'];
        //Einfügen der Besucherdaten Lieferanschrift
        if ((($is_shipping_address == 1) && ($GLOBALS["shop_customer"]["customer_no"] == "")) || (($GLOBALS["shop_customer"]["customer_no"] != "") && $_POST['input_shipping_is_not_invoice'] != "on" && $_POST['input_shipment_address_id'] == 0)) {
            $_POST["input_salutation_shipping"]         = $_POST["input_salutation"];
            $_POST["input_title_shipping"]              = $_POST["input_title"];
            $_POST["input_name_shipping"]               = $_POST["input_name"];
            $_POST["input_surname_shipping"]            = $_POST["input_surname"];
            $_POST["input_lastname_shipping"]           = $_POST["input_lastname"];
            $_POST["input_company_shipping"]            = $_POST["input_company"];
            $_POST["input_user_street_shipping"]        = $_POST["input_user_street"];
            $_POST["input_user_street_name_shipping"]   = $_POST["input_user_street"];
            $_POST["input_user_street_no_shipping"]     = $_POST["input_user_street_no"];
            $_POST["input_user_street_2_shipping"]      = $_POST["input_user_street_2"];
            $_POST["input_post_code_shipping"]          = $_POST["input_post_code"];
            $_POST["input_city_shipping"]               = $_POST["input_city"];
            $_POST["input_shop_country_shipping"]       = $_POST["input_country"];
        } elseif ((($GLOBALS["shop_customer"]["customer_no"] != "") && $_POST['input_shipping_is_not_invoice'] != "on" && $_POST['input_shipment_address_id'] != 0)) {

            $prepStatement = " SELECT *
                            FROM shop_shipment_address
                          WHERE
                            id = :id
                 ";
            $params = [
                [':id',  $_POST["input_shipment_address_id"], PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $result = $pdo->getResultArray();

            $adress = $result[0];
            //$_POST["input_salutation_shipping"] = $_POST["input_salutation"];
            //$_POST["input_title_shipping"] = $_POST["input_title"];
            //TODO: company wenn eingegeben!
            $_POST["input_name_shipping"] = $address['name'];
            //$_POST["input_company_shipping"] = "";
            $_POST["input_user_street_shipping"] = $address['address'];
            $_POST["input_user_street_name_shipping"] = $address["input_user_street"];
            $_POST["input_user_street_no_shipping"] = $address["input_user_street_no"];
            $_POST["input_post_code_shipping"] = $address['post_code'];
            $_POST["input_city_shipping"] = $address['city'];
            $_POST["input_shop_country_shipping"] = $address["country"];

        }

        session_save_data_b2c("step1");
        if ($_POST['input_shipping_is_invoice']) {


            $prepStatement = " SELECT *
                          FROM shop_country
                          WHERE to_delete = 0
                            AND company = :company
                            AND shop_code = :shop_code
                            AND language_code = :language_code
                            AND country_code = :country_code
                            AND ship_to=1
                 ";
            $params = [
                [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
                [':shop_code', $GLOBALS["shop"]["code"], PDO::PARAM_STR],
                [':language_code', $GLOBALS["shop_language"]["code"], PDO::PARAM_STR],
                [':country_code',$_POST['input_country'] , PDO::PARAM_STR],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $result = $pdo->getResultArray();

            $shipment_possible = count($result);
            if (count($result) == 0) {
                get_requestbox($GLOBALS["tc"]["error_shipment"]);
              //  require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
            }
        }
        $customerRepository = $IOCContainer->create('DynCom\dc\dcShop\classes\CustomerRepository');
        if (!empty(getenv('TRACKING_API_ROOT'))) {
            $trackingApiService = new \DynCom\dc\tracking\TrackingAPIService();
        }
        $customerIdentifiedEventTracked = false;

        $custID = $GLOBALS["shop_customer"]["id"];
        $customerObj = $custID ? $customerRepository->findByID($custID) : null;
        if ($custID == 0) {
            $custID = existing_customer_id();
            if ($custID > 0) {


                $customerObj = $customerRepository->findByID($custID);
                $customerNo = $customerObj->customer_no;

                if (!empty(getenv('TRACKING_API_ROOT'))) {
                    $eventData = [
                        'current_visitor_id' => isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null,
                        'identified_customer_id' => $customerObj->getID(),
                    ];
                    $eventType = \DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_CUSTOMER_IDENTIFIED;
                    $creationTimestamp = time();
                    $customerIdentifiedEvent = new \DynCom\dc\tracking\TrackingEvent(
                        \DynCom\dc\tracking\create_uuid(),
                        session_id(),
                        $eventType,
                        $creationTimestamp,
                        $creationTimestamp,
                        $eventData,
                        $eventData['current_visitor_id'],
                        isset($GLOBALS['shop_user']['id']) ? (int)$GLOBALS['shop_user']['id'] : null,
                        $custID
                    );
                    $trackingApiService->addTrackingEvent($customerIdentifiedEvent);
                    $customerIdentifiedEventTracked = true;
                }
            }
        }
        if ($custID > 0) {
            if (null === $customerObj) {
                $customerObj = $customerRepository->findByID($custID);
                $customerNo = $customerObj->customer_no;
            }
        }
        $vis_id = ($GLOBALS['visitor']['id'] > 99999) ? ($GLOBALS['visitor']['id'] % 100000) : $GLOBALS['visitor']['id'];
        if ($_POST['register'] == 1 || $_POST["radio_guest"] == "register") {
            $pw_error = false;
            if ($_POST["input_password"] <> $_POST["input_password_2"]) {
                get_requestbox($GLOBALS["tc"]["error_2_shop_user"]);
                $_POST['radio_guest'] = 'register';
                $pw_error = true;
                //require ("user_order_step_1.inc.php");
            } elseif (strlen($_POST["input_password"]) < 6) {
                get_requestbox($GLOBALS["tc"]["error_3_shop_user"]);
                $_POST['radio_guest'] = 'register';
                $pw_error = true;
                //require ("user_order_step_1.inc.php");
            } elseif (($custID > 0) || 'TEMP_' === substr($customerNo, 0, 5)) {
                $userID = null;
                if (user_exists_for_customer($customerNo, $email, $userID)) {
                    if (!empty(getenv('TRACKING_API_ROOT'))) {
                        $eventType = \DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_USER_IDENTIFIED;
                        $creationTimestamp = time();
                        $eventData = [
                            'current_visitor_id' => isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null,
                            'identified_user_id' => $userID,
                        ];
                        $event = new \DynCom\dc\tracking\TrackingEvent(\DynCom\dc\tracking\create_uuid(), session_id(), $eventType, $creationTimestamp, $creationTimestamp, $eventData, $eventData['current_visitor_id'], $userID, $custID);
                        $trackingApiService->addTrackingEvent($event);
                    }
                    //Dublettenprüfung
                    get_requestbox($GLOBALS["tc"]["error_6_shop_user"]);
                    $_POST['radio_guest'] = 'register';
                    $pw_error = true;
                } else {
                    $userid = register_new_user($customerNo, $email, $vis_id);
                    update_customer($customerNo);
                    //update_sales_header_with_new_user($customerNo, $userid);
                }
                //require ("user_order_step_1.inc.php");
            } else {
                if (empty($custID)) {
                    $customerNo = "TEMP_" . $vis_id;
                    register_new_customer($customerNo, $name, $email);
                    register_new_user($customerNo, $email, $vis_id);
                } else {
                    create_user_for_existing_customer($customerObj);
                }
                $_POST["reg_new_user_called"] = "true";


                $arr = [
                    'company' => $GLOBALS['shop']['company'],
                    'shop_code' => $GLOBALS['shop']['code'],
                    'language_code' => $GLOBALS['shop_language']['code'],
                    'customer_no' => 'TEMP_' . $GLOBALS['visitor']['id'],
                    'name' => $_POST['input_surname'] . ' ' . $_POST['input_lastname'],
                    'surname' => $_POST['input_surname'],
                    'lastname' => $_POST['input_lastname'],
                    'company_name' => $_POST["input_company_shipping"],
                    'address' => $_POST['input_user_street'] . ' ' . $_POST["input_user_street_no"],
                    'address_street' => $_POST['input_user_street'],
                    'address_no' => $_POST["input_user_street_no"],
                    'post_code' => $_POST['input_post_code'],
                    'city' => $_POST['input_city'],
                    'country' => $_POST['input_country'],
                    'bill_to_customer_no' => 'TEMP_' . $GLOBALS['visitor']['id'],
                    'bill_to_name' => $_POST['input_surname'] . ' ' . $_POST['input_lastname'],
                    'bill_to_address' => $_POST['input_user_street'] . ' ' . $_POST["input_user_street_no"],
                    'bill_to_post_code' => $_POST['input_post_code'],
                    'bill_to_city' => $_POST['input_city'],
                    'bill_to_country' => $_POST['input_country'],
                    'phone_no' => $_POST['input_phone_no'],
                    'email' => $_POST['input_email'],
                    'salutation' => $_POST['input_salutation'],
                    'active' => 1,
                    'currency_code' => $GLOBALS['currency']['code']
                ];
                $GLOBALS["shop_customer"] = $arr;

            }
        } // --- SUBSCRIPTION ---
        else {
            $id = $GLOBALS["shop_customer"]["id"];
            if ($id == 0) $id = existing_customer_id();
            $customerRepository = $IOCContainer->create('DynCom\dc\dcShop\classes\CustomerRepository');

            if ($id > 0) {
                $customerObj = $customerRepository->findByID($id);
                if ($customerObj instanceof Customer) {
                    if (!$customerIdentifiedEventTracked) {
                        if (!empty(getenv('TRACKING_API_ROOT'))) {
                            $eventData = [
                                'current_visitor_id' => isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null,
                                'identified_customer_id' => $customerObj->getID(),
                            ];
                            $eventType = \DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_CUSTOMER_IDENTIFIED;
                            $creationTimestamp = time();
                            $trackingEvent = new \DynCom\dc\tracking\TrackingEvent(
                                \DynCom\dc\tracking\create_uuid(),
                                session_id(),
                                $eventType,
                                $creationTimestamp,
                                $creationTimestamp,
                                $eventData,
                                $eventData['current_visitor_id'],
                                isset($GLOBALS['shop_user']['id']) ? (int)$GLOBALS['shop_user']['id'] : null,
                                $custID
                            );
                            $trackingApiService->addTrackingEvent($trackingEvent);
                        }
                    }
                    $GLOBALS['shop_customer'] = $customerObj->getAllFieldsAsArray();
                }
            } else {
                $arr = [
                    'company' => $GLOBALS['shop']['company'],
                    'shop_code' => $GLOBALS['shop']['code'],
                    'language_code' => $GLOBALS['shop_language']['code'],
                    'customer_no' => 'TEMP_' . $GLOBALS['visitor']['id'],
                    'name' => $_POST['input_surname'] . ' ' . $_POST['input_lastname'],
                    'surname' => $_POST['input_surname'],
                    'lastname' => $_POST['input_lastname'],
                    'company_name' => $_POST["input_company_shipping"],
                    'address' => $_POST['input_user_street'] . ' ' . $_POST["input_user_street_no"],
                    'address_street' => $_POST['input_user_street'],
                    'address_no' => $_POST["input_user_street_no"],
                    'post_code' => $_POST['input_post_code'],
                    'city' => $_POST['input_city'],
                    'country' => $_POST['input_country'],
                    'bill_to_customer_no' => 'TEMP_' . $GLOBALS['visitor']['id'],
                    'bill_to_name' => $_POST['input_surname'] . ' ' . $_POST['input_lastname'],
                    'bill_to_address' => $_POST['input_user_street'] . ' ' . $_POST["input_user_street_no"],
                    'bill_to_post_code' => $_POST['input_post_code'],
                    'bill_to_city' => $_POST['input_city'],
                    'bill_to_country' => $_POST['input_country'],
                    'phone_no' => $_POST['input_phone_no'],
                    'email' => $_POST['input_email'],
                    'salutation' => $_POST['input_salutation'],
                    'active' => 1,
                    'currency_code' => $GLOBALS['currency']['code']
                ];

                $customerObj = new Customer(new CustomerConfig());
                $customerObj->mapFromArray($arr);

                //Validate customer
                $newValidator = new NewValidator(
                    $customerObj->getFieldValidationData(),
                    $customerObj->getAllFieldsAsArray()
                );
                $dummy = [];
                $isValid = $newValidator->isValid($dummy);
                if ($customerRepository instanceof CustomerRepository && $isValid) {
                    $customerID = $customerRepository->createInDB($customerObj);
                    if ($customerID > 0) {
                        $arr['id'] = $customerID;
                        $GLOBALS["shop_customer"] = $arr;
                    }
                }
                $_SESSION['temp_customer'] = $GLOBALS['shop_customer'];
            }
        }
        // +++ SUBSCRIPTION +++
        if ($shipment_possible && !$pw_error) {
            require("user_order_step_2.inc.php");
        } else {
            require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
        }
    } elseif ($_GET["action"] == "step3" && check_mandatory_fields_b2c($_POST, "step2")) {

        $goBackToStep2 = true;
        $error_msgs_for_step2 = array();

        $termquery = "SELECT *
				  FROM shop_payment_option
				  WHERE line_no = '" . $_SESSION['payment_line_no'] . "'
				  	AND shop_code = '" . $GLOBALS['shop']['code'] . "'
				  	AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";
        $termresult = mysqli_query($GLOBALS['mysql_con'], $termquery);
        $payment_method = @mysqli_fetch_assoc($termresult);
        ($payment_method["credit_check_required"]) ? $_SESSION["creditworthiness"] = check_creditworthiness(
        ) : $_SESSION["creditworthiness"] = 0;

        if (($_SESSION["creditworthiness"] == 0 || $_SESSION["creditworthiness"] == 1) && $_POST["input_payment_line_no"] != "0") {
            $goBackToStep2 = false;
        }

        // Payolution +++

        $payment_option_line = get_payment_option_by_line_no($_POST["input_payment_line_no"]);
        if (in_array($payment_option_line["checkout"], array("17", "18"))) {
            // Payolution payment cant have different shipping and invoice addresses
            if (compare_addresses_for_differences($invoice_address, $shipment_address)) { // function defined in payolution.inc.php
                add_order_error_msg($GLOBALS['tc']['payolution_error_address_diff']);
                $goBackToStep2 = true;
            }


            $payolution_total_amount = basketTotal() + get_shipping_cost(basketTotal()) + $_SESSION['payment_cost'];
            if ($payment_option_line["checkout"] == "18") { // Payolution - Ratenkauf/Installment

                $_SESSION["payolution"]["installment_duration"] = $_POST["input_installment_select"];
                $response = payolution_calculation_or_precheck("INSTALLMENT", "PC", $invoice_address, $payolution_total_amount, "");

                if ($response["Status"] == "FAILED") {
                    $goBackToStep2 = true;
                    add_order_error_msg($GLOBALS['tc']['unspecific_payment_error']);
                } else {
                    $GLOBALS["payolution"]["installment_cl_response"] = $response;
                }
            }

            if ($payment_option_line["checkout"] == "17") { // Checkout 13 = Payolution Rechnungskauf
                $response = payolution_calculation_or_precheck("INVOICE", "PC", $invoice_address, $payolution_total_amount, "");
                if ($response["Status"] == "FAILED") {
                    $goBackToStep2 = true;
                    add_order_error_msg($GLOBALS['tc']['unspecific_payment_error']);
                } else {
                    $GLOBALS["payolution"]["invoice_pc_response"] = $response;
                }
            }


        }
        // Payolution ---


        if ($goBackToStep2 == true) { // goto step 2

            $creditworthiness = false;
            require_once("user_order_step_2.inc.php");

        } else { // goto step 3

            $creditworthiness = true;
            session_save_data_b2c("step2");
            //@TODO: Alter basket???
            $query = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.customer_price AS 'customer_price', shop_user_basket.allow_invoice_disc AS 'allow_invoice_disc' FROM shop_user_basket INNER JOIN shop_view_active_item ON shop_view_active_item.id = shop_user_basket.shop_item_id WHERE shop_user_basket.shop_visitor_id = '" .
                $GLOBALS["visitor"]["id"] . "' ORDER BY shop_user_basket.insert_datetime";

            /* ---SUBSCRIPTION--- */
            if (array_key_exists('in_subscription_order', $GLOBALS) && array_key_exists(
                    'subscription_item_basket_query',
                    $GLOBALS
                ) && $GLOBALS['in_subscription_order'] && $GLOBALS['subscription_item_basket_query']
            ) {
                $query = $GLOBALS['subscription_item_basket_query'];
            }
            /* +++SUBSCRIPTION+++ */

            $basketresult = mysqli_query($GLOBALS['mysql_con'], $query);

            //US Sales Tax ---
            $shippingCountry = strtoupper(trim($shipment_address['country']));
            $USSalesTaxEstimate = null;
            /** @var \DynCom\dc\dcShop\classes\CurrShopConfiguration $currShopConfig */
            $currShopConfig = $IOCContainer->create('$CurrShopConfig');
            if (($shippingCountry === 'US' || $shippingCountry === 'USA') && $currShopConfig->getShop()->isUSSalesTaxEnabled()) {
                //--- US SALES TAX ---

                /**
                 * @var $IOCContainer \Dice\Dice
                 * @var $taxJarRESTAPIConsumer \DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer
                 * @var $taxJarAdapter \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter
                 */
                $baseDir = dirname(dirname(dirname(__DIR__)));

                $taxEnvDir = $baseDir . '/config/USSalesTax/';
                $dotenvTax = new \Dotenv\Dotenv($taxEnvDir);
                $dotenvTax->load();

                $logDir = $baseDir . DIRECTORY_SEPARATOR . 'logs';
                $logFile = 'USSalesTax.log';
                $logFilePath = $logDir . DIRECTORY_SEPARATOR . $logFile;
                $logFileHandler = new \Monolog\Handler\RotatingFileHandler($logFilePath, 10, LOG_INFO);
                $processor = new \Monolog\Processor\PsrLogMessageProcessor();
                $logFileHandler->pushProcessor($processor);
                $logger = new \Monolog\Logger('USSalesTax', [$logFileHandler]);

                $taxJarRESTAPIConsumer = $IOCContainer->create(\DynCom\dc\dcShop\USSalesTax\TaxJar\TaxJarRESTAPIConsumer::class);
                $vatMgr = $IOCContainer->create('$VATManager');
                $shopPDO = $IOCContainer->create(\DynCom\dc\common\classes\PDOQueryWrapper::class);
                $taxJarAdapter = new \DynCom\dc\dcShop\USSalesTax\DefaultTaxJarAdapter($taxJarRESTAPIConsumer, $vatMgr, $shopPDO, $logger);

                $nexusZip =     getenv('US_NEXUS_ZIP');
                $nexusState =   getenv('US_NEXUS_STATE');
                $nexusCity =    getenv('US_NEXUS_CITY');
                $nexusStreet =  getenv('US_NEXUS_STREET');
                $nexusAddress = new \DynCom\dc\dcShop\USSalesTax\Address('US',$nexusZip,$nexusState,$nexusCity,$nexusStreet);
                $shipToStreetNo = $shipment_address['address_no'] . ' ' . $shipment_address['address_street'];
                $postCode = filter_var($shipment_address['post_code'], FILTER_SANITIZE_NUMBER_INT);
                $shipToState = $taxJarAdapter->getUSStateCodeFromZIP($postCode);
                $_SESSION["shipToState"] = $shipToState;
                $shippingAddress = new \DynCom\dc\dcShop\USSalesTax\Address('US',$postCode,$shipToState,$shipment_address['city'],$shipToStreetNo);
                $USSalesTaxEstimate = $taxJarAdapter->getEstimatedTaxesForBasket($currUserBasket,$nexusAddress,$shippingAddress,(float)$_SESSION['shipping_cost']);
                $subtotal += $USSalesTaxEstimate->getAmountToCollect();
                $taxJarAdapter->deleteTaxInfoForBasketHeaderID($currUserBasket->getID());
                $taxJarAdapter->storeTaxInformationForBasket($USSalesTaxEstimate, $currUserBasket, $shopPDO);

            }
            $GLOBALS['us_sales_tax_estimate'] = $USSalesTaxEstimate;
            //US Sales Tax +++

            require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_3.inc.php';
        }
    } elseif ($_GET["action"] == "step3" && !check_mandatory_fields_b2c($_POST, "step2")) {
        $fielderror_1 = true;
        require_once("user_order_step_2.inc.php");
    } else {

        if ($_GET['action'] != 'payment') {
            if ($GLOBALS['visitor']['frontend_login'] == 0 && $_GET['action'] == '') {
                require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
            } else {
                require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_step_1.inc.php';
            }
        }
    }
}


function addressValidation()
{

    if (!$GLOBALS['shop_language']['check_shipping_address']) {
        return true;
    }
    $validAddresses = true;

    $streetSession = trim($_SESSION["visitor_address"]);
    $spacedStreetNoSession = isset($_SESSION['visitor_address_no']) ? ' ' . $_SESSION['visitor_address_no'] : '';
    $citySession = trim($_SESSION['visitor_city']);
    $postCodeSession = trim($_SESSION['visitor_post_code']);
    $countrySession = $_SESSION['visitor_country'] ?: $_SESSION['visitor_country'] ?: 'DE';

    $street = trim($_POST['input_user_street']);
    $spacedStreetNo = $_POST['input_user_street_no'];
    $city = trim($_POST['input_city']);
    $postCode = trim($_POST['input_post_code']);
    $country = $_POST['input_country'] ?: $_POST['input_country'] ?: 'DE';

    if ((($street != $streetSession) && ($spacedStreetNo != $spacedStreetNoSession)) || ($postCode != $postCodeSession) || ($city != $citySession) || ($country != $countrySession)) {
        $addressData = validate_address($street, $spacedStreetNo, $postCode, $city, $country);

        if ($addressData == false || is_array($addressData)) {
            $_SESSION['is_valid_invoice_address'] = $addressData;
            $validAddresses = false;
        } else {
            $_SESSION['is_valid_invoice_address'] = true;
        }

    }

    if ($_POST['input_shipping_is_not_invoice'] == "on") {
        $streetSession = trim($_SESSION["visitor_user_street_shipping"]);
        $spacedStreetNoSession = isset($_SESSION['visitor_user_street_no_shipping']) ? ' ' . $_SESSION['visitor_user_street_no_shipping'] : '';
        $citySession = trim($_SESSION['visitor_city_shipping']);
        $postCodeSession = trim($_SESSION['visitor_post_code_shipping']);
        $countrySession = $_SESSION['visitor_country_shipping'] ?: $_SESSION['visitor_country_shipping'] ?: 'DE';

        $street = trim($_POST['input_user_street_shipping']);
        $spacedStreetNo = $_POST['input_user_street_no_shipping'];
        $city = trim($_POST['input_city_shipping']);
        $postCode = trim($_POST['input_post_code_shipping']);
        $country = $_POST['input_shop_country_shipping'] ?: $_POST['input_shop_country_shipping'] ?: 'DE';


        if ((($street != $streetSession) && ($spacedStreetNo != $spacedStreetNoSession)) || ($postCode != $postCodeSession) || ($city != $citySession) || ($country != $countrySession)) {
            $addressData = array();
            $addressData = validate_address($street, $spacedStreetNo, $postCode, $city, $country);

            if ($addressData == false || is_array($addressData)) {
                $_SESSION['is_valid_shipping_address'] = $addressData;
                $validAddresses = false;
            } else {
                $_SESSION['is_valid_shipping_address'] = true;
            }

        }
    }
    return $validAddresses;
}

?>