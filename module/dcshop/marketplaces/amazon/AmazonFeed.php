<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'sdk/MarketplaceWebService/Client.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonCore.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonFeedProduct.php';

class AmazonFeed extends AmazonCore {

	/**
	 * @var MarketplaceWebService_Client
	 */
	protected $service;

	/**
     * URLS fuer APIs
     * @access protected
     * @var array 
     */
    protected $api_urls = array(
    	'production'=>'https://mws.amazonservices.de',
		'production_na' => 'https://mws.amazonservices.com' // North America
    );
    
    /**
	 * XML Dokument zur weitergabe an Amazon
	 * 
	 * @access protected 
	 * @var DOMDocument
	 */
	protected $xmlDocument = "";
	
	protected $products = array();
	
	protected $feedType;
	
	protected $messageType;

	protected $showPurgeAndReplace = true;

	protected $purgeAndReplace = false;

    /**
     * @param bool $showPurgeAndReplace
     */
    public function setShowPurgeAndReplace($showPurgeAndReplace)
    {
        $this->showPurgeAndReplace = $showPurgeAndReplace;
    }

    /**
     * @return bool
     */
    public function isPurgeAndReplace()
    {
        return $this->purgeAndReplace;
    }

    /**
     * @param bool $purgeAndReplace
     */
    public function setPurgeAndReplace($purgeAndReplace)
    {
        $this->purgeAndReplace = $purgeAndReplace;
    }

	public function setFeedType($feedType) {
		$this->feedType = $feedType;
	}
	
	public function setMessageType($messageType) {
		$this->messageType = $messageType;
	}
	
	public function getFeedType() {
		return $this->feedType;
	}
	
	public function getMessageType() {
		return $this->messageType; 
	}
	
	protected function getXmlFile() {
		return $this->xmlFileName;
	}
	
	protected function setXmlFile($xmlFileName) {
		$this->xmlFileName = $xmlFileName;
	}
	
	protected function setService() {
		$this->marketplaceIdArray = array("Id" => array($this->get_marketplace_id()));
		$apiUrl = $this->api_urls['production'];

		if($this->get_marketplace_us() != "") {
			$this->marketplaceIdArray = array("Id" => array($this->get_marketplace_us()));
			$apiUrl = $this->api_urls['production_na'];
		}
		
		$this->config = array (
		  'ServiceURL' => $apiUrl,
		  'ProxyHost' => null,
		  'ProxyPort' => -1,
		  'MaxErrorRetry' => 3,
		);
		
		$this->service = new MarketplaceWebService_Client(
		     $this->get_aws_access_key_id(), 
		     $this->get_aws_secret_access_key(), 
		     $this->config,
		     $this->get_application_name(),
		     $this->get_application_version()
		);
	}
	
	public function getService() {
		return $this->service;
	}
	
	public function submitFeed() {
	    $feedHandle = @fopen($this->getXmlFile(), 'rw+');
		rewind($feedHandle);
		
		$request = new MarketplaceWebService_Model_SubmitFeedRequest();
		$request->setMerchant($this->get_merchant_id());
		$request->setMarketplaceIdList($this->marketplaceIdArray);
		$request->setFeedType($this->getFeedType());
		$request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
		rewind($feedHandle);
		$request->setPurgeAndReplace(false);
		$request->setFeedContent($feedHandle);
		
		if($this->get_mws_auth_token() != "") {
			$request->setMWSAuthToken($this->get_mws_auth_token());
		}
		
		rewind($feedHandle);
		
		$response = $this->service->submitFeed($request);
		
		@fclose($feedHandle);
		
		return $response;
	}
	
	public function addProduct(AmazonFeedProduct $product) {
		$this->products[] = $product;
	}
	
	public function createBaseDocument() {
		$bulkXmlFile = tempnam(__DIR__."/../xml/", "bulkxml");
		$newXmlName = $bulkXmlFile . ".xml";
		rename($bulkXmlFile, $newXmlName);
		chmod($newXmlName, 0755);

		// xml manuell erstellen.
		$xml = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';	
		$xml .= '<Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>' . $this->get_merchant_id() . '</MerchantIdentifier></Header>';
		$xml .= '<MessageType>' . $this->getMessageType() . '</MessageType>';
		if ($this->showPurgeAndReplace) {
		    if ($this->isPurgeAndReplace()) {
                $xml .= '<PurgeAndReplace>true</PurgeAndReplace>';
            } else {
                $xml .= '<PurgeAndReplace>false</PurgeAndReplace>';
            }

        }
		file_put_contents($newXmlName, $xml, FILE_APPEND);
		
		$this->setXmlFile($newXmlName);
		
		return $newXmlName;
	}
	
	public function endBaseDocument() {
		file_put_contents($this->getXmlFile(), "</AmazonEnvelope>", FILE_APPEND);
	}
}