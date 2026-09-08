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


<!-- Aufruf <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?> -->

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

// Variablen schützen
$_GET = secure_array($_GET);
$_POST = secure_array($_POST);

// Passwort prüfen
if($_GET["pass"] != $GLOBALS["shop_setup"]["shop_password"]) {
	echo "Webshop Zugangsdaten sind nicht korrekt";
	exit();
}
//deutscher Nav-Client
if($_GET['nav_lang'] == '1031')
{
	$language_code = 'de';
}
//andere Nav-Clients
else 
{
	$language_code = 'en';
}
//Sprachvariablen für Webform
$title['de'] = "Kategoredatei bearbeiten";
$title['en'] = "Edit category file";
$file_message['de'] = "Bitte wählen Sie ein Bild im JPG-, PNG- oder GIF-Format aus\n";
$file_message['en'] = "Choose a picture in JPG-, PNG- or GIF-Format\n";
$loading_message['de'] = "Datei wird hochgeladen.Bitte warten...";
$loading_message['en'] = "File is uploading. Please wait...";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$title[$language_code] ?></title>
<script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/dc/common/common.js"></script>
<? if(!$_GET[rtc]) { ?>
<link type="text/css" rel="stylesheet" href="webforms.css" />
<? } else { ?>
<link type="text/css" rel="stylesheet" href="webforms_rtc.css" />
<? } ?>
</head>
<body onload="toggleOff('box3');MM_preloadImages('img/load.jpg');">
<?
// Kategorie anhand GET-Parameter auslesen
$countquery = "SELECT id,category_picture,category_icon FROM shop_category WHERE company = '" . $_GET["company"] . "' AND shop_code = '" . $_GET["shop_code"] . "' AND language_code = '" . $_GET["language_code"] . "' AND line_no = '" . $_GET["line_no"] . "'";
$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
if(@mysqli_num_rows($result)==1) {
	$category = @mysqli_fetch_array($result);
	$file_message = "<br /><br />".$file_message[$language_code];
	switch($_GET["type"]) {
		case 0:
			$content_field = "category_picture";
			$file_path = $GLOBALS["shop_setup"]["uploaddir_category_picture"];
			$width = "category_picture_maxwidth";
			$height = "category_picture_maxheight";
		break;
		case 1:
			$content_field = "category_icon";
			$file_path = $GLOBALS["shop_setup"]["uploaddir_category_icon"];
			$width = "category_icon_maxwidth";
			$height = "category_icon_maxheight";
		break;
	}
	if(isset($_FILES['input_file'])) {
		//if((preg_match('/\.JPG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.JPG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.PNG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.GIF$/',strtoupper($_FILES['input_file']['name'])))) {
        if(is_valid_image($_FILES['input_file'])){
            $filename = $category["id"] . "_" . $_GET["type"] . "_" . filename_validation($_FILES['input_file']['name']);
			$uploadfile = "../../.." . $GLOBALS["shop_setup"]["uploaddir"] . $filename;
			@unlink($uploadfile);
			if(move_uploaded_file($_FILES['input_file']['tmp_name'],$uploadfile)) {
				$newuploadfile = "../../.." . $file_path . $filename;
				image_resize($uploadfile,$newuploadfile,$GLOBALS["shop_setup"][$width],$GLOBALS["shop_setup"][$height]);
				@unlink($uploadfile);
				$query = "UPDATE shop_category SET " . $content_field . " = '" . $filename . "' WHERE id = " . $category["id"];
				if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
					echo "Fehler in Webform 'edit_category_file'. query: " . $query . "\n";
					exit();
				}
				$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
				$category = @mysqli_fetch_array($result);
			} else {
				echo "Fehler in Webform 'edit_category_file'. query: " . $countquery . "\n";
				exit();
			}
		} else {
			$category[$content_field] = "";
		}
	}
?>

<div id="box1">
<?
if ($category[$content_field] <> '') {
	$file = $file_path . $category[$content_field];
	if (file_exists("../../.." . $file)) {
		echo "<img style=\"max-width: 100%; max-height: 100%; \" src=\"" . $file . "?id=" . rand(1,9000) . "\" />\n";
	}
} else {
	echo $file_message;
}
?>
</div>

<div id="box2">
  <form id="form_fileupload" name="form_fileupload" action="<?= $_SERVER["REQUEST_URI"] ?>&send=true" method="post" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td><input class="file" type="file" name="input_file" id="input_file" /></td>
        <td><input onmouseup="toggleOn('box3');" class="button" type="submit" name="button" id="button" value="Upload" /></td>
      </tr>
    </table>
  </form>
</div>

<div id="box3">
<br /><br /><br /><br />
<?=$loading_message[$language_code]?>
<br /><br />
<div class="load"><marquee><img src="img/load.jpg" width="100" height="20" alt="Laden..." /></marquee></div><br />
</div>

</body>
</html>
<?
} else {
	echo "Fehler in Webform 'edit_category_file'. \nquery: " . $countquery . "\n";
}
echo mysqli_error($GLOBALS['mysql_con']);
?>