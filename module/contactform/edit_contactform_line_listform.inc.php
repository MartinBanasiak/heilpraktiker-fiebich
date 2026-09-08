<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_field_list";
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_line_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />

    <h2><?php echo $translation->get("contact_field_headline"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_line_contactform', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_line_contactform')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_contactform', false, '{$translation->get('delete_contactform_field_confirm')}')", "", FALSE); ?>

        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_line_contactform')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_line_contactform')", "", FALSE); ?>

    </ul>
    <div class="clearfix"></div>

    <?
    $query  = "SELECT id, CASE typ WHEN 1 THEN '" . $translation->get("textfield") . "' WHEN 2 THEN '" . $translation->get("textarea") . "' WHEN 3 THEN '" . $translation->get("yesno") . "' WHEN 4 THEN '" . $translation->get("optionfield") . "' WHEN 5 THEN '" . $translation->get("headline") . "' WHEN 6 THEN '" . $translation->get("spacerow") . "' WHEN 7 THEN '" . $translation->get("title_news") . "' WHEN 8 THEN '" . $translation->get("file") . "' END AS 'Typ', code AS '" . $translation->get("textkey") . "', name AS '" . $translation->get("caption") . "', mandatory AS '" . $translation->get("mandatory_field") . "' FROM contactform_line WHERE header_id = '" . $input_id . "' ORDER BY sorting ASC";
    $format = array('option', 'text', 'text', 'text', 'boolean');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_line_id", "edit_line_contactform", TRUE, "update_sortorder_contactform");
    }
    ?>

</form>