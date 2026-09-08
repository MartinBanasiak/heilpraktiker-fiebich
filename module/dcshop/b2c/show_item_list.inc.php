<?php
use DynCom\dc\common\classes\FormBuilder;use DynCom\dc\dcShop\classes\ItemOrderButtonBuilder;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithImages;

$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
include_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/item_functions.inc.php';

function show_item_list( $result, $type, $formname = "",$columns = 4 ) {
    $input_counter = 1;
    if(is_a('mysqli_result',$result)) {
        $allTraversible = mysqli_fetch_all($result,MYSQLI_ASSOC);
    } else {
        $allTraversible = &$result;
    }
    if ($formname == "") {
        $formname = "form_itemlist_" . $GLOBALS["shop_item_listform"];
    }
    $GLOBALS["input_counter"] = 1;
    $GLOBALS["shop_item_listform"]++;

    if (count($allTraversible) > 0) {
        switch ($type) {
            case 1:
                show_item_list_1_header($formname);
                break;
            case 2:
                echo "<div class=\"itemcard_list2  itemcard_list\"><div class='row'>\n";
                $script = '
                        <script type="text/javascript">
                            $(document).ready(function() {
                                var itemlistOrderButtonNumberInputWrappers = $(\'.itemlist_qty_wrapper_active\');
                                
                                $(itemlistOrderButtonNumberInputWrappers).each(function() {
                                    var currWrapper = this,
                                        currInput = $(currWrapper).find(\'input[type=number]\'),
                                        currMin = parseFloat($(currInput).attr(\'min\')),
                                        currMax = parseFloat($(currInput).attr(\'max\')),
                                        currStep = parseFloat($(currInput).attr(\'step\')),
                                        currSpinnerUp = $(currWrapper).find(\'.spinner_up[data-disabled!="disabled"]\'),
                                        currSpinnerDown = $(currWrapper).find(\'.spinner_down[data-disabled!="disabled"]\');
                            
                                        $(currSpinnerUp).on(\'click\',function(e) {
                                            e.preventDefault();
                                            var form = $(this).parents(\'form\'),
                                                formname = $(form).attr(\'name\'),
                                                currVal = parseFloat(currInput.val()),
                                                newVal = currVal + currStep;
                                            if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
                                                $(currInput).val(newVal);
                                                $(this).closest(\'form\').find(\'.item_qty\').val(newVal);
                                            }
                                        });
                                        
                                        $(currSpinnerDown).on(\'click\',function(e) {
                                            e.preventDefault();
                                            var form = $(this).parents(\'form\'),
                                                formname = $(form).attr(\'name\'),
                                                currVal = parseFloat(currInput.val()),
                                                newVal = currVal - currStep;
                                            if(!isNaN(newVal) && (newVal <= currMax) && (newVal >= currMin)) {
                                                $(currInput).val(newVal);
                                                $(this).closest(\'form\').find(\'.item_qty\').val(newVal);
                                            }
                                        });
                                });
                                
                            });
                         </script>
                        ';
                        echo $script;
                break;
            case 3:
                echo "<div class=\"itemcard_list3  itemcard_list\"><div class='row'>\n";
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
                echo "<h1>".$GLOBALS["tc"]["gift_package_select"]."</h1>";
                echo '<a class="button_gift_package_back_to_basket " href="/'.customizeUrl().'/basket/" >' . $GLOBALS["tc"]["back_to_basket"] . '</a>';
                echo "<div class=\"itemcard_list2\">\n";
                break;
            case 8:
                echo "";
                break;
            case 10:
                echo "<div class=\"itemcard_list2  itemcard_list\">\n";
                break;
            case 11:
                echo "<div class=\"itemcard_list11 bxslider itemcard_list\">\n";
                break;
            case 14:
                echo  show_item_list_14_header($formname);
                break;
        }
        foreach ($allTraversible as $item) {
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

            switch ($columns) {
                case 1:
                    $columns_class = "col-xs-12";
                    break;
                case 2:
                    $columns_class = "col-xs-12 col-sm-6";
                    break;
                case 3:
                    $columns_class = "col-xs-12 col-sm-6 col-md-4";
                    break;
                case 4:
                    $columns_class = "col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xlg-3";
                    break;
                case 5:
                    $columns_class = "col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xlg-2-5";
                    break;
                case 6:
                    $columns_class = "col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xlg-2";
                    break;
                case 14:
                    $columns_class = "col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xlg-2";
                    break;
                default:
                    $columns_class = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
                    break;
            }

            switch ($type) {
                case 1:
                    $input_counter = show_item_list_1($item, $image, $formname, $input_counter);
                    break;
                case 2:
                    show_item_list_2($item, $image,$columns_class);
                    break;
                case 3:
                    show_item_list_3($item, $image,$columns_class);
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
                    show_item_list_7($item, $image, $formname);
                    break;
                case 8:
                    show_item_list_8($item, $formname);
                    break;
                case 10:
                    show_item_list_10($item, $image);
                    break;
                case 11:
                    show_item_list_11($item, $image);
                    break;
                case 13:
                    show_item_list_13($item, $image, $formname);
                    break;
                case 14:
                    show_item_list_14($item, $image, $formname);
                    break;
            }
        }
        switch ($type) {
            case 1:
                echo "</table>\n</form>\n";
                break;
            case 2:
                echo "</div></div>\n";
                break;
            case 3:
                echo "</div></div>\n";
                break;
            case 4:
                echo "</div></form>\n";
                break;
            case 5:
                echo "</div>\n";
                break;
            case 6:
                echo "</table>\n";
                break;
            case 7:
                echo "</div>\n";
                break;
            case 8:
                echo "\n";
                break;
            case 10:
                echo "</div>\n";
                break;
            case 11:
                echo "</div>\n";
                break;
         case 14:
                echo "</div>\n";
                break;
        }
    }
}

// Artikelliste 1 :Für Übersichten, Zubehörartikel und Ersatzteile

function show_item_list_1( $item, $image, $formname,$input_counter = 1 ) {
    $toggle_text = '';
    $imagelink = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $query = "SELECT shop_view_active_item.*
			  FROM shop_item_link
			  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
			  WHERE shop_item_link.type = '0'
			  	AND shop_item_link.item_no = '" . $item["item_no"] . "'
			  	AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        	  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        	  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'";

    $result         = @mysqli_query($GLOBALS['mysql_con'], $query);
    $no_of_variants = @mysqli_num_rows($result);
    if ($no_of_variants > 0) {
        while ($variant_item = @mysqli_fetch_array($result)) {
            $toggle_text .= "togglenb('item_" . $variant_item["item_no"] . "');";
        }
        show_item_list_1_has_variant($item, $image, $imagelink, $itemlink, $toggle_text, $formname);
        $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
        $local_input_counter = 1;
        $new_arr = '';
        while ($variant_item = @mysqli_fetch_array($result)) {
            $variant_image                 = get_item_main_image($variant_item, get_item_variant_parent($variant_item));
            $variant_item_description_link = str_replace('/', '', $variant_item["item_slug"]);
            $variant_item_description_link = str_replace(".", "", $variant_item_description_link);

            if ($variant_image["filename"] == '') {
                if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                    $variant_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                } else {
                    $variant_image["filename"] = "noimage.jpg";
                }
            }
            $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];

            if ($_GET["card"] != "") {
                $arr = explode("/", $_SERVER["REQUEST_URI"]);
                for ($i = 1; $i < count($arr) - 2; $i++) {
                    $new_arr .= "/" . $arr[$i];
                }
                $variant_itemlink = $new_arr . "/" . ($variant_item_description_link) ."-p" . $variant_item["id"]. "/";
            } else {
                $variant_itemlink = urlencode($variant_item_description_link) . "-p" . $variant_item["id"]."/";
            }
            $i++;
            show_item_list_1_is_variant($variant_item, $variant_image, $variant_imagelink, $variant_itemlink, $formname, ($i == $no_of_variants));
        }
    } else {
        show_item_list_1_no_variant($item, $imagelink, $itemlink, $formname);
    }
    return $input_counter;
}

function show_item_list_1_is_variant( $item, $image, $imagelink, $itemlink, $formname, $last_child = false ) {
    $parent_item     = get_item_variant_parent($item);
    $last_child_text = ($last_child) ? "_last_child" : "";
    ?>
    <tr id="item_<?= $item["item_no"] ?>" style="display:none;">
        <td width="19" align="center"
            style="background-image:url(/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/itemlist_tree<?= $last_child_text ?>.gif); background-repeat: no-repeat; background-position: left top;">
            <div></div>
        </td>
        <td width="45" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>"></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["item_no"] ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["variant_type"] ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, true); ?></div>
        </td>
        <td align="right" onclick="window.location.href='<?= $itemlink ?>'">

            <?
            if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false) . "";
            }
            ?>
        </td>
        <td align="center" width="40" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? get_inventory_sign($item) ?></div>
        </td>
        <td width="40" align="center">
            <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                        id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                        value="<?= $item["id"] ?>" /><input style="width:30px;"
                                                            name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            type="text" tabindex="<?= tabcounter() ?>" maxlength="3" />
            </div>
        </td>
        <td width="30" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_item_to_basket_list", "action_id", $item["id"]) ?>'; document.<?= $formname ?>.submit()"><img
                        onmouseover="TagToTip('basket_add1')" onmouseout="UnTip()"
                        src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add1.gif" border="0" /></a>
            </div>
        </td>
        <td width="30" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img
                        onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()"
                        src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add2.gif" border="0" /></a>
            </div>
        </td>
        <td width="30" align="center">
            <div><? get_favorite_sign($item) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_has_variant( $item, $image, $imagelink, $itemlink, $toggle_text, $formname ) {
    ?>
    <tr id="item_<?= $item["item_no"] ?>">
        <td width="19" align="center"
            style="background-image:url(/layout/frontend/shop/img/itemlist_tree_expand.gif); background-repeat: no-repeat; background-position: left top;"
            onclick="changeBg(this,'/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/itemlist_tree_expand.gif','/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/itemlist_tree_collapse.gif');<?= $toggle_text ?>"></td>
        <td width="45" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" ></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= get_item_no_or_no_of_variants($item) ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["description"] ?><br /><?= $item["summary"] ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, true); ?></div>
        </td>
        <td align="right" onclick="window.location.href='<?= $itemlink ?>'">
            <div>
                <?
                if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                } else {
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false) . "";
                }
                ?>
            </div>
        </td>
        <td align="center" width="40" onclick="window.location.href='<?= $itemlink ?>'">
            <div><? get_inventory_sign($item) ?></div>
        </td>
        <?
        if ($GLOBALS['shop']['variant_typ'] == '1') {
            //echo ml($sitepart,"action","shop_add_item_to_basket_list","action_id",$item["id"]);
            ?>
            <td width="40" align="center">
                <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                            id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                            value="<?= $item["id"] ?>" /><input style="width:30px;"
                                                                name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                                id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                                type="text" tabindex="<?= tabcounter() ?>"
                                                                maxlength="3" /></div>
            </td>
            <td width="30" align="center">
                <div><a href="javascript:void(0)"
                        onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_item_to_basket_list", "action_id", $item["id"]) ?>'; document.<?= $formname ?>.submit()"><img
                            onmouseover="TagToTip('basket_add1')" onmouseout="UnTip()"
                            src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/shop/img/basket_add1.gif" border="0" /></a></div>
            </td>
            <td width="30" align="center">
                <div><a href="javascript:void(0)"
                        onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img
                            onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()"
                            src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/shop/img/basket_add2.gif" border="0" /></a></div>
            </td>
        <?
        } else {
            ?>
            <td width="40" align="center">
                <div></div>
            </td>
            <td width="30" align="center">
                <div></div>
            </td>
            <td width="30" align="center">
                <div></div>
            </td>
        <?
        }
        ?>
        <td width="30" align="center">
            <div><? get_favorite_sign($item) ?></div>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_no_variant( $item, $imagelink, $itemlink, $formname ) {
    ?>
    <tr id="item_<?= $item["item_no"] ?>">
        <td width="19" align="center">
            <div></div>
        </td>
        <td width="45" align="center" onclick="window.location.href='<?= $itemlink ?>'">
            <div><img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>"  title="<?= $image['description'] ?>" ></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["item_no"] ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><?= $item["description"] ?><br /><?= $item["summary"] ?></div>
        </td>
        <td onclick="window.location.href='<?= $itemlink ?>'">
            <div><? show_item_promotion_banners($item, true); ?></div>
        </td>
        <td align="right" onclick="window.location.href='<?= $itemlink ?>';">
            <div>
                <?
                //NEUE STREICHPREISFINDUNG
                if ($GLOBALS['shop']['cross_price_typ'] != 0) {
                    get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                } else {
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false) . "";
                }
                ?>
            </div>
        </td>
        <td align="center" width="40"><a href="<?= $itemlink ?>">
                <div><? get_inventory_sign($item) ?></div>
            </a></td>
        <td width="40" align="center">
            <div><input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                        id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                        value="<?= $item["id"] ?>" /><input style="width:30px;"
                                                            name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                                                            type="text" tabindex="<?= tabcounter() ?>" maxlength="3" />
            </div>
        </td>
        <td width="30" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_item_to_basket_list", "action_id", $item["id"]) ?>'; document.<?= $formname ?>.submit()"><img
                        onmouseover="TagToTip('basket_add1')" onmouseout="UnTip()"
                        src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add1.gif" border="0" /></a>
            </div>
        </td>
        <td width="30" align="center">
            <div><a href="javascript:void(0)"
                    onclick="document.<?= $formname ?>.action='<?= ml($sitepart, "action", "shop_add_all_items_to_basket") ?>'; document.<?= $formname ?>.submit()"><img
                        onmouseover="TagToTip('basket_add2')" onmouseout="UnTip()"
                        src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/basket_add2.gif" border="0" /></a>
            </div>
        </td>
        <td width="30" align="center">
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
        <td width="19">
            <div>&nbsp;</div>
        </td>
        <td width="45">
            <div>&nbsp;</div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["item_no"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["description"] ?></div>
        </td>
        <td>
            <div><?= $GLOBALS["tc"]["notice"] ?></div>
        </td>
        <td align="right">
            <div><?= $GLOBALS["tc"]["your_price"] ?></div>
        </td>
        <td width="40" align="center">
            <div><?= $GLOBALS["tc"]["inventory"] ?></div>
        </td>
        <td width="40" align="center">
            <div><?= $GLOBALS["tc"]["quantity"] ?></div>
        </td>
        <td width="30">
            <div>&nbsp;</div>
        </td>
        <td width="30">
            <div>&nbsp;</div>
        </td>
        <td width="30">
            <div>&nbsp;</div>
        </td>
    </tr>
<?
}

// Artikelliste 2

function show_item_list_2( $item, $image,$columns_class ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_general_item_path($item, true);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $no_of_vars = get_item_no_of_variants($item);
    if ($no_of_vars > 0) {
        $variantItemsWithPictures = get_item_variant_pictures($item);
        $no_of_var_pictures = count($variantItemsWithPictures);
    }

    $itemNoForImageQuery = $item["item_no"];

    if ($no_of_var_pictures > 0) {
        $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $variantItemsWithPictures[0]["file_filename"];
        $itemNoForImageQuery = $variantItemsWithPictures["item_no"];
    }

    if(isset($GLOBALS['IOC'])) {
        $IOCContainer = $GLOBALS['IOC'];
    }
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $itemObj = $itemBuilder->getFirstActiveVariant($item["id"], '');
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $customer = $shopConfig->getCustomer();
    $currencyCode = $shopConfig->getCurrencyCode();


	$query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.filename <> '".$image["filename"]."'
			  	AND shop_item_file.company = '".$GLOBALS['shop']['company']."'
			  	AND shop_item_file.shop_code = '".$GLOBALS['shop']['item_source']."'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
			  ORDER BY line_no LIMIT 1";
	#print_r($query);
	$result = @mysqli_query($GLOBALS["mysql_con"], $query);
	if(@mysqli_num_rows($result) > 0) {
		while($hover_image = @mysqli_fetch_array($result)) {
				$imagelink_hover = $GLOBALS['shop_setup']['image_config'][3]["path"] . "/" . $hover_image["filename"];
		}
	}else{
		$imagelink_hover = $imagelink;
	}
    ?>
    <div class="itemlist2 itemlist <?=$columns_class?>" data-itemlink="<?= $itemlink ?>">
        <form name='form_<?= $item["item_no"] ?>' id='form_<?= $item["item_no"] ?>' method='POST' >
            <div class="itemlist_container<?= ($no_of_vars > 0) ? " has_variants" : "" ?><?= ($no_of_var_pictures > 0) ? " has_variant_pictures" : "" ?>">
                <div class="itemlist_item clearfix">
                    <a href="<?= $itemlink ?>">
                        <div class="itemlist_content banner">
                            <? show_item_promotion_banners($item); ?>
                            <div class="itemlist2_banners_campaign">
                                <? show_item_campaign_banners($item, false, true, true);?>
                            </div>
                                <? if($item['customizable'] != 0){ ?>
                                <i class="fa fa-cog" aria-hidden="true"></i>
                                <? } ?>
                        </div>
                        <div class="itemlist_content image">
                            <img data-main="<?= $imagelink ?>" src="<?= $imagelink ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">

                        </div>
                        <div class="itemlist_content brand">
                            <?= get_brand_name($item, 1) ?>
                        </div>
                        <div class="itemlist_description_wrapper">
                            <div class="itemlist_content description">
                                <?= $item["description"] ?>
                            </div>
                            <div class="itemlist_content summary" style="display: none;">
                                <?= $item["summary"] ?>
                            </div>
                        </div>
                        <div class="itemlist_content prices">
                            <?php

                            $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
                            /** @var \DynCom\dc\dcShop\classes\AdvancedPriceProvider $advancedPriceProvider */
                            $ItemCustomerPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);
                            $ItemCrossPrice = $advancedPriceProvider->getItemCrossPrice($itemObj, $customer, $currencyCode);

                            if($ItemCustomerPrice->getCustomerPrice() !== null && $ItemCustomerPrice->getCustomerPrice() > 0) {
                            //if(false) {
                                if(isset($ItemCrossPrice) && $ItemCrossPrice > 0) {?>
                                    <div class="cross_price"><?= format_amount($ItemCrossPrice, false)?></div>
                                <?}?>
                                <div class='base_price'><?= format_amount($ItemCustomerPrice->getCustomerPrice(), false)?></div>
                            <?php
                            } elseif ($GLOBALS['shop']['cross_price_typ'] != 1) {
                                $item = item_obj_to_itemlistlist_array($itemObj);
                                echo get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                            } else {
                                echo "<div class='base_price'>";
                                echo format_amount($ItemCustomerPrice->getCustomerPrice(), false) . "";
                                echo "</div>";
                            } ?>
                        </div>
                        <div class="itemlist_content rating">
                            <? show_item_rating($item, true); ?>
                        </div>
                    </a>
                </div>
                <div class="itemlist2_extended itemlist_extended">
                    <div class="itemlist_extended_content">
                        <a href="<?= $itemlink ?>">
                            <div class="itemlist_content image">
                                <img src="<?= $imagelink_hover ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" >
                            </div>
                        </a>
                        <?php
                        $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

                        ?>
                        <? if (!($no_of_vars > 0)) { ?>
                            <div class="basket">
                                <?
                                $shopConfig = $IOCContainer->create('$CurrShopConfig');
                                $customer = $shopConfig->getCustomer();
                                $currencyCode = $shopConfig->getCurrencyCode();
                                $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
                                $defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);

                                $subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

                                $validator = new \DynCom\dc\common\classes\Validator([], []);
                                $formBuilder = new FormBuilder('dummyID');
                                $templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

                                $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
                                $vatMgr = $IOCContainer->create('$VATManager');
                                $builder = new ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating,$vatMgr);
                                $htmlString = $builder->getItemlistButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,$GLOBALS["input_counter"],true);
                                //$htmlString = $builder->getItemcardButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,true);

                                echo '<div class="itemlist_content">' . $htmlString . '</div>';
        ?>
                            </div>
                        <? } else { ?>
                            <div class="itemlist_content">
                                <?= str_replace('%no_of_vars%',$no_of_vars,$GLOBALS["tc"]["no_of_variants"]) ?>
                            </div>
                        <? } ?>
                            <a href="<?= $itemlink ?>">
                                <?
                                switch ($item["vat_prod_posting_group"]) {
                                    case $GLOBALS["shop"]["vat_identifier_1"]:
                                        $suffix = '_vat1';
                                        break;
                                    case $GLOBALS["shop"]["vat_identifier_2"]:
                                        $suffix = '_vat2';
                                        break;
                                    case $GLOBALS["shop"]["vat_identifier_3"]:
                                        $suffix = '_vat3';
                                        break;
                                    default:
                                        $suffix = '_vat1';
                                        break;
                                }
                                ?>
                                <div class="itemlist_content vat_info">
                                <?= $GLOBALS["tc"]["item_vat_and_shipping_notice_plus_ship" . $suffix] ?>
                                </div>
                                <? /* <div class="itemlist_content">
                                <? if ($no_of_vars > 0) { get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item); } ?>
                                </div> */?>
                                <div class="price_per_gramm"><?= get_gram_price($item) ?></div>
                            </a>
                    </div>
                </div>
                <? if (($no_of_vars > 0) && ($no_of_var_pictures > 0)) { ?>
                    <?= show_variant_pictures($variantItemsWithPictures) ?>
                <? } ?>
            </div>
            <input type="hidden" name="item_qty" class="item_qty" value="0">
        </form>
    </div>
<?
}

// Artikelliste 3: Alternativartikel und Passende Artikel

function show_item_list_3( $item, $image, $columns_class ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_general_item_path($item, true);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $no_of_vars = get_item_no_of_variants($item);

    if(isset($_GET['shop_category'])) {
        $itemlistclass= "shop_category_" . $_GET['shop_category'];
    }else{
        $itemlistclass= "";
    }

	$query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.filename <> '".$image["filename"]."'
			  	AND shop_item_file.company = '".$GLOBALS['shop']['company']."'
			  	AND shop_item_file.shop_code = '".$GLOBALS['shop']['item_source']."'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
			  ORDER BY line_no LIMIT 1";
	#print_r($query);
	$result = @mysqli_query($GLOBALS["mysql_con"], $query);
	if(@mysqli_num_rows($result) > 0) {
		while($hover_image = @mysqli_fetch_array($result)) {
				$imagelink_hover = $GLOBALS['shop_setup']['image_config'][3]["path"] . "/" . $hover_image["filename"];
		}
	}else{
		$imagelink_hover = $imagelink;
	}
    ?>
    <div class="itemlist3 itemlist  <?=$columns_class?> <?=$itemlistclass?>">
        <div class="itemlist_container">
            <a href="<?= $itemlink ?>">
                <div class="itemlist_content banner">
                    <? show_item_promotion_banners($item); ?>
                    <div class="itemlist2_banners_campaign">
                        <? show_item_campaign_banners($item, false, false, true);?>
                    </div>
                </div>
                <div class="itemlist_content image">
                    <img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                </div>
                <div class="itemlist_content brand">
                    <?= get_brand_name($item, 1) ?>
                </div>
                <div class="itemlist_content description">
                    <?= $item["description"] ?>
                </div>
                <div class="itemlist_content summary" style="display: none;">
                    <?= $item["summary"] ?>
                </div>
                <div class="itemlist_content prices">
                    <?php
                    if(isset($item['customer_price'])) {
                    //if(false) {
                        if(isset($item['cross_price']) && $item['cross_price'] > 0) {?>
                            <div class="cross_price"><?= format_amount($item['cross_price'], false)?></div>
                        <?}?>
                        <div class='base_price'><?= format_amount($item['customer_price'], false)?></div>
                    <?php
                    } elseif ($GLOBALS['shop']['cross_price_typ'] != 1) {
                        echo get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                    } else {
                        echo "<div class='base_price'>";
                        echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false) . "";
                        echo "</div>";
                    } ?>
                </div>
                <div class="itemlist_content rating">
                    <? show_item_rating($item, true); ?>
                </div>
            </a>
            <div class="itemlist3_extended itemlist_extended">
                <a href="<?= $itemlink ?>">
                <div class="itemlist_content image">
                    <img src="<?= $imagelink_hover ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                </div>
            </a>
            <?php
            if(isset($GLOBALS['IOC'])) {
                $IOCContainer = $GLOBALS['IOC'];
            }
            $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
            $itemObj = $itemBuilder->getFirstActiveVariant($item["id"], '');
            $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

            ?>
            <? if (!($no_of_vars > 0)) { ?>
                <div class="basket">
                    <?
                    $shopConfig = $IOCContainer->create('$CurrShopConfig');
                    $customer = $shopConfig->getCustomer();
                    $currencyCode = $shopConfig->getCurrencyCode();
                    $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
                    $defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);

                    $subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

                    $validator = new \DynCom\dc\common\classes\Validator([], []);
                    $formBuilder = new FormBuilder('dummyID');
                    $templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

                    $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
                    $vatMgr = $IOCContainer->create('$VATManager');
                    $builder = new ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating,$vatMgr);
                    $htmlString = $builder->getItemlistButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,$GLOBALS["input_counter"],true);
                    //$htmlString = $builder->getItemcardButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,true);

                    echo '<div class="itemlist_content">' . $htmlString . '</div>';
?>
                </div>
            <? } else { ?>
                <div class="itemlist_content">
                    <?= str_replace('%no_of_vars%',$no_of_vars,$GLOBALS["tc"]["no_of_variants"]) ?>
                </div>
            <? } ?>
                <a href="<?= $itemlink ?>">
                    <?
                    switch ($item["vat_prod_posting_group"]) {
                        case $GLOBALS["shop"]["vat_identifier_1"]:
                            $suffix = '_vat1';
                            break;
                        case $GLOBALS["shop"]["vat_identifier_2"]:
                            $suffix = '_vat2';
                            break;
                        case $GLOBALS["shop"]["vat_identifier_3"]:
                            $suffix = '_vat3';
                            break;
                        default:
                            $suffix = '_vat1';
                            break;
                    }
                    ?>
                    <div class="itemlist_content vat_info">
                    <?= $GLOBALS["tc"]["item_vat_and_shipping_notice_plus_ship" . $suffix] ?>
                    </div>
                    <div class="itemlist_content">
                    <? if ($no_of_vars > 0) { get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item); } ?>
                    </div>
                    <div class="price_per_gramm"><?= get_gram_price($item) ?></div>
                </a>
            </div>
        </div>
    </div>
<?
}

// Artikelliste 4: Warenkorb

function show_item_list_4( $item, $image, $formname ) {

    $maxItemQuantity = $item['max_qty'];
    $minItemQuantity = $item['min_qty'];

	$pass = $GLOBALS['shop_setup']['shop_password'];
	$pass = pad_string_to_mb_length($pass,16);

	$key = $pass;
    //$encryptedItemKey = base64_encode(Crypto::Encrypt($item['item_key'],$key));
    $encryptedItemKey = base64_encode($item['item_key']);

    if(isset($GLOBALS['IOC'])) {
        $IOCContainer = $GLOBALS['IOC'];
        if ($IOCContainer instanceof \Dice\Dice) {
            $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
            if ($itemBuilder instanceof WebshopItemBuilder) {
                $itemObj = $itemBuilder->getWebshopItemByID($item["id"]);
                $maxItemQuantity = $itemObj->getMaxQty();
                $minItemQuantity = $itemObj->getMinQty();
            }
        }
    }

    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    $qty_protected = (isset($item['qty_protected']) && $item['qty_protected']);
    $qty_source_id = (isset($item['qty_source_id'])) ? $item['qty_source_id'] : 0;
    $unit_price_protected = (isset($item['qty_protected']) && $item['qty_protected']);
    $unit_price_protected_id = (isset($item['price_source_id'])) ? $item['price_source_id'] : 0;
    $itemKey = $item['item_key'];
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $availableGiftItems = false;
    if(!$item['is_gift_package'])
        {
             if ($item['allow_gift_package'] == 1) {
            $anz = $item['no_of_gift_wrappings'];
            $giftItems = get_gift_items();
            if(count($giftItems) > 0){
                $availableGiftItems = true;
            }
            if ($anz < $item['basket_quantity'] && $availableGiftItems) {
                $allow_packing = true;
            } else {
                $allow_packing = false;
            }
        }

    }

    if($item['is_greeting_card'] == 1)        {
        $availableGiftItems = false;
        $allow_packing = false;
           $greetingCardItem = true;
    }



    $addtext = "";
    if ($item['package_for_item'] != "" && $item['package_for_item'] != "0") {
        $itemquery  = "SELECT item_no FROM shop_item WHERE id = '" . $item['package_for_item'] . "'";
        $itemresult = mysqli_query($GLOBALS['mysql_con'], $itemquery);
        $pack_item  = mysqli_fetch_assoc($itemresult);
        $addtext    = "(" . $pack_item['item_no'] . ")";
    }
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell image_line text-center">
            <a href="<?= $itemlink ?>">
                <div class="image">
                    <img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                </div>
            </a>
        </div>
        <div class="table_cell description_line">
            <div class="brand"><?= get_brand_name($item) ?></div>
            <div class="description">
                <a href="<?= $itemlink ?>">
                        <?= $item["description"] . $addtext ?>
                        <? if ($item['changed_to_vpe'] == 1) { ?>
                            <br /><?= $GLOBALS['tc']['changed_to_vpe'] ?>
                        <? } elseif ($item['changed_to_minimum'] == 1) { ?>
                            <br /><?= $GLOBALS['tc']['changed_to_minimum'] ?>
                        <? } ?>
                </a>
            </div>
            <?
            if(!empty($item['notifications'])) {
                if(!empty($item['notifications']['creation'])) {
                    ?>
                    <div class="creation_info">
                    <?= $item['notifications']['creation'] ?>
                    </div>
                    <?php
                }
                if(!empty($item['notifications']['discounts'])) {
                    ?>
                    <div class="discount_info">
                    <?php
                    foreach($item['notifications']['discounts'] as $discountInfo) {
                        ?>
                        <div class="discount_info_element"><?php echo $GLOBALS['tc']['discount'] . ': <span class=\'discount_info_element_value\'>' . $discountInfo['value'] . '</span> (' . $discountInfo['text'] . ')'; ?></div>
                        <?php
                    }?>
                    </div>
                    <?php
                }
            }

             if($item['is_gift_package'] || item['is_greeting_card']) {

                  if ($GLOBALS['shop']['variant_typ'] == '2') {
                         $action = "?action=shop_remove_item_from_basket_by_key&item_key=".$encryptedItemKey."&var_code=".$item['var_code'];
                    }
                    else {
                          $action = "?action=shop_remove_item_from_basket_by_key&item_key=".$encryptedItemKey;
                    }

            }
            else{

                 if ($GLOBALS['shop']['variant_typ'] == '2') {
                         $action = "?action=shop_remove_item_from_basket&action_id=".$item["id"]."&var_code=".$item['var_code'];
                    }
                    else {
                          $action = "?action=shop_remove_item_from_basket&action_id=".$item["id"];
                    }

            }


            ?>
            <div class="summary_basket"><?= $item["summary"] ?> <?= show_special_shipment($item) ?></div>
<?
			//MH 06.04.2016

			if($item['customization_hash'] !== ''){
				$cust_query = "SELECT * FROM shop_user_basket_customize 
							WHERE customization_hash = '" . $item['customization_hash'] . "'";
				$cust_result = mysqli_query($GLOBALS['mysql_con'], $cust_query);
				if(mysqli_num_rows($cust_result)>0){
					echo '<div class="basket_customization_area"><div class="row"><div class="col-xs-10 col-sm-6 col-lg-7">';
					while($cust_res = mysqli_fetch_array($cust_result)){
						echo('<div class="customization_area_line_wrapper ">');
						switch($cust_res["field_type"]){
							case 0: $cust_element = "text"; break;
							case 1: $cust_element = "file";$cust_res["field_length"]=50; break;
							case 2: $cust_element = "textarea"; break;
							default: $cust_element = "text"; break;
						}

						if($cust_element == "file"){
								if ($cust_res["value"]){
									echo '<div class="basket_customization_text">' . $cust_res["field_name"] . ': <b><span  class="fa fa-check" title=""</span></b></div>';
								}
								else {
									echo '<div class="basket_customization_text">' . $cust_res["field_name"] . ': <b><span  class="fa fa-times" title=""</span></b></div>';
								}

						}else{
							echo '<div class="basket_customization_text">' . $cust_res["field_name"] . ": <b>" . $cust_res["value"] . "</b></div>";

						}

						echo('</div>');		//close div customization_area_line_wrapper
					}
					echo('<hl /></div><div class="customizable_icon col-xs-1 col-sm-6 col-lg-5"><a href="?action=shop_individualize_basket&action_id='.$item['customization_hash'].'"><span class="fa fa-cog fa-fw"></span><span class="hidden-xs">'. $GLOBALS["tc"]["change_customization"] .'</span></a></div>');
					echo '</div></div>';	//close div basket_customization_area, row
				}
			}
			?>
            <?php
            if($item["allow_gift_package"] == 1 AND $allow_packing == true){ ?>
               <div class="basket_gift_package">
				<a href= "?action=gift_package&item_key=<?= urlencode($encryptedItemKey) ?>"><span class="fa fa-gift">&nbsp;</span>
					   <?= $GLOBALS['tc']['add_gift_wrapping'] ?> (<?= number_format ($anz) ?>/<?= number_format ($item['basket_quantity'])?>)</a>
		    </div>
            <?} ?>
            <?if ( $item["allow_gift_package"] == 1 AND $allow_packing == false AND $availableGiftItems) {?>
			<div>
				<?= $GLOBALS['tc']['info_gift_wrapping'] ?> (<?= number_format ($anz) ?>/<?= number_format ($item['basket_quantity'])?>)
		    </div>
			<? }?>

			 <?if($greetingCardItem) { ?>
			  <div class="basket_gift_package">
                <a href="?action=greeting_card_text&action_event=change_greeting_card_text&item_key=<?= urlencode($encryptedItemKey) ?>"><span class="fa fa-file-text">&nbsp;</span>
					   <?= $GLOBALS['tc']['edit_greeting_card'] ?> </a>
			  </div>
            <?php } ?>

            <?if(!$qty_protected) { ?>
                <a href="<?= $action ?>" class="basket_delete"><?= $GLOBALS['tc']['remove_article'] ?></a>
            <?php } ?>
        </div>
        <?
        if ($GLOBALS['shop']['variant_typ'] == '2') {?>
            <div class="table_cell variant_line">
                <div class="itemtable_list_label"><?=$GLOBALS['tc']['variant']?></div>
                <?= $item["var_code"] ?>
            </div>
        <?}
        ?>
        <div class="table_cell price_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['price']?></div>
            <?php if($item['cross_price'] > 0) { ?>
            <div class="cross_price">
                <?= format_amount($item['cross_price'], false) ?>
            </div>
            <?}?>
            <div class="base_price">
                <?=format_amount($item["customer_price"], false) ?>
            </div>
        </div>
        <?
        if ($qty_protected || $item['is_coupon_item'] == 1 || $item['is_gift_package'] == 1) {
            $readonly = " readonly='readonly'";
        } else {
            $readonly = "";
        }
        ?>
        <div class="table_cell quantity_line text-center">
                <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
                <input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden" value="<?= $item["id"] ?>" />
                <input name="input_variant_code_<?= $GLOBALS["input_counter"] ?>"
                       id="input_variant_code_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                       value="<?= $item["var_code"] ?>" />

                <?php
                if(true) {
                    $max = '';
                    $min = '';
                    if ($maxItemQuantity && $minItemQuantity && !$readonly) {
                        $max = ' max="' . $maxItemQuantity . '"';
                        $min = ' min="' . $minItemQuantity . '"';
                    }
                ?>
                   <input class="input_quantity_line" value="<?= round($item["basket_quantity"]) ?>"
                       name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>" type="number"
                       tabindex="<?= tabcounter() ?>" <?= $readonly ?> maxlength="3" <?=$max?> <?=$min?> />
                <?php
                }
                ?>

                <input type="hidden" name ="input_item_key_<?= $GLOBALS['input_counter']?>" value="<?= htmlentities($itemKey) ?>"/>
                <span class="update-wrapper"><a onclick="$('#<?= $formname ?>').submit(); return false;" href="javascript:void(0);"
               class="button_refresh"><i class="fa fa-refresh fa-2x" aria-hidden="true"></i></a></span>
        </div>
        <div class="table_cell line_amount_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <div class="order_price"><?= format_amount($item["line_amount"], false) ?></div>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_4_header($formname) {
    ?>
    <form name="<?= $formname ?>" id="<?= $formname ?>" action="?action=shop_refresh_user_basket" method="POST">
        <div class="itemtable_list itemtable_list4 table_area">
            <div class="itemtable_row table_row table_header">
                <div class="table_cell image_line text-center"></div>
                <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
                <?if ($GLOBALS['shop']['variant_typ'] == '2') {?>
                    <div class="table_cell variant_line"><?= $GLOBALS["tc"]["variant"] ?></div>
                <?}?>
                <div class="table_cell price_line text-right"><?= $GLOBALS["tc"]["price"] ?></div>
                <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
                <div class="table_cell line_amount_line text-right"><?= $GLOBALS["tc"]["line_amount"] ?></div>
            </div>
<?
}

// Artikelliste 5: Bestellung

function show_item_list_5( $item, $image, $formname ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell image_line text-center">
              <div class="image">
                   <img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
              </div>
        </div>
        <? /*<div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["item_no"] ?>
        </div> */ ?>
        <div class="table_cell description_line">
            <div class="brand"><?= get_brand_name($item) ?></div>
            <div class="description"><?= $item["description"] ?></div><br />
            <?= $item["summary"] ?><br><?php
            if($item['customization_hash'] !== ''){

                $cust_query = "SELECT * FROM shop_user_basket_customize 
                                        WHERE customization_hash = '" . $item['customization_hash'] . "'";
                $cust_result = mysqli_query($GLOBALS['mysql_con'], $cust_query);
                if(mysqli_num_rows($cust_result)>0){
                    echo '</br><small><b>'.$GLOBALS["tc"]["customization"]. '</b>';
                    while($cust_res = mysqli_fetch_array($cust_result)){
                        switch($cust_res["field_type"]){
                            case 0: $cust_element = "text"; break;
                            case 1: $cust_element = "file";$cust_res["field_length"]=50; break;
                            case 2: $cust_element = "textarea"; break;
                            default: $cust_element = "text"; break;
                        }
                        if($cust_element == "file"){
                            if ($cust_res["value"]){
                                echo '</br>' . $cust_res["field_name"] . ': <b><span  class="fa fa-check" title=""</span></b>';
                            }
                            else {
                                echo '</br>' . $cust_res["field_name"] . ': <b><span  class="fa fa-times" title=""</span></b>';
                            }
                        }else{
                            echo '</br>' . $cust_res["field_name"] . ": <b>" . $cust_res["value"] . "</b>";

                        }
                    }
                    echo '</small>';
                }
            } ?>
        </div>
        <?if ($GLOBALS['shop']['variant_typ'] == '2') {?>
            <div class="table_cell variant_line">
                <div class="itemtable_list_label"><?=$GLOBALS['tc']['variant']?></div>
                <?= $item["var_code"] ?>
                </div>
        <?}?>
        <div class="table_cell price_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['your_price']?></div>
            <?= format_amount($item["customer_price"], false) ?>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= round($item["basket_quantity"]) ?>
            </div>
        <div class="table_cell line_amount_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <div class="order_price"><?= format_amount($item["basket_quantity"] * $item["customer_price"], false) ?></div>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_5_header($formname) {
    ?>
    <div class="itemtable_list itemtable_list5 table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell image_line text-center"></div>
            <? /*<div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div> */ ?>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <?if ($GLOBALS['shop']['variant_typ'] == '2') {?>
                <div class="table_cell variant_line"><?= $GLOBALS["tc"]["variant"] ?></div>
            <?}?>
            <div class="table_cell price_line text-right"><?= $GLOBALS["tc"]["your_price"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell line_amount_line text-right"><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </div>
<?
}

// Artikelliste 6: Bestell-Email
function show_item_list_6( $item, $image, $formname ) {
    ?>
    <tr class="item_tr">
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;">
            <?= $item["item_no"] ?>
        </td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;">
            <?= $item["description"] ?>
            <br /><?= $item["summary"] ?>
        </td>
        <?
        if ($GLOBALS['shop']['variant_typ'] == '2') {
            echo("<td width='90' align = 'left'  style='border-top:1px solid ".$GLOBALS['shop_setup']['mail_color'].";'>" . $item["var_code"] . "</td>");
        }
        ?>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90" align="right">
            <?= format_amount($item["customer_price"], false) ?>
        </td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="40" align="right">
            <?= round($item["basket_quantity"]) ?>
        </td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90" align="right">
            <?= format_amount($item["basket_quantity"] * $item["customer_price"], false) ?>
        </td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_6_header($formname) {
    ?>
    <table id="order_mail_itemlist" border="0" cellpadding="8" cellspacing="0" width="100%">
    <tr id="order_mail_itemlist_header">
        <td>
            <strong><?= $GLOBALS["tc"]["item_no"] ?></strong>
        </td>
        <td>
            <strong><?= $GLOBALS["tc"]["description"] ?></strong>
        </td>
        <?
        if ($GLOBALS['shop']['variant_typ'] == '2') {
            echo("<td width='90' align = 'left'>" . $GLOBALS["tc"]["variant"] . "</td>");
        }
        ?>
        <td width="90" align="right">
            <strong><?= $GLOBALS["tc"]["your_price"] ?></strong>
        </td>
        <td width="40" align="right">
            <strong><?= $GLOBALS["tc"]["quantity"] ?></strong>
        </td>
        <td width="90" align="right">
            <strong><?= $GLOBALS["tc"]["line_amount"] ?></strong>
        </td>
    </tr>
<?
}

// Artikelliste 7: Geschenkverpackungen

function show_item_list_7( $item, $image, $category_path ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $columns_class = "col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xlg-3";

    ?>
    <div class="itemlist2 itemlist <?=$columns_class?>">
        <div class="row">
            <div class="itemlist_content itemlist_image itemlist2_image" onclick="window.location.href='<?= $itemlink ?>'">
                <div class="itemlist2_image_layer1"><img src="<?= $imagelink ?>" border="0"
                                                         alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" ></div>
                <? show_item_promotion_banners($item); ?>
            </div>
            <div class="itemlist_content itemlist_description itemlist2_description" style="cursor:default;">
                <?= $item["description"] ?> <?= $item["summary"] ?>
            </div>
             <div class="itemlist_content itemlist_price itemlist2_price" style="cursor:default;">
                <?echo format_amount($item['base_price'], false); ?>
            </div>
            <div class="itemlist_content itemlist_basket itemlist2_basket">
                <a class="button comment_button button_action" href="?action=shop_add_gift_wrapping&item_key=<?= $_GET["item_key"] ?>&wrapping_item=<?= $item["id"] ?>"><?= $GLOBALS["tc"]["package_now"] ?></a>
            </div>
        </div>

    </div>


<?
}




// Artikel-Bemerkungen
function show_item_list_8( $item, $formname ) {
    if ($item["name"] == "") {
        $item["name"] = "Anonym";
    }
    if ($item["city"] != "") {
        $item["city"] = " ".$GLOBALS["tc"]["from"]." " . $item["city"];
    }

    ?>
    <div class="user_rating" itemscope itemtype="http://schema.org/Review">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 user_rating_left">
                <?show_item_rating($item, false, true, false);?>
                <div class="user_rating_name"><?=$GLOBALS['tc']['from_name'];?> <?= $item["name"] ?><?= $item["city"] ?>&nbsp;(<span itemprop="datePublished" content="<?= date("c", strtotime($item["creation_date"])) ?>"><?= datefromsql($item["creation_date"]) ?></span>)</div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-8 col-lg-9 user_rating_right">
                <div class="user_rating_headline"><?= $item["header"] ?></div>
                <div class="user_rating_comment"><?= $item["comment"] ?></div>
            </div>
        </div>
    </div>
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
                                              title="<?= $image['description'] ?>"       alt="<?= $image['description'] ?>"></div>
            <? show_item_promotion_banners($item); ?>
        </div>
        <div class="itemlist2_description" onclick="window.location.href='<?= $itemlink ?>'">
            <strong><?= $item["description"] ?></strong><br /><?= $item["summary"] ?></div>
        <div class="itemlist2_item_no"
             onclick="window.location.href='<?= $itemlink ?>'"><?= get_item_no_or_no_of_variants($item) ?></div>
        <div class="itemlist2_price" style="text-align:left; width:144px;"
             onclick="window.location.href='<?= $itemlink ?>'">
            <?
            if(isset($item['customer_price'])) {
                if(isset($item['cross_price']) && $item['cross_price'] > 0) {?>
                <div class="itemlist1_cross_price"><?= format_amount($item['cross_price'], false)?></div>
                    <?php
                }?>
				<div class="itemlist1_base_price"><?= format_amount($item['customer_price'], false)?></div>
					  <?php
            } elseif ($GLOBALS['shop']['cross_price_typ'] != 0) {
                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
            } else {
                echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false);
            }
            ?>
        </div>
    </div>
<?
}


//Artikelvorschau ohne Warenkorb und Favoriten
function show_item_list_11( $item, $image ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);

	$query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.filename <> '".$image["filename"]."'
			  	AND shop_item_file.company = '".$GLOBALS['shop']['company']."'
			  	AND shop_item_file.shop_code = '".$GLOBALS['shop']['item_source']."'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
			  ORDER BY line_no LIMIT 1";
	#print_r($query);
	$result = @mysqli_query($GLOBALS["mysql_con"], $query);
	if(@mysqli_num_rows($result) > 0) {
		while($hover_image = @mysqli_fetch_array($result)) {
				$imagelink_hover = $GLOBALS['shop_setup']['image_config'][3]["path"] . "/" . $hover_image["filename"];
		}
	}else{
		$imagelink_hover = $imagelink;
	}
    ?>
    <div class="itemlist11 itemlist">
        <a href="<?=$itemlink?>">
            <div class="itemlist_container">
                    <div class="itemlist_content image">
                        <img src="<?= $imagelink ?>" border="0" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                    </div>
                    <div class="itemlist_content brand">
                        <?= get_brand_name($item) ?>
                    </div>
                    <div class="itemlist_content description">
                        <?= $item["description"] ?>
                    </div>
                    <div class="itemlist_content prices">
                         <?if(isset($item['customer_price'])) {
                            if(isset($item['cross_price']) && $item['cross_price'] > 0) {?><div class="cross_price"><?= format_amount($item['cross_price'], false)?></div><?}?>
                            <div class="base_price"><?= format_amount($item['customer_price'], false)?></div>
                         <?} elseif ($GLOBALS['shop']['cross_price_typ'] != 0) {
                                get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                         } else {
                                echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false);
                         }?>
                    </div>
                    <div class="itemlist_content rating">
                        <? show_item_rating($item, true); ?>
                    </div>
            </a>
                <div class="itemlist11_extended itemlist_extended">
                    <a href="<?= $itemlink ?>">
                    <div class="itemlist_content image">
                        <img src="<?= $imagelink_hover ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>">
                    </div>
                    <? if ($item["summary"] != "") {
                    ?>
                    <div class="itemlist_content">
                        <?= $item["summary"] ?>
                    </div>
                    <?
                    }
                    ?>
                </a>
                <?php
                if(isset($GLOBALS['IOC'])) {
                    $IOCContainer = $GLOBALS['IOC'];
                }
                $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
                $itemObj = $itemBuilder->getFirstActiveVariant($item["id"], '');
                $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

                ?>
                <? if (!($no_of_vars > 0)) { ?>
                    <div class="basket">
                        <?
                        $shopConfig = $IOCContainer->create('$CurrShopConfig');
                        $customer = $shopConfig->getCustomer();
                        $currencyCode = $shopConfig->getCurrencyCode();
                        $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
                        $defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj, 1, $customer, $currencyCode);

                        $subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');

                        $validator = new \DynCom\dc\common\classes\Validator([], []);
                        $formBuilder = new FormBuilder('dummyID');
                        $templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');

                        $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);
                        $vatMgr = $IOCContainer->create('$VATManager');
                        $builder = new ItemOrderButtonBuilder($shopConfig, $availabilityProvider, $validator, $formBuilder, $templating,$vatMgr);
                        $htmlString = $builder->getItemlistButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,$GLOBALS["input_counter"],true);
                        //$htmlString = $builder->getItemcardButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,true);

                        echo '<div class="itemlist_content">' . $htmlString . '</div>';
?>
                    </div>
                <? } else { ?>
                    <div class="itemlist_content">
                        <?= str_replace('%no_of_vars%',$no_of_vars,$GLOBALS["tc"]["no_of_variants"]) ?>
                    </div>
                <? } ?>
                    <a href="<?= $itemlink ?>">
                        <?
                        switch ($item["vat_prod_posting_group"]) {
                            case $GLOBALS["shop"]["vat_identifier_1"]:
                                $suffix = '_vat1';
                                break;
                            case $GLOBALS["shop"]["vat_identifier_2"]:
                                $suffix = '_vat2';
                                break;
                            case $GLOBALS["shop"]["vat_identifier_3"]:
                                $suffix = '_vat3';
                                break;
                            default:
                                $suffix = '_vat1';
                                break;
                        }
                        ?>
                        <div class="itemlist_content vat_info">
                        <?= $GLOBALS["tc"]["item_vat_and_shipping_notice_plus_ship" . $suffix] ?>
                        </div>
                        <div class="itemlist_content">
                        <? if ($no_of_vars == 0) { get_favorite_sign(($parent_item["id"] <> '') ? $parent_item : $item); } ?>
                        </div>
                        <div class="price_per_gramm"><?= get_gram_price($item) ?></div>
                    </a>
                </div>
                <div class="clearfloat"></div>
            </div>
    </div>

<?
}

//Warenkorb Hover

// Artikelliste 4: Warenkorb

function show_item_list_12( $item, $image, $formname ) {

    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist12 itemlist">
        <a href="<?=$itemlink?>">
            <div class="itemlist_container">
                <div class="itemlist_content image">
                    <img src="<?= $imagelink ?>" alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" >
                </div>
                <div class="itemlist_content brand">
                    <?= get_brand_name($item) ?>
                </div>
                <div class="itemlist_content description">
                    <?= $item["description"] ?>
                </div>
                <div class="itemlist_content prices">
                     <?if(isset($item['customer_price'])) {
                        if(isset($item['cross_price']) && $item['cross_price'] > 0) {?><div class="cross_price"><?= format_amount($item['cross_price'], false)?></div><?}?>
                        <div class="base_price"><?= format_amount($item['customer_price'], false)?></div>
                     <?} elseif ($GLOBALS['shop']['cross_price_typ'] != 0) {
                            get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']);
                     } else {
                            echo format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false);
                     }?>
                </div>
            </div>
        </a>
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
                $descr    = ($item["variant_type"] == "") ? $item[""] : $item["variant_type"];
                if (isset($_GET["shop_category"]) && !empty($_GET["shop_category"])) {
                    $itemlink =  "-p" . $item["id"]."/";
                } else {
                    $itemlink = "-p" . $item["id"]."/";
                }
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
            echo "<a href=\"" . ml($sitepart, "action", "shop_add_item_to_basket", "action_id", $item["id"]) . "\"><img onmouseover=\"TagToTip('basket_add1')\" onmouseout=\"UnTip()\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/basket_add1.gif\" border=\"0\" /></a>\n";
        }
    }
}

function show_category_list( $result, $language ) {

    if(is_a('mysqli_result',$result)) {
        $allTraversible = mysqli_fetch_all($result,MYSQLI_ASSOC);
    } else {
        $allTraversible = &$result;
    }

    $showCategoryDescription =  $GLOBALS['shop_setup']['show_category_description_in_category_list'];
    foreach ($allTraversible as $category) {
        $categoryPath = category_get_path($category);
        $dcOrder = "";
        if ($category["line_no"] == $GLOBALS["shop_language"]["dc_category_line_no"]) {
            $dcOrder = "/".customizeUrl()."/dc_order/";
        }
        $categorylink = $categoryPath . $dcOrder;
        $image        = get_category_icon($category, $language);
        ?>
		<div class="categorylist col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xlg-3">
		    <a href="<?= $categorylink ?>">
		        <div class="categorylist_container">
                    <div class="categorylist_content image_wrapper">
                        <div class="image">
                            <?= $image ?>
                        </div>
                    </div>
                    <div class="categorylist_content description">
                        <div class="categorylist_content_inner">
                            <?= $category["name"] ?>
                        </div>
                    </div>
                    <? if($showCategoryDescription)
                        {
                            ?>
                              <div class="categorylist_content excerpt">
                                   <?= get_category_description_excerpt($category["id"]); ?>
                               </div>
                            <?
                        }
                    ?>
                </div>
            </a>
		</div>
		<?
    }
}
/*
function category_get_path( $current_category, $language ) {
    $query            = "SELECT *
			  FROM shop_category
			  WHERE language_code = '" . $language["code"] . "'
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND id = '" . $current_category . "'";
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
*/



function show_item_list_with_pages( $type, $query_, $category, $vendor_sql_string = "", $max_number = 0, $order_by = "", $show_all_items = false, $is_Search = false, $search_query = "", $columns = 4 ) {

    echo "<div class='itembox_header row'>";
        if (($category != "campain" && $category['user_sorting'] == 1) || $is_Search) {
            if ($category != "campain") {
                $query_order_by = get_sort_type($order_by);
                $order_by_val = $query_order_by ? " ORDER BY $query_order_by " : '';
            }
            if ($show_all_items) {
                echo "<div class='sort_by col-xs-12 col-sm-6 col-md-4 col-lg-3'>";
                echo "<form name=\"item_order\" method=\"post\">";
                echo create_sort_select("", "sort_by", $order_by);
                echo "</form>";
                echo "</div>";
            }
        } elseif ($category['sort_items'] >= 0) {
            $query_order_by = get_sort_Type($order_by);
            $order_by_val = $query_order_by ? " ORDER BY $query_order_by " : '';
        }
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        if (!$is_Search) {
            if ($category != "campain") {
                $cat_id       = $category["id"];
                $categoryPath =  category_get_path($category) . "?";
            } else {
                 $categoryPath = "/".customizeUrl()."/campain/?shop_campain=" . $_REQUEST['shop_campain'] . "&";
                $order_by_val = " ORDER BY shop_view_active_item.item_no ";
            }
        } else {
            $categoryPath = "?input_search=" . $_REQUEST["input_search"] . "&";
            if ($order_by == '') {
                $order_by = 'relevance';
            } else {
                $query_order_by = get_sort_Type($order_by);
                $order_by_val = $query_order_by ? " ORDER BY $query_order_by " : '';

            }
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
        $query = $query_;
        $limit = "LIMIT " . $from . ", " . $reach . "";
        //$query_limit = $query.$order_by_val.$limit;
        if ($GLOBALS["sim_result"] || $_POST["sim_result"]) {
            if ($order_by == 'relevance') {
                $query_limit = $query . $limit;
                $query_limit = str_replace("shop_view_active_item", "shop_item", $query_limit);
            } else {
                $query       = preg_replace('/ORDER BY[^\?]*/', '', $query);
                $query_limit = $query . $order_by_val . $limit;
                $query_limit = str_replace("shop_view_active_item", "shop_item", $query_limit);
            }
        } else {
            $pattern     = "/ORDER BY.*/";
            $query       = preg_replace($pattern, '', $query);
            $query_limit = $query . $order_by_val . $limit;
        }

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
        echo "<div class='col-xs-12 col-sm-6 col-md-4 col-md-offset-4 col-lg-3 col-lg-offset-6'>";
        get_page_switch($rows,$page,$categoryPath,$order_by);
        echo "</div>";
        if ($is_Search) {
            $list_type = 2;
        }
    echo "</div>";

    show_item_list_from_query($query_limit,$list_type,false,$columns);
    get_page_switch($rows,$page,$categoryPath,$order_by);
}

function print_pagination($no_of_results,$category_path,$order_by) {
    $page = (int) $_GET['page'];
    $is_Search = (bool)strstr($category_path,'search');
    if($page <= 1) {
        $page = 1;
    }
    if($no_of_results > $GLOBALS['shop_setup']['num_items_per_page']){
        $is_search = false;
        if(strpos($category_path,'search') !== false) {
            $is_search = true;
        }

        $no_of_pages = ceil($no_of_results / $GLOBALS['shop_setup']['num_items_per_page']);

        $remainder = $no_of_results % $GLOBALS['shop_setup']['num_items_per_page'];
        if($remainder != 0){
            $number_of_subrows = intval($remainder / $GLOBALS['shop_setup']['num_items_per_page'])+1;
        } else {
            $number_of_subrows = intval($remainder / $GLOBALS['shop_setup']['num_items_per_page']);
        }

        if(($no_of_pages > 1) ||(($no_of_pages <= 1) && ($remainder != 0))){

            if($page != 1){
                $output = $page-1;
                $step_back_link = "/" . customizeUrl() . "/" .$category_path . "&page=".$output."&sort_by=".$order_by;
                $step_back_output = '<a href="'.$step_back_link.'" rel="prev">< '.$GLOBALS["tc"]["back"].'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            } else {
                $step_back_output = "";
            }

            if($page != $no_of_pages){
                $output = $page+1;
                $step_forward_link = "/" . customizeUrl() . "/" .$category_path . "&page=".$output."&sort_by=".$order_by;
                $step_forward_output = '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$step_forward_link.'" rel="next">'.$GLOBALS["tc"]["next_step"].'></a>';
            } else {
                $step_forward_output = "";
            }

            echo "<div class=\"page_switch\">";
            echo $step_back_output;

            if(!$is_Search)
            {
                for($i = 1; $i <= $no_of_pages;$i++){
                    if($i >= 2){
                        //$pipe = "|&nbsp;";
                        $pipe = "";
                    } else {
                        $pipe = "";
                    }
                    if($page==$i){
                        $output = "<strong>".$i."</strong>";
                        $item_link_output = $output;
                    } else {
                        $output = $i;
                        $itemlink = "/" . customizeUrl() . "/" .$category_path . "&page=".$output."&sort_by=".$order_by;
                        $item_link_output = "<a href=\"".$itemlink."\">".$output."</a>";
                    }
                    echo $pipe.$item_link_output."&nbsp;";
                }
            } else {
                if($no_of_pages >4)
                {
                    $pages = 4;
                }
                else
                {
                    $pages=$no_of_pages;
                }
                for($i = 1; $i <= $pages;$i++){
                    if($i >= 2){
                        $pipe = "";
                    } else {
                        $pipe = "";
                    }
                    if($page==$i){
                        $output = "<strong>".$i."</strong>";
                        $item_link_output = $output;
                    } else {
                        $output = $i;
                        $itemlink = "/" . customizeUrl() . "/" .$category_path . "&page=".$output."&sort_by=".$order_by;
                        $item_link_output = "<a href=\"".$itemlink."\">".$output."</a>";
                    }
                    echo $pipe.$item_link_output."&nbsp;";
                }
            }

            echo $step_forward_output;
            echo "</div>";

        }
    }
}


function print_itemlist2_entry($item) {
        $imagelink = $item['final_imagelink'];
        $itemlink  = $item['final_itemlink'];
        $unit_price = $item['unit_price'];
        $cross_price = $item['cross_price'];
        $no_of_vars = get_item_no_of_variants($item);
    //var_dump($item);
        ?>
    <div class="itemlist2_spacer">
        <div class="itemlist2">
            <div class="itemlist2_banner">
            <?= show_food_type($item) ?>
            <? show_item_promotion_banners($item); ?>
            </div>
            <a class="image" href="<?= $itemlink ?>">
                <img src="<?= $imagelink ?>" border="0" alt="<?= $item['description'] ?>"  title="<?= $item['description'] ?>" >
            </a>
            <a class="description" href="<?= $itemlink ?>"><?= $item['manufacturer'] ?>
                <br /><?= $item["description"] ?></a>
            <a class="summary" href="<?= $itemlink ?>"><?= $item["summary"] ?></a>
            <a class="rating" href="<?= $itemlink ?>"><? show_item_rating($item, true); ?></a>
            <?
            if($item['cross_price'] > 0) {
                ?>
                <a class="cross_price" href="<?= $itemlink ?>"><?= format_amount($item['cross_price'], false) ?></a>
                <?
            }
                ?>

            <a class="price" href="<?= $itemlink ?>"><?= format_amount($item['unit_price']->unitPrice, false) ?></a>

            <div class="itemlist2_extended">
                <a class="inventory" href="<?= $itemlink ?>"><?= get_inventory_info($item) ?></a>
                <a class="details" href="<?= $itemlink ?>"><?= $GLOBALS["tc"]["details"] ?></a>
                <? //echo $category['sort_items']; ?>
        <?php
                if(isset($GLOBALS['IOC'])) {
                    $IOCContainer = $GLOBALS['IOC'];
                }
                $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
                $itemObj = $itemBuilder->getFirstActiveVariant($item["id"], '');
                $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');

                ?>
                <? if (!($no_of_vars > 0) && $availabilityProvider->isItemOrderable($itemObj)) { ?>
            <div class="basket">
                    <? show_basket_button("?action=shop_add_item_to_basket_list&action_id=" . $item['id'], $item['id']); ?>
            </div>
        <? } else { ?>
            <div class="no_of_variants_info"><?= $no_of_vars . $GLOBALS['tc']['variants'] ?></div>
        <? } ?>
        <?= $GLOBALS["tc"]["value_" . $item['vat_prod_posting_group']] ?><br /><?= get_gram_price($item) ?>

            </div>
        </div>
    </div>
<?

}


function show_item_list_from_query($query,$listNo,$formname = '',$columns = 4) {
    if ($GLOBALS["shop_setup"]["num_items_per_page"] > 0) {
        if (is_array($query)) {
            show_item_list($query,$listNo,$formname,$columns);
        }
        if(isset($GLOBALS['IOC'])) {
            $IOCContainer = $GLOBALS['IOC'];
            if ($IOCContainer instanceof \Dice\Dice) {
                $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
                if ($itemBuilder instanceof WebshopItemBuilder) {
                    //$objArr = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($itemBuilder->buildWebshopItemsFromItemQuery($query));

                    $item_result = [];
                                   /** @var \DynCom\dc\dcShop\classes\WebshopItem $itemObj */
                    foreach ($itemBuilder->buildWebshopItemsFromItemQuery($query) as $itemObjectPre) {
                        $itemObj = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray([$itemObjectPre])[0];

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
                        $item['customizable'] = $itemObj->getCustomizationStatus();
                        $item['line_discounts'] = $itemObj->getAppliedLineDiscounts();
                        $item['price_data'] = $itemObj->getPriceData();
                        $item['vat_prod_posting_group'] = $itemObj->getVATProdPostingGroup();
                        $item['item_slug'] = $itemObj->getItemSlug();
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
            if (isset($_REQUEST["sort_by"]) && $_REQUEST["sort_by"] === 'customer_price' && array_key_exists('customer_price',$item_result[0])) {
                sort_array_by_customer_price($item_result);
            }
            show_item_list($item_result,$listNo,$formname,$columns);
        }
    }
}
function get_page_switch($rows,$page,$categoryPath,$order_by) {
    if ($rows > $GLOBALS['shop_setup']['num_items_per_page'] && $GLOBALS['shop_setup']['num_items_per_page'] > 0) {
        $rest = $rows % $GLOBALS['shop_setup']['num_items_per_page'];
        if ($rest != 0) {
            $number_of_subrows = intval($rows / $GLOBALS['shop_setup']['num_items_per_page']) + 1;
        } else {
            $number_of_subrows = intval($rows / $GLOBALS['shop_setup']['num_items_per_page']);
        }
        if (($number_of_subrows > 1) || (($number_of_subrows <= 1) && ($rest != 0))) {
            $pages = $number_of_subrows;
            //Generieren der "weiter" bzw. "zurück" Links
            if ($page != 1) {
                $output           = $page - 1;
                $step_back_link   =  $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $step_back_output = '<a class="page_switch_control page_switch_prev" href="' . $step_back_link . '" rel="prev" title="'.$GLOBALS["tc"]["back"].'"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
            } else {
                $step_back_output = "";
            }
            if ($page != $pages) {
                $output              = $page + 1;
                $step_forward_link   =  $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $step_forward_output = '<a class="page_switch_control page_switch_next" href="' . $step_forward_link . '" rel="next" title="'.$GLOBALS["tc"]["next_step"].'"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
            } else {
                $step_forward_output = "";
            }
            //Ende

            //Erstellen der Seitenauswahl
            echo "<div class=\"page_switch\">";
            echo $step_back_output;
            echo "<span class='page_switch_text'>".$GLOBALS['tc']['pagination_page'] . " " . $page . " " .$GLOBALS['tc']['pagination_of'] . " " .$pages . "</span>";
            echo $step_forward_output;
            echo "</div>";
            //Ende
        }
    }
}

function get_page_switch_pagination($rows,$page,$is_Search,$categoryPath,$order_by) {
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
                $step_back_link   = "/" . customizeUrl() . "/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                $step_back_output = '<a class="page_switch_prev" href="' . $step_back_link . '" rel="prev" title="'.$GLOBALS["tc"]["back"].'">' . $GLOBALS["tc"]["back_step"] . '</a>';
            } else {
                $step_back_output = "";
            }
            if (!$is_Search) {
                if ($page != $number_of_subrows) {
                    $output              = $page + 1;
                    $step_forward_link   = "/" . customizeUrl() . "/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                    $step_forward_output = '<a class="page_switch_next" href="' . $step_forward_link . '" rel="next" title="'.$GLOBALS["tc"]["next_step"].'">' . $GLOBALS["tc"]["next_step"] . '</a>';
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
                    $step_forward_link   = "/" . customizeUrl() . "/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                    $step_forward_output = '<a class="page_switch_next" href="' . $step_forward_link . '" rel="next" title="'.$GLOBALS["tc"]["next_step"].'">' . $GLOBALS["tc"]["next_step"] . '</a>';
                } else {
                    $step_forward_output = "";
                }
            }
            //Ende

            //Erstellen der Seitenauswahl
            echo "<div class=\"page_switch col-xs-12 col-sm-6\">";
            echo $step_back_output;
            if (!$is_Search) {
                for ($i = 1; $i <= $number_of_subrows; $i++) {
                    if ($i >= 2) {
                        //$pipe = "|&nbsp;";
                        $pipe = "";
                    } else {
                        $pipe = "";
                    }
                    if ($page == $i) {
                        $output           = "<strong>" . $i . "</strong>";
                        $item_link_output = $output;
                    } else {
                        $output           = $i;
                        $itemlink         = "/" . customizeUrl() . "/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                        $item_link_output = "<a href=\"" . $itemlink . "\">" . $output . "</a>";
                    }
                    echo $pipe . $item_link_output . "&nbsp;";
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
                        $output           = "<strong>" . $i . "</strong>";
                        $item_link_output = $output;
                    } else {
                        $output           = $i;
                        $itemlink         = "/" . customizeUrl() . "/" . $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
                        $item_link_output = "<a href=\"" . $itemlink . "\">" . $output . "</a>";
                    }
                    echo $pipe . $item_link_output . "&nbsp;";
                }
            }
            echo $step_forward_output;
            echo "</div>";
            //Ende
        }
    }
}




function show_category_list_etsy( $result, $language ) {

    if(is_a('mysqli_result',$result)) {
        $allTraversible = mysqli_fetch_all($result,MYSQLI_ASSOC);
    } else {
        $allTraversible = &$result;
    }

    $shopquery = "SELECT * FROM shop_shop WHERE company = '" . $GLOBALS['language']['company'] . "' AND CODE = '" . $GLOBALS['language']['shop_code'] . "'";
	$shopresult = @mysqli_query($GLOBALS['mysql_con'], $shopquery);
    if (@mysqli_num_rows($shopresult) == 1) {
		$shop                                 = mysqli_fetch_assoc($shopresult);
        $shop['use_items_from_shop_code']     = (empty($shop['use_items_from_shop_code']) ? $shop['code'] : $shop['use_items_from_shop_code']);
        $shop['use_categorys_from_shop_code'] = (empty($shop['use_categorys_from_shop_code']) ? $shop['code'] : $shop['use_categorys_from_shop_code']);
		$item_no_snippet = '';

	}

	foreach ($allTraversible as $category) {

		$category_codes_query_snippet = ' IN (';
		$category_codes_query_snippet .= '\'' . $category['code'] . '\'';
		$category_codes_query_snippet .= ') ';

        $categoryPath = category_get_path($category);
        $categorylink = "/" . customizeUrl() . "/" . $categoryPath;
        $categoryImage        = get_category_icon($category, $language);
        ?>
		<?

		$dc_order_cat = 'gutscheine-per-mail';
		$dc_order_cat_2 = 'gutschein-per-mail';
		$dc_order_cat_3 = 'email-gift-tokens';
		if ($category['code'] == $dc_order_cat || $category['code'] == $dc_order_cat_2 || $category['code'] == $dc_order_cat_3 ) {
			 $categorylink = "/" . customizeUrl() . "/dc_order/";

		}


                $line_no_query       = "
					SELECT 
						CONCAT(' IN (',GROUP_CONCAT(line_no SEPARATOR ','),')') 
					FROM 
						`shop_category` 
					WHERE 
						`shop_code` = '" . $shop['use_categorys_from_shop_code'] . "' 
					  AND 
						`language_code` = '" . $GLOBALS['language']['shop_language_code'] . "' 
						AND 
						`company` = '" . $GLOBALS['language']['company'] . "' 
					  AND 
						`code` $category_codes_query_snippet				 
				";
                $line_no_result      = @mysqli_query($GLOBALS['mysql_con'], $line_no_query);
                $line_no_snippet_pre =   @mysqli_result($line_no_result, 0);
                $line_no_snippet     = ((strlen($line_no_snippet_pre) > 0) ? ' AND sihc.category_line_no ' . $line_no_snippet_pre . ' ' : '');

			$limit_no = 3;
            $limit_no_snippet = (($limit_no > 0) ? ' LIMIT ' . $limit_no . ' ' : ' LIMIT 12 ');
		$item_query  = "
			SELECT svai.*
			FROM 
				shop_view_active_item svai,
				shop_item_has_category sihc 
			WHERE (
					sihc.item_no = svai.item_no
                  AND 
				    svai.parent_item_no = ''
				  AND
					svai.company = sihc.company
				  AND
					svai.shop_code = sihc.shop_code
				  AND
					svai.language_code = sihc.language_code
				  AND
					sihc.shop_code = '" . $shop['use_items_from_shop_code'] . "'
				  AND
					sihc.language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
				  AND
					sihc.company = '" . $GLOBALS['language']['company'] . "'
				  AND
					sihc.category_language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
					
				  $line_no_snippet
				) $item_no_snippet 
			ORDER BY RAND()
			$limit_no_snippet
			";
		      $item_result = @mysqli_query($GLOBALS['mysql_con'], $item_query);

		     $category_items_count_query  = "
			SELECT count(*) as 'numer_of_items'
			FROM 
				shop_view_active_item svai,
				shop_item_has_category sihc 
			WHERE (
					sihc.item_no = svai.item_no
				  AND 
				    svai.parent_item_no = ''
				  AND 
					svai.company = sihc.company
				  AND
					svai.shop_code = sihc.shop_code
				  AND
					svai.language_code = sihc.language_code
				  AND
					sihc.shop_code = '" . $shop['use_items_from_shop_code'] . "'
				  AND
					sihc.language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
					AND
					sihc.company = '" . $GLOBALS['language']['company'] . "'
				  AND
					sihc.category_shop_code = '" . $shop['use_categorys_from_shop_code'] . "'
				  AND
					sihc.category_language_code = '" . $GLOBALS['language']['shop_language_code'] . "'
				  ".$line_no_snippet."
				) ".$item_no_snippet ."
			";
		          $categoryItemsCount = @mysqli_query($GLOBALS['mysql_con'], $category_items_count_query);
		          $numberofItems = mysqli_fetch_all($categoryItemsCount,MYSQLI_ASSOC);

                $items = mysqli_fetch_all($item_result,MYSQLI_ASSOC);
		?>
		<div class="categorylist col-xs-6 col-sm-4 col-md-6 col-lg-4">
		    <div class="categorylist_container">
                <a class="categorylink" href="<?= $categorylink ?>" title="<?=$category['name']?>">
                    <div class="categorylist_content description">
                          <div class="categorylist_content_inner">
                                <?= $category["name"] ?>
                          </div>
                    </div>
                </a>
                <div class="categorylist_big_image">
                    <? if(count($items) > 0)
                    {

                        $itemlink  = create_item_link($items[0]);
                        $itemDescription = $items[0]['description'];
                        $price = get_item_base_price($items[0], $GLOBALS['shop_currency']['code']);
                        $price = format_amount($price, false);

                        if(empty($items[0]['image_data'])) {
                        //$item['retail_price'] = get_item_retail_price($item,$GLOBALS['shop_currency']['code']);
                        $imageItem = get_item_main_image($items[0], get_item_variant_parent($items[0]));
                        } else {
                             $imageItem = $items[0]['image_data'][0];
                        }

                       if ($imageItem["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $imageItem['filename'])) {
                        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                            $imageItem["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                        } else {
                            $imageItem["filename"] = "noimage.jpg";
                        }
                        }

                            $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $imageItem["filename"];
                        ?>
                            <a href="<?=$itemlink?>">
                                <div class="categorylist_content image_wrapper">
                                    <div class="image">
                                        <img src="<?= $imagelink ?>" border="0" alt="<?= $imageItem['description'] ?>"  title="<?= $imageItem['description'] ?>"  >
                                    </div>
                                </div>
                                <div class="categorylist_content categorylist_details">
                                    <div class="categorylist_details_row">
                                        <div class="categorylist_details_cell description">
                                            <?= $itemDescription ?>
                                        </div>
                                        <div class="categorylist_details_cell price text-right">
                                            <div class="base_price"><?= $price ?></div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                   <? }else { ?>
                            <a href="<?=$categorylink?>">
                                <div class="categorylist_content image_wrapper">
                                    <div class="image">
                                        <?=$categoryImage?>
                                    </div>
                                </div>
                                <div class="categorylist_content categorylist_details">
                                    <div class="categorylist_details_row">
                                        <div class="categorylist_details_cell description">
                                            &nbsp;
                                        </div>
                                        <div class="categorylist_details_cell price text-right">
                                            <div class="base_price">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                  <? } ?>
                </div>
                <div class="categorylist_content categorylist_images_outer">
                    <div class="categorylist_images">
                        <div class="categorylist_images_inner">
                            <? if(count($items) > 0){
                                for ($i=1; $i<count($items); $i++) {
                                        if($i == 3) {
                                                break;
                                        }

                                        $itemlink  = create_item_link($items[$i]);
                                        $itemDescription = $items[$i]['description'];
                                        $price = get_item_base_price($items[$i], $GLOBALS['shop_currency']['code']);
                                        $price = format_amount($price, false);

                                         if(empty($items[$i]['image_data'])) {
                                            $imageItem = get_item_main_image($items[$i], get_item_variant_parent($items[$i]));
                                         } else {
                                            $imageItem = $items[$i]['image_data'][0];
                                         }

                                     if ($imageItem["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $imageItem['filename'])) {
                                        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                                            $imageItem["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                                        } else {
                                            $imageItem["filename"] = "noimage.jpg";
                                        }

                                        $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $imageItem["filename"];?>
                                        <a href="<?=$itemlink?>" class="categorylist_images_item">
                                            <div class="categorylist_images_item_inner">
                                                <div class="image">
                                                        <img src="<?= $imagelink ?>" alt="<?= $imageItem['description'] ?>" title="<?= $imageItem['description'] ?>" >
                                                </div>
                                            </div>
                                        </a>
                                    <?
                                     }
                                }
                            ?>
                                    <a href="<?= $categorylink ?>" title="<?=$category['name']?>" class="categorylist_images_item categorylist_images_item_counter">
                                        <div class="categorylist_images_item_inner">
                                            +<?= $numberofItems[0]['numer_of_items']?>
                                        </div>
                                    </a>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?
    }
}
// Artikelliste 13: Greeting Card

function show_item_list_13( $item, $image, $category_path ) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    $columns_class = "col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xlg-3";

    $IOCContainer = $GLOBALS['IOC'];
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $ordableItem = $itemBuilder->getWebshopItemOrderableEntityByID($item['id']);
    //$userBascket = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
     static $basket;
     $itemKey = '';
    $basket = $IOCContainer->resolve('$CurrUserBasket');
    if($basket instanceof \DynCom\dc\dcShop\interfaces\UserBasket){
            $itemKey = $basket->getKey($ordableItem);
      }

    $pass = $GLOBALS['shop_setup']['shop_password'];
	$pass = pad_string_to_mb_length($pass,16);
    $encryptedItemId = base64_encode($item['id']);
    $encryptedItemKey = base64_encode($itemKey);


    ?>
    <div class="itemlist2 itemlist <?=$columns_class?>">
        <div class="row">
            <div class="itemlist_content itemlist_image itemlist2_image" onclick="window.location.href='<?= $itemlink ?>'">
                <div class="itemlist2_image_layer1"><img src="<?= $imagelink ?>" border="0"
                                                         alt="<?= $image['description'] ?>" title="<?= $image['description'] ?>" ></div>
                <? show_item_promotion_banners($item); ?>
            </div>
            <div class="itemlist_content itemlist_description itemlist2_description" style="cursor:default;">
                <?= $item["description"] ?> <?= $item["summary"] ?>
            </div>
             <div class="itemlist_content itemlist_price itemlist2_price" style="cursor:default;">
                <?echo format_amount($item['base_price'], false); ?>
            </div>
            <div class="itemlist_content itemlist_basket itemlist2_basket">
                <a class="button comment_button button_action" href="?action=greeting_card_text&action_event=add_greeting_card_text&action_id=<?=$encryptedItemId?>&item_key=<?= $encryptedItemKey ?>"><?= $GLOBALS["tc"]["add_to_basket"] ?></a>
            </div>
        </div>

    </div>

<?
}




function show_item_list_14_header($formname) {
    ?>
    <div class="itemtable_list itemtable_list5 table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell image_line text-center"></div>
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["coupon_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["coupon_message"] ?></div>
            <div class="table_cell price_line text-right"><?= $GLOBALS["tc"]["your_price"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell line_amount_line text-right"><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </div>
<?
}

// Artikelliste 14: Bestellung Coupon

function show_item_list_14( $coupon, $image, $formname ) {

            $image = $coupon['image_data'];

            if ($image == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["dc_image_config"][1]["path"] . "/" . $image)) {
                $image = "noimage.jpg";
            }
             $imagelink = $GLOBALS["shop_setup"]["dc_image_config"][1]["path"] . "/" . $image;

    ?>
    <div id="item_<?= $coupon["coupon_code"] ?>" class="itemtable_row table_row">

        <div class="table_cell image_line text-center">
              <div class="image">
                   <img src="<?= $imagelink ?>" border="0" >
              </div>
        </div>

        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['coupon_no']?></div>
              <?= $coupon["coupon_code"] ?>
        </div>

     <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['coupon_message']?></div>
               <strong><?= $coupon["message"] ?></strong><br />
        </div>


        <div class="table_cell price_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['your_price']?></div>
            <?= format_amount($coupon["total"], false) ?>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= round(1) ?>
            </div>
        <div class="table_cell line_amount_line text-right">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <?= format_amount($coupon["total"], false) ?>
        </div>
    </div>
    <?
}
