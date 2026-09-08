<? $formname = "form_order_history_list"; ?>
<div class="category_info">
    <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["order_history"] ?></h1>
    <?=$GLOBALS['tc']['shop_account_order_history']?>
</div>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post"
      action="<?= '/' . customizeUrl() . '/?action=order_history&action_id=card' ?>">
    <? /*<div class="toolbar">
  <?= button("edit",$GLOBALS["tc"]["show_order"],$formname,"?shop_category=account&action=order_history&action_id=show"); ?>
</div> */ ?>
    <?
    $query = "SELECT
			id, 
			order_no AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_no"]) . "',
			DATE_FORMAT(order_date,'%d.%m.%Y') AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["order_date"]) . "',
			user_name AS '" . mysqli_real_escape_string($GLOBALS['mysql_con'], $GLOBALS["tc"]["name"]) . "',
			total AS '" . $GLOBALS["tc"]["total_amount"] . "'
		  FROM shop_sales_header
		  WHERE shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
		  	AND order_date >= DATE_SUB( NOW( ) , INTERVAL 1 YEAR ) 	
		  	AND company = '".$GLOBALS["shop"]["company"]."'
		  	AND order_error = 0
		  ORDER BY order_date DESC";
    /*AND ((process_payment != '0'
      AND payment_processed IS NULL)
      OR (process_payment = '0'
      AND payment_processed IS NOT NULL))*/

    if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
        $format = array("option", "text", "text", "text", "euro");
        linklist($result, $formname, $format, 'input_id', 'show');
        ?>
        <script type="text/javascript">

            $('div.account table.linklist tr.linklist_content').each(function () {
                $(this).on('dblclick', function () {
                    var inputId = $(this).data('inputid'),
                        listForm = $(this).parents('form'),
                        formAction = $(listForm).attr('action'),
                        newFormAction = '';
                    alert(inputId);
                    newFormAction = formAction + '&input_id=' + inputId;
                    window.location.href = newFormAction;
                });
            });
        </script>
        <?
        //$result,$formname = "linklist" ,$format = NULL, $inputname = "input_id", $dblclick_action="edit", $sortable = false, $sortableUpdateAction = 'update_sortorder'
    }
    ?>
</form>