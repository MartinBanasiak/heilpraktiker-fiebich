<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_shop_login_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_shop_login', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_shop_login')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_shop_login', false, '{$translation->get('delete_shop_login_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_text'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "SELECT
		main_shop_login.id, 
		target_site_code AS '" . $translation->get("target_site_code") . "',
		target_language_code AS '" . $translation->get("target_language_code") . "',
		target_url AS '" . $translation->get("target_url") . "', 
		CASE
			WHEN show_labels_in_fields = 0
				THEN '" . $translation->get("yes") . "'
			ELSE
				'" . $translation->get("no") . "'
		END AS '" . $translation->get("show_labels_in_fields") . "',
		from_unixtime(modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
		main_admin_user.name AS '" . $translation->get("changed_by") . "' 
		FROM
			main_shop_login 
		INNER JOIN 
			main_admin_user 
		  ON 
			main_shop_login.modified_user = main_admin_user.id 
		WHERE
			main_language_id = " . (int)$GLOBALS["language"]['id'] . " 
		  AND 
			collection_header = 0 
		ORDER BY id ASC";
        //echo $query;
        $format = array('option', 'text', 'text', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_shop_login");
        }
        ?>
    </form>

</div>