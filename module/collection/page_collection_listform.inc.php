<?php
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$formname      = "form_collection_list";
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
?>

<div id="overlaycrumb">
    <?php echo $translation->get("assign_collection_list"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)"); ?>
    <?= button("link", $translation->get("assoc_with_page"), $formname, "loadCard('assoc_collection_list_page')"); ?>
</ul>

<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" class="selected_linklist_row" name="input_collection_setup_id" value="" />
    <input type="hidden" name="get_real_page_link_id" value="1" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input name="insert_collection_list" type="hidden" value="1">

    <div class="requestLoader"></div>
    <?
    $query  = "SELECT main_collection_setup.id, description AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_collection_setup left join main_admin_user ON main_collection_setup.modified_user = main_admin_user.id where main_language_id = " . (int)$GLOBALS["language"]['id'] . " ORDER BY id asc";
    $format = array('option', 'text', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_collection_setup_id", "assoc_collection_list_page");
    }
    ?>
</form>
