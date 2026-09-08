<?php
$translation        = \DynCom\dc\common\classes\Registry::get("translation");
$formname           = "form_shop_main_card";
$input_page_id      = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_component_id = (isset($_REQUEST["input_component_id"]) ? $_REQUEST["input_component_id"] : "");
$input_shop_main    = $shop_sitepart;
?>

<div id="overlaycrumb">
    <?php if ($input_shop_main["id"] == "") {
        echo $translation->get("new_shop_main");
    } else {
        echo $translation->get("edit_shop_main");
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

    <?= button("save", $translation->get("save"), $formname, "loadCard('save_shop_main', true)"); ?>


    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save_shop_main', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_shop_main["id"] != "" && is_page_edit() === FALSE && is_component_edit() === FALSE) {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_shop_main', true, '{$translation->get('delete_shop_main_confirm')}', true)", "", FALSE);
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

//Arrays für Select füllen
$type_array      = array(0, 1, 2, 3, 4, 5, 6, 7);
$type_name_array = array(
    0 => $translation->get('webshop'),
    1 => $translation->get('webshop_basket'),
    2 => $translation->get('webshop_search'),
    3 => $translation->get('webshop_direct_order'),
    4 => $translation->get('webshop_account_header'),
    5 => $translation->get('webshop_password_reminder'),
    6 => $translation->get('webshop_sales_person'),
    7 => $translation->get('webshop_new_password_request'),
);

$input_description = (empty($input_shop_main['description']) ? $type_name_array[$input_shop_main['type']] : $input_shop_main['description']);

?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_shop_main["id"] ?>">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input type="hidden" name="input_component_id" value="<?php echo $input_component_id; ?>" />
    <input name="data-sitepartid" type="hidden" value="12">
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
                <? input_select($translation->get('content_type'), 'input_type', $type_array, $type_name_array, $input_shop_main['type']); ?>
            </td>
        </tr>
    </table>
</form>