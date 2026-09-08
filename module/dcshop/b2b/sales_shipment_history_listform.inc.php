<? $formname = "form_sales_shipment_history_list"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_shipment_history"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <?
    $query = "SELECT id, no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["sales_shipment_no"]) . "', DATE_FORMAT(posting_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["sales_shipment_date"]) . "', your_reference AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["your_reference"]) . "'
		  FROM shop_sales_shipment_header
		  WHERE sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company = '" . $GLOBALS["shop"]["company"] . "'
		  ORDER BY posting_date DESC";

    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "text");
        linklist($result, $formname, $format);
    } ?>
    <?
    ?></form>
