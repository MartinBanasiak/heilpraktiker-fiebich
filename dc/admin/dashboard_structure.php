<?php

function resolveUrl( $_url ) {
    //dummy function needed for ckfinder config
}

$siteparts     = \DynCom\dc\common\classes\Siteparts::get();
$pageIds       = array();
$sitepartCount = array();
$formname      = "form_edit_sitepart";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");

$action = (isset($_GET['action']) ? $_GET['action'] : "");

if ($action != "" && $action != "admin_login") {
    require_once(CMS_PATH . "admin/edit_page.inc.php");
    exit();
}

$adminMenu = \DynCom\dc\common\classes\Adminmenu::get();

$subsites       = $adminMenu[$_GET["level_1"]]['subsites'];
$count_subsites = array();

// seiten zaehlen
$query                   = "SELECT count(*) AS anzahl FROM main_page WHERE main_language_id = '" . $GLOBALS["language"]['id']."' AND is_shopping_world = 0 and is_template = 0";
$result                  = @mysqli_query($GLOBALS['mysql_con'], $query);
$row                     = @mysqli_fetch_array($result);
$count_subsites['sites'] = $row['anzahl'];

// Einkaufswelten zaehlen
$query                   = "SELECT count(*) AS anzahl FROM main_page WHERE main_language_id = '" . $GLOBALS["language"]['id']."' AND is_shopping_world = 1";
$result                  = @mysqli_query($GLOBALS['mysql_con'], $query);
$row                     = @mysqli_fetch_array($result);
$count_subsites['shoppingworld'] = $row['anzahl'];

// Templates zaehlen
$query                   = "SELECT count(*) AS anzahl FROM main_page WHERE main_language_id = '" . $GLOBALS["language"]['id']."' AND is_template = 1";
$result                  = @mysqli_query($GLOBALS['mysql_con'], $query);
$row                     = @mysqli_fetch_array($result);
$count_subsites['templates'] = $row['anzahl'];

// navigation zaehlen
$query                        = "SELECT count(*) AS anzahl FROM main_navigation WHERE main_site_id = '" . $site["id"] . "' AND main_language_id = '" . $GLOBALS["language"]['id']."'";
$result                       = @mysqli_query($GLOBALS['mysql_con'], $query);
$row                          = @mysqli_fetch_array($result);
$count_subsites['navigation'] = $row['anzahl'];

// Bausteine zaehlen
$query                        = "SELECT count(*) AS anzahl FROM main_component WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
$result                       = @mysqli_query($GLOBALS['mysql_con'], $query);
$row                          = @mysqli_fetch_array($result);
$count_subsites['components'] = $row['anzahl'];

// dateien
$count_subsites['files'] = count_all_files();

?>

<div id="mainContent">
    <?php echo current_website_language($site, $language);?>
    <h1><?php echo get_translation('top_structure'); ?></h1>

    <div class="dashboard_part_left">
        <h2><?php echo get_translation('main_menu'); ?></h2>


        <?php
        foreach ($subsites as $code => $subsite) {
            ?>
            <a class="dashoard_box button_inhalt_collection"
               href="<?php echo get_menu_link($_GET["level_1"] . "/" . $code, $site, $language); ?>">
                <span class="dashboard_box_number"><?php echo $count_subsites[$code]; ?></span>
                <span class="dashboard_box_description"><?php echo $subsite['name']; ?></span>
                <img src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/structure/<?php echo $subsite['icon']; ?>" />
            </a>
            <?php
        }
        ?>
    </div>

    <div class="dashboard_part_right">
        <h2><?php echo get_translation('last_changed_sites'); ?></h2>

        <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" class="selected_linklist_row" name="input_page_id" value="" />

            <div class="requestLoader"></div>
            <div id="page_linklist">
                <?
                $query = "SELECT main_page.id, title AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_page left join main_admin_user ON main_page.modified_user = main_admin_user.id where main_language_id = '" . (int)$GLOBALS["language"]['id'] . "' order by modified_date desc";
                //echo $query;
                $format = array('option', 'text', 'text', 'text');
                if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
                    $dbl_click_editing_mode = (($GLOBALS['admin_user']['edit_mode'] == 0) ? "edit_live_content_page" : "edit_content_page");
                    linklist($result, $formname, $format, "input_page_id", $dbl_click_editing_mode);
                }
                ?>
            </div>
        </form>
    </div>