<?
/**
 * filegallery_show() Modulaufruf Frontend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function filegallery_show( $sitepart_id ) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_filegallery.inc.php';
}

/**
 * filegallery_edit() Modulaufruf Backend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function filegallery_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_filegallery.inc.php';
}

?>