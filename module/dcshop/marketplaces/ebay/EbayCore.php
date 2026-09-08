<?php
abstract class EbayCore {
	
	public function __construct() {
	
		$query = "SELECT * FROM shop_marketplace_config_ebay WHERE 
			company = '" . $GLOBALS['shop']['company'] . "'
			and shop_code='" . $GLOBALS['shop']['code'] . "'
			and language_code='" . $GLOBALS['shop_language']['code'] . "'";
			
		
			
		$result = @mysqli_query($GLOBALS['mysql_con'], $query);
		$row = @mysqli_fetch_array($result);
		
		$this->set_auth_token($row['auth_token']);
		$this->set_dev_name($row['dev_name']);
		$this->set_app_name($row['app_name']);
		$this->set_cert_name($row['cert_name']);
		
		$this->set_config_row($row);
		
		if($row['environment'] == 0) {
			$this->sandbox = true;
		}
	}
	
	/**
	 * Sanbbox modus true/false
	 * 
	 * @access protected
	 * @var bool
	 */    
    protected $sandbox = false;
	
	/**
	 * Setzt den Sandbox modus
	 * 
	 * @access public
	 * 
	 */
	public function setSandbox($sandbox = false) {
		$this->sandbox = $sandbox;
	}
	
	protected function set_auth_token($auth_token) {
		$this->auth_token = $auth_token;
	}
	
	public function get_auth_token() {
		return $this->auth_token;
	}
	
	protected function set_dev_name($dev_name) {
		$this->dev_name = $dev_name;
	}
	
	public function get_dev_name() {
		return $this->dev_name;
	}
	
	protected function set_app_name($app_name) {
		$this->app_name = $app_name;
	}
	
	public function get_app_name() {
		return $this->app_name;
	}
	
	protected function set_cert_name($cert_name) {
		$this->cert_name = $cert_name;
	}
	
	public function get_cert_name() {
		return $this->cert_name;
	}
	
	public function set_config_row($config_row) {
		$this->config_row = $config_row;
	}
	
	public function get_config_row() {
		return $this->config_row;
	}
	
	public function isSanbox() {
		return $this->sandbox;
	}
}