<?
$formname = "form_dc_order";
if ($_SESSION["dc_id"]) {
    $dc = get_dc($_SESSION["dc_id"]);
}

// Kategoriebild-Erweiterung HT: Anzeige eines Hauptbildes pro Kategorie
get_category_picture($category["id"]);
get_promotion_description($category);
$category_description = get_category_description($category["id"]);
if(!$category_description) {
    echo "<h1 class=\"shop_site_headline\">" . $category['name'] . "</h1>";
}
// Ende
//PH 09.12.2013 Breaks aus Textarea raus +++
$breaks        = array("<br />", "<br>", "<br/>");
$dc["message"] = str_ireplace($breaks, "\r\n", $dc["message"]);
//PH 09.12.2013 Breaks aus Textarea raus ---
?>
<div id="dc_order">
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="POST">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <div id="dc_bg_wrapper">
                    <? if ($_POST["dc_error"] == TRUE) { ?>
                        <? get_requestbox($GLOBALS["tc"]["order_error_fields"])?>
                    <? } ?>
                    <? create_background_select($dc["background_image"]); ?>
                </div>
                <div id="coupon_background_image">
                    <div id="coupon_textfields">

                    </div>
                </div>
                <br/>
                <div id="dc_sender_recipient">
                    <h2><?= $GLOBALS["tc"]["sender_recipient"] ?></h2>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <?input_shop($GLOBALS["tc"]["from_name"], "input_from_name", "text", $dc["from_name"], 50, FALSE);?>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?input_shop($GLOBALS["tc"]["to_name"], "input_to_name", "text", $dc["to_name"], 50, FALSE);?>
                        </div>
                    </div>
                    <br/><br/>
                </div>
                <div id="dc_message">
                    <?input_shop($GLOBALS["tc"]["greeting_message"], "input_message", "textarea", $dc["message"], 256, FALSE);?>
                    <br/><br/>
                </div>
                <div id="dc_amnt_select">
                    <label><?= $GLOBALS["tc"]["dc_choose_amount"] ?></label>
                    <? create_amount_select($dc["amount_id"]); ?>
                    <br/>
                    <? create_individual_amount_select($dc["amount"], $dc["amount_id"], $GLOBALS["tc"]["or"]); ?>
                    <br/>
                </div>
                <div id="dc_shipping">
                    <h2><?= $GLOBALS["tc"]["shipping_options"] ?></h2>
                    <? create_shipping_option_select($dc); ?>
                    <br/>
                    <? create_shipping_inputs($dc); ?>
                </div>
                <div class="button_row text-right">
                    <?//= button("dc_order button button_action", $GLOBALS["tc"]["next_step"], $formname, ml("", "action", '2', 'dc_id', $dc["id"])); ?>
                    <a class="button_dc_order button button_action" href="javascript:void(0);" onclick="document.form_dc_order.action='?dc_id=<?= $dc["id"] ?>&action=2'; document.form_dc_order.submit(); return false;"><?= $GLOBALS["tc"]["next_step"] ?></a>
                </div>
            </div>
        </div>
        <?
        $imgsize = img_size_mm("http://icg-shop/userdata/images/vegandia/allgemein/logo.png", 72);

        ?>
    </form>
</div>
