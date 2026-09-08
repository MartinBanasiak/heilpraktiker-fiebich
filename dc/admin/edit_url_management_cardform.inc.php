<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_url_management_card";
$inputname   = "input_id";

static $rewrite_codes = [
    '301',
    '302',
];
?>

<div id="overlaycrumb">
    <?php if ($input_url_management["id"] == "") {
        echo $translation->get("new_url");
    } else {
        echo sprintf($translation->get("edit_url"), $input_url_management["name"]);
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>


<ul class="toolbar_menu">
    <?= button("save", $translation->get("save"), $formname, "loadCard('save', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save', true, '', true)"); ?>
    <li>
        <ul>
            <?php
            if ($input_url_management["id"] != "") {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete', true, '{$translation->get('delete_url_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>


<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
} else {
    echo '<div id="overlayMessages"></div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_url_management["id"] ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("old_url"), "input_old_url", "text", $input_url_management["old_url"], 255) ?>
                <? input($translation->get("active"), "input_active", "checkbox", $input_url_management["active"]) ?>
            </td>
            <td>
                <? input($translation->get("new_url"), "input_new_url", "text", $input_url_management["new_url"], 255) ?>
                <? input_select($translation->get('rewrite_code'),'input_rewrite_code',$rewrite_codes,$rewrite_codes,$input_url_management["rewrite_code"])?>
            </td>
        </tr>
    </table>
</form>