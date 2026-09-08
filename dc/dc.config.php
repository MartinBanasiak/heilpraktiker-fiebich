<?
/*
$GLOBALS["myservername"] = "localhost";
$GLOBALS["mylogin"]      = "root";
$GLOBALS["mypass"]       = "";
$GLOBALS["mydb"]         = "dcshop";
*/

$GLOBALS['myservername'] = getenv('MAIN_MYSQL_DB_HOST');
$GLOBALS["mylogin"]      = getenv('MAIN_MYSQL_DB_USER');
$GLOBALS["mypass"]       = getenv('MAIN_MYSQL_DB_PASS');
$GLOBALS["mydb"]         = getenv('MAIN_MYSQL_DB_SCHEMA');

date_default_timezone_set("EUROPE/Berlin");
?>