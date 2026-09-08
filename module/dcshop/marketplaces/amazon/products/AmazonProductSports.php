<?php

class AmazonProductSports {

    protected $addedAttributes = array();

    protected $variationAttributeOrder = array("Color", "GolfFlex", "GripMaterialType", "Hand", "HeadSize", "Irons", "Material", "Size");

    public function insert_parentage_variantion_tag($bulkXml, $item, $item_has_category, $VariationDataTag, $is_parent = false, $attributeresult = null, $allVariationAttributes) {
        if($is_parent === true) {
            $parentageValue = "parent";
        } else {
            $parentageValue = "child";
        }
        $variationTheme = $this->get_amazon_variation_theme($item_has_category, $allVariationAttributes, "Sports");

        if($variationTheme === false) {
            return false;
        }

        $parentage = $bulkXml->createElement("Parentage", $parentageValue);
        $VariationDataTag->appendChild($parentage);

        $variationTheme = $bulkXml->createElement("VariationTheme", $variationTheme);
        $VariationDataTag->appendChild($variationTheme);

        $itemAttributes = array();
        while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
            //$itemAttributes[$attributeRow['marketplace_attribute_code']] = get_item_attribute_value($item, $attributeRow);
            $itemAttributes[$attributeRow['headline']] = get_item_attribute_value($item, $attributeRow);
        }

        if($is_parent === false) {
            $itemAttributes = $this->sortAttributes($itemAttributes);

            foreach ($itemAttributes as $attribute_code => $value) {
                if (in_array($attribute_code, $this->addedAttributes)) {
                    continue;
                }

                switch ($attribute_code) {
                    case "Size":
                    case "Color":
                    case "Material":
                    case "GolfFlex":
                    case "Hand":
                    case "Irons":
                    case "HeadSize":
                        $VariationDataTag->appendChild(
                            $bulkXml->createElement($attribute_code, $value)
                        );
                        break;
                }

                $this->addedAttributes[] = $attribute_code;
            }
        }

        return true;
    }

    public function add_attributes($bulkXml, $item, $productDataHeader, $VariationDataTag, $attributeresult) {
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
                case "Material":
                case "GolfFlex":
                case "Hand":
                case "Irons":
                case "HeadSize":
                    $VariationDataTag->appendChild(
                        $bulkXml->createElement($attribute_code, $value)
                    );
                    break;
                default:
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
        $query .= " and productType = '" . $product_type . "' and description like '%" . join("%' and description like '%", $allOptions) . "%'";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if(mysqli_num_rows($result) == 0) {
            return false;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $description = $row['description'];

            foreach($allOptions as $optionKey) {
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
        
        if($parent === false && $variations === true) {
            // product type vom parent uebernehmen
            $parent_item = get_item_variant_parent($item);
            $product_type = get_attribute_value($parent_item, "PRODUCT_TYPE");
        } else {
            $product_type = get_attribute_value($item, "PRODUCT_TYPE");
        }

        $productData = $product->appendChild(
            $bulkXml->createElement("ProductData")
        );

        // Haupt Element. Jede Product-XSD ist ein Hauptelement z.B. Home
        $productDataHeader = $productData->appendChild(
            $bulkXml->createElement("Sports")
        );

        $productDataHeader->appendChild(
            $bulkXml->createElement("ProductType", $product_type)
        );

        $VariationDataTag = $productDataHeader->appendChild(
            $bulkXml->createElement("VariationData")
        );

        // Varianten
        if($variations === true) {
            // pruefen ob variationTheme ueberhaupt moeglich ist

            $all_marketplace_variant_attributes = get_category_variant_attributes($item_has_category['root_category_code'], AmazonMarketplace::MARKETPLACE_TYPE, array(
                "Sports"
            ));
            $all_shop_attributes = get_variant_item_attributes($all_marketplace_variant_attributes, AmazonMarketplace::ATTRIBUTE_TYPE);
            $attributeresult = get_item_attributes($item, $all_shop_attributes, AmazonMarketplace::ATTRIBUTE_TYPE);

            $variationTag = $this->insert_parentage_variantion_tag($bulkXml, $item, $item_has_category, $VariationDataTag, $parent, $attributeresult, $allVariationAttributes);

            if($variationTag === false) {
                return false;
            } else {
                // alle anderen, nicht variantenbildenden merkmale, hinzufuegen

                do { // einmal durchlaufen
                    $all_marketplace_attributes = get_category_normal_attributes($item_has_category['root_category_code'], AmazonMarketplace::MARKETPLACE_TYPE, array(
                        $product_type
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
                    $this->add_attributes($bulkXml, $item, $productDataHeader, $VariationDataTag, $attributeresult);
                } while(false);
            }
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
                    "Sports"
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
                $this->add_attributes($bulkXml, $item, $productDataHeader, $VariationDataTag, $attributeresult);
            } while(false);
        }

        return true;
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