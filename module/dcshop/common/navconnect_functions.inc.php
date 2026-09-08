<?php

// Gibt die Anzahl von Datensätzen einer Tabelle (POST "table") innerhalb eines Filters (POST Array "filter") zurück
function get_no_of_records() {
    if ($_POST["table"] <> '') {
        $tablename = $_POST["table"];
        $query     = "SELECT id FROM `" . $tablename . "`";
        if (is_array($_POST[filter])) {
            $first = TRUE;
            foreach ($_POST[filter] as $key => $value) {
                $query .= ($first) ? " WHERE " : " AND ";
                $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
                $query .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
                $first = FALSE;
            }
        }
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (!$result) {
            echo "Fehler in Funktion 'get_no_of_records' \n\nquery: " . $query . "\n\n";
        } else {
            echo @mysqli_num_rows($result);
        }
    } else {
        echo "Fehler in Funktion 'get_no_of_records'\nParameter 'table' muss angegeben werden";
    }
}

// Erzeugt oder Aktualisiert einen Datensatz (POST Array "primary") in einer Tabelle (POST "table") mit einem oder meheren Datenfeldern (POST Array "data")
function update() {
    if (($_POST["table"] <> '') & is_array($_POST["primary"])) {
        $tablename = $_POST["table"];
        // Prüfen ob der Datensatz bereits existiert
        $countquery = "SELECT * FROM `" . $tablename . "`";
        $first      = TRUE;
        foreach ($_POST['primary'] as $key => $value) {
            $countquery .= ($first) ? " WHERE " : " AND ";
            unset($locvalue);
            $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
            $countquery .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
            $first = FALSE;
        }
        $result = mysqli_query($GLOBALS['mysql_con'], $countquery);
        if (!$result) {
            echo "1 Fehler in Funktion 'update' \n\nquery: " . $countquery . "\n\n";
        } else {
            // Datensatz erzeugen wenn nicht vorhanden
            if (@mysqli_num_rows($result) == 0) {
                $query = "INSERT INTO `" . $tablename . "` (id";
                foreach ($_POST['primary'] as $key => $value) {

                    $query .= ", `" . $key . "`";
                }
                $query .= ") VALUES (NULL";
                foreach ($_POST['primary'] as $key => $value) {
                    unset($locvalue);
                    $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
                    $query .= ($value == 'NULL') ? ", NULL" : ", '" . $locvalue . "'";
                }
                $query .= ")";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                if (!$result) {
                    echo "2 Fehler in Funktion 'update' \n\nquery: " . $query . " \ncountquery: " . $countquery . " \n\n";
                }
            }
            // Aktualisierung des Datensatzes
            $result = mysqli_query($GLOBALS['mysql_con'], $countquery);
            if (@mysqli_num_rows($result) == 1) {
                if (is_array($_POST['data'])) {
                    $curr_rec = mysqli_fetch_array($result);
                    $query    = "UPDATE `" . $tablename . "` SET to_delete = 0";
                    foreach ($_POST['data'] as $key => $value) {
                        unset($locvalue);
                        $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
                        if ($_POST['function']['add'] == 1) {
                            $query .= ($value == 'NULL') ? ", `" . $key . "` = NULL" : ", `" . $key . "` = CONCAT(".$key.",'" . $locvalue . "')";
                        } else {
                            $query .= ($value == 'NULL') ? ", `" . $key . "` = NULL" : ", `" . $key . "` = '" . $locvalue . "'";
                        }
                    }
                    $query .= " WHERE id = " . $curr_rec["id"];
                    //echo "query start: ".$query." query end";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (!$result) {
                        echo "3 Fehler in Funktion 'update' \n\nquery: " . $query . "\ncountquery: " . $countquery . " \n\n";
                    }
                } else {
                    $curr_rec = mysqli_fetch_array($result);
                    $query    = "UPDATE `" . $tablename . "` SET to_delete = 0";
                    $query .= " WHERE id = " . $curr_rec["id"];
                    //echo "query start: ".$query." query end";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (!$result) {
                        echo "3 Fehler in Funktion 'update' \n\nquery: " . $query . "\ncountquery: " . $countquery . " \n\n";
                    }
                }
                echo "100";
            } else {
                echo "4 Fehler in Funktion 'update' \nPrimary nicht eindeutig\n\nquery: " . $countquery;
            }
        }
    } else {
        echo "5 Fehler in Funktion 'update'\nParameter 'table' und 'primary' muss angegeben werden";
    }
}

// Löscht einen oder mehrere Datensätze einer Tabelle (POST "table") innerhalb eines Filters (POST Array "filter") (2-Schritt über to_delete)
function delete( $instant_delete = FALSE ) {
    if ($_POST["table"] <> '') {
        $tablename = $_POST["table"];
        if ($instant_delete) {
            $query = "DELETE FROM `" . $tablename . "`";
        } else {
            $query = ($_POST["confirm_delete"]) ? "DELETE FROM `" . $tablename . "`" : "UPDATE " . $tablename . " SET to_delete = 1";
        }
        if (is_array($_POST[filter])) {
            $first = TRUE;
            foreach ($_POST[filter] as $key => $value) {
                $query .= ($first) ? " WHERE " : " AND ";
                unset($locvalue);
                $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
                $query .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
                $first = FALSE;
            }
            if (!$instant_delete) {
                $query .= ($_POST["confirm_delete"]) ? " AND to_delete = 1" : " AND to_delete = 0";
            }
        } else {
            if (!$instant_delete) {
                $query .= ($_POST["confirm_delete"]) ? " WHERE to_delete = 1" : " WHERE to_delete = 0";
            }
        }
        if (mysqli_query($GLOBALS['mysql_con'], $query)) {
            echo "100";
        } else {
            echo "Fehler in Funktion 'delete' \n\nquery: " . $query . " \n\n";
        }
    } else {
        echo "Fehler in Funktion 'delete'\nParameter 'table' muss angegeben werden";
    }
}

// Löscht einen oder mehrere Datensätze einer Tabelle (POST "table") innerhalb eines Filters (POST Array "filter") direkt (1-Schritt)
function instant_delete() {
    delete(TRUE);
}

// Gibt zurück ob ein Feld (Parameter "field") in einer Tabelle (Parameter "table") mit Primärschlüssel (POST Array "primary") Inhalt hat (Erfolg "100")
function check_field_content() {
    if (($_POST["table"] <> '') & ($_POST["field"] <> '') & is_array($_POST[primary])) {
        $tablename = $_POST["table"];
        $query     = "SELECT `" . $_POST["field"] . "` FROM `" . $tablename . "`";
        $first     = TRUE;
        foreach ($_POST[primary] as $key => $value) {
            $query .= ($first) ? " WHERE " : " AND ";
            unset($locvalue);
            $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
            $query .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
            $first = FALSE;
        }
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (!$result) {
            echo "Fehler in Funktion 'check_field_content' \n\nquery: " . $query . " \n\n";
        } else {
            if (@mysqli_num_rows($result) == 1) {
                $data = mysqli_fetch_assoc($result);
                if ($data[$_POST["field"]] <> '') {
                    echo "100";
                }
            } else {
                echo "Fehler in Funktion 'check_field_content' \nPrimary nicht eindeutig\n\nquery: " . $query;
            }
        }
    } else {
        echo "Fehler in Funktion 'check_field_content'\nParameter 'table', 'field' und 'primary' muss angegeben werden";
    }
}

// Gibt den Wert eines Feldes (Parameter "field") aus einer Tabelle (Parameter "table") mit Primärschlüssel (POST Array "primary") als max. 1000 Zeichen langen, für Dynamics NAV formatierten String zurück.
function get_field_content() {
    if (($_POST["table"] <> '') & ($_POST["field"] <> '') & is_array($_POST[primary])) {
        $tablename = $_POST["table"];
        $fieldname = $_POST["field"];
        $query     = "SELECT `" . $fieldname . "` FROM `" . $tablename . "`";
        $first     = TRUE;
        foreach ($_POST[primary] as $key => $value) {
            $query .= ($first) ? " WHERE " : " AND ";
            unset($locvalue);
            $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
            $query .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
            $first = FALSE;
        }
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (!$result) {
            echo "Fehler in Funktion 'get_field_content' \n\nquery: " . $query . " \n\n";
        } else {
            if (@mysqli_num_rows($result) == 1) {
                $data = mysqli_fetch_assoc($result);
                //$value = mysqli_real_escape_string($GLOBALS['mysql_con'],$data[$fieldname]);
                $fieldinfoquery  = "DESCRIBE `" . $tablename . "` `" . $fieldname . "`";
                $fieldinforesult = mysqli_query($GLOBALS['mysql_con'], $fieldinfoquery);
                $fieldinfo       = mysqli_fetch_assoc($fieldinforesult);
                $fieldtype       = (!strpos($fieldinfo["Type"], "(") ? $fieldinfo["Type"] : substr($fieldinfo["Type"], 0, strpos($fieldinfo["Type"], "(")));
                switch ($fieldtype) {
                    case "date":
                        $value = ($value <> '') ? date("d.m.Y", strtotime($value)) : "";
                        break;
                    case "time":
                        $value = ($value <> '') ? date("H:i:s", strtotime($value)) : "";
                        break;
                    case "datetime":
                        $value = ($value <> '') ? date("d.m.Y H:i:s", strtotime($value)) : "";
                        break;
                    case "decimal":
                        $value = number_format($value, 2, ',', '');
                        break;
                }
                //echo utf8_decode(substr($value,0,1000));
                echo $value;
            } else {
                echo "Fehler in Funktion 'get_field_content' \nPrimary nicht eindeutig\n\nquery: " . $query;
            }
        }
    } else {
        echo "Fehler in Funktion 'get_field_content'\nParameter 'table', 'field' und 'primary' muss angegeben werden";
    }
}



// Gibt alle Felder (Parameter "fields" leer) oder einzelne Felder (Parameter "fields" Komma-getrennte Werte) des ersten Datensatztes einer Tabelle (Parameter "table") innerhalb eines Filters (POST Array "filter") mit einem Trennzeichen (POST delimiter) getrennt aus.
function get_content() {
    if (($_POST["table"] <> '')) {
        $tablename = $_POST["table"];
        // Felder auslesen die abgefragt werden sollen
        if ($_POST["fields"] <> '') {
            $fields = explode(",", $_POST["fields"]);
            $first  = TRUE;
            foreach ($fields as $field) {
                if ($field == "combined_tax_rate" && $_POST["table"] == "shop_order_us_sales_tax") {
                    $fieldquery .= ($first) ? "(`" . $field  . "` * 100) AS " . $field . "" : ", (`" . $field . "` * 100) AS " . $field . "";
                } else {
                    $fieldquery .= ($first) ? "`" . $field . "`" : ", `" . $field . "`";
                }
                $first = FALSE;
            }
        } else {
            $fieldquery = "*";
        }
        // Daten mit Filter auslesen
        $query = "SELECT " . $fieldquery . " FROM `" . $tablename . "`";
        if (is_array($_POST[filter])) {
            $first = TRUE;
            foreach ($_POST[filter] as $key => $value) {
                $query .= ($first) ? " WHERE " : " AND ";
                unset($locvalue);
                $locvalue = preg_replace("#(?<!\\\\)(?:\\\\{2})*\K'#", "\'", $value);
                $query .= ($value == 'NULL') ? "`" . $key . "` IS NULL" : "`" . $key . "` = '" . $locvalue . "'";
                $first = FALSE;
            }
        }
        $query .= " LIMIT 1";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        // Daten mit Trennzeichen (POST delimiter) getrennt und für Dynamics NAV formatiert ausgeben
        if (!$result) {
            echo "Fehler in Funktion 'get_content' \n\nquery: " . $query . " \n\n";
        } else {
            if (@mysqli_num_rows($result) > 0) {
                $delimiter = ($_POST["delimiter"] <> '') ? $_POST["delimiter"] : ",";
                $data      = mysqli_fetch_assoc($result);
                $first     = TRUE;
                foreach ($data as $key => $value) {
                    $fieldinfoquery  = "DESCRIBE `" . $tablename . "` `" . $key . "`";
                    $fieldinforesult = mysqli_query($GLOBALS['mysql_con'], $fieldinfoquery);
                    $fieldinfo       = mysqli_fetch_assoc($fieldinforesult);
                    $fieldtype       = (!strpos($fieldinfo["Type"], "(") ? $fieldinfo["Type"] : substr($fieldinfo["Type"], 0, strpos($fieldinfo["Type"], "(")));
                    switch ($fieldtype) {
                        case "date":
                            if($value !== '0000-00-00') {
                                $value = ($value <> '') ? date("d.m.Y", strtotime($value)) : "";
                            }
                            break;
                        case "time":
                            $value = ($value <> '') ? date("H:i:s", strtotime($value)) : "";
                            break;
                        case "datetime":
                            $value = ($value <> '') ? date("d.m.Y H:i:s", strtotime($value)) : "";
                            break;
                        case "decimal":
                            $value = number_format($value, 2, ',', '');
                            break;
                    }
                    $returnvalue .= ($first) ? $value : $delimiter . $value;
                    $first = FALSE;
                }
                //echo utf8_decode(substr($returnvalue,0,1000));
                echo $returnvalue;
            } else {
                echo "0";
            }
        }
    } else {
        echo "Fehler in Funktion 'get_content'\nParameter 'table' muss angegeben werden";
    }
}

//MB 2015-05-07 Get SOLR update datetime
function get_solr_update_datetime() {
    $query = 'SELECT last_datetime_solr_updated FROM shop_setup LIMIT 1';
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $datetime = mysqli_result($result);
    $strlen_dt = strlen($datetime);
    if($strlen_dt > 0 && !($datetime === '0000-00-00 00:00:00')) {
        echo date('d.m.Y H:i:s', strtotime($datetime));
    } elseif($strlen_dt > 0) {
        echo "0";
    } else {
        echo "Fehler in Funktion 'get_solr_update_datetime'";
    }
}

//TIL: 26.08.13 Google Merchant Funktionen +++++
function google_data_feed_local( $_filename, $_content ) {
    // Datei local erzeugen
    if (!$_handle = fopen($_filename, 'w+')) {
        $_response = 'ERROR:Kann die Datei $filename nicht öffnen';
    }
    // Produkt XML in Datei schreiben
    if (is_writable($_filename)) {
        if (!fwrite($_handle, $_content)) {
            $_response = 'ERROR:Kann in die Datei $_filename nicht schreiben';
        }
        $_response = 'SUCCESS:Datei $_filename wurde erstellt';
        fclose($_handle);
    } else {
        $_response = 'ERROR:Die Datei $_filename ist nicht schreibbar';
    }
    return $_response;
}

function google_data_feed_upload( $_filename, $_content, $_config ) {
    // FTP-Zugansdaten
    $host     = $_config['FTP']['host'];
    $user     = $_config['FTP']['user'];
    $password = $_config['FTP']['password'];
    // erzeuge stream context
    $stream = stream_context_create(array('ftp' => array('overwrite' => TRUE)));
    // speichere die Datei
    $uri       = sprintf('ftp://%s:%s@%s/%s', $user, $password, $host, $_filename);
    $_response = file_put_contents($uri, $_content, 0, $stream);
    return $_response;
}

function google_data_feed_get_channel( $_config ) {
    // Channel Informationen festlegen
    $_response['channel']['title']       = $_config['channel']['title'];
    $_response['channel']['link']        = $_config['channel']['link'];
    $_response['channel']['description'] = $_config['channel']['description'];
    $_response['channel']['product_category'] = $_config['channel']['product_category'];
    return $_response;
}

function google_data_feed_get_items( $_config ) {
    // Sprache festlegen
    switch ($_config['country']) {
        case 'DE':
            $language_code = 'DEU';
            break;
        case 'GB':
            $language_code = 'ENU';
            break;
        default:
            $language_code = 'ENU';
    }
    // Produkt Array festlegen
    $query = "
	SELECT 
        svai.id,
        svai.company,
        svai.item_no,
        svai.description,
        svai.retail_price,
        svai.base_price,
        svai.inventory,
        svai.site_title,
        svai.parent_item_no,
        sif.filename,
        svai.summary,
        svai.g_product_category,
        (case when sat.description IS NULL THEN sao.description ELSE sat.description END) AS brand,
        sicr.item_reference_no AS ean
	FROM 
	  shop_item svai
	LEFT JOIN shop_item_file sif ON (sif.item_no = svai.item_no OR sif.item_no = svai.parent_item_no) AND sif.type = 0 AND sif.shop_code = '" . $_config['shop']['shop_code'] . "'
	LEFT JOIN shop_attribute_link sal ON sal.company = svai.company
		AND sal.shop_code = svai.shop_code
        AND sal.language_code = svai.language_code
        AND sal.no = svai.item_no AND sal.attribute_code = 'HERSTELLER' AND sal.value_option != ''
        LEFT JOIN
	shop_attribute_option sao ON sao.company = sal.company
		AND sao.attribute_code = sal.attribute_code
        AND sao.code = sal.value_option 
        LEFT join
	shop_attribute_translation sat ON sat.company = sao.company
		AND sat.type = 2
		AND sat.attribute_code = sao.attribute_code
        AND sat.attribute_link_no = sao.code
        AND sat.language_code = svai.language_code
	LEFT JOIN shop_item_cross_reference sicr ON sicr.item_no = svai.item_no AND sicr.reference_type = '3'
	WHERE svai.active = 1 AND svai.always_available <> 2 AND svai.company='" . $_config['shop']['company'] . "' AND svai.shop_code='" . $_config['shop']['shop_code'] . "' AND svai.language_code = '" . $language_code . "' AND svai.base_price > 0
    AND (
		SELECT count(*) FROM shop_item_has_category WHERE item_no = svai.item_no AND company = svai.company AND shop_code = '" . $_config['shop']['item_source'] . "' AND language_code = svai.language_code AND category_shop_code = '" . $_config['shop']['category_source'] . "' AND category_language_code = svai.language_code
        ) > 0
	GROUP BY svai.id";


    $_mysqli_result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($_mysqli_result) > 0) {
        while ($row = mysqli_fetch_array($_mysqli_result)) {


            $attribute_query = "SELECT 
                shop_attribute_link.*,
                shop_attribute.description AS 'headline',
                shop_attribute.map_to_attribute AS code,
                shop_attribute.code AS original_code,
                shop_attribute.data_type,
                shop_attribute.display_type,
                shop_attribute.navision_value
            FROM shop_attribute_link
		  INNER JOIN shop_attribute ON (shop_attribute.code = shop_attribute_link.attribute_code OR shop_attribute.map_to_attribute = shop_attribute_link.attribute_code)
		  WHERE shop_attribute_link.company = '" . $_config['shop']['company'] . "'
		  	AND shop_attribute.company = '" . $_config['shop']['company'] . "'
		  	AND shop_attribute_link.no = '" . $row['item_no'] . "'
			AND shop_attribute_link.shop_code='" . $_config['shop']['item_source'] . "'
            AND shop_attribute.attribute_type = 3
		  ORDER BY attribute_code;";


            $attributeresult = mysqli_query($GLOBALS['mysql_con'], $attribute_query);

            while ($attributeRow = mysqli_fetch_assoc($attributeresult)) {
                //$attribute_code = preg_replace('/\s+/', '_', $attributeRow['code']);
                //$attribute_code = preg_replace('/[^A-Za-z0-9\-]/', '', $attribute_code);
                $itemAttributes[$attributeRow['headline']] = get_item_attribute_value_merchant($row, $attributeRow, $_config);
            }

            $description_query  = "SELECT DISTINCT shop_item_description.*,shop_text_module.description AS 'shop_text_module_description',shop_text_module.content AS 'shop_text_module_content'
                          FROM shop_item_description
                          LEFT JOIN shop_text_module ON shop_item_description.shop_text_module_code = shop_text_module.code
						  INNER JOIN shop_item ON shop_item.item_no = shop_item_description.item_no
						  INNER JOIN shop_item AS parent_shop_item ON parent_shop_item.item_no = shop_item_description.item_no
						  WHERE shop_item_description.show_in_header = 1
							AND shop_item_description.company = '" . $_config['shop']['company'] . "'
							AND shop_item_description.shop_code = '" . $_config['shop']['item_source'] . "'
							AND shop_item_description.marketplace_only = 0
							AND (((shop_item_description.item_no = '" . $row['item_no'] . "' OR shop_item_description.item_no = '" . $row['parent_item_no'] . "')
								AND (shop_item_description.language_code = '" . $language_code . "'
									OR shop_item_description.all_language_codes = TRUE))
								)
						  ORDER BY shop_item_description.item_no, shop_item_description.line_no LIMIT 1";

            $description_result = @mysqli_query($GLOBALS['mysql_con'], $description_query);

            if (@mysqli_num_rows($description_result) > 0) {
                while ($description = @mysqli_fetch_array($description_result)) {
                    $google_description = $description["content"];
                    if($description["shop_text_module_code"] != '')
                    {
                        $google_description = $description["shop_text_module_content"];
                    }
                }
            } else {
                $google_description = $row['summary'];
            }

            preg_replace( "/\r|\n/", " ", $google_description);
            $google_description = "<![CDATA[".strip_tags(html_entity_decode($google_description))."]]>";

            // g:availability
            // 'Auf Lager' [in stock]
            // 'Bestellbar' [available for order]
            // 'Nicht auf Lager' [out of stock]
            // 'Vorbestellt' [preorder]
            if ($row['inventory'] <= 0) {
                $availability = 'preorder';
            } else {
                $availability = 'in stock';
            }
            #if(empty($row['brand'])){$row['brand'] = $_config['channel']['title'];}
            $item_link = get_google_canonical($row['id'],$_config);

            $description = $row['description'];
            if (!empty($row['site_title'])) {
                $description = $row['site_title'];
            }

            $_product['item']['title']       = htmlspecialchars($description);
            $_product['item']['link']        = htmlspecialchars($item_link);
            $_product['item']['description'] = $google_description;
            //$_product['item']['g:image_link'] = htmlspecialchars ($_config['channel']['link'].'/userdata/dcshop/images/normal/'.$row['filename']);
            $_product['item']['g:image_link'] = htmlspecialchars(rtrim($_config['channel']['link'],"/")."/" . ltrim($GLOBALS["shop_setup"]["image_config"][4]["path"],"/") . "/" . $row['filename']);
            $_product['item']['g:price']      = number_format($row['base_price'], 2, ',', '');
            #$_product['item']['g:shipping']['g:country'] = $_config['country'];
            #$_product['item']['g:shipping']['g:price'] = '4,60';
            $_product['item']['g:condition']               = 'new';
            $_product['item']['g:availability']            = $availability;
            $_product['item']['g:id']                      = $row['id'];
            $_product['item']['g:mpn']                     = $row['item_no'];
            $_product['item']['g:gtin']                    = $row['ean']; // EAN
            $_product['item']['g:brand']                   = htmlspecialchars($row['brand']); // Hersteller
            if ($row["g_product_category"] != "") {
                $_product['item']['g:google_product_category'] = htmlspecialchars($row['g_product_category']);
            } else {
                $_product['item']['g:google_product_category'] = htmlspecialchars($_config['channel']['product_category']);
            }

            if ($row["g_product_category"] != "") {
                $_product['item']['g:product_type']            = htmlspecialchars($row['g_product_category']);
            } else {
                $_product['item']['g:product_type']            = htmlspecialchars($_config['channel']['product_category']);
            }

            //Add Attributes
            foreach ($itemAttributes as $attribute_code => $attribute_value) {
                $_product['item']['attribute'][$attribute_code] = $attribute_value;
            }

            $_response[]                                   = $_product;
        }
    }
    return $_response;
}

function google_shopping_export( $_config ) {
    // Channel Werte holen
    $_xml_header = google_data_feed_get_channel($_config);
    // Produkte holen
    $_product_feed = google_data_feed_get_items($_config);

    //XML FEED
    if (is_array($_product_feed)) {
        $_xml = '<?xml version="1.0"?>';
        $_xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
        $_xml .= '<channel>';
        $_xml .= '<title>' . $_xml_header['channel']['title'] . '</title>';
        $_xml .= '<link>' . $_xml_header['channel']['link'] . '</link>';
        $_xml .= '<description>' . $_xml_header['channel']['description'] . '</description>';
        // Productlist Start
        foreach ($_product_feed as $key => $value) {
            $_xml .= '<item>' . '\n';
            $_xml .= '<title>' . $value['item']['title'] . '</title>';
            $_xml .= '<link>' . $value['item']['link'] . '</link>';
            $_xml .= '<description>' . $value['item']['description'] . '</description>';
            $_xml .= '<g:image_link>' . $value['item']['g:image_link'] . '</g:image_link>';
            $_xml .= '<g:price>' . $value['item']['g:price'] . '</g:price>';
            #$_xml .= '<g:shipping>';
            #	$_xml .= '<g:country>'.$value['item']['g:shipping']['g:country'].'</g:country>';
            #	$_xml .= '<g:price>'.$value['item']['g:shipping']['g:price'].'</g:price>';
            #$_xml .= '</g:shipping>';
            $_xml .= '<g:condition>' . $value['item']['g:condition'] . '</g:condition>';
            $_xml .= '<g:availability>' . $value['item']['g:availability'] . '</g:availability>';
            $_xml .= '<g:id>' . $value['item']['g:id'] . '</g:id>';
            $_xml .= '<g:mpn>' . $value['item']['g:mpn'] . '</g:mpn>';
            if (!empty($value['item']['g:gtin'])) {
                $_xml .= '<g:gtin>' . $value['item']['g:gtin'] . '</g:gtin>';
            }
            if (!empty($value['item']['g:brand'])) {
                $_xml .= '<g:brand>' . $value['item']['g:brand'] . '</g:brand>';
            }
            if (empty($value['item']['g:brand']) AND empty($value['item']['g:gtin'])) {
                $_xml .= '<g:identifier_exists>FALSE</g:identifier_exists>';
            }
            $_xml .= '<g:google_product_category>' . $value['item']['g:google_product_category'] . '</g:google_product_category>';
            $_xml .= '<g:product_type>' . $value['item']['g:product_type'] . '</g:product_type>';


            foreach ($value['item']['attribute'] as $attribute_code => $attribute_value) {
                $_xml .= '<g:' . strtolower($attribute_code) . '>' . $attribute_value . '</g:' . strtolower($attribute_code) . '>';
            }
            $_xml .= '</item>';

        }
        // Productlist End
        $_xml .= '</channel>';
        $_xml .= '</rss>';

        $_filename = 'data_feed_' . $_config['shop']['shop_code'] . "_" . $_config['country'] . '.xml';
        if (!file_exists("../../userdata/google_merchant/" . $_config['shop']['shop_code'] . "/")) {
            mkdir("../../userdata/google_merchant/" . $_config['shop']['shop_code'] . "/", 0777, true);
        }
        $_result   = google_data_feed_local("../../userdata/google_merchant/" . $_config['shop']['shop_code'] . "/" . $_filename, $_xml);

        if (stristr($_result, 'SUCCESS:')) {
            $_upload_size = google_data_feed_upload($_filename, $_xml, $_config);
            if ($_upload_size > 0) {
                $_result = "SUCCESS:Upload erfolgreich";
            } else {
                $_result = "ERROR:Upload fehlgeschlagen";
            }
            return $_result;
        } else {
            return $_result;
        }
    } else {
        $_result = "ERROR:Keine Produkte gefunden";
        return $_result;
    }
}

function get_item_attribute_value_merchant($item, $attribute, $_config) {

    $itemString = "('" . $item['item_no'] . "')";
    if ($attribute['navision_value'] != 0) {
        switch ($attribute['navision_value']) {
            case 1:
                $field = 'width';
                break;
            case 2:
                $field = 'lenght';
                break;
            case 3:
                $field = 'height';
                break;
            case 4:
                $field = 'volume';
                break;
            case 5:
                $field = 'weight';
                break;
            case 6:
                $field = 'retail_price';
                break;
            case 7:
                $field = 'base_price';
                break;
            case 8:
                $field = 'vendor_no';
                break;
            case 9:
                $field = 'inventory';
                break;
            default: $field= '';
                break;
        }
        if ($field <> ''){
            $query = "SELECT ".$field." 
					  FROM shop_item
					  WHERE company = '".$item['company']."'
					    AND shop_code = '".$item['shop_code']."'
					    AND language_code = '".$item['language_code']."'
					    AND item_no = '".$item['item_no']."'
					  LIMIT 1  ";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) == 1) {
                $item_attr_value = mysqli_fetch_assoc($result);
                return $item_attr_value[$field];
            }
        }

    } else {

        $option_values = get_option_values_merchant($attribute, $itemString, $_config);

        foreach($option_values as $key => $description) {
            if($description != "") {
                $returnDescription = $description;
            } else {
                $returnDescription = $key;
            }

            return $returnDescription;
        }
    }

    return "";
}

function get_option_values_merchant($attribute, $items, $_config)
{

    //Prüfen ob Optionen(mit Filter) oder nur Links vorhanden sind
    $options = FALSE;
    $optionsquery = "SELECT * 
					 FROM shop_attribute_option 
					 WHERE attribute_code = '" . $attribute['code'] . "'
					 	AND company = '" . $_config['shop']['company'] . "'";
    $optionsresult = mysqli_query($GLOBALS['mysql_con'], $optionsquery);
    if (@mysqli_num_rows($optionsresult) > 0) {
        $options = TRUE;
    }
    $optionsarray = array();
    switch ($attribute['data_type']) {
        case 0:
            $field = "value_option";
            break;
        case 1:
            $field = "value_integer";
            break;
        case 2:
            $field = "value_decimal";
            break;
        case 3:
            $field = "value_bool";
            break;
        case 4:
            $field = "value_text";
            break;
    }
    //Wenn keine Optionen vorhanden, die einzelnen Werte aus Links anzeigen
    if (!$options) {
        $query = "SELECT DISTINCT " . $field . "
				  FROM shop_attribute_link 
				  WHERE shop_attribute_link.attribute_code = '" . $attribute['code'] . "'
				  	AND shop_attribute_link.no IN " . $items . "
				
				  	AND shop_attribute_link.type=0 
				  	AND shop_attribute_link.company = '" . $_config['shop']['company'] . "'";
        if ($field == 'value_bool') {
            //echo "<!-- BOOL VALUE QUERY: ".$query." -->";
        }
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        while ($row = mysqli_fetch_assoc($result)) {
            if ($attribute['data_type'] == 2) {
                $description = str_replace(".", ",", $row[$field]);
                $description = str_replace(",00", "", $row[$field]);
                $description = str_replace(".00", "", $row[$field]);
            }
            if ($attribute['data_type'] == 3) {
                $description = ($row[$field] == 1) ? $GLOBALS["tc"]["yes"] : $GLOBALS["tc"]["no"];
            }
            $optionsarray[$row[$field]] = $description;
        }
    } //Wenn Optionen vorhanden, die Optionsbeschreibungen anzeigen, für die Werte vorhanden sind
    else {
        $optionvaluequery = "SELECT shop_attribute_option.* 
							 FROM shop_attribute_option
							 INNER JOIN shop_attribute_link ON (shop_attribute_link.attribute_code=shop_attribute_option.attribute_code AND shop_attribute_option.code = shop_attribute_link.value_option )
							 WHERE shop_attribute_option.attribute_code = '" . $attribute['code'] . "'
					 			AND shop_attribute_option.company = '" . $_config['shop']['company'] . "'
					 			AND shop_attribute_link.no IN " . $items . " 
					 			AND shop_attribute_link.type = 0
							  ORDER BY shop_attribute_option.sorting ASC";

        $optionsvalueresult = mysqli_query($GLOBALS['mysql_con'], $optionvaluequery);
        $optionvaluearray = array();
        if (@mysqli_num_rows($optionsvalueresult) > 0) {
            while ($optionvalue = mysqli_fetch_assoc($optionsvalueresult)) {
                $optionvaluearray[] = $optionvalue['code'];
            }
        }
        while ($option = mysqli_fetch_assoc($optionsresult)) {
            //Ohne Filter prüfen auf value_option in Links
            //if($option['filter'] == '')
            if (1 == 1) {
                if (in_array($option['code'], $optionvaluearray)) {
                    /*$linkquery = "SELECT *
								  FROM shop_attribute_link
								  WHERE attribute_code = '".$attribute['code']."'
								  	AND value_option='".$option['code']."'
								  	AND company='".$GLOBALS['shop']['company']."'
								  	AND no IN ".$items ."
								  	AND type = 0";
					$linkresult = mysqli_query($GLOBALS['mysql_con'],$linkquery);
					if(@mysqli_num_rows($linkresult) > 0)
					{*/
                    $optionsarray[$option['code']] = $option['description'];
                    //}
                }
            } //Mit Filter prüfen auf Wertebereich in Links
            else {
                $rangeparts = explode("..", trim($option['filter']));
                $min = $rangeparts[0];
                $max = $rangeparts[1];
                if ($attribute['data_type'] == 2) {
                    $min = str_replace(",", ".", $min);
                    $max = str_replace(",", ".", $max);
                }
                if (preg_match('/[\d\.]+/', trim($option['filter'])) !== 1) {
                    if (strlen($min) > 0) {
                        $minpart = "AND `" . $field . "` >= '" . $min . "'";
                    }
                    if (strlen($max) > 0) {
                        $maxpart = "AND `" . $field . "` <= '" . $max . "'";
                    }
                }
                $linkquery = "SELECT * 
							  FROM shop_attribute_link
							  WHERE attribute_code = '" . $attribute['code'] . "' 
							  " . $minpart . " 
							  " . $maxpart . " 
							  	AND company='" . $_config['shop']['company'] . "'
								AND shop_code='" . $_config['shop']['code'] . "'
				
							  	AND NO IN " . $items . " 
							  	AND type = 0";
                $linkresult = mysqli_query($GLOBALS['mysql_con'], $linkquery);
                if (@mysqli_num_rows($linkresult) > 0) {
                    $optionsarray[$option['code']] = $option['description'];
                }
            }
        }
    }
    return $optionsarray;
}

//TIL: 26.08.13 Google Merchant Funktionen ---

function reset_sales_lines() {
    if ($_POST["online_order_no"] <> '') {
        $query   = "UPDATE shop_sales_line SET update_insert=1 WHERE order_no='" . $_POST["online_order_no"] . "'";
        $success = mysqli_query($GLOBALS['mysql_con'], $query);
        if ($success) {
            echo "100";
        } else {
            echo "Fehler in Funktion 'reset_sales_lines'. Query nicht erfolgreich. \r\n Query: " . $query;
        }
    }
}

function reset_sales_lines_customize()
{
    if ($_POST["sales_line_id"] <> '') {
        $query = "UPDATE shop_sales_line_customize SET update_insert=1 WHERE sales_line_id='" . $_POST["sales_line_id"] . "'";
        $success = mysqli_query($GLOBALS['mysql_con'], $query);
        if ($success) {
            echo "100";
        } else {
            echo "Fehler in Funktion 'reset_sales_lines_customize'. Query nicht erfolgreich. Query: " . $query;
        }
    }
}

//RapidStart - TextModules
function copy_text_module_content() {
    if($_POST['company'] !== '' && $_POST['shop_language_code'] !== '' && $_POST['text_module_code'] !== '' && $_POST['field_name'] !== '' && $_POST['shop_name']) {
        $filePath = 'rapidstart_text_modules.php';
        if(file_exists($filePath)) {
            require($filePath);
        } else {
            echo "no file at: " . $filePath;
        }
        if(isset($rapidstart_text_modules[$_POST['shop_language_code']][$_POST['field_name']])) {
            $content = str_replace('%shopName%', $_POST['shop_name'], $rapidstart_text_modules[$_POST['shop_language_code']][$_POST['field_name']]);
            $query = "UPDATE shop_text_module
                    SET content = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$content) . "'
                    WHERE
                          company = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_POST['company']) . "'
                      AND code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'],$_POST['text_module_code']) . "'";
            $success = mysqli_query($GLOBALS['mysql_con'],$query);
            if($success) {
                echo "100";
            } else {
                $myfile = fopen("query.txt", "w") or die("Unable to open file!");
                fwrite($myfile, $query."\r\n");
                fclose($myfile);
                echo "Fehler in Funktion 'copy_text_module_content'. Query: $query";
            }
        } else {
            echo "no array index rapidstart_text_modules -> " . $_POST['shop_language_code'] . " -> " . $_POST['field_name'];
        }
    }
}

function force_solr_update() {
    /*ignore_user_abort(true);
    set_time_limit(0);
    ob_start();*/
    echo "100";
    /*header('Connection: close');
    headerFunctionBridge('Content-Length: '.ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();*/
    include_once(rtrim(dirname(dirname(dirname(__DIR__))),'/') . '/module/dcshop/itemsearch_cronjob.php');
}

function replace_temp_customer() {
    $custID = filter_var($_POST['customer_id'],FILTER_SANITIZE_NUMBER_INT);
    $tempCustNo = filter_var($_POST['temp_customer_no'],FILTER_SANITIZE_STRING);
    $newCustNo = filter_var($_POST['customer_no'],FILTER_SANITIZE_STRING);

    if(!isset($custID,$tempCustNo,$newCustNo) || !($custID > 0) || empty($tempCustNo) || empty($newCustNo)) {
        echo 'Fehler in Funktion \'replace_temp_customer\' Die Parameter `customer_id`, `temp_customer_no` und `customer_no` müssen gesetzt sein.';
    }
    try {

        $escapedNewCustNo = mysqli_real_escape_string($GLOBALS['mysql_con'], $newCustNo);
        $escapedTempCustNo = mysqli_real_escape_string($GLOBALS['mysql_con'], $tempCustNo);
    } catch(Exception $e) {
        echo "Exception with message: " . $e->getMessage();
    }

    $queryCustomer = '
        UPDATE shop_customer
        SET
          customer_no = \'' . $escapedNewCustNo . '\',
          bill_to_customer_no = \'' . $escapedNewCustNo . '\'
        WHERE id = ' . $custID . '
        AND customer_no = \'' . $escapedTempCustNo . '\'
    ';
    $queryUser = '
        UPDATE shop_user
        SET
            customer_no = \'' . $escapedNewCustNo . '\'
        WHERE
            customer_no = \'' . $escapedTempCustNo . '\'
    ';
    $querySalesHeader = '
        UPDATE shop_sales_header
        SET
            customer_no = \'' . $escapedNewCustNo . '\',
            bill_to_customer_no = \'' . $escapedNewCustNo . '\'
        WHERE
            customer_no = \'' . $escapedTempCustNo . '\'
    ';
    $querySubscriptionCustomerLink = '
        UPDATE shop_subscr_customer_link
        SET
            customer_no = \'' . $escapedNewCustNo . '\'
        WHERE
            customer_no = \'' . $escapedTempCustNo . '\'
    ';
    $queryCustPseudoPayData = '
        UPDATE shop_customer_pseudo_pay_data
        SET
            customer_no = \'' . $escapedNewCustNo . '\'
        WHERE
            customer_no = \'' . $escapedTempCustNo . '\'
    ';
    
    //SL Change customer_no in shop_shipment_address
    $queryShipmentAddress = '
        UPDATE shop_shipment_address
        SET
            customer_no = \'' . $escapedNewCustNo . '\'
        WHERE
            customer_no = \'' . $escapedTempCustNo . '\'
    ';
    
    //$transaction_started = mysqli_begin_transaction($GLOBALS['mysql_con']);
    //if($transaction_started) {
    try {
        $result1 = mysqli_query($GLOBALS['mysql_con'], $queryCustomer);
        if (!$result1) {
            echo "error in query: $queryCustomer";
        }
        $result2 = mysqli_query($GLOBALS['mysql_con'], $queryUser);
        if (!$result2) {
            echo "error in query: $queryUser";
        }
        $result3 = mysqli_query($GLOBALS['mysql_con'], $querySalesHeader);
        if (!$result3) {
            echo "error ihn query: $querySalesHeader";
        }
        $result4 = mysqli_query($GLOBALS['mysql_con'], $querySubscriptionCustomerLink);
        if (!$result4) {
            echo "error in query: $querySubscriptionCustomerLink";
        }
        $result5 = mysqli_query($GLOBALS['mysql_con'], $queryCustPseudoPayData);
        if (!$result5) {
            echo "error in query: " . $queryCustPseudoPayData;
        }
        //SL Change customer_no in shop_shipment_address
        $result6 = mysqli_query($GLOBALS['mysql_con'], $queryShipmentAddress);
        if (!$result6) {
            echo "error in query: " . $queryShipmentAddress;
        }
    } catch (Exception $e) {
        echo "Exception with msg: " . $e->getMessage();
    }
    //} else {
    //    echo 'Fehler in Funktion \'replace_temp_customer\' - MySQL-Transaktion kann nicht gestartet werden. Fehler:' . mysqli_error($GLOBALS['mysql_con']);

    //}
    //$transaction_committed = mysqli_commit($GLOBALS['mysql_con']);
    //if(!$transaction_committed) {
    //    echo 'Fehler in Funktion \'replace_temp_customer\' - MySQL-Transaktion kann nicht committed werden. Fehler:' . mysqli_error($GLOBALS['mysql_con']);

    //}
    echo "100";
}

function get_google_canonical($cardId,$_config) {

    //Artikelliste ohne Sortieroption
    if (isset($cardId)) {

        $card = $cardId;

        $canonical = "";
        IF ($cardId != '') {
            $itemquery  = "SELECT * FROM shop_item WHERE id='" . $card."'";
            $itemresult = @mysqli_query($GLOBALS['mysql_con'], $itemquery);
            $item       = @mysqli_fetch_assoc($itemresult);
            if (strlen($item["parent_item_no"]) > 0) {
                $parentitemquery  = "SELECT * FROM shop_item WHERE company='" . $item["company"] . "' AND shop_code='" . $item["shop_code"] . "' AND language_code='" . $item["language_code"] . "' AND item_no='" . $item["parent_item_no"] . "'";
                $parentitemresult = @mysqli_query($GLOBALS['mysql_con'], $parentitemquery);
                $parent_item      = @mysqli_fetch_assoc($parentitemresult);
            } else {
                $parent_item = $item;
            }
            IF ($parent_item['id'] == '' || $parent_item['id'] == $item['id']) {
                $canonical_card_id = $item['id'];
                IF ($item['main_category_line_no'] <> 0) {
                    $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = '" . $item['main_category_line_no'] . "'AND shop_code = '" . $_config['shop']['item_source'] . "' AND language_code = '" . $item["language_code"] . "'";
                } ELSE {
                    $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = (SELECT sihc.category_line_no FROM shop_item_has_category sihc RIGHT JOIN
    shop_category sc ON sc.company = sihc.company
        AND sc.shop_code = sihc.category_shop_code
        AND sc.language_code = sihc.category_language_code
        AND sc.line_no = sihc.category_line_no WHERE sihc.item_no = '" . $item['item_no'] . "' AND sihc.shop_code = '" . $_config['shop']['item_source'] . "' AND sihc.language_code = '" . $item['language_code'] . "' AND sihc.category_shop_code = '" . $_config['shop']['category_source'] . "' AND sihc.category_language_code = '" . $item['language_code'] . "' GROUP BY sihc.category_line_no ORDER BY sihc.category_line_no ASC LIMIT 1) AND shop_code = '" . $_config['shop']['category_source'] . "' AND language_code = '" . $parent_item['language_code'] . "'";

                }

                $cat_result   = @mysqli_query($GLOBALS['mysql_con'], $cat_query);
                $itemcategory = @mysqli_fetch_array($cat_result);
                $caturl       = array();
                $seek         = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                $replace      = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '+', '', '', '&quot;', '&quot;', '');
                $caturl[0]    = str_replace($seek, $replace, $itemcategory["code"]);
                $caturl[0]    = htmlspecialchars($caturl[0], ENT_QUOTES, "UTF-8");
                $caturl[0]    = $caturl[0] . "/";
                $i            = 1;
                WHILE ($itemcategory['plo'] > 0) {
                    $cat_query_rek = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = '" . $itemcategory['plo'] . "' AND shop_code = '" . $_config['shop']['item_source'] . "' AND language_code = '" . $item["language_code"] . "'";
                    $cat_res_rek   = @mysqli_query($GLOBALS['mysql_con'], $cat_query_rek);
                    $itemcategory  = @mysqli_fetch_array($cat_res_rek);
                    IF ($itemcategory['name'] != '') {
                        $seek       = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                        $replace    = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '+', '', '', '&quot;', '&quot;', '');
                        $caturl[$i] = str_replace($seek, $replace, $itemcategory["code"]);
                        $caturl[$i] = htmlspecialchars($caturl[$i], ENT_QUOTES, "UTF-8");
                        $caturl[$i] = $caturl[$i] . "/";
                    } ELSE {
                        $caturl[$i] = '';
                    }
                    $i++;
                }
                $catstring = "";
                DO {
                    $catstring .= $caturl[$i];
                    $i--;
                } WHILE ($i >= 0);
                $seek            = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                $replace         = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '+', '', '', '&quot;', '&quot;', '');
                $itemdescription = str_replace($seek, $replace, $item["description"]);
                $itemdescription = htmlspecialchars($itemdescription, ENT_QUOTES, "UTF-8");
                $itemdescription = str_replace("&Acirc;", "", $itemdescription);
                $itemdescription = str_replace("&acirc;", "", $itemdescription);
                $itemdescription = trim($itemdescription, "+");
                $base_string     = rtrim($_config['channel']['link'],"/");
                //$request_string  = $_config['site']['code'] . "/" . $_config['language']['code'] . "/" . $catstring . $itemdescription . "-p" . $canonical_card_id."/";
                //$request_string = customizeUrl() . "/". $itemdescription . "-p" . $canonical_card_id . "/";
                $GLOBALS['language']['code'] = $_config['language']['code'];
                $request_string = customizeUrl() . "/". $item["item_slug"] . "-p" . $canonical_card_id . "/";
                $request_string  = str_replace("//", "/", $request_string);
                $canonical       = $base_string . $request_string;
                $canonical       = str_replace(" ", "+", $canonical);
                $canonical       = str_replace("++", "+", $canonical);
            } else {
                $canonical_card_id = $parent_item["id"];
                IF ($parent_item['main_category_line_no'] <> 0) {
                    $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = '" . $parent_item['main_category_line_no'] . "' AND shop_code = '" . $_config['shop']['item_source'] . "' AND language_code = '" . $item["language_code"] . "'";
                } ELSE {
                    $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = (SELECT sihc.category_line_no FROM shop_item_has_category sihc RIGHT JOIN
    shop_category sc ON sc.company = sihc.company
        AND sc.shop_code = sihc.category_shop_code
        AND sc.language_code = sihc.category_language_code
        AND sc.line_no = sihc.category_line_no WHERE sihc.item_no = '" . $parent_item['item_no'] . "' AND sihc.shop_code = '" . $_config['shop']['item_source'] . "' AND sihc.language_code = '" . $parent_item['language_code'] . "' AND sihc.category_shop_code = '" . $_config['shop']['category_source'] . "' AND sihc.category_language_code = '" . $parent_item['language_code'] . "' GROUP BY sihc.category_line_no ORDER BY sihc.category_line_no ASC LIMIT 1) AND shop_code = '" . $_config['shop']['category_source'] . "' AND language_code = '" . $parent_item['language_code'] . "'";
                }
                $cat_result   = @mysqli_query($GLOBALS['mysql_con'], $cat_query);
                $itemcategory = @mysqli_fetch_array($cat_result);
                $caturl       = array();
                IF ($itemcategory['code'] != '') {
                    $seek      = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                    $replace   = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '+', '', '', '&quot;', '&quot;', '');
                    $caturl[0] = str_replace($seek, $replace, $itemcategory["code"]);
                    $caturl[0] = htmlspecialchars($caturl[0], ENT_QUOTES, "UTF-8");
                    $caturl[0] = $caturl[0] . "/";
                } ELSE {
                    $caturl[0] = '';
                }
                $i = 1;
                WHILE ($itemcategory['plo'] > 0) {
                    $cat_query_rek = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = '" . $itemcategory['plo'] . "' AND shop_code = '" . $_config['shop']['item_source'] . "' AND language_code = '" . $item["language_code"] . "'";
                    $cat_res_rek   = @mysqli_query($GLOBALS['mysql_con'], $cat_query_rek);
                    $itemcategory  = @mysqli_fetch_array($cat_res_rek);
                    IF ($itemcategory['name'] != '') {
                        $seek       = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                        $replace    = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '+', '', '', '&quot;', '&quot;', '');
                        $caturl[$i] = str_replace($seek, $replace, $itemcategory["code"]);
                        $caturl[$i] = htmlspecialchars($caturl[$i], ENT_QUOTES, "UTF-8");
                        $caturl[$i] = $caturl[$i] . "/";
                    } ELSE {
                        $caturl[$i] = '';
                    }
                    $i++;
                }
                $catstring = "";
                DO {
                    $catstring .= $caturl[$i];
                    $i--;
                } WHILE ($i >= 0);
                $seek            = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', '/', '\\', '%', ',', 'Ø', 'Ã', 'ø', 'ã', 'Õ', 'õ', '"', "''", "'");
                $replace         = array('ae', 'oe', 'ue', 'ss', '+', '+', '+', '', '', 'proz', '+', '&Oslash;', '&Atilde;', '&oslash;', '&atilde;', '&Otilde;', '&otilde;', '&quot;', '&quot;', '');
                $itemdescription = str_replace($seek, $replace, $item["description"]);
                $itemdescription = htmlspecialchars($itemdescription, ENT_QUOTES, "UTF-8");
                $itemdescription = str_replace("&Acirc;", "", $itemdescription);
                $itemdescription = str_replace("&acirc;", "", $itemdescription);
                $itemdescription = trim($itemdescription, "+");
                $base_string     = rtrim($_config['channel']['link'],"/");
                //$request_string  = $_config['site']['code'] . "/" . $_config['language']['code'] . "/" . $catstring . $itemdescription . "-p" . $canonical_card_id."/";
                //$request_string = customizeUrl() . "/" . $itemdescription . "-p" . $canonical_card_id . "/";
                $GLOBALS['language']['code'] = $_config['language']['code'];
                $request_string = customizeUrl() . "/". $item["item_slug"] . "-p" . $canonical_card_id . "/";
                $request_string  = str_replace("//", "/", $request_string);
                $canonical       = $base_string . $request_string;
                $canonical       = str_replace(" ", "+", $canonical);
                $canonical       = str_replace("++", "+", $canonical);
            }
        }
        $path = $canonical;

    }

    return $path;
}

function bulk_update()
{
    if (($_POST["table"] <> '')) {
        $tablename = $_POST["table"];
        if ($_POST["data"] <> '') {
            $query = "UPDATE " . $tablename . " SET ";
            $i = 0;
            foreach ($_POST['data'] as $fieldname => $newvalue) {
                if ($i == 0) {
                    $query .= $fieldname . " = '" . $newvalue . "'";
                    $i = 1;
                } else {
                    $query .= ", " . $fieldname . " = '" . $newvalue . "'";
                }
            }
            if ($_POST["filter"] <> '') {
                $j = 0;
                foreach ($_POST["filter"] as $filterfield => $filtervalue) {
                    if ($j == 0) {
                        $query .= "WHERE " . $filterfield . " = '" . $filtervalue . "'";
                        $j = 1;
                    } else {
                        $query .= " AND " . $filterfield . " = '" . $filtervalue . "'";
                    }
                }
            }
            if (mysqli_query($GLOBALS['mysql_con'], $query)) {
                echo "100";
            } else {
                echo "Fehler in Funktion 'bulk_update' \n\nquery: " . $query . " \n\n";
            }
        }
    }
}

function queueAttributesFetchPost()
{
    $company = null;
    $shopCode = null;
    $shopLanguageCode = null;
    $marketplaceType = null;
    $rootOnly = null;
    $categories = [];

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['shop_code'])) {
        $shopCode = filter_var($_POST['shop_code'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['language_code'])) {
        $shopLanguageCode = filter_var($_POST['language_code'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_NUMBER_INT);
    }
    if (isset($_POST['category_code'])) {
        $categoryCode = filter_var($_POST['category_code'], FILTER_SANITIZE_STRING);
    }

    if (isset($company) && isset($shopCode) && isset($shopLanguageCode) && isset($marketplaceType) && isset($categoryCode)) {
        $params = [
            'category_code' => $categoryCode,
        ];
        $queueEntryID = '';
        $queueEntryID = queueAttributesFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, $params);
        echo $queueEntryID;
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . ". Es müssen company, shop_code, language_code sowie parameter zu Kategorien gesetzt sein.";
    }
}

function queueAttributesFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, array $params)
{
    $company = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $shopCode = mysqli_real_escape_string($GLOBALS['mysql_con'], $shopCode);
    $shopLanguageCode = mysqli_real_escape_string($GLOBALS['mysql_con'], $shopLanguageCode);
    $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    recursiveArrayMySQLiEscape($params);
    $params = serialize($params);

    $query = "
      INSERT INTO
          shop_marketplace_queue
      SET
	      company 			= '$company',
		  shop_code 		= '$shopCode',
		  language_code		= '$shopLanguageCode',
		  marketplace_type  = '$marketplaceType',
		  operation 		= 'get_variant_attributes',
		  parameter 		= '" . $params . "',
		  timestamp 		= '" . time() . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    $queueEntryID = mysqli_insert_id($GLOBALS['mysql_con']);
    return $queueEntryID;
}

function checkMarketplaceAttributesFetchedPost()
{
    $company = null;
    $marketplaceType = null;
    $parentID = null;

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['category_code'])) {
        $categoryCode = filter_var($_POST['category_code'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['QueueEntryId'])) {
        $QueueEntryId = filter_var($_POST['QueueEntryId'], FILTER_SANITIZE_NUMBER_INT);
    }

    if (isset($categoryCode) && isset($company) && isset($marketplaceType) && isset($QueueEntryId)) {
        echo (checkMarketplaceAttributesFetched($QueueEntryId)) ? '1' : '0';
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . " - Notwendige Parameter nicht gefüllt.";
    }
}

function checkMarketplaceAttributesFetched($QueueEntryId)
{

    $query = '
        SELECT
            *
        FROM
           shop_marketplace_submissions_result
        WHERE
           queue_id = \''.$QueueEntryId.'\'
           AND
           processed = \'1\'
    ';

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $array = @mysqli_fetch_array($result);
    return (bool)$array[0];
}

function queueCategoryFetchPost()
{
    $company = null;
    $shopCode = null;
    $shopLanguageCode = null;
    $marketplaceType = null;
    $rootOnly = null;
    $categories = [];

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['shop_code'])) {
        $shopCode = filter_var($_POST['shop_code'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['language_code'])) {
        $shopLanguageCode = filter_var($_POST['language_code'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['rootOnly'])) {
        $rootOnly = (bool)$_POST['rootOnly'];
    }
    if ((is_array($_POST['categories'])) && (count($_POST['categories']) > 0)) {
        foreach ($_POST['categories'] as $catID) {
            $categories[] = filter_var($catID, FILTER_SANITIZE_STRING);
        }
    }
    if (isset($company) && isset($shopCode) && isset($shopLanguageCode) && !empty($categories)) {
        $delimiter = '-;-';
        $queueEntryIDs = '';
        foreach ($categories as $catID) {
            $params = [
                'parentID' => $catID,
            ];
            $queueEntryID = queueCategoryFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, $params);
            if (empty($queueEntryIDs)) {
                $queueEntryIDs .= $queueEntryID;
            } else {
                $queueEntryIDs .= $delimiter . $queueEntryID;
            }
        }
        echo $queueEntryIDs;
    } elseif (isset($company) && isset($shopCode) && isset($shopLanguageCode)) {
        $params = [
            'rootOnly' => true,
        ];
        $queueEntryID = queueCategoryFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, $params);
        echo $queueEntryID;
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . ". Es müssen company, shop_code, language_code sowie parameter zu Kategorien gesetzt sein.";
    }
}

function queueSubCategoryFetchPost()
{
    $company = null;
    $shopCode = null;
    $shopLanguageCode = null;
    $marketplaceType = null;
    $parentID = null;

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['shop_code'])) {
        $shopCode = filter_var($_POST['shop_code'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['language_code'])) {
        $shopLanguageCode = filter_var($_POST['language_code'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['parent_id'])) {
        $parentID = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['parent_id'], FILTER_SANITIZE_STRING));
    }
    if (isset($company) && isset($shopCode) && isset($shopLanguageCode) && !empty($marketplaceType) && !is_null($parentID)) {
        $entryID = queueCategoryFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, ['parentID' => $parentID]);
        echo $entryID;
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . ". Es müssen company, shop_code, language_code, marketplace_type und parent_id gesetzt sein.";
    }
}

function queueCategoryFetch($company, $shopCode, $shopLanguageCode, $marketplaceType, array $params)
{
    $company = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $shopCode = mysqli_real_escape_string($GLOBALS['mysql_con'], $shopCode);
    $shopLanguageCode = mysqli_real_escape_string($GLOBALS['mysql_con'], $shopLanguageCode);
    $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    recursiveArrayMySQLiEscape($params);
    $params = serialize($params);

    $query = "
      INSERT INTO
          shop_marketplace_queue
      SET
	      company 			= '$company',
		  shop_code 		= '$shopCode',
		  language_code		= '$shopLanguageCode',
		  marketplace_type  = '$marketplaceType',
		  operation 		= 'fetch_categories',
		  parameter 		= '" . $params . "',
		  timestamp 		= '" . time() . "'";
    @mysqli_query($GLOBALS['mysql_con'], $query);
    $queueEntryID = mysqli_insert_id($GLOBALS['mysql_con']);
    return $queueEntryID;
}

function getNoOfFetchedMarketplaceCategoriesPost()
{
    $company = null;
    $marketplaceType = null;
    $parentID = null;

    if (isset($_POST['company'])) {
        $company = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['company'], FILTER_SANITIZE_STRING));
    }
    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING));
    }
    if (isset($_POST['parent_id'])) {
        $parentID = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['parent_id'], FILTER_SANITIZE_STRING));
    }

    if (isset($company) && isset($marketplaceType) && isset($parentID)) {
        echo getNoOfFetchedSubCategories($company, $marketplaceType, $parentID);
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . " - Notwendige parameter nicht gefüllt.";
    }
}

function getNoOfFetchedSubCategories($company, $marketplaceType, $parentID)
{
    $company = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    $parentID = mysqli_real_escape_string($GLOBALS['mysql_con'], $parentID);

    $count = 0;
    $query = "
            SELECT
              COUNT(id)  AS 'counter'
            FROM
              shop_marketplace_categories
            WHERE
                  company = '$company'
              AND marketplace_type = '$marketplaceType'
		      AND parent_category_code = '$parentID'
        ";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $arr = @mysqli_fetch_assoc($result);
    if (isset($arr['counter']) && ($arr['counter'] > 0)) {
        $count = (int)$arr['counter'];
    }
    return $count;
}


function clearMarketplaceCategorySubTreePost()
{
    $company = null;
    $marketplaceType = null;
    $parent_id = null;

    if (isset($_POST['company'])) {
        $company = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['company'], FILTER_SANITIZE_STRING));
    }
    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING));
    }
    if (isset($_POST['root_id'])) {
        $parent_id = mysqli_real_escape_string($GLOBALS['mysql_con'], filter_var($_POST['parent_id'], FILTER_SANITIZE_STRING));
    }

    if (isset($company) && isset($marketplaceType) && isset($parent_id)) {
        $str = (deleteMarketplaceSubcategoriesRecursive($company, $marketplaceType, $parent_id)) ?
            '100'
            : "Fehler in Funktion " . __FUNCTION__ . " - Konnte unterkategorien nicht vollständig löschen.";
        echo $str;
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . " - Notwendige parameter nicht gefüllt.";
    }
}


function deleteMarketplaceSubcategoriesRecursive($company, $marketplaceType, $parentID)
{
    $children = getMarketplaceCategoryCodesChildren($company, $marketplaceType, $parentID);
    $companyEscaped = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $marketplaceTypeEscaped = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    $parentIDEscaped = mysqli_real_escape_string($GLOBALS['mysql_con'], $parentID);

    foreach ($children as $childCodeRow) {
        if (array_key_exists('category_code', $childCodeRow)) {
            $code = $childCodeRow['category_code'];
            if (!deleteMarketplaceSubcategoriesRecursive($company, $marketplaceType, $code)) {
                return false;
            }
        }
    }
    $deleteQuery = '
          DELETE FROM
                shop_marketplace_categories
          WHERE
                company = \'' . $companyEscaped . '\'
            AND marketplace_type = \'' . $marketplaceTypeEscaped . '\'
            AND parent_category_code = \'' . $parentIDEscaped . '\'
     ';
    @mysqli_query($GLOBALS['mysql_con'], $deleteQuery);
    return (!checkMarketplaceCategoryHasChildren($company, $marketplaceType, $parentID));
}

function getMarketplaceCategoryCodesChildren($company, $marketplaceType, $parentID)
{
    $company = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    $parentID = mysqli_real_escape_string($GLOBALS['mysql_con'], $parentID);

    $checkQuery = '
          SELECT
                category_code
          FROM
                shop_marketplace_categories
          WHERE
                company = \'' . $company . '\'
            AND marketplace_type = \'' . $marketplaceType . '\'
            AND parent_category_code = \'' . $parentID . '\'';
    $result = @mysqli_query($GLOBALS['mysql_con'], $checkQuery);
    $resultArr = @mysqli_fetch_array($result);
    if (is_array($resultArr)) {
        return $resultArr;
    } else {
        throw new ErrorException('Could not fetch list of sub-category codes.');
    }
}

function checkMarketplaceCategoryHasChildren($company, $marketplaceType, $parentID)
{
    $company = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $marketplaceType = mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType);
    $parentID = mysqli_real_escape_string($GLOBALS['mysql_con'], $parentID);

    $checkQuery = '
          SELECT
                COUNT(id) AS \'counter\'
          FROM
                shop_marketplace_categories
          WHERE
                company = \'' . $company . '\'
            AND marketplace_type = \'' . $marketplaceType . '\'
            AND parent_category_code = \'' . $parentID . '\'';
    $result = @mysqli_query($GLOBALS['mysql_con'], $checkQuery);
    $resultArr = @mysqli_fetch_assoc($result);
    return (!($resultArr['counter']) > 0);
}

function checkMarketplaceSubcategoriesFetchedPost()
{
    $company = null;
    $marketplaceType = null;
    $parentID = null;

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING);
    }
    if (isset($_POST['parent_id'])) {
        $parentID = filter_var($_POST['parent_id'], FILTER_SANITIZE_STRING);
    }

    if (isset($parentID) && isset($company) && isset($marketplaceType)) {
        echo (checkMarketplaceSubCategoriesFetched($marketplaceType, $parentID)) ? '1' : '0';
    } else {
        echo "Fehler in Funktion " . __FUNCTION__ . " - Notwendige Parameter nicht gefüllt.";
    }
}


function checkMarketplaceSubCategoriesFetched($marketplaceType, $parentID)
{
    if ($parentID === '0') {
        $query = '
            SELECT
                count(id)
            FROM
               shop_marketplace_categories
			WHERE
			   category_level = 1
			   AND
			   parent_category_code = \'\'
               AND
               marketplace_type = \'' . mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType) . '\'
        ';
    } else {
        $query = '
                SELECT
                    children_fetched
                FROM
                    shop_marketplace_categories
                WHERE
                        marketplace_type = \'' . mysqli_real_escape_string($GLOBALS['mysql_con'], $marketplaceType) . '\'
                    AND	category_code = \'' . mysqli_real_escape_string($GLOBALS['mysql_con'], $parentID) . '\'';
    }

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $array = @mysqli_fetch_array($result);
    return (bool)$array[0];
}

function checkMarketplaceHasSubmissionReadyEntryForQueueID($queueID)
{
    $query = '
        SELECT 1 FROM shop_marketplace_submissions_ready WHERE queue_id = ' . (int)$queueID . '
    ';
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $num_rows = mysqli_num_rows($result);
    return $num_rows > 0;
}


function recursiveArrayMySQLiEscape(array &$arr)
{
    foreach ($arr as &$val) {
        if (is_string($val)) {
            $val = mysqli_real_escape_string($GLOBALS['mysql_con'], $val);
        }
    }
    unset($val);
}

function update_marketplace_queue()
{
    $company = null;
    $shopCode = null;
    $languageCode = null;
    $marketplaceType = null;
    $operation = null;
    $parameterArray = null;

    if (isset($_POST['company'])) {
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['shop_code'])) {
        $shopCode = filter_var($_POST['shop_code'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['language_code'])) {
        $languageCode = filter_var($_POST['language_code'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['marketplace_type'])) {
        $marketplaceType = filter_var($_POST['marketplace_type'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['operation'])) {
        $operation = filter_var($_POST['operation'], FILTER_SANITIZE_STRING);
    }

    if (isset($_POST['parameter']) && is_array($_POST['parameter'])) {
        foreach ($_POST['parameter'] as $key => $value) {
            $parameterArray[filter_var($key, FILTER_SANITIZE_STRING)] = filter_var($value, FILTER_SANITIZE_STRING);
        }
    }

    $parameterSerialized = serialize($parameterArray);

    $query = "INSERT INTO shop_marketplace_queue (
        company, 
        shop_code, 
        language_code, 
        marketplace_type, 
        operation, 
        parameter, 
        timestamp
    ) VALUES ( 
        '" . $company . "', 
        '" . $shopCode . "', 
        '" . $languageCode . "', 
        " . $marketplaceType . ", 
        '" . $operation . "', 
        '" . $parameterSerialized . "', 
        " . time() . "
    );";


    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (!$result) {
        echo "Fehler in Funktion 'update_marketplace_queue' \n\nquery: " . $query . " \n\n";
    } else {
        echo "100";
    }
}

function send_marketplace_errors()
{
    if ($_POST["company"] <> '') {
        $query = "SELECT * FROM shop_shop WHERE company = '" . $_POST["company"] . "'";
        $result = mysqli_query($GLOBALS["mysql_con"], $query);
        if (mysqli_num_rows($result) > 0) {
            while ($shop = mysqli_fetch_array($result)) {
                $errorList = [];
                $query2 = '
                    SELECT * 
                      FROM shop_marketplace_errors 
                      WHERE marketplace_type = \'' . $shop["marketplace_type"] . '\'
                      AND update_insert = 1
                ';
                $result2 = mysqli_query($GLOBALS["mysql_con"], $query2);
                if (mysqli_num_rows($result2) > 0) {
                    $i = 0;
                    while ($error = mysqli_fetch_array($result2)) {
                        $errorList[$i]["id"] = $error["id"];
                        $errorList[$i]["table"] = "shop_marketplace_errors";
                        $errorList[$i]["operation"] = $error["operation"];
                        $errorList[$i]["errormsg"] = $error["errormsg"];
                        $i++;
                    }
                }

                $query2 = '
                    SELECT * 
                      FROM shop_item 
                      WHERE marketplace_error <> \'\'
                ';
                $result2 = mysqli_query($GLOBALS["mysql_con"], $query2);
                if (mysqli_num_rows($result2) > 0) {
                    while ($error = mysqli_fetch_array($result2)) {
                        $errorList[$i]["id"] = $error["id"];
                        $errorList[$i]["table"] = "shop_item";
                        $errorList[$i]["operation"] = $error["item_no"];
                        $errorList[$i]["errormsg"] = $error["marketplace_error"];
                        $i++;
                    }
                }

                $subject = "";
                $message = "";
                $from = "";
                $to = "";

                if (count($errorList) > 0) {
                    $subject = "Fehler bei der Marktplatz Aktualisierung";
                    $message = "Folgende Fehler sind aufgetreten:<br><br><ul>";
                    $from = $shop["email_sender"];
                    $to = $from;
                    $query_item = "UPDATE shop_item SET marketplace_error = '' WHERE id IN (";
                    $query = "UPDATE shop_marketplace_errors SET update_insert = 0 WHERE id IN (";
                    $first_item = true;
                    $first = true;
                    foreach ($errorList as $error) {
                        $message .= "<li>";
                        $message .= $error["operation"] . " - " . $error["errormsg"];
                        $message .= "</li>";
                        if ($error["table"] == "shop_item") {
                            if ($first_item) {
                                $query_item .= $error["id"];
                            } else {
                                $query_item .= "," . $error["id"];
                            }
                            $first_item = false;
                        } else {
                            if ($first) {
                                $query .= $error["id"];
                            } else {
                                $query .= "," . $error["id"];
                            }
                            $first = false;
                        }
                    }
                    $message = "</ul>";
                    $query_item .= ")";
                    $query .= ")";

                    if (mail_create($subject, $message, $from, $to, $from, $to, true, 0, '')) {
                        mysqli_query($GLOBALS["mysql_con"], $query);
                        mysqli_query($GLOBALS["mysql_con"], $query_item);
                    }
                }
                echo "100";
            }
        } else {
            echo "Fehler in Funktion send_marketplace_errors.";
        }
    } else {
        echo "Fehler in Funktion send_marketplace_errors.";
    }
}

function getLowestAmazonPrice()
{
    $delimiter = '-;-';
    $query = "
          SELECT 
            offer_listing_price, 
            offer_shipping_price, 
            (offer_listing_price + offer_shipping_price) AS sort 
          FROM 
            `shop_marketplace_amazon_prices` 
          WHERE 
            my_offer = 0 
          AND 
            sku = '".$_POST["sku"]."' 
          order by sort ASC LIMIT 1
     ";

    $result = mysqli_query($GLOBALS["mysql_con"], $query);
    $data = mysqli_fetch_assoc($result);
    $return = "";



    foreach ($data as $column) {
        if ($return == "") {
            $return = $column;
        } else {
            $return .= $delimiter.$column;
        }
    }

    echo $return;
}

?>