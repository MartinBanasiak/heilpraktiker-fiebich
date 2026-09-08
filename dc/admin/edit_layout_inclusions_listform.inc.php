<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_layout_inclusions_list";
?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_layout["id"] ?>">
    <input type="hidden" class="selected_linklist_row" name="input_inclusion_id" value="" />

    <h2><?php echo $translation->get("edit_css_javascript_config"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_inclusion', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_inclusion')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_inclusion', false, '{$translation->get('delete_layout_inclusion_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_inclusion')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_inclusion')", "", FALSE); ?>
    </ul>
    <div class="clearfix"></div>
    <?
    $query  = "SELECT id, type AS '" . $translation->get("type") . "', path AS '" . $translation->get("path") . "', default_active AS '" . $translation->get("default_active") . "' FROM main_layout_inclusions WHERE main_layout_id = '" . $input_layout["id"] . "' ORDER by sorting ASC";
    $format = array('option', 'text', 'text', 'boolean');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_inclusion_id", "edit_inclusion", TRUE, "update_sortorder_inclusion");
    }
    ?>

</form>