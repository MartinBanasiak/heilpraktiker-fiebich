<?
$formname                    = "form_line_card";
$translation                 = \DynCom\dc\common\classes\Registry::get("translation");
$input_page_id               = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id         = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
$collection_setup_content_id = (isset($_REQUEST["collection_setup_content_id"]) ? $_REQUEST["collection_setup_content_id"] : "");
$header_id                   = (isset($input_line["header_id"]) ? $input_line["header_id"] : "");
$input_component_id          = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
?>

<div id="overlaycrumb">
    <?php if ($input_line["id"] == "") {
        echo $translation->get("new_gallery_line");
    } else {
        echo $translation->get("edit_gallery_line");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?
    if (is_collection_edit()) {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_collection', true)");
    } else {
        button("left", $translation->get("overview"), $formname, "loadCard('edit_gallery', true)");
    }
    ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_line_gallery', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "jQuery('#save_and_close', jQuery('#" . $formname . "')).val(1);loadCard('save_line_gallery', true)"); ?>
    <?php
    if ($input_line["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_line_gallery', true, '{$translation->get('delete_line_confirm')}')");
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

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
    <input name="input_line_id" type="hidden" value="<?= $input_line["id"] ?>" />
    <input name="input_id" type="hidden" value="<?= $header_id; ?>" />
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_setup_content_id" value="<?php echo $collection_setup_content_id; ?>" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />
    <input type="hidden" name="linklist_form" value="form_line_gallery_list" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("file"), "input_file", "file", $input_line["file"]) ?>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>

        <tr>
            <td>

                <? input($translation->get("description"), "input_name", "text", $input_line["description"], 255) ?>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>
        <?php
        if ($input_line['preview'] != "") {
            ?>
            <tr>
                <td>
                    <div class="label"><?php echo $translation->get("preview"); ?></div>
                    <?php echo $input_line['preview']; ?>
                    <input type="hidden" name="preview" value="<?php echo $input_line['preview']; ?>" />
                </td>
                <td>

                </td>
            </tr>
        <?php
        }
        ?>
    </table>

</form>

<?php
if($input_line["id"] == "") {
    ?>
    <h2><?php echo $translation->get("dropzone_multiple_imageupload"); ?></h2>
    <ul class="toolbar_menu">
        <?
        if(is_collection_edit()) {
            button("left",$translation->get("overview"),$formname,"loadCard('edit_collection', true)", "", false);
        } else {
            button("left",$translation->get("overview"),$formname,"loadCard('edit_gallery', true)", "", false);
        }

        button("upload",$translation->get("dropzone_start_upload"),$formname,"startDropzoneUpload()", "", false);
        button("delete",$translation->get("dropzone_clear_list"),$formname,"removeDropzoneFiles()", "", false);
        ?>
    </ul>
    <div class="clearfix"></div>
    <form id="dropzone" class="dropzone" action="?action=save_line_gallery">
        <input name="input_id" type="hidden" value="<?= $header_id; ?>" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
        <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
        <input type="hidden" name="collection_setup_content_id" value="<?php echo $collection_setup_content_id; ?>" />
        <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
        <input name="save_and_close" id="save_and_close" type="hidden" value="0" />
        <input type="hidden" name="linklist_form" value="form_line_gallery_list" />
        <input type="hidden" name="dcDropzone" value="1" />
    </form>

    <script type="text/javascript">
        jQuery('#dropzone').dropzone({
            paramName : 'input_file',
            complete: function(file) {
                if (file._removeLink) {
                    file._removeLink.textContent = this.options.dictRemoveFile;
                }
                if (file.previewElement) {
                    return file.previewElement.classList.add("dz-complete");
                }
            },
            queuecomplete: function() {
                dcDropzone.options.autoProcessQueue = false;
            },
            dictDefaultMessage: '<?php echo $translation->get("dropzone_message1"); ?>',
            dictFallbackMessage: '<?php echo $translation->get("dropzone_message2"); ?>',
            dictFallbackText: '<?php echo $translation->get("dropzone_message3"); ?>',
            dictRemoveFile : "<?php echo $translation->get("dropzone_message4"); ?>",
            dictCancelUpload : '<?php echo $translation->get("dropzone_message5"); ?>',
            dictCancelUploadConfirmation : '<?php echo $translation->get("dropzone_message6"); ?>',
            acceptedFiles : 'image/*',
            autoProcessQueue : false,
            addRemoveLinks : true,
            maxFiles: 100,
            dictMaxFilesExceeded : '<?php echo $translation->get("dropzone_message7"); ?>'

        });

        var dcDropzone = jQuery('#dropzone').get(0).dropzone;

        function startDropzoneUpload() {
            if (dcDropzone.getQueuedFiles().length == 0 &&
                dcDropzone.getUploadingFiles().length == 0
            ) {
                return;
            }

            dcDropzone.options.autoProcessQueue = true;
            dcDropzone.processQueue();
        }

        function removeDropzoneFiles() {
            dcDropzone.removeAllFiles();
        }
    </script>
    <?php
} // end $input_line['preview'] != ""
?>