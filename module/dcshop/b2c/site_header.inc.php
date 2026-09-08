<div id="shopping_bag">
    <div class="user_account"><? IF (!($GLOBALS["visitor"]["frontend_login"])) { ?>
            <div>Benutzerkonto</div>
            <a href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/fix/login/">
                &gt; <?= $GLOBALS["tc"]["login"] ?></a>
            <a href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/fix/lost_pass/">
                &gt; <?= $GLOBALS["tc"]["forgot_password_plain"] ?></a> <? } else { ?>
            <div><?= $GLOBALS["tc"]["logged_in_as"] ?> <strong><?= $GLOBALS["shop_user"]["name"] ?></strong>
            </div>
            <a href="/<? echo customizeUrl(); ?>/account/?action=shop_login">>&nbsp;<?= $GLOBALS["tc"]["my_account"] ?></a>
            <a href="/<? echo customizeUrl(); ?>/?action=shop_logout">x&nbsp;<?= $GLOBALS["tc"]["logout"] ?></a>
        <? } ?>
    </div>
    <div class="shopping_bag">
        <div class="shopping_bag_left">
            <a href="/<? echo customizeUrl(); ?>/basket/"><?= $GLOBALS["tc"]["shopping_basket"] ?>
                (<?= shop_get_basket_quantity($GLOBALS["visitor"]["id"]) ?>)<br />
                <?= format_amount(shop_get_basket_amount($GLOBALS["visitor"]["id"]), TRUE, FALSE) ?></a>
        </div>
        <div class="shopping_bag_right">
            <a href="/<? echo customizeUrl(); ?>/basket/"><?= (shop_get_basket_quantity($GLOBALS["visitor"]["id"]) > 0) ? shop_get_basket_quantity($GLOBALS["visitor"]["id"]) : "" ?></a>
        </div>
    </div>
</div>