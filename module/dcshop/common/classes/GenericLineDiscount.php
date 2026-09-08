<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\Discount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 10:17
 */
class GenericLineDiscount extends DiscountBase implements Discount
{
    private static $type = self::DISCOUNT_TYPE_LINE_DISCOUNT;
}