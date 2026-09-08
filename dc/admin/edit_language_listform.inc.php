<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_language_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete', false, '{$translation->get('delete_language_confirm')}')"); ?>

</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_languages'); ?></h1>

    <?php
    if (count($messages)) {
        echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
    }
    ?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query  = "SELECT id,  name AS '" . $translation->get("description") . "',code AS '" . $translation->get("textkey") . "', (SELECT COUNT(id) FROM main_navigation WHERE main_language_id = main_language.id) AS '" . $translation->get("number_menu_items") . "' FROM main_language WHERE main_site_id = '" . $GLOBALS["site"]["id"] . "' ORDER BY code ASC";
        $format = array('option', 'text', 'text', 'integer');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, $formname, $format);
        }
        ?>
    </form>

</div>