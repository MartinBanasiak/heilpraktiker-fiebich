<div id="user_account_navigation" class="user_account">
    <? IF (!($GLOBALS["visitor"]["frontend_login"])) { ?>
        <div class="user_account_link login_link">
            <a href="/<?echo customizeUrl(); ?>/fix/login/">
                <?= $GLOBALS["tc"]["login"] ?>
            </a>
        </div>
        <? if ($GLOBALS["shop"]["show_comparsion"]) { ?>
            <div class="user_account_link item_compare_link">
                <a
                    <? if (isset($_GET['shop_category']) && $_GET['shop_category'] == "item_compare") {
                        echo 'class="active"';
                    } ?>
                        href="/<?echo customizeUrl(); ?>/item_compare/">
                    <?= $GLOBALS["tc"]["compare"] ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </a>
            </div>
        <? } ?>
        <div class="user_account_link favorites_link">
            <a
                <? if (isset($_GET['shop_category']) && $_GET['shop_category'] == "favorites") {
                    echo 'class="active"';
                } ?>
                    href="/<?echo customizeUrl(); ?>/favorites/">
                <?= $GLOBALS["tc"]["favorites"] ?> (<?= shop_get_favorites_quantity($GLOBALS["visitor"]["id"]) ?>) <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
        </div>
    <? } else { ?>
        <div class="user_account_link account_link">
            <a
                <? if (isset($_GET['shop_category']) && $_GET['shop_category'] == "account") {
                    echo 'class="active"';
                } ?>
                    href="/<?echo customizeUrl(); ?>/account/?action=shop_login_account">
                <?= $GLOBALS["tc"]["my_account"] ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
        </div>
        <div class="user_account_link logout_link">
            <?

            $code = "";
            $query = "SELECT code FROM main_navigation WHERE id = " . $GLOBALS["language"]["logout_navigation_id"];
            $result = mysqli_query($GLOBALS['mysql_con'], $query);
            if (mysqli_num_rows($result) == 1) {
                $code = mysqli_fetch_row($result);
                $code = $code[0] . "/";
            }

            ?>
            <a href="/<?echo customizeUrl(); ?>/<?= $code ?>?action=shop_logout">
                <?= $GLOBALS["tc"]["logout"] ?>
            </a>
        </div>
        <? if ($GLOBALS["shop"]["show_comparsion"]) { ?>
            <div class="user_account_link item_compare_link">
                <a
                    <? if (isset($_GET['shop_category']) && $_GET['shop_category'] == "item_compare") {
                        echo 'class="active"';
                    } ?>
                        href="/<? echo customizeUrl(); ?>/item_compare/">
                    <?= $GLOBALS["tc"]["compare"] ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
                </a>
            </div>
        <? } ?>
        <div class="user_account_link favorites_link">
            <a
                <? if (isset($_GET['shop_category']) && $_GET['shop_category'] == "favorites") {
                    echo 'class="active"';
                } ?>
                    href="/<?echo customizeUrl(); ?>/favorites/">
                <?= $GLOBALS["tc"]["favorites"] ?> (<?= shop_get_favorites_quantity($GLOBALS["visitor"]["id"]) ?>) <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
        </div>
    <? } ?>
    <? /*
    <div class="user_account_link language_link">
        <a href="#">
            <?= $GLOBALS["tc"]["language"] ?> (<?= strtoupper($GLOBALS["language"]["code"]); ?>) <i
                class="fa fa-angle-down" aria-hidden="true"></i>
        </a>
        <div class="user_account_subnavi">
            <div class="user_account_subnavi_headline">
                Sprache wählen
            </div>
            <ul>
                <li>
                    <a href="#">Deutsch</a>
                </li>
                <li>
                    <a href="#">Englisch</a>
                </li>
            </ul>
        </div>
    </div>
 */ ?>
</div>