<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_edit_main_navigation_list";
?>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new_menuitem"), $formname, "loadCard('new_navigation', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_navigation')"); ?>
    <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_navigation', false, '{$translation->get('delete_navitem_confirm')}')"); ?>

    <li>
        <ul>
            <?= button("up", $translation->get("go_up"), $formname, "sendRequest('moveup_navigation')", "", FALSE); ?>
            <?= button("down", $translation->get("go_down"), $formname, "sendRequest('movedown_navigation')", "", FALSE); ?>
        </ul>
    </li>

    <li>
        <ul>
            <?= button("left", $translation->get("go_left"), $formname, "sendRequest('moveleft_navigation')", "", FALSE); ?>
            <?= button("right", $translation->get("go_right"), $formname, "sendRequest('moveright_navigation')", "", FALSE); ?>
        </ul>
    </li>
</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('left_navigation'); ?></h1>

    <form id="<?php echo $formname; ?>" name="<?php echo $formname; ?>" method="post">
        <div class="requestLoader"></div>
        <input type="hidden" class="selected_linklist_row" name="input_main_navigation_id" value="" />

        <div class="dd" id="nestable">
            <?
            show_main_navigation($site, $language);
            ?>
        </div>

    </form>

</div>
<script type="text/javascript">
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
                                        cookieIndex         : 'nestedSortable_expanded',
                                        startCollapsed      : true,
                                        stop                : function ( event, ui ) {
                                            reorder_navigation();
                                            store_expanded();
                                        },
                                        disabled            : false
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
        $.cookie('nestedSortable_expanded', list.join(','));
    }

    function reorder_navigation() {
        if (reorderCall !== null) {
            reorderCall.abort();
        }

        serialized = $('ol.sortable').nestedSortable('serialize');
        arraied = $('ol.sortable').nestedSortable('toHierarchy', {startDepthCount: 0});
        var parameter = {
            list: arraied
        };
        reorderCall = jQuery.post("?action=reorder_navigation", parameter, function ( output, status, xhr ) {
            if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                window.location.replace("/dc/");
            } else {
                console.log(output);
            }
        });
    }

</script>