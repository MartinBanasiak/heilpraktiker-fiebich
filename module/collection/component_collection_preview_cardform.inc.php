<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_collection_preview_list";
$inputname           = "input_id";
$input_component_id  = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$collection_setup_id = 0;

// erstelle kollektionen sammeln
$collection_setups = array();
$query             = "SELECT * FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
$result            = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_array($result, 1)) {
    $collection_setups[$row['id']] = $row;
}

$collections    = array();
$collection_ids = array();
if ($input_line['main_collection_setup_id'] != "") {
    $query  = "SELECT * FROM main_collection WHERE main_collection_setup_id = '" . $input_line['main_collection_setup_id'] . "' AND description != ''";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    while ($row = @mysqli_fetch_array($result, 1)) {
        $collections[]    = $row['description'];
        $collection_ids[] = $row['id'];
    }
}

$active_groups = array();
$query         = "SELECT main_collection_setup_group_id FROM main_component_collection_group_link WHERE main_component_link_id = '" . (int)$input_line["id"]."'";
$result        = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_array($result)) {
    $active_groups[] = $row['main_collection_setup_group_id'];
}
?>

<script type="text/javascript">
    function loadPagesWithCollection( collection_setup_id ) {
        load_collections(collection_setup_id);

        $('.collection_pages', $('#<?php echo $formname; ?>')).html("");
        $('.collection_preview_group_select_container', $('#<?php echo $formname; ?>')).html("");

        if (collection_setup_id == "" || collection_setup_id == 0) {
            return;
        }

        $('.loader', $('#<?php echo $formname; ?>')).show();

        // ajax
        var formData = new FormData(jQuery('#<?php echo $formname; ?>')[0]);
        formData.append("collection_setup_id", collection_setup_id);
        formData.append("component_link_id", '<?php echo $input_line["id"]; ?>');
        $.ajax({
                   url        : "?action=load_collection_pages_page",
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
                           $('.collection_pages', $('#<?php echo $formname; ?>')).html(output.page_with_collection_select);
                           $('.collection_preview_group_select_container', $('#<?php echo $formname; ?>')).html(output.collection_preview_group_select);
                       }
                   },
                   dataType   : 'json'
               });
    }

    function load_collections( collection_setup_id ) {
        $('.collection_list_choose', $('#<?php echo $formname; ?>')).html("");

        var formData = new FormData(jQuery('#<?php echo $formname; ?>')[0]);
        formData.append("main_collection_setup_id", collection_setup_id);
        $.ajax({
                   url        : "?action=load_collections_page",
                   type       : 'POST',
                   data       : formData,
                   cache      : false,
                   processData: false, // Don't process the files
                   contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                   success    : function ( output, status, xhr ) {
                       if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                           window.location.replace("/DC/");
                       } else {
                           $('.collection_list_choose', $('#<?php echo $formname; ?>')).html(output);
                       }
                   },
                   error      : function ( jqXHR, textStatus, errorThrown ) {
                       //console.log('ERRORS: ' + textStatus);
                   }
               });
    }

    function toggle_view_type( _type ) {
        if (_type == 0) {
            $('.collection_list_choose', $('#<?php echo $formname; ?>')).show();
            $('.collection_items_choose', $('#<?php echo $formname; ?>')).hide();
        } else if(_type == 1) {
            $('.collection_list_choose', $('#<?php echo $formname; ?>')).hide();
            $('.collection_items_choose', $('#<?php echo $formname; ?>')).show();
        } else  {
            $('.collection_list_choose', $('#<?php echo $formname; ?>')).hide();
            $('.collection_items_choose', $('#<?php echo $formname; ?>')).hide();
        }
    }

    function save_and_close_page_line() {
        jQuery('#save_and_close', jQuery('#<?php echo $formname; ?>')).val("1");
        loadCard('save_collection_preview_page', true);
    }
</script>

<div id="overlaycrumb">
    <?php echo $translation->get("assign_collection_preview"); ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit_component', true)"); ?>

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_collection_preview_page', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "save_and_close_page_line();"); ?>
</ul>

<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_line["id"] ?>">
    <input type="hidden" name="get_real_page_link_id" value="0" />
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>

                <? create_collection_select($collection_setups, $translation->get("choose_collection_type"), "main_collection_setup_id", $input_line, FALSE, TRUE, "loadPagesWithCollection(this.value);"); ?>
            </td>
            <td>
                <? input_select($translation->get("collection_preview_type"), "main_collection_view_type", $values = array('0', '1', '2'), $value_names = array($translation->get("single_collection"), $translation->get("collection_list"), $translation->get("collection_calendar")), $input_line["main_collection_view_type"], FALSE, FALSE, 'onchange="toggle_view_type(this.value);"'); ?>
            </td>
        </tr>

        <tr>
            <td>
                <img class="loader" style="display:none;" src="<?= $GLOBALS['projectRoot'] ?>/layout/admin/img/2015/ajax-loader.gif" />

                <div class="collection_pages">
                    <?
                    create_page_with_collection_select($input_line['main_collection_setup_id'], $translation->get("page_with_collections"), "main_collection_page_list_id", $input_line, FALSE);
                    ?>
                </div>
            </td>

            <td>

                <?php
                if ($input_line["main_collection_view_type"] == 0) {
                    $display_list_choose = '';
                    $display_item_choose = 'style="display:none;"';
                } elseif($input_line["main_collection_view_type"] == 1) {
                    $display_item_choose = '';
                    $display_list_choose = 'style="display:none;"';
                } else {
                    $display_item_choose = 'style="display:none;"';
                    $display_list_choose = 'style="display:none;"';
                }

                // einzelne kollektion
                echo "<div class=\"collection_list_choose\" " . $display_list_choose . ">";
                input_select($translation->get("choose_collection"), "main_collection_id", $values = $collection_ids, $value_names = $collections, $input_line["main_collection_id"], FALSE, FALSE);
                echo "</div>";

                // kollektionsliste
                echo "<div class=\"collection_items_choose\" " . $display_item_choose . ">";
                input($translation->get("collection_list_items"), "main_collection_items", "text", $input_line["main_collection_items"], 255);
                echo "</div>";
                ?>

            </td>
        </tr>
    </table>
    <div class="collection_preview_group_select_container">
        <?php create_collection_preview_group_select($input_line['main_collection_setup_id'], $input_line["id"], $active_groups); ?>
    </div>
</form>
