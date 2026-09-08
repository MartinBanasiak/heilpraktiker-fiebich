<?php
abstract class AmazonCore {
	
	public function __construct() {
	
		// bei amazon gibt es keine sandbox
		$environment = "production";
	
		$query = "SELECT * FROM shop_marketplace_config_amazon WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'
			and environment = '" . $environment . "'";
		
			
		$result = @mysqli_query($GLOBALS['mysql_con'], $query);
		$row = @mysqli_fetch_array($result);
		
		$this->set_aws_access_key_id($row['aws_access_key_id']);
		$this->set_aws_secret_access_key($row['aws_secret_access_key']);
		$this->set_merchant_id($row['merchant_id']);
		
		$this->set_marketplace_id($row['marketplace_id']);	
		$this->set_marketplace_fr($row['marketplace_fr']);	
		$this->set_marketplace_it($row['marketplace_it']);	
		$this->set_marketplace_es($row['marketplace_es']);	
		$this->set_marketplace_uk($row['marketplace_uk']);
		$this->set_marketplace_us($row['marketplace_us']);

		$this->set_application_name("MysydeShopMarketplace");
		$this->set_application_version("1.0");
		
		$this->set_mws_auth_token($row['mws_auth_token']);
		
		$this->setService();
	}
	
	protected function setService() {}
	
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
	
	protected function set_aws_access_key_id($aws_access_key_id) {
		$this->aws_access_key_id = $aws_access_key_id;
	}
	
	public function get_aws_access_key_id() {
		return $this->aws_access_key_id;
	}
	
	protected function set_aws_secret_access_key($aws_secret_access_key) {
		$this->aws_secret_access_key = $aws_secret_access_key;
	}
	
	public function get_aws_secret_access_key() {
		return $this->aws_secret_access_key;
	}
	
	protected function set_merchant_id($merchant_id) {
		$this->merchant_id = $merchant_id;
	}
	
	public function get_merchant_id() {
		return $this->merchant_id;
	}
	
	protected function set_marketplace_id($marketplace_id) {
		$this->marketplace_id = $marketplace_id;
	}
	
	protected function set_marketplace_fr($marketplace_id) {
		$this->marketplace_fr = $marketplace_id;
	}
	
	protected function set_marketplace_it($marketplace_id) {
		$this->marketplace_it = $marketplace_id;
	}
	
	protected function set_marketplace_es($marketplace_id) {
		$this->marketplace_es = $marketplace_id;
	}
	
	protected function set_marketplace_uk($marketplace_id) {
		$this->marketplace_uk = $marketplace_id;
	}

	protected function set_marketplace_us($marketplace_id) {
		$this->marketplace_us = $marketplace_id;
	}
	
	public function get_marketplace_id() {
		return $this->marketplace_id;
	}
	
	public function get_marketplace_fr() {
		return $this->marketplace_fr;
	}
	
	public function get_marketplace_it() {
		return $this->marketplace_it;
	}
	
	public function get_marketplace_es() {
		return $this->marketplace_es;
	}
	
	public function get_marketplace_uk() {
		return $this->marketplace_uk;
	}

	public function get_marketplace_us() {
		return $this->marketplace_us;
	}
	
	protected function set_application_name($application_name) {
		$this->application_name = $application_name;
	}
	
	public function get_application_name() {
		return $this->application_name;
	}
	
	protected function set_application_version($application_version) {
		$this->application_version = $application_version;
	}
	
	public function get_application_version() {
		return $this->application_version;
	}
	
	protected function set_mws_auth_token($mws_auth_token) {
		$this->mws_auth_token = $mws_auth_token;
	}
	
	public function get_mws_auth_token() {
		return $this->mws_auth_token;
	}
}