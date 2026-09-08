<?
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module/dcshop/common/shop_functions.inc.php';
// Testprogramm
function testprogramm( $sitepart ) {
    if ($_GET["id" . $sitepart["navigation_has_sitepart_id"]] <> "a") {
        echo "<a href=\"" . ml($sitepart, "id", "a") . "\">a</a> - ";
    } else {
        echo "a - ";
    }
    if ($_GET["id" . $sitepart["navigation_has_sitepart_id"]] <> "b") {
        echo "<a href=\"" . ml($sitepart, "id", "b") . "\">b</a> - ";
    } else {
        echo "b - ";
    }
    if ($_GET["id" . $sitepart["navigation_has_sitepart_id"]] <> "c") {
        echo "<a href=\"" . ml($sitepart, "id", "c") . "\">c</a>";
    } else {
        echo "c";
    }
    echo "<br /><br />";
}

// Layout Area integration - 10. August 2009 MH
function get_content( $layout_area_id, $is_component_only = FALSE, &$IOCContainer = null, $category = null, $navigation = null) {
    require_once CMS_PATH . "common/classes/Siteparts.php";
    $allSiteparts = \DynCom\dc\common\classes\Siteparts::get();
    $page_id      = (int)$GLOBALS["navigation"]['forward_page_id'];

    if (null !== $navigation) {
        $page_id      = (int)$navigation["forward_page_id"];
    }
    if (null !== $category && !empty($category["in_use_with_page_id"])) {
        $page_id      = (int)$category["in_use_with_page_id"];
        $GLOBALS['shopping_world_page_id'] = $page_id;
    }

    if ($page_id <= 0) {
        return;
    }

    $layout_area_code = $layout_area_id;
    $layout_area_entry_query  = "SELECT id FROM main_layout_area WHERE code = '" . $layout_area_id . "' AND main_layout_id = '" . $GLOBALS["layout"]["id"] . "'";
    $layout_area_entry_result = @mysqli_query($GLOBALS['mysql_con'], $layout_area_entry_query);

    $layout_area_entry = @mysqli_fetch_array($layout_area_entry_result);
    $layout_area_id    = $layout_area_entry["id"];



    show_component_content((int)$layout_area_id, $IOCContainer, $layout_area_code, $page_id);
    if ($is_component_only === TRUE) {
        return;
    }


    $query  = "SELECT * FROM main_page_link WHERE main_page_id = " . (int)$page_id . " AND main_page_link_parent_id = 0 AND layout_area_id = " . (int)$layout_area_id . " AND active = 1 ORDER BY sorting ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {


        while ($sitepart = @mysqli_fetch_array($result)) {
            $backGroundImagePath = $sitepart['background_image_path'];

            if ($sitepart['main_page_group'] == 1) {
                show_group_content($sitepart, $backGroundImagePath, $IOCContainer);
                continue;
            }

            // layout klassen wrapper anzeigen
            $layoutClassIds = get_content_layout_classes($sitepart);
            if(count($layoutClassIds)) {
                echo "<div class=\"";
                $layout_class_codes = array();
                foreach($layoutClassIds as $layoutClassId) {
                    $layout_class_codes[] = $GLOBALS["layout_classes"][$layoutClassId]['code'];
                }
                echo join (' ', $layout_class_codes);
                echo "\">";
            }

            if ($sitepart['main_sitepart_id'] > 0) {
                show_sitepart_content($sitepart['main_sitepart_id'], $sitepart['main_sitepart_header_id'], $sitepart['id'],$IOCContainer, $backGroundImagePath);
            } elseif ($sitepart['main_collection_setup_id'] > 0) {
                show_collection_setup_content($sitepart);
            }

            // layout klassenwrapper beenden
            if(count($layoutClassIds)) {
                echo "</div>";
            }
        }
    }

}

function forward_intern( $forwardid ) {
    $link = get_link_to_navigation($forwardid);
    if (isset($_GET["live_edit"]) && $_GET["live_edit"] == 1) {
        $link .= "?live_edit=1";
    }
    headerFunctionBridge("Location: " . $link);
    exit();
}

function forward_extern( $url ) {
    headerFunctionBridge("Location: " . $url);
    exit();
}

function forward_url_intern ( $url ) {
	if(substr($url, 0, 1) != "/") {
		$url = "/" . $url;
	}
 headerFunctionBridge("Location: " . $url);
    exit();
}

function forward_url_shop_category ( $categoryLineNo ) {
    $company = $GLOBALS['language']['company'];
    $shop_code = $GLOBALS['language']['shop_code'];
    $language_code = $GLOBALS['language']['shop_language_code'];
    $shop = get_shop($company,$shop_code);
    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;
    $category = get_category($company,$category_shop_code,$language_code,$categoryLineNo);
    $url = get_simple_category_path($category,$company,$category_shop_code,$language_code);
    headerFunctionBridge("Location: " . $url);
    exit();
}

function show_group_content( $_group , $backgroundImage = false, &$IOCContainer= null) {

    $layout_shop_type = "";
    if($GLOBALS["shop"]["shop_typ"] == "0" && $GLOBALS["shop_customer"]["office_shop"] == "1" ) {//Händlerportal
        $layout_shop_type = " AND ((main_page_link.layout_shop_type = 2) OR (main_page_link.layout_shop_type IS NULL) OR (main_page_link.layout_shop_type = 0)) ";

    } elseif ($GLOBALS["shop"]["shop_typ"] == "0" && $GLOBALS["shop_customer"]["office_shop"] == "0"){
        $layout_shop_type = " AND ((main_page_link.layout_shop_type = 1) OR (main_page_link.layout_shop_type IS NULL) OR (main_page_link.layout_shop_type = 0)) ";
    }

    if($GLOBALS["shop"]["shop_typ"] == "0") {
        $query = "SELECT main_page_link.* 
                    FROM main_page_link
                    INNER JOIN shop_permissions_group_link ON 
                      shop_permissions_group_link.company = '". $GLOBALS["language"]["company"] ."'
                      AND shop_permissions_group_link.type = 0
                      AND shop_permissions_group_link.customer_no = '" . $GLOBALS['shop_customer']['customer_no'] . "'
                    WHERE main_page_link.main_page_link_parent_id = " . $_group['id'] . " 
                        AND main_page_link.active = 1 
                        " . $layout_shop_type . "
                        AND ((shop_permissions_group_link.permission_group_code = main_page_link.layout_permission_group_code
                            OR shop_permissions_group_link.permission_group_code IS NULL)
                            OR main_page_link.layout_permission_group_code = '')
                    GROUP BY main_page_link.id
                    ORDER BY main_page_link.sorting ASC";
    } else {
        $query  = "SELECT * FROM main_page_link 
                    WHERE main_page_link_parent_id = " . $_group['id'] . " 
                        AND active = 1
                    ORDER BY sorting ASC"; //Variable mit übergeben
    }

    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {

        $group_layout_classes = get_content_layout_classes($_group);
        if($backgroundImage == '') {
            echo "<div class=\"group ";
        } else {
            echo "<div style=\"background-image: url(".$backgroundImage.")\" class=\"group bg-cover ";
        }
        $layout_class_codes = array();
        foreach($group_layout_classes as $layoutClassId) {
            $layout_class_codes[] = $GLOBALS["layout_classes"][$layoutClassId]['code'];
        }
        echo join (' ', $layout_class_codes);
        echo "\">";

        echo "<div class='" . $_group["main_page_group_code"] . "'>";
        echo $_group['main_page_group_link'] != "" ? "<div class='link' onClick='window.location.href=\"".$_group['main_page_group_link']."\"'>" : "";

        while ($sitepart = @mysqli_fetch_array($result)) {

            if ($sitepart['main_page_group'] == 1) {
                show_group_content($sitepart, $sitepart['background_image_path'], $IOCContainer);
                continue;
            }

            // layout klassen wrapper anzeigen
            $layoutClassIds = get_content_layout_classes($sitepart);
            if(count($layoutClassIds)) {
                echo "<div class=\"";
                $layout_class_codes = array();
                foreach($layoutClassIds as $layoutClassId) {
                    $layout_class_codes[] = $GLOBALS["layout_classes"][$layoutClassId]['code'];
                }
                echo join (' ', $layout_class_codes);
                echo "\">";
            }

            $backGroundImagePath = $sitepart['background_image_path'];
            if ($sitepart['main_sitepart_id'] > 0) {
                show_sitepart_content($sitepart['main_sitepart_id'], $sitepart['main_sitepart_header_id'], $sitepart['id'], $IOCContainer, $backGroundImagePath);

            } elseif ($sitepart['main_collection_setup_id'] > 0) {
                show_collection_setup_content($sitepart);
            }

            // layout klassenwrapper beenden
            if(count($layoutClassIds)) {
                echo "</div>\n";
            }
        }

        echo $_group['main_page_group_link'] != "" ? "</div>" : "";
        echo "</div></div>";
    }
}

function show_sitepart_content( $sitepart_id, $sitepart_header_id, $link_id = NULL, &$IOCContainer = null, $background_image_path = null ) {
    require_once CMS_PATH . "common/classes/Siteparts.php";
    $allSiteparts = \DynCom\dc\common\classes\Siteparts::get();
    $sitepartData = $allSiteparts[$sitepart_id];
    // container um das sitepart, daurch ansprechbar mit javascript
    if ($GLOBALS["live_edit_mode"] === TRUE) {
        $translation = \DynCom\dc\common\classes\Registry::get("translation");
        echo "<div class=\"live_edit_container\">";

        if ($sitepart_id != 1) {
            // Den Button nur einblenden wenn es sich nicht um Textcontent handelt
            echo "
			<span class=\"live_edit_overlay\" data-linkid=\"" . $link_id . "\" data-sitepartid=\"" . $sitepart_id . "\" data-sitepartheaderid=\"" . $sitepart_header_id . "\" data-pageid=\"" . (isset($GLOBALS['shopping_world_page_id']) && !empty($GLOBALS['shopping_world_page_id']) ? $GLOBALS['shopping_world_page_id'] : $GLOBALS["page"]['id']) . "\">
				" . sprintf($translation->get("live_edit_button_sitepart"), $sitepartData['description']) . "
			</span>";
        }

    }

    $GLOBALS['background_image_path'] = '';
    if($background_image_path != '' && $background_image_path != null) {
        $GLOBALS['background_image_path'] = $background_image_path;
    }

    require_once(realpath(MODULE_PATH . $sitepartData['folder']) . DIRECTORY_SEPARATOR . $sitepartData['code'] . ".php");
    $function = $sitepartData['code'] . "_show";
    $refFunc = new ReflectionFunction($function);
    $noOfParams = $refFunc->getNumberOfParameters();
    $noOfReqParams = $refFunc->getNumberOfRequiredParameters();
    if ($sitepart_header_id > 0) {
        if($noOfParams === 1) {
            $function($sitepart_header_id);
        } elseif($noOfParams > 1) {
            $function($sitepart_header_id,$IOCContainer);
        }
    } elseif($noOfReqParams === 0) {
        if($noOfParams > 0 && isset($IOCContainer)) {
            $function($IOCContainer);
        } else {
            $function();
        }
    }

    if ($GLOBALS["live_edit_mode"] === TRUE) {
        echo "</div>";
    }
}

function show_collection_content( $collection_id ) {
    $query    = "SELECT main_collection_setup_id FROM main_collection WHERE id = '" . $collection_id."'";
    $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row      = @mysqli_fetch_array($result);
    $setup_id = $row['main_collection_setup_id'];

    show_collection_setup_content($setup_id, $collection_id);
}

function show_collection_setup_content( $collection_setup, $group_link_table = "main_page_collection_group_link" ) {
    require_once(MODULE_PATH . "collection/collection_config.inc.php");
    require_once CMS_PATH . "common/classes/Siteparts.php";
    $allSiteparts = \DynCom\dc\common\classes\Siteparts::get();

    $collectionPreview = FALSE;
    $where             = "";
    $limit             = "";
    $fullView          = FALSE;
    $container_class   = "collection_list";

    if ($collection_setup['main_collection_list'] == 2) {
        // hier wird die kollektions vorschau angezeigt, entweder 1 kollektion oder eine bestimmte anzahl
        $collectionPreview = TRUE;

        if ($collection_setup['main_collection_view_type'] == 0) {
            // eine bestimmte kollektion
            $where = " and id = " . $collection_setup['main_collection_id'];
        } elseif($collection_setup['main_collection_view_type'] == 1) {
            // liste an kollektionen mit einer bestimmten anzahl
            $limit = " limit 0, " . $collection_setup['main_collection_items'];
        } elseif($collection_setup['main_collection_view_type'] == 2) {
            // calendar
        }
    }

    if ($collectionPreview === FALSE && (isset($_GET['collection_id']) && (int)$_GET['collection_id'] > 0)) {

        $showedCollections = \DynCom\dc\common\classes\Registry::get("showedCollections");
        if($showedCollections === null) {
            $showedCollections = array();
        }

        if(in_array($_GET['collection_id'], $showedCollections)) {
            return; // ---> collection nicht nochmal anzeigen
        }

        $showedCollections[] = $_GET['collection_id'];
        \DynCom\dc\common\classes\Registry::set("showedCollections", $showedCollections);

        $collection_id   = $_GET['collection_id'];
        $where           = " and id = " . $collection_id;
        $fullView        = TRUE;
        $container_class = "collection_full";
    }

    $showRegistration = FALSE;
    if ((isset($_GET['registration']) && (int)$_GET['registration'] == 1)) {
        $showRegistration = TRUE;
    }

    if ($fullView === false){
        $query = "SELECT * FROM main_collection_setup_group WHERE main_collection_setup_id = ". (int)$collection_setup['main_collection_setup_id']."";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) > 0) {
            echo "<div class=\"group_filter_area\"><span>".$GLOBALS["tc"]["group_filters"]."</span>";						// --> Wrapper für Gruppen
            $activeall = "";
            if(!isset($_GET["group_filter"]) || (isset($_GET["group_filter"]) && $_GET["group_filter"] == 0)){
                $activeall = "active";
            }
            echo "<div class=\"group_filter ".$activeall."\"><a data-filter=\"*\"  href=\"?group_filter=0\">" . $GLOBALS['tc']['collection_all']. "</a></div>";
            while ($collection_group_data = @mysqli_fetch_array($result)) {
                if(isset($_GET["group_filter"]) && $_GET["group_filter"] == $collection_group_data["id"]) {
                    echo "<div class=\"group_filter active\"><a data-filter=\"collection_group_".$collection_group_data['id']."\" href=\"?group_filter=". $collection_group_data["id"]."\">". $collection_group_data["description"] ."</a></div>";
                } else {
                    echo "<div class=\"group_filter\"><a data-filter=\"collection_group_".$collection_group_data['id']."\" href=\"?group_filter=". $collection_group_data["id"] ."\">". $collection_group_data["description"] ."</a></div>";
                }
            }
            echo "</div>";
        }
    }

    // IN statement zusammenstellen um kollektionen aus gruppen auszulesen
    $where .= get_collection_sql_in_groups($collection_setup, $group_link_table);

    // datum query
    $where .= " AND (validity_from IS NULL OR validity_from <= '" . date("Y-m-d") . "') ";
    $where .= " AND (validity_to   IS NULL OR validity_to   >= '" . date("Y-m-d") . "') ";

    // active query
    $where .= " AND description != '' ";

    $query       = "SELECT * FROM main_collection WHERE main_collection_setup_id = " . $collection_setup['main_collection_setup_id'] . $where . " ORDER BY sorting ASC " . $limit;
    $result      = @mysqli_query($GLOBALS['mysql_con'], $query);
    $collections = array();
    while ($row = @mysqli_fetch_array($result)) {
        $collections[$row['id']] = $row;
        $query2       = "SELECT * FROM main_collection_group_link left join main_collection_setup_group on (main_collection_setup_group.id = main_collection_group_link.main_collection_setup_group_id) where active = 1 and main_collection_id = " . $row['id'];
        $result2      = @mysqli_query($GLOBALS['mysql_con'], $query2);
        $i = 0;
        if(!isset($collections[$row['id']]['groups'])) {
            $collections[$row['id']]['groups'] = array();
        }
        while ($row2 = @mysqli_fetch_array($result2)) {
            $collections[$row['id']]['groups'][$i]['id'] = $row2['id'];
            $collections[$row['id']]['groups'][$i]['description'] = $row2['description'];
            $i++;
        }
    }

    if (count($collections) == 0) {
        return;                                                                //--> Keine Kollektion mit der ID gefunden
    }

    if ($fullView === TRUE &&
        $showRegistration === TRUE &&
        $collections[$collection_id]['registration'] == 1 &&
        (int)$collections[$collection_id]['registration_contactform_id'] > 0
    ) {
        // Anmeldeformular anzeigen
        show_sitepart_content(3, $collections[$collection_id]['registration_contactform_id']);
        return;                                                                //--> Hier wird nur das Anmeldeformular angezeigt
    }

    // alle felder sammeln
    $setup_fields = array();
    $query        = "SELECT * FROM main_collection_setup_content WHERE main_collection_setup_id = " . $collection_setup['main_collection_setup_id'] . " ORDER BY sorting ASC";
    $result       = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result)) {
        $setup_fields[$row['id']] = $row;
    }

    if (count($setup_fields) == 0) {
        return;                                                                //--> Keine Felder zum anzeigen verfuegbar
    }

    $collection_lines = array();
    $query            = "SELECT * FROM main_collection_link WHERE main_collection_id IN (" . join(",", array_keys($collections)) . ")";
    $result           = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result)) {
        // aufbau: z.B. $collection_lines[1][8] bedeutet:
        // 1 = main_collection_id
        // 8 = main_collection_setup_content_id

        if (!isset($collection_lines[$row['main_collection_id']])) {
            $collection_lines[$row['main_collection_id']] = array();
        }

        if (!isset($collection_lines[$row['main_collection_id']][$row['main_collection_setup_content_id']])) {
            $collection_lines[$row['main_collection_id']][$row['main_collection_setup_content_id']] = array();
        }

        $collection_lines[$row['main_collection_id']][$row['main_collection_setup_content_id']] = $row;
    }

    if (count($collection_lines) == 0) {
        return;                                                                //--> Es wurde noch kein einziges Feld ausgefuellt
    }

    // Ab hier wurde mindestens eine Zuordnung zur collection gemacht

    // setup code auslesen
    $query             = "SELECT code,linked,description FROM main_collection_setup WHERE id = " . $collection_setup['main_collection_setup_id'];
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row               = @mysqli_fetch_array($result);
    $code              = $row['code'];
    $linked            = $row['linked'];
    $setup_description = $row['description'];

    $createFullViewLink = "%s";

    $live_edit_class = "";
    if ($GLOBALS["live_edit_mode"] === TRUE) {
        $live_edit_class = " live_edit_container live_edit_collection";
    }

    // datepicker, bevor Ausgaben kommen
    if($collection_setup['main_collection_view_type'] == 2) {
        include(MODULE_PATH . "collection/show_datepicker.inc.php");
        return; // keine ausgaben mehr
    }

    if($linked) {
        $is_linked = " linked ";
    }else{
        $is_linked = "";
    }

    if(!$fullView){
        $view = "isList";
        $collection_full_class =  "";
    }else{
        $view = "isFullView";
        $collection_full_class =  "collection_full";
    }

    echo "<div class=\"collection_wrapper ".$view." ".$collection_full_class."\">";


    if(file_exists($_SERVER['DOCUMENT_ROOT']."/dc/frontend/collections/collection_".$code.".php")){
        include('collections/collection_'.$code.'.php');
    }else {
        foreach ($collections as $collection_id => $collectionData) {

            $grouplabels = "";
            $groupclasses = "";

            if(isset($collectionData['groups'])) {
                $grouplabels .= "<div class='collection_group_labels'>";
                foreach ($collectionData['groups'] as $groupid => $groupdata) {
                    $grouplabels .= "<span class='collection_group_label'>".$groupdata['description']."</span><br/>";
                    $groupclasses .= " collection_group_" . $groupdata['id'];
                }
                $grouplabels .= "</div>";
            }

            echo "<div class=\"" . $container_class . " " . $code . $live_edit_class . $is_linked . $groupclasses . "\"><div class=\"collection_container\">";
            if ($GLOBALS["live_edit_mode"] === TRUE) {
                $translation = \DynCom\dc\common\classes\Registry::get("translation");
                echo "
				<span class=\"live_edit_overlay\" data-collection=\"true\" data-collection_setup_id=\"" . $collectionData['main_collection_setup_id'] . "\" data-collectionid=\"" . $collection_id . "\" data-pageid=\"" . (isset($GLOBALS['shopping_world_page_id']) && !empty($GLOBALS['shopping_world_page_id']) ? $GLOBALS['shopping_world_page_id'] : $GLOBALS["page"]['id']) . "\">
					" . sprintf($translation->get("live_edit_button_sitepart"), $setup_description) . "
				</span>";
            }

            foreach ($setup_fields as $fieldId => $fieldData) {
                if (!isset($collection_lines[$collection_id])) {
                    continue;
                }

                if (!isset($collection_lines[$collection_id][$fieldId])) {
                    continue;
                }

                if ($fullView === FALSE && $fieldData['is_teaser'] == 0) {
                    continue;
                }

                if ($linked == 1 && $fullView === FALSE) {
                    if ($collectionPreview === TRUE) {
                        // weiterleitung zu einer anderen seite
                        $query = "SELECT * FROM main_page_link WHERE main_collection_setup_id = '".$collection_setup['main_collection_setup_id']."' AND main_collection_view_type = 0 LIMIT 1";
                        $result = mysqli_query($GLOBALS["mysql_con"],$query);
                        if (@mysqli_num_rows($result) == 1) {
                            $mainPageLink = @mysqli_fetch_array($result);
                            $query = "SELECT * FROM main_navigation WHERE forward_page_id = '".$mainPageLink['main_page_id']."' LIMIT 1";
                            $result = mysqli_query($GLOBALS["mysql_con"],$query);
                            if (@mysqli_num_rows($result) == 1) {
                                $mainNavigation = @mysqli_fetch_array($result);
                                $collection_setup['main_collection_page_list_id'] = $mainNavigation["id"];
                            }
                        }
                        $fullviewLink = get_link_to_navigation($collection_setup['main_collection_page_list_id']) . get_collection_rewrite($collectionData['description'], $collection_id);
                    } else {
                        // gleiche seite nochmal laden
                        $fullviewLink = get_link_to_navigation($GLOBALS["navigation"]['id']) . get_collection_rewrite($collectionData['description'], $collection_id);
                    }

                    $createFullViewLink = '<a href="' . $fullviewLink . '" />%s</a>';
                }

                $line = $collection_lines[$collection_id][$fieldId];

                echo "<div class=\"collection_content " . $fieldData['code'] . "\">";
                switch ($fieldData['fieldtype']) {
                    case 'siteparts':

                        if($linked == 1 && $fullView === FALSE) {
                            echo '<a href="' . $fullviewLink . '" />';
                        }

                        show_sitepart_content($line['main_sitepart_id'], $line['main_sitepart_header_id']);

                        if($linked == 1 && $fullView === FALSE) {
                            echo '</a>';
                        }

                        break;

                    case 'standard':
                    default:
                        if ($fieldData['type_id'] == 4) { // link
                            echo "<a href=\"" . $line['data'] . "\" target=\"_blank\">" . $line['data'] . "</a>";
                            break;
                        }

                        if ($fieldData['type_id'] == 5) { // bild
                            if ($line['data'] != "" && file_exists(ROOT_PATH . PATH_ORIGINAL_FRONTEND_COLLECTION . $line['data'])) {
                                echo sprintf($createFullViewLink, "<img src=\"" . PATH_ORIGINAL_FRONTEND_COLLECTION . $line['data'] . "\" />");
                            }

                            if($fieldData['code'] == "collection_image"){
                                echo $grouplabels;
                            }

                            break;
                        }

                        echo sprintf($createFullViewLink, nl2br($line['data']));
                        break;
                }

                echo "</div>";
            }
            if ($linked == 1 && $fullView === FALSE) {
                echo "<div class='collection_link'><a class='button' href='".$fullviewLink."'>".$GLOBALS['tc']['learn_more']."</a></div>";
            }

            echo "</div></div>";
        }

        // Anmeldelink anzeigen
        if ($fullView === TRUE &&
            $collections[$collection_id]['registration'] == 1 &&
            (int)$collections[$collection_id]['registration_contactform_id'] > 0
        ) {
            echo "<div class=\"collection_register\"><h2><a href=\"" . ml2(array('registration' => 1)) . "\">Anmeldung</a></h2></div><br />\n";
        }
    }

    echo "</div>";
}

function get_inactive_components( $page_id ) {

    $default_inactive = array();
    $query            = "SELECT id FROM main_component WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) AND default_active = 0";
    $result           = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($row = @mysqli_fetch_array($result)) {
            $default_inactive[] = $row['id'];
        }
    }

    $component_ids = array();
    $query         = "SELECT main_component_id FROM main_page_component_include_link WHERE main_page_id = '" . (int)$page_id . "' AND active = 0";
    $result        = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($row = @mysqli_fetch_array($result)) {
            $component_ids[] = $row['main_component_id'];
        }
    }

    $allComponents = array_merge($component_ids, $default_inactive);

    return $allComponents;
}

function show_component_content($layout_area_id, &$IOCContainer = null, $layout_area_code, $pageId = null) {
    require_once CMS_PATH . "common/classes/Siteparts.php";
    $allSiteparts  = \DynCom\dc\common\classes\Siteparts::get();
    $page_id       = (int)$pageId;
    $component_ids = array();

    $query  = "SELECT id FROM main_component WHERE active = 1 AND (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) AND layout_area_id in (select id from main_layout_area where code = '" . $layout_area_code . "')" ;
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);

    if (@mysqli_num_rows($result) == 0) {
        return "";
    }

    while ($row = @mysqli_fetch_array($result)) {
        $component_ids[] = $row['id'];
    }


    $active_components = array();
    $query             = "SELECT active, main_component_id FROM main_page_component_include_link WHERE main_page_id = '" . (int)$page_id . "' AND main_component_id IN (" . join(',', $component_ids) . ")";
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($row = @mysqli_fetch_array($result)) {
            if ($row['active'] == 1) {
                $active_components[] = $row['main_component_id'];
            }
        }
    }

    // componenten filtern
    $final_components = array();
    foreach ($component_ids as $componentId) {
        if (count($active_components) && in_array($componentId, $active_components)) {
            $final_components[] = $componentId;
            continue;
        }

        if (!is_array($GLOBALS["inactive_components"]) || !in_array($componentId, $GLOBALS["inactive_components"])) {
            $final_components[] = $componentId;
            continue;
        }
    }

    if (count($final_components) == 0) {
        return "";
    }

    // componenten codes sammeln
    $componentCodes = array();
    $query          = "SELECT id, code FROM main_component WHERE id IN (" . join(',', $final_components) . ")";
    $result         = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result)) {
        $componentCodes[$row['id']] = $row['code'];
    }

    $query  = "SELECT * FROM main_component_link WHERE main_component_id IN (" . join(',', $final_components) . ")  ORDER BY main_component_id ASC, sorting ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);

    if (@mysqli_num_rows($result) == 0) {
        return "";
    }

    $currentComponent = NULL;

    $live_edit_class = "";
    if ($GLOBALS["live_edit_mode"] === TRUE) {
        $live_edit_class = " live_edit_container live_edit_component";
    }

    while ($sitepart = @mysqli_fetch_array($result)) {

        if ($currentComponent !== NULL && $currentComponent != $sitepart['main_component_id']) {
            echo "</div>";
        }

        if ($currentComponent === NULL || $currentComponent != $sitepart['main_component_id']) {
            echo "<div class=\"component " . $componentCodes[$sitepart['main_component_id']] . $live_edit_class . "\">";
            if ($GLOBALS["live_edit_mode"] === TRUE) {
                $translation = \DynCom\dc\common\classes\Registry::get("translation");
                echo "
					<span class=\"live_edit_overlay\" data-component=\"true\" data-componentid=\"" . $sitepart['main_component_id'] . "\" data-pageid=\"" . (isset($GLOBALS['shopping_world_page_id']) && !empty($GLOBALS['shopping_world_page_id']) ? $GLOBALS['shopping_world_page_id'] : $GLOBALS["page"]['id']) . "\">
						" . $translation->get("edit_component") . "
					</span>";
            }
        }

        if ((int)$sitepart['main_sitepart_id'] > 0) {
            // anzeige eines siteparts im baustein
            $sitepartData = $allSiteparts[$sitepart['main_sitepart_id']];
            require_once(realpath(MODULE_PATH . $sitepartData['folder']) . DIRECTORY_SEPARATOR . $sitepartData['code'] . ".php");
            $function = $sitepartData['code'] . "_show";
            $reflection = new ReflectionFunction($function);
            $noOfParams = $reflection->getNumberOfParameters();
            if($noOfParams > 1) {
                $function($sitepart['main_sitepart_header_id'], $IOCContainer);
            } else {
                $function($sitepart['main_sitepart_header_id']);
            }
        } elseif ((int)$sitepart['main_collection_list'] == 2) {
            // anzeige einer kollektionsvorschau im baustein
            show_collection_setup_content($sitepart, "main_component_collection_group_link");
        }

        $currentComponent = $sitepart['main_component_id'];
    }
    echo "</div>";
}

function create_includes( $main_layout_id, $page_id, $type ) {
    $query             = "
		SELECT id, path, default_active, type
		FROM main_layout_inclusions
		WHERE main_layout_id = '" . $main_layout_id . "' AND type = '" . $type . "'
		ORDER BY sorting ASC";
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    $layout_inclusions = array();
    while ($row = @mysqli_fetch_assoc($result)) {
        $layout_inclusions[$row['id']] = $row;
    }

    if (count($layout_inclusions) == 0) {
        return;
    }

    $includes_link_ids = array();
    $query             = "SELECT * FROM main_page_layout_inclusion_link WHERE main_page_id = '" . $page_id."'";
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_assoc($result)) {
        $includes_link_ids[$row['main_layout_includes_id']] = $row;
    }

    $time = 0;

    foreach ($layout_inclusions as $inclusion_id => $inclusion_row) {
        $include = FALSE;

        if (!isset($includes_link_ids[$inclusion_id]) &&
            $inclusion_row['default_active'] == 1
        ) {
            $include = TRUE;

        } elseif (isset($includes_link_ids[$inclusion_id]) && $includes_link_ids[$inclusion_id]['active'] == 1) {
            $include = TRUE;
        }

        if ($include === FALSE) {
            continue;
        }

        if(!file_exists(rtrim(dirname(dirname(__DIR__)),'/\\') . $inclusion_row["path"])) {
            continue;
        }

        $filetime = filemtime(rtrim(dirname(dirname(__DIR__)),'/\\') . $inclusion_row["path"]);
        if ($time < $filetime) {
            $time = $filetime;
        }

        switch ($inclusion_row['type']) {
            case 'js':
                echo "<script src=\"" . $inclusion_row["path"] . "?t=".$filetime."\" type=\"text/javascript\" ></script>\n";
                break;

            case 'css':
                echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $inclusion_row["path"] . "?t=".$filetime."\"></link>\n";
                break;
        }
    }

    //if ($inclusion_row['type'] == 'css') {
    //    echo '<link rel="stylesheet" href="/layout/frontend/css/'.$main_layout_id.'/'.$page_id.'/?t='.$time.'" />\n';
    //}

//	IF (@mysqli_num_rows($result) > 0) {
//		WHILE ($layout_inclusions = @mysqli_fetch_assoc($result)) {
//			$query2 = "SELECT active FROM main_page_layout_inclusion_link WHERE
//				main_layout_inclusions_id = " . $layout_inclusions["id"] . " AND
//				main_page_id = " . $page_id;
//	
//			$result2 = @mysqli_query($GLOBALS['mysql_con'],$query2);
//			IF (@mysqli_num_rows($result2) == 0 && $layout_inclusions["default_active"] == 1) {
//				SWITCH ($type) {
//					CASE 'js': echo "<script src=\"" . $layout_inclusions["path"] . "\" type=\"text/javascript\" ></script>\n"; break;	
//					CASE 'css': echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $layout_inclusions["path"] . "\"></link>\n"; break;	
//				}
//			} ELSE {
//				IF (@mysqli_result($result2,0) == 1) {
//					SWITCH ($type) {
//						CASE 'js': echo "<script src=\"" . $layout_inclusions["path"] . "\" type=\"text/javascript\"></script>\n"; break;	
//						CASE 'css': echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $layout_inclusions["path"] . "\"></link>\n"; break;	
//					}
//				}
//			}
//		}
//	}
}

function create_live_edit_includes() {
    if (isset($GLOBALS["admin_user_frontend"]) && $GLOBALS["admin_user_frontend"] !== FALSE && $GLOBALS["insert_live_edit_js"] === TRUE) {
        echo "<script src=\"/plugins/ckeditor/ckeditor.js\" type=\"text/javascript\" ></script>\n";
        echo "<script src=\"/plugins/ckeditor/adapters/jquery.js\" type=\"text/javascript\" ></script>\n";
        echo "<script src=\"/plugins/ckfinder/ckfinder.js\" type=\"text/javascript\" ></script>\n";

        echo "<script src=\"/dc/admin/edit_page_live.js?t=23\" type=\"text/javascript\" ></script>\n";
        echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"/layout/admin/css/live_edit.css?t=12\"></link>\n";

        // live edit modus setzen
        $GLOBALS["live_edit_mode"] = TRUE;

        // nur einmal einfuegen
        $GLOBALS["insert_live_edit_js"] = FALSE;

        // translation nochmal initalisieren
        // auch wenn das backend offen ist, im iframe sind wir im frontend
        \DynCom\dc\common\classes\Registry::set('translation', new \DynCom\dc\common\classes\Translate($GLOBALS["admin_user_frontend"]['main_language']));
    }
}

function get_layout_classes( $_layout_id ) {
    $classes = array();
    $query   = "SELECT * FROM main_layout_class WHERE main_layout_id = '" . $_layout_id."'";
    $result  = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_assoc($result)) {
        $classes[$row['id']] = $row;
    }

    return $classes;
}

function get_collection_sql_in_groups( $collection_setup, $table ) {
    // Pruefen ob es gruppen gibt und ob kollektion in der gruppe ist
    $query       = "SELECT id FROM main_collection_setup_group WHERE main_collection_setup_id = '" . $collection_setup['main_collection_setup_id']."'";
    $result      = @mysqli_query($GLOBALS['mysql_con'], $query);
    $countGroups = @mysqli_num_rows($result);

    if ($countGroups == 0) {
        return "";                                //--> Keine gruppen angelegt, keine weitere pruefung
    }


    $link_row = "main_page_link_id";
    if ($table == "main_component_collection_group_link") {
        $link_row = "main_component_link_id";
    }

    $query = "SELECT main_collection_setup_group_id FROM " . $table . " WHERE " . $link_row . " = " . $collection_setup['id'];

    $result                  = @mysqli_query($GLOBALS['mysql_con'], $query);
    $collection_setup_groups = array();
    while ($row = @mysqli_fetch_array($result)) {
        $collection_setup_groups[] = $row['main_collection_setup_group_id'];
    }

    if (count($collection_setup_groups) == 0) {
        return "";                                //--> Keine gruppen beim inhalt angecheckt
    }

    $query          = "SELECT main_collection_id FROM main_collection_group_link WHERE main_collection_setup_group_id IN (" . join(",", $collection_setup_groups) . ") AND active = 1";
    $result         = @mysqli_query($GLOBALS['mysql_con'], $query);
    $collection_ids = array();
    while ($row = @mysqli_fetch_array($result)) {
        $collection_ids[] = $row['main_collection_id'];
    }

    if (count($collection_ids) == 0) {
        return " and id IN(-1) ";                //--> Keine kollektion zur definierten gruppe zugeordnet
    }


    return " and id IN(" . join(",", $collection_ids) . ") ";
}

function finalize_meta_tags() {
    if (strpos($GLOBALS['site_title'], " | ") === FALSE) {
        $GLOBALS['site_title'] .= ' | ';
    }
    $GLOBALS['site_title'] .= $GLOBALS['language']["site_title_name"];
    $GLOBALS['meta_description'] .= substr(strip_tags($GLOBALS["language"]["meta_description"]), 0, 200);
    $GLOBALS['meta_keywords'] .= $GLOBALS["language"]["meta_keywords"];
}

function get_globals_and_sid_as_div() {
    $json_imagecfg = base64_encode(serialize($GLOBALS["shop_setup"]["image_config"]));
    $div = <<<EOT
    <div style="display: none;" id="search_data"
        data-company="{$GLOBALS['shop']['company']}"
        data-shop_code="{$GLOBALS['shop']['code']}"
        data-language_code="{$GLOBALS["shop_language"]["code"]}"
        data-site_language="{$GLOBALS["language"]["code"]}"
        data-item_source="{$GLOBALS['shop']['item_source']}"
        data-site_code="{$GLOBALS["site"]["code"]}"
        data-sid="{$_SESSION['sid' . $GLOBALS['site']['code']]}"
        data-image_config="{$json_imagecfg}"
        data-default_img="{$GLOBALS["shop_language"]["item_placeholder_image"]}"
        data-customer_no="{$GLOBALS['shop_customer']['customer_no']}"></div>
EOT;
    return $div;
}

function get_collection_rewrite($title, $id) {
    // Alles klein schreiben
    $str = mb_strtolower($title, 'UTF-8');

    // Alle deutschen Umlaute und Sonderfälle austauschen
    $str = str_replace(
        array('ä',    'ö',    'ü',    'ß',    ' - ',    ' + ',    '_',    ' / ',    '/'),
        array('ae',    'oe',    'ue',    'ss',    '-',    '-',    '-',    '-',    '-'),
        $str);

    // Alle restlichen Leerzeichen zu Bindestrichen
    $str=preg_replace('/\s/s', '-', $str);

    // Alles löschen, was nicht alphanumerisch ist
    $str = preg_replace('/[^a-z0-9_-]/isU', '', $str);

    // doppelte vorkommen von Bindestrichen entfernen
    $str = trim(preg_replace('/-+/', '-', $str), '-');

    return $str . "-" . $id . "/";
}

function get_gulp_sources($src,$stylename = "style",$jsname = "script") {
    // sourcemaping less
    if(file_exists(rtrim(dirname(dirname(__DIR__)),'/\\') . $src .'css/'.$stylename.'.css.map')){

        ?>
        <link rel="stylesheet" href="<?=$src?>css/components.css?time=<?= filemtime(rtrim(dirname(dirname(__DIR__)),'/\\') . $src . 'css/components.css'); ?>" />
        <link rel="stylesheet" href="<?=$src?>css/<?=$stylename?>.css?time=<?= filemtime(rtrim(dirname(dirname(__DIR__)),'/\\') . $src . 'css/'.$stylename.'.css'); ?>" />
    <?}else{
        ?>
        <link rel="stylesheet" href="<?=$src?>css/<?=$stylename?>.min.css?time=<?= filemtime(rtrim(dirname(dirname(__DIR__)),'/\\')  . $src . 'css/'.$stylename.'.min.css'); ?>" />
    <?}
    ?>
    <script src="<?=$src?>js/<?=$jsname?>.js?time=<?= filemtime(rtrim(dirname(dirname(__DIR__)),'/\\') .  $src . 'js/'.$jsname.'.js'); ?>"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="<?=$src?>favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?=$src?>favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?=$src?>favicons/favicon-16x16.png" sizes="16x16">
    <link rel="mask-icon" href="<?=$src?>favicons/safari-pinned-tab.svg" color="#FFB958">
    <link rel="shortcut icon" href="<?=$src?>favicons/favicon.ico">
    <?
    $siteLanguages = get_language_by_site_id($GLOBALS['site']['id']);
    $languageCount = count($siteLanguages);

    $customizedURL = customizeUrl(false);
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $defaultMainLanguageID = (int)($GLOBALS['site']['std_main_language_id'] ?? 0);
    if ($languageCount > 1) {
        for ($i = 0; $i < $languageCount; $i++) {
            $siteLangID = (int)($siteLanguages[$i]['id'] ?? 0);
            $siteLangCode = $siteLanguages[$i]['code'] ?? '';
            if($siteLangID === $defaultMainLanguageID)
            {
                echo '<link rel="alternate" hreflang="x-default" href="https://' . $serverName . '/' . $customizedURL . $siteLangCode . '/" /> ';
            }
            else
            {
                echo '<link rel="alternate" hreflang="' . $siteLanguages[$i]['code'] . '" href="https://' . $serverName . '/' . $customizedURL . $siteLangCode . '/" />  ';
            }

        }
    }

    ?>

    <meta name="msapplication-config">
    <!-- Attribute href not allowed on element meta at this point -->
    <!-- <meta name="msapplication-config" href="<?=$src?>favicons/browserconfig.xml"> -->
    <meta name="theme-color" content="#ffffff">
    <?
}

function get_content_layout_classes($sitepart) {
    $query = "SELECT * FROM main_page_link_layout_class_link WHERE main_page_link_id = " . $sitepart['id'] . " order by sorting asc";

    $result                  = @mysqli_query($GLOBALS['mysql_con'], $query);
    $layout_class_ids = array();
    while ($row = @mysqli_fetch_array($result)) {
        $layout_class_ids[] = $row['main_layout_class_id'];
    }

    return $layout_class_ids;
}

//Url rewrite management
/**
 * @param $pdo
 */
function checkForActiveRedirects($pdo,\Psr\Http\Message\ServerRequestInterface $request): void
{
    try {
        $query_rewrite = "
        SELECT new_url, rewrite_code FROM `main_rewrite_rules` WHERE 
        (TRIM(`old_url`) LIKE :serverRequestUri 
        OR CONCAT(TRIM(`old_url`),'/') LIKE :serverRequestUri 
        OR TRIM(`old_url`) LIKE :replacedServerRequestUri 
        OR CONCAT(TRIM(`old_url`),'/') LIKE :replacedServerRequestUri)
        AND active = 1
        LIMIT 1
    ";
        $reqURI = $request->getUri()->getPath();
        //$serverRequestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $serverRequestUri = filter_var($reqURI, FILTER_SANITIZE_URL);
        $replacedServerRequestUri = str_replace('&', '%26', rawurldecode($serverRequestUri));

        $params = [
            [':serverRequestUri', $serverRequestUri, PDO::PARAM_STR],
            [':replacedServerRequestUri', $replacedServerRequestUri, PDO::PARAM_STR],
        ];

        $pdo->setQuery($query_rewrite);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();

        $redirectRows = $pdo->getResultArray();

        if (count($redirectRows) == 1) {

            switch ($redirectRows[0]["rewriteCode"]) {
                case "302":
                    $redirect = "302 Found";
                    break;
                case "301":
                default:
                    $redirect = "301 Moved Permanently";
                    break;
            }

            $storedRedirectTarget = $redirectRows[0]["new_url"];
            $protocol = rtrim($request->getUri()->getScheme(),'://') . '://';
            $serverName = $request->getUri()->getHost();
            //$protocol = ((!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            if (strpos($storedRedirectTarget, 'http') !== false) {
                $new_url = $storedRedirectTarget;
            } else {
                //$new_url = $protocol . $_SERVER['SERVER_NAME'] . $storedRedirectTarget;
                $new_url = $protocol . $serverName . $storedRedirectTarget;
            }

            headerFunctionBridge("HTTP/1.1 " . $redirect);
            headerFunctionBridge('Location: ' . $new_url);
            headerFunctionBridge("Connection: close");
            exit;
        }
    } catch (Throwable $t) {

    } catch (Exception $e) {

    }
}

function get_psr7_request_from_globals_single_instance() : \Psr\Http\Message\ServerRequestInterface
{
    static $request;
    if (null === $request) {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }
    return $request;
}

function getCollectionFullLine($line)
{
    echo "<div class='collection_content " . $line['code'] . "'><div class=\"collection_content_inner\">";

    switch ($line['fieldtype']) {
        case 'siteparts':

            show_sitepart_content($line['main_sitepart_id'], $line['main_sitepart_header_id']);

            break;

        case 'standard':
        default:
            if ($line['type_id'] == 4) { // link
                echo "<span class='link' onclick=\"window.location=" . $line['data'] . "\">" . $line['data'] . "</a>";
                break;
            }

            if ($line['type_id'] == 5) { // bild
                if ($line['data'] != "" && file_exists(ROOT_PATH . PATH_ORIGINAL_FRONTEND_COLLECTION . $line['data'])) {
                    echo "<img src=\"" . PATH_ORIGINAL_FRONTEND_COLLECTION . $line['data'] . "\" />";
                }

                break;
            }
            echo nl2br($line['data']);
            break;
    }
    echo "</div></div>";
}

/**
 * @param $file
 * @return string
 */
function getFileWithModifiedTime($file) {
        $absolute_file_path = dirname(__DIR__, 2) . $file;
        $filetime = filemtime($absolute_file_path);
       if((int)$filetime > 0) {
                $file .= '?t='.$filetime;
            }

    return $file;
}