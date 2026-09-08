<?php

// Funktion zum erstellen eines Links in Admin-Modulen

function mal($sitepart, $key1, $value1, $key2 = NULL, $value2 = NULL, $key3 = NULL, $value3 = NULL) {
    $newget = $_GET;
    if($key1 <> "") { $newget[$key1] = $value1; }
    if($key2 <> "") { $newget[$key2] = $value2; }
    if($key3 <> "") { $newget[$key3] = $value3; }
    $newget["frontend_nav"] = $sitepart["main_navigation_id"];
    $newget["sitepart"] = $sitepart["navigation_has_sitepart_id"];
    unset($newget["action"],$newget["sid"],$newget["site"],$newget["language"],$newget["level_1"],$newget["level_2"],$newget["level_3"],$newget["level_4"],$newget["level_5"]);
    return("?" . http_build_query($newget));
}

function navigation_treemenualt($site, $language, $navigation, $from_level, $to_level) {
    echo "\n<script type=\"text/javascript\" src=\"../../plugins/treeview/mktree.js\"></script>\n";
    return navigation_treemenu_rek($site, $language, $navigation, 0, 1, "", $from_level, $to_level,true);
}

function navigation_treemenu_rekalt($site, $language, $navigation, $parent_id, $level, $navcode, $from_level, $to_level, $first) {
    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_view_active_navigation WHERE main_site_id = " . $site["id"] . " AND main_language_id = " . $language["id"] . " AND parent_id = " . $parent_id . " ORDER BY sorting ASC");
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        if($show_level) { echo ($first) ? "\n<ul class=\"mktree\" id=\"tree\">" : "\n<ul>"; }
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active = "";
            if($show_level) { echo "\n<li ID=\"" . $nav["code"] . "\">\n<a href=\"/" . $site["code"] . "/" . $language["code"] . "/$newnavcode\">" . htmlspecialchars($nav["menu_name"], ENT_QUOTES, "UTF-8") . "</a>"; }
            navigation_treemenu_rek($site, $language, $navigation, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level, false);
            if($show_level) { echo "\n</li>"; }
        }
        if($show_level) { echo "\n</ul>"; }
    }
}

function get_menu_link($_code, $_site, $_language, $_params="") {
    return sprintf("/dc/%s/%s/%s/%s",
        $_site['code'],
        $_language['code'],
        $_code,
        $_params
    );
}

function current_website_language() {
    return '<div id="site_language_name">' . $GLOBALS["site"]['name'] . " - " . $GLOBALS["language"]['name'] . '</div>';
}

function get_translation($_key) {
    $translation = \DynCom\dc\common\classes\Registry::get('translation');
    return $translation->get($_key);
}

// Gibt den Datenbankeintrag des aktuellen Frontend-Menübereichs zurück

function frontend_navigation_get() {
    if(isset($_GET["frontend_nav"])) {


        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = "SELECT * FROM main_navigation WHERE id = : id  ";
        $params = [
            [':id', $_GET["frontend_nav"], PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $result = $pdo->getResultArray();

        if(count($result) == 1) {
            return $result[0];
        }
    }
}

// Titel der Seite zusammenstellen

function admin_site_title() {
  $setup = setup_get();
  $title = "dynamic commerce";

  
  $menuname = '';
  $adminMenu = \DynCom\dc\common\classes\Adminmenu::get();
  
  if($_GET["level_1"] != "") {
  	$menuname = $adminMenu[$_GET["level_1"]]['name'];
  }
  
  if($_GET["level_2"] != "") {
  	$menuname = $adminMenu[$_GET["level_1"]]['subsites'][$_GET["level_2"]]['name'];
  }
  
  if($menuname<>'') {
	  $title .= " - " . $menuname;
  }
  return $title;
}

function admin_navigation_menu($site, $language, $admin_navigation, $from_level, $to_level) {
    admin_navigation_menu_rek($site, $language, $admin_navigation, 0, 1, "", $from_level, $to_level);
    echo "\n";
    return true;
}

function admin_navigation_menu_rek($site, $language, $navigation, $parent_id, $level, $navcode, $from_level, $to_level) {
    switch ($level) {
        case 1: $levelcode = $_GET["level_1"];break;
        case 2: $levelcode = $_GET["level_2"];break;
        case 3: $levelcode = $_GET["level_3"];break;
    }
    $spaces = "    ";
    $count = 0;
    while($count <= $level) {
        $spaces .= "  ";
        $count++;
    }

    $parent_id_query = ($parent_id == 0) ? "parent_id IS NULL" : "parent_id = '".$parent_id."'";
    $query = "SELECT * FROM main_admin_navigation WHERE " . $parent_id_query . " ORDER BY sorting ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        if($show_level) { echo "\n" . $spaces . "<ul class=\"level_$level\">"; }
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active = "";
            if($nav["code"] == $levelcode) { $active = "class=\"active_tree\" "; }
            if($nav["code"] == $navigation["code"]) { $active = "class=\"active\" "; }
            if($show_level) { echo "\n" . $spaces . "  <li class=\"level_$level\"><a " . $active . "href=\"/dc/" . $site["code"] . "/" . $language["code"] . "/$newnavcode\">" . htmlentities($nav["menu_name"], ENT_COMPAT, "UTF-8") . "</a>"; }
            if($nav["code"] == $levelcode) {
                admin_navigation_menu_rek($site, $language, $navigation, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level);
            }
            if($show_level) { echo "</li>"; }
        }
        if($show_level) { echo "\n" . $spaces . "</ul>"; }
    }
    return true;
}

// Erzeugt den dynamisch aufklappbaren Menübaum für den Bereich Inhalte

function navigation_treemenu($site, $language, $navigation, $from_level, $to_level) {
    echo "<script type=\"text/javascript\" src=\"/plugins/treeview/mktree.js\"></script>";
    navigation_treemenu_rek($site, $language, $navigation, 0, 1, $from_level, $to_level,true);
    echo "<script type=\"text/javascript\">expandToItem('tree','node" . $_GET["frontend_nav"] . "');</script>";
    echo "\n";
    return true;
}

function navigation_treemenu_rek($site, $language, $navigation, $parent_id, $level, $from_level, $to_level, $first) {
    $spaces = "    ";
    while($count <= $level) {
        $spaces .= "  ";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $language["id"]."'" . $parent_id_query . " ORDER BY sorting ASC");
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        $no_of_childs = @mysqli_num_rows($result);
        if($show_level) { echo ($first) ? "\n" . $spaces . "<ul class=\"mktree\" id=\"tree\">" : "\n" . $spaces . "<ul>"; }
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            if($show_level) { echo "\n" . $spaces . "  <li id=\"node" . $nav["id"] . "\"><a href=\"?frontend_nav=" . $nav["id"] . "\">" . htmlentities($nav["menu_name"], ENT_COMPAT, "UTF-8") . "</a>"; }
            navigation_treemenu_rek($site, $language, $navigation, $nav["id"], ($level+1), $from_level, $to_level, false);
            if($show_level) { echo "</li>"; }
            $count++;
        }
        if($show_level) { echo "\n" . $spaces . "</ul>"; }
    }
    return true;
}

// Prüft Login eines Admin-User

function check_admin_user_login() {

    $passwordOptions = get_password_options();

    if($_GET["action"]=="admin_logout") {
        $visitor = get_visitor_small(session_id());
        if((int)$visitor["id"]>0) {
            $query = "UPDATE main_visitor SET admin_login = FALSE, main_admin_user_id = NULL WHERE id = " . $visitor["id"] . " LIMIT 1";
            @mysqli_query($GLOBALS['mysql_con'],$query);
            // redirect
            headerFunctionBridge('Location: /dc/');
        }
    }
    if(($_GET["action"]=="admin_login" ) && ($_POST["input_login"]<>'') && ($_POST["input_password"]<>'')) {
        $visitor = get_visitor_small(session_id());
        if(!((int)$visitor['id'] > 0)) {
            $visitor = get_visitor();
        }
        $query = "SELECT * FROM main_admin_user WHERE UPPER(login) = UPPER('" . $_POST["input_login"] . "') LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);
            $rowID = $row['id'];
            $dbPasswordHash = $row['password'];
            $inputPassword = filter_input(INPUT_POST,'input_password',FILTER_SANITIZE_STRING);
            $validOldHash = md5($inputPassword) === $dbPasswordHash;

            if ($validOldHash || password_verify($inputPassword,$dbPasswordHash)) {

                if ($validOldHash || password_needs_rehash($dbPasswordHash,PASSWORD_DEFAULT,$passwordOptions)) {
                    $newPasswordHash = password_hash($inputPassword,PASSWORD_DEFAULT,$passwordOptions);
                    $upadateQuery = 'UPDATE main_admin_user SET password = \'' . $newPasswordHash . '\' WHERE id = ' . $rowID;
                    mysqli_query($GLOBALS['mysql_con'],$upadateQuery);
                }

                $admin_user = $row;
                $query = "UPDATE main_visitor SET admin_login = TRUE, main_admin_user_id = " . $admin_user["id"] . " WHERE id = " . $visitor["id"] . " LIMIT 1";
                @mysqli_query($GLOBALS['mysql_con'],$query);

                delete_false_admin_password_loign_counter(md5(getAdminUserIP()) , md5($_POST["input_login"]));

                // redirect
                headerFunctionBridge('Location: /dc/');
            }
            else
            {
                add_false_password_admin_loign_counter(md5(getAdminUserIP()),  md5($_POST["input_login"]), 1, time());
            }

        }
    }
    $visitor = get_visitor_small(session_id());
}

// Erzeugt Drop-Down aller Siteparts

function sitepart_select($selectname = "input_sitepart_id") {
    $query = "SELECT * FROM main_modul WHERE installed = 1 ORDER BY id ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $modules = array();
    $i = 0;
    WHILE ($row = @mysqli_fetch_assoc($result)) {
        $modules[$i] = $row;
        $i++;
    }
    FOR ($k = 0; $k < count($modules); $k++) {
        $query = "SELECT * FROM main_sitepart WHERE main_modul_id = '" .$modules[$k]['id']."'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        $i = 0;
        WHILE ($row = @mysqli_fetch_assoc($result)) {
            $modules[$k]['siteparts'][$i] = $row;
            $i++;
        }
    }
    $l = 1;
    FOR ($i = 0; $i < count($modules); $i++) {
        echo ("<tr><td id=\"img_".$l."\"><div>\n\t");
        FOR ($j = 0; $j < count($modules[$i]["siteparts"]); $j++) {
            echo ("<div>\n\t\t");
            echo ("<input name=\"".$selectname."\" type=\"radio\" id=\"sitepart_".$modules[$i]["siteparts"][$j]["id"]."\" value=\"".$modules[$i]["siteparts"][$j]["id"]."\" /><label for=\"sitepart_".$modules[$i]["siteparts"][$j]["id"]."\" >".$modules[$i]["siteparts"][$j]["name"]."</label>");
            echo ("</div>\n\t");
        }
        echo ("</div></td></tr>\n");
        $l++;
    }
}

function sitepart_select_dd($name,$selectname = "input_sitepart_id", $value = 1, $disabled = FALSE) {
    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_sitepart");
    if(mysqli_num_rows($result) > 0) {
        while($sitepart = mysqli_fetch_array($result)) {
            if($sitepart["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $sitepart["id"] . "\">" . $sitepart["name"] . "</option>";
            } else {
                echo "<option value=\"" . $sitepart["id"] . "\">" . $sitepart["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}

// Erzeugt Drop-Down der Seiten/Mandanten

function site_select($name,$selectname = "input_site_id", $value = NULL, $disabled = FALSE, $chooseText = FALSE, $onchange = "") {
    $translation  = \DynCom\dc\common\classes\Registry::get("translation");

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " onchange=\"" . $onchange . "\" class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    if ($chooseText === TRUE) {
        echo "<option value=\"\">" . $translation->get("please_choose") . "</option>";
    }

    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_site");
    if(mysqli_num_rows($result) > 0) {
        while($site= mysqli_fetch_array($result)) {
            if($site["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $site["id"] . "\">" . $site["name"] . "</option>";
            } else {
                echo "<option value=\"" . $site["id"] . "\">" . $site["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}

// Erzeugt Drop-Down der Layouts

function layout_select($name,$selectname = "input_layout_id", $value = NULL, $disabled = FALSE) {
    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_layout");
    if(mysqli_num_rows($result) > 0) {
        while($layout = mysqli_fetch_array($result)) {
            if($layout["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            } else {
                echo "<option value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}

// Entpacken einer ZIP-Datei

function extract_zip_file($zipfile,$path) {
    if (function_exists('zip_open'))
    {
        /* Absolute Pfadangabe ist hier erforderlich! */
        $zip_datei = $zipfile;
       // echo $zip_datei;
        /* relative Pfadangabe mit abschließendem Slash " / " */
        $ziel_ordner = $path;
        if (file_exists($zip_datei) && ($zip = zip_open($zip_datei)))
        {
            while($zip_entry = zip_read($zip))
            {
                $file_name = zip_entry_name($zip_entry);
                $file_size = zip_entry_filesize($zip_entry);
                $comp_meth = zip_entry_compressionmethod($zip_entry);

                if (zip_entry_open($zip, $zip_entry, 'rb'))
                {
                    $buffer = zip_entry_read($zip_entry, $file_size);

                    if (preg_match('/\/$/', $file_name) && ($comp_meth == 'stored'))
                    {
                        if (!is_dir($ziel_ordner . $file_name))
                            @mkdir($ziel_ordner . $file_name, 0777);
                    }
                    else
                    {
                        $fp = fopen($ziel_ordner . $file_name, 'wb');
                        fwrite($fp, $buffer);
                        fclose($fp);
                    }

                    zip_entry_close($zip_entry);
                }
            }

            zip_close($zip);
        }
        else
            echo 'Konnte die Datei <font color="#ff0000">' . basename($zip_datei) . '</font> nicht öffnen!';
    }
    else
        echo   'Bitte aktivieren Sie in der php.ini die Extensions '
            . '<font color="#ff0000">php_zip.dll</font> in dem sie '
            . 'das Semikolon vor dieser Zeile <font color="#ff0000"><b>;</b></font>'
            . '<font color="#0000ff">extension=php_zip.dll</font> entfernen.';
}

// Layout Area integration - 10. August 2009 MH
function layout_area_select($name,$selectname = "input_layout_area_id", $value = NULL, $disabled = FALSE) {
    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_layout_area WHERE main_layout_id = '" . $GLOBALS["language"]["main_layout_id"] . "' ORDER BY sorting ASC");
    if(mysqli_num_rows($result) > 0) {
        while($layout = mysqli_fetch_array($result)) {
            if($layout["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            } else {
                echo "<option value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}

function layout_class_select($name,$selectname = "input_layout_class_id", $value = NULL, $disabled = FALSE) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    echo "\n<option value=\"0\">" . $translation->get('please_choose') . "</option>";

    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_layout_class WHERE main_layout_id = '" . $GLOBALS["language"]["main_layout_id"] . "' ORDER BY sorting ASC");
    if(mysqli_num_rows($result) > 0) {
        while($layout = mysqli_fetch_array($result)) {
            if($layout["id"] == $value) {
                echo "<option selected=\"selected\" value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            } else {
                echo "<option value=\"" . $layout["id"] . "\">" . $layout["name"] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}

// Gibt den Datenbankeintrag des aktuellen Admin-Menübereichs zurück

function admin_navigation_get() {
    if($_GET["level_1"] <> '') { $levelcode = $_GET["level_1"]; }
    if($_GET["level_2"] <> '') { $levelcode = $_GET["level_2"]; }
    if($_GET["level_3"] <> '') { $levelcode = $_GET["level_3"]; }
    if($levelcode=="") { $levelcode = "Inhalte"; }
    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_admin_navigation WHERE code = '" . $levelcode . "'");
    if(@mysqli_num_rows($result) == 1) {
        return @mysqli_fetch_array($result);
    }
}

//Inhalt eines Select-Feldes über Navigationseinträge mit Rückgabe als String statt Ausgabe per echo
function navigation_select_noecho($navigation_id, $site_id, $language_id, $from_level = 1, $to_level = 5) {
    global $nav;
    $nav = '<option value=\"\">Bitte Wählen</option>';
    $nav .= navigation_select_rek_noecho($site_id,$language_id,navigation_getbyid($navigation_id), 0, 1, "", $from_level, $to_level);
    return $nav;
}

function navigation_select_rek_noecho($site_id, $language_id, $navigation, $parent_id, $level, $navcode, $from_level, $to_level) {
    global $nav_1;
    $spaces = "";
    $count = 1;
    while($count < $level) {
        $spaces .= "&nbsp;&nbsp;&nbsp;";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
    $query = "SELECT * FROM main_navigation WHERE main_site_id = '" . $site_id . "' AND main_language_id = '" . $language_id."'" . $parent_id_query . " ORDER BY sorting ASC";

    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active = "";
            if ($show_level) {
                if ($nav["id"] == $navigation["id"]) {
                    $nav_1 .= "\n<option selected=\"selected\" value=\"" . $nav["id"] . "\">" . $nav["menu_name"] . "</option>";
                } else {
                    $nav_1 .= "\n<option " . $active . "value=\"" . $nav["id"] . "\">" .  $spaces . $nav["menu_name"] . "</option>";
                }
                navigation_select_rek_noecho($site_id, $language_id, $navigation, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level );
            }
        }
    } else {
        if ($level == 1) {
            $nav_1 .= "\n<option selected=\"selected\" value=\"0\"> - </option>";
        }
    }
    return $nav_1;
}

// Ezreugt eingerücktes Drop-Down eines Navigation-Menü-Baums

function navigation_select($name,$selectname = "input_forward_navigation_id", $navigation_id, $site_id, $language_id, $from_level = 1, $to_level = 5, $disabled = FALSE) {
    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";
    navigation_select_rek($site_id,$language_id,navigation_getbyid($navigation_id), 0, 1, "", $from_level, $to_level);
    echo "</select></div>";
}

function navigation_select_rek($site_id, $language_id, $navigation, $parent_id, $level, $navcode, $from_level, $to_level) {
    $spaces = "";
    $count = 1;
    while($count < $level) {
        $spaces .= "&nbsp;&nbsp;&nbsp;";
        $count++;
    }
    $parent_id_query = ($parent_id == 0) ? " AND parent_id IS NULL" : " AND parent_id = '" . $parent_id."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_navigation WHERE main_site_id = '" . $site_id . "' AND main_language_id = '" . $language_id."'" . $parent_id_query . " ORDER BY sorting ASC");
    $show_level = (($level >= $from_level) && ($level <= $to_level)) ? true : false;
    if(@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $newnavcode = $navcode . $nav["code"] . "/";
            $active = "";
            if ($show_level) {
                if ($nav["id"] == $navigation["id"]) {
                    echo "\n<option selected=\"selected\" value=\"" . $nav["id"] . "\">" . $spaces . $nav["menu_name"] . "</option>";
                } else {
                    echo "\n<option " . $active . "value=\"" . $nav["id"] . "\">" .  $spaces . $nav["menu_name"] . "</option>";
                }
                navigation_select_rek($site_id, $language_id, $navigation, $nav["id"], ($level+1),$newnavcode, $from_level, $to_level );
            }
        }
    } else {
        if ($level == 1) {
            echo "\n<option selected=\"selected\" value=\"0\"> - </option>";
        }
    }
    return true;
}

function page_select($name,$selectname = "input_forward_page_id", $main_navigation, $disabled = FALSE, $onchange="") {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    $selected_new_site = $main_navigation['forward_page_id'] == "new_site" ? 'selected="selected"' : '';
    $selected_new_site_from_template = $main_navigation['forward_page_id'] == "new_site_from_template" ? 'selected="selected"' : '';

    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\" onchange=\"" . $onchange . "\">";

    echo "\n<option value=\"0\">" . $translation->get('please_choose') . "</option>";

    echo "\n<option value=\"new_site\" $selected_new_site>" . $translation->get('new_site') . "</option>";
    echo "\n<option value=\"new_site_from_template\" $selected_new_site_from_template>" . $translation->get('new_page_from_template') . "</option>";

    echo "\n<option value=\"\" disabled=\"disabled\">--------------------------</option>";

    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_page WHERE main_language_id = '" . $GLOBALS["language"]['id'] . "' ORDER BY id ASC");
    if(@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $selected = '';
            if($main_navigation['forward_page_id'] == $nav['id']) {
                $selected = 'selected="selected"';
            }

            echo "\n<option {$selected} value=\"" . $nav["id"] . "\">" . $nav["title"] . "</option>";
        }
    } else {
        echo "\n<option selected=\"selected\" value=\"0\"> - </option>";
    }

    echo "</select></div>";
}

function template_select($name,$selectname = "input_template_id", $template_id, $disabled = FALSE) {

    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . $name . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"bigselect\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    echo "\n<option value=\"0\">" . $translation->get('please_choose') . "</option>";

    $result = @mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_page WHERE main_language_id = '" . $GLOBALS["language"]['id'] . "' AND is_template = 1 ORDER BY id ASC");
    if(@mysqli_num_rows($result) > 0) {
        while ($nav = @mysqli_fetch_array($result)) {
            $selected = '';
            if($template_id == $nav['id']) {
                $selected = 'selected="selected"';
            }

            echo "\n<option {$selected} value=\"" . $nav["id"] . "\">" . $nav["title"] . "</option>";
        }
    } else {
        echo "\n<option selected=\"selected\" value=\"0\"> - </option>";
    }

    echo "</select></div>";
}

// Erzeugt Drop-Down der Sprachen

function language_select($name,$selectname = "input_language_id", $value = NULL, $disabled = FALSE, $site = FALSE, $chooseText = FALSE, $onchange = "", $chooseAll = false) {
    $translation  = \DynCom\dc\common\classes\Registry::get("translation");

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    echo "<div class=\"label\"><label for=\"" . $selectname . "\">" . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . "</label></div>";
    echo "<div class=\"input\"><select" . $disabled_text . " class=\"select\" name=\"" . $selectname . "\" id=\"" . $selectname . "\">";

    if ($chooseText === TRUE) {
        echo "<option value=\"\">" . $translation->get("please_choose") . "</option>";
    }

    if($site != FALSE){
        $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_language WHERE main_site_id = '" . $site . "'");
        if(mysqli_num_rows($result) > 0) {

            if ($chooseAll === TRUE) {
                echo "<option value=\"all\">" . $translation->get("all") . "</option>";
            }

            while($language = mysqli_fetch_array($result)) {
                if($language["id"] == $value) {
                    echo "<option selected=\"selected\" value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
                } else {
                    echo "<option value=\"" . $language["id"] . "\">" . $language["name"] . "</option>";
                }
            }
        } else {
            echo "<option value=\"\"> - </option>";
        }
    } else {
        echo "<option value=\"\"> - </option>";
    }
    echo "</select></div>";
}


// Gibt den Namen des Benutzers benutzers anhand seiner id zurück

function navigation_get_modified_admin_user($admin_user_id){
    $query = "SELECT name FROM main_admin_user WHERE id = '".$admin_user_id."' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    if($result){
        $val = @mysqli_fetch_array($result);
        return $val['name'];
    }
}

function cleanFilename($name) {
    $nameLower = strtolower($name);
    $nameNoSpaces = str_replace(' ','_',$nameLower);
    $stringAlNum = preg_replace('/[^a-zA-Z-_.]/','',$nameNoSpaces);
    return $stringAlNum;
}

function addhttp($url) {
    if($url == "") {
        return "";
    }

    // pruefen ob es sich um eine externe domain handelt
    $hostname = getHost($url);
    if($hostname == "") {
        return $url;
    }

    // keinen domain namen gefunden, also setze http davor
    if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function getHost($Address) {
    $parseUrl = parse_url(trim($Address));
    return trim($parseUrl["host"] ? $parseUrl["host"] : array_shift(explode('/', $parseUrl["path"], 2)));
}

function show_changed_on($_id, $_table) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");
    $query = "SELECT modified_date FROM " . $_table . " WHERE id = '" . $_id. "'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $val = @mysqli_fetch_array($result);

    text($translation->get("changed_on") . ":", date("d.m.Y H:i", $val['modified_date']));
}

function show_changed_by($_id, $_table) {
    $translation = \DynCom\dc\common\classes\Registry::get("translation");

    $query = "SELECT modified_user FROM " . $_table . " WHERE id = " . $_id;
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $val = @mysqli_fetch_array($result);

    $query = "SELECT name from main_admin_user where id = '" . $val['modified_user']."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $val = @mysqli_fetch_array($result);

    text($translation->get("changed_by") . ":", $val['name']);
}

function get_user_site_id() {
    if((int)$GLOBALS["admin_user"]['main_site_id'] > 0) {
        // pruefen ob seite existiert
        $query = "SELECT id FROM main_site where id = '" . $GLOBALS["admin_user"]['main_site_id']."'";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) > 0) {
            return $GLOBALS["admin_user"]['main_site_id'];
        }
    }

    // keine seite zugeordnet order nicht gefunden
    $query = "SELECT id FROM main_site order by id limit 1";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $val = @mysqli_fetch_array($result);
    return $val['id'];
}

function setup_get_allowed_site() {
    $setup  = array();

    $query = "SELECT main_site_id FROM main_site_admin_user_link where main_admin_user_id = '" . (int)$GLOBALS["admin_user"]['id'] . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $row = @mysqli_fetch_array($result);

    $query                     = "SELECT id from main_site WHERE id = '" . $row['main_site_id']."'";
    $result                    = @mysqli_query($GLOBALS['mysql_con'], $query);
    $row                       = @mysqli_fetch_array($result);
    $setup['std_main_site_id'] = $row['id'];
    return $setup;
}

function collection_exists() {
    $query = "SELECT count(*) as anzahl FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    $val = @mysqli_fetch_array($result);
    if($val['anzahl'] > 0) {
        return true;
    } else {
        return false;
    }
}

function count_all_files() {
    // WICHTIG:
    // resolveUrl() muss irgendwo definiert sein als leere funktion.

    $config = array();
    $not_include = true;
    include CMS_PATH . '../plugins/ckfinder/config.php';
    $resources = $config['resourceTypes'];
    $filecount = 0;
    foreach($resources as $res) {
        $path = rtrim(dirname(dirname(__DIR__)),'/\\') .  $res['absoluteUrl'] . DIRECTORY_SEPARATOR;
        $path = str_replace(['\\','/','\\\\','//'],DIRECTORY_SEPARATOR,$path);
        $Directory = new RecursiveDirectoryIterator($path);
        $Iterator = new RecursiveIteratorIterator($Directory, RecursiveIteratorIterator::SELF_FIRST);

        $Iterator->rewind();

        while($Iterator->valid()) {
            if ($Iterator->isDir() || $Iterator->isDot() || !$Iterator->isFile()) {
                $Iterator->next();
                continue;
            }

            $filecount++;

            $Iterator->next();
        }

    }

    return $filecount;
}

/**
 * Wird aufgerufen bevor headers gesetzt sind z.b. fuer redirects
 */
function before_header() {
    if(isset($_GET['preview_page'])){

        $input_page_id = $_GET['input_page_id'];

        if ((int)$_GET["shoppingworld"] === 1) {

            $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
            $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
            $pdoUser = getenv('MAIN_MYSQL_DB_USER');
            $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
            $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

            $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

            $prepStatement = "SELECT * FROM shop_category WHERE in_use_with_page_id = :input_page_id LIMIT 1";
            $params = [
                [':input_page_id', (int)$input_page_id, PDO::PARAM_INT],
            ];
            $pdo->setQuery($prepStatement);
            $pdo->prepareQuery();
            $pdo->bindParameters($params);
            $pdo->executePreparedStatement();
            $result = $pdo->getResultArray();

            if (count($result) === 1) {
                $url = '/' . customizeUrl() . '/';
                $navigationCodeArray[] = $result[0]["code"];
                $lineNo = $result[0]["parent_line_no"];
                while ($lineNo != 0) {
                    $query = "SELECT code, parent_line_no FROM shop_category WHERE company = :company AND shop_code = :shop_code AND language_code = :language_code AND line_no = :parent_line_no LIMIT 1";
                    $params = [
                        [':company', $result[0]["company"], PDO::PARAM_STR],
                        [':shop_code', $result[0]["shop_code"], PDO::PARAM_STR],
                        [':language_code', $result[0]["language_code"], PDO::PARAM_STR],
                        [':parent_line_no', (int)$lineNo, PDO::PARAM_INT],
                    ];
                    $pdo->setQuery($prepStatement);
                    $pdo->prepareQuery();
                    $pdo->bindParameters($params);
                    $pdo->executePreparedStatement();
                    $result2 = $pdo->getResultArray();
                    if (count($result2) === 1) {
                        $lineNo = $result2[0]["parent_line_no"];
                        $navigationCodeArray[] = $result2[0]["code"];
                    } else {
                        $lineNo = 0;
                    }
                }
                $navigationCodeArray = array_reverse($navigationCodeArray);
                foreach ($navigationCodeArray as $navigationCode) {
                    $url .= $navigationCode . '/';
                }

                headerFunctionBridge("Location: " . $url);
            }

            exit();
        }

        // pruefen ob menupunkt existiert
        $page_navigation = array();
        $query = "SELECT id FROM main_navigation WHERE forward_type = 1 and forward_page_id = '" . $input_page_id . "' LIMIT 1";
        $result = @mysqli_query($GLOBALS['mysql_con'],$query);
        if(@mysqli_num_rows($result) == 1) {
            $page_navigation = @mysqli_fetch_array($result);
            $targetUrl = "http://" . $_SERVER['HTTP_HOST'] . get_link_to_navigation($page_navigation['id']);

            headerFunctionBridge("Location: " . $targetUrl);
            exit();
        }
    }

    return;
}

function is_site_allowed($siteId) {
    $query = "SELECT * FROM main_site_admin_user_link where main_admin_user_id = '" . (int)$GLOBALS["admin_user"]['id']."'";
    $result = @mysqli_query($GLOBALS['mysql_con'],$query);
    if(@mysqli_num_rows($result) == 0) {
        return true;
    }

    while($row = mysqli_fetch_array($result)) {
        if($row['main_site_id'] == $siteId) {
            return true;
        }
    }

    return false;
}


function get_false_password_admin_login_counter($ipHash,$loginHash)
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " 
            
            SELECT 
    false_login.*
FROM
    false_login
WHERE
    ( false_login.ip_hash = :ip_hash  OR false_login.login_hash = :login_hash )
        ";
    $params = [
        [':ip_hash', $ipHash, PDO::PARAM_STR],
        [':login_hash', $loginHash, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $result = $pdo->getResultArray();
    return $result;
}
function add_false_password_admin_loign_counter($ipHash,$loginHash, $counter = 1, $nextLoginTime, $blockCounter = 0  )
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " 
            
            INSERT INTO `false_login`
                (`ip_hash`, `login_hash`, `total_counter`, `next_login_time`, block_counter)
                VALUES 
                (:ip_hash ,:login_hash, :counter, :next_login_time, 0)
                ON DUPLICATE KEY UPDATE block_counter = :block_counter , next_login_time = :next_login_time

                        ";

    if($counter == 1)
    {
        $prepStatement = " 
            
            INSERT INTO `false_login`
                (`ip_hash`, `login_hash`, `total_counter`, `next_login_time`, block_counter)
                VALUES 
                (:ip_hash ,:login_hash, :counter, :next_login_time, :block_counter)
                ON DUPLICATE KEY UPDATE total_counter = total_counter + 1 , next_login_time = :next_login_time

                        ";
    }
    $params = [
        [':ip_hash', $ipHash, PDO::PARAM_STR],
        [':login_hash', $loginHash, PDO::PARAM_STR],
        [':counter', $counter, PDO::PARAM_STR],
        [':next_login_time', $nextLoginTime, PDO::PARAM_STR],
        [':block_counter', $blockCounter, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
}


function delete_false_admin_password_loign_counter($ipHash,$loginhash)
{
    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " 
            
            Delete From  `false_login` where ip_hash = :ip_hash or login_hash = :login_hash

                        ";
    $params = [
        [':ip_hash', $ipHash, PDO::PARAM_STR],
        [':login_hash', $loginhash, PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
}

function getAdminUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function category_select(PDO $pdo,$company, $categoryShopCode, $languageCode, $parentLineNo, $label, $name, $preselectedLineNo, $disabled = false)
{

    $translation    = \DynCom\dc\common\classes\Registry::get("translation");

    static $query = '
        SELECT 
            line_no,
            name,
            level
        FROM
          shop_category
        WHERE
          company = :company AND
          shop_code = :category_shop_code AND 
          language_code = :language_code AND 
          (:parent_line_no = 0 OR parent_line_no = :parent_line_no)
    ';
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':company',$company,PDO::PARAM_STR);
    $stmt->bindValue(':category_shop_code',$categoryShopCode,PDO::PARAM_STR);
    $stmt->bindValue(':language_code',$languageCode,PDO::PARAM_STR);
    $stmt->bindValue(':parent_line_no',$parentLineNo,PDO::PARAM_INT);

    if (!$stmt->execute()) {
        $errorInfo = $pdo->errorInfo();
        $errorString = implode($errorInfo,PHP_EOL);
        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
    }

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $arr = $stmt->fetchAll();

    $disabled_text = ($disabled) ? " disabled=\"disabled\"" : "";
    $html = "<div class=\"label\">" . PHP_EOL;
    $html .= "  <label for=\"" . $name . "\">" . $label . "</label>" . PHP_EOL;
    $html .= "</div>" . PHP_EOL;
    $html .= "<div class=\"input\">" . PHP_EOL;
    $html .= "  <select" . $disabled_text . " class=\"bigselect\" name=\"" . $name . "\" id=\"" . $name . "\">" . PHP_EOL;
    $html .= "    <option value=\"0\">" . $translation->get("category_root") . "</option>" . PHP_EOL;
    foreach ($arr as $category) {
        $defaultSpacing = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $spacing = str_repeat($defaultSpacing,(int)$category['level']);
        $selectedStr = ((int)$category['line_no'] === $preselectedLineNo) ? ' selected=\"selected\" ' : '';
        $html .= "$spacing<option value=\"{$category['line_no']}\"$selectedStr>$spacing{$category['name']}</option>";
    }
    $html .= '  </select>' . PHP_EOL;
    $html .= '</div>';
    return $html;

}

function on_shopping_world(): bool
{
    return ($_GET["level_2"] === 'shoppingworld' ? TRUE : FALSE);
}

function on_templates(): bool
{
    return ($_GET["level_2"] === 'templates' ? TRUE : FALSE);
}

function copy_main_page($input_page_id, $input_language_id, $input_site_id) {
    $languageIds = array();

    // pruefen ob die language Id gleich ist
    if($input_language_id == "all") {
        $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_language WHERE main_site_id = '" . $input_site_id."'");
        while($language = mysqli_fetch_array($result)) {
            $languageIds[] = $language["id"];
        }
    } else {
        $languageIds[] = $input_language_id;
    }

    foreach($languageIds as $languageId) {
        $query = "DELETE FROM copy_link WHERE session_id = '" . session_id() . "'";
        @mysqli_query($GLOBALS['mysql_con'], $query);

        $LanguageFunctions = new LanguageFunctions($GLOBALS["language"]['id'], null, $languageId);
        $LanguageFunctions->set_noCopyPageCollectionGroupLink();
        $LanguageFunctions->set_copyOnlySpecificSiteparts();

        // Bausteinzuordnungen bei einer anderen Sprache nicht mitkopieren
        if($languageId != $GLOBALS["language"]['id']) {
            $LanguageFunctions->set_noCopyPageComponentIncludeLink();

            // wenn die andere sprache ein anderes layout hat, dann die layout inclusion links nicht kopieren
            $query = "SELECT main_layout_id from main_language WHERE id = '" . $languageId."'";
            $layoutResult = @mysqli_query($GLOBALS['mysql_con'], $query);
            $layoutRow = mysqli_fetch_array($layoutResult);
            if($GLOBALS["language"]["main_layout_id"] != $layoutRow['main_layout_id']) {
                $LanguageFunctions->set_noCopyPageLayoutInclusionLink();
            }
        }

        $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
        $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
        $pdoUser = getenv('MAIN_MYSQL_DB_USER');
        $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
        $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

        $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

        $prepStatement = " SELECT
                  main_sitepart_header_id,    main_sitepart_id 
                  FROM 
                  main_page_link
                   WHERE
                    main_page_id = :main_page_id  AND main_sitepart_id > 0   ";
        $params = [
            [':main_page_id', $input_page_id, PDO::PARAM_STR],
        ];
        $pdo->setQuery($prepStatement);
        $pdo->prepareQuery();
        $pdo->bindParameters($params);
        $pdo->executePreparedStatement();
        $lineResult = $pdo->getResultArray();

        foreach( $lineResult as $lineRow ) {
            $LanguageFunctions->addSitePartId($lineRow['main_sitepart_id']);
            $LanguageFunctions->addSitePartHeaderId($lineRow['main_sitepart_header_id']);
        }

        $LanguageFunctions->copy_siteparts();
        $LanguageFunctions->copy_main_page($input_page_id, true);
    }

    return $LanguageFunctions->get_new_id($input_page_id, "main_page");
}