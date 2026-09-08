<?

function textcontent_show( $sitepart_id ) {
    $query  = "SELECT * FROM textcontent_header WHERE id = '" . $sitepart_id . "' LIMIT 1";
    $result = @mysqli_query($GLOBALS['mysql_con'], $query);
    if (@mysqli_num_rows($result) == 1) {
        $textcontent = @mysqli_fetch_array($result);

        $ckeditorString = "";
        if ($GLOBALS["live_edit_mode"] === TRUE) {
            $ckeditorString = 'id="textcontent_editor_' . $sitepart_id . '" contenteditable="true" data-textcontentid="' . $sitepart_id . '"';
        }

        if ($GLOBALS['background_image_path'] != '') {
            echo "\n<div class=\"textcontent hasBackground\" " . $ckeditorString . " style='background-image: url(" . $GLOBALS['background_image_path'] . ")' >\n";
            echo $textcontent["content"] . "\n";
            echo "</div>\n";
        } else {
            echo "\n<div class=\"textcontent noBackground\" " . $ckeditorString . ">\n";
            echo $textcontent["content"] . "\n";
            echo "</div>\n";
        }
    }
    $GLOBALS['background_image_path'] = '';
}

function textcontent_edit() {
    require __DIR__ . DIRECTORY_SEPARATOR . 'edit_textcontent.inc.php';
}