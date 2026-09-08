<?php
/*
$GLOBALS["myservername"] = "localhost";
$GLOBALS["mylogin"]      = "root";
$GLOBALS["mypass"]       = "123";
$GLOBALS["mydb"]         = "dcshop";*/


$GLOBALS['myservername'] = getenv('MAIN_MYSQL_DB_HOST');
$GLOBALS["mylogin"]      = getenv('MAIN_MYSQL_DB_USER');
$GLOBALS["mypass"]       = getenv('MAIN_MYSQL_DB_PASS');
$GLOBALS["mydb"]         = getenv('MAIN_MYSQL_DB_SCHEMA');

/*
$GLOBALS["ftp_login"]    = "web828";
$GLOBALS["ftp_password"] = "girdidGion";
$GLOBALS["ftp_host"]     = "www.dcshop.dev";
*/

$GLOBALS["ftp_login"]    = getenv('FTP_USER');
$GLOBALS["ftp_password"] = getenv('FTP_PASS');
$GLOBALS["ftp_host"]     = getenv('FTP_HOST');
