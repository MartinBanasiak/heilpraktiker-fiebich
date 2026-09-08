<?php
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';


db_connect();

mkdir($rootDir . DIRECTORY_SEPARATOR . 'userdata/slideshow', 0777);
mkdir($rootDir . DIRECTORY_SEPARATOR . 'uerdata/slideshow/original', 0777);
mkdir($rootDir . DIRECTORY_SEPARATOR . 'userdata/slideshow/icon', 0777);


$sql_entry = "CREATE TABLE slideshow_entry
(
id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(id),
preview VARCHAR(250) NOT NULL,
description VARCHAR(250) NOT NULL,
headline VARCHAR(250),
text VARCHAR(250),
link VARCHAR(255),
info VARCHAR(250),
main_navigation_has_sitepart_id INT NOT NULL,
sorting INT NOT NULL
)";

$entry_query = mysqli_query($GLOBALS['mysql_con'], $sql_entry);
IF (!$entry_query) {
    echo "* Tabelle slideshow_entry konnte nicht erstellt werden.<br />";
} ELSE {
    echo "* Tabelle slideshow_entry wurde erstellt. (Schritt 1 von 5)<br />";
}

$sql_options = "CREATE TABLE slideshow_options 
(
id INT NOT NULL AUTO_INCREMENT,
PRIMARY KEY(id),
sitepart_id INT(3) UNIQUE NOT NULL,
width INT(4) NOT NULL,
height INT(4) NOT NULL,
effect VARCHAR(10),
arrows VARCHAR(10),
eff_interval VARCHAR(4),
effect_duration VARCHAR(3),
text_effect VARCHAR(10),
text_pos VARCHAR(15)
)";

$query_options = mysqli_query($GLOBALS['mysql_con'], $sql_options);
IF (!$query_options) {
    echo "* Tabelle slideshow_options konnte nicht erstellt werden.<br />";
} ELSE {
    echo "* Tabelle slideshow_options wurde erstellt. (Schritt 2 von 5) <br />";
}

$sql_main_modul = "INSERT INTO main_modul (
	code,
	name,
	path,
	installed
) VALUES (
	'slideshow',
	'Slideshow',
	'/module/slideshow/',
	'1'
)";

$query_modul = mysqli_query($GLOBALS['mysql_con'], $sql_main_modul);
IF (!$query_modul) {
    echo "* main_modul Eintrag konnte nicht erstellt werden.<br />";
} ELSE {
    echo "* main_modul Eintrag erstellt. (Schritt 3 von 5)<br />";
}

$sql2          = "SELECT id FROM main_modul WHERE code = 'slideshow'";
$query         = mysqli_query($GLOBALS['mysql_con'], $sql2);
$result        = mysqli_fetch_array($query);
$main_modul_id = $result[0];
IF (!$main_modul_id) {
    echo "* main_modul id konnte nicht abgefragt werden.<br />";
} ELSE {
    echo "* main_modul id abgefragt. (Schritt 4 von 5)<br />";
}

$sql_sitepart = "INSERT INTO main_sitepart (
	main_modul_id, 
	name, 
	description,
	frontend_include,
	frontend_start_parameter,
	admin_include,
	admin_start_parameter
) VALUES (
	'" . $main_modul_id . "',
	'Slideshow',
	'Verwaltet Slideshows',
	'/module/slideshow/slideshow.php',
	'slideshow_show',
	'/module/slideshow/slideshow.php',
	'slideshow_edit'
)";

$query_sitepart = mysqli_query($GLOBALS['mysql_con'], $sql_sitepart);
IF (!$query_sitepart) {
    echo "* main_sitepart Eintrag konnte nicht erstellt werden. <br />";
} ELSE {
    echo "* main_sitepart Eintrag wurde erstellt. (Schritt 5 von 5)<br />";
}






?>