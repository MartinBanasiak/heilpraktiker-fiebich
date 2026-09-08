<?
switch ($_GET["action_id"]) {
    case 'card':
        show_sales_contract_history();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_contract_history_listform.inc.php';
        break;
}
function show_sales_contract_history() {
    if ($_POST["input_id"] <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
				  FROM shop_nav_sales_header
				  WHERE id = :id
				  	AND bill_to_customer_no = :bill_to_customer_no
				  	AND bill_to_customer_no != ''
				  	AND bill_to_customer_no IS NOT NULL
				  	AND company = :company   LIMIT 1
        ";
        $params = [
            [':id', $_POST["input_id"], PDO::PARAM_STR],
            [':bill_to_customer_no', $GLOBALS["shop_customer"]["customer_no"], PDO::PARAM_STR],
            [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();


        if (count($result) == 1) {
            $sales_header      = $result[0];
            $query             = "SELECT *
					  FROM shop_nav_sales_line
					  WHERE document_no = '" . $sales_header["no"] . "'
					  	AND company = '" . $GLOBALS["shop"]["company"] . "'
						AND CONCAT(description,description_2)!=''";
            $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_contract_history_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'sales_contract_history_listform.inc.php';
    }
}

?>