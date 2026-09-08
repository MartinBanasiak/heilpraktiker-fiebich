<?php
$translation = \DynCom\dc\common\classes\Registry::get("translation");
?>
<h2><?php echo $translation->get("slideshow_options"); ?></h2>
<?php
if (!is_array($sitepart_options) || count($sitepart_options) == 0) {
    $width           = 500;
    $height          = 250;
    $effect          = 'fade';
    $text_pos        = 'bottom';
    $interval        = 4;
    $text_effect     = 'none';
    $effect_duration = 1;
    $arrows          = "yes";
} else {
    $width           = $sitepart_options["collection_setup_width"];
    $height          = $sitepart_options["collection_setup_height"];
    $effect          = $sitepart_options["collection_setup_effect"];
    $text_pos        = $sitepart_options["collection_setup_text_pos"];
    $interval        = $sitepart_options["collection_setup_eff_interval"];
    $text_effect     = $sitepart_options["collection_setup_text_effect"];
    $effect_duration = $sitepart_options["collection_setup_effect_duration"];
    $arrows          = $sitepart_options["collection_setup_arrows"];
}
?>

<table class="cardform" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?php input_select($translation->get("arrows"), "collection_setup_arrows", array(0 => "yes", 1 => "no"), array(0 => $translation->get("display"), 1 => $translation->get("dont_display")), $arrows) ?>
            <?php input($translation->get("width_px"), "collection_setup_width", "code", $width) ?>
            <?php input($translation->get("height_px"), "collection_setup_height", "code", $height) ?>
            <?php input($translation->get("show_duration"), "collection_setup_eff_interval", "code", $interval) ?>
        </td>
        <td>
            <?php input($translation->get("effect_duration"), "collection_setup_effect_duration", "code", $effect_duration) ?>
            <?php input_select($translation->get("text_effect"), "collection_setup_text_effect", $values = array(0 => 'fixed', 1 => 'slide', 2 => 'fade'), $value_names = array(0 => $translation->get("no_effect"), 1 => $translation->get("fade_in"), 2 => $translation->get("crossfade")), $text_effect) ?>
            <?php input_select($translation->get("text_position"), "collection_setup_text_pos", $values = array(0 => 'top', 1 => 'bottom'), $value_names = array(0 => $translation->get("top"), 1 => $translation->get("bottom")), $text_pos) ?>
            <?php input_select($translation->get("transition_effect"), "collection_setup_effect", $values = array(0 => 'none', 1 => 'scroll', 2 => 'fade'), $value_names = array(0 => $translation->get("no_effect"), 1 => $translation->get("fade_out"), 2 => $translation->get("crossfade")), $effect) ?>
        </td>
    </tr>
</table>