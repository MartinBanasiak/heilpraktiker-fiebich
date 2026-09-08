<? $formname = "form_main_setup_card"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_setup["id"] ?>">
    <?
    $disabled = TRUE;
    if ($GLOBALS['admin_user']['is_super_user'] == 1) {
        $disabled = FALSE;
        ?>
        <div class="toolbar">
            <?= button("save", "Speichern", $formname, "?action=save"); ?>
            <?= button("reset", "Zurücksetzen", $formname); ?>
        </div>
    <? } ?>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input("Admin Browser-Titel", "input_admin_name", "text", $input_setup["admin_name"], 45, $disabled) ?>
                <? input("Admin Webadresse", "input_admin_link", "text", $input_setup["admin_link"], 45, $disabled) ?>
                <? input("Empfänger Support-EMail", "input_support_recipient_email", "text", $input_setup["support_recipient_email"], 45, $disabled) ?>
                <? if (!$disabled) { ?>
                    <? site_select("Hauptwebseite", "input_std_main_site_id", $input_setup["std_main_site_id"], TRUE) ?>
                <? } ?>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>

</form>