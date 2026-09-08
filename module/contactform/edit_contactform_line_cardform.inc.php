<?
$formname                    = "form_line_card";
$translation                 = \DynCom\dc\common\classes\Registry::get("translation");
$input_page_id               = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id         = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
$collection_setup_content_id = (isset($_REQUEST["collection_setup_content_id"]) ? $_REQUEST["collection_setup_content_id"] : "");
$input_component_id          = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$header_id                   = (isset($input_line["header_id"]) ? $input_line["header_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_contactform_field");
    } else {
        echo $translation->get("edit_contactform_field");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?
    if (is_collection_edit()) {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_collection', true)");
    } else {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_contactform', true)");
    }
    ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_line_contactform', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_line_contactform', true)"); ?>
    <?php
    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_contactform', true, '{$translation->get('delete_contactform_field_confirm')}')");
    }
    ?>
    <?= button("reset", $translation->get("restore"), $formname, "?action=edit"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
    <input name="input_line_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $header_id; ?>" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_setup_content_id" value="<?php echo $collection_setup_content_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />
    <input type="hidden" name="linklist_form" value="form_field_contactform_list" />


    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="label"><label for="input_typ">Typ</label></div>
                <div class="input">
                    <select class="select" name="input_typ" id="input_typ">
                        <option<?= ($input_line["typ"] == 1) ? " selected=\"selected\"" : " "; ?>
                            value="1"><?php echo $translation->get("textfield"); ?></option>
                        <option<?= ($input_line["typ"] == 2) ? " selected=\"selected\"" : " "; ?>
                            value="2"><?php echo $translation->get("textarea"); ?></option>
                        <option<?= ($input_line["typ"] == 3) ? " selected=\"selected\"" : " "; ?>
                            value="3"><?php echo $translation->get("yesno"); ?></option>
                        <option<?= ($input_line["typ"] == 4) ? " selected=\"selected\"" : " "; ?>
                            value="4"><?php echo $translation->get("optionfield"); ?></option>
                        <option<?= ($input_line["typ"] == 5) ? " selected=\"selected\"" : " "; ?>
                            value="5"><?php echo $translation->get("headline"); ?></option>
                        <option<?= ($input_line["typ"] == 6) ? " selected=\"selected\"" : " "; ?>
                            value="6"><?php echo $translation->get("spacerow"); ?></option>
                        <option<?= ($input_line["typ"] == 7) ? " selected=\"selected\"" : " "; ?>
                            value="7"><?php echo $translation->get("title_news"); ?></option>
                        <option<?= ($input_line["typ"] == 8) ? " selected=\"selected\"" : " "; ?>
                            value="8"><?php echo $translation->get("file"); ?></option>
                    </select>
                </div>
                <? input($translation->get("textkey"), "input_code", "text", $input_line["code"], 50) ?>
                <? input($translation->get("caption"), "input_name", "text", $input_line["name"], 250) ?>
                <? input($translation->get("caption_in_field"), "input_infield", "checkbox", $input_line["infield"]) ?>
                <? input($translation->get("mandatory_field"), "input_mandatory", "checkbox", $input_line["mandatory"]) ?>
                <br />
                <? input($translation->get("optionvalues"), "input_option_string", "text", $input_line["option_string"], 200) ?>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>

</form>