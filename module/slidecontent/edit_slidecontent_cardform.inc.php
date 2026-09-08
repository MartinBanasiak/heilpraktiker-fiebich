<?
$formname           = "form_slidecontent_cardform";
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$inputname          = "input_id";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_slidecontent["id"] == "") {
            echo $translation->get("new_slidecontent");
        } else {
            echo $translation->get("edit_slidecontent");
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
        button("save", $translation->get("save"), $formname, "loadCard('save_slidecontent', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_slidecontent', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_slidecontent["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_slidecontent', true, '{$translation->get('delete_slidecontent_confirm')}', true)", "", FALSE);
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
        <input name="input_id" type="hidden" value="<?= $input_slidecontent["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="4">

        <?php
        if (!is_array($input_slidecontent)) {
            $width  = 450;
            $height = 450;
        } else {
            $width  = $input_slidecontent["width"];
            $height = $input_slidecontent["height"];
        }
        ?>

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_slidecontent["description"], 255) ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_slidecontent["all_languages"]) ?>
                </td>
                <td>
                    <?php
                    if ($input_slidecontent["id"] != "") {
                        show_changed_on($input_slidecontent["id"], "slidecontent_header");
                        show_changed_by($input_slidecontent["id"], "slidecontent_header");
                    }
                    ?>
                </td>
            </tr>


        </table>
    </form>

    <br />
<?
if ($input_slidecontent['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_slidecontent_line_listform.inc.php';
}
?>