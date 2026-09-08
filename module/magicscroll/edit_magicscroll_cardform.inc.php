<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'magicscroll.config.inc.php';
$formname           = "form_magicscroll_cardform";
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_magicscroll["id"] == "") {
            echo $translation->get("new_scrollbar");
        } else {
            echo $translation->get("edit_scrollbar");
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
        button("save", $translation->get("save"), $formname, "loadCard('save_magicscroll', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_magicscroll', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_magicscroll["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_magicscroll', true, '{$translation->get('delete_scrollbar_confirm')}', true)", "", FALSE);
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
        <input name="input_id" type="hidden" value="<?= $input_magicscroll["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="7">

        <?php
        if (!is_array($input_magicscroll)) {
            $item_width      = 100;
            $item_height     = 75;
            $width           = 940;
            $height          = 75;
            $items           = 3;
            $step            = 3;
            $eff_interval    = 3000;
            $effect_duration = 1000;
            $arrows          = '';
        } else {
            $item_width      = $input_magicscroll["item_width"];
            $item_height     = $input_magicscroll["item_height"];
            $width           = $input_magicscroll["width"];
            $height          = $input_magicscroll["height"];
            $items           = $input_magicscroll["items"];
            $step            = $input_magicscroll["step"];
            $eff_interval    = $input_magicscroll["eff_interval"];
            $effect_duration = $input_magicscroll["effect_duration"];
            $arrows          = $input_magicscroll["arrows"];
        }
        ?>

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_magicscroll["description"], 255) ?>
                    <?php input($translation->get("element_width"), "magicscroll_item_width", "code", $item_width) ?>
                    <?php input($translation->get("element_height"), "magicscroll_item_height", "code", $item_height) ?>
                    <?php input($translation->get("width_px"), "magicscroll_width", "code", $width) ?>
                    <?php input($translation->get("height_px"), "magicscroll_height", "code", $height) ?>
                    <?php input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_magicscroll["all_languages"]) ?>
                </td>
                <td>
                    <?php input_select($translation->get("arrows"), "magicscroll_arrows", $values = array(0 => 'inside', 1 => 'outside', 2 => 'false'), $values = array(0 => $translation->get("show_inside"), 1 => $translation->get("show_outside"), 2 => $translation->get("dont_show")), $arrows) ?>
                    <?php input($translation->get("showed_elements_at_once"), "magicscroll_items", "code", $items) ?>
                    <?php input($translation->get("elements_pro_scroll"), "magicscroll_step", "code", $step) ?>
                    <?php input($translation->get("effect_duration"), "magicscroll_effect_duration", "code", $effect_duration) ?>
                    <?php input($translation->get("view_duration"), "magicscroll_interval", "code", $eff_interval) ?>
                    <?php
                    if ($input_magicscroll["id"] != "") {
                        show_changed_on($input_magicscroll["id"], "scrollbar_header");
                        show_changed_by($input_magicscroll["id"], "scrollbar_header");
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>

<?
if ($input_magicscroll['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_magicscroll_line_listform.inc.php';
}
?>