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

<? use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

$formname = "form_user_basket";
$query  = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity',
				 shop_user_basket.customer_price AS 'customer_price',shop_user_basket.variant_code AS 'var_code',
				 shop_user_basket.changed_to_minimum AS 'changed_to_minimum',shop_user_basket.changed_to_vpe AS 'changed_to_vpe'
		  FROM shop_view_active_item
		  LEFT JOIN shop_user_basket ON shop_view_active_item.id = shop_user_basket.shop_item_id
		  WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  GROUP BY shop_user_basket.shop_item_id,shop_user_basket.variant_code
		  ORDER by shop_user_basket.insert_datetime";

if(!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
    $currUserBasket = $IOCContainer->create('$CurrUserBasket');
}
$itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
$itemArr = [];
$notifications = [];
$invoiceDiscounts = $currUserBasket->getAppliedInvoiceDiscounts();

foreach($currUserBasket as $basketItem) {
    if($basketItem instanceof BasketEntity && $basketItem->getOrderableType() === 2) {
        $basketItemPrice = $basketItem->getUnitPrice();
        $orderableEntity = $basketItem->getOrderableEntity();
        if($itemBuilder instanceof WebshopItemBuilder && $orderableEntity instanceof WebshopItemInterface) {

            $itemWithImages = $itemBuilder->decorateWebshopItemImages($orderableEntity);
            $singleItemArr = [];
            $singleItemArr['id'] = $itemWithImages->getID();
            $singleItemArr['company'] = $itemWithImages->getCompany();
            $singleItemArr['shop_code'] = $itemWithImages->getShopCode();
            $singleItemArr['language_code'] = $itemWithImages->getLanguageCode();
            $singleItemArr['item_no'] = $itemWithImages->getItemNo();
            $singleItemArr['var_code'] = $itemWithImages->getVariantCode();
            $singleItemArr['unit_price'] = $basketItem->getUnitPrice();
            $singleItemArr['base_price'] = $itemWithImages->getBasePrice();
            $singleItemArr['customer_price'] = $basketItem->getUnitPrice();
            $singleItemArr['cross_price'] = $basketItem->getCrossPrice();
            $singleItemArr['line_amount'] = $basketItem->getLineAmount();
            $singleItemArr['basket_id'] = $basketItem->getDBID();
            $singleItemArr['quantity'] = $basketItem->getQuantity();
            $singleItemArr['basket_quantity'] = $basketItem->getQuantity();
            $singleItemArr['image_data']= $itemWithImages->getImageData();
            $singleItemArr['main_image_data'] = $itemWithImages->getMainImageData();
            $singleItemArr['description'] = $itemWithImages->getDescription();
            $singleItemArr['variant_typ'] = $itemWithImages->getVariantType();
            $singleItemArr['variant_type'] = $itemWithImages->getVariantType();
            $singleItemArr['summary'] = $itemWithImages->getSummary();
            $singleItemArr['parent_item_no'] = $itemWithImages->getParentItemNo();
            $singleItemArr['item_key'] = $currUserBasket->getKey($basketItem);
            $singleItemArr['qty_protected'] = $basketItem->isQtyProtected();
            $singleItemArr['unit_price_protected'] = $basketItem->isUnitPriceProtected();
            $singleItemArr['inventory'] = $itemWithImages->getInventory();
            $singleItemArr['availability'] = $itemWithImages->getAvailability();
            $singleItemArr['is_available'] = $itemWithImages->isAvailable();
            $singleItemArr['notifications'] = get_item_user_notifications($basketItem);
            $notifications[] = $singleItemArr['notifications'];
            $itemArr[] = $singleItemArr;
        }
    }
}
usort($itemArr,'basketSortFunction');


//$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$resultCount = count($itemArr);
if ($resultCount == 0) {
    ?>
<?
}
if (basketNoOfPos() > 0) {
    ?>
    <div class="toolbar">
        <div class="basket_infobox">
            <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["my_shopping_basket"] ?></h1>
            <? if ($GLOBALS['shop_language']['shopping_basket_text_module'] != '') {
                $spacer = array();
                echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['shopping_basket_text_module'], $spacer));
            } ?>
            <?= $GLOBALS["tc"]["item_in_basket_1"] ?><?= round($currUserBasket->getTotalNoOfPos()) ?> <?= $GLOBALS["tc"]["item_in_basket_2"] ?><? //$GLOBALS["tc"]["basket_total"] ?><?= format_amount($currUserBasket->getBasketTotal(), true) ?>.</h3>
        </div>
    </div>
    <div class="button_row text-right">
        <a onclick="$('#<?= $formname ?>').attr('action',$('#<?= $formname ?>').attr('action') + '&redirect=<? echo urlencode("/".customizeUrl()."/order/address_select/"); ?>'); $('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
           class="button_next button button_action" href="<? echo "/".customizeUrl()."/order/address_select/"; ?>"><?= $GLOBALS["tc"]["order_basket_b2b"] ?></a>
    </div>
    <? show_item_list($itemArr, 4, $formname); ?>
    <div class="user_basket_refresh_buttons">
        <?= button("refresh button", $GLOBALS["tc"]["refresh_basket"], $formname, "?action=shop_refresh_user_basket"); ?>
        <?= button("delete button", $GLOBALS["tc"]["empty_basket"], $formname, "?action=shop_empty_user_basket", $GLOBALS["tc"]["empty_basket_conf"]); ?>
    </div>
    <div class="user_basket_bottom order_prices_box">
        <div class="user_basket_bottom_left order_prices_box_left">
            <?= round($currUserBasket->getTotalNoOfPos()); ?> <?= $GLOBALS["tc"]["item_in_basket"] ?>
        </div>
        <div class="user_basket_bottom_right order_prices_box_right">
            <?php

            $numberFormatter = new NumberFormatter('de-DE',NumberFormatter::CURRENCY);
            $currencyCode = $GLOBALS['shop_currency']['code'] ?? 'EUR';
            if (count($invoiceDiscounts) > 0) { ?>
                <div class="row">
                    <div class="col-xs-6 col-sm-6">
                        <div class="order_price_total_label"><?= $GLOBALS["tc"]["item_in_basket_2"] ?></div>
                </div>
                <div class="col-xs-6 col-sm-6 text-right">
                    <div class="order_price_total"><?= $numberFormatter->formatCurrency($currUserBasket->getBasketItemTotal(),$currencyCode) ?></div>
                </div>
                </div>
                <?php
            }

            foreach ($invoiceDiscounts as $key => $invoiceDiscount) {
                /**
                 * @var $invoiceDiscount \DynCom\dc\dcShop\classes\AppliedDiscount
                 */
                $invDiscVal = $invoiceDiscount->getDiscountedAmount();
                $formattedInvDiscStr = $numberFormatter->formatCurrency($invDiscVal,$currencyCode);
                $formattedInvDiscStr = '- ' . $formattedInvDiscStr;

                ?>
                <div class="row">
                    <div class="col-xs-6 col-sm-6">
                        <div class="order_price_total_label"><?= $GLOBALS["tc"]["discount"] . ' - ' . $invoiceDiscount->getNotification() ?></div>
                    </div>
                    <div class="col-xs-6 col-sm-6 text-right">
                        <div class="order_price_total"><?= $formattedInvDiscStr ?></div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="col-xs-6 col-sm-6">
                    <div class="order_price_total_label"><?= $GLOBALS["tc"]["basket_total"] ?></div>
                </div>
                <div class="col-xs-6 col-sm-6 text-right">
                    <div class="order_price_total"><?= format_amount($currUserBasket->getBasketTotal(), FALSE) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="button_row text-right">
        <a onclick="$('#<?= $formname ?>').attr('action',$('#<?= $formname ?>').attr('action') + '&redirect=<? echo urlencode("/".customizeUrl()."/order/address_select/"); ?>'); $('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
           class="button_next button button_action" href="<? echo "/".customizeUrl()."/order/address_select/"; ?>"><?= $GLOBALS["tc"]["order_basket_b2b"] ?></a>
    </div>
<?
} else {
    ?>
    <div class="basket_infobox">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["my_shopping_basket"] ?></h1>
    </div>
    <? if ($GLOBALS['shop_language']['empty_basket_text_module'] != '') {
        $spacer = array();
        echo "<div class=\"emptybox\">";
        echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['empty_basket_text_module'], $spacer));
        echo "</div>";
    } ?>
    <?
}
?>