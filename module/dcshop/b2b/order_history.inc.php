<?
switch ($_GET["action_id"]) {
    case 'card':
        show_order_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'order_history_listform.inc.php';
        break;
}
function show_order_history() {
   if ($_POST["input_id"] <> '') {


        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
				  FROM shop_sales_header
				  WHERE id = :id
				  	AND shop_customer_id = :shop_customer_id
				  	AND shop_customer_id != ''
				  	AND shop_customer_id != 0
				  	AND shop_customer_id IS NOT NULL
				  	AND order_error != 1
				  LIMIT 1
        ";
        $params = [
            [':id', $_POST["input_id"], PDO::PARAM_STR],
            [':shop_customer_id', $GLOBALS["shop_customer"]["id"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();


        if (count($result) > 0) {
            $sales_header      = $result[0];
            $query             = "SELECT shop_item_id AS 'id',
							 item_no, description, summary, allow_invoice_disc, list_price, unit_price AS 'customer_price', quantity AS 'basket_quantity',
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