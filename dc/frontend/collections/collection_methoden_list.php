<div class="col-xxs-12 col-xs-6 col-sm-4 col-md-6 col-lg-4">
    <a href="<?=$fullviewLink;?>" class="collection__item <?=$groupclasses?>">
        <div class="collection__icon">
            <div class="image">
                <img src="/userdata/collection/resize/<?=$collectionFullLines['icon']['data'] ?>" alt="<?=$collectionFullLines['headline']['data'] ?>" />
            </div>
        </div>
        <div class="collection__text">
            <div class="collection__title">
                <h3><?=$collectionFullLines['headline']['data'] ?></h3>
            </div>
            <div class="collection__intro">
                <?=$collectionFullLines['intro']['data'] ?>
            </div>
        </div>
        <div class='collection_link'>
            <span class='link'>
                <?=$GLOBALS['tc']['learn_more'];?>
            </span>
        </div>
    </a>
</div>