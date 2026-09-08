<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\traits\genericDBModelTrait;
use DynCom\dc\common\traits\universallyGettableTrait;

/**
 * Class ShippingZone
 */
class ShippingZone implements GenericDBModelInterface, Entity {

    use genericDBModelTrait, universallyGettableTrait;

      protected $id;
      protected $company;
      protected $code;
      protected $description;
      protected $to_delete;
    /**
     * @var ShippingZoneLineCollection
     */
      protected $lines;

    /**
     * @param ShippingZoneConfig $config
     */
    public function __construct( ShippingZoneConfig $config ) {
        $this->config = $config;
    }

    /**
     * @return ShippingZone
     */
    public function getNullObject() {
        return new self($this->config);
    }

    public function setLines(ShippingZoneLineCollection $lines)
    {
        $this->lines = $lines;
    }

    public function getLines() {
        return $this->lines;
    }

}