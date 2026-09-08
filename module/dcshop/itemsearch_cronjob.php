<?php
$GLOBALS['solr_update_successful'] = false;
ini_set('max_execution_time', 650);
function append_solr_logfile($text) {
    file_put_contents(rtrim(dirname(dirname(__DIR__)),'/') . '/' . date('Y-m-') . 'solr_update_logfile.txt',$text,FILE_APPEND);
}

$force = false;
$force_log_snippet = '';
if($_REQUEST['force']==='1') {
    $force = true;
    $force_log_snippet = ' WITH \'FORCE\' PARAMTER ';
}

append_solr_logfile(date('Y-m-d H:i:s') . ' - SOLR UPDATE CRONJOB PHP FILE CALLED' . $force_log_snippet . ' - ' . PHP_EOL);


//require_once(rtrim($_SERVER['DOCUMENT_ROOT'],'/') . "/dc/frontend/frontend_functions.inc.php");
$rootDir = rtrim(dirname(dirname(__DIR__)),'/\\');
require_once $rootDir . DIRECTORY_SEPARATOR . 'dc/common/common_functions.inc.php';
require_once (local_environment()) ? $rootDir . '/dc/dc.config.php' : $rootDir . '/dc/dc-server.config.php';
require_once $rootDir . '/module/dcshop/shop.config.php';
$is_local_call = ($_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR']);
$remote_call_is_authenticated = false;
if($_POST['shop_password'] === $GLOBALS["shop_setup"]["shop_password"]) {
    $remote_call_is_authenticated = true;
}

if (!$is_local_call && !$remote_call_is_authenticated){
    append_solr_logfile('Call from remote address: ' . $_SERVER['REMOTE_ADDR'] . ' - NOT ALLOWED!' . PHP_EOL . date('Y-m-d H:i:s') . ' - Script execution ends.' . PHP_EOL . PHP_EOL . PHP_EOL);
    headerFunctionBridge("HTTP/1.0 400 No remote access allowed");
    exit;
}

db_connect();

$query = 'SELECT last_datetime_nav_updated, last_datetime_solr_updated FROM shop_setup LIMIT 1';
$result = @mysqli_query($GLOBALS['mysql_con'],$query);
$arr = @mysqli_fetch_assoc($result);

$arr['last_datetime_nav_updated'] = strtotime($arr['last_datetime_nav_updated']);
$arr['last_datetime_solr_updated'] = strtotime($arr['last_datetime_solr_updated']);


append_solr_logfile('Last unixtime nav full-update:  ' . $arr['last_datetime_nav_updated'] . PHP_EOL);
append_solr_logfile('Last unixtime solr full-update: ' . $arr['last_datetime_solr_updated'] . PHP_EOL);
append_solr_logfile('FORCE-mode: ignoring update-times');


if($force || ($arr['last_datetime_solr_updated'] < $arr['last_datetime_nav_updated']) || empty($arr['last_datetime_solr_updated'])) {
    if($force) {
        append_solr_logfile('Start forced. Starting update...' . PHP_EOL);
    } else {
        append_solr_logfile('Solr not current. Starting update...' . PHP_EOL);
    }

    //Canonicals
    $querydenorm0 = 'UPDATE shop_item SET canonical_url = get_item_canonical_url(id) WHERE id > 0';
    append_solr_logfile('Updating item-canonicals... ');
    $microtimeDenorm0Start = microtime(true);
    $resultdenorm0 = @mysqli_query($GLOBALS['mysql_con'],$querydenorm0);
    while(mysqli_next_result($GLOBALS['mysql_con'])) {
        true;
    }
    $durationDenorm0 = microtime(true) - $microtimeDenorm0Start;
    append_solr_logfile('done! Duration (s): ' . $durationDenorm0 . PHP_EOL);


    //Create view
    $querydenorm1 = 'CALL create_replace_item_attr_denorm_view';
    append_solr_logfile('Calling MySQL denormalization-procedure 1: create_replace_item_attr_denorm_view ... ');
    $microtimeDenorm1Start = microtime(true);
    $resultdenorm1 = @mysqli_query($GLOBALS['mysql_con'],$querydenorm1);
    while(mysqli_next_result($GLOBALS['mysql_con'])) {
        true;
    }
    $durationDenorm1 = microtime(true) - $microtimeDenorm1Start;
    append_solr_logfile('done! Duration (s): ' . $durationDenorm1 . PHP_EOL);


    //Materialize and fill remaining values (permissions, nav-variants)
    $querydenorm2 = 'CALL materialize_solr_view';
    append_solr_logfile('Calling MySQL denormalization-procedure 2: materialize_solr_view ... ');
    $microtimeDenorm2Start = microtime(true);
    $resultdenorm2 = @mysqli_query($GLOBALS['mysql_con'],$querydenorm2);
    while(mysqli_next_result($GLOBALS['mysql_con'])) {
        true;
    }
    $durationDenorm2 = microtime(true) - $microtimeDenorm2Start;
    append_solr_logfile('done! Duration (s): ' . $durationDenorm2 . PHP_EOL);


    //Do update
    $updateURL = 'http://127.0.0.1:8984/solr/dcshop/dataimport?command=full-import';
    append_solr_logfile('Starting CURL call to Solr full-update URL: ' . $updateURL .  ' ... ');

    /*$cred = sprintf( 'Authorization: Basic %s',
        base64_encode( 'admin:ICG#2013x' )
    );*/

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$updateURL);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded", $cred));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("application/x-www-form-urlencoded"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    $curl_output = curl_exec($ch);

    curl_close($ch);
    append_solr_logfile('done!' . PHP_EOL);

    append_solr_logfile('++++++++ SOLR RESPONSE START ++++++++' . PHP_EOL);
    append_solr_logfile($curl_output);
    append_solr_logfile('++++++++ SOLR RESPONSE END ++++++++' . PHP_EOL);


    $updateQuery = 'UPDATE shop_setup SET last_datetime_solr_updated = NOW() WHERE id > 0';

    append_solr_logfile('Updating MySQL: setting last_datetime_solr_updated...');
    $result = mysqli_query($GLOBALS['mysql_con'],$updateQuery);
    if($result) {
        $GLOBALS['solr_update_successful'] = true;
        append_solr_logfile('done!' . PHP_EOL);
    } else {
        append_solr_logfile('UNSUCCESSFUL!' . PHP_EOL);
    }
    append_solr_logfile(date('Y-m-d H:i:s') . ' - Script execution ends.' . PHP_EOL . PHP_EOL . PHP_EOL);
} else {
    append_solr_logfile('Solr already up to date - nothing to do.' . PHP_EOL . date('Y-m-d H:i:s') . ' - Script execution ends.' . PHP_EOL . PHP_EOL . PHP_EOL);
}