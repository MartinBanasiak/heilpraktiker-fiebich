<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_line_list";
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_line_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />

    <h2><?php echo $translation->get("scrollbar_field_headline"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_line_magicscroll', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_line_magicscroll')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_magicscroll', false, '{$translation->get('delete_line_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_magicscroll')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_magicscroll')", "", FALSE); ?>

    </ul>
    <div class="clearfix"></div>

    <?
    $query = "SELECT id, preview AS '" . $translation->get("preview") . "', description AS '" . $translation->get("description") . "', link AS '" . $translation->get("link") . "' FROM scrollbar_line WHERE header_id = '" . $input_id . "' ORDER BY sorting ASC";
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_line_id", "edit_line_magicscroll", TRUE, "update_sortorder_magicscroll");
    }
    ?>
</form>