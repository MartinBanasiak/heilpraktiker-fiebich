<?php
namespace DynCom\dc\dcShop\rma\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:06
 */
class ReturnReasonConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\ReturnReason';
    protected $tableName    = 'shop_return_reason';
    protected $baseTabelName = 'shop_return_reason';
    protected $altPrimary   = array('company', 'code');
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'INT'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'code', 'type' => 'VARCHAR'),
        array('name' => 'description', 'type' => 'VARCHAR'),
        array('name' => 'to_delete', 'type' => 'TINYINT')
    );

}