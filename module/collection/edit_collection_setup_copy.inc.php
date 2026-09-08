<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_collection_setup_copy_card";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$from           = isset($_REQUEST['from']) ? $_REQUEST['from'] : '';
?>

<script type="text/javascript">
    function loadLanguages( website_id ) {
        $('#siteLanguages', $('#<?php echo $formname; ?>')).html("");

        if (website_id == "" || website_id == 0) {
            return;
        }

        $('.loader', $('#<?php echo $formname; ?>')).show();

        // ajax
        var formData = new FormData(jQuery('#<?php echo $formname; ?>')[0]);
        $.ajax({
            url        : "?action=load_languages",
            type       : 'POST',
            data       : formData,
            cache      : false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success    : function ( output, status, xhr ) {
                if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                    window.location.replace("/dc/");
                } else {
                    $('.loader', $('#<?php echo $formname; ?>')).hide();
                    $('#siteLanguages', $('#<?php echo $formname; ?>')).html(output.site_languages);
                }
            },
            dataType   : 'json'
        });
    }
</script>

<div id="overlaycrumb">
    <?php echo $translation->get("copy_collection"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">

    <? button("left", $translation->get("back"), $formname, "loadCard('edit_collection_setup', true)"); ?>
    <?= button("save", $translation->get("copy_and_save"), $formname, "loadCard('copy_and_save_collection_setup', true)"); ?>
    <li>
        <ul>
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
    <input name="input_id" type="hidden" value="<?= $input_collection["id"] ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td><?php echo $translation->get("copy_collection_to"); ?>:</td>
            <td></td>
        </tr>
        <tr>
            <td>

                <? site_select($translation->get("website"), "site_id", null, false, true, 'loadLanguages(this.value);'); ?>
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>
                <img class="loader" style="display:none;" src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/ajax-loader.gif" />
                <div id="siteLanguages"></div>
            </td>
            <td>
            </td>
        </tr>
    </table>
</form>