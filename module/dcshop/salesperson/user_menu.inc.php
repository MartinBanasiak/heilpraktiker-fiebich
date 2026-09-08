<div id="header_account">
    <div id="login_info">
        <div id="logged_in_text" class="header_account_headline"><?= $GLOBALS["tc"]["logged_in_as_b2b"] ?>:</div>
        <div id="logged_in_name" class="header_account_line_2"><?= $GLOBALS["shop_user"]["name"] ?></div>
        <div id="logged_in_links" class="header_account_line_3">
            <div id="header_account_link">
                <a href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=account&action=edit_curr_salesperson"
                   alt=""><?= $GLOBALS["tc"]["my_account"] ?></a>
            </div>
            <div id="header_customer_list_link">
                <a href="?shop_category=account&action=show_salesperson_customers"
                   alt=""><?= $GLOBALS['tc']['customer_list'] ?></a>
            </div>
            <div id="header_logout_link">
                <a href="?action=shop_logout" alt=""><?= $GLOBALS["tc"]["logout"] ?></a>
            </div>
        </div>
    </div>
    <div id="customer_select">
        <div id="customer_headline" class="header_account_headline"><?= $GLOBALS['tc']['select_customer'] ?></div>
        <div id="customer_select_box"
             class="header_account_line_2"><? create_customer_select($GLOBALS['shop_user']['salesperson_code']) ?></div>
        <div id="customer_account_link" class="header_account_line_3">
            <a href="?shop_category=account&action=edit_shop_customer"
               alt=""><?= $GLOBALS['tc']['customer_account'] ?></a>
        </div>
    </div>
</div>
