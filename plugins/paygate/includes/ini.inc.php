<?php

// File: ini.inc.php


// !!! security !!! these values are normally extracted from a database.

$MerchantID = "yourmerchantID";       // via e-mail from computop support
$Password   = "yourPassword";         // via phone from computop support


// initialize input.php

$iAmount     = 11;
$sCurrency   = "EUR";
$sURLSuccess = "http://localhost/ctPayGate/ok.php";
$sURLFailure = "http://localhost/ctPayGate/error.php";
$sURLNotify  = "http://localhost/ctPayGate/notify.php";
$sOrderDesc  = "your order";
$sUserData   = "your data";

$sthPayGate  = "PHP - PayGate";
$stdTransID  = "TransID";
$stdAmount   = "Amount";
$stdCurrency = "Currency";
$stdSuccess  = "Success-URL";
$stdFailure  = "Failure-URL";
$stdNotify   = "Notify-URL";
$stdOrder    = "Order description";
$stdUserdata = "Userdata";
$stdInpSend  = "Send Request";


// initialize payment.php

$stdPayType = "PayType";

$defPayType = 1;        // preselected PayType

$sPayType[0] = "Decryption/Check Page";
$sPayType[1] = "Creditcard payment with SET wallet";
$sPayType[2] = "Creditcard payment with 128 bit SSL";
$sPayType[3] = "GeldKarte";
$sPayType[4] = "Mobile payment with PayBox";
$sPayType[5] = "Electronic Direct Debit";
$sPayType[6] = "Notify/Check Page";

$sURL[0] = "decrypt.php";
$sURL[1] = "https://www.netkauf.de/paygate/payset.aspx";
$sURL[2] = "https://www.netkauf.de/paygate/payssl.aspx";
$sURL[3] = "https://www.netkauf.de/paygate/paygks.aspx";
$sURL[4] = "https://www.netkauf.de/paygate/paypaybox.aspx";
$sURL[5] = "https://www.netkauf.de/paygate/payelv.aspx";
$sURL[6] = "notify.php";

$stdInpCheck = " SubmitCheck ";
$checkText   = "Data that is to be transmitted to the CompuTop PayGate: ";
$checkURL    = "https://www.netkauf.de/paygate/encrypt.asp";
$checkLink   = "Check";


// initialize notify.php

$filename   = "./notifylog/urlnotify.log";
  // logfile example - database preferred.


// initialize html.inc.php

$sthInfo = "Information";


// realstatus strings ok.php, notify.php, error.php

$text['nodata'] = "No data found!";
  // No data. Tried to open directly (e.g. with a browser)? (*.php)

$text['paymentfailed'] = "Payment failed!";
  // Payment failed. Correct response. (notify.php, error.php)

$text['paymentsuccessful'] = "Payment successful!";
  // Payment succeeded. Correct response. (notify.php, ok.php)

$text['unknownstatus'] = "Unknown status!";
  // Unknown status (notify.php)

?>
