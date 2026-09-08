<script TYPE="text/javascript">
    function submitenter( myfield, e ) {
        var keycode;
        if (window.event) {
            keycode = window.event.keyCode;
        } else if (e) {
            keycode = e.which;
        } else {
            return true;
        }

        if (keycode == 13) {
            myfield.form.submit();
            return false;
        }
        else {
            return true;
        }
    }
</script>

<? $formname = "form_user_basket"; ?>
<?
if ($GLOBALS['shop_language']['shopping_basket_text_module'] != '') {
    $spacer = array();
    echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['shopping_basket_text_module'], $spacer));
}
$query  = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity',
				 shop_user_basket.customer_price AS 'customer_price',shop_user_basket.variant_code AS 'var_code',
				 shop_user_basket.changed_to_minimum AS 'changed_to_minimum',shop_user_basket.changed_to_vpe AS 'changed_to_vpe'
		  FROM shop_view_active_item
		  LEFT JOIN shop_user_basket ON shop_view_active_item.id = shop_user_basket.shop_item_id
		  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  GROUP BY shop_user_basket.shop_item_id,shop_user_basket.variant_code
		  ORDER by shop_user_basket.insert_datetime";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 0) {
    ?>
    <? if ($_SESSION['search'] != "") { ?>
        <div class="toolbar">
            <a class="button_back"
               href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search&term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
        </div>
    <? } ?>
<?
}
if (basketNoOfPos() > 0) {
    ?>
    <div class="toolbar">
        <? if ($_SESSION['search'] != "") { ?>
            <a class="button_back"
               href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search&term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
        <? } ?>
        <a class="button_next" href="<?= ml("", "shop_category", "order") ?>"><?= $GLOBALS["tc"]["order_basket"] ?></a>
        <?= button("refresh", $GLOBALS["tc"]["refresh_basket"], $formname, ml("", "action", "shop_refresh_user_basket")); ?>
        <?= button("delete", $GLOBALS["tc"]["empty_basket"], $formname, ml("", "action", "shop_empty_user_basket"), $GLOBALS["tc"]["empty_basket_conf"]); ?>
    </div>
    <? show_item_list(basketItems(), 4, $formname); ?>
    <div class="infobox" style="text-align:right;">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td align="left">
                    <h3><?= shop_get_basket_quantity($GLOBALS["visitor"]["id"]) ?> <?= $GLOBALS["tc"]["item_in_basket"] ?></h3>
                </td>
                <td align="right">
                    <table cellpadding="0" cellspacing="0" border="0" width="300">
                        <tr>
                            <td width="200" align="right"><h3><?= $GLOBALS["tc"]["basket_total"] ?></h3></td>
                            <td width="100" align="right">
                                <h3><?= format_amount(shop_get_basket_amount($GLOBALS["visitor"]["id"]), FALSE) ?></h3></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

<?
} else {
    ?>
    <div class="infobox">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><img src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/b2b/img/shopping_cart_empty.jpg" border="0"></td>
                <td width="10">
                    <div class="spacer_6"></div>
                </td>
                <td><?= $GLOBALS["tc"]["basket_howto"] ?></td>
            </tr>
        </table>
    </div>
<?
}
?>