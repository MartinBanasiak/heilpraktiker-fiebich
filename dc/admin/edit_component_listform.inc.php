<?
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_component_list";

?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_component', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_component')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_component', false, '{$translation->get('delete_component_confirm')}')"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_components'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" class="selected_linklist_row" name="input_component_id" value="" />

        <div class="requestLoader"></div>
        <div id="component_linklist">
            <?
            $query = "SELECT main_component.id, title AS '" . $translation->get("description") . "', main_component.code AS '" . $translation->get("code") . "', main_layout_area.name AS '" . $translation->get("layout_area") . "', from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', main_admin_user.name AS '" . $translation->get("changed_by") . "' from main_component left join main_admin_user ON main_component.modified_user = main_admin_user.id inner join main_layout_area ON main_component.layout_area_id = main_layout_area.id where (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) order by id asc";
            //echo $query;
            $format = array('option', 'text', 'text', 'text', 'text', 'text');
            if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
                linklist($result, $formname, $format, "input_component_id", "edit_component");
            }
            ?>
        </div>
    </form>

</div>