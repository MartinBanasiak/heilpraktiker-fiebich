<?
if ($_GET["action"] == "send") {
    if (($_POST["input_name"] <> '') && ($_POST["input_reply"] <> '') && ($_POST["input_subject"] <> '')) {
        $message = "<strong>Supportanfrage über das Dc-System von " . $GLOBALS["site"]["name"] . "</strong>\n\n";
        $message .= "Name: " . $_POST["input_name"] . "\n";
        $message .= "E-Mail: " . $_POST["input_reply"] . "\n\n";
        $message .= "<strong>" . $_POST["input_subject"] . "</strong>\n\n";
        $message .= $_POST["input_message"];
        if (mail_create($_POST["input_subject"], $message, "support@dc-solution.de", $GLOBALS['setup']['support_recipient_email'], "Dc-Support", "dynamic commerce Support", TRUE, 0, '')) {
            mail_send();
            echo "<div class=\"successbox\">Ihre Supportanfrage wurde angenommen</div>";
            $_POST["input_name"]    = "";
            $_POST["input_reply"]   = "";
            $_POST["input_subject"] = "";
            $_POST["input_message"] = "";
        } else {
            echo "<div class=\"errorbox\">Die E-Mail konnte nicht versendet werden/div>";
        }
    } else {
        echo "<div class=\"errorbox\">Bitte f&uuml;llen Sie die Felder Name, Antwort-Adresse und Betreff aus</div>";
    }
} else {
    $_POST["input_name"]  = $admin_user["name"];
    $_POST["input_reply"] = $admin_user["email"];
}

?>

<? $formname = "form_contact_formular"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">

    <div class="toolbar">
        <?= button("mail", "Senden", $formname, "?action=send"); ?>
        <?= button("reset", "Zurücksetzen", $formname); ?>
    </div>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <?= input("Benutzer", "input_name", "text", $_POST["input_name"], 45) ?>
                <?= input("Antwort-Adresse", "input_reply", "text", $_POST["input_reply"], 45) ?><br />
                <?= input("Betreff", "input_subject", "text", $_POST["input_subject"], 250) ?>
                <?= input("Nachricht", "input_message", "textarea", $_POST["input_message"], 250) ?>
            </td>
        </tr>
    </table>

</form>