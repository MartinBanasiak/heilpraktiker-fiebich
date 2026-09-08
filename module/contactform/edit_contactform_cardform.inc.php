<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_contactform_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

    <div id="overlaycrumb">
        <?php if ($input_contactform["id"] == "") {
            echo $translation->get("new_contactform");
        } else {
            echo $translation->get("edit_contactform");
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
        button("save", $translation->get("save"), $formname, "loadCard('save_contactform', true)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "loadCard('save_contactform', true, '', true)");
        ?>
        <li>
            <ul>
                <?php
                // Loeschen  button
                if ($input_contactform["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_contactform', true, '{$translation->get('delete_contactform_confirm')}', true)", "", FALSE);
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
        <input name="input_id" type="hidden" value="<?= $input_contactform["id"] ?>">
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="data-sitepartid" type="hidden" value="3">

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("description"), "input_description", "text", $input_contactform["description"], 255) ?>
                    <? input($translation->get("sender_name"), "input_sender_name", "text", $input_contactform["sender_name"], 255) ?>
                    <? input($translation->get("sender_email"), "input_sender_email", "text", $input_contactform["sender_email"], 255) ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_contactform["all_languages"]) ?>

                </td>
                <td>
                    <? input($translation->get("subject"), "input_subject", "text", $input_contactform["subject"], 255) ?>

                    <? input($translation->get("recipient_name"), "input_recipient_name", "text", $input_contactform["recipient_name"], 255) ?>
                    <? input($translation->get("recipient_email"), "input_recipient_email", "text", $input_contactform["recipient_email"], 255) ?>
                    <?php
                    if ($input_contactform["id"] != "") {
                        show_changed_on($input_contactform["id"], "contactform_header");
                        show_changed_by($input_contactform["id"], "contactform_header");
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>
    <br />
<?
if ($input_contactform['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_contactform_line_listform.inc.php';
}
?>