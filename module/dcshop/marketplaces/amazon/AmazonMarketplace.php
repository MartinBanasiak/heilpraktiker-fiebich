<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'sdk/MarketplaceWebService/Samples/.config.inc.php';
set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sdk/.');
/*function __autoload($className){
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "sdk" . DIRECTORY_SEPARATOR . $filePath;
    return;
}*/

function amazon_autoloader($className) {
    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "sdk" . DIRECTORY_SEPARATOR . $filePath;
}
spl_autoload_register("amazon_autoloader");

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "MarketplaceAbstract.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "MarketplaceInterface.php");

require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonOrder.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonFeed.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonProduct.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonReport.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AmazonSeller.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'products/AmazonProductHome.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'products/AmazonProductClothing.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'products/AmazonProductComputers.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'products/AmazonProductSports.php';

class AmazonMarketplace extends MarketplaceAbstract implements MarketplaceInterface {
    const AMAZON_ERROR_CODE_PRODUCT_EXISTS = '8541';
    const AMAZON_ERROR_CODE_PRODUCT_EXISTS_2 = '8542';
    const AMAZON_ERROR_CODE_PRODUCT_NO_LONGER_VALID = '8566';

    /**
     * definiert intern die ID vom marketplace
     *
     * @var int
     * @static
     */
    const MARKETPLACE_TYPE = 1;

    const ATTRIBUTE_TYPE = 1;

    /**
     * definiert einen suffix fuer mutterartikel bei shop_shop variant_typ = 1
     * Wenn die erste Variante gleichzeitig ein Parent ist
     *
     * @var string
     */
    const PARENTITEM_SUFFIX = 'm';

    // JS 13.12.2016
    const ITEM_DATA_MANAGEMENT_SOURCE_TABLE_DESCRIPTION = 'Webshop Item Description';
    const ITEM_DATA_MANAGEMENT_SOURCE_TABLE_FILE = 'Webshop Item File';
    const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_LONG = 0;
    const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_SEARCH = 2;
    const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_DESCRIPTION_SHORT = 3;
    const ITEM_DATA_MANAGEMENT_SOURCE_TYPE_FILE_PICTURE = 0;

    protected $currentFunction;

    protected $messageId = 1;

    protected $currentHandledOrders = null;

    protected $bulkXml = null;

    protected $insertedToBulk = false;

    protected function setCurrentFunction($currentFunction) {
        $this->currentFunction = $currentFunction;
    }

    protected function getCurrentFunction() {
        return $this->currentFunction;
    }

    protected function incrementMessageId() {
        $this->messageId++;
    }

    protected function getMessageId() {
        return $this->messageId;
    }

    protected function setBulkXmlFilename($bulkXmlFile) {
        $this->bulkXmlFilename = $bulkXmlFile;
    }

    protected function insertMessageToBulk() {
        $apiNode = $this->bulkXml->getElementsByTagName("Message");
        file_put_contents($this->bulkXmlFilename, $this->bulkXml->saveXML($apiNode->item(0)), FILE_APPEND);
    }

    /**
     * Artikel verarbeiten und noetige XML erstellen
     *
     * @param AmazonFeed $api
     * @access public
     */
    protected function handle_products($api, $parameter) {
        // Basis XML erstellen

        $bulkXmlFile = $api->createBaseDocument();
        $this->setBulkXmlFilename($bulkXmlFile);

        switch ($this->getCurrentFunction()) {
            case "update_inventory":
                $field = 'marketplace_update_inventory';
                break;
            case "update_price":
                $field = 'marketplace_update_price';
                break;
            case "update_images":
                $field = 'marketplace_update_images';
                break;
            default:
                $field = 'marketplace_update';
                break;
        }

        if(isset($parameter['item_no'])) {
            $items_result = get_item_result($parameter['item_no']);
        } else {
            $items_result = get_all_items_to_update($field);
        }



        $no_process_items = array();

        $this->bulkXml = null;

        while ($item = mysqli_fetch_assoc($items_result)) {

            if(in_array($item['item_no'], $no_process_items)) {
                //$this->set_item_submission_success($item,$field);
                continue;
            }

            // Preis, Bestand und Bilder brauchen keine weiteren Pruefungen
            if($this->getCurrentFunction() != 'update_products' && $this->getCurrentFunction() != 'update_relationship') {
                $this->create_pricing_feed($item);
                $this->create_inventory_feed($item);
                $this->create_product_images_feed($item);
                continue;
            }

            // nur anfuegen, kategorie pruefungen oder varianten pruefeungen sind nicht noetig
            if($item['marketplace_existing_item_per_shop'] == 1) {

                $this->create_product_feed($item, false, false, false);
                continue;
            }

            $item_is_parent = get_item_first_variant($item);
            $item_is_variant = get_item_variant_parent($item); // item ist variante wenn es einen parent gibt

            if($GLOBALS['shop']['variant_typ'] == 1) {
                if($item_is_parent !== false) {
                    $item_is_variant = true; // parent ist gleichzeitig auch eine variante
                }
            }

            // erstmal kein eigenstaendiger artikel
            $standalone = false;

            // item ist weder eine variante noch ein parent, dann ein eigenstaendiger artikel
            if($item_is_variant === false && $item_is_parent === false) {
                $standalone = true;
            }

            // wenn kennzeichen gesetzt, dann sowieso als eigensaetndigen artikel hochladen
            if($item['marketplace_standalone_product_per_shop'] == 1) {
                $standalone = true;
            }

            if($this->getCurrentFunction() == 'update_relationship' || isset($parameter['start_relationship'])) {
                if($item_is_parent === false) {
                    continue;
                }
            }



            // kategorie herausfinden
            $item_has_category = get_item_has_category($item);

            // item zu keiner kategorie zugeordnet, dann versuchen die parent kategorie auszulesen
            if($item_has_category === false && $item_is_variant !== false) {
                $parent_item = get_item_variant_parent($item);
                $item_has_category = get_item_has_category($parent_item);
            }

            if($item_has_category === false) {
                $this->set_item_submission_error($item['item_no'], "Item hat keine Kategorie zugeordnet");
                continue;
            }

            $xsd = $this->get_amazon_xsd($item_has_category);

            if($standalone === false) {

                // Hier werden varianten und parents betrachtet

                if($item_is_parent === false) {
                    $parent_item = get_item_variant_parent($item);
                    $variants_result = get_item_variants($parent_item,1);
                } else {
                    $variants_result = get_item_variants($item,1);
                    $parent_item = get_item_result($item['item_no'], true);
                }

                // alle moeglichen Varianten auslesen, in denen es das Produkt gibt.
                $all_variant_items = array();

                // Parentartikel ist gleichzeitig auch eine Variante
                if($GLOBALS['shop']['variant_typ'] == 1 && $parent_item['marketplace_standalone_product_per_shop'] == 0) {
                    $no_process_items[] = $parent_item['item_no'];
                    $all_variant_items[$parent_item['item_no']] = $parent_item;
                }

                while ($variant_item = mysqli_fetch_assoc($variants_result)) {
                    if($variant_item['marketplace_standalone_product_per_shop'] == 1) {
                        continue;
                    }
                    $all_variant_items[$variant_item['item_no']] = $variant_item;
                }

                if(count($all_variant_items) == 0) {
                    $this->set_item_submission_error($parent_item['item_no'], sprintf("Vom Parent %s gibt es keine Varianten oder alle Varianten sind als eigenstaendige Artikel gepflegt.", $parent_item['item_no']));
                    continue;
                }

                // pruefen ob die kategorie varianten erlaubt
                if(!category_variation_allowed($item_has_category, self::MARKETPLACE_TYPE)) {
                    $this->set_item_submission_error($item['item_no'], "Die Amazon-Kategorie (".$item_has_category['marketplace_category_code'].") des items laesst keine Variantenbildung zu.");
                    continue;
                }

                // pruefen ob die kategorie im marketplace variantenbildende merkmale enthaelt
                $product_type = get_attribute_value($item, "PRODUCT_TYPE");
                $attribute_codes = get_category_variant_attributes($item_has_category['root_category_code'], self::MARKETPLACE_TYPE, array(
                    $product_type, $xsd['element']
                ));

                if(count($attribute_codes) == 0) {
                    $this->set_item_submission_error($item['item_no'], sprintf("Attribut code für die Kategorie %s in der shop_marketplace_variant_attributes nicht vorhanden.", $item_has_category['root_category_code']));
                    continue;
                }

                // pruefen ob die marketplace merkmale auch im mysyde shop angelegt sind
                $shop_attributes = get_variant_item_attributes($attribute_codes, self::ATTRIBUTE_TYPE);
                if(count($shop_attributes) == 0) {
                    $this->set_item_submission_error($item['item_no'], sprintf("Marketplace Attribute: %s müssen auch im Webshop angelegt sein", join(',', $attribute_codes)));
                    continue;
                }

                $allVariationAttributes = $this->get_all_variation_attributes($all_variant_items, $shop_attributes);
                if(count($allVariationAttributes) == 0) {
                    $this->set_item_submission_error($parent_item['item_no'], "Nicht alle Varianten haben die selben Variantenbildenen Merkmale zugeordnet.");
                    continue;
                }

                // AB HIER: varianten theoretisch moeglich weil marketplace und mysyde shop es zulassen wuerden

                // varianten hochladen und kennzeichnen als child
                if($this->getCurrentFunction() != 'update_relationship') {
                    // nur eine einzelne Variante hochladen
                    if($item_is_variant !== false) {

                        if(in_array($item['item_no'], $no_process_items)) continue;

                        $no_process_items[] = $item['item_no'];

                        // variante auf attribute pruefen
                        $item_has_variant_attributes = item_has_attributes($item, $shop_attributes, self::ATTRIBUTE_TYPE);
                        if($item_has_variant_attributes === false) {
                            $this->set_item_submission_error($item['item_no'], "Item soll als Variante bei amazon erscheinen, hat aber keine amazon attribute zugeordnet.");
                            continue;
                        }

                        $this->create_product_feed($item, $item_has_category, true, false, $allVariationAttributes);

                        continue;
                    }
                }

                // AB HIER: relationship parent child Verknuepfung
                if(isset($parameter['start_relationship'])) {
                    $this->set_item_start_relationship($parent_item['item_no'], 0);
                    $this->set_item_to_update($parent_item['item_no']);
                    $this->create_product_feed($parent_item, $item_has_category, true, true, $allVariationAttributes);
                    continue; // naechster parent artikel
                }

                // alle Varianten entweder erst mal hochladen oder relationship erstellen
                $items_for_relationship = array();
                foreach($all_variant_items as $variant_item_no => $variant_item) {
                    // nur varianten bilden wenn mindestens ein Artikel werte zu den attributen hat

                    $item_has_variant_attributes = item_has_attributes($variant_item, $shop_attributes, self::ATTRIBUTE_TYPE);

                    if($item_has_variant_attributes === false) {
                        $this->set_item_submission_error($variant_item['item_no'], "Item soll als Variante bei amazon erscheinen, hat aber keine amazon attribute zugeordnet.");
                        continue; // naechste variante
                    }

                    if($this->getCurrentFunction() != 'update_relationship' && !in_array($variant_item['item_no'], $no_process_items)) {
                        $this->create_product_feed($variant_item, $item_has_category, true, false, $allVariationAttributes);
                        $no_process_items[] = $variant_item['item_no'];
                    }

                    // varianten artikel sammeln um relationship xml zu erstellen (parent zu kind)
                    $items_for_relationship[] = $variant_item;
                }

                if($this->getCurrentFunction() != 'update_relationship' && count($items_for_relationship)) {
                    $this->set_item_start_relationship($parent_item['item_no'], 1);
                    $this->extend_parameter_queue(array('check_relationship' => 1));
                    continue;
                }

                if($this->getCurrentFunction() == 'update_relationship' && count($items_for_relationship)) {
                    $this->create_relationship_feed($parent_item, $items_for_relationship);
                    continue; // naechste parent-child verknuepfung
                }

            } else {
                $this->create_product_feed($item, $item_has_category, false, false);
            }
        }

        $api->endBaseDocument();
    }

    protected function create_product_feed($item, $item_has_category, $variations = false, $parent = false, $allVariationAttributes = array()) {

        if($this->getCurrentFunction() != "update_products" && $this->getCurrentFunction() != "update_relationship") {
            return;
        }

        $xsdRow = $this->get_amazon_xsd($item_has_category);
        $productClass = "AmazonProduct" . $xsdRow['element'];



        if ((!class_exists($productClass) || empty($xsdRow['element'])) && $item_has_category != FALSE) {
            $this->set_item_submission_error($item['item_no'], "Item Product Type falsch.");
            return;
        }

        $parent_item = $item;
        if($variations === true && $parent === false) {
            $parent_item = get_item_variant_parent($item);
        }

        // verknuepfung mit z.b. EAN. ISBN etc. Parent braucht keine eigene EAN
        // steht nicht in der doku aber siehe: https://sellercentral.amazon.ca/forums/thread.jspa?threadID=12586
        if($parent === false) {

            $standard_product_id = get_standard_product_id($item);
            $productIdDescription = $standard_product_id['description'];
            if ($standard_product_id['reference_type'] == 3) {
                $productIdDescription = 'EAN';
            }

            if (empty($standard_product_id['item_reference_no']) || (mb_strlen($standard_product_id['item_reference_no']) < 13)) {
                $this->set_item_submission_error($item['item_no'], "EAN Fehlerhaft");
                return;
            }

        }

        $this->bulkXml = new DOMDocument('1.0', "UTF-8");

        $message = $this->bulkXml->createElement('Message');
        $this->bulkXml->appendChild($message);

        $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
        $operationtype = $this->bulkXml->createElement('OperationType', "Update");
        $message->appendChild($messageIdElement);
        $message->appendChild($operationtype);

        // product
        $product = $this->bulkXml->createElement('Product');
        $product->appendChild($this->bulkXml->createElement('SKU', $item['item_no']));

        //ean
        if($parent === false) {
            $stdproductid = $this->bulkXml->createElement('StandardProductID');
            $stdproductid->appendChild($this->bulkXml->createElement('Type', $productIdDescription));
            $stdproductid->appendChild($this->bulkXml->createElement('Value', $standard_product_id['item_reference_no']));
            $product->appendChild($stdproductid);
        }

        // launchdate
        $launchDate = getItemValidFrom($item);
        $product->appendChild($this->bulkXml->createElement('LaunchDate', $launchDate));

        // condition
        $condition = $this->bulkXml->createElement('Condition');
        $condition->appendChild($this->bulkXml->createElement('ConditionType', "New"));

        //condition note
        //$condition->appendChild($this->bulkXml->createElement('ConditionNote', ""));

        $product->appendChild($condition);

        // wenn das items bereits im marketplace existiert, dann braucht es keine weitere angaben mehr.
        if($item['marketplace_existing_item_per_shop'] == 1) {

            $message->appendChild($product);
            $this->incrementMessageId();
            $this->insertMessageToBulk();
            $this->insertedToBulk = true;
            $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));
            return;
        }

        // product description
        $productdescription = $this->bulkXml->createElement('DescriptionData');
        if (!empty($item["marketplace_title"])) {
            $titleEl = $this->bulkXml->createElement('Title');
            $productdescription->appendChild($titleEl);
            $titleEl->appendChild(($this->bulkXml->createTextNode(preg_replace('/\s\s+/', '', substr(html_entity_decode($item['marketplace_title']), 0, 500)))));
            //$productdescription->appendChild($this->bulkXml->createElement('Title', preg_replace('/\s\s+/', '', substr(html_entity_decode($item['marketplace_title']), 0, 500))));
        } else {
            $titleEl = $this->bulkXml->createElement('Title');
            $productdescription->appendChild($titleEl);
            $titleEl->appendChild(($this->bulkXml->createTextNode(preg_replace('/\s\s+/', '', substr(html_entity_decode($item['description']), 0, 500)))));
            //$productdescription->appendChild($this->bulkXml->createElement('Title', preg_replace('/\s\s+/', '', substr($item['description'], 0, 500))));
        }

        // brand
        $brand = get_brand_name($item,true);
        if(strlen($brand) == 0) {
            $brand = $GLOBALS["shop"]["company"];
        }
        $brandEl = $this->bulkXml->createElement('Brand');
        $productdescription->appendChild($brandEl);
        $brandEl->appendChild($this->bulkXml->createTextNode($brand));
        //$productdescription->appendChild($this->bulkXml->createElement('Brand', $brand));



        // beschreibung
        $description = get_item_description($parent_item, true);
        $descriptionText = array();
        $bulletPoints = array();
        while ($descriptionrow = mysqli_fetch_assoc($description)) {


            $allowed = array("<b>","</b>",
                "<br>","<br />","<br/>",
                "<ul>","</ul>",
                "<ol>","</ol>",
                "<li>","</li>",
                "<em>","</em>",
                "<h2>","</h2>",
                "<h3>","</h3>",
                "<h4>","</h4>",
                "<h5>","</h5>",
                "<hr>","</hr>",
                "<pre>","</pre>",
                "<strong>","</strong>",
                "<sub>","</sub>",
                "<sup>","</sup>");

            $allowedReplace = array("|b|","|/b|",
                "|br|","|br /|","|br/|",
                "|ul|","|/ul|",
                "|ol|","|/ol|",
                "|li|","|/li|",
                "|em|","|/em|",
                "|h2|","|/h2|",
                "|h3|","|/h3|",
                "|h4|","|/h4|",
                "|h5|","|/h5|",
                "|hr|","|/hr|",
                "|pre|","|/pre|",
                "|strong|","|/strong|",
                "|sub|","|/sub|",
                "|sup|","|/sup|");

            $allowed = array("<br>","<br />","<br/>","<li>");
            $allowedReplace = array("|br|","|br /|","|br/|","|li|");

            $convert = array("<li>");
            $convertReplace = array("- ");

            $output = str_replace(array("\r\n", "\r"), "<br>", html_entity_decode(str_ireplace($allowed,$allowedReplace,$descriptionrow['content'])));

            /*$lines = explode("\n", $output);
            $new_lines = array();

            foreach ($lines as $i => $line) {
                if(!empty($line))
                    $new_lines[] = trim($line);
            }

            if($descriptionrow['show_in_header'] == 1) {
                $descriptionText[] = implode($new_lines);
            } elseif ($descriptionrow['marketplace_title'] != 1) {
                $bulletPoints[] = implode($new_lines);
            }
            */
            if($descriptionrow['show_in_header'] == 1) {
                $allowedOutput = str_ireplace($allowedReplace,$allowed,$output);
                $allowedConvert = str_ireplace($convert,$convertReplace,$allowedOutput);
                $descriptionText[] = $allowedConvert."\r\n";
            } elseif ($descriptionrow['marketplace_title'] != 1) {
                $bulletPointFromUl = $this->ul_to_array(html_entity_decode($descriptionrow['content']));

                if(is_array($bulletPointFromUl)) {
                    foreach($bulletPointFromUl as $bullet) {
                        $bulletPoints[] = $bullet;
                    }
                } else {
                    $allowedOutput = str_ireplace($allowedReplace,$allowed,$output);
                    $allowedConvert = str_ireplace($convert,$convertReplace,$allowedOutput);
                    $bulletPoints[] = strip_tags($allowedConvert,implode(',',$allowed))."\r\n";
                }
            }
        }

        $descriptionText = implode(" ", $descriptionText);

        if(strlen($descriptionText) > 2000) {
            $descriptionText = substr($descriptionText, 0, 2000);
        }

        $descriptionText = strip_tags($descriptionText,implode(',',$allowed));

        $cData = $this->bulkXml->createCDATASection($descriptionText);
        $descriptionTag = $productdescription->appendChild(
            $this->bulkXml->createElement("Description")
        );
        $descriptionTag->appendChild($cData);

        // bullet points
        $countBulletpoints = 1;
        foreach($bulletPoints as $bulletPoint) {
            if($countBulletpoints > 5) break;

            $cData = $this->bulkXml->createCDATASection(substr($bulletPoint, 0, 500));
            $bulletPoint = $productdescription->appendChild(
                $this->bulkXml->createElement("BulletPoint")
            );
            $bulletPoint->appendChild($cData);

            $countBulletpoints++;
        }



        // item maße, nur wenn alle felder gefuellt sind
        if($item['width'] > 0 && $item['height'] > 0 && $item['length'] > 0 && $item['weight'] > 0) {
            $itemDimension = $productdescription->appendChild(
                $this->bulkXml->createElement("ItemDimensions")
            );

            $length = $this->bulkXml->createElement("Length", $item['length']);
            $length->setAttribute("unitOfMeasure", "MM");

            $width = $this->bulkXml->createElement("Width", $item['width']);
            $width->setAttribute("unitOfMeasure", "MM");

            $height = $this->bulkXml->createElement("Height", $item['height']);
            $length->setAttribute("unitOfMeasure", "MM");

            $weigth = $this->bulkXml->createElement("Weight", $item['weight']);
            $weigth->setAttribute("unitOfMeasure", "KG");

            $itemDimension->appendChild($length);
            $itemDimension->appendChild($width);
            $itemDimension->appendChild($height);
            $itemDimension->appendChild($weigth);
        }

        $productdescription->appendChild($this->bulkXml->createElement('Manufacturer', $brand));

        //Herstellerartikelnummer
        $productdescription->appendChild($this->bulkXml->createElement('MfrPartNumber', $item["item_no"]));

        // Suchbegriffe
        if($item['search_query'] != "") {
            $searchTerms = explode(",", $item['search_query']);
            if(count($searchTerms)) {
                $searchTermCounter = 1;
                foreach($searchTerms as $searchTerm) {
                    $productdescription->appendChild($this->bulkXml->createElement('SearchTerms', trim($searchTerm)));
                    $searchTermCounter++;
                    if($searchTermCounter > 5) {
                        break; // nur 5 Suchbegriffe erlaubt
                    }
                }
            }
        }

        $productdescription->appendChild($this->bulkXml->createElement('RecommendedBrowseNode', $item_has_category['marketplace_category_code']));

        $product->appendChild($productdescription);

        // Einfuegen des Bereichs ProductData anhand der XSD Files

        $amazonProduct = new $productClass();
        $amazonProduct->create($this->bulkXml, $item, $item_has_category, $parent, $xsdRow, $variations, $product, $allVariationAttributes);

        // gesamten Product block hinzufuegen
        $message->appendChild($product);

        $this->incrementMessageId();

        $this->insertMessageToBulk();
        $this->insertedToBulk = true;
        $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));

        return;
    }

    protected function create_pricing_feed($item) {
        if($this->getCurrentFunction() != "update_price") {
            return;
        }

        $this->bulkXml = new DOMDocument('1.0', "UTF-8");

        $base_price = number_format($item['base_price'], 2, '.', '');

        $message = $this->bulkXml->createElement('Message');
        $this->bulkXml->appendChild($message);

        $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
        $operationtype = $this->bulkXml->createElement('OperationType', "Update");
        $message->appendChild($messageIdElement);
        $message->appendChild($operationtype);

        // price
        $price = $this->bulkXml->createElement('Price');
        $price->appendChild($this->bulkXml->createElement('SKU', $item['item_no']));

        $standardPrice = $this->bulkXml->createElement('StandardPrice', $base_price);
        $standardPrice->setAttribute("currency",$GLOBALS['shop_currency']['code']);
        $price->appendChild($standardPrice);

        $message->appendChild($price);

        $this->incrementMessageId();

        $this->insertMessageToBulk();
        $this->insertedToBulk = true;
        $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));
    }

    protected function create_inventory_feed($item) {
        if($this->getCurrentFunction() != "update_inventory") {
            return;
        }

        $this->bulkXml = new DOMDocument('1.0', "UTF-8");

        // Verfuegbarkeit
        //$max_inventory = (int)$item["marketplace_max_inventory"];
        $max_inventory = (int)$item["inventory"];

        $message = $this->bulkXml->createElement('Message');
        $this->bulkXml->appendChild($message);

        $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
        $operationtype = $this->bulkXml->createElement('OperationType', "Update");
        $message->appendChild($messageIdElement);
        $message->appendChild($operationtype);

        // inventory
        $inventory = $this->bulkXml->createElement('Inventory');
        $inventory->appendChild($this->bulkXml->createElement('SKU', $item['item_no']));
        $inventory->appendChild($this->bulkXml->createElement('Quantity', $max_inventory));
        $inventory->appendChild($this->bulkXml->createElement('FulfillmentLatency', $this->dispatch_mapping($max_inventory, $item)));

        $message->appendChild($inventory);

        $this->incrementMessageId();

        $this->insertMessageToBulk();
        $this->insertedToBulk = true;
        $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));
    }

    protected function create_product_images_feed($item) {
        if($this->getCurrentFunction() != "update_images") {
            return;
        }

        // Artikelbilder
        $imagesresult = get_item_images_clean($item, $GLOBALS["shop_setup"]["image_config"]);
        if(mysqli_num_rows($imagesresult) == 0) {
            return;
        }

        $imageType = "Main";
        $imageCounter = 1;
        while ($image = mysqli_fetch_assoc($imagesresult)) {

            $this->bulkXml = new DOMDocument('1.0', "UTF-8");

            $message = $this->bulkXml->createElement('Message');
            $this->bulkXml->appendChild($message);

            $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
            $operationtype = $this->bulkXml->createElement('OperationType', "Update");
            $message->appendChild($messageIdElement);
            $message->appendChild($operationtype);

            $productImage = $message->appendChild(
                $this->bulkXml->createElement("ProductImage")
            );

            $productImage->appendChild(
                $this->bulkXml->createElement("SKU", $item['item_no'])
            );

            $productImage->appendChild(
                $this->bulkXml->createElement("ImageType", $imageType)
            );

            $filepath = rtrim($GLOBALS['shop']['shop_url'],'/') . $GLOBALS["shop_setup"]["image_config"][4]["path"] . "/" . $image['filename'];
            if (@getimagesize(rtrim($GLOBALS['shop']['shop_url'],'/') . $GLOBALS["shop_setup"]["uploaddir"] . $image['filename'])) {
                $filepath = rtrim($GLOBALS['shop']['shop_url'],'/') . $GLOBALS["shop_setup"]["uploaddir"] . $image['filename'];
            }

            $productImage->appendChild(
                $this->bulkXml->createElement("ImageLocation", $filepath)
            );

            /*$productImage->appendChild(
                $this->bulkXml->createElement("ImageLocation", rtrim($GLOBALS['shop']['shop_url'],'/') . $GLOBALS["shop_setup"]["image_config"][4]["path"] . "/" . $image['filename'])
            );*/

            $this->incrementMessageId();

            $this->insertMessageToBulk();
            $this->insertedToBulk = true;
            $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));

            // naechstes Bild ist nicht mehr main sondern PTx
            $imageType = "PT" . $imageCounter;
            $imageCounter++;

            if($imageCounter > 8) {
                break; // keine weiteren bilder
            }
        }
    }

    protected function create_relationship_feed($item, $variation_items) {
        if($this->getCurrentFunction() != "update_relationship") {
            return;
        }

        $this->bulkXml = new DOMDocument('1.0', "UTF-8");

        $message = $this->bulkXml->createElement('Message');
        $this->bulkXml->appendChild($message);

        $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
        $operationtype = $this->bulkXml->createElement('OperationType', "Update");
        $message->appendChild($messageIdElement);
        $message->appendChild($operationtype);

        $relationShip = $message->appendChild(
            $this->bulkXml->createElement("Relationship")
        );

        $sku = $item['item_no'];
        if($GLOBALS['shop']['variant_typ'] == 1) {
            $sku = $item['item_no'] . self::PARENTITEM_SUFFIX;
        }

        $relationShip->appendChild(
            $this->bulkXml->createElement("ParentSKU", $sku)
        );

        foreach($variation_items as $variation_item) {
            $relation = $relationShip->appendChild(
                $this->bulkXml->createElement("Relation")
            );

            $relation->appendChild(
                $this->bulkXml->createElement("SKU", $variation_item['item_no'])
            );

            $relation->appendChild(
                $this->bulkXml->createElement("Type", "Variation")
            );
        }

        $this->incrementMessageId();

        $this->insertMessageToBulk();
        $this->insertedToBulk = true;
        $this->setItemInBulk(array('item_no' => $item['item_no'], 'function' => $this->getCurrentFunction()));
    }

    protected function handle_complete_orders($api) {
        // Basis XML erstellen
        $bulkXmlFile = $api->createBaseDocument();
        $this->setBulkXmlFilename($bulkXmlFile);

        $shipped_orders = get_shipped_orders();

        $shipmentsAvailable = false;
        while($order = mysqli_fetch_assoc($shipped_orders)) {

            $query = "UPDATE shop_sales_header set marketplace_shipped = 0 where id = " . $order['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);

            if($order['shop_code'] != $GLOBALS['shop']['code']) continue;
            if($order['language_code'] != $GLOBALS['shop_language']['code']) continue;

            $shipmentsAvailable = true;

            $this->bulkXml = null;

            $this->bulkXml = new DOMDocument('1.0', "UTF-8");

            $message = $this->bulkXml->createElement('Message');
            $this->bulkXml->appendChild($message);

            $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
            $message->appendChild($messageIdElement);

            $orderFulfillment = $this->bulkXml->createElement('OrderFulfillment');

            $orderFulfillment->appendChild($this->bulkXml->createElement('AmazonOrderID', $order['marketplace_order']));
            $orderFulfillment->appendChild($this->bulkXml->createElement('FulfillmentDate', $this->genTime(false, 180)));

            /********** Optionale Daten ************/
            /*
            $FulfillmentData = $this->bulkXml->createElement('FulfillmentData');
            $FulfillmentData->appendChild($this->bulkXml->createElement('CarrierCode', "UPS"));
            $FulfillmentData->appendChild($this->bulkXml->createElement('ShipperTrackingNumber', "1234"));
            $orderFulfillment->appendChild($FulfillmentData);

            // Item Tag nur nötig wenn nicht die gesamte Bestellung verschickt wurde sondern nur ein Teil
            // Pro Artikel muss dann ein Item Tag erstellt werden (muss noch getestet werden, steht nichts in der Doku)
            $Item = $this->bulkXml->createElement('Item');
            $Item->appendChild($this->bulkXml->createElement('AmazonOrderItemCode', 1234));
            $Item->appendChild($this->bulkXml->createElement('Quantity', 5));
            $orderFulfillment->appendChild($Item);
            */

            $message->appendChild($orderFulfillment);

            $this->incrementMessageId();
            $this->insertMessageToBulk();
            $this->insertedToBulk = true;
        }

        $api->endBaseDocument();

        return $shipmentsAvailable;
    }

    protected function handle_complete_orders_shippment($api) {
        // Basis XML erstellen
        $bulkXmlFile = $api->createBaseDocument();
        $this->setBulkXmlFilename($bulkXmlFile);

        $shipments = get_shipments();

        $shipmentsAvailable = false;
        while($shipment = mysqli_fetch_assoc($shipments)) {

            $order = get_order($shipment['webshop_order_no']);

            if($order['shop_code'] != $GLOBALS['shop']['code']) continue;
            if($order['language_code'] != $GLOBALS['shop_language']['code']) continue;

            $query = "UPDATE shop_sales_shipment_header set marketplace_update = 0 where id = " . $shipment['id'];
            @mysqli_query($GLOBALS['mysql_con'], $query);

            $shipmentsAvailable = true;

            $this->bulkXml = null;

            $this->bulkXml = new DOMDocument('1.0', "UTF-8");

            $message = $this->bulkXml->createElement('Message');
            $this->bulkXml->appendChild($message);

            $messageIdElement = $this->bulkXml->createElement('MessageID', $this->getMessageId());
            $message->appendChild($messageIdElement);

            $orderFulfillment = $this->bulkXml->createElement('OrderFulfillment');

            $orderFulfillment->appendChild($this->bulkXml->createElement('AmazonOrderID', $order['marketplace_order']));
            $orderFulfillment->appendChild($this->bulkXml->createElement('FulfillmentDate', $this->genTime(false, 7200)));

            $sales_shipment_lines = get_sales_shipment_lines($shipment);

            while($shipmentLine = mysqli_fetch_assoc($sales_shipment_lines)) {

                $sales_line = get_order_line($order['id'],$shipmentLine['no'],$shipmentLine['quantity']);
                $salesLine = mysqli_fetch_assoc($sales_line);

                $Item = $this->bulkXml->createElement('Item');
                $Item->appendChild($this->bulkXml->createElement('AmazonOrderItemCode', $salesLine['marketplace_line_id']));
                $Item->appendChild($this->bulkXml->createElement('Quantity', (int)$shipmentLine['quantity']));
                $orderFulfillment->appendChild($Item);
            }

            /********** Optionale Daten ************/
            /*
            $FulfillmentData = $this->bulkXml->createElement('FulfillmentData');
            $FulfillmentData->appendChild($this->bulkXml->createElement('CarrierCode', "UPS"));
            $FulfillmentData->appendChild($this->bulkXml->createElement('ShipperTrackingNumber', "1234"));
            $orderFulfillment->appendChild($FulfillmentData);

            // Item Tag nur nötig wenn nicht die gesamte Bestellung verschickt wurde sondern nur ein Teil
            // Pro Artikel muss dann ein Item Tag erstellt werden (muss noch getestet werden, steht nichts in der Doku)
            $Item = $this->bulkXml->createElement('Item');
            $Item->appendChild($this->bulkXml->createElement('AmazonOrderItemCode', 1234));
            $Item->appendChild($this->bulkXml->createElement('Quantity', 5));
            $orderFulfillment->appendChild($Item);
            */

            $message->appendChild($orderFulfillment);

            $this->incrementMessageId();
            $this->insertMessageToBulk();
            $this->insertedToBulk = true;
        }

        $api->endBaseDocument();

        return $shipmentsAvailable;
    }

    /**
     * Artikel zu amazon uebertragen. nur die Basisdaten
     *
     * @param array $parameter
     */
    public function send_products($parameter = array()) {
        if(!isset($parameter['function'])) {
            $this->setCurrentFunction("update_products");
        } else {
            $this->setCurrentFunction($parameter['function']);
        }

        $api = $this->getAmazonApi();
        $this->handle_products($api, $parameter);
        $this->setPayload($this->getItemInBulk());

        if($this->bulkXml === null || $this->insertedToBulk == false) {
            $this->setCallOk(true);
            //Delete bulk file
            unlink($this->bulkXmlFilename);
            return; // kein call zu amazon weil keine artikel zum upload vorhanden
        }

        try {
            $response = $api->submitFeed();

            if(	$response->isSetSubmitFeedResult() &&
                $response->getSubmitFeedResult()->isSetFeedSubmissionInfo() &&
                $response->getSubmitFeedResult()->getFeedSubmissionInfo()->isSetFeedSubmissionId()
            ) {
                $feedSubmissionId = $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Artikel uebertragen beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function send_products_submission($submission_id) {
        $this->submission($submission_id);
    }

    /**
     * Artikeluebertragung beendet
     *
     * @param string $generated_id
     * @access public
     */
    public function send_products_submission_ready($generated_id) {
        $this->submission_ready($generated_id);
    }

    /**
     * Artikeluebertragung verarbeiten
     *
     * @param string $generated_id
     * @access public
     */
    public function send_products_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);

        //Set all items success
        $itemsInBulk = json_decode($this->getPayload());
        foreach ($itemsInBulk as $item) {
            $field = '';
            switch ($item->function) {
                case 'update_price':
                    $field = 'marketplace_update_price';
                    break;
                case 'update_inventory':
                    $field = 'marketplace_update_inventory';
                    break;
                case 'update_products':
                    $field = 'marketplace_update';
                    break;
                case 'update_images':
                    $field = 'marketplace_update_images';
                    break;
                case 'update_relationship':
                    $field = 'marketplace_start_relationship';
                    break;
            }
            if ($field !== '') {
                $this->set_item_submission_success(array("item_no" => $item->item_no),$field);
            }
        }

        $errors = array();
        foreach($xml->Message->ProcessingReport->Result as $result) {

            if($result->ResultCode == "Error") {
                $sku = (string)$result->AdditionalInfo->SKU;
                $messageCode = (string)$result->ResultMessageCode;
                $message = mysqli_real_escape_string($GLOBALS['mysql_con'], (string)$result->ResultDescription);
                $this->insert_item_error($sku, self::MARKETPLACE_TYPE, "send_products", $messageCode, $message);

                if(!is_array($errors[$sku])) {
                    $errors[$sku] = array();
                }

                $errors[$sku][] = $message;

                if (self::AMAZON_ERROR_CODE_PRODUCT_EXISTS == $messageCode) {
                    $this->set_item_exists($sku, self::MARKETPLACE_TYPE);
                }

                if (self::AMAZON_ERROR_CODE_PRODUCT_EXISTS_2 == $messageCode) {
                    $this->set_item_exists($sku, self::MARKETPLACE_TYPE);
                }

                if (self::AMAZON_ERROR_CODE_PRODUCT_NO_LONGER_VALID == $messageCode) {
                    $this->set_item_not_exists($sku, self::MARKETPLACE_TYPE);
                }
            }
        }
        if(count($errors)) {
            foreach($errors as $item_no => $errorMessage) {
                $this->set_item_submission_error($sku, join("; ", $errorMessage));
                $this->set_item_start_relationship($sku, 0);
            }
        }

        if(isset($parameter['check_relationship'])) {
            unset($parameter['check_relationship']);
            $result = get_items_for_relationship();

            if(@mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $this->set_item_start_relationship($row['item_no'], 0);
                    $this->set_item_to_update($row['item_no']);
                }

                $parameter['start_relationship'] = 1;
                $this->new_queue(self::MARKETPLACE_TYPE, "send_products", $parameter, 0, false);
            }

        } elseif(isset($parameter['start_relationship'])) {
            unset($parameter['start_relationship']);
            $parameter['function'] = 'update_relationship';
            $this->new_queue(self::MARKETPLACE_TYPE, "send_products", $parameter, 0, false);
        }

        $this->setCallOk(true);
    }

    /**
     * Bestellungen abholen
     *
     * @param array $parameter
     */
    public function get_orders($parameter = array()) {
        // request erstellen
        $report = new AmazonReport();
        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMarketplaceIdList($report->getMarketplaceIdArray());
        $request->setMerchant($report->get_merchant_id());
        $request->setReportType('_GET_FLAT_FILE_ORDERS_DATA_');
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }
        $request->setStartDate($this->genTime("-3 days", 120));

        $reportOptions = array();
        $reportOptions[] = 'ShowSalesChannel=true';
        $request->setReportOptions(join(";", $reportOptions));

        try {
            $response = $report->getService()->requestReport($request);

            if(	$response->isSetRequestReportResult() &&
                $response->getRequestReportResult()->isSetReportRequestInfo() &&
                $response->getRequestReportResult()->getReportRequestInfo()->isSetReportRequestId()
            ) {
                $feedSubmissionId = $response->getRequestReportResult()->getReportRequestInfo()->getReportRequestId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {



            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Abholen der Bestellungen (report ertstellen) beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function get_orders_submission($submission_id) {
        $this->report_submission($submission_id);
    }

    /**
     * Wenn Bestell Report erstellt ist
     *
     * @param string $generated_id
     * @access public
     */
    public function get_orders_submission_ready($generated_id) {
        $this->report_submission_ready($generated_id, "csv");
    }

    /**
     * Generierten Report verarbeiten
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function get_orders_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;


        $orderFile = new SplFileObject($filename);
        $orderFile->setFlags(SplFileObject::READ_CSV);
        $orderFile->setCsvControl("\t");

        $csvHeader = array();
        foreach ($orderFile as $line => $row) {
            if($line == 0) {
                // erste zeile ist header
                $csvHeader = $row;
                continue;
            }

            if(count($row) <= 2) {
                // leere zeile
                continue;
            }

            // header mit zeile kombinieren um vom array index wegzukommen
            $currentOrderRow = array_combine($csvHeader, $row);

            // amazon liefert ISO zurueck, in utf8 umwandeln
            $currentOrderRow = array_map("utf8_encode", $currentOrderRow );

            // Bestellzeile bearbeiten. Eine Zeile ist ein bestelltes Item.
            $this->handle_order($currentOrderRow);

            if($this->currentHandledOrders !== null) {

                // versandmethode auslesen
                $query = "SELECT line_no from shop_shipping_option 
                    where company = '" . $GLOBALS['shop']['company'] . "'
                    and shop_code = '" . $GLOBALS['shop']['code'] . "'
                    and language_code = '" . $GLOBALS['shop_language']['code'] . "'
                    and country_code = '" . $this->currentHandledOrders['ship-country'] . "'
                    and shipping_cost = '" . $this->currentHandledOrders['shipping_cost'] . "'
                    limit 1
                ";



                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $shipping_line_no = 0;
                if(mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $shipping_line_no = $row['line_no'];
                    $query = "UPDATE shop_sales_header SET
                                shipping_option_line_no = '" . $shipping_line_no . "'
                            WHERE id = " . $this->currentHandledOrders['currentSalesHeaderId'];


                    @mysqli_query($GLOBALS['mysql_con'], $query);
                }


                $this->currentHandledOrders = null;

            }
        }
    }

    /**
     * Bestelldetails auslesen
     *
     * @param array $parameter
     * @access public
     */
    public function get_order_detail($parameter = array()) {
        $order = new AmazonOrder();

        $request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
        $request->setSellerId($order->get_merchant_id());
        if($order->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($order->get_mws_auth_token());
        }

        $request->withAmazonOrderId("303-7761229-6580311");

        try {
            $response = $order->getService()->GetOrder($request);

            $this->setCallOk(true);
            $filename = $this->getResultFilename();
            $file = __DIR__."/../xml/" . $filename;

            // Result in Datei schreiben
            $splFile = new SplFileObject($file, "w");
            $splFile->fwrite($response->toXML());

            $this->setSubmissionResult($filename);
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * Abschluss
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function get_order_detail_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);
    }

    public function get_lowest_offer_listing($parameter = array()) {
        $product = new AmazonProduct();

        $condition = "Any";
        if(isset($parameter['condition'])) {
            $condition = $parameter['condition'];
        }

        $asin_list = new MarketplaceWebServiceProducts_Model_SellerSKUListType();
        $asin_list->setSellerSKU(array($parameter['item_no']));

        $request = new MarketplaceWebServiceProducts_Model_GetLowestOfferListingsForSKURequest();
        $request->setSellerId($product->get_merchant_id());
        $request->setMarketplaceId($product->get_marketplace_id());
        $request->setItemCondition($condition);
        $request->setSellerSKUList($asin_list);
        if($product->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($product->get_mws_auth_token());
        }

        try {
            $response = $product->getService()->GetLowestOfferListingsForSKU($request);

            $this->setCallOk(true);
            $filename = $this->getResultFilename();
            $file = __DIR__."/../xml/" . $filename;

            // Result in Datei schreiben
            $splFile = new SplFileObject($file, "w");
            $splFile->fwrite($response->toXML());

            $this->setSubmissionResult($filename);
        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    public function get_lowest_offer_listing_submission_result($result_file, $parameter) {

    }

    /**
     * Bestelldetails auslesen
     *
     * @param array $parameter
     * @access public
     */
    public function get_lowest_price($parameter = array()) {
        $product = new AmazonProduct();

        $condition = "New";
        if(isset($parameter['condition'])) {
            $condition = $parameter['condition'];
        }

        $request = new MarketplaceWebServiceProducts_Model_GetLowestPricedOffersForSKURequest();
        $request->setSellerId($product->get_merchant_id());
        $request->setMarketplaceId($product->get_marketplace_id());
        $request->setItemCondition($condition);
        $request->setSellerSKU($parameter['item_no']);
        if($product->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($product->get_mws_auth_token());
        }

        try {
            $response = $product->getService()->GetLowestPricedOffersForSKU($request);

            $this->setCallOk(true);
            $filename = $this->getResultFilename();
            $file = __DIR__."/../xml/" . $filename;

            // Result in Datei schreiben
            $splFile = new SplFileObject($file, "w");
            $splFile->fwrite($response->toXML());

            $this->setSubmissionResult($filename);
        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * Abschluss
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function get_lowest_price_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);

        if((string)$xml->GetLowestPricedOffersForSKUResult->Summary->TotalOfferCount == "0") {
            return;
        }

        $sku = (string)$xml->GetLowestPricedOffersForSKUResult->attributes()->SKU;
        $itemcondition = (string)$xml->GetLowestPricedOffersForSKUResult->attributes()->ItemCondition;
        $marketplaceId = (string)$xml->GetLowestPricedOffersForSKUResult->attributes()->MarketplaceID;

        @mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_amazon_prices where sku = '" . $sku . "' and `condition` = '" . $itemcondition . "' and marketplace_id = '" . $marketplaceId . "'");

        /*
        foreach($xml->GetLowestPricedOffersForASINResult->Summary->LowestPrices->LowestPrice as $LowestPrice) {
            $query = "INSERT INTO shop_marketplace_amazon_prices (
                        asin, sku, `condition`, marketplace_id, lowest_price, lowest_shipping, lowest_fulfillment_channel
                )
                VALUES (
                    '" . $asin . "',
                    '',
                    '" . $itemcondition . "',
                    '" . $marketplaceId . "',
                    '" . (string)$LowestPrice->ListingPrice->Amount . "',
                    '" . (string)$LowestPrice->Shipping->Amount . "',
                    '" . (string)$LowestPrice->attributes()->fulfillmentChannel . "'
                )
            ";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        // Block mit den buybox Preisen
        foreach($xml->GetLowestPricedOffersForASINResult->Summary->BuyBoxPrices->BuyBoxPrice as $BuyBoxPrice) {
            $query = "INSERT INTO shop_marketplace_amazon_prices (
                        asin, sku, `condition`, marketplace_id, buybox_price, buybox_shipping
                )
                VALUES (
                    '" . $asin . "',
                    '',
                    '" . $itemcondition . "',
                    '" . $marketplaceId . "',
                    '" . (string)$BuyBoxPrice->ListingPrice->Amount . "',
                    '" . (string)$BuyBoxPrice->Shipping->Amount . "'
                )
            ";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
        */

        foreach($xml->GetLowestPricedOffersForSKUResult->Offers->Offer as $Offer) {
            $query = "INSERT INTO shop_marketplace_amazon_prices (
						asin, sku, `condition`, marketplace_id, 
						offer_listing_price,
						offer_shipping_price,
						offer_ships_from,
						offer_is_fullfilled_by_amazon,
						offer_is_buybox_winner,
						offer_is_featured_merchant,
						seller_positive_feedback_rating,
						seller_feedback_count,
						shipping_time_minimum_hours,
						shipping_time_maximum_hours,
						shipping_availability_type,
						my_offer
				)
				VALUES (
					'',
					'" . $sku . "',
					'" . $itemcondition . "',
					'" . $marketplaceId . "',
					'" . (string)$Offer->ListingPrice->Amount . "',
					'" . (string)$Offer->Shipping->Amount . "',
					'" . (string)$Offer->ShipsFrom->Country . "',
					'" . (string)$Offer->IsFulfilledByAmazon . "',
					'" . (string)$Offer->IsBuyBoxWinner . "',
					'" . (string)$Offer->IsFeaturedMerchant . "',
					'" . (string)$Offer->SellerFeedbackRating->SellerPositiveFeedbackRating . "',
					'" . (string)$Offer->SellerFeedbackRating->FeedbackCount . "',
					'" . (string)$Offer->ShippingTime->attributes()->minimumHours . "',
					'" . (string)$Offer->ShippingTime->attributes()->maximumHours . "',
					'" . (string)$Offer->ShippingTime->attributes()->availabilityType . "',
					" . (string)$Offer->MyOffer . "
				)
			";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }

    /**
     * Bestellung abschliessen (Ship and Confirm Shipment (and get paid) - Order Fulfillment)
     *
     * @param array $parameter
     * @access public
     */
    public function complete_orders($parameter = array()) {

        //Sales Shipment Header
        $shipments = get_shipments();
        if(@mysqli_num_rows($shipments) == 0) {
            $this->setCallOk(true);
            return;
        }

        $class = new AmazonFeed();
        $class->setFeedType("_POST_ORDER_FULFILLMENT_DATA_");
        $class->setMessageType("OrderFulfillment");
        $class->setShowPurgeAndReplace(false);

        $shipmentsAvailable = $this->handle_complete_orders_shippment($class);

        if($shipmentsAvailable === false) {
            $this->setCallOk(true);
            return;
        }

        try {
            $response = $class->submitFeed();

            if(	$response->isSetSubmitFeedResult() &&
                $response->getSubmitFeedResult()->isSetFeedSubmissionInfo() &&
                $response->getSubmitFeedResult()->getFeedSubmissionInfo()->isSetFeedSubmissionId()
            ) {
                $feedSubmissionId = $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }


        //Sales Header
        /*
        $shipments = get_shipped_orders();
        if(@mysqli_num_rows($shipments) == 0) {
            $this->setCallOk(true);
            return;
        }

        $class = new AmazonFeed();
        $class->setFeedType("_POST_ORDER_FULFILLMENT_DATA_");
        $class->setMessageType("OrderFulfillment");

        $shipmentsAvailable = $this->handle_complete_orders($class);

        if($shipmentsAvailable === false) {
            $this->setCallOk(true);
            return;
        }

        try {
            $response = $class->submitFeed();

            if(	$response->isSetSubmitFeedResult() &&
                $response->getSubmitFeedResult()->isSetFeedSubmissionInfo() &&
                $response->getSubmitFeedResult()->getFeedSubmissionInfo()->isSetFeedSubmissionId()
            ) {
                $feedSubmissionId = $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
        */
    }

    /**
     * pruefen ob Request beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function complete_orders_submission($submission_id) {
        $this->submission($submission_id);
    }

    /**
     * Request beendet
     *
     * @param string $generated_id
     * @access public
     */
    public function complete_orders_submission_ready($generated_id) {
        $this->submission_ready($generated_id);
    }

    /**
     * Abschluss
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function complete_orders_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);
    }

    /**
     * Bestellung Quittiren / Bestaetigen und eigene Bestellnummer verknuepfen
     *
     * @param array $parameter
     * @access public
     */
    public function acknowledge_orders($parameter = array()) {
        $class = new AmazonFeed();
        $class->setFeedType("_POST_ORDER_ACKNOWLEDGEMENT_DATA_");
        $class->setMessageType("OrderAcknowledgement");

        $this->handle_acknowledge_orders($class);
        $api = $this->getAmazonApi();
        try {
            $response = $api->submitFeed();

            if(	$response->isSetSubmitFeedResult() &&
                $response->getSubmitFeedResult()->isSetFeedSubmissionInfo() &&
                $response->getSubmitFeedResult()->getFeedSubmissionInfo()->isSetFeedSubmissionId()
            ) {
                $feedSubmissionId = $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Request beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function acknowledge_orders_submission($submission_id) {
        $this->submission($submission_id);
    }

    /**
     * Request beendet
     *
     * @param string $generated_id
     * @access public
     */
    public function acknowledge_orders_submission_ready($generated_id) {
        $this->submission_ready($generated_id);
    }

    /**
     * Abschluss
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function acknowledge_orders_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);
    }

    /**
     * Bestellung bearbeiten wenn z.B. Ware zurueckgeschickt wurde
     *
     * @param array $parameter
     * @access public
     */
    public function adjust_orders($parameter = array()) {
        $class = new AmazonFeed();
        $class->setFeedType("_POST_PAYMENT_ADJUSTMENT_DATA_");
        $class->setMessageType("OrderAdjustment");

        $this->handle_acknowledge_orders($class);
        $api = $this->getAmazonApi();
        try {
            $response = $api->submitFeed();

            if(	$response->isSetSubmitFeedResult() &&
                $response->getSubmitFeedResult()->isSetFeedSubmissionInfo() &&
                $response->getSubmitFeedResult()->getFeedSubmissionInfo()->isSetFeedSubmissionId()
            ) {
                $feedSubmissionId = $response->getSubmitFeedResult()->getFeedSubmissionInfo()->getFeedSubmissionId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Request beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function adjust_orders_submission($submission_id) {
        $this->submission($submission_id);
    }

    /**
     * Request beendet
     *
     * @param string $generated_id
     * @access public
     */
    public function adjust_orders_submission_ready($generated_id) {
        $this->submission_ready($generated_id);
    }

    /**
     * Abschluss
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function adjust_orders_submission_result($result_file, $parameter) {
        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);
    }

    /**
     * Importiert alle Artikel vom marketplace zum webshop anstossen
     *
     */
    public function import_items($parameter = array()) {
        // request erstellen
        $report = new AmazonReport();
        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMarketplaceIdList(array("Id" => array($report->get_marketplace_id())));
        $request->setMerchant($report->get_merchant_id());
        $request->setReportType('_GET_MERCHANT_LISTINGS_DATA_');
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }
        $request->setStartDate($this->genTime("-1 day", 120));

        try {
            $response = $report->getService()->requestReport($request);

            if(	$response->isSetRequestReportResult() &&
                $response->getRequestReportResult()->isSetReportRequestInfo() &&
                $response->getRequestReportResult()->getReportRequestInfo()->isSetReportRequestId()
            ) {
                $feedSubmissionId = $response->getRequestReportResult()->getReportRequestInfo()->getReportRequestId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Abholen des Bestands (report ertstellen) beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function import_items_submission($submission_id) {
        $this->report_submission($submission_id);
    }

    /**
     * Wenn Bestands Report erstellt ist
     *
     * @param string $generated_id
     * @access public
     */
    public function import_items_submission_ready($generated_id) {
        $this->report_submission_ready($generated_id, "csv");
    }

    /**
     * Generierten Bestands Report verarbeiten
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function import_items_submission_result($result_file, $parameter) {

        $query = "DELETE from shop_marketplace_items 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE from shop_marketplace_items_link 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			and language_code = '" . $GLOBALS['shop_language']['code'] . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE from shop_marketplace_items_attributes 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			and language_code = '" . $GLOBALS['shop_language']['code'] . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "DELETE from shop_marketplace_items_import 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			and language_code = '" . $GLOBALS['shop_language']['code'] . "'
		";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $filename = __DIR__."/../xml/" . $result_file;

        $inventoryFile = new SplFileObject($filename);
        $inventoryFile->setFlags(SplFileObject::READ_CSV);
        $inventoryFile->setCsvControl("\t");

        $csvHeader = array();
        foreach ($inventoryFile as $line => $row) {
            if($line == 0) {
                // erste zeile ist header
                $csvHeader = $row;
                continue;
            }

            if(count($row) <= 2) {
                // leere zeile
                continue;
            }

            // amazon liefert ISO zurueck, in utf8 umwandeln
            $currentInventoryRow = array_map("utf8_encode", $row );

            // brand: marke (steht direkt unterm titel)
            // pulbisher:
            // manufacturer (hersteller)



            $ean = "";
            if($currentInventoryRow[9] == 4) {
                $ean = $currentInventoryRow[22];
            }

            $query = "INSERT INTO shop_marketplace_items (
				company, 
				shop_code, 
				language_code,
				marketplace_type, 
				item_no, 
				title, 
				description, 
				marketplace_item_id, 
				inventory, 
				base_price, 
				is_variation_parent, 
				channel,
				ean,
				timestamp_modified
			) VALUES (
				'" . $GLOBALS['shop']['company'] . "',
				'" . $GLOBALS['shop']['code'] . "',
				'" . $GLOBALS['shop_language']['code'] . "',
				" . self::MARKETPLACE_TYPE . ",
				'" . $currentInventoryRow[3] . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $currentInventoryRow[0]) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $currentInventoryRow[1]) . "',
				'" . $currentInventoryRow[16] . "',
				" . $currentInventoryRow[5] . ",
				" . $currentInventoryRow[4] . ",
				0,
				'Amazon.de',
				'" . $ean . "',
				" . time() . "
			)";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        //$this->new_queue(self::MARKETPLACE_TYPE, "get_item_import", array('start' => 0));
    }

    /**
     * Liest Daten eines Artikels fuer den Import aus.
     *
     */
    public function get_item_import($parameter = array()) {
        $start = 0;
        if(isset($parameter['start'])) {
            $start = $parameter['start'];
        }

        $asinIds = array();
        $query = "SELECT marketplace_item_id FROM shop_marketplace_items where 
				company = '" . $GLOBALS['shop']['company'] . "'
			  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
			  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
				order by id limit " . $start . ", 10
		";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $asinIds[] = $row['marketplace_item_id'];
        }

        if(count($asinIds) <= 0) {
            $this->setCallOk(true);
            return;
        }

        $product = new AmazonProduct();

        $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductRequest();
        $request->setSellerId($product->get_merchant_id());
        $request->setMarketplaceId($product->get_marketplace_id());

        $asin_list = new MarketplaceWebServiceProducts_Model_ASINListType();
        $asin_list->setASIN($asinIds);
        $request->setASINList($asin_list);

        if($product->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($product->get_mws_auth_token());
        }

        try {
            $response = $product->getService()->GetMatchingProduct($request);

            $this->setCallOk(true);
            $filename = $this->getResultFilename();
            $file = __DIR__."/../xml/" . $filename;

            // Result in Datei schreiben
            $splFile = new SplFileObject($file, "w");
            $splFile->fwrite($response->toXML());

            $this->setSubmissionResult($filename);
        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * Verarbeitet die Daten eines Artikel und importiert in die Import Zwischentabellen
     *
     */
    public function get_item_import_submission_result($result_file, $parameter = array()) {
        $start = 0;
        if(isset($parameter['start'])) {
            $start = $parameter['start'];
        }

        $filename = __DIR__."/../xml/" . $result_file;
        $resp = simplexml_load_file($filename);

        foreach($resp->GetMatchingProductResult as $result) {
            /**
             * Alle Artikel durchgehen, wenn Relationships->VariationParent dann diese verknüpfung speichern
             * Attribute durchgehen, wenn in der shop_marketplace_variant_attributes vorhanden dann speichern.
             * Alle "Feature" Elemente sind Bulletpoints (in der shop_item_description mit show_in_header = 0)
             * Brand: Marke
             * Manufacturer: Hersteller
             * Titel nochmal abspeichern (in der csv ist dieser gekürzt)
             *
             */
        }

        $this->new_queue(self::MARKETPLACE_TYPE, "get_item_import", array('start' => $start + 10), 0, false);
    }

    /**
     * Kategorien abholen
     *
     * @param array $parameter
     * @access public
     */
    public function fetch_categories($parameter = array()) {
        $report = new AmazonReport();

        // report options zusammenbauen
        $reportOptions = array();
        if(isset($parameter['rootOnly']) && $parameter['rootOnly'] == true) {
            $reportOptions[] = "RootNodesOnly=true";
        }

        if(isset($parameter['parentID'])) {
            $reportOptions[] = "BrowseNodeId=" . $parameter['parentID'];
        }

        $reportOptions[] = 'MarketplaceId=' . $report->get_marketplace_id();

        // request erstellen
        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMerchant($report->get_merchant_id());
        $request->setReportType('_GET_XML_BROWSE_TREE_DATA_');
        $request->setReportOptions(join(";", $reportOptions));
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }

        try {
            $response = $report->getService()->requestReport($request);

            if(	$response->isSetRequestReportResult() &&
                $response->getRequestReportResult()->isSetReportRequestInfo() &&
                $response->getRequestReportResult()->getReportRequestInfo()->isSetReportRequestId()
            ) {
                $feedSubmissionId = $response->getRequestReportResult()->getReportRequestInfo()->getReportRequestId();
                $this->setSubmissionId($feedSubmissionId);
                $this->setCallOk(true);
            } else {
                $this->setCallOk(false);
                $this->setErrorMessage("Keine Submission ID von amazon zurueckbekommen");
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * pruefen ob Abholen der Kategorien (report ertstellen) beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    public function fetch_categories_submission($submission_id) {
        $this->report_submission($submission_id);
    }

    /**
     * Wenn Kategorien Report erstellt ist
     *
     * @param string $generated_id
     * @access public
     */
    public function fetch_categories_submission_ready($generated_id) {
        $this->report_submission_ready($generated_id);
    }

    /**
     * Generierten Report verarbeiten
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function fetch_categories_submission_result($result_file, $parameter) {

        $filename = __DIR__."/../xml/" . $result_file;
        $xml = simplexml_load_file($filename);

        foreach($xml->Node as $category) {

            $pathById = $category->browsePathById;
            $pathById = explode(",", $pathById);
            $countPaths = count($pathById);

            $root_category = $pathById[1];

            if($countPaths <= 2) {
                $level = 1;
                $parentCategory = "";
                $category_name = $category->browsePathByName;
            } else {
                $level = $countPaths-1;
                $parentCategory = $pathById[$countPaths-2];
                $category_name = $category->browseNodeName;
            }

            if(isset($parameter['parentID'])) {
                $in_use = 1;
                $children_fetched = 1;
                $timestamp_children_fetched = time();
            } else {
                $in_use = 0;
                $children_fetched = 0;
                $timestamp_children_fetched = 0;
            }

            $query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = " . self::MARKETPLACE_TYPE .  " and category_code = '" . $category->browseNodeId . "' limit 1";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $row = @mysqli_fetch_array($result);

            if (@mysqli_num_rows($result) == 1) {
                $query = "
					UPDATE shop_marketplace_categories SET 
						category_code = '" . $category->browseNodeId . "', 
						category_name = '" . $category_name . "', 
						category_level = '" . $level . "',
						parent_category_code = '" . $parentCategory . "',
						root_category_code = '" . $root_category . "', 
						in_use = " . $in_use . ",
						children_fetched = " . $children_fetched . ", 
						timestamp_children_fetched = " . $timestamp_children_fetched . ",
						timestamp_fetched = " . time() . "
					WHERE
						marketplace_type =  " . self::MARKETPLACE_TYPE . " and
						category_code = '" . $category->browseNodeId . "'
				";
                @mysqli_query($GLOBALS['mysql_con'], $query);
            } else {
                $query = "	
					INSERT INTO shop_marketplace_categories (
						marketplace_type,
						category_id, 
						category_code, 
						category_name, 
						category_level, 
						parent_category_code, 
						root_category_code,
						in_use, 
						children_fetched, 
						timestamp_fetched,
						timestamp_children_fetched
					) VALUES (
						1,
						'',
						'" . $category->browseNodeId . "',
						'" . $category_name . "',
						'" . $level . "',
						'" . $parentCategory . "',
						'" . $root_category . "',
						" . $in_use . ",
						" . $children_fetched . ",
						" . time() . ",
						" . $timestamp_children_fetched . "
					)";
                @mysqli_query($GLOBALS['mysql_con'], $query);
            }

            $this->new_queue(self::MARKETPLACE_TYPE, "get_variant_attributes", array('category_code' => (string)$category->browseNodeId), 0, false);


        }


        $this->setCallOk(true);
    }

    public function get_variant_attributes() {
        $query = "SELECT category_code FROM shop_marketplace_categories where marketplace_type = 1 and in_use = 1 and category_level = 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        $category_ids = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $category_ids[] = $row['category_code'];
        }

        if(count($category_ids) == 0) {
            $this->setCallOk(true);
            return;
        }

        $query = "SELECT * from shop_marketplace_amazon_xsd where category_code in ('" . join("','", $category_ids) . "')";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);

        $this->helper_update_variant_attributes($result);

        $this->setCallOk(true);
        $query = "UPDATE shop_marketplace_queue SET finish_parent_queue = 1, parent_queue_id = '".$this->currentQueueId."' WHERE id = ".$this->currentQueueId;
        mysqli_query($GLOBALS["mysql_con"],$query);
        $this->setFinishSubmissionNow(true);
        $this->setSubmissionResult(true);
        return;
    }

    /**
     * Alle marketplaces abholen, bei denen der seller verkaufen kann
     *
     * @param array $parameter
     */
    public function get_seller_marketplaces($parameter = array()) {

        $report = new AmazonSeller();
        $request = new MarketplaceWebServiceSellers_Model_ListMarketplaceParticipationsRequest();
        $request->setSellerId($report->get_merchant_id());
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }

        try {
            $response = $report->getService()->ListMarketplaceParticipations($request);

            $this->setCallOk(true);
            $filename = $this->getResultFilename();
            $file = __DIR__."/../xml/" . $filename;

            // Result in Datei schreiben
            $splFile = new SplFileObject($file, "w");
            $splFile->fwrite($response->toXML());

            $this->setSubmissionResult($filename);
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * Generierten Report verarbeiten
     *
     * @param string $result_file
     * @param array $parameter
     * @access public
     */
    public function get_seller_marketplaces_submission_result($result_file, $parameter) {

    }

    protected function helper_update_variant_attributes($result) {
        while ($categoryRow = mysqli_fetch_assoc($result)) {
            $category_code = $categoryRow['category_code'];
            $productType = $categoryRow['productType'];
            $xsd = $categoryRow['xsd'];

            @mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_variant_themes where marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $category_code . "'");
            @mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_variant_attributes where marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $category_code . "'");

            $xsdFile = __DIR__."/xsd/Product/" . $xsd;

            $attributes = array();
            $XSDDOC = new DOMDocument();
            $XSDDOC->preserveWhiteSpace = false;
            if ($XSDDOC->load($xsdFile)) {

                $queryElementName = "element";
                if($xsd == "ProductClothing.xsd") {
                    $queryElementName = "enumeration";
                }

                // product types auslesen
                $productTypes = array();
                // Variant themes laden
                $xsdpath = new DOMXPath($XSDDOC);
                $productTypeTag = $xsdpath->query('//xsd:element[@name="' . $categoryRow['element_type_tag'] . '"]')->item(0);
                $productTypesQuery = $xsdpath->query('.//xsd:' . $queryElementName, $productTypeTag);
                foreach($productTypesQuery as $productTypeElement) {
                    if($productTypeElement->getAttribute("ref") != "") {
                        $productTypes[] = $productTypeElement->getAttribute("ref");
                        continue;
                    }

                    if($productTypeElement->getAttribute("name") != "") {
                        $productTypes[] = $productTypeElement->getAttribute("name");
                        continue;
                    }

                    if($productTypeElement->getAttribute("value") != "") {
                        $productTypes[] = $productTypeElement->getAttribute("value");
                        continue;
                    }
                }

                // variant themes vorm haupt produkt typ
                $attributeNodes = $xsdpath->query('//xsd:element[@name="' . $categoryRow['element'] . '"]')->item(0);
                $variationTheme = $xsdpath->query('.//xsd:element[@name="VariationTheme"]', $attributeNodes)->item(0);
                $update_variant_flag = false;
                if($variationTheme !== null) {
                    $themes = $xsdpath->query('.//xsd:enumeration', $variationTheme);
                    foreach($themes as $theme) {
                        $query = "INSERT INTO shop_marketplace_variant_themes (marketplace_type, category_code, productType, description, timestamp_fetched) VALUES (
						1,
						'" . $category_code . "',
						'" . $categoryRow['element'] . "',
						'" . $theme->getAttribute("value") . "',
						" . time() . "
					)";
                        @mysqli_query($GLOBALS['mysql_con'], $query);
                        $update_variant_flag = true;
                    }
                }


                // variant themes von unter produkt typen
                foreach($productTypes as $productTypeValue) {
                    // Variant themes laden
                    $attributeNodes = $xsdpath->query('//xsd:element[@name="' . $productTypeValue . '"]')->item(0);
                    $variationTheme = $xsdpath->query('.//xsd:element[@name="VariationTheme"]', $attributeNodes)->item(0);
                    if($variationTheme !== null) {
                        $themes = $xsdpath->query('.//xsd:enumeration', $variationTheme);
                        foreach ($themes as $theme) {
                            $query = "INSERT INTO shop_marketplace_variant_themes (marketplace_type, category_code, productType, description, timestamp_fetched) VALUES (
								1,
								'" . $category_code . "',
								'" . $productTypeValue . "',
								'" . $theme->getAttribute("value") . "',
								" . time() . "
							)";
                            @mysqli_query($GLOBALS['mysql_con'], $query);
                        }
                    }
                }

                // Attribute vom Haupt Produkt Type
                $attributeNodes = $xsdpath->query('//xsd:element[@name="' . $categoryRow['element'] . '"]')->item(0);
                $elements = $xsdpath->query('.//xsd:element', $attributeNodes);
                foreach($elements as $element) {
                    if($element->getAttribute("name") == "") {
                        continue;
                    }

                    $query = "SELECT id from shop_marketplace_variant_themes where description like '%" . $element->getAttribute("name") . "%' 
								and marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $category_code . "' limit 1";

                    $tempresult = @mysqli_query($GLOBALS['mysql_con'], $query);
                    if(mysqli_num_rows($tempresult) > 0) {
                        $is_variation = 1;
                    } else {
                        $is_variation = 0;
                    }

                    $query = "INSERT INTO shop_marketplace_variant_attributes (marketplace_type, category_code, productType, description, is_variation, timestamp_fetched) VALUES (
						1,
						'" . $category_code . "',
						'" . $categoryRow['element'] . "',
						'" . $element->getAttribute("name") . "',
						" . $is_variation . ",
						" . time() . "
					)";
                    mysqli_query($GLOBALS['mysql_con'], $query);
                }

                // Attribute vom Unter Produkt Typ
                foreach($productTypes as $productTypeValue) {
                    $attributeNodes = $xsdpath->query('//xsd:element[@name="' . $productTypeValue . '"]')->item(0);
                    $elements = $xsdpath->query('.//xsd:element', $attributeNodes);

                    foreach($elements as $element) {
                        if($element->getAttribute("name") == "" && $element->getAttribute("ref") == "") {
                            continue;
                        }

                        $attributeName = "";
                        if($element->getAttribute("name") != "") {
                            $attributeName = $element->getAttribute("name");
                        } elseif($element->getAttribute("ref") != "") {
                            $attributeName = $element->getAttribute("ref");
                        }

                        $query = "SELECT id from shop_marketplace_variant_themes where description like '%" . $attributeName . "%' 
									and marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $category_code . "' 
									and (productType = '" . $productTypeValue . "' or productType = '" . $categoryRow['element'] . "') limit 1";

                        $tempresult = @mysqli_query($GLOBALS['mysql_con'], $query);
                        if(mysqli_num_rows($tempresult) > 0) {
                            $is_variation = 1;
                        } else {
                            $is_variation = 0;
                        }

                        $query = "INSERT INTO shop_marketplace_variant_attributes (marketplace_type, category_code, productType, description, is_variation, timestamp_fetched) VALUES (
							1,
							'" . $category_code . "',
							'" . $productTypeValue . "',
							'" . $attributeName . "',
							" . $is_variation . ",
							" . time() . "
						)";
                        mysqli_query($GLOBALS['mysql_con'], $query);
                    }
                }

                unset($xsdpath);
                unset($XSDDOC);


//				// variant attributes fuer jede weitere kategorie eintragen. alle kategorien erben vom parent
//				$query = "select category_code from shop_marketplace_categories where marketplace_type = " . self::MARKETPLACE_TYPE . " and root_category_code = '" . $category_code . "' and category_code != '" . $category_code . "' ";
//				$tempresult = mysqli_query($GLOBALS['mysql_con'], $query);
//				if(mysqli_num_rows($tempresult) > 0) {
//					while ($childRow = mysqli_fetch_assoc($tempresult)) {
//						@mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_variant_themes where marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $childRow['category_code'] . "'");
//						@mysqli_query($GLOBALS['mysql_con'], "DELETE FROM shop_marketplace_variant_attributes where marketplace_type = " . self::MARKETPLACE_TYPE . " and category_code = '" . $childRow['category_code'] . "'");
//
//						$query = "INSERT INTO shop_marketplace_variant_attributes (marketplace_type, category_code, productType, description, is_variation, timestamp_fetched)
//							select marketplace_type, '" . $childRow['category_code'] . "', productType, description, is_variation, timestamp_fetched from shop_marketplace_variant_attributes where category_code = '" . $category_code . "'
//							and marketplace_type = " . self::MARKETPLACE_TYPE . "
//						";
//						mysqli_query($GLOBALS['mysql_con'], $query);
//
//						$query = "INSERT INTO shop_marketplace_variant_themes (marketplace_type, category_code, productType, description, timestamp_fetched)
//							select marketplace_type, '" . $childRow['category_code'] . "', productType, description, timestamp_fetched from shop_marketplace_variant_themes where category_code = '" . $category_code . "'
//							and marketplace_type = " . self::MARKETPLACE_TYPE . "
//						";
//						mysqli_query($GLOBALS['mysql_con'], $query);
//					}
//				}

                // Bei amazon erben alle kategorien vom parent
                if($update_variant_flag === true) {
                    $variant_available = 1;
                } else {
                    $variant_available = 0;
                }

                // variant defaults eintragen
                $query = "UPDATE shop_marketplace_categories SET variant_available = $variant_available where marketplace_type = " . self::MARKETPLACE_TYPE . " and (category_code = '" . $category_code . "' or root_category_code = '" . $category_code . "')";
                mysqli_query($GLOBALS['mysql_con'], $query);
            }
        }
    }

    /**
     * Hilfsfunktion fuer alle Submissions. Prueft ob submission beendet ist
     *
     * @param string $submission_id
     * @access public
     */
    protected function submission($submission_id) {
        $feed = new AmazonFeed();

        $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
        $request->setMerchant($feed->get_merchant_id());
        $request->setSubmittedFromDate($this->genTime("-20 days", 120));
        if($feed->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($feed->get_mws_auth_token());
        }

        $statusList = new MarketplaceWebService_Model_StatusList();
        //$statusList->withStatus('_SUBMITTED_');
        //$statusList->withStatus('_IN_PROGRESS_');
        $statusList->withStatus('_DONE_');
        $request->setFeedProcessingStatusList($statusList);

        $idList = new MarketplaceWebService_Model_IdList();
        $idList->withId($submission_id);
        $request->setFeedSubmissionIdList($idList);


        try {

            $response = $feed->getService()->getFeedSubmissionList($request);


            $this->setSubmissionReady(false);
            $this->setCallOk(true);

            if(	$response->isSetGetFeedSubmissionListResult()) {
                $feedSubmissionInfoList = $response->getGetFeedSubmissionListResult()->getFeedSubmissionInfoList();
                foreach($feedSubmissionInfoList as $feedSubmissionInfo) {
                    if(	$feedSubmissionInfo->getFeedSubmissionId() == $submission_id &&
                        $feedSubmissionInfo->getFeedProcessingStatus() == '_DONE_'
                    ) {
                        $this->setSubmissionReady(true);
                        $this->setSubmissionId( $feedSubmissionInfo->getFeedSubmissionId() );
                    }
                }
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    /**
     * Hilfsfunktion fuer alle Submissions. Wenn eine Submission beendet ist, schreibe result in ein file
     *
     * @param string $generated_id
     * @access public
     */
    protected function submission_ready($generated_id) {
        $feed = new AmazonFeed();

        $file = "amazon_" . $generated_id . "_result.xml";
        $filename = __DIR__."/../xml/" . $file;

        $handle = fopen($filename, 'w+');

        $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
        $request->setMerchant($feed->get_merchant_id());
        $request->setFeedSubmissionId($generated_id);
        $request->setFeedSubmissionResult($handle);
        if($feed->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($feed->get_mws_auth_token());
        }

        $this->setSubmissionResult(false);

        try {
            $feed->getService()->getFeedSubmissionResult($request);
            fclose($handle);

            $this->setCallOk(true);
            $this->setSubmissionResult($file); // Dateinamen uebergeben

        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    protected function report_submission($submission_id) {
        $report = new AmazonFeed();

        $idList = new MarketplaceWebService_Model_IdList();
        $idList->withId($submission_id);

        $request = new MarketplaceWebService_Model_GetReportRequestListRequest();
        $request->setMerchant($report->get_merchant_id());
        $request->setReportRequestIdList($idList);
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }

        try {
            $response = $report->getService()->getReportRequestList($request);

            $this->setSubmissionReady(false);
            $this->setCallOk(true);

            if(	$response->isSetGetReportRequestListResult()) {
                $ReportRequestInfoList = $response->getGetReportRequestListResult()->getReportRequestInfoList();
                foreach($ReportRequestInfoList as $reportRequestInfo) {
                    if(	$reportRequestInfo->getReportRequestId() == $submission_id &&
                        $reportRequestInfo->getReportProcessingStatus() == '_DONE_' &&
                        $reportRequestInfo->isSetGeneratedReportId()
                    ) {
                        $this->setSubmissionReady(true);
                        $this->setSubmissionId( $reportRequestInfo->getGeneratedReportId() );
                        return;
                    }

                    if(	$reportRequestInfo->getReportRequestId() == $submission_id &&
                        $reportRequestInfo->getReportProcessingStatus() == '_DONE_NO_DATA_'
                    ) {
                        $this->setCallOk(false);
                        $this->setErrorMessage("Report " . $submission_id . " zurueckgekommen als _DONE_NO_DATA_");
                        return;
                    }

                    if(	$reportRequestInfo->getReportRequestId() == $submission_id &&
                        $reportRequestInfo->getReportProcessingStatus() == '_CANCELLED_'
                    ) {
                        $this->setCallOk(false);
                        $this->setErrorMessage("Report " . $submission_id . " zurueckgekommen als _CANCELLED_, Grund nicht bekannt.");
                        return;
                    }
                }
            }
        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    protected function report_submission_ready($generated_id, $extension = "xml") {
        $report = new AmazonFeed();

        $file = "amazon_report_" . $generated_id . "." . $extension;
        $filename = __DIR__."/../xml/" . $file;

        $handle = fopen($filename, 'w+');

        $request = new MarketplaceWebService_Model_GetReportRequest();
        $request->setMerchant($report->get_merchant_id());
        $request->setReport($handle);
        $request->setReportId($generated_id);
        if($report->get_mws_auth_token() != "") {
            $request->setMWSAuthToken($report->get_mws_auth_token());
        }

        try {
            $response = $report->getService()->getReport($request);
            fclose($handle);
            $this->setCallOk(true);
            $this->setSubmissionResult($file); // Dateinamen uebergeben

        } catch (MarketplaceWebService_Exception $ex) {
            $this->setCallOk(false);
            $this->setErrorMessage("Catch bei amazon: " . $ex->getMessage());
        }
    }

    protected function getAmazonApi() {
        switch($this->getCurrentFunction()) {
            case 'update_products':
                $class = new AmazonFeed();
                $class->setFeedType("_POST_PRODUCT_DATA_");
                $class->setMessageType("Product");
                break;

            case 'update_price':
                $class = new AmazonFeed();
                $class->setFeedType("_POST_PRODUCT_PRICING_DATA_");
                $class->setMessageType("Price");
                break;

            case 'update_inventory':
                $class = new AmazonFeed();
                $class->setFeedType("_POST_INVENTORY_AVAILABILITY_DATA_");
                $class->setMessageType("Inventory");
                break;

            case 'update_images':
                $class = new AmazonFeed();
                $class->setFeedType("_POST_PRODUCT_IMAGE_DATA_");
                $class->setMessageType("ProductImage");
                break;

            case 'update_relationship':
                $class = new AmazonFeed();
                $class->setFeedType("_POST_PRODUCT_RELATIONSHIP_DATA_");
                $class->setMessageType("Relationship");
                break;
        }

        return $class;
    }

    protected function get_amazon_xsd($item_has_category) {
        $query = "SELECT * from shop_marketplace_amazon_xsd where category_code = '" . $item_has_category['root_category_code'] . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        $row = @mysqli_fetch_array($result);

        return $row;
    }

    protected function dispatch_mapping($inventory, $item) {
        if($inventory <= 0) {
            return 10;
        }

        if($inventory <= $item["insufficient_inventory_limit"]) {
            return 5;
        }

        return 1;
    }

    protected function genTime($time = false, $diff = 120){
        if (!$time){
            $time = time();
        } else {
            $time = strtotime($time);

        }
        return date('Y-m-d\TH:i:s',$time-$diff);

    }

    protected function handle_order($order) {
        // pruefen ob bestellung vorhanden
        $orderExists = false;
        $sqlCommand = "INSERT INTO";
        $query = "SELECT * from shop_sales_header 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['code'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			and marketplace_order = '" . $order['order-id'] . "'
			limit 1
		";
        $order_result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($order_result) == 1) {
            $order_array = @mysqli_fetch_array($order_result);
            $order_no = $order_array['order_no'];
            $sales_header_id = $order_array['id'];
            $orderExists = true;
            $sqlCommand = "UPDATE";
        }

        // neue interne bestellnummer ziehen, wenn es sich um eine neue bestellung handelt
        if($orderExists === false) {
            $order_no_query  = "SELECT order_no FROM shop_sales_header ORDER BY order_no DESC LIMIT 1";
            $order_no_result = @mysqli_query($GLOBALS['mysql_con'], $order_no_query);
            if (@mysqli_num_rows($order_no_result) == 1) {
                $order_no_array = @mysqli_fetch_array($order_no_result);
                $order_no       = $order_no_array["order_no"] + 1;
            } else {
                $order_no = 100000;
            }
        }

        // bestelldatum
        $order_date = strtotime($order['purchase-date']);
        $order_date = date('Y-m-d', $order_date);

        //shitfix argo
        $order['currency'] = "";

        $query = "$sqlCommand shop_sales_header
		  SET company = '" . $GLOBALS['shop']['company'] . "',
		  	  shop_code = '" . $GLOBALS['shop']['code'] . "',
		  	  language_code = '" . $GLOBALS['shop_language']['code'] . "',
		  	  order_no = $order_no,
			  marketplace_order = '" . $order['order-id'] . "',
		  	  shop_customer_id = 0,
		  	  customer_no = 0,
		  	  shop_user_id = 0,
		  	  user_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], substr($order['buyer-name'],0,30)) . "',
		  	  user_email = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['buyer-email']) . "',
		  	 
		  	  ship_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['recipient-name']) . "',
		  	  ship_to_name_2 = '',
		  	  ship_to_address = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-address-1']) . "',
		  	  ship_to_address_2 = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-address-2']) . "',
		  	  ship_to_post_code = '" . $order['ship-postal-code'] . "',
		  	  ship_to_city = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-city']) . "',
		  	  ship_to_country = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-country']) . "',
		  	  ship_to_contact = '',
		  	  ship_to_telephone_no = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-phone-number']) . "',

		      bill_to_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['buyer-name']) . "',
              
			  bill_to_address = '" . (!empty($order['bill-address-1']) ? mysqli_real_escape_string($GLOBALS['mysql_con'], $order['bill-address-1']) : mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-address-1'])) . "',
              
			  bill_to_address_2 = '" . (!empty($order['bill-address-2']) ? mysqli_real_escape_string($GLOBALS['mysql_con'], $order['bill-address-2']) : mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-address-2'])) . "',
              
			  bill_to_post_code = '" . (!empty($order['bill-postal-code']) ? $order['bill-postal-code'] : $order['ship-postal-code']) . "',
              
			  bill_to_city = '" . (!empty($order['bill-city']) ? mysqli_real_escape_string($GLOBALS['mysql_con'], $order['bill-city']) : mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-city'])) . "',
              
              bill_to_country = '" . (!empty($order['bill-country']) ? mysqli_real_escape_string($GLOBALS['mysql_con'], $order['bill-country']) : mysqli_real_escape_string($GLOBALS['mysql_con'], $order['ship-country'])) . "',
			  

		  	  bill_to_customer_no = '',
		  	  
		  	  order_date = '" . $order_date . "',

		  	  your_reference = '',
		  	  your_comment = '',
		  	  subtotal=0,
		  	  online_discount=0,
		  	  online_discount_amount=0,
			  invoice_discount=0,
			  invoice_discount_amount=0,
			  small_quantity_charge_amount=0,
			  total=0,
			  drop_shipment=0,
			  shipping_option_line_no=0,
			  shipping_cost=0,
			  payment_option_line_no = 0,
			  payment_cost = 0,
			  payment_transaction_id = '',
			  currency_code = '" . $order['currency'] . "',
			  coupon_amount = 0,
			  value_coupon = 0,
			  coupon_code = '',
			  shipping_coupon = 0,
			  user_salutation = '',
			  newsletter_registration = 0,
			  order_error = 0,
			  marketplace_order_status = '',
			  marketplace_sales_channel = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['sales-channel']) . "',
			  successful = 1,
			  update_insert = 1";


        if($orderExists === false) {
            @mysqli_query($GLOBALS['mysql_con'], $query);
            $sales_header_id = mysqli_insert_id($GLOBALS['mysql_con']);
        }

        // Bestellitems
        if($this->currentHandledOrders === null || $this->currentHandledOrders['orderId'] != $order['order-id']) {


            if ($this->currentHandledOrders !== null) {
                // versandmethode auslesen
                $query = "SELECT line_no from shop_shipping_option 
                    where company = '" . $GLOBALS['shop']['company'] . "'
                    and shop_code = '" . $GLOBALS['shop']['code'] . "'
                    and language_code = '" . $GLOBALS['shop_language']['code'] . "'
                    and country_code = '" . $this->currentHandledOrders['ship-country'] . "'
                    and shipping_cost = '" . $this->currentHandledOrders['shipping_cost'] . "'
                    limit 1
                ";



                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $shipping_line_no = 0;
                if(mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $shipping_line_no = $row['line_no'];
                    $query = "UPDATE shop_sales_header SET
                                shipping_option_line_no = '" . $shipping_line_no . "'
                            WHERE id = " . $this->currentHandledOrders['currentSalesHeaderId'];


                    @mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }

            $this->currentHandledOrders = array(
                'orderId' => $order['order-id'],
                'subtotal' => 0,
                'total' => 0,
                'shipping_cost' => 0,
                'currentSalesHeaderId' => $sales_header_id,
                'ship-country' => $order['ship-country'],
            );

        }

        $this->currentHandledOrders['subtotal'] += (float)$order['item-price'];
        $this->currentHandledOrders['shipping_cost'] += (float)$order['shipping-price'];
        $this->currentHandledOrders['total'] += ((float)$order['item-price'] + (float)$order['shipping-price']);

        $query = "UPDATE shop_sales_header SET
					subtotal = '" . $this->currentHandledOrders['subtotal'] . "',
					shipping_cost = '" . $this->currentHandledOrders['shipping_cost'] . "',
					total = '" . $this->currentHandledOrders['total'] . "'
				WHERE id = " . $sales_header_id;
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $query = "SELECT id from shop_item 
			where company = '" . $GLOBALS['shop']['company'] . "'
		  	and shop_code = '" . $GLOBALS['shop']['item_source'] . "'
		  	and language_code = '" . $GLOBALS['shop_language']['code'] . "'
			and item_no = '" . $order['sku'] . "'
			limit 1
		";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        $refItemNo = $order['sku'];

        //in referenznummern suchen
        if (mysqli_num_rows($result) != 1) {
            $query = "SELECT item_no FROM shop_item_cross_reference WHERE
                company = '" . $GLOBALS['shop']['company'] . "'
                AND reference_type = 0
                AND item_reference_no = '" . $order['sku'] . "'
                limit 1";

            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $row = mysqli_fetch_assoc($result);
            $query = "SELECT id from shop_item 
                where company = '" . $GLOBALS['shop']['company'] . "'
                and shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                and language_code = '" . $GLOBALS['shop_language']['code'] . "'
                and item_no = '" . $row['item_no'] . "'
                limit 1
            ";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $refItemNo = $row['item_no'];

        }
        $row = mysqli_fetch_assoc($result);
        $shop_item_id = $row['id'];

        $check_item_nos[] = $shop_item_id;

        // pruefen ob die bestellzeile schon existiert
        $lineExists = false;
        $lineSqlCommand = "INSERT INTO";
        $lineId = 0;
        $query = "SELECT id from shop_sales_line 
			where marketplace_line_id = '" . $order['order-item-id'] . "'
			and shop_sales_header_id = '" . $sales_header_id . "'
			limit 1
		";
        $order_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($order_line_result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $lineExists = true;
            $lineSqlCommand = "UPDATE";
            $lineId = $row['id'];
        }

        //Speichern der Bestellzeilen
        $query = "$lineSqlCommand shop_sales_line
				  SET company = '" . $GLOBALS['shop']['company'] . "',
					  shop_code = '" . $GLOBALS['shop']['code'] . "',
					  language_code = '" . $GLOBALS['shop_language']['code'] . "',
					  order_no = '" . $order_no . "',
					  shop_sales_header_id = '" . $sales_header_id . "',
					  shop_item_id = '" . $shop_item_id . "',
					  item_no = '" . $refItemNo . "',
					  variant_code = '',
					  description = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $order['product-name']) . "',
					  summary = '',
					  list_price = '" . number_format((float)$order['item-price'] / (int)$order['quantity-purchased'], 2, '.', '') . "',
					  unit_price = '" . number_format((float)$order['item-price'] / (int)$order['quantity-purchased'], 2, '.', '') . "',
					  quantity = '" . $order['quantity-purchased'] . "',
					  line_amount = '" . $order['item-price'] . "',
					  allow_invoice_disc = 1,
					  update_insert = 0,
					  marketplace_line_id = '" . $order['order-item-id'] . "'";

        if($lineExists === true) {
            $query .= " where id = $lineId ";
        }

        mysqli_query($GLOBALS['mysql_con'], $query);
    }

    protected function get_all_variation_attributes($all_variant_items, $all_shop_attributes) {
        $temp_result = array();

        $countVariants = 0;
        foreach($all_variant_items as $item_no => $variant_item) {
            $attributeresult = get_item_attributes($variant_item, $all_shop_attributes, AmazonMarketplace::ATTRIBUTE_TYPE);
            while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
                if(!isset($temp_result[$attributeRow['headline']])) {
                    $temp_result[$attributeRow['headline']] = 0;
                }

                $temp_result[$attributeRow['headline']]++;
            }

            $countVariants++;
        }

        $final_result = array();
        foreach($temp_result as $headline => $count) {
            // Wenn alle Varianten dieses Attribut zugeordnet haben, dann kann man damit amazon varianten bilden
            if($count == $countVariants) {
                $final_result[] = $headline;
            }
        }

        return $final_result;
    }

    protected function ul_to_array ($ul) {
        if (is_string($ul)) {
            // encode ampersand appropiately to avoid parsing warnings
            $ul=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $ul);
            if (!$ul = simplexml_load_string($ul)) {
                return FALSE;
            }
            return $this->ul_to_array($ul);
        } else if (is_object($ul)) {
            $output = array();
            foreach ($ul->li as $li) {
                $output[] = (isset($li->ul)) ? $this->ul_to_array($li->ul) : (string) $li;
            }
            return $output;
        } else return FALSE;
    }
}