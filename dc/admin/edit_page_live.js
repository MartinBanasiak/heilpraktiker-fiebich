function create_live_includes() {
    if(typeof jQuery=='undefined') {
        insert_javascript('/plugins/jquery/jquery-1.6.4.min.js');
    }
}

function insert_javascript(_src) {
    var headTag = document.getElementsByTagName("head")[0];
    var jqTag = document.createElement('script');
    jqTag.type = 'text/javascript';
    jqTag.src = _src;
    jqTag.onload = load_live_edit;
    headTag.appendChild(jqTag);
}

create_live_includes();

function load_live_edit() {
    // zu allen links den live_edit parameter hinzufuegen
    $('a:not(.textcontent a)').each(function() {
        var href = $(this).attr('href');

        if(href) {
            href += (href.match(/\?/) ? '&' : '?') + "live_edit=1";
            $(this).attr('href', href);
        }
    });

    // bei kollektionen die einzelnen sitepart-bearbeitungen ausschalten
    jQuery('.live_edit_collection, .live_edit_component').each(function() {
        var sitepartcontainer = $(this).find('.live_edit_container');
        sitepartcontainer.find('.live_edit_overlay').remove();
        sitepartcontainer.find("div[contenteditable='true']").removeAttr("contenteditable");
        sitepartcontainer.removeClass("live_edit_container");

        $(this).find("div[contenteditable='true']").removeAttr("contenteditable");
    });

    jQuery('.live_edit_container').each(function() {
        var containerWidth = jQuery(this).width();
        var containerHeight = jQuery(this).height();

        if($(this).parent().css("float") == "left" || $(this).parent().css("float") == 'right') {
            $(this).css("float", $(this).parent().css("float"));
        }

        if(jQuery(this).find("span.live_edit_overlay").data("sitepartheaderid") == 0) {
            jQuery(this).removeClass("live_edit_container");
            jQuery(this).find("span.live_edit_overlay").remove();
            return;
        }

        if(jQuery(this).find("div[contenteditable='true']").length > 0) {
            return;
        }
        jQuery(this).prepend(
            jQuery("<div>")
                .addClass("live_edit_inner_overlay")
        )
    });

    jQuery('.live_edit_overlay').click(function() {

        var is_collection = $(this).data("collection");
        if(is_collection === true) {
            // eine kollektion wird bearbeitet, nicht einzelne siteparts einer kollektion
            var collection_id = $(this).data("collectionid");
            var collection_setup_id = $(this).data("collection_setup_id");

            parent.gotoLocation(parent.build_collection_edit_url(collection_setup_id, collection_id));
            return;
        }

        var is_component = $(this).data("component");
        if(is_component === true) {
            // ein baustein wird bearbeitet, nicht einzelne siteparts eines bausteins
            var component_id = $(this).data("componentid");
            parent.gotoLocation(parent.component_url + component_id);
            return;
        }

        var aFormData = new FormData();

        // hier wird ein einzelnes sitepart bearbeitet
        var sitepart = $(this).data("sitepartid");
        var sitepart_header_id = $(this).data("sitepartheaderid");
        var page_id = $(this).data("pageid");
        var link_id = $(this).data("linkid");

        aFormData.append("input_page_id", page_id);
        aFormData.append("input_id", link_id);
        aFormData.append("data-sitepartid", sitepart);

        parent.loadCard('edit_line_page', true, '', false, aFormData);
    });

    CKEDITOR.disableAutoInline = true;
    CKEDITOR.dtd.$removeEmpty['span'] = false;

    var editorCounter = 1;
    $("div[contenteditable='true']" ).each(function( index ) {
        $(this).attr('id', "live_textcontainer_" + editorCounter);
        var content_id = $(this).attr('id');
        var sitepaer_header_id = $(this).data('textcontentid');

        editorCounter++;

        CKEDITOR.inline( content_id, {
            customConfig: '/plugins/ckeditor/dc_config_live.js',
            contentsCss: '/' + parent.config_CKEDITOR_custom_config,
            filebrowserBrowseUrl : '/plugins/ckfinder/ckfinder.html',
            filebrowserImageBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Images',
            filebrowserFlashBrowseUrl : '/plugins/ckfinder/ckfinder.html?type=Flash',
            filebrowserUploadUrl :
                '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            filebrowserImageUploadUrl :
                '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
            filebrowserFlashUploadUrl : '/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',

            on: {
                blur: function( event ) {
                    var data = event.editor.getData();
                    inline_ckeditor_update(sitepaer_header_id, data);
                }
            }
        } );
    });

    $(".live_edit_inner_overlay").click(function() {
        var editableContent = $(this).next("div[contenteditable='true']");
        if(editableContent.length == 0) {
            return;
        }

        //CKEDITOR.inline( editableContent.attr("id") );
        $(this).css("display", "none");
    });
}

jQuery(document).ready(function() {
    load_live_edit();
});

function inline_ckeditor_update(sitepaer_header_id, _data) {
    var request = jQuery.ajax({
        url: String(parent.document.location).replace(/\?.*$/, '') + "?action=edit_line_sitepart_update_page",
        type: "POST",
        data: {
            content : _data,
            sitepaer_header_id : sitepaer_header_id
        },
        dataType: "html"
    });
}