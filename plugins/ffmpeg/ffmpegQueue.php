<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 18.07.2016
 * Time: 11:28
 */

ini_set('display_errors', '0');
//Vendor Autoloader
include(rtrim(dirname(dirname(__DIR__)),'/') . '/vendor/autoload.php');
//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)),'/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

// Konfigurationsdatei laden
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/common/shop_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'module/dcshop/shop.config.php';


// Verbindung mit Datenbank herstellen
db_connect();

$fileId = (int)filter_var($_GET["id"],FILTER_SANITIZE_NUMBER_INT);
if (is_int($fileId)) {
    $query = "SELECT mp4,ogg,webm FROM shop_item_file WHERE id=?";

    if ($stmt = mysqli_prepare($GLOBALS["mysql_con"],$query)) {
        mysqli_stmt_bind_param($stmt, "i", $fileId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $shopItemFile["mp4"], $shopItemFile["ogg"], $shopItemFile["webm"]);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        $jsonResponse["progress"]["mp4"] = $shopItemFile["mp4"];
        $jsonResponse["progress"]["ogg"] = $shopItemFile["ogg"];
        $jsonResponse["progress"]["webm"] = $shopItemFile["webm"];
        echo json_encode($jsonResponse);
    }
}