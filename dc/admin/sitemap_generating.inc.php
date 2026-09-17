<?

function generate_robots_txt_file(){
    $GLOBALS['projectRoot'] = '/' . rtrim(getenv('PROJECT_ROOT'),'/\\');
    $handler_for_robots = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/robots.txt","w+");
    if(substr_count($_SERVER['SERVER_NAME'],'.dc-test.de') > 0) {
        $set_robots = "User-agent: *\n";
        $set_robots .= "Disallow: /\n";
        $set_robots .= "Sitemap: https://".$_SERVER["SERVER_NAME"]."/sitemap.xml";
    } else {
        /*
        PLACE ROBOTS-CHANGES HERE!
        */
        $set_robots = "
        
        ";

        $set_robots .= "Sitemap: https://".$_SERVER["SERVER_NAME"]. $GLOBALS['projectRoot'] . "/sitemap.xml";
    }
    fwrite($handler_for_robots, $set_robots);
    fclose($handler_for_robots);
}

function init_sitemap_file(){

    $handler_for_sitemap = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap.xml","w+");
    $start_sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $start_sitemap.="<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    fwrite($handler_for_sitemap, $start_sitemap);
    fclose($handler_for_sitemap);
}

function close_sitemap_file(){
    $handler_for_sitemap = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap.xml","a");
    $stop_sitemap = "</sitemapindex>";
    fwrite($handler_for_sitemap, $stop_sitemap);
    fclose($handler_for_sitemap);
}

function ping_sitemap() {
    $GLOBALS['projectRoot'] = '/' . rtrim(getenv('PROJECT_ROOT'),'/\\');
    $adress = "https://".$_SERVER["SERVER_NAME"]. $GLOBALS['projectRoot'] . "/sitemap.xml";
    $ch = curl_init();
    $address = "http://www.google.com/webmasters/tools/ping?sitemap=".urlencode($adress);
    curl_setopt($ch, CURLOPT_URL, $address);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response_shop = curl_exec($ch);
}

/**
 * Hat der Menuepunkt eine eigene Seite - und damit eine eigene Adresse?
 *
 * forward_type 1 heisst "Seite zuordnen". Alles andere sind Weiterleitungen
 * (2 intern, 3 extern, 5 interner Pfad, 6 Shop-Kategorie) oder ein Platzhalter
 * (4). Die Sitemap soll Zielseiten nennen, keine Zwischenstationen: Die
 * Untermenuepunkte der Behandlungsmethoden etwa sind Typ 5 und antworten mit
 * 302 auf die Kollektionsseiten, die weiter unten einzeln aufgefuehrt werden.
 *
 * Aktiv- und Gueltigkeitspruefung stehen hier mit drin, weil der Baum seit
 * dieser Aenderung ueber main_navigation laeuft und nicht mehr ueber die
 * gefilterte Ansicht - die Bedingungen entsprechen denen von
 * main_view_active_navigation.
 */
function sitemap_navigation_has_own_page($navigation) {

    if ((int)$navigation['active'] !== 1) {
        return FALSE;
    }

    $today = date("Y-m-d");
    if (!empty($navigation['validity_from']) && $navigation['validity_from'] > $today) {
        return FALSE;
    }
    if (!empty($navigation['validity_to']) && $navigation['validity_to'] < $today) {
        return FALSE;
    }

    return ((int)$navigation['forward_type'] === 1);
}

/**
 * Detailseiten der Kollektionen einer Seite anhaengen.
 *
 * Eine Seite kann eine Kollektionsliste enthalten - die Behandlungsmethoden
 * etwa. Jeder Eintrag darin hat im Frontend eine eigene Adresse aus der Adresse
 * der Listenseite und get_collection_rewrite(). Der Generator hat diese Seiten
 * bisher ueberhaupt nicht gekannt: In der Sitemap stand nur die Uebersicht.
 *
 * Nur Kollektionen aus einem verlinkten Aufbau (main_collection_setup.linked).
 * Bei den uebrigen - Linkliste, Fortbildungen - zeigt die Uebersicht bereits
 * alles, was es gibt; eine Detailseite wird dort nirgends verlinkt und waere in
 * der Sitemap eine Seite ohne eigenen Inhalt.
 */
function sitemap_append_collections($navigation_id, $conurl, $site, $language) {

    global $sitemap_co;

    $query = "SELECT pl.main_collection_setup_id AS setup_id
                FROM main_page_link pl
                JOIN main_navigation n ON n.forward_page_id = pl.main_page_id
                JOIN main_collection_setup cs ON cs.id = pl.main_collection_setup_id
               WHERE n.id = '" . $navigation_id . "'
                 AND n.forward_type = 1
                 AND pl.main_collection_list = 1
                 AND pl.active = 1
                 AND cs.linked = 1";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);

    while ($setup = @mysqli_fetch_assoc($result)) {

        // Dieselben Bedingungen, mit denen das Frontend die Liste fuellt
        // (show_collection_setup_content()): Gueltigkeitszeitraum und eine
        // gesetzte Bezeichnung. Dazu noindex - was die Seite selbst aus dem
        // Index haelt, gehoert nicht in die Sitemap.
        $query_col = "SELECT id, description FROM main_collection
                       WHERE main_collection_setup_id = '" . $setup['setup_id'] . "'
                         AND description != ''
                         AND noindex = 0
                         AND (validity_from IS NULL OR validity_from <= '" . date("Y-m-d") . "')
                         AND (validity_to   IS NULL OR validity_to   >= '" . date("Y-m-d") . "')
                       ORDER BY sorting ASC";
        $result_col = @mysqli_query($GLOBALS['mysql_con'],$query_col);

        while ($collection = @mysqli_fetch_assoc($result_col)) {
            $colurl = $conurl . get_collection_rewrite($collection['description'], $collection['id']);
            $sitemap_co .= "		<url>\n";
            $sitemap_co .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].customizeUrl(true, $site, $language)."/".$colurl)."</loc>\n";
            $sitemap_co .= "		</url>\n";
        }
    }
}

$sitemap_co = "";
function generate_content($language_code,$shop_code,$company) {

    global $sitemap_co;

    $query = "SELECT * FROM shop_shop WHERE company = '" . $company . "' AND code = '" . $shop_code . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $shop = mysqli_fetch_assoc($result);

    $query = "SELECT * FROM main_language WHERE company = '".$company."' AND shop_code = '".$shop_code."' AND shop_language_code = '".$language_code."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $language = mysqli_fetch_assoc($result);

    $query = "SELECT * FROM main_site WHERE id = '".$language['main_site_id']."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $site = mysqli_fetch_assoc($result);

    // Bewusst main_navigation statt main_view_active_navigation: Menuebereiche
    // wie "info" sind selbst nicht aktiv, sie sind nur der Behaelter fuer
    // Impressum und Datenschutz. Die Ansicht filtert sie heraus - und mit ihnen
    // die aktiven Seiten darunter, die dadurch nie in der Sitemap landeten.
    // Ob ein Eintrag eine eigene Adresse bekommt, entscheidet jetzt
    // sitemap_navigation_has_own_page(); durchlaufen wird der Baum vollstaendig.
    $query = "SELECT * FROM main_navigation WHERE main_site_id = " . $site["id"] . " AND main_language_id = " . $language["id"] . " AND level = '1' AND ISNULL(parent_id) ORDER BY sorting ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);

    $sitemap_co = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $sitemap_co .= "	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    // Startseite. Ihr Navigationseintrag ist in der Regel nicht aktiv - ueber
    // die Menuestruktur findet der Generator sie also nicht. Erreichbar ist sie
    // unter der Wurzel, und genau dorthin zeigt auch ihr Canonical
    // (siehe get_canonical() in common_functions.inc.php).
    $sitemap_co .= "		<url>\n";
    $sitemap_co .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'])."</loc>\n";
    $sitemap_co .= "		</url>\n";

    while ($content = mysqli_fetch_array($result)) {
        // Kein strtolower: Die Navigationscodes werden im Frontend
        // buchstabengetreu ausgewertet. "FAQ" kleingeschrieben ergibt eine
        // 404-Adresse in der Sitemap.
        $conurl = $content['code']."/";

        /*
        //ist shop sitepart?
        $subquery = "SELECT id FROM main_navigation_has_sitepart WHERE main_navigation_id = '".$content['id']."' AND main_sitepart_id = '7'";
        $subresult = @mysqli_query($GLOBALS['mysql_con'],$subquery);
        echo $subquery;
        if (mysqli_num_rows($subresult) == 0) {

            //hat inhalt?
            $subquery2 = "SELECT id FROM main_navigation_has_sitepart WHERE main_navigation_id = '".$content['id']."'";
            $subresult2 = @mysqli_query($GLOBALS['mysql_con'],$subquery2);
            #echo $subquery2;
            if (mysqli_num_rows($subresult2) > 0) {
            */
        if (sitemap_navigation_has_own_page($content)) {
            $sitemap_co .= "		<url>\n";
            $sitemap_co .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].customizeUrl(true, $site, $language)."/".$conurl)."</loc>\n";
            $sitemap_co .= "		</url>\n";

            sitemap_append_collections($content['id'], $conurl, $site, $language);
        }
        /*
        }
    }
    */
        $conurl_rek = get_content_rek($content['id'],$company,$shop,$language_code,$conurl,$language,$site);

    }

    $sitemap_co .= "	</urlset>";

    $handle = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap-content-".strtolower($shop['code'])."-".strtolower($language['code']).".xml", "wb");
    fwrite($handle, $sitemap_co);
    fclose($handle);
    $filename_content = "sitemap-content-".strtolower($shop['code'])."-".strtolower($language['code']).".xml";
    $filenamecontent = "https://".$GLOBALS['base_url']['de'].$filename_content;
    // lastmod verlangt das W3C-Datumsformat. "2026-09-17 21:30:58" ist keines -
    // es fehlt das T zwischen Datum und Zeit und die Zeitzone. date("c") liefert
    // die gueltige Schreibweise.
    $date_now = date("c");

    $sitemap_index_cont = "<sitemap>\n";
    $sitemap_index_cont .="<loc>".$filenamecontent."</loc>\n";
    $sitemap_index_cont .="<lastmod>".$date_now."</lastmod>\n";
    $sitemap_index_cont .="</sitemap>\n";

    $handler_for_sitemap = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap.xml","a");
    fwrite($handler_for_sitemap, $sitemap_index_cont);
    fclose($handler_for_sitemap);
}


function get_content_rek($pid,$company,$shop,$language_code,$conurl,$language,$site) {

    global $sitemap_co;

    $query_rek = "SELECT * FROM main_navigation WHERE main_site_id = " . $site["id"] . " AND main_language_id = " . $language["id"] . " AND parent_id = '".$pid."' ORDER BY sorting ASC";
    $con_result_rek = @mysqli_query($GLOBALS['mysql_con'],$query_rek);
    if (mysqli_num_rows($con_result_rek) > 0) {
        while ($content_rek = mysqli_fetch_array($con_result_rek)) {

            $conurl2 = $content_rek["code"];
            $conurl2 = $conurl.$conurl2."/";

            /*
            $subquery = "SELECT id FROM main_navigation_has_sitepart WHERE main_navigation_id = '".$content_rek['id']."' AND main_sitepart_id = '7'";
            $subresult = @mysqli_query($GLOBALS['mysql_con'],$subquery);

            if (mysqli_num_rows($subresult) == 0) {

                //hat inhalt?
                $subquery2 = "SELECT id FROM main_navigation_has_sitepart WHERE main_navigation_id = '".$content_rek['id']."'";
                $subresult2 = @mysqli_query($GLOBALS['mysql_con'],$subquery2);

                if (mysqli_num_rows($subresult2) > 0) {
            */
            if (sitemap_navigation_has_own_page($content_rek)) {
                $sitemap_co .= "		<url>\n";
                $sitemap_co .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].customizeUrl(true, $site, $language)."/".$conurl2)."</loc>\n";
                $sitemap_co .= "		</url>\n";

                sitemap_append_collections($content_rek['id'], $conurl2, $site, $language);
            }
            /*)
                }
            }
            */
            $conurl_rek = get_content_rek($content_rek['id'],$company,$shop,$language_code,$conurl2,$language,$site);
        }
    }
}

$sitemap_c = "";
function generate_categories($language_code,$shop_code,$company) {

    global $sitemap_c;

    $query = "SELECT * FROM shop_shop WHERE company = '" . $company . "' AND code = '" . $shop_code . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $shop = mysqli_fetch_assoc($result);


    $query = "SELECT * FROM main_language WHERE company = '".$company."' AND shop_code = '".$shop_code."' AND shop_language_code = '".$language_code."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $language = mysqli_fetch_assoc($result);

    $query = "SELECT * FROM main_site WHERE id = '".$language['main_site_id']."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $site = mysqli_fetch_assoc($result);

    $query = "SELECT code, name, line_no, parent_line_no AS plo FROM shop_category WHERE company='".$company."' AND shop_code='".$shop_code."' AND language_code='".$language_code."' AND parent_line_no = 0 AND active = 1";
    $cat_result = @mysqli_query($GLOBALS['mysql_con'],$query);


    $sitemap_c = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $sitemap_c .= "	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    /*$home_query = "SELECT code FROM main_navigation WHERE id = '".$language['std_main_navigation_id']."'";
    $home_result = @mysqli_query($GLOBALS['mysql_con'],$home_query);
    $home_code = mysql_fetch_row($home_result);

    $sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".$GLOBALS['base_url'][$language['code']].customizeUrl(true, $site, $language)."/".$home_code[0]."/</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";*/

    /*$sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url'][$language['code']].$site['code']."/".$language['code']."/shop/?shop_category=search")."</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";*/

    /*$sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/?shop_category=order")."</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";

    $sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/?shop_category=basket")."</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";*/

    /*$sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/?shop_category=account")."</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";*/

    /*$sitemap_c .= "		<url>\n";
    $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/?shop_category=favorites")."</loc>\n";
    $sitemap_c .= "			<changefreq>daily</changefreq>\n";
    $sitemap_c .= "			<priority>1</priority>\n";
    $sitemap_c .= "		</url>\n";*/



    while ($category = mysqli_fetch_array($cat_result)) {
        $seek = array('ä','ö','ü','ß','*',' ','.',',','/','\\','"',"''","'","%");
        $replace = array('ae','oe','ue','ss','-','-','-','-','','','&quot;','&quot;','',"proz");
        $caturl = str_replace($seek,$replace,$category["code"]);
        $caturl = str_replace('--','-',$caturl);
        $caturl = htmlspecialchars($caturl, ENT_QUOTES, "UTF-8");
        $caturl = strtolower($caturl."/");
        $sitemap_c .= "		<url>\n";
        $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/".$caturl."/")."</loc>\n";
        $sitemap_c .= "			<changefreq>daily</changefreq>\n";
        $sitemap_c .= "			<priority>1</priority>\n";
        $sitemap_c .= "		</url>\n";
        $caturl_rek = get_categories_rek($category['line_no'],$company,$shop,$language_code,$caturl,$language,$site);
    }

    $sitemap_c .= "	</urlset>";

    $handle = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap-categories-".strtolower($shop['code'])."-".strtolower($language['code']).".xml", "wb");
    fwrite($handle, $sitemap_c);
    fclose($handle);
    $filename_categories = "sitemap-categories-".strtolower($shop['code'])."-".strtolower($language['code']).".xml";
    $filenamecategories = "https://".$GLOBALS['base_url']['de'].$filename_categories;
    // lastmod verlangt das W3C-Datumsformat. "2026-09-17 21:30:58" ist keines -
    // es fehlt das T zwischen Datum und Zeit und die Zeitzone. date("c") liefert
    // die gueltige Schreibweise.
    $date_now = date("c");

    $sitemap_index_cat = "<sitemap>\n";
    $sitemap_index_cat .="<loc>".$filenamecategories."</loc>\n";
    $sitemap_index_cat .="<lastmod>".$date_now."</lastmod>\n";
    $sitemap_index_cat .="</sitemap>\n";

    $handler_for_sitemap = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap.xml","a");
    fwrite($handler_for_sitemap, $sitemap_index_cat);
    fclose($handler_for_sitemap);
}

function get_categories_rek($plo,$company,$shop,$language_code,$caturl,$language,$site) {

    global $sitemap_c;

    $query_rek = "SELECT code, name, line_no, parent_line_no AS plo FROM shop_category WHERE company='".$company."' AND shop_code='".$shop['code']."' AND language_code='".$language_code."' AND parent_line_no = '".$plo."' AND active = 1";
    $cat_result_rek = @mysqli_query($GLOBALS['mysql_con'],$query_rek);
    if (@mysqli_num_rows($cat_result_rek) > 0) {
        while ($category_rek = mysqli_fetch_array($cat_result_rek)) {

            $seek = array('ä','ö','ü','ß','*',' ','.',',','/','\\','"',"''","'","%",'+');
            $replace = array('ae','oe','ue','ss','-','-','-','-','','','&quot;','&quot;','',"proz",'plus');

            $caturl2 = str_replace($seek,$replace,$category_rek["code"]);
            $caturl2 = str_replace('--','-',$caturl2);
            $caturl2 = htmlspecialchars($caturl2, ENT_QUOTES, "UTF-8");
            $caturl2 = strtolower($caturl.$caturl2."/");

            //$caturl2 = $caturl.$category_rek["code"]."/";
            $sitemap_c .= "		<url>\n";
            $sitemap_c .= "			<loc>https://".str_replace("//","/",$GLOBALS['base_url']['de'].$site['code']."/".$language['code']."/shop/".$caturl2."/")."</loc>\n";
            $sitemap_c .= "			<changefreq>daily</changefreq>\n";
            $sitemap_c .= "			<priority>1</priority>\n";
            $sitemap_c .= "		</url>\n";
            $caturl_rek = get_categories_rek($category_rek['line_no'],$company,$shop,$language_code,$caturl2,$language,$site);
        }
    }
}

function generate_items($language_code,$shop_code,$company) {

    $query = "SELECT * FROM shop_shop WHERE company = '" . $company . "' AND code = '" . $shop_code . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $shop = mysqli_fetch_assoc($result);

    $query = "SELECT * FROM main_language WHERE company = '".$company."' AND shop_code = '".$shop_code."' AND shop_language_code = '".$language_code."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $language = mysqli_fetch_assoc($result);

    $query = "SELECT * FROM main_site WHERE id = '".$language['main_site_id']."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $site = mysqli_fetch_assoc($result);


    $query = "SELECT 
				shop_view_active_item.*
			FROM 
				shop_view_active_item 
			WHERE 
				shop_view_active_item.shop_code = '".$shop['code']."' 
				AND shop_view_active_item.language_code = '".$language_code."' 
				AND shop_view_active_item.company = '".$company."'
			";

    $result = @mysqli_query($GLOBALS['mysql_con'],$query);

    $sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $sitemap .= "	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    while($item = mysqli_fetch_assoc($result)) {

        IF ($parent_item['id'] == '' || $parent_item['id'] == $item['id']) {
            $canonical_card_id = $item['id'];
            IF ($item['main_category_line_no'] <> 0) {
                $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE company='".$company."' AND shop_code='".$shop['code']."' AND language_code='".$language_code."' AND line_no = '".$item['main_category_line_no']."' AND active = 1";
                $cat_tmp_result = @mysqli_query($GLOBALS['mysql_con'],$cat_query);
                if (@mysqli_num_rows($cat_tmp_result) < 1) {
                    $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE company='".$company."' AND shop_code='".$shop['code']."' AND language_code='".$language_code."' AND line_no = (SELECT category_line_no FROM shop_item_has_category WHERE active = 1 AND item_no = '".$item['item_no']."' GROUP BY category_line_no ORDER BY category_line_no ASC LIMIT 1)";
                }
            } ELSE {
                $cat_query = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE company='".$company."' AND shop_code='".$shop['code']."' AND language_code='".$language_code."' AND line_no = (SELECT category_line_no FROM shop_item_has_category WHERE active = 1 AND item_no = '".$item['item_no']."' GROUP BY category_line_no ORDER BY category_line_no ASC LIMIT 1)";
            }

            $cat_result = @mysqli_query($GLOBALS['mysql_con'],$cat_query);
            $itemcategory = @mysqli_fetch_array($cat_result);
            $caturl = array();
            $seek = array('ä','ö','ü','ß','*',' ','.',',','/','\\','"',"''","'","%","+");
            $replace = array('ae','oe','ue','ss','-','-','-','-','','','&quot;','&quot;','',"proz","plus");
            $caturl[0] = str_replace($seek,$replace,$itemcategory["code"]);
            $caturl[0] = htmlspecialchars($caturl[0], ENT_QUOTES, "UTF-8");
            $caturl[0] = $caturl[0]."/";
            $i = 1;
            WHILE ($itemcategory['plo'] > 0){
                $cat_query_rek = "SELECT code, name, parent_line_no AS plo FROM shop_category WHERE line_no = '".$itemcategory['plo']."'";
                $cat_res_rek = @mysqli_query($GLOBALS['mysql_con'],$cat_query_rek);
                $itemcategory = @mysqli_fetch_array($cat_res_rek);
                IF ($itemcategory['name'] != '') {
                    $seek = array('ä','ö','ü','ß','*',' ','.',',','/','\\','"',"''","'","%","+");
                    $replace = array('ae','oe','ue','ss','-','-','-','-','','','&quot;','&quot;','',"proz","plus");
                    $caturl[$i] = str_replace($seek,$replace,$itemcategory["code"]);
                    $caturl[$i] = htmlspecialchars($caturl[$i], ENT_QUOTES, "UTF-8");
                    $caturl[$i] = $caturl[$i]."/";
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

            $default_category = get_category($GLOBALS['shop']['company'], $GLOBALS['shop']['category_source'], $GLOBALS['shop_language']['code'], $GLOBALS["shop_language"]["default_category"]);
            if ( ($default_category !== false) && is_array($default_category) ) {
                $seek       = array('ä', 'ö', 'ü', 'ß', '*', ' ', '.', ',', '/', '\\', '"', "''", "'");
                $replace    = array('ae', 'oe', 'ue', 'ss', '-', '-', '-', '-', '', '', '&quot;', '&quot;', '');
                $tmp_cat_url = str_replace($seek, $replace, $default_category["code"]);
                $tmp_cat_url = htmlspecialchars($tmp_cat_url, ENT_QUOTES, "UTF-8");
                $tmp_cat_url = $tmp_cat_url . "/";

                $catstring = $tmp_cat_url;
            }

            $seek = array('ä','ö','ü','ß','*',' ','.',',','/','\\','"',"''","'","%","+");
            $replace = array('ae','oe','ue','ss','-','-','-','-','','','&quot;','&quot;','',"proz","plus");
            $itemdescription = str_replace($seek,$replace,$item["description"]);
            $itemdescription = htmlspecialchars($itemdescription, ENT_QUOTES, "UTF-8");
            $itemdescription = str_replace("&Acirc;","",$itemdescription);
            $itemdescription = str_replace("&acirc;","",$itemdescription);
            $itemdescription = trim($itemdescription, "-");
            $base_string = $GLOBALS['base_url']['de'];
            $request_string = $site['code']."/".$language['code']."/shop/".$catstring.$itemdescription."/?card=".$canonical_card_id;
            $request_string= str_replace("//","/",$request_string);
            $canonical = "https://".$base_string.$request_string;
            $canonical = str_replace(" ","-",$canonical);
            $canonical = str_replace("--","-",$canonical);
            $canonical = strtolower($canonical);

            $sitemap .= "		<url>\n";
            $sitemap .= "			<loc>".$canonical."</loc>\n";

            $sitemap .= "			<changefreq>daily</changefreq>\n";
            $sitemap .= "			<priority>1</priority>\n";
            $sitemap .= "		</url>\n";

        }
    }

    $sitemap .= "	</urlset>";

    $handle = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap-items-".strtolower($shop['code'])."-".strtolower($language['code']).".xml", "wb");
    fwrite($handle, $sitemap);
    fclose($handle);
    $filename_items = "sitemap-items-".strtolower($shop['code'])."-".strtolower($language['code']).".xml";
    $filenameitems = "https://".$GLOBALS['base_url']['de'].$filename_items;
    // lastmod verlangt das W3C-Datumsformat. "2026-09-17 21:30:58" ist keines -
    // es fehlt das T zwischen Datum und Zeit und die Zeitzone. date("c") liefert
    // die gueltige Schreibweise.
    $date_now = date("c");
    $sitemap_index = "<sitemap>\n";
    $sitemap_index .="<loc>".$filenameitems."</loc>\n";
    $sitemap_index .="<lastmod>".$date_now."</lastmod>\n";
    $sitemap_index .="</sitemap>\n";
    $handler_for_sitemap = fopen(rtrim(dirname(dirname(__DIR__)),'/\\') . "/sitemap.xml","a");
    fwrite($handler_for_sitemap, $sitemap_index);
    fclose($handler_for_sitemap);
}

function get_category( $company, $shop_code, $language_code, $category_line_no ) {
    $query  = "SELECT * FROM shop_category
			  WHERE line_no = '" . $category_line_no . "'
			  	AND company = '" . $company . "'
    		  	AND shop_code = '" . $shop_code . "'
    		  	AND language_code = '" . $language_code . "'
			  	LIMIT 1";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $category = mysqli_fetch_assoc($result);
        return $category;
    }
}


if ( isset($_REQUEST["generate_sitemap"])) {

    db_connect();
    $_GET  = secure_array($_GET, TRUE);
    $_POST = secure_array($_POST, FALSE);
    $_REQUEST = secure_array($_REQUEST, FALSE);

    $language_query = "SELECT shop_language_code,company,shop_code FROM main_language WHERE id = '".$site['std_main_language_id']."'";
    $language_result = @mysqli_query($GLOBALS['mysql_con'],$language_query);

    $GLOBALS['base_url']['de'] = $_SERVER["SERVER_NAME"]."/";

    init_sitemap_file();

    while($language_array = mysqli_fetch_assoc($language_result)){
        // Kategorien und Artikel nur, wenn die Sprache ueberhaupt an einem Shop
        // haengt. Ohne Shop entstanden hier zwei Sitemaps ohne eine einzige
        // Adresse - und standen trotzdem im Index, den Suchmaschinen abholen.
        if ($language_array['shop_code'] <> '') {
            generate_categories($language_array['shop_language_code'],$language_array['shop_code'],$language_array['company']);
            generate_items($language_array['shop_language_code'],$_REQUEST['shop_code'],$_REQUEST['company']);
        }
        generate_content($language_array['shop_language_code'],$_REQUEST['shop_code'],$_REQUEST['company']);
    }

    close_sitemap_file();

}

$messages = array();
$error    = FALSE;

$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_sitemap";

?>


<ul class="toolbar_menu">
    <?= button("down", $translation->get("update"), $formname, " document.getElementById('".$formname."').submit()"); ?>
</ul>


<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('sitemap'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" >

        <input type="hidden"  id="generate_sitemap" name="generate_sitemap" value = "generate_sitemap" >  </input>

    </form>

</div>