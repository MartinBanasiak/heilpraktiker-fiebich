<? $formname = "form_shop_customer_card"; ?>

<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["customer_account"] ?></h1>
    <?=$GLOBALS['tc']['shop_account_shop_customer']?>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
    <input name="input_id" id="input_id" type="hidden" value="<?= $input_shop_user["id"] ?>">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
            <div class="form-group">
                <label for="Anrede"><?=$GLOBALS["tc"]["salutation"]?></label>
                <div class='select_body'><select class="select" name='input_customer_salutation'>
                        <option value='Herr' <? if ($input_customer["salutation"] == "Herr") {
                            echo "selected";
                        } ?>><?= $GLOBALS["tc"]["mr"] ?></option>
                        <option value='Frau' <? if ($input_customer["salutation"] == "Frau") {
                            echo "selected";
                        } ?>><?= $GLOBALS["tc"]["mrs"] ?></option>
                    </select></div>
            </div>
            <? input_shop($GLOBALS["tc"]["surname"], "input_customer_name", "text", $input_customer["surname"], 30, FALSE) ?>
            <? input_shop($GLOBALS["tc"]["lastname"], "input_customer_lastname", "text", $input_customer["lastname"], 30) ?>
            <? input_shop($GLOBALS["tc"]["company"], "input_customer_company", "text", $input_customer["company_name"], 45, FALSE) ?>
            <div class="form-group-street">
                <label for="input_shop_customer_address_street"><?= $GLOBALS['tc']['address'] ?></label>
                <div class="input form-group text_street">
                    <input name="input_shop_customer_address_street" id="input_shop_customer_address_street" class="text form-control text_street" type="text" maxlength="30" value="<?= $input_customer["address_street"] ?>">
                </div>
                <div class="input form-group text_street_no">
                    <input name="input_shop_customer_address_no" id="input_shop_customer_address_no"  class="text form-control text_street" type="text" maxlength="4" value="<?= $input_customer["address_no"] ?>">
                </div>
            </div>

            <? input_shop($GLOBALS["tc"]["post_code"], "input_shop_customer_post_code", "code", $input_customer["post_code"], 6, FALSE) ?>
            <? input_shop($GLOBALS["tc"]["city"], "input_shop_customer_city", "code", $input_customer["city"], 30, FALSE);
            if (isset($_SESSION["visitor_country_shipping"]) && $_SESSION["visitor_country_shipping"] != "") {
                $countrycode = $_SESSION["visitor_country_shipping"];
            } elseif (isset($GLOBALS['shop_customer']['country']) && $GLOBALS['shop_customer']['country'] != '') {
                $countrycode = $GLOBALS['shop_customer']['country'];
            } else {
                $countrycode = "DE";
            }
            create_countries($GLOBALS['tc']['country'], "input_country", $countrycode, FALSE); ?>
            <? input_shop($GLOBALS["tc"]["phone_no"], "input_shop_customer_phone", "code", $input_customer["phone_no"], 30, FALSE) ?>
            <input type="hidden" name="save_data" value="1" />
        </div>
    </div>
    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["save"], $formname, "?action=edit_shop_customer&action_id=save"); ?>
    </div>
</form>