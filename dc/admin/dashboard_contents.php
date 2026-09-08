<?php
$siteparts     = \DynCom\dc\common\classes\Siteparts::get();
$pageIds       = array();
$sitepartCount = array();
$formname      = "form_edit_sitepart";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");

$action = (isset($_GET['action']) ? $_GET['action'] : "");

if ($action != "") {

    $sitepart_id = null;
    if(isset($_POST['data-sitepartid']) && !empty($_POST['data-sitepartid'])) {
        $sitepart_id = (int) $_POST['data-sitepartid'];
    }

    if($sitepart_id === null) {
        $subaction        = substr($action, strrpos($action, '_') + 1);
        foreach($siteparts as $id => $sitepartData) {
            if($subaction == $sitepartData['code']) {
                $sitepart_id = $id;
                break;
            }
        }
    }

    $sitepartcode = $siteparts[$sitepart_id]['code'];
    $sitepartfolder = $siteparts[$sitepart_id]['folder'];

    require_once(realpath(MODULE_PATH) . DIRECTORY_SEPARATOR . $sitepartfolder . DIRECTORY_SEPARATOR . "edit_" . $sitepartcode . ".inc.php");

    exit();
}
?>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_contents'); ?></h1>

    <div class="dashboard_part_left">
        <h2><?php echo get_translation('all_contents'); ?></h2>

        <?php foreach ($siteparts as $sitepartId => $sitepartData): ?>

            <?php
            if (isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === TRUE) {
                continue;
            }

            // anzahl der erstellten siteparts berechnen
            $query  = "SELECT count(*) AS anzahl FROM " . $sitepartData['header_table'] . " WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) AND collection_header = 0";
            $result = @mysqli_query($GLOBALS['mysql_con'], $query);
            $row    = @mysqli_fetch_array($result);
            $anzahl = 0;
            if (isset($row['anzahl'])) {
                $anzahl = $row['anzahl'];
            }
            ?>

            <a class="dashoard_box button_<?php echo $sitepartData['class']; ?>"
               href="<?php echo get_menu_link($_GET["level_1"] . "/" . $sitepartData['code'], $site, $language); ?>">
                <span class="dashboard_box_number"><?php echo $anzahl; ?></span>
                <span class="dashboard_box_description"><?php echo $sitepartData['description']; ?></span>
            </a>

        <?php endforeach; ?>

    </div>

    <div class="dashboard_part_right">
        <h2><?php echo get_translation('last_changed_contents'); ?></h2>

        <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
            <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

            <div class="requestLoader"></div>
            <?php
            $query = "SELECT *, main_admin_user.name AS username FROM main_sitepart_changes left join main_admin_user ON main_sitepart_changes.modified_user = main_admin_user.id WHERE main_language_id = '" . (int)$GLOBALS["language"]['id'] . "' ORDER BY modified_date DESC LIMIT 0,20";

            if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
                $num_rows = @mysqli_num_rows($result);
                if ($num_rows >= 1) {
                    echo "  <table cellpadding=0 cellspacing=0 border=0 class=\"linklist\">\n";

                    echo "<tr>";
                    echo format_key($translation->get("type"), "");
                    echo format_key($translation->get("description"), "");
                    echo format_key($translation->get("changed_on"), "");
                    echo format_key($translation->get("changed_by"), "");
                    echo "</tr>";

                    echo "<tr>";
                    echo format_seperator("");
                    echo format_seperator("");
                    echo format_seperator("");
                    echo format_seperator("");
                    echo "</tr>";

                    while ($row = @mysqli_fetch_array($result, 1)) {
                        $sitepartDescription      = "";
                        $query_description        = "SELECT description FROM " . $siteparts[$row['main_sitepart_id']]['header_table'] . " WHERE id = '" . $row['main_sitepart_header_id']."'";
                        $result_description       = @mysqli_query($GLOBALS['mysql_con'], $query_description);
                        $row_sitepart_description = @mysqli_fetch_array($result_description);

                        $sitepartType = $siteparts[$row['main_sitepart_id']]['description'];
                        $sitepartCode = $siteparts[$row['main_sitepart_id']]['code'];

                        echo "    <tr class=\"linklist_content\" data-sitepartid=\"{$row['main_sitepart_id']}\" data-checked=\"{$checkedRow}\" data-inputname=\"input_id\" data-action=\"edit_" . $siteparts[$row['main_sitepart_id']]['code'] . "\" data-inputid=\"{$row['main_sitepart_header_id']}\">\n";

                        echo format_value($sitepartType, "input_id");
                        echo format_value($row_sitepart_description['description'], "input_id");
                        echo format_value(date("d.m.Y H:i", $row['modified_date']), "input_id");
                        echo format_value($row['username'], "input_id");

                        echo "    </tr>\n";
                    }

                    echo "  </table>\n";
                } else {
                    echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
                }

            }
            ?>
        </form>
    </div>