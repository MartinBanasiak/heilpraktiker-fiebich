<table class="details" cellspacing="0" cellpadding="5" border="0">
    <? if ($item["retail_price"] > 0 && $item["retail_price"] <> $item['base_price']) { ?>
        <tr>
            <td align="left"><?= $GLOBALS["tc"]["retail_price"] ?> </td>
            <td><?= format_amount($item['retail_price'], FALSE) ?></td>
        </tr>
    <? } ?>
    <tr>
        <td align="left"><?= $GLOBALS["tc"]["base_price"] ?></td>
        <td><?= format_amount($item['base_price'], FALSE) ?></td>
    </tr>
    <tr>
        <td align="left"><?= $GLOBALS["tc"]["item_no"] ?></td>
        <td><?= $item["item_no"] ?></td>
    </tr>
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
                echo("<tr>
		    <td align='left'>" . $row['description'] . "</td>
		    <td>" . $row['item_reference_no'] . "</td>
		  	</tr>");
            }
        }
    }
    ?>
    <tr>
        <td align="left"><?= $GLOBALS["tc"]["inventory"] ?></td>
        <td><?= get_inventory_sign($item) ?></td>
    </tr>
    <? if ($item["minimum_order_quantity"] > 0) { ?>
        <tr>
            <td align="left"><?= $GLOBALS["tc"]["minimum_quantity"] ?></td>
            <td><?= $item["minimum_order_quantity"] ?></td>
        </tr>
    <? } ?>
    <? if ($item["order_per_packing_unit"] == 1 && $item["quantity_packing_unit"] > 1) { ?>
        <tr>
            <td align="left"><?= $GLOBALS["tc"]["packing_unit"] ?></td>
            <td><?= $item["quantity_packing_unit"] ?></td>
        </tr>
    <? } ?>
    <tr>
        <td align="left"><?= $GLOBALS['tc']['favorites'] ?></td>
        <td>
            <div
                id="card_inventory_wrapper"><? get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item) ?></div>
        </td>
    </tr>
</table>
<div style="height: 5px;"></div>
<div id="itemcard_price">
    <? show_all_item_customer_price($item, $GLOBALS["shop_customer"], $GLOBALS['shop_currency']['code'], $code) ?>
</div>
</div>
<?
if ($GLOBALS['shop']['variant_typ'] != '2') {
    $action = ml("", "action", "shop_add_item_to_basket_card", "action_id", $item["id"]);
} else {
    $action = ml("", "action", "shop_add_item_to_basket_card", "action_id", $item["id"], "var_code", $code);
}
?>
<form name="form_itemcard" id="form_itemcard" method="POST" action="<?= $action ?>">
    <table class="basket" border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><input style="width:60px; height: 25px;" name="input_item_quantity" type="text" id="input_item_quantity"
                       value="1" /></td>
            <td onclick="document.form_itemcard.submit();"><h3><?= $GLOBALS["tc"]["buy_item"] ?></h3></td>

            <td><!--<? get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item) ?>--></td>
        </tr>
    </table>
</form>

<br />
