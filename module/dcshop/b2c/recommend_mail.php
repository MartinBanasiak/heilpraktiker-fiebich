<?
$host           = $_SERVER["HTTP_HOST"];
$link           = $_SERVER["REQUEST_URI"];
$url            = "http://" . $host . $link;
$item_name      = $item["description"];
$sender_name    = $_POST["input_sender_name"];
$recipient_name = $_POST["input_recipient_name"];
$recipient_mail = $_POST["input_recipient_email"];
$firma          = $GLOBALS["language"]["site_name"];            //shop_text_module
$betreff        = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["recommend_mail_text_module"], $spacer, TRUE);    //shop_text_module
$sender_mail    = $GLOBALS["shop"]["email_sender"];        //shop_shop
$nachricht      = get_text_module($GLOBALS['shop']['company'], $GLOBALS["shop_language"]["recommend_mail_text_module"], $spacer); //shop_text_module
$nachricht      = str_replace("%name%", $recipient_name, $nachricht);
$nachricht      = str_replace("%shipper%", $sender_name, $nachricht);
$nachricht      = str_replace("%firm%", $firma, $nachricht);
$nachricht      = str_replace("%item%", $item_name, $nachricht);

mail($recipient_mail, $betreff, $nachricht, $GLOBALS["tc"]["from"] . ": $sendername <$sender_mail>");
?>

<div class="recommend_mail_sended"><p><?
        echo $GLOBALS["tc"]["recommend_sended"]; ?></p></div>