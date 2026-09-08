<?php
ini_set('display_errors', '0'); 
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('memory_limit', '128M');
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
headerFunctionBridge('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<? require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/version_comment.inc.php'; ?>


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
if($_GET["pass"] != $GLOBALS["shop_setup"]["shop_password"] && $_GET['pass'] != md5($GLOBALS['shop_setup']['shop_password'])) {
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
$title['de'] = "Dateianhang bearbeiten";
$title['en'] = "Edit attachment";
$file_message['de'] = "Bitte wählen Sie ein eine Datei aus\n";
$file_message['en'] = "Choose a file\n";
$file_message1['de'] = "Bitte wählen Sie eine Datei in beliebigem Format aus\n";
$file_message1['en'] = "Choose a file in any format\n";
$loading_message['de'] = "Datei wird hochgeladen.Bitte warten...";
$loading_message['en'] = "File is uploading. Please wait...";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$title[$language_code] ?></title>
<script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/dc/common/common.js"></script>
<? if($_GET[rtc]) { ?>
<link type="text/css" rel="stylesheet" href="webforms_rtc.css" />
<? } else { ?>
<link type="text/css" rel="stylesheet" href="webforms.css" />
<? } ?>
</head>
<body onload="toggleOff('box3');MM_preloadImages('img/load.jpg');">
<?
// Datei anhand GET-Parameter aus Datenbank laden
if((int)$_GET['attachment_no'] != 1 && (int)$_GET['attachment_no'] != 2) {
	$_GET['attachment_no'] = 1;
}
$countquery = "SELECT attachment_".$_GET['attachment_no'].",code FROM shop_text_module WHERE company = '" . $_GET["company"] . "' AND code = '" . $_GET["text_module_code"] . "'";
$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
if(@mysqli_num_rows($result)==1) {
	$input_file_loc = @mysqli_result($result,0,0);
	$input_file_code = @mysqli_result($result,0,1);

	$file_message = "<br /><br />".$file_message1[$language_code];
	if(isset($_FILES['input_file'])) {
		$oldfile = "../../.." . $GLOBALS["shop_setup"]["uploaddir_documents"] . $input_file_loc;
		if(file_exists($oldfile)) {
			@unlink($oldfile);
		}
		$input_file_loc = filename_validation($_FILES['input_file']['name']);
		$uploadfile = "../../.." . $GLOBALS["shop_setup"]["uploaddir_documents"] . $input_file_loc;
		if(file_exists($uploadfile)) {
			@unlink($uploadfile);
		}
		if(move_uploaded_file($_FILES['input_file']['tmp_name'],$uploadfile)) {
			$query = "UPDATE shop_text_module SET attachment_".$_GET['attachment_no']." = '" . $input_file_loc . "' WHERE code = '" . $_GET['text_module_code']."'";
			if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
				echo "Fehler in Webform 'edit_attachment'. query: " . $query . "\n";
				exit();
			}
		} else {
			echo "Fehler in Webform 'edit_attachment'. query: " . $countquery . "\n";
			exit();
		}
	}


?>
<div id="box1">
<?
if ($input_file_loc <> '') {
	$button_class = get_button_file_typ($input_file_loc);
	if (strpos($input_file_loc,'_')) {
		$input_file_loc = substr($input_file_loc,strpos($input_file_loc,'_')+1);
	}
	echo "<br /><br /><br /><br /><ul class=\"buttonlist\"><li><a class=\"" . $button_class . "\" href=\"/module/dcshop/webforms/download_file.php?file=" . $input_file_loc ."\">" . urldecode($input_file_loc) . "</a></li></ul>\n";
	
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
<?=$loading_message[$language_code]?>
<br /><br />
<div class="load"><img src="img/load.jpg" width="100" height="20" alt="Laden..." /></div><br />
</div>
</body>
</html>
<?
} else {
	echo "Fehler in Webform 'edit_attachment'. \nquery: " . $countquery . "\n";
}
echo mysqli_error($GLOBALS['mysql_con']);
?>