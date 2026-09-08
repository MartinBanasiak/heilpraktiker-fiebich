<?
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_page_link_layout_class_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_line["id"] ?>">
    <input type="hidden" class="selected_linklist_row" name="input_page_link_layout_class_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_id" value="<?php echo $_REQUEST["input_id"]; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />

    <h2><?php echo $translation->get("layout_classes"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_page_link_layout_class_page', true)", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_page_link_layout_class_page', false, '{$translation->get('delete_layout_class_confirm')}')", "", FALSE); ?>
        <?= button("up", $translation->get("go_up"), $formname, "loadCard('moveup_page_link_layout_class_page')", "", FALSE); ?>
        <?= button("down", $translation->get("go_down"), $formname, "loadCard('movedown_page_link_layout_class_page')", "", FALSE); ?>
    </ul>
    <div class="clearfix"></div>

    <?
    $query  = " SELECT main_page_link_layout_class_link.id,  main_layout_class.name AS '" . $translation->get("description") . "', main_layout_class.code AS '" . $translation->get("code") . "'
                from main_page_link_layout_class_link inner join main_layout_class on main_page_link_layout_class_link.main_layout_class_id = main_layout_class.id 
                WHERE main_page_link_id = '" . $input_line["id"] . "' ORDER by main_page_link_layout_class_link.sorting ASC";
    $format = array('option', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_page_link_layout_class_id", "edit_line_properties_page", TRUE, "update_sortorder_page_link_layout_class_page");
    }
    ?>

</form>