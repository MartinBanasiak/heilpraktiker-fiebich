<? $formname = "form_shipment_address_list"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["shipment_addresses"] ?></h1>
    <?=$GLOBALS['tc']['shop_account_shipment_address']?>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <?
    $query = "SELECT id, CONCAT(COALESCE(name,''),' ',COALESCE(name_2,''),' ') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["name"]) . "',
				 CONCAT(COALESCE(address,''),' ',COALESCE(address_2,'')) AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["address"]) . "',
				 post_code AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["post_code"]) . "', city AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["city"]) . "'
		  FROM shop_shipment_address
		  WHERE customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company ='" . $GLOBALS['shop']['company'] . "'
		  ORDER BY name ASC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        ?>

        <div class="button_row">
            <?= button("new button", $GLOBALS["tc"]["new"], $formname, "?action=edit_shipment_address&action_id=new"); ?>
        </div>

    <?
        linklist($result, $formname);
    }
    ?>
    <div class="button_row">
        <?= button("new button", $GLOBALS["tc"]["new"], $formname, "?action=edit_shipment_address&action_id=new"); ?>
    </div>
</form>