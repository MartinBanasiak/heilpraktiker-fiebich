<?
$formname = "form_shop_user_card";
if ($_GET['action_id'] == 'new') {
    $input_shop_user["right_order"] = 1;
}
?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["edit_shop_user"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
    <div class="button_row">
        <?= button("edit button", $GLOBALS["tc"]["list"], $formname, "?action=edit_shop_user&action_id=list"); ?>
    </div>
    <input name="input_id" id="input_id" type="hidden" value="<?= $input_shop_user["id"] ?>">
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-lg-6">
            <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $GLOBALS["shop_customer"]["customer_no"], 30, TRUE) ?>
            <div class="spacer_6">&nbsp;</div>
            <? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $input_shop_user["name"], 30) ?>
            <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $input_shop_user["email"], 50) ?>
            <div class="spacer_6">&nbsp;</div>
            <? shipment_address_select($GLOBALS["shop_customer"], $GLOBALS["tc"]["std_ship_address"], "input_shipment_address_id", $input_shop_user["shop_shipment_address_id"]) ?>
            <div class="spacer_6">&nbsp;</div>
            <? input_shop($GLOBALS["tc"]["username"], "input_login", "code", $input_shop_user["login"], 30) ?>
            <? input_shop($GLOBALS["tc"]["password"], "input_password", "password", $input_shop_user["password"], 30) ?>
            <? input_shop($GLOBALS["tc"]["password_2"], "input_password_2", "password", $input_shop_user["password_2"], 30) ?>
        </div>
        <div class="col-xs-12 col-sm-4 col-lg-6">
            <? input_shop($GLOBALS["tc"]["main_user"], "input_main_user", "checkbox", $input_shop_user["main_user"], 0, TRUE) ?>
            <? input_shop($GLOBALS["tc"]["right_user_mgt"], "input_right_user_management", "checkbox", $input_shop_user["right_user_management"], 0, $input_shop_user["main_user"]) ?>
            <? input_shop($GLOBALS["tc"]["right_order_hist"], "input_right_order_history", "checkbox", $input_shop_user["right_order_history"], 0, $input_shop_user["main_user"]) ?>
            <? input_shop($GLOBALS["tc"]["right_order"], "input_right_order", "checkbox", $input_shop_user["right_order"], 0, $input_shop_user["main_user"]) ?>
        </div>
    </div>

    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["save"], $formname, "?action=edit_shop_user&action_id=save"); ?>
        <?if ($_GET['action_id'] != 'new') {?>
        <?= button("delete button", $GLOBALS["tc"]["delete"], $formname, "?action=edit_shop_user&action_id=delete", $GLOBALS["tc"]["delete_user"]); ?>
        <?}?>

    </div>
</form>