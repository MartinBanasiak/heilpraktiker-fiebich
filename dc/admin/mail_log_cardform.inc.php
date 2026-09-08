<?
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_mail_log_card";
?>

<div id="overlaycrumb">
    <?php
    echo $translation->get("mail_details");
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("mail", $translation->get("send_again"), $formname, "loadCard('send', true)"); ?>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_mail_log["id"] ?>">
    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <?= input($translation->get("recipient_name"), "input_to_name", "text", $input_mail_log["to_name"], 45, TRUE) ?>
                <?= input($translation->get("recipient_email"), "input_to_adress", "text", $input_mail_log["to_adress"], 100, TRUE) ?>
                <?= input($translation->get("sender_name"), "input_from_name", "text", $input_mail_log["from_name"], 45, TRUE) ?>
                <?= input($translation->get("sender_email"), "input_from_adress", "text", $input_mail_log["from_adress"], 100, TRUE) ?>
                <br />
                <?= input($translation->get("subject"), "input_subject", "text", $input_mail_log["subject"], 100, TRUE) ?>
            </td>
            <td>
                <?= input($translation->get("send_date"), "input_send_date", "date", $input_mail_log["format_send_date"], 45, TRUE) ?>
                <?= input($translation->get("send_time"), "input_send_time", "date", $input_mail_log["format_send_time"], 45, TRUE) ?>
            </td>
        </tr>
    </table>

    <h2><?php echo $translation->get("message"); ?>:</h2>
    <?= ($input_mail_log["html_mail"]) ? $input_mail_log["message"] : nl2br($input_mail_log["message"]) ?>

</form>