<?php
ini_set('display_errors', '0');
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
//Vendor Autoloader
include $rootDir . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
//Load environment variables from config if exists
$envDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
if (is_dir($envDir)) {
	$dotenv = new \Dotenv\Dotenv($envDir);
	$dotenv->load();
}
// Konfigurationsdatei laden

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
$query = "SELECT * FROM shop_item_file WHERE id = '" . $_GET["file"] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'],$query);
if(@mysqli_num_rows($result)==1) {
	$input_file = @mysqli_fetch_assoc($result);
	$file = "../../.." . $GLOBALS["shop_setup"]["uploaddir_documents"] . $input_file["filename"];
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