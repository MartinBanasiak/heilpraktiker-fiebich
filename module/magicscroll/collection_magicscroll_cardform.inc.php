<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<h2><?php echo $translation->get("scrollbar_options"); ?></h2>
<?php
if (!is_array($sitepart_options) || count($sitepart_options) == 0) {
    $item_width      = 100;
    $item_height     = 75;
    $width           = 940;
    $height          = 75;
    $items           = 3;
    $step            = 3;
    $eff_interval    = 3000;
    $effect_duration = 1000;
    $arrows          = 'innen';
} else {
    $item_width  = $sitepart_options["collection_setup_item_width"];
    $item_height = $sitepart_options["collection_setup_item_height"];
    $width       = $sitepart_options["collection_setup_width"];
    $height      = $sitepart_options["collection_setup_height"];

    $items           = $sitepart_options["collection_setup_items"];
    $step            = $sitepart_options["collection_setup_step"];
    $eff_interval    = $sitepart_options["collection_setup_eff_interval"];
    $effect_duration = $sitepart_options["collection_setup_effect_duration"];
    $arrows          = $sitepart_options["collection_setup_arrows"];
}
?>

<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?php input($translation->get("effect_duration"), "collection_setup_effect_duration", "code", $effect_duration) ?>
        </td>
        <td>
            <?php input_select($translation->get("arrows"), "collection_setup_arrows", $values = array(0 => 'inside', 1 => 'outside', 2 => 'false'), $values = array(0 => $translation->get("show_inside"), 1 => $translation->get("show_outside"), 2 => $translation->get("dont_show")), $arrows) ?>
        </td>
    </tr>
    <tr>
        <td><?php input($translation->get("element_width"), "collection_setup_item_width", "code", $item_width) ?>  </td>
        <td><?php input($translation->get("showed_elements_at_once"), "collection_setup_items", "code", $items) ?> </td>
    </tr>
    <tr>
        <td><?php input($translation->get("element_height"), "collection_setup_item_height", "code", $item_height) ?></td>
        <td><?php input($translation->get("elements_pro_scroll"), "collection_setup_step", "code", $step) ?></td>
    </tr>
    <tr>
        <td>
            <?php input($translation->get("width_px"), "collection_setup_width", "code", $width) ?>
        </td>
        <td>
            <?php input($translation->get("view_duration"), "collection_setup_eff_interval", "code", $eff_interval) ?>
        </td>
    </tr>

    <tr>
        <td>
            <?php input($translation->get("height_px"), "collection_setup_height", "code", $height) ?>
        </td>
        <td>
        </td>
    </tr>
</table>