<?
$formname                    = "form_slidecontent_line_card";
$translation                 = \DynCom\dc\common\classes\Registry::get("translation");
$input_page_id               = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id         = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
$collection_setup_content_id = (isset($_REQUEST["collection_setup_content_id"]) ? $_REQUEST["collection_setup_content_id"] : "");
$header_id                   = (isset($input_line["header_id"]) ? $input_line["header_id"] : "");
$input_component_id          = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_slidecontent_line");
    } else {
        echo $translation->get("edit_slidecontent_line");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?
    if (is_collection_edit()) {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_collection', true)");
    } else {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_slidecontent', true)");
    }
    ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_line_slidecontent', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_line_slidecontent', true)"); ?>
    <?php
    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_slidecontent', true, '{$translation->get('delete_line_confirm')}')");
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

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_line_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $header_id; ?>" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_setup_content_id" value="<?php echo $collection_setup_content_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input type="hidden" name="linklist_form" value="form_line_slidecontent_list" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("headline"), "input_headline", "text", $input_line["headline"], 255) ?>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <h2><?php echo $translation->get("content"); ?></h2>
                <?php
                show_ck_editor($input_line['content'], "dc", 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/dist/css/fck.css', 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/fckstyles.xml');
                ?>
            </td>
        </tr>
    </table>

</form>