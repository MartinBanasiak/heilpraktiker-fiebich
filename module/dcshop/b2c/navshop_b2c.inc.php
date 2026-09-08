<?php
$baseDirectory = rtrim(dirname(__DIR__, 3), '/');

$basket = $IOCContainer->create('$CurrUserBasket');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';

$serverRequest = get_psr7_request_from_globals_single_instance();
$pdoWrapper = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
$customerAddressRepo = new \DynCom\dc\dcShop\CustomerAddress\CustomerAddressRepository($pdoWrapper, new \DynCom\dc\dcShop\CustomerAddress\CustomerAddressConfig(), new \DynCom\dc\common\classes\SelectionCriteriaHelper(), new \DynCom\dc\dcShop\CustomerAddress\CustomerAddressCollection(new \DynCom\dc\dcShop\CustomerAddress\CustomerAddressConfig(), new \DynCom\dc\common\classes\SelectionCriteriaHelper()), false);
$company = $GLOBALS['shop']['company'];
$customerNo = $GLOBALS['shop_customer']['customer_no'];

$locale = $GLOBALS['language']['locale_code'];
$textProvider = new \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider($locale);
$customerAddressController = new \DynCom\dc\dcShop\common\CustomerAddress\CustomerAddressController($customerAddressRepo,$textProvider);
$customerAddressModuleEnabled = (bool)getenv('MODULE_CUSTOMER_ADDRESS_ENABLED');
if ($customerAddressModuleEnabled && strpos($serverRequest->getRequestTarget(),'customerAddress') !== false) {
    $return = $customerAddressController->handleRequest($serverRequest,$company,$customerNo);
    echo $return;
}


$GLOBALS["image_config"] = $image_config;
//Prüfen ob Kreditorenfilter gesetzt und speichern
if (isset($_GET["vendor"])) {
    $GLOBALS["vendor_no"] = $_GET["vendor"];
    $vendor_sql_string = " AND (vendor_no = '" . $GLOBALS["vendor_no"] . "'') ";
} else {
    $GLOBALS["vendor_no"] = '';
    $vendor_sql_string = "";
}

//Zurück zum Suchergebnis nur einmal einblenden
if ($_GET["shop_category"] != 'search') {
    if ($_SESSION['search_counter'] == 0 || $_GET['var'] == 'true') {
        $_SESSION['search_counter'] = 1;
    } else {
        $_SESSION['search_counter'] = 2;
        unset($_SESSION['search']);
    }
}

if ($_GET["shop_category"] != 'order' && $_GET["shop_category"] != 'dc_order') {
    $_SESSION["dc_id"] = '';
}

// Suchanfrage auswerten
$_REQUEST["input_search"] = trim(filter_var($_REQUEST["input_search"], FILTER_SANITIZE_STRING));
if (($_GET["shop_category"] == 'search') && ($_REQUEST["input_search"] <> '' || $_SESSION['search'] != "") && ($_REQUEST["input_search"] != $GLOBALS["tc"]["search_term"]) && !isset($_GET["card"])) {
    $_SESSION['search_counter'] = 0;
    //Suchbegriff speichern für "zurück zum Suchergebnis"
    if ($_REQUEST['input_search'] != "") {
        $_SESSION['search'] = $_REQUEST['input_search'];
    }
    if ($_REQUEST['input_search'] == "" && isset($_SESSION['search'])) {
        $_REQUEST['input_search'] = $_SESSION['search'];
    }

    ?>
<? if ($GLOBALS["site"]["facebook_pixel_id"] <> "") { ?>
    <script>
        fbq('track', 'Search',{search_string: '<?= $_REQUEST["input_search"] ?>'});
    </script>
    <? } ?>
    <?

    echo "<h1 class='shop_site_headline'>" . $GLOBALS['tc']['search'] . ": ".htmlspecialchars($_REQUEST['input_search'], ENT_QUOTES, "UTF-8")."</h1>";

    //NEUE SUCHE
    $search_term = $_REQUEST["input_search"];

    if (!empty($search_term) && ($_REQUEST["input_search"] != $GLOBALS["tc"]["search_term"])) {
        $search_shop = (strlen($GLOBALS["shop"]["use_items_from_shop_code"]) > 0) ? $GLOBALS["shop"]["use_items_from_shop_code"] : $GLOBALS["shop"]["code"];

        $use_solr = $GLOBALS['shop']['extended_search'];
        $no_results = false;

        if ($use_solr) {
            $show_pagination = false;
            $startParam = 0;
            if ($_GET['page'] > 1) {
                $startParam = (($GLOBALS['shop_setup']['num_items_per_page']) * ($_GET['page'] - 1));
            }
            $rowsParam = $GLOBALS['shop_setup']['num_items_per_page'];

            $initTermCount = count(explode(' ', trim($_POST['input_search'])));
            $preparedInputString = normalize_search_string($_REQUEST["input_search"]);
            $preparedQueryString = $preparedInputString;
            //$preparedQueryString = '(' . $preparedInputString . ')^2';
            //$variants = get_all_whitespace_collapsed_variants($_REQUEST["input_search"]);
            $variantString = '';
            $variantCount = count($variants);
            $percent = round(((($initTermCount + $variantCount) / $initTermCount) * 100));
            if ($variantCount > 0) {
                foreach ($variants as $variant) {
                    $variantString .= '%20OR%20' . rawurlencode($variant);
                }
                $preparedQueryString = '' . $preparedQueryString . $variantString . '';
            }

            $queryfilterstring = '&fq=company:"' . urlencode($GLOBALS['shop']['company']) . '"' .
                '&ps=4' .
                '&tie=0.2' .
                '&fq=shop_code:' . urlencode($GLOBALS['shop']['code']) .
                '&fq=language_code:' . urlencode($GLOBALS['shop_language']['code']) .
                '&fq=active:true' .
                '&fq=validity_from:[*%20TO%20NOW]' .
                '&fq=validity_to:[NOW%20TO%20*]' .
                '&group.limit=100' .
                '&mm=' . rawurlencode('100%') .
                '&start=' . $startParam .
                '&rows=' . $rowsParam .
                '&group=true' .
                '&group.ngroups=true' .
                '&q.op=OR' .
                '&qf=item_no_intact^500 description^100 main_item_desc_combined^90 main_item_desc_combined_exact^90 main_item_desc_combined_edge^30 main_item_desc_combined_nowhitespace^100 main_item_desc_combined_nowhitespace_edge^80 main_item_desc_combined_ngram^10 did_you_mean^30 main_item_desc_combined_phon^2 main_item_desc_combined_phon_german^8 main_item_desc_combined_phon_english^8 long_descriptions^30 attribute_names^2 text_phon_german^5 text_phon_english^5' .
                '&pf=description_intact^150 description^100 main_item_desc_combined_exact^140 main_item_desc_combined^90 main_item_desc_combined_nowhitespace^100 did_you_mean^30 main_item_desc_combined_phon^2 main_item_desc_combined_phon_german^8 main_item_desc_combined_phon_english^8 long_descriptions^30 attribute_names^2 text_phon_german^5 text_phon_english^5';

            $finalQueryString = 'q=' . $preparedQueryString . '&wt=phps&rows=5&group.ngroups=true' . $queryfilterstring;
            $final_url = 'http://localhost:8983/solr/dcshop/itemquery';


            $cred = sprintf('Authorization: Basic %s',
                base64_encode('admin:ICG#2013x')
            );

            $opts = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => $cred
                )
            );
            $final_url = 'http://127.0.0.1:8983/solr/dcshop/itemquery';
            echo "<!-- SOLR: $final_url?$finalQueryString -->";


            $cred = sprintf('Authorization: Basic %s',
                base64_encode('admin:ICG#2013x')
            );

            $opts = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => $cred
                )
            );
            $ctx = stream_context_create($opts);

            echo "<!-- SOLR URL: $final_url -->";

            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $final_url);
            curl_setopt($process, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded", $cred));
            curl_setopt($process, CURLOPT_HEADER, 0);
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $finalQueryString);
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_return = curl_exec($process);
            curl_close($process);

            $serializedResult = $curl_return;
            $resultUnserialized = unserialize($serializedResult);

            $num_found = $resultUnserialized['grouped']['parent_item_no']['ngroups'];
            $result_items_pre = $resultUnserialized['grouped']['parent_item_no']['groups'];
            $currResultCount = count($result_items_pre);
            $iterator = 0;
            $default_img_path = $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $GLOBALS['shop_language']['item_placeholder_image'];
            $result_items = array();

            if ($num_found > 0) {

                if ($num_found > $rowsParam) {
                    $show_pagination = true;
                    $iteration_limit = $currResultCount;
                } else {
                    $iteration_limit = $num_found;
                }

                for ($iterator = 0; $iterator < $iteration_limit; $iterator++) {
                    $result_items[$iterator] = $result_items_pre[$iterator]['doclist']['docs'][0];
                    $currItem = $webshopItemRepository->findByID($result_items[$iterator]['id']);
                    $result_items[$iterator]['company'] = $currItem->company;
                    $result_items[$iterator]['shop_code'] = $currItem->shop_code;
                    $result_items[$iterator]['language_code'] = $currItem->language_code;
                    $result_items[$iterator]['item_no'] = $currItem->item_no;
                    $result_items[$iterator]['description'] = $result_items[$iterator]['variant_type_intact'];
                    $result_items[$iterator]['summary'] = $result_items[$iterator]['summary_intact'];
                    $result_items[$iterator]['manufacturer'] = $result_items[$iterator]['manufacturer_intact'];
                    $result_items[$iterator]['final_itemlink'] = '/' . customizeUrl() . '/shop' . $result_items[$iterator]['canonical_url'] . '-p' . $result_items[$iterator]['id']."/";
                    $tmp_path = '';
                    if (!empty($result_items[$iterator]['main_preview_image_filename'])) {
                        $tmp_path = $GLOBALS["shop_setup"]["image_config"][2]["path"] . '/' . $result_items[$iterator]['main_preview_image_filename'];
                    }
                    if (!file_exists(rtrim(dirname(dirname(dirname(__DIR__))), '/') . $tmp_path)) {
                        $result_items[$iterator]['final_imagelink'] = $default_img_path;
                        echo rtrim(dirname(dirname(dirname(__DIR__))), '/') . $tmp_path . '</br>';
                    } else {
                        $result_items[$iterator]['final_imagelink'] = $tmp_path;
                    }
                    $result_items[$iterator]['unit_price'] = $advancedPriceProvider->getItemCustomerPrice($currItem, '', 1, $currCustomer, $GLOBALS["shop_currency"]["code"]);
                    $result_items[$iterator]['cross_price'] = $advancedPriceProvider->getItemCrossPrice($currItem, '', $currCustomer, $GLOBALS["shop_currency"]["code"]);
                }

                echo "<div class=\"itembox\">";
                if ($show_pagination) {
                    print_pagination($num_found, '?shop_category=search', '');
                }

                echo "<div class=\"spacer_1\"></div>";
                echo "<div class=\"clearfloat\"></div>";

                echo "<div class=\"itemcard_list2\">";
                foreach ($result_items as $item) {
                    print_itemlist2_entry($item);
                }
                echo "</div>";

                echo "<div class=\"spacer_1\"></div>";
                echo "<div class=\"clearfloat\"></div>";

                if ($show_pagination) {
                    print_pagination($num_found, '?shop_category=search', '');
                }
                echo "</div>";

            } else {
                $no_results = true;
            }

        } else {
            $max_no = '';

            //Suche auf Referenznummern erweitern
            $query = "SELECT *
              FROM shop_item_cross_reference
              WHERE item_reference_no LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                AND (customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "' OR customer_no='')
                AND company ='" . $GLOBALS['shop']['company'] . "'
                ";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $add_query_num .= " OR shop_view_active_item.item_no LIKE '%" . $row['item_no'] . "%'";
                }
            }

            //Suche in Beschreibungstexten
            $query = "SELECT DISTINCT item_no
              FROM shop_item_description
              WHERE (content LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
              OR content LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], htmlentities($_REQUEST["input_search"])) . "%')
                AND company ='" . $GLOBALS['shop']['company'] . "'
                AND shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                AND (language_code = '" . $GLOBALS['shop_language']['code'] . "' OR all_language_codes=1)
                AND marketplace_only = 0
                ";
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            if (mysqli_num_rows($result) != 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $add_query_desc .= " OR shop_view_active_item.item_no ='" . $row['item_no'] . "'";
                }
            }

            $add_query = $add_query_num . $add_query_desc;

            //Suche nach Mutterartikeln von Varianten, welche den Suchkriterien entsprechen
            $variant_query ="";
            if($GLOBALS['shop']['variant_type'] != 2)
            {
                $query = "SELECT DISTINCT parent_item_no
				  FROM shop_view_active_item
				  WHERE shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
	    		  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
	    		  	AND shop_view_active_item.company ='".$GLOBALS['shop']['company']."'
				  	AND (shop_view_active_item.description LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
	    		  		OR shop_view_active_item.variant_type LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
	    		  		OR shop_view_active_item.item_no LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
	    		  		OR shop_view_active_item.summary LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
	    		  		OR shop_view_active_item.search_query LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%')
				  	AND shop_view_active_item.parent_item_no !=''";
                $result = mysqli_query($GLOBALS["mysql_con"], $query);
                if(mysqli_num_rows($result) > 0)
                {
                    if(mysqli_num_rows($result) == 1)
                    {
                        $row = mysqli_fetch_assoc($result);
                        $variant_query .= " OR (shop_view_active_item.item_no = '".$row['parent_item_no']."'";
                    }
                    else
                    {
                        $i=1;
                        $last = mysqli_num_rows($result);
                        while($row = mysqli_fetch_assoc($result))
                        {
                            if($i==1)
                            {
                                $variant_query .= " OR ((shop_view_active_item.item_no = '".$row['parent_item_no']."'";
                                $i++;
                            }
                            elseif($i == $last)
                            {
                                $variant_query .= " OR shop_view_active_item.item_no = '".$row['parent_item_no']."')";
                                $i++;
                            }
                            else
                            {
                                $variant_query .= " OR shop_view_active_item.item_no = '".$row['parent_item_no']."'";
                                $i++;
                            }
                        }
                    }
                    if($variant_query != "")
                    {
                        $variant_query .= " AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"]. "'
		 							AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
				  					AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "')";
                    }
                }
                $var_query = " AND parent_item_no = '' ";
            }
            else
            {
                $var_query = '';
            }

            $normal_query = "SELECT shop_view_active_item.*
          FROM shop_view_active_item
          LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
          WHERE  shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
            AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
            AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
            " . $var_query . "
            AND (shop_view_active_item.description LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                OR shop_view_active_item.variant_type LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                OR shop_view_active_item.item_no LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                OR shop_view_active_item.summary LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                OR shop_view_active_item.search_query LIKE '%" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_search"]) . "%'
                " . $add_query . " " . $variant_query . ")
          GROUP BY shop_view_active_item.item_no";

            $default_search_query = $normal_query;

            $query_result = mysqli_query($GLOBALS['mysql_con'], $default_search_query);
            $search_results = @mysqli_num_rows($query_result);
            if (!$search_results > 0) {
                $no_results = true;
            }
        }
    } else {
        $no_results = true;
        $use_solr = false;
        $search_results = 0;
    }



    //Ergebnisse oder Fehlerbox anzeigen
    if (!$no_results && !$use_solr) {
        echo "<div class=\"itembox\">";
        show_item_list_with_pages(2, $default_search_query, "", "", "", $_REQUEST["sort_by"], TRUE, TRUE, $_REQUEST["input_search"], 4);
        echo "</div>";
    } elseif ($no_results) {

        $spacer["%search_input%"] = $_REQUEST["input_search"];
        if ($GLOBALS["shop_language"]["text_search_results"] <> '') {
            $message = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["text_search_results"], $spacer);
            //echo "<br /><div class=\"errorbox\">" . $message . "</div>";
            echo "<div class='emptybox'>" . $message . "</div>";
            get_content('no-search-results',true);
        }
    }
    $special_content = TRUE;

    // Suche für Statistik speichern
    $query = "INSERT INTO shop_search_query (id,company,shop_code,search_query,no_of_results,search_datetime)
			  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS['shop']['code'] . "','" . strtoupper($_REQUEST["input_search"]) . "'," . $search_results . ",NOW())";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

$showBasket = true;
// Geschenkverpackungen
if (($_GET["action"] == 'gift_package')) {
    $showBasket = false;
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_gift_package.inc.php';
    $special_content = TRUE;
}
// greeting greeting_card
if (($_GET["action"] == 'greeting_card')) {
    $showBasket = false;
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_greeting_card.inc.php';
    $special_content = TRUE;
}
if (($_GET["action"] == 'greeting_card_text')) {
    $showBasket = false;
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_order_greeting_card_text.inc.php';
    $special_content = TRUE;
}


//Zwischenseite anzeigen
if (($_GET["shop_category"] == 'queue') && !isset($_GET["card"])  ) {
    //require __DIR__ . DIRECTORY_SEPARATOR . 'user_queue.inc.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';
    $special_content = TRUE;
}

// Warenkorb anzeigen
if (($_GET["shop_category"] == 'basket') && !isset($_GET["card"]) && $showBasket ) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_basket.inc.php';
    $special_content = TRUE;
    if ( $_GET["action"] =="shop_individualize_basket" ) {
        require __DIR__ . DIRECTORY_SEPARATOR . 'user_queue.inc.php';
    }
}

// Bestellung anzeigen
if (($_GET["shop_category"] == 'order') && !isset($_GET["card"])) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_order.inc.php';
    $special_content = TRUE;
}

// Favoriten anzeigen
if (($_GET["shop_category"] == 'favorites') && !isset($_GET["card"])) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_favorites.inc.php';
    $special_content = TRUE;
}

// Kundenkonto anzeigen
if (($_GET["shop_category"] == 'account') && !isset($_GET["card"])) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'shop_account.inc.php';
    $special_content = TRUE;
}

//rma (Reklamationsabwicklung) anzeigen
if (($_GET['shop_category'] == 'rma') && !isset($_GET['card'])) {

    include $baseDirectory . DIRECTORY_SEPARATOR . 'module/dcshop/rma/init.php';

    if (!isset($IOCContainer)) {
        $IOCContainer = unserialize($_SESSION['IOC'],\DynCom\dc\common\interfaces\IOCInterface::class);
    }

    $shopConfig = $IOCContainer->create('$CurrShopConfig');

    $rmaOrderHelper = $IOCContainer->create('DynCom\dc\dcShop\rma\classes\RMAOrderHelper');
    $rmaFrontController = $IOCContainer->create('DynCom\dc\dcShop\rma\classes\RMAFrontController');

    $rmaFrontController->handleRequest();


    $special_content = TRUE;

}


// Digitaler Gutscheinversand --------
if (($_GET["shop_category"] == 'dc_order') && !isset($_GET["card"])) {
    $category = $GLOBALS['category'];
    require __DIR__ . DIRECTORY_SEPARATOR . 'dc_order.inc.php';
    $special_content = TRUE;
}
// Digitaler Gutscheinversand ++++++++


//Artikelkarte oder Artikelliste anzeigen
if (!$special_content) {
    // Kategoriebaum anzeigen
    $category = $GLOBALS['category'];
    $is_user_specific_sort_type = FALSE;
    if ($category["show_all_items"] == 1 || $category["show_all_items"] == 2 || $category["show_all_items"] == 3) {
        $category_sort_order = get_shop_category_sort_type($category["sort_items"]);
        if ($category['user_sorting']) {
            $is_user_specific_sort_type = TRUE;
            if ($_REQUEST["sort_by"] == "") {
                $category_sort_order = get_shop_category_sort_type($category["sort_items"]);//$category_sort_order = get_shop_category_sort_type(3);
            } else {
                $category_sort_order = $_REQUEST["sort_by"];
            }
        }
    }

    //};
    //Anzeige des Kreditorenfilters
    if (($_GET["card"] == '') && ($category["show_all_items"]) && ($GLOBALS["shop"]["show_vendor_filter"] == 1)) {
        show_creditor_filter($category["line_no"]);
    }

    // Artikelkarte oder Artikelliste anzeigen
    if ($_GET["card"] <> '' && $_GET["action"] == "save") {
        $item = get_item_by_card_id($GLOBALS['shop']['company'], $GLOBALS['shop']['code'], $GLOBALS['shop_language']['code'], $_GET['card']);
        $error = FALSE;
        if (($_POST["input_captcha1"] <> "") | ((time() - $_POST["input_captcha2"]) < 10)) {
            $error = TRUE;
            echo "<div class='errorbox'><h3>Spam-Verdacht</h3></div>";
        }

        if ($_POST["input_comment_text"] != '' && $_POST["input_comment_header"] != '' && !$error) {
            $query = "INSERT INTO shop_item_comments (id, company, shop_code, language_code, item_id, item_no, rating, comment, name, city, header, email, creation_date,released,update_insert)
											  VALUES (NULL,'" . $GLOBALS["shop"]["company"] . "','" . $GLOBALS["shop"]["code"] . "','" . $GLOBALS["shop_language"]["code"] . "','" . $_GET["card"] . "','" . $item["item_no"] . "','" . $_POST["input_comment_rating"] . "', '" . $_POST["input_comment_text"] . "', '" . $_POST["input_comment_name"] . "',
													  '" . $_POST["input_comment_city"] . "','" . $_POST["input_comment_header"] . "','" . $_POST["input_comment_email"] . "',
													  NOW(),1,1)";
            if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
                echo "<div class=\"successbox\">Ihre Bemerkung wurde gespeichert.</div>";
            }
        }

        require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';

    } elseif ($_GET["card"] <> '') {
        require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';
    } else {
        //In category


        if (!empty(getenv('TRACKING_API_ROOT'))) {
            include_once $baseDirectory . '/module/tracking/tracking_functions.php';
            include_once $baseDirectory . '/module/tracking/TrackingEvent.php';
            include_once $baseDirectory . '/module/tracking/TrackingAPIService.php';

            $trackingAPIService = new \DynCom\dc\tracking\TrackingAPIService();
            //Set referrer data
            \DynCom\dc\tracking\tracking_set_referrer_info(\DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY, (int)$category['id']);


            //collect tracking data
            $event_uuid = \DynCom\dc\tracking\get_event_uuid_and_echo_div_once();
            \DynCom\dc\tracking\echo_tracking_data_div_once();
            $last_pageview_type = isset($GLOBALS['referrer_data']['LAST_PAGEVIEW_TYPE']) ? $GLOBALS['referrer_data']['LAST_PAGEVIEW_TYPE'] : '';
            $last_pageview_id = isset($GLOBALS['referrer_data']['LAST_PAGEVIEW_ID']) ? $GLOBALS['referrer_data']['LAST_PAGEVIEW_ID'] : '';

            $trackingData = [
                'unique_id' => $event_uuid,
                'current_category_id' => $category['id'],
                'current_visitor_id' => $GLOBALS['visitor']['id'],
                'current_user_id' => $GLOBALS['shop_user']['id'],
                'current_customer_id' => $GLOBALS['shop_customer']['id'],
                'visit_start_timestamp' => time(),
                'http_referrer' => $_SERVER['HTTP_REFERER'],
                'previous_pageview_type' => $last_pageview_type,
                'previous_pageview_id' => $last_pageview_id,
            ];

            $visitorID = isset($GLOBALS['visitor']['id']) ? (int)$GLOBALS['visitor']['id'] : null;
            $userID = isset($GLOBALS['shop_user']['id']) ? (int)$GLOBALS['shop_user']['id'] : null;
            $customerID = isset($GLOBALS['shop_customer']['id']) ? (int)$GLOBALS['shop_customer']['id'] : null;
            $itemID = null;
            $categoryID = isset($category['id']) ? (int)$category['id'] : null;

            $timestamp = (string)time();
            $event = new \DynCom\dc\tracking\TrackingEvent($event_uuid, session_id(), \DynCom\dc\tracking\TrackingEvent::EVENT_TYPE_PAGEVIEW_CATEGORY, $timestamp, $timestamp, $trackingData, $visitorID, $userID, $customerID, $itemID, $categoryID);
            $trackingAPIService->addTrackingEvent($event);
        }

        echo "<div class=\"category_info\">";
        // Kategoriebild-Erweiterung HT: Anzeige eines Hauptbildes pro Kategorie
        get_category_picture($category["id"]);
        get_promotion_description($category);
        $category_description = get_category_description($category["id"]);
        if(!$category_description) {
            echo "<h1 class=\"shop_site_headline category_headline\">" . $category['name'] . "</h1>";
        }
        // Ende
        echo "</div>";
        $startFilterToDisplayEnd = microtime(true);
        if ($GLOBALS["shop"]["show_filters"]) {
            if (!getenv('ELASTIC_BASE_URL')) {
                show_category_filters($category, $category_sort_order);
            }
        }
        if ($category["id"] <> '') {
            // Anzahl der Navision-Werte per call-by-reference übergeben für HAVING-Teil(wichtig für kombiniertes Filtern)
            /*$num_of_nav_values = 0;
            $filterquery = get_filterquery($num_of_nav_values);
            $havingquery = "";
            if (count($_SESSION['filters']) > 0) {
                $count = count($_SESSION['filters']) - $num_of_nav_values;
                if ($count > 0) {
                    $havingquery .= "HAVING COUNT(DISTINCT shop_attribute_link.id) = " . $count;
                }
            }*/

            //Abfragen ob zufällige Artikel angezeigt werden sollen.
            if ($category["show_random_items"] != 0) {

                $type = $category["show_random_items"];
                $max_random_no = $category["no_of_random_items"];

                $query = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                    emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							    AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							    AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                    $vendor_sql_string . "
							  GROUP BY shop_view_active_item.id
							  ORDER BY RAND()
							  LIMIT " . $max_random_no;
                //MB --- OOP ---

                $result = mysqli_query($GLOBALS["mysql_con"],$query);
                $numOfRandomItems = mysqli_num_rows($result);

                if ($numOfRandomItems > 0) {
                    //if ($category["show_campain_items"] == 0) {
                    echo "<div class=\"highlights_box\">";
                    echo "<div class=\"highlights_caption h1\">Highlights</div>";
                    //}
                    //if ($category["show_campain_items"] == 0) {
                    echo "<div class=\"random_item_list\">";
                    //}
                    show_item_list_from_query($query, $type, false);

                    //if ($category["show_campain_items"] == 0) {
                    echo "</div></div>";
                    //}
                }
                //$result = mysqli_query($GLOBALS['mysql_con'], $query);
                //show_item_list_from_query($result, $type, FALSE);
            }


            if ($category["list_type"] == "2") {
                $query = "SELECT shop_view_active_item.*
						  FROM shop_item_has_category
						  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
						  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                    emty_category_query($category) . ")" .
                    $vendor_sql_string . "
						  GROUP BY shop_view_active_item.id
						  ORDER BY shop_view_active_item.item_no";
                //$result = @mysqli_query($GLOBALS['mysql_con'], $query);
                //MB --- OOP ---
                show_item_list_from_query($query, 1, FALSE);
            } else {
                if ($category["show_sub_categorys"] == 1) {

                    //Erweiterung um Kategoriebilder für jede Unterkategorie
                    echo "<div class=\"categorybox\"><div class='row'>\n";
                    $cat_line_no = $category["line_no"];
                    $query = " SELECT *
						  FROM shop_category AS sc
						  WHERE sc.parent_line_no = " . $cat_line_no . "
							AND sc.company = '" . $category['company'] . "'
							AND sc.shop_code ='" . $category['shop_code'] . "'
							AND sc.language_code = '" . $category['language_code'] . "'
							AND sc.active=1
							ORDER BY sc.sorting ASC";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_category_list($result, $GLOBALS["shop_language"]);
                    echo "</div></div>\n";
                } elseif ($category["show_sub_categorys"] == 2) {
                    echo "<div class=\"categorybox categorybox_etsy\"><div class='row'>\n";
                    $cat_line_no = $category["line_no"];
                    $query = "SELECT *
						  FROM shop_category AS sc
						  WHERE sc.parent_line_no = " . $cat_line_no . "
							AND sc.company = '" . $category['company'] . "'
							AND sc.shop_code ='" . $category['shop_code'] . "'
							AND sc.language_code = '" . $category['language_code'] . "'
							AND sc.active=1
							ORDER BY sc.sorting ASC";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_category_list_etsy($result, $GLOBALS["shop_language"]);
                    echo "</div>
                        </div>\n";
                }

                if ($category["id"] != 0) {
                    $show_all_items = TRUE;
                } else {
                    $show_all_items = FALSE;
                }

                // Abfragen ob Aktionsartikel angezeigt werden sollen
                if ($category["show_campain_items"] != 0) {
                    $type = $category["show_campain_items"];

                    $max_campain_no = $category["no_of_campain_items"];
                    $query = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  INNER JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  INNER JOIN shop_category sc ON sc.line_no = shop_item_has_category.category_line_no
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							    AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                        $vendor_sql_string . "
							    AND sc.company = '" . $GLOBALS['shop']['company'] . "'
							 	AND sc.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							 	AND sc.language_code = '" . $GLOBALS['shop_language']['code'] . "'
							 	AND sc.promotion_active = 1
							 	AND (isnull(sc.promotion_validity_from) AND isnull(sc.promotion_validity_to)
							  		OR (isnull(sc.promotion_validity_from) AND (sc.promotion_validity_to >= curdate())
							  		OR (sc.promotion_validity_from <= curdate()) AND isnull(sc.promotion_validity_to))
							  		OR (sc.promotion_validity_from <= curdate()) AND (sc.promotion_validity_to >= curdate()))
							  ORDER BY RAND()
							  LIMIT " . $max_campain_no;

                    $result = mysqli_query($GLOBALS["mysql_con"],$query);
                    $numOfCampaignItems = mysqli_num_rows($result);

                    if ($numOfCampaignItems > 0) {
                        //if ($category["show_campain_items"] == 0) {
                        echo "<div class=\"campaign_item_list\">";
                        //MB --- OOP ---
                        show_item_list_from_query($query, $type, false);
                        echo("<div class='clearfloat'></div>");

                        //if ($category["show_campain_items"] == 0) {
                        echo "</div>";
                        //}
                    }

                    //$result         = mysqli_query($GLOBALS['mysql_con'], $query);
                    /*if (mysqli_num_rows($result) > 0) {
                        show_item_list_from_query($result, $type, FALSE);
                    }*/
                }


                //if ($category["show_campain_items"] != 0 || $category["show_random_items"] != 0) {
                // echo "</div>";
                //}
                //Abfragen ob alle Artikel angezeigt werden sollen
                if ($category["show_all_items"] != 0) {
                    $itemlistStartTime = microtime(true);

                    if ($category != "campain") {
                        $cat_id       = $category["id"];
                        $categoryPath = category_get_path($category) . "?";
                    } else {
                        $categoryPath = "?shop_category=campain&shop_campain=" . $_REQUEST['shop_campain'] . "&";
                    }

                    echo "<div class=\"itembox\">";

                    $noOfHits = 0;

                    $elasticBaseURL = getenv('ELASTIC_BASE_URL');
                    $envUseElastic = (bool)getenv('CATEGORY_USE_ELASTICSEARCH');
                    $use_elasticsearch = !empty($elasticBaseURL) && ($envUseElastic !== false);
                    if ($use_elasticsearch) {

                        /**
                         * @var $pdoWrapper \DynCom\dc\common\classes\PDOQueryWrapper
                         */
                        $pdoWrapper = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
                        $pdo = $pdoWrapper->getConnectionObject();

                        $elasticUser = getenv('ELASTIC_USER');
                        $elasticPass = getenv('ELASTIC_PASS');
                        $elasticBaseURL = getenv('ELASTIC_BASE_URL');
                        $elasticPort = getenv('ELASTIC_PORT');
                        $elasticIndexName = getenv('ELASTIC_INDEX');
                        $elasticItemType = getenv('ELASTIC_ITEM_TYPE');

                        $hosts = [
                            [
                                'host' => $elasticBaseURL,
                                'port' => $elasticPort,
                                'user' => $elasticUser,
                                'pass' => $elasticPass
                            ]
                        ];


                        $elasticClient = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

                        $cat_child_line_nos = [];
                        get_category_child_line_nos($pdo, $GLOBALS['shop']['company'], $GLOBALS['shop']['category_source'], $GLOBALS['shop_language']['code'], (int)$category['line_no'], $cat_child_line_nos);
                        $cat_line_nos = $cat_child_line_nos;
                        array_unshift($cat_line_nos,(int)$category['line_no']);


                        $page = (int)($_GET['page'] ?? 1);
                        $from  = ($page - 1) * $GLOBALS['shop_setup']['num_items_per_page'];
                        $size = $GLOBALS['shop_setup']['num_items_per_page'];

                        $category_filters = get_category_filters($pdo,$GLOBALS['shop']['company'],$GLOBALS['shop']['code'],$GLOBALS['shop']['category_source'],$GLOBALS['shop_language']['code'],(int)$category['line_no']);
                        evaluate_url_filters($category_filters);

                        $attribute_filters = get_active_filters();


                        $elasticQueryBodyArr = create_elastic_query($GLOBALS['shop']['company'], $GLOBALS['shop']['item_source'], $GLOBALS['shop']['category_source'], $GLOBALS['shop_language']['code'], $cat_line_nos, $attribute_filters,$category_filters , $category_sort_order, $from, $size);
                        //file_put_contents('elastic_log.txt','QUERY-Body: [' . var_export($elasticQueryBodyArr,1) . ']' . PHP_EOL . PHP_EOL,FILE_APPEND);
                        //echo "QUERY: " . var_export($elasticQueryBodyArr,1);
                        $params = [
                            'index' => $elasticIndexName,
                            'type' => $elasticItemType,
                            'body' => $elasticQueryBodyArr,
                        ];
                        $startMS = microtime(true);
                        $elasticResults = $elasticClient->search($params);
                        $duration = microtime(true) - $startMS;
                        //echo "QUERY TOOK [$duration] seconds. Internal elasticsearch duration [{$elasticResults['took']}] milliseconds";
                        $noOfHits = $elasticResults['hits']['total'] ?? 0;
                        if (array_key_exists('aggregations',$elasticResults)) {
                            $facetAttrs = [];
                            $facetHTML = '';
                            foreach ($elasticResults['aggregations'] as $attr_code => $aggArr) {
                                $attribute = get_attribute($pdo,$GLOBALS['shop']['company'],$attr_code);
                                $facetAttrs[$attr_code] = $attribute;
                                $attr_desc = get_attribute_description($pdo,$GLOBALS['shop']['company'],$attr_code,$GLOBALS['shop_language']['code']);
                                $optArr = [];
                                foreach ($aggArr['buckets'] as $bucketArr) {
                                    $attr_opt_code = $bucketArr['key'];
                                    $attr_opt_doc_no = $bucketArr['doc_count'];
                                    $attr_opt_desc = get_attribute_option_description($pdo,$GLOBALS['shop']['company'],$attr_code,$attr_opt_code,$GLOBALS['shop_language']['code']);
                                    $optArr[] = ['code' => $attr_opt_code,'desc' => $attr_opt_desc,'doc_num' => $attr_opt_doc_no];
                                }
                                $facetHTML .= create_facet_html($attr_code,$attr_desc,(int)$attribute['display_type'],(int)$attribute['data_type'],$optArr,$category_sort_order);
                            }
                            evaluate_url_filters($facetAttrs);
                            //var_dump($elasticResults['aggregations']);
                            echo "<div class='filterbox'>";
                            echo $facetHTML;
                            echo "</div>";
                        }

                        $milliseconds = $elasticResults['took'];
                        $foundItemData = [];
                        $items = [];
                        if ($noOfHits) {
                            //echo "HITS: $noOfHits";
                            echo "<div class='itembox_header row'>";

                            $hitItems = $elasticResults['hits']['hits'] ?? [];
                            foreach ($hitItems as $hitItemArr) {
                                $id = $hitItemArr['_id'] ?? 0;

                                $item = get_item_by_id($id);
                                $items[] = $item;
                                $foundItemData[] = $id;
                                /*
                                $itemNo = $hitItemArr['_source']['item_no'] ?? '';
                                $parentItemNo = $hitItemArr['_source']['parent_item_no'] ?? null;
                                $itemNoToIndex = $parentItemNo ?? $itemNo;
                                if (!$parentItemNo || !array_key_exists($parentItemNo, $foundItemData)) {
                                    $foundItemData[$itemNoToIndex] = $id;
                                }
                                */
                            }
                            if ($show_all_items) {
                                echo "<div class='sort_by col-xs-12 col-sm-6 col-md-4 col-lg-3'>";
                                echo "<form name=\"item_order\" method=\"post\">";
                                create_sort_select("", "sort_by", $category_sort_order);
                                echo "</form>";
                                echo "</div>";
                            }
                            echo "<div class='col-xs-12 col-sm-6 col-md-4 col-md-offset-4 col-lg-3 col-lg-offset-6'>";
                            get_page_switch($noOfHits,$page,$categoryPath,$category_sort_order);
                            echo "</div>";
                            echo "</div>";
                            show_item_list($items,$category["show_all_items"]);
                            get_page_switch($noOfHits,$page,$categoryPath,$category_sort_order);
                            $itemlistDuration = microtime(true) - $startFilterToDisplayEnd;
                            //echo "ELASTICSEARCH-DURATION: [$duration] - ITEMLIST DURATION: [$itemlistDuration] seconds.";

                        }
                        $itemIDSetString = implode(',', $foundItemData);
                        $item_list_stmt = 'shop_view_active_item.id IN (' . $itemIDSetString . ')';
                    }




                    if (!$use_elasticsearch || !$noOfHits) {

                        //echo "USING OLD STANDARD";

                        /*$attribute_link_query_join = '';
                        $attribute_link_query_where = '';
                        if($filterquery != '' || $havingquery != ''){
                            $attribute_link_query_join ='LEFT JOIN shop_attribute_link
                                    ON (
                                        shop_attribute_link.no = shop_view_active_item.item_no
                                      AND
                                        shop_attribute_link.shop_code = shop_view_active_item.shop_code
                                    )';
                            $attribute_link_query_where ="AND ((shop_attribute_link.type = 0
                                        AND shop_attribute_link.company = '" . $GLOBALS['shop']['company'] . "')
                                    OR( shop_attribute_link.company IS NULL
                                        AND shop_attribute_link.type IS NULL))
                                  ";
                        }*/


                        $attribute_link_query_where = '';
                        $num_of_nav_values = 0;
                        $filterquery = get_filterquery($num_of_nav_values);

                        $havingquery = "";
                        if (count($_SESSION['filters']) > 0) {
                            $count = count($_SESSION['filters']) - $num_of_nav_values;
                            if ($count > 0) {
                                $havingquery .= "GROUP BY item_no HAVING COUNT(item_no) = " . $count;
                            }
                        }

                        if($filterquery != ''){
                            if ($num_of_nav_values == 0) {
                                $attribute_link_query_where = "
                                    LEFT JOIN shop_item_attribute_links_ext sial ON (
                                            sial.company = '" . $GLOBALS['shop']['company'] . "'
                                        AND	sial.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                        AND sial.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                        AND sial.no = v_i.item_no
                                    )
                                    WHERE
                                            (" . $filterquery . ")
                                        AND sial.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                        AND sial.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                        AND (
                                                (
                                                    sial.type = 0
                                                AND sial.company = '" . $GLOBALS['shop']['company'] . "'
                                                )
                                            OR (
                                                    sial.company IS NULL
                                                AND sial.type IS NULL
                                                )
                                        ) " . $havingquery;
                            } else {
                                $attribute_link_query_where = $filterquery;
                            }
                        }


                        $query = "
                            select distinct shop_view_active_item.* from shop_view_active_item 
                            LEFT JOIN
                              shop_item_has_category 
                              ON 
                                    shop_item_has_category.company = shop_view_active_item.company
                                AND shop_item_has_category.shop_code = shop_view_active_item.shop_code
                                AND shop_item_has_category.language_code = shop_view_active_item.language_code
                                AND shop_item_has_category.item_no = shop_view_active_item.item_no
                              where shop_view_active_item.item_no in (
                            SELECT 
                                (CASE
                                    WHEN parent_item_no <> '' THEN parent_item_no
                                    ELSE item_no
                                END) AS item_no
                            FROM shop_view_active_item 
                            WHERE 
                                    company = '" . $GLOBALS["shop"]["company"] . "'
                                AND shop_code ='" . $GLOBALS['shop']['item_source'] . "'
                                AND language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                AND	item_no IN (
                                    SELECT DISTINCT item_no    
                                    FROM (
                                        SELECT item_no
                                        FROM shop_item_has_category
                                        WHERE
                                                shop_item_has_category.category_line_no = " . $category["line_no"] . "
                                            AND	shop_item_has_category.company = '" . $GLOBALS["shop"]["company"] . "'
                                            AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                            AND shop_item_has_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                            AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
                                        UNION SELECT item_no
                                        FROM shop_view_active_item
                                        WHERE 
                                                shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
                                            AND	shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                            AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                            AND shop_view_active_item.parent_item_no IN (
                                                SELECT item_no
                                                FROM shop_item_has_category
                                                WHERE
                                                        shop_item_has_category.category_line_no = " . $category["line_no"] .
                            emty_category_query($category) ."
                                                    AND	shop_item_has_category.company = '" . $GLOBALS["shop"]["company"] . "'
                                                    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                                    AND shop_item_has_category.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                                    AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
                                                    AND shop_item_has_category.category_language_code = '" . $GLOBALS['shop_language']['code'] . "'
                                            ) 
                                    ) AS v_i
                                 " . $vendor_sql_string . $attribute_link_query_where . "))
                                 AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
                                 AND shop_view_active_item.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                                 AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
                                 AND shop_item_has_category.category_line_no = " . $category["line_no"] . "
                                 AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
                                 AND shop_item_has_category.category_language_code = '" . $GLOBALS['shop_language']['code'] . "'
                                 ";


                        $order_by = $category_sort_order;
                        if (!$noOfHits && @mysqli_num_rows(@mysqli_query($GLOBALS['mysql_con'], $query)) > 0) {
                            //echo "<!-- PRE WITH PAGES ROWS > 0 -->";
                            show_item_list_with_pages($category["show_all_items"], $query, $category, $vendor_sql_string, 0, $order_by, $show_all_items, FALSE, "", 4);
                        } else {
                            //echo "<!-- PRE WITH PAGES NO ROWS!!! -->";
                        }
                        echo("</div>");

                        $itemlistDuration = microtime(true) - $itemlistStartTime;
                        //echo "ITEMLIST DURATION: [$itemlistDuration] seconds.";
                    }

                }
            }
        } else {
            //Anzeige ohne Kategorie
            echo "<div class=\"default_item_list\">";
            $query = "SELECT shop_view_active_item.*
					  FROM shop_view_active_item
					  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
					  WHERE shop_item_has_category.id <> 0
					  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
					    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
					    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
					    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
					    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
					    AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                $vendor_sql_string . "
					  GROUP BY shop_view_active_item.id
					  ORDER BY RAND()
					  LIMIT " . $GLOBALS['max_no_of_results'];
            //$result = mysqli_query($GLOBALS['mysql_con'], $query);
            show_item_list_from_query($query, 2, FALSE);
            echo "</div>";
        }
        echo get_category_description_2($category["id"]);
    }
}
//TF 10.07.2013 User_queue Lightbox_DIV
    if ($_GET["action"] == "shop_add_item_to_basket_card" || $_GET["action"] == "shop_add_item_to_basket_list" || $_GET["action"] == "shop_add_item_to_basket" ||  $_GET["action"] =="shop_individualize_item_card" ||  $_GET["action"] =="shop_individualize_basket" ) {
        require __DIR__ . DIRECTORY_SEPARATOR . 'user_queue.inc.php';
    }

?>
