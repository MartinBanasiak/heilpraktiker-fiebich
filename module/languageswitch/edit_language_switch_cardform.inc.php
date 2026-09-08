<?php
$translation             = \DynCom\dc\common\classes\Registry::get("translation");
$formname                = "form_language_switch_card";
$input_page_id           = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id      = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_language_switch = $language_switch_sitepart;
?>

<div id="overlaycrumb">
    <?php if (empty($input_language_switch["id"])) {
        echo $translation->get("new_language_switch");
    } else {
        echo $translation->get("edit_language_switch");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?php
    if (is_page_edit() || is_component_edit()) {
        button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)");
    }
    ?>

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_language_switch', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_language_switch', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_language_switch["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_language_switch', true, '{$translation->get('delete_language_switch_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit_iframe", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}

$input_description = (empty($input_language_switch['description']) ? $translation->get('webshop_language_switch') : $input_language_switch['description']);
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_language_switch["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="17">
    <input type="hidden" name="get_real_page_link_id" value="1" />
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class='label'><label for="input_description"><?= $translation->get('description') ?></label></div>
                <div class='input'><input type="text" name="input_description" id="input_description"
                                          value="<?= $input_description ?>" class="text"></div>
            </td>
        </tr>
    </table>
</form>