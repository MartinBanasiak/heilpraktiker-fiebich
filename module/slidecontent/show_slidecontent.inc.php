<div class="slidecontent">

    <div class="sitepart_<?= $sitepart_id ?>">

        <?

        if (isset($_GET["slidecontent" . $sitepart_id]) && $_GET["slidecontent" . $sitepart_id] <> '') {

            $query = "SELECT * FROM slidecontent_line WHERE id = '" . $_GET["slidecontent" . $sitepart_id] . "' LIMIT 1";

            $result = @mysqli_query($GLOBALS['mysql_con'], $query);

            if (@mysqli_num_rows($result) == 1) {

                $slidecontent = @mysqli_fetch_array($result);

                $headline          = $slidecontent['headline'];
                $content           = $slidecontent['content'];
                $slidecontent_item = "\n<div class=\"slide_container\">\n<div class=\"slidecontent_headline\">\n<h3>$headline</h3>\n</div>\n<div class=\"slidecontent_content_container\">\n$content\n</div>\n<br />\n</div>\n";

                echo $slidecontent_item;

            }

        } else {

            $query = "SELECT * FROM slidecontent_line WHERE header_id = '" . $sitepart_id . "' ORDER BY sorting asc";

            $result = @mysqli_query($GLOBALS['mysql_con'], $query);

            if (@mysqli_num_rows($result) > 0) {

                while ($slidecontent = @mysqli_fetch_array($result)) {

                    $headline          = $slidecontent['headline'];
                    $content           = $slidecontent['content'];
                    $slidecontent_item = "\n<div class=\"slide_container\">\n<div class=\"slidecontent_headline\">\n$headline\n</div>\n<div class=\"slidecontent_content_container\">\n$content\n</div>\n</div>\n";

                    echo $slidecontent_item;

                }

            }

        }



        ?>

    </div>

</div>