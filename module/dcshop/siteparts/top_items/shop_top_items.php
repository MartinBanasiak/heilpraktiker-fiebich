<?

function shop_top_items_show($sitePartId)
{
    $rootDir = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\');
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';
    require_once $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'category_functions.inc.php';

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $IOCContainer = $GLOBALS['IOC'];
    $shopConfig = $IOCContainer->create('$CurrShopConfig');
    $shopType = $shopConfig->getShopType();
    $userId = $shopConfig->getUserID();
    $customerId = $shopConfig->getCustomer()->getId();

    $resultArray = array();


    $numberOfItemsToDisplay = 0;
    try {

        $prepStatement = '
          SELECT 
            no_of_items
          FROM 
            main_shop_top_items  
          WHERE 
                id = :sidePartId 
        ';

        $params = [
            [':sidePartId', $sitePartId, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) > 0) {
            $numberOfItemsToDisplay = $resultArray[0]['no_of_items'];
        }

    } catch (Exception $e) {
        exit(1);
    }
    $topItemsJsonData = '';
    try {

        $prepStatement = '
         SELECT
          top_items_json_data
        FROM
          shop_user_customer_top_items
        WHERE
              shop_customer_id = :customer_id
          AND top_items_json_data IS NOT NULL 
          AND top_items_json_data != \'\'
        ORDER BY 
          CASE WHEN (shop_user_id = :user_id) 
            THEN 1 
            ELSE 0 
          END DESC 
        LIMIT 1
        ';

        $params = [
            [':customer_id', $customerId, PDO::PARAM_STR],
            [':user_id', $userId, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) > 0) {
            $topItemsJsonData = $resultArray[0]['top_items_json_data'];
        }

    } catch (Exception $e) {
        exit(1);
    }

    $topItemsArray = json_decode($topItemsJsonData, true);
   // $topItemsArray = unserialize($topItemsArray);
    $topItemsObjectsArray = array();
    /** @var \DynCom\dc\dcShop\classes\WebshopItemBuilder $itemBuilder */
    $itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    foreach ($topItemsArray as $innerArray) {
        if (count($topItemsObjectsArray) == $numberOfItemsToDisplay) {
            break;
        }
        $itemNo = $innerArray['item_no'] ?? '';
        /** @var \DynCom\dc\dcShop\abstracts\WebshopItemDecorator $itemObj */
        $itemObj = $itemBuilder->getWebshopItemByItemNo($itemNo);
        if (null === $itemObj->getID() || !$itemObj->isActive()) {
            continue;
        }
        $topItemsObjectsArray[] = $itemObj;
    }

    $objArr = $itemBuilder->getAllWebshopItemsDecoratedForItemListAsArray($topItemsObjectsArray);
    $item_result = [];
    foreach ($objArr as $itemObj) {
        $item = [];
        $item['id'] = $itemObj->getID();
        $item['company'] = $itemObj->getCompany();
        $item['shop_code'] = $itemObj->getShopCode();
        $item['language_code'] = $itemObj->getLanguageCode();
        $item['item_no'] = $itemObj->getItemNo();
        $item['var_code'] = $itemObj->getVariantCode();
        $item['unit_price'] = $itemObj->getUnitPrice();
        $item['customer_price'] = $itemObj->getUnitPrice();
        $item['cross_price'] = $itemObj->getCrossPrice();
        $item['image_data'] = $itemObj->getImageData();
        $item['main_image_data'] = $itemObj->getMainImageData();
        $item['description'] = $itemObj->getDescription();
        $item['variant_typ'] = $itemObj->getVariantType();
        $item['variant_type'] = $itemObj->getVariantType();
        $item['summary'] = $itemObj->getSummary();
        $item['parent_item_no'] = $itemObj->getParentItemNo();
        $item['availability'] = $itemObj->isAvailable();
        $item['inventory'] = $itemObj->getInventory();
        $item_result[] = $item;
    }
    $numRows = count($item_result);

    if ($numRows > 0) {
        echo "<div class='shop_item_preview'>";
        switch ($shopType) {
            case 0:
                $list_no = 10;
                $shop_type_snippet = 'b2b';
                break;
            case 1:
                $list_no = 11;
                $shop_type_snippet = 'b2c';
                break;
            case 2:
                $list_no = 10;
                $shop_type_snippet = 'salesperson';
                break;
            case 3:
                $list_no = 10;
                $shop_type_snippet = 'catalog';
                break;
            default:
                $list_no = 10;
                break;
        }
        $showItemListPath = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . $shop_type_snippet . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';
        require_once $showItemListPath;
        show_item_list($item_result, $list_no, FALSE);
        echo "</div>";
    }
}

function shop_top_items_edit()
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_shop_top_items.inc.php';
}

