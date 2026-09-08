<?
if ($_GET["send" . $sitepart["navigation_has_sitepart_id"]] == TRUE) {
    if (password_reminder()) {
        $_SESSION['password_reminder_success'] = true;
        //get_requestbox($GLOBALS["tc"]["password_success"],$GLOBALS["tc"]["password_success_title"],'success');
    } else {
       // $_SESSION['password_reminder_success'] = false;
        $_SESSION['password_reminder_success'] = true;
        //get_requestbox($GLOBALS["tc"]["password_error"],$GLOBALS["tc"]["password_error_title"],'error');
    }
}
?>
<? $formname = "form_password_reminder"; ?>
<div>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action="<?= ml($sitepart, "send".$sitepart["navigation_has_sitepart_id"], "TRUE") ?>">
        <input type="hidden" name="show_pw_reminder" value="1"/>

        <? create_password_reminder_html(); ?>
        <br>

        <div class="submit_button">
            <input type="submit" class="button" value="<?= $GLOBALS["tc"]["request_login"] ?>">
        </div>

    </form>
</div>