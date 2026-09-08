<?
if (($GLOBALS['visitor']['frontend_login'] == 1 && $GLOBALS['visitor']['cookie_only'] == 1) || !$GLOBALS['visitor']['frontend_login']) {
    ?>
    <div class="login_wrapper">
        <form id="form_shop_login" name="form_shop_login" method="post"
              action="?shop_category=account&action=edit_curr_shop_user&login=true">
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
    $formname = "form_user_order"; ?>

    <script type="text/javascript">
        //datepicker Einstellungen
        jQuery(function ( $ ) {
            $.datepicker.regional['de'] = {
                clearText      : 'löschen', clearStatus: 'aktuelles Datum löschen',
                closeText      : 'schließen', closeStatus: 'ohne Änderungen schließen',
                prevText       : '&#x3c;zurück', prevStatus: 'letzten Monat zeigen',
                nextText       : 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
                currentText    : 'heute', currentStatus: '',
                monthNames     : ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
                                  'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                monthNamesShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun',
                                  'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                monthStatus    : 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
                weekHeader     : 'Wo', weekStatus: 'Woche des Monats',
                dayNames       : ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
                dayNamesShort  : ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                dayNamesMin    : ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                dayStatus      : 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
                dateFormat     : 'dd.mm.yy', firstDay: 1,
                initStatus     : 'Wähle ein Datum', isRTL: false
            };
            $.datepicker.setDefaults($.datepicker.regional['de']);
        });
    </script>
    <?
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
        ?>
        <script type="text/javascript">
            //Feld mit id='date' durch datepicker ersetzen und kleinstes Datum in 3 Werktagen setzen
            $(function () {
                $("#date").datepicker({
                                          minDate: +<?=$days ?>
                                      });
            });
        </script>
    <?
    }
    $_SESSION['site_code']     = $GLOBALS['site']['code'];
    $_SESSION['language_code'] = $GLOBALS['language']['code'];
    if ($_GET['payment_error'] == 1) {
        ?>
        <div class="errorbox"><?= $GLOBALS["tc"]["unspecific_payment_error"] ?></div><br />
        <?
        $_GET['payment_error'] = 0;
    }
    ?>
    <div class="infobar">
        <?= $GLOBALS["tc"]["user_order"] ?>
    </div>
    <?
    if ($GLOBALS['shop_language']['order_step_1_text_module'] != '') {
        $spacer = array();
        echo(get_text_module($GLOBALS['shop_language']['company'], $GLOBALS['shop_language']['order_step_1_text_module'], $spacer));
    }
    ?>
    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <? if ($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            <table class="cardform_2">
                <tr>
                    <td>
                        <h3><?= $GLOBALS["tc"]["confirmation"] ?></h3>

                        <div class="spacer_6">&nbsp;</div>
                        <? if ($_GET["action"] == "complete_order" & $_POST['input_agb_checked'] == "") { ?>
                            <div
                                class="errorbox"><?= $GLOBALS['tc']['confirm_agb'] . "Bitte best&auml;tigen Sie die AGB" ?></div>
                        <? }
                        ob_start();
                        input($GLOBALS['tc']['agb_confirmed'], "input_agb_checked", "checkbox", "", 50, FALSE, FALSE);
                        $spacer['%checkbox%'] = ob_get_contents();
                        ob_end_clean();
                        $agb_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['checkout_confirmation_text_module'], $spacer);
                        echo $agb_text;
                        ?>
                    </td>
                </tr>
            </table>
        <? } else { ?>
            <input type="hidden" name="input_agb_checked" id="input_agb_checked" value="on" />
        <? } ?>
        <? if ($GLOBALS['shop_language']['newsletter_registration_text_module'] != '') { ?>
            <table class="cardform_2">
                <tr>
                    <td>
                        <h3><?= $GLOBALS["tc"]["newsletter"] ?></h3>

                        <div class="spacer_6">&nbsp;</div>
                        <?
                        ob_start();
                        input($GLOBALS['tc']['newsletter_registration'], "input_newsletter_checked", "checkbox", "", 50, FALSE, FALSE);
                        $spacer['%checkbox%'] = ob_get_contents();
                        ob_end_clean();
                        $newsletter_text = get_text_module($GLOBALS['shop']['company'], $GLOBALS['shop_language']['newsletter_registration_text_module'], $spacer);
                        echo $newsletter_text;
                        ?>
                    </td>
                </tr>
            </table>
        <? } ?>
        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><h3><?= $GLOBALS["tc"]["information"] ?> </h3>

                    <div class="spacer_6">&nbsp;</div>
                    <? input($GLOBALS["tc"]["customer_no"], "input_shop_customer_no", "code", $GLOBALS["shop_customer"]["customer_no"], 30, TRUE) ?>
                    <div class="spacer_6">&nbsp;</div>
                    <?
                    input($GLOBALS["tc"]["name"], "input_name", "text", $_POST['input_name'], 30) ?>
                    <? input($GLOBALS["tc"]["email"], "input_email", "text", '', 50) ?>
                    <? if ($GLOBALS["shop"]["order_options_display"] == 0) { ?>
                    <? payment_terms_select($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_POST["input_payment_line_no"], FALSE, TRUE, $invoice_address["country"], false) ?>
                    <? } else { ?>
                        <? payment_terms_list($GLOBALS["shop_user"], $GLOBALS["tc"]["payment_type"], "input_payment_line_no", $_POST["input_payment_line_no"], FALSE, TRUE, $invoice_address["country"], false) ?>
                    <? } ?>
                    <div class="spacer_6">&nbsp;</div>
                    <? if ($GLOBALS["shop"]["order_options_display"] == 0) { ?>
                    <? shipping_agent_select($GLOBALS["visitor"], $GLOBALS["tc"]["shipping_agent"], "input_shipping_line_no", $_POST["input_shipping_line_no"], FALSE, TRUE, $shipment_address["country"]) ?>
                    <? } else { ?>
                        <? shipping_agent_list($GLOBALS["visitor"], $GLOBALS["tc"]["shipping_agent"], "input_shipping_line_no", $_POST["input_shipping_line_no"], FALSE, TRUE, $shipment_address["country"]) ?>
                    <? } ?>
                    <? shipment_address_select($GLOBALS["shop_customer"], $GLOBALS["tc"]["shipment_address"], "input_shipment_address_id", $_POST["input_shipment_address_id"], FALSE, TRUE) ?>
                    <div class="spacer_6">&nbsp;</div>
                    <? input($GLOBALS["tc"]["your_reference"], "input_your_reference", "text", $_POST["input_your_reference"], 30) ?>
                    <? input($GLOBALS["tc"]["your_comment"], "input_your_comment", "text", $_POST["input_your_comment"], 160) ?>
                    <div class="spacer_6">&nbsp;</div>
                    <? if ($GLOBALS['shop']['select_req_delivery_date'] == 1) {
                        input($GLOBALS["tc"]["preferred_order_date"], "date", "input_pref_delivery_date", $_POST["date"], 30, FALSE, FALSE, " onChange='document.form_user_order.action=\"" . ml("", "shop_category", "order") . "\";document.form_user_order.submit()'");
                    } ?>
                    <? input($GLOBALS["tc"]["drop_shipment"], "input_drop_shipment", "checkbox", $_POST["input_drop_shipment"], 20) ?>
                    <div class="spacer_6">&nbsp;</div>
                    <? input($GLOBALS["tc"]["coupon_code"], "input_coupon_code", "text", $_SESSION['coupon']['coupon_code'], 30) ?>
                    <?($_SESSION['coupon'] == '') ? button('edit', $GLOBALS["tc"]["use_coupon"], $formname, '?shop_category=order&action=coupon') : button('delete', 'Gutschein entfernen', $formname, '?shop_category=order&action=coupon_delete') ?>
                </td>
                <td><h3>
                        <?= $GLOBALS["tc"]["invoice_address"] ?>
                    </h3>

                    <div class="spacer_6">&nbsp;</div>
                    <? format_address($invoice_address["name"], $invoice_address["name_2"], $invoice_address["address"], $invoice_address["address_2"], $invoice_address["post_code"], $invoice_address["city"], $invoice_address["country"], ''); ?>
                    <br />

                    <h3>
                        <?= $GLOBALS["tc"]["shipment_address"] ?>
                    </h3>

                    <div class="spacer_6">&nbsp;</div>
                    <? //&Auml;nderung Bluestar HT: Funktion format_address erweitert um Telefonnummer
                    format_address_with_phone($shipment_address["name"], $shipment_address["name_2"], $shipment_address["address"], $shipment_address["address_2"], $shipment_address["post_code"], $shipment_address["city"], $shipment_address["country"], $shipment_address["contact"], '', $shipment_address["telephone"], $_SESSION["input_is_company"]);
                    $total = $subtotal + $small_quantity_charge_amount - $invoice_discount_amount - $online_discount_amount + $_SESSION['shipping_cost'] + $_SESSION['payment_cost']; ?>
                </td>
            </tr>
        </table>
        <? show_item_list($basketresult, 5, $formname); ?>
        <div class="infobox" style="text-align:right;">
            <table class="order_step_1_bottom" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                    <td align="left" valign="top"><h3>
                            <?= shop_get_basket_quantity($GLOBALS["visitor"]["id"]) ?>
                            <?= $GLOBALS["tc"]["item_in_basket"] ?>
                        </h3>
                        <br />
                        <?= $GLOBALS["tc"]["vat_message"] ?>
                        <br />
                        <?= $GLOBALS["tc"]["shipment_message"] ?></td>
                    <td valign="top"
                        align="right"><? show_order_sum($subtotal, $online_discount, $online_discount_amount, $invoice_discount, $invoice_discount_amount, $small_quantity_charge_amount, $total, $_SESSION['shipping_cost'], $_SESSION['payment_cost']) ?></td>
                </tr>
            </table>
        </div>
        <div class="toolbar">
            <div><?= button("back", "< " . $GLOBALS["tc"]["back_to_basket"], $formname, ml("", "shop_category", "basket")); ?>
                <?= button_finish("next", $GLOBALS["tc"]["complete_order"], $formname, ml("", "shop_category", "order", "action", "payment", ""), $GLOBALS["tc"]["confirm_order"]); ?>
            </div>
        </div>

    </form>
    <script type="text/javascript">
        function pay_other() {
            <? if($GLOBALS['shop_language']['checkout_confirmation_text_module'] != '') { ?>
            if (document.form_user_order.input_agb_checked.checked == false) {

                alert("Bitte bestätigen Sie die AGB um fortzufahren");
                document.forms[0].input_agb_checked.focus();

            }
            else {
                <? } ?>
                var str = "<?= ml("","action","order","action","payment","")?>";
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