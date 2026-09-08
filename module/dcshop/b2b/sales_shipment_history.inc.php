<?
switch ($_GET["action_id"]) {
    case 'card':
        show_sales_shipment_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_shipment_history_listform.inc.php';
        break;
}
function show_sales_shipment_history() {
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_sales_shipment_header
				  WHERE id = '" . (int)$_POST["input_id"] . "'
				  	AND sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND sell_to_customer_no != ''
				  	AND sell_to_customer_no IS NOT NULL
				  	AND company = '" . $GLOBALS["shop"]["company"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) == 1) {
            $sales_shipment_header      = @mysqli_fetch_array($result);
            $query                      = "SELECT *
					  FROM shop_sales_shipment_line
					  WHERE document_no = '" . $sales_shipment_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND CONCAT(description,description_2)!=''";
            $sales_shipment_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require __DIR__ . DIRECTORY_SEPARATOR . 'sales_shipment_history_cardform.inc.php';
        }
    } elseif ($_GET["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_sales_shipment_header
				  WHERE id = '" . (int)$_GET["input_id"] . "'
				  	AND sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company = '" . $GLOBALS["shop"]["company"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_shipment_header      = @mysqli_fetch_array($result);
            $query                      = "SELECT *
					  FROM shop_sales_shipment_line
					  WHERE document_no = '" . $sales_shipment_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'";
            $sales_shipment_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_shipment_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_shipment_history_listform.inc.php';
    }
}

?>