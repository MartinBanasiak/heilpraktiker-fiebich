<?
$formname           = "form_google_maps_cardform";
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_google_maps["id"] == "") {
            echo $translation->get("new_google_maps");
        } else {
            echo $translation->get("edit_google_maps");
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
        button("save", $translation->get("save"), $formname, "loadCard('save_googlemaps', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_googlemaps', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_google_maps["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_googlemaps', true, '{$translation->get('delete_google_maps_confirm')}', true)", "", FALSE);
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
        <input name="input_id" type="hidden" value="<?= $input_google_maps["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="8">

        <?php
        if (!is_array($input_google_maps)) {
            $width  = 450;
            $height = 450;
        } else {
            $width  = $input_google_maps["width"];
            $height = $input_google_maps["height"];
        }
        ?>

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_google_maps["description"], 255) ?>
                    <?php input($translation->get("icon_path"), "google_maps_icon_location", "code", $input_google_maps['icon_location']); ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_google_maps["all_languages"]) ?>
                </td>
                <td>
                    <?php input($translation->get("maps_width"), "google_maps_width", "code", $width); ?>
                    <?php input($translation->get("maps_height"), "google_maps_height", "code", $height); ?>
                    <?php
                    if ($input_google_maps["id"] != "") {
                        show_changed_on($input_google_maps["id"], "google_maps_header");
                        show_changed_by($input_google_maps["id"], "google_maps_header");
                    }
                    ?>
                </td>
            </tr>


        </table>
    </form>

    <br />
<?
if ($input_google_maps['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_googlemaps_line_listform.inc.php';
}
?>