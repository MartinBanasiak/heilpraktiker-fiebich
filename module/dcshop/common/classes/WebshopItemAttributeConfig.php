<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 24.10.2016
 * Time: 12:34
 */
class WebshopItemAttributeConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopItemAttribute';
    protected $tableName      = '';
    protected $baseTableName  = '';
    protected $altPrimary     = array();
    protected $mappedFields   = array(
        array('name' => 'id', 'type' => 'STRING'),
        array('name' => 'company', 'type' => 'VARCHAR'),
        array('name' => 'shop_code', 'type' => 'VARCHAR'),
        array('name' => 'language_code', 'type' => 'VARCHAR'),
        array('name' => 'item_no', 'type' => 'VARCHAR'),
        array('name' => 'variant_code', 'type' => 'VARCHAR'),
        array('name' => 'attribute_code', 'type' => 'VARCHAR'),
        array('name' => 'attribute_description', 'type' => 'VARCHAR'),
        array('name' => 'attribute_value', 'type' => 'VARCHAR'),
        array('name' => 'attribute_value_type', 'type' => 'INT'),
        array('name' => 'attribute_display_type', 'type' => 'INT'),
        array('name' => 'icon_filename', 'type' => 'VARCHAR'),
        array('name' => 'filter_expression', 'type' => 'VARCHAR'),
        array('name' => 'option_link', 'type' => 'VARCHAR'),
    );

}