<?php
/*
$login['email']		= 'kontakt@dc-solution.de';
$login['password']	= 'IG#2013x';
$login['account']	= 'Agrobs';
$login['url']		= 'http://mailing.dc-solution.de/';
$login['charset']	= 'iso-8859-1';
$login['verbose']	= true;
$login['database']	= 'vegandia_ah';
*/

//SH: 20.01.14 Funktionen einbinden
//require_once("api_functions.inc.php");
require_once ("copernica_rest_api.php");

$helper['link_subscribe']   = '?action=subscribe';
$helper['link_unsubscribe'] = '?action=unsubscribe';
$helper['module_path']      = '/module/dcshop/newsletter_copernica/';
?>

    <link rel="stylesheet" href="<?= $helper['module_path']; ?>/css/newsletter_copernica.css" type="text/css">
    <div id="copernica_wrapper">
        <form id="copernica_subscribe" name="copernica_subscribe" class="copernica_form"
              action="<?= $helper['link_subscribe']; ?>" method="POST">

            <?php
            // function input($name,$inputname,$typ,$value = "on",$maxlength = NULL,$disabled = FALSE,$evaluate = FALSE,$script="")
            // function input_select($name,$inputname,$values, $value_names, $preselect = "", $disabled = FALSE, $auto_update = FALSE)
            $ah_anrede_labels[] = $GLOBALS["tc"]["mrs"];
            $ah_anrede_values[] = 'f';
            $ah_anrede_labels[] = $GLOBALS["tc"]["mr"];
            $ah_anrede_values[] = 'm';
            input_select($GLOBALS["tc"]["salutation"] . " *", "Anrede", $ah_anrede_values, $ah_anrede_labels, $_POST['Anrede'], $ah_disabled, $ah_autoupdate);


            $ah_typ = 'text';
            input($GLOBALS["tc"]["surname"] . " *", "Vorname", $ah_typ, $_POST['Vorname'], $ah_maxlenght, $ah_disabled, TRUE, $ah_script);
            input($GLOBALS["tc"]["lastname"] . " *", "Nachname", $ah_typ, $_POST['Nachname'], $ah_maxlenght, $ah_disabled, TRUE, $ah_script);
            input($GLOBALS["tc"]["email"] . " *", "EMail", $ah_typ, $_POST['EMail'], $ah_maxlenght, $ah_disabled, TRUE, $ah_script);
            input($GLOBALS["tc"]["birthday"], "Geburtsdatum", $ah_typ, $_POST['Geburtsdatum'], $ah_maxlenght, $ah_disabled, $ah_evaluate, $ah_script);
            input($GLOBALS["tc"]["area_code"], "PLZ", $ah_typ, $_POST['PLZ'], $ah_maxlenght, $ah_disabled, $ah_evaluate, $ah_script);
            input($GLOBALS["tc"]["city"], "Ort", $ah_typ, $_POST['Ort'], $ah_maxlenght, $ah_disabled, $ah_evaluate, $ah_script);
            ?>
            <input id='subscribe_button' type=submit value='<?= $GLOBALS["tc"]["register_nl"] ?>'>
        </form>

        <br />

        <h2>
            <?= $GLOBALS["tc"]["unregister_nl"] ?></h2>
        <br />
        <?= $GLOBALS["tc"]["unregister_nl_text"] ?>
        <br />

        <form id="copernica_unsubscribe" name="copernica_unsubscribe" class="copernica_form"
              action="<?= $helper['link_unsubscribe']; ?>" method="POST">

            <?php
            // function input($name,$inputname,$typ,$value = "on",$maxlength = NULL,$disabled = FALSE,$evaluate = FALSE,$script="")
            // function input_select($name,$inputname,$values, $value_names, $preselect = "", $disabled = FALSE, $auto_update = FALSE)

            $ah_typ = 'text';
            input($GLOBALS["tc"]["email"] . " *", "EMail", $ah_typ, $_POST['EMail'], $ah_maxlenght, $ah_disabled, TRUE, $ah_script);
            //button('subscribe copernica_subscribe', 'Abmelden', 'copernica_unsubscribe', $helper['link_unsubscribe'], $ah_confirm);
            ?>
            <input id='unsubscribe_button' type=submit value='<?= $GLOBALS["tc"]["unregister"] ?>'>
        </form>

        <div class="pflichtfeld">* <?= $GLOBALS["tc"]["required_fields"] ?></div>
    </div>

<?php
// +++ ANMELDUNG +++
if ($_GET['action'] == 'subscribe') {

    // (C) E-Mail Richtigkeit
    if (!filter_var($_POST['EMail'], FILTER_VALIDATE_EMAIL)) {
        echo '<script type="text/javascript"> $(function () { $("form#copernica_subscribe input#email").addClass("text_error"); }); </script>';
        $error_msg_email = $GLOBALS["tc"]["enter_valid_email"];
    }

    // (C) Alle Pflichtfelder angegeben
    if ($_POST['Anrede'] && $_POST['Vorname'] && $_POST['Nachname'] && $_POST['EMail']) {

        if ($error_msg_email != '') { ?>
            <script type="text/javascript">
                $(function () {
                    alert("<?= $error_msg_email; ?>");
                });
            </script><?

        } else { ?>
            <script type="text/javascript">
                $(function () {
                    $("#copernica_subscribe select, #copernica_subscribe input").attr("disabled", "disabled");
                    //$("#copernica_subscribe a.button_subscribe").attr("href","").attr("onclick","return false;").css("cursor","default").text("Anmeldung erfolgreich");
                });
                var subscribe_button = document.getElementById('subscribe_button').value = '<?= $GLOBALS["tc"]["register_success"] ?>';
            </script><?

            unset($actions, $values);
            $actions           = array("add_update_profile");
            $values['profile'] = array2values($_POST);
            // SOI == 1 { subscribe }
            $values['profile']['SOI'] = 1;
            // DOI == 0 { double opt in unbestätigt! }
            $values['profile']['DOI'] = 0;
            // OOI == 0 { nicht während bestellprozess bestellt }
            // OOI == 1 { während bestellprozess akzeptiert }
            // OOI == 2 { während bestellprozess nicht akzeptiert }
            $values['profile']['OOI']            = 0;
            $values['profile']['DatumAnmeldung'] = date("Y-m-d H:i:s");
            $values['profile']['Quelle']         = 'Manuelle Anmeldung';
            copernicaAPI($login, $actions, $values);

            /*
            // Beispiel Daten für add_collection und import_profiles
            // $actions = array("add_update_profile","add_collection");
            $values['item'][] = array('Bestellnr' => '0000002', 'Artikelnr' => 'A');
            $values['item'][] = array('Bestellnr' => '0000002', 'Artikelnr' => 'B');
            $values['item'][] = array('Bestellnr' => '0000002', 'Artikelnr' => 'C');

            $values['order']  = array(
                                      'Bestellnr' 	 => '0000002',
                                      'Gesamtbetrag' => 200.48,
                                      'Datum'  		 => date("Y-m-d")
                                     );
                                     */

            /*
            // $actions = array("import_profiles");
            $values['import'][] = array(
                'overwrite' => false,
                'Anrede' => '',
                'Vorname' => '',
                'Nachname' => '',
                'EMail' => '',
                'Name' => '',
                'Geburtsdatum' => '',
                'PLZ' => '',
                'Ort' => '',
                'DOI' => '',
                'SOI' => '',
                'OOI' => '',
                'Leadscore' => '',
                'LetzterKlick' => '',
                'DatumAnmeldung' => '',
                'DatumAbmeldung' => '',
                'DatumGutschein' => '',
                'Quelle' => 'z.B. Nav Import'
            );
            */


        }

    } else { ?>

        <script type="text/javascript">
            $(function () {
                alert("<?= $GLOBALS["tc"]["please_check_input"] ?>");
            });
        </script><?

    }

}
// ... ANMELDUNG ...


// +++ ABMELDUNG +++
if ($_GET['action'] == 'unsubscribe') {
    echo '<style><!-- form#copernica_subscribe input.text_error { border-color: #ABADB3 !important; --></style>';
    if (!filter_var($_POST['EMail'], FILTER_VALIDATE_EMAIL)) {

        $error_msg_email = $GLOBALS["tc"]["enter_valid_email"]; ?>
        <script type="text/javascript">
            $(function () {
                $("form#copernica_unsubscribe input#email").addClass("text_error");
                alert("<?= $error_msg_email; ?>");
            });
        </script> <?

    } else { ?>

        <script type="text/javascript">
            $(function () {
                $("#copernica_unsubscribe select, #copernica_unsubscribe input").attr("disabled", "disabled");
                //$("#copernica_unsubscribe a.button_subscribe").attr("href","").attr("onclick","return false;").css("cursor","default").text("Abmeldung erfolgreich");
            });
            var unsubscribe_button = document.getElementById('unsubscribe_button').value = '<?= $GLOBALS["tc"]["unregister_success"] ?>';
        </script><?

        unset($actions, $values);
        $actions           = array("add_update_profile", "unsubscribe");
        $values['profile'] = array2values($_POST);
        // DOI == 2 { unsubscribe }
        $values['profile']['DOI']            = 2;
        $values['profile']['DatumAbmeldung'] = date("Y-m-d H:i:s");
        //copernicaAPI($login, $actions, $values);
        copernica_api($login,$actions,$values);

    }
}
// ... ABMELDUNG ...

?>