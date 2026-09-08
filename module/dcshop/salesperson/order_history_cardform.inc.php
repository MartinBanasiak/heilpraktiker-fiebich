<?
$formname = "form_order_history_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_header["id"] ?>">

    <div
        class="toolbar"><?= button("edit", $GLOBALS["tc"]["list"], $formname, "?shop_category=account&action=order_history"); ?>
    </div>
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <h2><?= $GLOBALS["tc"]["user_order"] ?>: <?= $sales_header["order_no"] ?></h2>
                <br />
                <? input($GLOBALS["tc"]["order_date"], "input_shop_customer_no", "code", datefromsql($sales_header["order_date"]), 30, TRUE) ?>
                <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_header["customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["name"], "input_name", "text", $sales_header["user_name"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["email"], "input_email", "text", $sales_header["user_email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_header["your_reference"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["your_comment"], "input_your_comment", "text", $sales_header["your_comment"], 160, TRUE) ?>
            </td>
            <td>
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_header["bill_to_name"], $sales_header["bill_to_name_2"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_header["ship_to_name"], $sales_header["ship_to_name_2"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
            </td>
        </tr>
    </table>
</form>
<? show_item_list($sales_line_result, 5, $formname); ?>
<div class="infobox" style="text-align: right;">
    <table class="order_history" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="left" valign="top">
                <h3><?= @mysqli_num_rows($sales_line_result); ?> <?= $GLOBALS["tc"]["item_in_order"] ?></h3>
                <br />
                <?= $GLOBALS["tc"]["vat_message"] ?><br />
                <?= $GLOBALS["tc"]["shipment_message"] ?></td>
            <td valign="top"
                style="float: right;"><? show_order_sum($sales_header["subtotal"], $sales_header["online_discount"], $sales_header["online_discount_amount"], $sales_header["invoice_discount"], $sales_header["invoice_discount_amount"], $sales_header["small_quantity_charge_amount"], $sales_header["total"]) ?>
            </td>
        </tr>
    </table>
</div>
