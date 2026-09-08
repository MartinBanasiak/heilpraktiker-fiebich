<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 15.07.2016
 * Time: 14:01
 */

$shopCode = " B2C";
$generateThumbNail = " 1";
$thumbNailTimeCode = " 1";
$generateMp4 = " 1";
$generateWebM = " 1";
$generateOgg = " 1";

$binaryDir = rtrim(dirname(dirname(__DIR__)),'/').'/userdata/dcshop/videos/original/';
$scannedBinaryDir = array_diff(scandir($binaryDir), array('..', '.'));

foreach ($scannedBinaryDir as $binaryFile) {
    if (!file_exists(rtrim(dirname(dirname(__DIR__)),'/').'/userdata/dcshop/videos/mp4/'.pathinfo($binaryFile,PATHINFO_FILENAME).'.mp4')) {
        $cmd = "(cd ".rtrim(dirname(dirname(__DIR__)),'/')." && php ".rtrim(dirname(dirname(__DIR__)),'/')."/plugins/ffmpeg/ffmpeg-worker.php ".$binaryFile.$shopCode.$generateThumbNail.$thumbNailTimeCode.$generateMp4.$generateOgg.$generateWebM.") > /dev/null 2>/dev/null &";
        echo exec($cmd);
        exit;
    }
}