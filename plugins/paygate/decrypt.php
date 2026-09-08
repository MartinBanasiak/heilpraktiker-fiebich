<?php

//   File        : decrypt.php
//   Description : decrypting utility for internal test use

// required constants

include('./includes/function.inc.php');
$filetitle = "decrypt.php";

// obtain input values (for form method="POST")

$Data = $HTTP_POST_VARS["Data"];
$Len  = $HTTP_POST_VARS["Len"];
$sURL = $HTTP_POST_VARS["PayType"];


// decrypt the data string

$myPayGate = new ctPayGate;
$plaintext = $myPayGate->ctDecrypt($Data, $Len, $Password);


// prepare information string

$a = ""; $a = split ('&', $plaintext);
$info = $myPayGate->ctSplit($a, '=');


// html output

$realstatus = $sPayType[0];
require('./includes/html.inc.php');

?>
