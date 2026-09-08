<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_edit_main_navigation_list";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "new_ckfinder()"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "open_ckfinder()"); ?>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_files'); ?></h1>

    <form id="<?php echo $formname; ?>" name="<?php echo $formname; ?>" method="post">
        <div class="requestLoader"></div>
        <input type="hidden" class="selected_linklist_row" name="input_main_navigation_id" value="" />

        <div class="dd" id="nestable">
            <?

            show_dir_structure($site, $language);

            ?>
        </div>

    </form>

</div>

<script type="text/javascript">
    function new_ckfinder() {
        var finder = CKFinder.popup({
            basePath: '/plugins/ckfinder/',
            rememberLastFolder: false
        });
        /*var finder = new CKFinder();
        finder.basePath = '/ckfinder/';
        finder.rememberLastFolder = false;
        finder.popup();*/
    }

    function open_ckfinder( row ) {
        var row = row || false;

        if (row !== false) {
            var currentMarkedLinklistRow = $(row);
        } else {
            var currentMarkedLinklistRow = jQuery('.linklist_active', jQuery('#' + dc.current_formname));
        }


        if (currentMarkedLinklistRow.length <= 0) {
            return;
        }

        var startResource = currentMarkedLinklistRow.data('startresource');
        var startFolder = currentMarkedLinklistRow.data('startfolder');
        if (startFolder != "") {
            var startupPath = startResource + ':/' + startFolder + '/';
        } else {
            var startupPath = startResource + ':/';
        }

        var finder = CKFinder.popup({
            basePath: '/ckfinder/',
            rememberLastFolder: false,
            startupPath: startupPath,
            startupFolderExpanded: true
    });
        /*var finder = new CKFinder();
        finder.basePath = '/ckfinder/';
        finder.startupPath = startupPath;
        finder.startupFolderExpanded = true;
        finder.rememberLastFolder = false;

        finder.popup();*/
    }

    var reorderCall = null;
    var dragEnabled = false;

    function toggleDragDrop( _aTag ) {
        if (dragEnabled === false) {
            dragEnabled = true;
            $('ol.sortable').addClass("enabled");
            $('ol.sortable').nestedSortable("option", "disabled", false);
            $(_aTag).html("<?php echo $translation->get('disable_drag_drop'); ?>");
        } else {
            dragEnabled = false;
            $('ol.sortable').removeClass("enabled");
            $('ol.sortable').nestedSortable("option", "disabled", true);
            $(_aTag).html("<?php echo $translation->get('enable_drag_drop'); ?>");
        }
    }

    //$('.dd').nestable({ /* config options */ });
    $('ol.sortable').nestedSortable({
                                        forcePlaceholderSize: true,
                                        handle              : '.dd-icon',
                                        helper              : 'clone',
                                        items               : 'li',
                                        opacity             : .6,
                                        placeholder         : 'placeholder',
                                        revert              : 250,
                                        tabSize             : 25,
                                        tolerance           : 'pointer',
                                        toleranceElement    : '> div',
                                        maxLevels           : 5,
                                        isTree              : true,
                                        expandOnHover       : 1000,
                                        cookieIndex         : 'nestedSortable_files_expanded',
                                        startCollapsed      : true,
                                        stop                : function ( event, ui ) {
                                            reorder_navigation();
                                            store_expanded();
                                        },
                                        disabled            : true
                                    });

    $('.disclose').click(function () {
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
        store_expanded();
    });

    function store_expanded() {
        var list = [];
        $('ol.sortable').find('li.mjs-nestedSortable-expanded').each(function () {
            list.push($(this).attr("id"));
        });
        $.cookie('nestedSortable_files_expanded', list.join(','));
    }
</script>