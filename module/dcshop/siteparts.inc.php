<?php
function show_searchbar( $sitepart ) {
    switch ($GLOBALS['shop']['shop_typ']) {
        case 0:
            require_once 'b2b/site_search.inc.php';
            break;
        case 1:
            require_once 'b2c/site_search.inc.php';
            break;
        case 2:
            require_once 'salesperson/site_search.inc.php';
            break;
        case 3:
            require_once 'catalog/site_search.inc.php';
            break;
        default:
            require_once 'b2b/site_search.inc.php';
            break;
    }
}

function show_basket( $sitepart ) {
    // UT - Anpassung Mobile Version - 07.09.2012
    if ($GLOBALS["site"]["code"] == $GLOBALS["site"]["mobile_version"]) {
        switch ($GLOBALS['shop']['shop_typ']) {
            case 0:
                require_once 'b2b/mobile/site_header.inc.php';
                break;
            case 1:
                require_once 'b2c/mobile/site_header.inc.php';
                break;
            case 2:
                require_once 'salesperson/mobile/site_header.inc.php';
                break;
            case 3:
                require_once 'catalog/mobile/site_header.inc.php';
                break;
            default:
                require_once 'b2b/mobile/site_header.inc.php';
                break;
        }

    } else {
        switch ($GLOBALS['shop']['shop_typ']) {
            case 0:
                require_once 'b2b/site_header.inc.php';
                break;
            case 1:
                require_once 'b2c/site_header.inc.php';
                break;
            case 2:
                require_once 'salesperson/site_header.inc.php';
                break;
            case 3:
                require_once 'catalog/site_header.inc.php';
                break;
            default:
                require_once 'b2b/site_header.inc.php';
                break;
        }
    }
    // UT - Anpassung Mobile Version Ende - 07.09.2012

    // Alter Code:
    /*
    switch($GLOBALS['shop']['shop_typ'])
    {
        case 0: require_once 'b2b/site_header.inc.php'; break;
        case 1: require_once 'b2c/site_header.inc.php'; break;
        case 2: require_once 'salesperson/site_header.inc.php'; break;
        case 3: require_once 'catalog/site_header.inc.php'; break;
        default: require_once 'b2b/site_header.inc.php'; break;
    }
    */
}

function shop_item_preview( $sitepart ) {
    switch ($GLOBALS['shop']['shop_typ']) {
        case 0:
            require_once 'b2b/item_preview.inc.php';
            break;
        case 1:
            require_once 'b2c/item_preview.inc.php';
            break;
        case 2:
            require_once 'salesperson/item_preview.inc.php';
            break;
        case 3:
            require_once 'catalog/item_preview.inc.php';
            break;
        default:
            require_once 'b2b/item_preview.inc.php';
            break;
    }
}

function shop_password_reminder( $sitepart ) {
    if (empty($GLOBALS['shop']['code'])) {
        $query  = 'SELECT value_2 FROM main_navigation_has_sitepart WHERE main_navigation_id IN (SELECT id FROM main_navigation WHERE main_site_id=\'' . $GLOBALS['navigation']['main_site_id'] . '\' AND main_language_id=\'' . $GLOBALS['navigation']['main_language_id'] . '\') AND main_sitepart_id=\'24\'';
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $langID = @mysqli_result($result, 0, 0);
        if (!empty($langID)) {
            $query    = 'SELECT company,shop_code,shop_language_code AS \'language_code\' FROM main_language WHERE id=' . $langID;
            $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
            $company  = @mysqli_result($result, 0, 0);
            $code     = @mysqli_result($result, 0, 1);
            $langCode = @mysqli_result($result, 0, 2);
            if (!empty($company) && !empty($code) && !empty($langCode)) {
                $shop_query = 'SELECT * FROM shop_shop WHERE company=\'' . $company . '\' AND code=\'' . $code . '\'';
                $result     = @mysqli_query($GLOBALS['mysql_con'], $shop_query);
                if (@mysqli_num_rows($result) == 1) {
                    $shop            = @mysqli_fetch_assoc($result);
                    $GLOBALS['shop'] = $shop;
                }
                $lang_query = 'SELECT * FROM shop_language WHERE company=\'' . $company . '\' AND shop_code=\'' . $code . '\' AND code=\'' . $langCode . '\'';
                $result     = @mysqli_query($GLOBALS['mysql_con'], $lang_query);
                if (@mysqli_num_rows($result) == 1) {
                    $lang                     = @mysqli_fetch_assoc($result);
                    $GLOBALS['shop_language'] = $lang;
                }
            }
        }
    }

    //var_dump($GLOBALS['shop']);
    switch ($GLOBALS['shop']['shop_typ']) {
        case 0:
            require_once 'b2b/password_reminder.inc.php';
            break;
        case 1:
            require_once 'b2c/password_reminder.inc.php';
            break;
        case 2:
            require_once 'salesperson/password_reminder.inc.php';
            break;
        case 3:
            require_once 'catalog/password_reminder.inc.php';
            break;
        default:
            require_once 'b2b/password_reminder.inc.php';
            break;
    }
}

function shop_customer_salesperson( $sitepart ) {
    switch ($GLOBALS['shop']['shop_typ']) {
        case 0:
            require_once 'b2b/customer_salesperson.inc.php';
            break;
        case 1:
            require_once 'b2c/customer_salesperson.inc.php';
            break;
        case 2:
            require_once 'salesperson/customer_salesperson.inc.php';
            break;
        case 3:
            require_once 'catalog/customer_salesperson.inc.php';
            break;
        default:
            require_once 'b2b/customer_salesperson.inc.php';
            break;
    }
}

?>