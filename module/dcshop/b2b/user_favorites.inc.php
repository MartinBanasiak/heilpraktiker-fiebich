<? //$formname = "form_user_favorites";
$formname = "form_itemlist_1";
$query    = "SELECT shop_view_active_item.*
		  FROM shop_view_active_item
		  LEFT JOIN shop_user_favorites ON shop_view_active_item.id = shop_user_favorites.shop_item_id
		  WHERE shop_user_favorites.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  ORDER BY shop_view_active_item.item_no";
$result   = @mysqli_query($GLOBALS['mysql_con'], $query);?>

<h1 class="shop_site_headline"><?= $GLOBALS["shop"]["shop_typ"] == 1 ? $GLOBALS['tc']['favorites'] : $GLOBALS['tc']['favorites_b2b'];?></h1>

<?if (@mysqli_num_rows($result) > 0) {
    ?>
    <div class="button_row">
        <?= button("delete button", $GLOBALS["tc"]["empty_favorites_b2b"], $formname, "?action=shop_empty_user_favorites", $GLOBALS["tc"]["empty_favorites_conf_b2b"]); ?>
    </div>
    <?
    show_item_list_from_query($query, 1, $formname);
} else {
    ?>
    <div class="emptybox">
        <?= $GLOBALS["tc"]["favorites_howto_b2b"] ?>
    </div>
    <?
}