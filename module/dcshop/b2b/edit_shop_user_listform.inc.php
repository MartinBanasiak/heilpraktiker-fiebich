<? $formname = "form_shop_user_list"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["edit_shop_user"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="button_row">
        <?= button("button_new button", $GLOBALS["tc"]["new"], $formname, "?action=edit_shop_user&action_id=new"); ?>
    </div>
    <?
    $query = "SELECT id, name AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["name"]) . "', email AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["email"]) . "', login AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["username"]) . "',
				 main_user AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["main_user"]) . "'
		  FROM shop_user
		  WHERE customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company = '" . $GLOBALS['shop']['company'] . "'
		  	AND shop_code = '" . $GLOBALS['shop']['code'] . "'
		  ORDER BY name ASC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "boolean");
        linklist($result, $formname, $format);
    }
    ?>
    <div class="button_row">
        <?= button("button_new button", $GLOBALS["tc"]["new"], $formname, "?action=edit_shop_user&action_id=new"); ?>
    </div>
</form>