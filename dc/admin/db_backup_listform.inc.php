<? $formname = "form_backup_list"; ?>

<div class="toolbar">
    <?= button("new", "Erstellen", $formname, "?action=create"); ?>
    <?= button("delete", "Löschen", $formname, "?action=delete", "Wollen Sie die Datensicherung wirklich löschen?"); ?>
</div>

<form id="<?= $formname ?>" name="<?= $formname ?>" method="post">
    <table cellpadding="0" cellspacing="0" border="0" class="linklist">
        <tr>
            <td>&nbsp;</td>
            <td>Datum</td>
            <td>Dateiname</td>
        </tr>
        <?
        $path    = "../../userdata/db_backup/";
        $counter = 0;
        $dir     = opendir($path);
        while (FALSE !== ($file = readdir($dir))) {
            if (preg_match("/^.+\.(sql)\$/U", $file)) {
                $filetime = filemtime($path . $file);
                $checked  = ($file == $_POST[$input_filename]) ? " checked=\"checked\"" : "";
                echo "  <tr onclick=\"document." . $formname . ".input_filename[" . $counter . "].checked = true;\">\n";
                echo "    <td style=\"width: 20px;\"><div style=\"text-align:center;\"><input style=\"width: 14px; height: 14px; padding: 0px; margin: 0px;\" id=\"input_filename\" name=\"input_filename\" type=\"radio\" value=\"" . $file . "\"" . $checked . " /></div></td>";
                echo "    <td><div>vom " . date("d.m.Y", $filetime) . " um " . date("H:i", $filetime) . " Uhr</div></td>\n";
                echo "    <td><div>" . $file . "</div></td>\n";
                echo "  </tr>\n";
                $counter++;
            }
        }

        closedir($dir);
        if ($counter = 0) {
            echo "<div class=\"infobox\">Keine Datens&auml;tze vorhanden</div>";
        }
        ?>
    </table>
</form>