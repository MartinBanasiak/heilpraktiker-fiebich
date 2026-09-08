<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:25
 */
class UserConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\User';
    protected $tableName        = 'shop_user';
    protected $baseTableName    = 'shop_user';
    protected $altPrimary       = array('company', 'shop_code', 'email');
    protected $mappedFields     = array(
        array('name' => 'id','type' => 'INT'),
        array('name' => 'company','type' => 'VARCHAR'),
        array('name' => 'shop_code','type' => 'VARCHAR'),
        array('name' => 'customer_no','type' => 'VARCHAR'),
        array('name' => 'shop_shipment_address_id','type' => 'INT'),
        array('name' => 'name','type' => 'VARCHAR'),
        array('name' => 'email','type' => 'VARCHAR'),
        array('name' => 'login','type' => 'VARCHAR'),
        array('name' => 'password','type' => 'VARCHAR'),
        array('name' => 'main_user','type' => 'TINYINT'),
        array('name' => 'right_user_management','type' => 'TINYINT'),
        array('name' => 'right_order_history','type' => 'TINYINT'),
        array('name' => 'right_order','type' => 'TINYINT'),
        array('name' => 'right_return_order','type' => 'TINYINT'),
        array('name' => 'last_visitor_id','type' => 'INT'),
        array('name' => 'to_delete','type' => 'TINYINT')
    );

}