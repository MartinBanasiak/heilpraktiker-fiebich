<div class="col-xxs-12 col-xs-6">
    <a href="<?=$collectionFullLines['link']['data'] ?>" target="_blank" class="collection__item">
        <div class="collection__text">
            <div class="row flexrow">
                <div class="col-xs-12 xs-margin col-sm-4">
                    <div class="collection__image">
                        <div class="image bgcover" style="background-image: url(/userdata/collection/resize/<?=$collectionFullLines['preview_image']['data'] ?>)"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8">
                    <div class="collection__headline">
                        <?=$collectionFullLines['headline']['data'] ?>
                    </div>
                    <div class="collection__link">
                        <?
                        $url =  parse_url($collectionFullLines['link']['data']);

                        echo str_replace("www.","",$url['host']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>