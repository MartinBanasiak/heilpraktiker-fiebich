<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;


/**
 * Class ShipmentAddressConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShipmentAddressConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\ShipmentAddress';
    protected $tableName      = 'shop_shipment_address';
    protected $baseTableName  = 'shop_shipment_address';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'customer_no', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'code', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'name', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'name_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'contact', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'address', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'address_2', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'address_street', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'address_no', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'post_code', 'type' => 'varchar', 'maxlen' => '20'),
  array('name' => 'city', 'type' => 'varchar', 'maxlen' => '50'),
  array('name' => 'country', 'type' => 'varchar', 'maxlen' => '10'),
  array('name' => 'phone_no', 'type' => 'varchar', 'maxlen' => '80'),
  array('name' => 'surname', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'lastname', 'type' => 'varchar', 'maxlen' => '30'),
  array('name' => 'company_name', 'type' => 'varchar', 'maxlen' => '45'),
  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}