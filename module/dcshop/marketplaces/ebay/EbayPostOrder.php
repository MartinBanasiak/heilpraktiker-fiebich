<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'EbayCore.php';

class EbayPostOrder extends EbayCore {
	
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
    	'production'=>'https://api.ebay.com/post-order/v2/',
    	'sandbox'=>'https://api.sandbox.ebay.com/post-order/v2/'
    );
    
    /**
     * XML reponse
     * 
     * @access protected
     * @var string
     */
	protected $xmlResponse = "";
	
	protected $httpMethod = "GET";
	
	protected $params = array();
	
	/**
	 * Setzt den call (Funktionsname), der bei ebay aufgerufen wird 
	 * 
	 * @access protected
	 */
	public function setCallname($callname) {
		$this->callname = $callname;
	}
	
	public function setHttpMethod($method) {
		$this->httpMethod = $method;
	}
	
	public function getHttpMethod() {
		return $this->httpMethod;
	}
	
	public function setParam($params) {
		$this->params = $params;
	}
	
	public function getParam() {
		return $this->params;
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
            'Authorization: TOKEN ' . $this->get_auth_token(),
            'X-EBAY-C-MARKETPLACE-ID: EBAY-DE',
            'Content-Type: application/json',
            'Accept: application/json'
        );
		
		$this->send();
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
		
		$url .= $this->getCallname();
		
		// Setup
        $ch = curl_init();
        // Options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        if($this->httpMethod == "GET") {
        	curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        	if(count($this->getParam())) {
        		$url .= "?" . join("&", $this->getParam());
        	}
        }
        
        if($this->httpMethod == "POST") {
        	curl_setopt($ch, CURLOPT_POST, TRUE);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getParam()));
        }
        
        if($this->httpMethod == "PUT") {
        	curl_setopt($ch, CURLOPT_PUT, TRUE);
        }
        
        if($this->httpMethod == "DELETE") {
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        
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

	
	public function getXmlResult() {
		return $this->xmlResponse;
	}
}