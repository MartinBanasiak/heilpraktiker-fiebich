<?
/**
 * slideshow_show() Modulaufruf Frontend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function slideshow_show( $sitepart_id ) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_slideshow.inc.php';
}

/**
 * slideshow_edit() Modulaufruf Backend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function slideshow_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow.inc.php';
}

?>