<?php
switch ($GLOBALS['shop']['shop_typ']) {
    case 0:
        require_once 'b2b/navshop_b2b.inc.php';
        break;
    case 1:
        require_once 'b2c/navshop_b2c.inc.php';
        break;
    case 2:
        require_once 'salesperson/navshop_salesperson.inc.php';
        break;
    case 3:
        require_once 'catalog/navshop_catalog.inc.php';
        break;
    default:
        require_once 'b2b/navshop_b2b.inc.php';
        break;
}
?>