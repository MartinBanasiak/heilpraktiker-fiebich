<?php
$translation             = \DynCom\dc\common\classes\Registry::get("translation");
$formname                = "form_shop_item_preview_card";
$input_page_id           = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id      = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_shop_item_preview = $item_preview_sitepart;
?>

<div id="overlaycrumb">
    <?php if (empty($input_shop_item_preview["id"])) {
        echo $translation->get("new_shop_item_preview");
    } else {
        echo $translation->get("edit_shop_item_preview");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_shop_item_preview', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_shop_item_preview', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_shop_item_preview["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_shop_item_preview', true, '{$translation->get('delete_shop_item_preview_confirm')}', true)", "", FALSE);
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

$input_description = (empty($input_shop_item_preview['description']) ? $translation->get('webshop_item_preview') : $input_shop_item_preview['description']);
$include_sub_categories = 'on';
if((bool)$input_shop_item_preview['include_sub_categories']) {
    $checked = ' checked="checked"';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_shop_item_preview["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="14">
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
                <div class='label'>
                    <?= $translation->get('no_of_items') ?>
                </div>
                <div class='input'>
                    <input type="number" name="input_no_of_items" id="input_no_of_items" class="text"
                           value="<?= $input_shop_item_preview['no_of_items'] ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'>
                    <?= $translation->get('category_code_string') ?>
                </div>
                <div class='input'>
                    <input type="text" name="input_category_code_string" class="text" id="input_category_code_string"
                           value="<?= htmlspecialchars($input_shop_item_preview['category_code_string']) ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'>
                    <?= $translation->get('include_sub_categories') ?>
                </div>
                <div class='input'>
                    <input type="checkbox" name="input_include_sub_categories" class="checkbox" id="input_include_sub_categories"<?= $checked ?>
                    value="<?= $include_sub_categories ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='label'>
                    <?= $translation->get('item_no_string') ?>
                </div>
                <div class='input'>
                    <input type="text" name="input_item_no_string" class="text" id="input_item_no_string"
                           value="<?= htmlspecialchars($input_shop_item_preview['item_no_string']) ?>" />
                </div>
            </td>
        </tr>
    </table>
</form>