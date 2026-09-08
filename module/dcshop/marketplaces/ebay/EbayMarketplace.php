 <?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "MarketplaceAbstract.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "MarketplaceInterface.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "marketplace_functions.inc.php");

require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayFinding.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayTrading.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayBulk.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayFiletransfer.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayPostOrder.php';

class EbayMarketplace extends MarketplaceAbstract implements MarketplaceInterface {

	/**
	 * definiert intern die ID vom marketplace
	 * 
	 * @var int
	 * @static
	 */
	const MARKETPLACE_TYPE = 2;

	const ATTRIBUTE_TYPE = 2;
	
	/**
	 * definiert einen suffix fuer mutterartikel bei shop_shop variant_typ = 1
	 * Wenn die erste Variante gleichzeitig ein Parent ist
	 * 
	 * @var string
	 */
	const PARENTITEM_SUFFIX = 'm';
	
	/**
	 * Schnittstelle zur jeweiligen ebay Api
	 * 
	 */

	// JS 13.12.2016
	const ITEM_DATA_MANAGEMENT_SOURCE_TABLE_DESCRIPTION = 'Webshop Item Description';
	const ITEM_DATA_MANAGEMENT_SOURCE_TABLE_FILE = 'Webshop Item File';
	const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_LONG = 0;
	const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_SEARCH = 2;
	const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_SHORT = 3;
	const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_FILE_PICTURE = 0;

	protected $ebayCall = null;
	
	protected $bulkXml;
	
	protected $bulkXmlFilename;
	
	protected $variation_pictures_tag = null;
	
	protected function setBulkXmlFilename($bulkXmlFile) {
		$this->bulkXmlFilename = $bulkXmlFile;
	}
	
	protected function insertMessageToBulk($tagName) {
		$this->bulkXml->formatOutput = true;
		$this->bulkXml->preserveWhiteSpace = false;
		$apiNode = $this->bulkXml->getElementsByTagName($tagName);
		
		file_put_contents($this->bulkXmlFilename, $this->bulkXml->saveXML($apiNode->item(0)), FILE_APPEND);
	}
	
	public function get_session_id($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetSessionID");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;

		$config_row = $this->ebayCall->get_config_row();
	
		$root->appendChild(
			$dom->createElement("RuName", $config_row['runame'])
		);
		
		$this->callProcess();
	}
	
	public function get_session_id_submission_result($result_file, $parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$config_row = $this->ebayCall->get_config_row();
		
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);

		$sessionId = $resp->SessionID;
		
		update_ebay_token_url($config_row['token_url'], $sessionId, $config_row['runame'], $config_row['environment']);
	}
	
	public function fetch_user_token($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("FetchToken");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
	
		$config_row = $this->ebayCall->get_config_row();
		$session_id = get_ebay_session_id($config_row['environment']);
	
		$root->appendChild(
			$dom->createElement("SessionID", $session_id)
		);
		
		$this->callProcess();
	}
	
	public function fetch_user_token_submission_result($result_file, $parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$config_row = $this->ebayCall->get_config_row();
		
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);

		$user_token = $resp->eBayAuthToken;
		
		update_ebay_user_auth_token($user_token, $config_row['environment']);
	}
	
	public function get_orders($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetOrders");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
	
		$root->appendChild(
			$dom->createElement("NumberOfDays", "21")
		);
		
		$root->appendChild(
			$dom->createElement("DetailLevel", "ReturnAll")
		);
		
		$page = 1;
		if(isset($parameter['PageNumber'])) {
			$page = $parameter['PageNumber'];
		}
		
		$pagination = $root->appendChild(
			$dom->createElement("Pagination")
		);
		
		$pagination->appendChild(
			$dom->createElement("EntriesPerPage", 100)
		);
		
		$pagination->appendChild(
			$dom->createElement("PageNumber", $page)
		);
		
		$this->callProcess();
	}

	public function get_orders_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);	
		
		foreach($resp->OrderArray->Order as $order) {
			$this->handle_order($order);
		}
		
		// wenn noch mehr besellungen vorhanden sind, dann weiteren request starten
		if((string)$resp->HasMoreOrders == "true") {
			$lastpage = 1;
			if(isset($parameter['PageNumber'])) {
				$lastpage = (int)$parameter['PageNumber'];
			}
			
			$param = array(
				'PageNumber' => $lastpage+1
			);
	
			$this->new_queue(self::MARKETPLACE_TYPE, "get_orders", $param, 0, false);
		}
		
		$this->setCallOk(true);
	}
	
	public function cancel_orders($parameter = array()) {
		$this->ebayCall = new EbayPostOrder();
		$this->ebayCall->setCallname("cancellation");
		$this->ebayCall->setHttpMethod("POST");
		$param = array("legacyOrderId" => $parameter['marketplace_order']);
		$this->ebayCall->setParam($param);
		
		$this->setJsonFilename();
		$this->callProcess();
	}
	
	public function cancel_orders_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$data = json_decode(file_get_contents($filename));
		
		// Json verarbeitung
	}
	
	public function cancel_order_items($parameter = array()) {		
		$order_items = get_all_order_items_to_cancel();
		if(@mysqli_num_rows($order_items) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		// erste order nehmen, nur eine order pro ebay call
		$order_item = mysqli_fetch_assoc($order_items);
		
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("AddDispute");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$query = "UPDATE shop_sales_line set marketplace_update = 0 where id = " . $order_item['id'];
		@mysqli_query($GLOBALS['mysql_con'], $query);
	
		$root->appendChild(
			$dom->createElement("DisputeExplanation", $order_item['marketplace_dispute_explanation'])
		);
		
		$root->appendChild(
			$dom->createElement("DisputeReason", $order_item['marketplace_dispute_reason']) 
		);
		
		$root->appendChild(
			$dom->createElement("OrderLineItemID", $order_item['marketplace_line_id'])
		);
		
		$root->appendChild(
			$dom->createElement("MessageID", $order_item['id']) 
		);
		
		$this->callProcess();
	}
	
	public function cancel_order_items_submission_result($result_file, $parameter = array()) {
		$order_items = get_all_order_items_to_cancel();
		if(@mysqli_num_rows($order_items) > 0) {
			$this->new_queue(self::MARKETPLACE_TYPE, "cancel_order_items", $parameter, 0, false);
		}
	}
	
	public function complete_orders($parameter = array()) {
		$shipments = get_shipments();
		if(@mysqli_num_rows($shipments) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("CompleteSale");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$order = null;
		while($shipment = mysqli_fetch_assoc($shipments)) {
			$order = get_order($shipment['webshop_order_no']);
			
			if($order !== null) {
				break;
			}
		}
		
		if($order === null) {
			$this->setCallOk(true);
			return;
		}
		
		$query = "UPDATE shop_sales_shipment_header set marketplace_update = 0 where id = " . $shipment['id'];
		@mysqli_query($GLOBALS['mysql_con'], $query);
		
		$root->appendChild(
			$dom->createElement("OrderID", $order['marketplace_order']) 
		);
		
		$root->appendChild(
			$dom->createElement("Shipped", "true") 
		);
		
		$root->appendChild(
			$dom->createElement("MessageID", $order['id']) 
		);
		
		$this->callProcess();
	}
	
	public function complete_orders_submission_result($result_file, $parameter = array()) {
		$shipments = get_shipments();
		if(@mysqli_num_rows($shipments) > 0) {
			$this->new_queue(self::MARKETPLACE_TYPE, "complete_orders", $parameter, 0, false);
		}
	}
	
	public function update_order_byuer($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetUser");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
	
		$root->appendChild(
			$dom->createElement("UserID", $parameter['BuyerUserID'])
		);
		
		$root->appendChild(
			$dom->createElement("ItemID", $parameter['ItemID']) 
		);
		
		$root->appendChild(
			$dom->createElement("DetailLevel", "ReturnAll")
		);
		
		$this->callProcess();
	}
	
	public function update_order_byuer_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);	
		
		$sales_header_id = (int)$parameter['shop_sales_header_id'];
		
		$query = "UPDATE shop_sales_header SET 
				bill_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->Name) . "',
			  	bill_to_address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->Street) . "',
			  	bill_to_address_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->Street1) . "',
			  	bill_to_post_code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->PostalCode) . "',
			  	bill_to_city = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->CityName) . "',
			  	bill_to_country = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $resp->User->RegistrationAddress->Country) . "',
				update_insert = 1
			where id = $sales_header_id
		";//was 1
		
		@mysqli_query($GLOBALS['mysql_con'], $query);

		$query = "UPDATE shop_sales_line set update_insert = 1 where shop_sales_header_id = " . $sales_header_id;
		@mysqli_query($GLOBALS['mysql_con'], $query);
		
		$this->setCallOk(true);
	}
	
	/**
	 * Ein Job bei Bulk Data exchange abbrechen.
	 * Es kann nur ein Job pro service laufen (z.b. ein job fuer AddFixedPriceItem)
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function abort_job($parameter = array()) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("abortJob");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$item = $root->appendChild(
			$dom->createElement("jobId", $parameter['jobId'])
		);
		
		$this->callProcess();
	}
	
	/**
	 * Job aus der datenbank loeschen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function abort_job_submission_result($result_file, $parameter = array()) {
		delete_bulk_job($parameter['jobId']);
	}
	
	/**
	 * Ein Job bei Bulk Data exchange abbrechen.
	 * Es kann nur ein Job pro service laufen (z.b. ein job fuer AddFixedPriceItem)
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function get_ebay_jobs($parameter = array()) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("getJobs");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$this->callProcess();
	}
	
	/**
	 * Job aus der datenbank loeschen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function get_ebay_jobs_submission_result($result_file, $parameter = array()) {
		
	}
	
	/**
	 * Ein Job bei Bulk Data exchange abbrechen.
	 * Es kann nur ein Job pro service laufen (z.b. ein job fuer AddFixedPriceItem)
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function get_ebay_job_status($parameter = array()) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("getJobStatus");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("jobId", $parameter['jobId'])
		);
		
		$this->callProcess();
	}
	
	/**
	 * Job aus der datenbank loeschen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function get_ebay_job_status_submission_result($result_file, $parameter = array()) {
		
	}
	
	/**
	 * Produkte bei ebay aktualisieren.
	 * Hier wird erst mal ein Job bei Bulk Data Exchange genriert
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function update_products($parameter = array()) {
		
		$request = $this->get_request_name($parameter, "ReviseFixedPriceItem");
		
		// wenn parameter item_no ist vorhanden dann einen direkten call absetzen
		if(isset($parameter['item_no'])) {
			$this->items_result = get_item_result($parameter['item_no']);
			$item = mysqli_fetch_assoc($this->items_result);
			
			$parameter['request'] = $request;
			$this->createBulkXml($parameter);
			$this->callProcess();
			return;
		}
		
		// pruefen ob es artikel zum updaten gibt.
		$items = get_all_existing_items();

		if(@mysqli_num_rows($items) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		// die Bulk verarbeitung erst mal abschalten und einzelne calls machen.
		// bulk sollte nur als massenabgleich z.b. nachts gemacht werden
		
		// erstes gefundendes item lesen
		$item = mysqli_fetch_assoc($items);
		

        $updateParameter = $parameter;
        $updateParameter['item_no'] = $item['item_no'];
        $updateParameter['request'] = $request;
        $this->createBulkXml($updateParameter);
        $this->callProcess();

        $this->new_queue(self::MARKETPLACE_TYPE, "update_products", $parameter, 0, false);


		return; //--> keine Bulkverarbeitung
		
		// pruefen ob der job bereits noch lauft
		$bulkJobResult = active_bulk_job($request);
		if (@mysqli_num_rows($bulkJobResult) > 0) {
			$this->setRunAgain(true);
			return;
		}
		
		insert_bulk_job($request);
		
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("createUploadJob");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("uploadJobType", $request)
		);
		
		$root->appendChild(
			$dom->createElement("UUID", uniqid('', true))
		);
		
		$this->callProcess();
	}
	
	/**
	 * Nachdem ein job erstellt wurde, muss eine Datei zu diesem Job hochgeladen werden
	 * Dazu ein Auftrag in der queue mit den ersellten Daten von Ebay hinzufuegen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function update_products_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);
		
		$request = $this->get_request_name($parameter, "ReviseFixedPriceItem");
		$this->handle_downloaded_result_row($request, $resp);
		return;
		
		// Fehlgeschlagen z.b. weil es nur ein job mit AddFixedPriceItem geben kann.
		// @TODO ess muss noch ein Reorg geschrieben werden, der offene jobs abbbricht die aelter sind als 12 Stunden
		if($resp->ack == 'Failure') {
			$this->setCallOk(false);
			$this->setErrorMessage($resp->errorMessage->message);
			return;
		}
		
		$request = $this->get_request_name($parameter, "ReviseFixedPriceItem");
		
		update_bulk_job($request, (int)$resp->jobId);
		
		$param = array(
			'fileReferenceId' => (int)$resp->fileReferenceId,
			'jobId' => (int)$resp->jobId,
			'maxFileSize' => (int)$resp->maxFileSize,
			'operation' => 'update_products',
			'request' => $request
		);
		
		if(isset($parameter['function'])) {
			$param['function'] = $parameter['function'];
		}

		$this->new_queue(self::MARKETPLACE_TYPE, "upload_file", $param, 0, false);
	}
	
	/**
	 * Mehrere Produkte auf einmal zu ebay senden.
	 * Hier wird erst mal ein Job bei Bulk Data Exchange genriert
	 * 
	 * Vorhandene Produkte brauchen einen extra Job anlauf
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function send_products($parameter = array()) {

	    $request = $this->get_request_name($parameter, "AddFixedPriceItem");

		if($request != "AddFixedPriceItem") {
			$this->new_queue(self::MARKETPLACE_TYPE, "update_products", $parameter, 0, false);
			$this->setCallOk(true);
			return;
		}
		
		// wenn parameter item_no ist vorhanden dann einen direkten call absetzen
		if(isset($parameter['item_no'])) {
			$this->items_result = get_item_result($parameter['item_no']);
			$item = mysqli_fetch_assoc($this->items_result);

			// Artikel existiert bereits und darf mit diesem job nicht mehr bearbeitet werden
			if($item['marketplace_last_update_per_shop'] != '0000-00-00 00:00:00') {
				$this->new_queue(self::MARKETPLACE_TYPE, "update_products", $parameter, 0, false);
				$this->setCallOk(true);
				return;
			}

			$parameter['request'] = $request;
			$this->createBulkXml($parameter);
			$this->callProcess();
			return;
		}
		
		//$this->new_queue(self::MARKETPLACE_TYPE, "update_products", $parameter, 0, false);
		
		// pruefen ob es artikel zum hinzufuegen gibt
		$items = get_all_new_items();
		if(@mysqli_num_rows($items) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		// die Bulk verarbeitung erst mal abschalten und einzelne calls machen.
		// bulk sollte nur als massenabgleich z.b. nachts gemacht werden
		
		// erstes gefundendes item lesen
		$item = mysqli_fetch_assoc($items);
		
		$updateParameter = $parameter;
		$updateParameter['item_no'] = $item['item_no'];
		$updateParameter['request'] = $request;
		$this->createBulkXml($updateParameter);
		$this->callProcess();

		// nochmal ausfuehren und naechsten Artikel bearbeiten
		$this->new_queue(self::MARKETPLACE_TYPE, "send_products", $parameter, 0, false);

		return; //--> keine Bulkverarbeitung
		
		// pruefen ob der job bereits noch lauft
		$bulkJobResult = active_bulk_job("AddFixedPriceItem");
		if (@mysqli_num_rows($bulkJobResult) > 0) {
			$this->setRunAgain(true);
			return;
		}
		
		insert_bulk_job("AddFixedPriceItem");
		
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("createUploadJob");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("uploadJobType", "AddFixedPriceItem")
		);
		
		$root->appendChild(
			$dom->createElement("UUID", uniqid('', true))
		);
		
		$this->callProcess();
	}
	
	/**
	 * Nachdem ein job erstellt wurde, muss eine Datei zu diesem Job hochgeladen werden
	 * Dazu ein Auftrag in der queue mit den ersellten Daten von Ebay hinzufuegen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function send_products_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);

		$this->handle_downloaded_result_row("AddFixedPriceItem", $resp);
		return;
		
		// Fehlgeschlagen z.b. weil es nur ein job mit AddFixedPriceItem geben kann.
		// @TODO ess muss noch ein Reorg geschrieben werden, der offene jobs abbbricht die aelter sind als 12 Stunden
		if($resp->ack == 'Failure') {
			$this->setCallOk(false);
			$this->setErrorMessage($resp->errorMessage->message);
			return;
		}
		
		update_bulk_job("AddFixedPriceItem", (int)$resp->jobId);
		
		$param = array(
			'fileReferenceId' => (int)$resp->fileReferenceId,
			'jobId' => (int)$resp->jobId,
			'maxFileSize' => (int)$resp->maxFileSize,
			'operation' => 'send_products',
			'request' => 'AddFixedPriceItem'
		);
		
		$this->new_queue(self::MARKETPLACE_TYPE, "upload_file", $param, 0, false);
	}
	
	/**
	 * Gibt die aktuell gelisteten Produkte aus
	 * 
	 */
	public function get_listings($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetSellerList");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("EndTimeFrom", $this->genTime("-1 day"))
		);
		
		$root->appendChild(
			$dom->createElement("EndTimeTo", $this->genTime("+118 days"))
		);
		
//		$root->appendChild(
//			$dom->createElement("GranularityLevel", "Medium")
//		);
		
		$page = 1;
		if(isset($parameter['PageNumber'])) {
			$page = $parameter['PageNumber'];
		}
		
		$pagination = $root->appendChild(
			$dom->createElement("Pagination")
		);
		
			$pagination->appendChild(
				$dom->createElement("EntriesPerPage", 200)
			);
			
			$pagination->appendChild(
				$dom->createElement("PageNumber", $page)
			);
		
		$root->appendChild(
			$dom->createElement("IncludeVariations", "true")
		);
		
		$this->callProcess();
	}
	
	/**
	 * Verarbeitet die aufgelisteten Produkte
	 * 
	 */
	public function get_listings_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);	
		
		if((int)$resp->PageNumber == 1) {
			$query = "DELETE from shop_marketplace_items 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		foreach($resp->ItemArray->Item as $item) {
			$this->handle_active_listing($item);
		}
		
		// wenn noch mehr besellungen vorhanden sind, dann weiteren request starten
		if((string)$resp->HasMoreItems == "true") {
			$lastpage = 1;
			if(isset($parameter['PageNumber'])) {
				$lastpage = (int)$parameter['PageNumber'];
			}
			
			$param = array(
				'PageNumber' => $lastpage+1
			);
	
			$this->new_queue(self::MARKETPLACE_TYPE, "get_listings", $param, 0, false);
		} else {
			// pruefen ob items im marketplace nicht mehr existieren aber im webshop als "im marketplace" gekennzeichnet sind
			$query = "SELECT * FROM shop_marketplace_item_update where 
				company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "' 
			  	and item_shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	and item_language_code = '" . $GLOBALS['shop_language']['code'] . "'
				and marketplace_last_update != '0000-00-00 00:00:00' 
				and item_no not in (select item_no from shop_marketplace_items where 
					company = '" . $GLOBALS['shop']['company'] . "'
				  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
				  	and language_code = '" . $GLOBALS['shop_language']['code'] . "' 
				)
			";
			
			$result = mysqli_query($GLOBALS['mysql_con'], $query);
			
			while ($row = mysqli_fetch_assoc($result)) {
				$query = "UPDATE shop_marketplace_item_update set marketplace_last_update = '0000-00-00 00:00:00' where id = " . $row['id'];
				mysqli_query($GLOBALS['mysql_con'], $query);
			}	
			
			// pruefen ob items im marketplace existieren aber im webshop noch nicht als "im marketplace gekennzeichnet sind";
			$query = "SELECT * FROM shop_marketplace_item_update where 
				company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "' 
			  	and item_shop_code = '" . $GLOBALS['shop']['item_source'] . "'
			  	and item_language_code = '" . $GLOBALS['shop_language']['code'] . "' 
			  	
				and marketplace_last_update = '0000-00-00 00:00:00' 
				and item_no in (select item_no from shop_marketplace_items where 
					company = '" . $GLOBALS['shop']['company'] . "'
				  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
				  	and language_code = '" . $GLOBALS['shop_language']['code'] . "' 
				)
			";
			
			$result = mysqli_query($GLOBALS['mysql_con'], $query);
			
			while ($row = mysqli_fetch_assoc($result)) {
				$query = "UPDATE shop_marketplace_item_update set marketplace_last_update = NOW() where id = " . $row['id'];
				mysqli_query($GLOBALS['mysql_con'], $query);
			}
		}
		
		$this->setCallOk(true);
	}
	
	/**
	 * Liest Daten eines Artikels fuer den Import aus.
	 * 
	 */
	public function get_item_import($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetItem");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$query = "SELECT item_id FROM shop_marketplace_items_import where 
				company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
				LIMIT 1 
		";
		$result = mysqli_query($GLOBALS['mysql_con'], $query);
		if(@mysqli_num_rows($result) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		$row = mysqli_fetch_assoc($result);

		$root->appendChild(
			$dom->createElement("ItemID", $row['item_id'])
		);
		
		$root->appendChild(
			$dom->createElement("DetailLevel", "ItemReturnDescription")
		);
		
		$root->appendChild(
			$dom->createElement("IncludeItemSpecifics", "true")
		);
		
		$this->callProcess();
		
		$query = "DELETE FROM shop_marketplace_items_import where 
				company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
				and item_id = '" . $row['item_id'] . "'";
		@mysqli_query($GLOBALS['mysql_con'], $query);
		
		$this->new_queue(self::MARKETPLACE_TYPE, "get_item_import", array(), 0, false);
	}
	
	/**
	 * Verarbeitet die Daten eines Artikel und importiert in die Import Zwischentabellen
	 * 
	 */
	public function get_item_import_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);	
		$item = $resp->Item;
		
		$summary = "";
		if(isset($item->SubTitle)) {
			$summary = mysqli_real_escape_string($GLOBALS['mysql_con'], $item->SubTitle);
		}
		
		if(isset($item->Variations)) {
			$base_price = 0;
			$is_variation_parent = 1;
			$currency = "";
		} else {
			$base_price = $item->StartPrice;
			$is_variation_parent = 0;
			$currency = $item->StartPrice->attributes()->currencyID;
		}
		
		$query = "INSERT INTO shop_marketplace_items (
						company, 
						shop_code, 
						language_code,
						marketplace_type, 
						item_no, 
						title, 
						summary, 
						description, 
						marketplace_item_id, 
						inventory, 
						base_price, 
						currency,
						`condition`, 
						category, 
						is_variation_parent, 
						item_location_zip,
						channel,
						ean,
						vat_prod_posting_group,
						marketplace_custom_category_id_1,
						marketplace_custom_category_id_2,
						timestamp_modified
					) VALUES (
						'" . $GLOBALS['shop']['company'] . "',
						'" . $GLOBALS['shop']['code'] . "',
						'" . $GLOBALS['shop_language']['code'] . "',
						" . self::MARKETPLACE_TYPE . ",
						'" . $item->SKU . "',
						'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $item->Title) . "',
						'" . $summary . "',
						'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $item->Description) . "',
						'" . $item->ItemID . "',
						" . $item->Quantity . ",
						" . $base_price . ",
						'" . $currency . "',
						'" . $item->ConditionID . "',
						'" . $item->PrimaryCategory->CategoryID . "',
						" . $is_variation_parent . ",
						" . $item->PostalCode . ",
						'" . $item->Site . "',
						'',
						'" . (isset($item->VATDetails->VATPercent) ? $item->VATDetails->VATPercent : '') . "',
						'" . (isset($item->Storefront->StoreCategoryID) ? $item->Storefront->StoreCategoryID : '') . "',
						'" . (isset($item->Storefront->StoreCategory2ID) ? $item->Storefront->StoreCategory2ID : '') . "',
						" . time() . "
		)";
		@mysqli_query($GLOBALS['mysql_con'], $query);
		
		if(isset($item->ItemSpecifics)) {
			foreach($item->ItemSpecifics->NameValueList as $nameValueList) {
				foreach($nameValueList->Value as $nameValue) {
					$query = "INSERT INTO shop_marketplace_items_attributes (
							company, 
							shop_code, 
							language_code,
							item_no,
							is_variation, 
							attribute_code, 
							value_text
						) VALUES (
							'" . $GLOBALS['shop']['company'] . "',
							'" . $GLOBALS['shop']['code'] . "',
							'" . $GLOBALS['shop_language']['code'] . "',
							'" . $item->SKU . "',
							0,
							'" . $nameValueList->Name . "',
							'" . $nameValue . "'
					)";
					@mysqli_query($GLOBALS['mysql_con'], $query);
				}
			}
		}
		
		if($is_variation_parent == 1) {
			foreach($item->Variations->Variation as $variation) {
				$query = "INSERT INTO shop_marketplace_items_link (
						company, 
						shop_code, 
						language_code,
						item_no,
						linked_item_no
					) VALUES (
						'" . $GLOBALS['shop']['company'] . "',
						'" . $GLOBALS['shop']['code'] . "',
						'" . $GLOBALS['shop_language']['code'] . "',
						'" . $item->SKU . "',
						'" . $variation->SKU . "'
				)";
				@mysqli_query($GLOBALS['mysql_con'], $query);
				
				$ean = "";
				if(isset($variation->VariationProductListingDetails->EAN)) {
					$ean = $variation->VariationProductListingDetails->EAN;
				}
				
				$query = "INSERT INTO shop_marketplace_items (
						company, 
						shop_code, 
						language_code,
						marketplace_type, 
						item_no, 
						marketplace_item_id,
						inventory, 
						base_price,
						currency, 
						is_variation_parent,
						ean,
						timestamp_modified
					) VALUES (
						'" . $GLOBALS['shop']['company'] . "',
						'" . $GLOBALS['shop']['code'] . "',
						'" . $GLOBALS['shop_language']['code'] . "',
						" . self::MARKETPLACE_TYPE . ",
						'" . $variation->SKU . "',
						'" . $item->ItemID . "',
						" . $variation->Quantity . ",
						" . $variation->StartPrice . ",
						'" . $variation->StartPrice->attributes()->currencyID . "',
						0,
						'" . $ean . "',
						" . time() . "
				)";
				@mysqli_query($GLOBALS['mysql_con'], $query);
				
				foreach($variation->VariationSpecifics->NameValueList as $nameValueList) {
					foreach($nameValueList->Value as $nameValue) {
						$query = "INSERT INTO shop_marketplace_items_attributes (
								company, 
								shop_code, 
								language_code,
								item_no,
								is_variation, 
								attribute_code, 
								value_text
							) VALUES (
								'" . $GLOBALS['shop']['company'] . "',
								'" . $GLOBALS['shop']['code'] . "',
								'" . $GLOBALS['shop_language']['code'] . "',
								'" . $variation->SKU . "',
								1,
								'" . $nameValueList->Name . "',
								'" . $nameValue . "'
						)";
						@mysqli_query($GLOBALS['mysql_con'], $query);
					}
				}
			}

		}
	}
	
	/**
	 * Importiert alle Artikel vom marketplace zum webshop anstossen
	 * 
	 */
	public function import_items($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetSellerList");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		// zeitspanne darf nur 120 Tage betragen
		$root->appendChild(
			$dom->createElement("EndTimeFrom", $this->genTime("-1 day"))
		);
		
		$root->appendChild(
			$dom->createElement("EndTimeTo", $this->genTime("+118 days"))
		);
		
		$page = 1;
		if(isset($parameter['PageNumber'])) {
			$page = $parameter['PageNumber'];
		}
		
		$pagination = $root->appendChild(
			$dom->createElement("Pagination")
		);
		
			$pagination->appendChild(
				$dom->createElement("EntriesPerPage", 200)
			);
			
			$pagination->appendChild(
				$dom->createElement("PageNumber", $page)
			);
		
		$this->callProcess();
	}
	
	/**
	 * Verarbeitet die Artikel und importiert in den webshop
	 * 
	 */
	public function import_items_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);	
		
		if((int)$resp->PageNumber == 1) {
			$query = "DELETE from shop_marketplace_items 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'], $query);
			
			$query = "DELETE from shop_marketplace_items_link 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
				and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'], $query);
			
			$query = "DELETE from shop_marketplace_items_attributes 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
				and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'], $query);
			
			$query = "DELETE from shop_marketplace_items_import 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
				and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			";
			@mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		foreach($resp->ItemArray->Item as $item) {
			$query = "INSERT INTO shop_marketplace_items_import (
				company, 
				shop_code, 
				language_code,
				item_id, 
				timestamp_modified
			) VALUES (
				'" . $GLOBALS['shop']['company'] . "',
				'" . $GLOBALS['shop']['code'] . "',
				'" . $GLOBALS['shop_language']['code'] . "',
				'" . $item->ItemID . "',
				" . time() . "
			)";
			@mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		// wenn noch mehr Artikel vorhanden sind, dann weiteren request starten
		if((string)$resp->HasMoreItems == "true") {
			$lastpage = 1;
			if(isset($parameter['PageNumber'])) {
				$lastpage = (int)$parameter['PageNumber'];
			}
			
			$param = array(
				'PageNumber' => $lastpage+1
			);
	
			$this->new_queue(self::MARKETPLACE_TYPE, "import_items", $param, 0, false);
		} else {
			$this->new_queue(self::MARKETPLACE_TYPE, "get_item_import", array(), 0, false);
		}
	}
	
		/**
	 * Produkte bei ebay aktualisieren.
	 * Hier wird erst mal ein Job bei Bulk Data Exchange genriert
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function compare_active_listings($parameter = array()) {
		
		// pruefen ob der job bereits noch lauft
		$bulkJobResult = active_bulk_job("startDownloadJob");
		if (@mysqli_num_rows($bulkJobResult) > 0) {
			$this->setRunAgain(true);
			return;
		}
		
		insert_bulk_job("startDownloadJob");
		
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("startDownloadJob");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("downloadJobType", "ActiveInventoryReport")
		);
		
		$root->appendChild(
			$dom->createElement("UUID", uniqid('', true))
		);
		
		$downloadRequestFilter = $root->appendChild(
			$dom->createElement("downloadRequestFilter")
		);
		
			$activeInventoryReportFilter = $downloadRequestFilter->appendChild(
				$dom->createElement("activeInventoryReportFilter")
			);
			
				$fixedPriceItemDetails = $activeInventoryReportFilter->appendChild(
					$dom->createElement("fixedPriceItemDetails")
				);
					
					$fixedPriceItemDetails->appendChild(
						$dom->createElement("includeVariations", "true")
					);
					
				$activeInventoryReportFilter->appendChild(
					$dom->createElement("includeListingType", "AuctionAndFixedPrice")
				);
		
		$this->callProcess(false);
		
		if($this->callOk()) {
			$filename = __DIR__."/../xml/" . $this->getResultFilename();
			$resp = simplexml_load_file($filename);	
			$this->setSubmissionId((int)$resp->jobId);
			update_bulk_job("startDownloadJob", (int)$resp->jobId);
		}
	}
	
	/**
	 * Die Verabrietung der XMl Datei wird geprueft.
	 * 
	 * @param string $jobId
	 * @access public
	 */
	public function compare_active_listings_submission($jobId) {
		$this->start_upload_submission($jobId);
	}
	
	/**
	 * Wenn download report verarbeitet wurde
	 * 
	 * @param string $jobId
	 * @access public
	 */
	public function compare_active_listings_submission_ready($jobId) {
		$this->start_upload_submission_ready($jobId);
	}
	
	/**
	 * Upload der XML Datei beendet, download Vorgang starten.
	 * Es wird eine zip Datei mit den Ergebnissen des Bulk uploads heruntergeladen.
	 * In der ZIP Datei ist eine result XML vorhanden
	 * 
	 * @param string $result_file
	 * @param array $parameter
	 * @access public
	 */
	public function compare_active_listings_submission_result($result_file, $parameter = array()) {
		$this->start_upload_submission_result($result_file, $parameter);
	}
	
	/**
	 * XML Datei mit Produkten erstellen und gzip/base64 zum upload bereitstellen
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function upload_file($parameter = array()) {
		$this->createBulkXml($parameter);

		// Bulk XML Datei fuer uebertragung vorbereiten
		$data = gzencode(file_get_contents($this->bulkXmlFilename), 5);
		$file = base64_encode($data);
		$fileSize = strlen($file);

		// Normalen call mit der File Transfer API starten
		$this->ebayCall = new EbayFiletransfer();
		$this->ebayCall->setCallname("uploadFile");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$fileAttachment = $root->appendChild(
			$dom->createElement("fileAttachment")
		);
		
			$fileAttachment->appendChild(
				$dom->createElement("Data", $file)
			);
			
			$fileAttachment->appendChild(
				$dom->createElement("Size", $fileSize)
			);
			
		$root->appendChild(
			$dom->createElement("fileFormat", "gzip")
		);
		
		$root->appendChild(
			$dom->createElement("fileReferenceId", $parameter['fileReferenceId'])
		);
		
		$root->appendChild(
			$dom->createElement("taskReferenceId", $parameter['jobId'])
		);
		
		$this->callProcess();
	}
	
	/**
	 * Nachdem die XML Datei zum Bulk upload bereiestellt wurde,
	 * den Startvorgang in die queue schreiben
	 * 
	 */
	public function upload_file_submission_result($result_file, $parameter = array()) {
		$this->new_queue(self::MARKETPLACE_TYPE, "start_upload", $parameter, 0, false);
	}
	
	/**
	 * Upload vorgang starten. 
	 * Bei ebay wird die zuvor erstellte gezippte xml Datei hochgeladen und verarbeitet.
	 *
	 * @param array $parameter
	 * @access public 
	 */
	public function start_upload($parameter = array()) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("startUploadJob");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("jobId", $parameter['jobId'])
		);
		
		try {
			$this->ebayCall->call();
			
			if($this->ebayCall->getResults() !== false) {
				$this->setCallOk(true);
				$this->setSubmissionId($parameter['jobId']);
			} else {
				$this->setCallOk(false);
				$this->setErrorMessage("Kein Result von ebay zurueckbekommen.");
			}
			
		} catch (MarketplaceWebService_Exception $ex) {
	        $this->setCallOk(false);
			$this->setErrorMessage("Catch bei ebay: " . $ex->getMessage());
	    }
	}
	
	/**
	 * Die Verabrietung der XMl Datei wird geprueft.
	 * 
	 * @param string $jobId
	 * @access public
	 */
	public function start_upload_submission($jobId) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("getJobStatus");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("jobId", $jobId)
		);
		
		try {
			$this->ebayCall->call();
			
			if($this->ebayCall->getResults() !== false) {
				$this->setCallOk(true);
				$resp = simplexml_load_string($this->ebayCall->getXmlResult());

				// 3 Stati abfragen. Diese sagen aus, dass der vorgang wirklich abgeschlossen wurde.				
				if(	$resp->jobProfile->jobStatus == 'Aborted' || 
					$resp->jobProfile->jobStatus == 'Completed' ||
					$resp->jobProfile->jobStatus == 'Failed'
				) {
					$this->setSubmissionReady(true);
					$this->setSubmissionId($jobId);
				} else {
					$this->setSubmissionReady(false);
				}
				
			} else {
				$this->setCallOk(false);
				$this->setErrorMessage("Kein Result von ebay zurueckbekommen.");
			}
			
		} catch (MarketplaceWebService_Exception $ex) {
	        $this->setCallOk(false);
			$this->setErrorMessage("Catch bei ebay: " . $ex->getMessage());
	    }
	}

	/**
	 * Wenn die gezippte XML Datei verarbeitet wurde
	 * schreiben in die result Tabelle
	 * 
	 * @param string $jobId
	 * @access public
	 */
	public function start_upload_submission_ready($jobId) {
		$this->ebayCall = new EbayBulk();
	
		$this->ebayCall->setCallname("getJobStatus");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$item = $root->appendChild(
			$dom->createElement("jobId", $jobId)
		);
		
		$this->callProcess();
	}
	
	/**
	 * Upload der XML Datei beendet, download Vorgang starten.
	 * Es wird eine zip Datei mit den Ergebnissen des Bulk uploads heruntergeladen.
	 * In der ZIP Datei ist eine result XML vorhanden
	 * 
	 * @param string $result_file
	 * @param array $parameter
	 * @access public
	 */
	public function start_upload_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);
		
		$parameter['fileReferenceId'] = (string)$resp->jobProfile->fileReferenceId;
		$parameter['jobId'] = (string)$resp->jobProfile->jobId;
		
		$this->new_queue(self::MARKETPLACE_TYPE, "download_file", $parameter, 0, false);
		
		// job abgeschlossen
		delete_bulk_job((string)$resp->jobProfile->jobId);
		
		$this->setCallOk(true);
	}
	
	/**
	 * Datei Download. Erstellen eine ZIP Datei mit XML Inhalt
	 * 
	 * @param array $parameter
	 * @access public
	 */
	public function download_file($parameter = array()) {
		// Normalen call mit der File Transfer API starten
		$this->ebayCall = new EbayFiletransfer();
		$this->ebayCall->setCallname("downloadFile");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("fileReferenceId", $parameter["fileReferenceId"])
		);
		
		$root->appendChild(
			$dom->createElement("taskReferenceId", $parameter['jobId'])
		);
		
		try {
			$this->ebayCall->call();
			
			if($this->ebayCall->getResults() !== false) {
				$this->setCallOk(true);
				$filename = "result_ebay_download_" . $parameter['jobId'] . "_file.zip";
				$file = __DIR__."/../xml/" . $filename;

				$responseXML = $this->parseForResponseXML($this->ebayCall->getXmlResult());
		
				$responseDOM = DOMUtils::createDOM($responseXML);
		
				$uuid = $this->parseForXopIncludeUUID($responseDOM);
		
				$fileBytes = $this->parseForFileBytes($uuid, $this->ebayCall->getXmlResult());
		
				$this->writeZipFile($fileBytes, $file);
				
				$this->setSubmissionResult($filename);
			} else {
				$this->setCallOk(false);
				$this->setErrorMessage("Kein Result von ebay zurueckbekommen.");
			}
			
		} catch (MarketplaceWebService_Exception $ex) {
	        $this->setCallOk(false);
			$this->setErrorMessage("Catch bei ebay: " . $ex->getMessage());
	    }
	}
	
	public function download_file_submission_result($result_file, $parameter = array()) {
		$xmlDir = __DIR__."/../xml/";
		$filename = $xmlDir . $result_file;
		
		// zip entpacken
		$zip = new ZipArchive;
	    $res = $zip->open($filename);
	    if ($res === TRUE) {
	    	$zip->extractTo($xmlDir);
	        $zip->close();
	        
	        $xml = $xmlDir . $parameter['jobId'] . '_responses.xml';
	        
	        $resp = simplexml_load_file($xml);
	        
	        foreach($resp as $xmlKey => $xmlItem) {
	        	$this->handle_downloaded_result_row($xmlKey, $xmlItem);
	        }
	    } 
	}
	
	/**
	 * Alle Basis Kategorien auslesen
	 * 
	 * @access public
	 */
	public function fetch_categories($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetCategories");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		// nur root kategorien
		if(isset($parameter['rootOnly']) && $parameter['rootOnly'] == true) {
			$root->appendChild(
				$dom->createElement("LevelLimit", "1")
			);
		}
		
		// alle kategorien der parent kategorie
		if(isset($parameter['parentID'])) {
			$root->appendChild(
				$dom->createElement("CategoryParent", $parameter['parentID'])
			);
		}
		
		// alle details
		$root->appendChild(
			$dom->createElement("DetailLevel", "ReturnAll")
		);
		
		$this->callProcess();
	}
	
	public function fetch_categories_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		
		$resp = simplexml_load_file($filename);
		
		$in_use_categories = array();

		foreach($resp->CategoryArray->Category as $category) {
			$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and category_code = '" . $category->CategoryID . "' limit 1";
			$result = @mysqli_query($GLOBALS['mysql_con'], $query);
			$row = @mysqli_fetch_array($result);
			
			if(isset($parameter['rootOnly']) && $parameter['rootOnly'] == true) {
				$root_category = $category->CategoryID;
				$in_use = 0;
				$children_fetched = 0;
				$timestamp_children_fetched = 0;
				$parent_category_code = "";
			}
			
			if(isset($parameter['parentID'])) {
				$root_category = $parameter['parentID'];
				$in_use_categories[] = $parameter['parentID'];
				$in_use_categories[] = $category->CategoryID;
				$in_use = 1;
				$children_fetched = 1;
				$timestamp_children_fetched = time();
				$parent_category_code = $category->CategoryParentID;
			}
			
			if($category->CategoryLevel == "1") {
				$parent_category_code = "";
			}
			
			if (@mysqli_num_rows($result) == 1) {
				$query = "
					UPDATE shop_marketplace_categories SET 
						category_code = '" . $category->CategoryID . "', 
						category_name = '" . $category->CategoryName . "', 
						category_level = '" . $category->CategoryLevel . "',
						parent_category_code = '" . $parent_category_code . "',
						root_category_code = '" . $root_category . "',
						in_use = " . $in_use . ",
						children_fetched = " . $children_fetched . ", 
						timestamp_children_fetched = " . $timestamp_children_fetched . ",
						timestamp_fetched = " . time() . "
					WHERE
						marketplace_type = 2 and 
						category_code = '" . $category->CategoryID . "'
				";
				@mysqli_query($GLOBALS['mysql_con'], $query);	
			} else {
				$query = "	
					INSERT INTO shop_marketplace_categories (
						marketplace_type,
						category_id, 
						category_code, 
						category_name, 
						category_level, 
						parent_category_code, 
						root_category_code,
						in_use, 
						children_fetched, 
						timestamp_children_fetched,
						timestamp_fetched
					) VALUES (
						2,
						'',
						'" . $category->CategoryID . "',
						'" . $category->CategoryName . "',
						'" . $category->CategoryLevel . "',
						'" . $parent_category_code . "',
						'" . $root_category . "',
						" . $in_use . ",
						" . $children_fetched . ",
						" . $timestamp_children_fetched . ",
						" . time() . "
					)";
	
				@mysqli_query($GLOBALS['mysql_con'], $query);	
			}
            $this->new_queue(self::MARKETPLACE_TYPE, "get_variant_attributes", array('category_code' => $category->CategoryID), 0, false);
		}
		
		if(count($in_use_categories)) {
			$query = "UPDATE shop_marketplace_categories set children_fetched = 1, timestamp_children_fetched = " . time() . "
				WHERE marketplace_type = 2 and (category_code IN ('" . join("','", $in_use_categories) . "') or parent_category_code IN ('" . join("','", $in_use_categories) . "') )
			";
			
			@mysqli_query($GLOBALS['mysql_con'], $query);	
		}	
	}
	
	public function get_category_features($parameter = array()) {
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetCategoryFeatures");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		$root->appendChild(
			$dom->createElement("CategoryID", $parameter['CategoryID'])
		);
		
		$root->appendChild(
			$dom->createElement("ViewAllNodes", 1)
		);
		
		// alle details
		$root->appendChild(
			$dom->createElement("DetailLevel", "ReturnAll")
		);
		
		$root->appendChild(
			$dom->createElement("FeatureID", "VariationsEnabled")
		);
		
		$root->appendChild(
			$dom->createElement("FeatureID", "ConditionEnabled")
		);
		
		$root->appendChild(
			$dom->createElement("FeatureID", "ConditionValues")
		);
		
		$root->appendChild(
			$dom->createElement("FeatureID", "PaymentMethods")
		);
		
		$this->callProcess();
	}
	
	public function get_category_features_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);
		
		$query = "DELETE FROM shop_marketplace_payments where marketplace_type = 2 and category_code = '" . $parameter['CategoryID'] . "'";
		mysqli_query($GLOBALS['mysql_con'], $query);
		foreach($resp->SiteDefaults->PaymentMethod as $paymentMethod) {
			$query = "INSERT INTO shop_marketplace_payments (marketplace_type, category_code, payment_method) VALUES (
				2,
				'" . $parameter['CategoryID'] . "',
				'" . $paymentMethod . "'
			)";
			mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		// child categories haben die selbe einstellung:
		$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $parameter['CategoryID'] . "' and category_code != '" . $parameter['CategoryID'] . "'";
		$result = mysqli_query($GLOBALS['mysql_con'], $query);
		
		if(mysqli_num_rows($result) > 0) {
			$this->helper_update_payment_method($result, $resp->SiteDefaults->PaymentMethod);	
		}
		
		// erst die site defaults eintragen
		if((string)$resp->SiteDefaults->VariationsEnabled == "true") {
			$variant_available = 1;
		} else {
			$variant_available = 0;
		}
		
		// variant defaults eintragen
		$query = "UPDATE shop_marketplace_categories SET variant_available = " . $variant_available . " where marketplace_type = 2 and category_code = '" . $parameter['CategoryID'] . "'";
		mysqli_query($GLOBALS['mysql_con'], $query);
		
		// child categories haben die selbe einstellung:
		$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $parameter['CategoryID'] . "' and category_code != '" . $parameter['CategoryID'] . "'";
		$result = mysqli_query($GLOBALS['mysql_con'], $query);
		
		if(mysqli_num_rows($result) > 0) {
			$this->helper_update_variant_available($result, $variant_available, self::MARKETPLACE_TYPE);	
		}
		
		foreach($resp->Category as $category) {
			if((string)$category->VariationsEnabled == "true") {
				$variant_available = 1;
			} else {
				$variant_available = 0;
			}
			$query = "UPDATE shop_marketplace_categories SET variant_available = " . $variant_available . " where marketplace_type = 2 and category_code = '" . $category->CategoryID . "'";
			mysqli_query($GLOBALS['mysql_con'], $query);
			
			// child categories haben die selbe einstellung:
			$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $category->CategoryID . "' and category_code != '" . $category->CategoryID . "'";
			$result = mysqli_query($GLOBALS['mysql_con'], $query);
			
			if(mysqli_num_rows($result) > 0) {
				$this->helper_update_variant_available($result, $variant_available, self::MARKETPLACE_TYPE);	
			}
			
			// Megliche Artikelzustaende
			if(isset($category->ConditionValues->Condition)) {
				$query = "DELETE FROM shop_marketplace_conditions where marketplace_type = 2 and category_code = '" . $category->CategoryID . "'";
				mysqli_query($GLOBALS['mysql_con'], $query);
				foreach($category->ConditionValues->Condition as $condition) {
					$query = "INSERT INTO shop_marketplace_conditions (marketplace_type, category_code, condition_code, codition_description) VALUES (
						2,
						'" . $category->CategoryID . "',
						'" . $condition->ID . "',
						'" . $condition->DisplayName . "'
					)";
					mysqli_query($GLOBALS['mysql_con'], $query);
				}
				
				// child categories haben die selbe Artikelzustand Einstellung
				$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $category->CategoryID . "' and category_code != '" . $category->CategoryID . "'";
				$result = mysqli_query($GLOBALS['mysql_con'], $query);
				
				if(mysqli_num_rows($result) > 0) {
					$this->helper_update_condition($result, $category->ConditionValues->Condition);	
				}
			}
			
			// moegliche Zahlungsarten
			if(isset($category->PaymentMethod)) {
				$query = "DELETE FROM shop_marketplace_payments where marketplace_type = 2 and category_code = '" . $category->CategoryID . "'";
				mysqli_query($GLOBALS['mysql_con'], $query);
				foreach($category->PaymentMethod as $paymentMethod) {
					$query = "INSERT INTO shop_marketplace_payments (marketplace_type, category_code, payment_method) VALUES (
						2,
						'" . $category->CategoryID . "',
						'" . $paymentMethod . "'
					)";
					mysqli_query($GLOBALS['mysql_con'], $query);
				}
				
				// child categories haben die selbe einstellung:
				$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $category->CategoryID . "' and category_code != '" . $category->CategoryID . "'";
				$result = mysqli_query($GLOBALS['mysql_con'], $query);
				
				if(mysqli_num_rows($result) > 0) {
					$this->helper_update_payment_method($result, $category->PaymentMethod);	
				}
			}
		}
		
		$this->setCallOk(true);
	}
	
	public function get_variant_attributes($parameter = array()) {

	    $where = "";
		if(isset($parameter['category_code'])) {
			$where = " and category_code = '" . $parameter['category_code'] . "'";
		}
		
		// codes in_use sammeln
		$query = "SELECT count(*) as anzahl FROM shop_marketplace_categories where marketplace_type = 2 and in_use = 1 $where";
		$result = @mysqli_query($GLOBALS['mysql_con'],$query);
		$row = @mysqli_fetch_array($result);
		$anzahl = $row['anzahl'];
		
		$start = 0;
		if(isset($parameter['start'])) {
			$start = $parameter['start'];
		}
		
		$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and in_use = 1 $where order by id asc limit $start, 100";
		$result = mysqli_query($GLOBALS['mysql_con'], $query);
		$in_use_categories = array();
		
		while ($row = mysqli_fetch_assoc($result)) {
			$in_use_categories[] = $row['category_code'];
		}
		
		if(count($in_use_categories) == 0) {
			$this->setCallOk(true);
			return;
		}
		
		if($anzahl > 100 && $start == 0) {
			// pro aufruf koennen nur 100 kategorien abgefragt werden.
            $this->parentQueueId = $this->currentQueueId;
            for($i = 100; $i<=$anzahl; $i+=100) {
				$insertParameter = array(
					'start' => $i
				);
                if($i==$anzahl-1){
                    //last iteration :)
                    $this->new_queue(self::MARKETPLACE_TYPE, "get_variant_attributes", $insertParameter, $this->currentQueueId, true);
                } else {
				    $this->new_queue(self::MARKETPLACE_TYPE, "get_variant_attributes", $insertParameter, $this->currentQueueId, false);
                }
			}
		} else {
            //einzelne oder letzte
            if(!isset($parameter['start'])) {
                //einzelne
                $this->parentQueueId = $this->currentQueueId;
                $query = "UPDATE shop_marketplace_queue SET finish_parent_queue = 1 WHERE id = ".$this->currentQueueId;
                mysqli_query($GLOBALS["mysql_con"],$query);
            }
        }
		
		$this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname("GetCategorySpecifics");
		$dom = $this->ebayCall->createBaseDocument();
		$root = $dom->documentElement;
		
		foreach($in_use_categories as $category_code) {
			$root->appendChild(
				$dom->createElement("CategoryID", $category_code)
			);
		}
		
		$this->callProcess();
	}
	
	public function get_variant_attributes_submission_result($result_file, $parameter = array()) {
		$filename = __DIR__."/../xml/" . $result_file;
		$resp = simplexml_load_file($filename);

		foreach($resp->Recommendations as $recommendation) {
			@mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_variant_attributes where marketplace_type = 2 and category_code = '" . $recommendation->CategoryID . "'");
            $query3 = "DELETE FROM 
                              shop_marketplace_variant_attribute_values 
                          WHERE 
                              marketplace_type = 2
                          AND 
                              category_code = '" . $recommendation->CategoryID . "'  
                          ";

            mysqli_query($GLOBALS['mysql_con'], $query3);
			
			foreach($recommendation->NameRecommendation as $rec) {
				if(	!isset($rec->ValidationRules->VariationSpecifics) ||
					$rec->ValidationRules->VariationSpecifics == "Enabled"				 
				) {
					$is_variation = 1;
				} else {
					$is_variation = 0;
				}

                if(	isset($rec->ValidationRules->MinValues) &&
                    $rec->ValidationRules->MinValues == "1"
                ) {
                    $is_required = 1;
                } else {
                    $is_required = 0;
                }

                if(	isset($rec->ValidationRules->ValueType)) {
                    $value_type = $rec->ValidationRules->ValueType;
                } else {
                    $value_type = '';
                }

                if(	isset($rec->ValidationRules->MaxValues)) {
                    $max_values = $rec->ValidationRules->MaxValues;
                } else {
                    $max_values = '';
                }

                if(	isset($rec->ValidationRules->SelectionMode)) {
                    $selection_mode = $rec->ValidationRules->SelectionMode;
                } else {
                    $selection_mode = '';
                }

				
				$query = "INSERT INTO shop_marketplace_variant_attributes (marketplace_type, category_code, description, is_variation, is_required, ValueType, MaxValues, SelectionMode, timestamp_fetched) VALUES (
					2,
					'" . $recommendation->CategoryID . "',
					'" . $rec->Name . "',
					" . $is_variation . ",
					" . $is_required . ",
					'" . $value_type . "',
					" . $max_values . ",
					'" . $selection_mode . "',
					" . time() . "
				)";



                mysqli_query($GLOBALS['mysql_con'], $query);

				foreach ($rec->ValueRecommendation as $valueRecommendation) {
                    if(	isset($valueRecommendation->Value)) {
                        $query2 = "INSERT INTO shop_marketplace_variant_attribute_values (
                                marketplace_type, 
                                category_code, 
                                description, 
                                value, 
                                update_insert
                              ) VALUES (
                                2,
                                '" . $recommendation->CategoryID . "',
                                '" . $rec->Name . "',
                                '" . $valueRecommendation->Value . "',
                                1
                        )";


                        $queryres = mysqli_query($GLOBALS['mysql_con'], $query2);



                    }
                }



			}
		}

		$this->setCallOk(true);
	}
	
	protected function callProcess($setSubmissionResult = true) {
		try {
			$this->ebayCall->call();
			
			if($this->ebayCall->getResults() !== false) {
				$this->setCallOk(true);
				$filename = $this->getResultFilename();
				$file = __DIR__."/../xml/" . $filename;
				
				// Result in Datei schreiben
				$splFile = new SplFileObject($file, "w");
				$splFile->fwrite($this->ebayCall->getXmlResult());
				if($setSubmissionResult === true) {
					$this->setSubmissionResult($filename);
				}
			} else {
				$this->setCallOk(false);
				$this->setErrorMessage("Kein Result von ebay zurueckbekommen.");
			}
			
		} catch (MarketplaceWebService_Exception $ex) {
	        $this->setCallOk(false);
			$this->setErrorMessage("Catch bei ebay: " . $ex->getMessage());
	    }
	}
	
	public function test_xml() {
		$this->createBulkXml(array('request' => "AddFixedPriceItem", 'maxFileSize' => 50000000));
//	 headerFunctionBridge("Content-type: text/xml");
//		echo $this->bulkXml->saveXML();
		die();
	}
	
	protected function createBulkXml($parameter = array()) {
		switch($parameter['request']) {
			case 'AddFixedPriceItem':
				if(isset($parameter['item_no'])) {
					$this->items_result = get_item_result($parameter['item_no']);
				} else {
					$this->items_result = get_all_new_items();
				}
				
				return $this->create_send_products_xml($parameter, $parameter['request']);
				break;
				
			case 'ReviseFixedPriceItem':
				if(isset($parameter['item_no'])) {
					$this->items_result = get_item_result($parameter['item_no']);
				} else {
					$this->items_result = get_all_existing_items('smiu.marketplace_update_price = 1 OR smiu.marketplace_update_inventory = 1');
				}
				return $this->create_send_products_xml($parameter, $parameter['request']);
				break;
				
			case 'ReviseInventoryStatus':
				if(isset($parameter['item_no'])) {
					$this->items_result = get_item_result($parameter['item_no']);
				} else {
					$this->items_result = get_all_existing_items('smiu.marketplace_update_price = 1 OR smiu.marketplace_update_inventory = 1');
				}
				return $this->create_revise_inventory_status_request($parameter);
				break;
				
			case 'ActiveInventoryReport':
				
				break;
		}
	}
	
	protected function create_send_products_xml($parameter = array(), $apiName = "AddFixedPriceItem") {

	    $bulkXmlFile = $this->start_BulkDataExchangeRequest();
		
		$no_process_items = array();
		$parent_to_variant = array();

        $this->bulkXml = null;

		while ($item = mysqli_fetch_assoc($this->items_result)) {


			$this->variation_pictures_tag = null;
			$this->allAttributes = array();
            $this->allAttributesNoVariation = array();
			
			// pruefen ob die bulkdateien kurz vor ueberschreitung des limits ist
			$bulkSize = filesize($bulkXmlFile);
			if(!isset($parameter['item_no']) && ($bulkSize + 4000000) > $parameter['maxFileSize']) {
				
				// weitere items in einem extra request durchlaufen weil die bulk langsam zu gross wird.
				$this->new_queue(self::MARKETPLACE_TYPE, $parameter['operation'], $parameter, 0, false);
				break;
			}
			
			if(in_array($item['item_no'], $no_process_items)) continue;

			$item_is_parent = get_item_first_variant($item);
			$item_is_variant = get_item_variant_parent($item); // item ist variante wenn es einen parent gibt

            //temp zum testen
            //$item_is_parent = false;
            //$item_is_variant = false;

			if($GLOBALS['shop']['variant_typ'] == 1) {
				if($item_is_parent !== false) {
					$item_is_variant = true; // parent ist gleichzeitig auch eine variante
				}
			}
			
			// erstmal kein eigenstaendiger artikel
			$standalone = false;
			
			// item ist weder eine variante noch ein parent, dann ein eigenstaendiger artikel
			if($item_is_variant === false && $item_is_parent === false) {
				$standalone = true;
			}
			
			if($item['marketplace_standalone_product_per_shop'] == 1) {
				$standalone = true;
			}
			
			// artikel existiert noch nicht, hinzufuegen
			// oder artikel existiert bereits, nur updaten
			// update oder neu wird bei ebay nur anhand von Add oder revise untershieden, parameter sind gleich.
				
			// kategorie herausfinden
			$item_has_category = get_item_has_category($item);

			// wenn item zu keiner kategorie gehoert
			if($item_has_category === false) {
				continue;
			}

			if( $standalone === false ) {

				// Hier werden varianten und parents betrachtet. Bei ebay geht alles vom parent aus. 
				// Will man Varianten bearbeiten, muss der parent mit allen anderen varianten hochgeladen werden
				
				if($item_is_parent === false) {
					$parent_item = get_item_variant_parent($item);
					$no_process_items[] = $parent_item['item_no'];
					$variants_result = get_item_variants($parent_item,2);
					$item = get_item($item['company'], $item['shop_code'], $item['language_code'], $parent_item['item_no']);
					$query = "
                            UPDATE shop_marketplace_item_update set marketplace_update = 0, marketplace_update_images = 0, marketplace_update_inventory = 0, marketplace_update_price = 0 
                            where 
                            (item_no = '" . $parent_item['item_no'] . "' 
                            OR item_no = '" . $item['item_no'] . "')
                            AND company = '" . $GLOBALS["shop"]["company"] . "'
                            AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
                            AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                            AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
                            AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                            ";
					@mysqli_query($GLOBALS['mysql_con'], $query);
				} else {
					$variants_result = get_item_variants($item,2);
					$no_process_items[] = $item['item_no'];
					$item = get_item($item['company'], $item['shop_code'], $item['language_code'], $item['item_no']);
					$query = "UPDATE shop_marketplace_item_update set marketplace_update = 0, marketplace_update_images = 0, marketplace_update_inventory = 0, marketplace_update_price = 0 
                              where 
                              item_no = '" . $item['item_no'] . "'
                              AND company = '" . $GLOBALS["shop"]["company"] . "'
                                AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
                                AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
                                AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                              ";
					@mysqli_query($GLOBALS['mysql_con'], $query);
				}

				// alle moeglichen Varianten auslesen, in denen es das Produkt gibt.
				$all_variant_items = array();
				
				// Parentartikel ist gleichzeitig auch eine Variante
				if($GLOBALS['shop']['variant_typ'] == 1 && $item['marketplace_standalone_product_per_shop'] == 0) {
					$no_process_items[] = $item['item_no'];
					$all_variant_items[$item['item_no']] = $item;
				}
				
				while ($variant_item = mysqli_fetch_assoc($variants_result)) {	
					$no_process_items[] = $variant_item['item_no'];
					if($variant_item['marketplace_standalone_product_per_shop'] == 1) {
						continue;	
					}
					$all_variant_items[$variant_item['item_no']] = $variant_item;
				}
				
				if(count($all_variant_items) == 0) {
					$this->set_item_submission_error($item['item_no'], sprintf("Vom Parent %s gibt es keine Varianten oder alle Varianten sind als eigenstaendige Artikel gepflegt.", $item['item_no']));
					continue;
				}
				
				// pruefen ob die kategorie varianten erlaubt
				if(!category_variation_allowed($item_has_category, 2)) {
					$this->set_item_submission_error($item['item_no'], "Die Ebay-Kategorie des items laesst keine Variantenbildung zu.");
					continue;
				}
				
				// pruefen ob die kategorie variantenbildende merkmale enthaelt
				$attribute_codes = get_category_variant_attributes($item_has_category['marketplace_category_code'], 2);
                
                if(count($attribute_codes) == 0) {
					$this->set_item_submission_error($item['item_no'], sprintf("Attribut code für die Kategorie %s in der shop_marketplace_variant_attributes nicht vorhanden.", $item_has_category['marketplace_category_code']));
					continue;
				}
				
				// pruefen ob die marketplace merkmale auch im mysyde shop angelegt sind
				$shop_attributes = get_variant_item_attributes($attribute_codes, self::ATTRIBUTE_TYPE);

				if(count($shop_attributes) == 0) {
					$this->set_item_submission_error($item['item_no'], sprintf("Ebay: Marketplace Attribute: %s müssen auch im Webshop angelegt sein", join(',', $attribute_codes)));
					continue;
				}

				// AB HIER: varianten theoretisch moeglich weil marketplace und mysyde shop es zulassen wuerden
				$allOptions = $this->get_all_variation_attributes($all_variant_items, $shop_attributes);

				if(count($allOptions) == 0) {
					$this->set_item_submission_error($item['item_no'], "Alle Attribute müssen mindestens ein Variantenbildendes Merkmal besitzen.");
					continue;
				}

				$variantStartTagSet = false;
				$variant_items_for_images = array();
				foreach($all_variant_items as $variant_item_no => $variant_item) {			
					// nur varianten bilden wenn mindestens ein Artikel werte zu den attributen hat
						
					$item_has_variant_attributes = item_has_attributes($variant_item, $shop_attributes, self::ATTRIBUTE_TYPE);
					
					if($item_has_variant_attributes === false) {
						$this->set_item_submission_error($variant_item['item_no'], "Item soll als Variante bei ebay erscheinen, hat aber keine ebay attribute zugeordnet.");
						continue; // naechste variante
					}

					// Starttag beim parent element setzen
					if($variantStartTagSet === false) {
						$parentItemTag = $this->create_fixed_price_item_request($item, $item_has_category, true, $apiName);
						$variationsTag = $this->create_variations_start($parentItemTag, $item, $allOptions);
						$variantStartTagSet = true;
					}
					
					// variante hinzufuegen
					$this->create_variation($variationsTag, $variant_item, $item, $item_has_category);
					$variant_items_for_images[] = $variant_item;
				}	

				foreach($variant_items_for_images as $variant_item) {
					$this->create_variation_images($variationsTag, $variant_item, $item, $item_has_category);
				}
				
			} else {

				// eigenstaendiger Artikel ohne Varianten
				$this->create_fixed_price_item_request($item, $item_has_category, false, $apiName);

                $query = "UPDATE shop_marketplace_item_update set marketplace_update = 0, marketplace_update_images = 0, marketplace_update_inventory = 0, marketplace_update_price = 0 
                              where 
                              item_no = '" . $item['item_no'] . "'
                              AND company = '" . $GLOBALS["shop"]["company"] . "'
                                AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
                                AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
                                AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                              ";
				@mysqli_query($GLOBALS['mysql_con'], $query);
			}
			
			if($this->bulkXml !== null) {
				$this->insertMessageToBulk($apiName . "Request");
			}
		}
		
		$this->end_BulkDataExchangeRequest();
		
		return $bulkXmlFile;
	}
	
	protected function create_variations_start($parentItemTag, $item, $allOptions) {
		$variations = $parentItemTag->appendChild(
			$this->bulkXml->createElement("Variations")
		);
		
		$specificSet = $variations->appendChild(
			$this->bulkXml->createElement("VariationSpecificsSet")
		);
		
		foreach($allOptions as $attributeCode => $attributeData) {
			$valueList = $specificSet->appendChild(
				$this->bulkXml->createElement("NameValueList")
			);
			
			$valueList->appendChild(
				$this->bulkXml->createElement("Name", $attributeCode)
			);
			
			foreach($attributeData as $description) {
				$valueList->appendChild(
					$this->bulkXml->createElement("Value", $description)
				);
			}
		}
			
		return $variations;
	}
	
	protected function create_variation($variationsTag, $variant_item, $parent_item, $item_has_category) {
        $query = "UPDATE shop_marketplace_item_update set marketplace_update = 0
                              where 
                              item_no = '" . $variant_item['item_no'] . "'
                              AND company = '" . $GLOBALS["shop"]["company"] . "'
                                AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
                                AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
                                AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                              ";
		@mysqli_query($GLOBALS['mysql_con'], $query);
		
		$price = number_format($variant_item['base_price'], 2, '.', '');
		
		$variation = $variationsTag->appendChild(
			$this->bulkXml->createElement("Variation")
		);
		
		$variation->appendChild(
			$this->bulkXml->createElement("SKU", $variant_item['item_no'])
		);
		
		$variation->appendChild(
			$this->bulkXml->createElement("Quantity", (int)$variant_item['inventory'])
		);
		
		$variation->appendChild(
			$this->bulkXml->createElement("StartPrice", $price)
		);
		
		// EAN
        $standard_product_id = get_standard_product_id($variant_item);
        if (!empty($standard_product_id)) {
            $ProductListingDetails = $variation->appendChild(
                $this->bulkXml->createElement("VariationProductListingDetails")
            );

            $ProductListingDetails->appendChild(
                $this->bulkXml->createElement("EAN", $standard_product_id['item_reference_no'])
            );
        }

		
		// Variantenspezifische Attribute
		$variationSpecifics = $variation->appendChild(
			$this->bulkXml->createElement("VariationSpecifics")
		);
		
		$all_marketplace_variant_attributes = get_category_variant_attributes($item_has_category['marketplace_category_code'], self::MARKETPLACE_TYPE);
		$all_shop_attributes = get_variant_item_attributes($all_marketplace_variant_attributes, self::ATTRIBUTE_TYPE);
		$attributeresult = get_item_attributes($variant_item, $all_shop_attributes, self::ATTRIBUTE_TYPE);
		
		while ($attribute = mysqli_fetch_assoc($attributeresult)) {	
			$nameValueList = $variationSpecifics->appendChild(
				$this->bulkXml->createElement("NameValueList")
			);
			
			$nameValueList->appendChild(
				$this->bulkXml->createElement("Name", $attribute['headline'])
			);
			
			$attribute_value = get_item_attribute_value($variant_item, $attribute);
			$nameValueList->appendChild(
				$this->bulkXml->createElement("Value", $attribute_value)
			);
			
			if(!isset($this->allAttributes[$attribute['headline']])) {
				$this->allAttributes[$attribute['headline']] = array();
			}
			
			$this->allAttributes[$attribute['headline']][$attribute_value] = true;
		}
	}
	
	protected function create_variation_images($variationsTag, $variant_item, $parent_item, $item_has_category) {
		$imagesresult = get_item_images_clean($variant_item, $GLOBALS["shop_setup"]["image_config"]);
		
		if(mysqli_num_rows($imagesresult) > 0) {
			
			$variationSpecificName = $this->helper_get_variation_specific_name();
				
			if($variationSpecificName === false) {
				return;
			}
			
			if($this->variation_pictures_tag === null) {
				$this->variation_pictures_tag = $variationsTag->appendChild(
					$this->bulkXml->createElement("Pictures")
				);
				
				$this->variation_pictures_tag->appendChild(
					$this->bulkXml->createElement("VariationSpecificName", $variationSpecificName)
				);
			}
			
			$attribute_row = get_variant_item_attributes(array($variationSpecificName), self::ATTRIBUTE_TYPE);
			
			$variationSpecificSet = $this->variation_pictures_tag->appendChild(
				$this->bulkXml->createElement("VariationSpecificPictureSet")
			);

			$variationSpecificSet->appendChild(
				$this->bulkXml->createElement("VariationSpecificValue", get_item_attribute_value($variant_item, array_shift($attribute_row)))
			);
			
			while ($image = mysqli_fetch_assoc($imagesresult)) {
				$variationSpecificSet->appendChild(
					$this->bulkXml->createElement("PictureURL", rtrim($GLOBALS['shop']['shop_url'],'/') . '/' . $GLOBALS["shop_setup"]["image_config"][4]["path"] . "/" . $image['filename'])
				);
			}	
		}
	}
	
	protected function create_fixed_price_item_request($item, $item_has_category, $with_variant = false, $apiName) {

	    $this->ebayCall = new EbayTrading();
		$this->ebayCall->setCallname($apiName);
		$this->bulkXml = $this->ebayCall->createBaseDocument();
		$fixedPriceItem = $this->bulkXml->documentElement;
		
		$fixedPriceItem->appendChild(
			$this->bulkXml->createElement("MessageID", $item['item_no'])
		);
		
		$itemTag = $fixedPriceItem->appendChild(
			$this->bulkXml->createElement("Item")
		);
		
		// item inputs
		$item_description = $item['description'];

		$itemTag->appendChild(
			$this->bulkXml->createElement("Title", preg_replace('/\s+?(\S+)?$/', '', substr($item_description, 0, 80)))
		);


		if($item['marketplace_item_id'] != "") {
			$itemTag->appendChild(
				$this->bulkXml->createElement("ItemID", $item['marketplace_item_id'])
			);
		}

		// shop variant_typ = 1 ist die erste variante auch Mutter Artikel. Mutterartikel braucht aber eine Extra SKU deswegen erweiterung mit "m"
		if ($with_variant && $GLOBALS['shop']['variant_typ'] == 1) {
			$itemTag->appendChild(
				$this->bulkXml->createElement("SKU", $item['item_no'] . self::PARENTITEM_SUFFIX)
			);
		} else {
			$itemTag->appendChild(
				$this->bulkXml->createElement("SKU", $item['item_no'])
			);
		}
		


		//Beschreibung mit Template & all_shop_codes
		if (!empty($item["marketplace_ebay_item_template"])) {
            $template = get_text_module($item["company"],$item["marketplace_ebay_item_template"]);
        } else {
            $template = get_text_module($item["company"],$GLOBALS["shop_language"]["marketplace_ebay_item_template"]);
        }

        $cancellation_text = get_text_module($GLOBALS["shop"]["company"],$GLOBALS["shop_language"]["marketplace_cancellation_text"]);

		$descriptionText = replace_item_placeholders($template,$item["company"],$item["shop_code"],$item["language_code"],
			$item["item_no"],$item["item_no"],$item["variant_type"],$GLOBALS['shop']['variant_typ'],$GLOBALS["shop"]["shop_url"],$GLOBALS["shop_setup"]["image_config"],4,500000,$cancellation_text, $GLOBALS['mysql_con']);
		
		$cData = $this->bulkXml->createCDATASection($descriptionText);
		$descriptionTag = $itemTag->appendChild(
			$this->bulkXml->createElement("Description")
		);
		$descriptionTag->appendChild($cData); 
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("InventoryTrackingMethod", "SKU")
		);
		
		$category = $itemTag->appendChild(
			$this->bulkXml->createElement("PrimaryCategory")
		);
		$category->appendChild($this->bulkXml->createElement("CategoryID", $item_has_category['marketplace_category_code']));
		
		// EAN (nur wenn mutterartikel keine Varianten hat)
		//$ean_result = get_cross_reference_by_description($item, "EAN");
        $standard_product_id = get_standard_product_id($item);
		if($with_variant === false && !empty($standard_product_id)) {
			$ProductListingDetails = $itemTag->appendChild(
				$this->bulkXml->createElement("ProductListingDetails")
			);
			
			$ProductListingDetails->appendChild(
				$this->bulkXml->createElement("EAN", $standard_product_id['item_reference_no'])
			);
		}
		
		// Preis
		$price = number_format($item['base_price'], 2, '.', '');
		
		// Verfuegbarkeit
		$inventory = (int)$item["inventory"];
		
		// bei varianten kein preis und keine menge beim parent artikel
		if($with_variant === false) {
			$itemTag->appendChild(
				$this->bulkXml->createElement("StartPrice", $price)
			);
			
			$itemTag->appendChild(
				$this->bulkXml->createElement("Quantity", $inventory)
			);
		}
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("CategoryMappingAllowed", "true")
		);
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("ConditionID", $item['item_condition'])
		);
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("Country", $this->language_code_mapping($item['language_code'])) // z.B. DE
		);
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("Currency", $GLOBALS['shop_currency']['code']) // z.B. EUR
		);
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("DispatchTimeMax", $this->dispatch_mapping($inventory, $item)) // wieviele Tage nach Bestellung die Ware ausgeliefert wird
		);
		
		// In der Sandbox funktioniert nur Days_x
		if($this->ebayCall->isSanbox()) {
			$itemTag->appendChild(
				$this->bulkXml->createElement("ListingDuration", "Days_7")
			);
		} else {
			$itemTag->appendChild(
				$this->bulkXml->createElement("ListingDuration", "GTC")
			);
		}
		
		$itemTag->appendChild(
			$this->bulkXml->createElement("ListingType", "FixedPriceItem")
		);
		
		// Merkmale anzeigen
		if($with_variant === true) {
			// merkmale anzeigen, die keine varianten bilden
			$all_marketplace_attributes = get_category_normal_attributes($item_has_category['marketplace_category_code'], self::MARKETPLACE_TYPE);
			$all_shop_attributes = get_variant_item_attributes($all_marketplace_attributes, self::ATTRIBUTE_TYPE);
			$attributeresult = get_item_attributes($item, $all_shop_attributes, self::ATTRIBUTE_TYPE);
		} else {
			// alle merkmale anzeigen, weil keine varianten dazukommen
			$all_marketplace_attributes = get_category_attributes($item_has_category['marketplace_category_code'], self::MARKETPLACE_TYPE);

			$all_shop_attributes = get_variant_item_attributes($all_marketplace_attributes, self::ATTRIBUTE_TYPE);

			$attributeresult = get_item_attributes($item, $all_shop_attributes, self::ATTRIBUTE_TYPE);
		}
		
		if($attributeresult !== false && mysqli_num_rows($attributeresult) > 0) {
			$itemSpecifics = $itemTag->appendChild(
				$this->bulkXml->createElement("ItemSpecifics")
			);
				
			while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {

				$nameValueList = $itemSpecifics->appendChild(
					$this->bulkXml->createElement("NameValueList")
				);
				
				$nameValueList->appendChild(
					$this->bulkXml->createElement("Name", $attributeRow['headline'])
				);

                $attribute_value = get_item_attribute_value($item, $attributeRow);
				$nameValueList->appendChild(
					$this->bulkXml->createElement("Value", $attribute_value)
				);

                if(!isset($this->allAttributesNoVariation[$attributeRow['headline']])) {
                    $this->allAttributesNoVariation[$attributeRow['headline']] = array();
                }

                $this->allAttributesNoVariation[$attributeRow['headline']][$attribute_value] = true;
			}

            //MPN als Artikelnummer
            if (!isset($this->allAttributesNoVariation['MPN'])) {
                $nameValueList = $itemSpecifics->appendChild(
                    $this->bulkXml->createElement("NameValueList")
                );

                $nameValueList->appendChild(
                    $this->bulkXml->createElement("Name", "MPN")
                );

                $nameValueList->appendChild(
                    $this->bulkXml->createElement("Value", $item['item_no'])
                );
            }
		}
		
		// Zahlungsarten
		// Beschreibungen siehe hier: http://developer.ebay.com/devzone/xml/docs/reference/ebay/types/BuyerPaymentMethodCodeType.html
		$payment_methods = get_payment_methods($item);
		while ($payment = mysqli_fetch_assoc($payment_methods)) {
			$itemTag->appendChild(
				$this->bulkXml->createElement("PaymentMethods", $payment['description'])
			);
			
			if($payment['description'] == "PayPal") {
				$itemTag->appendChild(
					$this->bulkXml->createElement("PayPalEmailAddress", $GLOBALS['shop']['paypal_email'])
				);
			}
		}
		
		// Artikelbilder bei variant_typ = 1 sollen nicht beim Mutterartikel vorkommen.
		// sonst sind die bilder beim mutter und kindartikel gleich.
		if ($with_variant === false || $GLOBALS['shop']['variant_typ'] == 0) {
			$imagesresult = get_item_images_clean($item, $GLOBALS["shop_setup"]["image_config"]);
			if(mysqli_num_rows($imagesresult) > 0) {
				$picturedetails = $itemTag->appendChild(
					$this->bulkXml->createElement("PictureDetails")
				);
				
				while ($image = mysqli_fetch_assoc($imagesresult)) {
					$picturedetails->appendChild($this->bulkXml->createElement("PictureURL", rtrim($GLOBALS['shop']['shop_url'],'/') . '/' . $GLOBALS["shop_setup"]["image_config"][4]["path"] . "/" . $image['filename']));
				}
			}
		}
		
		// ZIP
		if($item['item_location_zip'] > 0) {
			$zip = $item['item_location_zip'];
		} else {
			$zip = $GLOBALS['shop']['shop_location_zip'];
		}
		$itemTag->appendChild(
			$this->bulkXml->createElement("PostalCode", $zip)
		);
		
		// Rueckgabe
		$returnpolicy = $itemTag->appendChild(
			$this->bulkXml->createElement("ReturnPolicy")
		);
		$returnpolicy->appendChild($this->bulkXml->createElement("ReturnsAcceptedOption", "ReturnsAccepted"));
		$returnpolicy->appendChild($this->bulkXml->createElement("ReturnsWithinOption", "Months_1"));
		//$returnpolicy->appendChild($this->bulkXml->createElement("Description", substr($GLOBALS["tc"]["ebay_widerruf"], 0, 5000)));
        $returnpolicy->appendChild($this->bulkXml->createElement("Description", $cancellation_text));
		$returnpolicy->appendChild($this->bulkXml->createElement("ShippingCostPaidByOption", "Buyer"));
		
		// Versand
		$shippingdetails = $itemTag->appendChild(
			$this->bulkXml->createElement("ShippingDetails")
		);
		$shippingdetails->appendChild($this->bulkXml->createElement("ShippingType", "Flat"));
		
		// Versandmoeglichkeiten
		// zu entnehmen aus: http://developer.ebay.com/DevZone/XML/docs/Reference/ebay/types/ShippingServiceCodeType.html
		$shipping_options = get_shipping_options($item);
		$order = 1;
		while ($shipping = mysqli_fetch_assoc($shipping_options)) {
			$ShippingServiceOptions = $shippingdetails->appendChild(
				$this->bulkXml->createElement("ShippingServiceOptions")
			);
			
			$ShippingServiceOptions->appendChild($this->bulkXml->createElement("ShippingServicePriority", $order));
			$ShippingServiceOptions->appendChild($this->bulkXml->createElement("ShippingService", $shipping['description']));
			
			if((int)$shipping['shipping_cost'] == 0) {
				$ShippingServiceOptions->appendChild($this->bulkXml->createElement("FreeShipping", "true"));
			} else {
				$ShippingServiceOptions->appendChild($this->bulkXml->createElement("ShippingServiceAdditionalCost", "0.00")); // keine zusaetzlichen versandkosten, wenn mehr als 1 artikel bestellt
				$ShippingServiceOptions->appendChild($this->bulkXml->createElement("ShippingServiceCost", $shipping['shipping_cost']));
			}
			
			$order++;
		}
		
		// in welche laender versendet wird
		$countries_result = get_countries(2);
    	if (@mysqli_num_rows($countries_result) > 0) {
    		while ($val = mysqli_fetch_array($countries_result)) {
    			$itemTag->appendChild(
					$this->bulkXml->createElement("ShipToLocations", $val['country_code'])
				);
    		}	
    	}
			
		return $itemTag;
	}
	
	protected function create_revise_inventory_status_request($parameter) {
		
		$bulkXmlFile = $this->start_BulkDataExchangeRequest();
		
		while ($item = mysqli_fetch_assoc($this->items_result)) {
			
			$this->bulkXml = null;
			
			// pruefen ob die bulkdateien kurz vor ueberschreitung des limits ist
			$bulkSize = filesize($bulkXmlFile);
			if(!isset($parameter['item_no']) && ($bulkSize + 4000000) > $parameter['maxFileSize']) {
				
				// weitere items in einem extra request durchlaufen weil die bulk langsam zu gross wird.
				$this->new_queue(self::MARKETPLACE_TYPE, $parameter['operation'], $parameter, 0, false);
				break;
			}

            $query = "UPDATE shop_marketplace_item_update set marketplace_update_inventory = 0, marketplace_update_price = 0 
                              where 
                              item_no = '" . $item['item_no'] . "'
                              AND company = '" . $GLOBALS["shop"]["company"] . "'
                                AND shop_code = '" . $GLOBALS["shop"]["code"] . "'
                                AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                AND item_shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
                                AND item_language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                              ";
			@mysqli_query($GLOBALS['mysql_con'], $query);
			
			$this->ebayCall = new EbayTrading();
			$this->ebayCall->setCallname("ReviseInventoryStatus");
			$this->bulkXml = $this->ebayCall->createBaseDocument();
			$fixedPriceItem = $this->bulkXml->documentElement;
		
			// Preis
			$price = number_format($item['base_price'], 2, '.', '');
			
			// Verfuegbarkeit
			$inventory = (int)$item["inventory"];
			
			$fixedPriceItem->appendChild(
				$this->bulkXml->createElement("MessageID", $item['item_no'])
			);
			
			$itemInventoryStatus = $fixedPriceItem->appendChild(
				$this->bulkXml->createElement("InventoryStatus")
			);
			
				$itemInventoryStatus->appendChild(
					$this->bulkXml->createElement("SKU", $item['item_no'])
				);
				
				if($item['marketplace_item_id'] != "") {
					$itemInventoryStatus->appendChild(
						$this->bulkXml->createElement("ItemID", $item['marketplace_item_id'])
					);
				}
				
				
				$itemInventoryStatus->appendChild(
					$this->bulkXml->createElement("Quantity", $inventory)
				);
				
				$itemInventoryStatus->appendChild(
					$this->bulkXml->createElement("StartPrice", $price)
				);
			
			$this->insertMessageToBulk("ReviseInventoryStatusRequest");
		}
		
		$this->end_BulkDataExchangeRequest();
	}
	
	protected function helper_get_variation_specific_name() {
		if(count($this->allAttributes) == 0) {
			return false;
		}
		
		$tempArray = array();
		foreach($this->allAttributes as $arraykey => $arrayValue) {
			$tempArray[$arraykey] = count($arrayValue);
		}
				
		$maxs = array_keys($tempArray, max($tempArray));
		if(isset($maxs[0])) {
			return $maxs[0];
		} else {
			return false;
		}
	}
	
	protected function helper_update_payment_method($result, $payment_methods) {
		while ($row = mysqli_fetch_assoc($result)) {
			$query = "DELETE FROM shop_marketplace_payments where marketplace_type = 2 and category_code = '" . $row['category_code'] . "'";
			mysqli_query($GLOBALS['mysql_con'], $query);
			
			foreach($payment_methods as $paymentMethod) {
				$query = "INSERT INTO shop_marketplace_payments (marketplace_type, category_code, payment_method) VALUES (
					2,
					'" . $row['category_code'] . "',
					'" . $paymentMethod . "'
				)";
				mysqli_query($GLOBALS['mysql_con'], $query);
			}
			
			// child categories haben die selbe einstellung:
			$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $row['category_code'] . "' and category_code != '" . $row['category_code'] . "'";
			$tempresult = mysqli_query($GLOBALS['mysql_con'], $query);
			
			if(mysqli_num_rows($tempresult) > 0) {
				$this->helper_update_payment_method($tempresult, $payment_methods);	
			}
		}
	}
	
	protected function helper_update_condition($result, $conditions) {
		while ($row = mysqli_fetch_assoc($result)) {
			$query = "DELETE FROM shop_marketplace_conditions where marketplace_type = 2 and category_code = '" . $row['category_code'] . "'";
			mysqli_query($GLOBALS['mysql_con'], $query);
			
			foreach($conditions as $condition) {
				$query = "INSERT INTO shop_marketplace_conditions (marketplace_type, category_code, condition_code, codition_description) VALUES (
					2,
					'" . $row['category_code'] . "',
					'" . $condition->ID . "',
					'" . $condition->DisplayName . "'
				)";
				mysqli_query($GLOBALS['mysql_con'], $query);
			}
			
			// child categories haben die selbe Artikelzustand Einstellung
			$query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 2 and parent_category_code = '" . $row['category_code'] . "' and category_code != '" . $row['category_code'] . "'";
			$tempresult = mysqli_query($GLOBALS['mysql_con'], $query);
			
			if(mysqli_num_rows($tempresult) > 0) {
				$this->helper_update_condition($tempresult, $conditions);	
			}
		}
	}
	
	protected function get_request_name($parameter = array(), $requestName = "") {
		if(isset($parameter['function'])) {
			switch($parameter['function']) {
				case 'update_price':
				case 'update_inventory':
					return "ReviseInventoryStatus";
					break;
					
				case 'update_images':
					return "ReviseFixedPriceItem";
					break;
					
				default:
					return $requestName;
					break;
				
			}
		} else {
			return $requestName;
		}
	}
	
	protected function handle_downloaded_result_row($xmlKey, $xmlItem) {
		
		switch($xmlKey) {
			case 'ReviseInventoryStatus':
			case 'ReviseInventoryStatusRequest':
				foreach($xmlItem->InventoryStatus as $inventoryStatus) {
					$item_no = $inventoryStatus->SKU;
					$item = get_item($GLOBALS['shop']['company'], $GLOBALS['shop']['code'], $GLOBALS['shop_language']['code'], $item_no);
					
					$this->set_item_submission_success($item, "marketplace_update_inventory");
					$this->set_item_submission_success($item, "marketplace_update_price");
				}
				break;
				
			case 'ActiveInventoryReport':
			
				break;
				
			default:
				if(isset($xmlItem->CorrelationID)) {
					$item_no = $xmlItem->CorrelationID;
				} elseif(isset($xmlItem->SKU)) {
					$item_no = $xmlItem->SKU;
				} else {
					$item_no = "";
				}

				$item = null;
				if($item_no != "") {
					$item = get_item($GLOBALS['shop']['company'], $GLOBALS['shop']['code'], $GLOBALS['shop_language']['code'], $item_no);
				}
				
				if($xmlItem->Ack == 'Failure') {
					$errors = array();
					foreach($xmlItem->Errors as $error) {
						$errors[] = $error->ShortMessage;
						$this->insert_item_error($item_no, self::MARKETPLACE_TYPE, $xmlKey, $error->ErrorCode, $error->LongMessage);
					}
					
					if($item_no != "") {
						$this->set_item_submission_error($item_no, join(", ", $errors));
					}
				} else {
					if($item !== null) {
						$this->set_item_submission_success($item, "marketplace_update");
						$this->set_item_submission_success($item, "marketplace_update_images");
					}
				}
				break;
		}
	}
	
	protected function handle_order($order) {
		
		// pruefen ob bestellung vorhanden
		$orderExists = false;
		$sqlCommand = "INSERT INTO";
		$query = "SELECT * from shop_sales_header 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			and marketplace_order = '" . $order->OrderID . "'
			limit 1
		";
		$order_result = @mysqli_query($GLOBALS['mysql_con'], $query);
		if (@mysqli_num_rows($order_result) == 1) {
			$order_array = @mysqli_fetch_array($order_result);
			$order_no = $order_array['order_no'];
			$sales_header_id = $order_array['id'];
			$orderExists = true;
			$sqlCommand = "UPDATE";
		} 
		
		// neue interne bestellnummer ziehen, wenn es sich um eine neue bestellung handelt
		if($orderExists === false) {
			$order_no_query  = "SELECT order_no FROM shop_sales_header ORDER BY order_no DESC LIMIT 1";
			$order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
			if (@mysqli_num_rows($order_no_result) == 1) {
			    $order_no_array = @mysqli_fetch_array($order_no_result);
			    $order_no       = $order_no_array["order_no"] + 1;
			} else {
			    $order_no = 100000;
			}
		}
		
		// bezahlmethode auslesen
		$query = "SELECT line_no, payment_cost from shop_payment_option 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			and description = '" . $order->CheckoutStatus->PaymentMethod . "'
			limit 1
		";
		$result = @mysqli_query($GLOBALS['mysql_con'], $query);
		$row = mysqli_fetch_assoc($result);
		$payment_line_no = $row['line_no'];
		$payment_cost = $row['payment_cost'];
		
		// versandmethode auslesen
		$query = "SELECT line_no from shop_shipping_option 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			and description = '" . $order->ShippingServiceSelected->ShippingService . "'
			limit 1
		";
		$result = @mysqli_query($GLOBALS['mysql_con'], $query);
		$row = mysqli_fetch_assoc($result);
		$shipping_line_no = $row['line_no'];
		
		// bestelldatum
		$order_date = strtotime($order->CreatedTime);
		$order_date = date('Y-m-d', $order_date);

        // Zahlung erfolg?
        $markedAsPaid = 0;
        if (isset($order->PaidTime)) {
            $markedAsPaid = 1;
        }

		
		$query = "$sqlCommand shop_sales_header
		  SET company = '" . $GLOBALS['shop']['company'] . "',
		  	  shop_code = '" . $GLOBALS['shop']['code'] . "',
		  	  language_code = '" . $GLOBALS['shop_language']['code'] . "',
		  	  order_no = $order_no,
			  marketplace_order = '" . $order->OrderID . "',
		  	  shop_customer_id = 0,
		  	  customer_no = 0,
		  	  shop_user_id = 0,
		  	  user_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->BuyerUserID) . "',
		  	  user_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->TransactionArray->Transaction->Buyer->Email) . "',
		  	 
		  	  ship_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->Name) . "',
		  	  ship_to_name_2 = '',
		  	  ship_to_address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->Street1) . "',
		  	  ship_to_address_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->Street2) . "',
		  	  ship_to_post_code = '" . $order->ShippingAddress->PostalCode . "',
		  	  ship_to_city = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->CityName) . "',
		  	  ship_to_country = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->Country) . "',
		  	  ship_to_contact = '',
		  	  ship_to_telephone_no = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->ShippingAddress->Phone) . "',

		  	  bill_to_customer_no = '',
		  	  bill_to_name_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order->BuyerUserID) . "',
		  	  order_date = '" . $order_date . "',

		  	  your_reference = '',
		  	  your_comment = '',
		  	  subtotal='" . $order->Subtotal . "',
		  	  online_discount=0,
		  	  online_discount_amount=0,
			  invoice_discount=0,
			  invoice_discount_amount=0,
			  small_quantity_charge_amount=0,
			  total='" . $order->Total . "',
			  drop_shipment=0,
			  shipping_option_line_no='" . $shipping_line_no . "',
			  shipping_cost='" . $order->ShippingServiceSelected->ShippingServiceCost . "',
			  payment_option_line_no = '" . $payment_line_no . "',
			  payment_cost = '" . $payment_cost . "',
			  payment_transaction_id = '',
			  currency_code = '" . $GLOBALS['shop_currency']['code'] . "',
			  coupon_amount = 0,
			  value_coupon = 0,
			  coupon_code = '',
			  shipping_coupon = 0,
			  user_salutation = '',
			  newsletter_registration = 0,
			  order_error = 0,
			  marketplace_sales_record_number = '" . $order->ShippingDetails->SellingManagerSalesRecordNumber . "',
			  marketplace_order_status = '" . $order->OrderStatus . "',
			  marked_as_paid = ".$markedAsPaid."";
			  
		if($order->CheckoutStatus->PaymentMethod == "PayPal") {
			$query .= ",marketplace_paypal_transaction_id = '" . $order->ExternalTransaction->ExternalTransactionID . "' ";
		}
		
		if($order->OrderStatus == "Completed") {
			$query .= ",payment_processed = NOW() ";
		}
		
		if($orderExists === true && ($order_array['marketplace_order_status'] != $order->OrderStatus || $order_array["marked_as_paid"] != $markedAsPaid)) {
			$query .= ",update_insert = 1 ";
		}
	
		// where und bei Neuanlage die ID auslesen
		if($orderExists === true) {
			$query .= " where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
		  	and marketplace_order = '" . $order->OrderID . "'";
		  	@mysqli_query($GLOBALS['mysql_con'], $query);
		} else {
			@mysqli_query($GLOBALS['mysql_con'], $query);
			$sales_header_id = mysqli_insert_id($GLOBALS['mysql_con']);
		}
		
		$check_item_nos = array();
		foreach($order->TransactionArray->Transaction as $transaction) {
			if(isset($transaction->Variation->SKU)) {
				$item_no = $transaction->Variation->SKU;
				$variation_code = $transaction->Variation->VariationSpecifics->NameValueList->Value;
			} else {
				$item_no = $transaction->Item->SKU;
				$variation_code = "";
			}
			
			// item auslesen
			$query = "SELECT id from shop_item 
				where company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
				and item_no = '" . $item_no . "'
				limit 1
			";
			$result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $shop_item_id = $row['id'];
                $check_item_nos[] = $shop_item_id;
            }
			
			// pruefen ob die bestellzeile schon existiert
			$lineExists = false;
			$lineSqlCommand = "INSERT INTO";
			$lineId = 0;
			$query = "SELECT id from shop_sales_line 
				where shop_item_id = '" . $shop_item_id . "'
				and shop_sales_header_id = '" . $sales_header_id . "'
				limit 1
			";

            $query = "SELECT id from shop_sales_line 
				where marketplace_line_id = '" . $transaction->Item->ItemID . "-" . $transaction->TransactionID . "'
				and shop_sales_header_id = '" . $sales_header_id . "'
				limit 1
			";

			$order_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
			if (@mysqli_num_rows($order_line_result) == 1) {
				$row = mysqli_fetch_assoc($result);
				$lineExists = true;	
				$lineSqlCommand = "UPDATE";
				$lineId = $row['id'];
			}

			//Beschreibung aus Variation


            $variationDescription = "";
            if(isset($transaction->Variation->SKU)) {
                foreach ($transaction->Variation->VariationSpecifics->NameValueList as $nameValueList) {
                    if ($variationDescription == "") {
                        $variationDescription .= $nameValueList->Name . ":" . $nameValueList->Value;
                    } else {
                        $variationDescription .= "," . $nameValueList->Name . ":" . $nameValueList->Value;
                    }
                }
                if ($variationDescription != "") {
                    $variationDescription = "[" . $variationDescription . "]";
                }
            }
			
			//Speichern der Bestellzeilen
            $query = "$lineSqlCommand shop_sales_line
					  SET company = '" . $GLOBALS['shop']['company'] . "',
						  shop_code = '" . $GLOBALS['shop']['code'] . "',
						  language_code = '" . $GLOBALS['shop_language']['code'] . "',
						  order_no = '" . $order_no . "',
						  shop_sales_header_id = '" . $sales_header_id . "',
						  shop_item_id = '" . $shop_item_id . "',
						  item_no = '" . $item_no . "',
						  variant_code = '" . $variation_code . "',
						  description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $variationDescription) . "',
						  summary = '',
						  list_price = '" . $transaction->TransactionPrice . "',
						  unit_price = '" . $transaction->TransactionPrice . "',
						  quantity = '" . $transaction->QuantityPurchased . "',
						  line_amount = '" . (float)$transaction->TransactionPrice * (float)$transaction->QuantityPurchased . "',
						  allow_invoice_disc = 1,
						  update_insert = 0,
						  marketplace_line_id = '" . $transaction->Item->ItemID . "-" . $transaction->TransactionID . "'";
						  
			if($lineExists === true) {
				$query .= " where id = $lineId ";
			}
						  
            mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		
		// pruefen ob bestellzeilen existieren, die nicht mehr in der XML vorkamen, weil evtl bestellung geaendert
		if(count($check_item_nos)) {
			$query = "UPDATE shop_sales_line
				set to_delete = 1, update_insert = 1 
				where shop_sales_header_id = '" . $sales_header_id . "'
				and shop_item_id NOT IN ('" . join("', '", $check_item_nos) . "')";
			mysqli_query($GLOBALS['mysql_con'], $query);
		}
		
		// bei einer neuen ebay bestellung muessen die rechnungsdaten extra vom benutzeraccount ausgelesen werden
		if($orderExists === false) {
			$param = array(
				'BuyerUserID' => (string)$order->BuyerUserID,
				'shop_sales_header_id' => $sales_header_id,
				'ItemID' => (string)$order->TransactionArray->Transaction->Item->ItemID // ist noetig, sonst gibt es keine daten her
			);

			$this->new_queue(self::MARKETPLACE_TYPE, "update_order_byuer", $param, 0, false);
		}
	}
	
	/**
	 * Verarbeitet Informationen ueber ein Item.
	 * Bis jetzt benutzt von: get_listings().
	 * 
	 */
	protected function handle_active_listing($item) {
		if(isset($item->Variations->Variation)) {
			if($GLOBALS['shop']['variant_typ'] == 0) {
				insert_marketplace_item(self::MARKETPLACE_TYPE, $item->SKU, 0, 0, $item->ItemID);
			}
			
			foreach($item->Variations->Variation as $variation) {
				insert_marketplace_item(self::MARKETPLACE_TYPE, $variation->SKU, $variation->Quantity, $variation->StartPrice);
			}
		} else {
			insert_marketplace_item(self::MARKETPLACE_TYPE, $item->SKU, $item->Quantity, $item->StartPrice, $item->ItemID);
		}
	}
	
	protected function language_code_mapping($code) {
		switch($code) {
			case 'DEU':
				return "DE";
				break;
			default:
				return "DE";
				break;
		}
	}
	
	protected function dispatch_mapping($inventory, $item) {
		if($inventory <= 0) {
			return 10;
		}
		
		if($inventory <= $item["insufficient_inventory_limit"]) {
			return 5;
		}
		
		return 1;
	}
	
	protected function setJsonFilename() {
		$this->setResultFilename($this->getQueueId(), "json");
	}
	
	protected function start_BulkDataExchangeRequest() {
		$bulkXmlFile = tempnam(__DIR__."/../xml/", "bulkxml");
		$newXmlName = $bulkXmlFile . ".xml";
		rename($bulkXmlFile, $newXmlName);
		chmod($newXmlName, 0755);

		// xml manuell erstellen.
		$xml = '<?xml version="1.0" encoding="UTF-8"?><BulkDataExchangeRequests>';	
		$xml .= '<Header><SiteID>' . EbayTrading::SITEID . '</SiteID><Version>' . EbayTrading::VERSION . '</Version></Header>';
		file_put_contents($newXmlName, $xml, FILE_APPEND);
		
		$this->setBulkXmlFilename($newXmlName);
		
		return $newXmlName;
	}
	
	protected function end_BulkDataExchangeRequest() {
		file_put_contents($this->bulkXmlFilename, "</BulkDataExchangeRequests>", FILE_APPEND);
	}
	
	protected function genTime($time=false){
        if (!$time){
            $time = time();
        } else {
            $time = strtotime($time);
            
        }
        
       	return date("Y-m-d", $time)."T".date("H:i:s", $time)."Z";
    }

	protected function get_all_variation_attributes($all_variant_items, $all_shop_attributes) {
		$temp_result = array();

		$countVariants = 0;
		foreach($all_variant_items as $item_no => $variant_item) {
			$attributeresult = get_item_attributes($variant_item, $all_shop_attributes, self::ATTRIBUTE_TYPE);
			while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
				if(!isset($temp_result[$attributeRow['headline']])) {
					$temp_result[$attributeRow['headline']] = array('count' => 0, 'values' => array());
				}

				$temp_result[$attributeRow['headline']]['count']++;
				$temp_result[$attributeRow['headline']]['values'][] = get_item_attribute_value($variant_item, $attributeRow);;
			}

			$countVariants++;
		}

		$final_result = array();
		foreach($temp_result as $headline => $result) {
			// Wenn alle Varianten dieses Attribut zugeordnet haben, dann kann man damit ebay varianten bilden
			if($result['count'] == $countVariants) {
				$final_result[$headline] = $result['values'];
			}
		}


		return $final_result;
	}
	
	/**********************************************
	 * Hilfsfunktionen
	 * Uebernommern von https://ebaydts.com/eBayKBDetails?KBid=1472
	 * Funktionen werden benutzt um fuer den service "downloadFile"
	 * Eine ZIP Datei mit XML Inhalt zu erstellen.
	 **********************************************/
	
	/**
	 * Parses for the XML Response in the MIME multipart message.
	 * @param string $response MIME multipart message
	 * @return string XML Response
	 */
	protected function parseForResponseXML($response) {
		$beginResponseXML = strpos($response, '<?xml');
		
		$endResponseXML = strpos($response, '</downloadFileResponse>',
			$beginResponseXML);
		
		//Assume a service level error and die.
		if($endResponseXML === FALSE) {
			//@TODO Fehlerbehandlung
		}	
		
		$endResponseXML += strlen('</downloadFileResponse>');
		
		return substr($response, $beginResponseXML,
			$endResponseXML - $beginResponseXML);
	}
	
	/**
	 * Parses for the file bytes between the MIME boundaries.
	 * @param $uuid UUID corresponding to the Content-ID of the file bytes.
	 * @param string $response MIME multipart message
	 * @return string bytes of the file
	 */
	protected function parseForFileBytes($uuid, $response) {
		$contentId = 'Content-ID: <' . $uuid . '>';
		
		$mimeBoundaryPart = strpos($response,'--MIMEBoundaryurn_uuid_');
		
		$beginFile = strpos($response, $contentId, $mimeBoundaryPart);
		$beginFile += strlen($contentId);
		
		//Accounts for the standard 2 CRLFs.
		$beginFile += 4;
		
		$endFile = strpos($response,'--MIMEBoundaryurn_uuid_',$beginFile);
		
		//Accounts for the standard 1 CRLFs.
		$endFile -= 2;
		
		$fileBytes = substr($response, $beginFile, $endFile - $beginFile);
		
		return $fileBytes;
	}
	
	/**
	 * Parses the XML Response for the UUID to ascertain the
	 * index of the file bytes in the MIME Message.
	 * @param DomDocument $responseDOM DOM of the XML Response.
	 * @return string UUID referring to the message body
	 */
	protected function parseForXopIncludeUUID($responseDOM) {
		$xopInclude = $responseDOM->getElementsByTagName('Include')->item(0);
		$uuid = $xopInclude->getAttributeNode('href')->nodeValue;
		$uuid = substr($uuid, strpos($uuid,'urn:uuid:'));
		
		return $uuid;
	}
	
	/**
	 * Writes the response file's bytes to disk.
	 * @param string $bytes bytes comprising a file
	 * @param string $zipFilename name of the zip to be created
	 */
	protected function writeZipFile($bytes, $zipFilename) {
		$handler = fopen($zipFilename, 'wb') 
			or die("Failed. Cannot Open $zipFilename to Write!</b></p>");
		fwrite($handler, $bytes);
		fclose($handler);
	}
	
}

/**
 * Hilfsklasse, uebernommern aus dem Beispiel von:
 * https://ebaydts.com/eBayKBDetails?KBid=1472
 * 
 */
class DOMUtils {

	/**
	 * Creates a DOM for XML; Defaults to using pretty print.
	 * @param string $xml XML Blob
	 * @return DomDocument DOM representation of the XML
	 */
	public static function createDOM($xml)
	{
		$dom = new DomDocument();
		$dom->preserveWhitespace = false;
		$dom->loadXML($xml);
		$dom->formatOutput = true;
		
		return $dom;
	}
	
}