<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_field_shop_item_preview_list" . $fieldSetup['id'];
$inputname           = "input_id";
$input_page_id       = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_modul_input_<?php echo $fieldSetup['id']; ?>" value="shop_item_preview" />


    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class='label'><label for="module_input_description_<?= $fieldSetup['id'] ?>"><?= $translation->get('description') ?></label></div>
                <div class='input'><input type="text" name="module_input_description_<?= $fieldSetup['id'] ?>" id="module_input_description_<?= $fieldSetup['id'] ?>"
                                          value="<?= $headerData["description"] ?>" class="text"></div>
            </td>

            <td>
                <div class='label'>
                    <?= $translation->get('no_of_items') ?>
                </div>
                <div class='input'>
                    <input type="number" name="module_input_no_of_items_<?= $fieldSetup['id'] ?>" id="module_input_no_of_items_<?= $fieldSetup['id'] ?>" class="text"
                           value="<?= $headerData['no_of_items'] ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'>
                    <?= $translation->get('category_code_string') ?>
                </div>
                <div class='input'>
                    <input type="text" name="module_input_category_code_string_<?= $fieldSetup['id'] ?>" class="text" id="module_input_category_code_string_<?= $fieldSetup['id'] ?>"
                           value="<?= htmlspecialchars($headerData['category_code_string']) ?>" />
                </div>
            </td>

            <td>
                <div class='label'>
                    <?= $translation->get('item_no_string') ?>
                </div>
                <div class='input'>
                    <input type="text" name="module_input_item_no_string_<?= $fieldSetup['id'] ?>" class="text" id="module_input_item_no_string_<?= $fieldSetup['id'] ?>"
                           value="<?= htmlspecialchars($headerData['item_no_string']) ?>" />
                </div>
            </td>
        </tr>
    </table>
</form>