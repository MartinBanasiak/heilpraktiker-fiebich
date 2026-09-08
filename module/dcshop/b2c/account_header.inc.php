<? if ($GLOBALS['visitor']['frontend_login']) { ?>
    <div id="site_header">
        <div class="logged_in_as"><?= $GLOBALS["tc"]["logged_in_as"] ?>
            <strong><?= $GLOBALS["shop_user"]["name"] ?></strong>
        </div>
        <div class="user_menu">
            <div class="button"><a style="background-image:url(/layout/frontend/shop/img/konto.gif);"
                                   href="/<? echo customizeUrl(); ?>/account/?action=edit_curr_shop_user"><?= $GLOBALS["tc"]["my_account"] ?></a>
                <a style="background-image:url(/layout/frontend/shop/img/favoriten.gif);"
                   href="/<? echo customizeUrl(); ?>/favorites/"><?= $GLOBALS["tc"]["favorites"] ?>
                    (<?= shop_get_favorites_quantity($GLOBALS["visitor"]["id"]) ?>)</a><a
                    style="background-image:url(/layout/frontend/shop/img/logout.gif);"
                    href="/<? echo customizeUrl(); ?>/?action=shop_logout"><?= $GLOBALS["tc"]["logout"] ?></a>
            </div>
        </div>
    </div>
<? } ?>
