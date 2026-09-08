<?php

// required constants

include('/includes/function.inc.php');


// read data (for form method="POST")

//$TransID    = $HTTP_POST_VARS["TransID"];
//$Amount     = $HTTP_POST_VARS["Amount"];
//$Currency   = $HTTP_POST_VARS["Currency"];
//$OrderDesc  = $HTTP_POST_VARS["OrderDesc"];
//$URLSuccess = $HTTP_POST_VARS["URLSuccess"];
//$URLFailure = $HTTP_POST_VARS["URLFailure"];
//$URLNotify  = $HTTP_POST_VARS["URLNotify"];
//$UserData   = $HTTP_POST_VARS["UserData"];


// optional values

//$Response = "&Response=encrypt";
//$Currency = trim($Currency);

//if($Currency == ""){
//    $Currency = "&Currency=$sCurrency";
//
//} else {
//    $Currency = "&Currency=".$Currency;
//}
//
//$UserData = "&UserData=".$UserData;
//$Capture = "&Capture=AUTO";


// format data which is to be transmitted - required

//$TransID    = "&TransID="     .$TransID;
//$Amount     = "&Amount="      .$Amount;
//$URLSuccess = "&URLSuccess="  .$URLSuccess;
//$URLFailure = "&URLFailure="  .$URLFailure;
//$OrderDesc  = "&OrderDesc="   .$OrderDesc;
//$URLNotify  = "&URLNotify="   .$URLNotify;


// building the string MerchantID, Len and Data (encrypted)

//$plaintext  = "MerchantID=".$MerchantID.$TransID.$Amount.$Currency.$URLSuccess.$URLFailure.$URLNotify.$UserData.$OrderDesc.$Response.$Capture;
//$Len        = strlen($plaintext);  // Length of the plain text string


// encryption

//$BlowFish = new ctBlowfish;
//$Data = $BlowFish->ctEncrypt($plaintext, $Len, $Password);


// prepare javascript array

//$jsURL = implode(';', $sURL);

?>

<html>
 <head>
  <title>payment.php</title>
  <script language="JavaScript">
  
   function pay_submit(arrURL){

       sURL = arrURL.split(";");
       
       for(var i = 0; i < sURL.length; i++){
           
           if(document.ctForm.PayType.options[document.ctForm.PayType.selectedIndex].value==i){
               document.ctForm.action=sURL[i];
           }
       }
       
       document.ctForm.submit();
   }
   
  </script>
 </head>
<body>
 <form method=post action=decrypt.php name=ctForm>
  <input type=hidden name=MerchantID value="<?php echo $MerchantID; ?>">
  <input type=hidden name=Data       value="<?php echo $Data; ?>">
  <input type=hidden name=Len        value="<?php echo $Len; ?>">
  <table border=0 cellspacing=0 cellpadding=5>
   <tr>
    <td><?php echo $stdPayType ?></td>
    <td>
     <select name=PayType>
     
     <?php
          $i = 0;
          
          foreach($sPayType as $selPayType){

              if($i == $defPayType){
                  echo "<option value=\"$i\" selected>$selPayType</option>";
                  
              } else {
                  echo "<option value=\"$i\">$selPayType</option>";
              }
              
              $i++;
          }
     ?>

     </select>
    </td>
   </tr>
   <tr><td colspan=2>&nbsp;</td></tr>
   <tr><td colspan=2><input type=button onclick=pay_submit('<?php echo $jsURL; ?>'); value="<?php echo $stdInpCheck; ?>"></td></tr>
  </table>
 </form><p>
 <?php echo $checkText; ?><a href=<?php echo "$checkURL?MerchantID=$MerchantID&Len=$Len&Data=$Data"; ?>><?php echo $checkLink; ?></a>
</body>
</html>
