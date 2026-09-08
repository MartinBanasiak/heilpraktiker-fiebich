<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class WebshopItemFileConfig
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemFileConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\classes\WebshopItemFile';
    protected $tableName = 'shop_item_file';
    protected $baseTableName = 'shop_item_file';
    protected $altPrimary = ['company', 'shop_code', 'language_code', 'item_no', 'variant_code', 'type', 'line_no'];
    protected $mappedFields = [
        ['name' => 'id', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'company', 'type' => 'varchar', 'maxlen' => '30'],
        ['name' => 'shop_code', 'type' => 'varchar', 'maxlen' => '10'],
        ['name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'],
        ['name' => 'item_no', 'type' => 'varchar', 'maxlen' => '20'],
        ['name' => 'variant_code', 'type' => 'varchar', 'maxlen' => '20'],
        ['name' => 'type', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'line_no', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'description', 'type' => 'varchar', 'maxlen' => '50'],
        ['name' => 'filename', 'type' => 'varchar', 'maxlen' => '100'],
        ['name' => 'youtube_video_id', 'type' => 'varchar', 'maxlen' => '20'],
        ['name' => 'all_language_codes', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'mp4', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'webm', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'ogg', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'main_medium', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'customization', 'type' => 'tinyint', 'maxlen' => '1'],
        ['name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'],
    ];

}