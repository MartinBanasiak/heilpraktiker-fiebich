<div id="user_basket">
    <?php
    use DynCom\dc\dcShop\interfaces\UserBasket;

    $query_vat_result = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.customer_price AS 'customer_price'
  FROM shop_user_basket
  INNER JOIN shop_view_active_item ON shop_view_active_item.id = shop_user_basket.shop_item_id
  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
  ORDER BY shop_user_basket.insert_datetime";
    $vat_result = @mysqli_query($GLOBALS['mysql_con'], $query_vat_result);
    ?>

    <? $formname = "form_user_basket"; ?>

    <?

    $greatingCardItems = get_greeting_items();
    $hasGreetingCard = false;
    if (count($greatingCardItems) > 0) {
        $hasGreetingCard = true;
    }

    $query = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.variant_code AS 'var_code',
				 shop_user_basket.customer_price AS 'customer_price',shop_user_basket.changed_to_minimum AS 'changed_to_minimum',
				 shop_user_basket.changed_to_vpe AS 'changed_to_vpe',shop_user_basket.id AS 'basket_id',shop_user_basket.package_for_item
		  FROM shop_view_active_item
		  LEFT JOIN shop_user_basket ON shop_view_active_item.id = shop_user_basket.shop_item_id
		  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  ORDER BY shop_user_basket.insert_datetime";
    //		  GROUP BY shop_user_basket.shop_item_id,shop_user_basket.variant_code,shop_user_basket.package_for_item


    if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
        $currUserBasket = $IOCContainer->create('$CurrUserBasket');
    }
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $itemArr = basketItems();

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $resultCount = count($itemArr);
    if ($resultCount == 0) {
        ?>
        <? /*if ($_SESSION['search'] != "") { ?>
        <div class="toolbar">
            <a class="button_back"
               href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search&term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
        </div>
    <? }*/ ?>
        <?
    }
    if ($resultCount > 0) {
        ?>
        <div class="toolbar">
            <? /*if ($_SESSION['search'] != "") { ?>
            <a class="button_back"
               href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search&term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
        <? }*/ ?>
            <div class="basket_infobox">
                <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["my_shopping_basket"] ?></h1>
                <? if ($GLOBALS['shop_language']['shopping_basket_text_module'] != '') {
                    $spacer = array();
                    echo(get_text_module(
                        $GLOBALS['shop_language']['company'],
                        $GLOBALS['shop_language']['shopping_basket_text_module'],
                        $spacer
                    ));
                } ?>
                <?= $GLOBALS["tc"]["item_in_basket_1"] ?><?= round(
                    $currUserBasket->getTotalNoOfPos()
                ) ?> <?= $GLOBALS["tc"]["item_in_basket_2"] ?><? //$GLOBALS["tc"]["basket_total"] ?><?= format_amount(
                    $currUserBasket->getBasketTotal(),
                    true
                ) ?>.</h3></td>
            </div>
        </div>
        <div class="button_row text-right">
            <? if ($hasGreetingCard) { ?>
                <a class="basket_button_next button button_action text-center"
                   href="?action=greeting_card"><?= $GLOBALS["tc"]["add_greeting_card"] ?></a>
            <? } ?>
            <a onclick="$('#<?= $formname ?>').attr('action',$('#<?= $formname ?>').attr('action') + '&redirect=<? echo urlencode("/".customizeUrl()."/order/address_select/"); ?>'); $('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
               class="basket_button_next button button_action text-center"
               href="<? echo "/".customizeUrl()."/order/address_select/"; ?>"><?= $GLOBALS["tc"]["order_basket"] ?></a>
        </div>
        <? show_item_list($itemArr, 4, $formname); ?>
        <div class="user_basket_refresh_buttons">
            <a onclick="$('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
               class="button_refresh button"><?= $GLOBALS["tc"]["refresh_basket"] ?></a>
            &nbsp;&nbsp;<?= button(
                "delete button",
                $GLOBALS["tc"]["empty_basket"],
                $formname,
               "?action=shop_empty_user_basket",
                $GLOBALS["tc"]["empty_basket_conf"]
            ); ?>
        </div>
        <div class="user_basket_bottom order_prices_box">
            <div class="user_basket_bottom_left order_prices_box_left">
                <div class="voucher_info"><?= $GLOBALS["tc"]["voucher_info"] ?></div>
            </div>
            <div class="user_basket_bottom_right order_prices_box_right">
                <?php
                $appliedInvoiceDiscounts = $currUserBasket->getAppliedInvoiceDiscounts();
                if (count($appliedInvoiceDiscounts) > 0) {
                    /**
                     * @var $appliedInvoiceDiscount \DynCom\dc\dcShop\classes\AppliedDiscount
                     */
                    foreach ($appliedInvoiceDiscounts as $appliedInvoiceDiscount) {
                        $desc = $GLOBALS['tc']['discount'] . ' ';
                        if ($appliedInvoiceDiscount->getDiscountValueType(
                            ) === \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_VALUE_TYPE_PERCENT
                        ) {
                            $desc .= round($appliedInvoiceDiscount->getDiscountValue(), 2) . '%';
                        }
                        $val = format_amount(round($appliedInvoiceDiscount->getDiscountedAmount(), 2));

                        ?>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6">
                                <div class="order_sum_2"><?= $desc ?></div>
                            </div>
                            <div class="col-xs-6 col-sm-6 text-right">
                                <div class="order_sum_2"><?= $val ?></div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <div class="row">
                    <div class="col-xs-6 col-sm-6">
                        <div class="order_price_total_label"><?= $GLOBALS["tc"]["total_amount"] . " " ?></div>
                    </div>
                    <div class="col-xs-6 col-sm-6 text-right">
                        <div class="order_price_total"><?= format_amount($currUserBasket->getBasketTotal()) ?></div>
                    </div>
                </div>
                <table class="basket_bottom" cellpadding="0" cellspacing="0" border="0" width="100%"><?
                    $vatPerGroup = $currUserBasket->getVATAmountsPerVATPercent();
                    foreach ($vatPerGroup as $vatPercent => $vatAmnt) {
                        echo("<tr>");
                        echo("<td class=\"order_sum_1\">" . $GLOBALS["tc"]["incl_tax"] . " " . $vatPercent . "%</td>");
                        echo("<td class=\"order_sum_2 text-right\">" . format_amount($vatAmnt) . "</td>");
                        echo("</tr>");
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="button_row text-right">
            <br/>
            <? if ($hasGreetingCard) { ?>
                <a class="basket_button_next button button_action text-center"
                   href="?action=greeting_card"><?= $GLOBALS["tc"]["add_greeting_card"] ?></a>
            <? } ?>

            <? create_amazon_payment_button(); ?>
            <? create_paypal_express(); ?>

            <a onclick="$('#<?= $formname ?>').attr('action',$('#<?= $formname ?>').attr('action') + '&redirect=<? echo urlencode("/".customizeUrl()."/order/address_select/"); ?>'); $('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
               class="pull-right basket_button_next button button_action text-center"
               href="<? echo "/".customizeUrl()."/order/address_select/"; ?>"><?= $GLOBALS["tc"]["order_basket"] ?></a>

        </div>

        <?
    } else {
        ?>
        <div class="basket_infobox">
            <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["my_shopping_basket"] ?></h1>
        </div>
        <? if ($GLOBALS['shop_language']['empty_basket_text_module'] != '') {
            echo "<div class='emptybox'>";
            $spacer = array();
            echo(get_text_module(
                $GLOBALS['shop_language']['company'],
                $GLOBALS['shop_language']['empty_basket_text_module'],
                $spacer
            ));
            echo "</div>";
        }
        get_content('empty-basket', true);
        ?>
        <?
    }
    ?>
</div>