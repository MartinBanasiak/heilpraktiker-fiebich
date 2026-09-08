<? $formname = "form_shop_user_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="toolbar">
        <?= button("edit", $GLOBALS["tc"]["edit"], $formname, "?action=edit_shop_user&action_id=edit"); ?>
        <?= button("new", $GLOBALS["tc"]["new"], $formname, "?action=edit_shop_user&action_id=new"); ?>
        <?= button("delete", $GLOBALS["tc"]["delete"], $formname, "?action=edit_shop_user&action_id=delete", $GLOBALS["tc"]["delete_user"]); ?>
    </div>
    <?
    $query = "SELECT id, name AS '" . $GLOBALS["tc"]["name"] . "', email AS '" . $GLOBALS["tc"]["email"] . "', login AS '" . $GLOBALS["tc"]["username"] . "',
				 main_user AS '" . $GLOBALS["tc"]["main_user"] . "'
		  FROM shop_user
		  WHERE shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
		  ORDER BY name ASC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "boolean");
        linklist($result, $formname, $format);
    }
    ?>
</form>