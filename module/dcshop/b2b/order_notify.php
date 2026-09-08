<?php
$handle = fopen("log.txt", "a+");
foreach ($_GET as $key => $value) {
    fwrite($handle, $_GET['$key'] . " = " . $value);
}
fclose($handle);
?>