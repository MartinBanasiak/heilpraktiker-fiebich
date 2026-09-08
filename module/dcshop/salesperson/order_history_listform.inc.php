<? $formname = "form_order_history_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <div class="toolbar">
        <?= button("edit", $GLOBALS["tc"]["show_order"], $formname, "?shop_category=account&action=order_history&action_id=card"); ?>
    </div>
    <?
    $query = "SELECT id, order_no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_no"]) . "', DATE_FORMAT(order_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_date"]) . "',
				 user_name AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["username"]) . "', your_reference AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["your_reference"]) . "',
				 total AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["total_amount"]) . "'
		  FROM shop_sales_header
		  WHERE shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
		  	AND order_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)
		  ORDER BY order_date DESC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "text", "euro");
        linklist($result, $formname, $format);
    }
    ?>
</form>