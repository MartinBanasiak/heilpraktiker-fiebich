<?
/**
 * magicscroll_show() Modulaufruf Frontend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function magicscroll_show( $sitepart_id ) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_magicscroll.inc.php';
}

/**
 * magicscroll_edit() Modulaufruf Backend
 *
 * @param mixed $sitepart
 *
 * @return
 */
function magicscroll_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll.inc.php';
}

?>