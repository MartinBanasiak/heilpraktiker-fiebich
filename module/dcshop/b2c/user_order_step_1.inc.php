<ul class="processbar">
    <li class="processbar_item active"><?= $GLOBALS["tc"]["order_bar_address"] ?>
        <div class="arrow"></div>
        <div class="arrow-border"></div>
    </li>
    <li class="processbar_item"><?= $GLOBALS["tc"]["order_bar_payment"] ?>
        <div class="arrow"></div>
        <div class="arrow-border"></div>
    </li>
    <li class="processbar_item"><?= $GLOBALS["tc"]["order_bar_check"] ?></li>
</ul>
<?
if ($GLOBALS['shop_language']['order_step_1_text_module'] != '') {
    $spacer = array();
    echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_step_1_text_module'], $spacer));
}
$formname = "form_user_order";
$formname_login = "form_shop_login";
session_save_data_b2c("step1");
$visitor_data = get_main_visitor_data_b2c("step1");
$_SESSION["order_previous_step"] = "step1";

if (check_mandatory_fields_b2c($_POST)) {
    $step = "step2";
} else {
    $step = "step2";
}

if ($_SESSION["dc_id"] != '') {
    $ml = ml("", "shop_category", "dc_order");
    $backText = $GLOBALS["tc"]["back_to_dc"];
} else {
    $ml = ml("", "shop_category", "basket");
    $backText = $GLOBALS["tc"]["back_to_basket"];
}

$radio_guest = "checked='checked'";
$radio_register = " ";
$radio_login = " ";
$display_login = " style=\"display:block;\" ";
$display_address = " style=\"display:none;\" ";
if ($_POST["radio_guest"] <> '') {
    $_SESSION["radio_guest"] = $_POST["radio_guest"];
}
if (isset($_POST["register"])) {
    if ($_POST["register"] == 1) {
        $_SESSION["radio_guest"] = "register";
    } else {
        $_SESSION["radio_guest"] = "guest";
    }
}
if ($_SESSION["radio_guest"] == "guest") {
    $radio_register = " ";
    $radio_guest = " checked='checked' ";
    $radio_login = " ";
    $display_login = " style=\"display:none;\" ";
    $display_address = " style=\"display:block;\" ";
}
if ($_SESSION["radio_guest"] == "register") {
    $radio_register = " checked='checked' ";
    $radio_guest = " ";
    $radio_login = " ";
    $display_login = " style=\"display:none;\" ";
    $display_address = " style=\"display:block;\" ";
}
if ($_SESSION["radio_guest"] == "login") {
    $radio_login = " checked='checked' ";
    $radio_guest = " ";
    $radio_register = " ";
    $display_login = " ";
}
if ($GLOBALS['visitor']['frontend_login']) {
    $display_address = " style=\"display:block;\" ";
    $radio_login = " checked='checked' ";
}

if ($_GET['login_error']) {
    get_requestbox($GLOBALS['tc']['login_error']);
}

get_requestbox($GLOBALS["tc"]["order_error_fields"], "", "error", false, "vat_check_requestbox");

?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <? if (!$GLOBALS['visitor']['frontend_login']) { ?>
            <div class="order_devision_headline"><?= $GLOBALS['tc']['existing_customer']; ?></div>
            <div class="order_devision_text"><?= $GLOBALS['tc']['existing_customer_text']; ?></div>
            <div class="order_login order_box">
                <? if ($GLOBALS["shop_customer"]["customer_no"] == "") { ?>
                    <div id="login_fields">
                        <form id="form_shop_login2" name="form_shop_login2" method="post"
                              action="<? echo "/" . customizeUrl() . "/account/?action=shop_login" ?>">
                            <?php
                            $params = "";
                            if (!empty($_GET["dc_id"])) {
                                $params = "?dc_id=" . $_GET["dc_id"];
                            }
                            ?>
                            <input type="hidden" name="redirect_url"
                                   value="/<?= customizeUrl() ?>/order/<?= $params ?>"/>
                            <? input_shop($GLOBALS["tc"]["email"], "input_email", "text", '', 125, FALSE, FALSE, false); ?>
                            <? input_shop($GLOBALS["tc"]["password"], "input_password", "password", '', 50, FALSE, FALSE, false); ?>
                            <br/>
                            <?= button("order_next button", $GLOBALS["tc"]["login"], "form_shop_login2", "/" . customizeUrl() . "/account/?action=shop_login"); ?>
                        </form>
                    </div>
                <? } ?>
            </div>

            <div class="order_login order_box">
                <?php get_content('order_step_1_login', true, $IOCContainer); ?>
            </div>
        <? } ?>
        <div class="secure_order">
            <div id="secure_order_text" class="secure_order_item"><?= $GLOBALS["tc"]["secure_order"]; ?></div>
            <div id='protection_order_text' class="secure_order_item"><?= $GLOBALS["tc"]["protection_order"]; ?></div>
            <div id='quality_order_text' class="secure_order_item"><?= $GLOBALS["tc"]["quality_order"]; ?></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-7 col-md-offset-1">
        <? if (!$GLOBALS['visitor']['frontend_login']) { ?>
            <div class="guest_select">
                <div class="order_devision_headline"><?= $GLOBALS['tc']['new_customer']; ?></div>
                <form name='form_register' id="form_register" method='POST'
                      action='//<?= $_SERVER['HTTP_HOST']; ?>/<? echo customizeUrl(); ?>/order/address_select/'>
                    <div class="tabs guest_checkout_tabs">
                        <div class="tabs_item active">
                            <div class="form-check form-group">
                                <label class="form-check-label" for='radio_guest'>
                                    <input type='radio' name='radio_guest' id='radio_guest'
                                           value='guest' <?= $radio_guest; ?> checked='checked'
                                           onClick='$("#register_fields").hide(); $("#register_fields").find("input").val(""); $("#hidden_register").val("0"); '>
                                    <?= $GLOBALS['tc']['guest_order']; ?>
                                </label>
                            </div>
                        </div>
                        <div class="tabs_item">
                            <div class="form-check form-group">
                                <label class="form-check-label" for='radio_register'>
                                    <input type='radio' name='radio_guest' id='radio_register'
                                           value='register' <?= $radio_register; ?>
                                           onClick='$("#hidden_register").val("1"); $("#register_fields").show();'>
                                    <?= $GLOBALS['tc']['new_customer']; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="subscription_item_id"
                           value="<?php echo urlencode(filter_var($_POST['subscription_item_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>
                    <input type="hidden" name="subscription_item_var_code"
                           value="<?php echo urlencode(filter_var($_POST['subscription_item_var_code'], FILTER_SANITIZE_STRING)); ?>"/>
                    <input type="hidden" name="subscription_item_qty"
                           value="<?php echo urlencode(filter_var($_POST['subscription_item_qty'], FILTER_SANITIZE_NUMBER_FLOAT)); ?>"/>
                    <input type="hidden" name="subscription_header_id"
                           value="<?php echo urlencode(filter_var($_POST['subscription_header_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>
                </form>
            </div>
        <? } ?>
        <form id="<?= $formname ?>" class="form-label-left" name="<?= $formname ?>" method="post"
              action='//<?= $_SERVER['HTTP_HOST']; ?>/<? echo customizeUrl(); ?>/order/shipment_payment_option_select/'>
            <? /* ---SUBSCRIPTION--- */ ?>
            <input type="hidden" name="subscription_item_id"
                   value="<?php echo urlencode(filter_var($_POST['subscription_item_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>
            <input type="hidden" name="subscription_item_var_code"
                   value="<?php echo urlencode(filter_var($_POST['subscription_item_var_code'], FILTER_SANITIZE_STRING)); ?>"/>
            <input type="hidden" name="subscription_item_qty"
                   value="<?php echo urlencode(filter_var($_POST['subscription_item_qty'], FILTER_SANITIZE_NUMBER_FLOAT)); ?>"/>
            <input type="hidden" name="subscription_header_id"
                   value="<?php echo urlencode(filter_var($_POST['subscription_header_id'], FILTER_SANITIZE_NUMBER_INT)); ?>"/>

            <? /* +++SUBSCRIPTION+++ */ ?>
            <div class="order_devision_headline"><?= $GLOBALS['tc']['invoice_address']; ?></div>
            <?
            if ($_GET["action"] == "step2") {
                if ((($_POST['register'] == 1 && check_duplicate_user()) || $_POST['register'] == 0) && $shipment_possible
                    && (!isset($_SESSION['is_valid_invoice_address']) || $_SESSION['is_valid_invoice_address'] === true)
                    && (!isset($_SESSION['is_valid_shipping_address']) || $_SESSION['is_valid_shipping_address'] === true)
                ) {
                    get_requestbox($GLOBALS["tc"]["order_error_fields"]);
                }
            }
            if (isset($_SESSION['is_valid_invoice_address']) && false === $_SESSION['is_valid_invoice_address']) {
                get_requestbox($GLOBALS['tc']['invalid_address_formal']);
                $_SESSION['is_valid_invoice_address'] = true;
            } else if (isset($_SESSION['is_valid_invoice_address']) && $_SESSION['is_valid_invoice_address'] !== true) {

                get_requestbox($GLOBALS['tc']['review_recommended_address'], '', 'warning');

                $select = ' <div id="recomended_address" style="display:block;">
                <div class="form-group">
                    <label for="recomended_invoice_address">' . $GLOBALS['tc']['recommended_address'] . '</label>
                    <div class="select_body">
                        <select  id=\'recomended_invoice_address\' name=\'recomended_invoice_address\' >
                          <option value="-1">' . $GLOBALS['tc']['please_select_address'] . '</option> ';

                foreach ($_SESSION['is_valid_invoice_address'] as $key => $value) {
                    $select .= '<option value="' . $key . '">' . $value . '</option>';
                }
                $select .= ' </select> 
                                </div>
                            </div>
                        </div> ';
                echo $select;
                $_SESSION['is_valid_invoice_address'] = true;
            }
            //US Sales Tax ---
            if (isset($_SESSION['is_us_address_valid']) && false === $_SESSION['is_us_address_valid']) {
                get_requestbox($GLOBALS['tc']['invalid_address_formal']);
            }
            //US Sales Tax +++
            $disabled = FALSE;
            if ($_GET['action'] == "step2") {
                $evaluate = TRUE;
            } else {
                $evaluate = FALSE;
            }

            if ($_SESSION["input_is_company"] == 'on' || (!isset($_SESSION["input_is_company"]) && $visitor_data["visitor_is_company"] == 1)) {
                $c_val = "1";
                $c_style = "display:block;";
                $p_style = "display:block;";
            } else {
                $c_val = "0";
                $p_style = "display:block;";
                $c_style = "display:none;";
            }

            input_shop($GLOBALS["tc"]["input_is_company"], "input_is_company", "checkbox", $c_val, 50, $disabled, FALSE, "onClick=\"if(this.checked==false){toggleOff('company_fields');;}else{toggleOn('company_fields')}\"", false);
            ?>
            <div id=company_fields style='<?= $c_style; ?>'>
                <?= input_shop($GLOBALS["tc"]["company"], "input_company", "text", $visitor_data["visitor_company"], 30, $disabled, FALSE); ?>
                <?= input_shop($GLOBALS["tc"]["vatid"], "input_vatid", "text", $visitor_data["visitor_vatid"], 30, $disabled, FALSE); ?>
            </div>
            <div id="person_fields" style='<?= $p_style; ?>'>
                <div class="form-group">
                    <label for="Anrede"><?= $GLOBALS["tc"]["salutation"]; ?></label>
                    <div class="select_body">
                        <select class="select" name='input_salutation'>
                            <option value='<?= $GLOBALS["tc"]["mr"] ?>' <? if ($visitor_data["visitor_salutation"] == $GLOBALS["tc"]["mr"]) {
                                echo "selected";
                            } ?>><?= $GLOBALS["tc"]["mr"] ?></option>
                            <option value='<?= $GLOBALS["tc"]["mrs"] ?>' <? if ($visitor_data["visitor_salutation"] == $GLOBALS["tc"]["mrs"]) {
                                echo "selected";
                            } ?>><?= $GLOBALS["tc"]["mrs"] ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            input_shop($GLOBALS["tc"]["surname"] . " *", "input_surname", "text", $visitor_data["visitor_surname"], 24, $disabled, $evaluate, '', true);
            input_shop($GLOBALS["tc"]["lastname"] . " *", "input_lastname", "text", $visitor_data["visitor_lastname"], 24, $disabled, $evaluate, '', true);
            ?>
            <div class="form-group-street">
                <label for="input_user_street"><?= $GLOBALS["tc"]["street"] . "  *" ?></label>
                <? input_shop('', "input_user_street", "text_street", $visitor_data["visitor_address"], 50, $disabled, $evaluate, '', true); ?>
                <? input_shop('', "input_user_street_no", "text_street_no", $visitor_data["visitor_address_no"], 30, $disabled, $evaluate, '', true); ?>
            </div>


            <? input_shop($GLOBALS["tc"]["post_code"] . " *", "input_post_code", "code", $visitor_data["visitor_post_code"], 30, $disabled, $evaluate, '', true) ?>
            <? input_shop($GLOBALS["tc"]["city"] . " *", "input_city", "text", $visitor_data["visitor_city"], 30, $disabled, $evaluate, '', true) ?>
            <? if (isset($_SESSION['visitor_country']) && $_SESSION['visitor_country'] != "") {
                $countrycode = $_SESSION['visitor_country'];
            } elseif (isset($GLOBALS['shop_customer']['country']) && $GLOBALS['shop_customer']['country'] != '') {
                $countrycode = $GLOBALS['shop_customer']['country'];
            } else {
                $countrycode = "DE";
            }
            $selectedReverseChargeAllowed = create_countries($GLOBALS["tc"]["country"], "input_country", $countrycode, $disabled, 1); ?>
            <? input_shop($GLOBALS["tc"]["email"] . " *", "input_email", "text", $visitor_data["visitor_email"], 80, $disabled, $evaluate, '', true) ?>
            <? input_shop($GLOBALS["tc"]["phone_no"], "input_phone_no", "text", $visitor_data["visitor_telephone"], 80, $disabled, FALSE) ?>
            <?
            $display_password_fields = " style=\"display: none;\"";
            $register_value = 0;
            if ($_SESSION['radio_guest'] == 'register' && $GLOBALS["shop_customer"]["customer_no"] == "") {
                $display_password_fields = "";
                $register_value = 1;
            }
            echo "<div id='register_fields' " . $display_password_fields . ">";
            input_shop($GLOBALS["tc"]["password"], "input_password", "password", '', 80, false, false);
            input_shop($GLOBALS["tc"]["password_2"], "input_password_2", "password", '', 80, false);
            echo "<input id='hidden_register' type='hidden' name='register' value='" . $register_value . "'>";
            echo "</div>";
            ?>

            <? if ($GLOBALS["shop_customer"]["customer_no"] != "" && substr($GLOBALS["shop_customer"]["customer_no"], 0, 4) != "TEMP") { ?>
                <? input_shop($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "text", $GLOBALS["shop_customer"]["customer_no"], 30, TRUE) ?>
            <? } ?>
            <? if ($_SESSION["dc_id"] == '') { ?>
                <hr/>
                <div class="order_devision_headline"><?= $GLOBALS['tc']['shipping_address']; ?></div>
            <?
            if ($_POST['input_country'] == '') {
                $_POST['input_country'] = "DE";
            }
            if (!isset($_SESSION["visitor_name"])) {
                $val = "1";
                $style = "\"display: none;\"";
            } else {
                $val = $visitor_data["address_is_shipping_address"];
                if ($visitor_data["address_is_shipping_address"] != "") {
                    $style = "\"display: none;\"";
                } else {
                    $style = "\"display: block;\"";
                }
            }

            if ($val == "on") {
                $val = "1";
            } else {
                $val = "0";
            }
            if ($_SESSION["visitor_new_shipping_address"] == "on" || $_SESSION["visitor_radio_shipping_is_not_invoice"] != "") {
                $val2 = "1";
                $style = "display: block;";
            } else {
                $val2 = "0";
                $style = "display: none;";
            }
            if ($_SESSION["visitor_packstation_address"] == "on" || $_SESSION["visitor_radio_shipping_is_packstation"] != "") {
                $val3 = "1";
                $style2 = "display: block;";
            } else {
                $val3 = "0";
                $style2 = "display: none;";
            }
            if ($_SESSION["visitor_new_shipping_address"] != "on" && $_SESSION["visitor_packstation_address"] != "on") {
                $val = "1";
            }
            ?><? if ($GLOBALS['visitor']['frontend_login'] && isset($GLOBALS["shop_customer"])) {
                $style = "\"display: none;\"";
                shipment_address_select($GLOBALS["shop_customer"], $GLOBALS['tc']['edit_shipment_address'], "input_shipment_address_id", $visitor_data["visitor_shipment_address_id"], FALSE, FALSE);
                echo("<div class='input'><a href='/" . customizeUrl() . "/account/?action=edit_shipment_address'>Lieferadressen verwalten</a></div><br/>");
                input_shop($GLOBALS['tc']['new_address'], "input_shipping_is_not_invoice", "checkbox", $visitor_data["visitor_shipping_new_address"], 50, FALSE, FALSE, "onClick=\"if(this.checked==true){toggleOn('new_address_type_select');toggleOn('new_address_fields');}else{toggleOff('new_address_fields'); toggleOff('new_address_fields_packstation');toggleOff('new_address_type_select');}\"");            //input_shop($GLOBALS['tc']['packstation'],"input_shipping_new_address","checkbox",$visitor_data["visitor_shipping_new_address"],50,false,false,"onClick=\"toggle('new_address_fields_packstation');\"");
                if ($visitor_data["visitor_shipping_new_address"] == "on") {
                    $style = "display: block;";
                } else {
                    $style = "display: none;";
                }
            } else {
            //packstation
            $radio_shipping_is_invoice = " checked='checked' ";
            $input_shipping_is_not_invoice = " ";
            $input_shipping_is_packstation = " ";
            if ($_SESSION["visitor_radio_shipping_is_not_invoice"] != "" || ($visitor_data["visitor_surname_shipping"] != "" && $visitor_data["visitor_surname_shipping_packstation"] == "")) {
                $input_shipping_is_not_invoice = " checked='checked' ";
                $radio_shipping_is_invoice = " ";
                $input_shipping_is_packstation = " ";
            }
            if ($_SESSION["visitor_radio_shipping_is_packstation"] != "") {
                $input_shipping_is_packstation = " checked='checked' ";
                $radio_shipping_is_invoice = " ";
                $input_shipping_is_not_invoice = " ";
            }

            if ($visitor_data["visitor_shipping_new_address"] == "on" || $_SESSION["visitor_shipping_new_address"] == "on" || $_POST["visitor_shipping_new_address"] == "on" || $_POST["input_shipping_is_not_invoice"] == "on") {
                $style = "display: block;";
            } else {
                $style = "display: none;";
            }
            ?>
                <div class="form-check form-group radio">
                    <label class="form-check-label" for='input_shipping_is_invoice'>
                        <input type='radio' name='input_shipping_is_invoice' id='input_shipping_is_invoice'
                               value='on' <?= $radio_shipping_is_invoice; ?>
                               onClick="document.getElementById('input_shipping_is_not_invoice').checked='';document.getElementById('input_shipping_is_packstation').checked='';
                               $('#new_address_fields_packstation').hide();
                               $('#new_address_fields').hide();">
                        <?= $GLOBALS['tc']['input_shipping_is_invoice']; ?>
                    </label>
                </div>
                <div class="form-check form-group radio">
                    <label class="form-check-label" for='input_shipping_is_not_invoice'>
                        <input type='radio' name='input_shipping_is_not_invoice' id='input_shipping_is_not_invoice'
                               value='on' <?= $input_shipping_is_not_invoice; ?>
                               onClick="document.getElementById('input_shipping_is_invoice').checked='';document.getElementById('input_shipping_is_packstation').checked='';
                               $('#new_address_fields_packstation').hide();
                               $('#new_address_fields').show();">
                        <?= $GLOBALS['tc']['input_shipping_is_not_invoice']; ?>
                    </label>
                </div>
                <div class="form-check form-group radio">
                    <label class="form-check-label" for='input_shipping_is_packstation'>
                        <input type='radio' name='input_shipping_is_packstation' id='input_shipping_is_packstation'
                               value='on' <?= $input_shipping_is_packstation; ?>
                               onClick="document.getElementById('input_shipping_is_not_invoice').checked='';document.getElementById('input_shipping_is_invoice').checked='';
                               $('#new_address_fields_packstation').show();
                               $('#new_address_fields').hide();">
                        <?= $GLOBALS['tc']['packstation']; ?>
                    </label>
                </div>
            <?
            }

            ?>
                <div id="new_address_type_select">
                    <div class="form-group">
                        <label for="new_address_type">&nbsp;</label>
                        <div class="select_body">
                            <select class="select" id="new_address_type" name='new_address_type'>
                                <option value="std"
                                        selected="selected"><?= $GLOBALS['tc']['standard_address'] ?></option>
                                <option value="packstation"><?= $GLOBALS['tc']['packstation_address'] ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <script>
                    $('#new_address_type').on('change', function () {
                        var selectedValue = $(this).val();
                        if (selectedValue == 'std') {
                            $('#new_address_fields_packstation').hide();
                            $('#new_address_fields').show();
                        } else if (selectedValue == 'packstation') {
                            $('#new_address_fields').hide();
                            $('#new_address_fields_packstation').show();
                        }
                    });

                    $(function () {
                        if ($('#input_shipping_is_not_invoice').is(':checked')) {
                            $('#new_address_fields').show();
                        }
                    });

                    $('#recomended_invoice_address').on('change', function () {

                        var address = $("#recomended_invoice_address :selected").text().split(",");

                        var streetData = (address[0]);
                        var houseNumber = $("#recomended_invoice_address").val();
                        var street = streetData.replace(houseNumber, "");

                        var postalCode = address[1];
                        var city = address[2];
                        var Country = address[3];

                        $('#input_user_street').val(street);
                        $('#input_user_street_no').val(houseNumber);
                        $('#input_post_code').val(postalCode);
                        $('#input_city').val(city);
                        $('#input_country').val(Country);

                    });


                </script>
                <div id="new_address_fields_packstation" style=" <?= $style2 ?>"><?
                    //PACKSTATION
                    input_shop($GLOBALS["tc"]["surname"] . " *", "input_surname_shipping_packstation", "text", $visitor_data["visitor_surname_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["lastname"] . " *", "input_lastname_shipping_packstation", "text", $visitor_data["visitor_lastname_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["adr_postnummer"] . " *", "input_company_shipping_packstation", "text", $visitor_data["visitor_company_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["adr_packstation"] . " *", "input_user_street_shipping_packstation", "text", $visitor_data["visitor_address_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["post_code"] . " *", "input_post_code_shipping_packstation", "code", $visitor_data["visitor_post_code_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["city"] . " *", "input_city_shipping_packstation", "text", $visitor_data["visitor_city_shipping_packstation"], 30, FALSE, $evaluate, '', true);
                    if (isset($_SESSION["visitor_country_shipping"]) && $_SESSION["visitor_country_shipping"] != "") {
                        $countrycode = $_SESSION["visitor_country_shipping"];
                    } elseif (isset($GLOBALS['shop_customer']['country']) && $GLOBALS['shop_customer']['country'] != '') {
                        $countrycode = $GLOBALS['shop_customer']['country'];
                    } else {
                        $countrycode = "DE";
                    }
                    create_countries($GLOBALS['tc']['country'], "input_shop_country_shipping", $countrycode, FALSE, 2);
                    //PACKSTATION ENDE
                    ?>
                </div>

                <div id="new_address_fields" style=" <?= $style ?>"><?

                    if (isset($_SESSION['is_valid_shipping_address']) && false === $_SESSION['is_valid_shipping_address']) {
                        get_requestbox($GLOBALS['tc']['invalid_address_formal']);
                        $_SESSION['is_valid_shipping_address'] = true;
                    } else if (isset($_SESSION['is_valid_shipping_address']) && $_SESSION['is_valid_shipping_address'] !== true) {

                        get_requestbox($GLOBALS['tc']['review_recommended_address'], '', 'warning');

                        $select = ' <div id="recomended_shipp_address" style="display:block;">
                <div class="form-group">
                    <label for="recomended_invoice_address">' . $GLOBALS['tc']['recommended_address'] . '</label>
                    <div class="select_body">
                        <select  id=\'recomended_shipping_address\' name=\'recomended_shipping_address\'>
                         <option value="-1">' . $GLOBALS['tc']['please_select_address'] . '</option>
                         ';

                        foreach ($_SESSION['is_valid_shipping_address'] as $key => $value) {
                            $select .= '<option value="' . $key . '">' . $value . '</option>';
                        }
                        $select .= ' </select> 
                                </div>
                            </div>
                        </div> ';
                        echo $select;
                        $_SESSION['is_valid_shipping_address'] = true;
                    }


                    input_shop($GLOBALS["tc"]["surname"] . " *", "input_surname_shipping", "text", $visitor_data["visitor_surname_shipping"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["lastname"] . " *", "input_lastname_shipping", "text", $visitor_data["visitor_lastname_shipping"], 30, FALSE, $evaluate, '', true);
                    input_shop($GLOBALS["tc"]["company"], "input_company_shipping", "text", $visitor_data["visitor_company_shipping"], 30, FALSE, FALSE, false);
                    ?>
                    <div class="form-group-street">
                        <label for="input_user_street_shipping"><?= $GLOBALS["tc"]["street"] ?> *</label>
                        <? input_shop("", "input_user_street_shipping", "text_street", $visitor_data["visitor_address_shipping"], 50, $disabled, $evaluate, '', true); ?>
                        <? input_shop("", "input_user_street_no_shipping", "text_street_no", $visitor_data["visitor_address_no_shipping"], 30, $disabled, $evaluate, '', true); ?>
                    </div>

                    <?
                    //input_shop($GLOBALS["tc"]["street"],"input_user_street_shipping","text",$visitor_data["visitor_address_shipping"],30,FALSE,$evaluate);
                    //input_shop($GLOBALS["tc"]["street_no"],"input_user_street_no_shipping","text",$visitor_data["visitor_address_no_shipping"],5,$disabled,$evaluate);
                    input_shop($GLOBALS["tc"]["post_code"] . " *", "input_post_code_shipping", "code", $visitor_data["visitor_post_code_shipping"], 30, FALSE, $evaluate);
                    input_shop($GLOBALS["tc"]["city"] . " *", "input_city_shipping", "text", $visitor_data["visitor_city_shipping"], 30, FALSE, $evaluate);
                    if (isset($_SESSION["visitor_country_shipping"]) && $_SESSION["visitor_country_shipping"] != "") {
                        $countrycode = $_SESSION["visitor_country_shipping"];
                    } elseif (isset($GLOBALS['shop_customer']['country']) && $GLOBALS['shop_customer']['country'] != '') {
                        $countrycode = $GLOBALS['shop_customer']['country'];
                    } else {
                        $countrycode = "DE";
                    }
                    create_countries($GLOBALS['tc']['country'], "input_shop_country_shipping", $countrycode, FALSE, 2);
                    ?>
                </div>
                <script>

                    $('#recomended_shipping_address').on('change', function () {

                        var address = $("#recomended_shipping_address :selected").text().split(",");

                        var streetData = (address[0]);
                        var houseNumber = $("#recomended_shipping_address").val();
                        var street = streetData.replace(houseNumber, "");

                        var postalCode = address[1];
                        var city = address[2];
                        var Country = address[3];

                        $('#input_user_street_shipping').val(street);
                        $('#input_user_street_no_shipping').val(houseNumber);
                        $('#input_post_code_shipping').val(postalCode);
                        $('#input_city_shipping').val(city);
                        $('#input_shop_country_shipping').val(Country);

                    });


                </script>
            <? } ?>
        </form>
    </div>
</div>
<div class="button_row">
    <div class="pull-right">
        <?
        if ($radio_login == " checked='checked' " && $GLOBALS["shop_customer"]["customer_no"] == "") {


            echo button("order_next button button_action", $GLOBALS["tc"]["login"], "form_shop_login2", "?action=shop_login");


        } else {
            // echo button("order_next button button_action validate_vat", $GLOBALS["tc"]["next_step"], $formname);
            // echo '<a class="button_order_next button button_action validate_vat" href="javascript:void(0);">'.$GLOBALS["tc"]["next_step"].'</a>';
            ?>
            <a class="order_next button button_action validate_vat"
               href='/<? echo customizeUrl(); ?>/order/shipment_payment_option_select/'><?= $GLOBALS["tc"]["next_step"] ?></a>
            <?
        }

        ?>
    </div>
    <div class="pull-left">
        <a class="order_basket button" href='/<? echo customizeUrl(); ?>/basket/'><?= $backText ?></a>
    </div>
</div>
