<?php
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$formname      = "form_google_maps_list";
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
?>

<div id="overlaycrumb">
    <?php echo $translation->get("assign_google_maps"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)"); ?>
    <?= button("link", $translation->get("assoc_with_page"), $formname, "loadCard('assoc_line_page')"); ?>
</ul>

<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" class="selected_linklist_row" name="input_id" value="" />
    <input type="hidden" name="get_real_page_link_id" value="1" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="8">

    <div class="requestLoader"></div>
    <?
    $query = "SELECT google_maps_header.id, description AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from google_maps_header left join main_admin_user ON google_maps_header.modified_user = main_admin_user.id where (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) and collection_header = 0 order by id asc";
    //echo $query;
    $format = array('option', 'text', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_id", "assoc_line_page");
    }
    ?>
</form>
