<? $formname = "form_shop_user_card"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["user_account"] ?></h1>
    <?=$GLOBALS['tc']['shop_account_curr_shop_user']?>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" class="form-label-left">
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
            <? input_shop($GLOBALS["tc"]["old_password"], "input_old_password", "password","", 30) ?>
            <? input_shop($GLOBALS["tc"]["password"], "input_password", "password", "", 30) ?>
            <? input_shop($GLOBALS["tc"]["password_2"], "input_password_2", "password", "", 30) ?>
            <? input("", "input_score", "hidden", 0, 45) ?>
        </div>
    </div>
    <div class="button_row">
        <?= button("save button button_action", $GLOBALS["tc"]["save"], $formname, "?action=edit_curr_shop_user&action_id=save"); ?>
    </div>
</form>
<link rel="stylesheet" type="text/css" href="<?= $GLOBALS['projectRoot'] ?>/components/password-strength-meter/dist/password.min.css">
<script type="text/javascript">

    jQuery(document).ready(function($) {
        var  options = {
            shortPass: '<?= $GLOBALS['tc']['short_password'] ?> ',
            badPass: '<?= $GLOBALS['tc']['weak_password'] ?> ',
            goodPass:  '<?= $GLOBALS['tc']['good_password'] ?> ',
            strongPass:  '<?= $GLOBALS['tc']['strong_password'] ?> ',
            containsUsername: '',
            enterPass: '',
            showPercent: false,
            showText: false, // shows the text tips
            animate: true, // whether or not to animate the progress bar on input blur/focus
            animateSpeed: 'fast', // the above animation speed
            username: false, // select the username field (selector or jQuery instance) for better password checks
            usernamePartialMatch: true, // whether to check for username partials
            minimumLength: '<?= $GLOBALS['shop_setup']['password_strength_length'] ?>' // minimum password length (below this threshold, the score is 0)
        };

        $('#input_password').password(options).bind('password.score', function (e, score) {
            $('#input_score').val(score);
        });

    });


</script>

