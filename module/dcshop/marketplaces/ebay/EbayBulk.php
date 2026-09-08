<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayCore.php';

class EbayBulk extends EbayCore {
	
	/**
	 * Id fuer ebay deutschland
	 * 
	 * @static
	 * @var int
	 */
	const SITEID = 77;
	
	/**
	 * Version der Trading API.
	 * Entommen von: http://developer.ebay.com/DevZone/XML/docs/ReleaseNotes.html
	 * 
	 * @static
	 * @var int
	 */
	const VERSION = "1.5.0";
	
	/**
     * Ressponse von ebay Anfragen
     * @var string
     */
    public $results = FALSE;
	
	/**
     * Error Report Array
     * @var array
     * @access public 
     */
    public $error = FALSE;
	
	/**
	 * Request Header
	 * 
	 * @access protected
	 * @var array
	 */
	protected $headers = array();
	
	/**
	 * Funktion, die aufgerufen werden soll
	 * 
	 * @access protected
	 * @var string
	 */
	protected $callname = '';
	
	/**
	 * XML Dokument zur weitergabe an eBay
	 * 
	 * @access protected 
	 * @var DOMDocument
	 */
	protected $xmlDocument = "";
	
	/**
     * URLS fuer APIs
     * @access protected
     * @var array 
     */
    protected $api_urls = array(
    	'production'=>'https://webservices.ebay.com/BulkDataExchangeService',
    	'sandbox'=>'https://webservices.sandbox.ebay.com/BulkDataExchangeService'
    );
    
    /**
     * XML reponse
     * 
     * @access protected
     * @var string
     */
	protected $xmlResponse = "";
	
	/**
	 * Setzt den call (Funktionsname), der bei ebay aufgerufen wird 
	 * 
	 * @access protected
	 */
	public function setCallname($callname) {
		$this->callname = $callname;
	}
	
	/**
	 * Gibt den ebay call namen zurueck
	 * 
	 * @access public
	 * @return string
	 */
	public function getCallname() {
		return $this->callname;
	}
	
	public function setXml($xml) {
		$this->xml = $xml;
	}
	
	public function getXmlDocument() {
		return $this->xmlDocument;
	}
	
	/**
	 * Aufruf einer ebay Trading methode.
	 * Sollte vom Hauptprogramm aufgerufen werden.
	 * Damit koennen alle Methoden der Trading Api von ebay aufgerufen werden
	 * 
	 * @access public
	 */
	public function call() {
		
		// Headers
       	$this->headers = array(
            'X-EBAY-SOA-SERVICE-NAME: BulkDataExchangeService',
            'X-EBAY-SOA-OPERATION-NAME: ' . $this->getCallname(),
            'CONTENT-TYPE: text/xml',
            'X-EBAY-SOA-SECURITY-TOKEN: ' . $this->get_auth_token()
        );
		
		$this->send();
	}
	
	/**
	 * Erstellt das Basisdokument fuer alle XML Calls bei ebay
	 * 
	 * @access protected
	 */
	public function createBaseDocument() {
		$this->xmlDocument = new DOMDocument('1.0', "UTF-8");
		
		// root
		$root = $this->xmlDocument->createElement($this->getCallname() . "Request");
		$root->setAttribute("xmlns", "http://www.ebay.com/marketplace/services");
		$this->xmlDocument->appendChild($root);
		
		return $this->xmlDocument;
	}
	
	/**
	 * Erstellt die xml elemente fuer GeteBayDetails
	 * 
	 * @access protected
	 */
	protected function createGeteBayDetailsString() {
		$root = $this->xmlDocument->getElementsByTagName($this->getCallname() . "Request")->item(0);
		$root->appendChild(
			$this->xmlDocument->createElement("DetailName", "ReturnPolicyDetails")
		);
	}
	
	/**
	 * Send eine Anfrage an ebay und speichert den result in $this->results
	 * 
	 * @access protected
	 * @return bool
	 */
	protected function send() {
		
		if($this->sandbox === true) {
			$url = $this->api_urls['sandbox'];
		} else {
			$url = $this->api_urls['production'];
		}
		
		// Setup
        $ch = curl_init();
        // Options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xmlDocument->saveXML());
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 0 );
        
        // Execute
        $response = curl_exec($ch);
        // Report Errors
        if (curl_errno($ch) !== 0) {
            $this->error['function'] = 'Send';
            $this->error['number'] = curl_errno($ch);
            $this->error['message'] = curl_error($ch);
            $this->error['curl_info'] = curl_getinfo($ch);
            $this->results = false;
        } else {
        	$this->xmlResponse = $response;
        	$this->results = true;
        }
        // Close
        curl_close($ch);
        return TRUE;
	}
	
	/**
	 * Gibt das result einer Anfrage zurueck
	 * 
	 * @access public
	 */
	public function getResults() {
		return $this->results;
	}
	
	public function callOk() {
		$xml = simplexml_load_string( $this->getXmlResult() );
		if($this->getResults() === true && $xml->Ack == "Success") {
			return true;
		} else {
			return false;
		}
	}
	
	public function getXmlResult() {
		return $this->xmlResponse;
	}
	
	public function getOrderStatus($order) {
		switch($order->Order->OrderStatus) {
			case "Active":
				return 1;
				
			case "Completed":
				return 2;
		}
	}
	
	public function getPaymentStatus($order) {
		switch($order->Order->OrderStatus) {
			case "Active":
				return 1;
				
			case "Completed":
				return 2;
		}
	}
}