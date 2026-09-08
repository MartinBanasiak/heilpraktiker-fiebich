<? $formname = "form_sales_cr_memo_history_list"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_cr_memo_history"] ?></h1>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <?
    $query = "SELECT id, no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["cr_memo_no"]) . "', DATE_FORMAT(posting_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["posting_date"]) . "', your_reference AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["your_reference"]) . "', amount_including_vat AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["total_amount"]) . "'
		  FROM shop_cr_memo_header
		  WHERE sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
		  	AND company = '" . $GLOBALS["shop"]["company"] . "'
		  ORDER BY posting_date DESC";
    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "euro");
        linklist($result, $formname, $format);
    }
    ?>
</form>
