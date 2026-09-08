<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class WebshopItemDescriptionConfig
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemDescriptionConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopItemDescription';
    protected $tableName = 'shop_item_description';
    protected $baseTableName = 'shop_item_description';
    protected $altPrimary = ['company', 'shop_code', 'language_code', 'item_no', 'line_no'];
    protected $mappedFields = [
        ['name' => 'id', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'company', 'type' => 'varchar', 'maxlen' => '30'],
        ['name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '10'],
        ['name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'],
        ['name' => 'item_no', 'type' => 'varchar', 'maxlen' => '20'],
        ['name' => 'line_no', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'description', 'type' => 'varchar', 'maxlen' => '50'],
        ['name' => 'content', 'type' => 'text', 'maxlen' => '1'],
        ['name' => 'show_in_header', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'marketplace_only', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'],
    ];

}