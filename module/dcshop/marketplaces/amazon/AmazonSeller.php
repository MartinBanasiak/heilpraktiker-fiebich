<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonCore.php';

class AmazonSeller extends AmazonCore {

    /**
     * URLS fuer APIs
     * @access protected
     * @var array
     */
    protected $api_urls = array(
        'production'=>'https://mws-eu.amazonservices.com/Sellers/2011-07-01',
        'production_na' => 'https://mws.amazonservices.com/Sellers/2011-07-01' // North America
    );

    public function setService() {
        $apiUrl = $this->api_urls['production'];

        $this->config = array (
            'ServiceURL' => $apiUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        );

        $this->service = new MarketplaceWebServiceSellers_Client(
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
}