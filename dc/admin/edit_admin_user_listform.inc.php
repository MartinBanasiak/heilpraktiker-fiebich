<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_admin_user_list";
$inputname   = "input_id";
?>
<? if ($GLOBALS['admin_user']['right_create_user'] == 1 | $GLOBALS['admin_user']['is_super_user'] == 1) { ?>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new', true)"); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit')"); ?>
        <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete', false, '{$translation->get('delete_user_confirm')}')"); ?>
    </ul>
<? } ?>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_users'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query  = "SELECT id, name AS '" . $translation->get("name") . "', email AS '" . $translation->get("email") . "', login AS " . $translation->get("backend_login_label") . " FROM main_admin_user ORDER BY name ASC";
        $format = array("option", "text", "text", "text");
        if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, "form_admin_user_list", $format);
        }
        ?>

    </form>
</div>