<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");

$query             = "SELECT * FROM main_admin_user_shop_link where main_admin_user_id = '" . (int)$GLOBALS["admin_user"]['id']."'";
$result            = @mysqli_query($GLOBALS['mysql_con'], $query);
$siteAdminAvailableShops = array();
if (@mysqli_num_rows($result) > 0) {
    $shopAdminLink = true;
    while ($row = @mysqli_fetch_array($result)) {
        if($row['active'] == 1) {
            $siteAdminAvailableShops[] = $row['shop_code'];
        }
    }
} else {
    $shopAdminLink = false;
}

$zeitraum_values = array(
    "%d.%m.%Y",
    "KW%v-%x",
    "%m/%Y",
    "%Y"
);

$zeitraum_names = array(
    $translation->get("days"),
    $translation->get("calendarweeks"),
    $translation->get("months"),
    $translation->get("years")
);

$shop_values = array();
$shop_names = array();

$query = "SELECT * 
		  FROM shop_shop";
$result = mysqli_query($GLOBALS['mysql_con'], $query);
while ($shop = mysqli_fetch_assoc($result)) {

    // wenn benutzer diesen shop nicht sehen darf
    if($shopAdminLink === true && !in_array($shop['code'], $siteAdminAvailableShops)) {
        continue;
    }

    if ($_POST['input_shop'] == '') {
        $_POST['input_shop'] = $shop['code'];
    }

    $shop_values[] = $shop['code'];
    $shop_names[] = $shop['description'];
}


if(count($shop_values)) {
    $von_value = "";
    $bis_value = date("d.m.Y", time());

    if (isset($_POST['input_from'])) {
        $von_value = $_POST['input_from'];
        $bis_value = $_POST['input_to'];
    }

    if ($_POST['input_shop'] != $_POST['old_shop_code']) {
        $von_value = "";
    }

    // datum der ersten bestellung aus dem aktuellen shop auslesen
    $query = "SELECT MIN(order_date) as order_date FROM shop_sales_header WHERE shop_code = '" . $_POST['input_shop'] . "' AND successful = 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $shop = mysqli_fetch_assoc($result);
    $mindate = $shop['order_date'];
    $mindateTs = strtotime($mindate);

    $vor2jahren = strtotime("-2 years");

    if ($von_value == "") {
        if ($mindateTs > $vor2jahren) {
            $von_value = datefromsql($mindate);
        } else {
            $von_value = date("d.m.Y", $vor2jahren);
        }
    }

    if ($von_value != "" && $mindateTs > strtotime($von_value)) {
        $von_value = datefromsql($mindate);
    }

    if ($mindateTs === false) {
        $von_value = "";
    }
}
?>

<ul class="toolbar_menu">
    <?= button("google", $translation->get("google_analytics"), $formname, "show_google_analytics()"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_order_stat'); ?></h1>

    <div class="clearfix"></div>

    <?php

    if(count($shop_values) == 0) {
        echo "<div class=\"infobox\">" . $translation->get("no_shops_available") . "</div>";
        return;
    }
    ?>

    <form id="order_statistic_form" name="order_statistic_form" action="<?php $PHP_SELF; ?>" method=POST>
        <input type="hidden" value="<?php echo $_POST['input_shop']; ?>" name="old_shop_code"/>
        <div class="requestLoader"></div>
        <table class="cardform" style="width:960px;" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <?php input_select($translation->get("shop"), "input_shop", $shop_values, $shop_names, $_POST['input_shop'], false, true); ?>
                    <?php input_select($translation->get("intervall"), "input_interval", $zeitraum_values, $zeitraum_names, $_POST['input_interval'], false, true); ?>
                </td>
                <td>
                    <?php input($translation->get("from"), "input_from", "date", $von_value, 10); ?>
                    <?php input($translation->get("to"), "input_to", "date", $bis_value, 10); ?>
                </td>
            </tr>
        </table>
        <input type="submit" value="Aktualisieren" style="display:none;"/>
    </form>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#mainContent input.date').datepicker("option", "onSelect", function () {
                $('#order_statistic_form').submit();
            });
        })

    </script>
    <?php
    if ($_POST['input_interval'] == "") {
        $interval = "%d.%m.%Y";
    } else {
        $interval = $_POST['input_interval'];
    }
    //$query="SELECT date_format( order_date, '".$interval."' ) AS 'Zeitraum', count( id ) AS 'Bestellungen', round( sum( total ) , 2 ) AS 'Summe Rechnung', round( sum( subtotal ) , 2 ) AS 'Summe Waren', round( avg( subtotal ) , 2 ) AS 'Durchschn. Warenwert', round( min( subtotal ) , 2 ) AS 'Kleinste', round( max( subtotal ) , 2 ) AS 'Größte',
    //SUM(
    //CASE WHEN payment_terms = 'LAST'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Lastschr.',
    //SUM(
    //CASE WHEN payment_terms = 'Nachnahme'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Nachnahme',
    //SUM(
    //CASE WHEN payment_terms = 'PAYPAL'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Paypal',
    //SUM(
    //CASE WHEN payment_terms = 'KREDITKART'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Kreditk.',
    //SUM(
    //CASE WHEN payment_terms = '14N'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Rechnung',
    //SUM(
    //CASE WHEN payment_terms = 'TRUFFEL'
    //THEN 1
    //ELSE 0
    //END )/COUNT(id)*100 as '% Trüffelzins'
    //FROM shop_sales_header
    //GROUP BY DATE_FORMAT( order_date, '".$interval."' )
    //ORDER BY order_date DESC
    //LIMIT 60
    //";
    //$format = array('text','integer','euro','euro','euro','euro','euro','decimal','decimal','decimal','decimal','decimal','decimal');
    $query = "SET @row_number:=0;";
    @mysqli_query($GLOBALS['mysql_con'], $query);

    // where zeitraum
    $where_von = "";
    $where_bis = "";
    if ($von_value != "") {
        $where_von = " and order_date >= " . datetosql($von_value) . " ";
    }

    if ($bis_value != "") {
        $where_bis = " and order_date <= " . datetosql($bis_value) . " ";
    }

    $query = "
	SELECT 
		@row_number:=@row_number+1 AS id,
		date_format( order_date, '" . $interval . "' ) AS '" . $translation->get("period") . "', 
		count( id ) AS '" . $translation->get("orders") . "', 
		round( sum( total ) , 2 ) AS '" . $translation->get("sum_bill") . "', 
		round( sum( subtotal ) , 2 ) AS '" . $translation->get("sum_goods") . "', 
		round( avg( subtotal ) , 2 ) AS '" . $translation->get("avarage_goods_sum") . "', 
		round( min( subtotal ) , 2 ) AS '" . $translation->get("sum_min") . "', 
		round( max( subtotal ) , 2 ) AS '" . $translation->get("sum_max") . "'
	FROM 
		shop_sales_header
	WHERE 
		shop_code = '" . $_POST['input_shop'] . "'
	  AND
	    successful = 1
		$where_von
		$where_bis
	GROUP BY 
		DATE_FORMAT( order_date, '" . $interval . "' )
	ORDER BY 
		order_date DESC
	LIMIT 
		60
";

    $format = array('option', 'text', 'integer', 'euro', 'euro', 'euro', 'euro', 'euro');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, "", $format);
    }
    ?>

</div>