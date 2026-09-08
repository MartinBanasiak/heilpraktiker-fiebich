<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 25.11.2016
 * Time: 04:58
 */

$baseDir = dirname(dirname(__DIR__));
$vendorDir = $baseDir . '/vendor';
$autoloaderPath = $vendorDir . '/autoload.php';
include($autoloaderPath);

/**
 * @param $msg
 */
function logHere($msg)
{
    file_put_contents('newserverclienttest.log', $msg . PHP_EOL, FILE_APPEND);
}

/**
 * @param $clientSocket
 * @param $msgStart
 * @param $msgTerm
 * @return string
 */
function getNextClientResponseFromSocket(&$clientSocket, $msgStart, $msgTerm)
{
    if (!is_resource($clientSocket)) {
        return '';
    }
    $response = '';
    $strlenMsgStart = strlen($msgStart);
    try {
        while ($chunk = fread($clientSocket, 1024)) {
            $strposMsgStart = strpos($chunk, $msgStart);
            $strposMsgTerm = strpos($chunk, $msgTerm);
            if ($strposMsgStart === false && $strposMsgTerm === false) {
                continue;
            } elseif ($strposMsgStart === 0) {
                $length = null;
                if ($strposMsgTerm) {
                    $length = $strposMsgTerm - $strlenMsgStart;
                    $substr = substr($chunk, $strlenMsgStart, $length);
                    $response .= $substr;
                    return $response;
                } else {
                    $response .= substr($chunk, $strlenMsgStart);
                }
            } elseif ($strposMsgTerm >= 0) {
                $response .= substr($chunk, 0, $strposMsgTerm);
                return $response;
            }
        }
    } catch (\Throwable $t) {
        return '';
    }
    return '';
}

$currConvJob = new \DynCom\dc\workerqueue\main\GenericJob('currencyconversion', 0, time() + 3600, 3, 0, []);
$currConvJob->setStatusOpen();
$currConvJobJSON = json_encode($currConvJob);

$start = '-#?#-';
$end = '-#!#-';
$jobGateWayDaemonAddr = 'tcp://0.0.0.0:4444';
$errno = 0;
$errstr = '';
$socket = stream_socket_client($jobGateWayDaemonAddr, $errno, $errstr, 30);
if (!$socket) {
    logHere("$errstr ($errno)");
} else {
    $request0 = 'CREATE ' . $currConvJobJSON;
    $request1 = 'PEEKNEXTOPEN currencyconversion';
    $request2 = 'GETNEXTOPEN currencyconversion';
    $request3 = 'GETNEXTFINISHED currencyconversion';
    $request0Msg = $start . $request0 . $end;
    $request1Msg = $start . $request1 . $end;
    $request2Msg = $start . $request2 . $end;
    $request3Msg = $start . $request3 . $end;
    logHere('Sending msg [' . $request1Msg . '].');
    $sent1 = fwrite($socket, $request1Msg);

    if ($sent1 > 0) {
        $response = getNextClientResponseFromSocket($socket, $start, $end);
        $arr = [];
        try {
            logHere('Response was [' . $response . ']');
            $job = \DynCom\dc\workerqueue\main\GenericJob::fromJSON($response);
            $serialJob = json_encode($job);
            logHere('Serialized attempted job parsed response: ' . $serialJob);
        } catch (Throwable $t) {
            logHere('Error thrown: ' . $t->getMessage());
        }
        logHere('After send.');
    } else {
        logHere("didn't send.");
    }

    logHere('Sending msg [' . $request0Msg . '].');
    $sent2 = fwrite($socket, $request0Msg);

    if ($sent2 > 0) {
        $response2 = getNextClientResponseFromSocket($socket, $start, $end);
        $arr2 = [];
        try {
            logHere('Response2 was [' . $response2 . ']');
            $job2 = \DynCom\dc\workerqueue\main\GenericJob::fromJSON($response2);
            $serialJob2 = serialize($job2);
            logHere('Serialized attempted job parsed response: ' . $serialJob2);
        } catch (Throwable $t2) {
            logHere('Error thrown: ' . $t2->getMessage());
        }
        logHere('After send2.');
    } else {
        logHere("didn't send2.");
    }

    /*
        fwrite($socket,$request1Msg);
        $response1 = '';
        while ($chunk = fread($socket,4096)) {
            $response1 .= $chunk;
        }
        logHere('Response1 was: [' . $response1 . ']');

        logHere('sending request 2: [' . $request2 . '].');

        fwrite($socket,$request2Msg);
        $response2 = '';
        while ($chunk2 = fread($socket,4096)) {
            $response2 .= $chunk;
        }
        logHere('Response2 was: [' . $response2 . ']');*/

    stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
}