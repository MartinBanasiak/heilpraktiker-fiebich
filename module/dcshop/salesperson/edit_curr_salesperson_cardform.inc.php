<? $formname = "form_shop_salesperson_card"; ?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="toolbar">
        <?= button("save", $GLOBALS["tc"]["save"], $formname, "?shop_category=account&action=edit_curr_salesperson&action_id=save"); ?>
        <?= button("reset", $GLOBALS["tc"]["reset"], $formname); ?>
    </div>
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($GLOBALS["tc"]["name"], "input_name", "text", $input_shop_user["name"], 30, TRUE) ?>
                <? input($GLOBALS["tc"]["email"], "input_email", "text", $input_shop_user["email"], 50, TRUE) ?>
                <div class="spacer_6">&nbsp;</div>
                <? input($GLOBALS["tc"]["password"], "input_password", "password", $input_shop_user["password"], 30) ?>
                <? input($GLOBALS["tc"]["password_2"], "input_password_2", "password", $input_shop_user["password_2"], 30) ?>
            </td>
        </tr>
    </table>
</form>
