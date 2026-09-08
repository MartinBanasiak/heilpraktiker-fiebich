<? $formname = "form_shipment_address_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_shipment_address["id"] ?>">

    <div class="toolbar">
        <?= button("edit", $GLOBALS["tc"]["list"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=list"); ?>
        <?= button("new", $GLOBALS["tc"]["new"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=new"); ?>
        <?= button("delete", $GLOBALS["tc"]["delete"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=delete", $GLOBALS["tc"]["delete_ship_addr"]); ?>
        <?= button("save", $GLOBALS["tc"]["save"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=save"); ?>
        <?= button("reset", $GLOBALS["tc"]["reset"], $formname); ?>
    </div>
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($GLOBALS["tc"]["name"], "input_name", "text", $input_shipment_address["name"], 30) ?>
                <? input($GLOBALS["tc"]["name_2"], "input_name_2", "text", $input_shipment_address["name_2"], 30) ?>
                <? input($GLOBALS["tc"]["contact"], "input_contact", "text", $input_shipment_address["contact"], 30) ?>
                <? input($GLOBALS["tc"]["address"], "input_address", "text", $input_shipment_address["address"], 30) ?>
                <? input($GLOBALS["tc"]["address_2"], "input_address_2", "text", $input_shipment_address["address_2"], 30) ?>
                <? input($GLOBALS["tc"]["post_code"], "input_post_code", "code", $input_shipment_address["post_code"], 6) ?>
                <? input($GLOBALS["tc"]["city"], "input_city", "text", $input_shipment_address["city"], 30) ?>
                <? create_countries("Land", "input_country", 'DE', FALSE, 2) ?>
                <? input($GLOBALS["tc"]["phone_no"], "input_telephone", "text", $input_shipment_address["phone_no"], 30) ?>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>