<?php

$allowed_functions = ['check_shipment','customize_delete_pic'];
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
if (in_array($_GET['function'],$allowed_functions,true)) {
    require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
    require_once (local_environment()) ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . 'dc/dc-server.config.php';
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/shop_functions.inc.php';
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';
    db_connect();
    $_GET['function']();
} else {
    $ret = Array("error" => "missing function");
    echo json_encode($ret);
}

/**
 * Proxy download the installment plan for payolution
 *
 * Require:
 * $_GET["months"]
 * $_GET["url"]
 * $GLOBALS["payolution_config"]["installment_plan_username"]
 * $GLOBALS["payolution_config"]["installment_plan_password"]
 */
function payolution_installment_plan() {

    $months = ( empty($_GET["months"]) ) ? "X" : $_GET["months"];
    $url = ( empty($_GET["url"]) ) ? die() : urldecode($_GET["url"]);

    $username = $GLOBALS["payolution_config"]["installment_plan_username"];
    $password = $GLOBALS["payolution_config"]["installment_plan_password"];

    $filename = "/Greenpanda_Ratenplan_".$months."_monate.pdf";
    headerFunctionBridge('Content-Type: application/octet-stream');
    headerFunctionBridge('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL,$filename);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
        echo $data;
        return strlen($data);
    });
    curl_exec($ch);
    curl_close($ch);
}


function check_shipment() {
    $query             = "SELECT *
				  FROM shop_country
				  WHERE to_delete = 0
				  	AND company = '" . $_POST['company'] . "'
				  	AND shop_code = '" . $_POST['shop_code'] . "'
				  	AND language_code = '" . $_POST['shop_language_code'] . "'
				  	AND country_code = '" . $_POST['country'] . "'
				  	AND ship_to=1";
    $result            = mysqli_query($GLOBALS['mysql_con'], $query);
    $shipment_possible = mysqli_num_rows($result);
    $ret               = Array("shipment_possible" => $shipment_possible);
    echo json_encode($ret);
}

function customize_delete_pic() {
    $query             = "SELECT * FROM shop_user_basket_customize WHERE id = '" . $_GET['action_id'] . "'";
    $result            = mysqli_query($GLOBALS['mysql_con'], $query);
    if(mysqli_num_rows($result)>0){
        $res=mysqli_fetch_array($result);
        $file = "../../../userdata/dcshop/customize/" . $res["value"];
        if (file_exists($file)) {
            @unlink($file);
            $query             = "UPDATE shop_user_basket_customize SET value='' WHERE id = '" . $_GET['action_id'] . "'";
            mysqli_query($GLOBALS['mysql_con'], $query);
        }
        echo '<div class="label"><label for="inputcustomize_'.$res["id"].'">'.$res["field_name"].'</label></div>
				<div class="input input-group rating_input_file">
					<span class="input-group-btn">
						<span class="btn btn-file">BILD AUSWÄHLEN
						<input type="file" name="inputcustomize_1_'.$res["item_customization_id"].'" id="inputcustomize_1_'.$res["item_customization_id"].'">
						</span>
					</span>
					<input type="text" class="form-control" readonly>
				</div>';
    }
}

?>