<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/tooltip/tooltip.js"></script>
<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';
echo "<div style=\"overflow:hidden\">";
$category = get_category_by_code($GLOBALS["shop"]["company"], $GLOBALS["shop"]["code"], $GLOBALS["shop_language"]["code"], $sitepart["value_2"]);
if ($category) {
    $query = "SELECT shop_view_active_item.*
			  FROM shop_view_active_item
			  INNER JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
			  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  										AND shop_permissions_group_link.company = shop_view_active_item.company
			  WHERE shop_item_has_category.category_line_no = " . $category["line_no"] . get_categories_query($category["line_no"]) . "
			  	AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			  	AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
			  	AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			  	AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
			  	" . get_permissions_group_customer() . "
			  GROUP BY shop_view_active_item.id
			  ORDER BY RAND()
			  LIMIT " . $sitepart[value_1];
} else {
    $query = "SELECT shop_view_active_item.*
			  FROM shop_view_active_item
			  INNER JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
			  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  										AND shop_permissions_group_link.company = shop_view_active_item.company
			  WHERE shop_item_has_category.category_line_no <> 0
			  	AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
			  	AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
			  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
			  	AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
			  	" . get_permissions_group_customer() . "
			  GROUP BY shop_view_active_item.id
			  ORDER BY RAND()
			  LIMIT " . $sitepart[value_1];
}
$result = mysqli_query($GLOBALS['mysql_con'], $query);
show_item_list_from_query($result, 10, FALSE);
echo "</div>";
?>