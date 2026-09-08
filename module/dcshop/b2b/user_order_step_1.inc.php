<?php

use DynCom\dc\dcShop\classes\BasketEntity;
use DynCom\dc\dcShop\classes\WebshopItemBuilder;
use DynCom\dc\dcShop\interfaces\UserBasket;

$secret = getenv('SHOP_PASSWORD');

$secretToken2 = array(
    "time"=>time(),
    "token_id"=>$_SERVER['SERVER_NAME']
);

$secretToken1 = base64_encode(hash_hmac("sha256",json_encode($secretToken2),$secret));
$secretToken2 = base64_encode(json_encode($secretToken2));

if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
    $currUserBasket = $IOCContainer->create('$CurrUserBasket');
    if ($currUserBasket instanceof UserBasket) {
        $invDiscs = $currUserBasket->getAppliedInvoiceDiscounts();
    }
} else {
    $invDiscs = $currUserBasket->getAppliedInvoiceDiscounts();
}
$itemBuilder = $IOCContainer->create('DynCom\dc\dcShop\classes\WebshopItemBuilder');
$itemArr = [];
foreach ($currUserBasket as $basketItem) {
    if ($basketItem instanceof BasketEntity && $basketItem->getOrderableType() === 2) {
        $orderableEntity = $basketItem->getOrderableEntity();
        if ($itemBuilder instanceof WebshopItemBuilder) {
            $itemWithImages = $itemBuilder->decorateWebshopItemImages($orderableEntity);
            $item = [];
            $item['id'] = $itemWithImages->getID();
            $item['company'] = $itemWithImages->getCompany();
            $item['shop_code'] = $itemWithImages->getShopCode();
            $item['language_code'] = $itemWithImages->getLanguageCode();
            $item['item_no'] = $itemWithImages->getItemNo();
            $item['var_code'] = $itemWithImages->getVariantCode();
            $item['unit_price'] = $basketItem->getUnitPrice();
            $item['customer_price'] = $basketItem->getUnitPrice();
            $item['line_amount'] = $basketItem->getLineAmount();
            $item['basket_id'] = $basketItem->getDBID();
            $item['quantity'] = $basketItem->getQuantity();
            $item['basket_quantity'] = $basketItem->getQuantity();
            $item['image_data'] = $itemWithImages->getImageData();
            $item['main_image_data'] = $itemWithImages->getMainImageData();
            $item['description'] = $itemWithImages->getDescription();
            $item['variant_typ'] = $itemWithImages->getVariantType();
            $item['variant_type'] = $itemWithImages->getVariantType();
            $item['summary'] = $itemWithImages->getSummary();
            $item['parent_item_no'] = $itemWithImages->getParentItemNo();
            $item["allow_invoice_disc"] = $itemWithImages->allowsInvoiceDiscount();
            $itemArr[] = $item;
        }
    }
}

if (($GLOBALS['visitor']['frontend_login'] == 1 && $GLOBALS['visitor']['cookie_only'] == 1) || !$GLOBALS['visitor']['frontend_login']) {
    ?>
    <div class="login_wrapper">
        <form id="form_shop_login" name="form_shop_login" method="post"
              action="?action=edit_curr_shop_user&login=true">
            <div class="login_headline"><strong><?= $GLOBALS['tc']['log_in_to_use'] ?></strong><br></div>
            <div class="login_input_wrapper side_login_input_wrapper">
                <?
                $fields = get_login_fields_html($GLOBALS['shop']);
                echo $fields;
                ?>
            </div>
            <div class="ok_button_wrapper">
                <div class="ok_button" onclick="document.forms['form_shop_login'].submit();"></div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        $(function () {
            $('#input_password').focus();
        });
    </script>

    <?
} else {

    $formname = "form_user_order";

    if ($GLOBALS['shop']['select_req_delivery_date'] == 1) {
        switch (date('w')) {
            case 3:
                $days = $GLOBALS['shop']['addition_req_delivery_date'] + 2;
                break;
            case 4:
                $days = $GLOBALS['shop']['addition_req_delivery_date'] + 2;
                break;
            case 5:
                $days = $GLOBALS['shop']['addition_req_delivery_date'] + 2;
                break;
            case 6:
                $days = $GLOBALS['shop']['addition_req_delivery_date'] + 1;
                break;
            default:
                $days = $GLOBALS['shop']['addition_req_delivery_date'];
                break;
        }
    }
    $_SESSION['site_code'] = $GLOBALS['site']['code'];
    $_SESSION['language_code'] = $GLOBALS['language']['code'];

    if ($_GET['payment_error'] == 1) {
        get_requestbox(get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['payment_error_text_module'], array()));
        $_GET['payment_error'] = 0;
    }
    ?>
    <div class="toolbar">
        <h1 class="shop_site_headline"><?= $GLOBALS["tc"]["user_order"] ?></h1>
    </div>
    <?
    if ($GLOBALS['shop_language']['order_step_1_text_module'] != '') {
        $spacer = array();
        echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_step_1_text_module'], $spacer));
    }
    if ($GLOBALS['shop_user']['right_order'] == 0) {
        get_requestbox($GLOBALS['tc']['no_order_rights']);
    } ?>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <? if ($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            <h3><?= $GLOBALS["tc"]["confirmation"] ?></h3>
            <? if ($_GET["action"] == "complete_order" & $_POST['input_agb_checked'] == "") {
                get_requestbox($GLOBALS['tc']['confirm_agb']);
            }
            ob_start();
            input($GLOBALS['tc']['agb_confirmed'], "input_agb_checked", "checkbox", "", 50, FALSE, FALSE);
            $spacer['%checkbox%'] = ob_get_contents();
            ob_end_clean();
            $agb_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['checkout_confirmation_text_module'], $spacer);
            echo $agb_text;
            ?>
        <? } else { ?>
            <input type="hidden" name="input_agb_checked" id="input_agb_checked" value="on"/>
        <? } ?>
        <? if ($GLOBALS['shop_language']['newsletter_registration_text_module'] != '') { ?>
            <h3><?= $GLOBALS["tc"]["newsletter"] ?></h3>
            <?
            ob_start();
            input($GLOBALS['tc']['newsletter_registration'], "input_newsletter_checked", "checkbox", "", 50, FALSE, FALSE);
            $spacer['%checkbox%'] = ob_get_contents();
            ob_end_clean();
            $newsletter_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['newsletter_registration_text_module'], $spacer);
            echo $newsletter_text;
            ?>
        <? } ?>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="checkout_left">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-12">
                            <? /*<h3><?= $GLOBALS["tc"]["information"] ?> </h3>*/ ?>
                            <div><? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $GLOBALS["shop_customer"]["customer_no"], 30, TRUE) ?></div>
                            <div><? input_shop($GLOBALS["tc"]["name"], "input_name", "text", $GLOBALS["shop_user"]["name"], 30, FALSE) ?></div>
                            <div><? input_shop($GLOBALS["tc"]["email"], "input_email", "text", $GLOBALS["shop_user"]["email"], 50, TRUE) ?></div>
                            <div>
                                <? if ($GLOBALS["shop"]["order_options_display"] == 0) { ?>
                                    <? payment_terms_select($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_POST["input_payment_line_no"], FALSE, TRUE, $invoice_address["country"], false) ?>
                                <? } else { ?>
                                    <? payment_terms_list($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_POST["input_payment_line_no"], FALSE, TRUE, $invoice_address["country"], false) ?>
                                <? } ?>
                            </div>
                            <div>
                                <? if ($GLOBALS["shop"]["order_options_display"] == 0) { ?>
                                    <? shipping_agent_select($GLOBALS["visitor"], $GLOBALS["tc"]["shipping_agent"], "input_shipping_line_no", $_POST["input_shipping_line_no"], FALSE, TRUE, $shipment_address["country"], $shipment_address["post_code"], $currUserBasket) ?>
                                <? } else { ?>
                                    <? shipping_agent_list($GLOBALS["visitor"], $GLOBALS["tc"]["shipping_agent"], "input_shipping_line_no", $_POST["input_shipping_line_no"], FALSE, TRUE, $shipment_address["country"], $currUserBasket) ?>
                                <? } ?>
                            </div>
                            <div>

                                <? shipment_address_select($GLOBALS["shop_customer"], $GLOBALS["tc"]["shipment_address"], "input_shipment_address_id", $_POST["input_shipment_address_id"], FALSE, TRUE) ?></div>
                            <div>
                            <?

                            $key = getenv('SHOP_PASSWORD');
                            $paramentersArray = [
                                    'secretAction' => 'GetOrAddOrEditData',

                            ];
                            $paramentersArray['secretAction'] = "GetOrAddOrEditData";

                            $jsondata = json_encode($paramentersArray);

                            $methods = openssl_get_cipher_methods();

                            $encryptedKey = openssl_encrypt($jsondata, $methods[37], $key);

                            echo "
                                     <input type=\"hidden\" name=\"secretKey\" id=\"secretKey\" value='".$encryptedKey."'/>
                                      <input type=\"hidden\" class=\"form-control\" name=\"token_1\" value='".$secretToken1."' id=\"token_1\">
                                        <input type=\"hidden\" class=\"form-control\" name=\"token_2\" value='".$secretToken2."' id=\"token_2\">
                                    <div class='form-group'>
                                    <button type=\"button\" id='addNewAddressOrderPage' name ='addNewAddressOrderPage' class=\"button_save button button_action\" data-toggle=\"modal\" data-target=\"#addEditAddressModel\">
                                    " . $GLOBALS["tc"]["new"] . "
                                    </button>
        
                                    <button type=\"button\" id='editAddressOrderPage' name ='editAddressOrderPage'  class=\"button_save button button_action\" data-toggle=\"modal\" data-target=\"#addEditAddressModel\">
                                     " . $GLOBALS["tc"]["edit"] . "
                                    </button>
                                    </div>
                                    ";



                            echo "   <div class=\"modal fade\" id=\"addEditAddressModel\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\">
                                    <div class=\"modal-dialog modal-md\" role=\"document\">
                                    <div class=\"modal-content\">
                                        <div class=\"modal-header\">
                                            <button type=\"button\" id='closeModelHeaderButton' class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                                            <h4 class=\"shop_site_headline\" id=\"myModalLabel\" style='margin-bottom: auto'>" . $GLOBALS["tc"]["shipment_address"] . "</h4>
                                        </div>
                                        <div class=\"modal-footer\"></div>
                                        <div class=\"modal-body\"> ";

                            echo "<div id='errorMessageDivModal' class=\"alert alert-danger\" style='display: none'>  
                                  ".$GLOBALS["tc"]["error_1_ship_addr"]."
                                </div>";


                            echo '
                                                <input name="input_id_Modal" type="hidden" value="">
        
                                                    ';
                            input_shop($GLOBALS["tc"]["name"], "input_name_Modal", "text", "", 30);
                            input_shop($GLOBALS["tc"]["name_2"], "input_name_2_Modal", "text", "", 30);
                            input_shop($GLOBALS["tc"]["contact"], "input_contact_Modal", "text", "", 30);
                            input_shop($GLOBALS["tc"]["address"], "input_address_Modal", "text", "", 30);
                            input_shop($GLOBALS["tc"]["address_2"], "input_address_2_Modal", "text", "", 30);
                            input_shop($GLOBALS["tc"]["post_code"], "input_post_code_Modal", "code", "", 6);
                            input_shop($GLOBALS["tc"]["city"], "input_city_Modal", "text", "", 30);
                            create_countries("Land", "input_country", 'DE', FALSE, 2);
                            input_shop($GLOBALS["tc"]["phone_no"], "input_telephone_Modal", "text", "", 30);
                            echo '
                                                <div class="button_row_bottom">
                                                    <button type="button"  id="saveModelFormButton" class="button_save button button_action" value="Add" name ="saveModelFormButton"  data-dismiss="modal">'.$GLOBALS["tc"]["save"].'</button>
                                                    <button type="button" class="button_back button" id="closeModelFooterButton" name ="closeModelFooterButton"  data-dismiss="modal">'.$GLOBALS["tc"]["textBtnClose"].'</button> 
                                            
                                                </div>
                                            </form> ';


                            echo "</div>
                                    </div>
                                </div>
                            </div>
                            ";
                            ?>
                            </div>

                            <div><? input_shop($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $_POST["input_your_reference"], 30) ?></div>
                            <div><? input_shop($GLOBALS["tc"]["your_comment"], "input_your_comment", "textarea", $_POST["input_your_comment"], 160) ?></div>
                            <div><? if ($GLOBALS['shop']['select_req_delivery_date'] == 1) {
                                    input_shop($GLOBALS["tc"]["preferred_order_date"], "date", "date", $_POST["date"], 30, FALSE, FALSE);
                                } ?></div>
                            <?//echo '<div>' . input_shop($GLOBALS["tc"]["drop_shipment"], "input_drop_shipment", "checkbox", $_POST["input_drop_shipment"], 20) . '</div>'?>
                            <div><? input_shop($GLOBALS["tc"]["coupon_code"], "input_coupon_code", "text", $_SESSION['coupon']['coupon_code'], 30) ?></div>
                            <div class="coupon_button"><?
                                if ($_SESSION['coupon'] == '') {
                                    button('edit button', $GLOBALS['tc']['use_coupon'], $formname, "?action=coupon");
                                } else {
                                    button('delete button', $GLOBALS['tc']['remove_coupon'], $formname,  "?action=coupon_delete");
                                }
                                ?>
                            </div>
                        </div>
                        <div style="display: none" class="col-xs-12 col-sm-6 col-md-12">
                            <h3>
                                <?= $GLOBALS["tc"]["invoice_address"] ?>
                            </h3>
                            <? format_address($invoice_address["name"], $invoice_address["name_2"], $invoice_address["address"], $invoice_address["address_2"], $invoice_address["post_code"], $invoice_address["city"], $invoice_address["country"], ''); ?>
                            <h3>
                                <?= $GLOBALS["tc"]["shipment_address"] ?>
                            </h3>
                            <? //&Auml;nderung Bluestar HT: Funktion format_address erweitert um Telefonnummer
                            format_address_with_phone($shipment_address["name"], $shipment_address["name_2"], $shipment_address["address"], $shipment_address["address_2"], $shipment_address["post_code"], $shipment_address["city"], $shipment_address["country"], $shipment_address["contact"], $shipment_address["telephone"], '', $_SESSION["input_is_company"]);
                            $total = $subtotal + $small_quantity_charge_amount - $invoice_discount_amount - $online_discount_amount + $_SESSION['shipping_cost'] + $_SESSION['payment_cost']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8">
                <div class=" checkout_right">
                    <? show_item_list($itemArr, 5, $formname); ?>
                    <div class="order_prices_box">
                        <div class="order_prices_box_left">
                            <?= basketQty() ?>
                            <?= $GLOBALS["tc"]["item_in_basket"] ?>
                            <br/>
                            <br/>
                            <?= $GLOBALS["tc"]["vat_message"] ?>
                            <br/>
                            <?= $GLOBALS["tc"]["shipment_message"] ?>
                        </div>
                        <div class="order_prices_box_right">
                            <? show_order_sum($subtotal, $online_discount, $online_discount_amount, $invoice_discount, $invoice_discount_amount, $small_quantity_charge_amount, $total, $_SESSION['shipping_cost'], $_SESSION['payment_cost']) ?>
                        </div>
                    </div>
                    <div class="button_row">
                        <?= button("back button", $GLOBALS["tc"]["back_to_basket"], $formname, "/".customizeUrl()."/basket/"); ?>
                        <? if ($GLOBALS['shop_user']['right_order'] == 1) { ?>
                            <?= button_finish("next button button_action pull-right", $GLOBALS["tc"]["complete_order"], $formname,  "/".customizeUrl()."/order/payment/", ""); ?>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        var secretKey = '';
        <?
         /*   $key = $GLOBALS["shop_setup"]["shop_password"];
            $paramentersArray = array();
            $paramentersArray['secretAction'] = "GetOrAddOrEditData";

            $jsondata = json_encode($paramentersArray);

             $methods = openssl_get_cipher_methods();

             $encryptedKey = openssl_encrypt($jsondata, $methods[37], $key);*/

        ?>

        // secretKey = <?  //echo $encryptedKey; ?>;


        function pay_other() {
            <? if($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            if (document.form_user_order.input_agb_checked.checked == false) {

                alert("<?= $GLOBALS["tc"]["confirm_agb"] ?>");
                document.forms[0].input_agb_checked.focus();

            }
            else {
                <? } ?>
                var str = "<? echo "/".customizeUrl()."/order/payment/"; ?>"
                str = str.replace(/\&amp;/g, '&');
                document.form_user_order.action = '' + str + '';
                document.form_user_order.submit();
                <? if($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            }
            <? } ?>
        }
    </script>
    <?
}
?>