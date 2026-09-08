<?php
$formname      = "form_gift_package";
$product_id    = $_GET['item'];
$product_count = $_GET['amount'];
$result = get_gift_items();
show_item_list($result, 7, $formname,$columns = 4);
?>