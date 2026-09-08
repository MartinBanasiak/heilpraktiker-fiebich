<? $formname = "form_shipment_address_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="toolbar">
        <?= button("edit", $GLOBALS["tc"]["edit"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=edit"); ?>
        <?= button("new", $GLOBALS["tc"]["new"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=new"); ?>
        <?= button("delete", $GLOBALS["tc"]["delete"], $formname, "?shop_category=account&action=edit_shipment_address&action_id=delete", $GLOBALS["tc"]["delete_ship_addr"]); ?>
    </div>
    <?
    $query = "SELECT id, CONCAT(COALESCE(name,''),' ',COALESCE(name_2,''),' ') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["name"]) . "',
				 contact AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["contact"]) . "', CONCAT(COALESCE(address,''),' ',COALESCE(address_2,'')) AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["address"]) . "',
				 post_code AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["post_code"]) . "', city AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["city"]) . "', phone_no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["phone_no"]) . "'
		  FROM shop_shipment_address
		  WHERE customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company ='" . $GLOBALS['shop']['company'] . "'
		  ORDER BY name ASC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname);
    }
    ?>
</form>