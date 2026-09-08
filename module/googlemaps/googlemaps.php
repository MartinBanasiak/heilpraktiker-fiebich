<?
function googlemaps_show( $sitepart_id ) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'googlemaps_config.inc.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_google_maps.inc.php';
}

function googlemaps_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps.inc.php';
}