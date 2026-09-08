<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 01.08.2017
 * Time: 13:13
 */

//--Setup--
set_time_limit(0);
ini_set('max_execution_time',0);
$startTime = microtime(true);
$batchLimit = 100;

$rootDir = dirname(__DIR__,2);
include $rootDir . '/vendor/autoload.php';

$configDir = $rootDir . '/config';
$envFilePath = $configDir . '/.env';
if (file_exists($envFilePath) && is_file($envFilePath) && is_readable($envFilePath)) {
    $dotEnv = new \Dotenv\Dotenv($configDir);
    $dotEnv->load();
}

//Instantiate PDO
$dsn = 'mysql:host=' . getenv('MAIN_MYSQL_DB_HOST') . ';dbname=' . getenv('MAIN_MYSQL_DB_SCHEMA');
$username = getenv('MAIN_MYSQL_DB_USER');
$pass = getenv('MAIN_MYSQL_DB_PASS');
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
);
$pdo = new PDO($dsn,$username,$pass,$options);

//Instantiate Elasticsearch Client
$elasticUser = getenv('ELASTIC_USER');
$elasticPass = getenv('ELASTIC_PASS');
$elasticBaseURL = getenv('ELASTIC_BASE_URL');
$elasticPort = getenv('ELASTIC_PORT');
$elasticIndexName = getenv('ELASTIC_INDEX');
$elasticItemType = getenv('ELASTIC_ITEM_TYPE');

$hosts = [
  [
      'host' => $elasticBaseURL,
      'port' => $elasticPort,
      'user' => $elasticUser,
      'pass' => $elasticPass
  ]
];
$elasticClient = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();


//--Execute--
//Iterate over Items, collect attributes & categories, send in batches
//Compare to last successfully sent version and create, skip or send delta-update in bulk
$responses = [];
$noItemsInBody = 0;
$newItems = [];
$params = [
    'body' => [],
];

$itemQuery = '
SELECT 
  shop_item.id,
  company,
  shop_code,
  language_code,
  item_no,
  parent_item_no,
  description,
  summary,
  width,
  length,
  height,
  volume,
  weight,
  order_ranking,
  retail_price,
  base_price,
  vendor_no,
  vendor_name,
  inventory,
  active,
  validity_from AS \'starting_date\',
  validity_to AS \'ending_date\',
  shop_item_elasticsearch.last_version_json AS \'last_version_json\'
FROM shop_item
LEFT JOIN shop_item_elasticsearch ON (
  shop_item_elasticsearch.id = shop_item.id
)';

$itemStmt = $pdo->query($itemQuery);
$itemStmt->setFetchMode(PDO::FETCH_ASSOC);
$noOfRecords = $itemStmt->rowCount();
echo "No of item Records: $noOfRecords" . PHP_EOL;

while ($item = $itemStmt->fetch()) {
    $lastVersion = json_decode($item['last_version_json'] ?? '',true);
    unset($item['last_version_json']); //Unset in array to get clean item-array for diff
    $attributes = get_attribute_array_for_item($pdo,$item['company'],$item['shop_code'],$item['language_code'],$item['item_no'],$item['parent_item_no']);
    $categories = getCategoryData($pdo,$item['company'],$item['shop_code'],$item['language_code'],$item['item_no'],$item['parent_item_no']);
    $item['categories'] = (array)(array_values($categories['filters']));
    $item['category_sortings'] = (array)$categories['sortings'];

    if (empty($item['categories'])) {
        unset($item['categories']);
    }

    if (empty($item['category_sortings'])) {
        unset($item['category_sortings']);
    }

    while ($attributeArr = array_pop($attributes)) {
        $key = $attributeArr['code'];
        $dataType = (int)$attributeArr['data_type'];
        $values = (array)$attributeArr['values'];
        if ($dataType === 4) {
            $dataType = 0;
        }
        switch ($dataType) {
            case 0:
                array_walk($values,function(&$el){$el = (string)$el;});
                break;
            case 1:
                array_walk($values,function(&$el){$el = (int)$el;});
                break;
            case 2:
                array_walk($values,function(&$el){$el = (float)$el;});
                break;
            case 3:
                array_walk($values,function(&$el){$el = (bool)$el;});
                break;
        }
        $item['attributes'][$key] = $values;
        $item[$key] = $values;
    }
    $item['id'] = (int)$item['id'];
    $item['inventory'] = (float)$item['inventory'];
    $item['attributes']['inventory'] = $item['inventory'];
    $item['width'] = (float)$item['width'];
    $item['attributes']['width'] = $item['width'];
    $item['length'] = (float)$item['length'];
    $item['attributes']['length'] = $item['length'];
    $item['height'] = (float)$item['height'];
    $item['attributes']['height'] = $item['height'];
    $item['volume'] = (float)$item['volume'];
    $item['attributes']['volume'] = $item['volume'];
    $item['order_ranking'] = (int)$item['order_ranking'];
    $item['weight'] = (float)$item['weight'];
    $item['attributes']['weight'] = $item['weight'];
    $item['base_price'] = (float)$item['base_price'];
    $item['attributes']['base_price'] = $item['base_price'];
    $item['retail_price'] = (float)$item['retail_price'];
    $item['attributes']['retail_price'] = $item['retail_price'];
    $item['attributes']['vendor_no'] = $item['vendor_no'];
    if (strpos($item['starting_date'],'0000-00-00') !== false) {
        $item['starting_date'] = '1960-01-01';
    }
    if (strpos($item['ending_date'],'0000-00-00') !== false) {
        $item['ending_date'] = '2100-01-01';
    }
    if (empty($lastVersion)) {
        $params['body'][] = [
            'index' => [
                '_index' => $elasticIndexName,
                '_type' => $elasticItemType,
                '_id' => $item['id'],
            ]
        ];
        $params['body'][] = $item;
        $newItems[$item['id']] = $item;
        $noItemsInBody++;
    } else {
        $isSameAsLastVersion = false;
        $diff = diff_items($item,$lastVersion,$isSameAsLastVersion);
        if (!$isSameAsLastVersion) {
             $params['body'][] = [
                 'update' => [
                     '_index' => $elasticIndexName,
                     '_type' => $elasticItemType,
                     '_id' => $item['id'],
                 ]
             ];

             $params['body'][] = [
                 'doc' => $diff
             ];
            $newItems[(string)$item['id']] = $item;
            $noItemsInBody++;
        }

    }

    if ($noItemsInBody === $batchLimit) {
        $responses[] = $elasticClient->bulk($params);
        $params = [
            'body' => [],
        ];
        $noItemsInBody = 0;
    }
}

if (count($params['body']) > 0) {
    $responses[] = $elasticClient->bulk($params);
}

$countSuccessful = 0;
$countErrors = 0;
foreach($responses as $response) {

    //file_put_contents('elastic_log.txt',print_r($response,1) . PHP_EOL . PHP_EOL,FILE_APPEND);
    $resonseItems = $response['items'] ?? [];
    foreach ($resonseItems as $itemArr) {
        $key = '';
        if (array_key_exists('index',$itemArr)) {
            $key = 'index';
        } elseif (array_key_exists('update',$itemArr)){
            $key = 'update';
        }
        $id = $itemArr[$key]['_id'] ?? 0;

        $status = (int)($itemArr[$key]['status'] ?? 0);
        if ($status >= 200 && $status < 300) {
            $countSuccessful++;
            updateItemLastElasticVersion($pdo,$newItems[(string)$id]);
        } else {
            $countErrors++;
        }
    }
}
$duration = microtime(true) - $startTime;
echo "Duration: $duration s | Successes: $countSuccessful | Errors: $countErrors" . PHP_EOL;


function updateItemLastElasticVersion(PDO $pdo, array $itemVersion)
{
    $query = '
    INSERT INTO
    shop_item_elasticsearch
    SET 
      id = :id,
      last_version_json = :last_version_json
    ON DUPLICATE KEY UPDATE
      last_version_json = :last_version_json
    ';

    $json = json_encode($itemVersion,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id',(int)$itemVersion['id'],PDO::PARAM_STR);
    $stmt->bindValue(':last_version_json',$json,PDO::PARAM_STR);
    $isUpdated = $stmt->execute();
    return $isUpdated;

}

function getCategoryData(PDO $pdo, string $company, string $shopCode, string $languageCode, string $itemNo, string $parentItemNo) {


    static $memo;
    static $query = '
        SELECT DISTINCT category_shop_code,category_line_no,sorting 
        FROM shop_item_has_category
        WHERE 
              company = :company
          AND shop_code = :shop_code
          AND language_code = :language_code
          AND (item_no = :item_no OR (:parent_item_no != \'\' AND item_no = :parent_item_no))
    ';
    if (null === $memo) {
        $memo = [];
    }

    $paramHash = md5($company . '|' . $shopCode . '|' . $languageCode . '|' . $itemNo . '|' . $parentItemNo);
    if (array_key_exists($paramHash,$memo)) {
        return $memo[$paramHash];
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':company',$company,PDO::PARAM_STR);
    $stmt->bindValue(':shop_code',$shopCode,PDO::PARAM_STR);
    $stmt->bindValue(':language_code',$languageCode,PDO::PARAM_STR);
    $stmt->bindValue(':item_no',$itemNo,PDO::PARAM_STR);
    $stmt->bindValue(':parent_item_no',$parentItemNo,PDO::PARAM_STR);
    $stmt->execute();
    $resultArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categories = [];
    $categories['filters'] = [];
    $categories['sortings'] = [];
    foreach ($resultArr as $resultRow) {
        $catVal = $resultRow['category_shop_code'] . '|' . $resultRow['category_line_no'];
        $sortVal = $resultRow['category_shop_code'] . '|' . $resultRow['category_line_no'] . '|' . $resultRow['sorting'];
        $categories['filters'][] = $catVal;
        $categories['sortings'][$catVal] = $sortVal;
    }
    $memo[$paramHash] = $categories;
    return $categories;
}


function get_variant_item_nos(PDO $pdo, string $company, string $shop_code, string $language_code, string $item_no)
{
    static $memo;
    static $query =
        '
        SELECT linked_item_no
        FROM shop_item_link
        WHERE type = 0
        AND company = :company
        AND shop_code = :shop_code
        AND language_code = :language_code
        AND item_no = :item_no
        ';

    if (null === $memo) {
        $memo = [];
    }

    $paramHash = md5($company . '|' . $shop_code . '|' . $language_code . '|' . $item_no);
    if (array_key_exists($paramHash,$memo)) {
        return $memo[$paramHash];
    }


    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':company',$company,PDO::PARAM_STR);
    $stmt->bindValue(':shop_code',$shop_code,PDO::PARAM_STR);
    $stmt->bindValue(':language_code',$language_code,PDO::PARAM_STR);
    $stmt->bindValue(':item_no',$item_no,PDO::PARAM_STR);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();
    $nos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nos[] = $row['linked_item_no'];
    }
    $memo[$paramHash] = $nos;
    return $nos;

}

function get_attribute_array_for_item(PDO $pdo, string $company, string $item_shop_code, string $language_code, string $item_no,string $parent_item_no) {
    static $memo;
    $query = '
        SELECT DISTINCT
        shop_attribute_link.no AS \'item_no\', 
		shop_attribute.code AS \'attribute_code\',
        shop_attribute.data_type AS \'data_type\',
        CASE 
            WHEN shop_attribute.data_type = 0 THEN shop_attribute_option.description            
            WHEN shop_attribute.data_type = 1 THEN shop_attribute_link.value_integer 
            WHEN shop_attribute.data_type = 2 THEN shop_attribute_link.value_decimal 
            WHEN shop_attribute.data_type = 3 THEN shop_attribute_link.value_bool 
            WHEN shop_attribute.data_type = 4 THEN shop_attribute_link.value_text 
        END AS \'attribute_value\'
    FROM
      shop_attribute_link
    INNER JOIN shop_attribute ON (
          shop_attribute.company = shop_attribute_link.company      
      AND shop_attribute.code = shop_attribute_link.attribute_code
    )
    LEFT JOIN shop_attribute_option ON (
            shop_attribute_option.company = shop_attribute.company
        AND shop_attribute_option.attribute_code = shop_attribute.code
        AND shop_attribute_option.code = shop_attribute_link.value_option 
    )
    WHERE
          shop_attribute_link.company = \'%company%\'
      AND shop_attribute_link.type = 0
      AND (
                (\'%parent_item_no%\' = \'\' AND shop_attribute_link.no IN (%in_stmt%))
            OR  (\'%parent_item_no%\' != \'\' AND (shop_attribute_link.no = \'%parent_item_no%\' OR shop_attribute_link.no = \'%item_no%\'))
      )          
';
    //@TODO: NOT WORKING! FIX!

    if (null === $memo) {
        $memo = [];
    }


    $paramHash = md5($company . '|' . $item_no . '|' . $parent_item_no);
    if (array_key_exists($paramHash,$memo)) {
        return $memo[$paramHash];
    }
    $itemNos = [];
    if (!$parent_item_no) {
        $itemNos = get_variant_item_nos($pdo, $company, $item_shop_code, $language_code, $item_no);
    }
    $itemNos[] = $item_no;
    $inStmtPart = '';
    $z = 0;
    foreach ($itemNos as $childItemNo)
    {
        if ($z > 0) {
            $inStmtPart .= ',';
        }
        $inStmtPart .= "'$childItemNo'";
        $z++;
    }
    $search = ['%in_stmt%','%company%','%item_no%','%parent_item_no%'];
    $replace = [$inStmtPart,$company,$item_no,$parent_item_no];
    $locQuery = str_replace($search,$replace,$query);
    try {
        $stmt = $pdo->query($locQuery);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        die(print_r($pdo->errorInfo(),1));
    }
    $attributes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $key = $row['attribute_code'];
        $val = $row['attribute_value'];
        $type = $row['data_type'];
        if (array_key_exists($key,$attributes) && !in_array($val,$attributes[$key]['values'])) {
            $attributes[$key]['values'][] = $val;
        } else {
            $attributes[$key]['values'] = [$val];
            $attributes[$key]['data_type'] = $type;
            $attributes[$key]['code'] = $key;
        }
    }

    $memo[$paramHash] = $attributes;
    return $attributes;
}

function diff_items(array $newItem, array $oldItem,bool &$isSame) {
    $isSame = $newItem === $oldItem;
    if ($isSame || empty($oldItem)) {
        return $newItem;
    }
    $diff = [];

    foreach ($newItem as $newKey => $newValue) {
        if (array_key_exists($newKey,$oldItem)) {
            if (is_array($newValue)) {
                $isSameInner = false;
                $valDiff = diff_items($newValue,(array)$oldItem[$newKey],$isSameInner);
                if (!$isSameInner) {
                    $diff[$newKey] = $valDiff;
                }
            } else {
                if ($newValue !== $oldItem[$newKey]) {
                    $diff[$newKey] = $newValue;
                }
            }
        }
    }
    return $diff;
}