<?php

use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;use DynCom\dc\dcShop\interfaces\WebshopItemWithImages;
function show_item_list( $result, $type, $formname = "",$columns = 4 ) {



$query = "SELECT  * 
             FROM main_shop_login
                where main_language_id = " . $GLOBALS["language"]["id"] . " order by id desc  ";

$sitePartResult = @mysqli_query($GLOBALS['mysql_con'], $query);

$siteLanguageUsedLoginSitePart = array();
    while ($sitePart = @mysqli_fetch_assoc($sitePartResult)) {

        $query = "SELECT  * 
                 FROM main_component_link
                    where main_sitepart_id = 13 and main_sitepart_header_id = " . $sitePart['id'] . "  ";

        $sitePartlinkResult = @mysqli_query($GLOBALS['mysql_con'], $query);
        $sitePartLinks = @mysqli_fetch_array($sitePartlinkResult);
        if (count($sitePartLinks) > 0) {
            $siteLanguageUsedLoginSitePart = $sitePart;
            break;
        }
    }

$redirectSiteCode = $siteLanguageUsedLoginSitePart['target_site_code'];

$loginShopUrl = ' https://' . $_SERVER['SERVER_NAME'] . "/" . ltrim($siteLanguageUsedLoginSitePart['target_url'], '/');

$companyCodeValue = $GLOBALS["shop"]["company"];

$query = "SELECT main_language.*
                         FROM main_language 
                            JOIN main_site 
                              ON main_language.id = main_site.std_main_language_id and main_site.code= '" . $redirectSiteCode . "'
                              
                WHERE   main_language.company =  '" . $companyCodeValue . "'     
                         ";

$languageResult = @mysqli_query($GLOBALS['mysql_con'], $query);
$redirectMainLanguage = @mysqli_fetch_array($languageResult);


    if(is_a('mysqli_result',$result)) {
        $allTraversible = mysqli_fetch_all($result,MYSQLI_ASSOC);
    } else {
        $allTraversible = &$result;
    }
    if ($formname == "") {
        $formname = "form_itemlist_" . $GLOBALS["shop_item_listform"];
    }
    $GLOBALS["input_counter"] = 1;
    $input_counter = 1;
    $GLOBALS["shop_item_listform"]++;
    if (count($allTraversible) > 0) {
        switch ($type) {
            case 1:
                $cardParam = '';
                if ((int)$_GET['card'] > 0) {
                    $cardParam = '-p' . (int)$_GET['card'] . '/?';
                }
                //show_item_list_1_header($formname);
                echo "<div class=\"itemcard_list1  itemcard_list\"><div class='row'>";
                break;
            case 2:
                echo "<div class=\"itemcard_list2 itemcard_list\"><div class='row'>";
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
            case 9:
                show_item_list_9_header($formname);
                break;
            case 10:
                echo "<div class=\"itemcard_list10  itemcard_list\"><div class='row'>\n";
                break;
            case 11:
                echo "<div class=\"itemcard_list11\">\n";
                break;
        }

        foreach ($allTraversible as $item) {

            $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
            //$item['retail_price'] = get_item_retail_price($item,$GLOBALS['shop_currency']['code']);
            $image = get_item_main_image($item, get_item_variant_parent($item));
            if ($image["filename"] == '' || !file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image['filename'])) {
                $imageFound = false;
                for($i = 1; $i < count($item['image_data']); $i++)
                {
                    $image["filename"] = $item['image_data'][$i]['filename'];
                    if ($image["filename"] != '' && file_exists("../../" . $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image['filename'])) {
                        $imageFound = true;
                        break;
                    }

                }
                if(!$imageFound)
                    {
                        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                            $image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                         } else {
                            $image["filename"] = "noimage.jpg";
                        }
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
                default:
                    $columns_class = "col-xs-12 col-sm-6 col-md-4 col-lg-3";
                    break;
            }

            switch ($type) {
                case 1:
                    show_item_list_1($item, $image, $formname, $input_counter);
                    break;
                case 2:
                    show_item_list_2($item, $image,$columns_class);
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
                case 9:
                    show_item_list_9($item, $formname);
                    break;
                case 10:
                    show_item_list_10($item, $image,$columns_class);
                    break;
                case 11:
                    show_item_list_11($item, $image);
                    break;
            }
        }

        switch ($type) {


            case 1:

                echo "</div>
                ".create_shop_login_modal($loginShopUrl)."
                  </div>\n";


                ?>
                  <script>

                          $(document).ready(function() {
                          $('.itemlist_variant_container .open_variants, .itemlist_open_variant_button').click(function(event) {
                            var variantcontainer = $(this).parentsUntil('.itemlist_variant_container');
                            if(variantcontainer.hasClass('active')){
                                variantcontainer.find('.is_variant').slideUp('fast');
                            }else{
                                variantcontainer.find('.is_variant').slideDown('fast');
                            }
                            variantcontainer.toggleClass('active');
                          })
                            event.preventDefault();
                        });


                  </script>
                <?
                break;
            case 2:

                echo "</div>
                ".create_shop_login_modal($loginShopUrl)."
                  </div>\n";
                ?>

                <?
                break;
            case 3:
                echo "</div>\n";
                break;
            case 4:
                echo "</div>\n</form>\n";
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
                echo "</div>\n";
                break;
            case 9:
                echo "</div>\n";
                break;
            case 10:

                echo "</div> ".create_shop_login_modal($loginShopUrl)."</div>\n";
                break;
            case 11:
                echo "</div>\n";
                break;

        }
        ?>

         <script>

                           function beforeShowModal(clickedButton)
                           {
                               var item_id =   $(clickedButton).data( "item" );
                               $( "#catalog_selected_item").val(item_id);

                                $("#login_error_Message").hide();
                           }
                           var companyCodeValue = "<?= $GLOBALS["shop"]["company"] ?>";
                            var siteValue = "<?= $redirectMainLanguage["shop_code"]?>";
                            var langaugeValue = "<?= $redirectMainLanguage["shop_language_code"] ?>";
                           function checkUserData()
                           {
                                 var forminputs = $("#form_shop_login_model").serializeArray();
                               var companyData = {};
                                companyData['name'] = "company";
                                companyData['value'] = companyCodeValue;
                               forminputs.push(companyData);


                               var siteData = {};
                                siteData['name'] = "site";
                                siteData['value'] = siteValue;
                               forminputs.push(siteData);

                               var languageData = {};
                                languageData['name'] = "language";
                                languageData['value'] = langaugeValue;
                               forminputs.push(languageData);
                              return  $.ajax({
                                type: "POST",
                                url:"/module/dcshop/user_check_ajax.php?action=shop_login",
                                data: forminputs,
                                success:function(data) {
                                    if(data == true)
                                        {
                                            $("#form_shop_login_model").submit();
                                        }
                                        else
                                        {
                                            $("#login_error_Message").show();
                                             return false;
                                        }


                                }
                              });
                           }


                           $( "#login_buy_modal_submit_button" ).click(function() {
                             checkUserData();
                            });

                  </script>

        <?

    }
}

// Artikelliste 1 :Für Übersichten, Zubehörartikel und Ersatzteile

function show_item_list_1( $item, $image, $formname, &$input_counter ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $image["filename"];
    $alt_text  = $image['description'];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    if(isset($_GET['page']) && $_GET['page'] != '' )
        {
            $itemlink = $itemlink.'&page='.$_GET['page'];
        }
    $IOCContainer = $GLOBALS['IOC'];
    //ini_set("display_errors",1);
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $currShopConfig = $IOCContainer->create('$CurrShopConfig');
    $variantService = $IOCContainer->create('\DynCom\dc\dcShop\classes\WebshopItemVariantService');
    $variants =  $variantService->getAllVariantsByPrimary($item['company'],$item['shop_code'],$item['language_code'],$item['item_no']);
   if (2 == $GLOBALS['shop']['variant_typ']) {
        $decoratedItems = [];
        foreach ($variants as $variant) {
            $decoratedItems[] = new \DynCom\dc\dcShop\classes\WebshopItemNAVVariantDecorator($item['item_obj'],$variant);
        }
    } else {
        $decoratedItems = $variants;
    }



    $variantsArray = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($decoratedItems);
    $variantsArray = item_obj_array_to_array($variantsArray);
     $item_result = $variantsArray;


   if ($image["filename"] == ''  || !file_exists("../.." . $imagelink ) || $image["filename"] == 'noimage.jpg'){

    foreach ($item_result as $variant_item) {
        $variant_image                 = get_item_main_image($variant_item, get_item_variant_parent($variant_item));
         $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
        if ($variant_image["filename"] != ''  && file_exists("../.." . $variant_imagelink  ) &&  $variant_image["filename"] != 'noimage.jpg'){

            $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
            $imagelink = $variant_imagelink;
            break;
        }
    }

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

        //$item_result = query_to_item_array($query);

        //$result         = @mysqli_query($GLOBALS['mysql_con'], $query);
        //$no_of_variants = @mysqli_num_rows($result);
        $no_of_variants = count($item_result);
        if ($no_of_variants > 0) {
            $toggle_text = '';

            /*while ($variant_item = @mysqli_fetch_array($result)) {
                $toggle_text .= "togglenb('item_" . $variant_item["item_no"] . "');";
            }*/

            echo "<div id='item_".$item['item_no']."' class='itemlist_variant_container'><div class='itemlist_variant_container_inner'>";
            echo "
                <div class=\"itemlist_open_variant_button\">
                    <i class=\"fa fa-angle-double-right\" aria-hidden=\"true\"></i>
                </div>";


            show_item_list_1_has_variant($item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text);
            $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
            $local_input_counter = 1;

            foreach ($item_result as $variant_item) {
                $variant_image                 = get_item_main_image($variant_item, get_item_variant_parent($variant_item));
                $variant_item_description_link = str_replace('/', '', $variant_item["item_slug"]);
                $variant_item_description_link = str_replace(".", "", $variant_item_description_link);
                 $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
                $varientImageExisit = true;
                if ($variant_image["filename"] == ''  || !file_exists("../.." . $variant_imagelink )){

                    if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
                        $variant_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
                    } else {
                        $variant_image["filename"] = "noimage.jpg";
                         $varientImageExisit = false;
                    }
                    if(!$varientImageExisit)
                        {
                             $variant_imagelink = $imagelink;
                        }
                        else
                        {
                                $variant_imagelink = $GLOBALS['shop_setup']['image_config'][1]["path"] . "/" . $variant_image["filename"];
                        }

                }



                $alt_text          = $variant_image['description'];

                if ($_GET["card"] != "") {
                    $pathStr = '';
                    $arr = explode("/", $_SERVER["REQUEST_URI"]);
                    for ($i = 1; $i < count($arr) - 2; $i++) {
                        $pathStr .= "/" . $arr[$i];
                    }
                    $variant_itemlink = $pathStr . "/" . ($variant_item_description_link) ."-p" . $variant_item["id"] ."/";
                } else {
                    $variant_itemlink = urlencode($variant_item_description_link) ."-p" . $variant_item["id"] . "/";
                }

                $i++;
                show_item_list_1_is_variant($variant_item, $variant_imagelink, $itemlink, $formname, ($i == $no_of_variants), $alt_text,$input_counter);
            }
            echo "</div></div>";
        } else {
            show_item_list_1_no_variant($item, $imagelink, $itemlink, $formname, $alt_text,$input_counter);
            $input_counter++;
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


        //$item_result = query_to_item_array($query);
        $item_result = $variantsArray;
        $no_of_variants = count($item_result);
        //$result         = @mysqli_query($GLOBALS['mysql_con'], $query);
        //$no_of_variants = @mysqli_num_rows($result);
        if ($no_of_variants > 0) {
            $toggle_text = '';
            /*while ($variant_item = @mysqli_fetch_array($result)) {
                $toggle_text .= "togglenb('item_" . $variant_item["item_no"] . $variant_item["color_code"] . "');";
            }*/
            echo "<div class='itemlist_variant_container'><div class='itemlist_variant_container_inner'>";
            echo "
                <div class=\"itemlist_open_variant_button\">
                    <i class=\"fa fa-angle-double-right\" aria-hidden=\"true\"></i>
                </div>";
            show_item_list_1_has_variant($item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text);
            $result              = @mysqli_query($GLOBALS['mysql_con'], $query);
            $local_input_counter = 1;
            foreach ($item_result as $variant_item) {
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
                $variant_imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $variant_image["filename"];
                /*if ($variant_imagelink == $imagelink) {
                    $variant_imagelink = '';
                }*/
                $alt_text = $variant_image['description'];

                if ($_GET["card"] != "") {
                    $pathStr = '';
                    $arr = explode("/", $_SERVER["REQUEST_URI"]);
                    for ($i = 1; $i < count($arr) - 2; $i++) {
                        $pathStr .= "/" . $arr[$i];
                    }
                    $variant_itemlink = $pathStr . "/" . ($variant_item_description_link) ."-p".$variant_item["id"]. "/";
                } else {
                    $variant_itemlink = urlencode($variant_item_description_link) ."-p". $variant_item["id"]. "/";
                }
                $i++;
                show_item_list_1_is_variant($variant_item, $variant_imagelink, $variant_itemlink, $formname, ($i == $no_of_variants), $alt_text,$input_counter);
            }
            echo "</div></div>";
        } else {
            show_item_list_1_no_variant($item, $imagelink, $itemlink, $formname, $alt_text,$input_counter);
        }
    }
    return $input_counter;
}

function show_item_list_1_is_variant( $item, $imagelink, $itemlink, $formname, $last_child = false, $alt_text = '', &$input_counter ) {
    $input_counter = (int)$input_counter;
    $parent_item     = get_item_variant_parent($item);
    $last_child_text = ($last_child) ? "_last_child" : "";
    if ($item["variant_code"] != '') {
        $id_add = '_' . $item["variant_code"];
    }
    if ($imagelink == '') {
        $img_tag = '';
    } else {
        $img_tag = "<img src=\"$imagelink\"  alt=\"$alt_text\" />";
    }

    $is_available = (bool)$item['is_available'];
    $quantity_disabled_switch = $is_available ? '' : 'disabled=\'disabled\'';
    $inactive_class_switch = $is_available ? '' : ' inactive';

    if (array_key_exists('item_obj',$item) && is_object($item['item_obj'])) {
        $order_button_html = build_itemlist_order_button($item['item_obj'],$input_counter);
    } else {
        $order_button_html = $item['order_button'];
        $input_counter++;
    }


    ?>
    <div id="item_<?= $item["item_no"] ?>" class="is_variant itemlist" style="display: none">
        <div class="itemlist_container">
            <div class="itemlist_left">
                <a href="<?= $itemlink ?>">
                    <div class="itemlist_image_container">
                            <div class="itemlist_content image">
                                 <?=$img_tag?>
                            </div>
                            <? show_item_promotion_banners($item); ?>
                    </div>
                    <div class="itemlist_details_container">
                        <div class="itemlist_content description itemlist_details_top">
                             <?= $item["description"] ?>
                        </div>
                        <div class="itemlist_details_left">
                            <div class="itemlist_content variant_counter">
                                 <?= $item["variant_type"] ?>
                            </div>
                        </div>
                        <div class="itemlist_details_right">
                                <div class="itemlist_content attributes">
                                            <?

                                              if ($GLOBALS['shop']['variant_typ'] == '0' ||$GLOBALS['shop']['variant_typ'] == '1' ) {
                                            $text = $GLOBALS['tc']['item_no'];
                                            $val = $item['item_no'];
                                     } elseif ($GLOBALS['shop']['variant_typ'] == '2') {
                                            $text = $GLOBALS['tc']['variant_code'];
                                            $val = $item['variant_code'];
                                     }
                                     ?>

                                     <div class="attribute_row">
                                        <div class="attribute_code">
                                            <?= $text ?>
                                        </div>
                                        <div class="attribute_text">
                                            <?= $val ?>
                                        </div>
                                      </div>
                                    <?
                                        if (count($item['attribute_collection']) > 0) {
                                            $i = 0;
                                            foreach($item['attribute_collection'] as $attribute) {
                                                if ($i > 4) {
                                                    break;
                                                }
                                                /**
                                                *@var $attribute \DynCom\dc\dcShop\classes\WebshopItemAttribute
                                                 */
                                                $attr_desc = $attribute->attribute_description;
                                                $attr_value = $attribute->attribute_value;

                                            ?>
                                            <div class="attribute_row">
                                                <div class="attribute_code">
                                                    <?= $attr_desc ?>
                                                </div>
                                                <div class="attribute_text">
                                                    <?= $attr_value ?>
                                                </div>
                                            </div>
                                            <?
                                            $i++;
                                            }
                                        }



                                            ?>


                                </div>
                            </div>
                    </div>
                </a>
            </div>

            <div class="itemlist_right">
                 <?  if($GLOBALS['language']['catalog_login']) { ?>
                    <!-- <input type="button"  onclick="beforeShowModal(this);" data-toggle="modal" data-target="#login_buy_modal" name="login_buy_buttton"  id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" class="button_new button button_action"  value="<?= $GLOBALS['tc']['login_buy'] ?>"> -->
                 <? } ?>
            </div>

        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_1_has_variant( $item, $imagelink, $itemlink, $toggle_text, $formname, $alt_text = '' ) {

    $from_price_string = $GLOBALS['tc']['from'] . ' ' . format_amount($item["base_price"], false);
    if ($GLOBALS['shop']['cross_price_typ'] != 0) {
        $item_price = return_item_cross_price_snippet($item, $GLOBALS['shop']['cross_price_typ'], true);
    } else {
        $item_price = get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']);
    }

    if ($item_price == $item['base_price']) {
        $replacement_string = format_amount($item['base_price'], false);
    } else {
        $replacement_string = format_amount($item_price, false);
    }

    ?>
    <div id="item_<?= $item["item_no"] ?>" class="has_variant itemlist">
        <div class="itemlist_container">
            <div class="itemlist_left">
                <a href="<?= $itemlink ?>">
                    <div class="itemlist_image_container">
                        <div class="itemlist_content image">
                             <img src="<?= $imagelink ?>" border="0" alt="<?= $item["description"] ?>" title="<?= $item["description"] ?>">
                        </div>
                    </div>
                    <div class="itemlist_details_container">
                            <div class="itemlist_content description itemlist_details_top">
                                 <?= $item["description"] ?>
                            </div>
                            <div class="itemlist_details_left">
                                 <div class="itemlist_content variant_counter">
                                     <?= get_item_no_or_no_of_variants($item) ?>
                                </div>
                                <div class="itemlist_content summary">
                                     <?= $item["summary"] ?>
                                </div>
                            </div>
                            <div class="itemlist_details_right">
                                <div class="itemlist_content attributes">
                                            <?


                                            // if ($GLOBALS['shop']['variant_typ'] == '1' ||$GLOBALS['shop']['variant_typ'] == '2' ) {
                                            ?>


                                     <div class="attribute_row">
                                        <div class="attribute_code">
                                            <?= $GLOBALS['tc']['item_no'] ?>
                                        </div>
                                        <div class="attribute_text">
                                            <?= $item['item_no'] ?>
                                        </div>
                                      </div>
                                      <?
                                        //}


                                        if (count($item['attribute_collection']) > 0) {
                                            $i = 0;
                                            foreach($item['attribute_collection'] as $attribute) {
                                                if ($i > 4) {
                                                    break;
                                                }
                                                /**
                                                *@var $attribute \DynCom\dc\dcShop\classes\WebshopItemAttribute
                                                 */
                                                $attr_desc = $attribute->attribute_description;
                                                $attr_value = $attribute->attribute_value;

                                            ?>
                                            <div class="attribute_row">
                                                <div class="attribute_code">
                                                    <?= $attr_desc ?>
                                                </div>
                                                <div class="attribute_text">
                                                    <?= $attr_value ?>
                                                </div>
                                            </div>
                                            <?
                                            $i++;
                                            }
                                        }


                                         ?>
                                </div>
                            </div>
                    </div>
                    </a>
            </div>
            <div class="itemlist_right">
                <div class="button button_action open_variants"><?=$GLOBALS['tc']['show_variants']?></div>

                        <?  if($GLOBALS['language']['catalog_login']) { ?>

                <input type="button"  onclick="beforeShowModal(this);" data-toggle="modal" data-target="#login_buy_modal" name="login_buy_buttton"  id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" class="button_new button button_action"  value="<?= $GLOBALS['tc']['login_buy'] ?>">

            <? } ?>

            </div>
        </div>
    </div>
    <?
}

function show_item_list_1_no_variant( $item, $imagelink, $itemlink, $formname, $alt_text = '', &$input_counter) {
        $is_available = (bool)$item['is_available'];
    $quantity_disabled_switch = $is_available ? '' : 'disabled=\'disabled\'';
    $inactive_class_switch = $is_available ? '' : ' inactive';

    if (array_key_exists('item_obj',$item) && is_object($item['item_obj'])) {
        $order_button_html = build_itemlist_order_button($item['item_obj'],$input_counter);
    } else {
        $order_button_html = $item['order_button'];
        $input_counter++;
    }


    ?>
    <div id="item_<?= $item["item_no"] ?>" class="no_variant itemlist">
        <div class="itemlist_container">
            <div class="itemlist_left">
                <a href="<?= $itemlink ?>">
                    <div class="itemlist_image_container">
                        <div class="itemlist_content image">
                             <img src="<?= $imagelink ?>" border="0" title="<?= $item["description"] ?>" alt="<?= $item["description"] ?>">
                        </div>
                    </div>
                    <div class="itemlist_details_container">
                        <div class="itemlist_content description itemlist_details_top">
                             <?= $item["description"] ?>
                        </div>
                        <div class="itemlist_details_left">
                             <div class="itemlist_content summary">
                                 <?= $item["summary"] ?>
                            </div>
                        </div>
                        <div class="itemlist_details_right">
                             <div class="itemlist_content attributes">
                                    <div class="attribute_row">
                                <div class="attribute_code">
                                    <?= $GLOBALS['tc']['item_no'] ?>
                                </div>
                                <div class="attribute_text">
                                    <?= $item['item_no'] ?>
                                </div>
                                 </div>

                                    <?
                                if (count($item['attribute_collection']) > 0) {
                                    foreach($item['attribute_collection'] as $attribute) {
                                        /**
                                        *@var $attribute \DynCom\dc\dcShop\classes\WebshopItemAttribute
                                         */
                                        $attr_desc = $attribute->attribute_description;
                                        $attr_value = $attribute->attribute_value;

                                    ?>
                                    <div class="attribute_row">
                                        <div class="attribute_code">
                                            <?= $attr_desc ?>
                                        </div>
                                        <div class="attribute_text">
                                            <?= $attr_value ?>
                                        </div>
                                    </div>
                                    <?
                                    }
                                }

                             ?>

                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?  if($GLOBALS['language']['catalog_login']) { ?>
            <div class="itemlist_right">

              <input type="button"  onclick="beforeShowModal(this);" data-toggle="modal" data-target="#login_buy_modal" name="login_buy_buttton"  id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" class="button_new button button_action"  value="<?= $GLOBALS['tc']['login_buy'] ?>">
            </div>
            <? } ?>
        </div>
    </div>
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

function show_item_list_2( $item, $image ,$columns_class) {
    $imagelink = $GLOBALS['shop_setup']['image_config'][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist2 itemlist <?=$columns_class?>">
        <a href="<?= $itemlink ?>">
            <div class="itemlist_container">
                <div class="itemlist_content banner">
                    <? show_item_promotion_banners($item); ?>
                    <div class="itemlist2_banners_campaign">
				        <? show_item_campaign_banners($item, false, false, true);?>
			        </div>
                </div>
                <div class="itemlist_content image">
                    <img src="<?= $imagelink ?>" title="<?= $image["description"] ?>" alt="<?= $image['description'] ?>">
                </div>
                <div class="itemlist_content description">
                    <?= $item["description"] ?>
                </div>
                <div class="itemlist_content item_no">
                    <?= get_item_no_or_no_of_variants($item) ?>
                </div>
                <div class="itemlist_content summary">
                    <?= $item["summary"] ?>
                </div>
                <div class="itemlist_content attributes">
                <?
                    if (count($item['attribute_collection']) > 0) {
                        $i = 0;
                        foreach($item['attribute_collection'] as $attribute) {
                            if ($i > 4) {
                                break;
                            }
                            /**
                            *@var $attribute \DynCom\dc\dcShop\classes\WebshopItemAttribute
                             */
                            $attr_desc = $attribute->attribute_description;
                            $attr_value = $attribute->attribute_value;

                        ?>
                        <div class="attribute_row">
                            <div class="attribute_code">
                                <?= $attr_desc ?>
                            </div>
                            <div class="attribute_text">
                                <?= $attr_value ?>
                            </div>
                        </div>
                        <?
                        $i++;
                        }
                    }

                 ?>
                </div>

                 <?  if(!$GLOBALS['language']['catalog_login']) { ?>
                  <div class="itemlist_button">
                      <span class="button"><?=$GLOBALS['tc']['itemlist_button']?></span>
                    </div></div> </a>
                <? }else{ ?> </div> </a>
                <input type="button" class="button" onclick="beforeShowModal(this);" name="login_buy_buttton"
                       id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" data-toggle="modal"
                       data-target="#login_buy_modal" value="<?= $GLOBALS['tc']['login_buy'] ?>"> <? } ?>


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
                                                   title="<?= $image["description"] ?>"      alt="<?= $image['description'] ?>"></div>
                <? show_item_promotion_banners($item); ?>
            </div>
            <div class="itemlist3_description"><strong><?= $item["description"] ?></strong><? //echo $item['summary']; ?>
            </div>
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
                    echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', false, $GLOBALS['shop']['campain_no']), false) . "";
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
    $itemKey = $item['item_key'];

    if ($item['is_coupon_item'] == 1) {
        $readonly = " readonly='readonly'";
    } else {
        $readonly = "";
    }
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell image_line text-center">
            <a href="<?=$itemlink;?>">
              <div class="image">
                   <img src="<?= $imagelink ?>" border="0" title="<?= $image["description"] ?>" alt="<?= $image['description'] ?>">
              </div>
            </a>
        </div>
        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["item_no"] ?>
                <?
                if (strlen($item["var_code"]) > 0) {
                    ?>
                    <div class="var_code_title"><?= $GLOBALS["tc"]["var_code"] ?></div>
                    <div class="var_code"><?= $item["var_code"] ?></div>
                <?
                }
                ?>
        </div>
        <div class="table_cell description_line">
            <div class="description">
                <a href="<?=$itemlink;?>">
                   <?= $item["description"] ?>
                </a>
            </div>
            <a class=basket_delete" href="<?= $action ?>"><?= $GLOBALS["tc"]["remove_item"] ?></a>
        </div>
        <div class="table_cell price_line list_price_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['base_price']?></div>
            <?= format_amount($item["base_price"], false) ?>
        </div>
        <div class="table_cell price_line your_price_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['your_price']?></div>
            <?= format_amount($item["customer_price"], false) ?>
        </div>
        <div class="table_cell inventory_line text-center text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['inventory']?></div>
            <? get_inventory_sign($item) ?>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
             <input name="input_item_id_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_id_<?= $GLOBALS["input_counter"] ?>" type="hidden" value="<?= $item["id"] ?>" />
                <input name="input_variant_code_<?= $GLOBALS["input_counter"] ?>"
                       id="input_variant_code_<?= $GLOBALS["input_counter"] ?>" type="hidden"
                       value="<?= $item["var_code"] ?>" />
                <input value="<?= round($item["basket_quantity"]) ?>" class="input_quantity_line"
                       name="input_item_quantity_<?= $GLOBALS["input_counter"] ?>"
                       id="input_item_quantity_<?= $GLOBALS["input_counter"] ?>" type="text"
                       tabindex="<?= tabcounter() ?>" <?= $readonly ?> maxlength="3"
                       onKeyPress="return submitenter(this,event)" />
                <input type="hidden" name ="input_item_key_<?= $GLOBALS['input_counter']?>" value="<?= htmlentities($itemKey) ?>"/>

        </div>
        <div class="table_cell line_amount_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <?= format_amount($item["basket_quantity"] * $item["customer_price"], false) ?>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_4_header($formname) {
    ?>
    <form name="<?= $formname ?>" id="<?= $formname ?>"
          action=<?= ml("", "action", "shop_refresh_user_basket") ?> method="POST">
    <div class="itemtable_list basket_table table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell image_line text-center"></div>
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <div class="table_cell price_line list_price_line text-right text-nowrap"><?= $GLOBALS["tc"]["base_price"] ?></div>
            <div class="table_cell price_line text-right text-nowrap"><?= $GLOBALS["tc"]["your_price"] ?></div>
            <div class="table_cell inventory_line text-center"><?= $GLOBALS["tc"]["inventory"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell line_amount_line text-right text-nowrap"><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </div>
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
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell image_line text-center">
              <div class="image">
                   <img src="<?= $imagelink ?>" border="0" title="<?= $image["description"] ?>" alt="<?= $image['description'] ?>">
              </div>
        </div>
        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["item_no"] ?>
                <?
                if (strlen($item["var_code"]) > 0) {
                    ?>
                    <div class="var_code_title"><?= $GLOBALS["tc"]["variant"] ?></div>
                    <div class="var_code"><?= $item["var_code"] ?></div>
                <?
                }
                ?>
        </div>
        <div class="table_cell description_line">
            <strong><?= $item["description"] ?></strong><br />
            <?= $item["summary"] ?>
        </div>
        <div class="table_cell invoice_line">
            <div class="itemtable_list_label"><?= $GLOBALS["tc"]["invoice_discount"] ?></div>
            <?= ($item["allow_invoice_disc"] == 1) ? $GLOBALS["tc"]["yes"] : $GLOBALS["tc"]["no"] ?>
        </div>
        <div class="table_cell price_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['your_price']?></div>
            <?= format_amount($item["customer_price"], false) ?>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= round($item["basket_quantity"]) ?>
            </div>
        <div class="table_cell line_amount_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <?= format_amount($item["basket_quantity"] * $item["customer_price"], false) ?>
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
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <div class="table_cell invoice_line"><?= $GLOBALS["tc"]["invoice_discount"] ?></div>
            <div class="table_cell price_line text-right text-nowrap"><?= $GLOBALS["tc"]["your_price"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell line_amount_line text-right text-nowrap"><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </div>
<?
}

// Artikelliste 6: Bestell-Email
function show_item_list_6( $item, $image, $formname ) {
    ?>
    <tr>
        <td valign="middle"
            style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;"><?= $item["item_no"] ?><? if (strlen($item["variant_code"]) > 0) { ?> &nbsp;&nbsp; <? echo $item["variant_code"];
            } ?></td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;"><?= $item["description"] ?></td>
        <td valign="middle"
            style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;"><?= ($item["allow_invoice_disc"] == 1) ? $GLOBALS["tc"]["yes"] : $GLOBALS["tc"]["no"] ?></td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90"
            align="right"><?= format_amount($item["list_price"], false) ?></td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90"
            align="right"><?= format_amount($item["unit_price"], false) ?></td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="40"
            align="right"><?= round($item["basket_quantity"]) ?></td>
        <td valign="middle" style="border-top:1px solid <?=$GLOBALS['shop_setup']['mail_color']?>;" width="90"
            align="right"><?= format_amount($item["basket_quantity"] * $item["unit_price"], false) ?></td>
    </tr>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_6_header($formname) {
    ?>
    <table border="0" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <td><strong><?= $GLOBALS["tc"]["item_no"] ?></strong></td>
        <td><strong><?= $GLOBALS["tc"]["description"] ?></strong></td>
        <td><strong><?= $GLOBALS["tc"]["allow_invoice_disc"] ?></strong></td>
        <td width="90" align="right">
            <strong><?= $GLOBALS["tc"]["base_price_alt"] ?></strong></td>
        <td width="90" align="right">
            <strong><?= $GLOBALS["tc"]["your_price"] ?></strong></td>
        <td width="40" align="right">
            <strong><?= $GLOBALS["tc"]["quantity"] ?></strong></td>
        <td width="90" align="right">
            <strong><?= $GLOBALS["tc"]["line_amount"] ?></strong></td>
    </tr>
<?
}

// Artikelliste 7: Lieferung-Sales Shipment History
function show_item_list_7( $item, $formname ) {
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["no"] ?>
        </div>
        <div class="table_cell description_line">
            <strong><?= $item["description"] ?></strong>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= ($item["quantity"] != '0') ? round($item["quantity"]) : '' ?>
        </div>
        <div class="table_cell unit_line">
            <div class="itemtable_list_label"><?= $GLOBALS["tc"]["unit"] ?></div>
                <?= $item["unit_of_measure"] ?>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_7_header($formname) {
    ?>
    <div class="itemtable_list itemtable_list7 table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell unit_line"><?= $GLOBALS["tc"]["unit"] ?></div>
        </div>
<?
}

// Artikelliste : Rechnung-Sales Invoice History
function show_item_list_8( $item, $formname ) {
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["no"] ?>
        </div>
        <div class="table_cell description_line">
            <strong><?= $item["description"] ?></strong>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= ($item["quantity"] != '0') ? round($item["quantity"]) : '' ?>
        </div>
        <div class="table_cell unit_line">
            <div class="itemtable_list_label"><?= $GLOBALS["tc"]["unit"] ?></div>
                <?= $item["unit_of_measure"] ?>
        </div>
        <div class="table_cell price_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['your_price']?></div>
            <?= ($item["unit_price"] != 0) ? format_amount($item["unit_price"], false) : '' ?>
        </div>
        <div class="table_cell line_amount_line text-right text-nowrap">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['line_amount']?></div>
            <?= ($item["line_amount"] != '0') ? format_amount($item["line_amount"], false) : '' ?>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_8_header($formname) {
    ?>
    <div class="itemtable_list itemtable_list8 table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell unit_line"><?= $GLOBALS["tc"]["unit"] ?></div>
            <div class="table_cell price_line text-right text-nowrap"><?= $GLOBALS["tc"]["your_price"] ?></div>
            <div class="table_cell line_amount_line text-right text-nowrap"><?= $GLOBALS["tc"]["line_amount"] ?></div>
        </div>
<?
}


// Artikelliste : Reklamation / Rücksendung
function show_item_list_9( $item, $formname ) {
    ?>
    <div id="item_<?= $item["item_no"] ?>" class="itemtable_row table_row">
        <div class="table_cell item_no_line">
              <div class="itemtable_list_label"><?=$GLOBALS['tc']['item_no']?></div>
              <?= $item["no"] ?>
        </div>
        <div class="table_cell description_line">
            <strong><?= $item["description"] ?></strong>
        </div>
        <div class="table_cell quantity_line text-center">
            <div class="itemtable_list_label"><?=$GLOBALS['tc']['quantity']?></div>
            <?= ($item["quantity"] != '0') ? round($item["quantity"]) : '' ?>
        </div>
        <div class="table_cell unit_line">
                <?= $item["unit_of_measure"] ?>
        </div>
    </div>
    <?
    $GLOBALS["input_counter"]++;
}

function show_item_list_9_header($formname) {
    ?>
    <div class="itemtable_list itemtable_list9 table_area">
        <div class="itemtable_row table_row table_header">
            <div class="table_cell item_no_line"><?= $GLOBALS["tc"]["item_no"] ?></div>
            <div class="table_cell description_line"><?= $GLOBALS["tc"]["description"] ?></div>
            <div class="table_cell quantity_line text-center"><?= $GLOBALS["tc"]["quantity"] ?></div>
            <div class="table_cell unit_line"><?= $GLOBALS["tc"]["unit"] ?></div>
        </div>
<?
}

//Artikelvorschau ohne Warenkorb und Favoriten
function show_item_list_10( $item, $image,$columns_class ) {

    $imagelink = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
        $itemlink = create_item_link_tab($item);
    }
    ?>
    <div class="itemlist10 itemlist <?=$columns_class?>">
        <a href="<?= $itemlink ?>">
            <div class="itemlist_container">
                <div class="itemlist_content banner">
                    <? show_item_promotion_banners($item); ?>
                    <div class="itemlist2_banners_campaign">
				        <? show_item_campaign_banners($item, false, false, true);?>
			        </div>
                </div>
                <div class="itemlist_content image">
                    <img src="<?= $imagelink ?>" title="<?= $image["description"] ?>" alt="<?= $image['description'] ?>">
                </div>
                <div class="itemlist_content description">
                    <?= $item["description"] ?>
                </div>
                <div class="itemlist_content item_no">
                    <?= get_item_no_or_no_of_variants($item) ?>
                </div>
                <div class="itemlist_content summary">
                    <?= $item["summary"] ?>
                </div>
                <div class="itemlist_content attributes">
                <?
                    if (count($item['attribute_collection']) > 0) {
                        $i = 0;
                        foreach($item['attribute_collection'] as $attribute) {
                            if ($i > 4) {
                                break;
                            }
                            /**
                            *@var $attribute \DynCom\dc\dcShop\classes\WebshopItemAttribute
                             */
                            $attr_desc = $attribute->attribute_description;
                            $attr_value = $attribute->attribute_value;

                        ?>
                        <div class="attribute_row">
                            <div class="attribute_code">
                                <?= $attr_desc ?>
                            </div>
                            <div class="attribute_text">
                                <?= $attr_value ?>
                            </div>
                        </div>
                        <?
                        $i++;
                        }
                    }

                 ?>
                </div>

                <?  if(!$GLOBALS['language']['catalog_login']) { ?>
                  <div class="itemlist_button">
                      <span class="button"><?=$GLOBALS['tc']['itemlist_button']?></span>
                    </div></div> </a>
                <? }else{ ?> </div> </a>
                <input type="button" class="button" onclick="beforeShowModal(this);" name="login_buy_buttton"
                       id="login_buy_buttton_<?= $item['id'] ?>" data-item="<?= $item['id'] ?>" data-toggle="modal"
                       data-target="#login_buy_modal" value="<?= $GLOBALS['tc']['login_buy'] ?>"> <? } ?>





    </div>
<?
}

//Artikelvorschau ohne Warenkorb und Favoriten
function show_item_list_11( $item, $image ) {
    $imagelink = $GLOBALS["shop_setup"]["image_config"][1]["path"] . "/" . $image["filename"];
    $itemlink  = create_item_link($item);
    ?>
    <div class="itemlist11">
        <a class="image" href="<?= $itemlink ?>"><img src="<?= $imagelink ?>" border="0"
                                              title="<?= $image["description"] ?>"        alt="<?= $image['description'] ?>"></a>
        <a class="brand" href="<?= $itemlink ?>"><?= get_brand_name($item) ?></a>
        <a class="description" href="<?= $itemlink ?>"><?= $item["description"] ?></a>
        <a class="price"
           href="<?= $itemlink ?>"><?= get_item_cross_price($item, $GLOBALS['shop']['cross_price_typ']); ?></a>
    </div>
    <div class="itemlist11_spacer"></div>

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

            if ($GLOBALS["shop"]["variant_typ"] != 0) {
                if ($parent_item != "") {
                    $item = $parent_item;
                }
                $descr    = ($item["variant_type"] == "") ? $item["item_slug"] : $item["variant_type"];
                $itemlink ="-p". $item["id"]. "/";
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

function show_category_list( $result, $language ) {
    while ($val = @mysqli_fetch_array($result)) {
        $categoryPath = category_get_path($val);
        $categorylink =  $categoryPath;
        $image        = get_category_icon($val, $language);

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
                            <?= $val["name"] ?>
                        </div>
                    </div>
                </div>
            </a>
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


function show_item_list_with_pages( $type, $query_, $category, $vendor_sql_string = "", $max_number = 0, $order_by = "", $show_all_items = false, $is_Search = false, $search_query = "" ) {
    echo "<div class='itembox_header row'>";

    if (!$is_Search && $category != "campain" && $category['user_sorting'] == 1) {
        if (!$is_Search && $category != "campain") {
            if ($order_by == "ranking") {
                $order_by_val = " ORDER BY " . get_sort_type($order_by) . " DESC ";
            } else {
                $order_by_val = " ORDER BY " . get_sort_type($order_by) . " ";
            }
        }
        if ($show_all_items) {
            echo "<div class='sort_by col-xs-12 col-sm-6 col-md-4 col-lg-3'>";
            echo "<form name=\"item_order\" method=\"post\">";
            echo create_sort_select("", "sort_by", $order_by);
            echo "</form>";
            echo "</div>";
        }
    } elseif ($category['sort_items'] != 0) {
                    //$order_by_val = " ORDER BY " . $GLOBALS['category_sort_types'][$category['sort_items']]['description'] . " ";
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
            $categoryPath =  "?shop_category=campain?shop_campain=" . $_REQUEST['shop_campain'] . "&";
            $order_by_val = " ORDER BY shop_view_active_item.item_no ";
        }
    } else {
        $categoryPath =   "search/?input_search=" . $_REQUEST["input_search"] . "&";
        $order_by_val = " ORDER BY shop_view_active_item.order_ranking DESC ";
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
    echo "<div class='col-xs-12'>";
    get_page_switch($rows,$page,$categoryPath,$order_by);
    echo "</div>";
    echo "</div>";
    show_item_list_from_query($query_limit, $list_type,"");
    get_page_switch($rows,$page,$categoryPath,$order_by);
}

/**
* @param $query
* @param $listNo
* @param $formname
 */
function show_item_list_from_query($query,$listNo,$formname) {
    if ($query instanceof mysqli_result) {
        $query = mysqli_fetch_assoc($query);
    }
    if (is_array($query)) {
        show_item_list($query,$listNo,$formname);
        return;
    }
    $numRows = 0;
    if(isset($GLOBALS['IOC'])) {
        $IOCContainer = $GLOBALS['IOC'];
        if ($IOCContainer instanceof \Dice\Dice) {
            $item_result = query_to_item_array($query);
            $numRows = count($item_result);
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

function query_to_item_array($query)
{
    $IOCContainer = $GLOBALS['IOC'];
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    /**
    *@var $itemBuilder \DynCom\dc\dcShop\classes\WebshopItemBuilder
     */
    $objArr = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($itemBuilder->buildWebshopItemsFromItemQuery($query));
    /**
    *@var $shopConfig \DynCom\dc\dcShop\classes\CurrShopConfiguration
     */
    $item_result = item_obj_array_to_array($objArr);
    return $item_result;
}

function build_itemlist_order_button(\DynCom\dc\dcShop\interfaces\WebshopItemInterface $item, &$input_counter)
{
    $input_counter = (int)$input_counter;
    $IOCContainer = $GLOBALS['IOC'];
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $customer = $shopConfig->getCustomer();
    $currencyCode = $shopConfig->getCurrencyCode();
    $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
    $subscriptionDataBuilder = $IOCContainer->create('DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder');
    $validator = new \DynCom\dc\common\classes\Validator([], []);
    $formBuilder = new \DynCom\dc\common\classes\FormBuilder('dummyID');
    $templating = $IOCContainer->create('DynCom\dc\common\classes\Templating');
    $attributeService = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemAttributeService');
    $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');
    $vatMgr = $IOCContainer->create('$VATManager');
    $itemOrderbuttonBuilder = new \DynCom\dc\dcShop\classes\ItemOrderButtonBuilder($shopConfig,$availabilityProvider,$validator,$formBuilder,$templating,$vatMgr);

     /**
    *@var $itemBuilder \DynCom\dc\dcShop\classes\WebshopItemBuilder
     */
    $itemObj = $itemBuilder->decorateWebshopItemForItemList($item);
    /**
    *@var $advancedPriceProvider \DynCom\dc\dcShop\classes\AdvancedPriceProvider
    */
    $defaultItemPrice = $advancedPriceProvider->getItemCustomerPrice($itemObj,$itemObj->getMinQty(),$customer,$currencyCode);
    /**
    *@var $subscriptionDataBuilder \DynCom\dc\dcShop\subscriptions\classes\ItemSubscriptionDataBuilder
    */
    $subscriptionData = $subscriptionDataBuilder->getItemSubscriptionData($itemObj);

    $htmlString = $itemOrderbuttonBuilder->getItemlistButtonHTML($itemObj,$defaultItemPrice,$subscriptionData,$input_counter,true);
    $input_counter++;
    return $htmlString;
}

/**
* @param \DynCom\dc\dcShop\interfaces\WebshopItemInterface $item
* @param int $input_counter
* @return array
*/
function item_obj_to_itemlistlist_array(\DynCom\dc\dcShop\interfaces\WebshopItemInterface $item, &$input_counter)
{
    $IOCContainer = $GLOBALS['IOC'];
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $attributeService = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemAttributeService');


    /**
    *@var $itemBuilder \DynCom\dc\dcShop\classes\WebshopItemBuilder
     */
    $itemObj = $itemBuilder->decorateWebshopItemForItemList($item);

    $htmlString = build_itemlist_order_button($itemObj,$input_counter);
    $attrCollection = $attributeService->getAllForItemByPrimary($itemObj->getCompany(),$shopConfig->getShopCode(),$shopConfig->getShopLanguageCode(),$shopConfig->getShop()->default_language_code,$itemObj->getItemNo(),$itemObj->getVariantCode());

    $itemArr = [];
    $itemArr['order_button'] = $htmlString;
    $itemArr['attribute_collection'] = $attrCollection;
    $itemArr['id'] = $itemObj->getID();
    $itemArr['company'] = $itemObj->getCompany();
    $itemArr['shop_code'] = $itemObj->getShopCode();
    $itemArr['language_code'] = $itemObj->getLanguageCode();
    $itemArr['item_no'] = $itemObj->getItemNo();
    $itemArr['var_code'] = $itemObj->getVariantCode();
    $itemArr['retail_price'] = $itemObj->getRetailPrice();
    $itemArr['base_price'] = $itemObj->getBasePrice();
    $itemArr['unit_price'] = $itemObj->getUnitPrice();
    $itemArr['customer_price'] = $itemObj->getUnitPrice();
    $itemArr['cross_price'] = $itemObj->getCrossPrice();
    $itemArr['image_data'] = $itemObj->getImageData();
    $itemArr['main_image_data'] = $itemObj->getMainImageData();
    $itemArr['description'] = $itemObj->getDescription();
    $itemArr['variant_typ'] = $itemObj->getVariantType();
    $itemArr['variant_type'] = $itemObj->getVariantType();
    $itemArr['summary'] = $itemObj->getSummary();
    $itemArr['parent_item_no'] = $itemObj->getParentItemNo();
    $itemArr['notifications'] = get_item_user_notifications($itemObj);
    $itemArr['inventory'] = $itemObj->getInventory();
    $itemArr['availability'] = $itemObj->getAvailability();
    $itemArr['is_available'] = $itemObj->isAvailable();
    $itemArr['min_qty'] = $itemObj->getMinQty();
    $itemArr['max_qty'] = $itemObj->getMaxQty();
    $itemArr['qty_step'] = $itemObj->getQtyStep();
    $itemArr['main_category_line_no'] = $itemObj->main_category_line_no;
    $itemArr['item_obj'] = $itemObj;
    $itemArr['item_slug'] = $itemObj->getItemSlug();

    return $itemArr;
}

/**
* @param $objArr
 * @return array
 */
function item_obj_array_to_array($objArr){

    $item_result = [];
    /**
    * @var $itemObj OrderableEntityInterface|WebshopItemWithImages
    */
    $i = 1;
    foreach ($objArr as $itemObj) {
        //$i is incremented by function
        $item = item_obj_to_itemlistlist_array($itemObj,$i);
        $item_result[] = $item;
    }
    return $item_result;
}

function get_page_switch($rows,$page,$categoryPath,$order_by) {
    if ($rows > $GLOBALS['shop_setup']['num_items_per_page']) {
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
                $step_forward_link   = $categoryPath . "page=" . $output . "&sort_by=" . $order_by;
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

function show_category_list_etsy( $result, $language ) {
    $shopquery = "SELECT * FROM shop_shop WHERE company = '" . $GLOBALS['language']['company'] . "' AND CODE = '" . $GLOBALS['language']['shop_code'] . "'";
	$shopresult = @mysqli_query($GLOBALS['mysql_con'], $shopquery);
    if (@mysqli_num_rows($shopresult) == 1) {
		$shop                                 = mysqli_fetch_assoc($shopresult);
        $shop['use_items_from_shop_code']     = (empty($shop['use_items_from_shop_code']) ? $shop['code'] : $shop['use_items_from_shop_code']);
        $shop['use_categorys_from_shop_code'] = (empty($shop['use_categorys_from_shop_code']) ? $shop['code'] : $shop['use_categorys_from_shop_code']);
		$item_no_snippet = '';

	}

	while ($category = @mysqli_fetch_array($result)) {

		$category_codes_query_snippet = ' IN (';
		$category_codes_query_snippet .= '\'' . $category['code'] . '\'';
		$category_codes_query_snippet .= ') ';

        $categoryPath = get_category_path($category['id'], $language);
        $categorylink = "/" . customizeUrl() . "/" . $categoryPath;
        $categoryImage        = get_category_icon($category, $language);
        ?>
		<?

		$dc_order_cat = 'gutscheine-per-mail';
		$dc_order_cat_2 = 'gutschein-per-mail';
		$dc_order_cat_3 = 'email-gift-tokens';
		if ($category['code'] == $dc_order_cat || $category['code'] == $dc_order_cat_2 || $category['code'] == $dc_order_cat_3 ) {
			$categorylink .= "?shop_category=dc_order";
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
                                        <img src="<?= $imagelink ?>" border="0" title="<?= $imageItem["description"] ?>" alt="<?= $imageItem['description'] ?>">
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
                                                        <img src="<?= $imagelink ?>" title="<?= $imageItem['description'] ?>" alt="<?= $imageItem['description'] ?>">
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

function create_shop_login_modal($loginShopUrl)
{
    $secret = getenv('SHOP_PASSWORD');

    $secretToken2 = array(
      "time"=>time(),
      "token_id"=>$_SERVER['SERVER_NAME']
    );

    $secretToken1 = base64_encode(hash_hmac("sha256",json_encode($secretToken2),$secret));
    $secretToken2 = base64_encode(json_encode($secretToken2));

         echo "      
                    
                        <!-- Modal -->
                    <div id=\"login_buy_modal\" class=\"modal fade\" role=\"dialog\">
                      <div class=\"modal-dialog\">
                    
                        <!-- Modal content-->
                        <div class=\"modal-content\">
                          <div class=\"modal-header\">
                             <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span
                                aria-hidden=\"true\">&times;</span></button>
                                 <div class=\"site_headline\">
                                    <h2>&nbsp;&nbsp;&nbsp;".$GLOBALS['tc']['login']."</h2>
                            </div>
                          </div>
                          <div class=\"modal-body\">
                                <div id='login_error_Message' class=\"alert alert-danger\" style='display:none'>
                                  ".$GLOBALS['tc']['login_error_modal_message']."                                </div>";


                            $email_field = "
                                <div class=\"form-group\">
                                    <label for=\"input_email\">" . $GLOBALS['tc']['email'] . "</label>
                                    <input type=\"text\" class=\"form-control\" name=\"input_email\" id=\"input_email\">
                                </div>
                            ";

                            $login_field = "
                                <div class=\"form-group\">
                                    <label for=\"input_login\">" . $GLOBALS['tc']['login_b2b'] . "</label>
                                    <input type=\"text\" class=\"form-control\" name=\"input_login\" id=\"input_login\">
                                </div>
                            ";

                            $customer_no_field =
                            "
                                <div class=\"form-group\">
                                    <label for=\"input_customer_no\">" . $GLOBALS['tc']['customer_no'] . "</label>
                                    <input type=\"text\" class=\"form-control\" name=\"input_customer_no\" id=\"input_customer_no\">
                                </div>
                            ";

                            $password_field =
                            "
                                <div class=\"form-group\">
                                    <label for=\"input_password\">" . $GLOBALS['tc']['password'] . "</label>
                                    <input type=\"password\" class=\"form-control\" name=\"input_password\" id=\"input_password\">
                                </div>
                            ";

                            $field_1_html = '';
                            $field_2_html = '';

                            switch ($GLOBALS["shop"]['login_type']) {
                                case 0: //E-Mail & Passwort
                                    $field_1_html = $email_field;
                                    break;
                                case 1: //Login & Passwort
                                    $field_1_html = $login_field;
                                    break;
                                case 2: //Kunden-Nr., Login & Passwort
                                    $field_1_html = $customer_no_field;
                                    $field_2_html = $login_field;
                                    break;
                                case 3: //Kunden-Nr., E-Mail & Passwort
                                    $field_1_html = $customer_no_field;
                                    $field_2_html = $email_field;
                                    break;
                                case 4: //Kunden-Nr. & Passwort
                                    $field_1_html = $customer_no_field;
                                    break;
                                default:
                                    break;
                            }


                            echo "
                            <form id=\"form_shop_login_model\" name=\"form_shop_login\" method=\"POST\" action='".$loginShopUrl."' >
                                <input type=\"hidden\" name=\"action\" value=\"shop_login\">
                                " . $field_1_html . $field_2_html . $password_field . "
                                
                                <input type=\"hidden\" class=\"form-control\" name=\"catalog_selected_item\" value='0' id=\"catalog_selected_item\">
                                <input type=\"hidden\" class=\"form-control\" name=\"token_1\" value='".$secretToken1."' id=\"token_1\">
                                <input type=\"hidden\" class=\"form-control\" name=\"token_2\" value='".$secretToken2."' id=\"token_2\">
                                <input type=\"hidden\" class=\"form-control\" name=\"login_type\" value='".$GLOBALS["shop"]['login_type']."' id=\"login_type\">
                                <input type=\"hidden\" class=\"form-control\" name=\"shop_typ\" value='".$GLOBALS["shop"]['shop_typ']."' id=\"shop_typ\">
                            
                                <br>
                                <div class=\"submit_button\">
                                    <input type=\"button\" id='login_buy_modal_submit_button' class=\"button\" value='" . $GLOBALS['tc']['login'] . "'>
                                </div>
                            </form>
                                
                                <div class=\"site_headline\">
                                <div class=\"textcontent\">
                                <h2>".$GLOBALS['tc']['become_customer']."</h2>
                                ".$GLOBALS['tc']['has_no_access']."<br>
                                <br>
                                 <div class=\"submit_button\">
                                        <input type=\"button\" id='register_modal_button' data-toggle=\"modal\" data-target=\"#lightbox_register1\" class=\"button\" value='".$GLOBALS['tc']['request_access']."'>
                                    </div>
                                </div>
                                </div>
                          
                          
                          </div>
                         
                          
                        </div>
                      </div>
                    </div>

                  ";
         ?>

        <div class="modal fade form-label-left" id="lightbox_register1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <? get_content("lightbox_register", TRUE, $GLOBALS['IOC']); ?>
                </div>
            </div>
        </div>
    </div>

        <?

         }



