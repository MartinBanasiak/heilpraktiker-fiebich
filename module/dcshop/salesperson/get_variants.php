<?php
$rootDir = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/frontend/frontend_functions.inc.php';
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once local_environment() ? $rootDir . DIRECTORY_SEPARATOR . 'dc/dc.config.php' : $rootDir . DIRECTORY_SEPARATOR . 'dc/dc-server.config.php';

db_connect();

if (isset($_POST["company"]) && isset($_POST["shop_code"]) && isset($_POST["language_code"]) && isset($_POST["item_no"])) {
    $company       = mysqli_real_escape_string($GLOBALS['mysql_con'], urldecode($_POST["company"]));
    $shop_code     = mysqli_real_escape_string($GLOBALS['mysql_con'], urldecode($_POST["shop_code"]));
    $language_code = mysqli_real_escape_string($GLOBALS['mysql_con'], urldecode($_POST["language_code"]));
    $item_no       = mysqli_real_escape_string($GLOBALS['mysql_con'], urldecode($_POST["item_no"]));

    $query    = "
	SELECT * 
	FROM shop_item_variant 
	WHERE company='" . $company . "' AND item_no='" . $item_no . "'";
    $result   = @mysqli_query($GLOBALS['mysql_con'], $query);
    $num_rows = @mysqli_num_rows($result);
    if ($num_rows > 0) {
        @mysqli_data_seek($result, 0);
        while ($row = @mysqli_fetch_assoc($result)) {
            ?>
            <option value="<?= $row["code"] ?>"><span><?= $row["code"] ?></span></option>
        <?
        }
    } else {
        ?>
        <option value=""><span>&nbsp;-&nbsp;</span></option>
    <?
    }
}
?>