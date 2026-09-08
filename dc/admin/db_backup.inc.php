<?
switch ($_GET["action"]) {
    case 'create':
        create_backup();
        break;
    case 'delete':
        delete_backup();
        break;
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'db_backup_listform.inc.php';

function delete_backup() {
    if ($_POST["input_filename"] <> "") {
        unlink("../../userdata/db_backup/" . $_POST["input_filename"]);
    }
}

function import_backup() {
    if ($_POST["input_filename"] <> "") {
        echo $_POST["input_filename"];
        $f = fopen("../../userdata/db_backup/" . $_POST["input_filename"], "r");
        while (!feof($f)) {
            $line = fgets($f);
            mysqli_query($GLOBALS['mysql_con'], $line);
        }
        fclose($f);
    }
}

function create_backup() {
    $filename = "../../userdata/db_backup/dc_backup_" . date("Ymd_His") . ".sql";
    $f        = fopen($filename, "w");
    $tables   = mysqli_list_tables($GLOBALS['mysql_con'], $GLOBALS["mydb"]);
    while ($cells = mysqli_fetch_array($tables)) {
        $table = $cells[0];
        fwrite($f, "DROP TABLE `$table`;\n");
        $res = mysqli_query($GLOBALS['mysql_con'], "SHOW CREATE TABLE `$table`");
        if ($res) {
            $create = @mysqli_fetch_array($res);
            $create[1] .= ";";
            $line = str_replace("\n", "", $create[1]);
            fwrite($f, $line . "\n");
            $data = mysqli_query($GLOBALS['mysql_con'], "SELECT * FROM `$table`");
            $num  = mysqli_num_fields($GLOBALS['mysql_con'], $data);
            while ($row = mysqli_fetch_array($data)) {
                $line = "INSERT INTO `$table` VALUES(";
                for ($i = 1; $i <= $num; $i++) {
                    $line .= "'" . mysqli_real_escape_string($GLOBALS['mysql_con'], $row[$i - 1]) . "', ";
                }
                $line = substr($line, 0, -2);
                fwrite($f, $line . ");\n");
            }
        }
    }
    fclose($f);
    if (file_exists($filename)) {
        echo "<div class=\"successbox\">Datensicherung wurde erstellt</div>";
    } else {
        echo "<div class=\"errorbox\">Datensicherung konnte nicht erstellt werden</div>";
    }
}

?>