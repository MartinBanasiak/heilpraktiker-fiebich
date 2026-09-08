<?
$basket_total_pos = $currUserBasket->getTotalNoOfPos();
?>

<div class="header_basket">
    <a class="header_basket_link" href="/<? echo customizeUrl(); ?>/basket/">
        <label><? echo $GLOBALS["tc"]["shopping_basket"] . "&nbsp;(" . $basket_total_pos . ")" ?></label>
        <div class="header_basket_total">
            <? echo format_amount($currUserBasket->getBasketTotal(), FALSE) ?>
        </div>
        <div class="header_basket_button">
            <?if($basket_total_pos > 0){?>
                <div class="header_basket_button_label"><? echo $basket_total_pos ?></div>
            <?}?>
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
        </div>
    </a>
</div>