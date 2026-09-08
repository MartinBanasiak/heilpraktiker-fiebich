<?php
$translation    = \DynCom\dc\common\classes\Registry::get("translation");
$formname       = "form_page_line_group";
$main_layout_id = $GLOBALS["language"]["main_layout_id"];
$input_page_id  = $_REQUEST["input_page_id"];
?>

<script type="text/javascript">
    function save_and_close_page_group() {
        jQuery('#save_and_close', jQuery('#<?php echo $formname; ?>')).val("1");
        loadCard('save_group_page', true);
    }
</script>

<div id="overlaycrumb">
    <?php
    if ($input_line["id"] == "") {
        echo $translation->get("new_group");
    } else {
        echo $translation->get("edit_group");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("left", $translation->get("overview"), $formname, "loadCard('edit_content_page', true)"); ?>
    <?= button("save", $translation->get("save"), $formname, "loadCard('save_group_page', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "save_and_close_page_group();"); ?>
    <?= button("reset", $translation->get("restore"), $formname, "?action=edit"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_page_id" type="hidden" value="<?= $input_page_id ?>">
    <input name="input_id" type="hidden" value="<?= $input_line["id"] ?>">
    <input name="save_and_close" id="save_and_close" type="hidden" value="0" />

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <?
                $groupcodes = array(
                    "groupcode" => "",
                    "row flexrow" => "Row",
                    "container" => "Container",
                    "box--white" => "Box weiß"
                );
                ?>
                <? input_select($translation->get("textkey"), "input_main_page_group_code", array_keys($groupcodes), array_values($groupcodes), $input_line["main_page_group_code"]) ?>

                <? //input($translation->get("textkey"), "input_main_page_group_code", "code", $input_line["main_page_group_code"], 20) ?>
                <? input($translation->get("description"), "input_main_page_group_name", "text", $input_line["main_page_group_name"], 255) ?>
                <? input($translation->get("link"), "input_main_page_group_link", "text", $input_line["main_page_group_link"], 255) ?>
            </td>
        </tr>
    </table>

</form>