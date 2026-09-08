<?php
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
//load environment variables
$dotenv = new \Dotenv\Dotenv($rootDir.'/config/');
$dotenv->load();

require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop.config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/shop_functions.inc.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common/navconnect_functions.inc.php';

// Verbindung mit Datenbank herstellen
db_connect();

/**
 * ***************************************************************************
 * STEP 1: Alle Kategorien abholen. Nur die Erste ebene
 * ***************************************************************************
 */
$params = array(
	'rootOnly' => true
);
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'fetch_categories',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

/**
 * ***************************************************************************
 * STEP 2: Unterkategorien abholen
 * ***************************************************************************
 */

$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and in_use = 1 and category_level = 1 order by id asc";

$result = mysqli_query($GLOBALS['mysql_con'], $query);
$in_use_categories = array();

while ($row = mysqli_fetch_assoc($result)) {
	$in_use_categories[] = $row['category_code'];
}

foreach($in_use_categories as $cat) {
	$params = array(
		'parentID' => $cat,
	);
	$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
		'CRONUS AG',
		'B2C-DEMO',
		'DEU',
		2,
		'fetch_categories',
		'" . serialize($params) . "',
		" . time() . "
	)";
	@mysqli_query($GLOBALS['mysql_con'], $query);
}

/**
 * ***************************************************************************
 * STEP 3: Kategorie Merkmale auslesen
 * ***************************************************************************
 */
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'get_variant_attributes',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

/**
 * ***************************************************************************
 * STEP 3.1 (NUR EBAY): Zahlungsarten, Pruefen welche Kategorie Varianten erlaubt.
 * ***************************************************************************
 * 
 * Hinweis: Hier kann nur 1 Reqest pro Kategorie erfolgen. 
 * Deswegen empfiehlt sich Nur die Kategorie zu uebergeben, die man auch wirklich braucht.
 */
 
$params = array(
	'CategoryID' => "20585"
);
 
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'get_category_features',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

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
 * 
 * Ohne parameter wird die Standardfunktion angestossen: senden von Produkten und deren Basisdaten zu den Marketplaces
 */

$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'send_products',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

/**
 * ***************************************************************************
 * STEP 5: Bestellungen abholen
 * ***************************************************************************
 */
 
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'get_orders',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

/**
 * ***************************************************************************
 * STEP 6: Bestellungen abschliessen
 * ***************************************************************************
 * 
 */
 
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'complete_orders',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);

/**
 * ***************************************************************************
 * STEP 7: Positionen stornieren
 * ***************************************************************************
 * 
 */
 
$query = "INSERT INTO shop_marketplace_queue (company, shop_code, language_code, marketplace_type, operation, parameter, timestamp) VALUES (
	'CRONUS AG',
	'B2C-DEMO',
	'DEU',
	2,
	'cancel_order_items',
	'" . serialize($params) . "',
	" . time() . "
)";
@mysqli_query($GLOBALS['mysql_con'], $query);
 