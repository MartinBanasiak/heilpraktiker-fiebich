<?php

class AmazonProductComputers {

    protected $addedAttributes = array();

    protected $addedVariationTagAttributes = false;

    protected $variationAttributeOrder = array("Color", "Size");

    public function insert_parentage_variantion_tag($bulkXml, $item, $item_has_category, $VariationDataTag, $is_parent = false, $allOptions, $attributeresult = null) {
        if($is_parent === true) {
            $parentageValue = "parent";
        } else {
            $parentageValue = "child";
        }

        $variationTheme = $this->get_amazon_variation_theme($item_has_category, $allOptions, "Clothing");

        if($variationTheme === false) {
            return false;
        }

        $this->addedVariationTagAttributes = true;

        $parentage = $bulkXml->createElement("Parentage", $parentageValue);
        $VariationDataTag->appendChild($parentage);

        if($is_parent === false) {
            $itemAttributes = array();
            while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
                //$itemAttributes[$attributeRow['marketplace_attribute_code']] = get_item_attribute_value($item, $attributeRow);
                $itemAttributes[$attributeRow['headline']] = get_item_attribute_value($item, $attributeRow);
            }

            $itemAttributes = $this->sortAttributes($itemAttributes);

            foreach ($itemAttributes as $attribute_code => $value) {
                if (in_array($attribute_code, $this->addedAttributes)) {
                    continue;
                }

                switch ($attribute_code) {
                    case "Size":
                    case "Color":
                        $VariationDataTag->appendChild(
                            $bulkXml->createElement($attribute_code, $value)
                        );
                        break;
                }

                $this->addedAttributes[] = $attribute_code;
            }
        }

        $variationTheme = $bulkXml->createElement("VariationTheme", $variationTheme);
        $VariationDataTag->appendChild($variationTheme);

        return true;
    }

    public function add_attributes($bulkXml, $item, $productDataHeader, $ProductTypeHeaderTag, $attributeresult) {
        $itemAttributes = array();
        while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
            //$itemAttributes[$attributeRow['marketplace_attribute_code']] = get_item_attribute_value($item, $attributeRow);
            $itemAttributes[$attributeRow['headline']] = get_item_attribute_value($item, $attributeRow);
        }

        $itemAttributes = $this->sortAttributes($itemAttributes);

        foreach($itemAttributes as $attribute_code => $value) {
            if(in_array($attribute_code, $this->addedAttributes)) {
                continue;
            }

            switch($attribute_code) {
                case "Color":
                case "Size":
                    $productDataHeader->appendChild(
                        $bulkXml->createElement($attribute_code, $value)
                    );
                    break;
                default:
                    $ProductTypeHeaderTag->appendChild(
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

    public function create($bulkXml, $item, $item_has_category, $parent, $xsdRow, $variations, $product, $allVariationAttributes) {

        $product_type = get_attribute_value($item, "PRODUCT_TYPE");

        $productData = $product->appendChild(
            $bulkXml->createElement("ProductData")
        );

        // Haupt Element. Jede Product-XSD ist ein Hauptelement z.B. Home
        // bei Argo faengt das ganze mit Computers an
        $productDataHeader = $productData->appendChild(
            $bulkXml->createElement("Computers")
        );

        $ProductTypeTag = $productDataHeader->appendChild(
            $bulkXml->createElement("ProductType")
        );

            // <ProductData><Computers><ProductType><Monitor>
            // Monitor ist in dem Fall der Producttype, was ein eigenstaendiger xml Tag ist und mit </Monitor> beendet wird
            $ProductTypeHeaderTag = $ProductTypeTag->appendChild(
                $bulkXml->createElement($product_type)
            );

        // Varianten
        if($variations === true) {
            // pruefen ob variationTheme ueberhaupt moeglich ist

            // Varianten erstmal noch nicht unterstuetzt, in der XSD gibt es keine variationThemes, muss getestet werden ob das ueberhaupt geht
            $variations = false;
        }

        // wenn varianten immer noch moeglich sind
        if($variations === true) {
            if($GLOBALS['shop']['variant_typ'] == 1 && $parent === true) {
                $oldSkuNode = $product->getElementsByTagName('SKU')->Item(0);
                $parentSku = $item['item_no'] . AmazonMarketplace::PARENTITEM_SUFFIX;
                $oldSkuNode->parentNode->replaceChild($bulkXml->createElement('SKU', $parentSku), $oldSkuNode);
            }
        }

        // alle merkmale, egal ob varianten oder nicht.
        if($variations === false) {
            do { // einmal durchlaufen
                $all_marketplace_attributes = get_category_attributes($item_has_category['root_category_code'], AmazonMarketplace::MARKETPLACE_TYPE, array(
                    $product_type, "Computers"
                ));
                if(count($all_marketplace_attributes) == 0) {
                    break;
                }

                $all_shop_attributes = get_variant_item_attributes($all_marketplace_attributes, AmazonMarketplace::ATTRIBUTE_TYPE);
                if(count($all_shop_attributes) == 0) {
                    break;
                }

                // richtige Attributzuordnung vorhanden, in die xml hinhzufuegen
                $attributeresult = get_item_attributes($item, $all_shop_attributes, AmazonMarketplace::ATTRIBUTE_TYPE);
                $this->add_attributes($bulkXml, $item, $productDataHeader, $ProductTypeHeaderTag, $attributeresult);
            } while(false);
        }
    }

    protected function get_amazon_xsd($item_has_category) {
        $query = "SELECT * from shop_marketplace_amazon_xsd where category_code = '" . $item_has_category['root_category_code'] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $row = @mysqli_fetch_array($result);

        return $row;
    }

    protected function sortAttributes($attributes) {
        $order = $this->variationAttributeOrder;
        uksort($attributes, function($key1, $key2) use ($order) {
            return ((array_search($key1, $order) > array_search($key2, $order)) ? 1 : -1);
        });

        return $attributes;
    }
}