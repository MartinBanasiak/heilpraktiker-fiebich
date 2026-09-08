<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SERVER['HTTP_REFERER'])) {
        if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) === false) {
            echo json_encode("1");
            http_response_code(401);
            exit();
        }
    } else {
        echo json_encode("2");
        http_response_code(401);
        exit();
    }
} else {
    echo json_encode("3");
    http_response_code(401);
    exit();
}

$root_dir = rtrim(dirname(__DIR__, 2));
$_SERVER["DOCUMENT_ROOT"] = $root_dir;

include($root_dir . '/vendor/autoload.php');

$envDir = $root_dir . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$includes = array(
    "/dc/dc-server.config.php",
    "/module/dcshop/shop.config.php",
    "/dc/common/common_functions.inc.php",
);

foreach ($includes as $include) {
    require_once($root_dir . $include);
}
db_connect();

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);


if ($_COOKIE["sid" . $input["siteCode"]] === "" || $_COOKIE["sid" . $input["siteCode"]] === NULL || !isset($_COOKIE["sid" . $input["siteCode"]]) || empty($_COOKIE["sid" . $input["siteCode"]])) {
    http_response_code(401);
    exit();
}
$id = bin2hex(random_bytes(16));

$query = "
    INSERT INTO
        dc_cookie
    SET 
        id = '$id',
        sid = '{$_COOKIE["sid" . $input["siteCode"]]}',
        action = '{$input['action']}',
        state = '" . json_encode($input['state']). "',
        device = '{$input['device']}',
        site_code = '{$input['siteCode']}'        
";
@mysqli_query($GLOBALS['mysql_con'],$query);

$queryDel = "
    DELETE FROM dc_cookie
    WHERE datetime <= DATE_SUB(NOW(), INTERVAL 18 MONTH)
";

@mysqli_query($GLOBALS['mysql_con'],$queryDel);