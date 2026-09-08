<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_line_gallery_list_" . $fieldSetup['id'];
$inputname           = "input_id";
$input_page_id       = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" class="selected_linklist_row" name="input_line_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_setup_content_id" value="<?php echo $fieldSetup['id']; ?>" />

    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_line_gallery', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_line_gallery')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_gallery', false, '{$translation->get('delete_line_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_gallery')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_gallery')", "", FALSE); ?>

    </ul>
    <div class="clearfix"></div>

    <?

    $query    = "SELECT * FROM main_collection_link WHERE main_collection_id = " . $collectionData['id'] . " AND main_collection_setup_content_id = " . $fieldSetup['id'];
    $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
    $num_rows = @mysqli_num_rows($result);
    if ($num_rows == 0) {
        echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div></form>";
        return;
    }

    $row = @mysqli_fetch_array($result, 1);

    $query = "SELECT id, preview AS '" . $translation->get("preview") . "', description AS '" . $translation->get("description") . "' FROM gallery_line WHERE header_id = " . $row['main_sitepart_header_id'] . " ORDER BY sorting ASC";
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_line_id", "edit_line_gallery", TRUE, "update_sortorder_gallery");
    }
    ?>
</form>