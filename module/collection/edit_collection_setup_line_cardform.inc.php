<?
$formname    = "form_collection_choose_cardform";
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$inputname   = "input_id";
$siteparts   = \DynCom\dc\common\classes\Siteparts::get();

$sitepart_options = array();
if (isset($input_line['options'])) {
    $sitepart_options = unserialize($input_line['options']);
}

?>

<script type="text/javascript">

    function setCollectionType( _select ) {
        var collectiontype = jQuery(_select).find('option:selected').parent().attr('value');
        jQuery('#collection_type', jQuery('#<?php echo $formname; ?>')).val(collectiontype);
        if (collectiontype == 'siteparts') {
            load_sitepart_settings(jQuery(_select).val());
        } else {
            jQuery('#collection_type_settings').html('');
        }
    }


    function load_sitepart_settings( _sitepartId ) {
        jQuery('#collection_type_settings').html('');
        var formData = new FormData(jQuery('#<?php echo $formname; ?>')[0]);
        formData.append("sitepartid", _sitepartId);
        $.ajax({
                   url        : "?action=load_sitepart_form",
                   type       : 'POST',
                   data       : formData,
                   cache      : false,
                   processData: false, // Don't process the files
                   contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                   success    : function ( output, status, xhr ) {
                       if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                           window.location.replace("/dc/");
                       } else {
                           jQuery('#collection_type_settings').html(output);
                       }
                   },
                   error      : function ( jqXHR, textStatus, errorThrown ) {
                       //console.log('ERRORS: ' + textStatus);
                   }
               });
    }

</script>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_collection_field");
    } else {
        echo $translation->get("edit_collection_field");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?php
    // Zurueck zur uebersicht
    button("left", $translation->get("back"), $formname, "loadCard('edit_collection_setup', true)");

    // Speichern buttons
    button("save", $translation->get("save"), $formname, "loadCard('save_line_collection_setup', true)");
    button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_line_collection_setup', true)");

    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_collection_setup', true, '{$translation->get('delete_line_confirm')}')");
    }
    ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}

$is_teaser = 1;
if (isset($input_line["is_teaser"])) {
    $is_teaser = $input_line["is_teaser"];
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_line_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $_REQUEST['input_id'] ?>">
    <input type="hidden" name="collection_type" id="collection_type" value="<?= $input_line["fieldtype"]; ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <?php if ($input_line['fieldtype'] == 'standard'): ?>
            <tr>
                <td>
                    <? create_type_select($translation->get("choose_type"), "main_input_choose_type", $input_line); ?>
                </td>
                <td>

                </td>
            </tr>
        <?php else: ?>
            <input type="hidden" name="main_input_choose_type" value="<?php echo $input_line['type_id']; ?>" />
        <?php endif; ?>

        <tr>
            <td>

                <? input($translation->get("textkey"), "main_input_code", "code", $input_line["code"], 20) ?>
            </td>

            <td>

            </td>
        </tr>

        <tr>
            <td>
                <? input($translation->get("description"), "main_input_description", "text", $input_line["fieldname"], 45) ?>
            </td>

            <td>
            </td>
        </tr>

        <tr>
            <td>
                <? input($translation->get("show_in_teaser"), "main_input_is_teaser", "checkbox", $is_teaser) ?>
            </td>

            <td>

            </td>
        </tr>

    </table>

    <div id="collection_type_settings">
        <?php
        if ($input_line['fieldtype'] == 'siteparts' && $input_line['type_id'] > 0) {
            $sitepartCode   = $siteparts[$input_line['type_id']]['code'];
            $sitepartfolder = $siteparts[$input_line['type_id']]['folder'];

            $include = MODULE_PATH . $sitepartfolder . "/collection_" . $sitepartCode . "_cardform.inc.php";
            if (file_exists($include)) {
                require_once($include);
            }
        } elseif ($input_line['fieldtype'] == 'standard' && $input_line['type_id'] == 5) {
            $include = MODULE_PATH . "collection/edit_collection_image_config.inc.php";
            if (file_exists($include)) {
                require_once($include);
            }
        }
        ?>
    </div>

</form>