<?php

//           File: result.php
//    Description: This file explains how to work with an encrypted message transmitted by the CompuTop PayGate
//                 - use 'Notify' for Server2Server communication (The PayGate informs the Shop-Server via 'Notify' about the payment result before URLOk or URLFailure is envoked)
//                 - Possible-Status values: "FAILED", "AUTHORIZED" or "OK"
//                 -                          failed , SET/SSL      or GeldKarte, EDD (German: Lastschrift/ELV)
//                 - TransID - your shop`s payment ID (please use a unique TransID for each payment)
//                 - PaymentID (PayID) - unique CompuTop ID for the payment
//                 - Type - Payment type (SSL, GK, EDD, PayBox...)
// Important note: This page requires the use of "response=encrypt" in the payment request


// required constants

include('./includes/function.inc.php');


// obtain input values (method="GET")

$Data = $HTTP_GET_VARS["Data"];
$Len  = $HTTP_GET_VARS["Len"];


// decrypt the data string

$myPayGate = new ctPayGate;
$plaintext = $myPayGate->ctDecrypt($Data, $Len, $Password);


// prepare information string

$a = ""; $a = split ('&', $plaintext);
$info = $myPayGate->ctSplit($a, '=');
$Status = $myPayGate->ctSplit($a, '=', 'Status');


// check transmitted decrypted status

$realstatus = $myPayGate->ctRealstatus($Status);


// html output

require('./includes/html.inc.php');

?>
