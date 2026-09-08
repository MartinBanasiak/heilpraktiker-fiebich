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
if($_GET["pass"] != $GLOBALS["shop_setup"]["shop_password"]) {
	echo "Webshop Zugangsdaten sind nicht korrekt";
	exit();
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kampagnenbilder bearbeiten</title>
<script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/dc/common/common.js"></script>
<link type="text/css" rel="stylesheet" href="webforms.css" />
</head>
<body onload="toggleOff('box3');MM_preloadImages('img/load.jpg');">
<?
// Datei anhand GET-Parameter aus Datenbank laden

$field = filter_var($_GET['field'],FILTER_SANITIZE_STRING);
$company = filter_var($_GET['company'],FILTER_SANITIZE_STRING);
$code = filter_var($_GET['code'],FILTER_SANITIZE_STRING);

$mysqlField = mysqli_real_escape_string($GLOBALS['mysql_con'],$field);
$mysqlCompany = mysqli_real_escape_string($GLOBALS['mysql_con'],$company);
$mysqlCode = mysqli_real_escape_string($GLOBALS['mysql_con'],$code);

$countquery = <<<SQL
	SELECT
		`id`,
		`{$mysqlField}`
	FROM
		`dcshop`.`shop_campaign_header`
	WHERE
			`company` = '{$mysqlCompany}'
		AND `code` = '{$mysqlCode}'
SQL;
$result = @mysqli_query($GLOBALS['mysql_con'],$countquery);
$numRows = @mysqli_num_rows($result);
$campaignHeader = [];
if ($numRows > 0) {
	$campaignHeader = @mysqli_fetch_assoc($result);
}
/*
echo $countquery;
var_dump($campaignHeader);
die();*/
if($campaignHeader['id'] > 0) {
        $file_message = "<br /><br />Bitte wählen Sie ein Bild im jpg-,png- oder gif-Format\n";
        if (isset($_FILES['input_file'])) {

            $maxWidth = null;
            $maxHeight = null;
            switch ($field) {
                case 'icon_condition_items':
                    $maxWidth = $GLOBALS["shop_setup"]["campaign_icon_condition_items_maxwidth"];
                    $maxHeight = $GLOBALS["shop_setup"]["campaign_icon_condition_items_maxheight"];
                    break;
                case 'banner_condition_items':
                    $maxWidth = $GLOBALS["shop_setup"]["campaign_banner_condition_items_maxwidth"];
                    $maxHeight = $GLOBALS["shop_setup"]["campaign_banner_condition_items_maxheight"];
                    break;
                case 'icon_action_items':
                    $maxWidth = $GLOBALS["shop_setup"]["campaign_icon_action_items_maxwidth"];
                    $maxHeight = $GLOBALS["shop_setup"]["campaign_icon_action_items_maxheight"];
                    break;
                case 'banner_action_items':
                    $maxWidth = $GLOBALS["shop_setup"]["campaign_banner_action_items_maxwidth"];
                    $maxHeight = $GLOBALS["shop_setup"]["campaign_banner_action_items_maxheight"];
                    break;
                case 'banner_basket':
                    $maxWidth = $GLOBALS["shop_setup"]["campaign_banner_basket_maxwidth"];
                    $maxHeight = $GLOBALS["shop_setup"]["campaign_banner_basket_maxheight"];
                    break;
                default:
                    $maxWidth = 1;
                    $maxHeight = 1;
            }

    /*        if ((preg_match('/\.JPG$/', strtoupper($_FILES['input_file']['name']))) | (preg_match(
                    '/\.JPG$/',
                    strtoupper(
                        $_FILES['input_file']['name']
                    )
                )) | (preg_match('/\.PNG$/', strtoupper($_FILES['input_file']['name']))) | (preg_match(
                    '/\.GIF$/',
                    strtoupper(
                        $_FILES['input_file']['name']
                    )
                ))
            )*/
            if(is_valid_image($_FILES['input_file'])){
                $oldfile = "../../.." . $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . $campaignHeader[$field];
                if (file_exists($oldfile)) {
                    @unlink($oldfile);
                }

                $campaignHeader[$field] = $campaignHeader["id"] . "_" . filename_validation($_FILES['input_file']['name']);
                $uploadfile = rtrim(dirname(dirname(dirname(__DIR__))),'/'). $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . '/original/' . $campaignHeader[$field];
                /*if (file_exists($uploadfile)) {
                    die('one');
                    @unlink($uploadfile);
                }*/
                if (move_uploaded_file($_FILES['input_file']['tmp_name'], $uploadfile)) {
                    //die('two');
                    //echo "file moved to $uploadfile";
                    $newuploadfile = rtrim(dirname(dirname(dirname(__DIR__))),'/') . $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . $campaignHeader[$field];
                    image_resize($uploadfile, $newuploadfile, $maxWidth, $maxHeight);
                    /*if(file_exists($uploadfile)) {
                        @unlink($uploadfile);
                    }*/
                    $query = "UPDATE shop_campaign_header SET $field = '" . $campaignHeader[$field] . "' WHERE id = " . $campaignHeader["id"];
                    if (!@mysqli_query($GLOBALS['mysql_con'], $query)) {
                        echo "Fehler in Webform 'edit_filter_image'. query: " . $query . "\n";
                        exit();
                    }
                } else {
                    echo "Fehler in Webform 'edit_filter_image'. cquery: " . $countquery . "\n";
                    exit();
                }
            } else {
                $language["item_placeholder_image"] = '';
            }
        }

        ?>
        <div id="box1">
    <?

if ($campaignHeader[$field] != '' && $campaignHeader[$field] != 0) {
	// Bild anzeigen
	echo "<img style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . $campaignHeader[$field] . "?id=" . rand(1, 900) . "\" />\n";
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
	echo "Fehler in Webform 'edit_filter_image'. \nzquery: " . $countquery . "\n";
	//echo "GET:".utf8_encode($_GET['code']);
}
echo mysqli_error($GLOBALS['mysql_con']);
?>