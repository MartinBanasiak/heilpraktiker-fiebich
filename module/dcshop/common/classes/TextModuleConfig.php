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
class TextModuleConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\TextModule';
    protected $tableName      = 'shop_text_module';
    protected $baseTableName  = 'shop_text_module';
    protected $altPrimary     = array('company', 'code');
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'code', 'type' => 'VARCHAR'),
        array('name' => 'description', 'type' => 'VARCHAR'),
        array('name' => 'content', 'type' => 'LONGTEXT'),
        array('name' => 'attachment_1', 'type' => 'VARCHAR'),
        array('name' => 'attachment_2', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

}