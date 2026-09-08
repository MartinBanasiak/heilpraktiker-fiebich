<?php
/**
 * Created by PhpStorm.
 * User: Grosch
 * Date: 25.01.2017
 * Time: 11:44
 */
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'plugins/paygate/computop_functions.inc.php';



/**
 *
 * Creates a Request to the paygate using the given parameters and the GLOBAL/SESSION variables
 *
 * $paytype:
 *      INSTALLMENT -> Ratenkauf
 *      INVOICE -> Rechnungskauf
 *
 * $eventtoken:
 *      CL -> Calculation
 *      PC -> PreCheck
 *      PA -> PreAuthorization
 *
 * $amount:
 *      eg: 19.23 or 19.5500 or 2124.99
 *
 * $order_no:
 *      optional
 */
function payolution_calculation_or_precheck($paytype, $eventtoken, $invoice_address, $amount, $order_no = "", $trans_id = "") {

    // set the function relevant variables from session or globals

    // set the session of the payment_id to "", when its the first request
    $payid = ($_SESSION["payolution"]["payment_id"])?:"";
    if ($eventtoken == "CL" || ($eventtoken=="PC" && $paytype=="INVOICE") ) {
        $payid = "";
    }

    //$trans_id = ($_SESSION['trans_id'] <> '') ? $_SESSION['trans_id'] : generate_trans_id(); // defined in shop_function
    if ($trans_id == "") {
        $trans_id = get_payment_transaction_id(); // defined in shop_function
    }
    
    $birthday = ($_POST["input_birthday"] <> '') ? $_POST["input_birthday"] : $_SESSION["visitor_birthday"];

    $merchant_id = $GLOBALS['shop']['computop_merchant_id'];
    $countrycode = strtolower($invoice_address["country"]);
    if ($countrycode=="") {
        $countrycode = strtolower($GLOBALS['shop_language']['default_country_code']);
    }

    $amount = round($amount * 100 ,0); // format amount from euro to cent

    $gender = "F";
    if($_SESSION["visitor_salutation"] == "Herr") {
        $gender = "M";
    }



    // Fill payolution request array
    $payolution_array = array();

    $payolution_array[] = "MerchantID=".$GLOBALS['shop']['computop_merchant_id'];
    $payolution_array[] = "TransID=".$trans_id;
    $payolution_array[] = "Amount=".$amount;
    $payolution_array[] = "Currency=EUR";
    //$payolution_array[] = "Reason="; // TODO
    $payolution_array[] = "PayType=".$paytype;

    if ($order_no) {
        $payolution_array[] = "RefNr=".$order_no;
        $payolution_array[] = "OrderID=".$order_no;
    }

    $payolution_array[] = "EventToken=".$eventtoken;

    // Ratenkauf spezifische Parameter
    if ($paytype == "INSTALLMENT" && $eventtoken == "PC") {
        $payolution_array[] = "InstallmentNumber=".$_SESSION["payolution"]["installment_duration"];
    }

    // pflicht wenn eventtoken=PC
    if ($eventtoken == "PC") {
        $payolution_array[] = "FirstName=".$_SESSION["visitor_surname"];
        $payolution_array[] = "LastName=".$_SESSION["visitor_lastname"];
        $payolution_array[] = "BirthDate=" . get_payolution_converted_date($birthday); // definition in shop_functions
        $payolution_array[] = "Gender=".$gender;
        $payolution_array[] = "AddrCountryCode=".strtoupper($countrycode); // optional bei eventtoken=CL
        $payolution_array[] = "AddrCity=".$invoice_address["city"];
        $payolution_array[] = "AddrZip=".$invoice_address["post_code"];
        $payolution_array[] = "AddrStreet=".$invoice_address["address_street"]. " ".$invoice_address["address_no"];
        $payolution_array[] = "Email=".$invoice_address["email"];
        $payolution_array[] = "IPAddr=" . $_SERVER["REMOTE_ADDR"];

        if ($paytype == "INSTALLMENT") {
            $payolution_array[] = "PayID=" . $payid;
            if ($_SESSION["payolution"]["cl_reference"] <> '') {
                $payolution_array[] = "Reference=".$_SESSION["payolution"]["cl_reference"]; // set after the CL Request
            }
        }

        if ($countrycode=="nl") {
            $payolution_array[] = "Phone=" ; // Pflicht bei AddrCountryCode <nl>
        }
    }



    if ($eventtoken == "PA") {
        $payolution_array[] = "PayID=" . $payid;

        if ( $paytype == "INSTALLMENT" || $paytype == "DIRECTDEBIT" ) {
            $payolution_array[] = "CountryCode=".strtoupper($countrycode);
            $payolution_array[] = "HolderName=".$invoice_address["name"]; // Name des Kontoinhabers


            if ( in_array($countrycode, array("de", "at")) ) {
                $payolution_array[] = "IBAN=".$_SESSION["account_no"];
                $payolution_array[] = "BIC=".$_SESSION["bank_no"];
            }
        }

    }



    // Generate MAC and Parameter string
    $payolution_array[] = "MAC=".computop_get_mac($amount, "EUR", $merchant_id, $GLOBALS['shop']['computop_hmac_key'], $trans_id, $payid);

    // Log the unencrypted string that is going to be send to the paygate
    computop_payment_log("payolution", "Send to paygate (".$paytype."/".$eventtoken.")", $payolution_array);

    // Send the parameter to the paygate
    $parameter = computop_encrypt_array($payolution_array, $amount, $merchant_id, $GLOBALS['shop']['computop_password']);
    $html = file_get_contents('https://www.computop-paygate.com/payolution.aspx' . $parameter);

    // Log the unencrypted response from the paygate
    //computop_payment_log("payolution", "Response from paygate: ".$html);

    // decrypt the response from the paygate and log it
    $response = computop_decrypt_response_from_content($GLOBALS["shop"]["computop_password"], $html);
    computop_payment_log("payolution", "Decrypted Response", $response);

    if ($response["Code"] == "20500077") {
        computop_payment_log("payolution", "FAILED! Merchant is not allowed to use this paymethod. Contact Computop!");
    }
    if ( isset($response["PayID"]) ) {
        $pay_id = $response["PayID"];
        if ($pay_id == "00000000000000000000000000000000") {
            $_SESSION["payolution"]["payment_id"] = "";
        } else {
            $_SESSION["payolution"]["payment_id"] = $pay_id;
        }
    }

    return $response;
}




/**
 * Sends the $type the the Paygate
 *
 * For Capture and Reverse:
 *  Only available with INVOICE. With INSTALLMENT there is an automatic booking
 *  (autocapture) and no reverse is possible.
 *
 * @param $type
 *      0 -> Capture
 *      1 -> Reverse
 *      2 -> Credit
 * @param $shop -> shop_shop array OR $_GLOBALS["shop"]
 * @param $pay_id
 * @param $trans_id
 * @param $ref_nr
 * @param $amount
 * @param string $currency
 */
function payolution_navconnect($type, $shop, $pay_id, $trans_id, $ref_nr, $amount, $currency = "EUR") {

    $suffix = array("capture.aspx", "reverse.aspx", "credit.aspx");
    $payolution_array = array();

    $amount = round($amount * 100 ,0);

    $payolution_array[] = "MerchantID=".$shop['computop_merchant_id'];
    $payolution_array[] = "PayID=".$pay_id;
    $payolution_array[] = "TransID=".$trans_id;
    $payolution_array[] = "RefNr=".$ref_nr;
    $payolution_array[] = "Amount=".$amount;
    $payolution_array[] = "Currency=".$currency;

    $payolution_array[] = "MAC=".computop_get_mac($amount, "EUR", $shop['computop_merchant_id'], $shop['computop_hmac_key'], $trans_id, $pay_id);
    computop_payment_log("payolution", "Navconnect (".$suffix[$type].")", $payolution_array);
    $parameter = computop_encrypt_array($payolution_array, $amount, $shop['computop_merchant_id'], $shop['computop_password']);

    $link = "https://www.computop-paygate.com/". $suffix[$type] . $parameter;
    computop_payment_log("payolution", "Navconnect (".$suffix[$type].") encrypted: " . $link);
    $html = file_get_contents($link);
    $data_decrypted = computop_decrypt_response_from_content($shop["computop_password"], $html);
    return $data_decrypted;

}

/**
 * Show the inputs for user_order_step_2
 * $paytype:
 *   INSTALLMENT -> Ratenkauf
 *   INVOICE -> Rechnungskauf
 * account no = BIC während bank_no = IBAN ist
 */
function payolution_show_step_2_inputs($field_error = False, $paytype = "INSTALLMENT", $response = "") {
    $headline = ($paytype == "INSTALLMENT") ? $GLOBALS["tc"]["payolution_headline_installment"] : $GLOBALS["tc"]["payolution_headline_invoice"];

    $evaluate_agb = false;
    $evaluate_birthday = false;
    if ($_GET["action"] == "step3") {
        $evaluate_agb = ($_SESSION["input_payolution_agb"] <> '') ? false : true;
        $evaluate_birthday = ($_SESSION["visitor_birthday"] <> '') ? false : true;
    }
    $value_birthday = (isset($_POST["input_birthday"])) ? $_POST["input_birthday"] : $_SESSION["visitor_birthday"];
    $value_payolution_agb = (isset($_POST["input_payolution_agb"])) ? $_POST["input_payolution_agb"] : $_SESSION["input_payolution_agb"];

    ?>
    <div class="cardform order_2">
        <div id="cardform_headline">
            <h2><?= $headline ?></h2>
        </div>
        <div>
            <?

            if ($response["Status"] == "FAILED") {
                echo "<p>".$GLOBALS['tc']['payolution_error_1']."</p>";
            } else {
                echo "<div class='row'><div class='col-xs-12 col-sm-6 col-md-4'>";
                if ($paytype == "INSTALLMENT") { // ratenkauf

                    echo "<div".$GLOBALS['tc']['payolution_installment_fee_hint']."</div>";
                    
                    // iban ist 34 stellen lang, bic 11
                    input_shop($GLOBALS["tc"]["iban"]."*", "input_account_no", "text", $_SESSION["account_no"], 34, False, $field_error);

                    input_shop($GLOBALS["tc"]["bic"]."*", "input_bank_no", "text", $_SESSION["bank_no"], 11, False, $field_error);

                    input_shop($GLOBALS["tc"]["birthday"]."*", 'input_birthday', 'date', $_SESSION["visitor_birthday"]);


                    $paymentdetails = get_paymentdetails_from_string($response["paymentdetails"]);
                    show_installment_plan($paymentdetails);
                } else { // rechnung

                    input_shop($GLOBALS["tc"]["birthday"]."*", 'input_birthday', 'date', $value_birthday, NULL, false, $evaluate_birthday);

                }

                input_shop($GLOBALS["tc"]["payolution_agb_accepted"]."*", 'input_payolution_agb', 'checkbox', $value_payolution_agb, NULL, false, $evaluate_agb);

                echo '<div>'.$GLOBALS["tc"]["payolution_agb"].'</div>';

                echo "</div></div>";

            }
            ?>
        </div>
    </div>

    <?
}

/**
 * Check the payolution fields on validity
 * Used in check_mandatory_fields_b2c()
 * For used functions inside, see shop_functions.
 * 
 * @param string $paytype INVOICE|INSTALLMENT
 * @param array $post_var
 * @return bool
 */
function payolution_check_mandatory_fields($paytype = "INVOICE", $post_var) {
    $ret_val = TRUE;
    if ($post_var["input_payolution_agb"] <> 'on') {
        $ret_val = FALSE;
        add_order_error_msg($GLOBALS['tc']['confirm_agb']);
    }
    if ($post_var["input_birthday"] == "") {
        $ret_val = FALSE;
    } else {
        if (is_valid_date($post_var['input_birthday'])) {
            if (!check_age($post_var['input_birthday'], 18)) {
                add_order_error_msg($GLOBALS['tc']['payolution_error_birthday_age']);
                $ret_val = FALSE;
            }
        } else {
            add_order_error_msg($GLOBALS['tc']['payolution_error_birthday_age_format']);
            $ret_val = FALSE;
        }

    }

    if ($paytype == "INSTALLMENT") { // Installment / Ratenkauf specific
        if ($post_var["input_account_no"] == "") {
            $ret_val = FALSE;
        } elseif ($post_var["input_bank_no"] == "") {
            $ret_val = FALSE;
        } elseif ($post_var["input_payolution_agb"] <> 'on') {
            $ret_val = FALSE;
        }
    }
    return $ret_val;
}


/**
 * Shows the Infoblock for the Payment with Payolution on order_step_3
 */
function payolution_show_step_3() {
    ?>
    <br />
    <?= ($_SESSION["account_no"] <> '') ? "<strong>" . $GLOBALS["tc"]["bic"] . ": </strong>" . $_SESSION["account_no"] : ''; ?>
    <br />
    <?= ($_SESSION["bank_no"] <> '') ? "<strong>" . $GLOBALS["tc"]["iban"] . ": </strong>" . $_SESSION["bank_no"] : ''; ?>
    <br />
    <?
    echo "<strong>".$GLOBALS["tc"]["payolution_minimum_installment_duration"].":</strong> ".$_SESSION["payolution"]["installment_duration"]."<br/>";
}

/**
 * Returns the paymentDetails of the installment response in an ordered array
 * 
 * @param $string
 * @return mixed
 */
function get_paymentdetails_from_string($string) {
    $paymentdetails = array();

    $raten = explode("+", $string);

    foreach ($raten as $rate) {
        $detail = array();
        $tmp = explode(";", $rate);

        // splitting and name of the keys based on the computop documentation of this response field
        $detail["Originalbetrag"] = array_shift($tmp);
        $detail["Gesamtbetrag"] = array_shift($tmp);
        $detail["MinimumRatenbetrag"] = array_shift($tmp);
        $detail["Ratendauer"] = array_shift($tmp);
        $detail["Zinssatz"] = array_shift($tmp);
        $detail["effZinssatz"] = array_shift($tmp);
        $detail["Zahlungsgrund"] = array_shift($tmp);
        $detail["Währung"] = array_shift($tmp);
        $detail["CreditInfoUrl"] = urldecode( array_shift($tmp) );

        do {
            $detail["Zahlungsbetrag"][] = array_shift($tmp);
            $detail["Zahlungsdatum"][] = array_shift($tmp);
        } while ( count($tmp) > 0);


        $paymentdetails[] = $detail;

    }

    //$paymentdetails = explode(";", $string);

    return $paymentdetails;

    
}


/**
 * Shows the installmentplan based on the given paymentdetails
 * @param $paymentdetails
 */
function show_installment_plan($paymentdetails) {

    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $('#input_installment_select').on("change", function (){
                $('.installment_conditions').hide();
                $('#installment_conditions_'+$(this).val() ).show();
            });
        });
    </script>

    <div class="label">
        <label for="input_installment_select"><?= $GLOBALS["tc"]["payolution_minimum_installment_duration"] ?></label>
    </div>
    <div class="input select_body">
        <select class="select_2" name="input_installment_select" id="input_installment_select">
            <?
            foreach ($paymentdetails as $rate) {
                $key = $rate["Ratendauer"];
                $value = $rate["Ratendauer"]." ".$GLOBALS['tc']['months'];
                ?>
                <option value="<?=$key?>"><?=$value?></option>
                <?
            }
            ?>
        </select>
    </div>

    <?


    ?>
    <div class="label text"><?= $GLOBALS['tc']['conditions'] ?></div>

    <?
    $firstVisible = true;
    foreach ($paymentdetails as $rate) {

        $months = $rate["Ratendauer"];

        // shows the first block
        $hidden = ($firstVisible) ? "block" : "none" ;
        $firstVisible = false;
        ?>
        <div id="installment_conditions_<?=$months?>" class="input installment_conditions" style="height:initial;display: <?=$hidden?>">

            <?
            $installment_rate = number_format($rate["Zinssatz"], 2, ',', '.')." %";
            $installment_rate_2 = number_format($rate["effZinssatz"], 2, ',', '.')." %";
            $months_text = $months." ".$GLOBALS['tc']['months'];
            //$creditinfo = "<a href=".$rate["CreditInfoUrl"]." target='_blank' >".$GLOBALS['tc']['payolution_installment_download']."</a>";
            $href = array();
            $href[] = "/module/mysydeshop/b2c/ajax.php?";
            $href[] = "function=payolution_installment_plan";
            $href[] = "months=".$months;
            $href[] = "url=".urlencode($rate["CreditInfoUrl"]);
            $creditinfo = "<a href='".implode("&", $href)."' target='_blank' >".$GLOBALS['tc']['payolution_installment_download']."</a>";
            ?>

            <strong>Gesamtbetrag:</strong> <?=format_amount($rate["Gesamtbetrag"])?><br/>
            <strong><?= $GLOBALS['tc']['duration'] ?></strong> <?=$months_text?><br/>
            <strong><?= $GLOBALS['tc']['payolution_installment_rate'] ?>: </strong> <?=$installment_rate?><br/>
            <strong><?= $GLOBALS['tc']['payolution_installment_rate_2'] ?>: </strong> <?=$installment_rate_2?><br/>
            <strong><?= $GLOBALS['tc']['payolution_installment_creditinfo'] ?>: </strong> <?=$creditinfo?><br/>

            <br><strong>Geplante Zahlungen: </strong><br/>
            <?
            foreach ($rate["Zahlungsbetrag"] as $number => $amount) {
                $right = datefromsql($rate["Zahlungsdatum"][$number]);
                $amount = format_amount( $rate["Zahlungsbetrag"][$number] );
                ?>
                <strong><?=  $right ?></strong> <?= $amount ?><br/>
                <?
            }
            ?>



        </div>
        <?


    }

    // calculate the payment_costs (originalbetrag - gesamtbetrag)
    //$_SESSION['payment_cost'] = $paymentdetails[1] - $paymentdetails[0];

}


/**
 * @param $datestring
 * @return bool
 */
function is_valid_date($datestring) {
    $pattern = '/^\d{2}\.\d{2}\.\d{4}';
    if (preg_match('/\d{2}\.\d{2}\.\d{4}/i', $datestring)) {
        try {
            $date = new DateTime($datestring);
            return true;
        } catch (Exception $e) {
            return false;
        }
    } else {
        return false;
    }
}


function get_payolution_converted_date($old_date) {
    // matches DD.MM.YYYY
    if ( preg_match('/\d{2}\.\d{2}\.\d{4}/i', $old_date)
        && strlen(trim($old_date)) == 10
    ) {
        $date1 = DateTime::createFromFormat('d.m.Y', $old_date);
        $date2 = $date1->format('Y-m-d');
        return $date2;
    }
    return $old_date;
}


/**
 * Compares the fields of the given addresses and returns true if they are different
 * @param $invoice_address
 * @param $shipment_address
 * @return bool
 */
function compare_addresses_for_differences($invoice_address, $shipment_address) {
    $fields = array("name", "name_2", "address", "address_street", "address_no", "address_2", "post_code", "city", "country");
    foreach ($fields as $field) {
        if ($invoice_address[$field] != $shipment_address[$field]) {
            return true;
        }
    }
    return false;
}


