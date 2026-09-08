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

<?php
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/version_comment.inc.php';
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
if((isset($_GET["item"])) && (isset($_GET["video"]))) {
  $query = "SELECT * FROM shop_item WHERE id = '" . $_GET["item"]."'";
  $result = @mysqli_query($GLOBALS['mysql_con'],$query);
  if(@mysqli_num_rows($result)==1) {
  	$item = @mysqli_fetch_array($result);
	$query = "SELECT * FROM shop_item_file WHERE id = '" . $_GET["video"]."'";
	$result = @mysqli_query($GLOBALS['mysql_con'],$query);
	if(@mysqli_num_rows($result)==1) {
		$video = @mysqli_fetch_array($result);
		if(file_exists(rtrim(dirname(dirname(dirname(__DIR__))),'/') . $GLOBALS["shop_setup"]["uploaddir_videos"] . $video["filename"])) {
			$flash_file = $GLOBALS["shop_setup"]["uploaddir_videos"] . $video["filename"];
			$flash_width = "568";
			$flash_height = "375";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $item["description"] ?> - <?= $video["description"] ?></title>
<link href="<?= $GLOBALS['projectRoot'] ?>/layout/frontend/shop/css/image_popup.css" rel="stylesheet" type="text/css" />
</head>
<body>
<style type="text/css">
body {
font-family: Arial, sans-serif;
}

div#header {
display: block;
width: 100%;
height: 105px;
}

div#logo {
float: left;
background-image: url(/layout/frontend/b2b/img/b2b_logo_deu.jpg);
height: 105px;
width: 230px;
}

div#close_button {
float: right;
height: 65px;
padding-top: 40px;
}

div#item_title {
width: 100%;
background-color: #035297;
color: #fff;
text-align: center;
padding: 10px 0;
}

div#main {
width: 100%;
text-align: center;
}
</style>
<div id="header"> <div id="logo"></div><div id="close_button"> <a class="button_close" href="javascript:window.close()">Fenster schlie&szlig;en</a> </div></div>
<div id="item_title">
  <h3><?= $item["description"] ?></h3><?= $video["description"] ?>
</div>
<div id="main">
<? require(rtrim(dirname(dirname(dirname(__DIR__))),'/') . "/plugins/flashplayer/flashplayer.inc.php") ?>
</div>
</body>
</html>
<?
		}
	}
  }
}
?>