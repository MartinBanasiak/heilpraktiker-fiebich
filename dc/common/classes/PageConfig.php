<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;

/**
 * Class PageConfig
 * @package DynCom\dc\common\classes
 */
class PageConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\common\classes\Page';
    protected $tableName = 'main_page';
    protected $baseTableName = 'main_page';
    protected $altPrimary = [];
    protected $mappedFields = [
        ['name' => 'id', 'type' => 'int', 'maxlen' => '10'],
        ['name' => 'title', 'type' => 'varchar', 'maxlen' => '255'],
        ['name' => 'subtitle', 'type' => 'varchar', 'maxlen' => '255'],
        ['name' => 'meta_keywords', 'type' => 'mediumtext', 'maxlen' => '11'],
        ['name' => 'active', 'type' => 'tinyint', 'maxlen' => '4'],
        ['name' => 'modified_date', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'modified_user', 'type' => 'int', 'maxlen' => '11'],
        ['name' => 'validity_from', 'type' => 'date', 'maxlen' => ''],
    ];

}