<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_url_management_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete', false, '{$translation->get('delete_website_confirm')}')"); ?>
</ul>

<div id="mainContent">

    <h1><?php echo get_translation('url_management'); ?></h1>

    <?php
    if (count($messages)) {
        echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
    }
    ?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query  = "SELECT main_rewrite_rules.id AS 'id',  main_rewrite_rules.old_url AS '" . $translation->get("old_url") . "', main_rewrite_rules.new_url AS '" . $translation->get("new_url") . "', main_rewrite_rules.rewrite_code AS '" . $translation->get("rewrite_code") . "', 
        (
            case when main_rewrite_rules.active = 1 THEN '" . $translation->get("yes") . "' ELSE '" . $translation->get("no") . "'
            END
        ) AS '" . $translation->get("active") . "' 
        FROM main_rewrite_rules ORDER BY main_rewrite_rules.id DESC";



        $format = array('option', 'text', 'text', 'text', 'text');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, "form_site_list", $format, "input_id");
        }
        ?>

    </form>
</div>