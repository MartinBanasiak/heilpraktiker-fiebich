<?php
$formname    = "form_layout_inclusion_card";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<script type="text/javascript">
    function inclusion_cardform_SetFileField( fileUrl ) {
        $('#input_inclusion_path').val(fileUrl);
    }


    function inclusion_cardform_browseScripts() {
        /*var finder = new CKFinder();
        finder.basePath = "/plugins/ckfinder/";
        finder.selectActionFunction = inclusion_cardform_SetFileField;
        finder.popup();*/

        var finder = CKFinder.popup({
            chooseFiles: true,
            onInit: function( finder ) {
                finder.on( 'files:choose', function( evt ) {
                    var file = evt.data.files.first();

                    $('#input_inclusion_path', jQuery('#form_layout_inclusion_card')).val(file.getUrl());
                    var scriptPath = file.getUrl();
                    var typeArray = scriptPath.split(".");
                    typeArray = typeArray.reverse();
                    var type = typeArray[0];
                    type.toLowerCase();
                    $('#input_inclusion_type', jQuery('#form_layout_inclusion_card')).val(type);
                } );
            }
        });
    }

    function inclusion_cardform_getType() {
        var scriptPath = $('#input_inclusion_path', jQuery('#form_layout_inclusion_card')).val();
        var typeArray = scriptPath.split(".");
        typeArray = typeArray.reverse();
        var type = typeArray[0];
        type.toLowerCase();
        $('#input_inclusion_type', jQuery('#form_layout_inclusion_card')).val(type);
        return true;
    }

    function save_and_close() {
        jQuery('#save_and_close', jQuery('#form_layout_inclusion_card')).val("1");
        loadCard('save_inclusion', true);
    }
</script>


<div id="overlaycrumb">
    <?php if ($input_layout_inclusion["id"] == "") {
        echo $translation->get("new_css_javascript");
    } else {
        echo $translation->get("edit_css_javascript");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit', true)"); ?>
    <?= button("save", $translation->get("save"), $formname, "inclusion_cardform_getType(); loadCard('save_inclusion', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "inclusion_cardform_getType(); save_and_close()"); ?>
    <?php
    if ($input_layout_inclusion["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_inclusion', true, '{$translation->get('delete_layout_inclusion_confirm')}')");
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
    <input name="input_inclusion_id" type="hidden" value="<?= $input_layout_inclusion["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $_POST["input_id"] ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />
    <input type="hidden" id="input_inclusion_type" class="text" type="text"
           value="<?= $input_layout_inclusion["type"] ?>" name="input_inclusion_type" />
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="label">
                    <label for="input_inclusion_path"><?php echo $translation->get("path"); ?></label>
                </div>
                <div class="input">
                    <input id="input_inclusion_path" class="text" type="text"
                           value="<?= $input_layout_inclusion["path"] ?>" name="input_inclusion_path">
                </div>
                <div class="input">
                    <div class="browse_button"
                         onclick="javascript: inclusion_cardform_browseScripts();"><?php echo $translation->get("browse"); ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <? input_select($translation->get("default_active"), "input_inclusion_default_active", $values = array('1', '0'), $value_names = array($translation->get("yes"), $translation->get("no")), $input_layout_inclusion["default_active"]); ?>
            </td>
        </tr>
    </table>
</form>