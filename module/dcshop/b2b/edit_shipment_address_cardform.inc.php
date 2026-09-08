<? $formname = "form_shipment_address_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["edit_shipment_address"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
    <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=edit_shipment_address&action_id=list"); ?>
    <br/><br/>
    <input name="input_id" type="hidden" value="<?= $input_shipment_address["id"] ?>">

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
                <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $input_shipment_address["name"], 30) ?>
                <? input_shop($GLOBALS["tc"]["name_2"], "input_name_2", "text", $input_shipment_address["name_2"], 30) ?>
                <? input_shop($GLOBALS["tc"]["contact"], "input_contact", "text", $input_shipment_address["contact"], 30) ?>
                <? input_shop($GLOBALS["tc"]["address"], "input_address", "text", $input_shipment_address["address"], 30) ?>
                <? input_shop($GLOBALS["tc"]["address_2"], "input_address_2", "text", $input_shipment_address["address_2"], 30) ?>
                <? input_shop($GLOBALS["tc"]["post_code"], "input_post_code", "code", $input_shipment_address["post_code"], 6) ?>
                <? input_shop($GLOBALS["tc"]["city"], "input_city", "text", $input_shipment_address["city"], 30) ?>
                <? create_countries("Land", "input_country", 'DE', FALSE, 2) ?>
                <? input_shop($GLOBALS["tc"]["phone_no"], "input_telephone", "text", $input_shipment_address["phone_no"], 30) ?>
        </div>
    </div>
    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["save"], $formname, "?action=edit_shipment_address&action_id=save"); ?>
        <?if(isset($_REQUEST['action_id']) && $_REQUEST['action_id'] != "new"){?>
        <?= button("delete button", $GLOBALS["tc"]["delete"], $formname, "?action=edit_shipment_address&action_id=delete", $GLOBALS["tc"]["delete_ship_addr"]); ?>
        <?}?>

    </div>
</form>