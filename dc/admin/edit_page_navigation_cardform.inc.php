<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_page_card";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$main_page_id   = (int)$input_page["id"];

function create_checkboxes( $main_layout_id, $main_page_id, $type ) {
    $query             = "
			SELECT id, path, default_active
			FROM main_layout_inclusions
			WHERE main_layout_id = '" . $main_layout_id . "' AND type = '" . $type . "'
			ORDER BY sorting ASC";
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    $layout_inclusions = array();
    IF (@mysqli_num_rows($result) > 0) {
        WHILE ($layout_inclusions = @mysqli_fetch_assoc($result)) {
            $query2        = "SELECT active FROM main_page_layout_inclusion_link WHERE
					main_layout_includes_id = '" . $layout_inclusions["id"] . "' AND
					main_page_id = " . $main_page_id;
            $result2       = mysqli_query($GLOBALS['mysql_con'], $query2);
            $boxname_array = array();
            $boxname_array = explode("/", $layout_inclusions["path"]);
            $array_index   = count($boxname_array) - 1;
            $boxname       = $boxname_array[$array_index];
            IF (@mysqli_num_rows($result2) == 0) {
                input($boxname, "input_script_" . $layout_inclusions["id"] . "_active", "checkbox", $layout_inclusions["default_active"]);
            } ELSE {
                input($boxname, "input_script_" . $layout_inclusions["id"] . "_active", "checkbox", @mysqli_result($result2, 0));
            }
        }
    }
}

?>

<div id="overlaycrumb">
    <?php if ($input_page["id"] == "") {
        echo $translation->get("new_site");
    } else {
        echo $translation->get("edit_site");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_page', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_page', true, '', true)"); ?>
    <?= button("link", $translation->get("assign_navigation"), $formname, "loadCard('assign_navigation_page', true, '', true)"); ?>
    <?php
    if ($input_page["id"] != "") {
        echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_page', true, '{$translation->get('delete_textcontent_confirm')}', true)");
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

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_page_id" type="hidden" value="<?= $input_page["id"] ?>">
    <input name="input_main_layout_id" type="hidden" value="<?= $main_layout_id ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("name"), "input_page_name", "text", $input_page["title"], 255) ?>
                <? input($translation->get("browsertitle"), "input_browser_title", "text", $input_page["subtitle"], 255) ?>
                <? input($translation->get("validity_from"), "input_validity_from", "date", datefromsql($input_page["validity_from"]), 10) ?>
                <? input($translation->get("validity_to"), "input_validity_to", "date", datefromsql($input_page["validity_to"]), 10) ?>
                <? input($translation->get("active"), "input_active", "checkbox", $input_page["active"]) ?>
                <? input($translation->get("is_shopping_world"), "input_shopping_world", "checkbox", $input_page["shopping_world"]) ?>
            </td>
            <td>

                <? create_checkboxes($main_layout_id, $main_page_id, 'js'); ?>
                <? create_checkboxes($main_layout_id, $main_page_id, 'css'); ?>
            </td>
        </tr>
    </table>
</form>