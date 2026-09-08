<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:26
 */
class VisitorConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\common\classes\Visitor';
    protected $tableName      = 'main_visitor';
    protected $altPrimary     = array('session_id');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'session_id','type'=>'VARCHAR'),
        array('name'=>'last_ipv4_anon','type' => 'VARCHAR'),
        array('name'=>'last_ipv6_anon','type' => 'VARCHAR'),
        array('name'=>'session_date','type'=>'DATETIME'),
        array('name'=>'valid_until','type'=>'DATETIME'),
        array('name'=>'frontend_login','type'=>'TINYINT'),
        array('name'=>'cookie_only','type'=>'TINYINT'),
        array('name'=>'main_user_id','type'=>'INT'),
        array('name'=>'shop_salesperson_id','type'=>'INT'),
        array('name'=>'admin_login','type'=>'TINYINT'),
        array('name'=>'main_admin_user_id','type'=>'INT'),
        array('name'=>'currency_code','type'=>'VARCHAR'),
        array('name'=>'data','type'=>'LONGTEXT'),
        array('name'=>'remember_token','type'=>'VARCHAR'),
        array('name'=>'nav_login','type'=>'TINYINT'),
        array('name'=>'serialized_objects','type'=>'MEDIUMBLOB')
    );
	
}