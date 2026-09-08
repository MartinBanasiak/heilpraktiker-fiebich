<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_collection_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_collection_setup', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_collection_setup')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_collection_setup', false, '{$translation->get('delete_collection_setup_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_collections'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT main_collection_setup.id, description AS '" . $translation->get("description") . "', main_collection_setup.code AS '" . $translation->get("code") . "',  from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_collection_setup left join main_admin_user ON main_collection_setup.modified_user = main_admin_user.id where (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) ORDER BY id asc";
        //echo $query;
        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_collection_setup");
        }
        ?>
    </form>

</div>