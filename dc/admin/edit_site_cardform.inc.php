<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
$formname    = "form_site_card";
$inputname   = "input_id";
?>

<div id="overlaycrumb">
    <?php if ($input_site["id"] == "") {
        echo $translation->get("new_website");
    } else {
        echo sprintf($translation->get("edit_website"), $input_site["name"]);
    }
    ?>
    <div id="closeoverlay" onclick="disableOverlay();"></div>
</div>


<ul class="toolbar_menu">
    <?= button("save", $translation->get("save"), $formname, "loadCard('save', true)"); ?>
    <?= button("save", $translation->get("save_and_close"), $formname, "loadCard('save', true, '', true)"); ?>
    <?php
    if ($input_site["id"] != "") {
        echo button("copy", $translation->get("copy_site"), $formname, "copy_site('" . $formname . "')");
    }
    ?>
    <li>
        <ul>
            <?php
            if ($input_site["id"] != "") {
                echo button("delete", $translation->get("delete"), $formname, "loadCard('delete', true, '{$translation->get('delete_website_confirm')}', true)", "", FALSE);
            }
            ?>
            <?= button("reset", $translation->get("restore"), $formname, "?action=edit", "", FALSE); ?>
        </ul>
    </li>
</ul>
<div class="clearfix"></div>


<?php
if (is_array($messages) && count($messages)) {
    echo '<div id="overlayMessages">' . join("\r\n", $messages) . '</div>';
} else {
    echo '<div id="overlayMessages"></div>';
}
?>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <input name="input_id" type="hidden" value="<?= $input_site["id"] ?>">
    <input name="input_std_main_language_id" type="hidden" value="<?= $input_site["std_main_language_id"] ?>">

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? input($translation->get("textkey"), "input_code", "code", $input_site["code"], 20) ?>
                <? input($translation->get("description"), "input_name", "text", $input_site["name"], 45) ?>
                <? input($translation->get("domain"), "input_site_url", "text", $input_site["site_url"], 80) ?>
                <? input($translation->get("use_as_unique_site"), "input_is_unique_site", "checkbox", $input_site["is_unique_site"]) ?>
                <? language_select($translation->get("default_language"), "input_std_main_language_id", $input_site["std_main_language_id"], FALSE, $input_site["id"]) ?>
                <? input($translation->get("google_tag_container_id"), "input_google_tag_container_id", "code", $input_site["google_tag_container_id"], 45) ?>
                <? input($translation->get("google_analytics_id"), "input_google_analytics_id", "code", $input_site["google_analytics_id"], 45) ?>
                <? input($translation->get("facebook_pixel_id"), "input_facebook_pixel_id", "code", $input_site["facebook_pixel_id"], 45) ?>
                <? input($translation->get("default_country_codes"), "input_default_country_codes", "textarea", $input_site["default_country_codes"]) ?>
                <? input($translation->get("number_menu_items"), "input_no_of_navigation", "code", $input_site["no_of_navigation"], 20, TRUE) ?>

            </td>
            <td>
                <? input($translation->get("use_as_default_site"), "input_is_standard_site", "checkbox", $input_site["is_standard_site"]) ?>
                <? input($translation->get("login_required"), "input_login_required", "checkbox", $input_site["login_required"]) ?>
                <? input($translation->get("use_session_id"), "input_use_session_id", "checkbox", $input_site["use_session_id"]) ?>
                <? input($translation->get("create_xml_sitemap"), "input_create_xml_sitemap", "checkbox", $input_site["create_xml_sitemap"]) ?>

                <? input($translation->get("use_browser_language_detection"), "input_use_browser_language_detection", "checkbox", $input_site["use_browser_language_detection"]) ?>
                <? input($translation->get("use_ip_detection"), "input_use_ip_detection", "checkbox", $input_site["use_ip_detection"]) ?>


            </td>
        </tr>
    </table>
</form>