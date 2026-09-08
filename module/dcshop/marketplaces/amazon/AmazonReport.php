<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonCore.php';

class AmazonReport extends AmazonCore {
	
	/**
     * URLS fuer APIs
     * @access protected
     * @var array 
     */
    protected $api_urls = array(
    	'production'=>'https://mws.amazonservices.de',
		'production_na' => 'https://mws.amazonservices.com' // North America
    );
	
	public function setService() {
		$marketplaceArray = array();
		$apiUrl = $this->api_urls['production'];
		if($this->get_marketplace_id() != "") {
			$marketplaceArray[] = $this->get_marketplace_id();
		}
		
		if($this->get_marketplace_fr() != "") {
			$marketplaceArray[] = $this->get_marketplace_fr();
		}
		
		if($this->get_marketplace_it() != "") {
			$marketplaceArray[] = $this->get_marketplace_it();
		}
		
		if($this->get_marketplace_es() != "") {
			$marketplaceArray[] = $this->get_marketplace_es();
		}
		
		if($this->get_marketplace_uk() != "") {
			$marketplaceArray[] = $this->get_marketplace_uk();
		}

		if($this->get_marketplace_us() != "") {
			$marketplaceArray = array(); // mischen von EU und NA nicht moeglich
			$marketplaceArray[] = $this->get_marketplace_us();
			$apiUrl = $this->api_urls['production_na'];
		}
		
		$this->marketplaceIdArray = array("Id" => $marketplaceArray);	
		
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
	
	public function getMarketplaceIdArray() {
		return $this->marketplaceIdArray;
	}
	
	public function requestReport() {
		
	}
}