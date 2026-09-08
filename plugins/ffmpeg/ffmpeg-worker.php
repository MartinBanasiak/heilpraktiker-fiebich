<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 15.07.2016
 * Time: 13:37
 */
//ini_set('display_errors', '0');

$baseDirectory = rtrim(dirname(dirname(__DIR__)), '/');
include($baseDirectory . '/vendor/autoload.php');


//Load environment variables from config if exists
$envDir = rtrim($baseDirectory, '/') . '/config';
if (is_dir($envDir)) {
    $dotenv = new \Dotenv\Dotenv($envDir);
    $dotenv->load();
}

require_once($baseDirectory . "/dc/common/common_functions.inc.php");
require_once($baseDirectory . "/dc/dc-server.config.php");
require_once($baseDirectory . "/module/dcshop/common/shop_functions.inc.php");
require_once($baseDirectory . "/module/dcshop/shop.config.php");

db_connect();

$binaryDir = $baseDirectory . '/plugins/ffmpeg/bin/';
$scannedBinaryDir = array_diff(scandir($binaryDir), array('..', '.'));

foreach ($scannedBinaryDir as $binaryFile) {
    if (substr(sprintf('%o', fileperms($binaryDir . $binaryFile)), -4) != "0755") {
        if (!chmod($binaryDir . $binaryFile, 0755)) {
            return false;
        }
    }
}

$ffmpegBinaries = $binaryDir . 'ffmpeg';
$ffprobeBinaries = $binaryDir . 'ffprobe';
$ffmpeg = FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries' => $ffmpegBinaries,
    'ffprobe.binaries' => $ffprobeBinaries,
    'timeout' => 3600, // The timeout for the underlying process
    'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
));

$workFile = "";
$videoPath = $baseDirectory . $GLOBALS["shop_setup"]["uploaddir_videos"];
$originalPath = $videoPath . 'original/';

$mp4Dir = $videoPath . 'mp4/';
$oggDir = $videoPath . 'ogg/';
$webmDir = $videoPath . 'webm/';
$scannedVideoDir = array_diff(scandir($originalPath), array('..', '.'));
foreach ($scannedVideoDir as $videoFile) {
    if (!file_exists($mp4Dir . pathinfo($videoFile, PATHINFO_FILENAME) . '.mp4') ||
        !file_exists($oggDir . pathinfo($videoFile, PATHINFO_FILENAME) . '.ogg') ||
        !file_exists($webmDir . pathinfo($videoFile, PATHINFO_FILENAME) . '.webm')
    ) {
        $workFile = $videoFile;
        $query = "
            SELECT * FROM shop_item_file WHERE filename = '" . pathinfo($workFile, PATHINFO_FILENAME) . "." . pathinfo($workFile, PATHINFO_EXTENSION) . "'
        ";

        $result = mysqli_query($GLOBALS["mysql_con"], $query);
        $GLOBALS["shopItemFile"] = mysqli_fetch_array($result);
        break;
    }
}

if ($workFile != "") {
    $fileExtension = pathinfo($workFile, PATHINFO_EXTENSION);
    $fileName = pathinfo($workFile, PATHINFO_FILENAME);
    $video = $ffmpeg->open($videoPath . 'original/' . $workFile);
    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
        ->save($videoPath . 'thumb/' . $fileName . '.jpg');

    if ($fileExtension == 'mp4' && !file_exists($mp4Dir . $fileName . '.mp4')) {
        if (copy($originalPath . $fileName . '.mp4', $mp4Dir . $fileName . '.mp4')) {
            $query = "UPDATE shop_item_file SET mp4 = ? WHERE id=?";
            if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                $percentage = 100;
                mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                mysqli_stmt_execute($stmt);
            }
        }
    } else {
        if (!file_exists($mp4Dir . $fileName . '.mp4')) {
            $formatMp4 = new FFMpeg\Format\Video\X264('libmp3lame', 'libx264');
            $formatMp4->on('progress', function ($video, $formatMp4, $percentage) {
                $query = "UPDATE shop_item_file SET mp4 = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            });
            $formatMp4->setKiloBitrate(600)->setAudioChannels(2)->setAudioKiloBitrate(128);
            if ($video->save($formatMp4, $videoPath . 'mp4/' . $fileName . '.mp4')) {
                $query = "UPDATE shop_item_file SET mp4 = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    $percentage = 100;
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }

    if ($fileExtension == 'ogg' && !file_exists($oggDir . $fileName . '.ogg')) {
        if (copy($originalPath . $fileName . '.ogg', $oggDir . $fileName . '.ogg')) {
            $query = "UPDATE shop_item_file SET ogg = ? WHERE id=?";
            if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                $percentage = 100;
                mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                mysqli_stmt_execute($stmt);
            }
        }
    } else {
        if (!file_exists($oggDir . $fileName . '.ogg')) {
            $formatOgg = new FFMpeg\Format\Video\Ogg();
            $formatOgg->on('progress', function ($video, $formatOgg, $percentage) {
                $query = "UPDATE shop_item_file SET ogg = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            });
            $formatOgg->setKiloBitrate(100)->setAudioChannels(2)->setAudioKiloBitrate(128);
            if ($video->save($formatOgg, $videoPath . 'ogg/' . $fileName . '.ogg')) {
                $query = "UPDATE shop_item_file SET ogg = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    $percentage = 100;
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }

    if ($fileExtension == 'webm' && !file_exists($webmDir . $fileName . '.webm')) {
        if (copy($originalPath . $fileName . '.webm', $webmDir . $fileName . '.webm')) {
            $query = "UPDATE shop_item_file SET webm = ? WHERE id=?";
            if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                $percentage = 100;
                mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                mysqli_stmt_execute($stmt);
            }
        }
    } else {
        if (!file_exists($webmDir . $fileName . '.webm')) {
            $formatWebm = new FFMpeg\Format\Video\WebM();
            $formatWebm->on('progress', function ($video, $formatWebm, $percentage) {
                $query = "UPDATE shop_item_file SET webm = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            });
            $formatWebm->setKiloBitrate(100)->setAudioChannels(2)->setAudioKiloBitrate(128);
            if ($video->save($formatWebm, $videoPath . 'webm/' . $fileName . '.webm')) {
                $query = "UPDATE shop_item_file SET webm = ? WHERE id=?";
                if ($stmt = mysqli_prepare($GLOBALS["mysql_con"], $query)) {
                    $percentage = 100;
                    mysqli_stmt_bind_param($stmt, "ii", $percentage, $GLOBALS["shopItemFile"]["id"]);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }
}