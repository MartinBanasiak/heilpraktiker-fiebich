<?
if($GLOBALS["shop_user"]["right_return_order"] && $GLOBALS["shop_setup"]["show_rma"] == 1)
{
    switch ($_GET["action_id"]) {
        case 'card':
            show_sales_return_history();
            break;
        default:
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_return_history_listform.inc.php';
            break;
    }
}

function show_sales_return_history() {
    if ($_REQUEST["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_nav_sales_header
				  WHERE id = '" . (int)$_REQUEST["input_id"] . "'
				  	AND bill_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND bill_to_customer_no != ''
				  	AND bill_to_customer_no IS NOT NULL
				  	AND company = '" . $GLOBALS["shop"]["company"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_header      = @mysqli_fetch_array($result);
            $query             = "SELECT *
					  FROM shop_nav_sales_line
					  WHERE document_no = '" . $sales_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND CONCAT(description,description_2)!=''";
            $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_return_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_return_history_listform.inc.php';
    }
}

?>