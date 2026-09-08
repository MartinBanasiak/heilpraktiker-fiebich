<?
use DynCom\dc\dcShop\subscriptions\classes\SubscriptionItemDecorator;

$formname = "form_user_order_2";
if (isset($_POST["input_dispatch_type"])) {
    session_save_data_b2c("step2");
}

if ( $GLOBALS['visitor']['frontend_login'] && $GLOBALS["shop_customer"]["customer_no"] !="" && $_POST["input_surname_shipping"] != "") {
    $name_combined = $_POST["input_surname_shipping"] . ' ' . $_POST["input_lastname_shipping"];
    $address_combined = $_POST["input_user_street_shipping"] . ' ' . $_POST['input_user_street_no_shipping'];
    $query = "
      INSERT INTO 
        shop_shipment_address 
      SET
        id              = NULL,
        company         = '{$GLOBALS['shop']['company']}',
        customer_no     = '{$GLOBALS["shop_customer"]["customer_no"]}',
        `name`          = '{$name_combined}',
        name_2          = '{$_POST['input_company_shipping']}',
        address         = '{$address_combined}',
        address_street  = '{$_POST['input_user_street_shipping']}',
        address_no      = '{$_POST['input_user_street_no_shipping']}',
        post_code       = '{$_POST["input_post_code_shipping"]}',
        city            = '{$_POST["input_city_shipping"]}',
        country         = '{$_POST["input_shop_country_shipping"]}',
        surname         = '{$_POST["input_surname_shipping"]}',
        lastname        = '{$_POST["input_lastname_shipping"]}'
    ";
    if (($_POST["input_surname_shipping"] == '') | ($_POST["input_user_street_shipping"] == '') | ($_POST["input_city_shipping"] == '') | ($_POST["input_post_code_shipping"] == '')) {
        $error = TRUE;
    }
    $query_2 = "SELECT * FROM shop_shipment_address WHERE customer_no = '" . $GLOBALS["shop_customer"]["customer_no"] . "' AND address = '" . $address_combined . "' AND post_code = '" . $_POST["input_post_code_shipping"] . "'";
    $result  = mysqli_query($GLOBALS['mysql_con'], $query_2);
    if (mysqli_num_rows($result) == 0) {
    } else {
        $double = TRUE;
    }
    if (!$error && !$double) {
        @mysqli_query($GLOBALS['mysql_con'], $query);
    }

}

$visitor_data = get_main_visitor_data_b2c("step2");

if (check_mandatory_fields_b2c($_POST, "step2")) {
    $step = "step3";
} else {
    $step = "step3";
}
$_SESSION["order_previous_step"] = "step2";
if ($_GET['payment_error'] == 1) {
    get_requestbox(get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['payment_error_text_module'], array()),"");

    /*<div
        class="errorbox"><?= get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['payment_error_text_module'], array()) ?></div>
    <br />
    <?*/
    $_GET['payment_error'] = 0;
}
?>
<ul class="processbar">
    <li class="processbar_item done"><?= $GLOBALS["tc"]["order_bar_address"] ?><div class="arrow"></div><div class="arrow-border"></div></li>
    <li class="processbar_item active"><?= $GLOBALS["tc"]["order_bar_payment"] ?><div class="arrow"></div><div class="arrow-border"></div></li>
    <li class="processbar_item"><?= $GLOBALS["tc"]["order_bar_check"] ?></li>
</ul>
<?
if ($GLOBALS['shop_language']['order_step_2_text_module'] != '') {
    $spacer = array();
    echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_step_2_text_module'], $spacer));
}

if (!$creditworthiness) {
    get_requestbox($GLOBALS["tc"]["creditworthiness_error"]);
}

$payolution_field_error = False;
if ($fielderror_1) {
    add_order_error_msg($GLOBALS["tc"]["mandatory_fields_error"]);
    $fielderror_1 = FALSE;
    $payolution_field_error = True;
}

$errormsg = "";
foreach (get_all_order_error_msg() as $error_msg) {
    $errormsg .= ($errormsg == "" ? '' : '<br>') . $error_msg;
}

if ($errormsg != "") {
    get_requestbox($errormsg);
}

?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" action='/<? echo customizeUrl();?>/order/shipment_payment_option_select/'>
    <? /* ---SUBSCRIPTION--- */?>
    <input type="hidden" name="subscription_item_id" value="<?php echo urlencode(filter_var($_POST['subscription_item_id'],FILTER_SANITIZE_NUMBER_INT));?>"/>
    <input type="hidden" name="subscription_item_var_code" value="<?php echo urlencode(filter_var($_POST['subscription_item_var_code'],FILTER_SANITIZE_STRING));?>"/>
    <input type="hidden" name="subscription_item_qty" value="<?php echo urlencode(filter_var($_POST['subscription_item_qty'],FILTER_SANITIZE_NUMBER_FLOAT));?>"/>
    <input type="hidden" name="subscription_header_id" value="<?php echo urlencode(filter_var($_POST['subscription_header_id'],FILTER_SANITIZE_NUMBER_INT));?>"/>
    <?/* +++ SUBSCRIPTION +++ */?>
    <div class="row">
        <?

        $isSubscriptionOrder = false;
        if (!array_key_exists('in_subscription_order',$GLOBALS) || !$GLOBALS['in_subscription_order']) {

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

            $locale = $GLOBALS['language']['locale_code'];
            $textProvider = new \DynCom\dc\regionalization\PHPFileRegionalizedTextProvider($locale);
            $shippingOptionController = new \DynCom\dc\dcShop\ShippingOptions\ShippingOptionController($shippingOptionRepository, $textProvider, $locale);

            $return = $shippingOptionController->getShippingOptions($currShopConfiguration, $currUserBasket, $_SESSION["visitor_country_shipping"], $_SESSION["visitor_post_code_shipping"], $_SESSION['coupon']);
            echo $return;
        } else {
            $isSubscriptionOrder = true;
            $subscriptionItem = $GLOBALS['subscription_item'];
            if($subscriptionItem instanceof SubscriptionItemDecorator) {
                $shipLineNo = $subscriptionItem->getSubscriptionShippingOptionLineNo();
                ?>
                <input type="hidden" name="input_shipping_line_no" value="<?= $shipLineNo ?>"/>
                <?
            }
        }

        if (get_available_payment_terms($_SESSION["visitor_country"], $_SESSION["dc_id"], $isSubscriptionOrder, $currUserBasket)) {?>
            <? if ($GLOBALS["shop"]["order_options_display"] == 0) { ?>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                    <br/>
                    <? /* <div class="order_devision_headline"><?=$GLOBALS['tc']['payment_type']?></div> */ ?>
                    <? payment_terms_select($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_SESSION["payment_line_no"], FALSE, TRUE, $_SESSION["visitor_country"], $isSubscriptionOrder) ?>
                </div>
                <div class="clearfix"></div>
            <? } else { ?>
                <div class="col-xs-12 order_option_list">
                        <hr/>
                        <? /* <div class="order_devision_headline"><?=$GLOBALS['tc']['payment_type']?></div> */ ?>
                        <? payment_terms_list($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_SESSION["payment_line_no"], FALSE, TRUE, $_SESSION["visitor_country"], $isSubscriptionOrder) ?>
                </div>
            <? } ?>
            <?
            //Felder für Kontodaten / Bonitätsprüfung einblenden
            $termquery = "SELECT *
              FROM shop_payment_option
              WHERE 
                    active = 1
                AND
                    line_no = '" . $_SESSION['payment_line_no'] . "'
                AND 
                    shop_code = '" . $GLOBALS['shop']['code'] . "'
                AND 
                    language_code ='" . $GLOBALS['shop_language']['code'] . "'";
            $result    = mysqli_query($GLOBALS['mysql_con'], $termquery);
            if (mysqli_num_rows($result) > 0) {
                $terms = mysqli_fetch_assoc($result);

                $specialPaymentText = '';
                if ($terms['billpay_agb_text_module'] != '') {
                    $specialPaymentText = get_text_module($GLOBALS['shop_language']['company'], $terms['payment_text_module']);
                }

                switch ($terms["checkout"]) {
                    // Lastschrift - eigenes Risiko
                    case "1":
                        ?>
                        <div class="col-xs-12">
                            <div class="order_devision_headline"><?=$GLOBALS['tc']['bank']?></div>
                            <? input_shop($GLOBALS["tc"]["account_no"], "input_account_no", "text", $visitor_data["input_account_no"], 11) ?>
                            <? input_shop($GLOBALS["tc"]["bank_no"], "input_bank_no", "text", $visitor_data["input_bank_no"], 34) ?>
                        </div>
                        <?
                        break;
                    // Rechnung Billpay
                    case "6":
                        // Datepicker für Geburtsdatum einblenden
                        ?>
                        <div class="clearfloat"></div>
                        <div class="col-xs-12 col-sm-6 col-sm-offset-6">
                            <div class="order_box">
                                <div class="order_devision_headline"><?=$GLOBALS['tc']['bank_billpay_2']?></div>
                                <?= $GLOBALS["tc"]["bank_billpay_subline"] ?>
                                <? input_shop($GLOBALS["tc"]["birthday"], 'input_birthday', 'date', $_SESSION["visitor_birthday"]); ?>
                                <? input_shop($GLOBALS["tc"]["billpay_agb_accepted"], 'input_billpay_agb', 'checkbox', $visitor_data["input_billpay_agb"]); ?>
                            </div>
                        </div>
                        <?
                        break;
                    // Lastschrift Billpay
                    case "7":
                        // Geburtsdatum, Kontodaten, AGB einblenden
                        ?>
                        <div class="clearfloat"></div>
                        <div class="col-xs-12 col-sm-6 col-sm-offset-6">
                            <div class="order_box">
                                <div class="order_devision_headline"><?=$GLOBALS['tc']['bank_billpay']?></div>
                                <?= $GLOBALS["tc"]["bank_billpay_subline"] ?>
                                <? input_shop($GLOBALS["tc"]["account_no"], "input_account_no", "text", $_SESSION["account_no"], 11) ?>
                                <? input_shop($GLOBALS["tc"]["bank_no"], "input_bank_no", "text", $_SESSION["bank_no"], 34) ?>
                                <? input_shop($GLOBALS["tc"]["birthday"], 'input_birthday', 'date', $_SESSION["visitor_birthday"]); ?>
                                <? input_shop($GLOBALS["tc"]["billpay_agb_accepted"], 'input_billpay_agb', 'checkbox', $_SESSION["input_billpay_agb"]); ?>
                            </div>
                        </div>
                        <?
                        break;
                    // Ratenkauf Billpay
                    case "8":
                        break;
                        // Payolution ---
                    case "17": // Rechnung Payolution
                        echo "<div class='col-xs-12'>";
                        payolution_show_step_2_inputs($payolution_field_error, "INVOICE");
                        echo "</div>";
                        break;
                    case "18": // Ratenkauf Payolution
                        $payolution_total_amount = basketTotal() + get_shipping_cost( basketTotal() ) + $_SESSION['payment_cost'];
                        $response = payolution_calculation_or_precheck("INSTALLMENT", "CL", $invoice_address, $payolution_total_amount, "");
                        $_SESSION["payolution"]["cl_reference"] = $response["reference"];
                        echo "<div class='col-xs-12'>";
                        payolution_show_step_2_inputs($payolution_field_error, "INSTALLMENT", $response);
                        echo "</div>";
                        $GLOBALS["payolution"]["installment_cl_response"] = $response;
                        break;
                    // Payolution +++
                }
                echo "<div>" . $specialPaymentText . "</div>";
            }
            ?>
        <? } ?>
        <div class="col-xs-12">
            <hr/>
        </div>

        <?php
        $isPayolution = false;
        if (in_array($terms["checkout"], array("17", "18") )) {
            $isPayolution = true;
        }
        ?>

        <? if (!$_SESSION["dc_id"] && !$isPayolution) { ?>
            <div class="clearfloat"></div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                <div class="order_devision_headline"><?=$GLOBALS['tc']['other']?></div>
                <? //SH: 20.08.13 digitaler Gutscheinversand +++
                if (!$_SESSION["dc_id"]) {
                    input_shop($GLOBALS["tc"]["coupon_code"], "input_coupon_code", "text", $_SESSION['coupon']['coupon_code'], 30);
                } ?>

                <? ((!isset($_SESSION['coupon']['coupon_code']))) ? button('gift button', $GLOBALS['tc']['use_coupon'], $formname, '?doaction=coupon') : button('delete button', 'Gutschein entfernen', $formname, '?doaction=coupon_delete') ?>
            </div>
            <div class="col-xs-12">
                <hr/>
            </div>
        <? }?>
        <div class="col-xs-12">
            <div class="order_devision_headline"><?=$GLOBALS['tc']['newsletter']?></div>
            <? input_shop($GLOBALS["tc"]["newsletter_registration"], "newsletter", "checkbox", $_SESSION['newsletter_subscribe']); ?>
        </div>
    </div>
    <div class="button_row">
        <div class="pull-right">
            <a class="button_order button button_action" href="javascript:void(0);" onclick="document.form_user_order_2.action='/<? echo customizeUrl();?>/order/buy/'; document.form_user_order_2.submit(); return false;"><?= $GLOBALS["tc"]["next_step"] ?></a>
        </div>
        <div class="pull-left">
            <a class="button_order button" href="javascript:void(0);" onclick="document.form_user_order_2.action='/<? echo customizeUrl();?>/order/address_select/'; document.form_user_order_2.submit(); return false;"><?= $GLOBALS["tc"]["back"] ?></a>
        </div>
    </div>
</form>
<script type="text/javascript">
    function kontrolle() {
        var x = document.form_user_order_2.input_your_comment.value;
        if (x.length > 250) {
            x = x.substring(0, 250);
        }
        document.form_user_order_2.input_your_comment.value = x;
    }

</script>
