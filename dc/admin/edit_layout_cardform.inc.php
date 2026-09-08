<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_layout_card";
?>

    <div id="overlaycrumb">
        <?php if ($input_layout["id"] == "") {
            echo $translation->get("new_layout");
        } else {
            echo sprintf($translation->get("edit_layout"), $input_layout["name"]);
        }
        ?>
        <div id="closeoverlay" onclick="disableOverlay();"></div>
    </div>

    <ul class="toolbar_menu">
        <?= button("save", $translation->get("save"), $formname, "loadCard('save', true)"); ?>
        <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save', true, '', true)"); ?>
        <li>
            <ul>
                <?php
                if ($input_layout["id"] != "") {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete', true, '{$translation->get('delete_layout_confirm')}', true)", "", FALSE);
                }
                ?>
                <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
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
        <input name="input_id" type="hidden" value="<?= $input_layout["id"] ?>">
        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <? input($translation->get("textkey"), "input_code", "code", $input_layout["code"], 20) ?>
                    <? input($translation->get("description"), "input_name", "text", $input_layout["name"], 45) ?>
                    <? input($translation->get("frontend_file"), "input_frontend_include", "text", $input_layout["frontend_include"], 45) ?>
                </td>
                <td>

                </td>
            </tr>
        </table>
    </form>
    <br />
<?
if ($input_layout['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_inclusions_listform.inc.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_area_listform.inc.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_layout_class_listform.inc.php';
}