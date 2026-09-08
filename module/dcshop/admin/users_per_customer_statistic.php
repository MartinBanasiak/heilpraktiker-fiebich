<h3><?= $GLOBALS["tc"]["user_per_customer"] ?></h3><br />
<? $formname = "form_user_per_customer_statistic"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">

    <?
    $query = "SELECT sc.name, sc.id, sc.customer_no FROM shop_customer as sc WHERE active = 1";

    echo "  <table cellpadding=0 cellspacing=0 border=0 class=\"linklist\">\n";
    echo "<tr>\n" . format_key("Kundennr", "input_id", "text") . "\n" . format_key("Kunde", "input_id", "text") . "\n" . format_key("Benutzer", "input_id", "text") . "\n" . format_key("EMail", "input_id", "text") . "\n" . format_key("Hauptnutzer", "input_id", "text") . "\n</tr>\n";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    while ($val = mysqli_fetch_array($result)) {
        echo "<tr>" . format_value($val['customer_no'], "input_id", "text") . "\n" . format_value($val['name'], "input_id", "text") . "\n" . format_key("", "input_id", "option") . "\n" . format_key("", "input_id", "option") . "\n" . format_key("", "input_id", "option") . "</tr>\n";

        $query2  = "SELECT su.name as 'name', su.email as 'email', su.main_user as 'main_user' FROM shop_user as su WHERE su.customer_no = '" . $val['customer_no'] . "'";
        $result2 = mysqli_query($GLOBALS['mysql_con'], $query2);

        if (mysqli_num_rows($result2) > 0) {
            while ($val2 = mysqli_fetch_array($result2)) {
                echo "<tr>\n" . format_key("", "input_id", "option") . "\n" . format_key("", "input_id", "option") . "\n" . format_value($val2['name'], "input_id", "text") . "\n" . format_value($val2['email'], "input_id", "text") . "\n" . format_value($val2['main_user'], "input_id", "boolean") . "</tr>\n";
            }
        }
    }
    echo "  </table>\n";

    ?>
</form>
