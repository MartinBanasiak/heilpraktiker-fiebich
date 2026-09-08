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
<title>Kommentar bearbeiten</title>
<script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
<? if($_GET["rtc"]=='TRUE') { ?>
<link type="text/css" rel="stylesheet" href="webforms_rtc.css" />
<? } else { ?>
<link type="text/css" rel="stylesheet" href="webforms.css" />
<? } ?>
<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/ckeditor/ckeditor.js"></script>
</head>
<body>
<?
// Beschreibung anhand der GET-Parameter ermitteln
$countquery = "SELECT * FROM shop_item_comments WHERE company = '" . $_GET["company"] . "' AND shop_code = '" . $_GET["shop_code"] . "' AND language_code = '" . $_GET["language_code"] . "' AND item_no = '" . $_GET["item_no"] . "'  AND line_no = '" . $_GET["line_no"] . "' LIMIT 1";
$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
if(@mysqli_num_rows($result)==1) {
	$input_description = @mysqli_fetch_assoc($result);
	// Änderungen speichern
	if($_GET["send"]) {
		$query = "UPDATE shop_item_comments SET comment = '" . $_POST["textcontent"] . "' WHERE id = '" . $input_description["id"] . "'";
		if (!@mysqli_query($GLOBALS['mysql_con'],$query)) {
			echo "Fehler in Webform 'edit_comment'. query: " . $query . "\n";
			exit();
		}
		$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
		$input_description = @mysqli_fetch_assoc($result);
	}
?>
<form id="editor" name="editor" action="<?= $_SERVER["REQUEST_URI"] ?>&send=TRUE" method="post">

<? if(empty($GLOBALS["shop_setup"]["fck_style_dir"])) { $GLOBALS["shop_setup"]["fck_style_dir"] = 'layout/admin/ckeditor.css'; }
if(empty($GLOBALS["shop_setup"]["fck_xml_dir"])) { $GLOBALS["shop_setup"]["fck_xml_dir"] = 'layout/admin/ck_styles.js'; }
show_ck_editor($input_description["comment"],"nav",$GLOBALS["shop_setup"]["fck_style_dir"],$GLOBALS["shop_setup"]["fck_xml_dir"]); ?>

</form>

</body>
</html>
<?
} else {
	echo "Fehler in Webform 'edit_comment'. \nquery: " . $countquery . "\n";
}
echo mysqli_error($GLOBALS['mysql_con']);
?>