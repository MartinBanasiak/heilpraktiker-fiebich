<?
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_page_list";
$headline = 'left_sites';

$template = 0;
$shoppingworld = 0;

if($_GET['level_2'] == 'templates') {
    $template = 1;
    $headline = 'left_templates';
}
if($_GET['level_2'] == 'shoppingworld') {
    $shoppingworld = 1;
    $headline = 'left_shoppingworld';
}

$language_part = 'page';
if($_GET['level_2'] == 'templates') {
    $language_part = 'template';
}
?>

<script type="text/javascript">
    function filter_page() {
        jQuery('#form_page_list .requestLoader').show();

		var filter_id = jQuery('#page_filter').val();
		var include_sub = jQuery('#include_children').is(':checked') ? 1 : 0;

		jQuery('#form_page_list').attr("action", "?filter_id=" + filter_id + "&include_sub=" + include_sub).submit();

		return;

        var formData = {
            filter_id       : jQuery('#page_filter').val(),
            include_children: jQuery('#include_children').is(':checked') ? 1 : 0
        };

        $.ajax({
                   url    : "?action=filter_page",
                   type   : 'POST',
                   data   : formData,
                   success: function ( output, status, xhr ) {
                       if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                           window.location.replace("/dc/");
                       } else {
                           jQuery('#form_page_list .requestLoader').hide();
                           jQuery('#page_linklist').html(output);

                           initForm();
                           initLinklist();
                       }
                   }
               });
    }

    function preview_page() {
        var template = <?= $template ?>;
        var shoppingworld = <?= $shoppingworld ?>;
        var currentMarkedLinklistRow = jQuery('.linklist_active', jQuery('#' + dc.current_formname));
        var input_id = currentMarkedLinklistRow.data('inputid');
		var current_location = String(document.location).replace(/\?.*$/, '');
		var href = current_location + "?preview_page=true&input_page_id=" + input_id;
		if (template) {
            href = href + '&template=1';
        }
        if (shoppingworld) {
		    href = href + '&shoppingworld=1';
        }
		window.open(href);
    }
</script>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_page', true)"); ?>
    <?
        if($_GET['level_2'] !== 'templates') {
            button("edit_live", $translation->get("live_edit"), $formname, "loadCard('edit_live_content_page')");
        }
    ?>
	<?= button("edit",$translation->get("edit"),$formname,"loadCard('edit_content_start_page')"); ?>

    <?
    if($_GET['level_2'] !== 'templates') {
        button("preview", $translation->get("preview"), $formname, "preview_page()");
    }
    ?>
    <li>
        <ul>
            <?= button("properties", $translation->get($language_part . "_properties"), $formname, "loadCard('edit_page')", "", FALSE); ?>
            <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_page', false, '{$translation->get('delete_' . $language_part . '_confirm')}')", "", FALSE); ?>
        </ul>
    </li>

</ul>

<div id="mainContent">
    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation($headline); ?></h1>

    <?
    if ($_GET["level_2"] !== "shoppingworld" && $_GET['level_2'] !== 'templates' ) {
        ?>
        <h3><?php echo get_translation('filter'); ?>:</h3>
        <?
    }
    ?>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">
        <?php
        if ($_GET["level_2"] !== "shoppingworld" && $_GET['level_2'] !== 'templates') {
            page_filter($GLOBALS["site"]['id'],$GLOBALS["language"]['id'], (int)$_REQUEST['filter_id'], (isset($_REQUEST['include_sub']) ? (int)$_REQUEST['include_sub'] : 1));
            ?>
            <div class="clearfix"></div>
            <?
        }
        ?>

        <input type="hidden" class="selected_linklist_row" name="input_page_id" value="" />
        <div class="requestLoader"></div>
        <div id="page_linklist">
            <?
            filter_page();
            ?>
        </div>
    </form>

</div>