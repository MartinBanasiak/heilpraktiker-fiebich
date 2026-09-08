<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_textcontent_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_textcontent["id"] == "") {
        echo $translation->get("new_textcontent");
    } else {
        echo $translation->get("edit_textcontent");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_textcontent', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_textcontent', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_textcontent["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_textcontent', true, '{$translation->get('delete_textcontent_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit_textcontent", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_textcontent["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="1">
    <input type="hidden" name="get_real_page_link_id" value="1" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_description", "text", $input_textcontent["description"], 255) ?>
                <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_textcontent["all_languages"]) ?>

            </td>
            <td>
                <?php
                if ($input_textcontent["id"] != "") {
                    show_changed_on($input_textcontent["id"], "textcontent_header");
                    show_changed_by($input_textcontent["id"], "textcontent_header");
                }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <h2><?php echo $translation->get("content"); ?></h2>
                <?php
                show_ck_editor($input_textcontent['content'], "dc", 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/dist/css/fck.css', 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/fckstyles.xml');
                ?>
            </td>
        </tr>
    </table>
</form>