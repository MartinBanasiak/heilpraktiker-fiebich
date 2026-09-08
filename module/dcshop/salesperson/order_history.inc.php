<?
switch ($_GET["action_id"]) {
    case 'show':
        show_order_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_listform.inc.php';
        break;
}
function show_order_history() {
    if ($_POST["input_id"] <> '') {
        $query  = "SELECT *
				  FROM shop_sales_header
				  WHERE id = '" . $_POST["input_id"] . "'
				  	AND shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
          		  	AND shop_customer_id != ''
				  	AND shop_customer_id != 0
				  	AND shop_customer_id IS NOT NULL
				  	AND order_error != 1
				  LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $sales_header      = @mysqli_fetch_array($result);
            $query             = "SELECT shop_item_id AS 'id',
										 item_no, description, summary, allow_invoice_disc, list_price, unit_price, quantity AS 'basket_quantity',
										 line_amount
								  FROM shop_sales_line
								  WHERE shop_sales_header_id = '" . $sales_header["id"] . "'";
            $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_listform.inc.php';
    }
}

?>