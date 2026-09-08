<?
use DynCom\dc\dcShop\classes\WebshopItemBuilder;

function shop_category_preview_show($sitepart_id, &$IOCContainer = null  ) {
    $rootDir = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\');
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'category_functions.inc.php';

    if(!($IOCContainer instanceof Dice\Dice) && isset($_SESSION['IOC'])) {
        $size = mb_strlen($_SESSION['IOC'],'8bit');
        $unserialized = unserialize($_SESSION['IOC'],\Dice\Dice::class);
        if($unserialized instanceof \Dice\Dice) {
            $IOCContainer = $unserialized;
        }
    }
    $languageRepo = $IOCContainer->create(\DynCom\dc\common\classes\LanguageRepository::class);
    $shopRepo = $IOCContainer->create(\DynCom\dc\dcShop\classes\ShopRepository::class);
    /** @var \DynCom\dc\dcShop\classes\CategoryRepository $categoryRepo */
    $categoryRepo = $IOCContainer->create(\DynCom\dc\dcShop\classes\CategoryRepository::class);
    /** @var \DynCom\dc\dcShop\classes\CurrShopConfiguration $currShopConfig */
    $currShopConfig = $IOCContainer->create('$CurrShopConfig');
    /** @var \DynCom\dc\dcShop\classes\ShopLanguage $currShopLanguage */
    $currShopLanguage = $IOCContainer->create('$CurrShopLanguage');

    $pdo = get_main_db_pdo_from_env_single_instance();

    $query  = "SELECT * FROM main_shop_category_preview WHERE id = :sitepart_id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':sitepart_id',$sitepart_id,PDO::PARAM_INT);

    if (!$stmt->execute()) {
        $errorInfo = $pdo->errorInfo();
        $errorString = implode($errorInfo,PHP_EOL);
        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
    }

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $categoryPreview = $stmt->fetch();

    if (is_array($categoryPreview)) {
        $categoryDisplayOption = \DynCom\dc\dcShop\classes\Category::SHOW_SUBCATEGORY_OPTION_DEFAULT;
        $languageId = (int)$categoryPreview["main_language_id"];
        $language = $languageRepo->findByID($languageId);
        $company = $language->company;
        $shop_code = $language->shop_code;
        $shop_language_code = $language->shop_language_code;
        $shop_language = $currShopLanguage->getAllFieldsAsArray();
        /**
         * @var $shop \DynCom\dc\dcShop\classes\Shop
         */
        $shop = $shopRepo->findByAltPrimary(['company' => $company,'code' => $shop_code]);
        $category_shop_code = $shop->getUseCategoriesFromShopCode();
        if ((int)$categoryPreview["category_line_no"] > 0) {
            $catPrimary = [
                'company' => $company,
                'shop_code' => $category_shop_code,
                'language_code' => $shop_language_code,
                'line_no' => $categoryPreview["category_line_no"]
            ];
            /**
             * @var $category \DynCom\dc\dcShop\classes\Category
             */
            $category = $categoryRepo->findByAltPrimary($catPrimary);
            $categoryDisplayOption = $category->getSubcategoryDisplayOption;
        }

        $cat_line_no = (int)$categoryPreview["category_line_no"];
        $categoryCollection = $categoryRepo->getAllChildrenSorted($cat_line_no,$currShopConfig,true);
        $categoryArray = [];
        foreach ($categoryCollection as $category) {
            /**
             * @var $category \DynCom\dc\dcShop\classes\Category
             */
            $catArr = $category->getAllFieldsAsArray();
            $categoryArray[] = $catArr;
        }

        $shopDir = 'b2c';
        switch ($shop->shop_typ) {
            case 0:
                $shopDir = 'b2b';
                break;
            case 1:
                $shopDir = 'b2c';
                break;
            case 2:
                $shopDir = 'salesperson';
                break;
            case 3:
                $shopDir = 'catalog';
                break;
        }
        require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shopDir . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';

        switch ($categoryDisplayOption) {
            case \DynCom\dc\dcShop\classes\Category::SHOW_SUBCATEGORY_OPTION_WITH_ITEMS:
                echo "<div class=\"categorybox categorybox_etsy\"><div class='row'>\n";
                show_category_list_etsy($categoryArray, $shop_language);
                echo "</div>
                        </div>\n";
                break;
            default:
                //Erweiterung um Kategoriebilder für jede Unterkategorie
                echo "<div class=\"categorybox\"><div class='row'>\n";
                show_category_list($categoryArray, $shop_language);
                echo "</div></div>\n";
                break;
        }

    }

}

function shop_category_preview_edit() {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_category_preview.inc.php';
}
/*
if((int) $sitepart['main_sitepart_header_id'] > 0 ){
	shop_main_show($sitepart['main_sitepart_header_id']);
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_main.inc.php';*/