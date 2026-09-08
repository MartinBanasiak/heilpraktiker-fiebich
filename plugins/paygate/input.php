<?php

// required constants

include('./includes/ini.inc.php');

mt_srand((double)microtime()*1000000);
$sTransID  = (string)mt_rand();
$sTransID .= date("yzGis");

?>

<html>
 <head>
  <title>input.php</title>
 </head>
<body>
 <form method=post action="payment.php">
  <table border=0 cellspacing=0 cellpadding=5>
   <tr><th colspan=2>  <?php echo $sthPayGate; ?>  </th></tr>
   <tr><td align=right><?php echo $stdTransID; ?>  </td><td><input type=text name=TransID    value="<?php echo $sTransID; ?>"    size=30></td></tr>
   <tr><td align=right><?php echo $stdAmount; ?>   </td><td><input type=text name=Amount     value="<?php echo $iAmount; ?>"     size=10></td></tr>
   <tr><td align=right><?php echo $stdCurrency; ?> </td><td><input type=text name=Currency   value="<?php echo $sCurrency; ?>"   size=3> </td></tr>
   <tr><td align=right><?php echo $stdSuccess; ?>  </td><td><input type=text name=URLSuccess value="<?php echo $sURLSuccess; ?>" size=50></td></tr>
   <tr><td align=right><?php echo $stdFailure; ?>  </td><td><input type=text name=URLFailure value="<?php echo $sURLFailure; ?>" size=50></td></tr>
   <tr><td align=right><?php echo $stdNotify; ?>   </td><td><input type=text name=URLNotify  value="<?php echo $sURLNotify; ?>"  size=50></td></tr>
   <tr><td align=right><?php echo $stdOrder; ?>    </td><td><input type=text name=OrderDesc  value="<?php echo $sOrderDesc; ?>"  size=50></td></tr>
   <tr><td align=right><?php echo $stdUserdata; ?> </td><td><input type=text name=UserData   value="<?php echo $sUserData; ?>"   size=50></td></tr>
   <tr><td colspan=2>&nbsp;</td></tr>
   <tr><td>&nbsp; </td><td><input type=submit value=" <?php echo $stdInpSend; ?> "></td></tr>
  </table>
 </form>
</body>
</html>
