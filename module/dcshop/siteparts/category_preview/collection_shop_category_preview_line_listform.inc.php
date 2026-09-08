<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_field_shop_category_preview_list" . $fieldSetup['id'];
$inputname           = "input_id";
$input_page_id       = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
$company = $GLOBALS['language']['company'];
$shop_code = $GLOBALS['language']['shop_code'];
$language_code = $GLOBALS['language']['shop_language_code'];
$shop = get_shop($company,$shop_code);
$category_shop_code = !empty($shop['use_categorys_from_shop_code']) ? $shop['use_categorys_from_shop_code'] : $shop_code;
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_modul_input_<?php echo $fieldSetup['id']; ?>" value="shop_category_preview" />


    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class='label'><label for="module_input_description_<?= $fieldSetup['id'] ?>"><?= $translation->get('description') ?></label></div>
                <div class='input'><input type="text" name="module_input_description_<?= $fieldSetup['id'] ?>" id="module_input_description_<?= $fieldSetup['id'] ?>"
                                          value="<?= $headerData["description"] ?>" class="text"></div>
            </td>
            <td>
                <?
                $pdo = get_main_db_pdo_from_env_single_instance();
                echo category_select($pdo,$company,$category_shop_code,$language_code,0,'input_category_line_no',$translation->get('show_subcategories_for'),(int)$headerData['category_line_no'],false);
                ?>
            </td>
        </tr>
    </table>
</form>