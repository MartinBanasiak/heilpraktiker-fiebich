<?php require_once dirname(__DIR__, 2) . '/common/editorial_images.inc.php'; ?>
<div class="row">
    <div class="col-xs-12 col-sm-8">
        <h2 class="collection__title">
            <?=$collectionFullLines['headline']['data'] ?>
        </h2>
        <div class="collection__text">
            <strong class="collection__intro">
                <?=$collectionFullLines['intro']['data'] ?>
            </strong>
        </div>
        <div class="collection__text">
            <div class="collection__details">
                <?=getCollectionFullLine($collectionFullLines['details']) ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 xs-margin col-sm-4">
        <div class="collection__icon">
            <div class="image">
                <?= dc_image_tag('/userdata/collection/resize/' . $collectionFullLines['icon']['data'], $collectionFullLines['headline']['data']) ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 marginTop">
        <div class="collection__details">
            <?=getCollectionFullLine($collectionFullLines['details2']) ?>
        </div>
    </div>
</div>
<hr/>
<div class="back_to_overview text-right">
    <a href="<?=get_link_to_navigation($GLOBALS["navigation"]['id']);?>">
        <?=$GLOBALS['tc']['back_to_overview']?>
    </a>
</div>