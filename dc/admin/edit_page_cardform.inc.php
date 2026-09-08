<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_page_card";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$main_page_id   = (int)$input_page["id"];
$from           = isset($_REQUEST['from']) ? $_REQUEST['from'] : '';
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';

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

function create_component_checkbox( $main_page_id ) {
    $query             = "SELECT id, title, default_active FROM main_component WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
    $result            = @mysqli_query($GLOBALS['mysql_con'], $query);
    $layout_inclusions = array();
    IF (@mysqli_num_rows($result) > 0) {
        WHILE ($layout_inclusions = @mysqli_fetch_assoc($result)) {
            $query2  = "SELECT active FROM main_page_component_include_link WHERE
					main_component_id = '" . $layout_inclusions["id"] . "' AND
					main_page_id = '" . $main_page_id."'";
            $result2 = mysqli_query($GLOBALS['mysql_con'], $query2);
            $boxname = $layout_inclusions['title'];
            IF (@mysqli_num_rows($result2) == 0) {
                input($boxname, "input_component_" . $layout_inclusions["id"] . "_active", "checkbox", $layout_inclusions["default_active"]);
            } ELSE {
                input($boxname, "input_component_" . $layout_inclusions["id"] . "_active", "checkbox", @mysqli_result($result2, 0));
            }
        }
    }
}

$language_part = 'page';
if($_GET['level_2'] == 'templates') {
    $language_part = 'template';
}

$headline = $translation->get("new_site");
if($input_page["id"] == "") {
    if($_GET['level_2'] == 'templates') {
        $headline = $translation->get("new_template");
    } else {
        $headline = $translation->get("new_site");
    }
} else {
    if($_GET['level_2'] == 'templates') {
        $headline = $translation->get("edit_template");
    } else {
        $headline = $translation->get("edit_site");
    }
}

?>

<div id="overlaycrumb">
    <?php
        echo $headline;
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">

    <?
    if ($from == "page_line_listform") {
        button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)");
    }
    ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_page', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_page', true, '', true)"); ?>
    <?
        if ($input_page["id"] != "" && $_GET['level_2'] !== 'templates') {
            echo button("copy", $translation->get("copy_site"), $formname, "loadCard('copy_page', true)");
        }
    ?>
    <?php
        if($_GET['level_2'] !== 'templates' && $input_page["id"] == "") {
            echo button("copy", $translation->get("from_template"), $formname, "loadCard('select_template_page', true)");
        }
    ?>
    <li>
        <ul>
            <?php
            if ($input_page["id"] != "") {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_page', true, '{$translation->get('delete_' . $language_part . '_confirm')}', true)", "", FALSE);
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
    <input name="input_page_id" type="hidden" value="<?= $input_page["id"] ?>">
    <input name="input_main_layout_id" type="hidden" value="<?= $main_layout_id ?>">
    <input name="from" type="hidden" value="<?= $from ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("description"), "input_page_name", "text", $input_page["title"], 255) ?>
                <? input($translation->get("browsertitle"), "input_browser_title", "text", $input_page["subtitle"], 255) ?>
                <? input($translation->get("active"), "input_active", "checkbox", $input_page["active"]) ?>
                <? input($translation->get("validity_from"), "input_validity_from", "date", datefromsql($input_page["validity_from"]), 10) ?>
                <? input($translation->get("validity_to"), "input_validity_to", "date", datefromsql($input_page["validity_to"]), 10) ?>

            </td>
            <td>

                <? //input($translation->get("is_shopping_world"), "input_shopping_world", "checkbox", $input_page["is_shopping_world"]) ?>
                <? input($translation->get("meta_description"), "input_meta_description", "textarea", $input_page["meta_description"]) ?>
                <? input($translation->get("meta_keywords"), "input_meta_keywords", "textarea", $input_page["meta_keywords"]) ?>
                <? input($translation->get("noindex"), "input_noindex", "checkbox", $input_page["noindex"]) ?>
                <? input($translation->get("nofollow"), "input_nofollow", "checkbox", $input_page["nofollow"]) ?>
                <?
                if($input_page["is_shopping_world"] == 1){
                    $company = $GLOBALS['language']['company'];
                    $shop_code = $GLOBALS['language']['shop_code'];
                    $language_code = $GLOBALS['language']['shop_language_code'];
                    $shop = get_shop($company,$shop_code);
                    $category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;

                    $pdo = get_main_db_pdo_from_env_single_instance();
                    $preselectedLineNo = get_linked_category_line_no_for_page_id($pdo, $input_page["id"]);

                    static $query = '
                        SELECT 
                            hide_category_sub_navigation
                        FROM
                          shop_category
                        WHERE
                          company = :company AND
                          shop_code = :category_shop_code AND 
                          language_code = :language_code AND 
                          line_no = :line_no
                    ';
                    $stmt = $pdo->prepare($query);
                    $stmt->bindValue(':company',$company,PDO::PARAM_STR);
                    $stmt->bindValue(':category_shop_code',$category_shop_code,PDO::PARAM_STR);
                    $stmt->bindValue(':language_code',$language_code,PDO::PARAM_STR);
                    $stmt->bindValue(':line_no',$preselectedLineNo,PDO::PARAM_INT);

                    if (!$stmt->execute()) {
                        $errorInfo = $pdo->errorInfo();
                        $errorString = implode($errorInfo,PHP_EOL);
                        throw new ErrorException('Could not execute prepared statement. Error: ' . $errorString);
                    }

                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $hide_category_sub_navigation = $stmt->fetchColumn(0);

                    echo category_select($pdo,$company,$category_shop_code,$language_code,0,$translation->get('category'),'input_category_line_no',$preselectedLineNo,false);
                    input($translation->get("hide_category_sub_navigation"), "input_hide_category_sub_navigation", "checkbox", $hide_category_sub_navigation);
                }
                ?>
                <?php
                if ($input_page["id"] !== "") {
                    show_changed_on($input_page["id"], "main_page");
                    show_changed_by($input_page["id"], "main_page");
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <h2><?php echo $translation->get("Bausteine"); ?></h2>
                <? create_component_checkbox($main_page_id); ?>
            </td>

            <td>
                <h2><?php echo $translation->get("edit_css_javascript_config"); ?></h2>
                <? create_checkboxes($main_layout_id, $main_page_id, 'js'); ?>
                <? create_checkboxes($main_layout_id, $main_page_id, 'css'); ?>
            </td>
        </tr>
    </table>
</form>