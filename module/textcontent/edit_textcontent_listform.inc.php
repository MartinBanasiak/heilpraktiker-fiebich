<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_textcontent_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_textcontent', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_textcontent')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_textcontent', false, '{$translation->get('delete_textcontent_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_text'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT textcontent_header.id, description AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from textcontent_header left join main_admin_user ON textcontent_header.modified_user = main_admin_user.id where (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) and collection_header = 0 order by id asc";
        //echo $query;
        $format = array('option', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_textcontent");
        }
        ?>
    </form>

</div>