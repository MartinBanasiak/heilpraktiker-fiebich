<?
//Dateipfade
define("PATH_ORIGINAL_FILEGALLERY", "../../userdata/filegallery/original/");
define("PATH_ORIGINAL_FRONTEND_FILEGALLERY", "/userdata/filegallery/original/");
define("PATH_ICON_FRONTEND_FILEGALLERY", "/userdata/filegallery/icons/");

function formatSizeUnits( $bytes ) {
    if ($bytes >= 1024) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes > 1) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}