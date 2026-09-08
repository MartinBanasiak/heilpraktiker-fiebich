<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class CouponLineConfig
 * @package DynCom\dc\dcShop\classes
 */
class CouponLineConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\CouponLine';
    protected $tableName = 'shop_coupon_line';
    protected $baseTableName = 'shop_coupon_line';
    protected $altPrimary = array('company', 'code', 'coupon_code');
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'coupon_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'customer_no', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'times_used', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'max_no_of_usage', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'amount', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'amount_left', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'last_date_used', 'type' => 'date', 'maxlen' => ''),
        array('name' => 'value_type', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'percentage', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'item_no', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'variant_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'valid_from', 'type' => 'date', 'maxlen' => ''),
        array('name' => 'valid_to', 'type' => 'date', 'maxlen' => ''),
        array('name' => 'amount_from', 'type' => 'decimal', 'maxlen' => ''),
        array('name' => 'value_coupon', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'active', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'coupon_group', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'update_insert', 'type' => 'tinyint', 'maxlen' => '1'),
        array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}