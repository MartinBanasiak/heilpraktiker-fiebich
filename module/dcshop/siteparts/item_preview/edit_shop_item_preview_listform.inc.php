<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_shop_item_preview_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_shop_item_preview', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_shop_item_preview')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_shop_item_preview', false, '{$translation->get('delete_shop_item_preview_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_text'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT
		main_shop_item_preview.id, 
		no_of_items AS '" . $translation->get("no_of_items") . "', 
		category_code_string AS '" . $translation->get("category_code_string") . "',
		from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
		main_admin_user.name AS '" . $translation->get("changed_by") . "' 
		FROM
			main_shop_item_preview 
		INNER JOIN 
			main_admin_user 
		  ON 
			main_shop_item_preview.modified_user = main_admin_user.id 
		WHERE
			main_language_id = " . (int)$GLOBALS["language"]['id'] . " 
		  AND 
			collection_header = 0 
		ORDER BY id ASC";
        //echo $query;
        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_shop_item_preview");
        }
        ?>
    </form>
</div>