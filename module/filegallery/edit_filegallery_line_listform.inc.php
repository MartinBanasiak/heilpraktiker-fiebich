<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_filegallery_line_list";
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_line_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />

    <h2><?php echo $translation->get("filegallery_field_headline"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_line_filegallery', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_line_filegallery')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_filegallery', false, '{$translation->get('delete_line_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_filegallery')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_filegallery')", "", FALSE); ?>

    </ul>
    <div class="clearfix"></div>

    <?
    $query = "SELECT id, description AS '" . $translation->get("description") . "', filename AS '" . $translation->get("filename") . "' FROM filegallery_line WHERE header_id = '" . $input_id . "' ORDER BY sorting ASC";
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_line_id", "edit_line_filegallery", TRUE, "update_sortorder_filegallery");
    }
    ?>
</form>