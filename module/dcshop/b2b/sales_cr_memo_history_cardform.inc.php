<? $formname = "form_sales_cr_memo_history_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_cr_memo_history"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_header["id"] ?>">

    <div
        class="button_row"><?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=sales_cr_memo_history"); ?>

    </div>
    <?php
    if ($_GET['require'] == 1) {
    ?>
    <div class="infobox infobox-mailsend">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-lg-6">
        <h2><?= $GLOBALS["tc"]["input_email"] ?></h2>
        <br />
        <?
        input_shop($GLOBALS["tc"]["email"], "input_email_request", "text", $GLOBALS["shop_customer"]["email"], 50, FALSE);
        echo "<br />";
        echo button("big button", $GLOBALS["tc"]["require_doc"], $formname, "?action=sales_cr_memo_history&action_id=card&input_id=" . (int)$_GET['input_id'] . "&update_insert=1");
        ?><br />
        <br />
            </div>
        </div>
    </div>
    <?


    }

    if ($_GET['update_insert'] == 1 && $_POST["input_email_request"] != '') {
        $query  = "UPDATE shop_sales_invoice_header
			  SET send_request=1, request_mail= '" . $_POST["input_email_request"] . "',
			  	  request_shop_code = '" . $GLOBALS['shop']['code'] . "', request_language_code = '" . $GLOBALS['shop_language']['code'] . "'
			  WHERE id = '" . $_GET["input_id"] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if ($result == 1) {
            ?>
            <div class="infobox">
                <h3><?= $GLOBALS["tc"]["require_document"] ?></h3>
            </div>
            <?
        }
    }
    ?>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
                <h3><?= $GLOBALS["tc"]["cr_memo_no"] ?>: <?= $sales_header["no"] ?></h3>
                <? input_shop($GLOBALS["tc"]["order_date"], "input_shop_customer_no", "code", datefromsql($sales_header["order_date"]), 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_header["sell_to_customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $sales_header["sell_to_name_2"] . " " . $sales_header["sell_to_name"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_header["your_reference"], 30, TRUE) ?>
        </div>
        <div class="col-xs-12 col-sm-4 col-lg-6">
                <h3>&nbsp;</h3>
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>
                <? format_address($sales_header["bill_to_name_2"], $sales_header["bill_to_name"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>
                <? format_address($sales_header["ship_to_name_2"], $sales_header["ship_to_name"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
         </div>
    </div>

</form>
<? show_item_list($sales_line_result, 8, $formname); ?>
<div class="order_prices_box">
    <div class="order_prices_box_left">
        &nbsp;
    </div>
    <div class="order_prices_box_right">
        <table width="100%">
            <tbody>
            <tr>
                <td class="order_sum_1">
                    <?= $GLOBALS["tc"]["total_amount_vat"];?>
                </td>
                <td class="order_sum_2 text-right">
                    <?=format_amount($sales_header["amount"], FALSE)?>
                </td>
            </tr>
            <tr>
                <td class="order_sum_1">
                    <div class="order_price_total_label"><?= $GLOBALS["tc"]["total_amount"] . $GLOBALS["tc"]["vat"];?></div>
                </td>
                <td class="order_sum_2 text-right">
                    <div class="order_price_total">
                        <?=format_amount($sales_header["amount_including_vat"], FALSE)?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div  class="button_row">
    <a class="button_mail button_action button"
       href="<?= $_SERVER['REQUEST_URI'] . "&input_id=" . $_POST['input_id'] . "&require=1" ?>"><?= $GLOBALS["tc"]["require_document_1"] ?></a>
</div>
