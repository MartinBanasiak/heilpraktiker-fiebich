<?php
use DynCom\dc\common\classes\CollectionTypes;

$translation     = \DynCom\dc\common\classes\Registry::get("translation");
$formname        = "form_group_line_list";
$inputname       = "input_id";
$siteparts       = \DynCom\dc\common\classes\Siteparts::get();
$collectionTypes = \DynCom\dc\common\classes\CollectionTypes::get();
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_group_id" value="" />

    <h2><?php echo $translation->get("collection_groups"); ?></h2>
    <ul class="toolbar_menu">
        <?= button("new", $translation->get("new"), $formname, "loadCard('new_group_collection_setup', true)", "", FALSE); ?>
        <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_group_collection_setup')", "", FALSE); ?>
        <?= button("delete", $translation->get("delete"), $formname, "loadCard('delete_group_collection_setup', false, '{$translation->get('delete_collection_group_confirm')}')", "", FALSE); ?>
    </ul>
    <div class="clearfix"></div>

    <?
    $query  = "SELECT id,  description AS '" . $translation->get("description") . "' from main_collection_setup_group WHERE main_collection_setup_id = '" . $input_id . "' ORDER by id asc";
    $format = array('option', 'text', 'text');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format, "input_group_id", "edit_group_collection_setup");
    }
    ?>

</form>