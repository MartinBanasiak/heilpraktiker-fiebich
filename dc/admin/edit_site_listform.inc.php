<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_site_list";
$inputname   = "input_id";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete', false, '{$translation->get('delete_website_confirm')}')"); ?>
    <?= button("down", $translation->get("geoip_update"), $formname, "sendRequest('geoip', true)"); ?>
    <?= button("copy", $translation->get("copy_site"), $formname, "copy_site('" . $formname . "')"); ?>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_websites'); ?></h1>

    <?php
    if (count($messages)) {
        echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
    }
    ?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_id" value="" />

        <div class="requestLoader"></div>
        <?
        $query  = "SELECT main_site.id AS 'id',  main_site.name AS '" . $translation->get("description") . "', main_site.code AS '" . $translation->get("textkey") . "', main_language.name AS '" . $translation->get("default_language") . "', (SELECT Count(id) FROM main_navigation WHERE main_site_id = main_site.id) AS '" . $translation->get("number_menu_items") . "' FROM main_site LEFT JOIN main_language ON main_site.std_main_language_id = main_language.id ORDER BY main_site.code ASC";
        $format = array('option', 'text', 'text', 'text', 'integer');
        if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
            linklist($result, "form_site_list", $format, "input_id");
        }
        ?>

    </form>
</div>

<script type="text/javascript">
    function copy_site(_formname) {

        // in einer normalen liste pruefen ob eine zeile markiert ist
        if (dc.overlay_mode !== true) {
            var currentMarkedLinklistRow = jQuery('.linklist_active', jQuery('#' + dc.current_formname));
            if (dc.current_linklist === null || currentMarkedLinklistRow.length <= 0) {
                return;
            }
        }

        if(!confirm("<?php echo $translation->get("copy_site_confirm"); ?>")) {
            return;
        }

        // in einer normalen liste wird die id der markierten row ausgelesen
        if (dc.overlay_mode !== true) {
            var input_id = currentMarkedLinklistRow.data('inputid');
            jQuery('input.selected_linklist_row', jQuery('#' + dc.current_formname)).val(input_id);
        }

        // loader wird engezeigt
        jQuery('#overlay').show();
        if (dc.overlay_mode === true) {
            $('#overlayLoader').show();
        }
        jQuery('body').append(jQuery('<div />').attr("id", "copyOverlay"));

        jQuery("#copyOverlay").append("<p><?php echo $translation->get("copy_start"); ?></p>");

        copy_ajax_call("copy_site", 0, _formname);
    }

    function copy_ajax_call(step, languageId, _formname) {
        var formData = new FormData(jQuery('#' + _formname)[0]);
        formData.append("next_step", step);
        formData.append("copy_language_id", languageId);
        $.ajax({
            url        : "?action=copy",
            type       : 'POST',
            data       : formData,
            cache      : false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success    : function ( output, status, xhr ) {
                if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                    window.location.replace("/dc/");
                } else {
                    if(output.next_step != "") {
                        jQuery("#copyOverlay").append("<p>"+ output.message +"</p>");
                        copy_ajax_call(output.next_step, output.copy_language_id, _formname);
                        return;
                    }
                    setTimeout(function() {
                        $.each(output.message, function( index, value ) {
                            jQuery('#overlayMessages').html(value);
                        });

                        if (dc.overlay_mode !== true) {
                            jQuery('#overlay').hide();
                            reloadList();
                            return;
                        }

                        jQuery('#overlayLoader').hide();
                        jQuery("#copyOverlay").remove();
                        jQuery('#overlayContent').show();

                    }, 1500);
                }
            },
            dataType   : 'json'
        });
    }

</script>

<style>
    #copyOverlay {
        position:fixed;
        background-color:#fff;
        border: 2px solid #666;
        padding:10px;
        width:500px;
        left:50%;
        top:40px;
        margin-left:-250px;
        z-index:2004;
        min-height:100px;
    }
</style>