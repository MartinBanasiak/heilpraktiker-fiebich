<?
$query  = "SELECT * FROM shop_salesperson WHERE company = '" . $GLOBALS['shop']['company'] . "' AND salesperson_code = '" . $GLOBALS["shop_customer"]["salesperson_code"] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $salesperson = @mysqli_fetch_array($result);
    echo "<div class=\"spacer_6\"></div>";
    echo "<strong>" . $salesperson["name"] . "</strong><br />\n";
    echo "<div class=\"spacer_6\"></div>";
    echo "<table cellpadding=0 cellspacing=0 bolder=0>";
    if ($salesperson["phone_no"] <> '') {
        echo "<tr>\n";
        echo "<td>" . $GLOBALS["tc"]["phone_no"] . ":</td>\n";
        echo "<td><div class=\"spacer_6\"></div></td>\n";
        echo "<td>" . $salesperson["phone_no"] . "</td>\n";
        echo "</tr>\n";
    }
    if ($salesperson["email"] <> '') {
        echo "<tr>\n";
        echo "<td>" . $GLOBALS["tc"]["email"] . ":</td>\n";
        echo "<td><div class=\"spacer_6\"></div></td>\n";
        echo "<td><a href=\"mailto:" . $salesperson["email"] . "\">" . $salesperson["email"] . "</a></td>\n";
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<div class=\"spacer_6\"></div>";
}
$_POST["input_customer_no"] = $GLOBALS["shop_customer"]["customer_no"];
$_POST["input_user_name"]   = $GLOBALS["shop_user"]["name"];
$_POST["input_email"]       = $GLOBALS["shop_user"]["email"];
?>