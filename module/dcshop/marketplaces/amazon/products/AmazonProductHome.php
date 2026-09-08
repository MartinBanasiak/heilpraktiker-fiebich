<?php

class AmazonProductHome {
	
	protected $allowed_attributes = array("Size", "Color", "Material");
	
	protected $allowed_productTypes = array("Home", "Kitchen");
	
	protected $addedAttributes = array();
	
	protected $productTypeVariationElement = null;
	
	public function insert_parentage_variantion_tag($bulkXml, $item, $item_has_category, $productDataHeader, $productTypeElement, $product_type, $is_parent = false, $xsdRow, $allOptions, $attributeresult = null) {
		if($is_parent === true) {
			$parentageValue = "parent";
		} else {
			$parentageValue = "child";
		}
		
		$variationTheme = $this->get_amazon_variation_theme($item_has_category, $allOptions, "Home");
		
		if($variationTheme === false) {
			return false;
		}
		
		$parentage = $bulkXml->createElement("Parentage", $parentageValue);
		$variationData = $bulkXml->createElement("VariationData");
		$this->productTypeVariationElement = $variationData;
		
		$productDataHeader->appendChild($parentage);
		$productDataHeader->appendChild($variationData);
		
		$variationTheme = $bulkXml->createElement("VariationTheme", $variationTheme);
		
		// beim parent keine angaben zu den varianten
		$variationData->appendChild($variationTheme);
		if($is_parent === true) {
			return true;
		}
		
		$itemAttributes = array();
		while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
			//$itemAttributes[$attributeRow['marketplace_attribute_code']] = get_item_attribute_value($item, $attributeRow);
			$itemAttributes[$attributeRow['headline']] = get_item_attribute_value($item, $attributeRow);
		}

		foreach($itemAttributes as $attribute_code => $value) {
			if(in_array($attribute_code, $this->addedAttributes)) {
				continue;
			}
			
			switch($attribute_code) {
				case "Size":
				case "Color":
					$this->productTypeVariationElement->appendChild(
						$bulkXml->createElement($attribute_code, $value)
					);
					break;
					
				case "Material":
					$productDataHeader->appendChild(
						$bulkXml->createElement($attribute_code, $value)
					);
					break;
			}
			
			$this->addedAttributes[] = $attribute_code;
		}
		
		return true;
	}
	
	public function add_attributes($bulkXml, $item, $item_has_category, $productDataHeader, $productTypeElement, $product_type, $is_parent = false, $xsdRow, $allOptions, $attributeresult = null) {
		$itemAttributes = array();
		while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
			//$itemAttributes[$attributeRow['marketplace_attribute_code']] = get_item_attribute_value($item, $attributeRow);
			$itemAttributes[$attributeRow['headline']] = get_item_attribute_value($item, $attributeRow);
		}
		
		if($this->productTypeVariationElement === null) {
			$this->productTypeVariationElement = $bulkXml->createElement("VariationData");
			$productTypeElement->appendChild($this->productTypeVariationElement);
		}
		
		foreach($itemAttributes as $attribute_code => $value) {
			if(in_array($attribute_code, $this->addedAttributes)) {
				continue;
			}
			
			switch($attribute_code) {
				case "Size":
				case "Color":
					$this->productTypeVariationElement->appendChild(
						$bulkXml->createElement($attribute_code, $value)
					);
					break;
					
				case "Material":
					$productDataHeader->appendChild(
						$bulkXml->createElement($attribute_code, $value)
					);
					break;
			}
			
			$this->addedAttributes[] = $attribute_code;
		}
	}
	
	protected function get_amazon_variation_theme($item_has_category, $allOptions, $product_type) {
		
		$query = "SELECT description from shop_marketplace_variant_themes where marketplace_type = 1 and category_code = '" . $item_has_category['root_category_code'] . "'";
		$query .= " and productType = '" . $product_type . "' and description like '%" . join("%' and description like '%", array_keys($allOptions)) . "%'";
		
		$result = @mysqli_query($GLOBALS['mysql_con'], $query);
		
		if(mysqli_num_rows($result) == 0) {
			return false;
		}
		
		while ($row = mysqli_fetch_assoc($result)) {
			$description = $row['description'];
			
			foreach($allOptions as $optionKey => $values) {
				$description = str_replace($optionKey, "", $description);
			}
			
			$description = str_replace("-", "", $description);
			
			if($description == "") {
				return $row['description'];
			}
		}
		
		return false;
	}
}