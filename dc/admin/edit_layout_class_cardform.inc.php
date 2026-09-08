<?php
$formname    = "form_layout_class_card";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<script type="text/javascript">

    function layout_class_save_and_close() {
        jQuery('#save_and_close', jQuery('#form_layout_class_card')).val("1");
        loadCard('save_class', true);
    }

</script>
<div id="overlaycrumb">
    <?php if ($input_layout_inclusion["id"] == "") {
        echo $translation->get("new_layout_class");
    } else {
        echo $translation->get("edit_layout_class");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit', true)"); ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_class', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "layout_class_save_and_close()"); ?>
    <?php
    if ($input_layout_class["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_class', true, '{$translation->get('delete_layout_class_confirm')}')");
    }
    ?>
    <?= button("reset", $translation->get("restore"), $formname, "?action=edit"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_class_id" type="hidden" value="<?= $input_layout_class["id"] ?>">
    <input name="input_id" type="hidden" value="<?= $_POST["input_id"] ?>">
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("textkey"), "input_class_code", "code", $input_layout_class["code"], 80) ?>
                <? input($translation->get("description"), "input_class_name", "text", $input_layout_class["name"], 80) ?>
            </td>
        </tr>
    </table>
</form>