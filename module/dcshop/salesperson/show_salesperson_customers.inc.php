<? $formname = 'shop_customer_select' ?>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <?
        $query = "SELECT id,customer_no AS '" . $GLOBALS['tc']['customer_no'] . "',name as '" . $GLOBALS['tc']['name'] . "',address AS '" . $GLOBALS['tc']['address'] . "', post_code AS '" . $GLOBALS['tc']['post_code'] . "',city as '" . $GLOBALS['tc']['city'] . "',email AS '" . $GLOBALS['tc']['city'] . "'
		 FROM shop_customer
		 WHERE 
			salesperson_code = '" . $GLOBALS['shop_user']['salesperson_code'] . "'
		  AND 
			company = '" . $GLOBALS['shop']['company'] . "'
		 ORDER BY customer_no ASC";

        if ($result = mysqli_query($GLOBALS['mysql_con'], $query)) {
            ?>
            <input type="hidden" name="action_id" value="set" />
            <div id="customer_list_button_wrapper">
                <div id="customer_list_button" onClick="document.forms['<?= $formname ?>'].submit()"></div>
            </div>
            <?
            show_customer_select_list($result, $formname);
            //linklist($result,$formname);
        }
        ?>
    </form>
<?

?>