<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_page_line_properties";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$input_page_id  = $_REQUEST["input_page_id"];
?>

<script type="text/javascript">
    function save_and_close_page_line() {
        jQuery('#save_and_close', jQuery('#<?php echo $formname; ?>')).val("1");
        loadCard('save_line_properties_page', true);
    }
</script>

<div id="overlaycrumb">
    <?php echo $translation->get("edit_page_content_properties"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit_content_page', true)"); ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_line_properties_page', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "save_and_close_page_line();"); ?>
    <?= button("reset", $translation->get("restore"), $formname, "?action=edit"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input name="input_id" type="hidden" value="<?= $input_line["id"] ?>">
    <input name="input_main_layout_id" type="hidden" value="<?= $main_layout_id ?>">
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? layout_area_select($translation->get("layout_area"), "input_layout_area_id", $input_line['layout_area_id']); ?>
            </td>
            <td>
                <? input($translation->get("active"), "input_active", "checkbox", $input_line["active"]) ?>
            </td>
        </tr>
        <tr>
            <td>
                <? input($translation->get("background_image_path"), "input_background_image", "text", $input_line["background_image_path"]) ?>
                <div class="input">
                    <div class="browse_button"
                         onclick="javascript: show_ck_finder();"><?php echo $translation->get("browse"); ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</form>

<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_page_line_layout_class_cardform.inc.php';
?>

<script>
    function show_ck_finder() {
        var finder = CKFinder.popup({
            chooseFiles: true,
            onInit: function( finder ) {
                finder.on( 'files:choose', function( evt ) {
                    var file = evt.data.files.first();

                    $('#input_background_image', $('#form_page_line_properties')).val(file.getUrl());
                    /*var scriptPath = file.getUrl();
                    var typeArray = scriptPath.split(".");
                    typeArray = typeArray.reverse();
                    var type = typeArray[0];
                    type.toLowerCase();
                    $('#input_inclusion_type', jQuery('#form_layout_inclusion_card')).val(type);*/
                } );
            }
        });
    }
</script>

