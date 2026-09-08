<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_newsletter_sitepart_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_newsletter_sitepart', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_newsletter_sitepart')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_newsletter_sitepart', false, '{$translation->get('delete_newsletter_sitepart_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_text'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query = "
	SELECT 
		newsletter_sitepart.id, 
		newsletter_sitepart.description AS '" . $translation->get("description") . "',
		CASE WHEN newsletter_sitepart.type=0 THEN '" . $translation->get('registration') . "' ELSE '" . $translation->get('deregistration') . "' END AS '" . $translation->get('type') . "',
		from_unixtime(newsletter_sitepart.modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
		main_admin_user.name AS '" . $translation->get("changed_by") . "'
	FROM newsletter_sitepart 
	left join main_admin_user 
		ON 
			textcontent_header.modified_user = main_admin_user.id 
	WHERE 
		main_language_id = " . (int)$GLOBALS["language"]['id'] . "
	  AND
		collection_header = 0 
	ORDER BY 
		newsletter_sitepart.id ASC";
        //echo $query;
        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format, "input_id", "edit_newsletter_sitepart");
        }
        ?>
    </form>
</div>