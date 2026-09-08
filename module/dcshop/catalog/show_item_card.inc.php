<?
use DynCom\dc\common\classes\Hook;

$query = "SELECT * FROM shop_view_active_item WHERE id = '" . $_GET["card"]."'";

$result = @mysqli_query($GLOBALS['mysql_con'], $query);

if (@mysqli_num_rows($result) == 1) {
    $item = @mysqli_fetch_array($result);
    $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
    $item['retail_price'] = get_item_retail_price($item, $GLOBALS['shop_currency']['code']);
    $parent_item = get_item_variant_parent($item);
    $variant_item = get_item_first_variant($item);
    if ($variant_item["id"] <> '') {
        $hasVariants = true;
        $parent_item = $item;
        if ($GLOBALS["shop"]["variant_typ"] == '0') {
            $item = $variant_item;
            $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
            $item['retail_price'] = get_item_retail_price($item, $GLOBALS['shop_currency']['code']);
        }
    } else {
        $hasVariants = false;
    }
    ini_set("display_errors", 1);

    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $id = (int)$_GET['card'];
    $varCode = filter_var($_GET['variant'], FILTER_SANITIZE_STRING);
    if ($varCode) {
        $parentItemObj = $itemBuilder->getWebshopItemByPrimary($parent_item["company"], $parent_item["shop_code"], $parent_item["language_code"], $parent_item["item_no"]);
        $itemObj = $itemBuilder->getWebshopItemByIDVarID($id, $varCode);
    } else {
        if ($hasVariants) {
            $parentItemObj = $itemBuilder->getWebshopItemByPrimary($parent_item["company"], $parent_item["shop_code"], $parent_item["language_code"], $parent_item["item_no"]);
            $itemObj = $itemBuilder->getFirstVariant($id, $varCode);
        } else {
            $parentItemObj = $itemBuilder->getWebshopItemByPrimary($parent_item["company"], $parent_item["shop_code"], $parent_item["language_code"], $parent_item["item_no"]);
            $itemObj = $itemBuilder->getFirstVariant($id, $varCode);
        }
    }

    $eventName = 'onShowItemCard';
    $eventData = ['item_obj' => $itemObj];
    Hook::update($eventName, $eventData);


    $attributequeryForHeader    = "SELECT shop_attribute_link.*,shop_attribute.description AS 'headline',shop_attribute.data_type,shop_attribute.display_type,shop_attribute.navision_value
					  FROM shop_attribute_link
					  INNER JOIN shop_attribute ON shop_attribute.code = shop_attribute_link.attribute_code
					  WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.show_on_card = 1
					  	AND shop_attribute.show_in_header = 1
					  	AND shop_attribute_link.no IN ('" . $item['item_no'] . "')
						AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['item_source'] . "'
						AND shop_attribute_link.language_code='" . $GLOBALS['shop_language']['code'] . "'
						GROUP by attribute_code, value_option
					  ORDER BY attribute_code";


    $attributeresultHeader    = mysqli_query($GLOBALS['mysql_con'], $attributequeryForHeader);


    ?>

    <div id="itemcard_top" itemscope itemtype="http://schema.org/product">
        <div class="row">
            <div id="itemcard_left" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xlg-3-5">
                <? show_item_images($item, $parent_item, $GLOBALS['shop_setup']['image_config']); ?>
            </div>
            <div id="itemcard_right" class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xlg-2-5">
                <div class="shop_site_headline itemcard_infoblock">

                      <span itemprop="name">
                            <? echo stripslashes($item["description"]);?>
                        </span>
                </div>
                <?
                if ($item["summary"] != '') {
                    echo "<div class='itemcard_summary itemcard_infoblock'>" . $item["summary"] . "</div>";
                }

                createDescription(
                    1,
                    $item["item_no"],
                    $item["language_code"],
                    $parent_item["item_no"],
                    $parent_item["language_code"],
                    1
                );
                createDescription(
                    1,
                    $item["item_no"],
                    $item["language_code"],
                    $parent_item["item_no"],
                    $parent_item["language_code"],
                    2
                );


                show_item_videos($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_videos"]);
                if ($hasVariants) {

                    $currShopConfig = $IOCContainer->create('$CurrShopConfig');
                    $variantService = $IOCContainer->create('\DynCom\dc\dcShop\classes\WebshopItemVariantService');
                    $PDOQueryWrapper = $IOCContainer->create('\DynCom\dc\common\classes\PDOQueryWrapper');
                    $variants = $variantService->getAllVariantsByPrimary($parent_item['company'], $parent_item['shop_code'], $parent_item['language_code'], $parent_item['item_no']);
                    if (count($variants) == 1) {
                        echo ' <div class="itemcard_order_button_vat_ship_notice_wrapper">
                          <a href="#itemcard_variants">' . $GLOBALS["tc"]["item_has_variant"] . '</a>
                        </div> ';
                    } else {
                        echo ' <div class="itemcard_order_button_vat_ship_notice_wrapper">
                          <a href="#itemcard_variants">' . str_replace('%value%', count($variants), $GLOBALS["tc"]["item_has_variants"]) . '</a>
                     </div> ';
                    }

                }

                $attributeHeader_num_rows = mysqli_num_rows($attributeresultHeader);
                if($attributeHeader_num_rows > 0 )
                {
                    echo "<div class='itemcard_summary itemcard_infoblock'>" . show_attribute_info($attributeresultHeader) . "</div>";
                }

                include __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card_details.inc.php';

                ?>
                <div class="item_card_sharing">
                    <? //require_once __DIR__ . DIRECTORY_SEPARATOR . 'share_facebook.php'; ?>
                    <? //require_once __DIR__ . DIRECTORY_SEPARATOR . 'recommend_item.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="itemcard_bottom">
        <?php


        if ($hasVariants) {


            if (2 == $GLOBALS['shop']['variant_typ']) {
                $decoratedItems = [];
                foreach ($variants as $variant) {
                    $decoratedItems[] = new \DynCom\dc\dcShop\classes\WebshopItemNAVVariantDecorator($parent_item['item_obj'], $variant);
                }
            } else {
                $decoratedItems = $variants;
            }
            $list = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($decoratedItems);
            $variantHTML = [];
            /** @var \DynCom\dc\dcShop\classes\WebshopItemImagesDecorator $decoratedParentItem */
            $decoratedParentItem = new \DynCom\dc\dcShop\classes\WebshopItemImagesDecorator($parentItemObj, $variantService, $PDOQueryWrapper);
            $decoratedParentItemImage = $decoratedParentItem->getImageData()[0];
            foreach ($list as $locVariantItem) {
                $defaultPrice = $advancedPriceProvider->getItemCustomerPrice(
                    $locVariantItem,
                    1,
                    $customer,
                    $currencyCode
                );
                $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($locVariantItem);
                $builder = new \DynCom\dc\dcShop\classes\ItemOrderButtonBuilder(
                    $shopConfig,
                    $availabilityProvider,
                    $validator,
                    $formBuilder,
                    $templating
                );
                //$orderButton = $builder->getItemcardButtonHTML($locVariantItem, $defaultPrice, $subscriptionData, true);
                /**
                 * @var $item \DynCom\dc\dcShop\interfaces\WebshopItemWithImages
                 */

                $variantImage["filename"] = $locVariantItem->getImageData()[1]['filename'];
                if ($variantImage["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $variantImage['filename'])) {
                    if ($decoratedParentItemImage["filename"] != '' && file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $decoratedParentItemImage['filename'])) {
                        $variantImage["filename"] = $decoratedParentItemImage["filename"];
                    } else {
                        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                            $variantImage["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                        } else {
                            $variantImage["filename"] = "noimage.jpg";
                        }
                    }
                }

                $variantHTML[] =
                    '
    <div class="is_variant itemlist">
                            <div class="itemlist_container">
                                <div class="itemlist_left">
                                    <div class="itemlist_image_container">
                                        <div class="itemlist_content image">
                                            <img src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $variantImage["filename"] . '" alt="" border="0">
                                        </div>
                                    </div>
                                    <div class="itemlist_details_container">
                                        <div class="itemlist_content info">
                                            <div class="variant_name">' . $locVariantItem->getDescription() . '</div>
                                            <div class="variant_type">' . $locVariantItem->getVariantType() . '</div>
                                        </div>
                                        <div class="itemlist_content summary">
                                            ' . $locVariantItem->getSummary() . '
                                        </div>
                                    </div>
                                </div>
                              <!--  <div class="itemlist_right">
                                   
                                </div> -->
                            </div>
                        </div>              
';
            }
            $variantsHTML = implode(PHP_EOL, $variantHTML);

            $strWrapper = <<<EOS
<div id='itemcard_variants'>
            <div class='site_headline'>
                <h2>{$GLOBALS['tc']['variants']}</h2>
            </div>
            <div class="itemcard_list1 itemcard_list">
                <div class="itemlist_variant_container">
                    <div class="itemlist_variant_container_inner active">
                        $variantsHTML                                      
                    </div>
                </div>
            </div>
        </div>
EOS;
            echo $strWrapper;

        }

        ?>
        <script language="javascript">
            var expectedHash = "";
            var currentLayer = "tab_content1";
            var currentTab = "tab1";
        </script>

        <?

        $parentItemNoQuery = "";
        if (!empty($parent_item['item_no'])) {
            //$parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
        }

        // Erstellen der Tabs für Beschreibungen, Zubehör und Ersatzteile
        $description_query = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                          LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
                          INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
                          LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
                          WHERE shop_item_description.show_in_header = 0
                            AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
                            AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                            AND ((shop_item_description.item_no = '" . $item['item_no'] . "'
                                AND (shop_item_description.all_language_codes = TRUE 
                                    OR shop_item_description.language_code = '" . $GLOBALS['shop_language']['code'] . "')
                            )
                            OR	(shop_item_description.item_no = '" . $parent_item['item_no'] . "'
                                AND (shop_item_description.all_language_codes = TRUE 
                                    OR shop_item_description.language_code = '" . $GLOBALS['shop_language']['code'] . "')
                            ))
                            ORDER BY shop_item_description.line_no ASC
                            ";
        $acc_item_query = "SELECT DISTINCT
                                shop_view_active_item.*
                            FROM
                                shop_item_link
                            LEFT JOIN shop_permissions_group_link ON (shop_permissions_group_link.item_no = shop_view_active_item.item_no
                                                                    AND shop_permissions_group_link.company = shop_view_active_item.company)	
                            INNER JOIN
                                shop_view_active_item
                                ON (
                                    shop_view_active_item.item_no = shop_item_link.linked_item_no
                                  AND
                                    shop_view_active_item.item_no != '" . $item['item_no'] . "'
                                  AND
                                    shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
                                  AND
                                    shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                  AND
                                    shop_view_active_item.language_code = '" . $item["language_code"] . "'
                                )
                            WHERE
                                shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
                              AND
                                shop_item_link.type = 1
                            " . get_permissions_group_customer() . "
                            ORDER BY RAND()
                           LIMIT 6";
        $spare_part_query = "SELECT DISTINCT
                                shop_view_active_item.*
                            FROM
                                shop_item_link
                            INNER JOIN
                                shop_view_active_item
                                ON (
                                    shop_view_active_item.item_no = shop_item_link.linked_item_no
                                  AND
                                    shop_view_active_item.item_no != '" . $item['item_no'] . "'
                                  AND
                                    shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
                                  AND
                                    shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                  AND
                                    shop_view_active_item.language_code = '" . $item["language_code"] . "'
                                )							
                            LEFT JOIN shop_permissions_group_link ON (shop_permissions_group_link.item_no = shop_view_active_item.item_no
                                                                    AND shop_permissions_group_link.company = shop_view_active_item.company)	
                            
                            WHERE
                                shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
                              AND
                                shop_item_link.type = 2
                            " . get_permissions_group_customer() . "
                            ORDER BY RAND()
                           LIMIT 6";
        $attributequery = "SELECT shop_attribute_link.*,shop_attribute.description AS 'headline',shop_attribute.data_type,shop_attribute.display_type,shop_attribute.navision_value
                          FROM shop_attribute_link
                          INNER JOIN shop_attribute ON shop_attribute.code = shop_attribute_link.attribute_code
                          WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
                            AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
                            AND shop_attribute.show_on_card = 1
                             AND shop_attribute.show_in_header = 0
                            AND shop_attribute_link.no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
                            AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['item_source'] . "'
                            AND shop_attribute_link.language_code='" . $GLOBALS['shop_language']['code'] . "'
                            GROUP by attribute_code, value_option
                          ORDER BY attribute_code";

        $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
        $description_num_rows = @mysqli_num_rows($description_result);
        $acc_item_result = @mysqli_query($GLOBALS['mysql_con'], $acc_item_query);
        $acc_item_num_rows = @mysqli_num_rows($acc_item_result);
        $spare_part_result = @mysqli_query($GLOBALS['mysql_con'], $spare_part_query);
        $spare_part_num_rows = @mysqli_num_rows($spare_part_result);
        $attributeresult = mysqli_query($GLOBALS['mysql_con'], $attributequery);
        $attribute_num_rows = @mysqli_num_rows($attributeresult);
        $tab_count = 1;
        $result = get_item_documents($item, $parent_item);
        $number_of_documents = @mysqli_num_rows($result);

        if ((@mysqli_num_rows($description_result) + @mysqli_num_rows($acc_item_result) + @mysqli_num_rows(
                    $spare_part_result + $number_of_documents
                )) > 0
        ) {
            echo "<div class='tabs'>";
            echo "<ul class=\"tab\">";


            // Edit The footer Description Here
            if ($description_num_rows > 0) {
                while ($description = @mysqli_fetch_array($description_result)) {
                    $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";

                    if ($description["description"] != '') {
                        echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $description["description"] . "</a></li>";
                    } else {
                        echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $description["shop_text_module_description"] . "</a></li>";
                    }

                    $tab_count++;
                }
            }


            if ($acc_item_num_rows > 0) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
                echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["accessories"] . "</a></li>";
                $tab_count++;
            }
            if ($spare_part_num_rows > 0) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
                echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["spare_parts"] . "</a></li>";
                $tab_count++;
            }
            if ($attribute_num_rows > 0) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
                echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["features"] . "</a></li>";
                $tab_count++;
            }
            if ($number_of_documents > 0) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
                echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["downloads"] . "</a></li>";
                $tab_count++;
            }

            echo "</ul>\n";


            $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
            $tab_count = 1;


            // Edit the Footer Content Here
            if ($description_num_rows > 0) {
                while ($description = @mysqli_fetch_array($description_result)) {
                    $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";

                    $itemDescription = $description['description'];
                    if ($itemDescription == '') {
                        $itemDescription = $description['shop_text_module_description'];
                    }

                    if ($description['shop_text_module_code'] == '') {
                        echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\"><div class='tab_content_headline'>" . $itemDescription . "</div>" . $description["content"] . "\n</div>";
                    } else {
                        echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\"><div class='tab_content_headline'>" . $itemDescription . "</div>" . $description["shop_text_module_content"] . "\n</div>";
                    }

                    //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                    $tab_count++;
                }
            }


            if ($acc_item_num_rows > 0) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
                echo "<div class='tab_content_headline'>" . $GLOBALS["tc"]["accessories"] . "</div>";
                show_item_list_from_query($acc_item_query, 1, 'form_itemlist_acc_items');
                echo "</div>\n";
                echo "<div class=\"clearfloat\"></div>\n";
                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }
            if ($spare_part_num_rows > 0) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
                echo "<div class='tab_content_headline'>" . $GLOBALS["tc"]["spare_parts"] . "</div>";
                show_item_list_from_query($spare_part_query, 1, 'form_itemlist_spare_parts');
                echo "</div>\n";
                echo "<div class=\"clearfloat\"></div>\n";
                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }
            if ($attribute_num_rows > 0) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
                echo "<div class='tab_content_headline'>" . $GLOBALS["tc"]["features"] . "</div>";
                show_attribute_info($attributeresult);
                echo "</div>\n";
                echo "<div class=\"clearfloat\"></div>\n";
                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }

            if ($number_of_documents > 0) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
                echo "<div class='tab_content_headline'>" . $GLOBALS["tc"]["downloads"] . "</div>";
                show_item_documents($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_documents"]);
                echo "</div>\n";
                echo "<div class=\"clearfloat\"></div>\n";
                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }
            echo "</div>";
        }


        ?>

    </div>
    <? //show_item_fit_item($item, $parent_item); ?>
    <?// show_item_alt_item($item, $parent_item); ?>
    <?
}

// Bluestar HT: Funktion zur Anzeige der Artikelbeschreibung
function createDescription($limit, $item_no, $language_code, $parent_item_no, $parent_item_language_code, $showInHeaderValue = 1)
{
    $description_query = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                              LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
							  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
							  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
							  WHERE shop_item_description.show_in_header = " . $showInHeaderValue . "
							  	AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
							  	AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							  	AND ((shop_item_description.item_no = '" . $item_no . "'
							  		AND (shop_item_description.all_language_codes = TRUE 
						  				OR shop_item_description.language_code = '" . $GLOBALS['shop_language']['code'] . "')
							  	)
							  	OR	(shop_item_description.item_no = '" . $parent_item_no . "'
							  		AND (shop_item_description.all_language_codes = TRUE 
						  				OR shop_item_description.language_code = '" . $GLOBALS['shop_language']['code'] . "')
							  	))
							  	ORDER BY FIND_IN_SET(shop_item_description.item_no,'" . $item_no . "," . $parent_item_no . "'),line_no";
    $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
    if (@mysqli_num_rows($description_result) > 0) {
        while ($description = @mysqli_fetch_array($description_result)) {

            // Edit the header Contet Here

            $itemDescription = $description['description'];
            if ($itemDescription == '') {
                $itemDescription = $description['shop_text_module_description'];
            }

            $content = $description["content"];
            if ($description["shop_text_module_code"] != '') {
                $content = $description["shop_text_module_content"];
            }

            if ($showInHeaderValue == 1) {
                echo "<div class='itemcard_description itemcard_infoblock'>" . $content . "</div>";
            } elseif ($showInHeaderValue == 2) {
                echo "
                    <div class=\"tabs\">
                                  <button type=\"button\" id=\"showItemContentButton\" name=\"showItemContentButton\" class=\"button_save button button_action\" data-toggle=\"modal\" data-target=\"#itemContentModal\">    " . $itemDescription . "  </button>
                    </div>";

                echo "   <div class=\"modal fade\" id=\"itemContentModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"itemContentModal\">
                                    <div class=\"modal-dialog modal-md\" role=\"document\">
                                        <div class=\"modal-content\">
                                            <div class=\"modal-header\">
                                                <button type=\"button\" id='closeModelHeaderButton' class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                                                <h4 class=\"shop_site_headline\" id=\"myModalLabel\" style='margin-bottom: auto'>" . $itemDescription . "</h4>
                                            </div>
                                            <div class=\"modal-body\"> " . $content . "
    
                                           </div>
                                        </div>
                                    </div>
                      </div>
                            ";
            }

        }
    }
}

// Funktion zum Anzeigen von Alternativartikeln auf der Artikelkarte
function show_item_alt_item($item, $parent_item)
{
    $query = "SELECT DISTINCT
							shop_view_active_item.*
						FROM
							shop_item_link
						
						INNER JOIN
							shop_view_active_item
							ON (
								shop_view_active_item.item_no = shop_item_link.linked_item_no
							  AND
								shop_view_active_item.item_no != '" . $item['item_no'] . "'
							  AND
								shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
							  AND
								shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							  AND
								shop_view_active_item.language_code = '" . $item["language_code"] . "'
							)							
							
						LEFT JOIN shop_permissions_group_link ON (shop_permissions_group_link.item_no = shop_view_active_item.item_no
																AND shop_permissions_group_link.company = shop_view_active_item.company)	
						
						WHERE
							shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
						  AND
							shop_item_link.type = 3
						" . get_permissions_group_customer() . "
						ORDER BY RAND()
					   LIMIT 6";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        echo "<div class=\"alt_items\">";
        echo "<div class=\"infotext alt_item\">" . $GLOBALS["tc"]["related_items"] . "</div>\n";
        show_item_list_from_query($result, 2, FALSE);
        echo "</div>";
    }
}

// Funktion zum Anzeigen von passenden Artikeln auf der Artikelkarte

function show_item_fit_item($item, $parent_item)
{
    $query = "SELECT DISTINCT
					shop_view_active_item.*
				FROM
					shop_item_link
					
				INNER JOIN
					shop_view_active_item
					ON (
						shop_view_active_item.item_no = shop_item_link.linked_item_no
					  AND
						shop_view_active_item.item_no != '" . $item['item_no'] . "'
					  AND
						shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
					  AND
						shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
					  AND
						shop_view_active_item.language_code = '" . $item["language_code"] . "'
					)
					
				LEFT JOIN shop_permissions_group_link ON (shop_permissions_group_link.item_no = shop_view_active_item.item_no
														AND shop_permissions_group_link.company = shop_view_active_item.company)	
				
				WHERE
					shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
				  AND
					shop_item_link.type = 4
				" . get_permissions_group_customer() . "
				ORDER BY RAND()
			   LIMIT 6";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        echo "<div class=\"fit_items\">";
        echo "<div class=\"infotext fit_item\">" . $GLOBALS["tc"]["references"] . "</div>\n";
        echo "<div class=\"itemcard_list3\">\n";
        show_item_list_from_query($result, 2, FALSE);
        echo "</div></div>\n";
    }
}

// Funktion zum Anzeigen der Artikel-Bildergalerie auf der Artikelkarte
function show_item_images($item, $parent_item, $image_config)
{
    $itemQuery = 'shop_item.item_no ';
    $tempItem = $item;
    if ($parent_item) {
        $tempItem = $parent_item;
        $itemQuery = 'shop_item.parent_item_no ';
    }
    $main_image = get_item_main_image($tempItem, $parent_item);

    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((" . $itemQuery . "= '" . $tempItem["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $tempItem["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);

    if (($main_image["filename"] == '') | (!file_exists("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]))) {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            $main_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
        } else {

            $main_image["filename"] = "noimage.jpg";

            if (@mysqli_num_rows($result) > 1) {
                while ($tempImage = @mysqli_fetch_array($result)) {
                    if (($tempImage["filename"] <> '') && (file_exists("../../" . $image_config[3]["path"] . "/" . $tempImage["filename"]))) {
                        $main_image["filename"] = $tempImage["filename"];
                        break;
                    }
                }
            }

        }
    }

    $imagesize = getimagesize("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]);
    $padding = round((($GLOBALS[image_config][3]["maxheight"] - $imagesize[1]) / 2));

    ?>
    <div class="item_images_container">
        <div class="itemcard_banner"><?= show_item_promotion_banners($item); ?></div>
        <div class="item_main_image">
            <div class="item_main_container">
                <a href="<?= $image_config[4]["path"]; ?>/<?= $main_image["filename"]; ?>" class="MagicZoomPlus"
                   id="zoom"
                   data-options="
                   textHoverZoomHint: <?= $GLOBALS['tc']['textHoverZoomHint']; ?>;
                   textClickZoomHint: <?= $GLOBALS['tc']['textClickZoomHint']; ?>;
                   textExpandHint:  <?= $GLOBALS['tc']['textExpandHint']; ?>;
                   textBtnClose:  <?= $GLOBALS['tc']['textBtnClose']; ?>;
                   textBtnNext:  <?= $GLOBALS['tc']['textBtnNext']; ?>;
                   textBtnPrev:  <?= $GLOBALS['tc']['textBtnPrev']; ?>;
                   textTouchZoomHint: <?= $GLOBALS['tc']['textTouchZoomHint']; ?>;
                   textClickZoomHint: <?= $GLOBALS['tc']['textClickZoomHint']; ?>;
                   textExpandHint: <?= $GLOBALS['tc']['textExpandHint']; ?>;
                   zoomPosition: inner;
                   zoomMode: off;
                   hint: off;
                   cssClass: white-bg; ">
                    <img itemprop="image" id="item_main_picture" src="<?= $image_config[3]["path"]; ?>/<?= $main_image["filename"]; ?>"
                         title="<?= $main_image["description"]; ?>"   alt="<?= $main_image["description"]; ?>"/>
                </a>
            </div>
        </div>
        <div class="item_images MagicScroll">
            <?php

            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) > 1) {
                while ($tumb_image = @mysqli_fetch_array($result)) {
                    if (($tumb_image["filename"] <> '') && (file_exists("../../" . $image_config[1]["path"] . "/" . $tumb_image["filename"])) && (file_exists("../../" . $image_config[3]["path"] . "/" . $tumb_image["filename"]))) { ?>
                        <a href="<?= $image_config[4]["path"] . "/" . $tumb_image["filename"] ?>" data-zoom-id="zoom"
                           data-image="<?= $image_config[3]["path"] . "/" . $tumb_image["filename"] ?>">
                            <img src="<?= $image_config[1]["path"] . "/" . $tumb_image["filename"]; ?>"
                                 title="<?= $tumb_image["description"]; ?>"   alt="<?= $tumb_image["description"]; ?>"/>
                        </a>
                        <?
                    }
                }
            } ?>
        </div>
    </div>

    <?
}


// Funktion zum Anzeigen der Artikel-Videogalerie auf der Artikelkarte
function show_item_videos($item, $parent_item, $uploaddir_videos)
{
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '2'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        echo "<div class=\"infotext\">" . $GLOBALS["tc"]["video_clips"] . "</div>\n";
        echo "<div class=\"item_videos\"><ul class=\"buttonlist\">\n";
        while ($video = @mysqli_fetch_array($result)) {
            if (($video["filename"] <> '') && (file_exists("../.." . $uploaddir_videos . $video["filename"]))) {
                echo "<li><a class=\"button_play\" href=\"javascript:void(0);\" onclick=\"window.open('/module/dcshop/webforms/video_popup.php?item=" . $item["id"] . "&video=" . $video["id"] . "','popup','width=600, height=465, scrollbars=no')\">" . $video["description"] . "</a></li>\n";
            }
        }
        echo "</ul></div>\n";
    }
}

// Funktion zum Anzeigen der Artikel-Dokumente auf der Artikelkarte
function show_item_documents($item, $parent_item, $uploaddir_documents)
{
    $result = get_item_documents($item, $parent_item);
    if (@mysqli_num_rows($result) > 0) {
        // echo "<div class=\"infotext\">" . $GLOBALS["tc"]["downloads"] . "</div>\n";
        echo "<div class=\"item_documents\"><ul class=\"buttonlist\">\n";
        while ($document = @mysqli_fetch_array($result)) {
            if (($document["filename"] <> '') && (file_exists("../.." . $uploaddir_documents . $document["filename"]))) {
                $button_class = get_button_file_typ($document["filename"]);
                echo "<li><a class=\"" . $button_class . "\" href=\"/module/dcshop/webforms/download_file.php?file=" . $document["id"] . "\">" . $document["description"] . "</a></li><br>";
            }
        }
        echo "</ul></div>\n";
    }
}

function get_item_documents($item, $parent_item)
{
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '1'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = 1))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = 1)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        return $result;
    }
}

// Funktionen zum anzeigen von Artikelvarianten
function variant_select($item)
{
    $parent_item = get_item_variant_parent($item);
    if ($parent_item["id"] <> '') {
        $query = "
			SELECT shop_view_active_item.*
			FROM shop_view_active_item
			LEFT JOIN shop_item_link ON (shop_item_link.type = 0 AND shop_item_link.item_no = '" . $parent_item["item_no"] . "' AND shop_item_link.linked_item_no=shop_view_active_item.item_no)
			LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  										AND shop_permissions_group_link.company = shop_view_active_item.company
			WHERE
				shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        	  AND
				shop_view_active_item.shop_code= '" . $parent_item["shop_code"] . "'
        	  AND
				shop_view_active_item.language_code = '" . $parent_item["language_code"] . "'
			  AND
				NOT ISNULL(shop_item_link.id)
			   " . get_permissions_group_customer() . "
          	ORDER BY shop_view_active_item.base_price ASC
		";
    } else {
        /*$query = "SELECT DISTINCT shop_view_active_item.*
        		  FROM shop_item_link
        		  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
        		  WHERE shop_item_link.type = '0'
	        		AND shop_item_link.item_no = '" . $item["item_no"] . "'
	        		AND shop_view_active_item.company = '".$GLOBALS['shop']['company']."'
        		  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        		  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
        		  	ORDER BY shop_view_active_item.base_price ASC";*/

        $query = "SELECT shop_view_active_item.*
				FROM shop_view_active_item
				LEFT JOIN shop_item_link ON (shop_item_link.type=0 AND shop_item_link.item_no = '" . $item["item_no"] . "' AND shop_item_link.linked_item_no=shop_view_active_item.item_no)
				LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  										AND shop_permissions_group_link.company = shop_view_active_item.company
				WHERE
					shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  AND
					shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        		  AND 
					shop_view_active_item.language_code = '" . $item["language_code"] . "'
				  AND
					NOT ISNULL(shop_item_link.id)
				  " . get_permissions_group_customer() . "
        		ORDER BY shop_view_active_item.base_price ASC";
    }
    if ($query <> '') {
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) > 0) {
            if ($parent_item == FALSE) {
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                echo "<div id=\"variant_select\" onclick=\"toggle('variant_select_over_" . $item["id"] . "');\">" . $descr . "</div>\n";
            } else {
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                echo "<div id=\"variant_select\" onclick=\"toggle('variant_select_over_" . $item["id"] . "');\">" . $descr . "</div>\n";
            }
            echo "<div id=\"variant_select_over_" . $item["id"] . "\" class=\"variant_select_over\"><table cellspacing=0 cellpadding=0 border=0>\n";

            if ($GLOBALS["shop"]["variant_typ"] != 0) {
                if ($parent_item != "") {
                    $item = $parent_item;
                }
                $descr = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                $itemlink =  "?var=true";
                echo "<tr onclick=\"window.location.href = '" . $itemlink . "'\"><td>" .
                    $descr . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" .
                    $item["item_no"] . "</td><td>";
                get_inventory_sign($item);
                echo "</td></tr>";
            }
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_itemlink = "?var=true";
                $descr = ($variant_item["variant_type"] == "") ? $variant_item["description"] : $variant_item["variant_type"];
                echo "<tr onclick=\"window.location.href = '" . $variant_itemlink . "'\"><td>" .
                    $descr . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" .
                    $variant_item["item_no"] . "</td><td>";
                get_inventory_sign($variant_item);
                echo "</td></tr>";
            }
            echo "</table></div>\n";
        }
    }
}

function variant_select_nav($item)
{
    $var_query = "SELECT *
				  FROM shop_item_variant
				  WHERE item_no = '" . $item['item_no'] . "'
				  AND company = '" . $GLOBALS['shop']['company'] . "'";
    $var_result = mysqli_query($GLOBALS['mysql_con'], $var_query);

    if (mysqli_num_rows($var_result) > 0) {
        ?>
        <select class='select' name='input_variant'
                onchange="location.href='?var=true&variant='+this.options[this.selectedIndex].value;">
            <?
            while ($var_row = mysqli_fetch_assoc($var_result)) {
                if ($_GET['variant'] == "") {
                    $_GET['variant'] = $var_row['id'];
                }
                if ($_GET['variant'] == $var_row['id']) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                //Übersetzung suchen
                $description = get_variant_translation($item['item_no'], $var_row['code']);
                if (strlen($description) < 1) {
                    $description = $var_row['description'];
                }
                echo("<option value=" . $var_row['id'] . $selected . ">" . $description . "</option>");
            }
            ?>
        </select>
        <?
    }
}

// Artikelmerkmale anzeigen
function show_attribute_info($attributeresult)
{
    echo("<table width='100%'>");
    $last_attribute_name = "";
    while ($attribute = mysqli_fetch_assoc($attributeresult)) {
        if ($attribute['headline'] == $last_attribute_name) {
            $attribute['headline'] = '';
        }
        switch ($attribute['data_type']) {
            case 0:
                $field = "value_option";
                break;
            case 1:
                $field = "value_integer";
                break;
            case 2:
                $field = "value_decimal";
                break;
            case 3:
                $field = "value_bool";
                break;
            case 4:
                $field = "value_text";
                break;
        }
        if ($field == "value_bool") {
            echo("<tr><td width='10%'>");
            if ($attribute['headline'] != '') {
                $translation = get_attribute_translation($attribute['attribute_code'], $attribute['type']);
                if (!$translation) {
                    echo $attribute['headline'];
                } else {
                    echo $translation;
                }
            }
            echo("</td>");
        } else {
            echo("<tr><td width='50%'>");
            if ($attribute['headline'] != '') {
                $translation = get_attribute_translation($attribute['attribute_code'], $attribute['type']);
                if (!$translation) {
                    echo $attribute['headline'];
                } else {
                    echo $translation;
                }
            }
            echo("</td>");
        }
        $last_attribute_name = $attribute['headline'];
        if ($attribute['display_type'] != 5) {
            if ($attribute['navision_value'] == 0) {
                if ($attribute['data_type'] == 0) {
                    $option = get_option($attribute['attribute_code'], $attribute['value_option']);
                    $text = get_attribute_value_translation($attribute['attribute_code'], 2, $attribute['value_option']);
                    if (!$text) {
                        if (!$option) {
                            $text = $attribute['value_option'];
                        } else {
                            $text = $option["description"];
                        }
                    }
                } else {
                    $text = $attribute[$field];
                    if ($text == '1' && $field == "value_bool") {
                        $text = $GLOBALS["tc"]["yes"];
                    } elseif ($text == '0' && $field == "value_bool") {
                        $text = $GLOBALS["tc"]["no"];
                    }
                }
            } else {
                switch ($attribute['navision_value']) {
                    case 1:
                        $field = 'width';
                        break;
                    case 2:
                        $field = 'lenght';
                        break;
                    case 3:
                        $field = 'height';
                        break;
                    case 4:
                        $field = 'volume';
                        break;
                    case 5:
                        $field = 'weight';
                        break;
                    case 6:
                        $field = 'retail_price';
                        break;
                    case 7:
                        $field = 'base_price';
                        break;
                    case 8:
                        $field = 'vendor_name';
                        break;
                    case 9:
                        $field = 'inventory';
                        break;
                }
                $text = $GLOBALS['item'][$field];

            }
            echo("<td width='50%'>" . $text . "</td></tr>");

        } else {
            $option = get_option($attribute['attribute_code'], $attribute['value_option']);
            echo("<td width='50%'><img src = '" . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_filter_icon"] . $option['icon'] . "' alt='" . $option['description'] . "'></td></tr>");
        }
    }
    echo("</table>");
}

?>

