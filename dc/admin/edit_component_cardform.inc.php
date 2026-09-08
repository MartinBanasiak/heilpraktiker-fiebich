<?php
$translation       = \DynCom\dc\common\classes\Registry::get("translation");
$formname          = "form_component_card";
$main_layout_id    = $GLOBALS["language"]["main_layout_id"];
$main_component_id = (int)$input_component["id"];

function create_checkboxes( $main_layout_id, $main_component_id, $type ) {
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
					main_component_id = '" . $main_component_id."'";
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

// layout area ID auslesen
$layout_area_id = 0;
if($input_component['layout_area_id'] > 0) {
    $result = mysqli_query($GLOBALS['mysql_con'],"SELECT * FROM main_layout_area WHERE id = " . $input_component['layout_area_id']);
    $row = @mysqli_fetch_assoc($result);
    if($row['main_layout_id'] == $GLOBALS["language"]["main_layout_id"]) {
        $layout_area_id = $row['id'];
    } else {
        $result = mysqli_query($GLOBALS['mysql_con'],"select id from main_layout_area where code = '" . $row['code'] . "' AND main_layout_id = " . $GLOBALS["language"]["main_layout_id"]);
        $row = @mysqli_fetch_assoc($result);
        $layout_area_id = $row['id'];
    }
}

?>

    <div id="overlaycrumb">
        <?php if ($input_component["id"] == "") {
            echo $translation->get("new_component");
        } else {
            echo $translation->get("edit_component");
        }
        ?>
        <div id="closeoverlay" onclick="disableOverlay();"></div>
    </div>

    <ul class="toolbar_menu">
        <?= button("save", $translation->get("save"), $formname, "loadCard('save_component', true)"); ?>
        <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_component', true, '', true)"); ?>
        <li>
            <ul>
                <?php
                if ($input_component["id"] != "") {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_component', true, '{$translation->get('delete_component_confirm')}', true)", "", FALSE);
                }
                ?>
                <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
            </ul>
        </li>
    </ul>
    <div class="clearfix"></div>

<?php
if (is_array($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input name="input_component_id" type="hidden" value="<?= $input_component["id"] ?>">
        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>

                    <? input($translation->get("textkey"), "input_code", "code", $input_component["code"], 20) ?>
                    <? input($translation->get("description"), "input_description", "text", $input_component["title"], 255) ?>
                    <? input($translation->get("active"), "input_active", "checkbox", $input_component['active']); ?>
                    <? input($translation->get("show_in_all_languages"), "input_all_languages", "checkbox", $input_component["all_languages"]) ?>


                </td>

                <td>
                    <? layout_area_select($translation->get("layout_area"), "input_layout_area_id", $layout_area_id); ?>
                    <? input_select($translation->get("default_active"), "input_component_default_active", $values = array('1', '0'), $value_names = array($translation->get("yes"), $translation->get("no")), $input_component["default_active"]); ?>
                    <?php
                    if ($input_component["id"] != "") {
                        show_changed_on($input_component["id"], "main_component");
                        show_changed_by($input_component["id"], "main_component");
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>

<?
if ($input_component['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_component_line_listform.inc.php';
}
?>