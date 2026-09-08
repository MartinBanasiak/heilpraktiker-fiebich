<?
// Inhalt laden
$filePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . ($admin_navigation['menu_include'] ?? '');
if (file_exists($filePath) && is_file($filePath) && is_readable($filePath)) {
    require_once $filePath;
}

?>