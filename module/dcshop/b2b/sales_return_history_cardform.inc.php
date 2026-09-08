<?
if($GLOBALS["shop_user"]["right_return_order"] && $GLOBALS["shop_setup"]["show_rma"] == 1) {

    $formname = "form_sales_return_history_card"; ?>
    <div class="category_info">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_return_history"] ?></h1>
    </div>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post"><input
                name="input_id" id="input_id" type="hidden"
                value="<?= $sales_header["id"] ?>">

        <div
                class="button_row"><?php button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=sales_return_history"); ?>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                <h3><?= $GLOBALS["tc"]["rma"] ?>: <?= $sales_header["no"] ?></h3>
                <? input_shop($GLOBALS["tc"]["order_date"], "input_shop_customer_no", "code", datefromsql($sales_header["order_date"]), 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_header["sell_to_customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $sales_header["sell_to_name_2"] . " " . $sales_header["sell_to_name"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_header["your_reference"], 30, TRUE) ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>
                <? format_address($sales_header["bill_to_name_2"], $sales_header["bill_to_name"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                <br/>

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>
                <? format_address($sales_header["ship_to_name_2"], $sales_header["ship_to_name"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
            </div>
        </div>

    </form>
    <? show_item_list($sales_line_result, 9, $formname);
}?>

