<div id="fb-root"></div>
<script>(function ( d, s, id ) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&appId=336141683103141&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php
$query  = "SELECT * FROM facebook_header WHERE id = '" . $sitepart_id."'";
$result = @mysqli_query($GLOBALS['mysql_con'], $query);
if (@mysqli_num_rows($result) == 1) {
    $facebook = @mysqli_fetch_array($result);
    $fburl    = $facebook['url'];
    $fbWidth  = $facebook['width'];
    $fbHeight = $facebook['height'];
    $fbFaces  = $facebook['show_faces'] == 1 ? 'true' : 'false';
    $fbPosts  = $facebook['show_posts'] == 1 ? 'true' : 'false';
}
?>

<?php if ($fburl != ""): ?>
    <div class="fb-like-box"
         data-href="<?php echo $fburl; ?>"
         data-colorscheme="light"
         data-width="<?php echo $fbWidth; ?>"
         data-height="<?php echo $fbHeight; ?>"
         data-show-faces="<?php echo $fbFaces; ?>"
         data-header="true"
         data-stream="<?php echo $fbPosts; ?>"
         data-show-border="true">
    </div>
<?php endif; ?>