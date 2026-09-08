<?

$basket = $IOCContainer->create('$CurrUserBasket');


// UT: Anpassung Mobile Version - 07.09.2012
if ($GLOBALS["site"]["code"] == $GLOBALS["site"]["mobile_version"]) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'mobile/show_item_list.inc.php';
} else {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'show_item_list.inc.php';
}
// UT: Anpassung Mobile Version - 07.09.2012


$GLOBALS["image_config"] = $image_config;

//Prüfen ob Kreditorenfilter gesetzt und speichern
if (isset($_GET["vendor"])) {
    $GLOBALS["vendor_no"] = $_GET["vendor"];
    $vendor_sql_string = " AND (vendor_no = " . $GLOBALS["vendor_no"] . ") ";
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

    $outputSearchTerm = htmlentities($_REQUEST['input_search']);
    ?>
    <div class="search_headline">
        <?php echo $GLOBALS['tc']['search'] . ': ' . $outputSearchTerm ?>
    </div>
    <?php

    //Suche auf Referenznummern erweitern
    $query = "SELECT *
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
    $query = "SELECT DISTINCT item_no
    		  FROM shop_item_description
    		  WHERE content LIKE '%" . $_REQUEST['input_search'] . "%'
    		  	AND company ='" . $GLOBALS['shop']['company'] . "'
    		  	AND shop_code = '" . $GLOBALS['shop']['item_source'] . "'
    		  	AND (language_code = '" . $GLOBALS['shop_language']['code'] . "' OR all_language_codes=1)
    		  	AND marketplace_only = 0";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $add_query .= " OR shop_view_active_item.item_no ='" . $row['item_no'] . "'";
        }
    }

    $variant_query = "";
    if ($GLOBALS['shop']['variant_type'] != 2) {
        $query = "SELECT DISTINCT parent_item_no
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
                $i = 1;
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
    //Query angepasst für Berechtigungen 20.12.2012 FK
    /*$query = "SELECT shop_view_active_item.*
    		  FROM shop_view_active_item
    		  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
    		  WHERE  shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
    		  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
    		  	AND shop_view_active_item.company ='".$GLOBALS['shop']['company']."'
    		  	".$var_query."
    		  	AND (shop_view_active_item.description LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.variant_type LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.item_no LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.summary LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.search_query LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		" . $add_query . " ".$variant_query.")
    		  GROUP BY shop_view_active_item.item_no";*/
    $query = "SELECT shop_view_active_item.*
    		  FROM shop_view_active_item
    		  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
    		  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  										AND shop_permissions_group_link.company = shop_view_active_item.company
    		  WHERE  shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
    		  	AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
    		  	AND shop_view_active_item.company ='" . $GLOBALS['shop']['company'] . "'
    		  	" . $var_query . "
    		  	AND (shop_view_active_item.description LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.variant_type LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.item_no LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.summary LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		OR shop_view_active_item.search_query LIKE '%" . $_REQUEST["input_search"] . "%'
    		  		" . $add_query . " " . $variant_query . ")
    		  		" . get_permissions_group_customer() . "
    		  GROUP BY shop_view_active_item.item_no";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $search_results = @mysqli_num_rows($result);

    //Ergebnisse oder Fehlerbox anzeigen
    if ($search_results > 0) {
        echo "<div class=\"itembox\">";
        show_item_list_with_pages(1, $query, "", "", "", "", FALSE, TRUE, $_REQUEST["input_search"]);
        echo "</div>";
    } else {
        if ($GLOBALS["shop_language"]["text_search_results"] <> '') {
            $message = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["text_search_results"], $spacer);
            echo "<div>" . $message . "</div>";
        }
    }
    $special_content = TRUE;

    // Suche für Statistik speichern
    $query = "INSERT INTO shop_search_query (id,company,shop_code,search_query,no_of_results,search_datetime)
			  VALUES (NULL,'" . $GLOBALS['shop']['company'] . "','" . $GLOBALS['shop']['code'] . "','" . strtoupper($_REQUEST["input_search"]) . "'," . $search_results . ",NOW())";
    @mysqli_query($GLOBALS['mysql_con'], $query);
}

//FK: Fehler bei direct order jetzt in errorbox
if ($GLOBALS['error_direct_order'] == 1) {
    echo("<div class='errorbox'>" . $GLOBALS['tc']['item_not_found'] . "</div>");
}


// Warenkorb anzeigen
if (($_GET["shop_category"] == 'basket') && !isset($_GET["card"])) {
    /*$special_breadcrumb = '
    <div class="infobar">
            <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/' . $GLOBALS['site']['code'] . '/' . $GLOBALS['language']['code'] . '/" itemprop="url"><span itemprop="title">' . $GLOBALS['tc']['homepage'] . '</span></a></span>  &gt; ' . $GLOBALS["tc"]["shopping_basket"] . '
    </div>';
    echo $special_breadcrumb;*/
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_basket.inc.php';
    $special_content = TRUE;

}

// Bestellung anzeigen
if (($_GET["shop_category"] == 'order') && !isset($_GET["card"])) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_order.inc.php';
    $special_content = TRUE;
}

// Favoriten anzeigen
if (($_GET["shop_category"] == 'favorites') && !isset($_GET["card"])) {
    /*$special_breadcrumb = '
    <div class="infobar">
            <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/' . $GLOBALS['site']['code'] . '/' . $GLOBALS['language']['code'] . '/" itemprop="url"><span itemprop="title">' . $GLOBALS['tc']['homepage'] . '</span></a></span>  &gt; ' . $GLOBALS["tc"]["favorites"] . '
    </div>';
    echo $special_breadcrumb;*/
    require __DIR__ . DIRECTORY_SEPARATOR . 'user_favorites.inc.php';
    $special_content = TRUE;
}

// Kundenkonto anzeigen
if (($_GET["shop_category"] == 'account') && !isset($_GET["card"])) {
    $tk = (empty($_GET['action']) ? $GLOBALS['tc']['shop_account'] : $GLOBALS['tc'][$_GET['action']]);
    /*$special_breadcrumb = '
    <div class="infobar">
            <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/' . $GLOBALS['site']['code'] . '/' . $GLOBALS['language']['code'] . '/" itemprop="url"><span itemprop="title">' . $GLOBALS['tc']['homepage'] . '</span></a></span>  &gt; ' . $tk . '
    </div>';
    echo $special_breadcrumb;*/
    require __DIR__ . DIRECTORY_SEPARATOR . 'shop_account.inc.php';
    $special_content = TRUE;
}

//rma (Reklamationsabwicklung) anzeigen
if (($_GET['shop_category'] == 'rma') && !isset($_GET['card']) && $GLOBALS["shop_setup"]["show_rma"] == 1) {

    $rootDir = dirname(dirname(dirname(__DIR__)));
    $rmaDir = $rootDir . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'rma';

    include($rmaDir . DIRECTORY_SEPARATOR . 'init.php');

    if (!isset($IOCContainer)) {
        $IOCContainer = unserialize($_SESSION['IOC']);
    }


    /**@var $rmaOrderHelper \DynCom\dc\dcShop\rma\classes\RMAOrderHelper
     *
     */
    $rmaOrderHelper = $IOCContainer->create('DynCom\dc\dcShop\rma\classes\RMAOrderHelper');
    /**
     * @var $rmaFrontController \DynCom\dc\dcShop\rma\classes\RMAFrontController
     */
    $rmaFrontController = $IOCContainer->create('DynCom\dc\dcShop\rma\classes\RMAFrontController');


    /*$special_breadcrumb = '
    <div class="infobar">
    <span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="/' . $GLOBALS['site']['code'] . '/' . $GLOBALS['language']['code'] . '/" itemprop="url"><span itemprop="title">' . $GLOBALS['tc']['homepage'] . '</span></a></span>  &gt; ' . $GLOBALS["tc"]["rma"] . '
    </div>';
    echo $special_breadcrumb;*/

    $rmaFrontController->handleRequest();

    $special_content = TRUE;
}


//Artikelkarte oder Artikelliste anzeigen
if (!$special_content) {
    // Kategoriebaum anzeigen
    $category = $GLOBALS['category'];
    $is_user_specific_sort_type = FALSE;
    if ($category["show_all_items"] == 1 || $category["show_all_items"] == 2 || $category["show_all_items"] == 3) {
        $category_sort_order = get_shop_category_sort_type($category["sort_items"]);
        if ($category['user_sorting']) {
            $is_user_specific_sort_type = TRUE;
            if ($_REQUEST["sort_by"] != "") {
                $category_sort_order = $_REQUEST["sort_by"];
            }
        }
    }


    //Anzeige des Kreditorenfilters
    if (($_GET["card"] == '') && ($category["show_all_items"]) && ($GLOBALS["shop"]["show_vendor_filter"] == 1)) {
        show_creditor_filter($category["line_no"]);
    }

    // Artikelkarte oder Artikelliste anzeigen
    if ($_GET["card"] <> '') {
        // UT: Anpassung Mobile Version - 07.09.2012
        if ($GLOBALS["site"]["code"] == $GLOBALS["site"]["mobile_version"]) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'mobile/show_item_card.inc.php';
        } else {
            require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';
        }
        // UT: Anpassung Mobile Version - 07.09.2012
        // require __DIR__ . DIRECTORY_SEPARATOR . 'show_item_card.inc.php';
    } else {
        echo "<div class=\"category_info\">";
        get_category_picture($category["id"]);
        get_promotion_description($category);
        $category_description = get_category_description($category["id"]);
        if(!$category_description) {
            echo "<h1 class=\"shop_site_headline category_headline\">" . $category['name'] . "</h1>";
        }
        echo "</div>";

        if ($GLOBALS["shop"]["show_filters"]) {
            show_category_filters($category, $category_sort_order);
        }

        if ($category["id"] <> '') {

            $num_of_nav_values = 0;
            $filterquery = get_filterquery($num_of_nav_values);
            $havingquery = "";
            if (count($_SESSION['filters']) > 0) {
                $count = count($_SESSION['filters']) - $num_of_nav_values;
                if ($count > 0) {
                    $havingquery .= "HAVING COUNT(DISTINCT shop_attribute_link.id) = " . $count;
                }
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
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                show_item_list_from_query($result, 1, '');
            } else {
                if ($category["show_sub_categorys"] == 1) {
                    //Unterkategorien anzeigen
                    //Erweiterung um Kategoriebilder f�r jede Unterkategorie
                    if (file_exists($GLOBALS["shop_setup"]["uploaddir_category_picture"] . $category['category_picture'])) {
                        ?>
                        <div class="shop_category_image">
                            <img
                                    src="<?= $GLOBALS["shop_setup"]["uploaddir_category_picture"] . $category['category_picture'] ?>"
                                    alt="<?= $category['name'] ?>"
                                    title="<?= $category['name'] ?>"
                            />
                        </div>
                        <?
                    }
                    echo "<div class=\"categorybox\"><div class='row'>";
                    $cat_line_no = $category["line_no"];
                    //Query angepasst für Berechtigungen 20-12-2012 FK
                    /*$query = "SELECT *
                              FROM shop_category AS sc
                              WHERE sc.parent_line_no = ".$cat_line_no."
                                AND sc.company = '".$category['company']."'
                                AND sc.shop_code ='".$category['shop_code']."'
                                AND sc.language_code = '".$category['language_code']."'
                                AND sc.active=1";*/
                    $query = "SELECT sc.*
						  FROM shop_category AS sc
						  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.line_no = sc.line_no
		  											AND shop_permissions_group_link.shop_code = sc.shop_code
		  											AND shop_permissions_group_link.language_code = sc.language_code
		  											AND shop_permissions_group_link.company = sc.company
						  WHERE sc.parent_line_no = " . $cat_line_no . "
							AND sc.company = '" . $category['company'] . "'
							AND sc.shop_code ='" . $category['shop_code'] . "'
							AND sc.language_code = '" . $category['language_code'] . "'
							AND sc.active=1 " . get_permissions_group_customer() . "
							AND (sc.validity_from IS NULL OR sc.validity_from = '0000-00-00' OR sc.validity_from <= '" . date('Y-m-d') . "')
							AND (sc.validity_to IS NULL OR sc.validity_to = '0000-00-00' OR sc.validity_to > '" . date('Y-m-d') . "')
						  ORDER BY sc.sorting ASC";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_category_list($result, $GLOBALS["shop_language"]);
                    echo "</div></div>";
                } elseif ($category["show_sub_categorys"] == 2) {
                    echo "<div class=\"categorybox\"><div class='row'>\n";
                    $cat_line_no = $category["line_no"];
                    $query = "SELECT sc.*
						  FROM shop_category AS sc
						  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.line_no = sc.line_no
		  											AND shop_permissions_group_link.shop_code = sc.shop_code
		  											AND shop_permissions_group_link.language_code = sc.language_code
		  											AND shop_permissions_group_link.company = sc.company
						  WHERE sc.parent_line_no = " . $cat_line_no . "
							AND sc.company = '" . $category['company'] . "'
							AND sc.shop_code ='" . $category['shop_code'] . "'
							AND sc.language_code = '" . $category['language_code'] . "'
							AND sc.active=1 " . get_permissions_group_customer() . "
							AND (sc.validity_from IS NULL OR sc.validity_from = '0000-00-00' OR sc.validity_from <= '" . date('Y-m-d') . "')
							AND (sc.validity_to IS NULL OR sc.validity_to = '0000-00-00' OR sc.validity_to > '" . date('Y-m-d') . "')
						  ORDER BY sc.sorting ASC";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_category_list_etsy($result, $GLOBALS["shop_language"]);
                    echo "</div>
                        </div>\n";
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
                    //Query angepasstfür Berechtigungen 20.12.2012 FK
                    /*$query = "SELECT DISTINCT shop_view_active_item.*
                              FROM shop_item_has_category
                              INNER JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
                              INNER JOIN shop_category sc ON sc.line_no = shop_item_has_category.category_line_no
                              WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                                emty_category_query($category) . ")
                                AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
                                AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"]. "'
                                AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
                                AND shop_item_has_category.company = '".$GLOBALS['shop']['company']."'
                                AND shop_item_has_category.shop_code = '".$GLOBALS['shop']['item_source']."'
                                AND shop_item_has_category.language_code = '".$GLOBALS['shop_language']['code']."'
                                AND shop_item_has_category.category_shop_code = '".$GLOBALS['shop']['category_source']."'" .
                                $vendor_sql_string."
                                AND sc.company = '".$GLOBALS['shop']['company']."'
                                AND sc.shop_code = '".$GLOBALS['shop']['category_source']."'
                                AND sc.language_code = '".$GLOBALS['shop_language']['code']."'
                                AND sc.promotion_active = 1
                                AND (isnull(sc.promotion_validity_from) AND isnull(sc.promotion_validity_to)
                                    OR (isnull(sc.promotion_validity_from) AND (sc.promotion_validity_to >= curdate())
                                    OR (sc.promotion_validity_from <= curdate()) AND isnull(sc.promotion_validity_to))
                                    OR (sc.promotion_validity_from <= curdate()) AND (sc.promotion_validity_to >= curdate()))
                              ORDER BY RAND()
                              LIMIT ".$max_campain_no;*/
                    $query = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  INNER JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  INNER JOIN shop_category sc ON sc.line_no = shop_item_has_category.category_line_no
							  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  														AND shop_permissions_group_link.company = shop_view_active_item.company
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
								AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
								AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
								AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
								AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
								AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
								AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'" .
                        $vendor_sql_string . "
								AND sc.company = '" . $GLOBALS['shop']['company'] . "'
								AND sc.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
								AND sc.language_code = '" . $GLOBALS['shop_language']['code'] . "'
								AND sc.promotion_active = 1
								AND (isnull(sc.promotion_validity_from) AND isnull(sc.promotion_validity_to)
									OR (isnull(sc.promotion_validity_from) AND (sc.promotion_validity_to >= curdate())
									OR (sc.promotion_validity_from <= curdate()) AND isnull(sc.promotion_validity_to))
									OR (sc.promotion_validity_from <= curdate()) AND (sc.promotion_validity_to >= curdate()))
								" . get_permissions_group_customer() . "
							  ORDER BY RAND()
							  LIMIT " . $max_campain_no;
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (mysqli_num_rows($result) > 0) {
                        show_item_list_from_query($result, $type, '');
                        echo("<div class='clearfloat'></div>");
//						echo("<div class='seperator_line'></div>");
                    }
                }

                //Abfragen ob zufällige Artikel angezeigt werden sollen.
                if ($category["show_random_items"] != 0) {
                    if ($category["show_campain_items"] == 0) {
                        echo "<div class=\"seperator_line_2\"><div style=\"height:10px;margin-top:13px;\">Highlights</div></div>";
                    }
                    $type = $category["show_random_items"];
                    $max_random_no = $category["no_of_random_items"];
                    if ($category["show_campain_items"] == 0) {
                        echo "<div class=\"random_item_list\">";
                    }
                    //Query angepasst für Berechtigungen 20.12.2012 FK
                    /*$query = "SELECT DISTINCT shop_view_active_item.*
                              FROM shop_item_has_category
                              RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
                              WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                                emty_category_query($category) . ")
                                AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
                                AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"]. "'
                                AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
                                AND shop_item_has_category.company = '".$GLOBALS['shop']['company']."'
                                AND shop_item_has_category.shop_code = '".$GLOBALS['shop']['item_source']."'
                                AND shop_item_has_category.category_shop_code = '".$GLOBALS['shop']['category_source']."'
                                AND shop_item_has_category.language_code = '".$GLOBALS['shop_language']['code']."'" .
                                $vendor_sql_string."
                              GROUP BY shop_view_active_item.id
                              ORDER BY RAND()
                              LIMIT ".$max_random_no;*/
                    $query = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  														AND shop_permissions_group_link.company = shop_view_active_item.company
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
								AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
								AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
								AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
								AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
								AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
								AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
								AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                        $vendor_sql_string . " " .
                        get_permissions_group_customer() . "
							  GROUP BY shop_view_active_item.id
							  ORDER BY RAND()
							  LIMIT " . $max_random_no;
                    //$result = mysqli_query($GLOBALS['mysql_con'], $query);
                    show_item_list_from_query($query, $type, '');
                }
                if ($category["show_campain_items"] != 0 || $category["show_random_items"] != 0) {
                    echo "</div>";
                }


                $attribute_link_query_join = '';
                $attribute_link_query_where = '';

                if($filterquery != '' ){
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
                }

                //Abfragen ob alle Artikel angezeigt werden sollen

                if ($category["show_all_items"] != 0) {

//					echo "<div class=\"seperator_line\"></div>";
                    echo "<div class=\"itembox\">";
                    //Query angepasst für Berechtigungen 20.12.2012 FK
                    /*$query = "SELECT DISTINCT shop_view_active_item.*
                              FROM shop_item_has_category
                              RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
                              WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                              emty_category_query($category) .")
                              AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
                              AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"]. "'
                              AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
                              AND shop_item_has_category.company = '".$GLOBALS['shop']['company']."'
                              AND shop_item_has_category.shop_code = '".$GLOBALS['shop']['item_source']."'
                              AND shop_item_has_category.category_shop_code = '".$GLOBALS['shop']['category_source']."'
                              AND shop_item_has_category.language_code = '".$GLOBALS['shop_language']['code']."'" .
                              $vendor_sql_string. " ";*/



                  /*  $query = "SELECT DISTINCT shop_view_active_item.*
							  FROM shop_item_has_category
							  RIGHT JOIN shop_view_active_item ON shop_item_has_category.item_no = shop_view_active_item.item_no
							    ".   $attribute_link_query_join    ."
							  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  														AND shop_permissions_group_link.company = shop_view_active_item.company
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
                        " . $filterquery . "
                        " . $attribute_link_query_where ."
							  AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							  AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							  AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							  AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							  AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
							  AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							  AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                        $vendor_sql_string . "
							  " . get_permissions_group_customer() . " " .
                        $havingquery; */



                    $query = "SELECT DISTINCT shop_view_active_item.*
							FROM shop_item_has_category
							  INNER JOIN shop_view_active_item ON (
							    shop_view_active_item.company = shop_item_has_category.company AND 
							    shop_view_active_item.shop_code = shop_item_has_category.shop_code AND 
							    shop_view_active_item.language_code = shop_item_has_category.language_code AND 
                                    ( 
                                      shop_item_has_category.item_no = shop_view_active_item.item_no OR 
                                      shop_item_has_category.item_no = shop_view_active_item.parent_item_no
                                    )
							    )
							  $attribute_link_query_join
                                RIGHT JOIN
                                    shop_view_active_item svac ON CASE
                                        WHEN shop_view_active_item.parent_item_no = '' THEN svac.item_no = shop_view_active_item.item_no
                                        ELSE svac.item_no = shop_view_active_item.parent_item_no
                                    END
                                    AND svac.company = shop_view_active_item.company
                                    AND svac.shop_code = shop_view_active_item.shop_code
                                        AND svac.language_code = shop_view_active_item.language_code
                                 LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  														AND shop_permissions_group_link.company = shop_view_active_item.company         
							  WHERE (shop_item_has_category.category_line_no = " . $category["line_no"] .
                        emty_category_query($category) . ")
							  AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
							  AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
							  AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
							  AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
							  AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['category_source'] . "'
							  AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'
							  AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'" .
                        $vendor_sql_string .
                          get_permissions_group_customer()  .
                        $filterquery . "
							 ".$attribute_link_query_where."
							  GROUP BY shop_view_active_item.id " .
                        $havingquery;











                    $order_by = $category_sort_order;
                    if (@mysqli_num_rows(@mysqli_query($GLOBALS['mysql_con'], $query)) > 0) {
//                    	echo "<div class=\"seperator_line\"></div>";
                        show_item_list_with_pages($category["show_all_items"], $query, $category, $vendor_sql_string, 0, $order_by, $show_all_items, FALSE, "", $is_user_specific_sort_type);
                    }
                    echo "</div>";
                }
            }
            get_category_description_2($category['id']);
        } else {
            //Anzeige ohne Kategorie
            echo "<div class=\"default_item_list\">";
            //Query angepasst für Berechtigungen 20-12-2012 FK
            /*$query = "SELECT shop_view_active_item.*
                      FROM shop_view_active_item
                      LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
                      WHERE shop_item_has_category.id <> 0
                        AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"]. "'
                        AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"]. "'
                        AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"]. "'
                        AND shop_item_has_category.company = '".$GLOBALS['shop']['company']."'
                        AND shop_item_has_category.shop_code = '".$GLOBALS['shop']['item_source']."'
                        AND shop_item_has_category.category_shop_code = '".$GLOBALS['shop']['category_source']."'
                        AND shop_item_has_category.language_code = '".$GLOBALS['shop_language']['code']."'" .
                        $vendor_sql_string."
                      GROUP BY shop_view_active_item.id
                      ORDER BY shop_view_active_item.no_of_campain DESC, RAND()
                      LIMIT ".$GLOBALS['max_no_of_results'];*/
            $query = "SELECT shop_view_active_item.*
					  FROM shop_view_active_item
					  LEFT JOIN shop_item_has_category ON shop_view_active_item.item_no = shop_item_has_category.item_no
					  LEFT JOIN shop_permissions_group_link ON shop_permissions_group_link.item_no = shop_view_active_item.item_no
    		  												AND shop_permissions_group_link.company = shop_view_active_item.company
					  WHERE shop_item_has_category.id <> 0
						AND shop_view_active_item.language_code = '" . $GLOBALS["shop_language"]["code"] . "'
						AND shop_view_active_item.company = '" . $GLOBALS["shop"]["company"] . "'
						AND shop_view_active_item.shop_code = '" . $GLOBALS["shop"]["item_source"] . "'
						AND shop_item_has_category.company = '" . $GLOBALS['shop']['company'] . "'
						AND shop_item_has_category.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
						AND shop_item_has_category.category_shop_code = '" . $GLOBALS['shop']['category_source'] . "'
						AND shop_item_has_category.language_code = '" . $GLOBALS['shop_language']['code'] . "'" .
                $vendor_sql_string . "
						" . get_permissions_group_customer() . "
					  GROUP BY shop_view_active_item.id
					  ORDER BY shop_view_active_item.no_of_campain DESC, RAND()
					  LIMIT " . $GLOBALS['max_no_of_results'];
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            show_item_list_from_query($result, 2, '');
            echo "</div>";
        }
        //echo("</div>");
    }
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'tooltips.inc.php';
?>