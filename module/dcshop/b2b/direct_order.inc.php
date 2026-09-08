<?
$secret = getenv('SHOP_PASSWORD');

$secretToken2 = array(
"time"=>time(),
"token_id"=>$_SERVER['SERVER_NAME']
);

$secretToken1 = base64_encode(hash_hmac("sha256",json_encode($secretToken2),$secret));
$secretToken2 = base64_encode(json_encode($secretToken2));
?>

<span id="company" style="display:none;" data-company="<?= $GLOBALS["shop"]["company"] ?>"></span>
<span id="shop_code" style="display:none;" data-shop-code="<?= $GLOBALS["shop"]["code"] ?>"></span>
<span id="lang_code" style="display:none;" data-lang-code="<?= $GLOBALS["shop_language"]["code"] ?>"></span>
<span id="item_src" style="display:none;" data-item-src="<?= $GLOBALS["shop"]["item_source"] ?>"></span>
<div id="direct_order">
    <label><?= $GLOBALS["tc"]["quick_order"] ?></label>
    <form id="form_direct_order" name="form_direct_order" method="post"
          action="/<? echo customizeUrl();?>/basket/?action=direct_order">
        <div class="direct_order_field">
            <div class="direct_order_field_inner">
                <input class="direct_order_field_item_no" type="text" name="input_item_no" id="input_item_no" value="<?= $GLOBALS["tc"]["item_no"] ?>"
                   onfocus="if(this.form.input_item_no.value =='<?= $GLOBALS["tc"]["item_no"] ?>'){this.form.input_item_no.value=''} " />
            </div>
            <div class="direct_order_field_inner">
                <div class="select_body">
                    <select name="input_var_code" id="input_var_code" value="<?= $GLOBALS["tc"]["colour_size"] ?>">
                        <option value=""><span><?= $GLOBALS["tc"]["colour_size"] ?></span></option>
                    </select>
                </div>
            </div>
            <div class="direct_order_field_inner">
                <input name="input_item_quantity" class="direct_order_field_quantity"  type="text" id="input_item_quantity"
                       onKeyPress="return submitenter(this,event)" onfocus="if(this.form.input_item_quantity.value =='<?= $GLOBALS["tc"]["quantity"] ?>'){this.form.input_item_quantity.value=''}"
                       value="<?= $GLOBALS["tc"]["quantity"] ?>" maxlength="3" />
            </div>
        </div>
        <div class="direct_order_button">
            <span class="fa fa-cart-plus fa-fw"></span>
        </div>
        <input type="hidden" class="form-control" name="token_1" value="<?= $secretToken1 ?>" id="token_1">
        <input type="hidden" class="form-control" name="token_2" value="<?= $secretToken2 ?>" id="token_2">
    </form>
</div>
<script>
    $(".direct_order_button").click(function() {

        var ammount = parseInt($("#input_item_quantity").val());

        if($("#input_item_no").val() == "" || $("#input_item_no").val() =="<?= $GLOBALS["tc"]["item_no"] ?>" || $("#input_item_quantity").val() == "" || !(Number.isInteger(ammount)) || ammount <= 0 )
        {
            return false;
        }
        document.forms['form_direct_order'].submit();
    });
</script>