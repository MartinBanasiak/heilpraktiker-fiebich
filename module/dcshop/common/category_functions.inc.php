<?php
function get_category($company, $shop_code, $language_code, $category_line_no)
{
    $query = "SELECT * FROM shop_category
			  WHERE line_no = '" . $category_line_no . "'
			  	AND company = '" . $company . "'
    		  	AND shop_code = '" . $shop_code . "'
    		  	AND language_code = '" . $language_code . "'
			  	LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $category = mysqli_fetch_assoc($result);
        return $category;
    }
}

function get_category_by_code($company, $shop_code, $language_code, $category_code)
{
    $query = "SELECT *
			  FROM shop_category
			  WHERE code= '" . $category_code . "'
			  	AND company = '" . $company . "'
    		  	AND shop_code = '" . $shop_code . "'
    		  	AND language_code = '" . $language_code . "'
			  	LIMIT 1";

    if(!isset($GLOBALS['mysql_con']))
    {
        db_connect();
    }
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $category = mysqli_fetch_assoc($result);
        return $category;
    } else {
        return FALSE;
    }
}

function shop_category_menu($company, $shop_code, $language_code, $category, $from_level, $to_level)
{
    shop_category_menu_rek($company, $shop_code, $language_code, $category, 0, 1, "", $from_level, $to_level);
    echo "\n";
    return TRUE;
}

function shop_category_menu_rek($company, $shop_code, $language_code, $category, $parent_id, $level, $last_category, $from_level, $to_level)
{
    $spaces = "    ";
    while ($count <= $level) {
        $spaces .= "  ";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id . "'";
    $query = "SELECT *
			  FROM shop_category
			  WHERE company = '" . $company . "'
			  	AND shop_code = '" . $shop_code . "'
			  	AND language_code = '" . $language_code . "'
			  	" . $parent_id_query . "
			  ORDER BY sorting ASC";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? TRUE : FALSE;
    if (@mysqli_num_rows($result) > 0) {
        if ($show_level) {
            echo "\n" . $spaces . "<ul class=\"level_$level\">";
        }
        while ($curr_category = mysqli_fetch_assoc($result)) {
            $active = "";
            if ($curr_category["id"] == $category["parent_id"]) {
                $active = "class=\"active_tree\" ";
            }
            if ($curr_category["id"] == $category["id"]) {
                $active = "class=\"active\" ";
            }
            if ($show_level) {
                echo "\n" . $spaces . "  <li class=\"level_$level\"><a " . $active . "href='" . customizeUrl() . "/' >" . $curr_category["name"] . "</a>";
            }
            if (($curr_category["id"] == $category["id"]) | ($curr_category["id"] == $category["parent_id"])) {
                shop_category_menu_rek($company, $shop_code, $language_code, $category, $curr_category["id"], ($level + 1), $curr_category, $from_level, $to_level);
            }
            if ($show_level) {
                echo "</li>";
            }
        }
        if ($show_level) {
            echo "\n" . $spaces . "</ul>";
        }
    }
    return TRUE;
}

function curr_category_path($category, $link_last_category = FALSE)
{
    //Link zur Startseite
    $home = '<span><a href="/' . customizeUrl() . '/"><span>' . $GLOBALS["tc"]["homepage"] . '</span></a></span>';
    if ($link_last_category) {
        return $home . curr_category_path_rek($category["line_no"], '', get_layer_no());
    } else {
        return $home . curr_category_path_rek($category["parent_line_no"], '', get_layer_no()) .
            "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<span class='current'>" . $category["name"] . "</span>";
    }
}

function curr_category_path_rek($parent_line_no, $path, $i)
{
    if ($parent_line_no > 0) {

        $category = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_line_no);

        if (array_key_exists('id',$category) && (int)$category['id'] > 0) {
            $link = create_category_path($category["root_line_no"], $category["level"], $category["line_no"]);
            $path = "&nbsp;<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>&nbsp;<span itemscope itemtype='http://data-vocabulary.org/Breadcrumb'><a href=\"" . $link . "\" itemprop=\"url\">" . "<span  itemprop=\"title\">" . htmlspecialchars($category["name"], ENT_QUOTES, "UTF-8") . "</span></a></span>" . $path;
            $path = curr_category_path_rek($category["parent_line_no"], $path, --$i);
        }
    }
    return $path;
}

function category_get_path($category)
{
    $root_line_no = $category['root_line_no'];
    $line_no = $category['line_no'];

    $path = "/" . customizeUrl() ."/";


    //Breadcrumb link erstellen anhand der parent id
    $parent_line_no = $line_no;
    while ($parent_line_no != '0') {
        $parentCategory = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_line_no);

        $parent_line_no = '0';
        if (array_key_exists('id',$parentCategory) && (int)$parentCategory['id'] > 0) {
            $path_2 = URLNormalize($parentCategory['code']) . "/" . URLNormalize($path_2);
            $parent_line_no = $parentCategory['parent_line_no'];

            if ($GLOBALS['shop_setup']['show_short_url'] ) {
                $parent_line_no = 0;
            }
        }
    }
    return strtolower($path . $path_2);
}




function category_get_full_path($category)
{
    $root_line_no = $category['root_line_no'];
    $line_no = $category['line_no'];


    $path = "/" .customizeUrl().     "/";


    //Breadcrumb link erstellen anhand der parent id
    $parent_line_no = $line_no;
    while ($parent_line_no != '0') {
        $parentCategory = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_line_no);
        $parent_line_no = '0';
        if (array_key_exists('id',$parentCategory) && (int)$parentCategory['id'] > 0) {
            $path_2 = URLNormalize($parentCategory['code']) . "/" . URLNormalize($path_2);
            $parent_line_no = $parentCategory['parent_line_no'];
        }
    }
    return strtolower($path . $path_2);
}


function create_category_path($root_line_no, $to_level = 0, $line_no)
{
    $path = "/" . customizeUrl() . "/";

    //Breadcrumb link erstellen anhand der parent id
    $parent_id = $line_no;
    while ($parent_id != '0') {
        $parentCategory = get_category_from_tree_as_array_by_line_no($GLOBALS['curr_category_tree'],(int)$parent_id);
        $parent_id = '0';
        if (array_key_exists('id',$parentCategory) && (int)$parentCategory['id'] > 0) {

            if ( $GLOBALS['shop_setup']['show_short_url'] ) {
                $path_2 = $parentCategory['code'] . "/";
                $parent_id = 0;
            } else {
                $path_2 = $parentCategory['code'] . "/" . $path_2;
                $parent_id = $parentCategory['parent_line_no'];
            }

        }
    }
    return $path . $path_2;


    /*	$level = 2;
        while ($level <= $to_level) {
            $next_category = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'],"SELECT *
                                                            FROM shop_category
                                                            WHERE level = '" .$level . "'
                                                                AND ".$sql_part_1."
                                                                AND code = '" . $_GET["slevel_" . $level] ."'
                                                                AND company = '".$GLOBALS['shop']['company']."'
                                                                  AND shop_code = '".$GLOBALS['shop']['code']."'
                                                                  AND language_code = '".$GLOBALS['shop_language']['code']."'"));
            $path = $path . $next_category["code"] . "/";
            $level++;
        }
        return $path;*/
}

function emty_category_query($category)
{
    return emty_category_query_rek($category["line_no"], '');
}

function emty_category_query_rek($category_line_no, $query)
{
    if ($category_line_no > 0) {
        $result = mysqli_query($GLOBALS['mysql_con'], "SELECT *
								FROM shop_category
								WHERE parent_line_no = '" . $category_line_no . "'
								AND shop_code = '" . $GLOBALS["shop"]["category_source"] . "'
								AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
								AND company = '" . $GLOBALS["shop"]["company"] . "'
								AND active=1");
        if (@mysqli_num_rows($result) > 0) {
            while ($nav = mysqli_fetch_assoc($result)) {
                $query = " OR shop_item_has_category.category_line_no = '" . $nav["line_no"] . "'" . $query;
                $query = emty_category_query_rek($nav["line_no"], $query);
            }
        }
    }
    return $query;
}

function get_shop_category($company, $shop_code, $language_code, $id)
{
    $category = get_category_from_tree_as_array_by_id($GLOBALS['curr_category_tree'],(int)$id);
    return $category;
}

function get_link_to_shop_category($navigation_id)
{
    $query = "SELECT * FROM main_navigation WHERE id = '" . $navigation_id . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $navigation = mysqli_fetch_assoc($result);
        $link = navigation_path($GLOBALS["site"], $GLOBALS["language"], $navigation);
    }
    return $link;
}

function get_product_amount($category_line_no)
{
    $query = "SELECT count(shop_view_active_item.id) AS num
			  FROM shop_item_has_category
			  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
			  WHERE shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
			  	AND shop_view_active_item.shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	AND shop_view_active_item.language_code ='" . $GLOBALS['shop_language']['code'] . "'
			  	AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			  	AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			  	AND shop_item_has_category.category_line_no = '" . $category_line_no . "' " . get_categories_query($category_line_no) . "";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $val = mysqli_fetch_assoc($result);
    return $val["num"];
}

function get_product_amount_by_vendor($vendor_no, $category_line_no)
{
    $query = "SELECT count(result.id) AS num
			  FROM (SELECT DISTINCT shop_view_active_item.id
			  		FROM shop_item_has_category
			  		RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
			  		WHERE (shop_item_has_category.category_line_no = " . $category_line_no . " " . get_categories_query($category_line_no) . ")
			  			AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
					  	AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
					  	AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			  			AND shop_view_active_item.vendor_no = '" . $vendor_no . "'
			  			AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
					  	AND shop_view_active_item.shop_code = '" . $GLOBALS['shop']['code'] . "'
					  	AND shop_view_active_item.language_code ='" . $GLOBALS['shop_language']['code'] . "')
			  		AS result";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $val = mysqli_fetch_assoc($result);
    return $val["num"];
}


function get_category_picture($category_id)
{
    $category = get_category_from_tree_as_array_by_id($GLOBALS['curr_category_tree'],(int)$category_id);
    if (array_key_exists('id',$category) && (int)$category['id'] > 0) {

        if (!is_null($category["category_picture"]) && $category["category_picture"] != '') {
            echo '<div class="category_picture"><img alt="' . $category["name"] . '" src="' . $GLOBALS['projectRoot'] . $GLOBALS['shop_setup']['shop_userdata_basedir'] . '/category_picture/' . $category["category_picture"] . '" id="category_main_pic"/></div>';
        }
    }
}

function get_category_description($category_id)
{
    $category = get_category_from_tree_as_array_by_id($GLOBALS['curr_category_tree'],(int)$category_id);
    if (array_key_exists('id',$category) && (int)$category['id'] > 0) {
        if ($category["category_description"] != "") {
            echo "<div class=\"category_description_1\">" . $category["category_description"] . "</div>";
            return true;
        }
    }
    return false;
}

function get_category_description_2($category_id)
{
    $category = get_category_from_tree_as_array_by_id($GLOBALS['curr_category_tree'],(int)$category_id);
    if (array_key_exists('id',$category) && (int)$category['id'] > 0) {

        echo "<div class=\"category_description_2\">" . $category["category_description_2"] . "</div>";
    }
}

function get_category_description_excerpt($category_id)
{
    $category = get_category_from_tree_as_array_by_id($GLOBALS['curr_category_tree'],(int)$category_id);
    if (array_key_exists('id',$category) && (int)$category['id'] > 0) {
        echo "<div class=\"category_description_excerpt\">" . $category["category_description_excerpt"] . "</div>";
    }
}


function get_categories_query($category_line_no)
{
    return get_category_query_rek($category_line_no, '');
}

function get_category_query_rek($category_line_no, $query)
{
    if ($category_line_no > 0) {
        $result = mysqli_query($GLOBALS['mysql_con'], "SELECT *
								FROM shop_category
								WHERE parent_line_no = '" . $category_line_no . "'
									AND company = '" . $GLOBALS['shop']['company'] . "'
									AND shop_code = '" . $GLOBALS['shop']['category_source'] . "'
									AND language_code = '" . $GLOBALS['shop_language']['code'] . "'");
        if (@mysqli_num_rows($result) > 0) {
            while ($nav = mysqli_fetch_assoc($result)) {
                $query = " OR shop_item_has_category.category_line_no = " . $nav["line_no"] . "" . $query;
                $query = get_category_query_rek($nav["line_no"], $query);
            }
        }
    }
    return $query;
}

function show_creditor_filter($category_line_no)
{
    $marked_vendor = $GLOBALS["vendor_no"];
    $query = "SELECT DISTINCT *
  			FROM shop_vendor AS sv
  			RIGHT JOIN shop_view_active_item AS svai ON sv.vendor_no = svai.vendor_no
  			LEFT JOIN shop_item_has_category ON shop_item_has_category.item_no = svai.item_no
  			WHERE sv.to_delete = 0
  				AND (shop_item_has_category.category_line_no = '" . $category_line_no . "' " . get_categories_query($category_line_no) . ")
  				AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			  	AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
  				AND svai.company = '" . $GLOBALS['shop']['company'] . "'
  				AND svai.shop_code = '" . $GLOBALS['shop']['code'] . "'
  				AND svai.language_code = '" . $GLOBALS['shop_language']['code'] . "'
  			GROUP BY sv.name";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        echo "<div class=\"shop_category_2\"><ul><b>" . $GLOBALS[tc]["vendor"] . "</b>:&nbsp;&nbsp;";
        $itemlink = "?shop_category=" . $_GET["shop_category"];
        if ($GLOBALS["vendor_no"] == '') {
            echo "<b><a class=\"current_vendor\" href=" . $itemlink . "><li>" . $GLOBALS["tc"]["all"] . "</li></a></b>&nbsp;&nbsp";
        } else {
            echo "<a class=\"vendor_list\" href=" . $itemlink . "><li>" . $GLOBALS["tc"]["all"] . "</li></a>&nbsp;&nbsp";
        }

        while ($val = mysqli_fetch_assoc($result)) {
            $amount = " (" . get_product_amount_by_vendor($val["vendor_no"], $category_line_no) . ")";
            if ($marked_vendor == $val["vendor_no"]) {
                $vendor_no = "&vendor=" . $val["vendor_no"];
                $itemlink = "?shop_category=" . $_GET["shop_category"] . $vendor_no;
                echo "<b><a class=\"current_vendor\" href=" . $itemlink . "><li>" . $val['name'] . $amount . "</li></a></b>&nbsp;&nbsp";
            } else {
                $vendor_no = "&vendor=" . $val["vendor_no"];
                $itemlink = "?shop_category=" . $_GET["shop_category"] . $vendor_no;
                echo "<a class=\"vendor_list\" href=" . $itemlink . "><li>" . $val['name'] . $amount . "</li></a>&nbsp;&nbsp";
            }
        }
        echo "</ul></div>";
    }
}

function get_shop_category_sort_type($sort_item_type)
{
    $sort_item_type += 1;
    switch ($sort_item_type) {
        case 1:
            $order_by = "ranking";
            break;
        case 2:
            $order_by = "item_no";
            break;
        case 3:
            $order_by = "description";
            break;
        case 4:
            $order_by = "creation_date";
            break;
        case 5:
            $order_by = "sorting";
            break;
        case 6:
            $order_by = "base_price_asc";
            break;
        case 7:
            $order_by = "base_price_desc";
            break;
        default:
            $order_by = "user";
    }

    return $order_by;
}

function get_category_id_by_line_no($line_no)
{
    $category = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], "SELECT *
											   FROM shop_category
											   WHERE line_no = '" . $line_no . "'
											   	 AND company = '" . $GLOBALS['shop']['company'] . "'
											  	 AND shop_code = '" . $GLOBALS['shop']['code'] . "'
											  	 AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
											   LIMIT 1"));
    return $category["id"];
}

function get_code_by_id($category_id)
{
    $code = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], "SELECT *
										   FROM shop_category
										   WHERE id = '" . $category_id . "'
										   	 AND company = '" . $GLOBALS['shop']['company'] . "'
										  	 AND shop_code = '" . $GLOBALS['shop']['code'] . "'
										  	 AND language_code = '" . $GLOBALS['shop_language']['code'] . "'"));
    return $code["code"];
}

function get_id_by_code($category_code)
{


    $prepStatement = " SELECT *
			  FROM shop_category
			  WHERE code = :code
			  	AND company = :company
			  	AND shop_code =:shopCode
			  	AND language_code =:languageCode
			 ";

    $IOCContainer = $GLOBALS['IOC'];
    $pdo = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    $params = [
        [':code', $category_code, PDO::PARAM_STR],
        [':company', $GLOBALS['shop']['company'], PDO::PARAM_STR],
        [':shopCode', $GLOBALS['shop']['category_source'], PDO::PARAM_STR],
        [':languageCode', $GLOBALS['shop_language']['code'], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();

    if (count($resultArray) > 0) {
        return $resultArray[0]["id"];
    }

}

function get_promotion_description($category)
{
    $tstoday = strtotime(date('Y-m-d'));
//	(isnull(sc.promotion_validity_from) AND isnull(sc.promotion_validity_to)
//			  		OR (isnull(sc.promotion_validity_from) AND (sc.promotion_validity_to >= curdate())
//			  		OR (sc.promotion_validity_from <= curdate()) AND isnull(sc.promotion_validity_to))
//			  		OR (sc.promotion_validity_from <= curdate()) AND (sc.promotion_validity_to >= curdate())
    if ($category['promotion_active'] &&
        (($category['promotion_validity_from'] == '' && $category['promotion_validity_to'] == '')
            || ($category['promotion_validity_from'] == '' && strtotime($category['promotion_validity_to']) >= $tstoday)
            || (strtotime($category['promotion_validity_from']) <= $tstoday && $category['promotion_validity_to'] == '')
            || (strtotime($category['promotion_validity_from']) <= $tstoday && strtotime($category['promotion_validity_to']) >= $tstoday))
    ) {
        if ($category['promotion_description'] != '') {
            echo("<div>" . $category['promotion_description'] . "</div><br/>");
        }
    }
}

function show_categories_start()
{
    $query = "SELECT *
	          FROM shop_category
						WHERE company = '" . $GLOBALS['shop']['company'] . "'
						  AND shop_code ='" . $GLOBALS['shop']['code'] . "'
							AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
							AND level = 1
							AND homepage_active= 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    echo " <div class = \"categories_start\">";
    while ($category = mysqli_fetch_assoc($result)) {
        $language["code"] = $GLOBALS['shop_language']['code'];
        $image = get_category_icon($category, $language);
        $categoryPath = get_category_path($category['id'], $language);
        $categorylink = "/" . customizeUrl() . "/" . $categoryPath;
        ?>
        <div class="category_main">
            <div class="category_image"><a href="<?= $categorylink ?>"><?= $image ?></a></div>
            <div class="category_desc"><a href="<?= $categorylink ?>"><?= $category["name"] ?></a></div>
        </div>
        <?
    }
    echo " </div>";
}

function get_category_parent_line_nos(PDO $pdo, $cat_company,$cat_shop_code,$cat_lang_code,$cat_line_no,array &$results) {
    static $query =
        'SELECT line_no,parent_line_no FROM shop_category WHERE company = :company AND shop_code = :shop_code AND language_code = :language_code AND line_no = :line_no';

    $currLineNo = $cat_line_no;
    $parentLineNos = [];
    do {

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':company',$cat_company,PDO::PARAM_STR);
        $stmt->bindValue(':shop_code',$cat_shop_code,PDO::PARAM_STR);
        $stmt->bindValue(':language_code',$cat_lang_code,PDO::PARAM_STR);
        $stmt->bindValue(':line_no',$currLineNo,PDO::PARAM_INT);
        $stmt->execute();
        $resLine = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($resLine['parent_line_no'])) {
            $hasParent = true;
            $parentLineNos[] = (int)$resLine['parent_line_no'];
            $currLineNo = $resLine['parent_line_no'];
        } else {
            $hasParent = false;
        }
    } while ($hasParent);
    $results = $parentLineNos;
}

function get_category_child_line_nos(PDO $pdo, $cat_company,$cat_shop_code,$cat_lang_code,$cat_line_no,array &$results)
{
    static $query =
        'SELECT line_no FROM shop_category WHERE company = :company AND shop_code = :shop_code AND language_code = :language_code AND parent_line_no = :parent_line_no';
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':company',$cat_company,PDO::PARAM_STR);
    $stmt->bindValue(':shop_code',$cat_shop_code,PDO::PARAM_STR);
    $stmt->bindValue(':language_code',$cat_lang_code,PDO::PARAM_STR);
    $stmt->bindValue(':parent_line_no',$cat_line_no,PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $row) {
        $results[] = $row['line_no'];
        get_category_child_line_nos($pdo,$cat_company,$cat_shop_code,$cat_lang_code,$row['line_no'],$results);
    }
}
function get_category_icon( $category, $language ) {
    $catIconPath = $GLOBALS["shop_setup"]["uploaddir_category_icon"];
    if ($category["category_icon"] != '') {
        return '<img src="' . $GLOBALS['projectRoot'] . $catIconPath . $category["category_icon"] . '"  alt="'.$category["name"].'"  >';
    } else {
        if ($GLOBALS['shop_language']['item_placeholder_image'] != '') {
            return '<img src="' . $GLOBALS['projectRoot'] . $catIconPath .  $GLOBALS['shop_language']['item_placeholder_image'] . '" alt="'.$category["name"].'">';
        } else {
            return '<img src="' . $GLOBALS['projectRoot'] . $catIconPath . 'noimage.jpg" alt="'.$category["name"].'">';
        }
    }
}

function get_category_from_tree_by_line_no(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $root, int $lineNo) : \DynCom\dc\dcShop\classes\Category
{
    $category = null;
    $visitorFunction = function(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $node) use(&$category,$lineNo) { if ($node->getLineNo() === $lineNo) {$category = $node->getCategory();}};
    $visitor = new \DynCom\dc\dcShop\classes\TreeNodeVisitor($visitorFunction);
    $visitor->visitByLevel($root);
    return $category;
}

function get_category_from_tree_as_array_by_line_no(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $root, int $lineNo) : array
{
    static $memo;
    $company = $root->getCompany();
    $categoryShopCode = $root->getShopCode();
    $hash = md5($company . '|' . $categoryShopCode . '|' . $lineNo);
    if (array_key_exists($hash,$memo)) {
        return $memo[$hash];
    }

    $category = get_category_from_tree_by_line_no($root,$lineNo);
    $categoryArr = $category->getAllFieldsAsArray();
    $memo[$hash] = $categoryArr;
    return $categoryArr;
}

function get_category_from_tree_as_array_by_id(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $root, int $id) : array
{
    static $memo;
    $company = $root->getCompany();
    $categoryShopCode = $root->getShopCode();
    $hash = md5($company . '|' . $categoryShopCode . '|' . $id);
    if (array_key_exists($hash,$memo)) {
        return $memo[$hash];
    }

    $category = get_category_from_tree_by_id($root,$id);
    if ($category instanceof  \DynCom\dc\dcShop\classes\Category) {
        $categoryArr = $category->getAllFieldsAsArray();
    } else {
        $categoryArr = [];
    }
    $memo[$hash] = $categoryArr;
    return $categoryArr;
}


function get_category_from_tree_by_id(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $root,  int $id) : ?\DynCom\dc\dcShop\classes\Category
{
    $category = null;
    $visitorFunction = function(\DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface $node) use(&$category,$id) { if ($node->getID() === $id) {$category = $node->getCategory();}};
    $visitor = new \DynCom\dc\dcShop\classes\TreeNodeVisitor($visitorFunction);
    $visitor->visitByLevel($root);
    return $category;
}



function get_simple_category_path($category, $company, $shopCode, $languageCode)
{
    $line_no = $category['line_no'];
    $path = "/" . customizeUrl() ."/";
    //Breadcrumb link erstellen anhand der parent id
    $parent_id = $line_no;
    $path_2 = '';
    while ($parent_id != '0') {
        $query_2 = "SELECT * FROM shop_category WHERE line_no = '" . $parent_id . "'
								AND company = '" . $company . "'
							  	AND shop_code = '" . $shopCode. "'
							  	AND language_code = '" . $languageCode. "' LIMIT 1";
        $result_2 = mysqli_query($GLOBALS['mysql_con'], $query_2);
        $parent_id = '0';
        if ($row = mysqli_fetch_object($result_2)) {
            $path_2 = categoryURLnormalize($row->code) . '/' . categoryURLnormalize($path_2);
            $parent_id = $row->parent_line_no;
            if ($GLOBALS['shop_setup']['show_short_url'] ) {
                $parent_id = 0;
            }
        }
    }
    return strtolower($path . $path_2);
}

function categoryURLnormalize($string)
{

    //Trim
    $string = trim($string);

    //Alle Umlaute umschreiben
    $string = str_replace(
        array('ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'),
        array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'),
        $string
    );

    //Alle Sonderzeichen außer Plus zu '-' machen
    $string = preg_replace('/([^a-zA-Z0-9+-\/]|[.])/', '-', $string);

    //Doppelte Vorkommen von '+' und '-' mit einzelnen ersetzen
    $string = strtolower(preg_replace('/([+-]){2,}/', '$1', $string));

    return $string;
}


?>