<?
if($GLOBALS["shop_user"]["right_return_order"] && $GLOBALS["shop_setup"]["show_rma"] == 1) {

    $formname = "form_sales_return_history_list"; ?>
    <div class="category_info">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["sales_return_history"] ?></h1>
    </div>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <?
        $query = "SELECT id, no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["return_no"]) . "', DATE_FORMAT(order_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_date"]) . "', your_reference AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["your_reference"]) . "'
		  FROM shop_nav_sales_header
		  WHERE sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
			AND type = 5
		  	AND company = '" . $GLOBALS["shop"]["company"] . "'
		  ORDER BY order_date DESC";
        $numRows = mysqli_num_rows($GLOBALS['mysql_con'], $query);
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        //button("new button", $GLOBALS["tc"]["new"], $formname, "?shop_category=rma");
        if ($numRows > 0) {
            button("new button ", $GLOBALS["tc"]["new"], $formname, "/".customizeUrl()."/rma/");
            $format = array("option", "text", "text", "text", "text");
            linklist($result, $formname, $format);
            button("new button ", $GLOBALS["tc"]["new"], $formname, "/".customizeUrl()."/rma/");
        } else {
            echo "<div class=\"emptybox\"" . $style . ">Keine Datens&auml;tze vorhanden</div>";
            button("new button", $GLOBALS["tc"]["new"], $formname, "/".customizeUrl()."/rma/");
        }

        ?>
    </form>
    <?
}
?>
