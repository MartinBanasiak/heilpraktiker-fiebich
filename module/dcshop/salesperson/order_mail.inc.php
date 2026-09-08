<?
$query  = "SELECT * FROM shop_sales_header WHERE order_no = '" . $_SESSION['order_no'] . "' AND company = '" . $GLOBALS['shop']['company'] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $sales_header      = @mysqli_fetch_array($result);
    $query             = "SELECT shop_item_id AS 'id', '" . $GLOBALS["shop"]["code"] . "' AS 'shop_code', '" . $GLOBALS["language"]["code"] . "' AS 'language_code',
					 item_no, description, summary, allow_invoice_disc, list_price, unit_price, quantity AS 'basket_quantity', line_amount
			  FROM shop_sales_line
			  WHERE shop_sales_header_id = '" . $sales_header["id"] . "'";
    $sales_line_query = $query;
    $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
    ?>
    <? $formname = "form_order_history_card"; ?>
    <h2><?= $GLOBALS["tc"]["user_order"] ?>: <?= $sales_header["order_no"] ?>
        , <?= datefromsql($sales_header["order_date"]) ?></h2><br />
    <table class="mail_header" border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <td valign="top" width="35%">
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>
                <? format_address($sales_header["bill_to_name"], $sales_header["bill_to_name_2"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                <br />
            </td>
            <td valign="top" width="35%">
                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>
                <? format_address($sales_header["ship_to_name"], $sales_header["ship_to_name_2"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
            </td>
            <td valign="top" width="30%">
                <h3><?= ($sales_header["your_reference"] <> '') ? $GLOBALS["tc"]["your_reference"] : "" ?></h3>
                <?= $sales_header["your_reference"] ?>
                <h3><?= ($sales_header["your_comment"] <> '') ? $GLOBALS["tc"]["your_comment"] : "" ?></h3>
                <?= $sales_header["your_comment"] ?>
            </td>
        </tr>
    </table>
    <br />
    <? show_item_list($sales_line_result, 6, $formname); ?>
    <br />
    <table class="mail_bottom" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="left" valign="top" width="50%">
                <h3"><?= @mysqli_num_rows($sales_line_result); ?> <?= $GLOBALS["tc"]["item_in_order"] ?></h3><br />
                <?= $GLOBALS["tc"]["vat_message"] ?><br /><?= $GLOBALS["tc"]["shipment_message"] ?></td>
            <td valign="top" align="right" width="50%">
                <? show_order_sum($sales_header["subtotal"], $sales_header["online_discount"], $sales_header["online_discount_amount"], $sales_header["invoice_discount"], $sales_header["invoice_discount_amount"], $sales_header["small_quantity_charge_amount"], $sales_header["total"], $sales_header['shipping_cost']) ?>
            </td>
        </tr>
    </table>
<?
}
?>