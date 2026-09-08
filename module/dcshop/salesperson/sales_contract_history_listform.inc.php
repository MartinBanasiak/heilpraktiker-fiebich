<? $formname = "form_sales_contract_history_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div
        class="toolbar toolbar-list"><?= button("edit", $GLOBALS["tc"]["show_contract"], $formname, "?shop_category=account&action=contract_history&action_id=card"); ?>
    </div>
    <?
    $query = "SELECT id, no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["contract_no"]) . "', DATE_FORMAT(order_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_date"]) . "', your_reference AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["your_reference"]) . "', amount_including_vat AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["total_amount"]) . "'
		  FROM shop_nav_sales_header
		  WHERE sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company = '" . $GLOBALS["shop"]["company"] . "'
		  ORDER BY order_date DESC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "text", "euro");
        linklist($result, $formname, $format);
    }
    ?>
</form>
