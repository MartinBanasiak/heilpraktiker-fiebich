<?

global $parentSort;
$parentSort = array();

switch ($_GET["action"]) {
    case "moveup_navigation":
        moveup_main_navigation();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
    case "movedown_navigation":
        movedown_main_navigation();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
    case "moveleft_navigation":
        moveleft_main_navigation();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
    case "moveright_navigation":
        moveright_main_navigation();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
    case "reorder_navigation":
        reorder_navigation();
        break;
    case "delete_navigation":
        delete_main_navigation();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
    case "edit_navigation":
        edit_main_navigation();
        break;
    case "save_navigation":
        save_main_navigation($site, $language);
        break;
    case "new_navigation":
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_cardform.inc.php';
        break;
    case "send_sitemap":
        send_sitemap();
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        echo $response_output_1;
        IF ($response_output_2 <> '') {
            echo $response_output_2;
        };
        break;
    default:
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
        break;
}

function update_children($_parentId, $_list, $_level)
{
    global $parentSort;

    foreach ($_list as $site) {
        if (!array_key_exists($_parentId, $parentSort)) {
            $parentSort[$_parentId] = 1;
        }
        $query = "UPDATE main_navigation SET parent_id = " . $_parentId . ", level = " . $_level . ", sorting = " . $parentSort[$_parentId] . " WHERE id = " . (int)$site['id'];
        mysqli_query($GLOBALS['mysql_con'], $query);
        $parentSort[$_parentId]++;
        if (isset($site['children']) && count($site['children'])) {
            update_children($site['id'], $site['children'], $_level + 1);
        }
    }
}

function reorder_navigation()
{
    global $parentSort;
    $list = $_POST['list'];
    if (!is_array($list) || count($list) == 0) {
        return;
    }

    $level = 1;
    foreach ($list as $site) {
        if (!array_key_exists(0, $parentSort)) {
            $parentSort[0] = 1;
        }
        $query = "UPDATE main_navigation SET parent_id = NULL, level = 1, sorting = " . $parentSort[0] . " WHERE id = " . (int)$site['id'];
        mysqli_query($GLOBALS['mysql_con'], $query);
        $parentSort[0]++;
        if (isset($site['children']) && count($site['children'])) {
            update_children($site['id'], $site['children'], $level + 1);
        }
    }
}

function send_sitemap()
{
    global $response_output_1;
    global $response_output_2;
    $company = $GLOBALS["language"]["company"];
    $shop_code = $GLOBALS["language"]["shop_code"];
    $language_code = $GLOBALS["language"]["language_code"];
    $query = "SELECT main_site_id FROM main_language WHERE company = '" . $company . "' AND shop_code = '" . $shop_code . "' AND language_code = '" . $language_code . "'";
    $result = mysqli_query($GLOBALS['mysql_con'], $query);
    $ms_id = mysqli_result($result, 0);
    IF ($ms_id <> '') {
        $query_2 = "SELECT login_required FROM main_site WHERE id = '" . $ms_id . "'";
        $result_2 = mysqli_query($GLOBALS['mysql_con'], $query_2);
        $login_required = mysqli_result($result_2, 0);
        IF ($login_required = 0) {
            $adress = "http://" . $_SERVER["SERVER_NAME"] . "/sitemap_shop.php?company=" . $company . "&shop_code=" . $shop_code . "&language_code=" . $language_code;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($adress));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response_shop = curl_exec($ch);
            $goodresponse = "Sitemap Notification Received";
            $find1 = strpos($response, $goodresponse);
            if ($find1 === FALSE) {
                $response_output_2 = "<div class=\"errorbox\">Das Senden der Sitemap für den öffentlichen Shop war nicht erfolgreich. Zur erfolgreichen Übertragung muss cURL auf dem Apache-Server aktiviert sein.</div>\n";
            } else {
                $response_output_2 = "<div class=\"successbox\">Das Senden der Sitemap für den öffentlichen Shop war erfolgreich.</div>";
            }
        }
    }

    $adress = "http://" . $_SERVER["SERVER_NAME"] . "/sitemap_site.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($adress));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $goodresponse = "Sitemap Notification Received";
    $find1 = strpos($response, $goodresponse);
    if ($find1 === FALSE) {
        $response_output_1 = "<div class=\"errorbox\">Das Senden der Sitemap für den öffentlich zugänglichen redaktionellen Teil war nicht erfolgreich. Zur erfolgreichen Übertragung muss cURL auf dem Apache-Server aktiviert sein.</div>\n";
    } else {
        $response_output_1 = "<div class=\"successbox\">Das Senden der Sitemap für den öffentlich zugänglichen redaktionellen Teil war erfolgreich.</div>";
    }

}

function edit_main_navigation($_id = "", $messages = array())
{
    if ((int)$_id > 0) {
        $input_id = $_id;
    } else {
        $input_id = $_REQUEST["input_main_navigation_id"];
    }

    if ($input_id <> '') {
        $query = "SELECT * FROM main_navigation WHERE id = '" . $input_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_main_navigation = @mysqli_fetch_array($result);
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_cardform.inc.php';
        }
    } else {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_listform.inc.php';
    }
}

function delete_main_navigation()
{
    if ($_POST["input_main_navigation_id"] <> '') {
        $query = "DELETE FROM main_navigation WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            // Alle Unterkategorien eine eben nach oben setzen - 02. Dezember 2009 MH
            @mysqli_query($GLOBALS['mysql_con'], "UPDATE main_navigation SET parent_id = NULL, level = level -1 WHERE parent_id = '" . $_POST["input_main_navigation_id"] . "'");
        }
    }
}

/**
 * Navigationspunkt speichern
 *
 * weiterleitungstypen:
 * 1: seite zuweisen
 * 2: interne weiterleitung auf einen anderen menupunkt
 * 3: externe weiterleitung
 *
 */
function save_main_navigation($site, $language)
{

    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $messages = array();

    $input_id = "";
    $input_active = ($_POST["input_active"] == "on") ? 1 : 0;
    $input_hidden = 0;
    $input_forward = ($_POST["input_forward"] == "on") ? 1 : 0;
    $input_validity_from = datetosql($_POST["input_validity_from"]);
    $input_validity_to = datetosql($_POST["input_validity_to"]);
    $input_is_landing_page = ($_POST["input_is_landing_page"] == "on") ? 1 : 0;
    $input_template_id = $_POST["input_template_id"];

    $forward_page_id = 0;
    $forward_url = "";
    $forward_navigation_id = 0;
    $newSite = FALSE;

    switch ($_POST["input_forward_type"]) {
        case 1: // seite zuweisen
            $forward_page_id = $_POST['input_forward_page_id'];
            if ($forward_page_id == "new_site" || $forward_page_id == "new_site_from_template") {
                $newSite = TRUE;
                $forward_page_id = 1;
            }

            break;

        case 2: // interne weiterleitung
            $forward_navigation_id = $_POST['input_forward_navigation_id'];
            break;

        case 3: // externe weiterleitung
            $forward_url = addhttp($_POST['input_forward_url_external']);
            break;

        case 5: // interne pfad weiterleitung
            $forward_url = $_POST['input_forward_url'];
            break;
    }
    $shopCategories = ["account", "search", "order", "basket", "queue", "dc_order", "favorites", "rma", "item_compare"];
    if ( !in_array($_REQUEST["input_code"], $shopCategories)) {

        if ($_POST["input_main_navigation_id"] <> '') {
            $query = "UPDATE main_navigation SET
					code = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
					menu_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "',
					title_name = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_title_name"]) . "',
					active = '" . $input_active . "', 
					validity_from = " . $input_validity_from . ", 
					validity_to = " . $input_validity_to . ", 
					modified_date = now(), 
					modified_admin_user_id = " . $GLOBALS['admin_user']['id'] . ",
					forward_type = '" . (int)$_POST["input_forward_type"] . "', 
					forward_navigation_id = '" . $forward_navigation_id . "',
					forward_page_id = " . $forward_page_id . ",
					forward_url = '" . $forward_url . "',
					forward_shop_category = '" . (int)$_POST["input_forward_shop_category"] . "',
					hidden = '" . $input_hidden . "',
					is_landing_page = " . $input_is_landing_page . "
					WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";

            $inserted = FALSE;
            $input_id = $_REQUEST["input_main_navigation_id"];
        } else {
            $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "' AND level = 1 ORDER BY sorting DESC LIMIT 1");
            if (@mysqli_num_rows($result) == 1) {
                $curr_main_navigation = @mysqli_fetch_array($result);
            }
            $query = "INSERT INTO main_navigation (main_site_id,main_language_id,parent_id,sorting,level,code,menu_name,title_name,active,validity_from,validity_to,modified_date,modified_admin_user_id, forward_type, forward_navigation_id, forward_page_id, forward_url, forward_shop_category, hidden, is_landing_page)
			VALUES (
				" . $site["id"] . ",
				" . $language["id"] . ",
				NULL,
				" . ($curr_main_navigation["sorting"] + 1) . ",
				1,
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_code"]) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "',
				'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_title_name"]) . "',
				" . $input_active . ",
				" . $input_validity_from . ",
				" . $input_validity_to . ",
				now(), 
				" . $GLOBALS['admin_user']['id'] . ",
				'" . (int)$_POST["input_forward_type"] . "', 
				'" . $forward_navigation_id . "',
				'" . $forward_page_id . "',
				'" . $forward_url . "',
				'" . (int)$_POST["input_forward_shop_category"] . "',
				'" . $input_hidden . "',
				" . $input_is_landing_page . "
			)";

            $inserted = TRUE;
        }


        if (($_POST["input_code"] == '') | ($_POST["input_menu_name"] == '')) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_name") . "</div>\n";
            $error = TRUE;
        }
        if ($_POST["input_code"] <> urlencode($_POST["input_code"])) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_encoding") . "</div>\n";
            $error = TRUE;
        }
        if (($_POST["input_forward_type"] == 2) && ($forward_navigation_id == $_POST["input_main_navigation_id"])) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("navigation_msg_error1") . "</div>\n";
            $error = TRUE;
        }

        if ($_POST["input_forward_type"] == 1 && $forward_page_id == 0) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("navigation_msg_error2") . "</div>\n";
            $error = TRUE;
        }

        if ($_POST["input_forward_type"] == 1 && $_POST['input_forward_page_id'] == "new_site_from_template" && (int)$input_template_id == 0) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("navigation_msg_error4") . "</div>\n";
            $error = TRUE;
        }

        if ($_POST["input_forward_type"] == 6 && (int)$_POST["input_forward_shop_category"] === 0) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("navigation_msg_error2") . "</div>\n";
            $error = TRUE;
        }

        $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM  main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "' AND code = '" . $_POST["input_code"] . "'");
        if ((@mysqli_num_rows($result) <> 0) && ($_POST["input_main_navigation_id"] == '')) {
            $messages[] = "<div class=\"errorbox\">" . $translation->get("error_textkey_exists") . "</div>\n";
            $error = TRUE;
        }


    }else
    {
        $messages[] = "<div class=\"errorbox\">" . $translation->get("navigation_code_is_not_allowed") . "</div>\n";
        $error = TRUE;
    }

    if (!$error) {
        @mysqli_query($GLOBALS['mysql_con'], $query);

        if ($inserted == TRUE) {
            $input_id = mysqli_insert_id($GLOBALS['mysql_con']);
            $messages[] = '<div class="successbox">' . $translation->get("navigation_msg_success1") . '</div>';
        } else {
            $messages[] = '<div class="successbox">' . $translation->get("navigation_msg_success2") . '</div>';
        }

        if ($newSite === TRUE) {
            if((int)$input_template_id > 0) {
                $page_id = copy_main_page($input_template_id, $GLOBALS["language"]['id'], $GLOBALS["site"]['id']);
                $query = "UPDATE main_page SET 
                            is_template = 0, 
                            title = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "', 
                            subtitle = '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "' 
                          WHERE id = " . $page_id;
                @mysqli_query($GLOBALS['mysql_con'], $query);
            } else {
                $query = "INSERT INTO main_page
                (title, subtitle, meta_keywords, meta_description, main_language_id, active, modified_date, modified_user, validity_from, validity_to)
                VALUES (
                    '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "',
                    '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "',
                    '',
                    '',
                    '" . $GLOBALS["language"]['id'] . "',
                    1,
                    " . time() . ",
                    " . (int)$GLOBALS["admin_user"]['id'] . ",
                    " . $input_validity_from . ",
                    " . $input_validity_to . "
                )";
                @mysqli_query($GLOBALS['mysql_con'], $query);
                $page_id = mysqli_insert_id($GLOBALS['mysql_con']);
            }

            $query = "UPDATE main_navigation SET forward_page_id = " . $page_id . " WHERE id = " . $input_id;
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }

        edit_main_navigation($input_id, $messages);
    } else {
        headerFunctionBridge('EDIT_ERROR: 1');
        $query = "SELECT * FROM main_navigation WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $input_main_navigation = @mysqli_fetch_array($result);
        }
        $input_main_navigation["code"] = $_REQUEST["input_code"];
        $input_main_navigation["menu_name"] = $_REQUEST["input_menu_name"];
        $input_main_navigation["title_name"] = $_REQUEST["input_title_name"];
        $input_main_navigation["active"] = $input_active;
        $input_main_navigation["hidden"] = 0;
        $input_main_navigation["validity_from"] = $_REQUEST["input_validity_from"];
        $input_main_navigation["validity_to"] = $_REQUEST["validity_to"];

        $input_main_navigation["forward_type"] = $_REQUEST["input_forward_type"];
        $input_main_navigation["forward_url"] = $_REQUEST["input_forward_url"];
        $input_main_navigation["forward_navigation_id"] = $_REQUEST["input_forward_navigation_id"];
        $input_main_navigation["forward_page_id"] = $_REQUEST["input_forward_page_id"];
        $input_main_navigation["forward_shop_category"] = $_REQUEST["input_forward_shop_category"];
        $input_main_navigation["template_id"] = $_REQUEST["input_template_id"];
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_main_navigation_cardform.inc.php';
    }

}

function show_main_navigation($site, $language)
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $result = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "'");
    $num_rows = mysqli_num_rows($result);
    if ($num_rows > 0) {
        echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"linklist\"><tr>";
        echo "<td><div>" . $translation->get("name") . "</div></td>";
        echo "<td width=\"150\" align=\"left\"><div></div></td>";
        echo "<td width=\"300\" align=\"left\"><div>" . $translation->get("code") . "</div></td>";
        echo "<td width=\"100\" align=\"left\"><div>" . $translation->get("state") . "</div></td>";
        echo "<td class=\"changed_on\" width=\"160\" align=\"left\"><div>" . $translation->get("changed_on") . "</div></td>";
        echo "<td class=\"changed_by\" width=\"250\" align=\"left\"><div>" . $translation->get("changed_by") . "</div></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class=\"linklist_seperator\"></td>";
        echo "<td class=\"linklist_seperator\"></td>";
        echo "<td class=\"linklist_seperator\"></td>";
        echo "<td class=\"linklist_seperator\"></td>";
        echo "<td class=\"changed_on linklist_seperator\"></td>";
        echo "<td class=\"changed_by linklist_seperator\"></td>";
        echo "</tr>";

        echo "</table>";

        show_main_navigation_rek($site, $language, 0, 1, 0, $num_rows);


    } else {
        echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
    }
    echo "\n";
    return TRUE;
}


function show_main_navigation_rek($site, $language, $parent_id, $level, $input_counter, $num_rows)
{
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "'" . $parent_id_query . " ORDER BY sorting ASC");
    if (@mysqli_num_rows($result) > 0) {
        if ($parent_id == 0) {
            echo "<ol class=\"linklist sortable\">";
        } else {
            echo "<ol>";
        }

        while ($main_navigation = @mysqli_fetch_array($result)) {
            $parentclass = "";
            if ($level > 1) {

                $parentclass = " data-tt-parent-id=\"{$main_navigation['parent_id']}\"";
            }

            $modified_array = explode(" ", $main_navigation["modified_date"]);
            $modified_date = $modified_array[0];
            $modified_time = $modified_array[1];

            $checkedRow = "false";
            if (isset($_REQUEST['input_main_navigation_id']) && $_REQUEST['input_main_navigation_id'] == $main_navigation['id']) {
                $checkedRow = "true";
            }

            echo "<li id=\"list_{$main_navigation['id']}\">";
            echo "<div data-inputid=\"" . $main_navigation['id'] . "\" data-action=\"edit_navigation\" data-inputname=\"input_main_navigation_id\" data-checked=\"" . $checkedRow . "\" class=\"linklist_content dd-handle\"><span class=\"disclose\"><span>&nbsp;</span></span>" . htmlentities($main_navigation["menu_name"], ENT_COMPAT, "UTF-8") . "<div class=\"dd-icon\"></div>";
            echo "<div class=\"dd-code\">" . $main_navigation["code"] . "</div>";
            echo "<div class=\"dd-status\">" . ($main_navigation["active"] == 1 ? $translation->get("active") : $translation->get("inactive")) . "</div>";
            echo "<div class=\"dd-gaendert-am\">" . datefromsql($modified_date) . " " . $modified_time . "</div>";
            echo "<div class=\"dd-gaendert-von\">" . navigation_get_modified_admin_user($main_navigation['modified_admin_user_id']) . "</div></div>";
            $input_counter++;
            $input_counter = show_main_navigation_rek($site, $language, $main_navigation["id"], ($level + 1), $input_counter, $num_rows);
            echo "</li>";
        }
        echo "</ol>";
    }

    return $input_counter;
}

/*
function show_main_navigation_tr($main_navigation, $level, $input_counter, $num_rows) {
	$count = 1;
	while($count < ($level)) {
    	$spaces .= "";
    	$count++;
    }
    
    $parentclass="";
    if($level > 1) {
    	$parentclass = " data-tt-parent-id=\"{$main_navigation['parent_id']}\"";
    }
    
    $modified_array = explode(" ",$main_navigation["modified_date"]);
    $modified_date = $modified_array[0];
    $modified_time = $modified_array[1];
	$input_arr_text = ($num_rows > 1) ? "[" . $input_counter . "]" : "";
//	echo "<tr class=\"treegrid-{$main_navigation['id']}{$parentclass}\" onClick=\"document.form_edit_main_navigation_list.input_main_navigation_id" . $input_arr_text . ".checked = true;\">\n";
//	echo "<tr class=\"linklist_content\" data-sorting=\"{$main_navigation['sorting']}\" data-tt-id=\"{$main_navigation['id']}\"{$parentclass} data-checked=\"false\" data-inputname=\"input_main_navigation_id\" data-action=\"edit\" data-inputid=\"{$main_navigation['id']}\">\n";
//	$selected = ($main_navigation["id"]==$_POST["input_main_navigation_id"]) ? " checked=\"checked\"" : "";
//	//echo "<td style=\"width: 25px; text-align: center;\"><input" . $selected . " style=\"width: 14px; height: 14px; padding: 0px; margin: 0px;\" id=\"input_main_navigation_id\" name=\"input_main_navigation_id\" type=\"radio\" value=\"" .  $main_navigation["id"] . "\" /></td>\n";
//	echo ($level==1) ? "<td><span class=\"folder\">" . $spaces . htmlentities($main_navigation["menu_name"], ENT_COMPAT, "UTF-8") . "</span></td>\n" : "<td><span class=\"folder\">" . $spaces . htmlentities($main_navigation["menu_name"], ENT_COMPAT, "UTF-8") . "</span></td>\n";
//   	echo "<td>" . $main_navigation["code"] . "</td>\n";
//   	echo "<td>"; echo ($main_navigation["active"]) ? "Aktiv" : "Inaktiv"; echo "</td>\n";
//   	echo "<td>" . datefromsql($main_navigation["validity_from"]) . " - " . datefromsql($main_navigation["validity_to"]) . "</td>\n";
//   	echo "<td>". datefromsql($modified_date) . ", ".$modified_time." von ".navigation_get_modified_admin_user($main_navigation['modified_admin_user_id'])."</td>\n";
//   	echo "<td style=\"display:none;\">{$main_navigation['sorting']}</td>";
//	echo "</tr>";
//	echo "<tr class=\"droppableSpacer\" data-sorting=\"{$main_navigation['sorting']}\" data-tt-id=\"drop_{$main_navigation['id']}\"{$parentclass}><td></td><td></td><td></td><td></td><td></td><td style=\"display:none;\">{$main_navigation['sorting']}</td></tr>";

	echo "<li>" . $spaces . htmlentities($main_navigation["menu_name"], ENT_COMPAT, "UTF-8") . "</li>";
}
*/

function moveup_main_navigation()
{
    move_navigation_vertical("<", "DESC", $_POST["input_main_navigation_id"]);
}

function movedown_main_navigation()
{
    move_navigation_vertical(">", "ASC", $_POST["input_main_navigation_id"]);
}

function move_navigation_vertical($way, $order, $main_navigation_id)
{
    $query = "SELECT * FROM main_navigation WHERE id = '" . $main_navigation_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_main_navigation = @mysqli_fetch_array($result);
    }
    $parent_query = ($curr_main_navigation["parent_id"] <> '') ? " parent_id = '" . $curr_main_navigation["parent_id"] . "'" : " parent_id IS NULL";
    $query = "SELECT * FROM main_navigation WHERE main_site_id = '" . $curr_main_navigation["main_site_id"] . "' AND main_language_id = '" . $curr_main_navigation["main_language_id"] . "' AND" . $parent_query . " AND sorting " . $way . " " . $curr_main_navigation["sorting"] . " ORDER BY sorting " . $order . " LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $change_main_navigation = @mysqli_fetch_array($result);
    }
    if (($curr_main_navigation["id"] <> '') && ($change_main_navigation["id"] <> '')) {
        $query = "UPDATE main_navigation SET sorting = " . $change_main_navigation["sorting"] . " WHERE id = " . $curr_main_navigation["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
        $query = "UPDATE main_navigation SET sorting = " . $curr_main_navigation["sorting"] . " WHERE id = " . $change_main_navigation["id"];
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }
}

function moveleft_main_navigation()
{
    $query = "SELECT * FROM main_navigation WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_main_navigation = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM main_navigation WHERE id = '" . $curr_main_navigation["parent_id"] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $parent_main_navigation = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM main_navigation WHERE parent_id = '" . $curr_main_navigation["parent_id"] . "' ORDER BY sorting DESC LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $last_child_main_navigation = @mysqli_fetch_array($result);
    }

    if (($curr_main_navigation["id"] <> '') && ($parent_main_navigation["id"] <> '')) {
        // Eigentlichen Menüeintrag aktualisieren
        if ($parent_main_navigation["parent_id"] == '') {
            $parent_main_navigation["parent_id"] = "NULL";
        }
        $query = "UPDATE main_navigation SET parent_id = " . $parent_main_navigation["parent_id"] . ", level = " . $parent_main_navigation["level"] . ", sorting = " . ($parent_main_navigation["sorting"] + 1) . " WHERE id = " . $curr_main_navigation["id"];
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            // Level der ursprünglichen Untermenüpunkte aktualisieren
            update_main_navigation_children($curr_main_navigation["id"], "-");
            // Parent-ID und Sortierung der neuen Untermenüpunkte aktualisieren
            $parent_query = ($curr_main_navigation["parent_id"] <> '') ? " parent_id = '" . $curr_main_navigation["parent_id"] . "'" : " parent_id IS NULL";
            $query = "SELECT * FROM main_navigation WHERE" . $parent_query . " AND sorting > " . $curr_main_navigation["sorting"] . " AND main_site_id = '" . $GLOBALS["site"]["id"] . "' AND main_language_id = '" . $GLOBALS["language"]["id"] . "'";
            $new_sorting = 1;
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) > 0) {
                while ($main_navigation = @mysqli_fetch_array($result)) {
                    $query = "UPDATE main_navigation SET parent_id = " . $curr_main_navigation["id"] . ", sorting = " . $new_sorting . " WHERE id = " . $main_navigation["id"] . " LIMIT 1";
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                    $new_sorting++;
                }
            }
            // Sortierung der neuen Nachfolger aktualisieren
            $new_sorting = $parent_main_navigation["sorting"] + 1;
            $parent_query = ($parent_main_navigation["parent_id"] <> 'NULL') ? " parent_id = '" . $parent_main_navigation["parent_id"] . "'" : " parent_id IS NULL";
            $query = "SELECT * FROM main_navigation WHERE" . $parent_query . " AND id <> " . $curr_main_navigation["id"] . " AND sorting >= " . $new_sorting . " AND main_site_id = " . $GLOBALS["site"]["id"] . " AND main_language_id = " . $GLOBALS["language"]["id"];
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) > 0) {
                while ($main_navigation = @mysqli_fetch_array($result)) {
                    $new_sorting++;
                    $query = "UPDATE main_navigation SET sorting = " . $new_sorting . " WHERE id = " . $main_navigation["id"] . " LIMIT 1";
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }
        }
    }
}

function moveright_main_navigation()
{
    $query = "SELECT * FROM main_navigation WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $curr_main_navigation = @mysqli_fetch_array($result);
    }
    $parent_query = ($curr_main_navigation["parent_id"] <> '') ? " parent_id = '" . $curr_main_navigation["parent_id"] . "'" : " parent_id IS NULL";
    $query = "SELECT * FROM main_navigation WHERE" . $parent_query . " AND sorting < " . $curr_main_navigation["sorting"] . " AND main_site_id = " . $GLOBALS["site"]["id"] . " AND main_language_id = " . $GLOBALS["language"]["id"] . " ORDER BY sorting DESC LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $pre_main_navigation = @mysqli_fetch_array($result);
    }
    $query = "SELECT * FROM main_navigation WHERE parent_id = '" . $pre_main_navigation["id"] . "' ORDER BY sorting DESC LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $lastprechild_main_navigation = @mysqli_fetch_array($result);
    }
    if (($curr_main_navigation["id"] <> '') && ($pre_main_navigation["id"] <> '')) {
        // Eigentlichen Menüeintrag aktualisieren
        if ($lastprechild_main_navigation["sorting"] == '') {
            $lastprechild_main_navigation["sorting"] = 0;
        }
        $query = "UPDATE main_navigation SET parent_id = " . $pre_main_navigation["id"] . ", level = " . ($curr_main_navigation["level"] + 1) . ", sorting = " . ($lastprechild_main_navigation["sorting"] + 1) . " WHERE id = " . $curr_main_navigation["id"] . " AND main_site_id = " . $GLOBALS["site"]["id"] . " AND main_language_id = " . $GLOBALS["language"]["id"];
        if (@mysqli_query($GLOBALS['mysql_con'], $query)) {
            // Level der ursprünglichen Untermenüpunkte aktualisieren
            update_main_navigation_children($curr_main_navigation["id"], "+");

            // Sortierung der neuen Nachfolger aktualisieren
            $new_sorting = $curr_main_navigation["sorting"];
            $parent_query = ($curr_main_navigation["parent_id"] <> '') ? " parent_id = " . $curr_main_navigation["parent_id"] : " parent_id IS NULL";
            $query = "SELECT * FROM main_navigation WHERE" . $parent_query . " AND sorting >= " . $new_sorting . " AND main_site_id = " . $GLOBALS["site"]["id"] . " AND main_language_id = " . $GLOBALS["language"]["id"];
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            if (@mysqli_num_rows($result) > 0) {
                while ($main_navigation = @mysqli_fetch_array($result)) {
                    $query = "UPDATE main_navigation SET sorting = " . $new_sorting . " WHERE id = " . $main_navigation["id"] . " LIMIT 1";
                    $new_sorting++;
                    @mysqli_query($GLOBALS['mysql_con'], $query);
                }
            }
        }
    }
}

function update_main_navigation_children($main_navigation_id, $way)
{
    $query = "SELECT * FROM main_navigation WHERE parent_id = '" . $main_navigation_id . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) > 0) {
        while ($main_navigation = @mysqli_fetch_array($result)) {
            $query = "UPDATE main_navigation SET level = level " . $way . " 1 WHERE id = " . $main_navigation["id"] . " LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'], $query);
            update_main_navigation_children($main_navigation["id"], $way);
        }
    }
}

function insert_main_navigation2($site, $language)
{
    if ($_POST["input_menu_name"] <> '') {
        if ($_POST["input_main_navigation_id"] <> '') {
            $query = "SELECT * FROM main_navigation WHERE id = '" . $_POST["input_main_navigation_id"] . "' LIMIT 1";
        } else {
            $query = "SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"] . "' AND level = 1 ORDER BY sorting DESC LIMIT 1";
        }
        $result = @mysqli_query($GLOBALS['mysql_con'], $query);
        if (@mysqli_num_rows($result) == 1) {
            $curr_main_navigation = @mysqli_fetch_array($result);
            $query = "INSERT INTO main_navigation (id, main_site_id, main_language_id, parent_id, sorting, level, code, menu_name, title_name, active) 
                                      VALUES (NULL, 
                                      " . $curr_main_navigation["main_site_id"] . ", 
                                      " . $curr_main_navigation["main_language_id"] . ", 
                                      NULL, 
                                      " . ($curr_main_navigation["sorting"] + 1) . ", 
                                      " . $curr_main_navigation["main_site_id"] . ", 
                                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "', 
                                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "', 
                                      '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $_REQUEST["input_menu_name"]) . "', 
                                      '1'
                                    )";
            @mysqli_query($GLOBALS['mysql_con'], $query);
        }
    }
}

?>

