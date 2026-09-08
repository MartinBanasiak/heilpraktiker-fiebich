<?php
$translation             = \DynCom\dc\common\classes\Registry::get("translation");
$formname                = "form_shop_category_preview_card";
$input_page_id           = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id      = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_shop_category_preview = $category_preview_sitepart;
$rootDir = rtrim(dirname(dirname(dirname(dirname(__DIR__)))),'/\\');
require_once $rootDir. DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'shop_functions.inc.php';

?>

<div id="overlaycrumb">
    <?php if (empty($input_shop_category_preview["id"])) {
        echo $translation->get("new_shop_category_preview");
    } else {
        echo $translation->get("edit_shop_category_preview");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?php
    if (is_page_edit() || is_component_edit()) {
        button("left", $translation->get("back"), $formname, "loadCard('edit_content_page', true)");
    }
    ?>

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_shop_category_preview', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_shop_category_preview', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_shop_category_preview["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_shop_category_preview', true, '{$translation->get('delete_shop_category_preview_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit_iframe", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}

$input_description = (empty($input_shop_category_preview['description']) ? $translation->get('webshop_category_preview') : $input_shop_category_preview['description']);
$company = $GLOBALS['language']['company'];
$shop_code = $GLOBALS['language']['shop_code'];
$language_code = $GLOBALS['language']['shop_language_code'];
$shop = get_shop($company,$shop_code);
$category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_shop_category_preview["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="19">
    <input type="hidden" name="get_real_page_link_id" value="1" />
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class='label'><label for="input_description"><?= $translation->get('description') ?></label></div>
                <div class='input'><input type="text" name="input_description" id="input_description"
                                          value="<?= $input_description ?>" class="text"></div>
            </td>
        </tr>
        <tr>
            <td>
                <?
                $pdo = get_main_db_pdo_from_env_single_instance();
                $page = get_page_by_id($pdo,$input_page_id);
                if (
                        (!is_array($input_shop_category_preview) || !array_key_exists('id',$input_shop_category_preview) || !((int)$input_shop_category_preview["id"] > 0)) &&
                        (is_array($page) && array_key_exists('is_shopping_world',$page) && (bool)$page['is_shopping_world'])
                ) {
                    $preselectedLineNo = get_linked_category_line_no_for_page_id($pdo, $input_page_id);
                } else {
                    $preselectedLineNo = (int)$input_shop_category_preview["category_line_no"];
                }

                echo category_select($pdo,$company,$category_shop_code,$language_code,0,$translation->get('show_subcategories_for'),'input_category_line_no',(int)$preselectedLineNo,false);
                ?>
            </td>
        </tr>
    </table>
</form>