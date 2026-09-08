 <?php

function get_all_items_to_update($field = "marketplace_update") {
    $query = "SELECT shop_item.*, smiu.marketplace_standalone_product as marketplace_standalone_product_per_shop, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, (SELECT 
			content 
		FROM 
			shop_item_description sid 
		WHERE 
			sid.company = shop_item.company 
		AND 
			sid.shop_code = shop_item.shop_code 
		AND 
			sid.language_code = shop_item.language_code 
		AND 
			sid.item_no = shop_item.item_no 
		AND 
			sid.marketplace_title = 1) AS marketplace_title FROM shop_item 
			RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
			WHERE smiu.". $field ." = 1 
			
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND smiu.active = 1
		order by id asc
	";

    $query = "SELECT shop_item.*, smiu.marketplace_standalone_product as marketplace_standalone_product_per_shop, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, shop_item.site_title AS marketplace_title FROM shop_item 
            RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
            WHERE smiu.". $field ." = 1 
            
            AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND smiu.active = 1
		order by id asc
	";



    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    return $result;
}

function get_all_new_items() {
    $query = "SELECT shop_item.*, smiu.marketplace_standalone_product as marketplace_standalone_product_per_shop, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, (SELECT 
			content 
		FROM 
			shop_item_description sid 
		WHERE 
			sid.company = shop_item.company 
		AND 
			sid.shop_code = shop_item.shop_code 
		AND 
			sid.language_code = shop_item.language_code 
		AND 
			sid.item_no = shop_item.item_no 
		AND 
			sid.marketplace_title = 1) AS marketplace_title FROM shop_item 
			RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
			WHERE smiu.marketplace_update = 1 
			
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND smiu.marketplace_last_update = '0000-00-00 00:00:00'
			AND shop_item.active = 1
		order by id asc
	";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    return $result;
}

function get_all_existing_items($filter_query = 'smiu.marketplace_update = 1 OR smiu.marketplace_update_images = 1 OR smiu.marketplace_update_price = 1 OR smiu.marketplace_update_inventory = 1') {
    /*$query = "SELECT shop_item.*, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, (SELECT
			content 
		FROM 
			shop_item_description sid 
		WHERE 
			sid.company = shop_item.company 
		AND 
			sid.shop_code = shop_item.shop_code 
		AND 
			sid.language_code = shop_item.language_code 
		AND 
			sid.item_no = shop_item.item_no 
		AND 
			sid.marketplace_title = 1) AS marketplace_title FROM shop_item 
			RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
			WHERE (".$filter_query.")
			
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND (smiu.marketplace_last_update != '0000-00-00 00:00:00' OR shop_item.invonly = 1)
			AND smiu.active = 1
		order by id asc
	";*/

    $query = "SELECT shop_item.*, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, shop_item.site_title AS marketplace_title
            FROM shop_item 
			RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
			WHERE (".$filter_query.")
			
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND (smiu.marketplace_last_update != '0000-00-00 00:00:00' OR shop_item.invonly = 1)
			AND smiu.active = 1
		order by id asc
	";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    return $result;
}

function get_item_result($item_no, $fetch = false) {

    $query = "SELECT shop_item.*, smiu.marketplace_standalone_product as marketplace_standalone_product_per_shop, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, shop_item.site_title AS marketplace_title 
            FROM shop_item 
            RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
            WHERE item_no = '" . $item_no . "' 
			
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			AND smiu.active = 1
	";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    if ($fetch && @mysqli_num_rows($result) == 1) {
        $item = mysqli_fetch_array($result);
        return $item;
    }

    return $result;
}

function get_items_for_relationship() {
    $query = "SELECT * FROM shop_item 
            RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
WHERE smiu.marketplace_start_relationship = 1
			AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
	";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function get_category_variant_attributes($category, $marketplace_type, $product_type=array()) {
    $query = "SELECT * FROM shop_marketplace_variant_attributes where category_code = '" . $category . "' and marketplace_type = " . $marketplace_type . " and is_variation = 1";
    if(count($product_type) > 0) {
        $query .= " and productType IN ('" . join("', '", $product_type) . "')";
    }

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $variant_codes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $variant_codes[] = $row['description'];
    }

    return $variant_codes;
}

function get_category_attributes($category, $marketplace_type, $product_type=array()) {
    $query = "SELECT description FROM shop_marketplace_variant_attributes where category_code = '" . $category . "' and marketplace_type = '" . $marketplace_type."'";
    if(count($product_type) > 0) {
        $query .= " and productType IN ('" . join("', '", $product_type) . "')";
    }

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $attributes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $attributes[] = $row['description'];
    }

    return $attributes;
}

function get_category_normal_attributes($category, $marketplace_type, $product_type=array()) {
    $query = "SELECT description FROM shop_marketplace_variant_attributes where category_code = '" . $category . "' and marketplace_type = '" . $marketplace_type . "' and is_variation = 0";
    if(count($product_type) > 0) {
        $query .= " and productType IN ('" . join("', '", $product_type) . "')";
    }

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $attributes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $attributes[] = $row['description'];
    }

    return $attributes;
}

function get_item_has_category($item) {
    // nur eine kategorie pro marktplatz erlaubt, deswegen limit 1

    $query = "SELECT shop_item_has_category.*, shop_category.code as 'marketplace_category_code', shop_marketplace_categories.root_category_code FROM shop_item_has_category 
			inner join shop_category 				ON shop_item_has_category.category_line_no = shop_category.line_no
			inner join shop_marketplace_categories 	ON shop_marketplace_categories.category_code = shop_category.code
			WHERE shop_item_has_category.item_no = '" . $item['item_no'] . "'
			AND shop_item_has_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_item_has_category.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_item_has_category.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND shop_item_has_category.category_shop_code = '" . $GLOBALS["shop"]["category_source"] . "'
			AND shop_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			AND shop_category.company = '" . $GLOBALS["shop"]["company"] . "'
			AND shop_category.shop_code = '" . $GLOBALS["shop"]["category_source"] . "'
		order by shop_item_has_category.sorting, shop_item_has_category.category_line_no asc limit 1
	";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if(mysqli_num_rows($result) > 0) {
        return 	mysqli_fetch_assoc($result);
    } else {
        return false;
    }
}

function get_variant_item_attributes($variant_codes, $attribute_type) {

    $query = "SELECT * from shop_attribute where 
      attribute_type = " . $attribute_type . " 
      and company = '" . $GLOBALS["shop"]["company"] . "' 
      and description IN ('" . join("', '", $variant_codes) . "')";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    // keine variantenbildung moeglich
    if(mysqli_num_rows($result) == 0) {
        return array();
    }

    $attributes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $attributes[$row['id']] = $row;
    }

    return $attributes;
}

function category_variation_allowed($shop_item_has_category, $marketplace_type) {
    $query = "SELECT variant_available FROM shop_marketplace_categories WHERE category_code = '" . $shop_item_has_category['marketplace_category_code'] . "'
			AND marketplace_type = " . $marketplace_type;

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $row = mysqli_fetch_assoc($result);
    if($row['variant_available'] == 1) {
        return true;
    } else {
        return false;
    }
}

function get_item_description($item) {
    $description_query  = "SELECT DISTINCT shop_item_description.*
						  FROM shop_item_description
						  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
						  INNER JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_description.item_no
						  WHERE shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
							AND shop_item_description.shop_code = '" . $item["shop_code"] . "'
							AND (
									(shop_item_description.item_no = '" . $item["item_no"] . "'
										AND (shop_item_description.language_code = '" . $item["language_code"] . "'
										OR shop_item_description.all_language_codes = TRUE)
									) 
							) order by line_no asc";

    $result = mysqli_query($GLOBALS['mysql_con'], $description_query);

    return $result;
}

function get_item_description_new($item) {
    $description_query  = "SELECT DISTINCT shop_item_description.*
						  FROM shop_item_description
						  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
						  INNER JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_description.item_no
						  WHERE shop_item_description.company = '" . $GLOBALS['shop']['company'] . "'
							AND shop_item_description.shop_code = '" . $item["shop_code"] . "'
							AND (
									(shop_item_description.item_no = '" . $item["item_no"] . "'
										AND (shop_item_description.language_code = '" . $item["language_code"] . "'
										OR shop_item_description.all_language_codes = TRUE)
									) 
							) order by line_no asc";

    $result = mysqli_query($GLOBALS['mysql_con'], $description_query);

    return $result;
}

function get_item_variants($item, $marketplace_type = 1) {

    $shopItemLinkType = 0;
    if ($marketplace_type == 2) {
        $shopItemLinkType = 0;
    }

    $query = "SELECT shop_item.*, smiu.marketplace_standalone_product as marketplace_standalone_product_per_shop, smiu.marketplace_last_update as marketplace_last_update_per_shop, smiu.marketplace_existing_item as marketplace_existing_item_per_shop, 
	
		(SELECT 
			content 
		FROM 
			shop_item_description sid 
		WHERE 
			sid.company = shop_item.company 
		AND 
			sid.shop_code = shop_item.shop_code 
		AND 
			sid.language_code = shop_item.language_code 
		AND 
			sid.item_no = shop_item.item_no 
		AND 
			sid.marketplace_title = 1) 
	 AS marketplace_title 
				FROM shop_item
				LEFT JOIN shop_item_link ON (shop_item_link.type=".$shopItemLinkType." AND shop_item_link.item_no = '" . $item["item_no"] . "' AND shop_item_link.linked_item_no=shop_item.item_no)
				RIGHT JOIN shop_marketplace_item_update smiu on smiu.company = shop_item.company AND smiu.item_no = shop_item.item_no
				WHERE
					shop_item.company = '" . $GLOBALS['shop']['company'] . "'
        		  AND
					shop_item.shop_code= '" . $item["shop_code"] . "'
        		  AND 
					shop_item.language_code = '" . $item["language_code"] . "'
				  AND
					NOT ISNULL(shop_item_link.id)
					AND smiu.item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND smiu.shop_code = '" . $GLOBALS["shop"]["code"] . "'
            AND smiu.item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND smiu.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
        		ORDER BY shop_item.base_price ASC";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);


    return $result;
}

function item_has_attributes ($item, $shop_attributes, $attribute_type) {
    $result = get_item_attributes($item, $shop_attributes, $attribute_type);

    if (@mysqli_num_rows($result) == 0) {
        return false;
    }

    return true;
}

function get_item_attributes ($item, $shop_attributes, $attribute_type) {
    $attributes_array = array();
    foreach($shop_attributes as $shop_attribute) {
        $attributes_array[] = $shop_attribute['code'];
    }

    if(count($attributes_array) == 0) {
        return false;
    }

    $attributequery = "
			SELECT 
				shop_attribute_link.*,
				shop_attribute.description AS 'headline',
				(IF (shop_attribute.map_to_attribute !='',shop_attribute.map_to_attribute,shop_attribute.code)) AS code, 
				shop_attribute.code AS original_code,
				shop_attribute.data_type,
				shop_attribute.display_type,
				shop_attribute.navision_value
		  FROM shop_attribute_link
		  INNER JOIN shop_attribute ON (shop_attribute.code = shop_attribute_link.attribute_code OR shop_attribute.map_to_attribute = shop_attribute_link.attribute_code)
		  WHERE shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "'
		  	AND shop_attribute.company = '" . $GLOBALS['shop']['company'] . "'
		  	AND shop_attribute_link.no = '" . $item['item_no'] . "'
			AND shop_attribute_link.shop_code='" . $GLOBALS['shop']['item_source'] . "'
			AND shop_attribute.code IN ('" . join("', '", $attributes_array) . "') 
			AND shop_attribute.attribute_type = " . $attribute_type . "
		  ORDER BY attribute_code";

    $result = @mysqli_query($GLOBALS['mysql_con'], $attributequery);
    return $result;
}

function get_item_attribute_value($item, $attribute) {
    $itemString = "('" . $item['item_no'] . "')";

    if ($attribute['navision_value'] != 0) {
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
                $field = 'vendor_no';
                break;
            case 9:
                $field = 'inventory';
                break;
            case 10:
                $field = 'material';
                break;
            case 11:
                $field = 'color';
                break;
            case 12:
                $field = 'gross_weight';
                break;
            case 13:
                $field = 'net_weight';
                break;
            case 14:
                $field = 'umverpackunngscode';
                break;
            case 15:
                $field = 'box_height';
                break;
            case 16:
                $field = 'box_width';
                break;
            case 17:
                $field = 'box_length';
                break;
            case 18:
                $field = 'tbox_height';
                break;
            case 19:
                $field = 'tbox_width';
                break;
            case 20:
                $field = 'tbox_length';
                break;
            default: $field= '';
                break;
        }
        if ($field <> ''){
            $query = "SELECT ".$field." 
					  FROM shop_item
					  WHERE company = '".$item['company']."'
					    AND shop_code = '".$item['shop_code']."'
					    AND language_code = '".$item['language_code']."'
					    AND item_no = '".$item['item_no']."'
					  LIMIT 1  ";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $item_attr_value = mysqli_fetch_assoc($result);
                if ($attribute['navision_value'] == 10) {
                    switch ($item_attr_value[$field]) {
                        case 1:
                            return "Kunststoff";
                            break;
                        case 2:
                            return "Holz";
                            break;
                        case 3:
                            return "Metall";
                            break;
                        case 4:
                            return "Glas";
                            break;
                        case 5:
                            return "Stoff";
                            break;
                        default: return "";
                            break;
                    }
                } else {
                    return $item_attr_value[$field];
                }
            }
        }

    } else {



        $option_values = get_option_values($attribute, $itemString);

        foreach($option_values as $key => $description) {
            if($description != "") {
                $returnDescription = $description;
            } else {
                $returnDescription = $key;
            }

            return $returnDescription;
        }
    }

    return "";
}

function get_item_images_clean($item, $image_config ) {
    $query     = "SELECT DISTINCT shop_item_file.*
			  FROM shop_item_file
			  LEFT JOIN shop_item ON shop_item.item_no = shop_item_file.item_no
			  LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_file.item_no
			  WHERE shop_item_file.type = '0'
			  	AND shop_item_file.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_item_file.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_file.filename <> ''
			  	AND (shop_item.item_no = '" . $item["item_no"] . "'
			  	AND (shop_item_file.language_code = '" . $item["language_code"] . "' OR shop_item_file.all_language_codes = TRUE))
			  ORDER BY line_no";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function insert_item_error($operation, $errorcode, $errortext) {

}

function get_payment_methods($item) {
    $query = "SELECT * FROM shop_payment_option 
			WHERE
					shop_payment_option.company = '" . $GLOBALS['shop']['company'] . "'
        		  AND
					shop_payment_option.shop_code= '" . $GLOBALS['shop']['code'] . "'
        		  AND 
					shop_payment_option.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			      AND
				    shop_payment_option.active = 1
			order by line_no asc
		";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function get_shipping_options($item) {
    $query = "SELECT * FROM shop_shipping_option 
			WHERE
					shop_shipping_option.company = '" . $GLOBALS['shop']['company'] . "'
        		  AND
					shop_shipping_option.shop_code= '" . $GLOBALS['shop']['code'] . "'
        		  AND 
					shop_shipping_option.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			order by line_no asc
		";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function active_bulk_job($jobname) {
    $query = "SELECT * from shop_marketplace_ebay_jobs where request_name = '" . $jobname . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function insert_bulk_job($jobname) {
    $query = "INSERT INTO shop_marketplace_ebay_jobs SET
		request_name = '" . $jobname . "',
		timestamp = " . time() . "
	";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function update_bulk_job($jobname, $jobid) {
    $query = "UPDATE shop_marketplace_ebay_jobs SET
		job_id = '" . $jobid . "',
		timestamp = " . time() . "
		WHERE request_name = '" . $jobname . "'
	";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function delete_bulk_job($jobid) {
    $query = "DELETE FROM shop_marketplace_ebay_jobs WHERE
		job_id = '" . $jobid . "'
	";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
}

function get_standard_product_id($item) {
    $query  = "SELECT *
		FROM shop_item_cross_reference
		WHERE item_no = '" . $item['item_no'] . "'
			AND reference_type = 3
			AND company = '" . $GLOBALS['shop']['company'] . "' limit 1";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row = mysqli_fetch_assoc($result);

    return $row;
}

function get_cross_reference_by_description($item, $description) {
    $query  = "SELECT *
		FROM shop_item_cross_reference
		WHERE item_no = '" . $item['item_no'] . "'
			AND reference_type = 3
			AND company = '" . $GLOBALS['shop']['company'] . "'
			AND description = '" . $description . "'";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function get_attribute_value($item, $attribute_code) {
    $query  = "SELECT * FROM shop_attribute_link WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['item_source'] . "'
			and attribute_code = '" . $attribute_code . "' 
			and no = '" . $item['item_no'] . "' limit 1"
    ;

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $row = mysqli_fetch_object($result);

    $query_2    = "SELECT * FROM shop_attribute_option WHERE company = '" . $GLOBALS['shop']['company'] . "' and code = '" . $row->value_option . "'";
    $result_2   = mysqli_query($GLOBALS['mysql_con'], $query_2);
    $row_2 = mysqli_fetch_object($result_2);

    return $row_2->description;
}

function get_shipments() {
    $query  = "SELECT * FROM shop_sales_shipment_header WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and marketplace_update = 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    return $result;
}

function get_sales_shipment_lines($shipment) {
    $query = "SELECT *
			  FROM 
			    shop_sales_shipment_line
			  WHERE 
			    document_no = '" . $shipment["no"] . "'
			  AND 
			    company = '" . $GLOBALS["shop"]["company"] . "'
			  AND
			    type = 2
			  ";
    $sales_shipment_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);

    return $sales_shipment_line_result;
}

function get_order_line($salesHeaderId,$itemNo,$quantity) {
    $query = "
        SELECT
          *
        FROM
          shop_sales_line
        WHERE
          shop_sales_header_id = '" . $salesHeaderId . "'
        AND 
          item_no = '" . $itemNo . "'
        AND
          quantity >= '" . $quantity . "'
        LIMIT
          1
    ";
    $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);

    return $sales_line_result;
}

function get_shipped_orders() {
    $query = "SELECT * FROM shop_sales_header WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and marketplace_shipped = 1" ;

    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function get_order($order_no) {
    $query = "SELECT * FROM shop_sales_header WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and order_no = " . $order_no ;

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);

    $row = mysqli_fetch_assoc($result);
    return $row;
}

function callSubmissionWorkerUntilItDoesSomething($queueID) {
    $workerPath = __DIR__ . DIRECTORY_SEPARATOR . 'WorkerSubmissions.php';
    $cmd = 'php ' . $workerPath;
    $timeoutSec = 200;
    $startDateTime = new DateTime();
    $interval = new DateInterval('PT' . $timeoutSec. 'S');
    $timeoutDate = $startDateTime->add($interval);
    $finished = false;

    $currDateTime = clone $startDateTime;
    while (!$finished && ($currDateTime <= $timeoutDate)) {
        execReturnArray($cmd);
        $finished = checkQueueEntrySubmitted($queueID);
        sleep(30);
        $currDateTime = new DateTime();
    }
    return $finished;
}

function checkQueueEntrySubmitted($queueID) {
    $query = "SELECT 1 FROM shop_marketplace_submissions WHERE queue_id = '" . (int)$queueID."'";
    $result = mysqli_query($GLOBALS['mysql_con'],$query);
    $num_rows = mysqli_num_rows($result);
    return $num_rows === 0;
}

function execReturnArray($cmd)
{
    $outputArr = [];
    $returnCode = 0;
    exec($cmd, $outputArr, $returnCode);
    if ($returnCode !== 0) {
        throw new \ErrorException(
            'The following command returned with exit-code ' . $returnCode . ' : ' . $cmd . PHP_EOL . 'Output: ' . print_r(
                $outputArr,
                1
            )
        );
    }
    return $outputArr;
}

function update_ebay_token_url($url, $session_id, $runame, $environment) {
    $final_url = $url . urlencode($session_id);

    $query = "UPDATE shop_marketplace_config_ebay SET session_id = '" . $session_id . "', final_token_url = '" . $final_url . "' WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and environment = '" . $environment . "'";

    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function get_ebay_session_id($environment) {
    $query = "SELECT * FROM shop_marketplace_config_ebay WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and environment = '" . $environment . "'";

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row = mysqli_fetch_assoc($result);
    return $row['session_id'];
}

function update_ebay_user_auth_token($user_auth_token, $environment) {
    $query = "UPDATE shop_marketplace_config_ebay SET user_auth_token = '" . $user_auth_token . "' WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and environment = '" . $environment . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function get_all_order_items_to_cancel() {
    $query  = "SELECT * FROM shop_sales_line WHERE 
			marketplace_update = 1
			and marketplace_dispute_reason != ''
			and company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    return $result;
}

function insert_marketplace_item($marketplace_type, $item_no, $inventory, $base_price, $item_id = "") {
    $query = "INSERT INTO shop_marketplace_items (company, shop_code, language_code, marketplace_type, item_no, marketplace_item_id, inventory, base_price, timestamp_modified) VALUES (
		'" . $GLOBALS['shop']['company'] . "',
		'" . $GLOBALS['shop']['code'] . "',
		'" . $GLOBALS['shop_language']['code'] . "',
		" . $marketplace_type . ",
		'" . $item_no . "',
		'" . $item_id . "',
		" . $inventory . ",
		" . $base_price . ",
		'" . time() . "'
	)";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

function getItemValidFrom($item) {
    //2017-07-26T00:00:01
    if (is_null($item["validity_from"]) || $item["validity_from"] == '0000-00-00') {
        return date("Y-m-d\TH:i:s",time());
    } else {
        return date("Y-m-d\TH:i:s",strtotime($item["validity_from"]));
    }
}

function replace_item_placeholders($text,$company,$item_shop_code,$item_lang_code,$item_no,$parent_item_no,$variant_code,$shop_variant_type,$document_root,array $image_config,$image_size_index,$max_size,$cancellation_text,$mysqli_conn)
{
    if (empty($mysqli_conn)) {
        $mysqli_conn = $GLOBALS['mysql_con'];
    }

    if (empty($shop_variant_type)) {
        $shop_variant_type = $GLOBALS['shop']['variant_type'];
    }

    $document_root = rtrim($GLOBALS['projectRoot'],'\/');
    $item = get_item_clean($company,$item_shop_code,$item_lang_code,$item_no,$mysqli_conn);
    $item['prepared_base_price'] = (isset($item['base_price']) && (float)$item['base_price'] > 0) ? number_format($item['base_price'],2,',','.') : '';
    $item['prepared_retail_price'] = (isset($item['retail_price']) && (float)$item['retail_price'] > 0) ? number_format($item['retail_price'],2,',','.') : '';

    $images = get_item_images_clean_new($company,$shop_variant_type,$item_shop_code,$item_lang_code,$item_no,$parent_item_no,$variant_code,$mysqli_conn);
    foreach ($images as &$imageArr) {
        $filepath = $document_root . DIRECTORY_SEPARATOR . $image_config[$image_size_index]["path"] . DIRECTORY_SEPARATOR .  $imageArr['filename'];
        $html = '<div class="item_image_wrapper"><div class="item_image"><img style="max-width: 80%;" border="0" src="' . $filepath . '" alt="' . urlencode($imageArr['description']) . '" /></div></div>';
        $imageArr['filepath'] = $filepath;
        $imageArr['html'] = $html;
    }
    unset($imageArr);
    $item['images'] = $images;


    $descriptions = get_item_descriptions_marketplace($company,$item_shop_code,$item_lang_code,$item_no,$parent_item_no,$mysqli_conn);

    $descriptionArr = array();

    while ($descriptionrow = mysqli_fetch_assoc($descriptions)) {
        $descriptionArr[]['html'] = '<div class="item_description_wrapper"><div class="item_description">' . $descriptionrow['content'] . '</div></div>';
    }

    $item['descriptions'] = $descriptionArr;

    $placeholders = [
        'cancellation' => $cancellation_text,
        'item.no' => safe_array_dot_access($item,'no',''),
        'item.description' => safe_array_dot_access($item,'description',''),
        'item.descriptions.0' => safe_array_dot_access($item,'descriptions.1.html',''),
        'item.descriptions.1' => safe_array_dot_access($item,'descriptions.2.html',''),
        'item.descriptions.2' => safe_array_dot_access($item,'descriptions.3.html',''),
        'item.descriptions.3' => safe_array_dot_access($item,'descriptions.4.html',''),
        'item.descriptions.4' => safe_array_dot_access($item,'descriptions.5.html',''),
        'item.descriptions.5' => safe_array_dot_access($item,'descriptions.6.html',''),
        'item.descriptions.6' => safe_array_dot_access($item,'descriptions.7.html',''),
        'item.descriptions.7' => safe_array_dot_access($item,'descriptions.8.html',''),
        'item.descriptions.8' => safe_array_dot_access($item,'descriptions.9.html',''),
        'item.descriptions.9' => safe_array_dot_access($item,'descriptions.0.html',''),
        'item.images.0' => safe_array_dot_access($item,'images.1.html',''),
        'item.images.1' => safe_array_dot_access($item,'images.2.html',''),
        'item.images.2' => safe_array_dot_access($item,'images.3.html',''),
        'item.images.3' => safe_array_dot_access($item,'images.4.html',''),
        'item.images.4' => safe_array_dot_access($item,'images.5.html',''),
        'item.images.5' => safe_array_dot_access($item,'images.6.html',''),
        'item.images.6' => safe_array_dot_access($item,'images.7.html',''),
        'item.images.7' => safe_array_dot_access($item,'images.8.html',''),
        'item.images.8' => safe_array_dot_access($item,'images.9.html',''),
        'item.images.9' => safe_array_dot_access($item,'images.0.html',''),
        'item.summary' => safe_array_dot_access($item,'summary',''),
        'item.base_price' => safe_array_dot_access($item,'prepared_base_price'),
        'item.retail_price' => safe_array_dot_access($item,'prepared_base_price'),
    ];

    $newText = $text;
    $maxSizeReached = FALSE;

    foreach ($placeholders as $placeholder => $replacementValue) {
        $pattern = '%' . $placeholder . '%';
        if (!$maxSizeReached) {
            $tempText = str_replace($pattern,$replacementValue,$newText);
            if (strlen($tempText) < $max_size) {
                $newText = $tempText;
            } else {
                $newText = str_replace($pattern,'',$newText);
                $maxSizeReached = TRUE;
            }
        } else {
            $newText = str_replace($pattern,'',$newText);
        }
    }
    return $newText;

}