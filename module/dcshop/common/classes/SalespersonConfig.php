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
class SalespersonConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\Salesperson';
    protected $tableName        = 'shop_salesperson';
    protected $baseTableName    = 'shop_salesperson';
    protected $altPrimary       = array('company', 'salesperson_code');
    protected $mappedFields     = array(
        array('name' => 'id','type' => 'INT'),
        array('name' => 'company','type' => 'VARCHAR'),
        array('name' => 'shop_code','type' => 'VARCHAR'),
        array('name' => 'language_code','type' => 'VARCHAR'),
        array('name' => 'salesperson_code','type' => 'VARCHAR'),
        array('name' => 'name','type' => 'VARCHAR'),
        array('name' => 'email','type' => 'VARCHAR'),		
        array('name' => 'phone_no','type' => 'VARCHAR'),
        array('name' => 'max_discount','type' => 'DECIMAL'),
        array('name' => 'password','type' => 'VARCHAR'),
        array('name' => 'user','type' => 'VARCHAR'),
        array('name' => 'to_delete','type' => 'TINYINT')
    );

}