<div class="info_button_recommend sharing">
    <a href="#" data-toggle="modal" data-target="#inlineContent_mail">
        <?= $GLOBALS["tc"]["recommend_per_mail"] ?> <i class="fa fa-envelope" aria-hidden="true"></i>
    </a>
</div>
<script type="text/javascript">
    $('.defaultDOMWindow').openDOMWindow({
                                             eventType      : 'click',
                                             loader         : 1,
                                             height         : 310,
                                             loaderImagePath: 'animationProcessing.gif',
                                             loaderHeight   : 16,
                                             loaderWidth    : 17
                                         });

    function pruefen() {
        document.getElementById('errorbox_name').style.display = "none";
        document.getElementById('errorbox_mail').style.display = "none";
        if (document.form_item_recommendation[0].value == "") {
            document.getElementById('errorbox_name').style.display = "block";
            return false;
        }
        if (document.form_item_recommendation[1].value == "") {
            document.getElementById('errorbox_name').style.display = "block";
            return false;
        }
        if (document.form_item_recommendation[2].value.indexOf("@") == -1) {
            document.getElementById('errorbox_mail').style.display = "block";
            return false;
        }
        return true;
    }
</script>

<? if (empty($_POST["input_recipient_email"])) { ?>
    <div class="modal fade" id="inlineContent_mail" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="modal-headline"><?= $GLOBALS["tc"]["recommend_per_mail"] ?></div>
                    <div class="recommend_item">
                        <div id="errorbox_name"
                             style=" display:none;"><? echo $GLOBALS["tc"]["mail_error_name"]; ?></div>
                        <div id="errorbox_mail"
                             style=" display:none;"><? echo $GLOBALS["tc"]["mail_error_mail"]; ?></div>
                        <? get_modal_infobox($item); ?>
                        <form name="form_item_recommendation" accept-charset="utf-8" method="POST"
                              onsubmit="return pruefen()">
                            <div style="display:none;">
                                <input type="text" name="input_captcha1" id="input_captcha1"
                                       value=""/>
                                <input type="text" name="input_captcha2" id="input_captcha2"
                                       value="<?= time() ?>"/>
                            </div>
                            <div class="label_sender_name">
                                <?
                                input_shop($GLOBALS["tc"]["sender_name"], "input_sender_name", "text", $_POST["input_sender_name"], 30, FALSE, FALSE, "onBlur=\"if(this.value==''){this.style.border='solid 1px red'}else{this.style.border='1px solid #abadb3'}\"");
                                ?>
                            </div>
                            <div class="label_recipient_name">
                                <?
                                input_shop($GLOBALS["tc"]["recipient_name"], "input_recipient_name", "text", $_POST["input_recipient_name"], 30, FALSE, FALSE, "onBlur=\"if(this.value==''){this.style.border='solid 1px red'}else{this.style.border='1px solid #abadb3'}\"");
                                ?>
                            </div>
                            <div class="label_recipient_email">
                                <?
                                input_shop($GLOBALS["tc"]["recipient_email"], "input_recipient_email", "text", $_POST["input_recipient_email"], 50, FALSE, FALSE, "onBlur=\"if(this.value==''){this.style.border='solid 1px red'}else{this.style.border='1px solid #abadb3'}\"");
                                ?>
                            </div>
                            <div class="button_row">
                                <input class="button button_action" name="submit" type="submit"
                                       id="submit_button_rec"
                                       value="<?= $GLOBALS["tc"]["recommend_send"] ?>"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? } elseif (!empty($_POST["input_recipient_email"])) {
    if (($_POST["input_captcha1"] <> "") | ((time() - $_POST["input_captcha2"]) < 10)) {
        $error = TRUE;
        echo "<div class='errorbox'><h3>Spam-Verdacht</h3></div>";
    } else {
        $host           = $_SERVER["HTTP_HOST"];
        $link           = $_SERVER["REQUEST_URI"];
        $url            = "http://" . $host . $link;
        $item_name      = $item["description"];
        $sender_name    = $_POST["input_sender_name"];
        $recipient_name = $_POST["input_recipient_name"];
        $recipient_mail = $_POST["input_recipient_email"];
        $firm           = $GLOBALS["language"]["site_title_name"];            //shop_text_module
        $subject        = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["recommend_mail_text_module"], $spacer, TRUE);    //shop_text_module
        $message        = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["recommend_mail_text_module"], $spacer); //shop_text_module
        $message        = str_replace("%name%", $recipient_name, $message);
        $message        = str_replace("%shipper%", $sender_name, $message);
        $message        = str_replace("%firm%", $firm, $message);
        $message        = str_replace("%item%", $item_name, $message);
        $message        = str_replace("%url%", $url, $message);
        if (mail_create($subject, $message, $GLOBALS["shop"]["email_sender"], $recipient_mail, "", "", TRUE, 0, '')) {
            mail_send();
            $mail_send = TRUE;
        }
    }
    //mail($recipient_mail, $betreff, $nachricht, "From: ".$sendername." <".$sender_mail.">");
    ?>
    <? //<div class="recommend_mail_sended"><p>
    //echo $GLOBALS["tc"]["recommend_sended"]</p></div>?>
<?
} ?>