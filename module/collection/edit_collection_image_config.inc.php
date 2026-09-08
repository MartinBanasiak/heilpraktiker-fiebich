<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");

if (!is_array($sitepart_options) || count($sitepart_options) == 0) {
    $width  = 300;
    $height = 200;
} else {
    $width  = $sitepart_options["collection_setup_image_width"];
    $height = $sitepart_options["collection_setup_image_height"];
}
?>
<h2>Bildoptionen</h2>
<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td><?php input($translation->get("max_width"), "collection_setup_image_width", "code", $width); ?>  </td>
        <td></td>
    </tr>
    <tr>
        <td><?php input($translation->get("max_height"), "collection_setup_image_height", "code", $height); ?> </td>
        <td></td>
    </tr>
</table>