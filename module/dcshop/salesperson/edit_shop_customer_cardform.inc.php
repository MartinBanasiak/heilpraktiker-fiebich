<? $formname = "form_shop_customer_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" id="input_id" type="hidden" value="<?= $input_shop_user["id"] ?>">
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $GLOBALS["shop_customer"]["customer_no"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["name"], "input_customer_name", "text", $input_customer["name"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["name_2"], "input_shop_customer_name_2", "text", $input_customer["name_2"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["address"], "input_shop_customer_address", "text", $input_customer["address"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["address_2"], "input_shop_customer_address_2", "text", $input_customer["address_2"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["post_code"], "input_shop_customer_post_code", "code", $input_customer["post_code"], 6, TRUE) ?>
                <? input($GLOBALS["tc"]["city"], "input_shop_customer_city", "code", $input_customer["city"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["country"], "input_shop_customer_country", "code", $input_customer["country"], 30, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["phone_no"], "input_shop_customer_city", "code", $input_customer["phone_no"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["fax_no"], "input_shop_customer_city", "code", $input_customer["fax_no"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["email"], "input_shop_customer_city", "text", $input_customer["email"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["homepage"], "input_shop_customer_city", "text", $input_customer["homepage"], 30, TRUE) ?>
            </td>
            <td>
                <? if ($GLOBALS["shop_customer"]["bill_to_customer_no"] <> '') { ?>
                    <h3><?= $GLOBALS["tc"]["bill_to_customer"] ?></h3><br />
                    <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $GLOBALS["shop_customer"]["bill_to_customer_no"], 30, TRUE) ?>
                    <div class="spacer_6">&nbsp;</div>
                    <? input($GLOBALS["tc"]["name"], "input_customer_name", "text", $input_customer["bill_to_name"], 30, TRUE) ?>
                    <? input($GLOBALS["tc"]["name_2"], "input_shop_customer_name_2", "text", $input_customer["bill_to_name_2"], 30, TRUE) ?>
                    <? input($GLOBALS["tc"]["address"], "input_shop_customer_address", "text", $input_customer["bill_to_address"], 30, TRUE) ?>
                    <? input($GLOBALS["tc"]["address_2"], "input_shop_customer_address_2", "text", $input_customer["bill_to_address_2"], 30, TRUE) ?>
                    <? input($GLOBALS["tc"]["post_code"], "input_shop_customer_post_code", "code", $input_customer["bill_to_post_code"], 6, TRUE) ?>
                    <? input($GLOBALS["tc"]["city"], "input_shop_customer_city", "code", $input_customer["bill_to_city"], 30, TRUE) ?>
                    <? input($GLOBALS["tc"]["country"], "input_shop_customer_country", "code", $input_customer["bill_to_country"], 30, TRUE) ?>
                <? } ?>
            </td>
        </tr>
    </table>
</form>