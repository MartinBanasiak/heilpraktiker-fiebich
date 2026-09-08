<?php
session_id($_GET['UserData']);
session_start();
$_SESSION['pay_id'] = $_GET['PayID'];
$query              = "SELECT *
		  FROM shop_payment_option
		  WHERE line_no='" . $_SESSION['payment_line_no'] . "'
		  AND shop_code='" . $GLOBALS['shop']['code'] . "'
		  AND language_code ='" . $GLOBALS['shop_language']['code'] . "'";
$result             = mysqli_query($GLOBALS['mysql_con'], $query);
$terms              = mysqli_fetch_assoc($result);
if ($terms['checkout'] == "4" || $terms['checkout'] == "5") {
    headerFunctionBridge('Location: https://' . $_SERVER["SERVER_NAME"] . '/' . $_SESSION['site_code'] . '/' . $_SESSION['language_code'] . '/shop/?shop_category=order&action=complete_order');
} else {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>


        <script type="text/javascript">
            parent.location.href = 'https://<?=$_SERVER["SERVER_NAME"]?>/<?=$_SESSION['site_code']?>/<?=$_SESSION['language_code']?>/shop/?shop_category=order&action=complete_order';
            self.close();
        </script>

    </head>
    <body></body>
    </html>
<? } ?>