<?
//if (isset($_FILES['input_layout_file']) && ! $_FILES['input_layout_file']['error']) {
//  move_uploaded_file($_FILES['input_layout_file']['tmp_name'], "../../layout/frontend/" . $_FILES['input_layout_file']['name']);
//  extract_zip_file($GLOBALS["site_server_path"] . "layout/frontend/" . $_FILES['input_layout_file']['name'],"../../layout/frontend/");
//printf("Die Datei %s steht jetzt als " . "newfile.jpg zur Verfügung.<br />\n",$_FILES['input_layout_file']['name']);
//printf("Sie ist %u Bytes groß und vom Typ %s.<br />\n",$_FILES['input_layout_file']['size'], $_FILES['input_layout_file']['type']);
//}


?>

<? $formname = "form_modul_list"; ?>
<form id="<?= $formname ?>" name="<?= $formname ?>" method="post" enctype="multipart/form-data">

    <div class="toolbar">
        <a class="button_install" href="javascript:void(0);" onclick="toggle('upload_layout');">Installieren</a>
        <?= button("delete", "Deinstallieren", $formname, "?action=card"); ?>
    </div>

    <div id="upload_layout" class="infobox" style="display:none">
        <table cellpadding="2" cellspacing="0" border="0">
            <tr>
                <td><?= input("Dateiname", "input_layout_file", "file") ?></td>
                <td><?= button("install", "Upload", $formname, "?action=card"); ?></td>
            </tr>
        </table>
    </div>

    <?
    $query  = "SELECT id, code AS 'Name', name AS 'Beschreibung', installed AS Status FROM main_modul ORDER BY code ASC";
    $format = array('option', 'text', 'text', 'boolean_active');
    if ($result = @mysqli_query($GLOBALS['mysql_con'], $query)) {
        linklist($result, $formname, $format);
    }
    ?>

</form>