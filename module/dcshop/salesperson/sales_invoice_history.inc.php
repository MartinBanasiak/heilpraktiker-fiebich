<?
switch ($_GET["action_id"]) {
    case 'card':
        show_sales_invoice_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_invoice_history_listform.inc.php';
        break;
}
function show_sales_invoice_history() {
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_sales_invoice_header
				  WHERE id = '" . $_POST["input_id"] . "'
				  	AND bill_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND bill_to_customer_no != ''
				  	AND bill_to_customer_no IS NOT NULL				  	
				  	AND company = '" . $GLOBALS["shop"]["company"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_invoice_header      = @mysqli_fetch_array($result);
            $query                     = "SELECT *
					  FROM shop_sales_invoice_line
					  WHERE document_no = '" . $sales_invoice_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND CONCAT(description,description_2)!=''";
            $sales_invoice_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_invoice_history_cardform.inc.php';
        }
    } elseif ($_GET["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_sales_invoice_header
				  WHERE id = '" . $_GET["input_id"] . "'
				  	AND bill_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company = '" . $GLOBALS["shop"]["company"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_invoice_header      = @mysqli_fetch_array($result);
            $query                     = "SELECT *
					  FROM shop_sales_invoice_line
					  WHERE document_no = '" . $sales_invoice_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'";
            $sales_invoice_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_invoice_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_invoice_history_listform.inc.php';
    }
}

?>