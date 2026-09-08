<div class="basket_infobox">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["greeting_card"] ?></h1>
    <p><strong><?= $GLOBALS["tc"]["greeting_card_header"] ?></strong></p>
</div>

<?php
$formname = "form_greeting_card";
$product_id = $_GET['item'];
$product_count = $_GET['amount'];
$result = get_greeting_items();
show_item_list($result, 13, $formname, $columns = 4);
?>