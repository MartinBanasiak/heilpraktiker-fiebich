<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class WebshopItemVariantConfig
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemVariantConfig implements ModelDBConfigInterface {

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopItemVariant';
    protected $tableName      = 'shop_item_variant';
    protected $baseTableName  = 'shop_item_variant';
    protected $altPrimary     = array('company','item_no','code');
    protected $mappedFields   = array(
          array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
		  array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
		  array('name' => 'item_no', 'type' => 'varchar', 'maxlen' => '20'),
		  array('name' => 'code', 'type' => 'varchar', 'maxlen' => '10'),
		  array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
		  array('name' => 'description_2', 'type' => 'varchar', 'maxlen' => '50'),
		  array('name' => 'inventory', 'type' => 'decimal', 'maxlen' => ''),
		  array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1')
    );

}