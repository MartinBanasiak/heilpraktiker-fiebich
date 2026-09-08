<?
$formname = "form_order_history_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["order_history"] ?></h1>
</div>
<div class="button_row">
    <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "account/?action=order_history"); ?>
</div>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_header["id"] ?>">

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
                <h3><?= $GLOBALS["tc"]["user_order"] ?>: <?= $sales_header["order_no"] ?></h3>
                <? input_shop($GLOBALS["tc"]["order_date"], "input_shop_customer_no", "code", datefromsql($sales_header["order_date"]), 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_header["customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $sales_header["user_name"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $sales_header["user_email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_header["your_reference"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["your_comment"], "input_your_comment", "text", $sales_header["your_comment"], 160, TRUE) ?>
        </div>
        <div class="col-xs-12 col-sm-4 col-lg-6">
                <h3>&nbsp;</h3>
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>
                <? format_address($sales_header["bill_to_name"], $sales_header["bill_to_name_2"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>
                <? format_address($sales_header["ship_to_name"], $sales_header["ship_to_name_2"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
        </div>
    </div>
</form>
<? show_item_list($sales_line_result, 5, $formname); ?>
<div class="order_prices_box">
    <div class="order_prices_box_left">
        <?= @mysqli_num_rows($sales_line_result); ?> <?= $GLOBALS["tc"]["item_in_order"] ?><br/><br/>
        <?= $GLOBALS["tc"]["vat_message"] ?><br />
        <?= $GLOBALS["tc"]["shipment_message"] ?>
    </div>
    <div class="order_prices_box_right">
        <? show_order_sum($sales_header["subtotal"], $sales_header["online_discount"], $sales_header["online_discount_amount"], $sales_header["invoice_discount"], $sales_header["invoice_discount_amount"], $sales_header["small_quantity_charge_amount"], $sales_header["total"], $sales_header["shipping_cost"], $sales_header["payment_cost"], true, $sales_header) ?>
    </div>
</div>

<div class="button_row">
    <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=order_history"); ?>
</div>

