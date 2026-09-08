<?

function encodeVideo(&$jsonResponse, $file, $fileid, $generateThumbNail = true, $thumbNailTimeCode = 1, $generateMp4 = true, $generateWebM = true, $generateOgg = true) {
    ini_set('display_errors', '1');
    $binaryDir = rtrim(dirname(dirname(__DIR__)),'/').'/plugins/ffmpeg/bin/';
    $scannedBinaryDir = array_diff(scandir($binaryDir), array('..', '.'));

    $GLOBALS["fileId"] = $fileid;

    foreach ($scannedBinaryDir as $binaryFile) {
        if (substr(sprintf('%o', fileperms($binaryDir.$binaryFile)), -4) != "0755") {
            if (!chmod($binaryDir.$binaryFile, 0755)) {
                return false;
            }
        }
    }
    $ffmpegBinaries = $binaryDir.'ffmpeg';
    $ffprobeBinaries = $binaryDir.'ffprobe';

    $ffmpeg = FFMpeg\FFMpeg::create(array(
        'ffmpeg.binaries'  => $ffmpegBinaries,
        'ffprobe.binaries' => $ffprobeBinaries,
        'timeout' => 3600, // The timeout for the underlying process
        'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
    ));

    $fileExtension = pathinfo($file,PATHINFO_EXTENSION);
    $fileName = pathinfo($file,PATHINFO_FILENAME);
    $videoPath = rtrim(dirname(dirname(__DIR__)),'/').$GLOBALS["shop_setup"]["uploaddir_videos"];


    $video = $ffmpeg->open($videoPath.'original/'.$file);

    if ($generateThumbNail) {
        $video
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($thumbNailTimeCode))
            ->save($videoPath.'thumb/'.$fileName.'.jpg');
    }

    return true;
}