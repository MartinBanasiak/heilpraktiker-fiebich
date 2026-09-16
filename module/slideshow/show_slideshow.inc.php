<?
require_once __DIR__ . DIRECTORY_SEPARATOR . 'slideshow.config.inc.php';
$query   = "SELECT * FROM slideshow_header WHERE id = '" . $sitepart_id."'";
$result1 = @mysqli_query($GLOBALS['mysql_con'], $query);
$row1    = mysqli_fetch_array($result1);

// datum query
$where  = " AND (validity_from IS NULL OR validity_from <= '" . date("Y-m-d") . "') ";
$where .= " AND (validity_to   IS NULL OR validity_to   >= '" . date("Y-m-d") . "') ";

$query2  = "SELECT * FROM slideshow_line WHERE header_id = '" . $sitepart_id . "' $where ORDER BY sorting ASC";
$result2 = @mysqli_query($GLOBALS['mysql_con'], $query2);
?>
    <script type="text/javascript">
        $(document).ready(function(){
            var owl = $("#a<?=$sitepart_id?>.owl-carousel").owlCarousel(
                {
                    autoplay:true,
                    autoplayTimeout: <?=$row1['eff_interval'] * 1000?>,
                    loop: true,
                    autoplayHoverPause: true,
                    items: 1,
                    responsiveClass: true,
                    animateOut: 'fadeOut',
                    nav: true,
                    navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                    onInitialized: function(elem){
                        setTimeout(function(){
                            $(elem.currentTarget).find('.owl-item.active .slideshow_content_inner').addClass('active');
                        },1000);
                    }
                }
            );
            owl.on('change.owl.carousel',function(property){
                var current = property.item.index;
                $(property.target).find(".owl-item").eq(current).find('.slideshow_content_inner').removeClass('active');
            });
            owl.on('changed.owl.carousel',function(property){
                var current = property.item.index;
                $(property.target).find(".owl-item").eq(current).find('.slideshow_content_inner').addClass('active');
            });
        });
    </script>
    <style>
        /*
         * Die eingestellte Hoehe wird zusaetzlich als CSS-Variable
         * weitergereicht. Das Layout braucht sie, um den Platz schon vor dem
         * Aufbau des Karussells zu reservieren - Owl blendet den Container
         * bis dahin aus, wodurch der Banner mit null Hoehe startet und alles
         * darunter beim Erscheinen nach unten springt.
         */
        .slideshow_box {
            --slideshow-hoehe: <?php echo (int)$row1['height']; ?>px;
        }
        .slideshow_box .owl-carousel-item.animated {
            -webkit-animation-duration : <?=$row1['effect_duration'] / 100?>s  ;
            animation-duration : <?=$row1['effect_duration'] /100?>s  ;
            height: <?echo $row1['height']?>px;
        }
    </style>
<?php

$buttontext = $GLOBALS['tc']['slideshow_button'];


echo "<div class=\"slideshow_box ".$GLOBALS['navigation']['code']."\"><div id=\"a" . $sitepart_id . "\" class=\"owl-carousel\">";
WHILE ($row2 = mysqli_fetch_array($result2)) {
    if($row2['text2'] <> "") {
        $buttontext = $row2['text2'];
    }

    $bgposition = "center center";

    switch ($row2['bg_position']){
        case 1:
            $bgposition = "left center";
            break;
        case 2:
            $bgposition = "right center";
            break;
        default:
            $bgposition = "center center";
            break;
    }

    echo "<div class='owl-carousel-item animated'  style=\"background-position: ".$bgposition."; background-image:url(".PATH_ORIGINAL_FRONTEND . $row2['filename'].")\">";
    if($row2['link'] <> "") {
        echo "<a href=\"" . $row2["link"] . "\">";
    }
    ?>
    <?/*<img src="<?=PATH_ORIGINAL_FRONTEND . $row2['filename']?>" />*/?>
    <div class="slideshow_content container">
    <? if($row2['headline'] <> "" || $row2['text'] <> "") { ?>
        <div class="slideshow_content_inner">
            <? if($row2['headline'] <> "") { ?>
                <div class="slideshow_headline">
                    <div class="slideshow_headline_inner">
                        <?=$row2['headline']?>
                    </div>
                </div>
            <? }?>
            <? if($row2['text'] <> "") { ?>
                <div class="slideshow_text">
                    <div class="slideshow_text_inner">
                        <?=$row2['text']?>
                    </div>
                </div>
            <? }?>
            <? if($row2['link'] <> "") { ?>
                <div class="slideshow_button">
                    <div class="button"><?=$buttontext?></div>
                </div>
            <? }?>
        </div>
    <? }?>
    </div>
    <? if($row2['link'] <> "") {
        echo "</a>";
    }
    echo "</div>";
}
echo("</div></div>\n");
?>