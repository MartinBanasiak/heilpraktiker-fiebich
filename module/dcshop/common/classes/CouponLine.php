<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class CouponLine
 */
class CouponLine implements GenericDBModelInterface, Entity
{

    use genericDBModelTrait, universallyGettableTrait;

    public const VALUE_TYPE_AMOUNT = 0;
    public const VALUE_TYPE_PERCENT = 1;
    public const VALUE_TYPE_FREE_ITEM = 2;
    public const VALUE_TYPE_SHIPPING = 3;

    protected $id;
    protected $company;
    protected $code;
    protected $coupon_code;
    protected $customer_no;
    protected $times_used;
    protected $max_no_of_usage;
    protected $amount;
    protected $amount_left;
    protected $last_date_used;
    protected $value_type;
    protected $percentage;
    protected $item_no;
    protected $variant_code;
    protected $valid_from;
    protected $valid_to;
    protected $amount_from;
    protected $value_coupon;
    protected $active;
    protected $coupon_group;
    protected $update_insert;
    protected $to_delete;


    /**
     * @param CouponLineConfig $config
     */
    public function __construct(CouponLineConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return CouponLine
     */
    public function getNullObject()
    {
        return new self($this->config);
    }

}