<?
$formname    = "form_collection_group_cardform";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$inputname   = "input_id";
$siteparts   = \DynCom\dc\common\classes\Siteparts::get();

?>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_collection_group");
    } else {
        echo $translation->get("edit_collection_group");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?php
    // Zurueck zur uebersicht
    button("left", $translation->get("back"), $formname, "loadCard('edit_collection_setup', true)");

    // Speichern buttons
    button("save", $translation->get("save"), $formname, "loadCard('save_group_collection_setup', true)");
    button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_group_collection_setup', true)");

    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_group_collection_setup', true, '{$translation->get('delete_collection_group_confirm')}')");
    }
    ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_group_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $_REQUEST['input_id'] ?>">
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_description", "text", $input_line["description"], 255) ?>
                <? input_select($translation->get("default_active"), "input_default_active", $values = array('0', '1'), $value_names = array($translation->get("no"), $translation->get("yes")), $input_line["default_active"]); ?>
            </td>

            <td>
            </td>
        </tr>

    </table>

</form>