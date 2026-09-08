<?
require_once("gallery.config.inc.php");
$query  = "SELECT * FROM gallery_line WHERE header_id = '" . $sitepart_id . "' ORDER BY sorting asc";
$result = mysqli_query($GLOBALS['mysql_con'], $query);
$countRows = @mysqli_num_rows($result);
if ($countRows > 0) {
    echo "<div id='imageGallery".$sitepart_id."' class=\"imageGallery\"><div class='row'>";
    $count = 0;
    while ($row = mysqli_fetch_array($result)) {
        echo '<div class="imageGallery__item col-xs-6 col-sm-4"><a  href="' . PATH_ORIGINAL_FRONTEND_GALLERY . $row['filename'] . '" data-toogle="lightbox" data-gallery="imageGallery'.$sitepart_id.'" style="background-image:url(' . PATH_THUMB_FRONTEND_GALLERY . $row['filename'] . ')"></a></div>';
        $count ++;
    }
    echo "</div></div>";
    ?>
    <script>
        $(document).ready(function () {
            $('#imageGallery<?=$sitepart_id?> a').click(function (event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    leftArrow: '<i class="fa fa-angle-left"></i>',
                    rightArrow: '<i class="fa fa-angle-right"></i>',
                });
            });
        });
    </script>
    <?
}

?>
