<?
$formname = "form_language_card";
$translation = \DynCom\dc\common\classes\Registry::get("translation");

static $locales = [
    'af-ZA',
    'am-ET',
    'ar-AE',
    'ar-BH',
    'ar-DZ',
    'ar-EG',
    'ar-IQ',
    'ar-JO',
    'ar-KW',
    'ar-LB',
    'ar-LY',
    'ar-MA',
    'arn-CL',
    'ar-OM',
    'ar-QA',
    'ar-SA',
    'ar-SY',
    'ar-TN',
    'ar-YE',
    'as-IN',
    'az-Cyrl-AZ',
    'az-Latn-AZ',
    'ba-RU',
    'be-BY',
    'bg-BG',
    'bn-BD',
    'bn-IN',
    'bo-CN',
    'br-FR',
    'bs-Cyrl-BA',
    'bs-Latn-BA',
    'ca-ES',
    'co-FR',
    'cs-CZ',
    'cy-GB',
    'da-DK',
    'de-AT',
    'de-CH',
    'de-DE',
    'de-LI',
    'de-LU',
    'dsb-DE',
    'dv-MV',
    'el-GR',
    'en-029',
    'en-AU',
    'en-BZ',
    'en-CA',
    'en-GB',
    'en-IE',
    'en-IN',
    'en-JM',
    'en-MY',
    'en-NZ',
    'en-PH',
    'en-SG',
    'en-TT',
    'en-US',
    'en-ZA',
    'en-ZW',
    'es-AR',
    'es-BO',
    'es-CL',
    'es-CO',
    'es-CR',
    'es-DO',
    'es-EC',
    'es-ES',
    'es-GT',
    'es-HN',
    'es-MX',
    'es-NI',
    'es-PA',
    'es-PE',
    'es-PR',
    'es-PY',
    'es-SV',
    'es-US',
    'es-UY',
    'es-VE',
    'et-EE',
    'eu-ES',
    'fa-IR',
    'fi-FI',
    'fil-PH',
    'fo-FO',
    'fr-BE',
    'fr-CA',
    'fr-CH',
    'fr-FR',
    'fr-LU',
    'fr-MC',
    'fy-NL',
    'ga-IE',
    'gd-GB',
    'gl-ES',
    'gsw-FR',
    'gu-IN',
    'ha-Latn-NG',
    'he-IL',
    'hi-IN',
    'hr-BA',
    'hr-HR',
    'hsb-DE',
    'hu-HU',
    'hy-AM',
    'id-ID',
    'ig-NG',
    'ii-CN',
    'is-IS',
    'it-CH',
    'it-IT',
    'iu-Cans-CA',
    'iu-Latn-CA',
    'ja-JP',
    'ka-GE',
    'kk-KZ',
    'kl-GL',
    'km-KH',
    'kn-IN',
    'kok-IN',
    'ko-KR',
    'ky-KG',
    'lb-LU',
    'lo-LA',
    'lt-LT',
    'lv-LV',
    'mi-NZ',
    'mk-MK',
    'ml-IN',
    'mn-MN',
    'mn-Mong-CN',
    'moh-CA',
    'mr-IN',
    'ms-BN',
    'ms-MY',
    'mt-MT',
    'nb-NO',
    'ne-NP',
    'nl-BE',
    'nl-NL',
    'nn-NO',
    'nso-ZA',
    'oc-FR',
    'or-IN',
    'pa-IN',
    'pl-PL',
    'prs-AF',
    'ps-AF',
    'pt-BR',
    'pt-PT',
    'qut-GT',
    'quz-BO',
    'quz-EC',
    'quz-PE',
    'rm-CH',
    'ro-RO',
    'ru-RU',
    'rw-RW',
    'sah-RU',
    'sa-IN',
    'se-FI',
    'se-NO',
    'se-SE',
    'si-LK',
    'sk-SK',
    'sl-SI',
    'sma-NO',
    'sma-SE',
    'smj-NO',
    'smj-SE',
    'smn-FI',
    'sms-FI',
    'sq-AL',
    'sr-Cyrl-BA',
    'sr-Cyrl-CS',
    'sr-Cyrl-ME',
    'sr-Cyrl-RS',
    'sr-Latn-BA',
    'sr-Latn-CS',
    'sr-Latn-ME',
    'sr-Latn-RS',
    'sv-FI',
    'sv-SE',
    'sw-KE',
    'syr-SY',
    'ta-IN',
    'te-IN',
    'tg-Cyrl-TJ',
    'th-TH',
    'tk-TM',
    'tn-ZA',
    'tr-TR',
    'tt-RU',
    'tzm-Latn-DZ',
    'ug-CN',
    'uk-UA',
    'ur-PK',
    'uz-Cyrl-UZ',
    'uz-Latn-UZ',
    'vi-VN',
    'wo-SN',
    'xh-ZA',
    'yo-NG',
    'zh-CN',
    'zh-HK',
    'zh-MO',
    'zh-SG',
    'zh-TW',
    'zu-ZA',
];

//Vorauswahl für Logout-Verweis füllen
$query = "SELECT id, code, name FROM main_site ORDER BY id ASC";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$sites = array();
$i = 0;
WHILE ($row = @mysqli_fetch_array($result)) {
    $sites[$i]["id"] = $row["id"];
    $sites[$i]["code"] = $row["code"];
    $sites[$i]["name"] = $row["name"];
    $i++;
}

$query = "
SELECT 
	logout_site_id AS 'logout_site',
	logout_language_id AS 'logout_language',
	logout_navigation_id AS 'logout_navigation'
FROM 
	main_language
WHERE 
	id = '" . $GLOBALS["language"]["id"] . "'
";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$preselect = @mysqli_fetch_assoc($result);
extract($preselect);

IF (isset($logout_site)) {
    $site_preselect = site_getbycode($logout_site);
    $sitecode = $site_preselect["code"];
}

IF (isset($logout_site)) {
    $query_1 = "
	SELECT 
		ml.id AS 'id', 
		ml.name AS 'name', 
		ms.id AS 'ms_id' 
	FROM 
		main_language ml, 
		main_site ms 
	WHERE 
		ms.id = '" . $logout_site . "' 
	AND 
		ml.main_site_id = ms.id
	";
    $result_1 = @mysqli_query($GLOBALS['mysql_con'], $query_1);
    $logout_langs = "<option value=\"\">" . $translation->get("please_choose") . "</option>";
    WHILE ($row = mysqli_fetch_assoc($result_1)) {
        IF (isset($logout_site) && $row["id"] == $logout_language) {
            $logout_langs .= "<option value=\"" . $row["id"] . "\" selected=\"selected\">" . $row["name"] . "</option>";
        } ELSE {
            $logout_langs .= "<option value=\"" . $row["id"] . "\">" . $row["name"] . "</option>";
        }
    }
}

IF (isset($logout_language)) {
    $logout_navs = navigation_select_noecho($logout_navigation, $logout_site, $logout_language);
}

//Vorauswahl für Mandant-Shop-Sprache füllen
$query = "SELECT id, company FROM shop_setup";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
$companies = array();
WHILE ($row = @mysqli_fetch_array($result)) {
    $companies[]["company"] = $row["company"];
}
$company_pre = $input_language["company"];
$shop_pre = $input_language["shop_code"];
$lang_pre = $input_language["shop_language_code"];

IF (!empty($company_pre)) {
    $query_1 = "SELECT code, description FROM shop_shop WHERE company = '" . $company_pre . "'";
    $result_1 = @mysqli_query($GLOBALS['mysql_con'], $query_1);
    $shops = "<option value=\"\">" . $translation->get("please_choose") . "</option>";
    WHILE ($row = @mysqli_fetch_assoc($result_1)) {
        IF (isset($shop_pre) && $row["code"] == $shop_pre) {
            $shops .= "<option value=\"" . $row["code"] . "\" selected=\"selected\">" . $row["description"] . "</option>";
        } ELSE {
            $shops .= "<option value=\"" . $row["code"] . "\">" . $row["description"] . "</option>";
        }
    }
    IF (!empty($shop_pre)) {
        $query_2 = "SELECT code, description FROM shop_language WHERE shop_code = '" . $shop_pre . "'";
        $result_2 = @mysqli_query($GLOBALS['mysql_con'], $query_2);
        $langs = "<option value=\"\">" . $translation->get("please_choose") . "</option>";
        WHILE ($row = @mysqli_fetch_assoc($result_2)) {
            IF (isset($lang_pre) && $row["code"] == $lang_pre) {
                $langs .= "<option value=\"" . $row["code"] . "\" selected=\"selected\">" . $row["description"] . "</option>";
            } ELSE {
                $langs .= "<option value=\"" . $row["code"] . "\">" . $row["description"] . "</option>";
            }
        }
    }
}


?>

<div id="overlaycrumb">
    <?php if ($input_language["id"] == "") {
        echo $translation->get("new_language");
    } else {
        echo $translation->get("edit_language");
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>

<ul class="toolbar_menu">
    <?= button("save", $translation->get("save"), $formname, "loadCard('save', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save', true, '', true)"); ?>
    <?php
    if ($input_language["id"] != "") {
        echo button("copy", $translation->get("copy_language"), $formname, "copy_language()");
    }
    ?>
    <li>
        <ul>
            <?php
            if ($input_language["id"] != "") {
                echo button(
                    "delete",
                    $translation->get("delete"),
                    $formname,
                    "loadCard('delete', true, '{$translation->get('delete_language_confirm')}', true)",
                    "",
                    false
                );
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", false); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>

<?php
if (count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
} else {
    echo '<div id="overlayMessages"></div>';
    $active = false;
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_language["id"] ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("textkey"), "input_code", "code", $input_language["code"], 20) ?>
                <? input_select($translation->get('Locale-Code'),'input_locale_code',$locales,$locales,$input_language['locale_code'])?>
                <? input($translation->get("description"), "input_name", "text", $input_language["name"], 45) ?><br/>
                <? navigation_select(
                    $translation->get("startpage"),
                    "input_std_main_navigation_id",
                    $input_language["std_main_navigation_id"],
                    $input_language["main_site_id"],
                    $input_language["id"],
                    false
                ); ?>
                <? layout_select(
                    $translation->get("layout"),
                    "input_main_layout_id",
                    $input_language["main_layout_id"],
                    false
                ) ?>
                <br/>
                <? input(
                    $translation->get("browsertitle"),
                    "input_site_title_name",
                    "text",
                    $input_language["site_title_name"],
                    45
                ) ?>
                <br/>
                <? input(
                    $translation->get("meta_description"),
                    "input_meta_description",
                    "textarea",
                    $input_language["meta_description"]
                ) ?>
                <? input(
                    $translation->get("meta_keywords"),
                    "input_meta_keywords",
                    "textarea",
                    $input_language["meta_keywords"]
                ) ?>
            </td>
            <td>
                <? input(
                    $translation->get("number_menu_items"),
                    "input_no_of_navigation",
                    "decimal",
                    $input_language["no_of_navigation"],
                    45,
                    true
                ) ?>
                <br/>

                <div class="label"><label for="input_login_site"><?php echo $translation->get("logout_page"); ?></label>
                </div>
                <div class="input"><select class="bigselect" name="input_login_site" id="site_id"
                                           onChange="LoginAJAXreq1();">
                        <option value=""> <?php echo $translation->get("please_choose"); ?> </option>
                        <? FOR ($i = 0; $i < count($sites); $i++) {
                            IF (isset($logout_site) && $sites[$i]["id"] == $logout_site) {
                                echo "<option value=\"" . $sites[$i]["id"] . "\" selected=\"selected\">" . $sites[$i]["name"] . "</options>";
                            } ELSE {
                                echo "<option value=\"" . $sites[$i]["id"] . "\">" . $sites[$i]["name"] . "</option>";
                            }
                        }
                        ?>
                    </select></div>
                <div class="label"><label
                            for="input_login_language"><?php echo $translation->get("logout_language"); ?></label></div>
                <div class="input"><select name="input_login_language" class="bigselect" id="main_language_id"
                                           onChange="LoginAJAXreq2();">
                        <?= $logout_langs ?>
                    </select></div>
                <div class="label"><label
                            for="input_login_navigation"><?php echo $translation->get("logout_navigation"); ?></label>
                </div>
                <div class="input"><select class="bigselect" name="input_login_navigation" id="main_navigation_id">
                        <?= $logout_navs ?>
                    </select></div>
                <br/>

                <div class="label"><label for="input_company"><?php echo $translation->get("shop_mandant"); ?></label>
                </div>
                <div class="input"><select class="bigselect" name="input_company" id="company"
                                           onChange="LangAJAXreq1();">
                        <option value=""><?php echo $translation->get("please_choose"); ?></option>
                        <? FOR ($i = 0; $i < count($companies); $i++) {
                            IF (isset($company_pre) && $companies[$i]["company"] == $company_pre) {
                                echo "<option value=\"" . $companies[$i]["company"] . "\" selected=\"selected\">" . $companies[$i]["company"] . "</options>";
                            } ELSE {
                                echo "<option value=\"" . $companies[$i]["company"] . "\">" . $companies[$i]["company"] . "</option>";
                            }
                        }
                        ?>
                    </select></div>
                <div class="label"><label for="input_shopcode"><?php echo $translation->get("shop_code"); ?></label>
                </div>
                <div class="input"><select name="input_shopcode" class="bigselect" id="shopcode"
                                           onChange="LangAJAXreq2();">
                        <? IF (isset($company_pre)) {
                            echo $shops;
                        } ?>
                    </select></div>
                <div class="label"><label
                            for="input_language_code"><?php echo $translation->get("shop_language"); ?></label></div>
                <div class="input"><select class="bigselect" name="input_language_code" id="language_code">
                        <? IF (isset($company_pre)) {
                            echo $langs;
                        } ?>
                    </select></div>
                <? input(
                    $translation->get("related_language_codes"),
                    "input_related_language_codes",
                    "textarea",
                    $input_language["related_language_codes"]
                ) ?>
                <? /*input(
                    $translation->get("catalog_login"),
                    "input_catalog_login",
                    "checkbox",
                    $input_language["catalog_login"]
                ) */ ?>
                <? input($translation->get("active"), "input_active", "checkbox", $input_language["active"]) ?>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    function copy_language() {

        if (!confirm("<?php echo $translation->get("copy_language_confirm"); ?>")) {
            return;
        }

        // loader wird engezeigt
        jQuery('#overlayLoader').show();
        jQuery('body').append(jQuery('<div />').attr("id", "copyOverlay"));

        jQuery("#copyOverlay").append("<p><?php echo $translation->get("copy_start"); ?></p>");

        copy_ajax_call("copy_language");
    }

    function copy_ajax_call(step) {
        var formData = new FormData(jQuery('#<?php echo $formname; ?>')[0]);
        formData.append("next_step", step);
        $.ajax({
            url: "?action=copy",
            type: 'POST',
            data: formData,
            cache: false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function (output, status, xhr) {
                if (xhr.getResponseHeader('REQUIRES_AUTH') == 1) {
                    window.location.replace("/dc/");
                } else {
                    if (output.next_step != "") {
                        jQuery("#copyOverlay").append("<p>" + output.message + "</p>");
                        copy_ajax_call(output.next_step);
                        return;
                    }

                    setTimeout(function () {
                        $.each(output.message, function (index, value) {
                            jQuery('#overlayMessages').html(value);
                        });

                        jQuery('#overlayLoader').hide();
                        jQuery('#overlayContent').show();
                        jQuery("#copyOverlay").remove();
                    }, 1500);
                }
            },
            dataType: 'json'
        });
    }
</script>

<style>
    #copyOverlay {
        position: fixed;
        background-color: #fff;
        border: 2px solid #666;
        padding: 10px;
        width: 500px;
        left: 50%;
        top: 40px;
        margin-left: -250px;
        z-index: 2004;
        min-height: 100px;
    }
</style>
