<?
$query = "SELECT * FROM shop_view_active_item WHERE id = '" . $_GET["card"]."'";

$result = @mysqli_query($GLOBALS['mysql_con'], $query);

if (@mysqli_num_rows($result) == 1) {
$item                 = @mysqli_fetch_array($result);




$item['base_price']   = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
$item['retail_price'] = get_item_retail_price($item, $GLOBALS['shop_currency']['code']);
$parent_item          = get_item_variant_parent($item);
$variant_item         = get_item_first_variant($item);
if ($variant_item["id"] <> '') {
    $has_variant = TRUE;
    $parent_item = $item;
    if ($GLOBALS["shop"]["variant_typ"] == '0') {
        $item                 = $variant_item;
        $item['base_price']   = get_item_base_price($item, $GLOBALS['shop_currency']['code']);
        $item['retail_price'] = get_item_retail_price($item, $GLOBALS['shop_currency']['code']);
    }

} else {
    $has_variant = FALSE;
}
?>
<? if ($_SESSION['search'] != "") { ?>
    <div class="toolbar">
        <a class="button_back"
           href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=search&term=<?= $_SESSION['search'] ?>"><?= $GLOBALS["tc"]["back_to_search"] ?></a>
    </div>
<? } ?>
<br />
    <div id="itemcard">
        <div id="itemcard_left">
            <? show_item_promotion_banners($item); ?>
            <? show_item_images($item, $parent_item, $GLOBALS['shop_setup']['image_config']); ?>
            <? show_item_videos($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_videos"]) ?>
            <? show_item_documents($item, $parent_item, $GLOBALS["shop_setup"]["uploaddir_documents"]) ?>
        </div>
        <div id="itemcard_right">
            <h1>
                <?
                echo stripslashes($item["description"]);
                ?>
            </h1>
            <?
            if ($item["summary"] != '') {
                echo $item["summary"];
                echo "<br />";
            }
            ?>
            <br />
            <? //Anzeige von markierten Artikelbeschreibungen direkt unter dem Artikelnamen
            createDescription(1, $item["item_no"], $item["language_code"], $parent_item["item_no"], $parent_item["language_code"]);
            ?>
            </br />
            <?
            if ($GLOBALS['shop']['variant_typ'] != '2') {
                variant_select($item);
            } else {
                variant_select_nav($item);
                if (isset($_GET['variant']) && $_GET['variant'] != "") {
                    $query  = "SELECT * FROM shop_item_variant WHERE id = '" . $_GET['variant'] . "'";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    $row    = mysqli_fetch_assoc($result);
                    $code   = $row['code'];
                } else {
                    $code = '';
                }
            }
            ?>

            <?php
            include __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card_details.inc.php';
            ?>


            <br />
        </div>
    </div>
    <div class="clearfloat"></div>
<div id="itemcard_bottom">
    <script language="javascript">
        var expectedHash = "";
        var currentLayer = "tab_content1";
        var currentTab = "tab1";
    </script>

    <?

    // Erstellen der Tabs für Beschreibungen, Zubehör und Ersatzteile
    $description_query = "SELECT DISTINCT shop_item_description.*
					  FROM shop_item_description
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
    $acc_item_query    = "SELECT DISTINCT
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
    $spare_part_query  = "SELECT DISTINCT
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
							shop_item_link.type = 2
						" . get_permissions_group_customer() . "
						ORDER BY RAND()
					   LIMIT 6";

    $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
    $acc_item_result    = @mysqli_query($GLOBALS['mysql_con'], $acc_item_query);
    $spare_part_result  = @mysqli_query($GLOBALS['mysql_con'], $spare_part_query);
    $tab_count          = 1;

    if ((@mysqli_num_rows($description_result) + @mysqli_num_rows($acc_item_result) + @mysqli_num_rows($spare_part_result)) > 0) {
        echo "<ul class=\"tab\">";
        if (@mysqli_num_rows($description_result) > 0) {
            while ($description = @mysqli_fetch_array($description_result)) {
                $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
                echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $description["description"] . "</a></li>";
                $tab_count++;
            }
        }
        if (@mysqli_num_rows($acc_item_result) > 0) {
            $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["accessories"] . "</a></li>";
            $tab_count++;
        }
        if (@mysqli_num_rows($spare_part_result) > 0) {
            $tabontext = ($tab_count == 1) ? " class=\"tabon\"" : " class=\"taboff\"";
            echo "<li id=\"tab" . $tab_count . "\"" . $tabontext . "><a href=\"javascript:void(0);\" onclick=\"showLayer('tab_content" . $tab_count . "');showTab('tab" . $tab_count . "');\">" . $GLOBALS["tc"]["spare_parts"] . "</a></li>";
            $tab_count++;
        }
        echo "</ul>\n";


        $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);
        $tab_count          = 1;

        if (@mysqli_num_rows($description_result) > 0) {
            while ($description = @mysqli_fetch_array($description_result)) {
                $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
                echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n" . $description["content"] . "\n</div>\n";
                //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
                $tab_count++;
            }
        }
        if (@mysqli_num_rows($acc_item_result) > 0) {
            $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            show_item_list_from_query($acc_item_result, 1);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
        if (@mysqli_num_rows($spare_part_result) > 0) {
            $showtext = ($tab_count == 1) ? " class=\"show\"" : " class=\"hide\"";
            echo "<div" . $showtext . " class=\"hide\" id=\"tab_content" . $tab_count . "\">\n";
            show_item_list_from_query($spare_part_result, 1);
            echo "</div>\n";
            echo "<div class=\"clearfloat\"></div>\n";
            //echo "<a id=\"_tab_content" . $tab_count . "\" name=\"_tab_content" . $tab_count . "\"></a>\n";
            $tab_count++;
        }
    }
    }
    ?>

</div>
<? show_item_fit_item($item, $parent_item); ?>
<? show_item_alt_item($item, $parent_item); ?>
<?

// Bluestar HT: Funktion zur Anzeige der Artikelbeschreibung
function createDescription( $limit, $item_no, $language_code, $parent_item_no, $parent_item_language_code ) {
    $description_query  = "SELECT DISTINCT shop_item_description.*
							  FROM shop_item_description
							  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
							  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
							  WHERE shop_item_description.show_in_header = 1
							  	AND shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
							  	AND shop_item_description.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							  	AND shop_item_description.marketplace_only = 0
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
            echo $description["content"] . "<br>";
        }
    }
}

// Funktion zum Anzeigen von Alternativartikeln auf der Artikelkarte
function show_item_alt_item( $item, $parent_item ) {
    $query  = "SELECT DISTINCT
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

function show_item_fit_item( $item, $parent_item ) {
    $query  = "SELECT DISTINCT
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
function show_item_images( $item, $parent_item, $image_config ) {
    $main_image = get_item_main_image($item, $parent_item);
    if (($main_image["filename"] == '') | (!file_exists("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]))) {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            $main_image["filename"] = $GLOBALS['shop_language']['item_placeholder_image'];
        } else {
            $main_image["filename"] = "noimage.jpg";
        }
    }
    $imagesize = getimagesize("../../" . $image_config[3]["path"] . "/" . $main_image["filename"]);
    $padding   = round((($GLOBALS[image_config][3]["maxheight"] - $imagesize[1]) / 2));
    //echo "<div class=\"item_main_image\"><div class=\"item_main_image_layer1\"><a href=\"javascript:void(0);\"  onmouseover=\"TagToTip('zoom_picture')\" onmouseout=\"UnTip()\" onclick=\"window.open('/module/navshop_b2b/image_popup.php?item=" . $item["id"] . "&image=" . $main_image["id"] . "','popup','width=600, height=550, scrollbars=no')\"><img border=\"0\" name=\"item_main_picture\" src=\"" . $image_config[3]["path"] . "/" . $main_image["filename"] . "\" alt=\"" . $main_image["description"] . "\" /></a></div>";

    echo "<div class=\"item_main_image\"><div class=\"item_main_image_layer1\"><a href=\"" . $image_config[4]["path"] . "/" . $main_image["filename"] . "\" class=\"MagicZoomPlus\" id=\"zoom\"  rel=\"disable-zoom: true\"><img border=\"0\" name=\"item_main_picture\" src=\"" . $image_config[3]["path"] . "/" . $main_image["filename"] . "\" alt=\"" . $main_image["description"] . "\" title=\"" . $main_image["description"] . "\" /></a></div>";
    echo "<div id=\"zoom-big\"></div>";
    //if (($item["no_of_campain"] > 0) | ($parent_item["no_of_campain"] > 0)) {
    //echo "<div onmouseover=\"TagToTip('zoom_picture')\" onmouseout=\"UnTip()\" onclick=\"window.open('/module/navshop_b2b/image_popup.php?item=" . $item["id"] . "&image=" . $main_image["id"] . "','popup','width=600, height=550, scrollbars=no')\" class=\"item_main_image_layer2\">&nbsp;</div>";
    //	echo "<div class=\"item_main_image\"><div class=\"item_main_image_layer1\"><a href=\"".$image_config[4]["path"]."/".$main_image["filename"]."\" class=\"MagicZoomPlus\" id=\"zoom\"  rel=\"zoom-position: custom; zoom-width: 360px; zoom-height: 300px; zoom-fade:true; smoothing-speed:17; background-opacity:10; loading-msg: Lade Zoom-Bild...;\"><img border=\"0\" name=\"item_main_picture\" src=\"" . $image_config[3]["path"] . "/" . $main_image["filename"] . "\" alt=\"" . $main_image["description"] . "\" /></a></div>";
    //}
    echo "</div>\n";
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no			  
			  WHERE shop_item_file.type = '0'
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
    if (@mysqli_num_rows($result) > 1) {
        echo "<div class=\"item_images\">";
        while ($tumb_image = @mysqli_fetch_array($result)) {
            if (($tumb_image["filename"] <> '') && (file_exists("../../" . $image_config[1]["path"] . "/" . $tumb_image["filename"])) && (file_exists("../../" . $image_config[3]["path"] . "/" . $tumb_image["filename"]))) {
                //echo "<a href=\"javascript:void(0);\" onclick=\"window.open('/module/navshop_b2b/image_popup.php?item=" . $item["id"] . "&image=" . $tumb_image["id"] . "','popup','width=600, height=550, scrollbars=no')\" onmouseout=\"MM_swapImgRestore();UnTip()\" onmouseover=\"MM_swapImage('item_main_picture','','" . $image_config[3]["path"] . "/" . $tumb_image["filename"] . "',1);TagToTip('zoom_picture')\"><img border=\"0\" src=\"" . $image_config[1]["path"] . "/" . $tumb_image["filename"] . "\" alt=\"" . $tumb_image["description"] . "\" /></a>\n";
                echo "<div><a href=\"" . $image_config[4]["path"] . "/" . $tumb_image["filename"] . "\" rel=\"zoom-id:zoom;thumb-id:zoom;\" rev=\"" . $image_config[3]["path"] . "/" . $tumb_image["filename"] . "\" ><img border=\"0\" src=\"" . $image_config[1]["path"] . "/" . $tumb_image["filename"] . "\" alt=\"" . $tumb_image["description"] . "\" /></a></div>\n";
            }
        }
        echo "<div class='clearfloat'></div>";
        echo "</div>";
    }
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
			  		AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = 1))
			  	OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "'
			  		AND (shop_item_file.language_code = '" . $parent_item["language_code"] . "' OR shop_item_file.all_language_codes = 1)))
				AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        echo "<div class=\"infotext\">" . $GLOBALS["tc"]["downloads"] . "</div>\n";
        echo "<div class=\"item_documents\"><ul class=\"buttonlist\">\n";
        while ($document = @mysqli_fetch_array($result)) {
            if (($document["filename"] <> '') && (file_exists("../.." . $uploaddir_documents . $document["filename"]))) {
                $button_class = get_button_file_typ($document["filename"]);
                echo "<li><a class=\"" . $button_class . "\" href=\"/module/dcshop/webforms/download_file.php?file=" . $document["id"] . "\">" . $document["description"] . "</a></li>";
            }
        }
        echo "</ul></div>\n";
    }
}

// Funktionen zum anzeigen von Artikelvarianten
function variant_select( $item ) {
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
                $descr    = ($item["variant_type"] == "") ? $item["description"] : $item["variant_type"];
                $itemlink = "?shop_category=" . $_GET["shop_category"] . "&card=" . $item["id"] . "&var=true";
                echo "<tr onclick=\"window.location.href = '" . $itemlink . "'\"><td>" .
                    $descr . "</td><td>" . $GLOBALS["tc"]["item_no"] . ":" .
                    $item["item_no"] . "</td><td>";
                get_inventory_sign($item);
                echo "</td></tr>";
            }
            while ($variant_item = @mysqli_fetch_array($result)) {
                $variant_itemlink = "?shop_category=" . $_GET["shop_category"] . "&card=" . $variant_item["id"] . "&var=true";
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

function variant_select_nav( $item ) {
    $var_query  = "SELECT *
				  FROM shop_item_variant
				  WHERE item_no = '" . $item['item_no'] . "'
				  AND company = '" . $GLOBALS['shop']['company'] . "'";
    $var_result = mysqli_query($GLOBALS['mysql_con'], $var_query);

    if (mysqli_num_rows($var_result) > 0) {
        mysqli_data_seek($var_result, 0);
        ?>
        <select class='select' name='input_variant'
                onchange="location.href='?shop_category=<?= $_GET["shop_category"] ?>&var=true&card=<?= $_GET['card'] ?>&variant='+this.options[this.selectedIndex].value;">
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

?>

