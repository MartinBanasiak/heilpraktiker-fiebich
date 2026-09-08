<?php
$query      = "SELECT * FROM youtube_header WHERE id = '" . $sitepart_id."'";
$youtube_id = "";
$result     = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $youtube    = @mysqli_fetch_array($result);
    $youtube_id = $youtube['youtube_id'];
    $width      = $youtube['width'];
    $height     = $youtube['height'];
}
?>

<?php if ($youtube_id != ""): ?>
    <div class="youtubecontent embed-responsive embed-responsive-16by9">
        <div class="DCCookie_youtube DCCookie_youtube_container" id="<?= $youtube_id ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"
             src="//www.youtube.com/embed/<?php echo $youtube_id; ?>?wmode=transparent&rel=0"
             allowfullscreen></div>
    </div>
<?php endif; ?>