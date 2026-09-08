<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_page_link_layout_class_list";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php echo $translation->get("assign_page_link_layout_class"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("back"), $formname, "loadCard('edit_line_properties_page', true)"); ?>
    <?= button("link", $translation->get("assoc_with_content_element"), $formname, "loadCard('assoc_page_link_layout_class_page')"); ?>

</ul>

<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" class="selected_linklist_row" name="input_layout_class_id" value="" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_id" value="<?php echo $_REQUEST["input_id"]; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />

    <div class="requestLoader"></div>
    <?
    $query  = "SELECT id,  name AS '" . $translation->get("description") . "', code as '" . $translation->get("textkey") . "' from main_layout_class WHERE main_layout_id = '" . $GLOBALS["language"]["main_layout_id"] . "' ORDER by sorting ASC";
    $format = array('option', 'text', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_layout_class_id", "assoc_page_link_layout_class_page");
    }
    ?>

</form>