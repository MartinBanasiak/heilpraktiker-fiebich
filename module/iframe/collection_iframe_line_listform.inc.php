<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_field_iframe_list" . $fieldSetup['id'];
$inputname           = "input_id";
$input_page_id       = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_modul_input_<?php echo $fieldSetup['id']; ?>" value="iframe" />

    <?php
    if (!is_array($headerData)) {
        $width  = 450;
        $height = 300;
    } else {
        $width  = $headerData["width"];
        $height = $headerData["height"];
    }
    ?>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <?php input($translation->get("element_width"), "module_input_iframe_width_" . $fieldSetup['id'], "code", $width) ?>
            </td>
            <td>
                <? input($translation->get("iframe_page_url"), "module_input_iframe_url_" . $fieldSetup['id'], "text", $headerData["url"], 255) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php input($translation->get("element_height"), "module_input_iframe_height_" . $fieldSetup['id'], "code", $height) ?>
            </td>
            <td>

            </td>
        </tr>
    </table>
</form>