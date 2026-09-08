<?

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $token2 = json_decode(base64_decode($_POST["token_2"]));
    $timeout = 3600;

    if ($token2->time < (time() - $timeout)) {
        die();
    }

    if ($token2->token_id !== $_SERVER['SERVER_NAME']) {
        die();
    }


    $baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
    include($baseDirectory . '/vendor/autoload.php');

    //Load environment variables from config if exists
    $envDir = rtrim($baseDirectory, '/') . '/config';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    $secret = getenv('SHOP_PASSWORD');

    $secretToken2 = array(
        "time"=>$token2->time,
        "token_id"=>$token2->token_id
    );


    $valid = ($_POST["token_1"] === base64_encode(hash_hmac("sha256", json_encode($secretToken2), $secret)));

    if (!$valid) {
        die();
    }


    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $GLOBALS["myservername"] = $pdoHost;
    $GLOBALS["mydb"] = $pdoSchema;
    $GLOBALS["mylogin"] = $pdoUser;
    $GLOBALS["mypass"] = $pdoPass;
    $GLOBALS['tc'] = '';
    $GLOBALS['site']['site_url'] = '';
    $GLOBALS['site']['code'] = '';
    $GLOBALS['language']['code'] = '';

    $jsonSecretToken = '';

    if (isset($_POST['secretToken']) && $_POST['secretToken'] != '') {

        $methods = openssl_get_cipher_methods();
        $jsonSecretToken = openssl_decrypt( $_POST['secretToken'], $methods[37], $secret);
    } else {
        die();
    }
    $secretToken = json_decode($jsonSecretToken, true);

    if (!isset($secretToken['secretAction']) || $secretToken['secretAction'] != "GetOrAddOrEditData") {
        die();
    }

    $action = "";

    if (isset($_POST['action']) && $_POST['action'] != '') {
        $shiptmentId = '';
        $responseArray = array();

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);
        if (isset($_POST["shop_shipment_address_id"]) && $_POST["shop_shipment_address_id"] != '' && $_POST['action'] = 'getShipmentAddressData') {

            $shiptmentId = $_POST['shop_shipment_address_id'];

            $resultArray = getShipmentAddress($shiptmentId, $pdo);

            echo json_encode($resultArray);
        } elseif ($_POST['action'] == 'Add') {
            if ((!isset($_POST["input_name_Modal"]) || $_POST["input_name_Modal"] == '') || (!isset($_POST["input_address_Modal"]) || $_POST["input_address_Modal"] == '')
                || (!isset($_POST["input_post_code_Modal"]) || $_POST["input_post_code_Modal"] == '') || (!isset($_POST["input_city_Modal"]) || $_POST["input_city_Modal"] == '')
            ) {
                $data = array('type' => 'error', 'message' => 'Please fill required fields');
                headerFunctionBridge('HTTP/1.1 400 Bad Request');
                headerFunctionBridge('Content-Type: application/json; charset=UTF-8');
                echo json_encode($data);
                die();
            }

            $prepStatement = "INSERT 
                                INTO 
                                      shop_shipment_address
                                      (
                                          company,
                                          customer_no,
                                          name,
                                          name_2,
                                          contact,
                                          address,
                                          address_2,
                                          post_code,
                                          city,
                                          phone_no
                                      )
                                      VALUES 
                                         (
                                            :company,
                                            :customer_no,
                                            :name,
                                            :name_2,
                                            :contact,
                                            :address,
                                            :address_2,
                                            :post_code,
                                            :city,
                                            :phone_no
                                            
                                         )
                         ";

            $params = [
                [':company', $_POST["company"], PDO::PARAM_STR],
                [':customer_no', $_POST["customer_no"], PDO::PARAM_STR],
                [':name', $_POST["input_name_Modal"], PDO::PARAM_STR],
                [':name_2', $_POST["input_name_2_Modal"], PDO::PARAM_STR],
                [':contact', $_POST["input_contact_Modal"], PDO::PARAM_STR],
                [':address', $_POST["input_address_Modal"], PDO::PARAM_STR],
                [':address_2', $_POST["input_address_2_Modal"], PDO::PARAM_STR],
                [':post_code', $_POST["input_post_code_Modal"], PDO::PARAM_STR],
                [':city', $_POST["input_city_Modal"], PDO::PARAM_STR],
                [':phone_no', $_POST["input_telephone_Modal"], PDO::PARAM_STR],

            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();

            $addressId = $pdo->getLastInsertId();

            $shipment_address = getShipmentAddress($addressId, $pdo);

            $options = '';
            if (count($shipment_address) > 0) {
                for ($i = 0; $i < count($shipment_address); $i++) {

                    if (!empty($shipment_address[$i]["name_2"])) {
                        $description = $shipment_address[$i]["name"] . " - " . $shipment_address[$i]["name_2"] . " - " . $shipment_address[$i]["address"] . " " . $shipment_address[$i]["address_2"] . ", " . $shipment_address[$i]['post_code'] . " " . $shipment_address[$i]["city"];
                    } else {
                        $description = $shipment_address[$i]["name"] . " - " . $shipment_address[$i]["address"] . " " . $shipment_address[$i]["address_2"] . ", " . $shipment_address[$i]['post_code'] . " " . $shipment_address[$i]["city"];
                    }
                    if ($shipment_address[$i]["id"] == $addressId) {
                        $options = $options . "<option selected=\"selected\" value=\"" . htmlentities($shipment_address[$i]["id"]) . "\">" . htmlentities($description) . "</option>";
                    } else {
                        $options = $options . "<option value=\"" . htmlentities($shipment_address[$i]["id"]) . "\">" . htmlentities($description) . "</option>";
                    }

                }
            }

            echo $options;

        } elseif ($_POST['action'] == 'Edit') {
            if ((!isset($_POST['input_shipment_address_id']) || $_POST['input_shipment_address_id'] == '') || (!isset($_POST["input_name_Modal"]) || $_POST["input_name_Modal"] == '') || (!isset($_POST["input_address_Modal"]) || $_POST["input_address_Modal"] == '')
                || (!isset($_POST["input_post_code_Modal"]) || $_POST["input_post_code_Modal"] == '') || (!isset($_POST["input_city_Modal"]) || $_POST["input_city_Modal"] == '')
            ) {
                $data = array('type' => 'error', 'message' => 'Please fill required fields');
                headerFunctionBridge('HTTP/1.1 400 Bad Request');
                headerFunctionBridge('Content-Type: application/json; charset=UTF-8');
                echo json_encode($data);
                die();
            }

            $prepStatement = "
              UPDATE
               shop_shipment_address
				  SET   name = :name,
				        name_2 = :name_2,
				  	    contact = :contact,
				  	    address = :address,
				  	    address_2 = :address_2,
				  	    post_code = :post_code,
				  	    city = :city,
				  	   	phone_no = :phone_no
              WHERE
                    id = :id
                    ";


            $params = [
                [':name', $_POST["input_name_Modal"], PDO::PARAM_STR],
                [':name_2', $_POST["input_name_2_Modal"], PDO::PARAM_STR],
                [':contact', $_POST["input_contact_Modal"], PDO::PARAM_STR],
                [':address', $_POST["input_address_Modal"], PDO::PARAM_STR],
                [':address_2', $_POST["input_address_2_Modal"], PDO::PARAM_STR],
                [':post_code', $_POST["input_post_code_Modal"], PDO::PARAM_STR],
                [':city', $_POST["input_city_Modal"], PDO::PARAM_STR],
                [':phone_no', $_POST["input_telephone_Modal"], PDO::PARAM_STR],
                [':id', $_POST["input_shipment_address_id"], PDO::PARAM_STR],

            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();


            echo '';

        }

    }
} else {
    die();
}


function getShipmentAddress($shiptmentId, $pdo)
{
    $prepStatement = '
          SELECT 
            *
          FROM 
            shop_shipment_address  
          WHERE 
                id = :shiptmentId 
        ';
    $params = [
        [':shiptmentId', $shiptmentId, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    return $resultArray = $pdo->getResultArray();
}

?>