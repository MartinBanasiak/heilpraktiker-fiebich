<div class="site_header_main">
    <div class="logged_in_as">
        <?= ($GLOBALS["shop_user"]["name"] != '') ? $GLOBALS["shop_user"]["name"] . ", " : "" ?><?= $GLOBALS["shop_customer"]["name"] ?>
    </div>
</div>
<div class="user_menu">
    <a href="/<? echo customizeUrl(); ?>/account/?action=edit_curr_shop_user">
        <i class="fa fa-user" aria-hidden="true"></i> <span><?= $GLOBALS["tc"]["my_account"] ?></span>
    </a>
    <a href="/<? echo customizeUrl(); ?>/favorites/">
        <i class="fa fa-star" aria-hidden="true"></i> <span><?= $GLOBALS["tc"]["favorites_b2b"] ?> (<?= shop_get_favorites_quantity($GLOBALS["visitor"]["id"]) ?>)</span>
    </a>
    <a href="/<?echo customizeUrl(); ?>/?action=shop_logout">
        <i class="fa fa-power-off" aria-hidden="true"></i> <span><?= $GLOBALS["tc"]["logout"] ?></span>
    </a>
</div>