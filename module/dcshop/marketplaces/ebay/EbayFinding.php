<?php
class EbayFinding {
	
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
	 * @var string
	 */
	const VERSION = '1.13.0';
	
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
     * @access private
     * @var array 
     */
    protected $api_urls = array(
    	'production'=>'http://svcs.ebay.com/services/search/FindingService/v1',
    	'sandbox'=>'http://svcs.sandbox.ebay.com/services/search/FindingService/v1'
    );

	/**
	 * Sanbbox modus true/false
	 * 
	 * @access protected
	 * @var bool
	 */    
    protected $sandbox = true;
	
	/**
	 * Setzt den Sandbox modus
	 * 
	 * @access public
	 * 
	 */
	public function setSandbox($sandbox = true) {
		$this->sandbox = $sandbox;
	}
	
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
            'X-EBAY-SOA-OPERATION-NAME: ' . $this->getCallname(),
            "X-EBAY-SOA-SERVICE-VERSION:" . self::VERSION,
            "X-EBAY-SOA-GLOBAL-ID: EBAY-DE",
            "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
            "X-EBAY-SOA-RESPONSE-DATA-FORMAT: XML",
            "X-EBAY-SOA-SECURITY-APPNAME: dynamicc-8068-4c6a-84e8-a8fda69c4848"
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
		$root->setAttribute("xmlns", "http://www.ebay.com/marketplace/search/v1/services");
		$this->xmlDocument->appendChild($root);
		
		return $this->xmlDocument;
	}
	
	protected function createfindItemsByKeywordsString() {
		$root = $this->xmlDocument->getElementsByTagName($this->getCallname() . "Request")->item(0);
		
		// keywords
		$keyWords = $root->appendChild(
			$this->xmlDocument->createElement("keywords", "assassins creed xbox")
		);
		
		// pagination
		$paginationInput = $root->appendChild(
			$this->xmlDocument->createElement("paginationInput")
		);
		$paginationInput->appendChild($this->xmlDocument->createElement("entriesPerPage", "50"));
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
        // Execute
        $response = curl_exec($ch);
        // Report Errors
        if (curl_errno($ch) !== 0) {
            $this->error['function'] = 'Send';
            $this->error['number'] = curl_errno($ch);
            $this->error['message'] = curl_error($ch);
            $this->error['curl_info'] = curl_getinfo($ch);
            
            $response = FALSE;
        }

        // Close
        curl_close($ch);
        $this->results = $response;
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
}