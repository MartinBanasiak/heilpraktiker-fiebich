<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\dcShop\abstracts\DiscountBase;
use DynCom\dc\dcShop\interfaces\Discount;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 06.10.2015
 * Time: 21:21
 */
class GenericInvoiceDiscount extends DiscountBase implements Discount
{
    const EVALUATION_TYPE_TAXABLE_DISCOUNT = 0;
    const EVALUATION_TYPE_PAYMENT = 1;

    private static $type = self::DISCOUNT_TYPE_INVOICE_DISCOUNT;

    protected $evaluationType;

    public function __construct(
        $sourceType,
        $sourceID,
        $percent,
        $amount = 0.00,
        $evaluationType = self::EVALUATION_TYPE_TAXABLE_DISCOUNT
    ) {
        parent::__construct($sourceType, $sourceID, $percent, $amount);
        $this->evaluationType = (int)$evaluationType;
    }

    public function getEvaluationType()
    {
        return $this->evaluationType;
    }

    public function getType()
    {
        return self::$type;
    }
}