<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_iframe_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_iframe["id"] == "") {
        echo $translation->get("new_iframe");
    } else {
        echo $translation->get("edit_iframe");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_iframe', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_iframe', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_iframe["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_iframe', true, '{$translation->get('delete_iframe_confirm')}', true)", "", FALSE);
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
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_iframe["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="11">
    <input type="hidden" name="get_real_page_link_id" value="1" />

    <?php
    if (!is_array($input_iframe)) {
        $width  = 450;
        $height = 300;
    } else {
        $width  = $input_iframe["width"];
        $height = $input_iframe["height"];
    }
    ?>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_description", "text", $input_iframe["description"], 255) ?>
                <?php input($translation->get("element_width"), "input_width", "code", $width) ?>
                <?php input($translation->get("element_height"), "input_height", "code", $height) ?>
                <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_iframe["all_languages"]) ?>
            </td>
            <td>
                <? input($translation->get("iframe_page_url"), "input_url", "text", $input_iframe["url"], 255) ?>
                <?php
                if ($input_iframe["id"] != "") {
                    show_changed_on($input_iframe["id"], "iframe_header");
                    show_changed_by($input_iframe["id"], "iframe_header");
                }
                ?>
            </td>
        </tr>
    </table>
</form>