<?
// Inhalt laden
$query  =
    "SELECT *, main_navigation_has_sitepart.id AS navigation_has_sitepart_id " .
    "FROM main_navigation_has_sitepart " .
    "LEFT JOIN main_sitepart ON main_navigation_has_sitepart.main_sitepart_id = main_sitepart.id " .
    "WHERE main_navigation_id = '" . $navigation["id"] . "' " .
    "AND active ORDER BY sorting";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) > 0) {
    while ($sitepart = @mysqli_fetch_array($result)) {
        $filePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . ($sitepart['frontend_include'] ?? '');
        if (file_exists($filePath) && is_file($filePath) && is_readable($filePath)) {
            require_once $filePath;
            if (function_exists($sitepart["frontend_start_parameter"])) {
                $sitepart["frontend_start_parameter"]($sitepart);
            }
        }
    }
}
?>