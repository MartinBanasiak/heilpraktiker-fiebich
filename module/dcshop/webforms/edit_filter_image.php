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

// Variablen schützen
$_GET = secure_array($_GET);
$_POST = secure_array($_POST);

// Passwort prüfen
if(($_GET["pass"] != $GLOBALS["shop_setup"]["shop_password"]) && ($_GET['pass'] != md5($GLOBALS['shop_setup']['shop_password']))) {
	echo "Webshop Zugangsdaten sind nicht korrekt";
	exit();
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Filtericon bearbeiten</title>
<script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/dc/common/common.js"></script>
<link type="text/css" rel="stylesheet" href="webforms.css" />
</head>
<body onload="toggleOff('box3');MM_preloadImages('img/load.jpg');">
<?
// Datei anhand GET-Parameter aus Datenbank laden
$countquery = "SELECT id, icon FROM shop_attribute_option WHERE company = '" . $_GET["company"] . "' AND code = '" . $_GET["code"] . "' AND attribute_code = '" . $_GET["attribute_code"] . "'";
$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
if(@mysqli_num_rows($result)==1) {
	$option = @mysqli_fetch_assoc($result);
	$file_message = "<br /><br />Bitte wählen Sie ein Bild im jpg-,png- oder gif-Format\n";
	if(isset($_FILES['input_file'])) {
		//if((preg_match('/\.JPG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.JPG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.PNG$/',strtoupper($_FILES['input_file']['name']))) | (preg_match('/\.GIF$/',strtoupper($_FILES['input_file']['name'])))) {
        if(is_valid_image($_FILES['input_file'])){
			foreach($GLOBALS["shop_setup"]["image_config"] as $image) {
				$oldfile = "../../.." . $image["path"] . $option["icon"];
				if(file_exists($oldfile)) {
					@unlink($oldfile);
				}
			}
			$option["icon"] = $option["id"] . "_" . filename_validation($_FILES['input_file']['name']);
			$uploadfile = "../../.." . $GLOBALS["shop_setup"]["uploaddir"] . $option["icon"];
			if(file_exists($uploadfile)) {
				@unlink($uploadfile);
			}
			if(move_uploaded_file($_FILES['input_file']['tmp_name'],$uploadfile)) {
				$newuploadfile = "../../../" . $GLOBALS["shop_setup"]["uploaddir_filter_icon"] ."/". $option["icon"];
				image_resize($uploadfile,$newuploadfile,$GLOBALS["shop_setup"]["filter_icon_maxwidth"],$GLOBALS["shop_setup"]["filter_icon_maxheight"]);
				@unlink($uploadfile);
				$query = "UPDATE shop_attribute_option SET icon = '" . $option["icon"] . "' WHERE id = " . $option["id"];
				if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
					echo "Fehler in Webform 'edit_filter_image'. query: " . $query . "\n";
					exit();
				}
			} else {
				echo "Fehler in Webform 'edit_filter_image'. query: " . $countquery . "\n";
				exit();
			}
		} else {
			$language["item_placeholder_image"] = '';
		}
	}

?>
<div id="box1">
<?
if ($option["icon"] <> '') {
	// Bild anzeigen
	echo "<img style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_filter_icon"] . $option["icon"] . "?id=" . rand(1,900) . "\" />\n";
} else {
	echo $file_message;
}
?>
</div>
<div id="box2">
  <form id="form_fileupload" name="form_fileupload" action="<?= $_SERVER["REQUEST_URI"] ?>" method="post" enctype="multipart/form-data">
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
Datei wird hochgeladen, bitte warten...
<br /><br />
<div class="load"><marquee><img src="img/load.jpg" width="100" height="20" alt="Laden..." /></marquee></div><br />
</div>
</body>
</html>
<?
} else {
	echo "Fehler in Webform 'edit_filter_image'. \nquery: " . $countquery . "\n";
	//echo "GET:".utf8_encode($_GET['code']);
}
echo mysqli_error($GLOBALS['mysql_con']);
?>