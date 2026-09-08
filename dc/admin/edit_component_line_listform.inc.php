<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_component_field_list";
$main_layout_id     = $GLOBALS["language"]["main_layout_id"];
$siteparts          = \DynCom\dc\common\classes\Siteparts::get();
$input_component_id = $input_component["id"];
?>


<script type="text/javascript">
    var clickmode = 'new';

    function toggle_inhalte_scroller() {

        if (contentscroller.css('display') == 'none') {
            contentscroller.show();
        } else {
            //contentscroller.hide();
        }
    }

    function toggleScrollerHeadline( _headline, _clickAction ) {
        var contentscroller = $('.inhalte_toggle_container');
        $('#srollerInhalteHeadline h2').html(_headline);
        contentscroller.show();

        clickmode = _clickAction;
    }

    function contentmenuClick( _el, _module, _listOnly, _sitepartId ) {
        var listOnly = _listOnly || false;
        var sitepartId = _sitepartId || 0;
        setCurrentToolbarClicked(_el);

        if (listOnly === true) {
            loadCard('list_' + _module + '_page', true);
            return;
        }

        $('input[name="sitepart_id"]', $('#form_inhalte')).val(_sitepartId);

        if (clickmode == 'list') {
            loadCard('list_' + _module + '_page', true);
        } else {
            loadCard('new_' + _module, true);
        }
    }
</script>

<h2><?php echo $translation->get("top_contents"); ?></h2>
<ul class="toolbar_menu">
    <?= button("new", $translation->get("add_content2"), $formname, "toggleScrollerHeadline('" . $translation->get("add_content2") . "', 'new');", "", FALSE); ?>
    <?= button("link", $translation->get("add_content"), $formname, "toggleScrollerHeadline('" . $translation->get("add_content") . "', 'list');", "", FALSE); ?>
    <?= button("edit", $translation->get("edit_content"), $formname, "loadCard('edit_line_component')", "", FALSE); ?>

    <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_component')", "", FALSE); ?>
    <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_component')", "", FALSE); ?>
    <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_component', false, '{$translation->get('delete_assoc_component_line')}')", "", FALSE); ?>
</ul>
<div class="clearfix"></div>

<form id="form_inhalte" name="form_inhalte" method="post" class="inhalte_toggle_container">
    <input type="hidden" name="update_visitor_data" value="1" />
    <input type="hidden" name="sitepart_id" value="0" />
    <input name="input_component_id" type="hidden" value="<?= $input_component_id ?>">

    <div class="inhalte_toggle_container" id="inhalte_form_headline">
        <div id="srollerInhalteHeadline"><h2><?php echo $translation->get("insert_new_field"); ?></h2></div>
    </div>
    <div id="pagecontentScroller" class="pagecontentScroller inhalte_toggle_container">
        <div class="scroller_inhalte">
            <ul class="inhalte_menu">
                <?php
                foreach ($siteparts as $sitepartId => $sitepartData) {
                    // keine shop siteparts anzeigen
                    if (isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === TRUE) {
                        continue;
                    }

                    button($sitepartData['class'], $sitepartData['description'], 'form_inhalte', "contentmenuClick(this, '{$sitepartData['code']}', false, $sitepartId)");
                }

                button("inhalt_collection graybox", $translation->get("collection_preview"), 'form_inhalte', "contentmenuClick(this, 'collection_preview', true)");
                foreach ($siteparts as $sitepartId => $sitepartData) {
                    // nur shop siteparts anzeigen
                    if (!isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === FALSE) {
                        continue;
                    }

                    button($sitepartData['class'] . ' graybox', $sitepartData['description'], 'form_inhalte', "contentmenuClick(this, '{$sitepartData['code']}', false, $sitepartId)");
                }
                ?>
            </ul>
        </div>
    </div>
</form>

<div class="clearfix"></div>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_component_id" type="hidden" value="<?= $input_component_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

    <?
    // alle layoutareas
    $layoutareas = array();
    $query       = "SELECT * FROM main_layout_area WHERE main_layout_id = '" . $main_layout_id."'";
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        while ($row = @mysqli_fetch_array($result, 1)) {
            $layoutareas[$row['id']] = $row;
        }
    }

    $query = "SELECT
			main_component_link.id, 
			main_component_link.main_sitepart_id, 
			main_component_link.main_sitepart_header_id,
			main_component_link.main_collection_id, 
			main_component_link.main_collection_setup_id,
			from_unixtime(main_component_link.modified_date, '%d.%m.%Y %H:%i:%s') AS aenderungs_datum, 
			main_admin_user.name AS username 
		FROM
			main_component_link left join main_admin_user
		ON 
			main_component_link.modified_user = main_admin_user.id 
		WHERE
			main_component_link.main_component_id = " . $input_component_id . " 
		ORDER BY 
			sorting ASC";

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

            get_real_component_link_id();

            while ($row = @mysqli_fetch_array($result, 1)) {

                if ($row['main_collection_id'] > 0 || $row['main_collection_setup_id'] > 0) {
                    $sitepartType             = "Kollektion Vorschau";
                    $query_description        = "SELECT description FROM main_collection_setup WHERE id = '" . $row['main_collection_setup_id']."'";
                    $result_description       = @mysqli_query($GLOBALS['mysql_con'], $query_description);
                    $row_sitepart_description = @mysqli_fetch_array($result_description);
                    $dblClickAction           = "edit_collection_preview_page";
                } else {

                    if ((int)$row['main_sitepart_header_id'] <= 0) {

                        // siteparts, die keine header_table brauchen
                        $sitepartType                            = $siteparts[$row['main_sitepart_id']]['description'];
                        $row_sitepart_description['description'] = $siteparts[$row['main_sitepart_id']]['sub_description'];
                        $dblClickAction                          = "edit_line_component";

                    } else {

                        // siteparts mit einer header_table
                        $query_description        = "SELECT description FROM " . $siteparts[$row['main_sitepart_id']]['header_table'] . " WHERE id = '" . $row['main_sitepart_header_id']."'";
                        $result_description       = @mysqli_query($GLOBALS['mysql_con'], $query_description);
                        $row_sitepart_description = @mysqli_fetch_array($result_description);
                        $sitepartType             = $siteparts[$row['main_sitepart_id']]['description'];
                        $dblClickAction           = "edit_line_component";
                        if (empty($row_sitepart_description['description'])) {
                            $row_sitepart_description['description'] = $siteparts[$row['main_sitepart_id']]['description'];
                        }
                    }

                }

                $checkedRow = $row['id'] == $_REQUEST["input_id"] ? 'true' : 'false';
                echo "    <tr class=\"linklist_content\" data-sitepartid=\"{$row['main_sitepart_id']}\" data-checked=\"{$checkedRow}\" data-inputname=\"input_id\" data-action=\"" . $dblClickAction . "\" data-inputid=\"{$row['id']}\">\n";

                echo format_value($sitepartType, "input_id");
                echo format_value($row_sitepart_description['description'], "input_id");
                echo format_value($row['aenderungs_datum'], "input_id");
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