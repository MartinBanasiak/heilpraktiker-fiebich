<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class CountryConfig
 * @package DynCom\dc\dcShop\classes
 */
class CountryConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\Country';
    protected $tableName      = 'shop_country';
    protected $baseTableName  = 'shop_country';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
          array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
          array('name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'country_code', 'type' => 'varchar', 'maxlen' => '10'),
          array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
          array('name' => 'invoice_to', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'ship_to', 'type' => 'tinyint', 'maxlen' => '1'),
          array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}