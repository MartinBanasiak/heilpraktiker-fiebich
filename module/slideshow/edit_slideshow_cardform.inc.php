<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'slideshow.config.inc.php';
$formname           = "form_slideshow_cardform";
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_slideshow["id"] == "") {
            echo $translation->get("new_slideshow");
        } else {
            echo $translation->get("edit_slideshow");
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
        button("save", $translation->get("save"), $formname, "loadCard('save_slideshow', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_slideshow', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_slideshow["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_slideshow', true, '{$translation->get('delete_slideshow_confirm')}', true)", "", FALSE);
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
        <input name="input_id" type="hidden" value="<?= $input_slideshow["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="2">

        <?php
        if (!is_array($input_slideshow)) {
            $width           = 500;
            $height          = 250;
            $effect          = 'fade';
            $text_pos        = 'bottom';
            $interval        = 4;
            $text_effect     = 'none';
            $effect_duration = 1;
            $arrows          = "yes";
        } else {
            $width           = $input_slideshow["width"];
            $height          = $input_slideshow["height"];
            $effect          = $input_slideshow["effect"];
            $text_pos        = $input_slideshow["text_pos"];
            $interval        = $input_slideshow["eff_interval"];
            $text_effect     = $input_slideshow["text_effect"];
            $effect_duration = $input_slideshow["effect_duration"];
            $arrows          = $input_slideshow["arrows"];
        }
        ?>

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_slideshow["description"], 255) ?>
                    <?php input($translation->get("width_px"), "slideshow_width", "code", $width) ?>
                    <?php input($translation->get("height_px"), "slideshow_height", "code", $height) ?>
                    <?php input($translation->get("show_duration"), "slideshow_interval", "code", $interval) ?>
                    <?php input($translation->get("effect_duration"), "slideshow_effect_duration", "code", $effect_duration) ?>
                    <?php input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_slideshow["all_languages"]) ?>
                </td>
                <td>
                    <?php input_select($translation->get("arrows"), "slideshow_arrows", array(0 => "yes", 1 => "no"), array(0 => $translation->get("display"), 1 => $translation->get("dont_display")), $arrows) ?>
                    <?php input_select($translation->get("text_effect"), "slideshow_text_effect", $values = array(0 => 'fixed', 1 => 'slide', 2 => 'fade'), $value_names = array(0 => $translation->get("no_effect"), 1 => $translation->get("fade_in"), 2 => $translation->get("crossfade")), $text_effect) ?>
                    <?php input_select($translation->get("text_position"), "slideshow_text_pos", $values = array(0 => 'top', 1 => 'bottom'), $value_names = array(0 => $translation->get("top"), 1 => $translation->get("bottom")), $text_pos) ?>
                    <?php input_select($translation->get("transition_effect"), "slideshow_effect", $values = array(0 => 'none', 1 => 'scroll', 2 => 'fade'), $value_names = array(0 => $translation->get("no_effect"), 1 => $translation->get("fade_out"), 2 => $translation->get("crossfade")), $effect) ?>
                    <?php
                    if ($input_slideshow["id"] != "") {
                        show_changed_on($input_slideshow["id"], "slideshow_header");
                        show_changed_by($input_slideshow["id"], "slideshow_header");
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>

<?
if ($input_slideshow['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slideshow_line_listform.inc.php';
}
?>