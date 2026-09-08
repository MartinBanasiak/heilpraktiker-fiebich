<?
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $token2 = json_decode(base64_decode($_POST["token_2"]));
    $timeout = 3600;

    if ($token2->time < (time() - $timeout)) {
        die();
    }

    if ($token2->token_id !== $_SERVER['SERVER_NAME']) {
        die();
    }

    $baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
    include($baseDirectory . '/vendor/autoload.php');

    //Load environment variables from config if exists
    $envDir = rtrim($baseDirectory, '/') . '/config';
    if (is_dir($envDir)) {
        $dotenv = new \Dotenv\Dotenv($envDir);
        $dotenv->load();
    }

    $secret = getenv('SHOP_PASSWORD');
    $valid = ($_POST["token_1"] === base64_encode(hash_hmac("sha256", json_encode($token2), $secret)));

    if (!$valid) {
        die();
    }

    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $itemNumber = '';
    $companyName = '';
    $shopCode = '';
    $languageCode = '';

//if(array_key_exists('item_number',$_REQUEST))
//$itemNumber = $_REQUEST['item_number'] ?? '';

    if (isset($_REQUEST['item_number'])) {
        $itemNumber = $_REQUEST['item_number'];
    }
    if (isset($_REQUEST['company'])) {
        $companyName = $_REQUEST['company'];
    }
    if (isset($_REQUEST['shop_code'])) {
        $shopCode = $_REQUEST['shop_code'];
    }
    if (isset($_REQUEST['language_code'])) {
        $languageCode = $_REQUEST['language_code'];
    }


    $variantType = 0;

    try {
        $prepStatement = '
          SELECT 
            variant_typ 
          FROM 
            shop_shop  
          WHERE 
                company = :company 
            AND code = :code 
        ';

        $params = [
            [':company', $companyName, PDO::PARAM_STR],
            [':code', $shopCode, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $resultArray = $pdo->getResultArray();

        if (count($resultArray) > 0) {
            $variantType = (int)$resultArray[0]['variant_typ'];
        }

    } catch (Exception $e) {
        exit(1);
    }

    switch ($variantType) {

        case 0:
            $prepStatement = '
            SELECT
              item_no,
              description
            FROM
              shop_item
            WHERE 
                  parent_item_no = :itemNumber
              AND company = :company
              AND shop_code = :shopCode
              AND language_code = :languageCode
            ';
            $params = [
                [':itemNumber', $itemNumber, PDO::PARAM_STR],
                [':company', $companyName, PDO::PARAM_STR],
                [':shopCode', $shopCode, PDO::PARAM_STR],
                [':languageCode', $languageCode, PDO::PARAM_STR],
            ];
            $keys = [
                'item_no',
                'description',
            ];
            break;
        case 1:
            $prepStatement = '
            SELECT
              item_no,
              description
            FROM
              shop_item
            WHERE 
                  company = :company
              AND shop_code = :shopCode
              AND language_code = :languageCode
              AND (
                      parent_item_no = :itemNumber
                  OR  item_no = :itemNumber
              )
            ';
            $params = [
                [':itemNumber', $itemNumber, PDO::PARAM_STR],
                [':company', $companyName, PDO::PARAM_STR],
                [':shopCode', $shopCode, PDO::PARAM_STR],
                [':languageCode', $languageCode, PDO::PARAM_STR],
            ];
            $keys = [
                'item_no',
                'description',
            ];
            break;
        case 2:
            $prepStatement = '
          SELECT
            siv.code as variant_code,
            CASE WHEN
                    sivt.id IS NOT NULL
              THEN  sivt.description
              ELSE  siv.description
            END AS \'description\'
          FROM
            shop_item_variant siv
          LEFT JOIN
            shop_item_variant_translation sivt
          ON (
                sivt.company = siv.company
            AND sivt.variant_code = siv.code
            AND sivt.language_code = :languageCode
          )
          WHERE
                siv.company = :company
            AND siv.item_no = :itemNumber
        ';

            $params = [
                [':company', $companyName, PDO::PARAM_STR],
                [':itemNumber', $itemNumber, PDO::PARAM_STR],
                [':languageCode', $languageCode, PDO::PARAM_STR],
            ];

            $keys = [
                'variant_code',
                'description',
            ];
            break;
        default:
            throw new ErrorException('Variant type [' . $variantType . '] unknown.');
            break;
    }

    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $resultArray = $pdo->getResultArray();


    $options = '';
    if (count($resultArray) > 0) {
        for ($i = 0; $i < count($resultArray); $i++) {
            $options = $options . '  <option value="' . htmlentities($resultArray[$i][$keys[0]]) . '"><span>' . htmlentities($resultArray[$i][$keys[1]]) . '</span></option>';

        }
    }

    echo $options;
} else {
    die();
}
