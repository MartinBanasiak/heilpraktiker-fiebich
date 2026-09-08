<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_shop_main_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_shop_main', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_shop_main')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_shop_main', false, '{$translation->get('delete_shop_main_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_text'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT main_shop_sitepart.id, type AS '" . $translation->get("type") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_shop_sitepart left join main_admin_user ON main_shop_sitepart.modified_user = main_admin_user.id where main_language_id = " . (int)$GLOBALS["language"]['id'] . " and collection_header = 0 order by id asc";
        //echo $query;
        $format = array('option', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_shop_main");
        }
        ?>
    </form>

</div>