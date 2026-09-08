<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'magicscroll.config.inc.php';

$query   = "SELECT * FROM scrollbar_header WHERE id = '" . $sitepart_id."'";
$result1 = mysqli_query($GLOBALS['mysql_con'], $query);
$row1    = mysqli_fetch_array($result1);

$query2  = "SELECT * FROM scrollbar_line WHERE header_id = '" . $sitepart_id . "' ORDER BY sorting ASC";
$result2 = mysqli_query($GLOBALS['mysql_con'], $query2);

echo "<div id=\"a" . $sitepart_id . "\" class=\"MagicScroll scrollbar\" data-options='
    speed:".$row1['effect_duration'].";
    items:".$row1['items'].";
    step:".$row1['step'].";
    height:".$row1['height'].";
    arrows:".$row1['arrows'].";
    autoplay:".$row1['eff_interval']."
    '>\n";
WHILE ($row2 = mysqli_fetch_array($result2)) {
    $entry = '';
    if ($row2['link'] <> '') {
        $entry .= "<a href=\"" . $row2['link'] . "\">";
    }
    IF ($row2['filename'] <> '') {
        $entry .= "<img src=\"" . PATH_ORIGINAL_FRONTEND_MAGICSCROLL . $row2['filename'] . "\" alt='".$row2['description'] ."' />";
    }
    IF ($row2['text'] <> '') {
        $entry .= $row2['text'];
    }
    if ($row2['link'] <> '') {
        $entry .= "</a>";
    }

    echo $entry;
}
echo "</div>";