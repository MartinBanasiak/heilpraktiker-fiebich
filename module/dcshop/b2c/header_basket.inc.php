<?
$activeClass = "";
$filledClass = "";

if(isset($_GET['shop_category']) && $_GET['shop_category'] == "basket"){
    $activeClass = "active";
}

$totalBasketPos = $currUserBasket->getTotalNoOfPos();

if($totalBasketPos > 0) {
    $filledClass = "filled";
}

?>

<div id="header_basket" class="<?=$activeClass?> <?=$filledClass?>">
    <div class="shopping_bag">
        <a href="/<? echo customizeUrl();?>/basket/">
            <span
                class="hidden-xs hidden-sm hidden-md"><?= $GLOBALS["tc"]["shopping_basket"] ?> </span>
            <?if($totalBasketPos > 0){?><span class="totalBasketpos"><?= $totalBasketPos ?></span><?}?>
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        </a>
    </div>
    <? if ($totalBasketPos > 0) { ?>
        <div class="shopping_bag_hover hidden-xs hidden-sm">
            <div class="basket_area">
                <div class="basket_items">
                    <div class="basket_items_inner">
                        <? $basketItems = basketItems();
                        echo "<div class=\"itemcard_list12 itemcard_list\">";
                        foreach ($basketItems as $item) {
                            $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);

                            if($item['customization_hash'] != "")
                            {
                                if (empty($item['image_data'])) {
                                    $image = get_item_main_image($item, get_item_variant_parent($item));
                                } else {
                                    $image = $item['image_data'][0];

                                    for($i = 0; $i < count($item['image_data']); $i++)
                                    {
                                        if($item['image_data'][$i]['customization'] == 1)
                                        {
                                            $image = $item['image_data'][$i];
                                            $i = count($item['image_data']);
                                        }
                                    }
                                }

                            }
                            else
                            {
                                if (empty($item['image_data'])) {
                                    $image = get_item_main_image($item, get_item_variant_parent($item));
                                } else {
                                    $image = $item['image_data'][0];
                                }
                            }


                            if ($image["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image['filename'])) {
                                if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                                    $image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                                } else {
                                    $image["filename"] = "noimage.jpg";
                                }
                            }

                            $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
                            $itemlink = create_item_link($item);
                            if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
                                $itemlink = create_item_link_tab($item);
                            }
                            ?>
                            <div class="itemlist12 itemlist">
                                <a href="<?= $itemlink ?>">
                                    <div class="itemlist_container">
                                        <div class="itemlist_content image">
                                            <img src="<?= $imagelink ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                                        </div>
                                        <div class="itemlist_content info">
                                            <div class="itemlist_content brand">
                                                <?= get_brand_name($item) ?>
                                            </div>
                                            <div class="itemlist_content description">
                                                <?= $item["description"] ?>
                                            </div>
                                            <div class="itemlist_content prices">
                                                <? if (isset($item['customer_price'])) {
                                                    if (isset($item['cross_price']) && $item['cross_price'] > 0) { ?>
                                                        <div
                                                                class="cross_price"><?= format_amount($item['cross_price'], FALSE) ?></div><? } ?>
                                                    <div class="base_price"><?= format_amount($item['customer_price'], FALSE) ?></div>
                                                <? } elseif ($GLOBALS['shop']['cross_price_typ'] != 0) {
                                                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                                                } else {
                                                    echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), FALSE);
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?
                        }
                        echo "</div>"; ?>
                    </div>
                </div>

                <div class="basket_amount">
                    <div class="basket_no_of_pos">
                        <?= $totalBasketPos ?>&nbsp;<?= $GLOBALS["tc"]["items"] ?>
                    </div>
                    <div class="basket_total">
                        <?= $GLOBALS["tc"]["total"] ?>
                        &nbsp;<?= format_amount($currUserBasket->getBasketTotal(), true, FALSE) ?>
                    </div>
                </div>
                <div class="basket_links">
                    <div class="button_wrapper">
                        <a class="button"
                           href="/<? echo customizeUrl(); ?>/basket/"><?= $GLOBALS["tc"]["to_basket"] ?></a>
                    </div>
                    <div class="button_wrapper">
                        <a class="button button_action"
                           href="/<?echo customizeUrl(); ?>/order/address_select/"><?= $GLOBALS["tc"]["order_basket"] ?></a>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</div>