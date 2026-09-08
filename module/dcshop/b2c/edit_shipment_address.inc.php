<?
switch ($_GET["action_id"]) {
    case 'edit':
        edit_shipment_address();
        break;
    case 'delete':
        delete_shipment_address();
        break;
    case 'new':
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_cardform.inc.php';
        break;
    case 'save':
        save_shipment_address();
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
        break;
}

function edit_shipment_address() {


    if ($_POST["input_id"] <> '') {

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT *
				FROM shop_shipment_address
				WHERE id = :id
					AND customer_no = :customer_no
					AND company = :company
				LIMIT 1
        ";
        $params = [
            [':id', $_POST["input_id"], PDO::PARAM_STR],
            [':customer_no', $GLOBALS["shop_customer"]["customer_no"], PDO::PARAM_STR],
            [':company', $GLOBALS["shop"]["company"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if (count($result) == 1) {
            $input_shipment_address = $result[0];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
    }
}

function delete_shipment_address() {
    if ($_POST["input_id"] <> '') {
        $query = "DELETE FROM shop_shipment_address
				  WHERE id = '" . $_POST["input_id"] . "'
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
}

function save_shipment_address() {
    if (isset($_POST["save_data"])) {
        if ($_POST["input_name_2"] != "") {
            $name_1 = $_POST["input_name_2"];
            $name_2 = $_POST["input_name"] . " " . $_POST["input_lastname"];
        } else {
            $name_1 = $_POST["input_name"] . " " . $_POST["input_lastname"];
            $name_2 = "";
        }
        $address = $_POST["input_address_street"] . " " . $_POST["input_address_no"];
        if ($_POST["input_id"] <> '') {
            $query = "UPDATE shop_shipment_address
                      SET name = '" . $name_1 . "', name_2 = '" . $name_2 . "',
                          contact = '" . $_POST["input_contact"] . "', address = '" . $address . "',					  
                          address_2 = '" . $_POST["input_address_2"] . "',address_street = '" . $_POST["input_address_street"] . "', address_no = '" . $_POST["input_address_no"] . "',
                          post_code = '" . $_POST["input_post_code"] . "',
                          city = '" . $_POST["input_city"] . "', country = '" . $_POST["input_country"] . "',
                          phone_no = '" . $_POST["input_telephone"] . "', surname = '" . $_POST["input_name"] . "', lastname = '" . $_POST["input_lastname"] . "', company_name = '" . $_POST["input_name_2"] . "'
                      WHERE id = '" . $_POST["input_id"] . "'
                        AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
                        AND company ='" . $GLOBALS['shop']['company'] . "'
                      LIMIT 1";
        } else {
            $query = "INSERT INTO shop_shipment_address (id,company,customer_no,name,name_2,contact,address,address_2,address_street,address_no,post_code,city,country,phone_no,surname,lastname,company_name)
                      VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS["shop_customer"]["customer_no"] . "','" . $name_1 . "',
                              '" . $name_2 . "','" . $_POST["input_contact"] . "','" . $address . "',
                              '" . $_POST["input_address_2"] . "','" . $_POST["input_address_street"] . "','" . $_POST["input_address_no"] . "','" . $_POST["input_post_code"] . "','" . $_POST["input_city"] . "',
                              '" . $_POST["input_country"] . "', '" . $_POST["input_telephone"] . "', '" . $_POST["input_name"] . "','" . $_POST["input_lastname"] . "','" . $_POST["input_name_2"] . "')";
        }
        if (($_POST["input_name"] == '') | ($address == ' ') | ($_POST["input_city"] == '') | ($_POST["input_post_code"] == '')) {
            //echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_ship_addr"] . "</div>\n";
            $error = TRUE;
            $errormessage = $GLOBALS["tc"]["error_1_ship_addr"];
        }
        if ($error) {
            get_requestbox($errormessage, "");
        }
        if (!$error) {
            @mysqli_query($GLOBALS['mysql_con'], $query);

            get_requestbox($GLOBALS['tc']['data_saved'], "", "success");
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
        } else {
            $input_shipment_address["id"] = $_POST["input_id"];
            $input_shipment_address["name"] = $_POST["input_name"];
            $input_shipment_address["name_2"] = $_POST["input_name_2"];
            $input_shipment_address["contact"] = $_POST["input_contact"];
            $input_shipment_address["address_street"] = $_POST["input_address_street"];
            $input_shipment_address["address_no"] = $_POST["input_address_no"];
            $input_shipment_address["address_2"] = $_POST["input_address_2"];
            $input_shipment_address["post_code"] = $_POST["input_post_code"];
            $input_shipment_address["city"] = $_POST["input_city"];
            $input_shipment_address["country"] = $_POST["input_country"];
            $input_shipment_address["telephone"] = $_POST["input_telephone"];
            $input_shipment_address["surname"] = $_POST["input_name"];
            $input_shipment_address["lastname"] = $_POST["input_lastname"];
            $input_shipment_address["company_name"] = $_POST["input_name_2"];
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
    }
}

?>