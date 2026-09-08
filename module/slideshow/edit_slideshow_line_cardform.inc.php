<?
$formname                    = "form_line_card";
$translation                 = \DynCom\dc\common\classes\Registry::get("translation");
$input_page_id               = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id         = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
$input_component_id          = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$collection_setup_content_id = (isset($_REQUEST["collection_setup_content_id"]) ? $_REQUEST["collection_setup_content_id"] : "");
$header_id                   = (isset($input_line["header_id"]) ? $input_line["header_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_slideshow_line");
    } else {
        echo $translation->get("edit_slideshow_line");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?
    if (is_collection_edit()) {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_collection', true)");
    } else {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_slideshow', true)");
    }
    ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_line_slideshow', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_line_slideshow', true)"); ?>
    <?php
    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_slideshow', true, '{$translation->get('delete_line_confirm')}')");
    }
    ?>
    <?= button("reset", $translation->get("restore"), $formname, "?action=edit"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}

$bg_values = array(
        0,
        1,
        2,
);
$bg_names = array(
    $translation->get("bg_center_center"),
    $translation->get("bg_left_center"),
    $translation->get("bg_right_center"),
);
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
    <input name="input_line_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $header_id; ?>" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_setup_content_id" value="<?php echo $collection_setup_content_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />
    <input type="hidden" name="linklist_form" value="form_line_slideshow_list" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_name", "text", $input_line["description"], 255) ?>
                <? input($translation->get("link"), "input_link", "text", $input_line["link"], 255) ?>
                <? input($translation->get("file"), "input_file", "file", $input_line["file"]) ?>
                <? input($translation->get("validity_from"), "input_validity_from", "date", datefromsql($input_line["validity_from"]), 10); ?>
                <? input($translation->get("validity_to"), "input_validity_to", "date", datefromsql($input_line["validity_to"]), 10); ?>
            </td>
            <td>
                <? input($translation->get("image_headline"), "input_headline", "text", $input_line["headline"], 255) ?>
                <? input($translation->get("image_text"), "input_text", "text", $input_line["text"], 255) ?>
                <? input($translation->get("image_text2"), "input_text2", "text", $input_line["text2"], 255) ?>
                <?php input_select($translation->get("bg_position"), "input_bg_position", $bg_values, $bg_names, $input_line['bg_position']); ?>
            </td>
        </tr>

        <?php
        if ($input_line['preview'] != "") {
            ?>
            <tr>
                <td>
                    <div class="label"><?php echo $translation->get("preview"); ?></div>
                    <?php echo $input_line['preview']; ?>
                    <input type="hidden" name="preview" value="<?php echo $input_line['preview']; ?>" />
                </td>
                <td>

                </td>
            </tr>
        <?php
        }
        ?>
    </table>

</form>