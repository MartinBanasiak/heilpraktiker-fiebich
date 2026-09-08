<?php
$rootDir = dirname(dirname(dirname(__DIR__)));
require_once($rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
//load environment variables
$dotenv = new \Dotenv\Dotenv($rootDir.'/config/');
$dotenv->load();

/*require_once("../../../dc/common/common_functions.inc.php");
require_once((local_environment()) ? "../../../dc/dc.config.php" : "../../../dc/dc-server.config.php");
require_once("../shop.config.php");
require_once("../common/shop_functions.inc.php");
require_once("../common/navconnect_functions.inc.php");
require_once("../common/shop_functions.inc.php");

// Verbindung mit Datenbank herstellen
db_connect();

$shop            = get_shop('liedeco GmbH', 'AMAZON');
$GLOBALS['shop'] = $shop;

$shop_language   = get_shop_language('liedeco GmbH', 'AMAZON', 'DE');
$GLOBALS['shop_language'] = $shop_language;

$query = "SELECT shop_item.*, (SELECT 
			content 
		FROM 
			shop_item_description sid 
		WHERE 
			sid.company = shop_item.company 
		AND 
			sid.shop_code = shop_item.shop_code 
		AND 
			sid.language_code = shop_item.language_code 
		AND 
			sid.item_no = shop_item.item_no 
		AND 
			sid.marketplace_title = 1) AS marketplace_title FROM shop_item WHERE marketplace_update = 1 
			AND shop_item.language_code = 'DE'
			AND shop_item.company = 'liedeco GmbH'
			AND shop_item.shop_code = 'AMAZON'
			AND active = 1
		order by id asc
	";

$result = mysqli_query($GLOBALS['mysql_con'], $query);

$item = mysqli_fetch_assoc($result);
$brand = get_brand_name($item);
//var_dump($brand);
// die(); 

$timestamp = strtotime("2016-09-15T06:50:16.000Z");
echo date("Y-m-d H:i:s", $timestamp);


//$xmlDir = __DIR__."/xml/";
//$xml = $xmlDir . '50012313937_responses.xml';
//$resp = simplexml_load_file($xml);
//
//foreach($resp as $key => $xmlItem) {
//	var_dump($key);
//}
//
//die();

$params = array(
);
//
//$params = array(
//	'parentID' => 11700,
//);
//
//$params = array(
//	'jobId' => 50012224297,
//	'fileReferenceId' => 50014864137
//);

//a:3:{s:15:"fileReferenceId";s:11:"50014864327";s:5:"jobId";i:50012224397;s:11:"maxFileSize";i:15728640;}

//$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and in_use = 1 and category_level = 1 order by id asc";
//
//$result = mysqli_query($GLOBALS['mysql_con'], $query);
//$in_use_categories = array();
//
//while ($row = mysqli_fetch_assoc($result)) {
//	$in_use_categories[] = $row['category_code'];
//}
//
//foreach($in_use_categories as $cat) {
//	$params = array(
//		'category_code' => $cat,
//	);
//	$query = "INSERT INTO shop_marketplace_queue (marketplace_type, operation, parameter, timestamp) VALUES (
//		2,
//		'get_variant_attributes',
//		'" . serialize($params) . "',
//		" . time() . "
//	)";
//	
//	@mysqli_query($GLOBALS['mysql_con'], $query);
//}

//$params = array(
//	'category_code' => "20585",
//);
//$query = "INSERT INTO shop_marketplace_queue (marketplace_type, operation, parameter, timestamp) VALUES (
//	2,
//	'get_variant_attributes',
//	'" . serialize($params) . "',
//	" . time() . "
//)";
//
//@mysqli_query($GLOBALS['mysql_con'], $query);
//die();

//set_time_limit(0);
//$dom = new DOMDocument('1.0');
//for ($i=0; $i<=5000000; ++$i) {
//    $root = $dom->createElement('message');
//    $dom->appendChild($root);
//    $content = $dom->createElement('content');
//    $root->appendChild($content);
//    $content->appendChild($dom->createTextNode('Example content'));
//}
//
//$data = gzencode($dom->saveXML(), 5);
//$file = base64_encode($data);
//$fileSize = strlen($file);
//echo memory_get_usage() . "\n"; // 36640
//echo $fileSize;
//die();

//
//$xsdFile = __DIR__."/amazon/xsd/Product/Home.xsd";
//$productType = "Kitchen";
//	
//$attributes = array();
//$XSDDOC = new DOMDocument();
//$XSDDOC->preserveWhiteSpace = false;
//if ($XSDDOC->load($xsdFile)) {
//	
//	// Variant themes laden
//    $xsdpath = new DOMXPath($XSDDOC);
//    $attributeNodes = $xsdpath->query('//xsd:element[@name="' . $productType . '"]')->item(0);
//	$VariationData = $xsdpath->query('.//xsd:element[@name="VariationData"]', $attributeNodes)->item(0);
//	$elements = $xsdpath->query('.//xsd:element', $attributeNodes);
//	foreach($elements as $element) {
//		echo $element->getAttribute("name") . "<br />";
//	}
//	$themes = $xsdpath->query('.//xsd:enumeration', $parentage);
//	//$theme->getAttribute("value")
//
//	// variant attributes laden
//	$variationData= $xsdpath->query('.//xsd:element[@name="VariationData"]', $attributeNodes)->item(0);	
//	$variantAttributes = $xsdpath->query('.//xsd:element', $variationData);	
//
//}
//
//die("OK");

//$query = "SELECT width from shop_item where id = 59";
//$result = mysqli_query($GLOBALS['mysql_con'], $query);
//$row = mysqli_fetch_assoc($result);
//if($row['width'] > 0) {
//	echo $row['width'];
//}
//die();

//
//$array = array();
//		$array["Color"] = array();
//		$array["Size"] = array();
//		
//		$array["Color"]["Schwarz"] = true;
//		$array["Color"]["Schwarz1"] = true;
//		$array["Color"]["Schwarz2"] = true;
//		$array["Color"]["Schwarz5"] = true;
//		$array["Color"]["Schwarz6"] = true;
//		$array["Size"]["M"] = true;
//		$array["Size"]["XL"] = true;
//		$array["Size"]["XXL"] = true;
//		$array["Size"]["XXL2"] = true;
//		
//$testArray = array();
//foreach($array as $arraykey => $arrayValue) {
//	$testArray[$arraykey] = count($arrayValue);
//}
//		
//		
//$maxs = array_keys($testArray, max($testArray));
//echo "<pre>";		
//var_dump($maxs[0]);
//die();


//$params = array(
//	//'function' => 'update_relationship'
//);
//$params = array(
//	'func' => "send_products",
//	'maxFileSize' => 10000000000
//);
// 
//$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
//	'CRONUS AG',
//	'B2C-DEMO',
//	'DEU',
//	2,
//	'test_xml',
//	'" . serialize($params) . "',
//	" . time() . "
//)";
//@mysqli_query($GLOBALS['mysql_con'], $query);

/*
include_once "WorkerAbstract.php";
include_once "WorkerInterface.php";
require_once("ebay/EbayMarketplace.php");
$class = new EbayMarketplace("liedeco GmbH", "EBAY", "DE");
$class->test_xml();
die();

*/

/**
 * ***************************************************************************
 * STEP 4: Artikel Übertragung.
 * ***************************************************************************
 *
 * Mit der Funktion "send_products" werden die Artikel Übertragen bzw. aktualisiert.
 * Moegliche Parameter:
 * fuer ebay und amazon:
 * $params = array('function' => 'update_price')
 * $params = array('function' => 'update_inventory')
 *
 * fuer amazon weitere parameter:
 * $params = array('function' => 'update_products') // standard
 * $params = array('function' => 'update_images')
 * $params = array('function' => 'update_relationship')
 * //$params = array('function' => 'get_lowest_price', 'item_no' => 'SJ.043.060.02.SB')
 *
 * Ohne parameter wird die Standardfunktion angestossen: senden von Produkten und deren Basisdaten zu den Marketplaces
 */

/*$params = array(
    'item_no' => 'A04-10031'
);

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'liedeco GmbH',
	'AMAZON',
	'DE',
	1,
	'get_lowest_price',
	'" . serialize($params) . "',
	" . time() . "
)";*/
//A04-10031

$params = array(
    'function' => 'update_products'
);

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'liedeco GmbH',
	'AMAZON',
	'DE',
	1,
	'send_products',
	'" . serialize($params) . "',
	" . time() . "
)";

$params2 = array();

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'liedeco GmbH',
	'EBAY',
	'DE',
	2,
	'update_products',
	'" . serialize($params2) . "',
	" . time() . "
)";

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'argo Fulfillment',
	'0620',
	'DEU',
	1,
	'get_orders',
	'" . serialize($params2) . "',
	" . time() . "
)";



/*$params = array(
    'function' => 'update_inventory'
);

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'liedeco GmbH',
	'AMAZON',
	'DE',
	1,
	'send_products',
	'" . serialize($params) . "',
	" . time() . "
)";

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'liedeco GmbH',
	'EBAY-LIVE',
	'DE',
	2,
	'send_products',
	'" . serialize($params) . "',
	" . time() . "
)";*/

$params = array();

$params = array(
    //'parentID' => '361139011'
    'item_no' => 'A05-10530',
    'function' => 'update_products'
);

/*$params = array(
    'rootOnly' => true
);*/

$query = "
      INSERT INTO
          shop_marketplace_queue
      SET
	      company 			= 'argo Fulfillment',
		  shop_code 		= '0620',
		  language_code		= 'DEU',
		  marketplace_type  = '1',
		  operation 		= 'get_variant_attributes',
		  parameter 		= '" . serialize($params) . "',
		  timestamp 		= '" . time() . "'";

$query = "
      INSERT INTO
          shop_marketplace_queue
      SET
	      company 			= 'argo Fulfillment',
		  shop_code 		= '0620',
		  language_code		= 'DEU',
		  marketplace_type  = '1',
		  operation 		= 'fetch_categories',
		  parameter 		= '" . serialize($params) . "',
		  timestamp 		= '" . time() . "'";

/*$params = array(
    //'parentID' => '361139011'
    'item_no' => 'A05-10012',
    'function' => 'update_images'
);*/

/*$params = array(
    'function' => 'update_inventory'
);*/

/*$params = array(
    'rootOnly' => true
);*/

$params = array(
    'item_no' => 'A04-10031'
);

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'argo Fulfillment',
	'0420',
	'DEU',
	1,
	'get_variant_attributes',
	'" . serialize($params) . "',
	" . time() . "
)";

//echo $query;
//@mysqli_query($GLOBALS['mysql_con'], $query);