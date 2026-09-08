<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_facebook_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_facebook["id"] == "") {
        echo $translation->get("new_facebook");
    } else {
        echo $translation->get("edit_facebook");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_facebook', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_facebook', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_facebook["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_facebook', true, '{$translation->get('delete_facebook_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit_facebook", "", FALSE); ?>
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
    <input name="input_id" type="hidden" value="<?= $input_facebook["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="9">
    <input type="hidden" name="get_real_page_link_id" value="1" />

    <?php
    if (!is_array($input_facebook)) {
        $width  = 450;
        $height = 300;
    } else {
        $width  = $input_facebook["width"];
        $height = $input_facebook["height"];
    }
    ?>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_description", "text", $input_facebook["description"], 255) ?>
                <?php input($translation->get("element_width"), "input_width", "code", $width) ?>
                <?php input($translation->get("element_height"), "input_height", "code", $height) ?>
                <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_facebook["all_languages"]) ?>
            </td>
            <td>
                <? input($translation->get("facebook_page_url"), "input_url", "text", $input_facebook["url"], 255) ?>
                <? input($translation->get("show_friends"), "input_show_faces", "checkbox", $input_facebook['show_faces']); ?>
                <? input($translation->get("show_posts"), "input_show_posts", "checkbox", $input_facebook['show_posts']); ?>
                <?php
                if ($input_facebook["id"] != "") {
                    show_changed_on($input_facebook["id"], "facebook_header");
                    show_changed_by($input_facebook["id"], "facebook_header");
                }
                ?>
            </td>
        </tr>
    </table>
</form>