<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");

if (!is_array($sitepart_options) || count($sitepart_options) == 0) {
    $sitepart_options = array(
        'width'        => 500,
        'height'       => 250,
        'thumb_width'  => '',
        'thumb_height' => ''
    );
    $width            = 500;
    $height           = 250;
    $thumb_width      = 100;
    $thumb_height     = 100;
} else {
    $width        = $sitepart_options["collection_setup_gallery_width"];
    $height       = $sitepart_options["collection_setup_gallery_height"];
    $thumb_width  = $sitepart_options["collection_setup_gallery_thumb_width"];
    $thumb_height = $sitepart_options["collection_setup_gallery_thumb_height"];
}
?>
<h2><?php echo $translation->get("gallery_options"); ?></h2>
<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><?php input($translation->get("max_width"), "collection_setup_gallery_width", "code", $width); ?>
            <?php input($translation->get("max_height"), "collection_setup_gallery_height", "code", $height); ?>
        </td>
        <td>
            <?php input($translation->get("max_thumb_width"), "collection_setup_gallery_thumb_width", "code", $thumb_width); ?>
            <?php input($translation->get("max_thumb_height"), "collection_setup_gallery_thumb_height", "code", $thumb_height); ?>
        </td>
    </tr>
</table>