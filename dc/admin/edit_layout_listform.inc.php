<?
//if (isset($_FILES['input_layout_file']) && ! $_FILES['input_layout_file']['error']) {
//  move_uploaded_file($_FILES['input_layout_file']['tmp_name'], "../../layout/frontend/" . $_FILES['input_layout_file']['name']);
//  extract_zip_file($GLOBALS["site_server_path"] . "layout/frontend/" . $_FILES['input_layout_file']['name'],"../../layout/frontend/");
//printf("Die Datei %s steht jetzt als " . "newfile.jpg zur Verfügung.<br />\n",$_FILES['input_layout_file']['name']);
//printf("Sie ist %u Bytes groß und vom Typ %s.<br />\n",$_FILES['input_layout_file']['size'], $_FILES['input_layout_file']['type']);
//}
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_layout_list";

?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete', false, '{$translation->get('delete_layout_confirm')}')"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_layouts'); ?></h1>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>

        <?
        $query  = "SELECT id,  name AS '" . $translation->get("description") . "', code as '" . $translation->get("textkey") . "' from main_layout ORDER by code ASC";
        $format = array('option', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format);
        }
        ?>

    </form>

</div>