<?php
$translation         = \DynCom\dc\common\classes\Registry::get("translation");
$formname            = "form_field_textcontent_list_" . $fieldSetup['id'];
$inputname           = "input_id";
$input_page_id       = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");
$input_collection_id = (isset($_REQUEST["input_collection_id"]) ? $_REQUEST["input_collection_id"] : "");
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />
    <input type="hidden" name="input_collection_id" value="<?php echo $input_collection_id; ?>" />
    <input type="hidden" name="collection_modul_input_<?php echo $fieldSetup['id']; ?>" value="textcontent" />

    <table style="height:550px;" class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2">
                <?php
                show_ck_editor($headerData['content'], "dc", 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/dist/css/fck.css', 'layout/frontend/' . $GLOBALS["layout"]["code"] . '/fckstyles.xml', "module_input_textcontent_text_" . $fieldSetup['id']);
                ?>
            </td>
        </tr>
    </table>
</form>