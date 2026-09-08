<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;


/**
 * Class ShippingClassConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShippingClassConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\ShippingOptions\ShippingClass';
    protected $tableName = 'shipping_classes';
    protected $baseTableName = 'shipping_classes';
    protected $altPrimary = ['company', 'code'];
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'priority', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'),
    );

}