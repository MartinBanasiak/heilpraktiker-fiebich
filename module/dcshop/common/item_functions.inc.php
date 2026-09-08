<?php
use DynCom\dc\common\classes\Hook;
use DynCom\dc\common\interfaces\IOCInterface;

//Sucht einen Artikel nach Artikelnummer
function get_item($company, $shop_code, $language_code, $item_no)
{
    $query = "SELECT *
    		  FROM shop_item
    		  WHERE item_no = '" . $item_no . "'
    		  	AND company = '" . $company . "'
    		  	AND shop_code = '" . $shop_code . "'
    		  	AND language_code = '" . $language_code . "'
    		  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $item = mysqli_fetch_array($result);
        return $item;
    }
}

//Sucht einen Artikel oder ein Artikelfeld(get_val) nach der id
function get_item_by_card_id($company, $shop_code, $language_code, $item_id, $get_val = "")
{


    $prepStatement = "SELECT *
			  FROM shop_item
			  WHERE shop_item.id = :id
			  	AND company = :company
			  	AND shop_code =:shopCode
			  	AND language_code =:languageCode
			  	LIMIT 1 ";


    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $params = [
        [':id', $item_id, PDO::PARAM_STR],
        [':company', $company, PDO::PARAM_STR],
        [':shopCode', $shop_code, PDO::PARAM_STR],
        [':languageCode', $language_code, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();

    if (count($resultArray) > 0) {

        if ($get_val == "") {
            return $resultArray[0];
        } else {
            return $resultArray[0][$get_val];
        }


    }
    return $resultArray;

}

function get_item_by_id($id)
{
    $prepStatement = " SELECT * FROM shop_view_active_item WHERE id = :id ";


    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $params = [
        [':id', $id, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();
    if (count($resultArray) > 0) {
        return $resultArray[0];
    }
    return $resultArray;

    /*   $result = mysqli_query($GLOBALS['mysql_con'],$query);
       if(@mysqli_num_rows($result) === 1) {
           $item = mysqli_fetch_assoc($result);
           return $item;
       }*/
}

//Sucht das Hauptartikelbild
function get_item_main_image($item, $parent_item)
{
    $query = "SELECT id,item_no,type,line_no,description,filename,all_language_codes
			  FROM shop_item_file
			  WHERE type = 0
			  	AND ((item_no = '" . $item["item_no"] . "' AND line_no = '" . $item["main_picture_line_no"] . "')
			  	OR (item_no = '" . $parent_item["item_no"] . "' AND line_no = '" . $parent_item["main_picture_line_no"] . "'))
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND filename <> ''
			  LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 0) {
        $query = "SELECT shop_item_file.id,shop_item_file.item_no,shop_item_file.type,
						 shop_item_file.line_no,shop_item_file.description,shop_item_file.filename,shop_item_file.all_language_codes
				  FROM shop_item_file
				  LEFT JOIN shop_item ON (shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $item["language_code"] . "' )
				  LEFT JOIN shop_item AS parent_shop_item ON (parent_shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $parent_item["language_code"] . "' )
				  WHERE 
						shop_item_file.company='" . $GLOBALS["shop"]["company"] . "'
				    AND 
						shop_item_file.shop_code='" . $GLOBALS['shop']['item_source'] . "'
					AND
						(shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
					AND
						shop_item_file.item_no='" . $item["item_no"] . "'
					AND 
						(shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
					AND
						shop_item_file.type = '0'
					AND 
						shop_item_file.filename <> ''
					AND 
						(shop_item_file.line_no=shop_item.main_picture_line_no OR shop_item_file.line_no=parent_shop_item.main_picture_line_no)					
				  ORDER BY find_in_set(shop_item_file.item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
				  LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 0) {
            $query = "SELECT id,item_no,type,line_no,description,filename,all_language_codes
					  FROM shop_item_file
					  WHERE type =  0
					  	AND (item_no = '" . $item["item_no"] . "' OR item_no = '" . $parent_item["item_no"] . "')
						AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
					  	AND company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
					  	AND filename <> ''
						AND (shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
					  ORDER BY find_in_set(item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
					  LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);

            if (@mysqli_num_rows($result) == 0) {
                $variantquery = "SELECT * 
								 FROM shop_view_active_item
								 WHERE parent_item_no = '" . $item['item_no'] . "'
									AND company='" . $GLOBALS['shop']['company'] . "'
									AND shop_code='" . $GLOBALS['shop']['item_source'] . "'
									AND language_code='" . $GLOBALS['shop_language']['code'] . "'";
                $variant_result = mysqli_query($GLOBALS['mysql_con'], $variantquery);
                if (@mysqli_num_rows($variant_result) > 0) {
                    $in_variants = "(";
                    while ($variant = mysqli_fetch_assoc($variant_result)) {
                        $in_variants .= "'" . $variant['item_no'] . "',";
                    }
                    $in_variants = substr($in_variants, 0, -1);
                    $in_variants .= ")";
                    $query = "SELECT shop_item_file.id,shop_item_file.item_no,shop_item_file.type,
									 shop_item_file.line_no,shop_item_file.description,shop_item_file.filename,shop_item_file.all_language_codes
							  FROM shop_item_file
							  LEFT JOIN shop_item ON (shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $item["language_code"] . "' )
							  LEFT JOIN shop_item AS parent_shop_item ON (parent_shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $parent_item["language_code"] . "' )
							  WHERE 
									shop_item_file.company='" . $GLOBALS["shop"]["company"] . "'
								AND 
									shop_item_file.shop_code='" . $GLOBALS['shop']['item_source'] . "'
								AND
									(shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
								AND
									shop_item_file.item_no IN " . $in_variants . "
								AND 
									(shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
								AND
									shop_item_file.type = '0'
								AND 
									shop_item_file.filename <> ''
								AND 
									(shop_item_file.line_no=shop_item.main_picture_line_no OR shop_item_file.line_no=parent_shop_item.main_picture_line_no)					
							  ORDER BY find_in_set(item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
							  LIMIT 1";
                    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }
        }
    }

    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

//Sucht das Hauptartikelbild
function get_item_main_customization_image($item, $parent_item)
{
    $query = "SELECT id,item_no,type,line_no,description,filename,all_language_codes
			  FROM shop_item_file
			  WHERE type = 0
			    AND customization = '1'	
			  	AND ((item_no = '" . $item["item_no"] . "' )
			  	OR (item_no = '" . $parent_item["item_no"] . "' ))
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND filename <> ''
			  LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 0) {
        $query = "SELECT shop_item_file.id,shop_item_file.item_no,shop_item_file.type,
						 shop_item_file.line_no,shop_item_file.description,shop_item_file.filename,shop_item_file.all_language_codes
				  FROM shop_item_file
				  LEFT JOIN shop_item ON (shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $item["language_code"] . "' )
				  LEFT JOIN shop_item AS parent_shop_item ON (parent_shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $parent_item["language_code"] . "' )
				  WHERE 
						shop_item_file.company='" . $GLOBALS["shop"]["company"] . "'
				    AND 
						shop_item_file.shop_code='" . $GLOBALS['shop']['item_source'] . "'
					AND
						(shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
					AND
						shop_item_file.item_no='" . $item["item_no"] . "'
					AND 
						(shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
					AND
						shop_item_file.type = '0'
					AND
						shop_item_file.customization = '1'	
					AND 
						shop_item_file.filename <> ''
					AND 
						(shop_item_file.line_no=shop_item.main_picture_line_no OR shop_item_file.line_no=parent_shop_item.main_picture_line_no)					
				  ORDER BY find_in_set(shop_item_file.item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
				  LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 0) {
            $query = "SELECT id,item_no,type,line_no,description,filename,all_language_codes
					  FROM shop_item_file
					  WHERE type =  0
					  	AND (item_no = '" . $item["item_no"] . "' OR item_no = '" . $parent_item["item_no"] . "')
						AND (shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
					  	AND company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
					  	AND filename <> ''
						AND (shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
					  ORDER BY find_in_set(item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
					  LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);

            if (@mysqli_num_rows($result) == 0) {
                $variantquery = "SELECT * 
								 FROM shop_view_active_item
								 WHERE parent_item_no = '" . $item['item_no'] . "'
									AND company='" . $GLOBALS['shop']['company'] . "'
									AND shop_code='" . $GLOBALS['shop']['item_source'] . "'
									AND language_code='" . $GLOBALS['shop_language']['code'] . "'";
                $variant_result = mysqli_query($GLOBALS['mysql_con'], $variantquery);
                if (@mysqli_num_rows($variant_result) > 0) {
                    $in_variants = "(";
                    while ($variant = mysqli_fetch_assoc($variant_result)) {
                        $in_variants .= "'" . $variant['item_no'] . "',";
                    }
                    $in_variants = substr($in_variants, 0, -1);
                    $in_variants .= ")";
                    $query = "SELECT shop_item_file.id,shop_item_file.item_no,shop_item_file.type,
									 shop_item_file.line_no,shop_item_file.description,shop_item_file.filename,shop_item_file.all_language_codes
							  FROM shop_item_file
							  LEFT JOIN shop_item ON (shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $item["language_code"] . "' )
							  LEFT JOIN shop_item AS parent_shop_item ON (parent_shop_item.item_no = shop_item_file.item_no AND shop_item.company=shop_item_file.company AND shop_item.shop_code=shop_item_file.shop_code AND shop_item.language_code='" . $parent_item["language_code"] . "' )
							  WHERE 
									shop_item_file.company='" . $GLOBALS["shop"]["company"] . "'
								AND 
									shop_item_file.shop_code='" . $GLOBALS['shop']['item_source'] . "'
								AND
									(shop_item_file.language_code='" . $GLOBALS["shop_language"]["code"] . "' OR shop_item_file.all_language_codes = 1)
								AND
									shop_item_file.item_no IN " . $in_variants . "
								AND 
									(shop_item_file.variant_code = '" . $_GET['variant'] . "' OR shop_item_file.variant_code='')
								AND
									shop_item_file.type = '0'
								AND
									shop_item_file.customization = '1'	
								AND 
									shop_item_file.filename <> ''
								AND 
									(shop_item_file.line_no=shop_item.main_picture_line_no OR shop_item_file.line_no=parent_shop_item.main_picture_line_no)					
							  ORDER BY find_in_set(item_no,'" . $item['item_no'] . "," . $parent_item['item_no'] . "'),line_no
							  LIMIT 1";
                    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }
        }
    }

    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}


//Sucht Aktionsbanner für einen Artikel und gibt die entsprechenden divs aus
function show_item_promotion_banners($item, $small = FALSE)
{
    $query = "SELECT DISTINCT sc.promotion_label
			  FROM shop_item_has_category sic
			  INNER JOIN shop_category sc ON sc.line_no = sic.category_line_no
			  WHERE sic.company = '" . $GLOBALS['shop']['company'] . "'
			 	AND sic.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			 	AND sic.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			 	AND sic.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			 	AND sc.company = '" . $GLOBALS['shop']['company'] . "'
			 	AND sc.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			 	AND sc.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			 	AND sc.promotion_active = 1
			 	AND (isnull(sc.promotion_validity_from) AND isnull(sc.promotion_validity_to)
			  		OR (isnull(sc.promotion_validity_from) AND (sc.promotion_validity_to >= curdate())
			  		OR (sc.promotion_validity_from <= curdate()) AND isnull(sc.promotion_validity_to))
			  		OR (sc.promotion_validity_from <= curdate()) AND (sc.promotion_validity_to >= curdate()))
			 	AND sic.item_no = '" . $item['item_no'] . "'";
    //echo $query;
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            IF ($small) {
                echo("<div class=\"promotion_banner small\"><div class='promotion promotion_" . $row['promotion_label'] . "'></div></div>");
            } ELSE {
                echo("<div class=\"promotion_banner\"><div class='promotion promotion_" . $row['promotion_label'] . "'></div></div>");
            }
        }
    }
}

//Sucht die richtige Ampel fÃ¼r die Lagerbestandsanzeige
function get_inventory_sign($item)
{

    $IOCContainer = $GLOBALS['IOC'];
    /**
     * @var $itemBuilder \DynCom\dc\dcShop\classes\WebshopItemBuilder
     */
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    $itemObj = $itemBuilder->getWebshopItemByID($item["id"]);
    /** @var \DynCom\dc\dcShop\classes\CurrShopConfiguration $shopConfig */
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    /** @var \DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider $availabilityProvider */
    $availabilityProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\DefaultItemAvailabilityProvider');
    $availability_code = $availabilityProvider->getItemAvailability($itemObj);

    $text = '';

    switch ($shopConfig->getShop()->getInventoryDisplay()) {
        case 1:
            $inventoryDisplay = 'traffic';
            $text = $GLOBALS["tc"][$availability_code] . $text;
            break;
        case 2:
            $inventoryDisplay = 'number';
            $currentInventory = $availabilityProvider->getInventory($itemObj);
            if (($currentInventory > $itemObj->getLowInventoryLimit())) {
                $currentInventory = $itemObj->getLowInventoryLimit();
            }
            $text .= $GLOBALS["tc"][$availability_code . '_' . $inventoryDisplay];
            break;
        case 0:
        default:
            $inventoryDisplay = 'checkmark';
            $text = $GLOBALS["tc"][$availability_code] . $text;
            break;
    }

    $text = str_replace('%CURR_INVENTORY%', $currentInventory, $text);

    echo '<div class="inventory ' . $availability_code . ' ' . $inventoryDisplay . '">' . $text . '</div>';

    /*$inventory    = $item["inventory"];
    $variant_item = get_item_first_variant($item);
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        if ($item['variant_code'] == '' && $variant_item["id"] <> '') {
            $query             = "SELECT SUM(shop_view_active_item.inventory) AS 'sum'
					  FROM shop_item_link
					  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
					  WHERE shop_item_link.type = '0'
						AND shop_item_link.item_no = '" . $item["item_no"] . "'
						AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
						AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
						AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
					  ORDER BY shop_view_active_item.item_no
					  LIMIT 1";
            $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
            $variant_inventory = @mysqli_fetch_array($result);
            $inventory         = $variant_inventory["sum"];
        }
    } else {
        if ($item['variant_code'] == '' && $variant_item['id'] <> '') {
            $query     = 'SELECT 
						SUM(inventory) AS \'sum\'
					FROM 
						shop_item_variant
					WHERE 
						company=\'' . $GLOBALS['shop']['company'] . '\'
					  AND
						item_no=\'' . $item['item_no'] . '\'
					  AND
						to_delete=0';
            $result    = @mysqli_query($GLOBALS['mysql_con'], $query);
            $inventory = @mysqli_result($result, 0, 0);
        }
    }
    if (is_null($inventory) && $GLOBALS['shop']['shop_typ'] != 1) {
        //echo "<img class=\"inventory_image\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/not_available.png\" title=\"" . $GLOBALS['tc']['inventory_red'] . "\" />";
        echo "<div class='inventory not_available'>".$GLOBALS['tc']['not_available']."</div>";
    } elseif ($GLOBALS['shop']['shop_typ'] != 1) {
        switch ($inventory) {
            case ($inventory <= 0):
                //echo "<img class=\"inventory_image\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/not_available.png\" title=\"" . $GLOBALS['tc']['inventory_red'] . "\" />";/* IF($GLOBALS["shop"]["shop_typ"] == 1 && $_GET['shop_category'] != 'basket' && $_GET['shop_category'] != 'order') { echo "<div class=\"inventory_text\">Im Zulauf</div>";};
                echo "<div class='inventory not_available'>".$GLOBALS['tc']['not_available']."</div>";
                break;
            case ($inventory <= $item["insufficient_inventory_limit"]):
                //echo "<img class=\"inventory_image\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/limited.png\" title=\"" . $GLOBALS['tc']['inventory_yellow'] . "\" >"; /*IF($GLOBALS["shop"]["shop_typ"] == 1 && $_GET['shop_category'] != 'basket' && $_GET['shop_category'] != 'order') { echo "<div class=\"inventory_text\">Im Zulauf</div>";};
                echo "<div class='inventory low_availability'>".$GLOBALS['tc']['low_availability']."</div>";
                break;
            default:
               // echo "<img class=\"inventory_image\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/available.png\" title=\"" . $GLOBALS['tc']['inventory_green'] . "\" />";/*IF($GLOBALS["shop"]["shop_typ"] == 1 && $_GET['shop_category'] != 'basket' && $_GET['shop_category'] != 'order') { echo "<div class=\"inventory_text\">Im Zulauf</div>";};
                echo "<div class='inventory available'>".$GLOBALS['tc']['available']."</div>";
                break;
        }
    }
    if (is_null($inventory) && $GLOBALS['shop']['shop_typ'] == 1) {
        echo "<img class=\"inventory_image\" src=\"/layout/frontend/" . $GLOBALS["layout"]["code"] . "/img/not_available.png\" title=\"" . $GLOBALS['tc']['inventory_red'] . "\" />";
    } elseif ($GLOBALS['shop']['shop_typ'] == 1) {
        switch ($inventory) {
            case ($inventory <= 0):
                echo "<div class=\"cross_red\">" . $GLOBALS["tc"]["check_red"] . "</div>";
                break;
            case ($inventory <= $item["insufficient_inventory_limit"]):
                echo "<div class=\"check_orange\">" . $GLOBALS["tc"]["check_orange"] . "</div>";
                break;
            default:
                echo "<div class=\"check_green\">" . $GLOBALS["tc"]["check_green"] . "</div>";
                break;
        }
    }*/
}

//Sucht den Preis fÃ¼r den angemeldeten Debitor
function get_item_customer_price($item, $shop_customer, $quantity, $currency_code, $variant_code = '', $inv_disc_array = FALSE, $campaign_no = '')
{
    if ($currency_code == $GLOBALS['shop_language']['default_currency_code']) {
        $currency_query = " AND (currency_code = '" . $currency_code . "' OR currency_code = '') ";
    } else {
        $currency_query = " AND currency_code = '" . $currency_code . "' ";
    }
    $customer_no = ($shop_customer["bill_to_customer_no"] <> '') ? $shop_customer["bill_to_customer_no"] : $shop_customer["customer_no"];
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= " . $quantity . "
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_price_group"] . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $GLOBALS['shop']['cust_price_group_base_price'] . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '".$campaign_no."')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	".$currency_query."
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  ORDER BY unit_price ASC
			  LIMIT 1";*/
    $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_price_group"] . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $GLOBALS['shop']['cust_price_group_base_price'] . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $campaign_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	" . $currency_query . "
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  ORDER BY unit_price ASC
			  LIMIT 1";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_price = @mysqli_fetch_array($result);
        $best_price = $sales_price["unit_price"];
        $best_price_aid = $sales_price["allow_invoice_disc"];
        if ($sales_price['allow_line_disc']) {
            $best_price_disc_allowed = $sales_price["unit_price"];
            $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
        } else {
            $best_price_disc_allowed = 0;
            $best_price_disc_allowed_aid = 0;
        }
        if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
        if (!$sales_price["allow_line_disc"]) {
            /*$query = "SELECT *
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND minimum_quantity <= " . $quantity . "
					  	AND ((sales_type = 2)
					  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_price_group"] . "'))
					  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
					  		OR ((sales_type = 3) AND (sales_code = '".$campaign_no."')))
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		".$currency_query."
					  	AND (variant_code = '".$variant_code."'  OR variant_code = '')
					  	AND company = '".$GLOBALS['shop']['company']."'
					  ORDER BY unit_price ASC
					  LIMIT 1";*/
            $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					  		OR(minimum_quantity <= (" . $quantity . "*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
					  	AND ((sales_type = 2)
					  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_price_group"] . "'))
					  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
					  		OR ((sales_type = 3) AND (sales_code = '" . $campaign_no . "')))
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		" . $currency_query . "
					  	AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
					  	AND company = '" . $GLOBALS['shop']['company'] . "'
					  ORDER BY unit_price ASC
					  LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $sales_price = @mysqli_fetch_array($result);
                $best_price_disc_allowed = $sales_price["unit_price"];
                $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
                if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / (100 + $vat_percent)) * 100;
                        }
                    }
                } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / 100) * (100 + $vat_percent);
                        }
                    }
                }
            }
        }
    } else {
        $best_price = $item["base_price"];
        $best_price_aid = ($item["allow_invoice_discount"] == 1);
        $best_price_disc_allowed = $item["base_price"];
        $best_price_disc_allowed_aid = ($item["allow_invoice_discount"] == 1);
    }
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= " . $quantity . "
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_disc_group"] . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $GLOBALS['shop']['cust_disc_group_base_price'] . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '".$campaign_no."')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (" . $quantity . "*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND ((sales_type = 2)
			  		OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_disc_group"] . "'))
			  		OR ((sales_type = 1) AND (sales_code='" . $GLOBALS['shop']['cust_disc_group_base_price'] . "'))
			  		OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
			  		OR ((sales_type = 3) AND (sales_code = '" . $campaign_no . "')))
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = @mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0 && $best_price_disc_allowed != 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price["price"] = $best_price;
            $return_price["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price["price"] = $best_price;
        $return_price["allow_invoice_discount"] = $best_price_aid;
    }

    //Artikelrabattgruppen
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND minimum_quantity <= " . $quantity . "
				  AND ((sales_type = 2)
					  OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_disc_group"] . "'))
					  OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
					  OR ((sales_type = 3) AND (sales_code = '".$campaign_no."')))
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '".$variant_code."'  OR variant_code = '')
			  	  AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	  AND company = '".$GLOBALS['shop']['company']."'
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND ((minimum_quantity <= " . $quantity . " AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
				      OR(minimum_quantity <= (" . $quantity . "*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
				  AND ((sales_type = 2)
					  OR ((sales_type = 1) AND (sales_code='" . $shop_customer["customer_disc_group"] . "'))
					  OR ((sales_type = 0) AND (sales_code = '" . $customer_no . "'))
					  OR ((sales_type = 3) AND (sales_code = '" . $campaign_no . "')))
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
			  	  AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	  AND company = '" . $GLOBALS['shop']['company'] . "'
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = @mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0 && $best_price_disc_allowed != 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price_2["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price_2["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price_2["price"] = $best_price;
            $return_price_2["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price_2["price"] = $best_price;
        $return_price_2["allow_invoice_discount"] = $best_price_aid;
    }

    if ($return_price_2['price'] < $return_price['price']) {
        $return_price['price'] = $return_price_2['price'];
        $return_price["allow_invoice_discount"] = $return_price_2["allow_invoice_discount"];
    }
    if ($inv_disc_array) {
        return $return_price;
    } else {
        return $return_price["price"];
    }
}

//Sucht den Muterartikel, sonst false
function get_item_variant_parent($item)
{
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        $query = "SELECT shop_view_active_item.*
				  FROM shop_item_link
				  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.item_no
				  WHERE shop_item_link.type = '0'
				  	AND shop_item_link.linked_item_no = '" . $item["item_no"] . "'
				  	AND shop_view_active_item.company= '" . $GLOBALS["shop"]['company'] . "'
				  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
				  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
				  LIMIT 1";
        // echo "<!-- PARENT-QUERY: $query -->";
    } else {
        return $item;
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    } else {
        return FALSE;
    }
}

//Sucht die erste Variante(nach Item No), sonst false
function get_item_first_variant($item)
{
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        $query = "SELECT shop_view_active_item.*
				  FROM shop_item_link
				  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
				  WHERE shop_item_link.type = '0'
				  	AND shop_item_link.item_no = '" . $item["item_no"] . "'
				  	AND shop_view_active_item.company= '" . $GLOBALS["shop"]['company'] . "'
				  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
				  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'
				  ORDER BY shop_view_active_item.base_price ASC,shop_view_active_item.item_no LIMIT 1";
    } else {
        $query = "SELECT *
    			  FROM shop_item_variant
    			  WHERE company = '" . $GLOBALS['shop']['company'] . "'
    			  	AND item_no = '" . $item['item_no'] . "'
    			  LIMIT 1";
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (strlen($item['variant_code'] == 0) && (@mysqli_num_rows($result) == 1)) {
        return @mysqli_fetch_assoc($result);
    } else {
        return FALSE;
    }
}

//Gibt Anzahl der Varianten zurÃ¼ck
function get_item_no_of_variants($item)
{
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        $query = "SELECT DISTINCT shop_view_active_item.*
        		  FROM shop_item_link
        		  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
        		  WHERE shop_item_link.type = '0'
	        		AND shop_item_link.item_no = '" . $item["item_no"] . "'
	        		AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
        		  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'";
    } else {
        $query = "SELECT *
    			  FROM shop_item_variant
    			  WHERE company = '" . $GLOBALS['shop']['company'] . "'
    			  	AND item_no = '" . $item['item_no'] . "'";
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return @mysqli_num_rows($result);
}

//Gibt Anzahl der Varianten zurÃ¼ck wenn vorhanden, sonst Artikelnummer(mit Textkonstanten)
function get_item_no_or_no_of_variants($item)
{
    $parent_item = get_item_variant_parent($item);
    if ($parent_item["id"] <> '') {
        if ($GLOBALS['shop']['variant_typ'] != '2') {
            $query = "SELECT DISTINCT shop_view_active_item.*
	        		  FROM shop_item_link
	        		  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
	        		  WHERE shop_item_link.type = '0'
		        		AND shop_item_link.item_no = '" . $item["item_no"] . "'
		        		AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
	        		  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
	        		  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'";
        } else {
            $query = "SELECT *
	    			  FROM shop_item_variant
	    			  WHERE company = '" . $GLOBALS['shop']['company'] . "'
	    			  	AND item_no = '" . $item['item_no'] . "'";
        }
    } else {
        $query = "SELECT shop_view_active_item.*
				  FROM shop_item_link
				  LEFT JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
				  WHERE shop_item_link.type = '0'
				  	AND shop_item_link.item_no = '" . $item["item_no"] . "'
				  	AND shop_view_active_item.company= '" . $GLOBALS["shop"]['company'] . "'
				  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
				  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'";
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        $num_rows = @mysqli_num_rows($result);
        $text = ($GLOBALS['shop']['variant_typ'] == 1 ? $item['item_no'] . ' - ' . $num_rows . ' ' . $GLOBALS["tc"]["variants"] : $num_rows . ' ' . $GLOBALS["tc"]["variants"]);
        return $text;
    } else {
        return $item["item_no"];
    }
}

function get_item_variant_pictures($item)
{

    if ($GLOBALS['shop']['variant_type'] == 0 || $GLOBALS['shop']['variant_type'] == 1) {
        if (!isset($IOCContainer) || !($IOCContainer instanceof IOCInterface)) {
            $IOCContainer = $GLOBALS['IOC'];
        }
        /** @var \DynCom\dc\common\classes\PDOQueryWrapper $pdo */
        $pdo = $IOCContainer->resolve('DynCom\dc\common\classes\PDOQueryWrapper');

        $prepStatement = '
          SELECT 
            si.*, sif.description as file_description, sif.filename as file_filename
          FROM
            shop_item si
          inner JOIN
            shop_item_file sif ON si.company = sif.company
              AND si.shop_code = sif.shop_code
              AND si.item_no = sif.item_no
              AND si.active = 1
              AND 
                (si.language_code = sif.language_code
                  OR sif.all_language_codes = 1)
          WHERE
            si.parent_item_no = :parentItemNo 
          AND
            si.company = :company
          AND
            si.shop_code = :shopCode
          AND
            si.language_code = :languageCode
          AND
            sif.filename <> \'\'
          AND
            sif.type = 0
          GROUP BY 
            si.item_no  
        ';

        $params = [
            [':parentItemNo', $item["item_no"], PDO::PARAM_STR],
            [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
            [':shopCode', $GLOBALS["shop"]["code"], PDO::PARAM_STR],
            [':languageCode', $GLOBALS["shop_language"]["code"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resArr = $pdo->getResultArray();

        return $resArr;
    } else {
        return [];
    }
}

function show_variant_pictures($variantItemsWithPictures)
{

    echo '<div class="itemlist_variants"><div class="variant-next"><i class="fa fa-angle-up"></i></div><div class="itemlist_variants_wrapper">';

    $first = true;
    foreach ($variantItemsWithPictures as $variantItemWithPictures) {
        $itemlink = create_general_item_path($variantItemWithPictures, true);
        if ($_GET['shop_category'] == 'search' || $_GET['shop_category'] == 'basket' || $_GET['shop_category'] == 'favorites') {
            $itemlink = create_item_link_tab($variantItemWithPictures);
        }
        $itemlink .= '?var=true';
        $filePath = $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["image_config"][1]["path"];
        $filePathBig = $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["image_config"][2]["path"];
        $fileName = $variantItemWithPictures['file_filename'];
        $fileDescription = $variantItemWithPictures['file_description'];
        $itemDescription =$variantItemWithPictures['description'];
        if (!file_exists(rtrim(dirname(dirname(dirname(__DIR__))), '/') . $filePath . DIRECTORY_SEPARATOR . $fileName)) {
            $fileName = 'noimage.jpg';
        }
        echo '<div class="variant_image' . ($first ? ' active main_active' : '') . '"><a href=" ' . $itemlink . '"><img data-big="' . $filePathBig . DIRECTORY_SEPARATOR . $fileName . '" src="' . $filePath . DIRECTORY_SEPARATOR . $fileName . '" alt="'.$itemDescription.'" /></a></div>';
        $first = false;
    }

    echo '</div><div class="variant-prev"><i class="fa fa-angle-down"></i></div></div>';

}

function is_item_visitor_favorite($itemID, $visitorID)
{
    $query = "SELECT *
			  FROM shop_user_favorites
			  WHERE shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
			  AND shop_item_id = '" . $itemID . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        return true;
    } else {
        return false;
    }
}

//Zeigt "Favorit hinzufügen" oder "Favorit entfernen" an (mit passendem Link)
function get_favorite_sign($item)
{

    if (is_array($item)) {
        $itemID = (int)$item['id'];
    } elseif (is_object($item)) {
        $itemID = (int)$item->getID();
    } else {
        throw new ErrorException('Item can only be received as array or WebshopItemInterface');
    }

    $visitorID = (int)$GLOBALS['visitor']['id'];
    $is_item_favorite = is_item_visitor_favorite($itemID, $visitorID);

    if ($GLOBALS["shop"]["shop_typ"] == 1) {
        $favorite_delete = $GLOBALS['tc']['favorit_delete'];
        $favorite_add = $GLOBALS['tc']['favorit_add'];
    } else {
        $favorite_delete = $GLOBALS['tc']['favorit_delete_b2b'];
        $favorite_add = $GLOBALS['tc']['favorit_add_b2b'];
    }

    if ($is_item_favorite) {
        echo "<a class=\"favorite-button\" href='?action=shop_remove_item_from_favorites&action_id=" . $itemID . "' >" . $favorite_delete . " <i class=\"fa fa-star\" aria-hidden=\"true\"></i></a>";
    } else {
        echo "<a class=\"favorite-button\" href='?action=shop_add_item_to_favorites&action_id=" . $itemID . "' >" . $favorite_add . " <i class=\"fa fa-star-o\" aria-hidden=\"true\"></i></a>";
    }
}

//Gibt Staffelpreise aus
function show_all_item_customer_price($item, $customer, $currency_code = '', $variant_code = '')
{
    if ($currency_code == $GLOBALS['shop_language']['default_currency_code']) {
        $currency_query = " AND (currency_code = '" . $currency_code . "' OR currency_code = '') ";
    } else {
        $currency_query = " AND currency_code = '" . $currency_code . "' ";
    }
    $sales_price_query = "SELECT minimum_quantity
						  FROM shop_sales_price
						  WHERE (item_no = '" . $item["item_no"] . "'
							  	AND ((sales_type = 2)
							  		OR ((sales_type = 1) AND (sales_code='" . $customer["customer_price_group"] . "'))
							  		OR ((sales_type = 0) AND (sales_code = '" . $customer["customer_no"] . "')))
						  	OR (item_no='' AND sales_type=2 AND discount_group='" . $item['discount_group'] . "'))
						  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
						  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
						  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
						  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
						  	AND currency_code = '" . $currency_code . "'
						  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
						  	AND company = '" . $GLOBALS['shop']['company'] . "'"
        . $currency_query;
    $sales_price_list_result = @mysqli_query($GLOBALS['mysql_con'], $sales_price_query . " GROUP BY minimum_quantity ORDER BY minimum_quantity");
    if (@mysqli_num_rows($sales_price_list_result) > 0) {
        echo "<table class=\"details\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" style=\"margin-bottom:5px\">\n";
        while ($sales_price = @mysqli_fetch_array($sales_price_list_result)) {
            if ($sales_price["minimum_quantity"] == 0) {
                $sales_price["minimum_quantity"] = 1;
            }
            $price = get_item_customer_price($item, $customer, $sales_price["minimum_quantity"], $currency_code, $variant_code, FALSE, $GLOBALS['shop']['campain_no']);
            if ((!isset($last_best_price)) | ($last_best_price > $price)) {
                echo "  <tr><td align=\"left\"; style=\"padding:0px 12px 0px 3px\">" . $GLOBALS["tc"]["your_price_from"] . " " . number_format($sales_price["minimum_quantity"], 0, ',', '.') . " " . $item["base_unit_of_measure"] . "</td><td>" . format_amount($price, FALSE) . "</td>\n";
                $last_best_price = $price;
            }
        }
        echo "</table>\n";
        if ($customer["invoice_disc_code"] <> '') {
            echo $GLOBALS["tc"]["excl_invoice_disc"] . "<br /><br />\n";
        }
    }
}

//PrÃ¼ft ob Varianten vorhanden sind
function item_has_variants($item)
{
    if ($GLOBALS['shop']['variant_typ'] != '2') {
        $query = " SELECT shop_view_active_item.*
		  			FROM shop_item_link
		  			LEFT JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.linked_item_no
		  			WHERE shop_item_link.type = '0'
		  				AND shop_item_link.item_no = '" . $item["item_no"] . "'
						AND shop_view_active_item.company= '" . $GLOBALS["shop"]['company'] . "'
					  	AND shop_view_active_item.shop_code= '" . $item["shop_code"] . "'
					  	AND shop_view_active_item.language_code = '" . $item["language_code"] . "'";
    } else {
        $query = "SELECT *
    			  FROM shop_item_variant
    			  WHERE company = '" . $GLOBALS['shop']['company'] . "'
    			  	AND item_no = '" . $item['item_no'] . "'";
    }
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $no_of_variants = @mysqli_num_rows($result);
    if ($no_of_variants > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//Erstellt den Link zur Artikelkarte
function create_item_link($item)
{
    $link = "/" . customizeUrl() . "/" . $item["item_slug"];

    $link .= "-p" . $item["id"] . "/";
    //$link .= "&sid=" . session_id();

    if ($_GET['shop_category'] === 'search') {
        $link .= "?search=1";
    }
    return $link;
}

function create_item_link_from_id_and_slug(int $item_id, string $item_slug, string $shop_category) : string
{
    $link = "/" . customizeUrl() . "/" . $item_slug;

    $link .= "-p" . $item_id . "/";
    //$link .= "&sid=" . session_id();

    if ($shop_category === 'search') {
        $link .= "?search=1";
    }
    return $link;
}

function create_item_link_tab($item)
{
    return create_item_link($item);
}

function create_item_link_old($item)
{
    $link = "/" . customizeUrl() ."/shop";
    for ($i = 1; $i <= 5; $i++) {
        if ($_GET["slevel_" . $i] <> '') {
            if ($_GET['card'] == "") {
                $curpart = $_GET["slevel_" . $i];
                $seek = array(
                    'ä',
                    'ö',
                    'ü',
                    'ß',
                    '*',
                    ' ',
                    '.',
                    '/',
                    '\\',
                    '%',
                    ',',
                    'Ø',
                    'Ã',
                    'ø',
                    'ã',
                    'Õ',
                    'õ',
                    '"',
                    "'",
                    "!",
                    "§",
                    "$",
                    "&",
                    '(',
                    ')',
                    '=',
                    '?',
                    '`',
                    '´',
                    '{',
                    '[',
                    ']',
                    '}',
                    '~',
                    '#',
                    ';',
                    ':',
                    '-',
                    '_',
                    '<',
                    '>',
                    '|'
                );

                $replace = array(
                    'ae',
                    'oe',
                    'ue',
                    'ss',
                    '+',
                    '+',
                    '+',
                    '',
                    '',
                    'proz',
                    '+',
                    '&Oslash;',
                    '&Atilde;',
                    '&oslash;',
                    '&atilde;',
                    '&Otilde;',
                    '&otilde;',
                    '&quot;',
                    '&quot;',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                );
                //$link .= "/" . str_replace($seek,$replace,$curpart);
                $link .= "/" . rawurlencode($curpart);
            } else {
                if ($_GET['slevel_' . ($i + 1)] != "") {
                    $curpart = $_GET["slevel_" . $i];
                    $seek = array(
                        'ä',
                        'ö',
                        'ü',
                        'ß',
                        '*',
                        ' ',
                        '.',
                        '/',
                        '\\',
                        '%',
                        ',',
                        'Ø',
                        'Ã',
                        'ø',
                        'ã',
                        'Õ',
                        'õ',
                        '"',
                        "'",
                        "!",
                        "§",
                        "$",
                        "&",
                        '(',
                        ')',
                        '=',
                        '?',
                        '`',
                        '´',
                        '{',
                        '[',
                        ']',
                        '}',
                        '~',
                        '#',
                        ';',
                        ':',
                        '-',
                        '_',
                        '<',
                        '>',
                        '|'
                    );

                    $replace = array(
                        'ae',
                        'oe',
                        'ue',
                        'ss',
                        '+',
                        '+',
                        '+',
                        '',
                        '',
                        'proz',
                        '+',
                        '&Oslash;',
                        '&Atilde;',
                        '&oslash;',
                        '&atilde;',
                        '&Otilde;',
                        '&otilde;',
                        '&quot;',
                        '&quot;',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    );
                    //$link .= "/" . str_replace($seek,$replace,$curpart);
                    $link .= "/" . rawurlencode($curpart);
                }
            }
        } else {
            break;
        }
    }
    $seek = array(
        'ä',
        'ö',
        'ü',
        'ß',
        '*',
        ' ',
        '.',
        '/',
        '\\',
        '%',
        ',',
        'Ø',
        'Ã',
        'ø',
        'ã',
        'Õ',
        'õ',
        '"',
        "'",
        "!",
        "Â§",
        "$",
        "&",
        '(',
        ')',
        '=',
        '?',
        '`',
        '´',
        '{',
        '[',
        ']',
        '}',
        '~',
        '#',
        ';',
        ':',
        '-',
        '_',
        '<',
        '>',
        '|'
    );

    $replace = array(
        'ae',
        'oe',
        'ue',
        'ss',
        '+',
        '+',
        '+',
        '',
        '',
        'proz',
        '+',
        '&Oslash;',
        '&Atilde;',
        '&oslash;',
        '&atilde;',
        '&Otilde;',
        '&otilde;',
        '&quot;',
        '&quot;',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
    );
    $item_description = str_replace($seek, $replace, $item["description"]);
    $item_description = str_replace("&Acirc;", "", $item_description);
    $item_description = str_replace("&acirc;", "", $item_description);
    $item_description = trim($item_description, "+");
    $link .= "/" . $item_description;
    //$link .= "/".urlencode($item["description"]);
    $link .= "-p" . $item["id"] . "/";
    return ($link);

}

function create_item_link_tab_old($item)
{
    $link = "/" . $GLOBALS["site"]["code"];
    $link .= "/" . $GLOBALS["language"]["code"];
    $link .= "/" . "shop";
    $link .= "/" . create_item_category_path($item);
    $seek = array(
        'ä',
        'ö',
        'ü',
        'ß',
        '*',
        ' ',
        '.',
        '/',
        '\\',
        '%',
        ',',
        'Ø',
        'Ã',
        'ø',
        'ã',
        'Õ',
        'õ',
        '"',
        "'",
        "!",
        "§",
        "$",
        "&",
        '(',
        ')',
        '=',
        '?',
        '`',
        '´',
        '{',
        '[',
        ']',
        '}',
        '~',
        '#',
        ';',
        ':',
        '-',
        '_',
        '<',
        '>',
        '|'
    );

    $replace = array(
        'ae',
        'oe',
        'ue',
        'ss',
        '+',
        '+',
        '+',
        '',
        '',
        'proz',
        '+',
        '&Oslash;',
        '&Atilde;',
        '&oslash;',
        '&atilde;',
        '&Otilde;',
        '&otilde;',
        '&quot;',
        '&quot;',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
    );
    $item_description = str_replace($seek, $replace, $item["description"]);
    $item_description = str_replace("&Acirc;", "", $item_description);
    $item_description = str_replace("&acirc;", "", $item_description);
    $item_description = trim($item_description, "+");
    $link .= "/" . $item_description;
    //$link .= "/".rawurlencode($item["description"]);
    $link .= "-p" . $item["id"] . "/";
    //$link .= "&sid=" . session_id();
    return ($link);
}

/*
function create_item_category_path($item)
{
	if($item["main_category_line_no"] != "" && $item["main_category_line_no"] != '0'){
		$category = get_category($GLOBALS["shop"]["company"],$GLOBALS["shop"]["code"],$GLOBALS["shop_language"]["code"],$item["main_category_line_no"]);	
		$link = $category['code'];
		while($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0)
		{
			$category = get_category($GLOBALS["shop"]["company"],$GLOBALS["shop"]["code"],$GLOBALS["shop_language"]["code"],$category["parent_line_no"]);	
			$link = $category['code']."/".$link;
		}
	}else{
		if($item["parent_item_no"] != ""){
			$parent_item = get_item($GLOBALS["shop"]["company"],$GLOBALS[shop][code],$GLOBALS["shop_language"]["code"],$item["parent_item_no"]);
			$category = get_category($GLOBALS["shop"]["company"],$GLOBALS[shop][code],$GLOBALS[shop_language][code],$parent_item["main_category_line_no"]);		
			$link = $category['code'];
			while($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0)
			{
				$category = get_category($GLOBALS["shop"]["company"],$GLOBALS["shop"]["code"],$GLOBALS["shop_language"]["code"],$category["parent_line_no"]);	
				$link = $category['code']."/".$link;
			}
		}else{
			// Kategorie des Artikels holen
			$query = "SELECT * FROM shop_item_has_category WHERE shop_item_id = '".$item["id"]."' LIMIT 1";
			$result = @mysqli_query($GLOBALS['mysql_con'],$query);
			$category1 = @mysqli_fetch_array($result);
			if($category1["shop_category_id"] > 0){
				$category = get_category_by_id($GLOBALS[site][code],$GLOBALS[shop_language][code],$category1["shop_category_id"]);
				$link = $category['code'];
				while($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0)
				{
					$category = get_category($GLOBALS["shop"]["company"],$GLOBALS["shop"]["code"],$GLOBALS["shop_language"]["code"],$category["parent_line_no"]);	
					$link = $category['code']."/".$link;
				}
			}
		}
	}
	return $link;
}
*/

function create_item_category_path($item)
{
    if (array_key_exists('main_category_line_no', $item) && $item["main_category_line_no"] != "" && $item["main_category_line_no"] != '0') {
        $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$item['main_category_line_no']);
        $catcode = $category["code"];
        $seek = array(
            'ä',
            'ö',
            'ü',
            'ß',
            '*',
            ' ',
            '.',
            '/',
            '\\',
            '%',
            ',',
            'Ø',
            'Ã',
            'ø',
            'ã',
            'Õ',
            'õ',
            '"',
            "'",
            "!",
            "§",
            "$",
            "&",
            '(',
            ')',
            '=',
            '?',
            '`',
            '´',
            '{',
            '[',
            ']',
            '}',
            '~',
            '#',
            ';',
            ':',
            '-',
            '_',
            '<',
            '>',
            '|'
        );

        $replace = array(
            'ae',
            'oe',
            'ue',
            'ss',
            '+',
            '+',
            '+',
            '',
            '',
            'proz',
            '+',
            '&Oslash;',
            '&Atilde;',
            '&oslash;',
            '&atilde;',
            '&Otilde;',
            '&otilde;',
            '&quot;',
            '&quot;',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        );
        $catcode = str_replace($seek, $replace, $catcode);
        $link = $catcode;
        while ($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0) {
            $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["parent_line_no"]);

            $catcode = $category["code"];
            $seek = array(
                'ä',
                'ö',
                'ü',
                'ß',
                '*',
                ' ',
                '.',
                '/',
                '\\',
                '%',
                ',',
                'Ø',
                'Ã',
                'ø',
                'ã',
                'Õ',
                'õ',
                '"',
                "'",
                "!",
                "§",
                "$",
                "&",
                '(',
                ')',
                '=',
                '?',
                '`',
                '´',
                '{',
                '[',
                ']',
                '}',
                '~',
                '#',
                ';',
                ':',
                '-',
                '_',
                '<',
                '>',
                '|'
            );

            $replace = array(
                'ae',
                'oe',
                'ue',
                'ss',
                '+',
                '+',
                '+',
                '',
                '',
                'proz',
                '+',
                '&Oslash;',
                '&Atilde;',
                '&oslash;',
                '&atilde;',
                '&Otilde;',
                '&otilde;',
                '&quot;',
                '&quot;',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            );
            $catcode = str_replace($seek, $replace, $catcode);
            $link = $catcode . "/" . $link;
        }
    } else {
        if ($item["parent_item_no"] != "") {
            $parent_item = get_item($GLOBALS["shop"]["company"], $GLOBALS['shop']['item_source'], $GLOBALS['shop_language']['code'], $item["parent_item_no"]);
            $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_item["main_category_line_no"]);
            if (empty($category)) {
                $query = "SELECT * 
						FROM shop_item_has_category 
						WHERE item_no= '" . $item["item_no"] . "' 
						AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND shop_code = '" . $GLOBALS['shop']['item_source'] . "'
						AND category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
						AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
						AND category_language_code = '" . $GLOBALS['shop_language']['code'] . "'
						LIMIT 1";
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $category = @mysqli_fetch_array($result);
                $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["category_line_no"]);
                $catcode = $category["code"];
                $seek = array(
                    'ä',
                    'ö',
                    'ü',
                    'ß',
                    '*',
                    ' ',
                    '.',
                    '/',
                    '\\',
                    '%',
                    ',',
                    'Ø',
                    'Ã',
                    'ø',
                    'ã',
                    'Õ',
                    'õ',
                    '"',
                    "'",
                    "!",
                    "§",
                    "$",
                    "&",
                    '(',
                    ')',
                    '=',
                    '?',
                    '`',
                    '´',
                    '{',
                    '[',
                    ']',
                    '}',
                    '~',
                    '#',
                    ';',
                    ':',
                    '-',
                    '_',
                    '<',
                    '>',
                    '|'
                );

                $replace = array(
                    'ae',
                    'oe',
                    'ue',
                    'ss',
                    '+',
                    '+',
                    '+',
                    '',
                    '',
                    'proz',
                    '+',
                    '&Oslash;',
                    '&Atilde;',
                    '&oslash;',
                    '&atilde;',
                    '&Otilde;',
                    '&otilde;',
                    '&quot;',
                    '&quot;',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                );
                $catcode = str_replace($seek, $replace, $catcode);
                $link = $catcode;
                while ($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0) {
                    $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["parent_line_no"]);
                    $catcode = $category["code"];
                    $seek = array(
                        'ä',
                        'ö',
                        'ü',
                        'ß',
                        '*',
                        ' ',
                        '.',
                        '/',
                        '\\',
                        '%',
                        ',',
                        'Ø',
                        'Ã',
                        'ø',
                        'ã',
                        'Õ',
                        'õ',
                        '"',
                        "'",
                        "!",
                        "§",
                        "$",
                        "&",
                        '(',
                        ')',
                        '=',
                        '?',
                        '`',
                        '´',
                        '{',
                        '[',
                        ']',
                        '}',
                        '~',
                        '#',
                        ';',
                        ':',
                        '-',
                        '_',
                        '<',
                        '>',
                        '|'
                    );

                    $replace = array(
                        'ae',
                        'oe',
                        'ue',
                        'ss',
                        '+',
                        '+',
                        '+',
                        '',
                        '',
                        'proz',
                        '+',
                        '&Oslash;',
                        '&Atilde;',
                        '&oslash;',
                        '&atilde;',
                        '&Otilde;',
                        '&otilde;',
                        '&quot;',
                        '&quot;',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    );
                    $catcode = str_replace($seek, $replace, $catcode);
                    $link = $catcode . "/" . $link;
                }
            } else {
                $catcode = $category["code"];
                $seek = array(
                    'Ã¤',
                    'Ã¶',
                    'Ã¼',
                    'ÃŸ',
                    '*',
                    ' ',
                    '.',
                    '/',
                    '\\',
                    '%',
                    ',',
                    'Ø',
                    'Ã',
                    'ø',
                    'ã',
                    'Õ',
                    'õ',
                    '"',
                    "'",
                    "!",
                    "§",
                    "$",
                    "&",
                    '(',
                    ')',
                    '=',
                    '?',
                    '`',
                    '´',
                    '{',
                    '[',
                    ']',
                    '}',
                    '~',
                    '#',
                    ';',
                    ':',
                    '-',
                    '_',
                    '<',
                    '>',
                    '|'
                );

                $replace = array(
                    'ae',
                    'oe',
                    'ue',
                    'ss',
                    '+',
                    '+',
                    '+',
                    '',
                    '',
                    'proz',
                    '+',
                    '&Oslash;',
                    '&Atilde;',
                    '&oslash;',
                    '&atilde;',
                    '&Otilde;',
                    '&otilde;',
                    '&quot;',
                    '&quot;',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                );
                $catcode = str_replace($seek, $replace, $catcode);
                $link = $catcode;
                while ($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0) {
                    $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["parent_line_no"]);
                    $catcode = $category["code"];
                    $seek = array(
                        'ä',
                        'ö',
                        'ü',
                        'ß',
                        '*',
                        ' ',
                        '.',
                        '/',
                        '\\',
                        '%',
                        ',',
                        'Ø',
                        'Ã',
                        'ø',
                        'ã',
                        'Õ',
                        'õ',
                        '"',
                        "'",
                        "!",
                        "§",
                        "$",
                        "&",
                        '(',
                        ')',
                        '=',
                        '?',
                        '`',
                        '´',
                        '{',
                        '[',
                        ']',
                        '}',
                        '~',
                        '#',
                        ';',
                        ':',
                        '-',
                        '_',
                        '<',
                        '>',
                        '|'
                    );

                    $replace = array(
                        'ae',
                        'oe',
                        'ue',
                        'ss',
                        '+',
                        '+',
                        '+',
                        '',
                        '',
                        'proz',
                        '+',
                        '&Oslash;',
                        '&Atilde;',
                        '&oslash;',
                        '&atilde;',
                        '&Otilde;',
                        '&otilde;',
                        '&quot;',
                        '&quot;',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    );
                    $catcode = str_replace($seek, $replace, $catcode);
                    $link = $catcode . "/" . $link;
                }
            }
        } else {
            $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_item["main_category_line_no"]);
            if (empty($category)) {
                $cat_source = (!strlen($GLOBALS["shop"]["category_source"]) > 0) ? $GLOBALS["shop"]["code"] : $GLOBALS["shop"]["category_source"];

                // Kategorie des Artikels holen
                $query = "SELECT * 
						FROM shop_item_has_category 
						WHERE item_no= '" . $item["item_no"] . "' 
						AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND shop_code = '" . $GLOBALS['shop']['item_source'] . "'
						AND category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
						AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
						AND category_language_code = '" . $GLOBALS['shop_language']['code'] . "'
						LIMIT 1";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                $category1 = mysqli_fetch_array($result);
                if (isset($category1)) {
                    $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category1["category_line_no"]);
                    $catcode = $category["code"];
                    $seek = array(
                        'ä',
                        'ö',
                        'ü',
                        'ß',
                        '*',
                        ' ',
                        '.',
                        '/',
                        '\\',
                        '%',
                        ',',
                        'Ø',
                        'Ã',
                        'ø',
                        'ã',
                        'Õ',
                        'õ',
                        '"',
                        "'",
                        "!",
                        "§",
                        "$",
                        "&",
                        '(',
                        ')',
                        '=',
                        '?',
                        '`',
                        '´',
                        '{',
                        '[',
                        ']',
                        '}',
                        '~',
                        '#',
                        ';',
                        ':',
                        '-',
                        '_',
                        '<',
                        '>',
                        '|'
                    );

                    $replace = array(
                        'ae',
                        'oe',
                        'ue',
                        'ss',
                        '+',
                        '+',
                        '+',
                        '',
                        '',
                        'proz',
                        '+',
                        '&Oslash;',
                        '&Atilde;',
                        '&oslash;',
                        '&atilde;',
                        '&Otilde;',
                        '&otilde;',
                        '&quot;',
                        '&quot;',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    );
                    $catcode = str_replace($seek, $replace, $catcode);
                    $link = $catcode;

                    if ( !$GLOBALS['shop_setup']['show_short_url'] ) {


                        while ($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0) {
                            $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["parent_line_no"]);
                            $catcode = $category["code"];
                            $seek = array(
                                'ä',
                                'ö',
                                'ü',
                                'ß',
                                '*',
                                ' ',
                                '.',
                                '/',
                                '\\',
                                '%',
                                ',',
                                'Ø',
                                'Ã',
                                'ø',
                                'ã',
                                'Õ',
                                'õ',
                                '"',
                                "'",
                                "!",
                                "§",
                                "$",
                                "&",
                                '(',
                                ')',
                                '=',
                                '?',
                                '`',
                                '´',
                                '{',
                                '[',
                                ']',
                                '}',
                                '~',
                                '#',
                                ';',
                                ':',
                                '-',
                                '_',
                                '<',
                                '>',
                                '|'
                            );

                            $replace = array(
                                'ae',
                                'oe',
                                'ue',
                                'ss',
                                '+',
                                '+',
                                '+',
                                '',
                                '',
                                'proz',
                                '+',
                                '&Oslash;',
                                '&Atilde;',
                                '&oslash;',
                                '&atilde;',
                                '&Otilde;',
                                '&otilde;',
                                '&quot;',
                                '&quot;',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                            );
                            $catcode = str_replace($seek, $replace, $catcode);
                            $link = $catcode . "/" . $link;
                        }


                    }


                }
            } else {
                $catcode = $category["code"];
                $seek = array(
                    'ä',
                    'ö',
                    'ü',
                    'ß',
                    '*',
                    ' ',
                    '.',
                    '/',
                    '\\',
                    '%',
                    ',',
                    'Ø',
                    'Ã',
                    'ø',
                    'ã',
                    'Õ',
                    'õ',
                    '"',
                    "'",
                    "!",
                    "§",
                    "$",
                    "&",
                    '(',
                    ')',
                    '=',
                    '?',
                    '`',
                    '´',
                    '{',
                    '[',
                    ']',
                    '}',
                    '~',
                    '#',
                    ';',
                    ':',
                    '-',
                    '_',
                    '<',
                    '>',
                    '|'
                );

                $replace = array(
                    'ae',
                    'oe',
                    'ue',
                    'ss',
                    '+',
                    '+',
                    '+',
                    '',
                    '',
                    'proz',
                    '+',
                    '&Oslash;',
                    '&Atilde;',
                    '&oslash;',
                    '&atilde;',
                    '&Otilde;',
                    '&otilde;',
                    '&quot;',
                    '&quot;',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                );
                $catcode = str_replace($seek, $replace, $catcode);
                $link = $catcode;
                while ($category['parent_line_no'] != '' AND $category['parent_line_no'] != 0) {
                    $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$category["parent_line_no"]);
                    $catcode = $category["code"];
                    $seek = array(
                        'ä',
                        'ö',
                        'ü',
                        'ß',
                        '*',
                        ' ',
                        '.',
                        '/',
                        '\\',
                        '%',
                        ',',
                        'Ø',
                        'Ã',
                        'ø',
                        'ã',
                        'Õ',
                        'õ',
                        '"',
                        "'",
                        "!",
                        "§",
                        "$",
                        "&",
                        '(',
                        ')',
                        '=',
                        '?',
                        '`',
                        '´',
                        '{',
                        '[',
                        ']',
                        '}',
                        '~',
                        '#',
                        ';',
                        ':',
                        '-',
                        '_',
                        '<',
                        '>',
                        '|'
                    );

                    $replace = array(
                        'ae',
                        'oe',
                        'ue',
                        'ss',
                        '+',
                        '+',
                        '+',
                        '',
                        '',
                        'proz',
                        '+',
                        '&Oslash;',
                        '&Atilde;',
                        '&oslash;',
                        '&atilde;',
                        '&Otilde;',
                        '&otilde;',
                        '&quot;',
                        '&quot;',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    );
                    $catcode = str_replace($seek, $replace, $catcode);
                    $link = $catcode . "/" . $link;
                }
            }
        }
    }
    return $link;
}


//Sucht den VK-Preis nach neuer Logik

function get_item_base_price($item, $currency_code = '')
{
    switch ($GLOBALS['shop']['base_price_typ']) {
        case 0:
            return get_price_all_customer($item, $currency_code);
            break;
        case 1:
            return get_price_customer_group($item, $currency_code, $GLOBALS['shop']['cust_price_group_base_price'], $GLOBALS['shop']['cust_disc_group_base_price']);
            break;
        case 2:
            //Geändert, da beim Artikel, der Haken "Preise inkl. MwSt" nicht berücksichtigt wird
            //if($GLOBALS['shop']['prices_including_vat'] == 0 && $item['price_includes_vat'] == 1)
//			if($GLOBALS['shop']['prices_including_vat'] == 0 && $GLOBALS['item_source_shop']['prices_including_vat'] == 1)
//			{
//				$vatquery = "SELECT vat_percent
//							 FROM shop_vat_posting_setup
//							 WHERE company = '".$GLOBALS['shop']['company']."'
//							 	AND vat_bus_posting_group = '".$GLOBALS['shop']['vat_bus_posting_group']."'
//							 	AND vat_prod_posting_group = '".$item['vat_prod_posting_group']."'";
//				$vatresult = mysqli_query($GLOBALS['mysql_con'],$vatquery);
//				if(@mysqli_num_rows($vatresult) == 1)
//				{
//					$vatrow = mysqli_fetch_assoc($vatresult);
//					$vat_percent = $vatrow['vat_percent'];
//					if($vat_percent > 0)
//					{
//						return ($item['base_price']/(100 + $vat_percent)) * 100; break;
//					}
//				}
//			}
//			//elseif($GLOBALS['shop']['prices_including_vat'] == 1 && $item['price_includes_vat'] == 0)
//			elseif($GLOBALS['shop']['prices_including_vat'] == 1 && $GLOBALS['item_source_shop']['prices_including_vat'] == 0)
//			{
//				$vatquery = "SELECT vat_percent
//							 FROM shop_vat_posting_setup
//							 WHERE company = '".$GLOBALS['shop']['company']."'
//							 	AND vat_bus_posting_group = '".$GLOBALS['shop']['vat_bus_posting_group']."'
//							 	AND vat_prod_posting_group = '".$item['vat_prod_posting_group']."'";
//				$vatresult = mysqli_query($GLOBALS['mysql_con'],$vatquery);
//				if(@mysqli_num_rows($vatresult) == 1)
//				{
//					$vatrow = mysqli_fetch_assoc($vatresult);
//					$vat_percent = $vatrow['vat_percent'];
//					if($vat_percent > 0)
//					{
//						return ($item['base_price']/100) * (100 + $vat_percent); break;
//					}
//				}
//			}
            return $item['base_price'];
            break;
        default:
            return $item['base_price'];
            break;
    }
}

//Sucht den Listenpreis nach neuer Logik

function get_item_retail_price($item, $currency_code = '')
{
    switch ($GLOBALS['shop']['retail_price_typ']) {
        case 0:
            return get_price_all_customer($item, $currency_code);
            break;
        case 1:
            return get_price_customer_group($item, $currency_code, $GLOBALS['shop']['cust_price_group_retail_price'], $GLOBALS['shop']['cust_disc_group_retail_price']);
            break;
        case 2:
            //Geändert, da beim Artikel, der Haken "Preise inkl. MwSt" nicht berücksichtigt wird
            //if($GLOBALS['shop']['prices_including_vat'] == 0 && $item['price_includes_vat'] == 1 && !get_item_first_variant($item))
//			if($GLOBALS['shop']['prices_including_vat'] == 0 && $GLOBALS['item_source_shop']['prices_including_vat'] == 1 && !get_item_first_variant($item))
//			{
//				$vatquery = "SELECT vat_percent
//							 FROM shop_vat_posting_setup
//							 WHERE company = '".$GLOBALS['shop']['company']."'
//							 	AND vat_bus_posting_group = '".$GLOBALS['shop']['vat_bus_posting_group']."'
//							 	AND vat_prod_posting_group = '".$item['vat_prod_posting_group']."'";
//				$vatresult = mysqli_query($GLOBALS['mysql_con'],$vatquery);
//				if(@mysqli_num_rows($vatresult) == 1)
//				{
//					$vatrow = mysqli_fetch_assoc($vatresult);
//					$vat_percent = $vatrow['vat_percent'];
//					if($vat_percent > 0)
//					{
//						return ($item['retail_price']/(100 + $vat_percent)) * 100;
//						break;
//					}
//				}
//			}
//			//elseif($GLOBALS['shop']['prices_including_vat'] == 1 && $item['price_includes_vat'] == 0 && !get_item_first_variant($item))
//			elseif($GLOBALS['shop']['prices_including_vat'] == 1 && $GLOBALS['item_source_shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
//			{
//				$vatquery = "SELECT vat_percent
//							 FROM shop_vat_posting_setup
//							 WHERE company = '".$GLOBALS['shop']['company']."'
//							 	AND vat_bus_posting_group = '".$GLOBALS['shop']['vat_bus_posting_group']."'
//							 	AND vat_prod_posting_group = '".$item['vat_prod_posting_group']."'";
//				$vatresult = mysqli_query($GLOBALS['mysql_con'],$vatquery);
//				if(@mysqli_num_rows($vatresult) == 1)
//				{
//					$vatrow = mysqli_fetch_assoc($vatresult);
//					$vat_percent = $vatrow['vat_percent'];
//					if($vat_percent > 0)
//					{
//						return ($item['retail_price']/100) * (100 + $vat_percent);
//						break;
//					}
//				}
//			}
            return $item['retail_price'];
            break;
        default:
            return $item['retail_price'];
            break;
    }
}

//Sucht den Preis für alle Debitoren

function get_price_all_customer($item, $currency_code = '')
{
    if ($currency_code == $GLOBALS['shop_language']['default_currency_code']) {
        $currency_query = " AND (currency_code = '" . $currency_code . "' OR currency_code = '') ";
    } else {
        $currency_query = " AND currency_code = '" . $currency_code . "' ";
    }
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= 1
			  	AND sales_type = 2
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	".$currency_query."
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";*/
    $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						   ELSE 9999999
					  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND sales_type = 2
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	" . $currency_query . "
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_price = @mysqli_fetch_array($result);
        $best_price = $sales_price["unit_price"];
        $best_price_aid = $sales_price["allow_invoice_disc"];
        $best_price_disc_allowed = $sales_price["unit_price"];
        $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
        if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
        if (!$sales_price["allow_line_disc"]) {
            /*$query = "SELECT *
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND minimum_quantity <= 1
					  	AND sales_type = 2
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		".$currency_query."
					  	AND (variant_code = '".$variant_code."'  OR variant_code = '')
					  	AND company = '".$GLOBALS['shop']['company']."'
					  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
					  ORDER BY unit_price ASC
					  LIMIT 1";*/
            $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   		WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						   		ELSE 9999999
					  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
							OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
					  	AND sales_type = 2
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		" . $currency_query . "
					  	AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
					  	AND company = '" . $GLOBALS['shop']['company'] . "'
					  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
					  ORDER BY unit_price ASC
					  LIMIT 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $sales_price = @mysqli_fetch_array($result);
                $best_price_disc_allowed = $sales_price["unit_price"];
                $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
                if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / (100 + $vat_percent)) * 100;
                        }
                    }
                } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / 100) * (100 + $vat_percent);
                        }
                    }
                }
            }
        }
    } else {
        $best_price = $item["base_price"];
        $best_price_disc_allowed = $item["base_price"];
        $best_price_aid = ($item["allow_invoice_discount"] == 1);
        $best_price_disc_allowed_aid = ($item["allow_invoice_discount"] == 1);
        //if($item['price_includes_vat'] ==0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item))
        if ($GLOBALS['item_source_shop']['prices_including_vat'] == 0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        } //elseif($item['price_includes_vat'] ==1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
        elseif ($GLOBALS['item_source_shop']['prices_including_vat'] == 1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
    }
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= 1
			  	AND sales_type = 2
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND sales_type = 2
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price["price"] = $best_price;
            $return_price["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price["price"] = $best_price;
        $return_price["allow_invoice_discount"] = $best_price_aid;
    }

    //Artikelrabattgruppen
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND minimum_quantity <= 1
				  AND sales_type = 2
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '".$variant_code."'  OR variant_code = '')
			  	  AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	  AND company = '".$GLOBALS['shop']['company']."'
			  	  AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
				  AND sales_type = 2
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
			  	  AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	  AND company = '" . $GLOBALS['shop']['company'] . "'
			  	  AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price_2["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price_2["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price_2["price"] = $best_price;
            $return_price_2["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price_2["price"] = $best_price;
        $return_price_2["allow_invoice_discount"] = $best_price_aid;
    }

    if ($return_price_2['price'] < $return_price['price']) {
        $return_price['price'] = $return_price_2['price'];
        $return_price["allow_invoice_discount"] = $return_price_2["allow_invoice_discount"];
    }
    if ($inv_disc_array) {
        return $return_price;
    } else {
        return $return_price["price"];
    }
}

//Sucht den Preis für eine bestimmte Debitorenpreis- und/oder Debitorenrabattgruppe

function get_price_customer_group($item, $currency, $price_group, $discount_group)
{
    if ($currency_code == $GLOBALS['shop_language']['default_currency_code']) {
        $currency_query = " AND (currency_code = '" . $currency_code . "' OR currency_code = '') ";
    } else {
        $currency_query = " AND currency_code = '" . $currency_code . "' ";
    }
    if ($price_group != '') {
        /*$query = "SELECT *
				  FROM shop_sales_price
				  WHERE type = 0
				  	AND item_no = '" . $item["item_no"] . "'
				  	AND minimum_quantity <= 1
				  	AND (sales_type = 1 AND sales_code='" . $price_group . "')
				  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	".$currency_query."
				  	AND (variant_code = '".$variant_code."' OR variant_code = '')
				  	AND company = '".$GLOBALS['shop']['company']."'
				  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
				  ORDER BY unit_price ASC
				  LIMIT 1";*/
        $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   		WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						  		ELSE 9999999
					  	  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
				  FROM shop_sales_price
				  WHERE type = 0
				  	AND item_no = '" . $item["item_no"] . "'
				  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
						OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
				  	AND (sales_type = 1 AND sales_code='" . $price_group . "')
				  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	" . $currency_query . "
				  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
				  	AND company = '" . $GLOBALS['shop']['company'] . "'
				  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
				  ORDER BY unit_price ASC
				  LIMIT 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_price = mysqli_fetch_array($result);
            $best_price = $sales_price["unit_price"];
            $best_price_aid = $sales_price["allow_invoice_disc"];
            $best_price_disc_allowed = $sales_price["unit_price"];
            $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
            if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
                $vatquery = "SELECT vat_percent
							 FROM shop_vat_posting_setup
							 WHERE company = '" . $GLOBALS['shop']['company'] . "'
							 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
							 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                if (mysqli_num_rows($vatresult) == 1) {
                    $vatrow = mysqli_fetch_assoc($vatresult);
                    $vat_percent = $vatrow['vat_percent'];
                    if ($vat_percent > 0) {
                        $best_price = ($best_price / (100 + $vat_percent)) * 100;
                        $best_price_disc_allowed = $best_price;
                    }
                }
            } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
                $vatquery = "SELECT vat_percent
							 FROM shop_vat_posting_setup
							 WHERE company = '" . $GLOBALS['shop']['company'] . "'
							 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
							 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                if (@mysqli_num_rows($vatresult) == 1) {
                    $vatrow = mysqli_fetch_assoc($vatresult);
                    $vat_percent = $vatrow['vat_percent'];
                    if ($vat_percent > 0) {
                        $best_price = ($best_price / 100) * (100 + $vat_percent);
                        $best_price_disc_allowed = $best_price;
                    }
                }
            }
            if (!$sales_price["allow_line_disc"]) {
                /*$query = "SELECT *
						  FROM shop_sales_price
						  WHERE type = 0
						  	AND allow_line_disc = 1
						  	AND item_no = '" . $item["item_no"] . "'
						  	AND minimum_quantity <= 1
						  	AND (sales_type = 1 AND sales_code='" . $price_group . "')
						  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
					  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
					  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
					  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
					  		".$currency_query."
						  	AND (variant_code = '".$variant_code."'  OR variant_code = '')
						  	AND company = '".$GLOBALS['shop']['company']."'
						  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
						  ORDER BY unit_price ASC
						  LIMIT 1";*/
                $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
						   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
						  			ELSE 9999999
					  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
						  FROM shop_sales_price
						  WHERE type = 0
						  	AND allow_line_disc = 1
						  	AND item_no = '" . $item["item_no"] . "'
						  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
								OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
						  	AND (sales_type = 1 AND sales_code='" . $price_group . "')
						  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
					  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
					  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
					  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
					  		" . $currency_query . "
						  	AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
						  	AND company = '" . $GLOBALS['shop']['company'] . "'
						  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
						  ORDER BY unit_price ASC
						  LIMIT 1";
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                if (@mysqli_num_rows($result) == 1) {
                    $sales_price = @mysqli_fetch_array($result);
                    $best_price_disc_allowed = $sales_price["unit_price"];
                    $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
                    if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
                        $vatquery = "SELECT vat_percent
									 FROM shop_vat_posting_setup
									 WHERE company = '" . $GLOBALS['shop']['company'] . "'
									 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
									 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                        $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                        if (mysqli_num_rows($vatresult) == 1) {
                            $vatrow = mysqli_fetch_assoc($vatresult);
                            $vat_percent = $vatrow['vat_percent'];
                            if ($vat_percent > 0) {
                                $best_price_disc_allowed = ($best_price / (100 + $vat_percent)) * 100;
                            }
                        }
                    } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
                        $vatquery = "SELECT vat_percent
									 FROM shop_vat_posting_setup
									 WHERE company = '" . $GLOBALS['shop']['company'] . "'
									 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
									 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                        $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                        if (@mysqli_num_rows($vatresult) == 1) {
                            $vatrow = mysqli_fetch_assoc($vatresult);
                            $vat_percent = $vatrow['vat_percent'];
                            if ($vat_percent > 0) {
                                $best_price_disc_allowed = ($best_price / 100) * (100 + $vat_percent);
                            }
                        }
                    }
                }
            }
        } else {
            $best_price = $item["base_price"];
            $best_price_aid = ($item["allow_invoice_discount"] == 1);
            $best_price_disc_allowed = $item["base_price"];
            $best_price_disc_allowed_aid = ($item["allow_invoice_discount"] == 1);
            //Geändert, da beim Artikel, der Haken "Preise inkl. MwSt" nicht berücksichtigt wird
            //if($item['price_includes_vat'] ==0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item))
            if ($GLOBALS['item_source_shop']['prices_including_vat'] == 0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item)) {
                $vatquery = "SELECT vat_percent
							 FROM shop_vat_posting_setup
							 WHERE company = '" . $GLOBALS['shop']['company'] . "'
							 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
							 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                if (@mysqli_num_rows($vatresult) == 1) {
                    $vatrow = mysqli_fetch_assoc($vatresult);
                    $vat_percent = $vatrow['vat_percent'];
                    if ($vat_percent > 0) {
                        $best_price = ($best_price / 100) * (100 + $vat_percent);
                        $best_price_disc_allowed = $best_price;
                    }
                }
            } //elseif($item['price_includes_vat'] ==1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
            elseif ($GLOBALS['item_source_shop']['prices_including_vat'] == 1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item)) {
                $vatquery = "SELECT vat_percent
							 FROM shop_vat_posting_setup
							 WHERE company = '" . $GLOBALS['shop']['company'] . "'
							 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
							 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                if (@mysqli_num_rows($vatresult) == 1) {
                    $vatrow = mysqli_fetch_assoc($vatresult);
                    $vat_percent = $vatrow['vat_percent'];
                    if ($vat_percent > 0) {
                        $best_price = ($best_price / (100 + $vat_percent)) * 100;
                        $best_price_disc_allowed = $best_price;
                    }
                }
            }
        }
    } else {
        $best_price = $item["base_price"];
        $best_price_aid = ($item["allow_invoice_discount"] == 1);
        $best_price_disc_allowed = $item["base_price"];
        $best_price_disc_allowed_aid = ($item["allow_invoice_discount"] == 1);
        //if($item['price_includes_vat'] ==1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
        if ($GLOBALS['item_source_shop']['prices_including_vat'] == 1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        } //elseif($item['price_includes_vat'] ==1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
        elseif ($GLOBALS['item_source_shop']['prices_including_vat'] == 1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
    }
    if ($discount_group != '') {

        /*$query = "SELECT *
				  FROM shop_sales_price
				  WHERE type = 1
				  	AND item_no = '" . $item["item_no"] . "'
				  	AND minimum_quantity <= 1
				  	AND (sales_type = 1 AND sales_code='" . $discount_group . "')
				  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	AND (variant_code = '".$variant_code."' OR variant_code = '')
				  	AND (currency_code = '".$currency_code."' OR currency_code = '')
				  	AND company = '".$GLOBALS['shop']['company']."'
				  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
				  ORDER BY line_discount DESC
				  LIMIT 1";*/
        $query = "SELECT *
				  FROM shop_sales_price
				  WHERE type = 1
				  	AND item_no = '" . $item["item_no"] . "'
				  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
						OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
				  	AND (sales_type = 1 AND sales_code='" . $discount_group . "')
				  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
				  	AND (currency_code = '" . $currency_code . "' OR currency_code = '')
				  	AND company = '" . $GLOBALS['shop']['company'] . "'
				  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
				  ORDER BY line_discount DESC
				  LIMIT 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_line_discount = mysqli_fetch_array($result);
            $best_line_discount = $sales_line_discount["line_discount"];
        }
        if ($best_line_discount > 0) {
            if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
                $return_price["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
                $return_price["allow_invoice_discount"] = $best_price_disc_allowed_aid;
            } else {
                $return_price["price"] = $best_price;
                $return_price["allow_invoice_discount"] = $best_price_aid;
            }
        } else {
            $return_price["price"] = $best_price;
            $return_price["allow_invoice_discount"] = $best_price_aid;
        }
        //Artikelrabattgruppen
        /*$query = "SELECT *
				  FROM shop_sales_price
				  WHERE type = 1
					  AND discount_group = '" . $item["discount_group"] . "'
					  AND item_no =''
					  AND minimum_quantity <= 1
					  AND (sales_type = 1 AND sales_code='" . $discount_group . "')
					  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	  AND (variant_code = '".$variant_code."'  OR variant_code = '')
				  	  AND (currency_code = '".$currency_code."' OR currency_code = '')
				  	  AND company = '".$GLOBALS['shop']['company']."'
				  	  AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
				  ORDER BY line_discount DESC
				  LIMIT 1";*/
        $query = "SELECT *
				  FROM shop_sales_price
				  WHERE type = 1
					  AND discount_group = '" . $item["discount_group"] . "'
					  AND item_no =''
					  AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
						OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
					  AND (sales_type = 1 AND sales_code='" . $discount_group . "')
					  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  	  AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
				  	  AND (currency_code = '" . $currency_code . "' OR currency_code = '')
				  	  AND company = '" . $GLOBALS['shop']['company'] . "'
				  	  AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
				  ORDER BY line_discount DESC
				  LIMIT 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_line_discount = mysqli_fetch_array($result);
            $best_line_discount = $sales_line_discount["line_discount"];
        }
    } else {
        $return_price['price'] = $best_price;
        $return_price['allow_invoice_discount'] = $best_price_aid;
    }
    if ($best_line_discount > 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price_2["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price_2["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price_2["price"] = $best_price;
            $return_price_2["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price_2["price"] = $best_price;
        $return_price_2["allow_invoice_discount"] = $best_price_aid;
    }

    if ($return_price_2['price'] < $return_price['price']) {
        $return_price['price'] = $return_price_2['price'];
        $return_price["allow_invoice_discount"] = $return_price_2["allow_invoice_discount"];
    }

    if ($inv_disc_array) {
        return $return_price;
    } else {
        return $return_price["price"];
    }
}

//Streichpreisanzeige nach neuer Logik

function get_item_cross_price($item, $type)
{
    switch ($type) {
        case 1:
            if ($item['retail_price'] > $item ['base_price']) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount($item["base_price"], FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 2:
            if ($item['retail_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no'])) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 3:
            if ($item['retail_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 4:
            if ($item ['base_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $GLOBALS['shop']['campain_no'])) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount($item["base_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 5:
            if ($item ['base_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount($item["base_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 6:
            if (get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']) > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                echo "<div class=\"itemlist1_base_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']), FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        default:
            echo "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            break;
    }
}


function return_item_cross_price_snippet($item, $type, $number_only = FALSE)
{
    switch ($type) {
        case 1:
            if ($item['retail_price'] > $item ['base_price']) {
                if ($number_only) {
                    return $item['base_price'];
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount($item["base_price"], FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 2:
            if ($item['retail_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no'])) {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 3:
            if ($item['retail_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                if ($number_only) {
                    return get_item_campaign_price($item, $GLOBALS['shop_currency']['code']);
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount($item["retail_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 4:
            if ($item ['base_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $GLOBALS['shop']['campain_no'])) {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $GLOBALS['shop']['campain_no']);
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount($item["base_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 5:
            if ($item ['base_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                if ($number_only) {
                    return get_item_campaign_price($item, $GLOBALS['shop_currency']['code']);
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount($item["base_price"], FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        case 6:
            if (get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']) > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                if ($number_only) {
                    return get_item_campaign_price($item, $GLOBALS['shop_currency']['code']);
                }
                return "<div class=\"itemlist1_base_price\">" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']), FALSE) . "</div>
					  <div class=\"itemlist1_campain_price\">" . format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) . "</div>";
            } else {
                if ($number_only) {
                    return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
                }
                return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            }
            break;
        default:
            if ($number_only) {
                return get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']);
            }
            return "" . format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) . "";
            break;
    }
}


function get_item_cross_price_card($item, $type)
{
    echo("<div class='itemcard_prices'>");
    switch ($type) {
        case 1:
            if ($item['retail_price'] > $item ['base_price']) {
                ?>
                <div class='cross_price'>
                    <?= format_amount($item['retail_price'], FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount($item['base_price'], FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        case 2:
            if ($item['retail_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no'])) {
                ?>
                <div class='cross_price'>
                    <?= format_amount($item['retail_price'], FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        case 3:
            if ($item['retail_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                ?>
                <div class='cross_price'>
                    <?= format_amount($item['retail_price'], FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        case 4:
            if ($item ['base_price'] > get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $GLOBALS['shop']['campain_no'])) {
                ?>
                <div class='cross_price'>
                    <?= format_amount($item['base_price'], FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        case 5:
            if ($item ['base_price'] > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                ?>
                <div class='cross_price'>
                    <?= format_amount($item['base_price'], FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        case 6:
            if (get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']) > get_item_campaign_price($item, $GLOBALS['shop_currency']['code'])) {
                ?>
                <div class='cross_price'>
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code']), FALSE) ?>
                </div>
                <div class="base_price">
                    <?= format_amount(get_item_campaign_price($item, $GLOBALS['shop_currency']['code']), FALSE) ?>
                </div>
                <?
            } else {
                ?>
                <div class="base_price">
                    <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
                </div>
                <?
            }
            break;
        default:
            ?>
            <div class="base_price">
                <?= format_amount(get_item_customer_price($item, $GLOBALS["shop_customer"], 1, $GLOBALS['shop_currency']['code'], '', FALSE, $GLOBALS['shop']['campain_no']), FALSE) ?>
            </div>
            <?
            break;
    }
    echo("</div>");
}

//Sucht Preis für die aktive Kampagne des Shops
function get_item_campaign_price($item, $currency_code = '', $inv_disc_array = FALSE)
{
    if ($currency_code == $GLOBALS['shop_setup']['default_currency_code']) {
        $currency_query = " AND (currency_code = '" . $currency_code . "' OR currency_code = '') ";
    } else {
        $currency_query = " AND currency_code = '" . $currency_code . "' ";
    }
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= 1
			  	AND (sales_type = 3 AND sales_code = '".$GLOBALS['shop']['campain_no']."')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	".$currency_query."
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";*/
    $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
			   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
			  			ELSE 9999999
		  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
			  FROM shop_sales_price
			  WHERE type = 0
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND (sales_type = 3 AND sales_code = '" . $GLOBALS['shop']['campain_no'] . "')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	" . $currency_query . "
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY unit_price ASC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_price = mysqli_fetch_array($result);
        $best_price = $sales_price["unit_price"];
        $best_price_aid = $sales_price["allow_invoice_disc"];
        $best_price_disc_allowed = $sales_price["unit_price"];
        $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
        if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
        if (!$sales_price["allow_line_disc"]) {
            /*$query = "SELECT *
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND minimum_quantity <= 1
					  	AND (sales_type = 3 AND sales_code = '".$GLOBALS['shop']['campain_no']."')
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		".$currency_query."
					  	AND (variant_code = '".$variant_code."'  OR variant_code = '')
					  	AND company = '".$GLOBALS['shop']['company']."'
					  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
					  ORDER BY unit_price ASC
					  LIMIT 1";*/
            $query = "SELECT (CASE WHEN unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' THEN unit_price
					   			WHEN unit_of_measure_code = '' OR unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' THEN unit_price * " . $item['multiplier'] . "
					  			ELSE 9999999
				  	  		  END) AS 'unit_price',allow_line_disc,allow_invoice_disc,price_includes_vat
					  FROM shop_sales_price
					  WHERE type = 0
					  	AND allow_line_disc = 1
					  	AND item_no = '" . $item["item_no"] . "'
					  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
							OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
					  	AND (sales_type = 3 AND sales_code = '" . $GLOBALS['shop']['campain_no'] . "')
					  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
				  			OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
				  			OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
				  		" . $currency_query . "
					  	AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
					  	AND company = '" . $GLOBALS['shop']['company'] . "'
					  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
					  ORDER BY unit_price ASC
					  LIMIT 1";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $sales_price = mysqli_fetch_array($result);
                $best_price_disc_allowed = $sales_price["unit_price"];
                $best_price_disc_allowed_aid = $sales_price["allow_invoice_disc"];
                if ($GLOBALS['shop']['prices_including_vat'] == 0 && $sales_price['price_includes_vat'] == 1 && !get_item_first_variant($item)) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / (100 + $vat_percent)) * 100;
                        }
                    }
                } elseif ($GLOBALS['shop']['prices_including_vat'] == 1 && $sales_price['price_includes_vat'] == 0 && !get_item_first_variant($item)) {
                    $vatquery = "SELECT vat_percent
								 FROM shop_vat_posting_setup
								 WHERE company = '" . $GLOBALS['shop']['company'] . "'
								 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
								 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
                    $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
                    if (@mysqli_num_rows($vatresult) == 1) {
                        $vatrow = mysqli_fetch_assoc($vatresult);
                        $vat_percent = $vatrow['vat_percent'];
                        if ($vat_percent > 0) {
                            $best_price_disc_allowed = ($best_price / 100) * (100 + $vat_percent);
                        }
                    }
                }
            }
        }
    } else {
        $best_price = $item["base_price"];
        $best_price_aid = ($item["allow_invoice_discount"] == 1);
        $best_price_disc_allowed = $item["base_price"];
        $best_price_disc_allowed_aid = ($item["allow_invoice_discount"] == 1);
        //if($item['price_includes_vat'] ==0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item))
        if ($GLOBALS['item_source_shop']['prices_including_vat'] == 0 && $GLOBALS['shop']['prices_including_vat'] == 1 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / 100) * (100 + $vat_percent);
                    $best_price_disc_allowed = $best_price;
                }
            }
        } //elseif($item['price_includes_vat'] ==1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item))
        elseif ($GLOBALS['item_source_shop']['prices_including_vat'] == 1 && $GLOBALS['shop']['prices_including_vat'] == 0 && !get_item_first_variant($item)) {
            $vatquery = "SELECT vat_percent
						 FROM shop_vat_posting_setup
						 WHERE company = '" . $GLOBALS['shop']['company'] . "'
						 	AND vat_bus_posting_group = '" . $GLOBALS['shop']['vat_bus_posting_group'] . "'
						 	AND vat_prod_posting_group = '" . $item['vat_prod_posting_group'] . "'";
            $vatresult = mysqli_query($GLOBALS['mysql_con'], $vatquery);
            if (@mysqli_num_rows($vatresult) == 1) {
                $vatrow = mysqli_fetch_assoc($vatresult);
                $vat_percent = $vatrow['vat_percent'];
                if ($vat_percent > 0) {
                    $best_price = ($best_price / (100 + $vat_percent)) * 100;
                    $best_price_disc_allowed = $best_price;
                }
            }
        }
    }
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND minimum_quantity <= 1
			  	AND (sales_type = 3 AND sales_code = '".$GLOBALS['shop']['campain_no']."')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '".$variant_code."' OR variant_code = '')
			  	AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	AND company = '".$GLOBALS['shop']['company']."'
			  	AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
			  	AND item_no = '" . $item["item_no"] . "'
			  	AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
			  	AND (sales_type = 3 AND sales_code = '" . $GLOBALS['shop']['campain_no'] . "')
			  	AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	AND (variant_code = '" . $variant_code . "' OR variant_code = '')
			  	AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	AND company = '" . $GLOBALS['shop']['company'] . "'
			  	AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price["price"] = $best_price;
            $return_price["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price["price"] = $best_price;
        $return_price["allow_invoice_discount"] = $best_price_aid;
    }

    //Artikelrabattgruppen
    /*$query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND minimum_quantity <= 1
				  AND (sales_type = 3 AND sales_code = '".$GLOBALS['shop']['campain_no']."')
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '".$variant_code."'  OR variant_code = '')
			  	  AND (currency_code = '".$currency_code."' OR currency_code = '')
			  	  AND company = '".$GLOBALS['shop']['company']."'
			  	  AND (unit_of_measure_code = '".$item['unit_of_measure_code']."' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";*/
    $query = "SELECT *
			  FROM shop_sales_price
			  WHERE type = 1
				  AND discount_group = '" . $item["discount_group"] . "'
				  AND item_no =''
				  AND ((minimum_quantity <= 1 AND unit_of_measure_code = '" . $item['unit_of_measure_code'] . "')
					OR(minimum_quantity <= (1*" . $item['multiplier'] . ") AND (unit_of_measure_code = '" . $item['nav_base_unit_code'] . "' OR unit_of_measure_code = '')))
				  AND (sales_type = 3 AND sales_code = '" . $GLOBALS['shop']['campain_no'] . "')
				  AND (isnull(`shop_sales_price`.`starting_date`) AND isnull(`shop_sales_price`.`ending_date`)
			  		OR (isnull(`shop_sales_price`.`starting_date`) AND (`shop_sales_price`.`ending_date` >= curdate())
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND isnull(`shop_sales_price`.`ending_date`))
			  		OR (`shop_sales_price`.`starting_date` <= curdate()) AND (`shop_sales_price`.`ending_date` >= curdate()))
			  	  AND (variant_code = '" . $variant_code . "'  OR variant_code = '')
			  	  AND (currency_code = '" . $currency_code . "' OR currency_code = '')
			  	  AND company = '" . $GLOBALS['shop']['company'] . "'
			  	  AND (unit_of_measure_code = '" . $item['unit_of_measure_code'] . "' OR unit_of_measure_code = '')
			  ORDER BY line_discount DESC
			  LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $sales_line_discount = mysqli_fetch_array($result);
        $best_line_discount = $sales_line_discount["line_discount"];
    }
    if ($best_line_discount > 0) {
        if ($best_price >= ($best_price_disc_allowed / 100 * (100 - $best_line_discount))) {
            $return_price_2["price"] = ($best_price_disc_allowed / 100 * (100 - $best_line_discount));
            $return_price_2["allow_invoice_discount"] = $best_price_disc_allowed_aid;
        } else {
            $return_price_2["price"] = $best_price;
            $return_price_2["allow_invoice_discount"] = $best_price_aid;
        }
    } else {
        $return_price_2["price"] = $best_price;
        $return_price_2["allow_invoice_discount"] = $best_price_aid;
    }

    if ($return_price_2['price'] < $return_price['price']) {
        $return_price['price'] = $return_price_2['price'];
        $return_price["allow_invoice_discount"] = $return_price_2["allow_invoice_discount"];
    }
    if ($inv_disc_array) {
        return $return_price;
    } else {
        return $return_price["price"];
    }
}

function get_variant_translation($item_no, $variant_code)
{
    $query = "SELECT 
			  CASE 
				WHEN ISNULL(shop_item_variant_translation.id) THEN shop_item_variant.description
				ELSE shop_item_variant_translation.description
			  END AS 'description'
			  FROM shop_item_variant 
			  LEFT JOIN shop_item_variant_translation ON shop_item_variant_translation.company=shop_item_variant.company AND shop_item_variant_translation.item_no=shop_item_variant.item_no AND shop_item_variant_translation.language_code='" . $GLOBALS["shop_language"]["code"] . "'			  
			  WHERE shop_item_variant.item_no = '" . $item_no . "'
			  	AND shop_item_variant.code = '" . $variant_code . "'
			  	AND shop_item_variant.company = '" . $GLOBALS['shop']['company'] . "'";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['description'];
    } else {
        return '';
    }
}


function show_basket_button_card($action)
{
    ?>
    <form name="add_item_to_basket" class="orderbox_basket_button" id="add_item_to_basket" method="POST"
          action="<?= $action ?>">
        <input name="input_item_id" id="input_item_id" type="hidden" value="<?= $id ?>"></input>
        <div class="basket_button">
            <div class="input">
                <input type="text" name="input_item_quantity" id="input_item_quantity" value="1"></input>
                <div class="spinner_up" onclick="document.add_item_to_basket.input_item_quantity.value++;">+</div>
                <div class="spinner_down"
                     onclick="if (document.add_item_to_basket.input_item_quantity.value == 1) {} else { document.add_item_to_basket.input_item_quantity.value--; };">
                    -
                </div>
            </div>
            <div class="button">
                <input type="submit" value="<?= $GLOBALS["tc"]["add_to_basket"] ?>" id="add_to_basket">
                <i class="fa fa-shopping-cart"></i>
            </div>
        </div>
    </form>
    <?
    $GLOBALS["input_counter"]++;
}


function show_basket_button($action, $id)
{
    ?>
    <form name="add_item_to_basket_<?= $GLOBALS["input_counter"]; ?>"
          id="add_item_to_basket_<?= $GLOBALS["input_counter"]; ?>" method="POST" action="<?= $action ?>">
        <input name="input_item_id_<?= $GLOBALS["input_counter"] ?>" id="input_item_id_<?= $GLOBALS["input_counter"] ?>"
               type="hidden" value="<?= $id ?>"></input>

        <div class="basket_button">
            <div class="col1">
                <input type="text" name="input_item_quantity_<?= $GLOBALS["input_counter"]; ?>"
                       id="input_item_quantity_<?= $GLOBALS["input_counter"]; ?>" value="1"></input>
            </div>
            <div class="col2">
                <div class="spinner_up"
                     onclick="document.add_item_to_basket_<?= $GLOBALS["input_counter"]; ?>.input_item_quantity_<?= $GLOBALS["input_counter"]; ?>.value++;"></div>
                <div class="spinner_down"
                     onclick="if (document.add_item_to_basket_<?= $GLOBALS["input_counter"]; ?>.input_item_quantity_<?= $GLOBALS["input_counter"]; ?>.value == 1) {} else { document.add_item_to_basket_<?= $GLOBALS["input_counter"]; ?>.input_item_quantity_<?= $GLOBALS["input_counter"]; ?>.value--; };"></div>
            </div>
            <div class="col3">
                <input type="submit" value="<?= $GLOBALS["tc"]["add_to_basket"] ?>" id="add_to_basket">
            </div>
        </div>
    </form>
    <?
    $GLOBALS["input_counter"]++;
}

function get_all_item_nos_for_item($item)
{
    $parent_snippet = (strlen($item["parent_item_no"]) > 0 ? "OR item_no='" . $item["parent_item_no"] . "'" : "");

    $itemno_query = "SELECT DISTINCT item_no 
	FROM shop_item
	WHERE
		company = '" . $GLOBALS["shop"]["company"] . "'
	  AND
		shop_code = '" . $GLOBALS["shop"]["code"] . "'
	  AND
		language_code = '" . $GLOBALS["shop_language"]["code"] . "'
	  AND
		(
			item_no = '" . $item["item_no"] . "'
		  OR
		    parent_item_no = '" . $item["item_no"] . "'
		  " . $parent_snippet . "
		)
	";


    $itemno_result = mysqli_query($GLOBALS['mysql_con'], $itemno_query);
    if (@mysqli_num_rows($itemno_result) > 0) {
        mysqli_data_seek($itemno_result, 0);
        $itemnos = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($itemno_result)) {
            $itemnos[] = $row["item_no"];
        }
        return $itemnos;
    } else {
        return FALSE;
    }
}

function create_general_item_path($item, $withItem = true)
{
    $curr_category = (!empty($GLOBALS['category']['id']) ? $GLOBALS['category'] : FALSE);
    $_SESSION['current_item_category'] = $curr_category;
    return "/".customizeUrl()."/". $item['item_slug'] . '-p' . $item['id'] . "/";
}

function get_item_default_category($item)
{
    $category = FALSE;
    if (!empty($item['main_category_line_no']) && $item['main_category_line_no'] > 0) {
        $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$item['main_category_line_no']);
    } else {
        $oldquery = "
            SELECT 
              * 
            FROM 
              shop_category 
            WHERE 
                    company='" . $GLOBALS['shop']['company'] . "' 
                AND shop_code='" . $GLOBALS['shop']['category_source'] . "' 
                AND language_code='" . $GLOBALS['shop_language']['code'] . "' 
                AND line_no = 
                    (
                        SELECT 
                            category_line_no 
                        FROM 
                            shop_item_has_category 
                        WHERE 
                                company=shop_category.company 
                            AND shop_code = '" . $GLOBALS['shop']['item_source'] . "' 
                            AND category_shop_code = shop_category.shop_code 
                            AND category_language_code = shop_category.language_code 
                        ORDER BY 
                            id 
                        LIMIT 1
                    ) 
                LIMIT 1";
        $query = "
           SELECT shop_category.* FROM shop_category
           INNER JOIN shop_item_has_category ON shop_category.line_no = shop_item_has_category.category_line_no
                AND shop_item_has_category.company = shop_category.company
                AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                AND shop_item_has_category.category_shop_code=shop_category.shop_code
                AND shop_item_has_category.category_language_code=shop_category.language_code
           WHERE shop_category.company='" . $GLOBALS['shop']['company'] . "'
            AND shop_category.shop_code='" . $GLOBALS['shop']['category_source'] . "'
            AND shop_category.language_code='" . $GLOBALS['shop_language']['code'] . "'
            AND (
                    shop_item_has_category.item_no='" . $item['item_no'] . "'
                OR  shop_item_has_category.item_no='" . $item['parent_item_no'] . "'
            )
           AND shop_category.active=1
           ORDER BY shop_item_has_category.id
           LIMIT 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $category = mysqli_fetch_assoc($result);
        $error = mysqli_error($GLOBALS['mysql_con']);
    }
    return $category;
}

function replace_item_placeholders_($text, $company, $item_shop_code, $item_lang_code, $item_no, $parent_item_no, $variant_code, $shop_variant_type, $document_root, array $image_config, $image_size_index, $mysqli_conn)
{
    $document_root = rtrim($document_root, '\/');
    $item = get_item_clean($company, $item_shop_code, $item_lang_code, $item_no, $mysqli_conn);
    $item['prepared_base_price'] = (isset($item['base_price']) && (float)$item['base_price'] > 0) ? number_format($item['base_price'], 2, ',', '.') : '';
    $item['prepared_retail_price'] = (isset($item['retail_price']) && (float)$item['retail_price'] > 0) ? number_format($item['retail_price'], 2, ',', '.') : '';

    $images = get_item_images($company, $shop_variant_type, $item_shop_code, $item_lang_code, $item_no, $parent_item_no, $variant_code, $mysqli_conn);
    foreach ($images as &$imageArr) {
        $imageArr['filepath'] = $GLOBALS['projectRoot'] . DIRECTORY_SEPARATOR . $image_config[$image_size_index] . DIRECTORY_SEPARATOR . $imageArr['filename'];
        $imageArr['html'] = '<div class="item_image_wrapper"><div class="item_image"><img border="0" src="' . $imageArr['filepath'] . '" alt="' . urlencode($imageArr['description']) . '" /></div></div>';
    }
    unset($imageArr);
    $item['images'] = $images;

    $descriptions = get_item_descriptions($company, $item_shop_code, $item_lang_code, $item_no, $parent_item_no, $mysqli_conn);
    foreach ($descriptions as &$descriptionArr) {
        $descriptionArr['html'] = '<div class="item_description_wrapper"><div class="item_description">' . $descriptionArr['content'] . '</div></div>';
    }
    unset($descriptionArr);
    $item['descriptions'] = $descriptions;

    $placeholders = [
        'item.no' => safe_array_dot_access($item, 'no', ''),
        'item.description' => safe_array_dot_access($item, 'description', ''),
        'item.descriptions.1' => safe_array_dot_access($item, 'descriptions.1.html', ''),
        'item.descriptions.2' => safe_array_dot_access($item, 'descriptions.2.html', ''),
        'item.descriptions.3' => safe_array_dot_access($item, 'descriptions.3.html', ''),
        'item.descriptions.4' => safe_array_dot_access($item, 'descriptions.4.html', ''),
        'item.descriptions.5' => safe_array_dot_access($item, 'descriptions.5.html', ''),
        'item.descriptions.6' => safe_array_dot_access($item, 'descriptions.6.html', ''),
        'item.descriptions.7' => safe_array_dot_access($item, 'descriptions.7.html', ''),
        'item.descriptions.8' => safe_array_dot_access($item, 'descriptions.8.html', ''),
        'item.descriptions.9' => safe_array_dot_access($item, 'descriptions.9.html', ''),
        'item.descriptions.10' => safe_array_dot_access($item, 'descriptions.10.html', ''),
        'item.images.1' => safe_array_dot_access($item, 'images.1.html', ''),
        'item.images.2' => safe_array_dot_access($item, 'images.2.html', ''),
        'item.images.3' => safe_array_dot_access($item, 'images.3.html', ''),
        'item.images.4' => safe_array_dot_access($item, 'images.4.html', ''),
        'item.images.5' => safe_array_dot_access($item, 'images.5.html', ''),
        'item.images.6' => safe_array_dot_access($item, 'images.6.html', ''),
        'item.images.7' => safe_array_dot_access($item, 'images.7.html', ''),
        'item.images.8' => safe_array_dot_access($item, 'images.8.html', ''),
        'item.images.9' => safe_array_dot_access($item, 'images.9.html', ''),
        'item.images.10' => safe_array_dot_access($item, 'images.10.html', ''),
        'item.summary' => safe_array_dot_access($item, 'summary', ''),
        'item.base_price' => safe_array_dot_access($item, 'prepared_base_price'),
        'item.retail_price' => safe_array_dot_access($item, 'prepared_base_price'),
    ];

    $newText = $text;
    foreach ($placeholders as $placeholder => $replacementValue) {
        $pattern = '%' . $placeholder . '%';
        $newText = str_replace($pattern, $replacementValue, $newText);
    }
    return $newText;

}

// Funktion zum Anzeigen der Artikel-Bildergalerie auf der Artikelkarte
function get_item_images($company, $shop_variant_type, $item_shop_code, $item_language_code, $item_no, $parent_item_no, $variant_code, $mysqli_conn)
{
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.company = '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
			  	AND shop_item_file.shop_code = '" . mysqli_real_escape_string($mysqli_conn, $item_shop_code) . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
			  		AND (shop_item_file.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . mysqli_real_escape_string($mysqli_conn, $parent_item_no) . "'
			  		AND (shop_item_file.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . mysqli_real_escape_string($mysqli_conn, $variant_code) . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";


    $images = [];
    $result = @mysqli_query($mysqli_conn, $query);
    if (@mysqli_num_rows($result) > 1) {
        $images = mysqli_fetch_array($result);
    }
    return $images;
}

// Funktion zum Anzeigen der Artikel-Bildergalerie auf der Artikelkarte
function get_item_images_clean_new($company, $shop_variant_type, $item_shop_code, $item_language_code, $item_no, $parent_item_no, $variant_code, $mysqli_conn)
{
    $query = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.company = '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
			  	AND shop_item_file.shop_code = '" . mysqli_real_escape_string($mysqli_conn, $item_shop_code) . "'
			  	AND shop_item_file.filename <> ''
			  	AND ((shop_item.item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
			  		AND (shop_item_file.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "' OR shop_item_file.all_language_codes = TRUE))
			  	OR (parent_shop_item.item_no = '" . mysqli_real_escape_string($mysqli_conn, $parent_item_no) . "'
			  		AND (shop_item_file.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "' OR shop_item_file.all_language_codes = TRUE)))
				AND (shop_item_file.variant_code = '" . mysqli_real_escape_string($mysqli_conn, $variant_code) . "' OR shop_item_file.variant_code='')
			  ORDER BY line_no";

    $result = @mysqli_query($mysqli_conn, $query);
    $array = [];
    while ($row = @mysqli_fetch_assoc($result)) {
        $array[] = $row;
    }
    return $array;
}

//Sucht den Muterartikel, sonst false
function get_item_variant_parent_clean($company, $variant_type, $item_no, $item_shop_code, $item_language_code, $mysqli_conn)
{
    if ((int)$variant_type !== '2') {
        $query = "SELECT shop_view_active_item.*
				  FROM shop_item_link
				  INNER JOIN shop_view_active_item ON shop_view_active_item.item_no = shop_item_link.item_no
				  WHERE shop_item_link.type = '0'
				  	AND shop_item_link.linked_item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
				  	AND shop_view_active_item.company= '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
				  	AND shop_view_active_item.shop_code= '" . mysqli_real_escape_string($mysqli_conn, $item_shop_code) . "'
				  	AND shop_view_active_item.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "'
				  LIMIT 1";
        // echo "<!-- PARENT-QUERY: $query -->";
    } else {
        return get_item($company, $item_shop_code, $item_language_code, $item_no);
    }
    $result = @mysqli_query($mysqli_conn, $query);
    if (@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    } else {
        return false;
    }
}

function get_item_clean($company, $shop_code, $language_code, $item_no, $mysqli_conn)
{
    $query = "SELECT *
    		  FROM shop_item
    		  WHERE item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
    		  	AND company = '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
    		  	AND shop_code = '" . mysqli_real_escape_string($mysqli_conn, $shop_code) . "'
    		  	AND language_code = '" . mysqli_real_escape_string($mysqli_conn, $language_code) . "'
    		  LIMIT 1";
    $result = @mysqli_query($mysqli_conn, $query);
    if (@mysqli_num_rows($result) == 1) {
        $item = mysqli_fetch_array($result);
        return $item;
    }
    return false;
}

function get_item_descriptions($company, $item_shop_code, $item_language_code, $item_no, $parent_item_no, $mysqli_conn)
{
    $descriptions_query = "SELECT DISTINCT shop_item_description.*
					  FROM shop_item_description
					  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
					  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
					  WHERE shop_item_description.show_in_header = 0
					  	AND shop_item_description.company = '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
					  	AND shop_item_description.shop_code = '" . mysqli_real_escape_string($mysqli_conn, $item_shop_code) . "'
					  	AND shop_item_description.marketplace_only = 0
					  	AND ((shop_item_description.item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
					  		AND (shop_item_description.all_language_codes = TRUE 
				  				OR shop_item_description.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "')
					  	)
					  	OR	(shop_item_description.item_no = '" . mysqli_real_escape_string($mysqli_conn, $parent_item_no) . "'
					  		AND (shop_item_description.all_language_codes = TRUE 
				  				OR shop_item_description.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "')
					  	))
						ORDER BY shop_item_description.line_no ASC
					  	";
    $descriptions = [];
    $result = @mysqli_query($mysqli_conn, $descriptions_query);
    if (@mysqli_num_rows($result) > 0) {
        $descriptions = mysqli_fetch_array($result);
    }
    return $descriptions;
}

function get_item_descriptions_marketplace($company, $item_shop_code, $item_language_code, $item_no, $parent_item_no, $mysqli_conn)
{
    $descriptions_query = "SELECT DISTINCT shop_item_description.*
					  FROM shop_item_description
					  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
					  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item.parent_item_no
					  WHERE shop_item_description.show_in_header = 0
					  	AND shop_item_description.company = '" . mysqli_real_escape_string($mysqli_conn, $company) . "'
					  	AND shop_item_description.shop_code = '" . mysqli_real_escape_string($mysqli_conn, $item_shop_code) . "'
					  	AND shop_item_description.marketplace_only = 1
					  	AND ((shop_item_description.item_no = '" . mysqli_real_escape_string($mysqli_conn, $item_no) . "'
					  		AND (shop_item_description.all_language_codes = TRUE 
				  				OR shop_item_description.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "')
					  	)
					  	OR	(shop_item_description.item_no = '" . mysqli_real_escape_string($mysqli_conn, $parent_item_no) . "'
					  		AND (shop_item_description.all_language_codes = TRUE 
				  				OR shop_item_description.language_code = '" . mysqli_real_escape_string($mysqli_conn, $item_language_code) . "')
					  	))
						ORDER BY shop_item_description.line_no ASC
					  	";
    $descriptions = [];
    $result = @mysqli_query($mysqli_conn, $descriptions_query);
    /*if (@mysqli_num_rows($result) > 0) {
        $descriptions = mysqli_fetch_array($result);
    }*/
    return $result;
}

//Sucht Kampagnenbanner für einen Artikel
function show_item_campaign_banners($item, $showDiscountText = FALSE, $showDiscountTextDescription = FALSE, $showDiscountIcon = TRUE)
{
    if (array_key_exists('notifications', $item)) {
        if (array_key_exists('discounts', $item['notifications']) && $showDiscountText) {
            $count = count($item['notifications']['discounts']);
            $max = ($count - 1) > 3 ? 3 : $count - 1;
            for ($i = 0; $i <= $max; $i++) {
                $text = $item['notifications']['discounts'][$i]['text'];
                $value = $item['notifications']['discounts'][$i]['value'];
                $escapedText = htmlentities($text);
                $escapedValue = htmlentities($value);
                echo "<div class=\"item_campaign_wrapper campaign_discount_wrapper\">";
                echo("<div class=\"item_campaign_banner campaign_discount_banner\">-$escapedValue</div>");
                if ($showDiscountTextDescription) {
                    echo("<div class=\"item_campaign_text campaign_discount_text\">$escapedText</div>");
                }
                echo "</div>";
            }
        }

        if (array_key_exists('conditions', $item['notifications']) && $showDiscountIcon) {
            $count = count($item['notifications']['conditions']);
            $max = ($count - 1) > 3 ? 3 : $count - 1;
            for ($i = 0; $i <= $max; $i++) {
                $text = $item['notifications']['conditions'][$i]['text'];
                $icon = $item['notifications']['conditions'][$i]['icon'];
                $banner = $item['notifications']['conditions'][$i]['banner'];
                $escapedText = htmlentities($text);
                echo "<div class=\"item_campaign_wrapper campaign_condition_item_wrapper\">";
                if (file_exists(rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . $icon) && !empty($icon)) {
                    echo("<div class=\"item_campaign_banner campaign_condition_item_banner\"><img src=\"$icon\" alt=\"$escapedText\"/></div>");
                }
                if (file_exists(rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . $banner) && !empty($banner)) {
                    echo("<div class=\"item_campaign_banner campaign_condition_item_banner\"><img src=\"$banner\" alt=\"$escapedText\"/></div>");
                }
                if ($showDiscountTextDescription) {
                    echo("<div class=\"item_campaign_text campaign_condition_text\">$text</div>");
                }
                echo "</div>";
            }
        }

        if (array_key_exists('actions', $item['notifications']) && $showDiscountIcon) {
            $count = count($item['notifications']['actions']);
            $max = ($count - 1) > 3 ? 3 : $count - 1;
            for ($i = 0; $i <= $max; $i++) {
                $text = $item['notifications']['actions'][$i]['text'];
                $icon = $item['notifications']['actions'][$i]['icon'];
                $banner = $item['notifications']['actions'][$i]['banner'];
                $escapedText = htmlentities($text);

                echo "<div class=\"item_campaign_wrapper campaign_actions_item_wrapper\">";
                if (file_exists(rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . $icon) && !empty($icon)) {
                    echo("<div class=\"item_campaign_banner campaign_actions_item_banner\"><img src=\"$icon\" alt=\"$escapedText\"/></div>");
                }
                if (file_exists(rtrim(dirname(dirname(dirname(__DIR__))),'/\\') . $banner) && !empty($banner)) {
                    echo("<div class=\"item_campaign_banner campaign_actions_item_banner\"><img src=\"$banner\" alt=\"$escapedText\"/></div>");
                }
                if ($showDiscountTextDescription) {
                    echo("<div class=\"item_campaign_text campaign_actions_text\">$text</div>");
                }
                echo "</div>";
            }
        }
    }
}

function get_gift_items()
{
    $shopCode = $GLOBALS["shop"]["item_source"];
    $shopLanguage = $GLOBALS["shop_language"]["code"];
    $shopCompany = $GLOBALS['shop']['company'];
    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $prepStatement = "
          SELECT
                shop_view_active_item.*
          FROM shop_view_active_item
              WHERE  shop_view_active_item.shop_code = :shopCode
            AND shop_view_active_item.language_code = :languageCode
            AND shop_view_active_item.company = :company
            AND shop_view_active_item.is_gift_package = 1
            ";
    $params = [
        [':shopCode', $shopCode, PDO::PARAM_STR],
        [':languageCode', $shopLanguage, PDO::PARAM_STR],
        [':company', $shopCompany, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();
    return $resultArray;
}

function get_greeting_items()
{
    $shopCode = $GLOBALS["shop"]["item_source"];
    $shopLanguage = $GLOBALS["shop_language"]["code"];
    $shopCompany = $GLOBALS['shop']['company'];
    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $prepStatement = "
          SELECT
                shop_view_active_item.*
          FROM shop_view_active_item
              WHERE  shop_view_active_item.shop_code = :shopCode
            AND shop_view_active_item.language_code = :languageCode
            AND shop_view_active_item.company = :company
            AND shop_view_active_item.is_greeting_card = 1
            ";
    $params = [
        [':shopCode', $shopCode, PDO::PARAM_STR],
        [':languageCode', $shopLanguage, PDO::PARAM_STR],
        [':company', $shopCompany, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();
    return $resultArray;
}

function sort_array_by_customer_price(array &$array)
{
    usort($array, function ($a, $b) {
        return (float)$a['customer_price'] < (float)$b['customer_price'] ? -1 : 1;

    });
}
function get_item_has_categories($item) {
    // nur eine kategorie pro marktplatz erlaubt, deswegen limit 1

    $query = "SELECT shop_category.*
            FROM  shop_category
			inner join shop_item_has_category 	
			  ON shop_item_has_category.category_line_no = shop_category.line_no
			       AND   shop_item_has_category.company = shop_category.company
                   AND   shop_item_has_category.shop_code = shop_category.shop_code
                   AND   shop_item_has_category.language_code = shop_category.language_code
			WHERE shop_item_has_category.item_no = '" . $item['item_no'] . "'
			AND shop_item_has_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item_has_category.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item_has_category.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND shop_item_has_category.category_shop_code = '" . $GLOBALS["shop"]["category_source"] . "'
			AND shop_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_category.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_category.shop_code = '" . $GLOBALS["shop"]["category_source"] . "'
		order by shop_item_has_category.sorting, shop_item_has_category.category_line_no asc
	";
    $categories = [];
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
        while ($category = mysqli_fetch_assoc($result)) {
            $categories[count($categories)] = $category;
        }
    return $categories;
}


function get_item_slug() {
    // nur eine kategorie pro marktplatz erlaubt, deswegen limit 1

    $query = " Select id,description from shop_item  WHERE description <> ''";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    $seek = array(
        '=',
        '[',
        ']',
        '^',
        '`',
        '{',
        '|',
        '}',
        '~',
        '',
        '`',
        '?',
        '‚',
        'ƒ',
        '„',
        '…',
        '†',
        '‡',
        'ˆ',
        '‰',
        'Š',
        '‹',
        'Œ',
        '?',
        'Ž',
        '?',
        '?',
        '‘',
        '’',
        '“',
        '”',
        '•',
        '–',
        '—',
        '˜',
        '™',
        'š',
        '›',
        'œ',
        '?',
        'ž',
        'Ÿ',
        '',
        '¡',
        '¢',
        '£',
        '¤',
        '¥',
        '¦',
        '§',
        '¨',
        '©',
        'ª',
        '«',
        '¬',
        '',
        '®',
        '¯',
        '°',
        '±',
        '²',
        '³',
        '´',
        'µ',
        '¶',
        '·',
        '¸',
        '¹',
        'º',
        '»',
        '¼',
        '½',
        '¾',
        '¿',
        'À',
        'Á',
        'Â',
        'Ã',
        'Ä',
        'Å',
        'Æ',
        'Ç',
        'È',
        'É',
        'Ê',
        'Ë',
        'Ì',
        'Í',
        'Î',
        'Ï',
        'Ð',
        'Ñ',
        'Ò',
        'Ó',
        'Ô',
        'Õ',
        'Ö',
        '×',
        'Ø',
        'Ù',
        'Ú',
        'Û',
        'Ü',
        'Ý',
        'Þ',
        'ß',
        'à',
        'á',
        'â',
        'ã',
        'ä',
        'å',
        'æ',
        'ç',
        'è',
        'é',
        'ê',
        'ë',
        'ì',
        'í',
        'î',
        'ï',
        'ð',
        'ñ',
        'ò',
        'ó',
        'ô',
        'õ',
        'ö',
        '÷',
        'ø',
        'ù',
        'ú',
        'û',
        'ü',
        'ý',
        'þ',
        'ÿ',
        'ä',
        'Ö',
        'ö',
        'Ü',
        'ü',
        'ß',
        '%',
        '°',
        '/',
        '\\',
        '.',
        ',',
        ';',
        ':',
        '#',
        ' ',
        '\'',
        '+',
        '"'
    );

    $replace = array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        'S',
        '',
        'CE',
        '',
        'Z',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        'S',
        '',
        'ce',
        '',
        'Z',
        'Y',
        '',
        '',
        '',
        '',
        '',
        'Y',
        '',
        '',
        '',
        '',
        'A',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        'A',
        'A',
        'A',
        'A',
        'A',
        'A',
        'AE',
        'C',
        'E',
        'E',
        'E',
        'E',
        'I',
        'I',
        'I',
        'I',
        'D',
        'N',
        'O',
        'O',
        'O',
        'O',
        'O',
        'x',
        '',
        'U',
        'U',
        'U',
        'U',
        'Y',
        '',
        'ss',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        '',
        'c',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        '',
        'n',
        'o',
        'o',
        'o',
        'o',
        'o',
        '',
        '',
        'u',
        'u',
        'u',
        'u',
        'y',
        '',
        'y',
        'ae',
        'Oe',
        'oe',
        'Ue',
        'ue',
        'ss',
        'proz',
        '-Grad',
        '-',
        '-',
        '-',
        '-',
        '-',
        '-',
        '-',
        '-',
        '-',
        '-',
        ''
    );

    while ($item = mysqli_fetch_assoc($result)) {

        $item_description = str_replace($seek, $replace, $item["description"]);
        $item_description = str_replace("&Acirc;", "", $item_description);
        $item_description = str_replace("&acirc;", "", $item_description);
        $item_description = preg_replace('/-{2,}/','-',$item_description);
        $item_description = trim($item_description, "-");
        $item_description = strtolower($item_description);

        $updateQuery =  "update shop_item set item_slug='".$item_description."' where id =".$item['id'];
        mysqli_query($GLOBALS['mysql_con'], $updateQuery);
    }
}
