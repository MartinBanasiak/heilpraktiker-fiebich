<?
$formname = "form_sales_shipment_history_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post"><input
        name="input_id" id="input_id" type="hidden"
        value="<?= $sales_shipment_header["id"] ?>">

    <div
        class="toolbar"><?= button("edit", $GLOBALS["tc"]["list"], $formname, "?shop_category=account&action=sales_shipment_history"); ?>
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
            echo button("big", $GLOBALS["tc"]["require_doc"], $formname, "?shop_category=account&action=sales_shipment_history&action_id=show&input_id=" . $_GET['input_id'] . "&update_insert=1");
            ?><br />
            <br />
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
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <h2><?= $GLOBALS["tc"]["user_sales_shipment"] ?>: <?= $sales_shipment_header["no"] ?></h2>
                <br />
                <? input($GLOBALS["tc"]["sales_shipment_date"], "input_shop_customer_no", "code", datefromsql($sales_shipment_header["posting_date"]), 30, TRUE) ?>
                <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_shipment_header["sell_to_customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["name"], "input_name", "text", $sales_shipment_header["sell_to__name_2"] . " " . $sales_shipment_header["sell_to_name"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_customer"]["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $sales_shipment_header["your_reference"], 30, TRUE) ?>
            </td>
            <td width="250">
                <h3><?= $GLOBALS["tc"]["invoice_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_shipment_header["bill_to_name_2"], $sales_shipment_header["bill_to_name"], $sales_shipment_header["bill_to_address"], $sales_shipment_header["bill_to_address_2"], $sales_shipment_header["bill_to_post_code"], $sales_shipment_header["bill_to_city"], $sales_shipment_header["bill_to_country"], ''); ?>
                <br />

                <h3><?= $GLOBALS["tc"]["shipment_address"] ?></h3>

                <div class="spacer_6">&nbsp;</div>
                <? format_address($sales_shipment_header["ship_to_name_2"], $sales_shipment_header["ship_to_name"], $sales_shipment_header["ship_to_address"], $sales_shipment_header["ship_to_address_2"], $sales_shipment_header["ship_to_post_code"], $sales_shipment_header["ship_to_city"], $sales_shipment_header["ship_to_country"], $sales_shipment_header["ship_to_contact"], ''); ?>
            </td>
        </tr>
    </table>
</form>
<? show_item_list($sales_shipment_line_result, 7, $formname); ?>

