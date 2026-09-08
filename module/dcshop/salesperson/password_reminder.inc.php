<?
if ($_GET["send" . $sitepart["navigation_has_sitepart_id"]] == TRUE) {

    if (password_reminder()) {
        echo "<br /><h3>" . $GLOBALS["tc"]["password_success"] . "</h3><br />";
    } else {
       // echo "<br /><h3>" . $GLOBALS["tc"]["password_error"] . "</h3><br />";
        echo "<br /><h3>" . $GLOBALS["tc"]["password_success"] . "</h3><br />";

    }
}
?>
<? $formname = "form_password_reminder"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="<?= ml($sitepart, "send", "TRUE") ?>">
    <? input($GLOBALS["tc"]["email"], "input_email", "text", $_POST["input_email"], 50) ?>
    <div class="spacer_6"></div>
    <div class="button"><input type="submit" name="button" class="password_button" id="button"
                               value="<?= $GLOBALS["tc"]["request_login"] ?>"></div>
</form>