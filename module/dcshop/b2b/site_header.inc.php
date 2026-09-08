<div class="user_menu">
    <div class="button"><a
            href="/<? echo customizeUrl(); ?>/account/?action=edit_curr_shop_user">
            &gt;&nbsp;<?= $GLOBALS["tc"]["my_account"] ?></a>
        <a href="/<?echo customizeUrl(); ?>/favorites/">
            &gt;&nbsp;<?= $GLOBALS["tc"]["favorites_b2b"] ?> (<?= shop_get_favorites_quantity($GLOBALS["visitor"]["id"]) ?>
            )</a>
        <a href="/<?echo customizeUrl(); ?>/?action=shop_logout"> <?= $GLOBALS["tc"]["logout"] ?></a>
    </div>
</div>

<div id="site_header_main">
    <div id="site_header">
        <div class="logged_in_as">
            <div><?= $GLOBALS["tc"]["logged_in_as_b2b"] ?>:</div>
            <div
                class="customer_name"><?= ($GLOBALS["shop_user"]["name"] != '') ? $GLOBALS["shop_user"]["name"] . ", " : "" ?><?= $GLOBALS["shop_customer"]["name"] ?></div>
        </div>
    </div>


</div>