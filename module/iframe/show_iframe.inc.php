<?php
$query      = "SELECT * FROM iframe_header WHERE id = '" . $sitepart_id."'";
$iframe_url = "";
$result     = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $iframe        = @mysqli_fetch_array($result);
    $iframe_url    = $iframe['url'];
    $iframe_width  = $iframe['width'];
    $iframe_height = $iframe['height'];
}
?>

<?php if ($iframe_url != ""): ?>
    <div class="iframecontent">
        <iframe width="<?php echo $iframe_width; ?>" height="<?php echo $iframe_height; ?>"
                src="<?php echo $iframe_url; ?>"></iframe>
    </div>
<?php endif; ?>