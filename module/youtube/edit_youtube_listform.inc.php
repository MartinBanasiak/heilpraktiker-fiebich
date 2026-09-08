<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_youtube_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_youtube', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_youtube')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_youtube', false, '{$translation->get('delete_youtube_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_youtube'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT youtube_header.id, description AS '" . $translation->get("description") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from youtube_header left join main_admin_user ON youtube_header.modified_user = main_admin_user.id where (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) and collection_header = 0 order by id asc";
        //echo $query;
        $format = array('option', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_youtube");
        }
        ?>
    </form>

</div>