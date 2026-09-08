<?
/**
 * gallery_show() Modulaufruf Frontend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function gallery_show( $sitepart_id ) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_gallery.inc.php';
}

/**
 * gallery_edit() Modulaufruf Backend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function gallery_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery.inc.php';
}

?>