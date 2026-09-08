<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'filegallery.config.inc.php';


?>

<div class="filegallery_content">

        <?
        $query = "SELECT * FROM filegallery_line WHERE header_id = '" . $sitepart_id . "' ORDER BY sorting ASC";

        $result = @mysqli_query($GLOBALS['mysql_con'], $query);

        if (@mysqli_num_rows($result) > 0) {
            while ($filegalleryContent = @mysqli_fetch_array($result)) {
                $description = $filegalleryContent['description'];
                $filename    = $filegalleryContent['filename'];
                $extension   = $filegalleryContent['extension'];

                if ($filename == "") {
                    continue;
                }

                // datei laden
                $fileInfo = new SplFileInfo(ROOT_PATH . PATH_ORIGINAL_FRONTEND_FILEGALLERY . $filename);
                if (!$fileInfo->isReadable()) {
                    continue;
                }

                $icon = "";

                switch ($extension) {
                    case "7z":
                    case "cap":
                    case "rar":
                    case "zip":
                        $iconclass = "file-archive-o";
                        break;
                    case "mp3":
                        $iconclass = "file-audio-o";
                        break;
                    case "htm":
                    case "html":
                        $iconclass = "file-code-o";
                        break;
                    case "xls":
                    case "xlsx":
                    case "xlsm":
                        $iconclass = "file-excel-o";
                        break;
                    case "pdf":
                        $iconclass = "file-pdf-o";
                        break;
                    case "ai":
                    case "bmp":
                    case "cdr":
                    case "eps":
                    case "iso":
                    case "jpg":
                    case "jpeg":
                    case "raw":
                    case "tif":
                        $iconclass = "file-photo-o";
                        break;
                    case "":
                        $iconclass = "file-text-o";
                        break;
                    case "doc":
                    case "docx":
                    case "dot":
                        $iconclass = "file-word-o";
                        break;
                    case "mov":
                    case "mp4":
                    case "mpg":
                    case "wmv":
                        $iconclass = "file-video-o";
                        break;
                    case "flv":
                    case "swf":
                    case "gif":
                        $iconclass = "spinner";
                        break;
                    case "stp":
                        $iconclass = "paint-brush";
                        break;
                    case "exe":
                        $iconclass = "cog";
                        break;
                    default:
                        $iconclass = "file-o";
                }

                /*$icon = PATH_ICON_FRONTEND_FILEGALLERY . "32px/" . $extension . ".png";
                if (!file_exists(ROOT_PATH . $icon)) {
                    $icon = PATH_ICON_FRONTEND_FILEGALLERY . "32px/_blank.png";
                }*/

                $icon = "<i class=\"filegallery_content_file_icon fa fa-".$iconclass."\" aria-hidden=\"true\"></i>";

                $link            = PATH_ORIGINAL_FRONTEND_FILEGALLERY . $filename;

                if(trim($description) == ""){
                    $description = $filename;
                }
                $showDescription = $description . " (" . formatSizeUnits($fileInfo->getSize()) . ")";

                echo "<div class='filegallery_content_file'><a href=\"" . $link . "\" target=\"_blank\">".$icon . $showDescription . "</a></div>";

            }
        }
        ?>

</div>