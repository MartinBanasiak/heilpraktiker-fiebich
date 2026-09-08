<?
$formname = "form_order_history_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["order_history"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" id="input_id" type="hidden" value="<?= $sales_header["id"] ?>">
    <div class="button_row">
        <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=order_history", '', FALSE); ?>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="order_devision_headline order_devision_headline_small">
                <?= $GLOBALS["tc"]["user_order"] ?>: <?= $sales_header["order_no"] ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-8 col-lg-6">
            <div class="form-label-left">
                <? input_shop($GLOBALS["tc"]["order_date"], "input_shop_customer_no", "code", datefromsql($sales_header["order_date"]), 30, TRUE) ?>
                <? //input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $sales_header["customer_no"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $sales_header["user_name"], 30, TRUE) ?>
                <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $sales_header["user_email"], 50, TRUE) ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-lg-6">
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <strong><?= $GLOBALS["tc"]["invoice_address"] ?></strong>
                    <br/>
                    <? format_address($sales_header["bill_to_name"], $sales_header["bill_to_name_2"], $sales_header["bill_to_address"], $sales_header["bill_to_address_2"], $sales_header["bill_to_post_code"], $sales_header["bill_to_city"], $sales_header["bill_to_country"], ''); ?>
                    <br/>
                </div>
                <div class="col-xs-12 col-lg-6">
                    <strong><?= $GLOBALS["tc"]["shipment_address"] ?></strong>
                    <br/>
                    <? format_address($sales_header["ship_to_name"], $sales_header["ship_to_name_2"], $sales_header["ship_to_address"], $sales_header["ship_to_address_2"], $sales_header["ship_to_post_code"], $sales_header["ship_to_city"], $sales_header["ship_to_country"], $sales_header["ship_to_contact"], ''); ?>
                    <br/>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <? if ($_SESSION["dc_id"] == '') { ?>
                        <strong><?= $GLOBALS["tc"]["ship_type"] ?></strong>
                        <br>
                        <?
                        $query = "SELECT *
                                      FROM shop_shipping_option
                                      WHERE line_no = '" . $sales_header['shipping_option_line_no'] . "'
                                        AND shop_code ='" . $sales_header['shop_code'] . "'
                                        AND language_code = '" . $sales_header['language_code'] . "'
                                        AND company = '" . $sales_header['company'] . "'";

                        $result = mysqli_query($GLOBALS['mysql_con'], $query);
                        if (mysqli_num_rows($result) > 0) {
                            $sterms = mysqli_fetch_assoc($result);
                            echo($sterms['description']);
                        }
                        ?>
                        <br/>
                    <? } ?>
                </div>

                <div class="col-xs-12 col-lg-6">
                        <strong><?= $GLOBALS["tc"]["payment_type"] ?></strong>
                        <br>
                    <?
                    $showBankInfo = false;
                    $query = "SELECT *
                                      FROM shop_payment_option
                                      WHERE line_no = '" . $sales_header['payment_option_line_no'] . "'
                                        AND shop_code ='" . $sales_header['shop_code'] . "'
                                        AND language_code = '" . $sales_header['language_code'] . "'
                                        AND company = '" . $sales_header['company'] . "'";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (mysqli_num_rows($result) > 0) {
                        $terms = mysqli_fetch_assoc($result);
                        echo($terms['description']);
                    } ?>
                </div>
            </div>
        </div>
    </div>
</form>
<hr/>


<?
if ($isCouponOrder) {
    ?>
    <h2 style="margin-top:0"><?= $GLOBALS['tc']['coupon_overview'] ?></h2>
    <?
    show_item_list($couponData, 14, $formname);
} else {
    ?>
    <h2 style="margin-top:0"><?= $GLOBALS['tc']['item_overview'] ?></h2>
    <?
    show_item_list($sales_line_result, 5, $formname);
}


?>
<div class="order_prices_box">
    <div class="order_prices_box_left">
        <?
        if ($isCouponOrder) {
            echo @mysqli_num_rows($couponData);
            echo ' ' . $GLOBALS["tc"]["coupon_in_order"];

        } else {
            echo @mysqli_num_rows($sales_line_result);
            echo ' ' . $GLOBALS["tc"]["item_in_order"];
        }
        ?>
    </div>
    <div class="order_prices_box_right">
        <? show_order_sum($sales_header["subtotal"], $sales_header["online_discount"], $sales_header["online_discount_amount"], $sales_header["invoice_discount"], $sales_header["invoice_discount_amount"], $sales_header["small_quantity_charge_amount"], $sales_header["total"], $sales_header["shipping_cost"], $sales_header["payment_cost"], TRUE, $slr); ?>

        <? if (!$isCouponOrder) {
            show_vat($slr, $sales_header['shipping_cost'], $sales_header['payment_cost']);
        } ?>

    </div>
</div>
<div class="button_row">
    <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=order_history", '', FALSE); ?>
</div>
