<?php
if ($get['ajax'] != 1) {
    //echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI'];
}
$jsonResponse = array();
$dlUrl = "";
$isNextRecord = false;
$isPrevRecord = false;
$nextLineNo = 0;
$prevLineNo = 0;
$downloadAble = false;
$deleteAble = false;
$maxFileSize = 35;

$inFileUpload = isset($_FILES['input_file']);
$noOfFilesInUpload = 0;
$isFirstFileInUpload = true;
if ($inFileUpload) {
    $noOfFilesInUpload = 1;
    if (array_key_exists('no_of_files',$post)) {
        $noOfFilesInUpload = (int)$post['no_of_files'];
    }
    if (array_key_exists('is_first',$post)) {
        $isFirstFileInUpload = (bool)$post['is_first'];
    }
}
$logPath = __DIR__ . DIRECTORY_SEPARATOR . '../../../logs/logfile_upload.txt';
$logPrefix = date('Y-m-d H:i:s') . ' WEBFORM ';
if ($inFileUpload) {
    $name = $_FILES['input_file']['name'];
    $str = $logPrefix .' - FILE UPLOAD - name [' . $name . ']: ' . print_r($_GET,true) . PHP_EOL . print_r($_POST,true) . PHP_EOL . print_r($_REQUEST,true) . PHP_EOL . PHP_EOL;
    file_put_contents($logPath,$str,FILE_APPEND);
}

require_once rtrim(dirname(dirname(dirname(__DIR__))),'/') . '/plugins/ffmpeg/ffmpeg.php';

if ($get["ajax"] != 1) {
    ?>
    <script type="text/javascript">
        var files = "";
        $(document).ready(function () {
            $('input[type=file]').on('change', prepareUpload);
        });

        function prepareUpload(event) {
            files = event.target.files;
            changes = true;
            SendMessage(window.parent, "changedetected", "", "*");
        }

        var deleteFilePath;
        var filename;
        var filenameDelete;
        var fileIndex;
        var totalFileCount;
        function ReceiveMessage(evt) {
            try {
                var data = JSON.parse(evt.data);
                action = data.Event;
                data = data.Data;

                if (action == "delete") {

                }
                if (action == "download") {

                }
            }
            catch(err) {

            }
        }
    </script>

    <?php
}
$actionUriComponents = $get;

//$file[0] = "<div class='file'><div class='delete_upload' data-file-id='%1'></div><div class='download_upload' data-url=\"%3\" data-file-id=\"%4\"></div>";
$file[0] = "<div class='file'>";
$file[1] = "</div>";

$multiupload = false;
if (isset($get["multiupload"]) && $get["multiupload"] == 1) {
    $multiupload = true;
}
$genThumbs = FALSE;
$deleteOriginal = FALSE;
$deleteExisting = FALSE;

$spacer = "";
$ul[0] = "<ul class=\"buttonlist\">";
$ul[1] = "</ul>";
if ($request["ajax"] == 1) {
    $spacer = "~||~";
    $ul[0] = "";
    $ul[1] = "";
}
$return = "";
if (!empty($post["delete_file"])) {
    $logData = $logPrefix . ' - IN DELETE FILE! ' . __LINE__ . PHP_EOL;
    file_put_contents($logPath,$logData,FILE_APPEND);
    if (!empty($post["delete_file_path"])) {
        $filePath = explode("/", $post["delete_file_path"]);
        $fileName = array_pop($filePath);
        $filePath = implode("/", $filePath);
        $filePath = realpath(dirname(dirname(dirname(__DIR__))) . $filePath) . "/" . $fileName;
        $fileId = $post["delete_file"];
        $rootPath = rtrim(dirname(dirname(dirname(__DIR__))),'/\\');

        if (file_exists($filePath)) {
            if (is_file($filePath)) {
                if (unlink($filePath)) {
                    foreach ($GLOBALS["shop_setup"]["image_config"] as $image) {
                        $filePath = str_replace("//", "/", $rootPath . '/' . $image["path"] . "/" . $fileName);
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                    //videos
                    $filePath = str_replace("//", "/", $rootPath . '/' . $GLOBALS["shop_setup"]["uploaddir_videos"] . "mp4/" . pathinfo($fileName, PATHINFO_FILENAME) . ".mp4");
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $filePath = str_replace("//", "/", $rootPath . '/' . $GLOBALS["shop_setup"]["uploaddir_videos"] . "ogg/" . pathinfo($fileName, PATHINFO_FILENAME) . ".ogg");
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $filePath = str_replace("//", "/", $rootPath . '/' . $GLOBALS["shop_setup"]["uploaddir_videos"] . "webm/" . pathinfo($fileName, PATHINFO_FILENAME) . ".webm");
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $filePath = str_replace("//", "/", $rootPath . '/' . $GLOBALS["shop_setup"]["uploaddir_videos"] . "thumb/" . pathinfo($fileName, PATHINFO_FILENAME) . ".jpg");
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $query = "UPDATE shop_item_file SET mp4 = 0, ogg = 0, webm = 0 WHERE id = '" . $fileId . "'";
                    @mysqli_query($GLOBALS["mysql_con"], $query);
                    $return = "SUCCESS";
                } else {
                    $return = "ERROR";
                }
            } elseif (is_dir($filePath)) {
                $realFilePath = realpath($filePath);
                $realRootPath = realpath(dirname(dirname(dirname(__DIR__))));
                $realUserdataPath = rtrim($realRootPath,'/\\') . DIRECTORY_SEPARATOR . 'userdata';

                $str = $logPrefix . ' - DELETE DIR [raw: ' . $filePath . '|real: ' . $realFilePath . '].' . __LINE__ . PHP_EOL;
                file_put_contents($logPath,$str,FILE_APPEND);

                if (mb_strlen($realFilePath) > mb_strlen($realUserdataPath)) {
                    $output = [];
                    $returnVar = 0;
                    if (0 === stripos(PHP_OS, 'WIN')) {
                        exec(sprintf('rd /s /q %s', escapeshellarg($realFilePath)),$output,$returnVar);
                    } else {
                        //Linux
                        exec(sprintf('rm -rf %s', escapeshellarg($realFilePath)),$output,$returnVar);
                    }
                    $str = $logPrefix . ' - DIR DELETE Output [' . print_r($output,1) . '] - returnVar [' . $returnVar . '].' . __LINE__ . PHP_EOL ;
                    file_put_contents($logPath,$str,FILE_APPEND);
                    $return = ($returnVar === 0) ? 'SUCCESS' : 'ERROR';
                }
            }
        } else {
            $return = "ERROR";
        }
    } else {
        $return = "ERROR";
    }
}

$showFiles = "";
$fileTemplate = "";


if ($request["webform"] == "ItemFile") {

    $itemFileType = (int)$get['type'];


    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_item_file SET filename = '' WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "' AND type = '" . $get["type"] . "' AND line_no = '" . $get["line_no"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    $fileNamePrefix = random_int(100,999999) . "_";

    $countquery = "SELECT * FROM shop_item_file WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "' AND type = '" . $get["type"] . "' AND line_no = '" . $get["line_no"] . "'";

    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);

    $fileExists = false;

    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $fileExists = true;
        $insertID = $input_file["id"];
    } elseif (@mysqli_num_rows($result) == 0 && $multiupload) {
        $createQuery = "INSERT into shop_item_file SET 
                  company = '" . $get["company"] . "', 
                  shop_code = '" . $get["shop_code"] . "', 
                  language_code = '" . $get["language_code"] . "', 
                  item_no = '" . $get["item_no"] . "', 
                  type = '" . $get["type"] . "', 
                  line_no = '" . $get["line_no"] . "'
                  ";
        @mysqli_query($GLOBALS["mysql_con"], $createQuery);

        $str = $logPrefix . ' - row inserted [' . $createQuery . '] - ' . __LINE__ . PHP_EOL ;
        file_put_contents($logPath,$str,FILE_APPEND);

        $insertID = mysqli_insert_id($GLOBALS["mysql_con"]);
        $countquery = "SELECT * FROM shop_item_file WHERE id = '" . $insertID . "'";
        $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
        $input_file = @mysqli_fetch_assoc($result);
    }

    $updateMarketplace = false;
    switch ($itemFileType) {
        case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_IMAGE:
            $uploaddir = $GLOBALS["shop_setup"]["uploaddir"];
            $genThumbs = true;
            $input_file_loc = $input_file["filename"];
            $dlUrl .= "&type=" . $get['type'] . "&file=" . $input_file["id"];
            if ($fileExists && $input_file["filename"] != "") {
                $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";
            }

            $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["image_config"][2]["path"] . "/%1\" />";
            $acceptedFiles = ".jpg,.jpeg,.gif,.png";
            $updateMarketplace = true;
            break;
        case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE:
            $lineNo = (isset($get['line_no']) && !empty($get['line_no'])) ? $get['line_no'] : $get['initialLineNo'];
            $primaryString = (string)$get['company'] . '|' . (string)$get['shop_code'] . '|' . (string)$get['language_code'] . '|' . (string)$get['item_no'] . '|' . (string)$get['type'] . '|' . (string)$lineNo;
            $uploadSubDir = md5($primaryString);
            $uploaddir = rtrim($GLOBALS["shop_setup"]["uploaddir_360_degree_images"],'/\\') . DIRECTORY_SEPARATOR . $uploadSubDir . DIRECTORY_SEPARATOR;

            $genThumbs = true;
            $input_file_loc = $input_file["filename"];
            $dlUrl .= "&type=" . $get['type'] . "&file=" . $input_file["id"];
            if ($fileExists && $input_file["filename"] != "") {
                $dirPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . $uploaddir . DIRECTORY_SEPARATOR;

                if (is_dir($dirPath)) {
                    $dirIt = new DirectoryIterator($dirPath);
                    $showFiles = '';
                    foreach ($dirIt as $fileinfo) {
                        if (!$fileinfo->isDot()) {
                            $filePathImg = $uploaddir . $fileinfo->getFilename();
                            $dlUrl = $dlUrl . '&filename=' . urlencode($fileinfo->getFilename());
                            $showFiles .= "<div class=\"img-wrapper\"><img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 200px; max-height: 200px;\" src=\"" . $filePathImg . "?id=" . rand(1, 900) . "\" /></div>\n";
                        }
                    }
                }
            }

            $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "/%1\" />";
            $acceptedFiles = ".jpg,.jpeg,.gif,.png";
            $updateMarketplace = true;
            break;
        case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_DOCUMENT_FILE:
            $uploaddir = $GLOBALS["shop_setup"]["uploaddir_documents"];
            if ($fileExists && $input_file["filename"] != "") {
                $button_class = get_button_file_typ_new($input_file["filename"]);
                if (strpos($input_file["filename"], '_')) {
                    $input_file["filename"] = substr($input_file["filename"], strpos($input_file["filename"], '_') + 1);
                }
                $input_file_loc = $input_file["filename"];
                $dlUrl .= "&type=" . $get['type'] . "&file=" . $input_file["id"];
                if ($fileExists && $input_file["filename"] != "") {
                    $showFiles = "<i data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' class=\"fa fa-4x fa-file-" . $button_class . "-o\"></i><br><br><span>" . urldecode($input_file_loc) . "</span>";

                }

            }
            $fileTemplate = "<i data-url=\"%3\" data-file-id=\"%4\" class=\"fa fa-4x fa-file-%2\"></i><br><br><span>%1</span>";
            $acceptedFiles = ".pdf,.doc,.docx,.txt,.xls,.xlsx,.csv,.rtf,.zip,.rar";

            break;
        case \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_VIDEO_FILE:
            $uploaddir = $GLOBALS["shop_setup"]["uploaddir_videos"] . 'original/';
            $dlUrl .= "&type=" . $get['type'] . "&file=" . $input_file["id"];
            $progress["show"] = false;

            $maxFileSize = 2000;

            if ($input_file["mp4"] < 100 && $input_file["filename"] != "") {
                $progress["mp4"]["show"] = true;
                $progress["mp4"]["percent"] = $input_file["mp4"];
                $progress["show"] = true;
            }

            if ($input_file["mp4"] == 100 && $input_file["filename"] != "") {
                $progress["mp4"]["percent"] = $input_file["mp4"];
            }

            if ($input_file["ogg"] < 100 && $input_file["filename"] != "") {
                $progress["ogg"]["show"] = true;
                $progress["ogg"]["percent"] = $input_file["ogg"];
                $progress["show"] = true;
            }

            if ($input_file["ogg"] == 100 && $input_file["filename"] != "") {
                $progress["ogg"]["percent"] = $input_file["ogg"];
            }

            if ($input_file["webm"] < 100 && $input_file["filename"] != "") {
                $progress["webm"]["show"] = true;
                $progress["webm"]["percent"] = $input_file["webm"];
                $progress["show"] = true;
            }

            if ($input_file["webm"] == 100 && $input_file["filename"] != "") {
                $progress["webm"]["percent"] = $input_file["webm"];
            }


            if ($fileExists && $input_file["filename"] != "") {
                $input_file_loc = $input_file["filename"];
                $videoFileName = pathinfo($input_file["filename"], PATHINFO_FILENAME);
                if (!$progress["show"] && $input_file["filename"] != "") {
                    $showFiles = '<video id="video" data-url=\'' . $dlUrl . '\' data-file-id=\'' . $input_file["id"] . '\' class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="300" height="150"
                       poster="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'thumb/' . $videoFileName . '.jpg" data-setup="{}">
                    <source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'mp4/' . $videoFileName . '.mp4" type="video/mp4">
                    <source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'webm/' . $videoFileName . '.webm" type="video/webm">
                    <source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'webm/' . $videoFileName . '.ogg" type="video/wogg">
                    <p class="vjs-no-js">
                      To view this video please enable JavaScript, and consider upgrading to a web browser that
                      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>
                </video>';
                } else {
                    $fileName = pathinfo($input_file["filename"],PATHINFO_FILENAME);
                    $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 300px; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_videos"] . "/thumb/" . $fileName . ".jpg?id=" . rand(1, 900) . "\" />\n";
                }
            }
            if (!$progress["show"] && $input_file["filename"] != "") {
                $fileTemplate = '<video id="video" data-url="%3" data-file-id="%4" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="300" height="150"\'+
                       \'poster="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'thumb/%1.jpg" data-setup="{}">\'+
                    \'<source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'mp4/%1.mp4" type="video/mp4">\'+
                    \'<source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'webm/%1.webm" type="video/webm">\'+
                    \'<source src="' . $GLOBALS['projectRoot'] . $GLOBALS["shop_setup"]["uploaddir_videos"] . 'webm/%1.ogg" type="video/ogg">\'+
                    \'<p class="vjs-no-js">\'+
                      \'To view this video please enable JavaScript, and consider upgrading to a web browser that\'+
                      \'<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>\'+
                    \'</p>\'+
                \'</video>';
            } else {
                $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 300px; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_videos"] . "/thumb/%1.jpg\" />";
            }
            $fileTemplateThumbnail = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 300px; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_videos"] . "/thumb/%1.jpg\" />";
            $acceptedFiles = ".avi,.mpeg,.mkv,.mp4,.webm,.ogg,.m4v";

            $genVideoFiles = true;


            if ($progress["show"]) {
                $showFiles .= "</div><div class=\"file-progress\"><div><div class=\"progressLabel\">mp4</div><div class=\"progress mp4\"></div></div><div><div class=\"progressLabel\">ogg</div><div class=\"progress ogg\"></div></div><div><div class=\"progressLabel\">webm</div><div class=\"progress webm\"></div></div><script>
                        progressMp4 = $(\".progress.mp4\").progressbar({color: '#9BC243'});
                        progressOgg = $(\".progress.ogg\").progressbar({color: '#9BC243'});
                        progressWebm = $(\".progress.webm\").progressbar({color: '#9BC243'});
                        progressMp4.progress(" . $progress["mp4"]["percent"] . ");
                        progressOgg.progress(" . $progress["ogg"]["percent"] . ");
                        progressWebm.progress(" . $progress["webm"]["percent"] . ");
</script>";
            }

            break;
    }


    $updatequery[0] = "UPDATE shop_item_file SET filename = '";
    $updatequery[1] = "', mp4 = 0, webm = 0, ogg = 0 WHERE id = " . $input_file["id"];
    $thumbPath = $GLOBALS["shop_setup"]["image_config"];

    if ($fileExists && $input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

    if ($multiupload) {
        if ($actionUriComponents["ajax"] == 1) {
            $lineNoQuery = "SELECT max(line_no) AS lastLineNo FROM shop_item_file where company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "' AND type = '" . $itemFileType . "'";
            $lineNoResult = mysqli_query($GLOBALS["mysql_con"], $lineNoQuery);
            $lineNo = mysqli_fetch_array($lineNoResult);
            if (empty($actionUriComponents["mu"])) {
                $actionUriComponents["initialLineNo"] = $actionUriComponents["line_no"];
                $actionUriComponents["old_line_no"] = $actionUriComponents["line_no"];
                $actionUriComponents["mu"] = 1;
                if ($itemFileType !== \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE) {
                    $actionUriComponents["line_no"] = $lineNo["lastLineNo"] + 10000;
                } else {
                    $actionUriComponents["line_no"] = $lineNo["lastLineNo"];
                }
            } else {
                $actionUriComponents["old_line_no"] = $actionUriComponents["line_no"];
                if ($itemFileType !== \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE) {
                    $actionUriComponents["line_no"] = $lineNo["lastLineNo"] + 10000;
                } else {
                    $actionUriComponents["line_no"] = $lineNo["lastLineNo"];
                }
                $actionUriComponents["mu"] = 1;
                //$actionUriComponents["initialLineNo"] = $actionUriComponents["initialLineNo"];
                $updatequery[2] = "UPDATE shop_item_file SET description = '";
                $updatequery[3] = "' WHERE id = " . $input_file["id"];
            }
        } else {
            $actionUriComponents["initialLineNo"] = $actionUriComponents["line_no"];
        }

        if ($get["ajax"] != 1) {

            ?>
            <script language="JavaScript">
                var initialLineNo = '<?= $actionUriComponents["initialLineNo"] ?>';
                var newLineNo = '<?= $actionUriComponents["line_no"] ?>';
                var oldLineNo = '<?= $actionUriComponents["old_line_no"] ?>';
                var insertId = '<?= $insertID ?>';
                var fileObject = {
                    ShopCode: "<?= $get["shop_code"] ?>",
                    LanguageCode: "<?= $get["language_code"] ?>",
                    ItemNo: "<?= $get["item_no"] ?>",
                    Type: "<?= $get["type"] ?>",
                    VariantCode: "",
                    LineNo: "",
                    FileName: ""
                };
            </script>
            <?
        }

        $nextPrevUri = $_SERVER['PHP_SELF'];
        foreach ($actionUriComponents as $key => $value) {
            if ($count == 0) {
                if ($key != "line_no" && $key != "initialLineNo") {
                    $nextPrevUri .= "?" . $key . "=" . urlencode($value);
                }
                $count = 1;
            } else {
                if ($key != "line_no" && $key != "initialLineNo") {
                    $nextPrevUri .= "&" . $key . "=" . urlencode($value);
                }
            }
        }
        $nextPrevUri .= "&line_no=";

        //Next exists?
        $nextQuery = "SELECT line_no FROM shop_item_file WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "' AND type = '" . $get["type"] . "' AND line_no > " . $get["line_no"] . " ORDER BY line_no ASC LIMIT 1";
        $nextResult = mysqli_query($GLOBALS["mysql_con"], $nextQuery);
        if (mysqli_num_rows($nextResult) > 0) {
            $isNextRecord = true;
            $nextLineNo = @mysqli_fetch_assoc($nextResult)["line_no"];
            $nextUri = $nextPrevUri . $nextLineNo;
        }


        //Prev exists?
        $prevQuery = "SELECT line_no FROM shop_item_file WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND item_no = '" . $get["item_no"] . "' AND type = '" . $get["type"] . "' AND line_no < " . $get["line_no"] . " ORDER BY line_no DESC LIMIT 1";
        $prevResult = mysqli_query($GLOBALS["mysql_con"], $prevQuery);
        if (mysqli_num_rows($prevResult) > 0) {
            $isPrevRecord = true;
            $prevLineNo = @mysqli_fetch_assoc($prevResult)["line_no"];
            $prevUri = $nextPrevUri . $prevLineNo;
        }


    }

}

if ($request["webform"] == "TxtModAttac") {

    if ((int)$get['attachment_no'] != 1 && (int)$get['attachment_no'] != 2) {
        $get['attachment_no'] = 1;
    }

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_text_module SET attachment_" . $get['attachment_no'] . " = '' WHERE company = '" . $get["company"] . "' AND code = '" . $get["text_module_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = random_int(100,999999) . "_";

    $countquery = "SELECT id, attachment_" . $get['attachment_no'] . ",code FROM shop_text_module WHERE company = '" . $get["company"] . "' AND code = '" . $get["text_module_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_documents"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file["id"] = @mysqli_result($result, 0, 0);
        $input_file_loc = @mysqli_result($result, 0, 1);
        $input_file_code = @mysqli_result($result, 0, 2);
        $dlUrl .= "&attachment_no=" . $get['attachment_no'] . "&file=" . $input_file["id"];
        if ($input_file_loc != "" && $input_file_loc !== 0) {
            $button_class = get_button_file_typ_new($input_file_loc);
            if (strpos($input_file_loc, '_')) {
                $input_file_loc = substr($input_file_loc, strpos($input_file_loc, '_') + 1);
            }
            $showFiles = "<i data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' class=\"fa fa-4x fa-file-" . $button_class . "-o\"></i><br><br><span>" . urldecode($input_file_loc) . "</span>";

        }

        $fileTemplate = "<i data-url=\"%3\" data-file-id=\"%4\" class=\"fa fa-4x fa-file-%2\"></i><br><br><span>%1</span>";
    }
    $acceptedFiles = ".jpg,.jpeg,.gif,.png,.pdf,.doc,.docx,.txt,.xls,.xlsx,.csv,.rtf,.zip,.rar";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

    $updatequery[0] = "UPDATE shop_text_module SET attachment_" . $get['attachment_no'] . " = '";
    $updatequery[1] = "' WHERE code = '" . $get['text_module_code'] . "'";
}

if ($request["webform"] == "AttributeIcon") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_attribute_option SET icon = '' WHERE company = '" . $get["company"] . "' AND code = '" . $get["code"] . "' AND attribute_code = '" . $get["attribute_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = random_int(100,999999) . "_";

    $countquery = "SELECT id, icon AS filename FROM shop_attribute_option WHERE company = '" . $get["company"] . "' AND code = '" . $get["code"] . "' AND attribute_code = '" . $get["attribute_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_filter_icon"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&file=" . $input_file["id"];
        if ($input_file_loc != "" && $input_file_loc !== 0) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";

        }

        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "%1\" />";
    }
    $updatequery[0] = "UPDATE shop_attribute_option SET icon = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $uploaddir;
    $thumbWidth = $GLOBALS["shop_setup"]["filter_icon_maxwidth"];
    $thumbHeight = $GLOBALS["shop_setup"]["filter_icon_maxheight"];
    $deleteOriginal = TRUE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "CouponBackground") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_attribute_option SET icon = '' WHERE company = '" . $get["company"] . "' AND code = '" . $get["code"] . "' AND attribute_code = '" . $get["attribute_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = random_int(100,999999) . "_";
    // Datei anhand GET-Parameter aus Datenbank laden
    $countquery = "SELECT id, digital_coupon_background_" . $get["no"] . " AS filename FROM shop_language WHERE company = '" . $get["company"] . "' AND shop_code= '" . $get["shop_code"] . "' AND code = '" . $get["code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_dc"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&background_no=" . $get["no"] . "&file=" . $input_file["id"];
        if ($input_file_loc != "" && $input_file_loc !== 0) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["dc_image_config"][2]["path"] . "/" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";

        }

        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["dc_image_config"][2]["path"] . "/%1\" />";
    }

    $updatequery[0] = "UPDATE shop_language SET digital_coupon_background_" . $get["no"] . " = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $GLOBALS["shop_setup"]["dc_image_config"];
    $deleteOriginal = FALSE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }
}

if ($request["webform"] == "CategoryImage") {

    switch ($get['type']) {
        case 0:
            $thumbPath = $GLOBALS["shop_setup"]["uploaddir_category_picture"];
            $content_field = "category_picture";
            $thumbWidth = $GLOBALS["shop_setup"]["category_picture_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["category_picture_maxheight"];
            break;
        case 1:
            $thumbPath = $GLOBALS["shop_setup"]["uploaddir_category_icon"];
            $content_field = "category_icon";
            $thumbWidth = $GLOBALS["shop_setup"]["category_icon_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["category_icon_maxheight"];
            break;
    }

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_category SET " . $content_field . " = '' WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = random_int(100,999999) . "_";

    // Datei anhand GET-Parameter aus Datenbank laden
    $countquery = "SELECT id," . $content_field . " AS filename FROM shop_category WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);

    $uploaddir = $GLOBALS["shop_setup"]["uploaddir"];
    $genThumbs = true;
    $deleteOriginal = FALSE;

    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
    }
    $dlUrl .= "&type=" . $content_field . "&file=" . $input_file["id"];
    if ($input_file["filename"] != "" && $input_file["filename"] != '0') {
        $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $thumbPath . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";

    }

    $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $thumbPath . "%1\" />";

    $updatequery[0] = "UPDATE shop_category SET " . $content_field . " = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];#
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }
}

if ($request["webform"] == "Placeholder") {


    // Datei anhand GET-Parameter aus Datenbank laden
    $countquery = "SELECT id, item_placeholder_image AS filename FROM shop_language WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND code = '" . $get["language_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);

    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        //Zufallszahl für Dateinamen erstellen
        $fileNamePrefix = "";
    } else {
        $fileNamePrefix = random_int(100,999999) . "_";
    }

    $uploaddir = $GLOBALS["shop_setup"]["uploaddir"];
    $genThumbs = true;
    $deleteOriginal = FALSE;
    $deleteExisting = TRUE;

    $thumbPath = $GLOBALS["shop_setup"]["image_config"];

    $newArrayKey = max(array_keys($thumbPath)) + 1;
    $thumbPath[$newArrayKey]["path"] = $GLOBALS["shop_setup"]["uploaddir_category_icon"];
    $thumbPath[$newArrayKey]["maxwidth"] = $GLOBALS["shop_setup"]["category_icon_maxwidth"];
    $thumbPath[$newArrayKey]["maxheight"] = $GLOBALS["shop_setup"]["category_icon_maxheight"];

    //Delete
    //$jsonResponse["debug"]["POST"] = $post;
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_language SET item_placeholder_image = '' WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND code = '" . $get["language_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        foreach ($thumbPath as $image) {
            $oldfile = "../../.." . $image["path"] . "/" . $fileNamePrefix . $input_file["filename"];
            if (file_exists($oldfile)) {
                @unlink($oldfile);
            }
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    // Datei anhand GET-Parameter aus Datenbank laden
    $countquery = "SELECT id, item_placeholder_image AS filename FROM shop_language WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND code = '" . $get["language_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);

    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        //Zufallszahl für Dateinamen erstellen
        $fileNamePrefix = random_int(100,999999) . "_";
    }else {
        $fileNamePrefix = random_int(100,999999) . "_";
    }
    $dlUrl .= "&file=" . $input_file["id"];
    if ($input_file["filename"] != "" && $input_file["filename"] != '0') {
        $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $thumbPath[3]["path"] . "/" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";
    }

    $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $thumbPath[3]["path"] . "/%1\" />";


    $updatequery[0] = "UPDATE shop_language SET item_placeholder_image = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "CampaignImages") {

    $field = filter_var($get['field'], FILTER_SANITIZE_STRING);
    $company = filter_var($get['company'], FILTER_SANITIZE_STRING);
    $code = filter_var($get['code'], FILTER_SANITIZE_STRING);

    $mysqlField = mysqli_real_escape_string($GLOBALS['mysql_con'], $field);
    $mysqlCompany = mysqli_real_escape_string($GLOBALS['mysql_con'], $company);
    $mysqlCode = mysqli_real_escape_string($GLOBALS['mysql_con'], $code);

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_campaign_header SET " . $mysqlField . " = '' WHERE company = '" . $mysqlCompany . "' AND code = '" . $mysqlCode . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }


    $countquery = <<<SQL
	SELECT
		`id`,
		`{$mysqlField}` AS filename
	FROM
		`shop_campaign_header`
	WHERE
			`company` = '{$mysqlCompany}'
		AND `code` = '{$mysqlCode}'
SQL;
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $numRows = @mysqli_num_rows($result);
    $input_file = [];
    if ($numRows > 0) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $fileNamePrefix = $input_file["id"] . "_";
    }

    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . '/original/';
    $genThumbs = true;
    $deleteOriginal = FALSE;
    $deleteExisting = FALSE;

    $thumbPath = $GLOBALS["shop_setup"]["uploaddir_campaign_images"];

    switch ($field) {
        case 'icon_condition_items':
            $thumbWidth = $GLOBALS["shop_setup"]["campaign_icon_condition_items_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["campaign_icon_condition_items_maxheight"];
            break;
        case 'banner_condition_items':
            $thumbWidth = $GLOBALS["shop_setup"]["campaign_banner_condition_items_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["campaign_banner_condition_items_maxheight"];
            break;
        case 'icon_action_items':
            $thumbWidth = $GLOBALS["shop_setup"]["campaign_icon_action_items_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["campaign_icon_action_items_maxheight"];
            break;
        case 'banner_action_items':
            $thumbWidth = $GLOBALS["shop_setup"]["campaign_banner_action_items_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["campaign_banner_action_items_maxheight"];
            break;
        case 'banner_basket':
            $thumbWidth = $GLOBALS["shop_setup"]["campaign_banner_basket_maxwidth"];
            $thumbHeight = $GLOBALS["shop_setup"]["campaign_banner_basket_maxheight"];
            break;
        default:
            $thumbWidth = 1;
            $thumbHeight = 1;
    }

    $dlUrl .= "&field=" . $mysqlField . "&file=" . $input_file["id"];
    if ($input_file["filename"] != "" && $input_file["filename"] != '0') {
        $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";
    }
    $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $GLOBALS["shop_setup"]["uploaddir_campaign_images"] . "%1\" />";


    $updatequery[0] = "UPDATE shop_campaign_header SET " . $mysqlField . " = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "ShippingOption") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_shipping_option SET logo = '' WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = "";

    $countquery = "SELECT id, logo AS filename FROM shop_shipping_option WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND line_no = '" . $get["line_no"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_order_icons"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&file=" . $input_file["id"];
        if ($input_file_loc != "" && !(is_int($input_file_loc) && $input_file_loc == 0)) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";

        }
        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "%1\" />";
    }
    $updatequery[0] = "UPDATE shop_shipping_option SET logo = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $uploaddir;
    $thumbWidth = $GLOBALS["shop_setup"]["order_icon_maxwidth"];
    $thumbHeight = $GLOBALS["shop_setup"]["order_icon_maxheight"];
    $deleteOriginal = TRUE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "ShippingOptionTranslation") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shipping_option_translation SET logo = '' WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND line_no = '" . $get["line_no"] . "' AND language_code = '" . $get["language_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = "";

    $countquery = "SELECT id, logo AS filename FROM shipping_option_translation WHERE company = '" . $get["company"] . "' AND shipping_group_code = '" . $get["shipping_group_code"] . "' AND line_no = '" . $get["line_no"] . "' AND language_code = '" . $get["language_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_order_icons"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&file=" . $input_file["id"];
        if ($input_file_loc != "" && !(is_int($input_file_loc) && $input_file_loc == 0)) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";

        }
        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "%1\" />";
    }
    $updatequery[0] = "UPDATE shipping_option_translation SET logo = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $uploaddir;
    $thumbWidth = $GLOBALS["shop_setup"]["order_icon_maxwidth"];
    $thumbHeight = $GLOBALS["shop_setup"]["order_icon_maxheight"];
    $deleteOriginal = TRUE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "PaymentOption") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_payment_option SET logo = '' WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = "";

    $countquery = "SELECT id, logo AS filename FROM shop_payment_option WHERE company = '" . $get["company"] . "' AND shop_code = '" . $get["shop_code"] . "' AND language_code = '" . $get["language_code"] . "' AND line_no = '" . $get["line_no"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_order_icons"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&file=" . $input_file["id"];
        if ($input_file_loc != "" && !(is_int($input_file_loc) && $input_file_loc == 0)) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";
        }

        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "%1\" />";
    }
    $updatequery[0] = "UPDATE shop_payment_option SET logo = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $uploaddir;
    $thumbWidth = $GLOBALS["shop_setup"]["order_icon_maxwidth"];
    $thumbHeight = $GLOBALS["shop_setup"]["order_icon_maxheight"];
    $deleteOriginal = TRUE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }

}

if ($request["webform"] == "SalesPerson") {

    //Delete
    if (!empty($post["delete_file"]) && $return != "ERROR") {
        $delquery = "UPDATE shop_salesperson SET image = '' WHERE company = '" . $get["company"] . "' AND salesperson_code = '" . $get["sales_person_code"] . "'";
        if (!mysqli_query($GLOBALS['mysql_con'], $delquery)) {
            $return = "ERROR";
        }
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    } else {
        //echo $spacer . $return . $spacer;
        $jsonResponse["deleteResponse"] = $return;
    }

    //Zufallszahl für Dateinamen erstellen
    $fileNamePrefix = "";

    $countquery = "SELECT id, image AS filename, salesperson_code FROM shop_salesperson WHERE company = '" . $get["company"] . "' AND salesperson_code = '" . $get["sales_person_code"] . "'";
    $result = @mysqli_query($GLOBALS['mysql_con'], $countquery);
    $uploaddir = $GLOBALS["shop_setup"]["uploaddir_salesperson_images"];
    if (@mysqli_num_rows($result) == 1) {
        $input_file = @mysqli_fetch_assoc($result);
        $input_file_loc = $input_file["filename"];
        $dlUrl .= "&file=" . $input_file["id"];
        if ($input_file_loc != "" && !(is_int($input_file_loc) && $input_file_loc == 0)) {
            $showFiles = "<img data-url='" . $dlUrl . "' data-file-id='" . $input_file["id"] . "' style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "" . $input_file["filename"] . "?id=" . rand(1, 900) . "\" />\n";
        }

        $fileTemplate = "<img data-url=\"%3\" data-file-id=\"%4\" style=\"max-width: 100%; max-height: 100%;\" src=\"" . $uploaddir . "%1\" />";
    }
    $acceptedFiles = ".jpg,.jpeg,.gif,.png,.pdf,.doc,.docx,.txt,.xls,.xlsx,.csv,.rtf,.zip,.rar";

    $updatequery[0] = "UPDATE shop_salesperson SET image = '";
    $updatequery[1] = "' WHERE id = " . $input_file["id"];
    $genThumbs = true;
    $thumbPath = $uploaddir;
    $thumbWidth = $GLOBALS["shop_setup"]["salesperson_image_maxwidth"];
    $thumbHeight = $GLOBALS["shop_setup"]["salesperson_image_maxheight"];
    $deleteOriginal = TRUE;
    $acceptedFiles = ".jpg,.jpeg,.gif,.png";

    if ($input_file["filename"] != "") {
        $downloadAble = true;
        $deleteAble = true;
    }
}

if (@mysqli_num_rows($result) == 1) {

    $deleteFilePath = $uploaddir;

    //Sprachvariablen für Webform
    $file_message['de'] = "";
    $file_message['en'] = "";
    $file_message1['de'] = "Datei" . ($multiupload ? '(en)' : '') . " hierher ziehen\n";
    $file_message1['en'] = "Drag &amp; Drop Files Here\n";
    $file_message2['de'] = "oder\n";
    $file_message2['en'] = "or\n";
    $loading_message['de'] = "Datei wird hochgeladen.Bitte warten...";
    $loading_message['en'] = "File is uploading. Please wait...";
    $waiting_message['de'] = "Warte auf upload...";
    $waiting_message['en'] = "waiting for upload...";
    $success_message['de'] = "Datei wurde erfolgreich hochgeladen";
    $success_message['en'] = "File upload is complete";
    $upload['de'] = "Upload starten";
    $upload['en'] = "start upload";

    $text_constant["de"]["dropzone_message1"] = "Hier klicken, um Dateien hochzuladen oder Dateien hierher ziehen.<br>Erlaubte Dateiformate: " . $acceptedFiles;
    $text_constant["en"]["dropzone_message1"] = "Drop files here to upload.<br>Accepted files: " . $acceptedFiles;

    $text_constant["de"]["dropzone_message2"] = "Ihr Browser unterstützt kein Drag n Drop von Dateiuploads.";
    $text_constant["en"]["dropzone_message2"] = "Your browser does not support drag\'n\'drop file uploads.";

    $text_constant["de"]["dropzone_message3"] = "Bitte das Standardformular für den Upload benutzen.";
    $text_constant["en"]["dropzone_message3"] = "Please use the fallback form below to upload your files like in the olden days.";

    $text_constant["de"]["dropzone_message4"] = "Bild entfernen";
    $text_constant["en"]["dropzone_message4"] = "Remove file";

    $text_constant["de"]["dropzone_message5"] = "Upload Abbrechen";
    $text_constant["en"]["dropzone_message5"] = "Cancel upload";

    $text_constant["de"]["dropzone_message6"] = "Upload wirklich abbrechen?";
    $text_constant["en"]["dropzone_message6"] = "Are you sure you want to cancel this upload?";

    $text_constant["de"]["dropzone_message7"] = "Sie können keine weiteren Dateien zur Warteschlange hinzufügen.";
    $text_constant["en"]["dropzone_message7"] = "You can not upload any more files.";


    $downloadCaption["de"] = "Herunterladen";
    $downloadCaption["en"] = "Download";

    $deleteCaption["de"] = "Löschen";
    $deleteCaption["en"] = "Delete";

    $ribbonCaption["de"] = "Vorgang";
    $ribbonCaption["en"] = "Process";

    $prevCaption["de"] = "Vorheriger";
    $prevCaption["en"] = "Previous";

    $nextCaption["de"] = "Nächster";
    $nextCaption["en"] = "Next";

    $jsonResponse["dlUrl"] = $dlUrl;
    //$jsonResponse["debug"]["before_isset_files"] = print_r($_FILES,true);

    if (isset($_FILES['input_file'])) {

        if ($itemFileType === \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE) {
            $fileNamePrefix = '';
        }

        //$jsonResponse["debug"]["in_isset_files"] = true;
        if ($multiupload) {

            $input_file_loc = filename_validation($_FILES['input_file']['name']);
            //echo $spacer . $fileNamePrefix . $input_file_loc . $spacer;
            $jsonResponse["fileName"] = $fileNamePrefix . $input_file_loc;
            $uld = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . $uploaddir;
            if (!is_dir($uld)) {
                if (!mkdir($uld) && !is_dir($uld)) {
                    echo "Fehler in Webform 0 - no upload directory at $uld";
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $uld));
                }
            }
            $uploadfile = $uld . DIRECTORY_SEPARATOR . $fileNamePrefix . $input_file_loc;

            if (file_exists($uploadfile)) {
                @unlink($uploadfile);
            }
            $updateFileName = $fileNamePrefix . $input_file_loc;
            if ($itemFileType === \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE) {
                $updateFileName = $uploadSubDir;
            }
            if (move_uploaded_file($_FILES['input_file']['tmp_name'], $uploadfile)) {

                if (!@mysqli_query($GLOBALS['mysql_con'], $updatequery[0] . $updateFileName . $updatequery[1])) {

                    echo "Fehler in Webform 1. query: " . $updatequery[0] . $updateFileName . $updatequery[1] . "\n";
                    exit();
                }
                if (!empty($updatequery[2]) && !empty($updatequery[3])) {
                    if (!@mysqli_query($GLOBALS['mysql_con'], $updatequery[2] . $updateFileName . $updatequery[3])) {
                        echo "Fehler in Webform 1,5. query: " . $updatequery[2] . $updateFileName . $updatequery[3] . "\n";
                        exit();
                    }
                }
            } else {
                $src = $_FILES['input_file']['tmp_name'];
                $tgt = $uploadfile;
                echo "Fehler in Webform 2. Could not move uploaded file from [$src] to [$tgt]. Query: " . $countquery . "\n";
                exit();
            }

            //echo $spacer . $actionUriComponents["line_no"];
            $jsonResponse["lineNo"] = $actionUriComponents["line_no"];
            //echo $spacer . $actionUriComponents["old_line_no"];
            $jsonResponse["oldLineNo"] = $actionUriComponents["old_line_no"];
            //echo $spacer . $input_file["id"];
            $jsonResponse["fileId"] = $input_file["id"];
            $button_class = get_button_file_typ_new($input_file_loc);
            //echo $spacer . $button_class . $spacer;
            $jsonResponse["buttonClass"] = $button_class;
        } else {


            //$jsonResponse["debug"]["no_multi"] = true;
            $oldfile = "../../.." . $uploaddir . $input_file_loc;
            if (file_exists($oldfile)) {
                @unlink($oldfile);
            }
            $input_file_loc = filename_validation($_FILES['input_file']['name']);
            //echo $spacer . $fileNamePrefix . $input_file_loc . $spacer;
            $jsonResponse["fileName"] = $fileNamePrefix . $input_file_loc;
            $uploadfile = "../../.." . $uploaddir . $fileNamePrefix . $input_file_loc;

            if (file_exists($uploadfile)) {
                @unlink($uploadfile);
            }
            if (move_uploaded_file($_FILES['input_file']['tmp_name'], $uploadfile)) {
                if (!@mysqli_query($GLOBALS['mysql_con'], $updatequery[0] . $fileNamePrefix . $input_file_loc . $updatequery[1])) {
                    $jsonResponse["ERROR"]["save_uploaded_file_mysql"] = "ERROR";
                    //echo "Fehler in Webform 3. query: " . $updatequery[0] . $fileNamePrefix . $input_file_loc . $updatequery[1] . "\n";
                    //exit();
                }
            } else {
                $jsonResponse["ERROR"]["move_uploaded_file"] = "ERROR";
                //echo "Fehler in Webform 4. query: " . $countquery . "\n";
                //exit();
            }
            //echo $spacer;
            //echo $spacer;
            //echo $spacer . $input_file["id"];
            $jsonResponse["fileId"] = $input_file["id"];
            $button_class = get_button_file_typ_new($input_file_loc);
            //echo $spacer . $button_class . $spacer;
            $jsonResponse["buttonClass"] = $button_class;
        }

        if ($updateMarketplace) {
            //Update Marketplace
            $query = "SELECT * FROM shop_shop WHERE (code = '" . $input_file["shop_code"] . "' OR use_items_from_shop_code = '" . $input_file["shop_code"] . "')";
            $result = mysqli_query($GLOBALS["mysql_con"],$query);
            while ($shop = mysqli_fetch_array($result)) {
                $query2 = "UPDATE shop_marketplace_item_update SET marketplace_update = 1, marketplace_update_images = 1 WHERE 
                        item_no = '" . $input_file["item_no"] . "' 
                        AND item_shop_code = '" . $input_file["shop_code"] . "' 
                        AND item_language_code = '" . $input_file["language_code"] . "' 
                        AND language_code = '".$shop["code"]."'
                        AND shop_code = '".$shop["code"]."'
                        ";
                @mysqli_query($GLOBALS["mysql_con"],$query2);
            }
        }

        if ($genVideoFiles) {
            $jsonResponse["videoCue"] = true;
            if (!encodeVideo($jsonResponse, $fileNamePrefix . $input_file_loc, $input_file["id"], true, 1, false, false, false)) {
                $jsonResponse["videoGenerateResponse"] = "Error";
            } else {
                $jsonResponse["videoGenerateResponse"] = "Success";
            }
            $jsonResponse["videoFileTemplate"] = $fileTemplateThumbnail;
        }

        if ($genThumbs) {
            if ($deleteExisting) {
                if (is_array($thumbPath)) {
                    foreach ($thumbPath as $image) {
                        $oldfile = "../../.." . $image["path"] . "/" . $fileNamePrefix . $input_file_loc;
                        if (file_exists($oldfile)) {
                            @unlink($oldfile);
                        }
                    }
                } else {
                    $oldfile = "../../.." . $thumbPath . "/" . $fileNamePrefix . $input_file_loc;
                    if (file_exists($oldfile)) {
                        @unlink($oldfile);
                    }
                }
            }

            if (is_array($thumbPath)) {
                foreach ($thumbPath as $image) {
                    $newuploadfile = "../../.." . $image["path"] . "/" . $fileNamePrefix . $input_file_loc;
                    image_resize($uploadfile, $newuploadfile, $image["maxwidth"], $image["maxheight"]);
                }
                if (file_exists($uploadfile) && $deleteOriginal) {
                    @unlink($uploadfile);
                }
            } else {
                $newuploadfile = "../../../" . $thumbPath . "/" . $fileNamePrefix . $input_file_loc;
                //$jsonResponse["debug"]["1"] = print_r($newuploadfile, true);
                //$jsonResponse["debug"]["2"] = print_r($uploadfile, true);
                image_resize($uploadfile, $newuploadfile, $thumbWidth, $thumbHeight);
                /*if (file_exists($uploadfile) && $deleteOriginal) {
                    @unlink($uploadfile);
                }*/
            }
        }
    }
    if ($get["ajax"] != 1) {
        ?>
        <div class="wrapper">
            <div class="ribbon">
                <div class="ribbon-section">
                    <?
                    $showNextRibbon = false;
                    if ($multiupload) {
                        $showNextRibbon = true;
                    }
                    $disableNextRibbon = " disabled";
                    if ($multiupload && $isNextRecord) {
                        $disableNextRibbon = "";
                    }
                    $showPrevRibbon = false;
                    if ($multiupload) {
                        $showPrevRibbon = true;
                    }
                    $disablePrevRibbon = " disabled";
                    if ($multiupload && $isPrevRecord) {
                        $disablePrevRibbon = "";
                    }
                    $disableDownloadRibbon = " disabled";
                    if ($downloadAble) {
                        $disableDownloadRibbon = "";
                    }
                    $disableDeleteRibbon = " disabled";
                    if ($deleteAble) {
                        $disableDeleteRibbon = "";
                    }
                    ?>
                    <div class="ribbon-icon download<?= $disableDownloadRibbon ?>">
                        <div class="ribbon-icon-inactive"></div>
                        <div class="ribbon-button"></div>
                        <div class="ribbon-caption"><?= $downloadCaption[$language_code] ?></div>
                    </div>
                    <div class="ribbon-icon delete<?= $disableDeleteRibbon ?>">
                        <div class="ribbon-icon-inactive"></div>
                        <div class="ribbon-button"></div>
                        <div class="ribbon-caption"><?= $deleteCaption[$language_code] ?></div>
                    </div>
                    <?
                    if ($showPrevRibbon) {
                        ?>
                        <div data-url="<?= $prevUri ?>"
                             class="ribbon-icon prev ribbon-navigation<?= $disablePrevRibbon ?>">
                            <div class="ribbon-icon-inactive"></div>
                            <div class="ribbon-button"></div>
                            <div class="ribbon-caption"><?= $prevCaption[$language_code] ?></div>
                        </div>
                        <?
                    }
                    ?>
                    <?
                    if ($showNextRibbon) {
                        ?>
                        <div data-url="<?= $nextUri ?>"
                             class="ribbon-icon next ribbon-navigation<?= $disableNextRibbon ?>">
                            <div class="ribbon-icon-inactive"></div>
                            <div class="ribbon-button"></div>
                            <div class="ribbon-caption"><?= $nextCaption[$language_code] ?></div>
                        </div>
                        <?
                    }
                    ?>
                    <div class="ribbon-name"><?= $ribbonCaption[$language_code] ?></div>

                </div>
            </div>
            <div id="box1" class="<?= $box ?>">
                <div id="fileList" style="display: none;">
                </div>
                <?
                if ($showFiles <> '') {
                    echo str_replace('%1', $input_file["id"], str_replace('%2', $dlUrl, $file[0]));
                    echo $showFiles;
                    echo $file[1];
                } else {
                    echo $file_message[$language_code];
                }
                ?>
            </div>
            <div class="dropzone-wrapper">
                <div id="dropzone" class="dropzone">
                    <input type="hidden" name="dcDropzone" value="1"/>
                </div>
            </div>
        </div>
        <!-- <div class="button-area">
            <button id="startUpload"><?= $upload[$language_code] ?></button>
        </div> -->

        <script type="text/javascript">
            var multiupload = <?= ($multiupload) ? 'true' : 'false' ?>;
            var multiuploadtoSingleFile = <?= ($multiupload && $itemFileType === \DynCom\dc\dcShop\classes\WebshopItemFile::TYPE_360_DEGREE_IMAGE) ? 'true' : 'false' ?>;
            Dropzone.autoDiscover = false;
            var fileHtml = [];
            fileHtml[0] = "<?= $file[0] ?>";
            fileHtml[1] = "<?= $file[1] ?>";
            var first = true;
            var fileTemplate = '<?= $fileTemplate ?>';
            var fileMessage = '<?= $file_message[$language_code] ?>';
            deleteFilePath = '<?= $deleteFilePath ?>';
            filename = '<?= $fileNamePrefix . $input_file_loc ?>';
            filenameDelete = '<?= $fileNamePrefix . $input_file_loc ?>';
            filenameDelete = '<?= $input_file_loc ?>';
            //filename = '<?= $input_file_loc ?>';
            var add = true;
            var newFile = '';
            var polling = false;
            var progressMp4;
            var progressOgg;
            var progressWebm;
            $(document).ready(function () {
                <?php
                $actionUri = $_SERVER['PHP_SELF'] . "";
                $dropzoneUri = $_SERVER['PHP_SELF'] . "";
                if (!array_key_exists("ajax", $actionUriComponents)) {
                    $actionUriComponents["ajax"] = 1;
                }
                if (!array_key_exists("mu", $actionUriComponents) && $multiupload) {
                    $actionUriComponents["mu"] = 1;
                }
                $count = 0;
                foreach ($actionUriComponents as $key => $value) {
                    if ($count == 0) {
                        $actionUri .= "?" . $key . "=" . urlencode($value);
                        if ($key != "line_no") {
                            $dropzoneUri .= "?" . $key . "=" . urlencode($value);
                        }
                        $count = 1;
                    } else {
                        $actionUri .= "&" . $key . "=" . urlencode($value);
                        if ($key != "line_no") {
                            $dropzoneUri .= "&" . $key . "=" . urlencode($value);
                        }
                    }
                    $jsonResponse["params"][$key] = urlencode($value);
                }
                $dropzoneUri .= "&line_no=";
                ?>

                <? if($progress["show"]) { ?>
                poll();
                <? } ?>

                function poll(self) {
                    if (!polling || self) {
                        polling = true;
                        poller = setTimeout(function () {
                            $.ajax({
                                url: "<?= '/plugins/ffmpeg/ffmpegQueue.php?id=' . $input_file["id"] ?>&time=" + Math.floor(Math.random() * Math.floor(999999)),
                                type: "GET",
                                success: function (data) {
                                    progressMp4.progress(data.progress.mp4);
                                    progressOgg.progress(data.progress.ogg);
                                    progressWebm.progress(data.progress.webm);
                                    if (data.progress.mp4 == 100 && data.progress.ogg == 100 && data.progress.webm == 100) {
                                        location.reload();
                                    }

                                },
                                dataType: "json",
                                complete: poll(true),
                                timeout: 1000
                            })
                        }, 2000);
                    }
                }
                $('#dropzone').dropzone({
                    paramName: 'input_file',
                    //url: '<?= $actionUri ?>',
                    url: '<?= $actionUri ?>',
                    thumbnailWidth: 80,
                    thumbnailHeight: 80,
                    videoUpload: <?= ($genVideoFiles) ? 'true' : 'false' ?>,
                    complete: function (file) {
                        dcDropzone.removeFile(file);
                    },
                    success: function (file, responseText) {

                        //output = responseText.split("~||~");
                        //alert(output);
                        //filename = output[3];
                        //lineNo = output[5];
                        //oldLineNo = output[6];
                        //fileId = output[7];
                        //buttonClass = output[8];

                        filename = responseText.fileName;
                        filenameDelete = responseText.fileName;
                        var lineNo = responseText.lineNo;
                        var oldLineNo = responseText.oldLineNo;
                        var fileId = responseText.fileId;
                        var buttonClass = responseText.buttonClass;
                        var fileDlUrl = responseText.dlUrl;
                        var videoCue = responseText.videoCue;
                        var videoFileTemplate = responseText.videoFileTemplate;

                        if ($('#box1').children('.file_message').lenght > 0) {
                            $('#box1').children('.file_message').remove();
                        }

                        <? if ($genVideoFiles) { ?>
                        filename = filename.replace(/\.[^/.]+$/, "");
                        <? } ?>

                        if (videoCue) {
                            newFile = fileHtml[0] + videoFileTemplate.replace(/%1/g, filename).replace(/%2/g, buttonClass).replace(/%4/g, fileId).replace(/%3/g, fileDlUrl) + fileHtml[1];
                        } else {
                            newFile = fileHtml[0] + fileTemplate.replace(/%1/g, filename).replace(/%2/g, buttonClass).replace(/%4/g, fileId).replace(/%3/g, fileDlUrl) + fileHtml[1];
                        }

                        //alert(newFile);
                        //alert(fileHtml[0]);

                        if (multiupload) {
                            //Enable Next Icon
                            if (!first) {
                                if (oldLineNo > initialLineNo && $('.ribbon .ribbon-icon.next').hasClass("disabled")) {
                                    $('.ribbon .ribbon-icon.next').data('url', '<?= $nextPrevUri ?>' + oldLineNo)
                                    $('.ribbon .ribbon-icon.next').removeClass('disabled');
                                }
                            }
                        }
                        //Enable Download Icon
                        $('.ribbon .ribbon-icon.download ').removeClass('disabled');
                        //Enable Delete Icon
                        $('.ribbon .ribbon-icon.delete ').removeClass('disabled');

                        if (multiupload) {
                            if (first) {
                                $('#box1').children().remove();
                                first = false;
                            } else {
                                add = false;
                            }
                        } else {
                            $('#box1').children().remove();
                        }

                        if (add) {
                            //if (!videoCue) {
                            $('#box1').append(newFile);
                            //}

                            <? if ($genVideoFiles) { ?>
                            //videojs("my-video").dispose();
                            <? } ?>
                            <? if ($genVideoFiles) { ?>

                            /*videojs("my-video", {}, function () {
                             // Player (this) is initialized and ready.
                             });*/
                            <? } ?>
                        }

                        if (multiupload) {
                            fileObject.FileName = filename;
                            fileObject.LineNo = oldLineNo;
                            dcDropzone.options.url = '<?= $dropzoneUri ?>' + lineNo;
                            //SendMessage(window.parent, "fileuploaded", fileObject, "*");
                            fileObjectString = JSON.stringify(fileObject);
                            SendMessage(window.parent, "fileuploaded", fileObjectString, "*");
                        } else {
                            dcDropzone.removeAllFiles();
                        }
                        if (videoCue) {
                            if (!$('.file-progress').length) {
                                $('#box1').append($('<div class="file-progress"><div><div class="progressLabel">mp4</div><div class="progress mp4"></div></div><div><div class="progressLabel">ogg</div><div class="progress ogg"></div></div><div><div class="progressLabel">webm</div><div class="progress webm"></div></div></div>'));
                                progressMp4 = $(".progress.mp4").progressbar({color: '#9BC243'});
                                progressOgg = $(".progress.ogg").progressbar({color: '#9BC243'});
                                progressWebm = $(".progress.webm").progressbar({color: '#9BC243'});
                                progressMp4.progress(0);
                                progressOgg.progress(0);
                                progressWebm.progress(0);
                            }

                            poll();
                        }
                        if (file._removeLink) {
                            file._removeLink.textContent = this.options.dictRemoveFile;
                        }
                        if (file.previewElement) {
                            return file.previewElement.classList.add("dz-complete");
                        }
                    },
                    queuecomplete: function () {
                        //dcDropzone.options.autoProcessQueue = false;
                        first = true;
                        add = true;
                        if (multiupload) {
                            dcDropzone.options.url = '<?= $dropzoneUri ?>' + initialLineNo;
                        } else {
                            dcDropzone.options.url = '<?= $actionUri ?>'
                        }
                        filesCount = 0;
                    },
                    dictDefaultMessage: '<?php echo $text_constant[$language_code]["dropzone_message1"]; ?>',
                    dictFallbackMessage: '<?php echo $text_constant[$language_code]["dropzone_message2"]; ?>',
                    dictFallbackText: '<?php echo $text_constant[$language_code]["dropzone_message3"]; ?>',
                    dictRemoveFile: "<?php echo $text_constant[$language_code]["dropzone_message4"]; ?>",
                    dictCancelUpload: '<?php echo $text_constant[$language_code]["dropzone_message5"]; ?>',
                    dictCancelUploadConfirmation: '<?php echo $text_constant[$language_code]["dropzone_message6"]; ?>',
                    acceptedFiles: '<?= $acceptedFiles ?>',
                    autoProcessQueue: true,
                    addRemoveLinks: false,
                    maxFiles: <?= ($multiupload) ? 100 : 1 ?>,
                    maxFilesize: <?= $maxFileSize ?>,
                    parallelUploads: 1,
                    dictMaxFilesExceeded: '<?php echo $text_constant[$language_code]["dropzone_message7"]; ?>'

                });

                var dcDropzone = jQuery('#dropzone').get(0).dropzone;
                var filesCount = 0;
                dcDropzone.on("addedfiles",function(files) {
                    filesCount += files.length;
                });

                dcDropzone.on("sendingmultiple",function(files,xhr,formData) {
                   formData.append("no_of_files",filesCount);
                });

                dcDropzone.on("sending",function(file,xhr,formData) {
                    formData.append("file_stats",'all:' + dcDropzone.files.length + '|queued:' + dcDropzone.getQueuedFiles().length + '|added: ' + dcDropzone.getAddedFiles().length + '|active:' + dcDropzone.getActiveFiles().length + '|accepted: ' + dcDropzone.getAcceptedFiles().length + '|rejected: ' + dcDropzone.getRejectedFiles().length + '|files:' + dcDropzone.files.length);
                    formData.append("no_of_files",filesCount);
                    formData.append("is_first",first);
                });


                //dcDropzone.on("sending", function(file, xhr, data) {
                    //window.alert("sending data " + data.serialize());
                    // First param is the variable name used server side
                    // Second param is the value, you can add what you what
                    // Here I added an input value
                    //data.append("filecount", this.files.all.length);
                //});

                function removeDropzoneFiles() {
                    dcDropzone.removeAllFiles();
                }

                $('body').on('click', '#startUpload', function () {
                    if (dcDropzone.getQueuedFiles().length == 0 &&
                        dcDropzone.getUploadingFiles().length == 0
                    ) {
                        return;
                    }
                    dcDropzone.options.autoProcessQueue = true;
                    dcDropzone.processQueue();
                });
            });


            var elem;
            var dlUrl = "<?= $dlUrl ?>";
            $(document).ready(function () {
                $('body').on('click', '.ribbon .ribbon-icon.delete:not(.disabled)', function () {
                    elem = $($('#box1').children('.file').children()[0]);
                    var formData = new FormData();
                    formData.append('delete_file', elem.data('file-id'));
                    formData.append('delete_file_path', deleteFilePath + filenameDelete);
                    $.ajax({
                        url: '<?= $_SERVER["REQUEST_URI"] ?>&webform=<?= $request["webform"] ?>&webformtype=<?= $request["webformtype"] ?>&ajax=1',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data) {
                            output = data.deleteResponse;
                            //output = data.split("~||~");
                            //output = output[1];
                            if (output == "SUCCESS") {

                                if (elem.parents('#box1').children('.file').length == 1) {
                                    elem.parents('#box1').children().remove();
                                    $('#box1').append(fileMessage);
                                    if (!typeof poller === 'undefined') {
                                        clearTimeout(poller);
                                    }
                                    polling = false;
                                } else {
                                    elem.parent().remove();
                                }
                                //Disable Download Icon
                                $('.ribbon .ribbon-icon.download ').addClass('disabled');
                                //Disable Delete Icon
                                $('.ribbon .ribbon-icon.delete ').addClass('disabled');
                            }
                        },
                        error: function (xhr, err) {

                        }
                    });
                });
                $('body').on('click', '.ribbon .ribbon-icon.download:not(.disabled)', function () {
                    $('#dlframe').remove();
                    $('body').append('<iframe id="dlframe" style="display:none;"></iframe>');
                    $('#dlframe').attr('src', '/module/dcshop/webforms/download_file_new.php?webform=<?= $request["webform"] ?>' + $($('#box1').children('.file').children()[0]).data('url'));
                });
                $('body').on('click', '.ribbon .ribbon-navigation:not(.disabled)', function () {
                    window.location.href = $(this).data('url');
                });
            });
        </script>
        <?
    }

} else {
    echo "Fehler in Webform 5. \nquery: " . $countquery . "\n";
}

if ($get["ajax"] == 1) {
    echo json_encode($jsonResponse);
}