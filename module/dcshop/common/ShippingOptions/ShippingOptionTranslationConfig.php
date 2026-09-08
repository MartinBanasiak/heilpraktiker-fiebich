<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\interfaces\ModelDBConfigInterface;
use DynCom\dc\common\traits\genericConfigTrait;


/**
 * Class ShippingOptionTranslationConfig
 * @package DynCom\dc\dcShop\classes
 */
class ShippingOptionTranslationConfig implements ModelDBConfigInterface
{

    use genericConfigTrait;

    protected $modelClassName = 'DynCom\dc\dcShop\ShippingOptions\ShippingOptionTranslation';
    protected $tableName = 'shipping_option_translation';
    protected $baseTableName = 'shipping_option_translation';
    protected $altPrimary = ['company', 'shipping_group_code', 'line_no', 'language_code'];
    protected $mappedFields = array(
        array('name' => 'id', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'company', 'type' => 'varchar', 'maxlen' => '30'),
        array('name' => 'shipping_group_code', 'type' => 'varchar', 'maxlen' => '20'),
        array('name' => 'line_no', 'type' => 'int', 'maxlen' => '11'),
        array('name' => 'language_code', 'type' => 'varchar', 'maxlen' => '10'),
        array('name' => 'description', 'type' => 'varchar', 'maxlen' => '50'),
		array('name' => 'logo', 'type' => 'varchar', 'maxlen' => '100'),
		array('name' => 'content', 'type' => 'varchar', 'maxlen' => '250'),
		array('name' => 'to_delete', 'type' => 'tinyint', 'maxlen' => '1'),
    );

}