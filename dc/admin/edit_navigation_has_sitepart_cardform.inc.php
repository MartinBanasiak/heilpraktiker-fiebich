<h2><?= $frontend_navigation["menu_name"] ?></h2>
<form id="form_navigation_has_sitepart_card" name="form_navigation_has_sitepart_card" method="post">
    <input name="input_navigation_has_sitepart_id" id="input_navigation_has_sitepart_id" type="hidden"
           value="<?= $input_navigation_has_sitepart["navigation_has_sitepart_id"] ?>">

    <div class="toolbar">
        <a class="button_edit" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_card.action='?frontend_nav=<?= $frontend_navigation["id"] ?>&action=list'; form_navigation_has_sitepart_card.submit(); return false;">&Uuml;bersicht</a>
        <a class="button_new" href="javascript:void(0);" onclick="toggle('new_sitepart');">Neu</a>
        <a class="button_delete" href="javascript:void(0);"
           onclick="if(confirm('Wollen Sie das Sitepart mit allen Inhalten wirklich l&ouml;schen?')) { document.form_navigation_has_sitepart_card.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=delete_navigation_has_sitepart'; document.form_navigation_has_sitepart_card.submit(); return false; }">L&ouml;schen</a>
        <a class="button_save" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_card.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=save_navigation_has_sitepart'; document.form_navigation_has_sitepart_card.submit(); return false;">Speichern</a>
        <a class="button_reset" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_card.reset(); return false;">Zur&uuml;cksetzen</a>
    </div>

    <div class="subtoolbar" id="new_sitepart" style="display:none;">
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><? sitepart_select("Typ", "input_sitepart_type"); ?></td>
                <td><a class="button_new" href="javascript:void(0);"
                       onclick="form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=new_navigation_has_sitepart'; document.form_navigation_has_sitepart_list.submit(); return false;">Einf&uuml;gen</a>
                </td>
            </tr>
        </table>
    </div>

    <table class="cardform" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <? sitepart_select_dd("Typ", "input_sitepart_type", $input_navigation_has_sitepart["main_sitepart_id"], TRUE); ?>
                <? layout_area_select("Layout Area", "input_layout_area_id", $input_navigation_has_sitepart["layout_area_id"]); ?>
                <? input("Beschreibung", "input_user_description", "text", $input_navigation_has_sitepart["user_description"], 45) ?>
                <br />

                <? for ($count = 1; $count <= 5; $count++) {
                    if ($input_navigation_has_sitepart["value_" . $count . "_name"] <> '') {
                        input($input_navigation_has_sitepart["value_" . $count . "_name"], "input_value_" . $count, "text", $input_navigation_has_sitepart["value_" . $count]);
                    }
                }
                ?>

            </td>
            <td>
                <? input("Aktiv", "input_active", "checkbox", $input_navigation_has_sitepart["active"]) ?>
            </td>
        </tr>
    </table>
</form>

<?
$adminIncludeFilePath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $input_navigation_has_sitepart["admin_include"];
if (file_exists($adminIncludeFilePath) && is_file($adminIncludeFilePath) && is_readable($adminIncludeFilePath)) {
    $sitepart = $input_navigation_has_sitepart;
    require_once $adminIncludeFilePath;
    if (function_exists($sitepart["admin_start_parameter"])) {
        $sitepart["admin_start_parameter"]($sitepart);
    }
}
?>