<?php

class AmazonFeedProduct {
	
	protected $productTitle;
	
	protected $productDescription;
	
	protected $sku;
	
	protected $document;
	
	public function __construct() {
		
	}
	
	public function setSku($sku) {
		$this->sku = $sku;
	}
	
	public function setProductTitle($productTitle) {
		$this->productTitle = $productTitle;
	}
	
	public function setProductDescription($productDescription) {
		$this->productDescription = $productDescription;
	}
	
	public function getSku() {
		return $this->sku;
	}
	
	public function getProductTitle() {
		return $this->productTitle;
	}
	
	public function getProductDescription() {
		return $this->productDescription;
	}
	
	public function add(DOMDocument $document) {
		
	}
} 