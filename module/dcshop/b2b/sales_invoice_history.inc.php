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
   if ($_REQUEST["input_id"] <> '') {


        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = "SELECT *
				  FROM shop_sales_invoice_header
				  WHERE id = :id
				  	AND bill_to_customer_no = :bill_to_customer_no
				  	AND company = :company
        ";
        $params = [
            [':id', $_REQUEST["input_id"], PDO::PARAM_INT],
            [':bill_to_customer_no', $GLOBALS["shop_customer"]["customer_no"], PDO::PARAM_STR],
            [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $sales_invoice_header      = $result[0];
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