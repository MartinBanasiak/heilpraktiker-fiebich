<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class ShippingZoneLineConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShippingZoneLineConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\ShippingOptions\ShippingZoneLine';
    protected $tableName      = 'shipping_zone_line';
    protected $baseTableName  = 'shipping_zone_line';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
		  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
		  array('name' => 'shipping_zone_code', 'type' => 'varchar', 'maxlen' => '20'),
		  array('name' => 'post_code_code', 'type' => 'varchar', 'maxlen' => '20'),
		  array('name' => 'country_region_code', 'type' => 'varchar', 'maxlen' => '10'),
		  array('name' => 'type', 'type' => 'int', 'maxlen' => '11'),
		  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}