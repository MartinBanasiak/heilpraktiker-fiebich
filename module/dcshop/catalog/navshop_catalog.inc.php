<?

require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';
$GLOBALS["image_config"] = $image_config;

//Prüfen ob Kreditorenfilter gesetzt und speichern
if (isset($_GET["vendor"])) {
    $GLOBALS["vendor_no"] = $_GET["vendor"];
    $vendor_sql_string    = " AND (vendor_no = " . $GLOBALS["vendor_no"] . ") ";
} else {
    $GLOBALS["vendor_no"] = '';
    $vendor_sql_string    = "";
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
// Suchanfrage auswerten
if (($_GET["shop_category"] == 'search') && ($_REQUEST["input_search"] <> '' || $_SESSION['search'] != "") && ($_REQUEST["input_search"] != $GLOBALS["tc"]["search_term"]) && !isset($_GET["card"])) {
    $_SESSION['search_counter'] = 0;
    //Suchbegriff speichern für "zurück zum Suchergebnis"
    if ($_REQUEST['input_search'] != "") {
        $_SESSION['search'] = $_REQUEST['input_search'];
    }
    if ($_REQUEST['input_search'] == "" && isset($_SESSION['search'])) {
        $_REQUEST['input_search'] = $_SESSION['search'];
    }

    //Ausgabe Suchbegriff in Navigationsleiste
    /*$special_breadcrumb = '
	<div class="infobar">
		<span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/' . $GLOBALS['site']['code'] . '/' . $GLOBALS['language']['code'] . '/" itemprop="url"><span itemprop="title">' . $GLOBALS['tc']['homepage'] . '</span></a></span>  &gt; ' . $GLOBALS["tc"]["search"] . ' : ' . htmlspecialchars($_POST['input_search'], ENT_QUOTES, "UTF-8") . '
	</div>';
    echo $special_breadcrumb;*/

    //Suche auf Referenznummern erweitern
    $query  = "SELECT *
    		  FROM shop_item_cross_reference
    		  WHERE item_reference_no LIKE '%" . $_REQUEST['input_search'] . "%'
    		  	AND (customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "' OR customer_no='')
    		  	AND company ='" . $GLOBALS['shop']['company'] . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $add_query .= " OR shop_view_active_item.item_no ='" . $row['item_no'] . "'";
        }
    }

    //Suche in Beschreibungstexten
    $query  = "SELECT DISTINCT item_no
    		  FROM shop_item_description
    		  WHERE content LIKE '%" . $_REQUEST['input_search'] . "%'
    		  	AND company ='" . $GLOBALS['shop']['company'] . "'
    		  	AND shop_code = '" . $GLOBALS['shop']['item_source'] . "'
    		  	AND marketplace_only = 0
    		  	AND (language_code = '" . $GLOBALS['shop_language']['code'] . "' OR all_language_codes=1)";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $add_query .= " OR shop_view_active_item.item_no ='" . $row['item_no'] . "'";
        }
    }
    $variant_query = "";
    if ($GLOBALS['shop']['variant_type'] != 2) {
        $query  = "SELECT DISTINCT parent_item_no
				  FROM shop_view_active_item
				  WHERE shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
	    		  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
	    		  	AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
				  	AND (shop_view_active_item.description LIKE '%" . $_REQUEST["input_search"] . "%'
	    		  		OR shop_view_active_item.variant_type LIKE '%" . $_REQUEST["input_search"] . "%'
	    		  		OR shop_view_active_item.item_no LIKE '%" . $_REQUEST["input_search"] . "%'
	    		  		OR shop_view_active_item.summary LIKE '%" . $_REQUEST["input_search"] . "%'
	    		  		OR shop_view_active_item.search_query LIKE '%" . $_REQUEST["input_search"] . "%')
				  	AND shop_view_active_item.parent_item_no !=''";
        $result = mysqli_query($GLOBALS['mysql_con'], $query);
        if (mysqli_num_rows($result) > 0) {
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $variant_query .= " OR (shop_view_active_item.item_no = '" . $row['parent_item_no'] . "'";
            } else {
                $i    = 1;
                $last = mysqli_num_rows($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($i == 1) {
                        $variant_query .= " OR ((shop_view_active_item.item_no = '" . $row['parent_item_no'] . "'";
                        $i++;
                    } elseif ($i == $last) {
                        $variant_query .= " OR shop_view_active_item.item_no = '" . $row['parent_item_no'] . "')";
                        $i++;
                    } else {
                        $variant_query .= " OR shop_view_active_item.item_no = '" . $row['parent_item_no'] . "'";
                        $i++;
                    }
                }
            }
            if ($variant_query != "") {
                $variant_query .= " AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
		 							AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
				  					AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "')";
            }
        }
        $var_query = " AND parent_item_no = '' ";
    } else {
        $var_query = '';
    }
    //Suche ausführen
    $query          = "SELECT shop_view_active_item.*
    		  FROM shop_view_active_item
    		  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
    		  WHERE  shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
    		  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
    		  	AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
    		  	" . $var_query . "
    		  	AND (shop_view_active_item.description LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.variant_type LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.item_no LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.summary LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.search_query LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		" . $add_query . ")
    		  GROUP BY shop_view_active_item.item_no";
    $result         = mysqli_query($GLOBALS['mysql_con'], $query);
    $search_results = @mysqli_num_rows($result);

    //Ergebnisse oder Fehlerbox anzeigen
    if ($search_results > 0) {
        echo "<div class=\"itembox\">";
        show_item_list_with_pages(1, $query, "", "", "", "", FALSE, TRUE, $_REQUEST["input_search"]);
        echo "</div>";
    } else {
        $spacer["%search_input%"] = $_REQUEST["input_search"];
        if ($GLOBALS["shop_language"]["text_search_results"] <> '') {
            $message = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["text_search_results"], $spacer);
            //echo "<br /><div class=\"errorbox\">" . $message . "</div>";
            echo "<div>" . $message . "</div>";
        }
    }
    $special_content = TRUE;

    // Suche für Statistik speichern
    $query = "INSERT INTO shop_search_query (id,company,shop_code,search_query,no_of_results,search_datetime)
			  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS['shop']['code'] . "','" . strtoupper($_REQUEST["input_search"]) . "'," . $search_results . ",NOW())";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

// Kundenkonto anzeigen
if (($_GET["shop_category"] == 'account') && !isset($_GET["card"])) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'shop_account.inc.php';
    $special_content = TRUE;
}

//Artikelkarte oder Artikelliste anzeigen
if (!$special_content) {
    // Kategoriebaum anzeigen
    $category                   = $GLOBALS['category'];
    $is_user_specific_sort_type = FALSE;
    if ($category["show_all_items"] == 1 || $category["show_all_items"] == 2 || $category["show_all_items"] == 3) {
        $category_sort_order = get_shop_category_sort_type($category["sort_items"]);
        if ($category['user_sorting']) {
            $is_user_specific_sort_type = TRUE;
            if ($_REQUEST["sort_by"] == "") {
                $category_sort_order = get_shop_category_sort_type($category["sort_items"]);
            } else {
                $category_sort_order = $_REQUEST["sort_by"];
            }
        }
    }
    /*echo "<div class=\"infobar\">";
    if ($category["id"] <> '') {
        echo curr_category_path($category, ($_GET["card"] <> ''));
    } else {
        if ($_GET["slevel_2"]) {
            echo '&gt; <a href="">' . $_GET["slevel_2"] . '</a>';
            echo curr_category_path($category, ($_GET["card"] <> ''));
        } else {
            echo '&gt; <a href="/' . $GLOBALS["shop"]["code"] . '/' . $GLOBALS["language"]["code"] . '/shop/' . get_code_by_id(get_category_id_by_line_no($GLOBALS["shop_setup"]["category_line_no_portal"])) . '/">' . $GLOBALS["site"]["name"] . '</a>';
        }
    }
    echo "</div>";*/

    //Anzeige des Kreditorenfilters
    if (($_GET["card"] == '') && ($category["show_all_items"]) && ($GLOBALS["shop"]["show_vendor_filter"] == 1)) {
        show_creditor_filter($category["line_no"]);
    }

    // Artikelkarte oder Artikelliste anzeigen
    if ($_GET["card"] <> '') {
        require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';
    } else {
        echo "<div class=\"category_info\">";
            echo "<div class=\"shop_site_headline category_headline\">".$category['name']."</div>";
            get_category_picture($category["id"]);
            get_promotion_description($category);
            get_category_description($category["id"]);
        echo "</div>";
        // Ende
        if ($category["id"] <> '') {
            if ($category["list_type"] == "2") {
                $query  = "SELECT shop_view_active_item.*
						  FROM shop_item_has_category
						  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
						  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                    emty_category_query($category) . ")" .
                    $vendor_sql_string . "
						  GROUP BY shop_view_active_item.id
						  ORDER BY shop_view_active_item.item_no";
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                show_item_list_from_query($result, 1, FALSE);
            } else {
                if ($category["show_sub_categorys"] == 1) {

                    //Erweiterung um Kategoriebilder f�r jede Unterkategorie
                    echo "<div class=\"categorybox\"><div class='row'>";
                    $cat_line_no = $category["line_no"];
                    $query       = "SELECT sc.*
						  FROM shop_category AS sc
						  WHERE sc.parent_line_no = " . $cat_line_no . "
							AND sc.company = '" . $category['company'] . "'
							AND sc.shop_code ='" . $category['shop_code'] . "'
							AND sc.language_code = '" . $category['language_code'] . "'
							AND sc.active=1
							ORDER BY sc.sorting ASC";
                    $result      = @mysqli_query($GLOBALS['mysql_con'], $query);
                    show_category_list($result, $GLOBALS["shop_language"]);
                    echo "</div></div>";
                }

                //Spacer-Linie anzeigen, wenn Kategorien vorhanden
//				if((has_children($category["line_no"])==1) && ($category["show_sub_categorys"]!=0)){
//					echo "<div class=\"seperator_line\"></div>";
//				}

                if ($category["id"] != 0) {
                    $show_all_items = TRUE;
                } else {
                    $show_all_items = FALSE;
                }

                // Abfragen ob Aktionsartikel angezeigt werden sollen
                if ($category["show_campain_items"] != 0) {
                    $type = $category["show_campain_items"];
                    echo "<div class=\"campaign_item_list\">";
                    $max_campain_no = $category["no_of_campain_items"];
                    $query          = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  INNER JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  INNER JOIN shop_category sc ON sc.line_no = shop_item_has_category.category_line_no
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							    AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
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
                    $result         = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (mysqli_num_rows($result) > 0) {
                        show_item_list_from_query($result, $type, FALSE);
                        echo("<div class='clearfloat'></div>");
//						echo("<div class='seperator_line'></div>");
                    }
                }

                //Abfragen ob zufällige Artikel angezeigt werden sollen.
                if ($category["show_random_items"] != 0) {
                    if ($category["show_campain_items"] == 0) {
                        echo "<div class=\"seperator_line_2\"><div style=\"height:10px;margin-top:13px;\">Highlights</div></div>";
                    }
                    $type          = $category["show_random_items"];
                    $max_random_no = $category["no_of_random_items"];
                    if ($category["show_campain_items"] == 0) {
                        echo "<div class=\"random_item_list\">";
                    }
                    $query  = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							    AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							    AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                        $vendor_sql_string . "
							  GROUP BY shop_view_active_item.id
							  ORDER BY RAND()
							  LIMIT " . $max_random_no;
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_item_list_from_query($result, $type, FALSE);
                }
                if ($category["show_campain_items"] != 0 || $category["show_random_items"] != 0) {
                    echo "</div>";
                }

                //Abfragen ob alle Artikel angezeigt werden sollen
                if ($category["show_all_items"] != 0) {
//					echo "<div class=\"seperator_line\"></div>";
                    echo "<div class=\"itembox\">";
                    $query    = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
							  AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							  AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							  AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							  AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							  AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							  AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							  AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                        $vendor_sql_string . " ";
                    $order_by = $category_sort_order;
                    if (@mysqli_num_rows(@mysqli_query($GLOBALS['mysql_con'], $query)) > 0) {
                        show_item_list_with_pages($category["show_all_items"], $query, $category, $vendor_sql_string, 0, $order_by, $show_all_items, FALSE, "", $is_user_specific_sort_type);
                    }
                    echo "</div>";
                }
            }
        } else {
            //Anzeige ohne Kategorie
            echo "<div class=\"default_item_list\">";
            $query  = "SELECT shop_view_active_item.*
					  FROM shop_view_active_item
					  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
					  WHERE shop_item_has_category.id <> 0
					  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
					    AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
					    AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
					    AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
					    AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
					    AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
					    AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                $vendor_sql_string . "
					  GROUP BY shop_view_active_item.id
					  ORDER BY shop_view_active_item.no_of_campain DESC, RAND()
					  LIMIT " . $GLOBALS['max_no_of_results'];
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            show_item_list_from_query($result, 2, FALSE);
            echo "</div>";
        }
    }
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'tooltips.inc.php';
?>