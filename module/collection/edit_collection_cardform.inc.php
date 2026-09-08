<?
$formname      = "form_collection_cardform";
$translation   = \DynCom\dc\common\classes\Registry::get("translation");
$inputname     = "input_id";
$input_page_id = (isset($_REQUEST["input_page_id"]) ? $_REQUEST["input_page_id"] : "");

// erstelle kollektionen sammeln
$collection_setups = array();
$query             = "SELECT * FROM main_collection_setup WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1)";
$result            = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_array($result, 1)) {
    $collection_setups[$row['id']] = $row;
}

// erstellte kontaktformulare sammeln
$contactforms = array();
$query        = "SELECT * FROM contactform_header WHERE (main_language_id = " . (int)$GLOBALS["language"]['id'] . " OR all_languages = 1) AND collection_header = 0";
$result       = @mysqli_query($GLOBALS['mysql_con'], $query);
while ($row = @mysqli_fetch_array($result, 1)) {
    $contactforms[$row['id']] = $row;
}

$sorting_values = array(
    $translation->get("collection_sorting_keep"),
    $translation->get("collection_sorting_change_top"),
    $translation->get("collection_sorting_change_bottom")
);

?>

    <script type="text/javascript">
        function saveCollection( _close ) {

            var aFormData = new FormData();

            // wird benoetigt damit die Inhalte des fckeditors uebertragen werden koennen
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            $('#overlayContent :input').each(function () {
                if ($(this).attr("type") == "file") {
                    aFormData.append($(this).attr("name"), $(this).get(0).files[0]);
                } else if ($(this).attr("type") == "checkbox") {
                    if ($(this).prop("checked") === true) {
                        aFormData.append($(this).attr("name"), "on");
                    }
                } else {
                    aFormData.append($(this).attr("name"), $(this).val());
                }
            });

            loadCard('save_collection', true, '', _close, aFormData);
        }

        function toggle_registration_forms( _checkbox ) {
            if ($(_checkbox).is(":checked")) {
                $('.registration_select', $('#<?php echo $formname; ?>')).show();
            } else {
                $('.registration_select', $('#<?php echo $formname; ?>')).hide();
            }
        }

        function delete_image(_id) {
            if(!confirm("<?php echo $translation->get("delete_image"); ?>")) {
                return false;
            }
            $('#delete_collection_image_' + _id).val("on");
            $('#overlayWrapper ul.toolbar_menu').find('.button_save').first().click();
        }

    </script>


    <div id="overlaycrumb">
        <?php if ($input_collection["id"] == "") {
            echo $translation->get("new_collection");
        } else {
            echo $translation->get("edit_collection");
        }
        ?>
        <div id="closeoverlay" onclick="disableOverlay();"></div>
    </div>

<?php if (count($collection_setups) == 0) {

    // Wenn keine Kollektionseinstellungen angelegt wurden, dann nicht weiter machen

    echo "<div class=\"infobox\">" . $translation->get("no_rows_found") . "</div>";

    exit();
}
?>

    <ul class="toolbar_menu">
        <?php
        // Zurueck zur uebersicht
        if (is_page_edit()) {
            button("left", $translation->get("show_all"), $formname, "loadCard('list_collection_page', true)");
        }

        // Speichern button
        button("save", $translation->get("save"), $formname, "saveCollection(false)");

        // Speichern und schliessen
        button("save", $translation->get("save_and_close"), $formname, "saveCollection(true)");
        ?>

        <li>
            <ul>
                <?php


                // Loeschen  button
                if ($input_collection["id"] != "" && is_page_edit() === FALSE) {
                    echo button("delete", $translation->get("delete"), $formname, "loadCard('delete_collection', true, '{$translation->get('delete_collection_confirm')}', true)", "", FALSE);
                }

                // Zuruecksetzen
                button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE);
                ?>
            </ul>
        </li>

    </ul>
    <div class="clearfix"></div>

<?php
if (is_array($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
}
?>

    <form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
        <input name="input_collection_id" type="hidden" value="<?= $input_collection["id"] ?>">
        <input type="hidden" name="main_collection_setup_id"
               value="<?php echo $input_collection['main_collection_setup_id']; ?>" />
        <input type="hidden" name="get_real_page_link_id" value="1" />
        <input type="hidden" name="input_page_id" value="<?php echo $input_page_id; ?>" />

        <table class="cardform" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>

                    <?php
                    input($translation->get("description"), "input_description", "text", $input_collection["description"], 255);
                    input_select($translation->get("collection_change_sorting"), "input_change_sorting", $values = array_keys($sorting_values), $value_names = array_values($sorting_values), "", FALSE, FALSE);
                    input($translation->get("registration_possible"), "input_registration", "checkbox", $input_collection["registration"], NULL, FALSE, FALSE, 'onchange="toggle_registration_forms(this);"');
                    create_contactform_select($contactforms, $translation->get("registration_form"), "registration_contactform_id", $input_collection, (int)$input_collection["registration"] == 0 ? TRUE : FALSE);
                    input($translation->get("validity_from"), "input_validity_from", "date", datefromsql($input_collection["validity_from"]), 10);
                    input($translation->get("validity_to"), "input_validity_to", "date", datefromsql($input_collection["validity_to"]), 10);
                    ?>


                </td>
                <td>
                    <?php
                    if ($input_collection["id"] != "") {
                        show_changed_on($input_collection["id"], "main_collection");
                        show_changed_by($input_collection["id"], "main_collection");
                        create_collection_groups_select($input_collection);
                    }

                    ?>
                </td>
            </tr>
            <tr><td colspan="2"><div class="collection_field" style="padding:0px;padding-bottom:10px;margin-top:10px;height:1px;">&nbsp;</div></td> </tr>
            <tr>
                <td>
                    <?php
                    input($translation->get("browsertitle"), "input_browser_title", "text", $input_collection["subtitle"], 255);
                    input($translation->get("meta_description"), "input_meta_description", "textarea", $input_collection["meta_description"]);
                    ?>
                </td>
                <td>
                    <?php
                    input($translation->get("noindex"), "input_noindex", "checkbox", $input_collection["noindex"]);
                    input($translation->get("nofollow"), "input_nofollow", "checkbox", $input_collection["nofollow"]);
                    ?>
                </td>
            </tr>

        </table>
    </form>

    <br />
<?
if ($input_collection['id'] != "") {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'edit_collection_line_listform.inc.php';
}
?>