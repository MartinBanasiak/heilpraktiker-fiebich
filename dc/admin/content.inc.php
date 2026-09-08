<?
// Inhalt laden

$adminMenu             = \DynCom\dc\common\classes\Adminmenu::get();
$admin_start_parameter = "";

// default


if ($_GET["level_1"] != "") {
    $include = $adminMenu[$_GET["level_1"]]['include'];
    if (isset($adminMenu[$_GET["level_1"]]['admin_start_parameter'])) {
        $admin_start_parameter = $adminMenu[$_GET["level_1"]]['admin_start_parameter'];
    }
}

if ($_GET["level_2"] != "") {
    $include = $adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['include'];
    if (isset($adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['admin_start_parameter'])) {
        $admin_start_parameter = $adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['admin_start_parameter'];
    }
}

if ($include == "") {
    $include = $adminMenu["default"]['include'];
}


if (file_exists($include)) {
    $menuclass = $GLOBALS["menu_opened"] == TRUE
        ? "menu_opened"
        : "";

    echo "<div id=\"content_3\" class=\"{$menuclass}\">\n";

    require_once($include);

    if (function_exists($admin_start_parameter)) {
        $admin_start_parameter();
    }

    echo "</div>\n";
}

?>