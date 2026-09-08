<?php

namespace DynCom\dc\dcShop\CustomerAddress;

use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;


/**
 * Class CustomerAddressConfig
 * @package DynCom\dc\dcShop\CusotmerAddress
 */
class CustomerAddressConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = CustomerAddress::class;
    protected $tableName = 'shop_customer_address';
    protected $baseTableName = 'shop_customer_address';
    protected $altPrimary = array();
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'customer_no', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'name', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'name_2', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'address', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'address_2', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'post_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'city', 'type' => 'varchar', 'maxlen' => '50'),
        array('name' => 'country', 'type' => 'varchar', 'maxlen' => '20'),
    );

}