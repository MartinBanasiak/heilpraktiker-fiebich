<div class="shoppingcart">
    <div>
        <a class="header_basket_link"
           href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=basket">
            <span><?= $GLOBALS["tc"]["shopping_basket"] . "&nbsp;(" . shop_get_basket_quantity($GLOBALS["visitor"]["id"]) . ")" ?></span>
        </a>
    </div>
    <div>
        <a href="/<?= $GLOBALS["site"]["code"] ?>/<?= $GLOBALS["language"]["code"] ?>/shop/?shop_category=basket">
            <span><?= format_amount(shop_get_basket_amount($GLOBALS["visitor"]["id"]), FALSE) ?></span>
        </a>
    </div>
</div>