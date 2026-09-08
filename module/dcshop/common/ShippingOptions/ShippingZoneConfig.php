<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class ShippingZoneConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShippingZoneConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\ShippingOptions\ShippingZone';
    protected $tableName      = 'shipping_zones';
    protected $baseTableName  = 'shipping_zones';
    protected $altPrimary     = array('company','code');
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'code', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

    protected $linesClassName             = 'ShippingZoneLine';
    protected $linesCriteriaFieldMappings = array(
        array('company', '=', 'company'),
        array('shipping_zone_code','=','code')
    );

    public function getLinesClassName()
    {
        return $this->linesClassName;
    }

    public function getLineCriteriaFieldMappings()
    {
        return $this->linesCriteriaFieldMappings;
    }

}