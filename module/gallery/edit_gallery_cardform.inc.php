<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'gallery.config.inc.php';
$formname           = "form_gallery_cardform";
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_gallery["id"] == "") {
            echo $translation->get("new_gallery");
        } else {
            echo $translation->get("edit_gallery");
        }
        ?>
        <div id="closeoverlay" onclick="disableOverlay();"></div>
    </div>

    <ul class="toolbar_menu">
        <?php
        // Zurueck zur uebersicht
        if (is_page_edit() || is_component_edit()) {
            button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)");
        }

        // Speichern button
        button("save", $translation->get("save"), $formname, "loadCard('save_gallery', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_gallery', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_gallery["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_gallery', true, '{$translation->get('delete_gallery_confirm')}', true)", "", FALSE);
                }

                // Zuruecksetzen
                button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE);
                ?>
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
        <input name="input_id" type="hidden" value="<?= $input_gallery["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="6">

        <?php
        if (!is_array($input_gallery)) {
            $width        = 500;
            $height       = 250;
            $thumb_width  = 100;
            $thumb_height = 100;
        } else {
            $width        = $input_gallery["width"];
            $height       = $input_gallery["height"];
            $thumb_width  = $input_gallery["thumb_width"];
            $thumb_height = $input_gallery["thumb_height"];
        }
        ?>

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_gallery["description"], 255) ?>
                    <?php input($translation->get("max_width"), "gallery_width", "code", $width); ?>
                    <?php input($translation->get("max_height"), "gallery_height", "code", $height); ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_gallery["all_languages"]) ?>
                </td>
                <td>
                    <?php input($translation->get("max_thumb_width"), "gallery_thumb_width", "code", $thumb_width); ?>
                    <?php input($translation->get("max_thumb_height"), "gallery_thumb_height", "code", $thumb_height); ?>
                    <?php
                    if ($input_gallery["id"] != "") {
                        show_changed_on($input_gallery["id"], "gallery_header");
                        show_changed_by($input_gallery["id"], "gallery_header");
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>

<?
if ($input_gallery['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_gallery_line_listform.inc.php';
}
?>