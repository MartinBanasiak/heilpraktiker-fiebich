<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'sdk/MarketplaceWebServiceProducts/Client.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonCore.php';

class AmazonProduct extends AmazonCore {
	
	/**
     * URLS fuer APIs
     * @access protected
     * @var array 
     */
    protected $api_urls = array(
    	'production'=>'https://mws-eu.amazonservices.com/Products/2011-10-01'
    );
    
    protected function setService() {
		$this->marketplaceIdArray = array("Id" => array($this->get_marketplace_id()));	
		
		$this->config = array (
		   'ServiceURL' => $this->api_urls['production'],
		   'ProxyHost' => null,
		   'ProxyPort' => -1,
		   'ProxyUsername' => null,
		   'ProxyPassword' => null,
		   'MaxErrorRetry' => 3,
		);
		
		$this->service = new MarketplaceWebServiceProducts_Client(
			$this->get_aws_access_key_id(), 
		    $this->get_aws_secret_access_key(), 
	        $this->get_application_name(),
		    $this->get_application_version(),
	        $this->config
		);
	}
	
	public function getService() {
		return $this->service;
	}
	
	public function send() {
		$this->setService();
		
		$request = new MarketplaceWebServiceProducts_Model_ListMatchingProductsRequest();
 		$request->setSellerId(MERCHANT_ID);
 		$request->setMarketplaceId(MARKETPLACE_ID);
 		$request->setQuery("Harry Potter 5");
 		
 		$response = $this->service->ListMatchingProducts($request);
 		
 		return $response;
	}
}