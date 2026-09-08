<?
// Inhalt laden

$adminMenu = \DynCom\dc\common\classes\Adminmenu::get();

if ($_GET["level_1"] != "") {
    $include = $adminMenu[$_GET["level_1"]]['editmenu'];
}

if ($_GET["level_2"] != "") {
    $include = $adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['editmenu'];
}

if (($include <> '') && (file_exists($include))) {
    require_once($include);
}

?>
