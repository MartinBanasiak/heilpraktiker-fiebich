<?php

function order_table($result)
{
    ?>

    <div id="order_table" class="linklist table_area">
        <div class="table_row table_header">
            <div class="table_cell"><?= $GLOBALS["tc"]["order_no"] ?></div>
            <div class="table_cell"><?= $GLOBALS["tc"]["contract_no"] ?></div>
            <div class="table_cell"><?= $GLOBALS["tc"]["order_date"] ?></div>
            <div class="table_cell"><?= $GLOBALS["tc"]["username"] ?></div>
            <div class="table_cell"><?= $GLOBALS["tc"]["your_reference"] ?></div>
            <div class="table_cell"><?= $GLOBALS["tc"]["total_amount"] ?></div>
        </div>
        <?php
        foreach ($result as $key => $val) {
            if (count($val) > 1) {
                ?>
                <div id="row_<?= $key ?>" data-order_id="<?= $val[0]['id'] ?>" class='clickable-row table_row table_body'
                    data-href='?action=contract_history&action_id=card'>
                    <div class="clickable table_cell" data-toggle="collapse" data-target=".row_<?= $key ?>"><i id="viewOrder"
                                                                                                   class="glyphicon glyphicon-plus"></i><? if ($val[0]['order_no'] != 0) {
                            echo $val[0]['order_no'];
                        } ?></div>
                    <div class="table_cell"><?= $val[0]['sales_no'] ?></div>
                    <div class="table_cell"><?= $val[0]['order_date'] ?></div>
                    <div class="table_cell"><?= $val[0]['user_name'] ?></div>
                    <div class="table_cell"><?= $val[0]['your_reference'] ?></div>
                    <div class="table_cell"><?= format_amount($val[0]['total'], FALSE) ?></div>
                </div>
                <?

                for ($i = 1; $i < count($val); $i++) {
                    ?>

                    <div class='table_row table_body clickable-row collapse row_<?= $key ?>' data-order_id="<?= $val[$i]['id'] ?>"
                        data-href='?action=order_history&action_id=card'
                        id="row_<?= $key . '_' . $i ?>">
                        <div class="table_cell">&nbsp;</div>
                        <div class="table_cell"><?= $val[$i]['sales_no'] ?></div>
                        <div class="table_cell"><?= $val[$i]['order_date'] ?></div>
                        <div class="table_cell"><?= $val[$i]['user_name'] ?></div>
                        <div class="table_cell"><?= $val[$i]['your_reference'] ?></div>
                        <div class="table_cell"><?= format_amount($val[$i]['total'], FALSE) ?></div>
                    </div>

                    <?
                }

            } else {
                ?>
                <div id="row_<?= $key ?>" class='table_row table_body clickable-row'
                    data-order_id="<?= $val[0]['id'] ?>" <? if ($val[0]['order_type'] == 0) {
                    echo " data-href='?action=contract_history&action_id=card' ";
                } else {
                    echo " data-href='?action=order_history&action_id=card' ";
                } ?> >
                    <div class="table_cell"><? if ($val[0]['order_no'] != 0) {
                            echo $val[0]['order_no'];
                        }else{echo "&nbsp;";} ?></div>
                    <div class="table_cell"><?= $val[0]['sales_no'] ?></div>
                    <div class="table_cell"><?= $val[0]['order_date'] ?></div>
                    <div class="table_cell"><?= $val[0]['user_name'] ?></div>
                    <div class="table_cell"><?= $val[0]['your_reference'] ?></div>
                    <div class="table_cell"><?= format_amount($val[0]['total'], FALSE) ?></div>
                </div>
                <?
            }
        }

        ?>
    </div>

    <?
}

?>

<h1 class="shop_site_headline"><?= $GLOBALS["tc"]["orders"] ?></h1>
<? $formname = "form_order_history_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <?

    $query = " select * from 
		(
			(SELECT 
								 id,
								 order_no AS 'order_no',	
								 '' AS 'sales_no',
								 DATE_FORMAT(order_date,'%d.%m.%Y') AS 'order_date',
								  user_name AS 'user_name', 
								 your_reference AS 'your_reference',
								 total AS 'total',
								'1' AS 'order_type'
					  FROM shop_sales_header
					  WHERE shop_customer_id = '" . $GLOBALS["shop_customer"]["id"] . "'
						AND order_date >= DATE_SUB(NOW(),INTERVAL 1 YEAR)
					  ORDER BY order_date DESC )
					  
					  union all
					  
					  (SELECT 
								id, 
								 webshop_order_no  AS 'order_no',
								no AS 'sales_no',
								DATE_FORMAT(order_date,'%d.%m.%Y') AS 'order_date',
								sell_to_name AS 'Ihr user_name', 
								your_reference AS 'Ihre your_reference',
								amount_including_vat AS 'total',
								'0' AS 'order_type'
					  FROM shop_nav_sales_header
					  WHERE sell_to_customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "'
						AND company = '" . $GLOBALS["shop"]["company"] . "'
					  
					  
					 ORDER BY order_date DESC )
		) as totals 
        
        order by totals.order_no DESC,totals.order_type ASC ";

    $result = mysqli_query($GLOBALS['mysql_con'], $query);

    $oder_numbers = array();
    while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {

        if ($row['order_no'] != 0) {
            if (array_key_exists($row['order_no'], $oder_numbers)) {
                array_push($oder_numbers[$row['order_no']], $row);
            } else {
                $oder_numbers[$row['order_no']] = array();
                array_push($oder_numbers[$row['order_no']], $row);
            }
        } else {
            $oder_numbers[$row['sales_no'] . '_' . $row['order_no']] = array();
            array_push($oder_numbers[$row['sales_no'] . '_' . $row['order_no']], $row);
        }


    }
    order_table($oder_numbers);
    ?>

</form>
<br/>
<br/>
<script>

    var showSubRow = false;

    $('#order_table .table_row.table_body').click(function () {

        if (!showSubRow) {
            $('#form_order_history_list').attr('action', $(this).data("href"));

            var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "input_id").val($(this).data("order_id"));
            $('#form_order_history_list').append($(input));


            $('#form_order_history_list').submit();
        }
        showSubRow = false;

    });

    $('#viewOrder').click(function () {
        showSubRow = true;
    });


</script>