<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_field_list";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$siteparts      = \DynCom\dc\common\classes\Siteparts::get();
$input_page_id  = $_REQUEST["input_page_id"];

$language_part = 'page';
if($_GET['level_2'] == 'templates') {
    $language_part = 'template';
}
?>


<script type="text/javascript">
    var clickmode = 'new';

    function toggle_inhalte_scroller() {

        if (contentscroller.css('display') == 'none') {
            contentscroller.show();
        } else {
            //contentscroller.hide();
        }
    }

    function toggleScrollerHeadline( _headline, _clickAction ) {
        var contentscroller = $('.inhalte_toggle_container');
        $('#srollerInhalteHeadline h2').html(_headline);
        contentscroller.show();

        clickmode = _clickAction;
    }

    function contentmenuClick( _el, _module, _listOnly, _sitepartId ) {
        var listOnly = _listOnly || false;
        var sitepartId = _sitepartId || 0;
        setCurrentToolbarClicked(_el);

        if (listOnly === true) {
            loadCard('list_' + _module + '_page', true);
            return;
        }

        $('input[name="sitepart_id"]', $('#form_inhalte')).val(_sitepartId);

        if (clickmode == 'list') {
            loadCard('list_' + _module + '_page', true);
        } else {
            loadCard('new_' + _module, true);
        }
    }

    function preview_page_cardform() {
        var current_location = String(document.location).replace(/\?.*$/, '');
        window.open(current_location + "?preview_page=true&input_page_id=<?= $input_page_id ?>");
    }
</script>

<div id="overlaycrumb">
    <?php echo $translation->get("edit_assoc_content"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>


<ul class="toolbar_menu">
    <?= button("new", $translation->get("add_content2"), $formname, "toggleScrollerHeadline('" . $translation->get("add_content2") . "', 'new');"); ?>
    <?= button("link", $translation->get("add_content"), $formname, "toggleScrollerHeadline('" . $translation->get("add_content") . "', 'list');"); ?>
    <?= button("edit", $translation->get("edit_content"), $formname, "loadCard('edit_line_page')"); ?>
    <?
    if($_GET['level_2'] !== 'templates') {
        button("preview", $translation->get("preview"), $formname, "preview_page_cardform()");
    }
    ?>

    <li>
        <ul>
            <?= button("properties",$translation->get($language_part . "_properties"),$formname,"loadCard('edit_page', true)", "", false); ?>
            <?= button("properties",$translation->get("content_properties"),$formname,"loadCard('edit_line_properties_page')", "", false); ?>
        </ul>
    </li>

    <li>
        <ul>
            <?= button("delete",$translation->get("delete_" . $language_part),$formname,"loadCard('delete_page', true, '{$translation->get('delete_' . $language_part . '_confirm')}', true)", "", false); ?>
            <?= button("delete",$translation->get("delete_content"),$formname,"loadCard('delete_line_page', false, '{$translation->get('delete_assoc_pageline')}')","", false); ?>
        </ul>
    </li>


    <li>
        <ul>
            <?= button("up",$translation->get("go_up"),$formname,"loadCard('moveup_line_page')", "", false); ?>
            <?= button("down",$translation->get("go_down"),$formname,"loadCard('movedown_line_page')", "", false); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (is_array($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>


<form id="form_inhalte" name="form_inhalte" method="post" class="inhalte_toggle_container">
    <input type="hidden" name="update_visitor_data" value="1" />
    <input type="hidden" name="sitepart_id" value="0" />
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">

    <div class="inhalte_toggle_container" id="inhalte_form_headline">
        <div id="srollerInhalteHeadline"><h2></h2></div>
        <div id="scroller_inhalte_form">
            <table class="cardform" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <? layout_area_select($translation->get("layout_area")); ?>

                    </td>
                    <td>
                        <? input($translation->get("active"), "input_active", "checkbox", 1); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div id="pagecontentScroller" class="pagecontentScroller inhalte_toggle_container">
        <div class="scroller_inhalte">

            <ul class="inhalte_menu">
                <?php
                foreach ($siteparts as $sitepartId => $sitepartData) {
                    // keine shop siteparts anzeigen
                    if (isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === TRUE) {
                        continue;
                    }

                    button($sitepartData['class'], $sitepartData['description'], 'form_inhalte', "contentmenuClick(this, '{$sitepartData['code']}', false, $sitepartId)");
                }

                button("inhalt_collection graybox", $translation->get("collection"), 'form_inhalte', "contentmenuClick(this, 'collection_list', true)");
                button("inhalt_collection graybox", $translation->get("collection_preview"), 'form_inhalte', "contentmenuClick(this, 'collection_preview', true)");
                button("group graybox", $translation->get("group"), 'form_inhalte', "contentmenuClick(this, 'group', true)");

                foreach ($siteparts as $sitepartId => $sitepartData) {
                    // nur shop siteparts anzeigen
                    if (!isset($sitepartData['is_shop']) || $sitepartData['is_shop'] === FALSE) {
                        continue;
                    }

                    button($sitepartData['class'] . ' graybox', $sitepartData['description'], 'form_inhalte', "contentmenuClick(this, '{$sitepartData['code']}', false, $sitepartId)");
                }
                ?>


            </ul>

        </div>
    </div>
</form>

<div class="clearfix"></div>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" class="selected_linklist_row" name="input_id" value="" />
    <input type="hidden" name="from" value="page_line_listform" />
    <?
    show_page_link_rows();
    ?>
</form>

<script type="text/javascript">
    var reorderCall = null;

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
        maxLevels           : 6,
        isTree              : true,
        expandOnHover       : 1000,
        cookieIndex         : 'nestedSortable_pagecontent_expanded',
        startCollapsed      : true,
        stop                : function ( event, ui ) {
            reorder_navigation();
            store_expanded();
        },
        disabled            : false,
        isAllowed           : function ( placeholder, placeholderParent, originalItem ) {
            return true;
        }
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
        $.cookie('nestedSortable_pagecontent_expanded', list.join(','));
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
        reorderCall = jQuery.post("?action=reorder_content_page", parameter, function ( output, status, xhr ) {
            if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                window.location.replace("/dc/");
            }
        });
    }

</script>