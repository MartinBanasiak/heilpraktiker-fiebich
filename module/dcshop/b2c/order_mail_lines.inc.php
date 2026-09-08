<?php

//US Sales Tax ---
$baseDir = dirname(dirname(dirname(__DIR__)));

$taxEnvDir = $baseDir . '/config/USSalesTax/';
$taxEnvDirexists = is_dir($taxEnvDir) && file_exists($taxEnvDir);
if ($taxEnvDirexists) {
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

    $taxBreakdown = $taxJarAdapter->getTaxBreakdownByWebshopOrderNo((string)$_SESSION['order_no']);
    $GLOBALS['us_sales_tax_breakdown'] = $taxBreakdown;
}
//US Sales Tax +++

$query  = "SELECT * FROM shop_sales_header WHERE order_no = '" . $_SESSION['order_no'] . "' AND company = '" . $GLOBALS['shop']['company'] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $sales_header      = @mysqli_fetch_array($result);
    $query             = "SELECT shop_sales_line.shop_item_id AS 'id', '" . $GLOBALS["shop"]["code"] . "' AS 'shop_code', '" . $GLOBALS["language"]["code"] . "' AS 'language_code',
					 shop_sales_line.item_no, shop_sales_line.description, shop_sales_line.summary, shop_sales_line.allow_invoice_disc, shop_sales_line.list_price, shop_sales_line.unit_price AS 'customer_price', shop_sales_line.quantity AS 'basket_quantity', shop_sales_line.line_amount,
					 shop_item.vat_prod_posting_group
			  FROM shop_sales_line
			  LEFT JOIN shop_item ON shop_item.id = shop_sales_line.shop_item_id
			  WHERE shop_sales_line.shop_sales_header_id = '" . $sales_header["id"] . "'";
    $sales_line_query = $query;

    $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
    ?>
    <? $formname = "form_order_history_card"; ?>
    <h2 style="color: <?=$GLOBALS['shop_setup']['mail_color']?>"><?= $GLOBALS["tc"]["user_order"] ?>: <?= $sales_header["order_no"] ?>
        , <?= datefromsql($sales_header["order_date"]) ?></h2><br /><br />
    <table class="mail_header" border="0" cellspacing="0" cellpadding="8" width="100%">
        <tr>
            <td valign="top" width="35%">
                <strong><?= $GLOBALS["tc"]["invoice_address"] ?></strong><br/>
                <? format_address_with_phone($sales_header["bill_to_name"], $sales_header["bill_to_name_2"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], '', $sales_header["salutation_title"], $sales_header["user_phone_no"],$_SESSION["input_is_company"]); ?>
                <br />
            </td>
            <td valign="top" width="35%">
                <? if ($_SESSION["dc_id"] == '') {
                    ?>
                    <strong><?= $GLOBALS["tc"]["shipment_address"] ?></strong><br/>
                    <?
                    format_address($sales_header["ship_to_name"], $sales_header["ship_to_name_2"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], '');
                } ?>
            </td>
            <td valign="top" width="30%">
                <?
                $query  = "SELECT *
				  FROM shop_shipping_option
				  WHERE line_no = '" . $sales_header['shipping_option_line_no'] . "'
				  	AND shop_code ='" . $GLOBALS['shop']['code'] . "'
				  	AND language_code = '" . $GLOBALS['shop_language']['code'] . "'";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                if (mysqli_num_rows($result) > 0) {
                    $sterms = mysqli_fetch_assoc($result);
                    echo("<strong>" . $GLOBALS["tc"]["ship_type"] . "</strong><br/>");
                    echo($sterms['description']);
                }
                ?>
                <?
                $query  = "SELECT *
				  FROM shop_payment_option
				  WHERE line_no = '" . $sales_header['payment_option_line_no'] . "'
				  	AND shop_code ='" . $GLOBALS['shop']['code'] . "'
				  	AND language_code = '" . $GLOBALS['shop_language']['code'] . "'";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                if (mysqli_num_rows($result) > 0) {
                    $terms = mysqli_fetch_assoc($result);
                    echo("<strong>" . $GLOBALS["tc"]["payment_type"] . "</strong><br/>");
                    echo($terms['description']);
                    if ($terms["checkout"] == 6) {
                        format_address("<br/>" . $GLOBALS["tc"]["bp_acc_owner"] . $sales_header["bp_acc_owner"], $GLOBALS["tc"]["bp_bank"] . $sales_header["bp_bank"], $GLOBALS["tc"]["bp_acc_iban"] . $sales_header["bp_acc_iban"], $GLOBALS["tc"]["bp_acc_nr"] . $sales_header["bp_acc_nr"], $GLOBALS["tc"]["bp_invoice_ref"] . $sales_header["bp_invoice_ref"],'' ,'' , '');
                    }
                    if ($terms["checkout"] == 7) {
                        echo "<br/>" . $GLOBALS["tc"]["invoice_note"];
                    }
                }
                ?>
                <strong><?= ($sales_header["your_reference"] <> '') ? $GLOBALS["tc"]["your_reference"] : "" ?></strong><br/>
                <?= $sales_header["your_reference"] ?>
                <strong><?= ($sales_header["your_comment"] <> '') ? $GLOBALS["tc"]["your_comment"] : "" ?></strong><br/>
                <?= $sales_header["your_comment"] ?>
            </td>
        </tr>
    </table>
    <br />
<?
//SH: Digitaler Gutschein 
    if ($_SESSION["dc_id"] <> '') {
        $dc                            = get_dc($_SESSION["dc_id"]);
        $sales_header['coupon_amount'] = 0;
        $totalquantity                 = 1;
        ?>
        <table border="0" cellpadding="8" cellspacing="0" width="100%">
            <tr>
                <td><strong><?= $GLOBALS["tc"]["item_no"] ?></strong></td>
                <td><strong><?= $GLOBALS["tc"]["description"] ?></strong></td>
                <td width="90" align="right"><strong><?= $GLOBALS["tc"]["your_price"] ?></strong></td>
                <td width="40" align="right"><strong><?= $GLOBALS["tc"]["quantity"] ?></strong></td>
                <td width="90" align="right"><strong><?= $GLOBALS["tc"]["line_amount"] ?></strong></td>
            </tr>
            <tr>
                <td valign="middle"
                    style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;"><?= $GLOBALS["shop"]["gl_account_coupons"] ?></td>
                <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;"><?=$GLOBALS['tc']['digital_coupon']?></td>
                <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90"
                    align="right"><?= format_amount($dc["amount"], FALSE) ?></td>
                <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="40" align="right">1</td>
                <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90"
                    align="right"><?= format_amount($dc["amount"], FALSE) ?></td>
            </tr>
        </table>
    <?
    } else {
        show_item_list($sales_line_result, 6, $formname);
    }
    ?>
    <br />
    <table class="mail_bottom" cellpadding="8" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="left" valign="top" width="50%">
                <strong><?= @mysqli_num_rows($sales_line_result); ?> <?= $GLOBALS["tc"]["item_in_order"] ?></strong><br />
            <td valign="top" align="right" width="50%">
                <?
                mysqli_data_seek($sales_line_result, 0);
                show_order_sum($sales_header["subtotal"], $sales_header["online_discount"], $sales_header["online_discount_amount"], $sales_header["invoice_discount"], $sales_header["invoice_discount_amount"], $sales_header["small_quantity_charge_amount"], $sales_header["total"], $sales_header['shipping_cost'], $sales_header['payment_cost'], TRUE, $sales_line_result);
                ?>
            </td>
        </tr>
    </table>
<?
}
?>