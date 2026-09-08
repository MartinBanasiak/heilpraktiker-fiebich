<?php

use DynCom\dc\dcShop\classes\WebshopItemBuilder;use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;use DynCom\dc\dcShop\interfaces\WebshopItemWithImages;function show_item_list( $result, $type, $formname = "" ) {
    if ($formname == "") {
        $formname = "form_itemlist_" . $GLOBALS["shop_item_listform"];
    }
    $GLOBALS["input_counter"] = 1;
    $GLOBALS["shop_item_listform"]++;
    if (@mysqli_num_rows($result) > 0) {
        switch ($type) {
            case 1:
                show_item_list_1_header($formname);
                break;
            case 2:
                echo "<div class=\"itemcard_list2\">\n";
                break;
            case 3:
                echo "<div class=\"itemcard_list3\">\n";
                break;
            case 4:
                show_item_list_4_header($formname);
                break;
            case 5:
                show_item_list_5_header($formname);
                break;
            case 6:
                show_item_list_6_header($formname);
                break;
            case 7:
                show_item_list_7_header($formname);
                break;
            case 8:
                show_item_list_8_header($formname);
                break;
            case 10:
                echo "<div class=\"itemcard_list2\">\n";
                break;
        }
        while ($item = @mysqli_fetch_assoc($result)) {
            $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
            //$item['retail_price'] = get_item_retail_price($item,$GLOBALS['shop_currency']['code']);
            $image = get_item_main_image($item, get_item_variant_parent($item));
            if ($image["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image['filename'])) {
                if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                    $image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                } else {
                    $image["filename"] = "noimage.jpg";
                }
            }
            switch ($type) {
                case 1:
                    $input_counter = show_item_list_1($item, $image, $formname, $input_counter);
                    break;
                case 2:
                    show_item_list_2($item, $image);
                    break;
                case 3:
                    show_item_list_3($item, $image);
                    break;
                case 4:
                    show_item_list_4($item, $image, $formname);
                    break;
                case 5:
                    show_item_list_5($item, $image, $formname);
                    break;
                case 6:
                    show_item_list_6($item, $image, $formname);
                    break;
                case 7:
                    show_item_list_7($item, $formname);
                    break;
                case 8:
                    show_item_list_8($item, $formname);
                    break;
                case 10:
                    show_item_list_10($item, $image);
                    break;
            }
        }
        switch ($type) {
            case 1:
                echo "</table>\n</form>\n";
                break;
            case 2:
                echo "</div>\n";
                break;
            case 3:
                echo "</div>\n";
                break;
            case 4:
                echo "</table>\n</form>\n";
                break;
            case 5:
                echo "</table>\n";
                break;
            case 6:
                echo "</table>\n";
                break;
            case 7:
                echo "</table>\n";
                break;
            case 8:
                echo "</table>\n";
                break;
            case 10:
                echo "</div>\n";
                break;
        }
    }
}

// Artikelliste 1 :Für Übersichten, Zubehörartikel und Ersatzteile

function show_item_list_1( $item, $image, $formname ) {

    $imagelink = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image["filename"];
    $alt_text  = $image['description'];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        $query          = "SELECT shop_view_active_item.*
				  FROM shop_item_link
				  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
				  WHERE shop_item_link.type = '0'
				  	AND shop_item_link.item_no = '" . $item["item_no"] . "'
				  	AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
	        	  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
	        	  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
	        	  GROUP BY shop_view_active_item.item_no";
        $result         = @mysqli_query($GLOBALS['mysql_con'], $query);
        $no_of_variants = @mysqli_num_rows($result);
        if ($no_of_variants > 0) {
            while ($variant_item = @mysqli_fetch_array($result)) {
                $toggle_text .= "togglenb('item_" . $variant_item["item_no"] . "');";
            }
            show_item_list_1_has_variant($item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text);
            $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
            $local_input_counter = 1;
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_image                 = get_item_main_image($variant_item, get_item_variant_parent($variant_item));
                $variant_item_description_link = str_replace('/', '', $variant_item["description"]);
                $variant_item_description_link = str_replace(".", "", $variant_item_description_link);

                if ($variant_image["filename"] == '') {
                    if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                        $variant_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                    } else {
                        $variant_image["filename"] = "noimage.jpg";
                    }
                }
                $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
                $alt_text          = $variant_image['description'];

                if ($_GET["card"] != "") {
                    $arr = explode("/", $_SERVER["REQUEST_URI"]);
                    for ($i = 1; $i < count($arr) - 2; $i++) {
                        $new_arr .= "/" . $arr[$i];
                    }
                    $variant_itemlink = $new_arr . "/" . ($variant_item_description_link) . "/?shop_category=" . urlencode($_GET["shop_category"]) . "&card=" . $variant_item["id"];
                } else {
                    $variant_itemlink = urlencode($variant_item_description_link) . "/?shop_category=" . urlencode($_GET["shop_category"]) . "&card=" . $variant_item["id"];
                }
                $i++;
                show_item_list_1_is_variant($variant_item, $variant_imagelink, $variant_itemlink, $formname, ($i == $no_of_variants), $alt_text);
            }
        } else {
            show_item_list_1_no_variant($item, $imagelink, $itemlink, $formname, $alt_text);
        }
    } else {
        /*$query = "SELECT shop_view_active_item.*, shop_item_variant.code AS 'variant_code', shop_item_variant.description AS 'variant_desc'
          FROM shop_item_variant
          INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_variant.item_no
          WHERE shop_item_variant.item_no = '" . $item["item_no"] . "'
              AND shop_view_active_item.company = '".$GLOBALS['shop']['company']."'
            AND shop_item_variant.company = '".$GLOBALS['shop']['company']."'
              AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
              AND shop_view_active_item.language_code = '" . $item["language_code"] . "' ORDER BY shop_item_variant.code ASC";*/

        $query = "SELECT
					shop_item.id,
					shop_item.company,
					shop_item.shop_code,
					shop_item.language_code,
					shop_item.item_no,
					shop_item_variant.code AS 'variant_code',
					(CASE
					  WHEN
						(
							'" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant.description))>0
						)
						THEN 
							shop_item_variant.description
					  WHEN
						(
							'" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant.description))>0
						)
						THEN
							shop_item.description
					  WHEN
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant_translation.description))>0
						)
						THEN
							shop_item_variant_translation.description
					  WHEN
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant_translation.description))>0
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant.description))>0
						)
						THEN
							shop_item_variant.description
					  WHEN 
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant_translation.description))>0
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant.description))>0
						)
						THEN
							shop_item.description
					END) AS description,
					(CASE 
					  WHEN
						(
							'" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant.description_2))>0
						)
						THEN 
							shop_item_variant.description_2
					  WHEN
						(
							'" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant.description_2))>0
						)
						THEN
							shop_item.summary
					  WHEN
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant_translation.description_2))>0
						)
						THEN
							shop_item_variant_translation.description_2
					  WHEN
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant_translation.description_2))>0
						  AND
							CHAR_LENGTH(TRIM(shop_item_variant.description_2))>0
						)
						THEN
							shop_item_variant.description_2
					  WHEN
						(
							NOT '" . $GLOBALS["shop_language"]["code"] . "' = '" . $GLOBALS["shop"]["default_language_code"] . "'
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant_translation.description_2))>0
						  AND
							NOT CHAR_LENGTH(TRIM(shop_item_variant.description_2))>0
						)
						THEN
							shop_item.summary
					END) AS summary,
					shop_item.base_unit_of_measure,
					shop_item.unit_of_measure_code,
					shop_item.nav_base_unit_code,
					shop_item.multiplier,
					shop_item.variant_type,
					shop_item.main_picture_line_no,
					shop_item.main_category_line_no,
					shop_item.retail_price,
					shop_item.base_price,
					shop_item.price_includes_vat,
					(CASE
						WHEN 
							CHAR_LENGTH(shop_item_variant.id)>0
						  THEN
							shop_item_variant.inventory
						ELSE
							shop_item.inventory					
					END) AS 'inventory',
					shop_item_variant.inventory,
					shop_item.insufficient_inventory_limit,
					shop_item.quantity_on_purchase_order,
					shop_item.discount_group,
					shop_item.allow_invoice_discount,
					shop_item.search_query,
					shop_item.vendor_no,
					shop_item.vendor_name,
					shop_item.parent_item_no,
					shop_item.order_ranking,
					shop_item.weight,
					shop_item.width,
					shop_item.height,
					shop_item.length,
					shop_item.volume,
					shop_item.creation_date,
					shop_item.meta_keywords,
					shop_item.meta_description,
					shop_item.site_title,
					shop_item.allow_gift_package,
					shop_item.is_gift_package,
					shop_item.minimum_order_quantity,
					shop_item.quantity_packing_unit,
					shop_item.order_per_packing_unit,
					shop_item.vat_prod_posting_group				  
					FROM
					shop_item
				RIGHT JOIN
					shop_item_variant
				  ON
				  (
						shop_item_variant.company=shop_item.company
					AND
						shop_item_variant.item_no=shop_item.item_no
					AND
						shop_item_variant.to_delete=0
				  )
				LEFT JOIN
					shop_item_variant_translation
				  ON
				  (
						shop_item_variant_translation.company=shop_item.company
					  AND
						shop_item_variant_translation.item_no=shop_item.item_no
					  AND
						shop_item_variant_translation.language_code=shop_item.language_code
					  AND
						shop_item_variant_translation.variant_code=shop_item_variant.code
					  AND
						shop_item_variant_translation.to_delete=0
				  )
				  WHERE 
					shop_item.item_no='" . $item["item_no"] . "'
				  AND
					shop_item.active=1
				  AND
					shop_item.company = '" . $GLOBALS['shop']['company'] . "'
	        	  AND
					shop_item.shop_code= '" . $item["shop_code"] . "'
	        	  AND
					shop_item.language_code = '" . $item["language_code"] . "' 
				ORDER BY shop_item_variant.code ASC";
        echo "<!-- ITEM-QUERY LIST1: $query -->";
        $result         = @mysqli_query($GLOBALS['mysql_con'], $query);
        $no_of_variants = @mysqli_num_rows($result);
        if ($no_of_variants > 0) {
            while ($variant_item = @mysqli_fetch_array($result)) {
                $toggle_text .= "togglenb('item_" . $variant_item["item_no"] . $variant_item["color_code"] . "');";
            }
            show_item_list_1_has_variant($item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text);
            $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
            $local_input_counter = 1;
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_image                 = get_item_main_image($item, get_item_variant_parent($item));
                $variant_item_description_link = str_replace('/', '', $variant_item["description"]);
                $variant_item_description_link = str_replace(".", "", $variant_item_description_link);

                if ($variant_image["filename"] == '') {
                    if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                        $variant_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                    } else {
                        $variant_image["filename"] = "noimage.jpg";
                    }
                }
                $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
                if ($variant_imagelink == $imagelink) {
                    $variant_imagelink = '';
                }
                $alt_text = $variant_image['description'];

                if ($_GET["card"] != "") {
                    $arr = explode("/", $_SERVER["REQUEST_URI"]);
                    for ($i = 1; $i < count($arr) - 2; $i++) {
                        $new_arr .= "/" . $arr[$i];
                    }
                    $variant_itemlink = $new_arr . "/" . ($variant_item_description_link) . "/?shop_category=" . urlencode($_GET["shop_category"]) . "&card=" . $variant_item["id"];
                } else {
                    $variant_itemlink = urlencode($variant_item_description_link) . "/?shop_category=" . urlencode($_GET["shop_category"]) . "&card=" . $variant_item["id"];
                }
                $i++;
                show_item_list_1_is_variant($variant_item, $variant_imagelink, $variant_itemlink, $formname, ($i == $no_of_variants), $alt_text);
            }
        } else {
            show_item_list_1_no_variant($item, $imagelink, $itemlink, $formname, $alt_text);
        }
    }
    return $input_counter;
}

function show_item_list_1_is_variant( $item, $imagelink, $itemlink, $formname, $last_child = FALSE, $alt_text = '' ) {
    $parent_item     = get_item_variant_parent($item);
    $last_child_text = ($last_child) ? "_last_child" : "";
    if ($item["variant_code"] != '') {
        $id_add = '_' . $item["variant_code"];
    }
    if ($imagelink == '') {
        $img_tag = '';
    } else {
        $img_tag = "<img src=\"$imagelink\" border=\"0\" alt=\"$alt_text\" title=\"$alt_text\" />";
    }


    ?>
    <tr id="item_<?= $item["item_no"] . $id_add ?>" style="display:none;" class="isvar isvar_<?= $item["item_no"] ?>">
        <td class="list1_var_icon" width="19" align="center">
            <div></div>
        </td>
        <td class="list1_image" width="45" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $img_tag ?></div>
        </td>
        <td class="list1_no" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["item_no"] ?></div>
        </td>
        <td class="list1_desc" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["description"] ?></div>
        </td>
        <td class="list1_promotion" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, TRUE); ?></div>
        </td>
        <td class="list1_base_price" align="right" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?
                if ($GLOBALS['shop']['variant_typ'] != 2) {
                    echo $item['base_price'];
                } else {
                    echo format_amount(get_item_customer_price($item, '', 1, $GLOBALS['shop_currency']['code'], $item['variant_code'], FALSE, $GLOBALS['shop']['campain_no']), FALSE);
                }
                ?></div>
        </td>
        <td class="list1_cust_price" align="right" onclick="window.location.href='<?= $itemlink ?>'">

            <?
            if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $item['variant_code'], FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            ?>
        </td>
        <td class="list1_inventory" align="center" width="40" onclick="window.location.href='<?= $itemlink ?>'">
            <div><!-- ITEM INVENTORY: <?= $item["inventory"] ?> --><? get_inventory_sign($item) ?></div>
        </td>
        <td class="list1_quantity" width="40" align="center">
            <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                        id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                        value="<?= $item["id"] ?>" /><input type="hidden"
                                                            name="input_item_variant_<?= $GLOBALS["input_counter"] ?>"
                                                            value="<?= $item["variant_code"] ?>" /><input
                    style="width:30px;" name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                    id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>" type="text" tabindex="<?= tabcounter() ?>"
                    maxlength="3" /></div>
        </td>
        <td class="list1_basket" width="30" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()">
                    <!--<img src="/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_list.png" border="0" />--></a>
            </div>
        </td>
        <!--<td width="30" align="center"><div><a href="javascript:void(0)" onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()" src="/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add2.gif" border="0" /></a></div></td>-->
        <td class="list1_fav" width="30" align="center">
            <div><? get_favorite_sign($item) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_has_variant( $item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text = '' ) {
    ?>
    <tr id="item_<?= $item["item_no"] ?>" class="hasvar hasvar_<?= $item["item_no"] ?> vars_closed"
        data-itemno="<?= $item["item_no"] ?>">
        <td width="19" class="list1_var_icon" align="center"></td>
        <td width="45" class="list1_image" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><img src="<?= $imagelink ?>" border="0" alt="<?= $alt_text ?>" title="<?= $alt_text ?>"></div>
        </td>
        <td class="list1_no" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= get_item_no_or_no_of_variants($item) ?></div>
        </td>
        <td class="list1_desc" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["description"] ?><? //echo "<br />$item['summary']"; ?></div>
        </td>
        <td class="list1_promotion" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, TRUE); ?></div>
        </td>
        <td class="list1_base_price" align="right" onclick="window.location.href='<?= $itemlink ?>'">
            <div>ab <?= format_amount($item["base_price"], FALSE) ?></div>
        </td>
        <td class="list1_cust_price" align="right" onclick="window.location.href='<?= $itemlink ?>'">
            <div>
                <?
                if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                } else {
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
                }
                ?>
            </div>
        </td>
        <td class="list1_inventory" align="center" width="40" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? get_inventory_sign($item) ?></div>
        </td>
        <?
        if ($GLOBALS['shop']['variant_typ'] == '1') {
            ?>
            <td class="list1_quantity" width="40" align="center">
                <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                            id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                            value="<?= $item["id"] ?>" /><input style="width:30px;"
                                                                name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                                id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                                type="text" tabindex="<?= tabcounter() ?>"
                                                                maxlength="3" /></div>
            </td>
            <td class="list1_basket" width="30" align="center">
                <div><a href="javascript:void(0)"
                        onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()">
                        <!--<img src="/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_list.png" border="0" />--></a>
                </div>
            </td>
            <!--<td width="30" align="center"><div><a href="javascript:void(0)" onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()" src="/layout/frontend/shop/img/basket_add2.gif" border="0" /></a></div></td>-->
        <?
        } else {
            ?>
            <td class="list1_quantity" width="40" align="center">
                <div></div>
            </td>
            <td class="list1_basket" width="30" align="center">
                <div></div>
            </td>
            <!--<td width="30" align="center"><div></div></td>-->
        <?
        }
        ?>
        <td class="list1_fav" width="30" align="center">
            <div></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_no_variant( $item, $imagelink, $itemlink, $formname, $alt_text = '' ) {
    ?>
    <tr id="item_<?= $item["item_no"] ?>">
        <td width="19" class="list1_var_icon" align="center">
            <div></div>
        </td>
        <td width="45" class="list1_image" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><img src="<?= $imagelink ?>" border="0" alt="<?= $alt_text ?>" title="<?= $alt_text ?>" ></div>
        </td>
        <td class="list1_no" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["item_no"] ?></div>
        </td>
        <td class="list1_desc" onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["description"] ?><? //echo "<br />$item['summary']"; ?></div>
        </td>
        <td class="list1_promotion" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, TRUE); ?></div>
        </td>
        <td class="list1_base_price" align="right" onclick="window.location.href='<?= $itemlink ?>';">
            <div><?= format_amount($item["base_price"], FALSE) ?></div>
        </td>
        <td align="right" class="list1_cust_price" onclick="window.location.href='<?= $itemlink ?>';">
            <div>
                <?
                //NEUE STREICHPREISFINDUNG
                if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                } else {
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
                }
                ?>
            </div>
        </td>
        <td class="list1_inventory" align="center" width="40"><a href="<?= $itemlink ?>">
                <div><? get_inventory_sign($item) ?></div>
            </a></td>
        <td class="list1_quantity" width="40" align="center">
            <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                        id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                        value="<?= $item["id"] ?>" /><input style="width:30px;"
                                                            name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            type="text" tabindex="<?= tabcounter() ?>" maxlength="3" />
            </div>
        </td>
        <td width="30" class="list1_basket" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()">
                    <!--<img src="/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_list.png" border="0" />--></a>
            </div>
        </td>
        <!--  <td width="30" class="list1_basket_all" align="center"><div><a href="javascript:void(0)" onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()" src="/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add2.gif" border="0" /></a></div></td>
 -->
        <td class="list1_fav" width="30" align="center">
            <div><? get_favorite_sign($item) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_header( $formname ) {
    ?>
    <form name="<?= $formname ?>" id="<?= $formname ?>" method="POST">
    <table class="itemlist" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="list1_var_icon" width="15px">
            <div>&nbsp;</div>
        </td>
        <td class="list1_image" width="65px">
            <div>&nbsp;</div>
        </td>
        <td class="list1_no" width="72px">
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td class="list1_desc" width="190px">
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <td class="list1_promotion" width="58px">
            <div><?= $GLOBALS["tc"]["notice"] ?></div>
        </td>
        <td class="list1_base_price" width="70px" align="right">
            <div><?= $GLOBALS["tc"]["base_price"] ?></div>
        </td>
        <td class="list1_cust_price" width="70px" align="right">
            <div><?= $GLOBALS["tc"]["your_price"] ?></div>
        </td>
        <td class="list1_inventory" width="50px" width="40" align="center">
            <div><?= $GLOBALS["tc"]["inventory"] ?></div>
        </td>
        <td class="list1_quantity" width="58px" align="center">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td class="list1_basket" width="40px">
            <div>&nbsp;</div>
        </td>
        <!--
            <td class="list1_basket_all" width="30"><div>&nbsp;</div></td>-->
        <td class="list1_fav" width="22px">
            <div>&nbsp;</div>
        </td>
    </tr>
<?
}

// Artikelliste 2: Für Startseite und Hauptkategorien

function show_item_list_2( $item, $image ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist2">
        <div class="itemlist2_image" onclick="window.location.href='<?= $itemlink ?>'">
            <div class="itemlist2_image_layer1"><img src="<?= $imagelink ?>" border="0"
                                               title="<?= $image['description'] ?>"       alt="<?= $image['description'] ?>"></div>
            <? show_item_promotion_banners($item); ?>
        </div>
        <div class="itemlist2_description" onclick="window.location.href='<?= $itemlink ?>'">
            <strong><?= $item["description"] ?></strong><? //echo "<br />$item['summary']"; ?></div>
        <div class="itemlist2_item_no"
             onclick="window.location.href='<?= $itemlink ?>'"><?= get_item_no_or_no_of_variants($item) ?></div>
        <div class="itemlist2_price" onclick="window.location.href='<?= $itemlink ?>'">
            <?
            if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            ?>
        </div>
    </div>
<?
}

// Artikelliste 3: Alternativartikel und Passende Artikel

function show_item_list_3( $item, $image ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist3">
        <div class="itemlist3_top" onclick="window.location.href='<?= $itemlink ?>'">
            <div class="itemlist3_image">
                <div class="itemlist3_image_layer1"><img src="<?= $imagelink ?>" border="0"
                                                      title="<?= $image['description'] ?>"    alt="<?= $image['description'] ?>"></div>
                <? show_item_promotion_banners($item); ?>
            </div>
            <div class="itemlist3_description">
                <strong><?= $item["description"] ?></strong><? //echo "$item['summary']"; ?></div>
            <div class="itemlist3_item_no"><?= get_item_no_or_no_of_variants($item) ?></div>
        </div>
        <div class="itemlist3_bottom">
            <div class="itemlist3_quantity"
                 onclick="window.location.href='<?= $itemlink ?>'"><?= get_inventory_sign($item) ?></div>
            <div class="itemlist3_price" onclick="window.location.href='<?= $itemlink ?>'">
                <?
                if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                } else {
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
                }
                ?>
            </div>
            <div class="itemlist3_symbol"><? variant_select_list($item) ?></div>
            <div class="itemlist3_symbol"><? get_favorite_sign($item) ?></div>
        </div>
    </div>
<?
}

// Artikelliste 4: Warenkorb

function show_item_list_4( $item, $image, $formname ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    if ($GLOBALS['shop']['variant_typ'] == '2') {
        $action = ml($sitepart, "action", "shop_remove_item_from_basket", "action_id", $item["id"], "var_code", $item['var_code']);
    } else {
        $action = ml($sitepart, "action", "shop_remove_item_from_basket", "action_id", $item["id"]);
    }
    ?>
    <tr id="item_<?= $item["item_no"] ?>">
        <td class="basket_list_img" width="80" align="center"><a href="<?= $itemlink ?>">
                <div><img src="<?= $imagelink ?>" border="0" title="<?= $image['description'] ?>" alt="<?= $image['description'] ?>"></div>
            </a></td>
        <td class="basket_list_itemno" width="85" onclick="window.location.href='<?= $itemlink ?>'">
            <div>
                <div><?= $item["item_no"] ?></div>
                <? if ($GLOBALS['shop']['variant_typ'] == '2' && strlen($item["var_code"]) > 0) {
                    ?>
                    <div>
                        <div class="var_code_title"><?= $GLOBALS["tc"]["var_code"] ?></div>
                        <div class="var_code"><?= $item["var_code"] ?></div>
                    </div>
                <?
                }
                ?>
            </div>
        </td>
        <td class="basket_list_desc" width="185" onclick="window.location.href='<?= $itemlink ?>'">
            <div class="basket_list_desc_main">
                <div class="basket_list_item_desc"><?= $item["description"] ?></div>
                <? if ($item['changed_to_ vpe'] == 1) { ?>
                    <br /><font style='color:red'><?= $GLOBALS['tc']['changed_to_vpe'] ?></font>
                <? } elseif ($item['changed_to_minimum'] == 1) { ?>
                    <br /><font style='color:red'><?= $GLOBALS['tc']['changed_to_minimum'] ?></font>
                <? } ?>
            </div>
            </div>
            <div class="basket_list_delete">
                <div><a href="<?= $action ?>"><?= $GLOBALS["tc"]["remove_item"] ?></a></div>
            </div>
        </td>
        <?/*
    if($GLOBALS['shop']['variant_typ'] == '2')
    {
    	echo("<td width='90' align = 'left'><div>".$item["var_code"]."</div></td>");
    }*/
        ?>
        <td class="basket_list_price list_price" width="90" align="right"
            onclick="window.location.href='<?= $itemlink ?>';">
            <div><?= format_amount($item["base_price"], FALSE) ?></div>
        </td>
        <td class="basket_list_price cust_price" width="90" align="right"
            onclick="window.location.href='<?= $itemlink ?>';">
            <div><?= format_amount($item["customer_price"], FALSE) ?></div>
        </td>
        <?
        if ($item['is_coupon_item'] == 1) {
            $readonly = " readonly='readonly'";
        } else {
            $readonly = "";
        }
        ?>
        <td class="basket_list_qtty" width="40" align="center">
            <div>
                <input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden" value="<?= $item["id"] ?>" />
                <input name="input_variant_code_<?= $GLOBALS["input_counter"] ?>"
                       id="input_variant_code_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                       value="<?= $item["var_code"] ?>" />
                <input value="<?= round($item["basket_quantity"]) ?>" style="width:30px;"
                       name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>" type="text"
                       tabindex="<?= tabcounter() ?>" <?= $readonly ?> maxlength="3"
                       onKeyPress="return submitenter(this,event)" />
            </div>
        </td>
        <td class="basket_list_inventory" width="40" align="center"><a href="<?= $itemlink ?>">
                <div><? get_inventory_sign($item) ?></div>
            </a></td>
        <td class="basket_list_line_amnt" width="90" align="right" onclick="window.location.href='<?= $itemlink ?>';">
            <div><?= format_amount($item["basket_quantity"] * $item["customer_price"], FALSE) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_4_header($formname) {
    ?>
    <form name="<?= $formname ?>" id="<?= $formname ?>"
          action=<?= ml("", "action", "shop_refresh_user_basket") ?> method="POST">
    <table class="itemlist" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="basket_list_img">
            <div>&nbsp;</div>
        </td>
        <td class="basket_list_itemno">
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td class="basket_list_description">
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <?/*
    if($GLOBALS['shop']['variant_typ'] == '2')
    {
    	echo("<td width='90' align = 'left'><div>".$GLOBALS["tc"]["variant"]."</div></td>");
    }*/
        ?>
        <td class="basket_list_price" align="right">
            <div><?= $GLOBALS["tc"]["base_price"] ?></div>
        </td>
        <td class="basket_list_price cust_price" align="right">
            <div><?= $GLOBALS["tc"]["your_price"] ?></div>
        </td>
        <td class="basket_list_qtty" align="center">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td class="basket_list_inventory" align="center">
            <div><?= $GLOBALS["tc"]["inventory"] ?></div>
        </td>
        <td class="basket_list_line_amnt" align="right">
            <div><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </td>
    </tr>
<?
}

// Artikelliste 5: Bestellung

function show_item_list_5( $item, $image, $formname ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <tr id="item_<?= $item["item_no"] ?>" style="cursor: default;">
        <td class="list5_img" align="center">
            <div><img src="<?= $imagelink ?>" border="0" title="<?= $image['description'] ?>" alt="<?= $image['description'] ?>"></div>
        </td>
        <td class="list5_no">
            <div><?= $item["item_no"] ?></div>
            <?
            if (strlen($item["var_code"]) > 0) {
                ?>
                <div class="var_code_title"><?= $GLOBALS["tc"]["variant"] ?></div>
                <div class="var_code"><?= $item["var_code"] ?></div>
            <?
            }
            ?>
        </td>
        <td class="list5_desc">
            <div><?= $item["description"] ?><? //echo "<br />$item['summary']"; ?></div>
        </td>
        <td class="list5_disc">
            <div><?= ($item["allow_invoice_disc"] == 1) ? $GLOBALS["tc"]["yes"] : $GLOBALS["tc"]["no"] ?></div>
        </td>
        <td class="list5_price">
            <div><?= format_amount($item["customer_price"], FALSE) ?></div>
        </td>
        <? if ($GLOBALS['shop_user']['max_discount'] > 0) { ?>
            <td width="40" class="salesperson_discount" align="right">
                <div><input type="text" data-maxdiscount="<?= $GLOBALS['shop_user']['max_discount'] ?>"
                            name="sp_disc_<?= $item['item_no'] . $item['var_code'] ?>"
                            value="<?= $_POST['sp_disc_' . $item['item_no'] . $item['var_code']] ?>" style="width:40px"
                            maxlength=4></div>
            </td>
        <? } ?>
        <td class="list5_qtty">
            <div><?= round($item["basket_quantity"]) ?></div>
        </td>
        <td class="list5_amt">
            <div
                data-initvalue="<?= ($item["basket_quantity"] * $item["customer_price"]) ?>"><?= format_amount($item["basket_quantity"] * $item["customer_price"], FALSE) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_5_header($formname) {
    ?>
    <table class="itemlist" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="list5_img">
            <div>&nbsp;</div>
        </td>
        <td class="list5_no">
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td class="list5_desc">
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <td class="list5_disc">
            <div><?= $GLOBALS["tc"]["invoice_discount"] ?></div>
        </td>
        <td class="list5_price">
            <div><?= $GLOBALS["tc"]["your_price"] ?></div>
        </td>
        <? if ($GLOBALS['shop_user']['max_discount'] > 0) { ?>
            <td width="40" align="right">
                <div><?= $GLOBALS["tc"]["salesperson_discount"] ?></div>
            </td>
        <? } ?>
        <td class="list5_qtty">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td class="list5_amt">
            <div><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </td>
    </tr>
<?
}

// Artikelliste 6: Bestell-Email
function show_item_list_6( $item, $image, $formname ) {
    ?>
    <tr>
        <td valign="middle"
            style="height: 30px; border-bottom:1px solid #035297;"><?= $item["item_no"] ?><? if (strlen($item["variant_code"]) > 0) { ?> &nbsp;&nbsp; <? echo $item["variant_code"];
            } ?></td>
        <td valign="middle" style="height: 30px; border-bottom:1px solid #035297;"><?= $item["description"] ?>
            <br /><?= $item["summary"] ?></td>
        <td valign="middle"
            style="height: 30px; border-bottom:1px solid #035297;"><?= ($item["allow_invoice_disc"] == 1) ? $GLOBALS["tc"]["yes"] : $GLOBALS["tc"]["no"] ?></td>
        <td valign="middle"
            style="height: 30px; border-bottom:1px solid #035297;"><? $str = (round($item['salesperson_discount'], 2) > 0 ? round($item['salesperson_discount'], 2) . ' %' : '');
            echo $str; ?></td>
        <td valign="middle" style="height: 30px; border-bottom:1px solid #035297;" width="90"
            align="right"><?= format_amount($item["list_price"], FALSE) ?></td>
        <td valign="middle" style="height: 30px; border-bottom:1px solid #035297;" width="90"
            align="right"><?= format_amount($item["unit_price"], FALSE) ?></td>
        <td valign="middle" style="height: 30px; border-bottom:1px solid #035297;" width="40"
            align="right"><?= round($item["basket_quantity"]) ?></td>
        <td valign="middle" style="height: 30px; border-bottom:1px solid #035297;" width="90"
            align="right"><?= format_amount($item["basket_quantity"] * $item["unit_price"], FALSE) ?></td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_6_header($formname) {
    ?>
    <table border="0" cellpadding="4" cellspacing="0" width="100%" style="padding:6px;">
    <tr>
        <td style="border-bottom:1px solid #035297;"><strong><?= $GLOBALS["tc"]["item_no"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;"><strong><?= $GLOBALS["tc"]["description"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;"><strong><?= $GLOBALS["tc"]["allow_invoice_disc"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;"><strong><?= $GLOBALS["tc"]["salesperson_discount"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;" width="90" align="right">
            <strong><?= $GLOBALS["tc"]["base_price_alt"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;" width="90" align="right">
            <strong><?= $GLOBALS["tc"]["your_price"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;" width="40" align="right">
            <strong><?= $GLOBALS["tc"]["quantity"] ?></strong></td>
        <td style="border-bottom:1px solid #035297;" width="90" align="right">
            <strong><?= $GLOBALS["tc"]["line_amount"] ?></strong></td>
    </tr>
<?
}

// Artikelliste 7: Lieferung-Sales Shipment History
function show_item_list_7( $item, $formname ) {
    ?>
    <tr id="item_<?= $item["no"] ?>"
        style="cursor: default; height: 30px;">
        <td>
            <div><?= $item["no"] ?></div>
        </td>
        <td>
            <div><?= $item["description"] ?><? //echo "<br />$item['summary']"; ?></div>
        </td>
        <td width="100" align="left">
            <div><?= ($item["quantity"] != '0') ? round($item["quantity"]) : '' ?></div>
        </td>
        <td>
            <div><?= $item["unit_of_measure"] ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_7_header($formname) {
    ?>
    <table class="itemlist" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <td width="100" align="left">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["unit"] ?></div>
        </td>
    </tr>
<?
}

// Artikelliste : Rechnung-Sales Invoice History
function show_item_list_8( $item, $formname ) {
    ?>
    <tr id="item_<?= $item["no"] ?>"
        style="cursor: default; height: 30px;">
        <td>
            <div><?= $item["no"] ?></div>
        </td>
        <td>
            <div><?= $item["description"] ?><? //echo "<br />$item['summary']"; ?></div>
        </td>
        <td width="40" align="left">
            <div><?= ($item["quantity"] != '0') ? round($item["quantity"]) : '' ?></div>
        </td>
        <td>
            <div><?= $item["unit_of_measure"] ?></div>
        </td>
        <td width="130" align="right">
            <div><?= ($item["line_amount"] != '0') ? format_amount($item["line_amount"], FALSE) : '' ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_8_header($formname) {
    ?>
    <table class="itemlist" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <td width="40" align="left">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["unit"] ?></div>
        </td>
        <td width="130" align="right">
            <div><?= $GLOBALS["tc"]["line_amount_sales"] ?></div>
        </td>
    </tr>
<?
}


//Artikelvorschau ohne Warenkorb und Favoriten
function show_item_list_10( $item, $image ) {

    $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist2">
        <div class="itemlist2_image" onclick="window.location.href='<?= $itemlink ?>'">
            <div class="itemlist2_image_layer1"><img src="<?= $imagelink ?>" border="0"
                                                     alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" ></div>
            <? show_item_promotion_banners($item); ?>
        </div>
        <div class="itemlist2_description" onclick="window.location.href='<?= $itemlink ?>'">
            <strong><?= $item["description"] ?></strong><? //echo "<br />$item['summary']"; ?></div>
        <div class="itemlist2_item_no"
             onclick="window.location.href='<?= $itemlink ?>'"><?= get_item_no_or_no_of_variants($item) ?></div>
        <div class="itemlist2_price" style="text-align:left; width:144px;"
             onclick="window.location.href='<?= $itemlink ?>'">
            <?
            if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
            } else {
                echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE);
            }
            ?>
        </div>
    </div>
<?
}

function variant_select_list( $item ) {
    $parent_item = get_item_variant_parent($item);
    if ($parent_item["id"] <> '') {
        $query = "SELECT shop_view_active_item.* FROM shop_item_link LEFT JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no WHERE shop_item_link.type = '0' AND shop_item_link.item_no = '" . $parent_item["item_no"] . "' AND shop_view_active_item.main_site_id= '" . $parent_item["main_site_id"] . "' AND shop_view_active_item.main_language_id = '" . $parent_item["main_language_id"] . "'";
    } else {
        $query = "SELECT shop_view_active_item.* FROM shop_item_link LEFT JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no WHERE shop_item_link.type = '0' AND shop_item_link.item_no = '" . $item["item_no"] . "' AND shop_view_active_item.main_site_id= '" . $item["main_site_id"] . "' AND shop_view_active_item.main_language_id = '" . $item["main_language_id"] . "'";
    }
    if ($query <> '') {
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) > 0) {
            echo "<a href=\"javascript:void(0)\" onclick=\"toggle('variant_select_over_" . $item["id"] . "')\"><img src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/basket_add1.gif\" border=\"0\" /></a>\n";
            echo "<div id=\"variant_select_over_" . $item["id"] . "\" class=\"variant_select_over\"><table cellspacing=0 cellpadding=0 border=0>\n";

            if ($GLOBALS["shop_setup"]["variants_main_item_type"] != 0) {
                if ($parent_item != "") {
                    $item = $parent_item;
                }
                $descr    = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                $itemlink = "?shop_category=" . $_GET["shop_category"] . "&card=" . $item["id"];
                echo "<tr onclick=\"window.location.href = '" . $itemlink . "'\"><td>" .
                    $descr . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" .
                    $item["item_no"] . "</td><td>";
                get_inventory_sign($item);
                echo "</td></tr>";
            }

            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_itemlink = ml($sitepart, "action", "shop_add_item_to_basket", "action_id", $variant_item["id"], "sid", session_id());
                echo "<tr onclick=\"window.location.href = '" . $variant_itemlink . "'\"><td>" . $variant_item["variant_type"] . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" . $variant_item["item_no"] . "</td><td>";
                get_inventory_sign($variant_item);
                echo "</td></tr>";
            }
            echo "</table></div>\n";
        } else {
            echo "<a href=\"" . ml($sitepart, "action", "shop_add_item_to_basket", "action_id", $item["id"]) . "\"><img src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/basket_add1.gif\" border=\"0\" /></a>\n";
        }
    }
}

// Bluestar HT: Kategorieliste mit Kategoriebildern

function show_category_list( $result, $language ) {
    while ($val = @mysqli_fetch_array($result)) {
        $categoryPath = get_category_path($val['id'], $language);
        $categorylink = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath;
        $image        = get_category_icon($val, $language);

        ?>
<div class="categorylist">
  <div class="categorylist_image" onclick="window.location.href='<?= $categorylink ?>'">
    <div class="categorylist_image_layer1"><?= $image ?></div>
  </div>
  <div class="categorylist_description" onclick="window.location.href='<?= $categorylink ?>'"><strong><?= $val["name"] ?></strong></div>
</div>
<?
    }
}

function get_category_path( $current_category, $language ) {
    $query            = "SELECT *
			  FROM shop_category
			  WHERE language_code = '" . $language["code"] . "'
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND id = " . $current_category . "";
    $result           = @mysqli_query($GLOBALS['mysql_con'], $query);
    $val              = @mysqli_fetch_array($result);
    $current_category = $val["id"];
    $name             = $val["code"] . "/";
    $parent           = $val["parent_line_no"];
    if ($parent != 0) {
        $query  = "SELECT id FROM shop_category WHERE line_no='" . $val['parent_line_no'] . "' AND company = '" . $val['company'] . "' AND shop_code = '" . $val['shop_code'] . "' AND language_code = '" . $val['language_code'] . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_assoc($result);
        $name   = get_category_path($row['id'], $language) . $name;
    }
    return $name;
}


function show_item_list_with_pages( $type, $query_, $category, $vendor_sql_string = "", $max_number = 0, $order_by = "", $show_all_items = FALSE, $is_Search = FALSE, $search_query = "" ) {
    if (!$is_Search && $category != "campain" && $category['user_sorting'] == 1) {
        if (!$is_Search && $category != "campain") {
            if ($order_by == "ranking") {
                $order_by_val = " ORDER BY " . get_sort_type($order_by) . " ";
            } else {
                $order_by_val = " ORDER BY " . get_sort_type($order_by) . " ";
            }
        }
        if ($show_all_items) {
            echo "<form name=\"item_order\" method=\"post\">";
            echo create_sort_select($GLOBALS["tc"]["sort_items_by"] . ":", "sort_by", $order_by);
            echo "</form>";
        }
    } elseif ($category['sort_items'] != 0) {
         if (!empty($order_by_val)) {
            $order_by_val = " ORDER BY " . get_sort_type($order_by) . " ";
        }
    }
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }
    if (!$is_Search) {
        if ($category != "campain") {
            $cat_id       = $category["id"];
            $categoryPath = get_category_path($cat_id, $GLOBALS["shop_language"]) . "?";
        } else {
            $categoryPath = "?shop_category=campain&shop_campain=" . $_REQUEST['shop_campain'] . "&";
            $order_by_val = " ORDER BY shop_view_active_item.item_no ";
        }
    } else {
        $categoryPath = "?shop_category=search&input_search=" . $_REQUEST["input_search"] . "&";
        $order_by_val = " ORDER BY shop_view_active_item.order_ranking ";
    }
    if ($max_number > 0) {
        $from = (($page - 1) * $GLOBALS['shop_setup']['num_items_per_page']);
        if (($page * $GLOBALS['shop_setup']['num_items_per_page']) <= $max_number) {
            $reach = $GLOBALS['shop_setup']['num_items_per_page'];
        } else {
            $reach = $max_number - $from;
        }
    } else {
        $from  = ($page - 1) * $GLOBALS['shop_setup']['num_items_per_page'];
        $reach = $GLOBALS['shop_setup']['num_items_per_page'];
    }
    $query        = $query_;
    $limit        = "LIMIT " . $from . ", " . $reach . "";
    $query_limit  = $query . $order_by_val . $limit;
    $result_limit = mysqli_query($GLOBALS['mysql_con'], $query_limit);
    $result       = mysqli_query($GLOBALS['mysql_con'], $query);
    $rows         = mysqli_num_rows($result);
    if (($rows > $max_number) && ($max_number > 0)) {
        $rows = $max_number;
    }
    switch ($type) {
        case 1:
            //$list_type = $category["show_all_items"];
            $list_type = 1;
            break;
        case 2:
            //$list_type = $category["show_random_items"];
            $list_type = 2;
            break;
        case 3:
            $list_type = 3;
            break;
        default:
            $list_type = 1;
            //$list_type = $category["show_campain_items"];
            break;
    }
    if (($category == "" || $category == "campain") && !$is_Search) {
        $list_type = 2;
    }
    if ($rows > $GLOBALS['shop_setup']['num_items_per_page']) {
        $rest = $rows % $GLOBALS['shop_setup']['num_items_per_page'];
        if ($rest != 0) {
            $number_of_subrows = intval($rows / $GLOBALS['shop_setup']['num_items_per_page']) + 1;
        } else {
            $number_of_subrows = intval($rows / $GLOBALS['shop_setup']['num_items_per_page']);
        }
        if (($number_of_subrows > 1) || (($number_of_subrows <= 1) && ($rest != 0))) {
            //Generieren der "weiter" bzw. "zurück" Links
            if ($page != 1) {
                $output           = $page - 1;
                $step_back_link   = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $step_back_output = '<div class=\"step-back\"><a href="' . $step_back_link . '">&lt;</a></div>';
            } else {
                $step_back_output = "";
            }
            if (!$is_Search) {
                if ($page != $number_of_subrows) {
                    $output              = $page + 1;
                    $step_forward_link   = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                    $step_forward_output = '<div class=\"step-forward\"><a href="' . $step_forward_link . '">&gt;</a></div>';
                } else {
                    $step_forward_output = "";
                }
            } else {
                if ($number_of_subrows > 4) {
                    $pages = 4;
                } else {
                    $pages = $number_of_subrows;
                }
                if ($page != $pages) {
                    $output              = $page + 1;
                    $step_forward_link   = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                    $step_forward_output = '<div class=\"step-forward\"><a href="' . $step_forward_link . '">&gt;</a></div>';
                } else {
                    $step_forward_output = "";
                }
            }
            //Ende

            //Erstellen der Seitenauswahl
            echo "<div class=\"page_switch\">";
            echo $step_back_output;
            if (!$is_Search) {
                for ($i = 1; $i <= $number_of_subrows; $i++) {
                    if ($i >= 2) {
                        $pipe = "";
                    } else {
                        $pipe = "";
                    }
                    if ($page == $i) {
                        $output           = "<div class=\"current\"><div>" . $i . "</div></div>";
                        $item_link_output = $output;
                    } else {
                        $output           = $i;
                        $itemlink         = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                        $item_link_output = "<div class=\"non-current\"><a href=\"" . $itemlink . "\">" . $output . "</a></div>";
                    }
                    echo $pipe . $item_link_output . "";
                }
            } else {
                if ($number_of_subrows > 4) {
                    $pages = 4;
                } else {
                    $pages = $number_of_subrows;
                }
                for ($i = 1; $i <= $pages; $i++) {
                    if ($i >= 2) {
                        $pipe = "";
                    } else {
                        $pipe = "";
                    }
                    if ($page == $i) {
                        $output           = "<div class=\"current\"><div>" . $i . "</div></div>";
                        $item_link_output = $output;
                    } else {
                        $output           = $i;
                        $itemlink         = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                        $item_link_output = "<div class=\"non-current\"><a href=\"" . $itemlink . "\">" . $output . "</a></div>";
                    }
                    echo $pipe . $item_link_output . "";
                }
            }
            echo $step_forward_output;
            echo "</div>";
            //Ende
        }
    }
    echo "<div class=\"spacer_1\"></div>";
    //show_item_list_from_query($result_limit,$list_type,false,"",$categoryPath);
    show_item_list_from_query($result_limit, $list_type, "");
//Erstellen der Seitenauswahl
    echo "<div class ='clearfloat'></div>";
    echo "<div class=\"spacer_1\"></div>";
    echo "<div class=\"page_switch\">";
    echo $step_back_output;
    if (!$is_Search) {
        for ($i = 1; $i <= $number_of_subrows; $i++) {
            if ($i >= 2) {
                $pipe = "";
            } else {
                $pipe = "";
            }
            if ($page == $i) {
                $output           = "<div class=\"current\"><div>" . $i . "</div></div>";
                $item_link_output = $output;
            } else {
                $output           = $i;
                $itemlink         = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $item_link_output = "<div class=\"non-current\"><a href=\"" . $itemlink . "\">" . $output . "</a></div>";
            }
            echo $pipe . $item_link_output . "";
        }
    } else {
        if ($number_of_subrows > 4) {
            $pages = 4;
        } else {
            $pages = $number_of_subrows;
        }
        for ($i = 1; $i <= $pages; $i++) {
            if ($i >= 2) {
                $pipe = "";
            } else {
                $pipe = "";
            }
            if ($page == $i) {
                $output = "<div class=\"current\"><div>" . $i . "</div></div>";;
                $item_link_output = $output;
            } else {
                $output           = $i;
                $itemlink         = "/" . $GLOBALS["site"]["code"] . "/" . $GLOBALS["language"]["code"] . "/shop/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $item_link_output = "<div class=\"non-current\"><a href=\"" . $itemlink . "\">" . $output . "</a></div>";
            }
            echo $pipe . $item_link_output . "";
        }
    }
    echo $step_forward_output;
    echo "</div>";
    //Ende
    echo "</div>";
}

    
function show_item_list_from_query($query,$listNo,$formname) {
        if (is_array($query)) {
        show_item_list($query,$listNo,$formname);
    }
    if(isset($GLOBALS['IOC'])) {
        $IOCContainer = $GLOBALS['IOC'];
        if ($IOCContainer instanceof \Dice\Dice) {
            $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
            if ($itemBuilder instanceof WebshopItemBuilder) {
                $objArr = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($itemBuilder->buildWebshopItemsFromItemQuery($query));
                $item_result = [];
                /**
                * @var $itemObj OrderableEntityInterface|WebshopItemWithImages
                */
                foreach ($objArr as $itemObj) {
                    $item = [];
                    $item['id'] = $itemObj->getID();
                    $item['company'] = $itemObj->getCompany();
                    $item['shop_code'] = $itemObj->getShopCode();
                    $item['language_code'] = $itemObj->getLanguageCode();
                    $item['item_no'] = $itemObj->getItemNo();
                    $item['var_code'] = $itemObj->getVariantCode();
                    $item['retail_price'] = $itemObj->getRetailPrice();
                    $item['base_price'] = $itemObj->getBasePrice();
                    $item['unit_price'] = $itemObj->getUnitPrice();
                    $item['customer_price'] = $itemObj->getUnitPrice();
                    $item['cross_price'] = $itemObj->getCrossPrice();
                    $item['image_data'] = $itemObj->getImageData();
                    $item['main_image_data'] = $itemObj->getMainImageData();
                    $item['description'] = $itemObj->getDescription();
                    $item['variant_typ'] = $itemObj->getVariantType();
                    $item['variant_type'] = $itemObj->getVariantType();
                    $item['summary'] = $itemObj->getSummary();
                    $item['parent_item_no'] = $itemObj->getParentItemNo();
                    $item['notifications'] = get_item_user_notifications($itemObj);
                    $item['inventory'] = $itemObj->getInventory();
                    $item['availability'] = $itemObj->getAvailability();
                    $item['is_available'] = $itemObj->isAvailable();
                    $item['min_qty'] = $itemObj->getMinQty();
                    $item['max_qty'] = $itemObj->getMaxQty();
                    $item['qty_step'] = $itemObj->getQtyStep();
                    $item['main_category_line_no'] = $itemObj->main_category_line_no;
                    $item_result[] = $item;
                }
                $numRows = count($item_result);
            }
        }
    }

    if(empty($item_result)) {
        $item_result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $numRows = @mysqli_num_rows($item_result);
        if($numRows > 0) {
            $item_result = @mysqli_fetch_all($item_result, MYSQLI_ASSOC);
        }
    }
    if ($numRows > 0) {
        show_item_list($item_result,$listNo,$formname);
    }
}