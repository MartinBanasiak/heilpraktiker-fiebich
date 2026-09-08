<?php
ini_set('display_errors', '0');
//Vendor Autoloader

$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
include $rootDir . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
//Load environment variables from config if exists
$envDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}
$GLOBALS['projectRoot'] = '/' . rtrim(getenv('PROJECT_ROOT'),'/\\');
header('Content-Type: text/html; charset=UTF-8'); ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

<?php require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/version_comment.inc.php'; ?>
<?php
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

// Artikel auslesen
if(isset($_GET["item"])) {
  $query = "SELECT * FROM shop_item WHERE id = '" . $_GET["item"]."'";
  $result = @mysqli_query($GLOBALS['mysql_con'],$query);
  if(@mysqli_num_rows($result)==1) {
  	$item = @mysqli_fetch_array($result);
// Hauptbild auslesen
	if(isset($_GET["image"])) {
  		$query = "SELECT * FROM shop_item_file WHERE id = '" . $_GET["image"]."'";
  		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		if(@mysqli_num_rows($result)==1) {
  			$image = @mysqli_fetch_array($result);
			if(!file_exists("../../" . $image_config[4]["path"] . "/" . $image["filename"])) {
				$image["filename"] = "noimage.jpg";
			}
		} else {
			$image["filename"] = "noimage.jpg";
		}
	}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $item["description"] ?> </title>
<link href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/shop/css/image_popup.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header"> <a class="button_close" href="javascript:window.close()">Fenster schlie&szlig;en</a> </div>
<div id="item_title">
  <h3><?= $item["description"] ?></h3><?= $image["description"] ?>
</div>
<div id="main">
  <div id="item_main_image"><img border="0" name="item_main_picture" src="<?= $image_config[4]["path"] . "/" . $image["filename"] ?>" alt="<? $image["description"] ?>" /></div>
</div>
<div id="footer">
  <?
$parent_item = get_item_variant_parent($item);
$query = "SELECT shop_item_file.* FROM shop_item_file LEFT JOIN shop_item ON shop_item.id = shop_item_file.shop_item_id LEFT JOIN shop_item AS parent_shop_item ON parent_shop_item.id = shop_item_file.shop_item_id WHERE shop_item_file.type = '0' AND shop_item_file.filename <> '' AND ((shop_item.item_no = '" . $item["item_no"] . "' AND (shop_item.main_language_id = '" . $item["main_language_id"] . "' OR shop_item_file.all_language_codes = TRUE)) OR (parent_shop_item.item_no = '" . $parent_item["item_no"] . "' AND (parent_shop_item.main_language_id = '" . $parent_item["main_language_id"] . "' OR shop_item_file.all_language_codes = TRUE))) ORDER BY line_no";
$result = @mysqli_query($GLOBALS['mysql_con'],$query);
if(@mysqli_num_rows($result) > 0) {
	echo "<div class=\"item_images\">";
	while($tumb_image = @mysqli_fetch_array($result)) {
		if(!file_exists("../../" . $image_config[1]["path"] . "/" . $tumb_image["filename"])) {
			$tumb_image["filename"] = "noimage.jpg";
		}
		$active_text = ($tumb_image["id"]==$image["id"]) ? " class=\"active\"" : "";
		echo "<a" . $active_text . " href=\"?item=" . $item["id"] . "&image=" . $tumb_image["id"] . "\"><img border=\"0\" src=\"" . $image_config[1]["path"] . "/" . $tumb_image["filename"] . "\" alt=\"" . $tumb_image["description"] . "\" /></a>\n";
	}
	echo "</div>";
}
?>
</div>
</body>
</html>
<?
  }
}
?>