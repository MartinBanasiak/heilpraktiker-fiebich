<?php
$returnable = FALSE;
if (isset($sales_shipment_header['posting_date'])) {
    while ($line = mysqli_fetch_assoc($sales_shipment_line_result)) {
        if (($line['type'] == 2) && ($line['quantity'] > 0) && (($line['quantity'] - $line['return_quantity']) > 0)) {
            $returnable = TRUE;
        }
    }

    if ($GLOBALS['shop_typ'] == 1 && (int)$GLOBALS['shop']['max_days_shipment_returnable'] > 0) {
        $postingDate = date_create($sales_shipment_header['posting_date']);
        $cutoffDate  = $postingDate;
        date_sub($cutoffDate, date_interval_create_from_date_string((string)$GLOBALS['shop']['max_days_shipment_returnable'] . ' days'));
        if ($postingDate > $cutoffDate) {
            $returnable = FALSE;
        }
    }
}
mysqli_data_seek($sales_shipment_line_result, 0);
$formname = "form_sales_shipment_history_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_shipment_history"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_shipment_header["id"] ?>">

    <div
        class="button_row"><?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=sales_shipment_history"); ?>


    </div>
    <?
    if ($_GET['require'] == 1) {
        ?>
        <div class="infobox infobox-mailsend">
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-lg-6">
                    <h2><?= $GLOBALS["tc"]["input_email"] ?></h2>
                    <?
                    input_shop($GLOBALS["tc"]["email"], "input_email_request", "text", $GLOBALS["shop_customer"]["email"], 50, FALSE);
                    echo "<br />";
                    echo button("big button", $GLOBALS["tc"]["require_doc"], $formname, "?action=sales_shipment_history&action_id=card&input_id=" . (int)$_GET['input_id'] . "&update_insert=1");
                    ?>
                </div>
            </div>
            <hr/>
        </div>
    <?
    }

    if ($_GET['update_insert'] == 1 && $_POST["input_email_request"] != '') {
        $query  = "UPDATE shop_sales_shipment_header
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
                <h3><?= $GLOBALS["tc"]["user_sales_shipment"] ?>: <?= $sales_shipment_header["no"] ?></h3>
                <? input_shop($GLOBALS["tc"]["sales_shipment_date"], "input_shop_customer_no", "code", datefromsql($sales_shipment_header["posting_date"]), 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_shipment_header["sell_to_customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $sales_shipment_header["sell_to__name_2"] . " " . $sales_shipment_header["sell_to_name"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input_shop($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_shipment_header["your_reference"], 30, TRUE) ?>
        </div>
        <div class="col-xs-12 col-sm-4 col-lg-6">
                <h3>&nbsp;</h3>
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>
                <? format_address($sales_shipment_header["bill_to_name_2"], $sales_shipment_header["bill_to_name"], $sales_shipment_header["bill_to_address"], $sales_shipment_header["bill_to_address_2"], $sales_shipment_header["bill_to_post_code"], $sales_shipment_header["bill_to_city"], $sales_shipment_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>
                <? format_address($sales_shipment_header["ship_to_name_2"], $sales_shipment_header["ship_to_name"], $sales_shipment_header["ship_to_address"], $sales_shipment_header["ship_to_address_2"], $sales_shipment_header["ship_to_post_code"], $sales_shipment_header["ship_to_city"], $sales_shipment_header["ship_to_country"], $sales_shipment_header["ship_to_contact"], ''); ?>
        </div>
    </div>

</form>
<? show_item_list($sales_shipment_line_result, 7, $formname); ?>
<div      class="button_row">
    <a class="button_mail button "
       href="<?= $_SERVER['REQUEST_URI'] . "&input_id=" . $_POST['input_id'] . "&require=1" ?>"><?= $GLOBALS["tc"]["require_document_1"] ?></a>
    <?
    if ($returnable && $GLOBALS["shop_setup"]["show_rma"] == 1) {
        echo button("to_rma button ", $GLOBALS["tc"]["return_request"], $formname,  "/".customizeUrl()."/rma/?action=view-shipment&input_header_id=" . $sales_shipment_header['id']);
    }
    ?>
</div>
