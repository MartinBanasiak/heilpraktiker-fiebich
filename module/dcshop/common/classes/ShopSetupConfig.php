<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:24
 */
class ShopSetupConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\ShopSetup';
    protected $tableName      = 'shop_setup';
    protected $baseTableName  = 'shop_setup';
    protected $altPrimary     = array('company');
    protected $mappedFields   = array(
        array('name'=>'id','type'=>'INT'),
        array('name'=>'company','type'=>'VARCHAR'),
        array('name'=>'nas_email_text_module','type'=>'VARCHAR'),
		array('name'=>'nas_email_recipient','type'=>'VARCHAR'),
		array('name'=>'nas_email_recipient_2','type'=>'VARCHAR'),
		array('name'=>'nas_email_sender','type'=>'VARCHAR'),
		array('name'=>'default_currency_code','type'=>'VARCHAR'),
		array('name'=>'last_datetime_nav_updated','type'=>'DATETIME'),
		array('name'=>'last_datetime_solr_updated','type'=>'DATETIME'),
		array('name'=>'default_shipping_class_priority','type'=>'INT'),
		array('name'=>'to_delete','type'=>'TINYINT'),
    );

}