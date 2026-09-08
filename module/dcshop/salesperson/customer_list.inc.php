<? $formname = "form_shipment_address_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="toolbar">
        <?= button("edit", $GLOBALS["tc"]["customer_set"], $formname, "?shop_category=salesperson_account&action=customer_list&action_id=set"); ?>
    </div>
    <?
    $query = "SELECT id, customer_no AS '" . $GLOBALS["tc"]["cust_no_short"] . "', name AS '" . $GLOBALS["tc"]["name"] . "', city AS '" . $GLOBALS["tc"]["city"] . "', email AS '" . $GLOBALS["tc"]["email"] . "'
		  FROM shop_customer
		  WHERE salesperson_code = '" . $GLOBALS['shop_user']['salesperson_code'] . "'
		  	AND company = '" . $GLOBALS['shop']['company'] . "'";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname);
    }
    ?>
</form>