<?php
$formname    = "form_layout_area_card";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<script type="text/javascript">

    function layout_area_save_and_close() {
        jQuery('#save_and_close', jQuery('#form_layout_area_card')).val("1");
        loadCard('save_area', true);
    }

</script>
<div id="overlaycrumb">
    <?php if ($input_layout_inclusion["id"] == "") {
        echo $translation->get("new_layout_area");
    } else {
        echo $translation->get("edit_layout_area");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit', true)"); ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_area', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "layout_area_save_and_close()"); ?>
    <?php
    if ($input_layout_area["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_area', true, '{$translation->get('delete_layout_area_confirm')}')");
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
    <input name="input_area_id" type="hidden" value="<?= $input_layout_area["id"] ?>">
    <input name="input_id" type="hidden" value="<?= $_POST["input_id"] ?>">
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("textkey"), "input_area_code", "code", $input_layout_area["code"], 20) ?>
                <? input($translation->get("description"), "input_area_name", "text", $input_layout_area["name"], 45) ?>
            </td>
        </tr>
    </table>
</form>