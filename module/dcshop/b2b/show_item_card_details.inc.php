<?php
/**
 * @var $shopConfig \DynCom\dc\dcShop\classes\CurrShopConfiguration
 */
$shopConfig = $IOCContainer->create('$CurrShopConfig');
$domain = $shopConfig->getBaseShopUrl();
$customer = $shopConfig->getCustomer();
$user = $shopConfig->getUser();
$currencyCode = $shopConfig->getCurrencyCode();
$advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');

/**
 * @var $advancedPriceProvider \DynCom\dc\dcShop\classes\AdvancedPriceProvider
 */
$graduatedPrices = $advancedPriceProvider->getGraduatedPrices($itemObj,$customer,$currencyCode);
$defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);
//$PDO = $IOCContainer->create('PDOQueryWrapper');

$subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

$validator = new \DynCom\dc\common\classes\Validator([], []);
$formBuilder = new \DynCom\dc\common\classes\FormBuilder('dummyID');
$templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

$availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

$availability_code = $availabilityProvider->getItemAvailability($itemObj);
$is_available = $availabilityProvider->isItemAvailable($itemObj);

$variantService = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemVariantService');
$variants = [];
/**
 * @var $variantService \DynCom\dc\dcShop\classes\WebshopItemVariantService
 */
if (!empty($parent_item)) {
    $variants = $variantService->getAllVariants($parentItemObj);
}
$variantCount = count($variants);
?>
<div class="table_area itemcard_details itemcard_infoblock">
    <? if ($item["retail_price"] > 0 && $item["retail_price"] <> $item['base_price']) { ?>
        <div class="table_row retail_price">
            <div class="table_cell details_label"><?= $GLOBALS["tc"]["retail_price"] ?> </div>
            <div class="table_cell details_value"><?= format_amount($item['retail_price'], FALSE) ?></div>
        </div>
    <? } ?>
    <div class="table_row price">
        <div class="table_cell details_label"><?= $GLOBALS["tc"]["base_price"] ?></div>
        <div class="table_cell details_value"><?= format_amount($item['base_price'], FALSE) ?></div>
    </div>
    <div class="table_row item_no">
        <div class="table_cell details_label"><?= $GLOBALS["tc"]["item_no"] ?></div>
        <div class="table_cell details_value"><?= $item["item_no"] ?></div>
    </div>
    <?
    $query  = "SELECT item_reference_no,description
  			FROM shop_item_cross_reference
  			WHERE item_no='" . $item['item_no'] . "'
  				AND (customer_no ='" . $GLOBALS['shop_customer']['customer_no'] . "' OR customer_no='')
  				AND company = '" . $GLOBALS['shop']['company'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['item_reference_no'] != "") {
                echo("<div class='table_row item_reference_no'>
		    <div class='table_cell details_label'>" . $GLOBALS["tc"]["reference_no"] . "</div>
		    <div class='table_cell details_value'>" . $row['item_reference_no'] . "</div>
		  	</div>");
            }
        }
    }
    ?>
    <? if ($item["minimum_order_quantity"] > 0) { ?>
        <div class="table_row minimum_order_quantity">
            <div class="table_cell details_label">
                <?= $GLOBALS["tc"]["minimum_quantity"] ?>
            </div>
            <div class="table_cell details_value">
                <?= $item["minimum_order_quantity"] ?>
            </div>
        </div>
    <? } ?>
    <? if ($item["order_per_packing_unit"] == 1 && $item["quantity_packing_unit"] > 1) { ?>
        <div class="table_row packing_unit">
            <div class="table_cell details_label">
                <?= $GLOBALS["tc"]["packing_unit"] ?>
            </div>
            <div class="table_cell details_value">
                <?= $item["quantity_packing_unit"] ?>
            </div>
        </div>
    <? } /*?>
    <div class="table_row favorites">
        <div class="table_cell details_label"><?= $GLOBALS['tc']['favorites_b2b'] ?></div>
        <div class="table_cell details_value">
            <? get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item) ?>
        </div>
    </div> <? */ ?>
</div>

<?
if ($GLOBALS['shop']['variant_typ'] != '2') {
    $action = "?action=shop_add_item_to_basket_card&action_id=".$item["id"];
} else {
    //$action = ml("", "action", "shop_add_item_to_basket_card", "action_id", $item["id"], "var_code", $code);
    $action = "?action=shop_add_item_to_basket_card&action_id=".$item["id"];
}

$hasVariants = $variantCount > 0;

if (!$hasVariants) {
    $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
    $vatMgr = $IOCContainer->create('$VATManager');
    $builder = new \DynCom\dc\dcShop\classes\ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating,$vatMgr);
    $graduatedPriceTable = $builder->getGraduatedPricesTableHTML($graduatedPrices,$itemObj->getBaseUnitOfMeasure(),$GLOBALS['language']['locale_code']);
    $orderButtonHTML = $builder->getItemcardButtonHTML($itemObj, $defaultItemPrice, $subscriptionData, true);
    $orderbox = "<div class='orderbox'><div class='orderbox_button'>".$orderButtonHTML."</div></div>";
    echo $graduatedPriceTable;
    echo $orderbox;
}
get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item);







?>

