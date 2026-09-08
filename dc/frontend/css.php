<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 28.09.2016
 * Time: 16:43
 */

//ini_set('display_errors', '0');

//Vendor Autoloader
include(rtrim(dirname(dirname(__DIR__)),'/\\') . '/vendor/autoload.php');
//Load environment variables from config if exists
$envDir = rtrim(dirname(dirname(__DIR__)),'/\\') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

// Konfigurationsdatei laden
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '/dc/common/common_functions.inc.php';
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '/dc/dc-server.config.php';

// Verbindung mit Datenbank herstellen
db_connect();

// Variablen schützen
$_GET = secure_array($_GET);
$_POST = secure_array($_POST);


$query = "
		SELECT id, path, default_active, type
		FROM main_layout_inclusions
		WHERE main_layout_id = '" . $_GET["layout_id"] . "' AND type = 'css'
		ORDER BY sorting ASC";

$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$layout_inclusions = array();
while ($row = @mysqli_fetch_assoc($result)) {
    $layout_inclusions[$row['id']] = $row;
}

if (count($layout_inclusions) == 0) {
    return;
}


$includes_link_ids = array();
$query = "SELECT * FROM main_page_layout_inclusion_link WHERE main_page_id = '" . $_GET["page_id"]."'";

$result = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_assoc($result)) {
    $includes_link_ids[$row['main_layout_includes_id']] = $row;
}

$outputBuffer = "";


foreach ($layout_inclusions as $inclusion_id => $inclusion_row) {
    $include = FALSE;

    if (!isset($includes_link_ids[$inclusion_id]) &&
        $inclusion_row['default_active'] == 1
    ) {
        $include = TRUE;

    } elseif (isset($includes_link_ids[$inclusion_id]) && $includes_link_ids[$inclusion_id]['active'] == 1) {
        $include = TRUE;
    }

    if ($include === FALSE) {
        continue;
    }

    if (file_exists(rtrim(dirname(dirname(__DIR__)),'/\\') . $inclusion_row["path"])) {

        $buffer = file_get_contents(rtrim(dirname(dirname(__DIR__)),'/\\') . $inclusion_row["path"]);
        $url = explode("/", $inclusion_row["path"]);
        $baseURI = "/" . $url[1] . "/" . $url[2] . "/";
        $buffer = preg_replace("/url(?:\([\"']?)(.*?)(?:[\"']?\))/isx", 'url("'.$baseURI.'$1")', $buffer);

        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);

        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);


        $outputBuffer .= $buffer;
    }
    //echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $inclusion_row["path"] . "\"></link>\n";
}

// Enable GZip encoding.
ob_start("ob_gzhandler");

// Enable caching
headerFunctionBridge('Cache-Control: public');
headerFunctionBridge("Content-type: text/css");
echo $outputBuffer;