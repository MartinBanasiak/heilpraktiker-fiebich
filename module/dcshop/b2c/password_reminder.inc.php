<?
if ($_GET["send" . $sitepart["navigation_has_sitepart_id"]] == TRUE) {
    if (password_reminder()) {
        get_requestbox($GLOBALS["tc"]["password_success"], "", "success");
    } else {
        //get_requestbox($GLOBALS["tc"]["password_error"]);
        get_requestbox($GLOBALS["tc"]["password_success"], "", "success");
    }

}
?>

<? $formname = "form_password_reminder"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="<?= ml($sitepart, "send", "TRUE") ?>">
            <?
                create_password_reminder_html();
            ?>
            <br/>
                <input type="submit" name="button" class="password_button button" id="button"
                       value="<?= $GLOBALS["tc"]["request_login"] ?>">
</form>