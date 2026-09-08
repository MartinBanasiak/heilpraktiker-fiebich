<?

/*$query  = "SELECT * FROM shop_view_active_item WHERE id = '" . $_GET["card"]."'";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (empty($GLOBALS['shop_currency']['code'])) {
    $GLOBALS['shop_currency']['code'] = '';
}
if (@mysqli_num_rows($result) == 1) {
    $item               = @mysqli_fetch_array($result);
    $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
    //$item['retail_price'] = get_item_retail_price($item,$GLOBALS['shop_currency']['code']);
    $parent_item  = get_item_variant_parent($item);
    $variant_item = get_item_first_variant($item);
    if ($variant_item["id"] <> '') {
        $has_variant = TRUE;
        $parent_item = $item;
        if ($GLOBALS["shop"]["variant_typ"] == '0') {
            $item               = $variant_item;
            $item['base_price'] = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
            //$item['retail_price'] = get_item_retail_price($item,$GLOBALS['shop_currency']['code']);
        }

    } else {
        $has_variant = FALSE;
    }
    if (!empty($_GET['variant']) && $_GET['variant'] != "") {
        $query  = "SELECT * FROM shop_item_variant WHERE id = '" . $_GET['variant'] . "'";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $row    = mysqli_fetch_assoc($result);
        $code   = $row['code'];
    } else {
        $code = '';
    }
    $urlparts = explode('?', $_SERVER['REQUEST_URI']);
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        //$action = ml("","action","shop_add_item_to_basket_card","action_id",$item["id"]);
        $action = $urlparts[0] . "-p". $item['id']."/queue/?action=shop_add_item_to_basket_card&action_id=" . $item['id'];
    } else {
        //$action = ml("","action","shop_add_item_to_basket_card","action_id",$item["id"],"var_code",$code);
        $action = $urlparts[0] . "-p" . $item['id']."/queue/?action=shop_add_item_to_basket_card&action_id=" . $item['id'] . "&var_code=" . $code;
    }
}

*/

function show_item_tabs( $item, $parent_item ) {
    ?>
    <script language="javascript">
        var expectedHash = "";
        var currentLayer = "tab_content1";
        var currentTab = "tab1";
    </script>

    <?

    // Erstellen der Tabs für Beschreibungen, Zubehör und Ersatzteile
    $description_query = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                          LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
                          INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
                          LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
                          WHERE shop_item_description.show_in_header = 0
                            AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
                            AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                            AND shop_item_description.marketplace_only = 0
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
    $attributequery    = "SELECT shop_attribute_link.*,shop_attribute.description AS 'headline',shop_attribute.data_type,shop_attribute.display_type,shop_attribute.navision_value
					  FROM shop_attribute_link
					  INNER JOIN shop_attribute ON shop_attribute.code = shop_attribute_link.attribute_code
					  WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.show_on_card = 1
					  	AND shop_attribute_link.no = '" . $item['item_no'] . "'
						AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['code'] . "'
						AND shop_attribute_link.language_code='" . $GLOBALS['shop_language']['code'] . "'
						GROUP by attribute_code, value_option
					  ORDER BY attribute_code";
    $acc_item_query    = "SELECT DISTINCT
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
						WHERE
							shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
						  AND
							shop_item_link.type = 1
						ORDER BY RAND()
						LIMIT 6";
    $spare_part_query  = "SELECT DISTINCT
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
						WHERE
							shop_item_link.item_no IN ('" . $item['item_no'] . "','" . $parent_item['item_no'] . "')
						  AND
							shop_item_link.type = 2
						ORDER BY RAND()
						LIMIT 6";

    $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
    $attributeresult    = mysqli_query($GLOBALS['mysql_con'], $attributequery);
    $acc_item_result    = @mysqli_query($GLOBALS['mysql_con'], $acc_item_query);
    $spare_part_result  = @mysqli_query($GLOBALS['mysql_con'], $spare_part_query);
    if ($GLOBALS['shop_setup']['rating_active']) {
        $item_rating = get_item_rating($item);
    }
    $tab_count = 1;

    if (((@mysqli_num_rows($description_result) + @mysqli_num_rows($acc_item_result) + @mysqli_num_rows($spare_part_result) + @mysqli_num_rows($attributeresult)) > 0) || $GLOBALS['shop_setup']['rating_active']) {
        echo "<ul class=\"tab\">";
        if (@mysqli_num_rows($spare_part_result) > 0) {
            $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["spare_parts"] . "</a></li>";
            $tab_count++;
        }
        if (@mysqli_num_rows($description_result) > 0) {
            while ($description = @mysqli_fetch_array($description_result)) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";

                  if ($description["description"]  != '') {
                         echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $description["description"] . "</a></li>";
                    } else {
                       echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $description["shop_text_module_description"] . "</a></li>";
                    }

                $tab_count++;
            }
        }
        if (@mysqli_num_rows($attributeresult) > 0) {
            $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">Artikelmerkmale</a></li>";
            $tab_count++;
        }
        if (@mysqli_num_rows($acc_item_result) > 0) {
            $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["accessories"] . "</a></li>";
            $tab_count++;
        }

        // Anzahl der Bewertungen
        if ($GLOBALS['shop_setup']['rating_active']) {

            if ($item_rating["average"] > 0) {
                $rating = '';
                for ($i = 0; $i < $item_rating["average"]; $i++) {
                    $rating .= "<img src=\"/userdata/images/stern_on.png\">";
                }
                for ($i = 0; $i < (5 - $item_rating["average"]); $i++) {
                    $rating .= "<img src=\"/userdata/images/stern_off.png\">";
                }
                $rating .= '&nbsp;';
            }
            if ($item_rating['counter'] < 1) {
                $item_rating['counter'] = 0;
            }

            $tabontext = ($tab_count == $tab || $tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li  itemprop='aggregateRating' itemscope itemtype='http://schema.org/AggregateRating' id=\"tab" . $tab_count . "\"" . $tabontext . "><span  itemprop='ratingvalue' style='display:none'>" . $item_rating["average"] . "</span><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["item_comments"] . " " . $rating . "(<span itemprop='reviewCount'>" . $item_rating["counter"] . "</span>)</a></li>";
            $tab_count++;
        }
        echo "</ul>\n";

        $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
        $tab_count          = 1;
        if (@mysqli_num_rows($spare_part_result) > 0) {
            $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            show_item_list_from_query($spare_part_result, 2);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
        if (@mysqli_num_rows($description_result) > 0) {
            while ($description = @mysqli_fetch_array($description_result)) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                if ($tab_count == 1) {
                    $itemprop = "itemprop='description'";
                } else {
                    $itemprop = "";
                }

                 if ($description["shop_text_module_code"] == '') {
                         echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n  <span " . $itemprop . ">" . $description["content"] . "</span>\n</div>\n";
                 } else {
                         echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n  <span " . $itemprop . ">" . $description["shop_text_module_content"] . "</span>\n</div>\n";
                 }

                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }
        }
        if (@mysqli_num_rows($attributeresult) > 0) {
            $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            show_attribute_info($attributeresult);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
        if (@mysqli_num_rows($acc_item_result) > 0) {
            $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            show_item_list($acc_item_result, 2);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
        if ($GLOBALS['shop_setup']['rating_active']) {
            $showtext = ($tab_count == $tab || $tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            if ($GLOBALS["rating_sum"] < 1) {
                echo $GLOBALS['tc']['first_rating'] . "<br />";
            }
            require __DIR__ . DIRECTORY_SEPARATOR . 'shop_item_comments.inc.php';
            get_item_comments($item);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
    }
}

function show_header_attributes ($item, $parent_item) {

    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }

    $attributequery    = "SELECT shop_attribute_link.*,shop_attribute.description AS 'headline',shop_attribute.data_type,shop_attribute.display_type,shop_attribute.navision_value
					  FROM shop_attribute_link
					  INNER JOIN shop_attribute ON shop_attribute.code = shop_attribute_link.attribute_code
					  WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.show_on_card = 1
					  	AND shop_attribute.show_in_header = 1
					  	AND shop_attribute_link.no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
						AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['code'] . "'
						AND shop_attribute_link.language_code='" . $GLOBALS['shop_language']['code'] . "'
						GROUP by attribute_code, value_option
					  ORDER BY attribute_code";

    $attributeresult    = mysqli_query($GLOBALS['mysql_con'], $attributequery);

    if (@mysqli_num_rows($attributeresult) > 0 && $GLOBALS["shop"]["show_filter_on_card"]) {?>
        <div class="item-details-container attributes">
            <div class="item-details-content">
                <?=show_attribute_info($attributeresult)?>
            </div>
        </div>
    <? }

}


function show_item_details( $item, $parent_item ) {
    // Erstellen der Tabs für Beschreibungen, Zubehör und Ersatzteile

    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }

    $description_query = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                          LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
                          INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
                          LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
                          WHERE shop_item_description.show_in_header = 0
                            AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
                            AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                            AND (shop_item_description.content <> '' OR shop_text_module.content <> '')
                            AND shop_item_description.marketplace_only = 0
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
    $attributequery    = "SELECT shop_attribute_link.*,shop_attribute.description AS 'headline',shop_attribute.data_type,shop_attribute.display_type,shop_attribute.navision_value
					  FROM shop_attribute_link
					  INNER JOIN shop_attribute ON shop_attribute.code = shop_attribute_link.attribute_code
					  WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_attribute.show_on_card = 1
					  	AND shop_attribute.show_in_header = 2
					  	AND shop_attribute_link.no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
						AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['code'] . "'
						AND shop_attribute_link.language_code='" . $GLOBALS['shop_language']['code'] . "'
						GROUP by attribute_code, value_option
					  ORDER BY attribute_code";

    $document_query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '1'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";



    $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
    $attributeresult    = mysqli_query($GLOBALS['mysql_con'], $attributequery);
    $document_result = @mysqli_query($GLOBALS['mysql_con'], $document_query);
    if ($GLOBALS['shop_setup']['rating_active']) {
        $item_rating = get_item_rating($item);
    }

    if (((@mysqli_num_rows($description_result) + @mysqli_num_rows($attributeresult) + @mysqli_num_rows($document_result)) > 0) || $GLOBALS['shop_setup']['rating_active']) {

        //description
        $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
        if (@mysqli_num_rows($description_result) > 0) {
            while ($description = @mysqli_fetch_array($description_result)) {

                $itemDescription = $description['description'];
                if ($itemDescription == '') {
                    $itemDescription = $description['shop_text_module_description'];
                }
                $content = $description["shop_text_module_content"];
                if ($description["shop_text_module_code"] == '') {
                    $content = $description["content"];
                }
                ?>
                <div class="item-details-container">
                    <div class="item-details-headline"><h2><?=$itemDescription;?></h2></div>
                    <div class="item-details-content">
                        <?=$content?>
                    </div>
                </div>
                <?
            }
        }

        //artikelmerkmale
        if (@mysqli_num_rows($attributeresult) > 0 && $GLOBALS["shop"]["show_filter_on_card"]) {?>
            <div class="item-details-container attributes">
                <div class="item-details-headline"><h2><?=$GLOBALS['tc']['features'];?></h2></div>
                <div class="item-details-content">
                    <?=show_attribute_info($attributeresult)?>
                </div>
            </div>
        <?
        }

        //Dokumente
        if (@mysqli_num_rows($document_result) > 0) {
            echo '<div class="item-details-container">
                <div class="item-details-headline"><h2>' . $GLOBALS["tc"]["downloads"] . '</h2></div>
                <div class="item-details-content">';
                while ($document = @mysqli_fetch_array($document_result)) {
                    if (($document["filename"] <> '') && (file_exists("../.." . $GLOBALS["shop_setup"]["uploaddir_documents"] . $document["filename"]))) {
                        $button_class = get_button_file_typ($document["filename"]);
                        echo "<div class='file_button'><a class=\"" . $button_class . "\" href=\"/module/dcshop/webforms/download_file.php?file=" . $document["id"] . "\">" . $document["description"] . "</a></div>";
                    }
                }
                echo '</div>';
            echo '</div>';
        }


        //kundenbewertungen
        if ($GLOBALS['shop_setup']['rating_active']) {?>
            <div id="itemcard_comments" class="item-details-container">
                <div class="item-details-headline">
                    <h2>
                        <?if ($GLOBALS["rating_sum"] < 1) {
                            echo $GLOBALS['tc']['first_rating'];
                        }else{
                            echo $GLOBALS["tc"]["item_comments"];
                        }?>
                    </h2>
                </div>
                <div class="item-details-content">
                    <div class="item-details-content-headline">
                        <div class="row">
                            <?if($item_rating['counter'] != 0) {?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 rating_stars_wrapper">
                                    <?= show_item_rating($item, true, false, true) . " (" . $item_rating['counter'] . " " . $GLOBALS['tc']['item_comments'] . ")";?>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-8 col-lg-9 rating_button_wrapper">
                            <?}else{?>
                                <div class="col-xs-12 rating_button_wrapper">
                            <?}?>
                                <?require __DIR__ . DIRECTORY_SEPARATOR . 'shop_item_comments.inc.php';?>
                            </div>
                        </div>
                    </div>
                    <div class="item_comments">
                        <?
                        get_item_comments($item);
                        ?>
                    </div>
                </div>
            </div>
            <?
        }
    }
}

// Funktion zur Anzeige der Artikelbeschreibung
function show_item_description( $limit, $item_no, $language_code, $parent_item_no, $parent_item_language_code, $showInHeaderValue = 1) {
    $description_query  = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                          LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
						  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
						  INNER JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_description.item_no
						  WHERE shop_item_description.show_in_header = ".$showInHeaderValue."
							AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
							AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							AND shop_item_description.marketplace_only = 0
							AND ((shop_item_description.item_no = '" . $item_no . "'
								AND (shop_item_description.language_code = '" . $language_code . "'
									OR shop_item_description.all_language_codes = TRUE))
								OR (shop_item_description.item_no = '" . $parent_item_no . "'
									AND (parent_shop_item.language_code = '" . $parent_item_language_code . "'
										OR shop_item_description.all_language_codes = TRUE)))
						  ORDER BY FIND_IN_SET(shop_item_description.item_no,'" . $item_no . "," . $parent_item_no . "'),line_no";
    $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
    if (@mysqli_num_rows($description_result) > 0) {
        while ($description = @mysqli_fetch_array($description_result)) {

            $itemDescription = $description['description'];
            if ($itemDescription == '') {
                $itemDescription = $description['shop_text_module_description'];
            }

            $content = $description["content"];
            if($description["shop_text_module_code"] != '')
            {
                $content = $description["shop_text_module_content"];
            }

            if($showInHeaderValue == 1)
            {
                echo $content;
            }
            elseif ($showInHeaderValue == 2)
            {
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
function show_item_alt_item( $item, $parent_item, $no_of_items = 3, $itemlist = 2 ) {

    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }

    $query  = " SELECT DISTINCT
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
				WHERE
					shop_item_link.item_no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
				  AND
					shop_item_link.type = 3
				ORDER BY RAND()
				LIMIT " . $no_of_items;
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {?>
        <div class="item-details-container alt-item">
            <div class="item-details-headline">
                <h2><?=$GLOBALS["tc"]["related_items"]?></h2>
            </div>
            <div class="item-details-content">
                <? show_item_list_from_query($query, $itemlist, FALSE,$no_of_items);?>
            </div>
        </div>
        <?
    }
}

// Funktion zum Anzeigen von passenden Artikeln auf der Artikelkarte
function show_item_fit_item( $item, $parent_item, $no_of_items = 3, $itemlist = 2 ) {

    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }

    $query  = "SELECT DISTINCT
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
				WHERE
					shop_item_link.item_no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
				  AND
					shop_item_link.type = 4
				ORDER BY RAND()
				LIMIT " . $no_of_items;
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {?>
        <div class="item-details-container fit-item">
            <div class="item-details-headline">
                <h2><?=$GLOBALS["tc"]["references"]?></h2>
            </div>
            <div class="item-details-content">
                <?show_item_list_from_query($query, 3, FALSE,3);?>
            </div>
        </div>
    <?}
}

// Funktion zum Anzeigen von Ersatzteilen auf der Artikelkarte
function show_item_spare_parts( $item, $parent_item, $no_of_items = 3, $itemlist = 2 ) {
    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }

    $query  = "SELECT DISTINCT
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
				WHERE
					shop_item_link.item_no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
				  AND
					shop_item_link.type = 2
				ORDER BY RAND()
				LIMIT " . $no_of_items;
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {?>
        <div class="item-details-container alt-item">
            <div class="item-details-headline">
                <h2><?=$GLOBALS["tc"]["related_items"]?></h2>
            </div>
            <div class="item-details-content">
                <?show_item_list_from_query($query, $itemlist, FALSE,6);?>
            </div>
        </div>
        <?
    }
}

// Funktion zum Anzeigen von Ersatzteilen auf der Artikelkarte
function show_item_acc_items( $item, $parent_item, $no_of_items = 3, $itemlist = 2 ) {
    $parentItemNoQuery = "";
    if (!empty($parent_item['item_no'])) {
        $parentItemNoQuery = ", '" . $parent_item['item_no'] . "'";
    }
    $query  = "SELECT DISTINCT
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
				WHERE
					shop_item_link.item_no IN ('" . $item['item_no'] . "'" . $parentItemNoQuery . ")
				  AND
					shop_item_link.type = 1
				ORDER BY RAND()
				LIMIT " . $no_of_items;
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {?>
        <div class="item-details-container alt-item">
            <div class="item-details-headline">
                <h2><?=$GLOBALS["tc"]["related_items"]?></h2>
            </div>
            <div class="item-details-content">
                <?show_item_list_from_query($query, $itemlist, FALSE,6);?>
            </div>
        </div>
        <?
    }
}

// Funktion zum Anzeigen der Artikel-Bildergalerie auf der Artikelkarte
function show_item_images( $item, $parent_item, $image_config,  $customized = 0) {

    if($customized)
    {
          $main_image = get_item_main_customization_image($item, $parent_item);
    }
   else
   {
            $main_image = get_item_main_image($item, $parent_item);
   }

    if (($main_image["filename"] == '') | (!file_exists("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]))) {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            $main_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
        } else {
            $main_image["filename"] = "noimage.jpg";
        }
    }
    $imagesize = getimagesize("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]);
    $padding   = round((($GLOBALS[image_config][3]["maxheight"] - $imagesize[1]) / 2));
    $imageQuery     = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = " . \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_IMAGE . "
			    AND shop_item_file.customization = '".$customized."'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $videoQuery  = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = " . \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_VIDEO_FILE . "
                AND shop_item_file.customization = '".$customized."'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND shop_item_file.mp4 = 100
			  	AND shop_item_file.webm = 100
			  	AND shop_item_file.ogg = 100
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $youtubeIDQuery = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = " . \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_YOUTUBE_ID . "
                AND shop_item_file.customization = '".$customized."'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'			  	
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $magic360Query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = " . \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE . "
                AND shop_item_file.customization = '".$customized."'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'			  	
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $videoResult = @mysqli_query($GLOBALS['mysql_con'], $videoQuery);
    $youtubeIDResult = @mysqli_query($GLOBALS['mysql_con'], $youtubeIDQuery);
    $magic360Result = @mysqli_query($GLOBALS['mysql_con'],$magic360Query);
    ?>
    <div class="item_images_container">
        <div class="itemcard_banner"><?=show_item_promotion_banners($item);?></div>
        <div class="item_main_image">
            <div class="item_main_container magic-box" id="zoom-box">
                <a  href="<?=$image_config[4]["path"];?>/<?=$main_image["filename"];?>" class="MagicZoomPlus" id="zoom"
                   data-options="
                   textHoverZoomHint: <?=$GLOBALS['tc']['textHoverZoomHint'];?>;
                   textClickZoomHint: <?=$GLOBALS['tc']['textClickZoomHint'];?>;
                   textExpandHint:  <?=$GLOBALS['tc']['textExpandHint'];?>;
                   textBtnClose:  <?=$GLOBALS['tc']['textBtnClose'];?>;
                   textBtnNext:  <?=$GLOBALS['tc']['textBtnNext'];?>;
                   textBtnPrev:  <?=$GLOBALS['tc']['textBtnPrev'];?>;
                   textTouchZoomHint: <?=$GLOBALS['tc']['textTouchZoomHint'];?>;
                   textClickZoomHint: <?=$GLOBALS['tc']['textClickZoomHint'];?>;
                   textExpandHint: <?=$GLOBALS['tc']['textExpandHint'];?>;
                   hint: off;
                   zoomPosition: inner;
                   zoomMode: off;
                   cssClass: white-bg; ">
                    <img itemprop="image" id="main_image_item_<?= $customized ?>"  src="<?=$image_config[3]["path"];?>/<?=$main_image["filename"];?>" alt="<?= $main_image["description"] ?>" title="<?= $main_image["description"] ?>" />
                </a>
            </div>

            <?
                if (@mysqli_num_rows($videoResult) > 0 || @mysqli_num_rows($youtubeIDResult) > 0) {
             ?>

            <div class="item_video_container">
                <div class="item_video_container_inner" id="video-box">
                    <?
                }

                if (@mysqli_num_rows($videoResult) > 0) {


                    while ($video = mysqli_fetch_array($videoResult)){
                        $videoFileName = pathinfo($video["filename"], PATHINFO_FILENAME);
                        $videoId = $video["id"];

                    ?>
                    <div id="video_<?= $videoId ?>" class="video_container">
                        <video id="video" class="mejs__player" poster="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>thumb/<?= $videoFileName ?>.jpg"
                        controls preload="none" data-mejsoptions='{"enableAutosize": false, "stretching": "responsive"}'>
                            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>mp4/<?= $videoFileName ?>.mp4" type="video/mp4">
                            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>webm/<?= $videoFileName ?>.webm" type="video/webm">
                            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>ogg/<?= $videoFileName ?>.ogg" type="video/ogg">
                        </video>
                    </div>
                    <?
                    }

                }

            if (@mysqli_num_rows($youtubeIDResult) > 0) {
                while ($video = mysqli_fetch_array($youtubeIDResult)){
                $videoId = $video["id"];
                $youtubeID = $video["youtube_video_id"];
                ?>
                <div id="video_<?= $videoId ?>" class="video_container">
                    <div class="embed-container">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $youtubeID ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <?
                }
            }

            if (@mysqli_num_rows($videoResult) > 0 || @mysqli_num_rows($youtubeIDResult) > 0) {

                ?>
                </div>
            </div>
            <?
            }
            $numRows360 = @mysqli_num_rows($magic360Result);
            if ($numRows360 > 0) {
                $html = "
                        <script type=\"text/javascript\">
                        function switchZoom360Example(elm) {
                                var zoomID = elm.getAttribute('data-zoom-id');
                                var zoomIDEl = $('#' + zoomID);
                                var isSpin = zoomID.startsWith('spin');
                                if (!isSpin) {
                                    $('#spin-box').hide();
                                    $('#zoom-box').show();
                                } else {
                                    $('#zoom-box').hide();
                                    $('#spin-box').show();
                                    $(zoomIDEl).siblings().each(function(){
                                        this.hide();
                                    });
                                    $(zoomIDEl).show();                                  
                                }
                                return false;
                        }
                        </script>
                        <style type=\"text/css\" src=\"/plugins/magic360/magic360/magic360.css\"></style>
                        <script type=\"text/javascript\" src=\"/plugins/magic360/magic360/magic360.js\"></script>
                        <div id=\"spin-box\">
                ";
                $i = 0;
                while ($magic360Row = mysqli_fetch_assoc($magic360Result)) {

                    $i++;
                    $dirName = $magic360Row['filename'];
                    $is_main_medium = $magic360Row['main_medium'];
                    $is_main_medium_text = ' data-main-medium="0" ';
                    if ($is_main_medium) {
                        $is_main_medium_text = ' data-main-medium="1" ';
                    }
                    $urlPathDir = $GLOBALS['shop_setup']['uploaddir_360_degree_images'] . DIRECTORY_SEPARATOR . $dirName;
                    $fsPathDir = rtrim(dirname(dirname(dirname(__DIR__))),'\//') . $urlPathDir;
                    $dirIt = new DirectoryIterator($fsPathDir);
                    $filePathArray = [];
                    foreach ($dirIt as $fileinfo) {
                        if (!$fileinfo->isDot()) {
                            $urlPathImg = $urlPathDir . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
                            $filePathArray[] = $urlPathImg;
                        }
                    }
                    sort($filePathArray);
                    $firstImagePath = $filePathArray[0] ?? '';
                    $imagesList = '';
                    foreach ($filePathArray as $fileURL) {
                        $imagesList .= " $fileURL";
                    }
                    $html .= "                        
                        <a class=\"Magic360 magicZoomAchors\" id=\"spin_$i\" data-magic360-options=\"images: $imagesList\"><img src=\"" . $firstImagePath . "\"></a>
                        ";
                }
                $html .= "</div>";
                echo $html;
            }

            ?>
        </div>
        <div class="item_images MagicScroll">
            <?php
            $imageResult = @mysqli_query($GLOBALS['mysql_con'], $imageQuery);

            if (@mysqli_num_rows($imageResult) > 1 || (@mysqli_num_rows($videoResult) > 0 || @mysqli_num_rows($youtubeIDResult) > 0)) {
                while ($tumb_image = @mysqli_fetch_array($imageResult)) {
                    $is_main_medium = (bool)($tumb_image['main_medium'] ?? false);
                    $is_main_medium_text = ' data-main-medium="0" ';
                    if ($is_main_medium) {
                        $is_main_medium_text = ' data-main-medium="1" ';
                    }
                    if (($tumb_image["filename"] <> '') && (file_exists("../../" . $image_config[1]["path"] . "/" . $tumb_image["filename"])) && (file_exists("../../" . $image_config[3]["path"] . "/" . $tumb_image["filename"]))) {?>
                        <a class="magicZoomAchors" onclick="return switchZoom360Example(this);" ontouchstart="return switchZoom360Example(this);"  href="<?=$image_config[4]["path"] . "/" . $tumb_image["filename"]?>" <?= $is_main_medium_text ?> data-zoom-id="zoom" data-image="<?=$image_config[3]["path"] . "/" . $tumb_image["filename"]?>">
                            <img src="<?=$image_config[1]["path"] . "/" . $tumb_image["filename"];?>" alt="<?=$tumb_image["description"];?>" title="<?=$tumb_image["description"];?>" />
                        </a>
                        <?
                    }
                }
            }

            mysqli_data_seek($videoResult,0);
            if (@mysqli_num_rows($videoResult) > 0) {
                while ($video = mysqli_fetch_array($videoResult)){
                    $videoFileName = pathinfo($video["filename"], PATHINFO_FILENAME);
                    $videoThumb = $GLOBALS["shop_setup"]["uploaddir_videos"] . "thumb/" . $videoFileName . ".jpg";
                    $videoId = $video["id"];
                    $is_main_medium = (bool)($video['main_medium'] ?? false);
                    $is_main_medium_text = ' data-main-medium="0" ';
                    if ($is_main_medium) {
                        $is_main_medium_text = ' data-main-medium="1" ';
                    }
                    ?>
                    <div>
                        <a class="magicZoomAchors" onclick="return switchZoom360Example(this);" ontouchstart="return switchZoom360Example(this);"  data-zoom-id="zoom"  href="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?><?= $videoFileName ?>"" <?= $is_main_medium_text ?> data-videoId="<?= $videoId ?>" data-videoPath="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>" data-videoName="<?= $videoFileName ?>" data-selector="#video" class="white-bg mz-thumb video-select">
                            <img src="<?=$videoThumb ?>" alt="<?=$video["description"];?>" title="<?=$video["description"];?>" />
                        </a>
                    </div>
                    <?
                }
            }

            mysqli_data_seek($youtubeIDResult,0);
            if (@mysqli_num_rows($youtubeIDResult) > 0) {
                while ($video = mysqli_fetch_array($youtubeIDResult)){
                    $youtubeID = $video["youtube_video_id"];
                    $videoThumb = "https://img.youtube.com/vi/".$youtubeID."/default.jpg";
                    $videoId = $video["id"];
                    $is_main_medium = (bool)($video['main_medium'] ?? false);
                    $is_main_medium_text = ' data-main-medium="0" ';
                    if ($is_main_medium) {
                        $is_main_medium_text = ' data-main-medium="1" ';
                    }
                    ?>
                    <div>
                        <a onclick="return switchZoom360Example(this);" ontouchstart="return switchZoom360Example(this);" data-zoom-id="zoom" href="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?><?= $videoFileName ?>" <?= $is_main_medium_text ?> data-videoId="<?= $videoId ?>" data-videoPath="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>" data-videoName="<?= $videoFileName ?>" data-selector="#video" class="white-bg mz-thumb video-select">
                            <img src="<?=$videoThumb ?>" alt="<?=$video["description"];?>" title="<?=$video["description"];?>" />
                        </a>
                    </div>
                    <?php
                }
            }

            mysqli_data_seek($magic360Result,0);
            if (@mysqli_num_rows($magic360Result) > 0) {
               $html = "";
                $i = 0;
                while ($magic360Row = mysqli_fetch_assoc($magic360Result)) {

                    $i++;
                    $dirName = $magic360Row['filename'];
                    $is_main_medium = $magic360Row['main_medium'];
                    $is_main_medium_text = ' data-main-medium="0" ';
                    if ($is_main_medium) {
                        $is_main_medium_text = ' data-main-medium="1" ';
                    }
                    $urlPathDir = $GLOBALS['shop_setup']['uploaddir_360_degree_images'] . DIRECTORY_SEPARATOR . $dirName;
                    $fsPathDir = rtrim(dirname(dirname(dirname(__DIR__))),'\//') . $urlPathDir;
                    $dirIt = new DirectoryIterator($fsPathDir);
                    $filePathArray = [];
                    foreach ($dirIt as $fileinfo) {
                        if (!$fileinfo->isDot()) {
                            $urlPathImg = $urlPathDir . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
                            $filePathArray[] = $urlPathImg;
                            break;
                        }
                    }
                    sort($filePathArray);
                    $firstImagePath = $filePathArray[0] ?? '';
                    if ($firstImagePath !== '') {
                        $html .="
                        <div>
                            <a  class=\"magicZoomAchors mz-thumb\" $is_main_medium_text onclick=\"return switchZoom360Example(this);\" ontouchstart=\"return switchZoom360Example(this);\" data-zoom-id=\"spin_$i\" href=\"#\"><img src=\"$firstImagePath\" alt=\"\"/></a>
                        </div>
                        ";
                    }
                }
                echo $html;
            }

            ?>
        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
       $('*[data-main-medium="1"]').click();
    });
    </script>

    <?
}
// Funktion zum Anzeigen der Artikel-Videogalerie auf der Artikelkarte
function show_item_videos( $item, $parent_item, $uploaddir_videos ) {
    $query  = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '2'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND shop_item_file.mp4 = 100
			  	AND shop_item_file.webm = 100
			  	AND shop_item_file.ogg = 100
			  	AND ((shop_item.item_no = '" . $item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($video = mysqli_fetch_array($result)){
            $videoFileName = pathinfo($video["filename"], PATHINFO_FILENAME);
            ?>
            <video id="video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="300" height="150"
            poster="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>thumb/<?= $videoFileName ?>.jpg" data-setup="{}">
            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>mp4/<?= $videoFileName ?>.mp4" type="video/mp4">
            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>webm/<?= $videoFileName ?>.webm" type="video/webm">
            <source src="<?= $GLOBALS["shop_setup"]["uploaddir_videos"] ?>webm/<?= $videoFileName ?>.ogg" type="video/wogg">
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
            </video>
            <?
        }
    }
}

// Funktion zum Anzeigen der Artikel-Dokumente auf der Artikelkarte
function show_item_documents( $item, $parent_item, $uploaddir_documents ) {
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '1'
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
        echo '<div class="item-details-container">
            <div class="item-details-headline"><h2>' . $GLOBALS["tc"]["downloads"] . '</h2></div>
            <div class="item-details-content">';
            while ($document = @mysqli_fetch_array($result)) {
                if (($document["filename"] <> '') && (file_exists("../.." . $uploaddir_documents . $document["filename"]))) {
                    $button_class = get_button_file_typ($document["filename"]);
                    echo "<div><a class=\"" . $button_class . "\" href=\"/module/dcshop/webforms/download_file.php?file=" . $document["id"] . "\">" . $document["description"] . "</a></div>";
                }
            }
            echo '</div>';
        echo '</div>';
    }
}

// Funktionen zum anzeigen von Artikelvarianten
function variant_select( $item ) {
    $parent_item = get_item_variant_parent($item);
    if ($parent_item["id"] <> '') {
        $query = "SELECT DISTINCT shop_view_active_item.*
        		  FROM shop_item_link
        		  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
        		  WHERE shop_item_link.type = '0'
        		  	AND shop_item_link.item_no = '" . $parent_item["item_no"] . "'
        		  	AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  	AND shop_view_active_item.shop_code= '" . $parent_item["shop_code"] . "'
        		  	AND shop_view_active_item.language_code = '" . $parent_item["language_code"] . "'
        		  	ORDER BY shop_view_active_item.base_price ASC";
    } else {
        $query = "SELECT DISTINCT shop_view_active_item.*
        		  FROM shop_item_link
        		  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
        		  WHERE shop_item_link.type = '0'
	        		AND shop_item_link.item_no = '" . $item["item_no"] . "'
	        		AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        		  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
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
                $descr    = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                if (isset($_GET["shop_category"]) && !empty($_GET["shop_category"])) {
                    $itemlink = "?var=true";
                } else {
                    $itemlink = "?var=true";
                }
                echo "<tr onclick=\"window.location.href = '" . $itemlink . "'\"><td>" .
                    $descr . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" .
                    $item["item_no"] . "</td><td>";
                get_inventory_sign($item);
                echo "</td></tr>";
            }
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_itemlink = "?var=true";
                $descr            = ($variant_item["variant_type"] == "") ? $variant_item["description"] : $variant_item["variant_type"];
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

// Variantenauswahl
function variant_select_nav( $item ) {
    $var_query  = "SELECT *
				  FROM shop_item_variant
				  WHERE item_no = '" . $item['item_no'] . "'
				  AND company = '" . $GLOBALS['shop']['company'] . "'";
    $var_result = mysqli_query($GLOBALS['mysql_con'], $var_query);
    if (mysqli_num_rows($var_result) > 0) {
        ?>
        <select class='select' name='input_variant'
                onchange="location.href= '?var=true&variant='+this.options[this.selectedIndex].value;">
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
                if ($description = '') {
                    $description = $var_row['description'];
                }
                echo("<option value=" . $var_row['id'] . $selected . ">" . $description . "</option>");
            }
            ?>
        </select><br /><br />
    <?
    }
}

// Artikelmerkmale anzeigen
function show_attribute_info( $attributeresult ) {
    echo("<div class='attributes'>");
    $last_attribute_name = "";
    $last_attribute_value = "";
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
        echo "<div class='row'>";
        echo("<div class='col-xs-12 col-sm-6 col-md-4 col-lg-3 attribute_code'>");
        if ($attribute['headline'] != '') {
            $translation = get_attribute_translation($attribute['attribute_code'], $attribute['type']);
            if (!$translation) {
                echo $attribute['headline'];
            } else {
                echo $translation;
            }
        }
        echo("</div>");

        if ($attribute['headline'] != '') {
            $last_attribute_name = $attribute['headline'];
        }

        if ($attribute['display_type'] != 5) {
            if ($attribute['navision_value'] == 0) {
                if ($attribute['data_type'] == 0) {
                    $option = get_option($attribute['attribute_code'], $attribute['value_option']);
                    $text   = get_attribute_value_translation($attribute['attribute_code'], 2, $attribute['value_option']);
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
            echo("<div class='col-xs-12 col-sm-6 col-md-8 col-lg-9 attribute_text'>" . $text . "</div>");
            $last_attribute_value = $text;
        } else {
            $option = get_option($attribute['attribute_code'], $attribute['value_option']);
            $last_attribute_value = $option['description'];
            echo("<div class='col-xs-12 col-sm-6 col-md-8 col-lg-9 attribute_text'><img src = '" . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_filter_icon"] . $option['icon'] . "' alt='" . $option['description'] . "'></div>");
        }
        echo "</div>";
    }

    echo("</div>");
}

function show_button_back()
{
    if ($_SESSION['search'] != "") { ?>
        <div class="toolbar">
            <a class="button_back"
               href="/<? echo customizeUrl(); ?>/search/?term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
        </div>
        <?
    } else {
        $backurl = $_SERVER["REDIRECT_URL"];
        $backurl = explode('/', $backurl, -1);
        $arrcount = count($backurl);
        $arrcount--;
        $arrcount--;
        $i = 0;
        $back_url = "<a class='button_back' href='";
        while ($i < $arrcount) {
            $i++;
            $back_url .= "/" . $backurl[$i];
        }
        $back_url .= "/'>" . $GLOBALS['tc']['back_itemcard'] . "</a>";
        if ($GLOBALS['category'] == NULL && $_SESSION['search'] == NULL) {
            echo "<div class='toolbar'><a class='button_back' href='/" . customizeUrl() . "/'>" . $GLOBALS['tc']['back_startpage'] . "</a></div>";
        } else {
            echo '<div class="toolbar">' . $back_url . '</div>';
        }
    }
}


function show_price_notice() {
    ?>
    <div class='price_notice'><?= $GLOBALS["tc"]["value_VAT" . $item['vat_prod_posting_group']] ?></div>
    <? if ($item["minimum_order_quantity"] > 0) { ?>
        <div class='itemcard_minimum'>
            <div class='itemcard_minimum_label'><?= $GLOBALS["tc"]["minimum_quantity"] ?></div>
            <div class='itemcard_minimum_value'><?= $item["minimum_order_quantity"] ?></div>
        </div>
    <? } ?>
    <? if ($item["order_per_packing_unit"] == 1 && $item["quantity_packing_unit"] > 1) { ?>
        <div class='itemcard_packing_unit'>
            <div class='itemcard_packing_unit_label'><?= $GLOBALS["tc"]["packing_unit"] ?></div>
            <div class='itemcard_packing_unit_value'><?= $item["quantity_packing_unit"] ?></div>
        </div>
    <? }
}

//Hersteller Logo für die Itemkarte
function get_brand_logo( $item ) {
    $query  = "SELECT * FROM shop_attribute_link WHERE shop_code = '" . $GLOBALS['shop']['item_source'] . "' and language_code = '" . $GLOBALS['shop_language']['code'] . "' and attribute_code = '" . $GLOBALS['shop']['attribute_brand'] . "' AND (NO != '') AND (NO = '" . $item['item_no'] . "' OR NO = '" . $item['parent_item_no'] . "')";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = mysqli_fetch_object($result)) {
        $brand_code = $row->value_option;
        $query_2    = "SELECT * FROM shop_attribute_option WHERE code = '" . $brand_code . "'";
        $result_2   = mysqli_query($GLOBALS['mysql_con'], $query_2);
        while ($row = mysqli_fetch_object($result_2)) {
            $brand_name = $row->description;
            $brand_logo = $row->icon;
            $brand_url  = $row->link;
        }
    }
    if ($brand_logo) {
        //gibt img url zurück falls Hersteller logo vorhanden
        if ($brand_url != NULL) {
            return "<div class=\"itemcard_brand_logo\"><a href='" . $brand_url . "'><img src=\"/userdata/dcshop/filter_icon/" . $brand_logo . "\" border=\"0\" title='".$brand_name."' alt='".$brand_name."'></a></div>";
        } else {
            return "<div class=\"itemcard_brand_logo\"><img src=\"/userdata/dcshop/filter_icon/" . $brand_logo . "\" border=\"0\"  title='".$brand_name."' alt='".$brand_name."' ></div>";
        }
    } else {
        return '';
    }
}

function item_obj_to_itemcard_array(\DynCom\dc\dcShop\interfaces\WebshopItemInterface $item)
{
    $IOCContainer = $GLOBALS['IOC'];
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    /**
* @var $shopConfig \DynCom\dc\dcShop\classes\CurrShopConfiguration
 */
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $attributeService = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemAttributeService');
    $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');



    /**
    *@var $itemBuilder \DynCom\dc\dcShop\classes\WebshopItemBuilder
     */
    $itemObj = $itemBuilder->decorateWebshopItemForItemList($item);

    $attrCollection = $attributeService->getAllForItemByPrimary($itemObj->getCompany(),$shopConfig->getShopCode(),$shopConfig->getShopLanguageCode(),$shopConfig->getShop()->default_language_code,$itemObj->getItemNo(),$itemObj->getVariantCode());
    /**
    * @var $advancedPriceProvider \DynCom\dc\dcShop\classes\AdvancedPriceProvider
    */
    $graduatedPrices= $advancedPriceProvider->getGraduatedPrices($item,$shopConfig->getCustomer(),$shopConfig->getCurrencyCode());
    $graduatedPricesArray = $graduatedPrices->toArray();

    $itemArr = [];
    $itemArr['attribute_collection'] = $attrCollection;
    $itemArr['id'] = $itemObj->getID();
    $itemArr['company'] = $itemObj->getCompany();
    $itemArr['shop_code'] = $itemObj->getShopCode();
    $itemArr['language_code'] = $itemObj->getLanguageCode();
    $itemArr['item_no'] = $itemObj->getItemNo();
    $itemArr['parent_item_no'] = $itemObj->getParentItemNo();
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
    $itemArr['currency_code'] = $itemObj->getCurrencyCode(); //Works because item is wrapped as OrderableEntity first
    $itemArr['base_unit_of_measure'] = $itemObj->getBaseUnitOfMeasure();
    $itemArr['graduated_prices'] = $graduatedPricesArray;
    $itemArr['item_obj'] = $itemObj;
    $itemArr['item_slug'] = $itemObj->getItemSlug();
    $itemArr['customizable'] = $itemObj->customizable;
    return $itemArr;
}

?>