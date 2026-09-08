<?
$formname = "form_sales_invoice_history_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_invoice_header["id"] ?>">

    <div
        class="toolbar toolbar-list"><?= button("edit", $GLOBALS["tc"]["list"], $formname, "?shop_category=account&action=sales_invoice_history"); ?>
        <a class="button_mail" style="cursor: pointer;"
           href="<?= $_SERVER['REQUEST_URI'] . "&input_id=" . $_POST['input_id'] . "&require=1" ?>"><?= $GLOBALS["tc"]["require_document_1"] ?></a>
    </div>

    <?

    if ($_GET['require'] == 1) {
        ?>
        <div class="infobox infobox-mailsend">
            <h2><?= $GLOBALS["tc"]["input_email"] ?></h2>
            <br />
            <?
            input($GLOBALS["tc"]["email"], "input_email_request", "text", $GLOBALS["shop_customer"]["email"], 50, FALSE);
            echo "<br />";
            echo button("big", $GLOBALS["tc"]["require_doc"], $formname, "?shop_category=account&action=sales_invoice_history&action_id=show&input_id=" . $_GET['input_id'] . "&update_insert=1");
            ?><br />
            <br />
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

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <h2><?= $GLOBALS["tc"]["user_invoice_shipment"] ?>: <?= $sales_invoice_header["no"] ?></h2>
                <br />
                <? input($GLOBALS["tc"]["sales_invoice_date"], "input_shop_customer_no", "code", datefromsql($sales_invoice_header["posting_date"]), 30, TRUE) ?>
                <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_invoice_header["sell_to_customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["name"], "input_name", "text", $sales_invoice_header["sell_to_name_2"] . " " . $sales_invoice_header["sell_to_name"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_invoice_header["your_reference"], 30, TRUE) ?>
            </td>
            <td width="250">
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_invoice_header["bill_to_name_2"], $sales_invoice_header["bill_to_name"], $sales_invoice_header["bill_to_address"], $sales_invoice_header["bill_to_address_2"], $sales_invoice_header["bill_to_post_code"], $sales_invoice_header["bill_to_city"], $sales_invoice_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_invoice_header["ship_to_name_2"], $sales_invoice_header["ship_to_name"], $sales_invoice_header["ship_to_address"], $sales_invoice_header["ship_to_address_2"], $sales_invoice_header["ship_to_post_code"], $sales_invoice_header["ship_to_city"], $sales_invoice_header["ship_to_country"], $sales_invoice_header["ship_to_contact"], ''); ?>
            </td>
        </tr>
    </table>

</form>
<? show_item_list($sales_invoice_line_result, 8, $formname); ?>
<div class="infobox" style="text-align: right;">
    <table class="order_history" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td valign="top"
                style="float: right; text-align: right; width: 500px;"><?= $GLOBALS["tc"]["total_amount_vat"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . format_amount($sales_invoice_header["amount"], FALSE) ?>
                <h3><?= $GLOBALS["tc"]["total_amount"] . $GLOBALS["tc"]["vat"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?><?= format_amount($sales_invoice_header["amount_including_vat"], FALSE) ?></h3>
            </td>
        </tr>
    </table>
</div>
