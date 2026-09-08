<span id="company" style="display:none;" data-company="<?= $GLOBALS["shop"]["company"] ?>"></span>
<span id="shop_code" style="display:none;" data-shop-code="<?= $GLOBALS["shop"]["code"] ?>"></span>
<span id="lang_code" style="display:none;" data-lang-code="<?= $GLOBALS["shop_language"]["code"] ?>"></span>
<span id="item_src" style="display:none;" data-item-src="<?= $GLOBALS["shop"]["item_source"] ?>"></span>
<div id="direct_order">
    <span class="header_navigation_name"><span><?= $GLOBALS["tc"]["quick_order"] ?></span></span>

    <div id="direct_order_hover" class="header_menu_hover">
        <div class="container">
            <form id="form_direct_order" name="form_direct_order" method="post"
                  action="/<?= $GLOBALS['site']['code'] ?>/<?= $GLOBALS['language']['code'] ?>/shop/?shop_category=basket&action=direct_order">
                <div class="direct_order_field">
                    <input type="text" name="input_item_no" id="input_item_no" value="<?= $GLOBALS["tc"]["item_no"] ?>"
                           onfocus="this.form.input_item_no.value=''" />
                    <select name="input_var_code" id="input_var_code" value="<?= $GLOBALS["tc"]["colour_size"] ?>">
                        <option value=""><span><?= $GLOBALS["tc"]["colour_size"] ?></span></option>
                    </select>
                    <input name="input_item_quantity" type="text" id="input_item_quantity"
                           onKeyPress="return submitenter(this,event)" onfocus="this.form.input_item_quantity.value=''"
                           value="<?= $GLOBALS["tc"]["quantity"] ?>" maxlength="3" />
                </div>
                <div class="direct_order_button" onclick="document.forms['form_direct_order'].submit();">
                    <span class="fa fa-shopping-cart fa-fw"></span>
                </div>
            </form>
        </div>
    </div>
</div>