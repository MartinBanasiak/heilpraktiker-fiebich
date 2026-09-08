<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_template_list";
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
?>

<div id="overlaycrumb">
    <?php echo $translation->get("choose_template"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("back"), $formname, "loadCard('edit_page', true)"); ?>
    <?= button("save", $translation->get("from_template"), $formname, "loadCard('copy_and_save_page')"); ?>

</ul>

<div class="clearfix"></div>

<?php
if (is_array($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" class="selected_linklist_row" name="input_page_id" value="" />
    <input type="hidden" name="input_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="site_id" value="<?php echo $GLOBALS["site"]['id']; ?>" />
    <input type="hidden" name="main_language_id" value="<?php echo $GLOBALS["language"]['id']; ?>" />
    <input type="hidden" name="input_copy_from_template" value="1" />

    <div class="requestLoader"></div>
    <?
    $query = "SELECT main_page.id, title AS '" . $translation->get("description") . "', main_page.active AS '" . $translation->get("active") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_page left join main_admin_user ON main_page.modified_user = main_admin_user.id where main_page.is_shopping_world = 0 AND main_page.is_template = 1 AND main_language_id = '" . (int)$GLOBALS["language"]['id']."'";
    $format = array('option', 'text','boolean_active','text','text');
    if($result = @mysqli_query($GLOBALS['mysql_con'],$query)) {
        linklist($result,"form_page_list",$format, "input_page_id", "copy_and_save_page");
    }
    ?>
</form>
