<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;


/**
 * Class ShippingOptionConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShippingOptionConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\ShippingOptions\ShippingOption';
    protected $tableName = 'shop_shipping_option';
    protected $baseTableName = 'shop_shipping_option';
    protected $altPrimary = ['company', 'shipping_group_code', 'line_no'];
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'shipping_group_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'shipping_agent_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'shipping_agent_service_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'weight_from', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'weight_to', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'amount_from', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'amount_to', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'valid_from', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'valid_to', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'shipping_cost', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'exemption', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'logo', 'type' => 'varchar', 'maxlen' => '100'),
        array('name' => 'content', 'type' => 'varchar', 'maxlen' => '250'),
        array('name' => 'sorting', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'shipping_class_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'shipping_zone_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'final_shipping_cost', 'type' => 'decimal', 'maxlen' => ''),
    );

}