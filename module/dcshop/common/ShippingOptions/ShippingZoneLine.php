<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class ShippingZoneLine
 */
class ShippingZoneLine implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $coupon_code;
      protected $shipping_zone_code;
      protected $post_code_code;
      protected $country_region_code;
      protected $type;
      protected $to_delete;

    /**
     * @param ShippingZoneLineConfig $config
     */
    public function __construct( ShippingZoneLineConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return ShippingZoneLine
     */
    public function getNullObject() {
        return new self($this->config);
    }

}