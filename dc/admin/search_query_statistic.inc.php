<?php
	$translation = \DynCom\dc\common\classes\Registry::get("translation");
	$formname = "form_search_statistic";

	$query             = "SELECT * FROM main_admin_user_shop_link where main_admin_user_id = " . (int)$GLOBALS["admin_user"]['id'];
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
	
	// statistik leeren
	if(isset($_POST['clear_search']) && $_POST['clear_search'] == 1) {
		$query = "DELETE FROM shop_search_query WHERE shop_code = '" . $_POST['input_shop_code'] . "'";
		mysqli_query($GLOBALS['mysql_con'],$query);
	}
	
	$sort_values = array(
		"1",
		"2",
		"3",
		"4"
	);
	
	$sort_names = array(
		$translation->get("frequency_desc"),
		$translation->get("frequency_asc"),
		$translation->get("result_desc"),
		$translation->get("result_asc")
	);
	
	$shop_values = array();
	$shop_names = array();
	
	$query = "SELECT * 
		  FROM shop_shop";
		$result = mysqli_query($GLOBALS['mysql_con'],$query);
		while($shop=mysqli_fetch_assoc($result))
		{
			// wenn benutzer diesen shop nicht sehen darf
			if($shopAdminLink === true && !in_array($shop['code'], $siteAdminAvailableShops)) {
				continue;
			}

			if($_POST['input_shop_code'] == '')
			{
				$_POST['input_shop_code'] = $shop['code'];
			}
			
			$shop_values[] = $shop['code'];
			$shop_names[] = $shop['description'];
		}

$vor2jahren = strtotime("-2 years");
$von_value = date("d.m.Y", $vor2jahren);
$bis_value = date("d.m.Y", time());

if (isset($_POST['input_from'])) {
	$von_value = $_POST['input_from'];
	$bis_value = $_POST['input_to'];
}
?>

<script type="text/javascript">
	function clear_search_stat() {
		if(!confirm("<?php echo $translation->get("confirm_clear_search_stat"); ?>")) {
			return;
		}
		
		$('#clear_search').val("1");
		$('#<?php echo $formname ?>').submit();
	}
</script>

<ul class="toolbar_menu">
	<?= button("google",$translation->get("google_analytics"),$formname,"show_google_analytics()"); ?>
</ul>

<div id="mainContent">
<?php echo current_website_language($site, $language); ?>
<h1><?php echo get_translation('left_search_stat'); ?></h1>

<div class="clearfix"></div>

<?php

if(count($shop_values) == 0) {
	echo "<div class=\"infobox\">" . $translation->get("no_shops_available") . "</div>";
	return;
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
<input type="hidden" name="clear_search" id="clear_search" value="0" />
<div class="requestLoader"></div>
<table class="cardform" style="width:960px;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
		<?php input_select($translation->get("shop"), "input_shop_code", $shop_values, $shop_names, $_POST['input_shop_code'], false, true); ?>
		<?php input_select($translation->get("sorting"), "input_sort_id", $sort_values, $sort_names, $_POST['input_sort_id'], false, true); ?>
		
		</td>
		<td>
			<?php input($translation->get("from"), "input_from", "date", $von_value, 10); ?>
			<?php input($translation->get("to"), "input_to", "date", $bis_value, 10); ?>
		</td>
	</tr>
	<tr>
		<td>
            <br/>
			<input type="button" value="<?php echo $translation->get("clear_search_stat"); ?>" onclick="clear_search_stat();" />
            <br/>
            <br/>
		</td>
	</tr>
	<input type="submit" value="Aktualisieren" style="display:none;"/>
</table>
</form>
<?
$search_query_array = array();
$search_query = "";
if($_POST['input_shop_code'] != '')
{
	$search_query_array[] = "shop_code = '".$_POST['input_shop_code']."'";
}

$search_datetime = array();
if($von_value != "") {
	$search_query_array[] = "search_datetime >= " . datetimetosql($von_value);
}

if($bis_value != "") {
	$search_query_array[] = "search_datetime <= " . datetimetosql($bis_value);
}

if(count($search_query_array)) {
	$search_query = " WHERE " . join(" AND ", $search_query_array);
}

// Es muss eine ID fuer die linklist uebergeben werden
$query = "SET @row_number:=0;";
@mysqli_query($GLOBALS['mysql_con'],$query);

$query = "SELECT 
			@row_number:=@row_number+1 AS id,
			search_query AS '" . $translation->get("searchquery") . "', 
			COUNT(id) AS '" . $translation->get("occurence") . "', 
			MAX(no_of_results) AS '" . $translation->get("results") . "' 
		FROM 
			shop_search_query
			 
			$search_query
			
		GROUP BY 
			search_query"
;
switch($_POST["input_sort_id"]) {
	case "2": $query .= " ORDER BY `" . $translation->get("occurence") . "` ASC"; break;
	case "3": $query .= " ORDER BY `" . $translation->get("results") . "` DESC"; break;
	case "4": $query .= " ORDER BY `" . $translation->get("results") . "` ASC"; break;
	default: $query .= " ORDER BY `" . $translation->get("occurence") . "` DESC"; break;
}
if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
	$format = array("option", "text","integer","integer");
	linklist($result,$formname,$format);
}
?>

</div>