<?php
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
//load environment variables
$dotenv = new \Dotenv\Dotenv($rootDir.'/config/');
$dotenv->load();

require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/shop_functions.inc.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/item_functions.inc.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/navconnect_functions.inc.php';

// Verbindung mit Datenbank herstellen
db_connect();

$item_id = (int)filter_var($_GET['item_id'],FILTER_SANITIZE_NUMBER_INT);

$query = "
    SELECT * FROM shop_item WHERE id = '".$item_id."'";


$result = mysqli_query($GLOBALS["mysql_con"],$query);

if (mysqli_num_rows($result) == 1) {

$item = mysqli_fetch_array($result);

$template = get_text_module($item["company"],$item["marketplace_ebay_item_template"]);
$shop = get_shop($item["company"],$item["shop_code"]);
$shop_language = get_shop_language($item["company"],$item["shop_code"],$item["language_code"]);
$cancellation_text = get_text_module($item["company"],$shop_language["marketplace_cancellation_text"]);
$descriptionText = replace_item_placeholders($template,$item["company"],$item["shop_code"],$item["language_code"],
    $item["item_no"],$item["item_no"],$item["variant_type"],'',$GLOBALS["shop_setup"]["marketplace_url"],$GLOBALS["shop_setup"]["image_config"],4,500000,$cancellation_text);

echo $descriptionText;
}