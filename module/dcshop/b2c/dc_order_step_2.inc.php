<?
$formname = "form_dc_order";
if ($_SESSION["dc_id"]) {
    $dc = get_dc($_SESSION["dc_id"]);
}

// Kategoriebild-Erweiterung HT: Anzeige eines Hauptbildes pro Kategorie
get_category_picture($category["id"]);
get_promotion_description($category);
get_category_description($category["id"]);
// Ende

?>
<h1 class="shop_site_headline"><? echo $GLOBALS["tc"]["dc_order"]; ?></h1>
<div id="dc_order">
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="POST">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <div id="dc_bg_wrapper">
                    <h2><?= $GLOBALS["tc"]["preview"] ?></h2>
                    <? create_card_preview($dc, true, true); ?>
                </div>
                <div id="dc_amnt_select">
                    <h2><?= $GLOBALS["tc"]["amount"] ?></h2>
                    <?= $dc["amount"]; ?> €
                </div>
                <div id="dc_shipping">
                    <h2><?= $GLOBALS["tc"]["shipping"] ?></h2>
                    <? show_dc_shipping_option($dc); ?>
                </div>
                <br/>
                <div class="button_row">
                    <?//= button("dc_order_back button", $GLOBALS["tc"]["back"], $formname, ml("", "action", '1', 'dc_id', $dc["id"])); ?>
                    <a class="button_dc_order_back button" href="javascript:void(0);" onclick="document.form_dc_order.action='?dc_id=<?= $dc["id"] ?>&action=1'; document.form_dc_order.submit(); return false;"><?= $GLOBALS["tc"]["back"] ?></a>
                    <div class="pull-right">
                        <?//= button("dc_order button button_action", $GLOBALS["tc"]["next_step"], $formname, ml("", 'dc_id', $dc["id"])); ?>
                        <a class="button_dc_order button button_action" href="javascript:void(0);" onclick="document.form_dc_order.action='/<?= customizeUrl(); ?>/order/?dc_id=<?= $dc["id"] ?>'; document.form_dc_order.submit(); return false;"><?= $GLOBALS["tc"]["next_step"] ?></a>
                    </div>
                </div>
            </div>
        </div>
        <br/>
    </form>
</div>
