<div class="basket_infobox">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["greeting_card"] ?></h1>
    <p><strong><?= $GLOBALS["tc"]["enter_greeting_text"] ?></strong></p>
</div>
<?php
$formname = "form_greeting_card_text";

$textValue = '';
$itemKey = '';
if (isset($_REQUEST['action_event']) && $_REQUEST['action_event'] == 'change_greeting_card_text') {
    if (isset($_REQUEST['item_key'])) {
        $itemKey = $_REQUEST['item_key'];

        $pass = $GLOBALS['shop_setup']['shop_password'];
        $pass = pad_string_to_mb_length($pass, 16);

        $key = $pass;
        $encryptedItemKey = base64_decode(filter_var($_REQUEST['item_key'], FILTER_SANITIZE_STRING));

        $basket = $IOCContainer->resolve('$CurrUserBasket');
        if ($basket instanceof \DynCom\dc\dcShop\interfaces\UserBasket) {
            $item = $basket->getItemByKey($encryptedItemKey);
            $textValue = $item->getGreetingCardText();
        }
    }
}

?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="input form-group code ">
                <textarea maxlength="250" name="input_message" id="input_message" class="form-control" rows="5"
                          cols="5"> <?= $textValue ?> </textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
            <div class="itemlist2_basket">
                <a class="button comment_button button_action" href="javascript:void(0);"
                   onclick="document.form_greeting_card_text.action='?item_key=<?= $_REQUEST['item_key'] ?>&amp;action=<?= $_REQUEST['action_event'] ?>&amp;action_id=<?= $_REQUEST['action_id'] ?>'; document.form_greeting_card_text.submit(); return false;">
                    <?= $GLOBALS["tc"]["save"] ?>
                </a>
            </div>
        </div>
    </div>
</form>