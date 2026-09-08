<?php
// make sure the script doesn't die if parsing a long log
ini_set('max_execution_time', '0');

// include all the classes
require_once('class.log.php');
require_once('class.log.mysql.php');
require_once('class.log.output.php');

// see class.log.mysql.php for example table setup
// 
log::$parser = new log_mysql(array(
    'user'   => 'dccms_user',
    'pass'   => '1234',
    'db'     => 'dccms',
    'table'  => 'log',
    'fields' => array(
        'ip'            => 'ip',
        'identd'        => 'identd',
        'auth'          => 'auth',
        'day'           => 'day',
        'month'         => 'month',
        'year'          => 'year',
        'time'          => 'time',
        'request'       => 'request',
        'http_version'  => 'http_version',
        'response_code' => 'response_code',
        'size'          => 'size',
        'referrer'      => 'referrer',
        'navigator'     => 'navigator',
        'timestamp'     => 'timestamp'
    )
));

//log::$parser = new log_output();

log::parse('access.log');