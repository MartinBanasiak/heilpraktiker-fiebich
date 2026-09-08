<?php
use DynCom\dc\common\classes\CollectionTypes;

$translation     = \DynCom\dc\common\classes\Registry::get("translation");
$formname        = "form_line_list";
$inputname       = "input_id";
$siteparts       = \DynCom\dc\common\classes\Siteparts::get();
$collectionTypes = \DynCom\dc\common\classes\CollectionTypes::get();
?>

<script type="text/javascript">
    function toggleScrollerHeadline() {
        var contentscroller = $('.inhalte_toggle_container');
        contentscroller.show();
    }

    function contentmenuClick( _el, _fieldtype, _type_id ) {
        setCurrentToolbarClicked(_el);
        jQuery('input[name="fieldtype"]', jQuery('#<?php echo $formname; ?>')).val(_fieldtype);
        jQuery('input[name="type_id"]', jQuery('#<?php echo $formname; ?>')).val(_type_id);
        loadCard('new_line_collection_setup', true);
    }
</script>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_line_id" value="" />

    <input type="hidden" name="fieldtype" value="" />
    <input type="hidden" name="type_id" value="" />

    <h2><?php echo $translation->get("collection_field_headline"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "toggleScrollerHeadline();", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_line_collection_setup')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_collection_setup', false, '{$translation->get('delete_setup_line_collection_confirm')}')", "", FALSE); ?>

        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_collection_setup')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_collection_setup')", "", FALSE); ?>
    </ul>
    <div class="clearfix"></div>

    <div class="inhalte_toggle_container" id="inhalte_form_headline">
        <div id="srollerInhalteHeadline"><h2><?php echo $translation->get("insert_new_field"); ?></h2></div>
    </div>
    <div id="pagecontentScroller" class="pagecontentScroller inhalte_toggle_container">
        <div class="scroller_inhalte">

            <ul class="inhalte_menu">
                <?php
                button('inhalt_eingabefeld graybox', "Eingabefeld", $formname, "contentmenuClick(this, 'standard', 0)");

                foreach ($siteparts as $sitepartId => $sitepartData) {
                    if ((isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === TRUE) && $sitepartId != 14) {
                        continue;
                    }

                    button($sitepartData['class'], $sitepartData['description'], $formname, "contentmenuClick(this, 'siteparts', " . $sitepartId . ")");
                }

                ?>
            </ul>

        </div>
    </div>

    <?php
    $query = "SELECT * FROM main_collection_setup_content WHERE main_collection_setup_id = '" . $input_id . "' ORDER BY sorting ASC";

    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        $num_rows = @mysqli_num_rows($result);
        if ($num_rows >= 1) {
            echo "  <table data-sortableupdateaction=\"update_sortorder_collection_setup\" cellpadding=0 cellspacing=0 border=0 class=\"linklist sortableTable\">\n";

            echo "<tr>";
            echo format_key($translation->get("type"), "");
            echo format_key("", "");
            echo format_key($translation->get("description"), "");
            echo format_key($translation->get("show_in_teaser"), "");
            echo "</tr>";

            echo "<tr>";
            echo format_seperator("");
            echo format_seperator("");
            echo format_seperator("");
            echo format_seperator("");
            echo "</tr>";

            while ($row = @mysqli_fetch_array($result, 1)) {

                if ($row['fieldtype'] == 'siteparts') {
                    // die zeile bezieht sich auf ein sitepart
                    $sitepartType = $translation->get("sitepart") . ": " . $siteparts[$row['type_id']]['description'];
                } else {
                    // zeile ist ein extra datentyp fuer kollektionen
                    $sitepartType = $translation->get("input_field") . ": " . $collectionTypes[$row['type_id']]['description'];
                }

                $checkedRow = $row['id'] == $_REQUEST["input_line_id"] ? 'true' : 'false';

                echo "    <tr id=\"linklistid_{$row['id']}\" class=\"linklist_content\" data-checked=\"{$checkedRow}\" data-inputname=\"input_id\" data-action=\"edit_line_collection_setup\" data-inputid=\"{$row['id']}\">\n";

                echo format_value($sitepartType, "input_id");
                echo format_value("", "sorticon", "sort");
                echo format_value($row['fieldname'], "input_id");
                echo format_value($row['is_teaser'], "input_id", "boolean");

                echo "    </tr>\n";
            }

            echo "  </table>\n";
        } else {
            echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";
        }
    }
    ?>
</form>