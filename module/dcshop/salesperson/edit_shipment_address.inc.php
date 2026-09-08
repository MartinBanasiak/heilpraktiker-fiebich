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
        $query  = "SELECT *
				FROM shop_shipment_address
				WHERE id = '" . $_POST["input_id"] . "'
					AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
					AND company ='" . $GLOBALS['shop']['company'] . "'
				LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_shipment_address = @mysqli_fetch_array($result);
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
    if ($_POST["input_id"] <> '') {
        $query = "UPDATE shop_shipment_address
				  SET name = '" . $_POST["input_name"] . "', name_2 = '" . $_POST["input_name_2"] . "',
				  	  contact = '" . $_POST["input_contact"] . "', address = '" . $_POST["input_address"] . "',
				  	  address_2 = '" . $_POST["input_address_2"] . "', post_code = '" . $_POST["input_post_code"] . "',
				  	  city = '" . $_POST["input_city"] . "', country = '" . $_POST["input_country"] . "',
				  	  telephone = '" . $_POST["input_telephone"] . "'
				  WHERE id = '" . $_POST["input_id"] . "'
				  	AND customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
				  	AND company ='" . $GLOBALS['shop']['company'] . "'
				  LIMIT 1";
    } else {
        $query = "INSERT INTO shop_shipment_address (id,company,customer_no,name,name_2,contact,address,address_2,post_code,city,country,telephone)
				  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS["shop_customer"]["customer_no"] . "','" . $_POST["input_name"] . "',
				  		  '" . $_POST["input_name_2"] . "','" . $_POST["input_contact"] . "','" . $_POST["input_address"] . "',
				  		  '" . $_POST["input_address_2"] . "','" . $_POST["input_post_code"] . "','" . $_POST["input_city"] . "',
				  		  '" . $_POST["input_country"] . "', '" . $_POST["input_telephone"] . "')";
    }
    if (($_POST["input_name"] == '') | ($_POST["input_address"] == '') | ($_POST["input_city"] == '') | ($_POST["input_post_code"] == '')) {
        echo "<div class=\"errorbox\">" . $GLOBALS["tc"]["error_1_ship_addr"] . "</div>\n";
        $error = TRUE;
    }
    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_listform.inc.php';
    } else {
        $input_shipment_address["id"]        = $_POST["input_id"];
        $input_shipment_address["name"]      = $_POST["input_name"];
        $input_shipment_address["name_2"]    = $_POST["input_name_2"];
        $input_shipment_address["contact"]   = $_POST["input_contact"];
        $input_shipment_address["address"]   = $_POST["input_address"];
        $input_shipment_address["address_2"] = $_POST["input_address_2"];
        $input_shipment_address["post_code"] = $_POST["input_post_code"];
        $input_shipment_address["city"]      = $_POST["input_city"];
        $input_shipment_address["country"]   = $_POST["input_country"];
        $input_shipment_address["telephone"] = $_POST["input_telephone"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shipment_address_cardform.inc.php';
    }
}

?>