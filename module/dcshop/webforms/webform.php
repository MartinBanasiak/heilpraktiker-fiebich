<?php
ini_set('display_errors', 'On');
ini_set('upload_max_filesize', '2048M');
ini_set('post_max_size', '2048M');
ini_set('memory_limit', '2048M');

$logPath = __DIR__ . DIRECTORY_SEPARATOR . '../../../logs/logfile_upload.txt';
$logPrefix = date('Y-m-d H:i:s') . ' WEBFORM ';

$logData = $logPrefix . ' - Webform request received with GET: ' . print_r($_GET,1) . ' | POST : ' . print_r($_POST,1) . '.';
file_put_contents($logPath,$logData,FILE_APPEND);

//
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
include $rootDir . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
//Load environment variables from config if exists
$envDir = $rootDir . DIRECTORY_SEPARATOR . 'config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

$GLOBALS['projectRoot'] = rtrim(getenv('PROJECT_ROOT'),'/\\');

$getArgs = array(
    'pass'                      =>  FILTER_SANITIZE_STRING,
    'nav_lang'                  =>  FILTER_SANITIZE_NUMBER_INT,
    'ajax'                      =>  FILTER_SANITIZE_NUMBER_INT,
    'shop_code'                 =>  FILTER_SANITIZE_STRING,
    'language_code'             =>  FILTER_SANITIZE_STRING,
    'company'                   =>  FILTER_SANITIZE_STRING,
    'code'                      =>  FILTER_SANITIZE_STRING,
    'send'                      =>  FILTER_SANITIZE_STRING,
    'item_no'                   =>  FILTER_SANITIZE_STRING,
    'line_no'                   =>  FILTER_SANITIZE_NUMBER_INT,
    'type'                      =>  FILTER_SANITIZE_STRING,
    'multiupload'               =>  FILTER_SANITIZE_NUMBER_INT,
    'attachment_no'             =>  FILTER_SANITIZE_NUMBER_INT,
    'text_module_code'          =>  FILTER_SANITIZE_STRING,
    'attribute_code'            =>  FILTER_SANITIZE_STRING,
    'no'                        =>  FILTER_SANITIZE_NUMBER_INT,
    'field'                     =>  FILTER_SANITIZE_STRING,
    'webform'                   =>  FILTER_SANITIZE_STRING,
    'webformtype'               =>  FILTER_SANITIZE_STRING,
    'delete_file'               =>  FILTER_SANITIZE_NUMBER_INT,
    'delete_file_path'          =>  FILTER_SANITIZE_STRING,
    'textcontent'               =>  FILTER_UNSAFE_RAW,
    'initialLineNo'             =>  FILTER_SANITIZE_NUMBER_INT,
    'sales_person_code'         =>  FILTER_SANITIZE_STRING,
    'legacy'                    =>  FILTER_SANITIZE_NUMBER_INT,
    'shipping_group_code'       =>  FILTER_SANITIZE_STRING,
);

$get = array();
foreach ($getArgs as $arg => $filter) {
    if (array_key_exists($arg,$_GET)) {
        $get[$arg] = filter_var($_GET[$arg], $filter);
    }
}

$postArgs = array(
    'pass'                      =>  FILTER_SANITIZE_STRING,
    'nav_lang'                  =>  FILTER_SANITIZE_NUMBER_INT,
    'ajax'                      =>  FILTER_SANITIZE_NUMBER_INT,
    'shop_code'                 =>  FILTER_SANITIZE_STRING,
    'language_code'             =>  FILTER_SANITIZE_STRING,
    'company'                   =>  FILTER_SANITIZE_STRING,
    'code'                      =>  FILTER_SANITIZE_STRING,
    'send'                      =>  FILTER_SANITIZE_STRING,
    'item_no'                   =>  FILTER_SANITIZE_STRING,
    'line_no'                   =>  FILTER_SANITIZE_NUMBER_INT,
    'type'                      =>  FILTER_SANITIZE_STRING,
    'multiupload'               =>  FILTER_SANITIZE_NUMBER_INT,
    'attachment_no'             =>  FILTER_SANITIZE_NUMBER_INT,
    'text_module_code'          =>  FILTER_SANITIZE_STRING,
    'attribute_code'            =>  FILTER_SANITIZE_STRING,
    'no'                        =>  FILTER_SANITIZE_NUMBER_INT,
    'field'                     =>  FILTER_SANITIZE_STRING,
    'webform'                   =>  FILTER_SANITIZE_STRING,
    'webformtype'               =>  FILTER_SANITIZE_STRING,
    'delete_file'               =>  FILTER_SANITIZE_NUMBER_INT,
    'delete_file_path'          =>  FILTER_SANITIZE_STRING,
    'textcontent'               =>  FILTER_UNSAFE_RAW,
    'initialLineNo'             =>  FILTER_SANITIZE_NUMBER_INT,
    'sales_person_code'          =>  FILTER_SANITIZE_STRING,
    'legacy'                    =>  FILTER_SANITIZE_NUMBER_INT,
    'shipping_group_code'       =>  FILTER_SANITIZE_STRING,
    'no_of_files'               => FILTER_SANITIZE_NUMBER_INT,
    'is_first'                  => FILTER_SANITIZE_NUMBER_INT,
);

$post = array();
foreach ($postArgs as $arg => $filter) {
    if (array_key_exists($arg,$_POST)) {
        $post[$arg] = filter_var($_POST[$arg], $filter);
    }
}

$requestArgs = array(
    'webform'                   =>  FILTER_SANITIZE_STRING,
    'webformtype'               =>  FILTER_SANITIZE_STRING
);

$request = array();
foreach ($requestArgs as $arg => $filter) {
    if (array_key_exists($arg,$_REQUEST)) {
        $request[$arg] = filter_var($_REQUEST[$arg], $filter);
    }
}


if ($get["ajax"] == 1) {
    headerFunctionBridge('Content-Type: application/json');
    //header( 'Content-Encoding: none; ' );//disable apache compressed
    //header('Content-Type: text/html; charset=UTF-8');
} else {
    headerFunctionBridge('Content-Type: text/html; charset=UTF-8');
}

if ($get["ajax"] != 1) {
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <? require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/version_comment.inc.php'; ?>


    <!-- Aufruf <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?> -->

    <?php
}

require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';
$shopConfigPath = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $_GET['shop_code'] . 'config.php';
if(file_exists($shopConfigPath) && is_file($shopConfigPath) && is_readable($shopConfigPath))
{
    require_once $shopConfigPath;
}


// Verbindung mit Datenbank herstellen
db_connect();

// Variablen schützen
//$get = secure_array($get);
//$post = secure_array($post);

// Passwort prüfen
if($get["pass"] != $GLOBALS["shop_setup"]["shop_password"]) {
	echo "Webshop Zugangsdaten sind nicht korrekt";
	exit();
}
//deutscher Nav-Client
if($get['nav_lang'] == '1031')
{
	$language_code = 'de';
}
//andere Nav-Clients
else 
{
	$language_code = 'en';
}

//Sprachvariablen für Webform
$message_error['de'] = "Bitte aktualisieren Sie Ihren Browser\n";
$message_error['en'] = "Please update your browser\n";

if ($get["ajax"] != 1) {
    ?>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Webshop Webform</title>
        <script src="<?= $GLOBALS['projectRoot'] ?>/plugins/jquery/jquery-1.11.0.js" type="text/javascript"></script>
        <link type="text/css" rel="stylesheet" href="webform.css"/>

        <? if ($request["webformtype"] == "Upload") { ?>
            <link type="text/css" rel="stylesheet" href="<?= $GLOBALS['projectRoot'] ?>/plugins/dropzone/dropzone.css"/>
            <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/dropzone/dropzone.js"></script>
            <link href="<?= $GLOBALS['projectRoot'] ?>/plugins/videojs/css/video-js.css" rel="stylesheet">
            <script src="<?= $GLOBALS['projectRoot'] ?>/plugins/videojs/js/videojs-ie8.min.js"></script>
            <script src="<?= $GLOBALS['projectRoot'] ?>/plugins/ffmpeg/progressBar.js" type="text/javascript"></script>
        <? } ?>
        <? if ($request["webformtype"] == "Editor") { ?>
            <script type="text/javascript" src="<?= $GLOBALS['projectRoot'] ?>/plugins/ckeditor/ckeditor.js"></script>
        <? } ?>
        <link rel="stylesheet" href="<?= $GLOBALS['projectRoot'] ?>/plugins/font-awesome/css/font-awesome.min.css">
    </head>
<body>
<?//= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>
    <?

}
if ($request["webformtype"] == "Editor") {
    include ("webform_textcontent.php");
} elseif ($request["webformtype"] == "Upload") {
    include ("webform_upload.php");
} elseif ($request["webformtype"] == "Collection") {
    include ("webform_collection.php");
}

if ($get["ajax"] != 1) {
    if (!(bool)$get["legacy"]) {
    ?>
    <script language="JavaScript">
        if (!window['postMessage']) {
            alert("<?= $message_error['$language_code'] ?>");
        } else {
            if (window.addEventListener) {
                window.addEventListener("message", ReceiveMessage, false);
            } else {
                window.attachEvent("onmessage", ReceiveMessage);
            }
        }
        function SendMessage(dest, event, data, source) {
            var sendData = {Event: event, Data: data};
            dest.postMessage(sendData, source);
        }
    </script>
    <? }
    if ($request["webformtype"] == "Upload") { ?>
        <script src="<?= $GLOBALS['projectRoot'] ?>/plugins/videojs/js/video.js"></script>
    <? } ?>
    </body>
    </html>
    <?
}
echo mysqli_error($GLOBALS['mysql_con']);
?>