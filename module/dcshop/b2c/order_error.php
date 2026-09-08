<?php
session_id($_GET['UserData']);
session_start();
$query  = "SELECT *
		  FROM shop_payment_option
		  WHERE line_no='" . $_SESSION['payment_line_no'] . "'
		  AND shop_code='" . $GLOBALS['shop']['code'] . "'";
$result = mysqli_query($GLOBALS['mysql_con'], $query);
$terms  = mysqli_fetch_assoc($result);
if ($terms['checkout'] == "4" || $terms['checkout'] == "5") {
    headerFunctionBridge('Location: https://' . $_SERVER["SERVER_NAME"] . '/' . ($_SESSION['is_unique_site'] == 1 ? '' : $_SESSION['site'] . '/') . $_SESSION['language'] . '/order/buy/?payment_error=1');
} else {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>


        <script type="text/javascript">
            window.parent.location.href = 'https://<?=$_SERVER["SERVER_NAME"]?>/<?= ($_SESSION['is_unique_site'] == 1 ? '' : $_SESSION['site']) ?>/<?=$_SESSION['language']?>/order/buy/?payment_error=1';
            //self.close();
        </script>


    </head>
    <body></body>
    </html>
<? } ?>