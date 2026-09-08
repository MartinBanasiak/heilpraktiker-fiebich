<h2><?= $frontend_navigation["menu_name"] ?></h2>
<form id="form_navigation_has_sitepart_list" name="form_navigation_has_sitepart_list" method="post">
    <input name="input_main_navigation_id" id="input_main_navigation_id" type="hidden"
           value="<?= $frontend_navigation["id"] ?>">

    <div class="toolbar">
        <a class="button_edit" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=edit_navigation_has_sitepart'; document.form_navigation_has_sitepart_list.submit(); return false;">Karte</a>
        <a class="button_new" href="javascript:void(0);" onclick="toggle('new_sitepart');">Neu</a>
        <a class="button_delete" href="javascript:void(0);"
           onclick="if(confirm('Wollen Sie das Sitepart mit allen Inhalten wirklich l&ouml;schen?')) { document.form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=delete_navigation_has_sitepart'; form_navigation_has_sitepart_list.submit(); return false; }">L&ouml;schen</a>
        <a class="button_up" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=moveup_navigation_has_sitepart'; document.form_navigation_has_sitepart_list.submit(); return false;">Nach
            oben</a>
        <a class="button_down" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=movedown_navigation_has_sitepart'; document.form_navigation_has_sitepart_list.submit(); return false;">Nach
            unten</a>
        <a class="button_edit" target="_blank"
           href="<?= get_link_to_navigation($frontend_navigation["id"]) ?>">Vorschau</a>
        <a class="button_edit" href="javascript:void(0);"
           onclick="document.form_navigation_has_sitepart_list.action = '/dc/<?= $_GET["site"] ?>/<?= $_GET["language"] ?>/Webseite/Navigation/?action=edit_navigation'; form_navigation_has_sitepart_list.submit(); return false;">Navigation</a>
    </div>

    <div class="subtoolbar" id="new_sitepart" style="display:none;">
        <table cellpadding="0" cellspacing="0" border="0">
            <? sitepart_select("input_sitepart_type"); ?>
        </table>
        <br /><a class="button_new" href="javascript:void(0);"
                 onclick="form_navigation_has_sitepart_list.action = '?frontend_nav=<?= $frontend_navigation["id"] ?>&action=new_navigation_has_sitepart'; document.form_navigation_has_sitepart_list.submit(); return false;">Einf&uuml;gen</a>
    </div>

    <?
    $query  = "SELECT main_navigation_has_sitepart.id, main_sitepart.name as 'Typ', user_description as 'Beschreibung', active AS 'Status', CONCAT(date_format(modified_date, '%d.%m.%Y, %H:%i:%s'),' von ',(SELECT name FROM main_admin_user WHERE main_admin_user.id = main_navigation_has_sitepart.modified_user_id)) AS 'Letzte Änderung' FROM main_navigation_has_sitepart LEFT JOIN main_sitepart ON main_navigation_has_sitepart.main_sitepart_id = main_sitepart.id WHERE main_navigation_id = " . $frontend_navigation["id"] . " ORDER BY sorting ASC";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    $format = array("option", "text", "text", "boolean_active", "text");
    linklist($result, "form_navigation_has_sitepart_list", $format, "input_navigation_has_sitepart_id");
    ?>

</form>
