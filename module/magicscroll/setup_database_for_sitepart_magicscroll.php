<?php
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR .  'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';

db_connect();

function rrmdir( $dir ) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

global $main_modul_id;
global $dir1;
global $dir2;
global $dir3;
$dir1 = "../../userdata/magicscroll";
$dir2 = "../../userdata/magicscroll/original";
$dir3 = "../../userdata/magicscroll/icon";

function rollback() {
    //delete directories
    rrmdir($dir1);
    rrmdir($dir2);
    rrmdir($dir3);

    //check for created tables and delete
    $table_drop = "DROP TABLE IF EXISTS magicscroll_entry, magicscroll_options";
    @mysqli_query($GLOBALS['mysql_con'], $table_drop);

    //check for created records and delete
    $delete_modul_record = "DELETE FROM main_modul WHERE code = 'magicscroll' LIMIT 1";
    @mysqli_query($GLOBALS['mysql_con'], $delete_modul_record);
    $delete_sitepart_record = "DELETE FROM main_sitepart WHERE name = 'magicscroll' LIMIT 1";
    @mysqli_query($GLOBALS['mysql_con'], $delete_sitepart_record);

}

function install_error( $error ) {
    echo "DAS SITEPART KONNTE NICHT INSTALLIERT WERDEN. <br />Grund: " . $error . "<br />";
    $rollback = rollback();
    echo "Rollback wurde durchgef&uuml;hrt.<br />";
}

function make_directories( $dir1, $dir2, $dir3 ) {
    $makedir1 = mkdir("$dir1", 0777);
    $makedir2 = mkdir("$dir2", 0777);
    $makedir3 = mkdir("$dir3", 0777);
    IF ($makedir1 && $makedir2 && $makedir3) {
        echo "* Verzeichnisse erfolgreich erstellt. (Schritt 1 von 6)<br />";
        make_table_entry();
    } ELSE {
        $error = "Mindestens ein Verzeichnis konnte nicht erstellt werden.";
        install_error($error);
    }
}

function make_table_entry() {
    $sql_entry = "CREATE TABLE magicscroll_entry
	(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	preview VARCHAR(250) NOT NULL,
	description VARCHAR(250) NOT NULL,
	entry TEXT,
	main_navigation_has_sitepart_id INT NOT NULL,
	sorting INT NOT NULL
	)";

    $entry_query = mysqli_query($GLOBALS['mysql_con'], $sql_entry);
    IF (!$entry_query) {
        $error = "Tabelle magicscroll_entry konnte nicht erstellt werden.";
        install_error($error);
    } ELSE {
        echo "* Tabelle magicscroll_entry wurde erstellt. (Schritt 2 von 6)<br />";
        make_table_options();
    }
}

function make_table_options() {
    $sql_options = "CREATE TABLE magicscroll_options
	(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	sitepart_id INT(3) UNIQUE NOT NULL,
	width INT(4) NOT NULL,
	height INT(4) NOT NULL,
	arrows VARCHAR(10),
	eff_interval VARCHAR(4),
	effect_duration VARCHAR(3)
	)";

    $query_options = mysqli_query($GLOBALS['mysql_con'], $sql_options);
    IF (!$query_options) {
        $error = "Tabelle magicscroll_options konnte nicht erstellt werden.";
        install_error($error);
    } ELSE {
        echo "* Tabelle magicscroll_options wurde erstellt. (Schritt 3 von 6) <br />";
        append_main_modul();
    }
}

function append_main_modul() {
    $sql_main_modul = "INSERT INTO main_modul (
		code,
		name,
		path,
		installed
	) VALUES (
		'magicscroll',
		'magicscroll',
		'/module/magicscroll/',
		'1'
	)";

    $query_modul = mysqli_query($GLOBALS['mysql_con'], $sql_main_modul);
    IF (!$query_modul) {
        $error = "main_modul Eintrag konnte nicht erstellt werden.";
        install_error($error);
    } ELSE {
        echo "* main_modul Eintrag erstellt. (Schritt 4 von 6)<br />";
        get_main_modul_id();
    }
}

function get_main_modul_id() {
    $sql_modul_id = "SELECT id FROM main_modul WHERE code = 'magicscroll'";
    $query        = mysqli_query($GLOBALS['mysql_con'], $sql_modul_id);
    $result       = mysqli_fetch_array($query);
    global $main_modul_id;
    $main_modul_id = $result[0];
    IF ($main_modul_id <> '') {
        echo "* main_modul_id ausgelesen. (Schritt 5 von 6)<br />";
        append_main_sitepart();
    } ELSE {
        $error = "main_modul_id konnte nicht ausgelesen werden.";
        install_error($error);
    }
}

function append_main_sitepart() {
    $main_modul_id = $GLOBALS['main_modul_id'];
    $sql_sitepart  = "INSERT INTO main_sitepart (
		main_modul_id, 
		name, 
		description,
		frontend_include,
		frontend_start_parameter,
		admin_include,
		admin_start_parameter
	) VALUES (
		'" . $main_modul_id . "',
		'Scroll-Leiste',
		'Verwaltet Scroll-Leisten',
		'/module/magicscroll/magicscroll.php',
		'magicscroll_show',
		'/module/magicscroll/magicscroll.php',
		'magicscroll_edit'
	)";

    $query_sitepart = mysqli_query($GLOBALS['mysql_con'], $sql_sitepart);
    IF ($query_sitepart) {
        echo "* main_sitepart Eintrag erstellt. (Schritt 6 von 6)<br />DIE INSTALLTION WURDE ERFOLGREICH ABGESCHLOSSEN.";
    } ELSE {
        $error = "main_sitepart Eintrag konnte nicht erstellt werden.";
        install_error($error);
    }
}

make_directories($dir1, $dir2, $dir3);

?>