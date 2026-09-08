<?php
use DynCom\dc\dcShop\interfaces\UserBasket;

if (!isset($basket) || !($basket instanceof UserBasket)) {
    $IOC = $GLOBALS['IOC'];
    $basket = $IOC->create('$CurrUserBasket');
}
$noOfBasketPos = $basket->getTotalNoOfPos();
$totalBasketAmnt = $basket->getBasketTotal();

// Erstellt backlink für die Item Card
if ($_GET["action"] == "shop_add_item_to_basket_card" || $_GET["action"] == "shop_individualize_item_card") {
    $action_back = create_item_link_tab($item);
} elseif ($_GET["action"] == "shop_individualize_basket") {
    $action_back = "//" . $_SERVER['HTTP_HOST'] . "/" . customizeUrl() . "/basket/";
} elseif ($_GET["shop_category"] == "favorites") {
    $action_back = "//" . $_SERVER['HTTP_HOST'] . "/" . customizeUrl() . "/favorites/";
} elseif ($_GET["shop_category"] == "search") {
    $action_back = "//" . $_SERVER['HTTP_HOST'] . "/" . customizeUrl() . "/search/?input_search=" . $_GET["input_search"];
}else {
    $action_back = "//" . $_SERVER['HTTP_HOST'] . category_get_path($category);
}
$formname = "form_queue";
$category = $GLOBALS['category'];
//$action_back = "http://".$_SERVER['HTTP_HOST']."/".$GLOBALS['site']['code']."/".$GLOBALS['language']['code']."/".get_category_path($category['id'], $GLOBALS['shop_language']);
$action_basket = "//" . $_SERVER['HTTP_HOST'] . "/" . customizeUrl() . "/basket/";
$action_order = "//" . $_SERVER['HTTP_HOST'] . "/" . customizeUrl() . "/order/address_select/";
$itemId = isset($_GET['card']) ? $_GET['card'] : $_REQUEST["item_id"];
$item = get_item_by_card_id($GLOBALS['shop']['company'], $GLOBALS['shop']['item_source'], $GLOBALS['shop_language']['code'], $itemId);
$parent_item = get_item_variant_parent($item);
$shipmentOption = get_shop_shipping_option($basket);
$shipmentOptionMinimumAmount = 0;
if ($shipmentOption['exemption'] != '') {
    $shipmentOptionMinimumAmount = $shipmentOption['exemption'];
}
$showShipmentMessage = false;
$amountNeededForFreeShipment = 0;
if ($shipmentOptionMinimumAmount > $totalBasketAmnt) {
    $showShipmentMessage = true;
    $amountNeededForFreeShipment = $shipmentOptionMinimumAmount - $totalBasketAmnt;
    $amountNeededForFreeShipment = format_amount($amountNeededForFreeShipment, TRUE, FALSE);
}
?>
<script type="text/javascript">
    $(window).load(function () {
        $('#user_queue').modal('show');
    });
</script>
<?
$item_quantity = $_POST['item_qty'];
$customerCustomizationPrice = format_amount($item_quantity * $item['customization_price']);
$file_input_name = "";
if ($_GET["action"] == "shop_individualize_item_card") {

    $customization_html = "";

    for ($i = 1; $i <= $item_quantity; $i++) {

        if ($item_quantity > 1) {
            $customization_html .= ' <div class="row"> <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $GLOBALS['tc']['item'] . ' ' . $i . '</h2> </div>';
        }

        $customization_query = " SELECT * FROM shop_item_customize 
							WHERE shop_code =  '" . $GLOBALS['shop']['code'] . "'
							AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
							AND item_no = '" . $item["item_no"] . "' 
							order by line_no ASC ";
        $customizationData = mysqli_query($GLOBALS['mysql_con'], $customization_query);
        if (mysqli_num_rows($customizationData) > 0) {
            while ($customization_result = mysqli_fetch_array($customizationData)) {
                switch ($customization_result["field_type"]) {
                    case 0:
                        $cust_element = "text";
                        break;
                    case 1:
                        $cust_element = "file";
                        $customization_result["field_length"] = 50;
                        break;
                    case 2:
                        $cust_element = "textarea";
                        break;
                    default:
                        $cust_element = "text";
                        break;
                }
                $customization_html .= '  ' . PHP_EOL;
                $customization_html .= ' <div class="rating_text_headline">' . $customization_result["field_name"] . '</div> ' . PHP_EOL;

                if ($cust_element == "file") {
                    $file_input_name = 'inputcustomize_' . $i . '_' . $customization_result['id'];

                    $customization_html .= ' <div >
 
                                <input required accept="image/*"  class="form-control" name ="inputcustomize_' . $i . '_' . $customization_result['id'] . '"   id="inputcustomize_' . $i . '_' . $customization_result['id'] . '"  type="file" class="file"> <br />
                           
                           
                           </div> ';
                } else if ($cust_element == "textarea") {
                    $customization_html .= ' 
                                <textarea required maxlength="250" name="inputcustomize_' . $i . '_' . $customization_result['id'] . '"  id="inputcustomize_' . $i . '_' . $customization_result['id'] . '"  class="form-control" ></textarea><br />
                             ';
                } else {
                    $customization_html .= '  
                                <input required name="inputcustomize_' . $i . '_' . $customization_result['id'] . '"  id="inputcustomize_' . $i . '_' . $customization_result['id'] . '"  class="form-control" type="text" value="" maxlength="45" /><br />
                             ';
                }
                $customization_html .= PHP_EOL;
                $customization_html .= ' ';

            }
        }

    }


    ?>

    <div class="modal fade" id="user_queue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $GLOBALS["tc"]["add_to_basket"] ?></h4>
                </div>
                <div class="modal-body">

                    <form name="<?= $formname ?>" id="<?= $formname ?> "
                          action="?action=shop_add_item_to_basket_card"
                          enctype="multipart/form-data" method="POST">
                        <input type="hidden" name="item_id" id="input_item_id" value="<?= $item['id'] ?>">
                        <div class="row">

                            <div class="col-xs-12 col-sm-4 pull-right">
                                <div class="modal-item-info">
                                    <div class="modal-item-image">
                                        <?= get_customization_image($item, 2) ?>
                                    </div>
                                    <div class="modal-item-description text-center">
                                        <strong><?= $item['description'] ?></strong>
                                        <br/>
                                        <?= $item['summary'] ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-xs-12 col-sm-8">
                                <input name="customizable_input" value="1" hidden />
                                <?= $customization_html ?>
                            </div>
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-8">
                                        <div class="order_prices_box">
                                            <div class="order_prices_box_left">
                                                <?
                                                echo $item_quantity;
                                                echo ' ' . $GLOBALS["tc"]["item_in_order"];
                                                ?>
                                            </div>
                                            <div class="order_prices_box_right">
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <div class="order_price_total_label"><?= $GLOBALS['tc']['total_amount'] ?></div>
                                                    </div>
                                                    <div class="col-xs-6 text-right">
                                                        <div class="order_price_total"><?= $customerCustomizationPrice; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="itemcard_order_button_input_wrapper_outer item_qty_input_wrapper_outer display_switch_block_itemcard_order_button_active active_call_to_action_wrapper active_order_button_wrapper">

                                            <div class="itemcard_order_button_wrapper_text item_qty_wrapper_text">
                                                <button type="submit"
                                                        class="itemcard_order_submit_button itemcard_order_submit_button_active">
                                                    <span><?= $GLOBALS["tc"]["add_to_basket"] ?></span>
                                                    <i class="fa fa-shopping-cart"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <?
} elseif ($_GET["action"] == "shop_individualize_basket") {


    $pdoHost = getenv('MAIN_MYSQL_DB_HOST');
    $pdoPort = getenv('MAIN_MYSQL_DB_PORT');
    $pdoUser = getenv('MAIN_MYSQL_DB_USER');
    $pdoPass = getenv('MAIN_MYSQL_DB_PASS');
    $pdoSchema = getenv('MAIN_MYSQL_DB_SCHEMA');

    $pdo = new \DynCom\dc\common\classes\PDOQueryWrapper($pdoHost, $pdoPort, $pdoSchema, $pdoUser, $pdoPass);

    $prepStatement = " SELECT * FROM shop_user_basket_customize 
							WHERE customization_hash = :customization_hash
        ";
    $params = [
        [':customization_hash', $_REQUEST["action_id"], PDO::PARAM_STR],
    ];
    $pdo->setQuery($prepStatement);
    $pdo->prepareQuery();
    $pdo->bindParameters($params);
    $pdo->executePreparedStatement();
    $cust_result = $pdo->getResultArray();

    $customization_html = "";

    if (count($cust_result) > 0) {
        foreach ( $cust_result as $customization_result ) {
            switch ($customization_result["field_type"]) {
                case 0:
                    $cust_element = "text";
                    break;
                case 1:
                    $cust_element = "file";
                    $customization_result["field_length"] = 50;
                    break;
                case 2:
                    $cust_element = "textarea";
                    break;
                default:
                    $cust_element = "text";
                    break;
            }

            $customization_html .= ' <div class="col-xs-12 col-sm-12"> ' . PHP_EOL;
            $customization_html .= ' <div class="rating_text_headline">' . $customization_result["field_name"] . '</div> ' . PHP_EOL;
            if ($cust_element == "file") {
                $file_input_name = 'inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'];
                $imagelink = $GLOBALS['projectRoot'] . $GLOBALS['shop_setup']['uploaddir_customize'] . "/" . $customization_result['value'];
                if ($customization_result['value'] != '' && file_exists("../../" . $imagelink)) {

                    $customization_html .= '   <div  class="col-xs-12 col-sm-12" >
   
                    <div class="table_cell image_line text-center" >
                            <div class="image"> 
                                <img src="' . $imagelink . '" border="0" >
                            </div>
                    </div>  ';

                    $customization_html .= '  <div  class="col-xs-3 col-sm-3" >
                                                               </div>    
                                  <div  class="col-xs-12 col-sm-12" >    
                                <input  accept="image/*"  class="form-control" value="' . $customization_result['value'] . '" name ="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"   id="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  type="file" > 
                           </div>  </div> ';

                } else {
                    $customization_html .= ' <div >
                                <input  accept="image/*"  class="form-control" value="' . $customization_result['value'] . '" name ="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"   id="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  type="file" > 
                           </div> ';
                }

            } else if ($cust_element == "textarea") {
                $customization_html .= ' 
                                <textarea required maxlength="250" name="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  id="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  class="form-control" >' . $customization_result['value'] . '</textarea>
                             ';
            } else {
                $customization_html .= '  
                                <input required value="' . $customization_result['value'] . '" name="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  id="inputcustomize_' . $customization_result['id'] . '_' . $customization_result['item_customization_id'] . '"  class="form-control" type="text" value="" maxlength="45" />
                             ';
            }
            $customization_html .= PHP_EOL;
            $customization_html .= ' </div> ';
        }


    }

    ?>


    <div class="modal fade" id="user_queue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= $GLOBALS["tc"]["edit_customization"] ?></h4>
                </div>
                <div class="modal-body">

                    <form name="<?= $formname ?>" id="<?= $formname ?>"
                          action="?action=shop_refresh_user_basket_customize&action_id=<?= $_REQUEST['action_id'] ?>"
                          enctype="multipart/form-data" method="POST">
                        <div class="row">
                            <?= $customization_html ?>

                        </div>
                        <div class="row">
                            <div class="button_row text-right">
                                <button type="submit" name="save_data"
                                        class="button_save button button_action">
                                    <?= $GLOBALS["tc"]["save"] ?>
                                </button>
                            </div>

                        </div>

                    </form>
                   <!-- <div class="row"></div> -->
                </div>
            </div>
        </div>
    </div>


    <?
} else {

    ?>


    <div class="modal fade" id="user_queue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"
                        id="myModalLabel"><?= $item['description']; ?> <?= $GLOBALS["tc"]["added_to_basket"] ?></h4>
                </div>
                <div class="modal-body">
                    <div class="user_queue_content">
                        <div class="user_queue_added modal-item-info">
                            <? if ($showShipmentMessage) { ?>
                                <div class="row">
                                    <b>
                                        <? echo str_replace("%ammount_needed%", $amountNeededForFreeShipment, $GLOBALS["tc"]["free_shipment_message"]); ?>
                                    </b>
                                    <br>
                                    <br>
                                </div>
                            <? } ?>
                            <div class="user_queue_added_basket modal-item-description">
                                <?= $GLOBALS["tc"]["user_queue_basket_1"]; ?>
                                <strong><?= $noOfBasketPos; ?> <?= $GLOBALS["tc"]["item"]; ?></strong> <?= $GLOBALS["tc"]["user_queue_basket_2"]; ?>
                                <strong><?= format_amount($totalBasketAmnt, TRUE, FALSE); ?></strong>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-5">
                                    <div class="user_queue_added_image modal-item-image">
                                        <?
                                        if( isset($_POST['customizable_input']) && $_POST['customizable_input'] == 1)
                                        {
                                            echo get_customization_image($item, 2);
                                        }
                                        else
                                        {
                                            echo get_image($item, 2);
                                        }

                                       ?>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-7">
                                    <a href='<?= $action_order; ?>'
                                       class='button_action button text-center'><?= $GLOBALS['tc']['to_order']; ?></a><br/>
                                    <a href='<?= $action_basket; ?>'
                                       class='button_text button text-center'><?= $GLOBALS['tc']['to_basket']; ?></a><br/>
                                    <a href="<?= $action_back; ?>"
                                       class='button_text button text-center'><?= $GLOBALS['tc']['continue']; ?></a>
                                </div>
                            </div>
                        </div>

                        <?
                        $query = "SELECT DISTINCT shop_view_active_item.*
                      FROM shop_view_active_item
                      LEFT JOIN shop_item_link ON shop_item_link.linked_item_no = shop_view_active_item.item_no
                      WHERE shop_view_active_item.language_code = '" . $item["language_code"] . "'
                        AND shop_view_active_item.company = '" . $GLOBALS['shop']['company'] . "'
                        AND shop_item_link.company = '" . $GLOBALS['shop']['company'] . "'
                        AND shop_item_link.shop_code = '" . $GLOBALS['shop']['item_source'] . "'
                        AND (shop_item_link.type = 4 OR shop_item_link.type = 2)
                        AND (shop_item_link.item_no = '" . $item["item_no"] . "'
                            OR shop_item_link.item_no = '" . $parent_item["item_no"] . "')
                      ORDER BY RAND()
                      LIMIT 2";
                        $result = mysqli_query($GLOBALS['mysql_con'], $query);
                        if ($GLOBALS['shop_language']['order_queue_text_module'] != '') {
                            $spacer = array();
                            echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_queue_text_module'], $spacer));
                        }
                        if (mysqli_num_rows($result) > 0) {
                            echo("<br/><h2>" . $GLOBALS['tc']['cross_selling_text'] . "</h2><br/>");
                            show_item_list($result, 3, "", 2);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? if ($GLOBALS["site"]["facebook_pixel_id"] <> "") { ?>
    <script>
        fbq('track', 'AddToCart');
    </script>
    <? } ?>
    <?
}

?>
