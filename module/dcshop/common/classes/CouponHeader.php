<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class CouponHeader
 */
class CouponHeader implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $shop_code;
      protected $language_code;
      protected $code;
      protected $description;
      protected $coupon_type;
      protected $value_type;
      protected $amount;
      protected $percentage;
      protected $item_no;
      protected $valid_from;
      protected $valid_to;
      protected $amount_from;
      protected $value_coupon;
      protected $category_coupon;
      protected $category_line_no;
      protected $to_delete;
    /**
     * @var CouponLineCollection
     */
      protected $lines;

    /**
     * @param CouponHeaderConfig $config
     */
    public function __construct( CouponHeaderConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return CouponHeader
     */
    public function getNullObject() {
        return new self($this->config);
    }

    public function setLines(CouponLineCollection $lines)
    {
        $this->lines = $lines;
    }

    public function getLines() {
        return $this->lines;
    }

}