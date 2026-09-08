<?
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_layout_class_list";
?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_layout["id"] ?>">
    <input type="hidden" class="selected_linklist_row" name="input_class_id" value="" />

    <h2><?php echo $translation->get("layout_classes"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_class', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_class')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_class', false, '{$translation->get('delete_layout_class_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_class')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_class')", "", FALSE); ?>
    </ul>
    <div class="clearfix"></div>

    <?
    $query  = "SELECT id,  name AS '" . $translation->get("description") . "', code as '" . $translation->get("textkey") . "' from main_layout_class WHERE main_layout_id = '" . $input_layout["id"] . "' ORDER by sorting ASC";
    $format = array('option', 'text', 'text', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_class_id", "edit_class", TRUE, "update_sortorder_class");
    }
    ?>

</form>