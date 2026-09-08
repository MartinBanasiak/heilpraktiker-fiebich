<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_collection_list";
$inputname   = "input_id";
$filter = (isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "");

$collection_setup_info = get_collection_setup_info();
delete_unused_collections();
?>

<script type="text/javascript">
    function filter_collection() {
        jQuery('#form_collection_list .requestLoader').show();

        var filter = jQuery('#collection_filter').val();

        jQuery('#form_collection_list').attr("action", "?filter=" + filter).submit();

        return;

        var formData = {
            filter_id: jQuery('#collection_filter').val(),
        };

        jQuery('#filter_collection_setup_id').val(jQuery('#collection_filter').val());
        $.ajax({
                   url    : "?action=filter_collection",
                   type   : 'POST',
                   data   : formData,
                   success: function ( output, status, xhr ) {
                       if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                           window.location.replace("/dc/");
                       } else {
                           jQuery('#form_collection_list .requestLoader').hide();
                           jQuery('#page_linklist').html(output);

                           initForm();
                           initLinklist();
                       }
                   }
               });
    }
</script>

<ul class="toolbar_menu">
    <?= button("new", $translation->get("new"), $formname, "loadCard('new_collection', true)"); ?>
    <?= button("edit", $translation->get("edit"), $formname, "loadCard('edit_collection')"); ?>


    <li>
        <ul>
            <?= button("up", $translation->get("go_up"), $formname, "sendRequest('moveup_line_collection')", "", FALSE); ?>
            <?= button("down", $translation->get("go_down"), $formname, "sendRequest('movedown_line_collection')", "", FALSE); ?>
            <?= button("delete", $translation->get("delete"), $formname, "sendRequest('delete_collection', false, '{$translation->get('delete_collection_confirm')}')", "", FALSE); ?>
        </ul>
    </li>
</ul>

<div id="mainContent">

    <?php echo current_website_language($site, $language); ?>
    <h1><?php echo get_translation('top_collections'); ?> - <?php echo $collection_setup_info['description']; ?></h1>

    <h3><?php echo get_translation('filter'); ?>:</h3>



    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input type="hidden" class="selected_linklist_row" name="input_collection_id" value="" />
        <input type="hidden" id="filter_collection_setup_id" name="filter_collection_setup_id"
               value="<?php echo(isset($_REQUEST['filter_id']) ? (int)$_REQUEST['filter_id'] : ''); ?>" />


        <div class="input">
            <select id="collection_filter" name="collection_filter" class="bigselect" onchange="filter_collection();">
                <option value=""><?php echo get_translation('collection_filter_active'); ?></option>
                <option value="past"   <?php echo ($filter == "past" ? 'selected="selected"' : ""); ?>><?php echo get_translation('collection_filter_past'); ?></option>
                <option value="future" <?php echo ($filter == "future" ? 'selected="selected"' : ""); ?>><?php echo get_translation('collection_filter_future'); ?></option>
                <option value="all"    <?php echo ($filter == "all" ? 'selected="selected"' : ""); ?>><?php echo get_translation('collection_filter_all'); ?></option>
            </select>
        </div>

        <div class="clearfix"></div>
        <div class="requestLoader"></div>
        <div id="page_linklist">
            <?
            $where = "";
            if ($collection_setup_id != NULL) {
                $where = " and main_collection_setup_id = " . $collection_setup_id;
            }

            switch($filter) {
                case 'past':
                    $where .= " AND (validity_to IS NOT NULL AND validity_to < '" . date("Y-m-d") . "') ";
                    break;
                case 'future':
                    $where .= " AND (validity_from IS NOT NULL AND validity_from > '" . date("Y-m-d") . "') ";
                    break;

                case 'all':
                    $where .= " AND (main_collection.description != '' OR session_id = '" . session_id() . "') ";
                    break;

                default:
                    $where .= " AND (validity_from IS NULL OR validity_from <= '" . date("Y-m-d") . "') ";
                    $where .= " AND (validity_to   IS NULL OR validity_to   >= '" . date("Y-m-d") . "') ";
                    $where .= " AND main_collection.description != '' ";
                    break;
            }

            $query = "SELECT
		main_collection.id, 
		main_collection_setup.description AS '" . $translation->get("type") . "',
		main_collection.description AS '" . $translation->get("description") . "',
		from_unixtime(main_collection.modified_date, '%d.%m.%Y %H:%i:%s') AS '" . $translation->get("changed_on") . "', 
		main_admin_user.name AS '" . $translation->get("changed_by") . "' 
	from 
		main_collection left join main_admin_user
	ON 
		main_collection.modified_user = main_admin_user.id
		inner join main_collection_setup
	ON
	 	main_collection.main_collection_setup_id = main_collection_setup.id
	where 
		(main_collection_setup.main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR main_collection_setup.all_languages = 1) $where ORDER BY main_collection_setup_id asc, sorting asc";

            $format = array('option', 'text', 'text', 'text', 'text');
            if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
                linklist($result, $formname, $format, "input_collection_id", "edit_collection", TRUE, "update_sortorder_collection");
            }
            ?>
        </div>
    </form>

</div>