<?
function shop_main_show( $sitepart_id,&$IOCContainer = null ) {

    static $rootDir;
    static $shopCommonDir;

    if (null === $rootDir) {
        $rootDir = dirname(dirname(dirname(dirname(__DIR__))));
        $shopDir = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop';
        $dcDir = $rootDir . DIRECTORY_SEPARATOR . 'dc';
        $shopCommonDir = $shopDir . DIRECTORY_SEPARATOR . 'common';
        $initFilePath = $dcDir . DIRECTORY_SEPARATOR . 'init.php';
        $shopInitFilePath = $shopCommonDir . DIRECTORY_SEPARATOR . 'init.php';
        $shopStartFilePath = $shopDir . DIRECTORY_SEPARATOR . 'shop_start.inc.php';
        $shopFunctionsPath = $shopCommonDir . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';

        if (file_exists($initFilePath) && is_file($initFilePath) && is_readable($initFilePath)) {
            include_once $initFilePath;
        }
        if (file_exists($shopInitFilePath) && is_file($shopInitFilePath) && is_readable($shopInitFilePath)) {
            include_once $shopInitFilePath;
        }
        if (file_exists($shopStartFilePath) && is_file($shopStartFilePath) && is_readable($shopStartFilePath)) {
            include_once $shopStartFilePath;
        }
        if (file_exists($shopFunctionsPath) && is_file($shopFunctionsPath) && is_readable($shopFunctionsPath)) {
            include_once $shopFunctionsPath;
        }
    }


    if(!($IOCContainer instanceof Dice\Dice) && isset($_SESSION['IOC'])) {
        $size = mb_strlen($_SESSION['IOC'],'8bit');
        $unserialized = unserialize($_SESSION['IOC']);
        if($unserialized instanceof \Dice\Dice) {
            $IOCContainer = $unserialized;
        }
    }
    $query  = "SELECT * FROM main_shop_sitepart WHERE id = '" . $sitepart_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $shop_sitepart   = @mysqli_fetch_array($result);
        $type            = $shop_sitepart['type'];
        $shoptype_string = '';
        if (empty($GLOBALS['shop']['id'])) {
            set_shop_globals_from_site();
        }

        if($IOCContainer) {
            $vatrule = $IOCContainer->getRule('DynCom\dc\dcShop\classes\VATManager');
            $vatMgr = $IOCContainer->create('$VATManager');
            $advancedPriceProvider = $IOCContainer->create('DynCom\dc\dcShop\classes\AdvancedPriceProvider');
            $currShop         = $IOCContainer->create('$CurrShop');
            $currShopLanguage = $IOCContainer->create('$CurrShopLanguage');
            $currVisitor      = $IOCContainer->create('$CurrVisitor');
            $currUser         = $IOCContainer->create('$CurrUser');
            $currCustomer     = $IOCContainer->create('$CurrCustomer');
            $currShopConfig   = $IOCContainer->create('$CurrShopConfig');
            $currUserBasket = $IOCContainer->create('$CurrUserBasket');
        }

        /** OOP END **/


        switch ($GLOBALS['shop']['shop_typ']) {
            case 0: //B2B
                $shoptype_string = 'b2b';
                break;
            case 1: //B2C
                $shoptype_string = 'b2c';
                break;
            case 2: //Salesperson
                $shoptype_string = 'salesperson';
                break;
            case 3: //Catalog
                $shoptype_string = 'catalog';
                break;
        }

        switch ($type) {
            case 0: //Shop
                $required_navshop = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'navshop_' . $shoptype_string . '.inc.php';
                require_once($required_navshop);
                break;
            case 1: //Warenkorb
                $required_basket = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'header_basket.inc.php';
                require_once($required_basket);
                break;
            case 2: //Suche
                $required_search = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'site_search.inc.php';
                require_once($required_search);
                break;
            case 3: //Direktbestellung
                $required_direct_order = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'direct_order.inc.php';
                require_once($required_direct_order);
                break;
            case 4: //User-Menu
                $required_user_menu = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'user_menu.inc.php';
                require($required_user_menu);
                break;
            case 5: //Passwort  vergessen
                $required_password_lost = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'password_reminder.inc.php';
                require_once($required_password_lost);
                break;
            case 6: //Sales Person
                $required_sales_person = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'sales_person.inc.php';
                require_once($required_sales_person);
                break;
            case 7: // new password request
                $required_password_lost = realpath(MODULE_PATH . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shoptype_string) . DIRECTORY_SEPARATOR . 'password_change_request.inc.php';
                require_once($required_password_lost);
                break;
        }

    }
}

function shop_main_edit() {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_main.inc.php');
}
/*
if((int) $sitepart['main_sitepart_header_id'] > 0 ){
	shop_main_show($sitepart['main_sitepart_header_id']);
}
require_once('edit_shop_main.inc.php');*/