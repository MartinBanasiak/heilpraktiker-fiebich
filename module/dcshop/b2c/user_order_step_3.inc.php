<?php

use DynCom\dc\common\interfaces\SessionFlashMessageBag;
use DynCom\dc\dcShop\interfaces\UserBasket;
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemDecorator;
use DynCom\dc\dcShop\classes\GenericInvoiceDiscount;
use DynCom\dc\dcShop\classes\GenericLineDiscount;


if (!isset($currUserBasket) || !($currUserBasket instanceof UserBasket)) {
    $currUserBasket = $IOCContainer->create('$CurrUserBasket');
    if ($currUserBasket instanceof UserBasket) {
        $invDiscs = $currUserBasket->getAppliedInvoiceDiscounts();
    }
} else {
    $invDiscs = $currUserBasket->getAppliedInvoiceDiscounts();
}
$itemArr = basketItems();
$vatMgr = $IOCContainer->create('$VATManager');
$discounts = $currUserBasket->getAppliedInvoiceDiscounts();

if($_SESSION['use_paypal_express'] || $_SESSION['use_amazon_pay'])
{

    if (!isset($IOCContainer)) {
        $IOCContainer = $GLOBALS['IOC'];
    }

    $pdoWrapper = $IOCContainer->create('DynCom\dc\common\classes\PDOQueryWrapper');
    /** @var \DynCom\dc\dcShop\classes\CurrShopConfiguration $currShopConfiguration */
    $currShopConfiguration = $IOCContainer->create('$CurrShopConfig');
    $criteriaValidationService = new \DynCom\dc\common\classes\SelectionCriteriaHelper();

    $shippingOptionTranslationConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationConfig();
    $shippingOptionTranslationCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationCollection($shippingOptionTranslationConfig,$criteriaValidationService);
    $shippingOptionTranslationRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslationRepository($pdoWrapper,$shippingOptionTranslationConfig,$criteriaValidationService,$shippingOptionTranslationCollection);

    $shippingZoneLineConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneLineConfig();
    $shippingZoneLineCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneLineCollection($shippingZoneLineConfig,$criteriaValidationService);
    $shippingZoneLineRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneLineRepository($pdoWrapper,$shippingZoneLineConfig,$criteriaValidationService,$shippingZoneLineCollection);

    $shippingZoneConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneConfig();
    $shippingZoneCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneCollection($shippingZoneConfig,$criteriaValidationService);
    $shippingZoneRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingZoneRepository($pdoWrapper,$shippingZoneLineRepository,$shippingZoneConfig,$criteriaValidationService,$shippingZoneCollection);

    $shippingOptionConfig = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionConfig();
    $shippingOptionCollection = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionCollection($shippingOptionConfig,$criteriaValidationService);
    $shippingOptionRepository = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionRepository($pdoWrapper,$shippingOptionConfig,$criteriaValidationService,$shippingOptionCollection,$shippingZoneRepository,$shippingOptionTranslationRepository);

    /** @var \DynCom\dc\dcShop\ShippingOptions\ShippingOptionCollection $shippingOptions */
    $shippingOptions = $shippingOptionRepository->getAllForOrder($currShopConfiguration, $currUserBasket, $_SESSION["visitor_country_shipping"], $_SESSION["visitor_post_code_shipping"], NULL);
    $shippingOption = $shippingOptions->getFirst();

    if($shippingOption === null)
    {
        die("we are not delivering to this address ");
    }

    $_SESSION['shipping_line_no'] = $shippingOption->line_no;

    $paymentOption  = get_payment_option_by_line_no($_SESSION["payment_line_no"]);
    $_SESSION['payment_cost']  = $paymentOption['payment_cost'];

    $_SESSION['use_paypal_express'] = false;
    $_SESSION['use_amazon_pay'] = false;

}

$formname = "form_user_order_3";
$_SESSION["order_previous_step"] = "step3";

if ($_SESSION['coupon_error']) {
    unset($_SESSION['coupon_error']);
    get_requestbox($GLOBALS['coupon_valid'], $GLOBALS['coupon_error']);
}

if ($_GET['payment_error'] == 1) {
    get_requestbox(get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['payment_error_text_module'], array()), "");

    /*<div
        class="errorbox"><?= get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['payment_error_text_module'], array()) ?></div>
    <br />
    <?*/
    $_GET['payment_error'] = 0;
}
?>
    <ul class="processbar">
        <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_address"] ?>
            <div class="arrow"></div>
            <div class="arrow-border"></div>
        </li>
        <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_payment"] ?>
            <div class="arrow"></div>
            <div class="arrow-border"></div>
        </li>
        <li class="processbar_item active"><?= $GLOBALS["tc"]["order_bar_check"] ?></li>
    </ul>
<?
if ($GLOBALS['visitor']['frontend_login'] == 1 && $GLOBALS['visitor']['cookie_only'] == 1) {
    ?>
    <div class="login_wrapper">
        <form id="form_shop_login" name="form_shop_login" method="post"
              action="/<? echo customizeUrl(); ?>/account/?action=edit_curr_shop_user&login=true">
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


    if ($GLOBALS['shop_language']['order_step_3_text_module'] != '') {
        $spacer = array();
        echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_step_3_text_module'], $spacer));
    }
    ?>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <? /* ---SUBSCRIPTION--- */ ?>
        <input type="hidden" name="subscription_item_id"
               value="<?php echo urlencode(filter_var($_POST['subscription_item_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>
        <input type="hidden" name="subscription_item_var_code"
               value="<?php echo urlencode(filter_var($_POST['subscription_item_var_code'], FILTER_SANITIZE_STRING)); ?>"/>
        <input type="hidden" name="subscription_item_qty"
               value="<?php echo urlencode(filter_var($_POST['subscription_item_qty'], FILTER_SANITIZE_NUMBER_FLOAT)); ?>"/>
        <input type="hidden" name="subscription_header_id"
               value="<?php echo urlencode(filter_var($_POST['subscription_header_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>
        <? /* +++SUBSCRIPTION+++ */

        if ($_SESSION["order_query_error"]) {
            unset($_SESSION["order_query_error"]);
            /**
             * @var $sessionFlashMessageBag SessionFlashMessageBag
             */
            $sessionFlashMessageBag = $GLOBALS['flashMessageBag'];
            $sessionFlashMessageBag->set(SessionFlashMessageBag::TYPE_ERROR, $GLOBALS['tc']['order_error']);
        }


        ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="order_box_headline">
                    <h2><?= $GLOBALS["tc"]["user_data"] ?></h2>
                    <a class="order_box_change_link"
                       href="/<? echo customizeUrl();?>/order/address_select/"><?= $GLOBALS['tc']['change'] ?></a>
                </div>
                <? if ($GLOBALS["shop_customer"]["customer_no"] != "") {
                    echo($_SESSION['visitor_salutation'] . "<br/>");
                    if (isset($_SESSION['visitor_title']) && $_SESSION['visitor_title'] != "") {
                        echo($_SESSION['visitor_title'] . " ");
                    }
                    echo $_SESSION["visitor_name"];
                    echo ($_SESSION["visitor_name_2"] != "") ? "<br />" . $_SESSION["visitor_name_2"] : "";
                    echo ($_SESSION['visitor_company'] != "") ? "<br />" . $_SESSION['visitor_company'] : "";
                    echo ($_SESSION['visitor_vatid'] != "") ? "<br />" . $_SESSION['visitor_vatid'] : "";
                } else {
                    echo($_SESSION['visitor_salutation'] . "<br/>");
                    if (isset($_SESSION['visitor_title']) && $_SESSION['visitor_title'] != "") {
                        echo($_SESSION['visitor_title'] . " ");
                    }
                    echo $_SESSION["visitor_name"];
                    if ($_SESSION['visitor_company'] != "") {
                        echo "<br/> " . $_SESSION["visitor_company"];
                    }
                    if ($_SESSION['visitor_vatid'] != "") {
                        echo "<br/> " . $_SESSION["visitor_vatid"];
                    }
                } ?>


                <? echo "<br />" . $_SESSION["visitor_address"] . " " . $_SESSION["visitor_address_no"] ?><br/>
                <? echo $_SESSION["visitor_post_code"] ?> <? echo $_SESSION["visitor_city"] ?><br/>
                <? echo get_country_by_code($_SESSION["visitor_country"]); ?><br/><br/>

                <div class="order_box_headline">
                    <h2><?= $GLOBALS["tc"]["comm_data"] ?></h2>
                    <a class="order_box_change_link"
                       href="/<? echo customizeUrl();?>/order/address_select/"><?= $GLOBALS['tc']['change'] ?></a>
                </div>
                <? echo $_SESSION["visitor_email"] ?><br/>
                <?php
                if (isset($_SESSION["visitor_telephone"]) && $_SESSION["visitor_telephone"] != "") {
                    echo $_SESSION["visitor_telephone"] . "<br />";
                }
                ?>
                <?php
                if (isset($_SESSION["visitor_birthday"]) && $_SESSION["visitor_birthday"] != "") {
                    echo("<br><b>" . $GLOBALS["tc"]["birthday"] . ":</b><br />");
                    echo $_SESSION["visitor_birthday"] . "<br />";
                    echo("<div class=\"spacer_6\">&nbsp;</div>");
                }
                ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <? if ($_SESSION["dc_id"] == '') { ?>
                    <div class="order_box_headline">
                        <h2><?= $GLOBALS["tc"]["shipment_address"] ?></h2>
                        <a class="order_box_change_link"
                           href="/<? echo customizeUrl();?>/order/address_select/"><?= $GLOBALS['tc']['change'] ?></a>
                    </div>
                    <?

                    if (($GLOBALS["shop_customer"]["customer_no"] == "") || ($_SESSION["visitor_new_shipping_address"] == "on" || $_SESSION['input_shipment_address_id'] == 0) || ($_SESSION['visitor_packstation_address'] == "on")) {

                        if ($GLOBALS["shop_customer"]["customer_no"] == "" && $_SESSION["visitor_new_shipping_address"] == "on") {
                            echo $_SESSION["visitor_name_shipping"];
                            if ($_SESSION["visitor_company_shipping"] != "") {
                                echo("<br/>" . $_SESSION["visitor_company_shipping"]);
                            }
                            echo "<br />";
                        } elseif ($GLOBALS["shop_customer"]["customer_no"] == "" && $_SESSION["visitor_packstation_address"] == "on") {
                            echo $_SESSION["visitor_name_shipping_packstation"] . "<br />";
                            echo $_SESSION["visitor_company_shipping_packstation"] . "<br />";
                        } else {
                            echo $_SESSION["visitor_name_shipping"];
                            //echo $_SESSION["visitor_company_shipping"]."<br />";
                            if ($_SESSION["visitor_company_shipping"] != "") {
                                echo("<br/>" . $_SESSION["visitor_company_shipping"]);
                            }
                            echo "<br />";
                        }
                        if ($_SESSION["visitor_packstation_address"] == "on") {
                            echo $_SESSION["visitor_user_street_shipping_packstation"] . "<br />";
                            echo $_SESSION["visitor_post_code_shipping_packstation"] . " ";
                            echo $_SESSION["visitor_city_shipping_packstation"] . "<br />";
                        } else {
                            echo $_SESSION["visitor_user_street_shipping"] . " " . $_SESSION["visitor_user_street_no_shipping"] . "<br />";
                            echo $_SESSION["visitor_post_code_shipping"] . " ";
                            echo $_SESSION["visitor_city_shipping"] . "<br />";
                        }
                        echo get_country_by_code($_SESSION["visitor_country_shipping"]);
                    } else {
                        $shipment_address = get_shipment_address($_SESSION["visitor_shipment_address_id"]);
                        $match_found = preg_match('/[0-9]+\s*[a-zA-Z-]*/', $shipment_address["address"], $ship_street_no);
                        echo $shipment_address["name"] . "<br/>";
                        if ($shipment_address["name_2"]) {
                            echo $shipment_address["name_2"] . "<br />";
                        }
                        echo $shipment_address["address"] . "<br />";
                        echo $shipment_address["post_code"];
                        echo $shipment_address["city"] . "<br />";
                        echo get_country_by_code($shipment_address["country"]);
                        $_SESSION['visitor_name_shipping'] = $shipment_address["name"];
                        $_SESSION['visitor_company_shipping'] = $shipment_address["name_2"];
                        $_SESSION['visitor_user_street_shipping'] = trim(str_replace($ship_street_no[0], "", $shipment_address["address"]));
                        $_SESSION["visitor_user_street_no_shipping"] = $ship_street_no[0];
                        $_SESSION['visitor_post_code_shipping'] = $shipment_address["post_code"];
                        $_SESSION['visitor_city_shipping'] = $shipment_address["city"];
                        $_SESSION['visitor_country_shipping'] = $shipment_address["country"];
                    } ?>
                    <br/>
                <? } ?>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <? if ($_SESSION["dc_id"] == '') { ?>
                    <div class="order_box_headline">
                        <h2><?= $GLOBALS["tc"]["ship_type"] ?></h2>
                        <a class="order_box_change_link"
                           href="/<? echo customizeUrl();?>/order/shipment_payment_option_select/"><?= $GLOBALS['tc']['change'] ?></a>
                    </div>
                    <?
                    /** @var \DynCom\dc\dcShop\ShippingOptions\ShippingOptionRepository $shippingOptionsRepository */
                    $shippingOptionsRepository = $IOCContainer->create('$ShippingOptionRepository');
                    /** @var \DynCom\dc\dcShop\classes\CurrShopConfiguration $currShopConfig */
                    $currShopConfig = $IOCContainer->create('$CurrShopConfig');

                    $primary = ['company' => $currShopConfig->getCompany(), 'shipping_group_code' => $currShopConfig->getShop()->getShippingGroupCode(), 'line_no' => $_SESSION['shipping_line_no']];
                    $description = $shippingOptionsRepository->getDescriptionForLanguageByLanguageCodeAndPrimary($currShopConfig->getShopLanguageCode(), $primary);

                    echo $description;
                    ?>
                    <br/>
                <? } ?>
                <div class="order_box_headline">
                    <h2><?= $GLOBALS["tc"]["payment_type"] ?></h2>
                    <a class="order_box_change_link"
                       href="/<? echo customizeUrl();?>/order/shipment_payment_option_select/"><?= $GLOBALS['tc']['change'] ?></a>
                </div>
                <?
                $showBankInfo = false;
                $query = "SELECT *
							  FROM shop_payment_option
							  WHERE line_no = '" . $_SESSION['payment_line_no'] . "'
								AND shop_code ='" . $GLOBALS['shop']['code'] . "'
								AND language_code = '" . $GLOBALS['shop_language']['code'] . "'
								AND company = '" . $GLOBALS['shop']['company'] . "'";
                $result = mysqli_query($GLOBALS['mysql_con'], $query);
                if (mysqli_num_rows($result) > 0) {
                    $terms = mysqli_fetch_assoc($result);
                    echo($terms['description']);
                    if ((int)$terms["checkout"] == 6 || (int)$terms["checkout"] == 7) {
                        $showBankInfo = true;
                    }
                    // Payolution ---
                    if ($terms['checkout'] == '14') {
                        payolution_show_step_3();
                    }
                    // Payolution +++

                } ?>

                <div class="order_box_headline">
                    <h2><?= $GLOBALS["tc"]["newsletter_registration"] ?></h2>
                    <a class="order_box_change_link"
                       href="/<? echo customizeUrl();?>/order/shipment_payment_option_select/"><?= $GLOBALS['tc']['change'] ?></a>
                </div><?
                if ($_POST['newsletter'] != '') {
                    $_SESSION['newsletter_subscribe'] = 1;

                    echo $_SESSION["visitor_email"];
                } else {
                    echo $GLOBALS["tc"]["no"];
                    unset($_SESSION['newsletter_subscribe']);
                }
                echo '<br />';
                // ---

                /*if (isset($_SESSION["visitor_birthday"]) && $_SESSION["visitor_birthday"] != "") {
                    echo("<b>" . $GLOBALS["tc"]["birthday"] . ":</b><br />");

                    echo $_SESSION["visitor_birthday"] . "<br />";


                }*/
                ?>
                <?php if ($_SESSION['your_comment'] != "" || ($_SESSION["bank_no"] <> '' && $showBankInfo) || (isset($_SESSION["visitor_birthday"]) && $_SESSION["visitor_birthday"] != "")) { ?>
                    <h2><?= $GLOBALS["tc"]["other"] ?></h2>
                    <?= ($_SESSION["visitor_birthday"] <> '') ? "<strong>" . $GLOBALS["tc"]["birthday"] . ":</strong> " . $_SESSION["visitor_birthday"] . "<br />" : ''; ?>
                    <?= ($_SESSION["your_comment"] <> '') ? "<strong>" . $GLOBALS["tc"]["your_comment"] . ":</strong> " . $_SESSION["your_comment"] . "<br />" : ''; ?>
                    <? if ($showBankInfo) { ?>
                        <?= ($_SESSION["account_no"] <> '') ? "<strong>" . $GLOBALS["tc"]["account_no"] . ": </strong> " . $_SESSION["account_no"] . "<br />" : ''; ?>
                        <?= ($_SESSION["bank_no"] <> '') ? "<strong>" . $GLOBALS["tc"]["bank_no"] . ": </strong> " . $_SESSION["bank_no"] . "<br />" : ''; ?>
                    <? } ?>
                <?php } else {
                    echo("&nbsp;");
                } ?>
            </div>
            <div class="col-xs-12">
                <hr/>
            </div>
            <? if ($_SESSION["dc_id"] <> '') {
                $query = "SELECT * FROM shop_digital_coupon WHERE id = '" . $_SESSION["dc_id"] . "' LIMIT 1";
                $result = @mysqli_query($GLOBALS['mysql_con'], $query);
                $dc = @mysqli_fetch_assoc($result);
                ?>
                <div class="col-xs-12">
                    <h2><?= $GLOBALS["tc"]["preview"] ?></h2>
                    <div class="dc_order_box">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <? create_card_preview($dc, true, false); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <? create_card_preview($dc, false, true); ?>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <h3><?= $GLOBALS["tc"]["amount"] ?></h3>
                                <?= $dc["amount"]; ?> &euro;
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <h3><?= $GLOBALS["tc"]["shipping"] ?>:</h3>
                                <? show_dc_shipping_option($dc); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <hr/>
                </div>
            <? } else { ?>
                <div class="order-itemlist col-xs-12">
                    <?
                    if (!array_key_exists('in_subscription_order', $GLOBALS) || !$GLOBALS['in_subscription_order']) {

                        $query = "SELECT shop_view_active_item.*, shop_user_basket.item_quantity AS 'basket_quantity', shop_user_basket.customer_price AS 'customer_price'
                                                      FROM shop_user_basket
                                                      INNER JOIN shop_view_active_item ON shop_view_active_item.id = shop_user_basket.shop_item_id
                                                      WHERE shop_user_basket.shop_visitor_id = '" . $GLOBALS["visitor"]["id"] . "'
                                                      ORDER BY shop_user_basket.insert_datetime";

                        //$sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
                        $sales_line_result = $itemArr;
                        /* ---SUBSCRIPTION--- */
                    } elseif (array_key_exists('in_subscription_order', $GLOBALS) && array_key_exists('subscription_item_basket_query', $GLOBALS) && $GLOBALS['in_subscription_order'] && $GLOBALS['subscription_item_basket_query']) {
                        $query = $GLOBALS['subscription_item_basket_query'];
                        $sales_line_result = @mysqli_query($GLOBALS['mysql_con'], $query);
                    }
                    /* +++SUBSCRIPTION+++ */
                    show_item_list($sales_line_result, 5, $formname);
                    ?>
                    <?
                    //Werte für Kostenübersicht suchen
                    if (!array_key_exists('in_subscription_order', $GLOBALS) || !array_key_exists('subscription_item', $GLOBALS) || !is_object($GLOBALS['subscription_item'])) {
                        /*
                        $order_total              = shop_get_basket_amount($GLOBALS['visitor']['id']);
                        $query                    = "SELECT SUM(ub.item_quantity * ub.customer_price) AS total FROM shop_user_basket ub WHERE ub.shop_visitor_id=" . $GLOBALS['visitor']['id'];
                        $row                      = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], $query));
                        $subtotal                 = $row['total'];
                        $_SESSION['intermediate'] = $subtotal;
                        */
                        //MB - OOP - ---
                        $order_total = $currUserBasket->getBasketItemTotal();

                        //--- US SALES TAX ---
                        if (isset($GLOBALS['us_sales_tax_estimate']) && $GLOBALS['us_sales_tax_estimate'] instanceof \DynCom\dc\dcShop\USSalesTax\TaxJar\TaxInformation) {
                            /**
                             * @var $taxInfo \DynCom\dc\dcShop\USSalesTax\TaxJar\TaxInformation
                             */
                            $taxInfo = $GLOBALS['us_sales_tax_estimate'];
                            $order_total += $taxInfo->getAmountToCollect();
                            $order_item_total += $taxInfo->getAmountToCollect();
                        }

                        //+++ US SALES TAX +++

                        $invDiscounts = $currUserBasket->getAppliedInvoiceDiscounts();
                        $coupon_disc_amnt = 0.00;
                        $inv_disc_amnt = 0.00;
                        $online_disc_amnt = 0.00;
                        $rule_disc_amount = 0.00;
                        $rule_disc_percent = 0.00;

                        foreach ($invDiscounts as $invoiceDiscount) {
                            /**
                             * @var $invoiceDiscount \DynCom\dc\dcShop\classes\AppliedDiscount
                             */
                            switch ($invoiceDiscount->getSourceType()) {
                                case \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_SOURCE_TYPE_COUPON:
                                    $coupon_discount_amnt += $invoiceDiscount->getDiscountedAmount();
                                    break;
                                case \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_SOURCE_TYPE_ONLINE_DISCOUNT:
                                    $online_discount_amnt += $invoiceDiscount->getDiscountedAmount();
                                    break;
                                case \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_SOURCE_TYPE_INVOICE_DISCOUNT:
                                    $invoice_discount_amount += $invoiceDiscount->getDiscountedAmount();
                                    break;
                                case \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_SOURCE_TYPE_RULE:
                                    if ($invoiceDiscount->getDiscountValueType() === \DynCom\dc\dcShop\abstracts\DiscountBase::DISCOUNT_VALUE_TYPE_PERCENT) {
                                        $rule_disc_amount = $subtotal * ($invoiceDiscount->getDiscountValue() / 100);
                                    } else {
                                        $rule_disc_amount += $invoiceDiscount->getDiscountedAmount();
                                    }

                                    break;
                            }
                        }
                        if ($invoice_discount_amount) {
                            $invoice_discount = $subtotal / $invoice_discount_amount;
                        }

                        if ($rule_disc_amount) {
                            $rule_disc_percent = $subtotal / $rule_disc_amount;
                        }
                        $_SESSION['rule_discount_amount'] = $rule_disc_amount;
                        $_SESSION['rule_discount_percent'] = $rule_disc_percent;


                        $subtotal = $order_total;


                        //MB - OOP - +++

                        /* ---SUBSCRIPTION--- */
                    } else {
                        $subscriptionItem = $GLOBALS['subscription_item'];
                        if ($subscriptionItem instanceof SubscriptionItemDecorator) {
                            $order_total = $subscriptionItem->getLineAmount();
                            $subtotal = $order_total;
                            $_SESSION['intermediate'] = $subtotal;
                        }
                    }
                    /* +++SUBSCRIPTION+++ */

                    $query = "SELECT small_quantity_charge, small_quantity_charge_limit
					  FROM shop_shop
					  WHERE company = '" . $GLOBALS['shop']['company'] . "'
						AND code = '" . $GLOBALS['shop']['code'] . "'";
                    $row = mysqli_fetch_assoc(mysqli_query($GLOBALS['mysql_con'], $query));
                    if ($order_total < $row['small_quantity_charge_limit']) {
                        //echo("<tr><td style='width:90%; text-align:right;'>Zuschlag f&uuml;r Minderbestellung: </td><td style='width:10%; text-align:right;'>".format_amount($row['small_quantity_charge'])."</td></tr>");
                        $addcost = $row['small_quantity_charge'];
                    } else {
                        $addcost = 0;
                    }
                    $_SESSION['small_quantity'] = $addcost;

                    $value_type = 0;
                    if ($_SESSION['coupon']['value_type']) {
                        $value_type = $_SESSION['coupon']['value_type'];
                    }

                    /*$query = "SELECT IF (" . $value_type . " = 4 AND coupon_shipping_cost >0,
											coupon_shipping_cost,	
											shipping_cost) AS shipping_cost,
							exemption
					 FROM shop_shipping_option
					 WHERE line_no='" . $_SESSION['shipping_line_no'] . "'
					 AND shop_code='" . $GLOBALS['shop']['code'] . "'
					 AND language_code = '" . $GLOBALS['shop_language']['code'] . "'";
                    $result = mysqli_query($GLOBALS['mysql_con'], $query);
                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        if ($row['exemption'] > $order_total || $row['exemption'] == '0') {
                            $shipping_cost = $row['shipping_cost'];
                        } else {
                            $shipping_cost = 0;
                        }
                    }
                    $_SESSION['shipping_cost'] = $shipping_cost;*/

                    $shipping_cost = $_SESSION['shipping_cost'];


                    $order_total += $shipping_cost + $addcost + $_SESSION['payment_cost'];

                    if ($_SESSION['coupon']['category_coupon'] == 1) {
                        $category_item_amount = calculate_category_coupon_items($_SESSION['coupon']['category_line_no']);
                        $cat_coupon_item_total = $category_item_amount;
                        //echo "<!-- CAT ITEM AMOUNT: " . $category_item_amount . " -->";

                    } else {
                        //echo "<!-- NO CAT COUPON -->";
                    }
                    if ($_SESSION['coupon']['percentage'] > 0) {
                        if ($_SESSION['coupon']['category_coupon'] == 1) {
                            $base_val = $cat_coupon_item_total;
                        } else {
                            $base_val = ($coupon_disc_amnt + $order_total) + (float)$_SESSION['coupon']['amnt_disc_non_items'];

                        }
                        $coupon_discount = $_SESSION['coupon']['percentage'];
                        $coupon_discount_amount = round((($coupon_discount / 100) * $base_val), 2);
                    } elseif ($_SESSION['coupon']['amount'] > 0 || $_SESSION['coupon']['amount_left'] > 0) {


                        if ($_SESSION['coupon']['value_coupon'] != 1) {
                            if (($_SESSION['coupon']['amount'] < $order_total) || ($_SESSION['coupon']['amount'] < $cat_coupon_item_total)) {
                                $coupon_discount_amount = $_SESSION['coupon']['amount'];
                            } else {
                                if ($_SESSION['coupon']['category_coupon'] == 1) {
                                    $coupon_discount_amount = $cat_coupon_item_total;
                                } else {
                                    $coupon_discount_amount = $coupon_disc_amnt + (float)$_SESSION['coupon']['amnt_disc_non_items'];
                                }
                            }
                        } else {


                            if (($order_total > $_SESSION['coupon']['amount_deducted']) || ($cat_coupon_item_total > $_SESSION['coupon']['amount_deducted'])) {
                                $coupon_discount_amount = $_SESSION['coupon']['amount_deducted'];

                            } else {
                                if ($_SESSION['coupon']['category_coupon'] == 1) {
                                    $coupon_discount_amount = $category_item_amount;
                                } else {
                                    $coupon_discount_amount = $_SESSION['coupon']['amount_deducted'] + (float)$_SESSION['coupon']['amnt_disc_non_items'];
                                }
                                //$_SESSION['coupon']['amount_left_total'] = $coupon_discount_amount;
                            }
                            $coupon_amount_left = $_SESSION['coupon']['amount'] - $coupon_discount_amount;
                            $_SESSION['coupon']['amount_really_left'] = $coupon_amount_left;

                        }
                    } else {
                        $coupon_discount_amount = 0;
                    }

                    $_SESSION['coupon']['coupon_discount_amount'] = $coupon_discount_amount;


                    if ($currUserBasket instanceof UserBasket) {
                        //$subtotal -= $_SESSION['coupon']['amount_deducted'];
                        $order_total -= ((float)$_SESSION['coupon']['amnt_disc_non_items'] + $online_discount_amount + $invoice_discount_amount + $rule_disc_amount);

                        //$order_item_total -= ((float)$_SESSION['coupon']['amnt_disc_non_items'] + $online_discount_amount + $invoice_discount_amount);
                    } else {
                        $order_total -= ($coupon_discount_amount + $online_discount_amount + $invoice_discount_amount + $rule_disc_amount);
                        //$order_item_total -= ($coupon_discount_amount + $online_discount_amount + $invoice_discount_amount);
                    }
                    $_SESSION['total_basket'] = $order_total;
                    ?>
                    <div class="order_prices_box" style="overflow: hidden;">
                        <div class="order_prices_box_left">
                            <? if ($GLOBALS['shop_language']['newsletter_registration_text_module'] != '') { ?>
                                <h2><?= $GLOBALS["tc"]["newsletter"] ?></h2>
                                <?
                                ob_start();
                                input_shop($GLOBALS['tc']['newsletter_registration'], "input_newsletter_checked", "checkbox", "", 50, FALSE, FALSE);
                                $spacer['%checkbox%'] = ob_get_contents();
                                ob_end_clean();
                                $newsletter_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['newsletter_registration_text_module'], $spacer);
                                echo $newsletter_text;
                                ?>
                            <? } ?>
                            <? if ($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
                                <? if ($_GET["action"] == "complete_order" & $_POST['input_agb_checked'] == "") {
                                    get_requestbox($GLOBALS['tc']['confirm_agb']);
                                }
                                $agb_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['checkout_confirmation_text_module']);

                                if (substr_count($agb_text, '%checkbox%') == 0) {
                                    ob_start();
                                    input_shop($GLOBALS['tc']['agb_confirmed'], "input_agb_checked", "checkbox_action", "", 50, FALSE, FALSE);
                                    $spacer['%checkbox%'] = ob_get_contents();
                                    ob_end_clean();
                                    $agb_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['checkout_confirmation_text_module'], $spacer);
                                } else {
                                    ob_start();
                                    input_shop('%checkbox_text%', "input_agb_checked", "checkbox_action", "", 50, FALSE, FALSE);
                                    $spacer['%checkbox%'] = ob_get_contents();
                                    ob_end_clean();
                                    $agb_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['checkout_confirmation_text_module'], array('%checkbox%' => ''));
                                    $agb_text = str_replace('%checkbox_text%', $agb_text, $spacer['%checkbox%']);
                                }
                                echo $agb_text;

                                ?>
                            <? } else { ?>
                                <input type="hidden" name="input_agb_checked" id="input_agb_checked" value="on"/>
                            <? } ?>
                        </div>
                        <div class="order_prices_box_right">
                            <?php
                            if (is_resource($sales_line_result)) {
                                mysqli_data_seek($sales_line_result, 0);
                            }


                            if ($subtotal < 0) {
                                $subtotal = 0;
                            }
                            show_order_sum(
                                $subtotal,
                                $online_discount,
                                $online_discount_amount,
                                $invoice_discount,
                                $invoice_discount_amount,
                                $addcost,
                                $order_total,
                                $_SESSION['shipping_cost'],
                                $_SESSION['payment_cost'],
                                TRUE,
                                $sales_line_result,
                                $rule_disc_percent,
                                $rule_disc_amount);
                            ?>
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>

        <?
        if ($terms['checkout'] == '2' || $terms['checkout'] == '3' || $terms['checkout'] == '4' || $terms['checkout'] == '5') {
            $_SESSION['site_code'] = $GLOBALS['site']['code'];
            $_SESSION['language_code'] = $GLOBALS['language']['code'];
            $button_text = $GLOBALS['tc']['buy'];
        } else {
            $button_text = $GLOBALS['tc']['buy'];
        }
        ?>

        <div class="button_row">
            <div class="pull-right">
                <? if (($terms['checkout'] == '11')
                    && (!isset($_SESSION['amazon_PayID']) || $_SESSION['amazon_PayID'] == ''
                        || !isset($_SESSION['amazon_order_reference_id']) || $_SESSION['amazon_order_reference_id'] == '')
                ) {
                    create_amazon_payment_button();
                } else {
                    ?>
                    <a class='button_order button button_action' href="javascript:void(0);"
                       onclick="pay_other()"><?= $button_text ?></a>
                    <?
                } ?>

            </div>
            <div class="pull-left">
                <a class="order button"
                   href='/<? echo customizeUrl(); ?>/order/shipment_payment_option_select/'><?= $GLOBALS["tc"]["back"] ?></a>
            </div>
        </div>
    </form>
    <script type="text/javascript">

        function pay_other() {
            <? if($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            if (typeof document.form_user_order_3.input_agb_checked !== "undefined") {
                if (document.form_user_order_3.input_agb_checked.checked == false) {

                    alert("Bitte bestätigen Sie die AGB um fortzufahren");
                    document.forms[0].input_agb_checked.focus();

                } else {
                    var str = "<? echo "/" . customizeUrl() . "/order/payment/"; ?>"
                    str = str.replace(/\&amp;/g, '&');
                    document.form_user_order_3.action = '' + str + '';
                    document.form_user_order_3.submit();
                }
            } else {
                <? } ?>
                var str = "<? echo "/" . customizeUrl() . "/order/payment/"; ?>";
                str = str.replace(/\&amp;/g, '&');
                document.form_user_order_3.action = '' + str + '';
                document.form_user_order_3.submit();
                <? if($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            }
            <? } ?>
        }
    </script>

    <?
}
?>