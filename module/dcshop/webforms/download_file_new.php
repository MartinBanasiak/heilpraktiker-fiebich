<?php
ini_set('display_errors', '0');
//Vendor Autoloader
include(rtrim(dirname(dirname(dirname(__DIR__))),'/') . '/vendor/autoload.php');
//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(dirname(__DIR__))),'/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}
// Konfigurationsdatei laden
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . 'dc/dc-server.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';
$shopConfigPath = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $_GET['shop_code'] . 'config.php';
if(file_exists($shopConfigPath) && is_file($shopConfigPath) && is_readable($shopConfigPath))
{
    require_once $shopConfigPath;
}

// Verbindung mit Datenbank herstellen
db_connect();

// Variablen schützen
$_GET = secure_array($_GET);
$_POST = secure_array($_POST);

// Datei über Parameter aus Datenbank lesen und ausgeben
if ($_REQUEST["webform"] == "TxtModAttac") {
    $query = "SELECT attachment_" . $_GET['attachment_no'] . " AS filename FROM shop_text_module WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_documents"];
}

if ($_REQUEST["webform"] == "ItemFile") {
    $query = "SELECT * FROM shop_item_file WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    switch ($_GET['type']) {
        case "0":
            $uploadDir = $GLOBALS["shop_setup"]["uploaddir"];
            break;
        case "1":
            $uploadDir = $GLOBALS["shop_setup"]["uploaddir_documents"];
            break;
        case "3":
            $uploadDir = $GLOBALS["shop_setup"]["uploaddir_videos"];
            break;
    }
}

if ($_REQUEST["webform"] == "AttributeIcon") {
    $query = "SELECT icon AS filename FROM shop_attribute_option WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_filter_icon"];
}

if ($_REQUEST["webform"] == "CouponBackground") {
    $query = "SELECT digital_coupon_background_".$_GET["background_no"]." AS filename FROM shop_language WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_dc"];
}

if ($_REQUEST["webform"] == "CategoryImage") {
    $query = "SELECT ".$_GET["type"]." AS filename FROM shop_category WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_" . $_GET["type"]];
}

if ($_REQUEST["webform"] == "Placeholder") {
    $query = "SELECT item_placeholder_image AS filename FROM shop_language WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir"];
}

if ($_REQUEST["webform"] == "CampaignImages") {
    $query = "SELECT " . $_GET["field"] . " AS filename FROM shop_campaign_header WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . '/original/';
}

if ($_REQUEST["webform"] == "ShippingOption") {
    $query = "SELECT logo AS filename FROM shop_shipping_option WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_order_icons"];
}

if ($_REQUEST["webform"] == "PaymentOption") {
    $query = "SELECT logo AS filename FROM shop_payment_option WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_order_icons"];
}

if ($_REQUEST["webform"] == "SalesPerson") {
    $query = "SELECT image AS filename FROM shop_salesperson WHERE id = '" . $_GET["file"] . "' LIMIT 1";
    $uploadDir = $GLOBALS["shop_setup"]["uploaddir_salesperson_images"];
}

$result = @mysqli_query($GLOBALS['mysql_con'],$query);
if(@mysqli_num_rows($result)==1) {
	$input_file = @mysqli_fetch_assoc($result);
	$file = "../../.." . $uploadDir . $input_file["filename"];
	if (strpos($input_file["filename"],'_')) {
		$input_file["filename"] = substr($input_file["filename"],strpos($input_file["filename"],'_')+1);
	}

	if(file_exists($file)) {
	 headerFunctionBridge("Content-Type: application/force-download");
	 headerFunctionBridge("Content-Disposition: attachment; filename=" . utf8_decode(urldecode($input_file["filename"])));
	 headerFunctionBridge("Content-Length:" . filesize($file));
		readfile($file);
	}
}
?>