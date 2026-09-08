<? //$formname = "form_user_favorites";
$formname = "form_itemlist_1"; ?>
    <div class="infobar"><?= $GLOBALS["tc"]["favorites"] ?></div>
<?
$query  = "SELECT shop_view_active_item.*
		  FROM shop_view_active_item
		  LEFT JOIN shop_user_favorites ON shop_view_active_item.id = shop_user_favorites.shop_item_id
		  WHERE shop_user_favorites.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
		  ORDER BY shop_view_active_item.item_no";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) > 0) {
    ?>
    <div class="toolbar">
        <?= button("delete", $GLOBALS["tc"]["empty_favorites"], $formname, ml("", "action", "shop_empty_user_favorites"), $GLOBALS["tc"]["empty_favorites_conf"]); ?>
    </div>
    <?
    show_item_list_from_query($result, 1, FALSE, $formname);
} else {
    ?>
    <div class="infobox">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><img src="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/<?= $GLOBALS["layout"]["code"] ?>/img/favorit.gif" border="0" width="16"
                         height="16" /></td>
                <td width="10">
                    <div class="spacer_6"></div>
                </td>
                <td><?= $GLOBALS["tc"]["favorites_howto"] ?></td>
            </tr>
        </table>
    </div>
<?
}
?>