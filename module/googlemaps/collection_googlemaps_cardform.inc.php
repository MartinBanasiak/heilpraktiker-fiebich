<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<h2><?php echo $translation->get("googlemaps_options"); ?></h2>
<?php
if (!is_array($sitepart_options)) {
    $width  = 450;
    $height = 450;
} else {
    $width  = $sitepart_options["collection_setup_google_maps_width"];
    $height = $sitepart_options["collection_setup_google_maps_height"];
}
?>

<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?php input($translation->get("maps_width"), "collection_setup_google_maps_width", "code", $width); ?>
        </td>
        <td>
            <?php input($translation->get("icon_path"), "collection_setup_icon_location", "code", $sitepart_options['collection_setup_icon_location']); ?>

        </td>
    </tr>
    <tr>
        <td><?php input($translation->get("maps_height"), "collection_setup_google_maps_height", "code", $height); ?> </td>
        <td></td>
    </tr>

</table>