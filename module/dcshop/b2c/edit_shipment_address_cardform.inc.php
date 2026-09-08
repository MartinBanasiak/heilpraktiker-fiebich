<? $formname = "form_shipment_address_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["shipment_addresses"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
    <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=edit_shipment_address&action_id=list"); ?>
    <br/><br/>
    <input name="input_id" type="hidden" value="<?= $input_shipment_address["id"] ?>">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
            <? input_shop($GLOBALS["tc"]["surname"], "input_name", "text", $input_shipment_address["surname"], 30) ?>
            <? input_shop($GLOBALS["tc"]["lastname"], "input_lastname", "text", $input_shipment_address["lastname"], 30) ?>
            <? input_shop($GLOBALS["tc"]["company"], "input_name_2", "text", $input_shipment_address["company_name"], 45) ?>
            <div class="form-group-street">
                <label for="input_address_street"><?= $GLOBALS["tc"]["street_and_no"] ?> *</label>
                <div class="input form-group text_street">
                    <input type="text"  value="<?= $input_shipment_address["address_street"] ?>" maxlength="30" class="text form-control text_street" id="input_address_street" name="input_address_street">
                </div>
                <div class="input form-group text_street_no">
                    <input type="text" value="<?= $input_shipment_address["address_no"] ?>" maxlength="4" class="text form-control text_street" id="input_address_no" name="input_address_no">
                </div>
            </div>
            <? input_shop($GLOBALS["tc"]["post_code"], "input_post_code", "code", $input_shipment_address["post_code"], 6) ?>
            <? input_shop($GLOBALS["tc"]["city"], "input_city", "text", $input_shipment_address["city"], 30) ?>
            <?
                $countrycode = "DE";
            if (isset($input_shipment_address["country"]) && !empty($input_shipment_address["country"])) {
                $countrycode = $input_shipment_address["country"];
            }
            create_countries($GLOBALS['tc']['country'], "input_country", $countrycode, FALSE, 2); ?>
            <input type="hidden" name="save_data" value="1" />
        </div>
    </div>

    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["save"], $formname, "?action=edit_shipment_address&action_id=save"); ?>
        <?
            if (isset($input_shipment_address["id"]) && $input_shipment_address["id"] != "" )
            {
                echo  button("delete button", $GLOBALS["tc"]["delete"], $formname, "?action=edit_shipment_address&action_id=delete", $GLOBALS["tc"]["delete_ship_addr"]);
            }
        ?>
    </div>
</form>