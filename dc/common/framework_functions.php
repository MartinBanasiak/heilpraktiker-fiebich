<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 16.11.2015
 * Time: 10:53
 */
function defaultLogger()
{

}

function templateEngine()
{
    static $engine;
    if (null === $engine) {
        $engine = new \DynCom\dc\common\classes\MustacheTemplateEngine();
    }
    return $engine;
}

function formBuilder()
{
    static $formBuilder;
    if (null === $formBuilder) {
        $formBuilder = new \DynCom\dc\common\classes\FormBuilder('new_form');
    }
    return $formBuilder;
}

function validator()
{
    static $validator;
    if (null === $validator) {
        $validator = new \DynCom\dc\common\classes\Validator([]);
    }
    return $validator;
}

/**
 * @return array
 */
function basketItems()
{
    static $IOC;
    static $basket;
    static $itemBuilder;
    if (null === $IOC) {
        $IOC = $GLOBALS['IOC'];
        $itemBuilder = $IOC->resolve('DynCom\dc\dcShop\classes\WebshopItemBuilder');
    }

    if (null === $basket) {
        $basket = $IOC->resolve('$CurrUserBasket');
    }
    $itemArr = [];
    $itemGiftWrappingArr = [];
    if ($basket instanceof \DynCom\dc\dcShop\interfaces\UserBasket) {
        foreach ($basket as $basketItem) {
            if ($basketItem instanceof \DynCom\dc\dcShop\classes\BasketEntity && $basketItem->getOrderableType() === 2) {
                $orderableEntity = $basketItem->getOrderableEntity();
                if ($itemBuilder instanceof \DynCom\dc\dcShop\classes\WebshopItemBuilder) {
                    $itemWithImages = $itemBuilder->decorateWebshopItemImages($orderableEntity);
                    $item = [];
                    $item['id'] = $itemWithImages->getID();
                    $item['company'] = $itemWithImages->getCompany();
                    $item['shop_code'] = $itemWithImages->getShopCode();
                    $item['language_code'] = $itemWithImages->getLanguageCode();
                    $item['item_no'] = $itemWithImages->getItemNo();
                    $item['var_code'] = $itemWithImages->getVariantCode();
                    $item['unit_price'] = $basketItem->getUnitPrice();
                    $item['customer_price'] = $basketItem->getUnitPrice();
                    $item['cross_price'] = $basketItem->getCrossPrice();
                    $item['line_amount'] = $basketItem->getLineAmount();
                    $item['basket_id'] = $basketItem->getDBID();
                    $item['basket_line_id'] = $basketItem->getDBID();
                    $item['basket_header_id'] = $basket->getID();
                    $item['quantity'] = $basketItem->getQuantity();
                    $item['basket_quantity'] = $basketItem->getQuantity();
                    $item['image_data'] = $itemWithImages->getImageData();
                    $item['main_image_data'] = $itemWithImages->getMainImageData();
                    $item['description'] = $itemWithImages->getDescription();
                    $item['variant_typ'] = $itemWithImages->getVariantType();
                    $item['variant_type'] = $itemWithImages->getVariantType();
                    $item['summary'] = $itemWithImages->getSummary();
                    $item['parent_item_no'] = $itemWithImages->getParentItemNo();
                    $item['item_key'] = $basket->getKey($basketItem);
                    $item['qty_protected'] = $basketItem->isQtyProtected();
                    $item['unit_price_protected'] = $basketItem->isUnitPriceProtected();
                    $item['notifications'] = get_item_user_notifications($basketItem);
                    $item['availability'] = $orderableEntity->getAvailability();
                    $item['inventory'] = $orderableEntity->getInventory();
                    $item['allow_gift_package'] = $itemWithImages->getAllowGiftPackage();
                    $item['is_gift_package'] = $itemWithImages->getIsGiftPackage();
                    $item['is_greeting_card'] = $itemWithImages->getIsGreetingCard();
                    $item['vat_prod_posting_group'] = $itemWithImages->getVATProdPostingGroup();
                    $item['no_of_gift_wrappings'] = $basket->getNoOfGiftWrappingsForItemKey($item['item_key']);
                    $item['greeting_card_text'] = $basketItem->getGreetingCardText();
                    $item['customization_hash'] = $basketItem->getCustomizationHash();
                    $item['links_to_key'] = $basketItem->getBasketEntityLink();
                    $item['applied_line_discounts'] = $basketItem->getAppliedLineDiscounts();
                    $item['item_slug'] = $itemWithImages->getItemSlug();

                    if ($basketItem->getBasketEntityLink()["entity_link_type"] != 1) {
                        $itemArr[$basket->getKey($basketItem)] = $item;
                    } elseif ($basketItem->getBasketEntityLink()["entity_link_type"] == 1) {
                        $itemGiftWrappingArr[$basket->getKey($basketItem)] = $item;
                    }
                }
            }
        }
    }

    //uasort($itemArr, 'basketSortFunction');
    if (count($itemGiftWrappingArr) > 0) {
        foreach ($itemGiftWrappingArr as $item) {
            $itemArr = array_insert_after($item['links_to_key']["links_to_entity_key"], $itemArr, $item['item_key'], $item);
        }
    }
    return $itemArr;
}

function basketTotal($invDiscAllowedOnly = false)
{
    static $IOC;
    static $basket;
    if (null === $IOC) {
        $IOC = $GLOBALS['IOC'];
    }
    if (null === $basket) {
        $basket = $IOC->resolve('$CurrUserBasket');
    }
    if (!$invDiscAllowedOnly) {
        return $basket->getBasketTotal();
    } else {
        return $basket->getTotalAmntInvoiceDiscountAllowed();
    }

}

function basketQty()
{
    static $IOC;
    static $basket;
    if (null === $IOC) {
        $IOC = $GLOBALS['IOC'];
    }
    if (null === $basket) {
        $basket = $IOC->resolve('$CurrUserBasket');
    }
    return $basket->getTotalQty();
}

function basketNoOfPos()
{
    /**
     * @var $IOC \DynCom\dc\common\interfaces\IOCInterface
     */
    static $IOC;
    /**
     * @var $basket \DynCom\dc\dcShop\interfaces\UserBasket
     */
    static $basket;
    if (null === $IOC) {
        $IOC = $GLOBALS['IOC'];
    }
    if (null === $basket) {
        $basket = $IOC->resolve('$CurrUserBasket');
    }
    return $basket->getTotalNoOfPos();
}

function basketSortFunction($a, $b)
{
    $aKey = $a['item_no'] . '|' . $a['var_code'] . '|' . $a['unit_price'];
    $bKey = $b['item_no'] . '|' . $b['var_code'] . '|' . $b['unit_price'];
    if ($aKey === $bKey) {
        return 0;
    }
    return ($aKey < $bKey) ? -1 : 1;
}

function basketItemTotal()
{
    /**
     * @var $IOC \DynCom\dc\common\interfaces\IOCInterface
     */
    static $IOC;
    /**
     * @var $basket \DynCom\dc\dcShop\interfaces\UserBasket
     */
    static $basket;
    if (null === $IOC) {
        $IOC = $GLOBALS['IOC'];
    }
    if (null === $basket) {
        $basket = $IOC->resolve('$CurrUserBasket');
    }
    return $basket->getBasketItemTotal();
}

function array_insert_after($key, array &$array, $new_key, $new_value)
{
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
            $new[$k] = $value;
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
        }
        return $new;
    }
    return FALSE;
}